<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Customer;
use App\Models\Book;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NBSImportController extends Controller
{
    public function index()
    {
        return view('marketing.direct-sales.nbs-import', [
            'title' => 'NBS PO Import',
            'sidebar' => 'marketing'
        ]);
    }

    public function process(Request $request)
    {
        $request->validate([
            'po_file' => 'required|file'
        ]);

        $file = $request->file('po_file');
        $path = $file->getRealPath();
        
        $data = array_map(function($line) {
            return str_getcsv($line);
        }, file($path));

        if (empty($data)) {
            return redirect()->back()->with('error', 'The file is empty.');
        }

        $orders = [];
        $currentPO = null;
        $missingBooks = [];

        foreach ($data as $rowIndex => $row) {
            // Clean the row and handle BOM
            $row = array_map(function($val) {
                return trim(str_replace("\ufeff", '', $val));
            }, $row);
            
            if (empty(array_filter($row))) continue;

            // Log first few rows to help debug
            if ($rowIndex < 10) Log::info("NBS Import Row $rowIndex: " . json_encode($row));

            // PO Header (HD)
            if ($row[0] === 'HD') {
                $currentPO = $row[1] ?? '';
                if (!$currentPO) continue;
                
                Log::info("Detected NBS PO Header (HD): " . $currentPO);
                $orders[$currentPO] = [
                    'po_number' => $currentPO,
                    'po_date' => $row[2] ?? '',
                    'vendor_code' => $row[3] ?? '',
                    'remarks' => ($row[6] ?? '') . ' ' . ($row[7] ?? ''),
                    'items' => []
                ];
                continue;
            }

            // PO Item Detail (DT)
            if ($row[0] === 'DT') {
                $linePO = $row[2] ?? '';
                if (!$currentPO || $linePO !== $currentPO) {
                    $currentPO = $linePO;
                    if (!isset($orders[$currentPO])) {
                         $orders[$currentPO] = [
                            'po_number' => $currentPO,
                            'items' => []
                         ];
                    }
                }

                $description = $row[6] ?? '';
                $gtin = $row[5] ?? '';
                
                // Try looking up the book
                $book = Book::where('name', $description)
                            ->orWhere('name', 'like', $description . '%')
                            ->orWhere('nbs_barcode', $gtin)
                            ->first();
                
                if (!$book) {
                    $missingBooks[$description] = $description;
                }

                $discountPercent = isset($row[14]) ? (float)$row[14] : 0;

                $orders[$currentPO]['items'][] = [
                    'line_item' => $row[1],
                    'description' => $description,
                    'gtin' => $gtin,
                    'qty' => (float)($row[7] ?? 0),
                    'price' => (float)($row[9] ?? 0),
                    'discount_percent' => $discountPercent,
                    'book_id' => $book ? $book->id : null,
                ];
            }
        }

        // If any books are missing, stop and notify user
        if (!empty($missingBooks)) {
            $list = implode(', ', array_keys($missingBooks));
            Log::warning("NBS Import Failed: Missing books: " . $list);
            return redirect()->back()->with('error', 'The following books were not found in your Master Registry: ' . $list . '. Please add them exactly as named in the Excel/CSV file before importing.');
        }

        if (empty($orders)) {
            Log::warning("NBS Import: No orders were detected.");
            return redirect()->back()->with('error', 'No valid NBS PO data (HD/DT rows) found in the file.');
        }

        // STOCK VALIDATION: Check if all items have sufficient stock before processing
        $stockIssues = [];
        foreach ($orders as $poNum => $poData) {
            if (empty($poData['items'])) continue;
            
            foreach ($poData['items'] as $item) {
                $book = Book::find($item['book_id']);
                if (!$book || $book->stock < $item['qty']) {
                    $bookName = $book ? $book->name : "Book ID #{$item['book_id']}";
                    $availableStock = $book ? $book->stock : 0;
                    $stockIssues[] = "PO #$poNum: $bookName (Available: $availableStock pcs, Requested: {$item['qty']} pcs)";
                }
            }
        }

        if (!empty($stockIssues)) {
            Log::warning("NBS Import: Insufficient stock - " . implode(' | ', $stockIssues));
            return redirect()->back()->with('error', 'Insufficient stock for the following items in NBS import:<br>• ' . implode('<br>• ', $stockIssues));
        }

        // Process orders into the database
        DB::beginTransaction();
        try {
            $createdCount = 0;
            foreach ($orders as $poNum => $poData) {
                if (empty($poData['items'])) continue;

                $vendorCode = $poData['vendor_code'] ?? '';
                $customer = Customer::where('account_number', $vendorCode)->first();
                
                $so = SalesOrder::create([
                    'customer_id' => $customer ? $customer->customer_id : 1, 
                    'so_number' => 'SO-NBS-' . $poNum . '-' . date('His'),
                    'type' => 'direct_consignment',
                    'ref_number' => $poNum,
                    'remarks' => ($poData['remarks'] ?? '') . ($customer ? '' : " (Vendor Code: $vendorCode)"),
                    'status' => 'draft',
                    'prepared_by' => auth()->id(),
                    'discount_percentage' => $poData['items'][0]['discount_percent'] ?? 0,
                    'total_amount' => 0,
                ]);

                $totalAmount = 0;
                foreach ($poData['items'] as $item) {
                    $subtotal = $item['qty'] * $item['price'];
                    
                    SalesOrderItem::create([
                        'sales_order_id' => $so->id,
                        'book_id' => $item['book_id'],
                        'quantity' => $item['qty'],
                        'price' => $item['price'],
                        'subtotal' => $subtotal,
                    ]);
                    $totalAmount += $subtotal;
                }
                
                $so->update(['total_amount' => $totalAmount]);
                $createdCount++;
            }

            DB::commit();
            return redirect()->route('marketing.sales-orders.list')->with('success', "Successfully imported $createdCount NBS Purchase Orders.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('NBS Import Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Critical Error during import: ' . $e->getMessage());
        }
    }

    public function viewReceipt($id)
    {
        $order = SalesOrder::with(['customer', 'items.book', 'preparedBy'])->findOrFail($id);
        
        return view('marketing.direct-sales.nbs-consignment-receipt', [
            'order' => $order,
            'title' => 'Consignment Delivery Receipt',
            'sidebar' => 'marketing'
        ]);
    }
}

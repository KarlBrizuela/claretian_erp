<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Customer;
use App\Models\Book;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;

class NBSImportController extends Controller
{
    public function index()
    {
        // Pass top-level companies for the cascading company/branch dropdowns
        $companies = Company::whereNull('parent_id')
            ->where('is_inactive', false)
            ->orderBy('company_name', 'asc')
            ->get(['company_id', 'company_name']);

        return view('marketing.direct-sales.nbs-import', [
            'title' => 'NBS PO Import',
            'sidebar' => 'marketing',
            'companies' => $companies,
        ]);
    }

    public function downloadTemplate(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Simplified headers — no Customer, no NBS Account
        $headers = [
            'PO Number',    // A
            'PO Date',      // B
            'Qty',          // C
            'Book Article', // D
            'NBS Branch',   // E
        ];
        
        foreach ($headers as $colIndex => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($colLetter . '1', $header);
        }

        // --- NBS Branch dropdown from selected branch's sub-branches (column E) ---
        $branchId = $request->query('branch_id');
        $subBranches = [];

        if ($branchId) {
            $selectedBranch = Company::find($branchId);
            if ($selectedBranch) {
                $subBranches = $selectedBranch->branches()
                    ->where('is_inactive', false)
                    ->orderBy('company_name', 'asc')
                    ->pluck('company_name')
                    ->toArray();

                // If no sub-branches, use the branch itself as the single option
                if (empty($subBranches)) {
                    $subBranches = [$selectedBranch->company_name];
                }
            }
        }

        if (!empty($subBranches)) {
            $branchSheet = $spreadsheet->createSheet();
            $branchSheet->setTitle('NBSBranchesList');
            $branchSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

            foreach ($subBranches as $idx => $branchName) {
                $branchSheet->setCellValue('A' . ($idx + 1), $branchName);
            }

            $totalBranches = count($subBranches);

            // NBS Branch is now column E
            for ($row = 2; $row <= 100; $row++) {
                $validation = $sheet->getCell('E' . $row)->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(true);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Input error');
                $validation->setError('Value is not in list');
                $validation->setPromptTitle('Pick a Branch');
                $validation->setPrompt('Please pick an NBS branch from the dropdown list');
                $validation->setFormula1('NBSBranchesList!$A$1:$A$' . $totalBranches);
            }
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        
        $response = response()->stream(function() use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="nbs_po_bulk_template.xlsx"',
            'Cache-Control' => 'max-age=0',
        ]);
        
        return $response;
    }

    public function process(Request $request)
    {
        $request->validate([
            'po_file' => 'required|file'
        ]);

        $file = $request->file('po_file');
        $path = $file->getRealPath();
        
        try {
            $spreadsheet = IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $lines = $sheet->toArray();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error reading import file: ' . $e->getMessage());
        }

        if (empty($lines)) {
            return redirect()->back()->with('error', 'The file is empty.');
        }

        // Clean headers and handle BOM
        $header = array_map(function($val) {
            return trim(str_replace("\ufeff", '', $val ?? ''));
        }, $lines[0]);

        // Detect if this is the new bulk upload template or the old HD/DT format.
        $isBulkTemplate = false;
        if (isset($header[0]) && (strtolower($header[0]) === 'po number' || strtolower($header[0]) === 'nbs branch')) {
            $isBulkTemplate = true;
        }

        if ($isBulkTemplate) {
            return $this->processBulkTemplate($lines);
        } else {
            return $this->processLegacyFormat($lines);
        }
    }

    private function processBulkTemplate($lines)
    {
        // Lowercase and trim headers for mapping
        $headers = array_map(function($h) {
            return strtolower(trim(str_replace("\ufeff", '', $h ?? '')));
        }, $lines[0]);

        $colIndices = [
            'po_number'    => array_search('po number', $headers),
            'po_date'      => array_search('po date', $headers),
            'qty'          => array_search('qty', $headers),
            'book_article' => array_search('book article', $headers),
            'nbs_branch'   => array_search('nbs branch', $headers),
        ];

        // Validate critical fields are mapped
        if ($colIndices['po_number'] === false || $colIndices['qty'] === false || $colIndices['book_article'] === false) {
            return redirect()->back()->with('error', 'Critical headers are missing in the template. Please make sure PO Number, Qty, and Book Article columns exist.');
        }

        $getValue = function($row, $key) use ($colIndices) {
            if (!isset($colIndices[$key])) return '';
            $idx = $colIndices[$key];
            if ($idx === false || !isset($row[$idx])) {
                return '';
            }
            return trim($row[$idx] ?? '');
        };

        $orders = [];
        $missingBooks = [];

        for ($i = 1; $i < count($lines); $i++) {
            $row = $lines[$i];
            if (empty(array_filter($row))) continue;

            $poNumber = $getValue($row, 'po_number');
            if (!$poNumber) {
                continue;
            }

            $bookArticle = $getValue($row, 'book_article');
            $qty = (float)$getValue($row, 'qty');
            if ($qty <= 0) continue;

            // Find Book strictly by the article field
            $book = null;
            if ($bookArticle !== '') {
                $book = Book::where('article', $bookArticle)->first();
            }

            if (!$book) {
                $missingBooks[$bookArticle ?: 'Empty Article'] = $bookArticle ?: 'Empty Article';
            }

            if (!isset($orders[$poNumber])) {
                $orders[$poNumber] = [
                    'po_number'  => $poNumber,
                    'po_date'    => $getValue($row, 'po_date'),
                    'nbs_branch' => $getValue($row, 'nbs_branch'),
                    'items'      => []
                ];
            }

            // Always use the book's price in the Master Registry!
            $unitPrice = $book ? (float)$book->price : 0;

            $orders[$poNumber]['items'][] = [
                'book_id' => $book ? $book->id : null,
                'description' => $book ? $book->name : '',
                'qty' => $qty,
                'price' => $unitPrice,
            ];
        }

        // If any books are missing, stop and notify user
        if (!empty($missingBooks)) {
            $list = implode(', ', array_keys($missingBooks));
            Log::warning("NBS Import Failed: Missing books: " . $list);
            return redirect()->back()->with('error', 'The following books were not found in your Master Registry: ' . $list . '. Please add them exactly as named in the import file before importing.');
        }

        if (empty($orders)) {
            Log::warning("NBS Import: No orders were detected.");
            return redirect()->back()->with('error', 'No valid NBS PO data found in the file.');
        }

        // STOCK VALIDATION
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

                // Build remarks from NBS Branch
                $remarksStr = !empty($poData['nbs_branch']) ? 'Branch: ' . $poData['nbs_branch'] : '';

                // Create SalesOrder directly in "picking" status
                $so = SalesOrder::create([
                    'customer_id'      => 1,
                    'so_number'        => 'SO-NBS-' . $poNum . '-' . date('His'),
                    'type'             => 'direct_consignment',
                    'ref_number'       => $poNum,
                    'remarks'          => $remarksStr,
                    'status'           => 'picking',
                    'prepared_by'      => auth()->id(),
                    'approved_by_mkt'  => auth()->id(),
                    'approved_by_acct' => auth()->id(),
                    'mkt_approved_at'  => now(),
                    'acct_approved_at' => now(),
                    'total_amount'     => 0,
                ]);

                $totalAmount = 0;
                $itemsToPick = [];
                
                foreach ($poData['items'] as $item) {
                    $unitPrice = $item['price'];
                    $subtotal = $item['qty'] * $unitPrice;
                    
                    $soItem = SalesOrderItem::create([
                        'sales_order_id' => $so->id,
                        'book_id' => $item['book_id'],
                        'quantity' => $item['qty'],
                        'price' => $unitPrice,
                        'subtotal' => $subtotal,
                    ]);
                    $totalAmount += $subtotal;
                    
                    $itemsToPick[] = [
                        'sales_order_item_id' => $soItem->id,
                        'requested_qty' => $item['qty']
                    ];
                }
                
                $so->update(['total_amount' => $totalAmount]);

                // Automatically create Pick List
                $pickList = \App\Models\PickList::create([
                    'sales_order_id' => $so->id,
                    'pick_list_number' => 'PL-' . $so->so_number . '-' . date('YmdHis'),
                    'status' => 'in_progress',
                    'prepared_by' => auth()->id(),
                ]);

                foreach ($itemsToPick as $pickItem) {
                    \App\Models\PickListItem::create([
                        'pick_list_id' => $pickList->id,
                        'sales_order_item_id' => $pickItem['sales_order_item_id'],
                        'requested_qty' => $pickItem['requested_qty'],
                        'picked_qty' => 0,
                        'status' => 'pending',
                    ]);
                }

                $createdCount++;
            }

            DB::commit();
            return redirect()->route('production.logistic.pick-list-management')->with('success', "Successfully imported $createdCount NBS Purchase Orders. They have been routed directly to the Pick Lists.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('NBS Bulk Import Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Critical Error during bulk import: ' . $e->getMessage());
        }
    }

    private function processLegacyFormat($data)
    {
        $orders = [];
        $currentPO = null;
        $missingBooks = [];

        foreach ($data as $rowIndex => $row) {
            // Clean the row and handle BOM
            $row = array_map(function($val) {
                return trim(str_replace("\ufeff", '', $val ?? ''));
            }, $row);
            
            if (empty(array_filter($row))) continue;

            // PO Header (HD)
            if ($row[0] === 'HD') {
                $currentPO = $row[1] ?? '';
                if (!$currentPO) continue;
                
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

        if (!empty($missingBooks)) {
            $list = implode(', ', array_keys($missingBooks));
            Log::warning("NBS Import Failed: Missing books: " . $list);
            return redirect()->back()->with('error', 'The following books were not found in your Master Registry: ' . $list . '. Please add them exactly as named in the import file before importing.');
        }

        if (empty($orders)) {
            Log::warning("NBS Import: No orders were detected.");
            return redirect()->back()->with('error', 'No valid NBS PO data (HD/DT rows) found in the file.');
        }

        // STOCK VALIDATION
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

        // Process orders into database
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

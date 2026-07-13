<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EfordPayout;
use App\Models\EfordPayoutItem;

class FORDController extends Controller
{
    public function autoDebitIndex()
    {
        $debits = \App\Models\AutoDebit::with(['preparer', 'directorApprover', 'financeApprover'])
            ->latest()
            ->paginate(15);

        return view('production.ford.auto-debit.index', [
            'title' => 'Auto Debit Letters',
            'role' => auth()->user()->position,
            'debits' => $debits
        ]);
    }

    public function autoDebitCreate()
    {
        return view('production.ford.auto-debit.create');
    }

    public function clientPaymentPosting()
    {
        $customers = \App\Models\Customer::orderBy('customer_name')->get();
        return view('production.ford.client-payment-posting', compact('customers'));
    }

    public function eFordPayout()
    {
        $customers = \App\Models\Customer::orderBy('customer_name')->get();
        $reports = EfordPayout::with(['customer', 'creator'])
            ->where('prepared_by', auth()->id())
            ->latest()
            ->get();
        return view('production.ford.eford-payout', compact('customers', 'reports'));
    }

    public function storeEfordPayout(Request $request)
    {
        $request->validate([
            'period' => 'required|string|max:255',
            'customer_id' => 'nullable|integer',
            'order_no' => 'required|array',
            'order_no.*' => 'nullable|string',
            'date' => 'required|array',
            'date.*' => 'nullable|date',
            'si_no' => 'required|array',
            'si_no.*' => 'nullable|string',
            'customer' => 'required|array',
            'customer.*' => 'nullable|string',
            'amount' => 'required|array',
            'amount.*' => 'nullable|numeric|min:0',
            'freight' => 'required|array',
            'freight.*' => 'nullable|numeric|min:0',
            'payment_method' => 'required|array',
            'payment_method.*' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:5120|mimes:pdf,doc,docx,xls,xlsx,jpg,png,jpeg',
        ]);

        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('eford_payouts', $filename, 'public');
                $attachmentPaths[] = $path;
            }
        }

        // Calculate totals
        $totalAmount = 0;
        $totalFreight = 0;
        $totalGrossSales = 0;

        foreach ($request->amount as $index => $amt) {
            $a = (float)$amt;
            $f = (float)($request->freight[$index] ?? 0);
            $totalAmount += $a;
            $totalFreight += $f;
            $totalGrossSales += ($a + $f);
        }

        $payout = EfordPayout::create([
            'prepared_by' => auth()->id(),
            'period' => $request->period,
            'customer_id' => $request->customer_id,
            'total_amount' => $totalAmount,
            'total_freight' => $totalFreight,
            'total_gross_sales' => $totalGrossSales,
            'attachments' => $attachmentPaths,
        ]);

        foreach ($request->order_no as $index => $orderNo) {
            $customerName = $request->customer[$index] ?? null;
            $amt = $request->amount[$index] ?? 0;
            if ($customerName || $amt > 0) {
                EfordPayoutItem::create([
                    'eford_payout_id' => $payout->id,
                    'order_no' => $orderNo,
                    'date' => $request->date[$index] ?? null,
                    'si_no' => $request->si_no[$index] ?? null,
                    'customer_name' => $customerName,
                    'amount' => $amt,
                    'freight' => $request->freight[$index] ?? 0,
                    'gross_sales' => $amt + ($request->freight[$index] ?? 0),
                    'payment_method' => $request->payment_method[$index] ?? null,
                ]);
            }
        }

        return redirect()->route('production.ford.eford-payout')->with('success', 'E-FORD Sales Summary Report has been generated and submitted to Accounting.');
    }

    public function accountingIndex()
    {
        $reports = EfordPayout::with(['customer', 'creator'])->latest()->get();
        return view('admin-finance.accounting.eford-payouts.index', [
            'title' => 'E-FORD Payout Reports',
            'role' => auth()->user()->position,
            'sidebar' => 'admin-finance',
            'reports' => $reports
        ]);
    }

    public function accountingShow($id)
    {
        $report = EfordPayout::with(['customer', 'creator', 'items'])->findOrFail($id);
        return view('admin-finance.accounting.eford-payouts.show', [
            'title' => 'E-FORD Report #' . str_pad($report->id, 5, '0', STR_PAD_LEFT),
            'role' => auth()->user()->position,
            'sidebar' => 'admin-finance',
            'report' => $report
        ]);
    }

    public function downloadAttachment($id, $index)
    {
        $payout = EfordPayout::findOrFail($id);
        $attachments = $payout->attachments;
        if (!is_array($attachments) || !isset($attachments[$index])) {
            return redirect()->back()->with('error', 'Attachment not found.');
        }

        $relativePath = $attachments[$index];
        $path = storage_path('app/public/' . $relativePath);
        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'File not found on server.');
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $inlineExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'txt'];

        if (in_array($extension, $inlineExtensions)) {
            $contentType = 'application/octet-stream';
            if ($extension === 'pdf') {
                $contentType = 'application/pdf';
            } elseif (in_array($extension, ['jpg', 'jpeg'])) {
                $contentType = 'image/jpeg';
            } elseif ($extension === 'png') {
                $contentType = 'image/png';
            } elseif ($extension === 'gif') {
                $contentType = 'image/gif';
            } elseif ($extension === 'txt') {
                $contentType = 'text/plain';
            }

            return response()->file($path, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'inline; filename="' . basename($path) . '"'
            ]);
        }

        return response()->download($path, basename($path));
    }

    public function getUnpaidInvoices($customerId)
    {
        $orders = \App\Models\SalesOrder::with(['customer'])
            ->where('customer_id', $customerId)
            ->where('payment_status', 'unpaid')
            ->whereNotIn('status', ['cancelled', 'draft'])
            ->get();

        $unpaidSIs = [];
        foreach ($orders as $order) {
            $si = \App\Models\SalesInvoice::where('so_id', $order->id)->first();
            $siNumber = $si ? $si->si_number : 'SI-' . str_pad($order->id, 6, '0', STR_PAD_LEFT);
            $unpaidSIs[] = [
                'order_no' => $order->so_number,
                'date' => $order->created_at->format('Y-m-d'),
                'si_no' => $siNumber,
                'customer' => $order->customer->customer_name ?? '',
                'amount' => (float)$order->total_amount,
                'freight' => (float)($order->freight_charges ?? 0),
                'gross_sales' => (float)$order->total_amount + (float)($order->freight_charges ?? 0),
                'payment_method' => $order->payment_method ?? ''
            ];
        }

        return response()->json($unpaidSIs);
    }

    public function paymentRequest()
    {
        $requests = \App\Models\PaymentRequest::with(['requester', 'directorApprovedBy', 'adminApprovedBy', 'financeApprovedBy'])
            ->where('requester_id', auth()->id())
            ->latest()
            ->get();

        return view('production.ford.payment-request', compact('requests'));
    }

    public function purchaseOrder()
    {
        $suppliers = \App\Models\Supplier::where('status', 'active')->get();
        
        // Self-healing: Ensure every active Book has a corresponding Product record
        $books = \App\Models\Book::where('is_active', true)->get();
        foreach ($books as $book) {
            $product = \App\Models\Product::where('book_id', $book->id)->first();
            if (!$product) {
                \App\Models\Product::create([
                    'book_id' => $book->id,
                    'name' => $book->name,
                    'price' => $book->price ?? 0.00,
                    'is_active' => true,
                ]);
            }
        }

        $products = \App\Models\Product::with('book')->where('is_active', true)->orderBy('name')->get();

        return view('production.ford.purchase-order', [
            'suppliers' => $suppliers,
            'products' => $products,
        ]);
    }

    public function requestForQuotation()
    {
        $books = \App\Models\Book::orderBy('name', 'asc')->get();
        return view('production.ford.request-for-quotation', [
            'books' => $books
        ]);
    }

    public function salesOrder()
    {
        $orders = \App\Models\SalesOrder::with('customer', 'preparedBy')
            ->where('type', 'foreign')
            ->latest()
            ->get();

        $customers = \App\Models\Customer::orderBy('customer_name')->get();
        $books = \App\Models\Book::orderBy('name', 'asc')->get();

        return view('production.ford.sales-order', [
            'title' => 'Foreign Sales Orders',
            'role' => 'FORD Staff',
            'sidebar' => 'production',
            'orders' => $orders,
            'customers' => $customers,
            'books' => $books
        ]);
    }

    public function approveSalesOrder($id)
    {
        // Role Enforcement: Production Manager
        if (!str_contains(auth()->user()->position, 'Manager')) {
            return redirect()->back()->with('error', 'Only Production Managers can approve Foreign Sales Orders.');
        }

        $order = \App\Models\SalesOrder::findOrFail($id);
        
        $order->update([
            'status' => 'picking',
            'approved_by_prod' => auth()->id(),
            'prod_approved_at' => now()
        ]);

        return redirect()->back()->with('success', 'Foreign Sales Order #' . $order->so_number . ' has been approved by Production and sent to Logistics for picking.');
    }

    public function transmittal()
    {
        return view('production.ford.transmittal');
    }

    public function storeClientPaymentPosting(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'customer_id' => 'required|array|min:1',
            'customer_id.*' => 'required|exists:customers,customer_id',
            'bank_date' => 'nullable|array',
            'bank_date.*' => 'nullable|string',
            'document_no' => 'nullable|array',
            'document_no.*' => 'nullable|string',
            'amount' => 'required|array|min:1',
            'amount.*' => 'required|numeric|min:0',
            'proof_file' => 'nullable|array',
        ]);

        try {
            $postingId = \DB::transaction(function () use ($request) {
                $posting = \App\Models\ClientPaymentPosting::create([
                    'date' => $request->date,
                    'status' => 'pending',
                    'prepared_by' => auth()->id(),
                ]);

                foreach ($request->customer_id as $index => $customerId) {
                    $attachmentPath = null;
                    if ($request->hasFile("proof_file.{$index}")) {
                        $file = $request->file("proof_file.{$index}");
                        $filename = time() . '_' . $index . '_' . $file->getClientOriginalName();
                        $attachmentPath = $file->storeAs('proof_attachments', $filename, 'public');
                    }

                    $posting->items()->create([
                        'customer_id' => $customerId,
                        'bank_date' => $request->bank_date[$index] ?? null,
                        'document_no' => $request->document_no[$index] ?? null,
                        'amount' => $request->amount[$index],
                        'proof_attachment' => $attachmentPath,
                    ]);
                }
                return $posting->id;
            });

            return response()->json([
                'success' => true,
                'message' => 'Client Payment Posting request generated successfully and sent to Accounting.',
                'id' => $postingId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function paymentPostingIndex()
    {
        $postings = \App\Models\ClientPaymentPosting::with(['preparer', 'items'])
            ->withSum('items', 'amount')
            ->latest()
            ->paginate(15);

        return view('admin-finance.accounting.payment-posting.index', [
            'title' => 'Client Payment Posting Requests',
            'role' => 'Finance Staff',
            'postings' => $postings
        ]);
    }

    public function paymentPostingShow($id)
    {
        $posting = \App\Models\ClientPaymentPosting::with(['preparer', 'items.customer'])
            ->findOrFail($id);

        return view('admin-finance.accounting.payment-posting.show', [
            'title' => 'Client Payment Posting Details',
            'role' => 'Finance Staff',
            'posting' => $posting
        ]);
    }

    public function paymentPostingPost($id)
    {
        $posting = \App\Models\ClientPaymentPosting::findOrFail($id);
        $posting->update(['status' => 'posted']);

        return redirect()->route('admin-finance.accounting.payment-posting.index')
            ->with('success', 'Client Payment Posting has been successfully marked as posted.');
    }

    public function autoDebitStore(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'debit_date' => 'required|date',
            'item_reason' => 'required|string|max:255',
            'source_origin' => 'required|string|max:255',
        ]);

        try {
            $debit = \App\Models\AutoDebit::create([
                'date' => $request->date,
                'amount' => $request->amount,
                'debit_date' => $request->debit_date,
                'item_reason' => $request->item_reason,
                'source_origin' => $request->source_origin,
                'prepared_by' => auth()->id(),
                'status' => 'pending_director',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Auto Debit letter generated successfully and sent to Director for approval.',
                'id' => $debit->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate Auto Debit letter: ' . $e->getMessage()
            ], 500);
        }
    }

    public function autoDebitShow($id)
    {
        $debit = \App\Models\AutoDebit::with(['preparer', 'directorApprover', 'financeApprover'])
            ->findOrFail($id);

        return view('production.ford.auto-debit.show', [
            'title' => 'Auto Debit Letter Details',
            'role' => auth()->user()->position,
            'debit' => $debit
        ]);
    }

    public function autoDebitApproveDirector($id)
    {
        if (auth()->user()->position !== 'Director' && !auth()->user()->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Only the Director can perform this approval.');
        }

        $debit = \App\Models\AutoDebit::findOrFail($id);
        
        if ($debit->status !== 'pending_director') {
            return redirect()->back()->with('error', 'This request is not pending Director approval.');
        }

        $debit->update([
            'status' => 'pending_finance',
            'director_approved_by' => auth()->id(),
            'director_approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Auto Debit has been approved by the Director and sent to Admin & Finance Manager/Supervisor.');
    }

    public function autoDebitApproveFinance($id)
    {
        $isManager = str_contains(auth()->user()->position, 'Manager') || str_contains(auth()->user()->position, 'Supervisor') || auth()->user()->isSuperAdmin();
        if (!$isManager) {
            return redirect()->back()->with('error', 'Only Admin and Finance Managers/Supervisors can perform this approval.');
        }

        $debit = \App\Models\AutoDebit::findOrFail($id);
        
        if ($debit->status !== 'pending_finance') {
            return redirect()->back()->with('error', 'This request is not pending Finance approval.');
        }

        $debit->update([
            'status' => 'approved',
            'finance_approved_by' => auth()->id(),
            'finance_approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Auto Debit has been approved by the Admin & Finance Manager/Supervisor.');
    }

    public function autoDebitReject($id)
    {
        $debit = \App\Models\AutoDebit::findOrFail($id);
        $debit->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Auto Debit has been rejected.');
    }

    public function accountingAutoDebitIndex()
    {
        $debits = \App\Models\AutoDebit::with(['preparer', 'directorApprover', 'financeApprover'])
            ->where('status', 'approved')
            ->latest()
            ->paginate(15);

        return view('admin-finance.accounting.auto-debit.index', [
            'title' => 'Approved Auto Debit Letters',
            'role' => 'Finance Staff',
            'debits' => $debits
        ]);
    }

    public function accountingAutoDebitShow($id)
    {
        $debit = \App\Models\AutoDebit::with(['preparer', 'directorApprover', 'financeApprover'])
            ->findOrFail($id);

        return view('admin-finance.accounting.auto-debit.show', [
            'title' => 'Auto Debit Details',
            'role' => 'Finance Staff',
            'debit' => $debit
        ]);
    }

    /**
     * Save a Ford Purchase Order to the database and redirect to the Logistics show page.
     */
    public function storeFordPurchaseOrder(Request $request)
    {
        $request->validate([
            'supplier_id'   => 'required|exists:suppliers,id',
            'po_number'     => 'required|unique:purchase_orders,po_number',
            'date'          => 'required|date',
            'product_id'    => 'required|array|min:1',
            'product_id.*'  => 'required|string',
            'description'   => 'required|array|min:1',
            'description.*' => 'required|string',
            'quantity'      => 'required|array|min:1',
            'quantity.*'    => 'required|numeric|min:1',
            'unit_price'    => 'required|array|min:1',
            'unit_price.*'  => 'required|numeric|min:0',
        ]);

        $items = [];
        $totalAmount = 0;

        foreach ($request->description as $i => $desc) {
            $qty   = $request->quantity[$i] ?? 0;
            $price = $request->unit_price[$i] ?? 0;
            $total = $qty * $price;
            $totalAmount += $total;

            $prodId = $request->product_id[$i] ?? null;

            if ($prodId === 'new_custom_book') {
                // Automatically create Book if it doesn't exist
                $book = \App\Models\Book::where('name', $desc)->first();
                if (!$book) {
                    // Generate a unique SKU
                    $uniqueSku = 'SKU-' . strtoupper(substr(md5(uniqid($desc, true)), 0, 8));
                    while (\App\Models\Book::where('sku', $uniqueSku)->exists()) {
                        $uniqueSku = 'SKU-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
                    }

                    $book = \App\Models\Book::create([
                        'name' => $desc,
                        'sku' => $uniqueSku,
                        'price' => 0.00,
                        'cost' => 0.00,
                        'stock' => 0,
                        'is_active' => true,
                    ]);
                }

                // Automatically create Product for the new Book
                $product = \App\Models\Product::where('book_id', $book->id)->first();
                if (!$product) {
                    $product = \App\Models\Product::create([
                        'book_id' => $book->id,
                        'name' => $desc,
                        'price' => 0.00,
                        'is_active' => true,
                    ]);
                }

                $prodId = $product->id;
            }

            $items[] = [
                'product_id'   => $prodId,
                'description'  => $desc,
                'language'     => $request->language[$i] ?? null,
                'ft'           => $request->ft[$i] ?? null,
                'quantity'     => $qty,
                'unit_price'   => $price,
                'total_amount' => $total,
                'bindings'     => $request->bindings[$i] ?? null,
                'item_remarks' => $request->item_remarks[$i] ?? null,
            ];
        }

        $po = \App\Models\PurchaseOrder::create([
            'po_number'         => $request->po_number,
            'supplier_id'       => $request->supplier_id,
            'date'              => $request->date,
            'terms'             => $request->terms,
            'total_amount'      => $totalAmount,
            'status'            => 'ordered',
            'source'            => 'ford',
            'vendor_name'       => $request->vendor_name,
            'contact_persons'   => $request->contact_persons,
            'vendor_address'    => $request->vendor_address,
            'payment_schedule'  => $request->payment_schedule,
            'payment_schedule2' => $request->payment_schedule2,
            'prepared_by'       => auth()->id(),
        ]);

        foreach ($items as $item) {
            $po->items()->create($item);
        }

        \App\Models\ActivityLog::create([
            'user_id'        => auth()->id(),
            'action'         => 'Ford PO created',
            'description'    => 'Ford PO #' . $po->po_number . ' saved and sent to Logistics.',
            'reference_type' => 'PurchaseOrder',
            'reference_id'   => $po->id,
            'details'        => json_encode(['po_number' => $po->po_number, 'total' => $totalAmount]),
        ]);

        return redirect()
            ->route('production.logistic.purchase-order.show', $po->id)
            ->with('success', 'Purchase Order #' . $po->po_number . ' saved and sent to Logistics.');
    }
}


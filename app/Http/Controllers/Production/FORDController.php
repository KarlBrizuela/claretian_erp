<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EfordPayout;
use App\Models\EfordPayoutItem;

class FORDController extends Controller
{
    public function autoDebit()
    {
        return view('production.ford.auto-debit');
    }

    public function clientPaymentPosting()
    {
        return view('production.ford.client-payment-posting');
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
        return view('production.ford.purchase-order');
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

        return view('production.ford.sales-order', [
            'title' => 'Foreign Sales Orders',
            'role' => 'FORD Staff',
            'sidebar' => 'production',
            'orders' => $orders,
            'customers' => $customers
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
}

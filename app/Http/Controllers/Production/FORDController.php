<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        return view('production.ford.eford-payout');
    }

    public function paymentRequest()
    {
        return view('production.ford.payment-request');
    }

    public function purchaseOrder()
    {
        return view('production.ford.purchase-order');
    }

    public function requestForQuotation()
    {
        return view('production.ford.request-for-quotation');
    }

    public function salesOrder()
    {
        $orders = \App\Models\SalesOrder::with('customer', 'preparedBy')
            ->where('type', 'foreign')
            ->latest()
            ->get();

        return view('production.ford.sales-order', [
            'title' => 'Foreign Sales Orders',
            'role' => 'FORD Staff',
            'sidebar' => 'production',
            'orders' => $orders
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

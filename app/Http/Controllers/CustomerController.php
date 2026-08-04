<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\Payment;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = Customer::whereNotIn('customer_name', ['Lazada', 'Shopee', 'TikTok'])
            ->orderBy('customer_name', 'asc')
            ->get();
        return view('marketing.customers', [
            'customers' => $customers,
            'title' => 'Customer Management',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Not needed for modal-based creation
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Smart defaulting before validation to satisfy 'required' rules
        if (!$request->has('mobile') || empty($request->mobile)) {
            $request->merge(['mobile' => 'N/A']);
        }
        if (!$request->has('billing_address') || empty($request->billing_address)) {
            $request->merge(['billing_address' => 'N/A']);
        }
        if (!$request->has('shipping_address') || empty($request->shipping_address)) {
            $request->merge(['shipping_address' => $request->billing_address ?? 'N/A']);
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|unique:customers,account_number',
            'opening_balance' => 'nullable|numeric',
            'opening_balance_date' => 'nullable|date',
            'currency_code' => 'nullable|in:PHP,USD',
            'customer_type' => 'nullable|string',
            'rep' => 'nullable|string',
            'class' => 'nullable|string',
            'title' => 'nullable|string|max:10',
            'first_name' => 'nullable|string|max:100',
            'middle_initial' => 'nullable|string|max:10',
            'last_name' => 'nullable|string|max:100',
            'job_title' => 'nullable|string|max:100',
            'main_phone' => 'nullable|string',
            'home_phone' => 'nullable|string',
            'work_phone' => 'nullable|string',
            'mobile' => 'required|string',
            'fax' => 'nullable|string',
            'main_email' => 'nullable|email',
            'cc_email' => 'nullable|email',
            'website' => 'nullable|string',
            'other_contact' => 'nullable|string',
            'billing_address' => 'nullable|string',
            'shipping_address' => 'required|string',
            'is_default_shipping' => 'nullable|boolean',
            'payment_terms' => 'nullable|in:Net 15,Net 30,Net 60,Due on receipt',
            'preferred_delivery_method' => 'nullable|in:Lazada,Shopee,Main Warehouse',
            'preferred_payment_method' => 'nullable|in:check,cash',
            'credit_limit' => 'nullable|numeric',
            'price_level' => 'nullable|in:standard,wholesale',
            'card_number_last4' => 'nullable|string|max:4',
            'card_exp_month' => 'nullable|string|max:2',
            'card_exp_year' => 'nullable|string|max:4',
            'card_name' => 'nullable|string',
            'card_billing_address' => 'nullable|string',
            'card_zip' => 'nullable|string|max:20',
            'custom_contact_person' => 'nullable|string',
            'custom_customer_field' => 'nullable|string',
            'is_inactive' => 'nullable|boolean',
        ]);

        // Provide defaults for mandatory DB fields missing in quick registration
        if (empty($validated['company_name'])) {
            $validated['company_name'] = 'Individual';
        }
        if (empty($validated['account_number'])) {
            $validated['account_number'] = 'CUST-' . strtoupper(uniqid());
        }
        


        $validated['opening_balance'] = $validated['opening_balance'] ?? 0;
        $validated['credit_limit'] = $validated['credit_limit'] ?? 0;
        $validated['manual_status'] = 'good'; // Automatically set new customers as good

        $customer = Customer::create($validated);

        return response()->json([
            'message' => 'Customer created successfully',
            'customer' => $customer
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        return response()->json($customer);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        return response()->json($customer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        // Super Admin or Marketing with permission can edit customers
        $user = auth()->user();
        if (!($user->isSuperAdmin() || ($user->hasPermission('marketing.customers')))) {
            return response()->json(['message' => 'Unauthorized action. Only Super Admin or Marketing can edit customers.'], 403);
        }

        // Smart defaulting before validation
        if (!$request->has('mobile') || empty($request->mobile)) {
            $request->merge(['mobile' => 'N/A']);
        }
        if (!$request->has('billing_address') || empty($request->billing_address)) {
            $request->merge(['billing_address' => 'N/A']);
        }
        if (!$request->has('shipping_address') || empty($request->shipping_address)) {
            $request->merge(['shipping_address' => $request->billing_address ?? 'N/A']);
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|unique:customers,account_number,' . $customer->customer_id . ',customer_id',
            'opening_balance' => 'nullable|numeric',
            'opening_balance_date' => 'nullable|date',
            'currency_code' => 'nullable|in:PHP,USD',
            'customer_type' => 'nullable|string',
            'rep' => 'nullable|string',
            'class' => 'nullable|string',
            'title' => 'nullable|string|max:10',
            'first_name' => 'nullable|string|max:100',
            'middle_initial' => 'nullable|string|max:10',
            'last_name' => 'nullable|string|max:100',
            'job_title' => 'nullable|string|max:100',
            'main_phone' => 'nullable|string',
            'home_phone' => 'nullable|string',
            'work_phone' => 'nullable|string',
            'mobile' => 'required|string',
            'fax' => 'nullable|string',
            'main_email' => 'nullable|email',
            'cc_email' => 'nullable|email',
            'website' => 'nullable|string',
            'other_contact' => 'nullable|string',
            'billing_address' => 'nullable|string',
            'shipping_address' => 'required|string',
            'is_default_shipping' => 'nullable|boolean',
            'payment_terms' => 'nullable|in:Net 15,Net 30,Net 60,Due on receipt',
            'preferred_delivery_method' => 'nullable|in:Lazada,Shopee,Main Warehouse',
            'preferred_payment_method' => 'nullable|in:check,cash',
            'credit_limit' => 'nullable|numeric',
            'price_level' => 'nullable|in:standard,wholesale',
            'card_number_last4' => 'nullable|string|max:4',
            'card_exp_month' => 'nullable|string|max:2',
            'card_exp_year' => 'nullable|string|max:4',
            'card_name' => 'nullable|string',
            'card_billing_address' => 'nullable|string',
            'card_zip' => 'nullable|string|max:20',
            'custom_contact_person' => 'nullable|string',
            'custom_customer_field' => 'nullable|string',
            'is_inactive' => 'nullable|boolean',
        ]);


        $validated['opening_balance'] = $validated['opening_balance'] ?? 0;
        $validated['credit_limit'] = $validated['credit_limit'] ?? 0;

        $customer->update($validated);

        return response()->json(['message' => 'Customer updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        // Super Admin or Marketing with permission can delete customers
        $user = auth()->user();
        if (!($user->isSuperAdmin() || ($user->hasPermission('marketing.customers')))) {
            return response()->json(['message' => 'Unauthorized action. Only Super Admin or Marketing can delete customers.'], 403);
        }
        $customer->delete();

        return response()->json(['message' => 'Customer deleted successfully']);
    }

    /**
     * Get transaction history for a customer
     */
    public function getTransactionHistory(Request $request, Customer $customer)
    {
        $allOrders = $customer->salesOrders()->with(['invoice', 'payments'])->latest()->get();

        // Status Filter
        if ($request->filled('status')) {
            $statusFilter = strtolower($request->status);
            $allOrders = $allOrders->filter(function($order) use ($statusFilter) {
                $effectiveStatus = $order->computed_payment_status;

                if ($statusFilter === 'paid') {
                    return $effectiveStatus === 'paid';
                } elseif ($statusFilter === 'unpaid') {
                    return $effectiveStatus === 'unpaid';
                } elseif ($statusFilter === 'partially_paid') {
                    return $effectiveStatus === 'partially_paid';
                } elseif ($statusFilter === 'completed') {
                    return $order->status === 'completed';
                } elseif ($statusFilter === 'overdue') {
                    return $effectiveStatus !== 'paid' && $order->due_date && $order->due_date->isPast();
                } elseif ($statusFilter === 'cancelled') {
                    return $order->status === 'cancelled';
                }
                return true;
            })->values();
        }

        // Search Filter (SO Number, Ref Number, or SI Number)
        if ($request->filled('search')) {
            $searchTerm = strtolower(trim($request->search));
            $allOrders = $allOrders->filter(function($order) use ($searchTerm) {
                $soMatch = str_contains(strtolower($order->so_number ?? ''), $searchTerm);
                $refMatch = str_contains(strtolower($order->ref_number ?? ''), $searchTerm);
                $siMatch = $order->invoice && str_contains(strtolower($order->invoice->si_number ?? ''), $searchTerm);
                return $soMatch || $refMatch || $siMatch;
            })->values();
        }

        $perPage = max(1, (int) $request->get('per_page', 10));
        $page = max(1, (int) $request->get('page', 1));

        $total = $allOrders->count();
        $sliced = $allOrders->slice(($page - 1) * $perPage, $perPage)->values();

        $history = $sliced->map(function($order) {
            $totalAmount = (float)($order->total_amount ?? 0);
            $paidAmount = (float)$order->total_paid_amount;
            $remainingBalance = (float)$order->remaining_balance;
            $effectivePaymentStatus = $order->computed_payment_status;
            $isPaid = $effectivePaymentStatus === 'paid';
            $hasProof = !empty($order->proof_of_payment);
            $isOverdue = !$isPaid && $order->due_date && $order->due_date->isPast();

            return [
                'id' => $order->id,
                'so_number' => $order->so_number,
                'si_number' => $order->invoice ? $order->invoice->si_number : null,
                'date' => $order->created_at ? $order->created_at->format('Y-m-d') : 'N/A',
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'remaining_balance' => $remainingBalance,
                'payment_status' => $effectivePaymentStatus,
                'raw_payment_status' => $order->payment_status ?? 'unpaid',
                'has_proof_of_payment' => $hasProof,
                'proof_of_payment_url' => $hasProof ? asset('storage/' . $order->proof_of_payment) : null,
                'status' => $order->status ?? 'pending',
                'status_label' => ucfirst(str_replace('_', ' ', $order->status ?? 'pending')),
                'due_date' => $order->due_date ? $order->due_date->format('Y-m-d') : 'N/A',
                'is_overdue' => $isOverdue,
            ];
        });

        $lastPage = max(1, (int) ceil($total / $perPage));

        return response()->json([
            'customer_name' => $customer->customer_name,
            'balance' => (float)$customer->balance,
            'is_bad_client' => $customer->is_bad_client,
            'manual_status' => $customer->manual_status,
            'history' => $history,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => $lastPage,
                'from' => $total > 0 ? (($page - 1) * $perPage) + 1 : 0,
                'to' => min($page * $perPage, $total),
            ]
        ]);
    }

    /**
     * Record a partial or full payment for a customer sales order
     */
    public function recordPayment(Request $request, Customer $customer, SalesOrder $salesOrder)
    {
        $remainingBalance = (float) $salesOrder->remaining_balance;

        if ($remainingBalance <= 0) {
            return response()->json(['message' => 'This order is already fully paid.'], 422);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $remainingBalance,
            'payment_method' => 'required|string|in:cash,gcash,maya,bank_transfer,check,card',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:255',
            'proof_of_payment' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $amountPaid = (float) $validated['amount'];

        $proofPath = null;
        if ($request->hasFile('proof_of_payment')) {
            $proofPath = $request->file('proof_of_payment')->store('proof_of_payments', 'public');
        }

        // Create Payment record
        Payment::create([
            'customer_id' => $customer->customer_id,
            'sales_order_id' => $salesOrder->id,
            'amount' => $amountPaid,
            'payment_method' => $validated['payment_method'],
            'payment_date' => now()->toDateString(),
            'status' => 'verified',
            'reference_number' => $validated['reference_number'] ?? null,
            'verified_by' => auth()->id(),
            'notes' => $validated['notes'] ?? ("Installment payment for " . $salesOrder->so_number),
            'proof_of_payment' => $proofPath,
        ]);

        // Update sales order proof_of_payment if not set
        if ($proofPath && empty($salesOrder->proof_of_payment)) {
            $salesOrder->update(['proof_of_payment' => $proofPath]);
        }

        // Recalculate remaining balance
        $salesOrder->unsetRelation('payments');
        $newRemaining = (float) $salesOrder->remaining_balance;

        if ($newRemaining <= 0) {
            $salesOrder->update(['payment_status' => 'paid']);
        } else {
            $salesOrder->update(['payment_status' => 'partially_paid']);
        }

        // Log Activity
        if (class_exists('\App\Models\ActivityLog')) {
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id() ?: 1,
                'action' => 'Payment Recorded',
                'description' => "Recorded payment of ₱" . number_format($amountPaid, 2) . " for SO {$salesOrder->so_number} (Customer: {$customer->customer_name}). Remaining balance: ₱" . number_format($newRemaining, 2),
                'affected_model' => 'SalesOrder',
                'affected_model_id' => $salesOrder->id,
                'ip_address' => $request->ip(),
            ]);
        }

        return response()->json([
            'message' => 'Payment recorded successfully!',
            'paid_amount' => $amountPaid,
            'remaining_balance' => $newRemaining,
            'new_payment_status' => $salesOrder->fresh()->computed_payment_status,
            'new_customer_balance' => (float) $customer->fresh()->balance,
        ]);
    }

    /**
     * Change customer manual status (Super Admin only)
     */
    public function updateManualStatus(Request $request, Customer $customer)
    {
        // Super Admin or Marketing with permission can change customer status
        $user = auth()->user();
        if (!($user->isSuperAdmin() || ($user->hasPermission('marketing.customers')))) {
            return response()->json(['message' => 'Unauthorized action. Only Super Admin or Marketing can change customer status.'], 403);
        }

        $validated = $request->validate([
            'manual_status' => 'nullable|in:good,bad',
        ]);

        $customer->update(['manual_status' => $validated['manual_status']]);

        return response()->json(['message' => 'Customer status updated successfully']);
    }

    /**
     * Get payment history breakdown for a customer sales order
     */
    public function getPaymentHistory(Customer $customer, SalesOrder $salesOrder)
    {
        $payments = $salesOrder->payments()->with('verifiedBy')->latest()->get();

        $formattedPayments = $payments->map(function($payment) {
            $hasProof = !empty($payment->proof_of_payment);
            return [
                'id' => $payment->id,
                'date' => $payment->payment_date ? $payment->payment_date->format('M d, Y') : ($payment->created_at ? $payment->created_at->format('M d, Y') : 'N/A'),
                'amount' => (float) $payment->amount,
                'method' => ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'cash')),
                'reference_number' => $payment->reference_number ?: 'N/A',
                'notes' => $payment->notes ?: 'N/A',
                'has_proof' => $hasProof,
                'proof_url' => $hasProof ? asset('storage/' . $payment->proof_of_payment) : null,
                'recorded_by' => $payment->verifiedBy->name ?? 'System',
            ];
        });

        return response()->json([
            'so_number' => $salesOrder->so_number,
            'total_amount' => (float) $salesOrder->total_amount,
            'total_paid' => (float) $salesOrder->total_paid_amount,
            'remaining_balance' => (float) $salesOrder->remaining_balance,
            'payment_status' => $salesOrder->computed_payment_status,
            'payments' => $formattedPayments,
        ]);
    }
}

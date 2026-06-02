<?php

namespace App\Http\Controllers;

use App\Models\Customer;
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
        $customers = Customer::orderBy('customer_name', 'asc')->get();
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
    public function getTransactionHistory(Customer $customer)
    {
        $history = $customer->salesOrders()
            ->latest()
            ->get()
            ->map(function($order) {
                return [
                    'so_number' => $order->so_number,
                    'date' => $order->created_at->format('Y-m-d'),
                    'total_amount' => (float)$order->total_amount,
                    'payment_status' => $order->payment_status,
                    'status' => $order->status,
                    'due_date' => $order->due_date->format('Y-m-d'),
                    'is_overdue' => $order->payment_status === 'unpaid' && $order->due_date->isPast(),
                ];
            });

        return response()->json([
            'customer_name' => $customer->customer_name,
            'balance' => (float)$customer->balance,
            'is_bad_client' => $customer->is_bad_client,
            'history' => $history
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
}

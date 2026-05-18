<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductionController extends Controller
{
    public function dashboard()
    {
        return view('production.dashboard', [
            'title' => 'Production Dashboard',
            'role' => auth()->user()->position,
            'sidebar' => 'production'
        ]);
    }

    public function approvalQueue()
    {
        $user = auth()->user();
        $pos = $user->position;

        // 1. Pending Production Approvals
        // Currently, only Foreign Sales Orders go to Production for approval
        $salesOrders = \App\Models\SalesOrder::with('customer', 'preparedBy')
            ->where('status', 'pending_prod_approval')
            ->latest()
            ->get();

        // 2. Pending Cash Advances (Production Specific Authorization)
        $authorizedPositions = ['DTO Supervisor', 'Senior Ford Staff', 'Senior Logistics Staff'];
        $isAuthorized = (str_contains($pos, 'Manager') && str_contains($user->division, 'Production')) || 
                        in_array($pos, $authorizedPositions) || 
                        $pos === 'Super Admin';
        
        $pendingCashAdvances = $isAuthorized
            ? \App\Models\EmployeeCashAdvance::where('status', 'pending_supervisor_approval')
                ->where('department_source', 'Production')
                ->latest()
                ->get()
            : collect();

        // 3. Unified Listing for "My Approvals" tab
        $myApprovals = [];
        
        foreach ($salesOrders as $so) {
            $myApprovals[] = [
                'type' => 'Sales Order',
                'id' => $so->id,
                'reference_no' => $so->so_number,
                'submitted_by' => $so->preparedBy->name ?? 'N/A',
                'submitted_date' => $so->created_at,
                'amount' => '₱' . number_format($so->total_amount, 2),
                'attachment' => null,
                'status' => 'pending approval',
                'url' => route('production.sales-order.detail', $so->id),
                'original' => $so
            ];
        }

        foreach ($pendingCashAdvances as $ca) {
            $myApprovals[] = [
                'type' => 'Cash Advance',
                'id' => $ca->id,
                'reference_no' => 'CA-' . str_pad($ca->id, 5, '0', STR_PAD_LEFT),
                'submitted_by' => $ca->employee_name,
                'submitted_date' => $ca->created_at,
                'amount' => '₱' . number_format($ca->amount, 2),
                'attachment' => null,
                'status' => 'pending approval',
                'department' => $ca->department,
                'description' => $ca->purpose,
                'original' => $ca
            ];
        }

        // 4. My Submissions
        $mySubmissions = collect();
        
        $soSubmissions = \App\Models\SalesOrder::with('customer', 'preparedBy')
            ->where('prepared_by', auth()->id())
            ->latest()
            ->get();
        
        foreach ($soSubmissions as $so) {
            $mySubmissions->push((object)[
                'type' => 'Sales Order',
                'id' => $so->id,
                'reference_no' => $so->so_number,
                'prep_name' => $so->preparedBy->name ?? 'N/A',
                'submitted_date' => $so->created_at,
                'amount' => '₱' . number_format($so->total_amount, 2),
                'status' => $so->status,
                'url' => route('production.sales-order.detail', $so->id),
                'original' => $so
            ]);
        }

        $caSubmissions = \App\Models\EmployeeCashAdvance::where('user_id', auth()->id())
            ->latest()
            ->get();

        foreach ($caSubmissions as $ca) {
            $mySubmissions->push((object)[
                'type' => 'Cash Advance',
                'id' => $ca->id,
                'reference_no' => 'CA-' . str_pad($ca->id, 5, '0', STR_PAD_LEFT),
                'prep_name' => auth()->user()->name,
                'submitted_date' => $ca->created_at,
                'amount' => '₱' . number_format($ca->amount, 2),
                'status' => $ca->status,
                'original' => $ca
            ]);
        }

        // 5. My Approved Requests (Requests this manager has already approved)
        $caApproved = \App\Models\EmployeeCashAdvance::where('approved_by_manager', auth()->id())
            ->latest()
            ->get();
        
        $myApprovedRequests = collect();
        foreach($caApproved as $ca) {
            $myApprovedRequests->push((object)[
                'type' => 'Cash Advance',
                'id' => $ca->id,
                'reference_no' => 'CA-' . str_pad($ca->id, 5, '0', STR_PAD_LEFT),
                'submitted_by' => $ca->employee_name,
                'submitted_date' => $ca->created_at,
                'amount' => '₱' . number_format($ca->amount, 2),
                'status' => $ca->status,
                'original' => $ca
            ]);
        }

        return view('production.approval-queue', [
            'title' => 'Approval Queue',
            'role' => 'Production Manager',
            'sidebar' => 'production',
            'salesOrders' => $salesOrders,
            'pendingCashAdvances' => $pendingCashAdvances,
            'myApprovals' => collect($myApprovals)->sortByDesc('submitted_date'),
            'mySubmissions' => $mySubmissions->sortByDesc('submitted_date'),
            'myApprovedRequests' => $myApprovedRequests->sortByDesc('submitted_date')
        ]);
    }

    public function myRequests()
    {
        $cashAdvances = \App\Models\EmployeeCashAdvance::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('production.my-requests.index', [
            'title' => '',
            'role' => auth()->user()->position,
            'sidebar' => 'production',
            'cashAdvances' => $cashAdvances
        ]);
    }

    public function reviewSalesOrder($id)
    {
        $order = \App\Models\SalesOrder::with('customer', 'items.product', 'preparedBy')->findOrFail($id);

        return view('production.sales-orders.review', [
            'title' => 'Review Sales Order',
            'role' => 'Production Manager',
            'sidebar' => 'production',
            'order' => $order
        ]);
    }

    public function approveSalesOrder(Request $request, $id)
    {
        // Role Enforcement: Production Manager
        if (!str_contains(auth()->user()->position, 'Manager')) {
            return redirect()->back()->with('error', 'Only Production Managers can approve Sales Orders.');
        }

        $order = \App\Models\SalesOrder::findOrFail($id);
        
        // After Production approval, it goes to Logistics for Picking
        $order->update([
            'status' => 'picking',
            'approved_by_prod' => auth()->id(),
            'prod_approved_at' => now()
        ]);

        return redirect()->route('production.approval-queue')->with('success', 'Sales Order #' . $order->so_number . ' has been approved and sent to Logistics for picking.');
    }

    public function rejectSalesOrder(Request $request, $id)
    {
        $order = \App\Models\SalesOrder::findOrFail($id);
        $order->update([
            'status' => 'cancelled',
            'remarks' => $request->remarks . ' (Rejected by Production)'
        ]);

        return redirect()->route('production.approval-queue')->with('warning', 'Sales Order #' . $order->so_number . ' has been rejected.');
    }
}

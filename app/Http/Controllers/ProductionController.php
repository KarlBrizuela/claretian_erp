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
        $isProductionApprover = (str_contains($pos, 'Manager') || str_contains($pos, 'Supervisor'))
                        && \App\Models\StockTransfer::approvalDivisionForUser($user) === 'Production';

        $isAuthorized = $isProductionApprover ||
                        in_array($pos, $authorizedPositions) || 
                        $pos === 'Super Admin';
        
        $pendingCashAdvances = $isAuthorized
            ? \App\Models\EmployeeCashAdvance::where('status', 'pending_supervisor_approval')
                ->where('department_source', 'Production')
                ->latest()
                ->get()
            : collect();

        $pendingMaterials = $isAuthorized
            ? \App\Models\Admin\MIS\MaterialReq::with('user')
                ->where('status', 'pending_supervisor_approval')
                ->get()
                ->filter(function ($request) use ($user) {
                    return $request->canBeApprovedBy($user);
                })
            : collect();

        $pendingCctvRequests = $isAuthorized
            ? \App\Models\Admin\MIS\CCTVReq::with('user')
                ->where('status', 'pending approval')
                ->whereHas('user', function ($query) {
                    $query->where('division', 'like', '%Production%')
                        ->orWhereHas('divisions', function ($divisionQuery) {
                            $divisionQuery->where('division', 'like', '%Production%');
                        });
                })
                ->latest()
                ->get()
            : collect();

        $pendingTransfers = $isAuthorized
            ? \App\Models\StockTransfer::with('fromSite', 'toSite', 'book', 'bookIndex.book', 'bookBundle', 'createdBy', 'logisticsAssignedTo')
                ->whereIn('status', ['logistics_assignment', 'logistics_assigned', 'completed'])
                ->where(function ($query) use ($user) {
                    $query->where('created_by', $user->id)
                        ->orWhere('approval_division', 'Production')
                        ->orWhere(function ($legacyQuery) {
                            $legacyQuery->whereNull('approval_division')
                                ->whereHas('createdBy', function ($creatorQuery) {
                                    $creatorQuery->where('division', 'like', '%Production%')
                                        ->orWhere('position', 'like', '%Logistic%')
                                        ->orWhereHas('divisions', function ($divisionQuery) {
                                            $divisionQuery->where('division', 'like', '%Production%');
                                        });
                                });
                        });
                })
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

        foreach ($pendingMaterials as $req) {
            $myApprovals[] = [
                'type' => 'Material',
                'id' => $req->material_req_id,
                'reference_no' => 'MAT-' . str_pad($req->material_req_id, 5, '0', STR_PAD_LEFT),
                'submitted_by' => $req->user->name ?? $req->requested_by,
                'submitted_date' => $req->created_at,
                'amount' => '₱' . number_format($req->amount, 2),
                'attachment' => null,
                'status' => $req->status,
                'department' => $req->user->department ?? 'N/A',
                'description' => $req->request_details,
                'original' => $req
            ];
        }

        foreach ($pendingCctvRequests as $req) {
            $myApprovals[] = [
                'type' => 'CCTV',
                'id' => $req->cctv_req_id,
                'reference_no' => 'CCTV-' . str_pad($req->cctv_req_id, 4, '0', STR_PAD_LEFT),
                'submitted_by' => $req->user->name ?? $req->requested_by,
                'submitted_date' => $req->created_at,
                'amount' => 'N/A',
                'attachment' => $req->attachment,
                'status' => $req->status,
                'department' => $req->department,
                'description' => $req->purpose,
                'original' => $req
            ];
        }

        foreach ($pendingTransfers as $transfer) {
            $myApprovals[] = [
                'type' => 'Stock Transfer',
                'id' => $transfer->id,
                'reference_no' => 'ST-' . str_pad($transfer->id, 5, '0', STR_PAD_LEFT),
                'submitted_by' => $transfer->createdBy->name ?? 'N/A',
                'submitted_date' => $transfer->created_at,
                'amount' => $transfer->quantity . ' units',
                'attachment' => null,
                'status' => 'pending approval',
                'description' => ($transfer->book->name ?? 'Unknown Book') . ' from ' . ($transfer->fromSite->name ?? 'N/A') . ' to ' . ($transfer->toSite->name ?? 'N/A'),
                'original' => $transfer
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

        $materialSubmissions = \App\Models\Admin\MIS\MaterialReq::where('user_id', auth()->id())
            ->latest()
            ->get();

        foreach ($materialSubmissions as $req) {
            $mySubmissions->push((object)[
                'type' => 'Material',
                'id' => $req->material_req_id,
                'reference_no' => 'MAT-' . str_pad($req->material_req_id, 5, '0', STR_PAD_LEFT),
                'prep_name' => auth()->user()->name,
                'submitted_date' => $req->created_at,
                'amount' => '₱' . number_format($req->amount, 2),
                'status' => $req->status,
                'original' => $req
            ]);
        }

        // 5. My Approved Requests (Requests this manager has already approved)
        $caApproved = \App\Models\EmployeeCashAdvance::where('approved_by_manager', auth()->id())
            ->latest()
            ->get();
        
        $materialApproved = \App\Models\Admin\MIS\MaterialReq::where('approved_by_manager', auth()->id())
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

        foreach ($materialApproved as $req) {
            $myApprovedRequests->push((object)[
                'type' => 'Material',
                'id' => $req->material_req_id,
                'reference_no' => 'MAT-' . str_pad($req->material_req_id, 5, '0', STR_PAD_LEFT),
                'submitted_by' => $req->user->name ?? $req->requested_by,
                'submitted_date' => $req->created_at,
                'amount' => '₱' . number_format($req->amount, 2),
                'status' => $req->status,
                'original' => $req
            ]);
        }

        return view('production.approval-queue', [
            'title' => 'Approval Queue',
            'role' => 'Production Manager',
            'sidebar' => 'production',
            'salesOrders' => $salesOrders,
            'pendingCashAdvances' => $pendingCashAdvances,
            'pendingTransfers' => $pendingTransfers,
            'pendingCctvRequests' => $pendingCctvRequests,
            'pendingMaterials' => $pendingMaterials,
            'myApprovals' => collect($myApprovals)->sortByDesc('submitted_date'),
            'mySubmissions' => $mySubmissions->sortByDesc('submitted_date'),
            'myApprovedRequests' => $myApprovedRequests->sortByDesc('submitted_date'),
            'logisticsUsers' => \App\Models\User::where('position', 'like', '%Logistic%')
                ->where('status', true)
                ->orderBy('first_name')
                ->get(),
            'isLogisticsAssigner' => ($user && ($user->isSuperAdmin() || (str_contains(strtolower($user->position ?? ''), 'logistic') && (str_contains(strtolower($user->position ?? ''), 'manager') || str_contains(strtolower($user->position ?? ''), 'supervisor') || str_contains(strtolower($user->position ?? ''), 'senior')))))
        ]);
    }

    public function myRequests()
    {
        $cashAdvances = \App\Models\EmployeeCashAdvance::where('user_id', auth()->id())
            ->latest()
            ->get();
        $materialRequests = \App\Models\Admin\MIS\MaterialReq::where('user_id', auth()->id())
            ->latest()
            ->get();
        $cctvRequests = \App\Models\Admin\MIS\CCTVReq::where('user_id', auth()->id())
            ->latest()
            ->get();

        $mergedRequests = $cashAdvances->concat($materialRequests)->sortByDesc('created_at');

        return view('production.my-requests.index', [
            'title' => '',
            'role' => auth()->user()->position,
            'sidebar' => 'production',
            'cashAdvances' => $mergedRequests,
            'cctvRequests' => $cctvRequests,
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

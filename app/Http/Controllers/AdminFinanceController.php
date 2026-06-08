<?php

namespace App\Http\Controllers;

use App\Models\Admin\MIS\CCTVReq;
use App\Models\Admin\MIS\MaterialReq;
use App\Models\Admin\MIS\MisQbRequest;
use App\Models\Admin\MIS\MisServiceRequest;
use App\Models\Admin\MIS\MisUndertimeRequest;
use Illuminate\Http\Request;
use App\Services\AccountingService;
use App\Models\JournalEntry;
use App\Models\EmployeeCashAdvance;
use App\Models\PettyCashVoucher;
use App\Models\PettyCashVoucherItem;
use App\Models\JournalEntryItem;
use App\Models\SalesOrder;
use App\Models\User;
use App\Models\JournalVoucherRequest;
use App\Models\JournalVoucherItem;
use App\Models\Customer;

class AdminFinanceController extends Controller
{
  protected $accounting;

  public function __construct(AccountingService $accounting)
  {
    $this->accounting = $accounting;
  }

  public function memoList()
  {
    $memos = collect();

    // 1. Journal Entries (including Check Vouchers)
    $journalEntries = JournalEntry::whereNotNull('memo')->with('creator')->get();
    foreach ($journalEntries as $entry) {
      $memos->push([
        'date' => $entry->date,
        'source' => $entry->entry_type ?? 'Journal Entry',
        'ref_no' => $entry->entry_no,
        'id' => $entry->id,
        'memo' => $entry->memo,
        'submitted_by' => $entry->creator->name ?? 'Unknown',
        'url' => ($entry->entry_type == 'CV') ? route('admin-finance.check-voucher.show', $entry->id) : route('accounting.journal.show', $entry->id),
      ]);
    }

    // 2. Journal Entry Items (some memos are on line items)
    $journalItems = JournalEntryItem::whereNotNull('memo')->with(['journalEntry.creator'])->get();
    foreach ($journalItems as $item) {
      if ($item->journalEntry) {
        $memos->push([
          'date' => $item->journalEntry->date,
          'source' => ($item->journalEntry->entry_type ?? 'Journal Entry') . ' (Item)',
          'ref_no' => $item->journalEntry->entry_no,
          'id' => $item->journalEntry->id,
          'memo' => $item->memo,
          'submitted_by' => $item->journalEntry->creator->name ?? 'Unknown',
          'url' => ($item->journalEntry->entry_type == 'CV') ? route('admin-finance.check-voucher.show', $item->journalEntry->id) : route('accounting.journal.show', $item->journalEntry->id),
        ]);
      }
    }

    // 3. Petty Cash Vouchers (Using items' particulars as memos)
    $pettyItems = PettyCashVoucherItem::with(['voucher.creator'])->get();
    foreach ($pettyItems as $item) {
      if ($item->voucher && $item->particulars) {
        $memos->push([
          'date' => $item->voucher->date,
          'source' => 'Petty Cash',
          'ref_no' => $item->voucher->pcv_number,
          'id' => $item->voucher->id,
          'memo' => $item->particulars,
          'submitted_by' => $item->voucher->creator->name ?? 'Unknown',
          'url' => route('admin-finance.petty-cash.show', $item->voucher->id),
        ]);
      }
    }

    // 4. Employee Cash Advances
    $cashAdvances = EmployeeCashAdvance::whereNotNull('purpose')->with('user')->get();
    foreach ($cashAdvances as $ca) {
      $memos->push([
        'date' => $ca->created_at,
        'source' => 'Cash Advance',
        'ref_no' => 'CA-' . str_pad($ca->id, 4, '0', STR_PAD_LEFT),
        'id' => $ca->id,
        'memo' => $ca->purpose,
        'submitted_by' => $ca->employee_name ?? ($ca->user->name ?? 'Unknown'),
        'url' => '#', // No specific show page found for CA, but often in approval queue
      ]);
    }

    // 5. MIS Requests
    $cctv = CCTVReq::whereNotNull('purpose')->with('user')->get();
    foreach ($cctv as $req) {
      $memos->push([
        'date' => $req->created_at,
        'source' => 'CCTV Request',
        'ref_no' => 'CCTV-' . str_pad($req->cctv_req_id, 4, '0', STR_PAD_LEFT),
        'id' => $req->cctv_req_id,
        'memo' => $req->purpose,
        'submitted_by' => $req->requested_by ?? ($req->user->name ?? 'Unknown'),
        'url' => route('admin-finance.mis.cctv-requests.show', $req->cctv_req_id),
      ]);
    }

    $materials = MaterialReq::whereNotNull('request_details')->with('user')->get();
    foreach ($materials as $req) {
      $memos->push([
        'date' => $req->created_at,
        'source' => 'Material Request',
        'ref_no' => 'MAT-' . str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT),
        'id' => $req->material_req_id,
        'memo' => $req->request_details,
        'submitted_by' => $req->requested_by ?? ($req->user->name ?? 'Unknown'),
        'url' => route('admin-finance.mis.material-requests.show', $req->material_req_id),
      ]);
    }

    $qbs = MisQbRequest::whereNotNull('customer_item_name')->with('user')->get();
    foreach ($qbs as $req) {
      $memos->push([
        'date' => $req->created_at,
        'source' => 'QB Request',
        'ref_no' => 'QB-' . str_pad($req->qb_req_id, 4, '0', STR_PAD_LEFT),
        'id' => $req->qb_req_id,
        'memo' => $req->customer_item_name,
        'submitted_by' => $req->user->name ?? 'Unknown',
        'url' => route('admin-finance.mis.qb-requests.show', $req->qb_req_id),
      ]);
    }

    $services = MisServiceRequest::whereNotNull('nature_of_request')->with('user')->get();
    foreach ($services as $req) {
      $memos->push([
        'date' => $req->created_at,
        'source' => 'Service Request',
        'ref_no' => 'SRV-' . str_pad($req->service_req_id, 4, '0', STR_PAD_LEFT),
        'id' => $req->service_req_id,
        'memo' => $req->nature_of_request,
        'submitted_by' => $req->requestor_name ?? ($req->user->name ?? 'Unknown'),
        'url' => route('admin-finance.mis.service-requests.show', $req->service_req_id),
      ]);
    }

    $undertimes = MisUndertimeRequest::whereNotNull('reason')->with('user')->get();
    foreach ($undertimes as $req) {
      $memos->push([
        'date' => $req->created_at,
        'source' => 'Undertime Request',
        'ref_no' => 'UND-' . str_pad($req->undertime_req_id, 4, '0', STR_PAD_LEFT),
        'id' => $req->undertime_req_id,
        'memo' => $req->reason,
        'submitted_by' => $req->employee_name ?? ($req->user->name ?? 'Unknown'),
        'url' => route('admin-finance.mis.undertime-requests.show', $req->undertime_req_id),
      ]);
    }

    // 6. Sales Orders
    $salesOrders = SalesOrder::whereNotNull('remarks')->with('preparedBy')->get();
    foreach ($salesOrders as $so) {
      $memos->push([
        'date' => $so->created_at,
        'source' => 'Sales Order',
        'ref_no' => $so->so_number,
        'id' => $so->id,
        'memo' => $so->remarks,
        'submitted_by' => $so->preparedBy->name ?? 'Unknown',
        'url' => route('admin-finance.sales-order.detail', $so->id),
      ]);
    }

    // 7. Journal Voucher Requests
    $jvMemos = JournalVoucherRequest::with('requestor')->latest()->get();
    foreach ($jvMemos as $req) {
        $memos->push([
            'date' => $req->date ?? $req->created_at,
            'source' => 'JV Request',
            'ref_no' => $req->jv_number,
            'id' => $req->id,
            'memo' => $req->reason ?? 'Initial Summary Compilation Request',
            'submitted_by' => $req->requestor->name ?? 'Unknown',
            'url' => route('admin-finance.credit-collection.jv-requests.show', $req->id),
        ]);
    }

    // Sort by date descending
    $memos = $memos->sortByDesc('date');

    return view('admin-finance.memo-list', [
      'title' => 'Memo List',
      'role' => auth()->user()->position,
      'sidebar' => 'admin-finance',
      'memos' => $memos
    ]);
  }
  public function dashboard(\Illuminate\Http\Request $request)
  {
    // Determine period from query (daily, weekly, monthly, yearly)
    $period = $request->query('period', 'monthly');
    switch($period) {
      case 'daily':
        $start = \Carbon\Carbon::now()->startOfDay();
        $end = \Carbon\Carbon::now()->endOfDay();
        $periodLabel = 'Today';
        break;
      case 'weekly':
        $start = \Carbon\Carbon::now()->startOfWeek();
        $end = \Carbon\Carbon::now()->endOfWeek();
        $periodLabel = 'This Week';
        break;
      case 'yearly':
        $start = \Carbon\Carbon::now()->startOfYear();
        $end = \Carbon\Carbon::now()->endOfYear();
        $periodLabel = 'This Year';
        break;
      case 'monthly':
      default:
        $start = \Carbon\Carbon::now()->startOfMonth();
        $end = \Carbon\Carbon::now()->endOfMonth();
        $periodLabel = 'This Month';
        break;
    }

    // Financial summaries within the selected period
    $totalRevenue = \App\Models\SalesOrder::where('payment_status', 'paid')
      ->whereBetween('created_at', [$start, $end])
      ->sum('total_amount');

    $accountsReceivable = \App\Models\SalesOrder::where('payment_status', 'unpaid')
      ->where('status', '!=', 'cancelled')
      ->whereBetween('created_at', [$start, $end])
      ->sum('total_amount');

    // Expenses: sum debits for journal items whose account type is 'Expense' within period
    $totalExpenses = \App\Models\JournalEntryItem::whereHas('account', function($q){
      $q->where('type', 'Expense');
    })->whereBetween('created_at', [$start, $end])->sum('debit');

    $netProfit = $totalRevenue - $totalExpenses;

    // Admin & Finance: counts grouped by position (for sidebar summary)
    $deptCounts = \App\Models\User::select('position', \DB::raw('count(*) as cnt'))
      ->where('department', 'Admin & Finance')
      ->groupBy('position')
      ->get()
      ->keyBy('position');

    // Pending approvals: reflect the Departmental queue from gatherApprovalQueueItems()
    $user = auth()->user();
    $allPending = $this->gatherApprovalQueueItems($user);
    $userDept = strtolower($user->department ?? '');

    $filtered = array_filter($allPending, function($it) use ($userDept) {
      $dept = strtolower($it['department'] ?? '');
      if (empty($userDept)) return true;
      if (empty($dept)) return false;
      if (\Illuminate\Support\Str::contains($dept, $userDept) || \Illuminate\Support\Str::contains($userDept, $dept)) return true;
      // for Admin & Finance, include related departments
      if (\Illuminate\Support\Str::contains($userDept, 'admin') || \Illuminate\Support\Str::contains($userDept, 'finance')) {
        if (\Illuminate\Support\Str::contains($dept, 'admin') || \Illuminate\Support\Str::contains($dept, 'finance') || \Illuminate\Support\Str::contains($dept, 'credit') || \Illuminate\Support\Str::contains($dept, 'account')) return true;
      }
      return false;
    });

    $pending = collect($filtered)->map(function($it){
      return [
        'type' => $it['type'] ?? '',
        'submitted_by' => $it['submitted_by'] ?? '',
        'department' => $it['department'] ?? 'N/A',
        'amount' => isset($it['amount']) ? $it['amount'] : 0,
        'date' => $it['submitted_date'] ?? ($it['date'] ?? now()->toDateString()),
        'status' => $it['status'] ?? '',
        'id' => $it['id'] ?? null
      ];
    })->sortByDesc(function($row){
      return strtotime($row['date']);
    })->values()->take(6);

    // Build 7-point chart series (last 7 units according to period)
    $chartCategories = [];
    $chartRevenue = [];
    $chartExpenses = [];
    for ($i = 6; $i >= 0; $i--) {
      switch ($period) {
        case 'daily':
          $dt = \Carbon\Carbon::now()->subDays($i);
          $label = $dt->format('M j');
          $s = $dt->copy()->startOfDay();
          $e = $dt->copy()->endOfDay();
          break;
        case 'weekly':
          $dt = \Carbon\Carbon::now()->subWeeks($i);
          $label = 'W ' . $dt->weekOfYear;
          $s = $dt->copy()->startOfWeek();
          $e = $dt->copy()->endOfWeek();
          break;
        case 'yearly':
          $dt = \Carbon\Carbon::now()->subYears($i);
          $label = $dt->format('Y');
          $s = $dt->copy()->startOfYear();
          $e = $dt->copy()->endOfYear();
          break;
        case 'monthly':
        default:
          $dt = \Carbon\Carbon::now()->subMonths($i);
          $label = $dt->format('M');
          $s = $dt->copy()->startOfMonth();
          $e = $dt->copy()->endOfMonth();
          break;
      }

      $chartCategories[] = $label;

      $rev = \App\Models\SalesOrder::where('payment_status', 'paid')
        ->whereBetween('created_at', [$s, $e])
        ->sum('total_amount');

      $exp = \App\Models\JournalEntryItem::whereHas('account', function($q){
          $q->where('type', 'Expense');
        })->whereBetween('created_at', [$s, $e])->sum('debit');

      $chartRevenue[] = (float) $rev;
      $chartExpenses[] = (float) $exp;
    }

    // Admin & Finance division headcount summary
    $totalUsers = \App\Models\User::where('division', 'Admin & Finance Division')->count();
    $positionSummary = \App\Models\User::selectRaw('position, COUNT(*) as user_count')
        ->where('division', 'Admin & Finance Division')
        ->whereNotNull('position')
        ->where('position', '!=', '')
        ->groupBy('position')
        ->get()
        ->map(function($item) use ($totalUsers) {
            $percentage = $totalUsers > 0 ? round(($item->user_count / $totalUsers) * 100) : 0;
            return [
                'name' => $item->position,
                'count' => $item->user_count,
                'percentage' => $percentage
            ];
        });

    return view('admin-finance.dashboard', [
      'title' => 'Admin & Finance Dashboard',
      'role' => auth()->user()->position,
      'sidebar' => 'admin-finance',
      'totalRevenue' => $totalRevenue,
      'accountsReceivable' => $accountsReceivable,
      'totalExpenses' => $totalExpenses,
      'netProfit' => $netProfit,
      'deptCounts' => $deptCounts,
      'pendingApprovals' => $pending,
      'period' => $period,
      'periodLabel' => $periodLabel,
      'chartCategories' => $chartCategories,
      'chartRevenue' => $chartRevenue,
      'chartExpenses' => $chartExpenses,
      'positionSummary' => $positionSummary,
      'totalDivisionUsers' => $totalUsers
    ]);
  }

  /**
   * Build unified approval queue items for a user (reusable)
   */
  private function gatherApprovalQueueItems($user)
  {
    $pos = $user->position;

    // --- CCTV Requests ---
    $cctvQuery = CCTVReq::query();
    if ($pos === 'Manager') {
      $cctvQuery->whereIn('status', ['pending approval', 'Pending Final Approval']);
    } elseif ($pos === 'MIS Supervisor') {
      $cctvQuery->where('status', 'pending approval');
    } elseif (in_array($pos, ['HR Manager', 'HR Specialist', 'HR Staff'])) {
      $cctvQuery->where('status', 'Pending HR approval');
    } elseif ($pos === 'Director') {
      $cctvQuery->whereRaw('1 = 0');
    } elseif ($pos === 'Super Admin') {
      $cctvQuery->whereIn('status', ['pending approval', 'Pending HR approval', 'Pending Final Approval']);
    } else {
      $cctvQuery->whereRaw('1 = 0');
    }
    $cctvRequests = $cctvQuery->orderBy('created_at', 'desc')->get();

    // --- Material Requests ---
    $materialQuery = MaterialReq::query();
    if (in_array($pos, ['Manager', 'MIS Supervisor'])) {
      $materialQuery->where('status', 'pending approval');
    } elseif ($pos === 'Director') {
      $materialQuery->where('status', 'Pending Final Approval');
    } elseif ($pos === 'Super Admin') {
      $materialQuery->whereIn('status', ['pending approval', 'Pending Final Approval', 'forwarded to accounting', 'received']);
    } else {
      $materialQuery->whereRaw('1 = 0');
    }
    $materialRequests = $materialQuery->orderBy('created_at', 'desc')->get();

    // --- QB Requests ---
    $qbQuery = MisQbRequest::with(['user', 'items']);
    if (in_array($pos, ['Manager', 'MIS Supervisor'])) {
      $qbQuery->where('status', 'pending');
    } elseif (in_array($pos, ['HR Manager', 'HR Specialist', 'HR Staff'])) {
      $qbQuery->where('status', 'Pending HR approval');
    } elseif ($pos === 'Director') {
      $qbQuery->where('status', 'Pending Final Approval');
    } elseif ($pos === 'Super Admin') {
      $qbQuery->whereIn('status', ['pending', 'Pending HR approval', 'Pending Final Approval']);
    } else {
      $qbQuery->whereRaw('1 = 0');
    }
    $qbRequests = $qbQuery->orderBy('created_at', 'desc')->get();

    // --- Service Requests ---
    $serviceQuery = MisServiceRequest::query();
    if (in_array($pos, ['Manager', 'MIS Supervisor'])) {
      $serviceQuery->where('status', 'pending');
    } elseif (in_array($pos, ['HR Manager', 'HR Specialist', 'HR Staff'])) {
      $serviceQuery->where('status', 'Pending HR approval');
    } elseif ($pos === 'Director') {
      $serviceQuery->where('status', 'Pending Final Approval');
    } elseif ($pos === 'Super Admin') {
      $serviceQuery->whereIn('status', ['pending', 'Pending HR approval', 'Pending Final Approval']);
    } else {
      $serviceQuery->whereRaw('1 = 0');
    }
    $serviceRequests = $serviceQuery->orderBy('created_at', 'desc')->get();

    // --- Undertime Requests ---
    $undertimeQuery = MisUndertimeRequest::query();
    if (in_array($pos, ['Manager', 'MIS Supervisor'])) {
      $undertimeQuery->where('status', 'pending');
    } elseif (in_array($pos, ['HR Manager', 'HR Specialist', 'HR Staff'])) {
      $undertimeQuery->where('status', 'Pending HR approval');
    } elseif ($pos === 'Director') {
      $undertimeQuery->where('status', 'Pending Final Approval');
    } elseif ($pos === 'Super Admin') {
      $undertimeQuery->whereIn('status', ['pending', 'Pending HR approval', 'Pending Final Approval']);
    } else {
      $undertimeQuery->whereRaw('1 = 0');
    }
    $undertimeRequests = $undertimeQuery->orderBy('created_at', 'desc')->get();

    // Cash Advance Requests (Strict Role Filtering)
    $cashAdvanceQuery = \App\Models\EmployeeCashAdvance::query();
    $divisionMap = [
      'Admin & Finance Division' => 'Admin',
      'Marketing Division' => 'Marketing',
      'Production Division' => 'Production',
    ];
    $userMappedDivision = $divisionMap[$user->division] ?? $user->division;
    $isManager = str_contains($pos, 'Manager');
    $isAFManager = $isManager && (str_contains($user->division, 'Admin') || str_contains($user->division, 'Finance'));
    $isSuperAdmin = ($pos === 'Super Admin');
    if ($isAFManager) {
      $cashAdvanceQuery->where(function($q) use ($userMappedDivision) {
          $q->where(function($sub) use ($userMappedDivision) {
              $sub->where('status', 'pending_supervisor_approval')
                  ->where('department_source', $userMappedDivision);
          })->orWhere('status', 'pending_admin_approval');
      });
    } elseif ($isManager) {
      $cashAdvanceQuery->where('status', 'pending_supervisor_approval')
                       ->where('department_source', $userMappedDivision);
    } elseif ($pos === 'Director') {
      $cashAdvanceQuery->where('status', 'pending_director_approval');
    } elseif ($isSuperAdmin) {
      $cashAdvanceQuery->whereIn('status', ['pending_supervisor_approval', 'pending_admin_approval', 'pending_director_approval', 'approved', 'rejected']);
    } else {
      $cashAdvanceQuery->whereRaw('1 = 0');
    }
    $cashAdvanceRequests = $cashAdvanceQuery->orderBy('created_at', 'desc')->get();

    // JV Requests
    $jvPendingQuery = JournalVoucherRequest::with(['requestor', 'items']);
    if ($pos === 'Manager' || $pos === 'Super Admin') {
        $jvPendingQuery->whereIn('status', ['pending_accounting', 'pending_manager_approval', 'adjustment_prepared']);
    } else {
        $jvPendingQuery->where('status', 'pending_accounting');
    }
    $jvPending = $jvPendingQuery->orderBy('created_at', 'desc')->get();

    // Combine into a unified approval queue for the bottom card
    $pendingApprovals = [];
    foreach ($cctvRequests as $req) {
      $pendingApprovals[] = [
        'type' => 'CCTV',
        'id' => $req->cctv_req_id,
        'reference_no' => 'CCTV-' . str_pad($req->cctv_req_id, 4, '0', STR_PAD_LEFT),
        'submitted_by' => $req->requested_by,
        'submitted_date' => $req->created_at->format('M. d, Y'),
        'description' => \Illuminate\Support\Str::limit($req->purpose, 50),
        'full_description' => $req->purpose,
        'department' => $req->department,
        'status' => $req->status,
        'original' => $req
      ];
    }
    foreach ($materialRequests as $req) {
      $pendingApprovals[] = [
        'type' => 'Material',
        'id' => $req->material_req_id,
        'reference_no' => 'MAT-' . str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT),
        'submitted_by' => $req->requested_by,
        'submitted_date' => \Carbon\Carbon::parse($req->request_date)->format('M. d, Y'),
        'description' => \Illuminate\Support\Str::limit($req->request_details, 50),
        'full_description' => $req->request_details,
        'department' => 'N/A',
        'status' => $req->status,
        'original' => $req
      ];
    }
    foreach ($cashAdvanceRequests as $req) {
      $pendingApprovals[] = [
        'type' => 'Cash Advance',
        'id' => $req->id,
        'reference_no' => 'CA-' . str_pad($req->id, 4, '0', STR_PAD_LEFT),
        'submitted_by' => $req->employee_name,
        'submitted_date' => $req->created_at->format('M. d, Y'),
        'description' => \Illuminate\Support\Str::limit($req->purpose, 50),
        'full_description' => $req->purpose,
        'department' => $req->department,
        'status' => $req->status,
        'amount' => is_numeric($req->amount) ? (float)$req->amount : (float)0,
        'original' => $req
      ];
    }

    // Freight Bills (include amounts)
    $freightQuery = \App\Models\FreightBill::with('customer')->orderBy('created_at', 'desc');
    $freightRequests = $freightQuery->get();
    foreach ($freightRequests as $r) {
      $pendingApprovals[] = [
        'type' => 'Freight Bill',
        'id' => $r->id,
        'reference_no' => 'FB-' . str_pad($r->id, 4, '0', STR_PAD_LEFT),
        'submitted_by' => $r->customer->customer_name ?? 'N/A',
        'submitted_date' => $r->bill_date ?? ($r->created_at->format('M. d, Y')),
        'description' => \Illuminate\Support\Str::limit($r->remarks ?? '', 50),
        'full_description' => $r->remarks ?? '',
        'department' => 'Logistics',
        'status' => $r->status,
        'amount' => is_numeric($r->amount) ? (float)$r->amount : (float)0,
        'original' => $r
      ];
    }
    foreach ($qbRequests as $req) {
      $pendingApprovals[] = [
        'type' => 'QB',
        'id' => $req->qb_req_id,
        'reference_no' => 'QB-' . str_pad($req->qb_req_id, 4, '0', STR_PAD_LEFT),
        'submitted_by' => $req->user->name ?? 'Unknown',
        'submitted_date' => $req->created_at->format('M. d, Y'),
        'description' => \Illuminate\Support\Str::limit($req->customer_item_name, 50),
        'full_description' => "Customer Item: " . $req->customer_item_name,
        'department' => 'N/A',
        'status' => $req->status,
        'original' => $req
      ];
    }
    foreach ($serviceRequests as $req) {
      $pendingApprovals[] = [
        'type' => 'Service',
        'id' => $req->service_req_id,
        'reference_no' => 'SRV-' . str_pad($req->service_req_id, 4, '0', STR_PAD_LEFT),
        'submitted_by' => $req->requestor_name,
        'submitted_date' => \Carbon\Carbon::parse($req->date)->format('M. d, Y'),
        'description' => \Illuminate\Support\Str::limit($req->nature_of_request, 50),
        'full_description' => $req->nature_of_request,
        'department' => 'N/A',
        'status' => $req->status,
        'original' => $req
      ];
    }
    foreach ($undertimeRequests as $req) {
      $pendingApprovals[] = [
        'type' => 'Undertime',
        'id' => $req->undertime_req_id,
        'reference_no' => 'UND-' . str_pad($req->undertime_req_id, 4, '0', STR_PAD_LEFT),
        'submitted_by' => $req->employee_name,
        'submitted_date' => \Carbon\Carbon::parse($req->date)->format('M. d, Y'),
        'description' => \Illuminate\Support\Str::limit($req->reason, 50),
        'full_description' => $req->reason,
        'department' => 'N/A',
        'status' => $req->status,
        'original' => $req
      ];
    }
    foreach ($jvPending as $req) {
      $pendingApprovals[] = [
        'type' => 'JV',
        'id' => $req->id,
        'reference_no' => $req->jv_number,
        'submitted_by' => $req->requestor->name ?? 'Unknown',
        'submitted_date' => $req->created_at->format('M. d, Y'),
        'description' => $req->reason ?? 'Initial Summary Compilation Request',
        'full_description' => "JV Number: " . $req->jv_number . " | Status: " . str_replace('_', ' ', $req->status),
        'department' => 'Credit & Collection',
        'status' => $req->status,
        'amount' => is_numeric($req->total_amount) ? (float)$req->total_amount : (float)$req->items->sum('amount'),
        'original' => $req
      ];
    }

    // Sort by submitted date descending
    usort($pendingApprovals, function ($a, $b) {
      return strtotime($b['submitted_date']) - strtotime($a['submitted_date']);
    });

    return $pendingApprovals;
  }

  public function myRequests()
  {
    $cashAdvances = \App\Models\EmployeeCashAdvance::where('user_id', auth()->id())
      ->latest()
      ->get();

    return view('admin-finance.my-requests.index', [
      'title' => '',
      'role' => auth()->user()->position,
      'sidebar' => 'admin-finance',
      'cashAdvances' => $cashAdvances
    ]);
  }

  public function approvalQueue()
  {
    $user = auth()->user();
    $pendingApprovals = $this->gatherApprovalQueueItems($user);

    // --- My Approvals (The unified list for the top card) ---
    $myApprovals = [];

    // 1. Sales Orders awaiting current user's approval (Finance Manager)
    $salesOrdersForMe = \App\Models\SalesOrder::with('customer', 'preparedBy')
        ->whereIn('status', ['pending_acct_approval', 'pending_si_approval', 'pending_dr_approval'])
        ->orWhere(function($q) {
            $q->where('type', 'complimentary')
              ->where('status', 'picking')
              ->whereNull('ar_prepared_at');
        })
        ->latest()
        ->get();

    foreach ($salesOrdersForMe as $req) {
      $myApprovals[] = [
        'type' => 'Sales Order',
        'id' => $req->id,
        'reference_no' => $req->so_number,
        'submitted_by' => $req->preparedBy->name ?? 'Unknown',
        'submitted_date' => $req->created_at,
        'amount' => '₱' . number_format($req->total_amount, 2),
        'description' => 'Sales Order for ' . ($req->customer->customer_name ?? 'Unknown Customer'),
        'department' => 'Sales',
        'status' => ($req->type === 'complimentary' && $req->status === 'picking') ? 'Pending AR' : $req->status,
        'url' => route('admin-finance.sales-order.detail', $req->id),
        'attachment' => $req->attachment,
        'original' => $req
      ];
    }

    // 2. All MIS Requests awaiting current user's approval
    foreach ($pendingApprovals as $req) {
      $orig = $req['original'] ?? null;
      $attachment = null;
      if ($orig) {
        if (isset($orig->supporting_documents) && $orig->supporting_documents) {
          $attachment = $orig->supporting_documents;
        } elseif (isset($orig->attachment) && $orig->attachment) {
          $attachment = $orig->attachment;
        } elseif (isset($orig->documents) && $orig->documents) {
          $attachment = $orig->documents;
        }
      }

      $myApprovals[] = [
        'type' => $req['type'],
        'id' => $req['id'],
        'reference_no' => $req['reference_no'],
        'submitted_by' => $req['submitted_by'],
        'submitted_date' => $req['original']->created_at,
        'amount' => $req['amount'] ?? 'N/A', // Use provided amount or default to N/A
        'description' => $req['description'],
        'full_description' => $req['full_description'] ?? $req['description'],
        'department' => $req['department'],
        'status' => $req['status'],
        'url' => '#',
        'attachment' => $attachment,
        'original' => $req['original']
      ];
    }

    // Sort My Approvals by date descending
    usort($myApprovals, function ($a, $b) {
      return $b['submitted_date'] <=> $a['submitted_date'];
    });

    // Fetch My Submissions (Unified)
    $mySubmissions = [];

    // 1. Sales Orders
    $soSubmissions = \App\Models\SalesOrder::with('customer', 'preparedBy')
      ->where('prepared_by', auth()->id())
      ->latest()
      ->get();

    foreach ($soSubmissions as $so) {
      $mySubmissions[] = [
        'type' => 'Sales Order',
        'id' => $so->id,
        'reference_no' => $so->so_number,
        'submitted_date' => $so->created_at,
        'detail' => '₱' . number_format($so->total_amount, 2),
        'status' => $so->status,
        'url' => route('admin-finance.sales-order.detail', $so->id),
        'attachment' => $so->attachment,
        'original' => $so
      ];
    }

    // 2. MIS Requests (using user_id)
    $myCctv = CCTVReq::where('user_id', auth()->id())
      ->where('status', '!=', 'to submit')
      ->latest()
      ->get();
    foreach ($myCctv as $req) {
      $mySubmissions[] = [
        'type' => 'CCTV',
        'id' => $req->cctv_req_id,
        'reference_no' => 'CCTV-' . str_pad($req->cctv_req_id, 4, '0', STR_PAD_LEFT),
        'submitted_date' => $req->created_at,
        'detail' => \Illuminate\Support\Str::limit($req->purpose, 50),
        'status' => $req->status,
        'url' => '#',
        'attachment' => null,
        'original' => $req
      ];
    }

    $myMaterial = MaterialReq::where('user_id', auth()->id())
      ->where('status', '!=', 'to submit')
      ->latest()
      ->get();
    foreach ($myMaterial as $req) {
      $mySubmissions[] = [
        'type' => 'Material',
        'id' => $req->material_req_id,
        'reference_no' => 'MAT-' . str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT),
        'submitted_date' => $req->created_at,
        'detail' => \Illuminate\Support\Str::limit($req->request_details, 50),
        'status' => $req->status,
        'url' => '#',
        'attachment' => null,
        'original' => $req
      ];
    }

    $myQb = MisQbRequest::with('items')->where('user_id', auth()->id())->latest()->get();
    foreach ($myQb as $req) {
      $mySubmissions[] = [
        'type' => 'QB',
        'id' => $req->qb_req_id,
        'reference_no' => 'QB-' . str_pad($req->qb_req_id, 4, '0', STR_PAD_LEFT),
        'submitted_date' => $req->created_at,
        'detail' => $req->customer_item_name,
        'status' => $req->status,
        'url' => '#',
        'attachment' => null,
        'original' => $req
      ];
    }

    $myService = MisServiceRequest::where('user_id', auth()->id())->latest()->get();
    foreach ($myService as $req) {
      $mySubmissions[] = [
        'type' => 'Service',
        'id' => $req->service_req_id,
        'reference_no' => 'SRV-' . str_pad($req->service_req_id, 4, '0', STR_PAD_LEFT),
        'submitted_date' => $req->created_at,
        'detail' => \Illuminate\Support\Str::limit($req->nature_of_request, 50),
        'status' => $req->status,
        'url' => '#',
        'attachment' => null,
        'original' => $req
      ];
    }

    $myUndertime = MisUndertimeRequest::where('user_id', auth()->id())->latest()->get();
    foreach ($myUndertime as $req) {
      $mySubmissions[] = [
        'type' => 'Undertime',
        'id' => $req->undertime_req_id,
        'reference_no' => 'UND-' . str_pad($req->undertime_req_id, 4, '0', STR_PAD_LEFT),
        'submitted_date' => $req->created_at,
        'detail' => \Illuminate\Support\Str::limit($req->reason, 50),
        'status' => $req->status,
        'url' => '#',
        'attachment' => null,
        'original' => $req
      ];
    }

    // 5. Journal Voucher Requests
    $myJv = JournalVoucherRequest::where('requested_by', auth()->id())->latest()->get();
    foreach ($myJv as $req) {
      $mySubmissions[] = [
        'type' => 'JV Request',
        'id' => $req->id,
        'reference_no' => $req->jv_number,
        'submitted_date' => $req->created_at,
        'detail' => $req->reason ?? 'Initial Summary Compilation Request',
        'status' => $req->status,
        'url' => route('admin-finance.credit-collection.jv-requests.show', $req->id),
        'attachment' => $req->supporting_documents,
        'original' => $req
      ];
    }

    $myCashAdvances = \App\Models\EmployeeCashAdvance::where('user_id', auth()->id())->latest()->get();
    foreach ($myCashAdvances as $req) {
      $mySubmissions[] = [
        'type' => 'Cash Advance',
        'id' => $req->id,
        'reference_no' => 'CA-' . str_pad($req->id, 4, '0', STR_PAD_LEFT),
        'submitted_date' => $req->created_at,
        'detail' => '₱' . number_format($req->amount, 2),
        'status' => $req->status,
        'url' => '#',
        'attachment' => null,
        'original' => $req
      ];
    }

    // Sort all submissions by date
    usort($mySubmissions, function ($a, $b) {
      return $b['submitted_date'] <=> $a['submitted_date'];
    });

    // Filter approved requests for the Approved tab
    // This includes user's own submissions that are approved AND items they personally approved
    $myApprovedSubmissions = array_filter($mySubmissions, function($item) {
      $approvedStatuses = ['completed', 'approved', 'received', 'forwarded to accounting', 'picking', 'delivered', 'ready_for_delivery'];
      return in_array($item['status'], $approvedStatuses);
    });

    $userApprovedEntries = [];
    $userId = auth()->id();

    // Fetch CCTV approved by user
    $cctvApproved = CCTVReq::where(function($q) use ($userId) {
        $q->where('approved_by_manager', $userId)
          ->orWhere('approved_by_hr', $userId)
          ->orWhere('approved_by_director', $userId);
    })->get();
    foreach ($cctvApproved as $req) {
        $userApprovedEntries[] = [
            'type' => 'CCTV',
            'id' => $req->cctv_req_id,
            'reference_no' => 'CCTV-' . str_pad($req->cctv_req_id, 4, '0', STR_PAD_LEFT),
            'submitted_by' => $req->requested_by,
            'submitted_date' => $req->created_at,
            'detail' => $req->purpose,
            'status' => $req->status,
            'url' => '#',
            'attachment' => null,
            'original' => $req
        ];
    }

    // Fetch Material approved by user
    $materialApproved = MaterialReq::where(function($q) use ($userId) {
        $q->where('approved_by_manager', $userId)
          ->orWhere('approved_by_director', $userId);
    })->get();
    foreach ($materialApproved as $req) {
        $userApprovedEntries[] = [
            'type' => 'Material',
            'id' => $req->material_req_id,
            'reference_no' => 'MAT-' . str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT),
            'submitted_by' => $req->requested_by,
            'submitted_date' => $req->created_at,
            'detail' => $req->request_details,
            'status' => $req->status,
            'url' => '#',
            'attachment' => null,
            'original' => $req
        ];
    }

    // Fetch MIS (QB, Service, Undertime)
    $qbApproved = MisQbRequest::with('user')->where('approved_by', $userId)->get();
    foreach ($qbApproved as $req) {
        $userApprovedEntries[] = [
            'type' => 'QB',
            'id' => $req->qb_req_id,
            'reference_no' => 'QB-' . str_pad($req->qb_req_id, 4, '0', STR_PAD_LEFT),
            'submitted_by' => $req->user->name ?? 'Unknown',
            'submitted_date' => $req->created_at,
            'detail' => $req->customer_item_name,
            'status' => $req->status,
            'url' => '#',
            'attachment' => null,
            'original' => $req
        ];
    }

    $serviceApproved = MisServiceRequest::where('approved_by', $userId)->get();
    foreach ($serviceApproved as $req) {
        $userApprovedEntries[] = [
            'type' => 'Service',
            'id' => $req->service_req_id,
            'reference_no' => 'SRV-' . str_pad($req->service_req_id, 4, '0', STR_PAD_LEFT),
            'submitted_by' => $req->requestor_name,
            'submitted_date' => $req->created_at,
            'detail' => $req->nature_of_request,
            'status' => $req->status,
            'url' => '#',
            'attachment' => null,
            'original' => $req
        ];
    }

    $undertimeApproved = MisUndertimeRequest::where('approved_by', $userId)->get();
    foreach ($undertimeApproved as $req) {
        $userApprovedEntries[] = [
            'type' => 'Undertime',
            'id' => $req->undertime_req_id,
            'reference_no' => 'UND-' . str_pad($req->undertime_req_id, 4, '0', STR_PAD_LEFT),
            'submitted_by' => $req->employee_name,
            'submitted_date' => $req->created_at,
            'detail' => $req->reason,
            'status' => $req->status,
            'url' => '#',
            'attachment' => null,
            'original' => $req
        ];
    }

    // Sales Orders approved by user
    $soApproved = \App\Models\SalesOrder::with('preparedBy')->where(function($q) use ($userId) {
        $q->where('approved_by_acct', $userId)
          ->orWhere('signed_by_af_manager', $userId);
    })->get();
    foreach ($soApproved as $so) {
        $userApprovedEntries[] = [
            'type' => 'Sales Order',
            'id' => $so->id,
            'reference_no' => $so->so_number,
            'submitted_by' => $so->preparedBy->name ?? 'Unknown',
            'submitted_date' => $so->created_at,
            'detail' => '₱' . number_format($so->total_amount, 2),
            'status' => $so->status,
            'url' => route('admin-finance.sales-order.detail', $so->id),
            'attachment' => $so->attachment,
            'original' => $so
        ];
    }

    // Cash Advances approved by user
    $caApproved = \App\Models\EmployeeCashAdvance::where(function($q) use ($userId) {
        $q->where('approved_by_manager', $userId)
          ->orWhere('approved_by_admin', $userId)
          ->orWhere('approved_by_director', $userId);
    })->latest()->get();

    foreach ($caApproved as $req) {
        $userApprovedEntries[] = [
            'type' => 'Cash Advance',
            'id' => $req->id,
            'reference_no' => 'CA-' . str_pad($req->id, 4, '0', STR_PAD_LEFT),
            'submitted_by' => $req->employee_name,
            'submitted_date' => $req->created_at,
            'detail' => 'PhP ' . number_format($req->amount, 2) . ' - ' . \Illuminate\Support\Str::limit($req->purpose, 50),
            'status' => $req->status,
            'url' => '#',
            'attachment' => null,
            'original' => $req
        ];
    }

    // Merge: Personal Approval History FIRST, then own submissions
    // This ensures that even if I own a request, the "Approved" entry (with its full history)
    // takes precedence over my own submission record if it's not fully approved yet.
    $mergedApproved = array_merge($userApprovedEntries, $myApprovedSubmissions);
    $finalApproved = [];
    $seen = [];
    foreach ($mergedApproved as $item) {
        $key = $item['type'] . '_' . $item['id'];
        if (!isset($seen[$key])) {
            $seen[$key] = true;
            $finalApproved[] = $item;
        }
    }

    // Sort by date descending (using unix timestamp for stability)
    usort($finalApproved, function ($a, $b) {
      $dateA = $a['submitted_date'] instanceof \Carbon\Carbon ? $a['submitted_date']->timestamp : strtotime($a['submitted_date']);
      $dateB = $b['submitted_date'] instanceof \Carbon\Carbon ? $b['submitted_date']->timestamp : strtotime($b['submitted_date']);
      return $dateB - $dateA;
    });

    return view('admin-finance.approval-queue', [
      'title' => 'Approval Queue',
      'role' => auth()->user()->position,
      'sidebar' => 'admin-finance',
      'myApprovals' => $myApprovals,
      'pendingApprovals' => $pendingApprovals,
      'mySubmissions' => $mySubmissions,
      'myApprovedRequests' => $finalApproved,
      'salesOrders' => $salesOrdersForMe
    ]);
  }

  public function checkVoucherIndex()
  {
    $entries = JournalEntry::where('entry_type', 'CV')
                ->with('creator')
                ->latest()
                ->paginate(15);

    return view('admin-finance.check-voucher.list', [
      'title' => 'Check Vouchers',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance',
      'entries' => $entries
    ]);
  }

  public function showCheckVoucher($id)
  {
    $entry = JournalEntry::with(['items.account', 'creator'])->findOrFail($id);

    return view('admin-finance.check-voucher.show', [
      'title' => 'Check Voucher Details',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance',
      'entry' => $entry
    ]);
  }

public function checkVoucher()
{
  return view('admin-finance.check-voucher.create', [
    'title' => 'Create Check Voucher',
    'role' => 'Finance Manager',
    'sidebar' => 'admin-finance'
  ]);
}

  public function storeCheckVoucher(Request $request)
  {
    $request->validate([
      'payee' => 'required',
      'check_no' => 'required',
      'date' => 'required|date',
    ]);

    // --- ACCOUNTING INTEGRATION ---
    $this->accounting->postCheckVoucherEntry([
      'payee' => $request->payee,
      'check_no' => $request->check_no,
      'date' => $request->date,
      'amount' => $request->amount ?? 0,
      'memo' => $request->memo,
      'items' => $request->items
    ]);

    return redirect()->route('admin-finance.check-voucher')->with('success', 'Check Voucher #' . $request->check_no . ' has been recorded and posted to the General Ledger.');
  }

  public function salesInvoice()
  {
    $orders = \App\Models\SalesOrder::with('customer', 'preparedBy')
      ->whereIn('status', ['pending_si_prep', 'pending_si_approval'])
      ->whereNull('signed_by_af_manager')
      ->latest()
      ->get();

    return view('admin-finance.accounting.sales-invoice', [
      'title' => 'Sales Invoice Management',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance',
      'orders' => $orders
    ]);
  }

  public function prepareSalesInvoice($id)
  {
    $order = \App\Models\SalesOrder::with('customer', 'items.product', 'preparedBy')->findOrFail($id);

    return view('admin-finance.accounting.prepare-si', [
      'title' => 'Prepare Sales Invoice',
      'role' => 'Accounting Staff',
      'sidebar' => 'admin-finance',
      'order' => $order
    ]);
  }

  public function storeSalesInvoice(Request $request, $id)
  {
    $order = \App\Models\SalesOrder::findOrFail($id);

        $order->update([
      'status' => 'pending_si_approval',
      'si_prepared_by' => auth()->id(),
      'si_prepared_at' => now(),
      'remarks' => ($order->remarks ? $order->remarks . ' | ' : '') . 'SI Prepared by ' . auth()->user()->name
    ]);

    // Send Notification to Director if status is "pending_si_approval"
    $director = \App\Models\User::where('position', 'Director')->first();
    if ($director) {
        try {
            $director->notify(new \App\Notifications\DirectorApprovalRequested($order, 'Sales Order'));
        } catch (\Exception $e) {
            \Log::error("Failed to send Sales Order SI approval notification: " . $e->getMessage());
        }
    }

    return redirect()->route('admin-finance.accounting.sales-invoice')->with('success', 'Sales Invoice for #' . $order->so_number . ' has been prepared and is waiting for Manager signature.');
  }

  public function signSalesInvoice($id)
  {
    $order = \App\Models\SalesOrder::findOrFail($id);

    $order->update([
      'status' => 'ready_for_delivery',
      'signed_by_af_manager' => auth()->id(),
      'signed_at' => now()
    ]);

    // --- ACCOUNTING INTEGRATION ---
    $this->accounting->postSalesOrderEntry($order);

    return redirect()->back()->with('success', 'Sales Invoice for #' . $order->so_number . ' has been signed by Admin & Finance Manager.');
  }

  public function printSalesInvoice($id)
  {
    $order = \App\Models\SalesOrder::with('customer', 'items.product', 'siPreparedBy', 'signedBy')->findOrFail($id);

    return view('admin-finance.accounting.print-si', [
      'title' => 'Print Sales Invoice',
      'order' => $order
    ]);
  }

  public function materialsRequisition()
  {
    $requisitions = \App\Models\AdminFinance\MaterialRequisition::with('user')->orderBy('created_at', 'desc')->get();

    return view('admin-finance.accounting.materials-requisition', [
      'title' => 'Materials/Supplies Requisition',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance',
      'requisitions' => $requisitions
    ]);
  }

  public function storeMaterialRequisition(Request $request)
  {
      $request->validate([
          'date' => 'required|date',
          'department' => 'required|string',
          'items' => 'required|array|min:1',
          'items.*.description' => 'required|string',
          'items.*.qty' => 'required|numeric|min:0'
      ]);

      try {
          \DB::beginTransaction();

          // Generate Requisition No.
          $latest = \App\Models\AdminFinance\MaterialRequisition::latest('id')->first();
          $nextId = $latest ? $latest->id + 1 : 1;
          $reqNo = 'REQ-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

          $requisition = \App\Models\AdminFinance\MaterialRequisition::create([
              'requisition_no' => $reqNo,
              'date' => $request->date,
              'department' => $request->department,
              'supplier' => $request->supplier,
              'po_number' => $request->po_number,
              'requested_by' => auth()->id(),
              'status' => 'pending'
          ]);

          foreach ($request->items as $item) {
              \App\Models\AdminFinance\MaterialRequisitionItem::create([
                  'material_requisition_id' => $requisition->id,
                  'qty' => $item['qty'],
                  'unit' => $item['unit'],
                  'description' => $item['description'],
                  'supplier1_price' => $item['supplier1_price'] ?? null,
                  'supplier2_price' => $item['supplier2_price'] ?? null,
                  'supplier3_price' => $item['supplier3_price'] ?? null,
              ]);
          }

          \DB::commit();

          return response()->json([
              'success' => true,
              'message' => "Requisition $reqNo has been successfully recorded.",
              'requisition' => [
                  'id' => $requisition->id,
                  'requisition_no' => $reqNo,
                  'date' => $request->date,
                  'requested_by' => auth()->user()->name,
                  'department' => $request->department,
                  'po_number' => $request->po_number,
                  'status' => 'pending'
              ]
          ]);

      } catch (\Exception $e) {
          \DB::rollBack();
          return response()->json([
              'success' => false,
              'message' => 'An error occurred while saving the requisition: ' . $e->getMessage()
          ], 500);
      }
  }

  public function showMaterialRequisition($id)
  {
      $requisition = \App\Models\AdminFinance\MaterialRequisition::with(['items', 'user'])->findOrFail($id);
      return response()->json([
          'success' => true,
          'requisition' => $requisition
      ]);
  }

  public function destroyMaterialRequisition($id)
  {
      try {
          $requisition = \App\Models\AdminFinance\MaterialRequisition::findOrFail($id);
          $requisition->delete();
          
          return response()->json([
              'success' => true,
              'message' => 'Material Requisition deleted successfully.'
          ]);
      } catch (\Exception $e) {
          return response()->json([
              'success' => false,
              'message' => 'An error occurred while deleting the requisition: ' . $e->getMessage()
          ], 500);
      }
  }

  public function materialRequestsIncoming(Request $request)
  {
    $query = MaterialReq::with(['user', 'manager', 'director']);

    // Apply Filters for "All Requests" tab and general view
    if ($request->filled('status')) {
        $status = $request->status;
        if ($status === 'pending') $status = 'forwarded to accounting';
        elseif ($status === 'completed') $status = 'received';
        $query->where('status', $status);
    }

    if ($request->filled('department')) {
        $dept = $request->department;
        $query->whereHas('user', function($q) use ($dept) {
            $q->where('department', 'like', '%' . $dept . '%');
        });
    }

    if ($request->filled('requested_by')) {
        $name = $request->requested_by;
        $query->where(function($q) use ($name) {
            $q->where('requested_by', 'like', '%' . $name . '%')
              ->orWhereHas('user', function($sq) use ($name) {
                  $sq->where('name', 'like', '%' . $name . '%');
              });
        });
    }

    if ($request->filled('date_range')) {
        $range = explode(' - ', $request->date_range);
        if (count($range) == 2) {
            $start = \Carbon\Carbon::parse($range[0])->startOfDay();
            $end = \Carbon\Carbon::parse($range[1])->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        }
    }

    $allRequests = $query->latest()->get();
    
    // Maintain separate collections for the status-specific tabs (unfiltered by status but filtered by other criteria if desired)
    // For now, I'll keep the tabs consistent with the "All" view if filters are applied, 
    // OR just keep them as they were if the user expects them to always show "Pending Action" regardless of global filters.
    // Usually, global filters apply to the main view.
    
    $pendingRequests = MaterialReq::with(['user', 'manager', 'director'])->where('status', 'forwarded to accounting')->latest()->get();
    $processingRequests = MaterialReq::with(['user', 'manager', 'director'])->where('status', 'processing')->latest()->get();
    $completedRequests = MaterialReq::with(['user', 'manager', 'director'])->where('status', 'received')->latest()->get();

    return view('admin-finance.accounting.material-requests', [
      'title' => 'Material Requests',
      'role' => auth()->user()->position,
      'sidebar' => 'admin-finance',
      'pendingRequests' => $pendingRequests,
      'processingRequests' => $processingRequests,
      'completedRequests' => $completedRequests,
      'allRequests' => $allRequests
    ]);
  }

  public function expenseManagement()
  {
    return view('admin-finance.accounting.expense-management', [
      'title' => 'Expense Management',
      'role' => auth()->user()->position,
      'sidebar' => 'admin-finance'
    ]);
  }

  public function createCashAdvance()
  {
    return view('admin-finance.accounting.cash-advance.cash-advance', [
      'title' => 'Cash Advance',
      'role' => auth()->user()->position,
      'sidebar' => 'admin-finance'
    ]);
  }

  public function cashAdvanceLiquidation()
  {
    return view('admin-finance.expenses.cash-advance-liquidation', [
      'title' => 'Cash Advance Liquidation',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance'
    ]);
  }

  public function storeLiquidation(Request $request)
  {
    $request->validate([
      'employee_name' => 'required',
      'date' => 'required|date',
      'amount_advanced' => 'required|numeric',
      'total_expenses' => 'required|numeric',
      'expenses' => 'required|array',
    ]);

    // --- ACCOUNTING INTEGRATION ---
    $this->accounting->postLiquidationEntry([
      'date' => $request->date,
      'employee_name' => $request->employee_name,
      'reference' => $request->reference ?? 'CA-LIQ',
      'amount_liquidated' => $request->total_expenses, // Credit Receivable for the spent amount
      'expenses' => $request->expenses
    ]);

    return redirect()->back()->with('success', 'Liquidation for ' . $request->employee_name . ' has been recorded and expenses posted.');
  }

  public function billing()
  {
      $unpaidOrders = \App\Models\SalesOrder::with('customer')
          ->where('payment_status', 'unpaid')
          ->whereIn('status', ['completed', 'ready_for_delivery', 'verified'])
          ->latest()
          ->get();

      $statements = \App\Models\StatementOfAccount::with('customer')->latest()->get();
      $freightBills = \App\Models\FreightBill::with('customer')->latest()->get();
      
      $jvRequests = \App\Models\JournalVoucherRequest::with(['requestor', 'items'])->latest()->get();

      return view('admin-finance.credit-collection.billing', [
          'title' => 'Billing',
          'role' => 'Finance Manager',
          'sidebar' => 'admin-finance',
          'unpaidOrders' => $unpaidOrders,
          'statements' => $statements,
          'freightBills' => $freightBills,
          'jvRequests' => $jvRequests
      ]);
  }

  public function showAccountStatement($id)
  {
    return view('admin-finance.credit-collection.billing-show', [
      'title' => 'Statement Detail',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance',
      'id' => $id
    ]);
  }

  public function createAccountStatement($id)
  {
    $order = \App\Models\SalesOrder::with('customer')->findOrFail($id);
    return view('admin-finance.credit-collection.billing-create', [
      'title' => 'Prepare Statement',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance',
      'order' => $order,
      'mode' => 'create'
    ]);
  }

  public function editAccountStatement($id)
  {
    $soa = \App\Models\StatementOfAccount::with(['customer', 'salesOrders'])->findOrFail($id);
    return view('admin-finance.credit-collection.billing-create', [
      'title' => 'Edit Statement',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance',
      'soa' => $soa,
      'mode' => 'edit'
    ]);
  }

  public function createFreightBill()
  {
    return view('admin-finance.credit-collection.freight-billing-create', [
      'title' => 'Create New Freight Bill',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance',
      'customers' => \App\Models\Customer::all()
    ]);
  }

  public function storeAccountStatement(Request $request)
  {
      $request->validate([
          'soa_number' => 'required|unique:statement_of_accounts,soa_number',
          'customer_id' => 'required',
          'billing_period_start' => 'required|date',
          'billing_period_end' => 'required|date',
          'total_amount' => 'required|numeric',
          'sales_order_ids' => 'required|array',
          'items' => 'required|array',
          'status' => 'required'
      ]);

      $soa = \App\Models\StatementOfAccount::create([
          'soa_number' => $request->soa_number,
          'customer_id' => $request->customer_id,
          'billing_period_start' => $request->billing_period_start,
          'billing_period_end' => $request->billing_period_end,
          'total_amount' => $request->total_amount,
          'status' => $request->status
      ]);

      foreach ($request->items as $item) {
          \App\Models\StatementOfAccountItem::create([
              'statement_of_account_id' => $soa->id,
              'service' => $item['service'],
              'description' => $item['description'] ?? '',
              'qty' => $item['qty'] ?? '',
              'price' => $item['price'] ?? 0
          ]);
      }

      \App\Models\SalesOrder::whereIn('id', $request->sales_order_ids)
          ->update(['statement_of_account_id' => $soa->id]);

      return redirect()->route('admin-finance.credit-collection.billing')->with('success', 'Statement of Account created successfully.');
  }

  public function storeFreightBill(Request $request)
  {
      $request->validate([
          'bill_number' => 'required|unique:freight_bills,bill_number',
          'customer_id' => 'required',
          'bill_date' => 'required|date',
          'carrier' => 'required',
          'amount' => 'required|numeric'
      ]);

      \App\Models\FreightBill::create([
          'bill_number' => $request->bill_number,
          'customer_id' => $request->customer_id,
          'bill_date' => $request->bill_date,
          'carrier' => $request->carrier,
          'amount' => $request->amount,
          'status' => 'draft'
      ]);

      return redirect()->route('admin-finance.credit-collection.billing')->with('success', 'Freight Bill created successfully.');
  }

  public function updateStatementStatus($id, Request $request)
  {
      $request->validate(['status' => 'required']);
      $soa = \App\Models\StatementOfAccount::findOrFail($id);
      $soa->update(['status' => $request->status]);

      return response()->json(['success' => true]);
  }

  public function updateFreightBillStatus($id, Request $request)
  {
      $request->validate(['status' => 'required']);
      $fb = \App\Models\FreightBill::findOrFail($id);
      $fb->update(['status' => $request->status]);

      return response()->json(['success' => true]);
  }

  public function destroyFreightBill($id)
  {
      $fb = \App\Models\FreightBill::findOrFail($id);
      $fb->delete();

      return response()->json(['success' => true]);
  }

  public function compileStatements(Request $request)
  {
      $ids = $request->input('ids');
      \App\Models\StatementOfAccount::whereIn('id', $ids)->update(['status' => 'compiled']);
      return response()->json(['success' => true]);
  }

  public function compileFreightBills(Request $request)
  {
      $ids = $request->input('ids');
      \App\Models\FreightBill::whereIn('id', $ids)->update(['status' => 'compiled']);
      return response()->json(['success' => true]);
  }

  public function reports(Request $request)
  {
      // --- 1. AR Aging Summary ---
      $arFilterGroup = $request->input('ar_group', 'customer_type');
      $arFilterValue = $request->input('ar_value', 'Team A');
      $arAsOfDate    = $request->input('ar_date', now()->toDateString());

      $arQuery = \App\Models\Customer::with(['salesOrders' => function($q) use ($arAsOfDate) {
          $q->where('payment_status', 'unpaid')
            ->where('status', '!=', 'cancelled')
            ->whereDate('created_at', '<=', $arAsOfDate);
      }]);

      if ($arFilterGroup == 'class') {
          $arQuery->where('class', $arFilterValue);
      } else {
          $arQuery->where('customer_type', $arFilterValue);
      }

      $arCustomers = $arQuery->get();
      $arTotalOpenBalance = 0;

      foreach ($arCustomers as $customer) {
          $customer->current = 0;
          $customer->days_1_30 = 0;
          $customer->days_31_60 = 0;
          $customer->days_61_90 = 0;
          $customer->over_90 = 0;
          $customer->total_ar = 0;

          foreach ($customer->salesOrders as $order) {
              $daysOld = \Carbon\Carbon::parse($order->created_at)->startOfDay()->diffInDays(\Carbon\Carbon::parse($arAsOfDate)->startOfDay());
              $amount = $order->total_amount;

              if ($daysOld <= 30) {
                  $customer->current += $amount;
              } elseif ($daysOld <= 60) {
                  $customer->days_1_30 += $amount;
              } elseif ($daysOld <= 90) {
                  $customer->days_31_60 += $amount;
              } elseif ($daysOld <= 120) {
                  $customer->days_61_90 += $amount;
              } else {
                  $customer->over_90 += $amount;
              }
              $customer->total_ar += $amount;
          }
          $arTotalOpenBalance += $customer->total_ar;
      }
      
      // Filter out customers with 0 balance
      $arCustomers = $arCustomers->filter(function($cust) {
          return $cust->total_ar > 0;
      });


      // --- 2. Sales by Customer Summary ---
      $salesType  = $request->input('sales_type', 'Team A');
      $salesStart = $request->input('sales_start', now()->startOfMonth()->toDateString());
      $salesEnd   = $request->input('sales_end', now()->endOfMonth()->toDateString());

      $salesOrders = \App\Models\SalesOrder::with(['customer', 'items.product'])
          ->whereHas('customer', function($q) use ($salesType) {
              $q->where('customer_type', $salesType);
          })
          ->whereBetween('created_at', [$salesStart . ' 00:00:00', $salesEnd . ' 23:59:59'])
          ->where('status', '!=', 'cancelled')
          ->get();

      foreach ($salesOrders as $order) {
          $freightAmount = 0;
          foreach ($order->items as $item) {
              $prodName = $item->product ? strtolower($item->product->title ?? $item->product->product_name ?? '') : strtolower($item->product_name ?? '');
              if (str_contains($prodName, 'delivery') || str_contains($prodName, 'freight') || str_contains($prodName, 'shipping')) {
                  $freightAmount += ($item->qty ?? $item->quantity ?? 1) * ($item->unit_price ?? $item->price ?? 0);
              }
          }
          $order->calculated_freight = $freightAmount;
          $order->net_sales = $order->total_amount - $order->tax_amount - $freightAmount;
      }


      // --- 3. Collection Report ---
      $collGroup = $request->input('coll_group', 'Teams');
      $collStart = $request->input('coll_start', now()->startOfMonth()->toDateString());
      $collEnd   = $request->input('coll_end', now()->endOfMonth()->toDateString());

      // Because the "Payment" model was just created, there might not be data in it yet.
      // But we will query it regardless.
      $paymentsQuery = \App\Models\Payment::with(['customer', 'salesOrder'])
          ->whereBetween('payment_date', [$collStart, $collEnd]);

      if ($collGroup == 'Ecom') {
          $paymentsQuery->whereHas('salesOrder', function($q) {
              $q->whereIn('platform', ['ECOM POS', 'Lazada', 'Shopee', 'Tiktok']);
          });
      } elseif ($collGroup == 'Events') {
          $paymentsQuery->whereHas('salesOrder', function($q) {
              $q->whereIn('platform', ['Events', 'Ads and Promo', 'Catholic Directory']);
          });
      } elseif ($collGroup == 'Booksale') {
          $paymentsQuery->whereHas('salesOrder', function($q) {
              $q->whereIn('platform', ['direct POS', 'Bookchains', 'direct sales']);
          });
      } else {
          // Assume Teams, filter by customer type if passed
          $teamsVal = $request->input('coll_team_val', 'Team A');
          $paymentsQuery->whereHas('customer', function($q) use ($teamsVal) {
              $q->where('customer_type', $teamsVal);
          });
      }

      $collections = $paymentsQuery->get();

      return view('admin-finance.credit-collection.reports', [
          'title'   => 'Credit & Collection Reports',
          'role'    => 'Finance Manager',
          'sidebar' => 'admin-finance',
          'arCustomers' => $arCustomers,
          'arTotalOpenBalance' => $arTotalOpenBalance,
          'salesOrders' => $salesOrders,
          'collections' => $collections,
          'filters' => [
              'arGroup' => $arFilterGroup, 'arVal' => $arFilterValue, 'arDate' => $arAsOfDate,
              'salesType' => $salesType, 'salesStart' => $salesStart, 'salesEnd' => $salesEnd,
              'collGroup' => $collGroup, 'collStart' => $collStart, 'collEnd' => $collEnd
          ]
      ]);
  }

  public function invoice()
  {
    $invoices = \App\Models\SalesOrder::with(['customer', 'items.product'])
      ->whereIn('status', ['ready_for_delivery', 'completed', 'verified', 'draft', 'pending_si_prep']) // Adjust statuses as needed based on "Order Fulfillment" context
      ->latest()
      ->get();

    return view('admin-finance.credit-collection.invoice.invoice', [
      'title' => 'Invoice',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance',
      'invoices' => $invoices
    ]);
  }

  public function reviewSalesOrder($id)
  {
    $order = \App\Models\SalesOrder::with('customer', 'items.product', 'preparedBy')->findOrFail($id);

    return view('admin-finance.sales-orders.review', [
      'title' => 'Review Sales Order',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance',
      'order' => $order
    ]);
  }

  public function approveSalesOrder(Request $request, $id)
  {
    $order = \App\Models\SalesOrder::with('items')->findOrFail($id);
    
    \Log::info('Processing approval for SO #' . $order->so_number . ' with ' . $order->items->count() . ' items');
    
    $order->update([
      'status' => 'picking',
      'approved_by_acct' => auth()->id(),
      'acct_approved_at' => now()
    ]);

    // Automatically create a pick list after accounting approval
    try {
      // Check if SO has items
      if (!$order->items || $order->items->count() === 0) {
        \Log::warning('SO #' . $order->so_number . ' has NO items - cannot create pick list');
        return redirect()->route('admin-finance.approval-queue')->with('warning', 'Sales Order #' . $order->so_number . ' approved but has no items.');
      }

      $pickList = \App\Models\PickList::create([
        'sales_order_id' => $order->id,
        'pick_list_number' => 'PL-' . $order->so_number . '-' . date('YmdHis'),
        'status' => 'in_progress',
        'prepared_by' => auth()->id(),
      ]);

      \Log::info('Created pick list: ' . $pickList->pick_list_number);

      // Create pick list items from sales order items
      foreach ($order->items as $item) {
        \App\Models\PickListItem::create([
          'pick_list_id' => $pickList->id,
          'sales_order_item_id' => $item->id,
          'requested_qty' => $item->quantity,
          'picked_qty' => 0,
          'status' => 'pending',
        ]);
      }
      
      \Log::info('Successfully created pick list with ' . $order->items->count() . ' items');
      
    } catch (\Exception $e) {
      \Log::error('Failed to create pick list for SO #' . $order->so_number . ': ' . $e->getMessage());
    }

    return redirect()->route('admin-finance.approval-queue')->with('success', 'Sales Order #' . $order->so_number . ' has been approved and sent to Logistics for picking.');
  }

  public function rejectSalesOrder(Request $request, $id)
  {
    $order = \App\Models\SalesOrder::findOrFail($id);
    $order->update([
      'status' => 'cancelled',
      'remarks' => $request->remarks . ' (Rejected by Finance)'
    ]);

    return redirect()->route('admin-finance.approval-queue')->with('warning', 'Sales Order #' . $order->so_number . ' has been rejected.');
  }

  public function jobOrders()
  {
    $user = auth()->user();
    $pos = $user->position;

    // --- CCTV Requests ---
    $cctvQuery = CCTVReq::query();
    if ($pos === 'Director') {
      $cctvQuery->where('status', '!=', 'Pending Final Approval');
    }
    $cctvRequests = $cctvQuery->orderBy('created_at', 'desc')->get();
    $materialRequests = MaterialReq::orderBy('created_at', 'desc')->get();
    $qbRequests = MisQbRequest::with('items')->orderBy('created_at', 'desc')->get();
    $undertimeRequests = MisUndertimeRequest::orderBy('created_at', 'desc')->get();
    $serviceRequests = MisServiceRequest::orderBy('created_at', 'desc')->get();

    return view('admin-finance.mis.job-orders', [
      'title' => 'Job Orders',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance',
      'cctvRequests' => $cctvRequests,
      'materialRequests' => $materialRequests,
      'qbRequests' => $qbRequests,
      'undertimeRequests' => $undertimeRequests,
      'serviceRequests' => $serviceRequests
    ]);
  }

  public function hrJobOrders()
  {
    $cctvRequests = CCTVReq::where('status', 'Pending HR approval')->orderBy('created_at', 'desc')->get();

    return view('admin-finance.hr.job-orders', [
      'title' => 'Job Orders',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance',
      'cctvRequests' => $cctvRequests
    ]);
  }

  // GSD Job Orders Method (New)
  // GSD Job Orders Method (New)
  public function gsdJobOrders(Request $request)
  {
    $status = $request->query('status', 'all');
    
    if ($status === 'all') {
        $statuses = ['approved', 'ongoing', 'on_hold', 'completed'];
    } else {
        $statuses = [$status];
    }
    
    $qbRequests = MisQbRequest::with(['user', 'items', 'approver'])
        ->whereIn('status', $statuses)
        ->orderBy('updated_at', 'desc')
        ->get();
        
    $undertimeRequests = MisUndertimeRequest::with('approver')
        ->whereIn('status', $statuses)
        ->orderBy('updated_at', 'desc')
        ->get();
        
    $serviceRequests = MisServiceRequest::with('approver')
        ->whereIn('status', $statuses)
        ->orderBy('updated_at', 'desc')
        ->get();

    return view('admin-finance.gsd.job-orders', [
      'title' => 'GSD Job Orders',
      'role' => auth()->user()->position,
      'sidebar' => 'admin-finance',
      'qbRequests' => $qbRequests,
      'undertimeRequests' => $undertimeRequests,
      'serviceRequests' => $serviceRequests,
      'currentStatus' => $status
    ]);
  }

  // GSD Update Job Order Status (New)
  public function gsdUpdateJobOrderStatus(Request $request, $type, $id)
  {
      $validated = $request->validate([
          'status' => 'required|in:on_hold,ongoing,completed'
      ]);

      if ($type == 'qb') {
          $order = MisQbRequest::findOrFail($id);
      } elseif ($type == 'undertime') {
          $order = MisUndertimeRequest::findOrFail($id);
      } elseif ($type == 'service') {
          $order = MisServiceRequest::findOrFail($id);
      } else {
          abort(404);
      }

      $data = ['status' => $validated['status']];
      
      if ($validated['status'] === 'completed') {
          $data['completed_by'] = auth()->id();
          $data['completed_at'] = now();
      }

      $order->update($data);

      return redirect()->back()->with('success', 'Job Order status updated to ' . ucfirst(str_replace('_', ' ', $validated['status'])) . '.');
  }

  public function jvRequests()
  {
      $requests = JournalVoucherRequest::with(['requestor', 'items'])->latest()->get();
      return view('admin-finance.credit-collection.jv-requests.index', [
          'title' => 'Journal Voucher Requests',
          'role' => 'Finance Manager',
          'sidebar' => 'admin-finance',
          'requests' => $requests
      ]);
  }

  public function createJvRequest()
  {
      return view('admin-finance.credit-collection.jv-requests.create', [
          'title' => 'New JV Request',
          'role' => 'Finance Manager',
          'sidebar' => 'admin-finance',
          'customers' => \App\Models\Customer::all()
      ]);
  }

  public function storeJvRequest(Request $request)
  {
      $request->validate([
          'items' => 'required|array|min:1'
      ]);

      \DB::beginTransaction();
      try {
          $latest = JournalVoucherRequest::latest('id')->first();
          $nextNo = $latest ? (int)$latest->jv_number + 1 : 11082; 

          $jvRequest = JournalVoucherRequest::create([
              'jv_number' => (string)$nextNo,
              'date' => now(), 
              'requested_by' => auth()->id(),
              'reason' => $request->reason ?? null,
              'category' => $request->category ?? 'Account Statement',
              'documents' => 'Summary Report',
              'supporting_documents' => null,
              'status' => 'pending_accounting'
          ]);

          $total = 0;
          foreach ($request->items as $item) {
              JournalVoucherItem::create([
                  'jv_request_id' => $jvRequest->id,
                  'reference_no' => $item['reference_no'],
                  'customer_name' => $item['customer_name'],
                  'customer_id' => $item['customer_id'] ?? null,
                  'amount' => $item['amount'],
                  'remarks' => $item['remarks'] ?? 'QB Entry',
                  'type' => $item['type'] ?? 'item'
              ]);
              $total += $item['amount'];
          }

          $jvRequest->update(['total_amount' => $total]);

          \DB::commit();
          // Redirect back to Billing page and open JV Summary tab for a smoother UX
          return redirect()->route('admin-finance.credit-collection.billing', ['tab' => 'jv'])->with('success', "JV Request #$nextNo created successfully.");

      } catch (\Exception $e) {
          \DB::rollBack();
          return back()->with('error', 'Error creating JV Request: ' . $e->getMessage());
      }
  }

  public function showJvRequest($id)
  {
      $request = JournalVoucherRequest::with(['items', 'requestor', 'approver'])->findOrFail($id);
      return view('admin-finance.credit-collection.jv-requests.show', [
          'title' => 'JV Request Details',
          'role' => 'Finance Manager',
          'sidebar' => 'admin-finance',
          'request' => $request
      ]);
  }

  public function approveJvRequest($id)
  {
      $request = JournalVoucherRequest::with('items')->findOrFail($id);
      $request->update([
          // Mark as accounting-verified while keeping approval metadata
          'status' => 'accounting_verified',
          'approved_by' => auth()->id(),
          'approved_at' => now(),
          'accounting_remarks' => 'Verified by Accounting'
      ]);

      // Create and post Journal Entry from this JV Request
      try {
        $entry = $this->accounting->postJournalVoucherRequest($request);
        $msg = "Compilation #{$request->jv_number} verified by Accounting and posted as Journal Entry " . ($entry->entry_no ?? 'N/A') . ".";
      } catch (\Exception $e) {
        // Log exception and notify user, but don't break approval flow
        report($e);
        $msg = "Compilation #{$request->jv_number} approved by Accounting, but posting failed: " . $e->getMessage();
      }

      return redirect()->back()->with('success', $msg);
  }

  public function rejectJvRequest($id)
  {
      $request = JournalVoucherRequest::findOrFail($id);
      $request->update([
          'status' => 'rejected',
          'rejected_by' => auth()->id(),
          'rejected_at' => now(),
      ]);

      return redirect()->back()->with('warning', "JV Request #{$request->jv_number} has been rejected.");
  }

  public function managerApproveJvRequest($id)
  {
      $jvRequest = JournalVoucherRequest::with('items')->findOrFail($id);
      
      $jvRequest->update([
          'status' => 'posted',
          'manager_approved_by' => auth()->id(),
          'manager_approved_at' => now(),
      ]);

      $entry = \App\Models\JournalEntry::create([
          'entry_no' => 'JV-' . $jvRequest->jv_number,
          'entry_type' => 'JV',
          'date' => $jvRequest->date,
          'reference' => 'JV #' . $jvRequest->jv_number,
          'memo' => $jvRequest->reason ?? 'Journal Voucher Request Approval',
          'currency' => 'PHP',
          'exchange_rate' => 1.0,
          'created_by' => auth()->id(),
          'status' => 'posted'
      ]);

      foreach ($jvRequest->items as $item) {
          \App\Models\JournalEntryItem::create([
              'journal_entry_id' => $entry->id,
              'account_id' => 1,
              'description' => $item->customer_name . ' - ' . $item->reference_no,
              'debit' => $item->amount,
              'credit' => 0,
          ]);
      }

      return redirect()->route('admin-finance.credit-collection.billing')->with('success', 'JV Request approved and posted to General Journal.');
  }

  public function printJvRequest($id)
  {
      $request = JournalVoucherRequest::with(['items', 'requestor', 'approver'])->findOrFail($id);
      return view('admin-finance.credit-collection.jv-requests.print', [
          'request' => $request
      ]);
  }

  public function printSummaryRequest($id)
  {
      $request = JournalVoucherRequest::with(['items', 'requestor', 'approver'])->findOrFail($id);
      return view('admin-finance.credit-collection.jv-requests.print-summary', [
          'request' => $request
      ]);
  }

  public function prepareAdjustmentRequest($id)
  {
      $request = JournalVoucherRequest::findOrFail($id);
      return view('admin-finance.credit-collection.jv-requests.prepare-adjustment', [
          'title' => 'Prepare Adjustment Form',
          'role' => 'Finance Manager',
          'sidebar' => 'admin-finance',
          'request' => $request
      ]);
  }

  public function updateAdjustmentRequest(Request $request, $id)
  {
      $jvRequest = JournalVoucherRequest::findOrFail($id);

      $request->validate([
          'date' => 'required|date',
          'client_name' => 'nullable|string|max:255',
          'category' => 'required|string',
          'reason' => 'required|string',
          'documents' => 'required|string',
          'remarks' => 'nullable|string',
          'supporting_documents' => 'nullable|file|mimes:pdf,jpg,png,zip|max:5120'
      ]);

      $data = $request->only(['date', 'client_name', 'category', 'reason', 'documents']);
      $data['accounting_remarks'] = $request->remarks ?? null;

      // Preserve existing path by default
      $docPath = $jvRequest->supporting_documents;

      if ($request->hasFile('supporting_documents')) {
          // store new file
          $docPath = $request->file('supporting_documents')->store('jv_supporting', 'public');
      }

      // Handle explicit removal request from UI
      if ($request->has('remove_supporting') && $request->remove_supporting) {
          if ($jvRequest->supporting_documents) {
              $old = storage_path('app/public/' . $jvRequest->supporting_documents);
              if (file_exists($old)) {
                  @unlink($old);
              }
          }
          $docPath = null;
      }

      $data['supporting_documents'] = $docPath;
      $data['status'] = 'pending_manager_approval';
      $jvRequest->update($data);

      return redirect()->route('admin-finance.credit-collection.billing')->with('success', 'Adjustment request submitted for Manager approval.');
  }
    /**
     * Stream/download the supporting documents file stored in storage/app/public
     */
    public function downloadSupportingDocuments($id)
    {
        $jvRequest = JournalVoucherRequest::findOrFail($id);
        if (!$jvRequest->supporting_documents) {
            return redirect()->back()->with('error', 'No supporting documents available for download.');
        }

        $relative = $jvRequest->supporting_documents; // e.g. jv_supporting/xyz.pdf
        $path = storage_path('app/public/' . $relative);
        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'Supporting document file not found on server.');
        }

        return response()->download($path, basename($path));
    }

}

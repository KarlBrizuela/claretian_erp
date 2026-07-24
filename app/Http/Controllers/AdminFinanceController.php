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
use App\Models\StockTransfer;
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
      // for Admin & Finance, include related departments and Payment Requests
      if (\Illuminate\Support\Str::contains($userDept, 'admin') || \Illuminate\Support\Str::contains($userDept, 'finance')) {
        if (\Illuminate\Support\Str::contains($dept, 'admin') || \Illuminate\Support\Str::contains($dept, 'finance') || \Illuminate\Support\Str::contains($dept, 'credit') || \Illuminate\Support\Str::contains($dept, 'account') || ($it['type'] ?? '') === 'Payment Request') return true;
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
    $cctvRequests = CCTVReq::with('user')
      ->whereIn('status', ['pending approval', 'Pending HR approval', 'Pending Final Approval'])
      ->orderBy('created_at', 'desc')
      ->get()
      ->filter(function ($request) use ($user) {
        return $request->canBeApprovedBy($user);
      });

    // --- Material Requests (only non-MIS/GSD) ---
    // Exclude MIS and GSD modules entirely from the unified approval queue
    $materialQuery = MaterialReq::with('user')->whereNotIn('module', ['GSD', 'MIS']);
    if ($pos === 'Super Admin') {
      $materialQuery->whereIn('status', [
        'pending approval', 'Pending Final Approval', 'forwarded to accounting', 'received',
        'pending_supervisor_approval', 'pending_admin_approval', 'pending_director_approval'
      ]);
    } else {
      $materialQuery->whereIn('status', [
        'pending approval', 'Pending Final Approval',
        'pending_supervisor_approval', 'pending_admin_approval', 'pending_director_approval'
      ]);
    }
    $materialRequests = $materialQuery->orderBy('created_at', 'desc')->get();
    if ($pos !== 'Super Admin') {
      $materialRequests = $materialRequests->filter(function ($request) use ($user) {
        return $request->canBeApprovedBy($user);
      });
    }

    // GSD material requests intentionally excluded from approval queue.

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

    // --- Service Requests (MIS) ---
    $serviceQuery = MisServiceRequest::query()->where('module', '!=', 'GSD');
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

    // --- GSD Service Requests ---
    $gsdServiceQuery = MisServiceRequest::query()->where('module', 'GSD');
    if (in_array($pos, ['Manager', 'MIS Supervisor'])) {
      $gsdServiceQuery->where('status', 'pending');
    } elseif (in_array($pos, ['HR Manager', 'HR Specialist', 'HR Staff'])) {
      $gsdServiceQuery->where('status', 'Pending HR approval');
    } elseif ($pos === 'Director') {
      $gsdServiceQuery->where('status', 'Pending Final Approval');
    } elseif ($pos === 'Super Admin') {
      $gsdServiceQuery->whereIn('status', ['pending', 'Pending HR approval', 'Pending Final Approval']);
    } else {
      $gsdServiceQuery->whereRaw('1 = 0');
    }
    $gsdServiceRequests = $gsdServiceQuery->orderBy('created_at', 'desc')->get();

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

    // Stock Transfers awaiting Accounting/Admin & Finance review
    $stockTransfers = StockTransfer::with(['fromSite', 'toSite', 'book', 'bookIndex.book', 'bookBundle', 'createdBy', 'approvedBy'])
      ->where('status', 'accounting_review')
      ->orderBy('created_at', 'desc')
      ->get()
      ->filter(function ($transfer) use ($user) {
        return $transfer->canBeReviewedByAccounting($user);
      });

    // --- Payment Requests ---
    $paymentRequestQuery = \App\Models\PaymentRequest::with(['requester', 'items']);
    if ($pos === 'Director') {
        $paymentRequestQuery->where('status', 'pending_director_approval');
    } elseif ($pos === 'Super Admin') {
        $paymentRequestQuery->whereIn('status', ['pending_director_approval', 'pending_admin_finance_approval']);
    } else {
        $isAFManager = str_contains($pos, 'Manager') && 
                       (str_contains($user->division, 'Admin') || str_contains($user->division, 'Finance') || str_contains($user->department, 'Admin') || str_contains($user->department, 'Finance'));
        $isAdmin = str_contains($pos, 'Admin') || $pos === 'A&F Manager' || $isAFManager;
        $isFinance = str_contains($pos, 'Finance') || str_contains($pos, 'Accounting') || $pos === 'A&F Manager' || $isAFManager;
        
        if ($isAdmin && $isFinance) {
            $paymentRequestQuery->where('status', 'pending_admin_finance_approval')
                                ->where(function($q) {
                                    $q->whereNull('admin_approved_by')
                                      ->orWhereNull('finance_approved_by');
                                });
        } elseif ($isAdmin) {
            $paymentRequestQuery->where('status', 'pending_admin_finance_approval')
                                ->whereNull('admin_approved_by');
        } elseif ($isFinance) {
            $paymentRequestQuery->where('status', 'pending_admin_finance_approval')
                                ->whereNull('finance_approved_by');
        } else {
            $paymentRequestQuery->whereRaw('1 = 0');
        }
    }
    $pendingPaymentRequests = $paymentRequestQuery->orderBy('created_at', 'desc')->get();

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
        'amount' => is_numeric($req->amount) ? (float)$req->amount : (float)0,
        'original' => $req
      ];
    }
    // GSD material requests intentionally excluded from the approval queue.
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
    foreach ($gsdServiceRequests as $req) {
      $pendingApprovals[] = [
        'type' => 'GSD Service',
        'id' => $req->service_req_id,
        'reference_no' => 'GSD-SRV-' . str_pad($req->service_req_id, 4, '0', STR_PAD_LEFT),
        'submitted_by' => $req->requestor_name,
        'submitted_date' => \Carbon\Carbon::parse($req->date)->format('M. d, Y'),
        'description' => \Illuminate\Support\Str::limit($req->nature_of_request, 50),
        'full_description' => $req->nature_of_request,
        'department' => 'GSD',
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
    foreach ($stockTransfers as $transfer) {
      $pendingApprovals[] = [
        'type' => 'Stock Transfer',
        'id' => $transfer->id,
        'reference_no' => 'ST-' . str_pad($transfer->id, 5, '0', STR_PAD_LEFT),
        'submitted_by' => $transfer->createdBy->name ?? 'Unknown',
        'submitted_date' => $transfer->created_at->format('M. d, Y'),
        'description' => ($transfer->item_name ?? 'Unknown Item') . ' from ' . ($transfer->fromSite->name ?? 'N/A') . ' to ' . ($transfer->toSite->name ?? 'N/A'),
        'full_description' => 'Transfer ' . $transfer->quantity . ' unit(s) of ' . ($transfer->item_name ?? 'Unknown Item') . ' from ' . ($transfer->fromSite->name ?? 'N/A') . ' to ' . ($transfer->toSite->name ?? 'N/A') . '.',
        'department' => 'Admin & Finance',
        'status' => $transfer->status,
        'amount' => $transfer->quantity . ' units',
        'original' => $transfer
      ];
    }

    foreach ($pendingPaymentRequests as $req) {
      $pendingApprovals[] = [
        'type' => 'Payment Request',
        'id' => $req->id,
        'reference_no' => 'PR-' . str_pad($req->id, 5, '0', STR_PAD_LEFT),
        'submitted_by' => $req->requester->name ?? 'N/A',
        'submitted_date' => $req->created_at->format('M. d, Y'),
        'description' => 'Payment request to: ' . $req->payment_to . ' for: ' . ($req->payment_for ?? 'N/A'),
        'full_description' => 'Payment request to ' . $req->payment_to . ' for ' . ($req->payment_for ?? 'N/A') . '. PO#: ' . ($req->po_number ?? 'N/A'),
        'department' => 'FORD / Production',
        'status' => $req->status,
        'amount' => (float)$req->total_amount,
        'url' => route('payment-requests.show', $req->id),
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
    $materialRequests = MaterialReq::where('user_id', auth()->id())
      ->latest()
      ->get();
    $cctvRequests = CCTVReq::where('user_id', auth()->id())
      ->latest()
      ->get();

    $mergedRequests = $cashAdvances->concat($materialRequests)->sortByDesc('created_at');

    return view('admin-finance.my-requests.index', [
      'title' => '',
      'role' => auth()->user()->position,
      'sidebar' => 'admin-finance',
      'cashAdvances' => $mergedRequests,
      'cctvRequests' => $cctvRequests,
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
        ->where(function($query) {
            $query->whereIn('status', ['pending_acct_approval', 'pending_si_approval', 'pending_dr_approval'])
                  ->where('type', '!=', 'ecom_direct');
        })
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
        'url' => $req['url'] ?? '#',
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

    $myPaymentRequests = \App\Models\PaymentRequest::where('requester_id', auth()->id())->latest()->get();
    foreach ($myPaymentRequests as $req) {
      $mySubmissions[] = [
        'type' => 'Payment Request',
        'id' => $req->id,
        'reference_no' => 'PR-' . str_pad($req->id, 5, '0', STR_PAD_LEFT),
        'submitted_date' => $req->created_at,
        'detail' => 'PhP ' . number_format($req->total_amount, 2) . ' - ' . $req->payment_to,
        'status' => $req->status,
        'url' => route('payment-requests.show', $req->id),
        'attachment' => $req->attachment_path,
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
          ->orWhere('approved_by_admin', $userId)
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
    // Get SalesOrders pending SI prep/approval
    $allOrders = \App\Models\SalesOrder::with('customer', 'preparedBy', 'siPreparedBy')
      ->whereIn('status', ['pending_si_prep', 'pending_si_approval', 'si_created'])
      ->whereNull('signed_by_af_manager')
      ->latest()
      ->get();

    $normalOrders = $allOrders->filter(function($order) {
        return $order->type !== 'ecom_direct';
    });

    $ecomOrders = $allOrders->filter(function($order) {
        return $order->type === 'ecom_direct';
    });

    // Get SalesInvoices from area consignment
    $areaConsignmentSIs = \App\Models\SalesInvoice::with('customer', 'salesOrder')
      ->where('transaction_type', 'area_consignment_si')
      ->whereIn('status', ['draft', 'pending_approval'])
      ->latest()
      ->get();

    return view('admin-finance.accounting.sales-invoice', [
      'title' => 'Sales Invoice Management',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance',
      'normalOrders' => $normalOrders,
      'ecomOrders' => $ecomOrders,
      'areaConsignmentSIs' => $areaConsignmentSIs
    ]);
  }

  public function prepareSalesInvoice($id)
  {
    $order = \App\Models\SalesOrder::with('customer', 'items.product', 'preparedBy')->findOrFail($id);

    if (!$order->proof_of_payment) {
      return redirect()->back()->with('error', 'Cannot proceed. Sales Order #' . $order->so_number . ' does not have a Proof of Payment attached.');
    }

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

    if (!$order->proof_of_payment) {
      return redirect()->route('admin-finance.accounting.sales-invoice')->with('error', 'Cannot proceed. Sales Order #' . $order->so_number . ' does not have a Proof of Payment attached.');
    }

    $isEcomDirect = $order->type === 'ecom_direct';

    if ($isEcomDirect) {
      $order->update([
        'status' => 'picking',
        'si_prepared_by' => auth()->id(),
        'si_prepared_at' => now(),
        'signed_by_af_manager' => auth()->id(),
        'signed_at' => now(),
        'remarks' => ($order->remarks ? $order->remarks . ' | ' : '') . 'SI Prepared and auto-signed by ' . auth()->user()->name
      ]);

      // --- ACCOUNTING INTEGRATION ---
      $this->accounting->postSalesOrderEntry($order);

      // Automatically create a pick list for E-Com Direct Invoice
      try {
        $order->load('items');
        if ($order->items && $order->items->count() > 0) {
          $pickList = \App\Models\PickList::create([
            'sales_order_id' => $order->id,
            'pick_list_number' => 'PL-' . $order->so_number . '-' . date('YmdHis'),
            'status' => 'in_progress',
            'prepared_by' => auth()->id(),
          ]);

          foreach ($order->items as $item) {
            \App\Models\PickListItem::create([
              'pick_list_id' => $pickList->id,
              'sales_order_item_id' => $item->id,
              'requested_qty' => $item->quantity,
              'picked_qty' => 0,
              'status' => 'pending'
            ]);
          }
          \Log::info('Automatically created pick list for E-com direct invoice on SI store: ' . $pickList->pick_list_number);
        }
      } catch (\Exception $e) {
        \Log::error('Failed to automatically create pick list for E-com direct invoice on SI store: ' . $e->getMessage());
      }

      return redirect()->route('admin-finance.accounting.sales-invoice')->with('success', 'Sales Invoice for #' . $order->so_number . ' has been prepared and finalized. Routed to pick list.');
    }

    // Normal flow
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

  public function bulkFinalizeInvoices(Request $request)
  {
    $ids = $request->input('ids', []);
    if (empty($ids)) {
      return response()->json(['success' => false, 'message' => 'No invoices selected.'], 400);
    }

    $processed = 0;
    $errors = [];

    // Run within a transaction for DB safety
    \DB::beginTransaction();
    try {
      foreach ($ids as $id) {
        $order = \App\Models\SalesOrder::findOrFail($id);

        if (!$order->proof_of_payment) {
          $errors[] = "Order #{$order->so_number} is missing Proof of Payment.";
          continue;
        }

        $isEcomDirect = $order->type === 'ecom_direct';

        if ($isEcomDirect) {
          $order->update([
            'status' => 'picking',
            'si_prepared_by' => auth()->id(),
            'si_prepared_at' => now(),
            'signed_by_af_manager' => auth()->id(),
            'signed_at' => now(),
            'remarks' => ($order->remarks ? $order->remarks . ' | ' : '') . 'SI Prepared and auto-signed in bulk by ' . auth()->user()->name
          ]);

          // --- ACCOUNTING INTEGRATION ---
          $this->accounting->postSalesOrderEntry($order);

          // Automatically create a pick list for E-Com Direct Invoice
          $order->load('items');
          if ($order->items && $order->items->count() > 0) {
            $pickList = \App\Models\PickList::create([
              'sales_order_id' => $order->id,
              'pick_list_number' => 'PL-' . $order->so_number . '-' . date('YmdHis'),
              'status' => 'in_progress',
              'prepared_by' => auth()->id(),
            ]);

            foreach ($order->items as $item) {
              \App\Models\PickListItem::create([
                'pick_list_id' => $pickList->id,
                'sales_order_item_id' => $item->id,
                'requested_qty' => $item->quantity,
                'picked_qty' => 0,
                'status' => 'pending'
              ]);
            }
          }
        } else {
          $order->update([
            'status' => 'pending_si_approval',
            'si_prepared_by' => auth()->id(),
            'si_prepared_at' => now(),
            'remarks' => ($order->remarks ? $order->remarks . ' | ' : '') . 'SI Prepared in bulk by ' . auth()->user()->name
          ]);

          // Send Notification to Director if status is "pending_si_approval"
          $director = \App\Models\User::where('position', 'Director')->first();
          if ($director) {
              try {
                  $director->notify(new \App\Notifications\DirectorApprovalRequested($order, 'Sales Order'));
              } catch (\Exception $e) {
                  \Log::error("Failed to send bulk Sales Order SI approval notification: " . $e->getMessage());
              }
          }
        }

        $processed++;
      }

      \DB::commit();

      $message = "Successfully finalized {$processed} sales invoice(s).";
      if (!empty($errors)) {
        $message .= " Gaps: " . implode(', ', $errors);
      }

      return response()->json([
        'success' => true,
        'message' => $message,
        'processed' => $processed,
        'errors' => $errors
      ]);

    } catch (\Exception $e) {
      \DB::rollBack();
      \Log::error('Error in bulk finalizing invoices: ' . $e->getMessage());
      return response()->json([
        'success' => false,
        'message' => 'Error bulk finalizing invoices: ' . $e->getMessage()
      ], 500);
    }
  }

  public function signSalesInvoice($id)
  {
    $order = \App\Models\SalesOrder::findOrFail($id);

    if (!$order->proof_of_payment) {
      return redirect()->back()->with('error', 'Cannot proceed. Sales Order #' . $order->so_number . ' does not have a Proof of Payment attached.');
    }

    $isEcomDirect = $order->type === 'ecom_direct';
    $newStatus = $isEcomDirect ? 'picking' : 'ready_for_delivery';

    $order->update([
      'status' => $newStatus,
      'signed_by_af_manager' => auth()->id(),
      'signed_at' => now()
    ]);

    // --- ACCOUNTING INTEGRATION ---
    $this->accounting->postSalesOrderEntry($order);

    if ($isEcomDirect) {
      // Automatically create a pick list for E-Com Direct Invoice after signing SI
      try {
        $order->load('items');
        if ($order->items && $order->items->count() > 0) {
          $pickList = \App\Models\PickList::create([
            'sales_order_id' => $order->id,
            'pick_list_number' => 'PL-' . $order->so_number . '-' . date('YmdHis'),
            'status' => 'in_progress',
            'prepared_by' => auth()->id(),
          ]);

          foreach ($order->items as $item) {
            \App\Models\PickListItem::create([
              'pick_list_id' => $pickList->id,
              'sales_order_item_id' => $item->id,
              'requested_qty' => $item->quantity,
              'picked_qty' => 0,
              'status' => 'pending'
            ]);
          }
          \Log::info('Automatically created pick list for E-com direct invoice: ' . $pickList->pick_list_number);
        }
      } catch (\Exception $e) {
        \Log::error('Failed to automatically create pick list for E-com direct invoice: ' . $e->getMessage());
      }
    }

    $successMsg = 'Sales Invoice for #' . $order->so_number . ' has been signed by Admin & Finance Manager.';
    if ($isEcomDirect) {
      $successMsg .= ' Routed to pick list.';
    }
    return redirect()->back()->with('success', $successMsg);
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
    $allowedStatuses = ['forwarded to accounting', 'processing', 'received'];
    $query = MaterialReq::with(['user', 'manager', 'director']);

    // Apply Filters for "All Requests" tab and general view
    if ($request->filled('status')) {
        $status = $request->status;
        if ($status === 'pending') $status = 'forwarded to accounting';
        elseif ($status === 'completed') $status = 'received';
        $query->where('status', $status);
    } else {
        $query->whereIn('status', $allowedStatuses);
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
    
    // Exclude MIS and GSD module requests from the accounting approver queue
    $pendingRequests = MaterialReq::with(['user', 'manager', 'director'])
      ->where('status', 'forwarded to accounting')
      ->whereNotIn('module', ['MIS', 'GSD'])
      ->latest()->get();
    $processingRequests = MaterialReq::with(['user', 'manager', 'director'])->where('status', 'processing')->latest()->get();
    $completedRequests = MaterialReq::with(['user', 'manager', 'director'])->where('status', 'received')->latest()->get();

    // Get requests by department/division
    $directRequests = MaterialReq::with(['user', 'manager', 'director'])
        ->whereIn('status', $allowedStatuses)
        ->where(function($q) {
            $q->where('module', 'Direct')
              ->orWhereHas('user', function($sq) {
                  $sq->where('department', 'like', '%direct%');
              });
        })
        ->latest()->get();

    $gsdRequests = MaterialReq::with(['user', 'manager', 'director'])
        ->whereIn('status', $allowedStatuses)
        ->where(function($q) {
            $q->where('module', 'GSD')
              ->orWhereHas('user', function($sq) {
                  $sq->where('department', 'like', '%gsd%');
              });
        })
        ->latest()->get();

    $misRequests = MaterialReq::with(['user', 'manager', 'director'])
        ->whereIn('status', $allowedStatuses)
        ->where(function($q) {
            $q->where('module', 'MIS')
              ->orWhereHas('user', function($sq) {
                  $sq->where('department', 'like', '%mis%');
              });
        })
        ->latest()->get();

    return view('admin-finance.accounting.material-requests', [
      'title' => 'Material Requests',
      'role' => auth()->user()->position,
      'sidebar' => 'admin-finance',
      'pendingRequests' => $pendingRequests,
      'processingRequests' => $processingRequests,
      'completedRequests' => $completedRequests,
      'allRequests' => $allRequests,
      'directRequests' => $directRequests,
      'gsdRequests' => $gsdRequests,
      'misRequests' => $misRequests
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
          ->whereNull('statement_of_account_id')
          ->where('payment_status', 'unpaid')
          ->whereIn('status', ['completed', 'ready_for_delivery', 'verified'])
          ->latest()
          ->get();

      $statements = \App\Models\StatementOfAccount::with('customer')->latest()->get();
      $freightBills = \App\Models\FreightBill::with('customer')->latest()->get();
      
      $jvRequests = \App\Models\JournalVoucherRequest::with(['requestor', 'items'])->latest()->get();
      $reconsignmentRequests = \App\Models\SalesOrder::with('customer')
          ->where('status', 'reconsignment_pending')
          ->latest()
          ->get();

      return view('admin-finance.credit-collection.billing', [
          'title' => 'Billing',
          'role' => 'Finance Manager',
          'sidebar' => 'admin-finance',
          'unpaidOrders' => $unpaidOrders,
          'statements' => $statements,
          'freightBills' => $freightBills,
          'jvRequests' => $jvRequests,
          'reconsignmentRequests' => $reconsignmentRequests
      ]);
  }

  public function reconsignmentsList()
  {
      $reconsignmentRequests = \App\Models\SalesOrder::with('customer')
          ->where('status', 'reconsignment_pending')
          ->latest()
          ->get();

      return view('admin-finance.credit-collection.reconsignments', [
          'title' => 'Reconsignments',
          'role' => 'Finance Manager',
          'sidebar' => 'admin-finance',
          'reconsignmentRequests' => $reconsignmentRequests
      ]);
  }

  public function approveReconsignment(Request $request, $id)
  {
      $order = \App\Models\SalesOrder::with(['items.book', 'customer'])->findOrFail($id);
      
      if ($order->status !== 'reconsignment_pending') {
          return redirect()->back()->with('error', 'Order is not in pending reconsignment status.');
      }

      // 1. Calculate return books for this request
      $returnedBooks = [];
      foreach ($order->items as $item) {
          $alreadyPurchasedQty = \App\Models\SalesInvoiceItem::whereHas('invoice', function($query) use ($order) {
              $query->where('so_id', $order->id)->where('status', '!=', 'cancelled');
          })->where('book_id', $item->book_id)->sum('quantity');
          
          $returnedQty = max(0, $item->quantity - $alreadyPurchasedQty);
          if ($returnedQty > 0) {
              $returnedBooks[] = [
                  'book_id' => $item->book_id,
                  'quantity' => $returnedQty,
                  'price' => $item->price,
                  'unit' => $item->unit ?? 'pcs',
                  'area' => $item->area ?? null,
                  'source_price_at_sale' => $item->source_price_at_sale ?? 0,
                  'book_name' => $item->book->name ?? 'Unknown Book'
              ];
          }
      }

      if (empty($returnedBooks)) {
          return redirect()->back()->with('error', 'No return books found for this reconsignment request.');
      }

      // Start Database Transaction
      \DB::beginTransaction();
      try {
          // 2. Generate a new unique SO number
          $baseSoNumber = $order->so_number;
          $newSoNumber = $baseSoNumber . '-R';
          $suffix = 1;
          while (\App\Models\SalesOrder::where('so_number', $newSoNumber)->exists()) {
              $newSoNumber = $baseSoNumber . '-R' . $suffix;
              $suffix++;
          }

          // 3. Create the new Sales Order
          $newOrder = \App\Models\SalesOrder::create([
              'customer_id' => $order->customer_id,
              'area_sales_staff_id' => $order->area_sales_staff_id,
              'so_number' => $newSoNumber,
              'type' => $order->type, // 'area_consignment'
              'transaction_type' => $order->transaction_type,
              'terms' => $request->input('terms', $order->terms),
              'ref_number' => $order->ref_number,
              'status' => 'ready_for_delivery', // ready to be delivered
              'billing_address' => $order->billing_address,
              'shipping_address' => $order->shipping_address,
              'freight_option' => $order->freight_option,
              'freight_charges' => $order->freight_charges,
              'freight_notes' => $order->freight_notes,
              'prepared_by' => auth()->id(),
              'dr_prepared_by' => auth()->id(),
              'dr_prepared_at' => now(),
              'total_amount' => 0 // will update after items creation
          ]);

          $newTotalAmount = 0;
          // 4. Create the new Sales Order Items
          foreach ($returnedBooks as $bookData) {
              $subtotal = $bookData['quantity'] * $bookData['price'];
              $newTotalAmount += $subtotal;

              \App\Models\SalesOrderItem::create([
                  'sales_order_id' => $newOrder->id,
                  'book_id' => $bookData['book_id'],
                  'quantity' => $bookData['quantity'],
                  'price' => $bookData['price'],
                  'subtotal' => $subtotal,
                  'unit' => $bookData['unit'],
                  'area' => $bookData['area'],
                  'source_price_at_sale' => $bookData['source_price_at_sale'],
              ]);
          }

          // Update new Sales Order total amount
          $newOrder->update(['total_amount' => $newTotalAmount]);

          // 5. Find the previous Delivery Receipt and close it
          $previousDr = \App\Models\DeliveryReceipt::where('so_id', $order->id)->first();
          if ($previousDr) {
              $previousDr->update(['status' => 'completed']);
          }

          // 6. Generate a new unique DR number
          $baseDrNumber = $previousDr ? $previousDr->dr_number : 'DR-' . $order->so_number;
          $newDrNumber = $baseDrNumber . '-R';
          $suffix = 1;
          while (\App\Models\DeliveryReceipt::where('dr_number', $newDrNumber)->exists()) {
              $newDrNumber = $baseDrNumber . '-R' . $suffix;
              $suffix++;
          }

          // 7. Create the new Delivery Receipt record
          $newDr = \App\Models\DeliveryReceipt::create([
              'dr_number' => $newDrNumber,
              'so_id' => $newOrder->id,
              'so_number' => $newOrder->so_number,
              'customer_id' => $newOrder->customer_id,
              'customer_name' => $order->customer->customer_name ?? $order->customer->company_name ?? '',
              'delivery_address' => $previousDr ? $previousDr->delivery_address : ($order->shipping_address ?: $order->customer->shipping_address ?? ''),
              'total_amount' => $newTotalAmount,
              'delivery_date' => now(),
              'status' => 'pending', // new DR is pending delivery / tracking
              'prepared_by' => auth()->id(),
              'prepared_at' => now()
          ]);

          // 8. Create the new Delivery Receipt items
          foreach ($returnedBooks as $bookData) {
              \App\Models\DeliveryReceiptItem::create([
                  'dr_id' => $newDr->id,
                  'product_name' => $bookData['book_name'],
                  'quantity' => $bookData['quantity'],
                  'unit_price' => $bookData['price'],
                  'amount' => $bookData['quantity'] * $bookData['price'],
              ]);
          }

          // 9. Close the previous Sales Order
          $order->update(['status' => 'completed']);

          // 10. Log activity
          \App\Models\ActivityLog::create([
              'user_id' => auth()->id(),
              'action' => 'Reconsignment Approved',
              'description' => "Reconsignment request approved for Sales Order {$order->so_number}. Created new Sales Order {$newOrder->so_number} & DR {$newDr->dr_number} for returned books.",
              'affected_model' => 'SalesOrder',
              'affected_model_id' => $order->id,
          ]);

          \DB::commit();
          return redirect()->back()->with('success', "Reconsignment request approved. New DR {$newDr->dr_number} created successfully and the previous DR is now closed.");

      } catch (\Exception $e) {
          \DB::rollBack();
          \Log::error('Error approving reconsignment: ' . $e->getMessage());
          return redirect()->back()->with('error', 'Error approving reconsignment: ' . $e->getMessage());
      }
  }

  public function rejectReconsignment($id)
  {
      $order = \App\Models\SalesOrder::findOrFail($id);
      
      if ($order->status !== 'reconsignment_pending') {
          return redirect()->back()->with('error', 'Order is not in pending reconsignment status.');
      }

      // Reject: set back to si_created (meaning closed)
      $order->update(['status' => 'si_created']);

      // Log activity
      \App\Models\ActivityLog::create([
          'user_id' => auth()->id(),
          'action' => 'Reconsignment Rejected',
          'description' => "Reconsignment request rejected for Sales Order {$order->so_number}.",
          'affected_model' => 'SalesOrder',
          'affected_model_id' => $order->id,
      ]);

      return redirect()->back()->with('success', 'Reconsignment request rejected.');
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
      $date = $request->input('date') ? \Carbon\Carbon::parse($request->input('date'))->startOfDay() : now();
      \App\Models\StatementOfAccount::whereIn('id', $ids)->update([
          'status' => 'compiled',
          'created_at' => $date
      ]);
      return response()->json(['success' => true]);
  }

  public function compileFreightBills(Request $request)
  {
      $ids = $request->input('ids');
      $date = $request->input('date') ? \Carbon\Carbon::parse($request->input('date'))->startOfDay() : now();
      \App\Models\FreightBill::whereIn('id', $ids)->update([
          'status' => 'compiled',
          'bill_date' => $date,
          'created_at' => $date
      ]);
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

  public function finalizeInvoice(Request $request, $id)
  {
    $order = \App\Models\SalesOrder::findOrFail($id);

    // Start database transaction
    \DB::beginTransaction();
    try {
      // 1. Update Sales Order status to 'completed' and payment_status to 'paid'
      $order->update([
        'status' => 'completed',
        'payment_status' => 'paid',
        'remarks' => ($order->remarks ? $order->remarks . ' | ' : '') . 'Invoice finalized by ' . auth()->user()->name
      ]);

      // 2. Post accounting journal entries using AccountingService
      $this->accounting->postSalesOrderEntry($order);

      \DB::commit();
      
      return response()->json([
        'success' => true,
        'message' => 'Sales Invoice finalized, customer balance updated, and posted to Charts of Accounts successfully.'
      ]);

    } catch (\Exception $e) {
      \DB::rollBack();
      \Log::error('Error finalizing invoice: ' . $e->getMessage());
      return response()->json([
        'success' => false,
        'message' => 'Error finalizing invoice: ' . $e->getMessage()
      ], 500);
    }
  }

  public function ecomPayoutsIndex()
  {
    $orders = \App\Models\SalesOrder::with(['customer', 'preparedBy'])
      ->where('type', 'ecom_direct')
      ->whereIn('status', ['picking', 'ready_for_delivery', 'completed', 'verified'])
      ->latest()
      ->get();

    return view('admin-finance.accounting.ecom-payouts.index', [
      'title' => 'E-com Direct Payouts',
      'role' => auth()->user()->position ?? 'Finance Manager',
      'sidebar' => 'admin-finance',
      'orders' => $orders
    ]);
  }

  public function ecomPayoutsToggle(Request $request, $id)
  {
    $order = \App\Models\SalesOrder::findOrFail($id);

    if ($order->type !== 'ecom_direct') {
      return redirect()->back()->with('error', 'This is not an E-com direct invoice.');
    }

    $newStatus = $order->ecom_payout_status === 'completed' ? 'pending' : 'completed';
    $order->update([
      'ecom_payout_status' => $newStatus,
      'remarks' => ($order->remarks ? $order->remarks . ' | ' : '') . 'E-com payout marked as ' . $newStatus . ' by ' . auth()->user()->name
    ]);

    return redirect()->back()->with('success', 'Payout status for Invoice #' . $order->so_number . ' updated to ' . ucfirst($newStatus) . '.');
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

    if ($order->type === 'ecom_direct') {
      return redirect()->back()->with('error', 'E-com direct invoices do not require accounting approval.');
    }
    
    \Log::info('Processing approval for SO #' . $order->so_number . ' with ' . $order->items->count() . ' items');
    

    // Normal flow for other transaction types
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

  public function jobOrders(Request $request)
  {
    $user = auth()->user();
    $pos = $user->position;
    $status = $request->query('status', 'all');
    if ($status === 'approved') {
        $status = 'on_hold';
    }
    
    if ($status === 'all') {
        $statuses = ['to submit', 'pending approval', 'Pending HR approval', 'Pending Final Approval', 'approved', 'ongoing', 'on_hold', 'completed', 'forwarded to accounting', 'processing', 'received'];
    } elseif ($status === 'on_hold') {
        $statuses = ['on_hold', 'approved'];
    } else {
        $statuses = [$status];
    }

    // --- CCTV Requests ---
    $cctvQuery = CCTVReq::query();
    if ($pos === 'Director') {
      $cctvQuery->where('status', '!=', 'Pending Final Approval');
    }
    $cctvRequests = $cctvQuery->whereIn('status', $statuses)->orderBy('created_at', 'desc')->get();
    $materialRequests = MaterialReq::where(function($q) {
        $q->where('module', 'MIS')
          ->orWhereHas('user', function($sq) {
              $sq->where('department', 'like', '%mis%');
          });
    })
    ->whereIn('status', $statuses)
    ->orderBy('created_at', 'desc')
    ->get();
    $qbRequests = MisQbRequest::with('items')->whereIn('status', $statuses)->orderBy('created_at', 'desc')->get();
    $undertimeRequests = MisUndertimeRequest::whereIn('status', $statuses)->orderBy('created_at', 'desc')->get();
    
    // Service Requests - Filter by MIS department
    $serviceRequests = MisServiceRequest::where('department', 'MIS')
        ->whereIn('status', $statuses)
        ->orderBy('created_at', 'desc')
        ->get();

    return view('admin-finance.mis.job-orders', [
      'title' => 'Job Orders',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance',
      'cctvRequests' => $cctvRequests,
      'materialRequests' => $materialRequests,
      'qbRequests' => $qbRequests,
      'undertimeRequests' => $undertimeRequests,
      'serviceRequests' => $serviceRequests,
      'currentStatus' => $status
    ]);
  }

  public function misUpdateJobOrderStatus(Request $request, $type, $id)
  {
    $validated = $request->validate([
      'status' => 'required|in:on_hold,ongoing,completed'
    ]);

    if ($type === 'cctv') {
      $order = CCTVReq::findOrFail($id);
    } elseif ($type === 'material') {
      $order = MaterialReq::findOrFail($id);
    } elseif ($type === 'qb') {
      $order = MisQbRequest::findOrFail($id);
    } elseif ($type === 'undertime') {
      $order = MisUndertimeRequest::findOrFail($id);
    } elseif ($type === 'service') {
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

  public function hrJobOrders(Request $request)
  {
    $status = $request->query('status', 'all');
    
    if ($status === 'all') {
        $statuses = ['to submit', 'pending approval', 'Pending HR approval', 'Pending Final Approval', 'approved', 'ongoing', 'on_hold', 'completed'];
    } else {
        $statuses = [$status];
    }
    
    $cctvRequests = CCTVReq::whereIn('status', $statuses)->orderBy('created_at', 'desc')->get();
    
    // Service Requests - Filter by HR department
    $serviceRequests = MisServiceRequest::where('department', 'HR')
        ->whereIn('status', $statuses)
        ->orderBy('created_at', 'desc')
        ->get();

    return view('admin-finance.hr.job-orders', [
      'title' => 'Job Orders',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance',
      'cctvRequests' => $cctvRequests,
      'serviceRequests' => $serviceRequests,
      'currentStatus' => $status
    ]);
  }

  // GSD Job Orders Method (New)
  // GSD Job Orders Method (New)
  public function gsdJobOrders(Request $request)
  {
    $status = $request->query('status', 'all');
    
    if ($status === 'all') {
        $statuses = ['to submit', 'pending approval', 'Pending HR approval', 'Pending Final Approval', 'approved', 'ongoing', 'on_hold', 'completed', 'forwarded to accounting', 'processing', 'received'];
    } else {
        $statuses = [$status];
    }
    
    // Material Requests - apply status filter
    $materialRequests = MaterialReq::where(function($q) {
        $q->where('module', 'GSD')
          ->orWhereHas('user', function($sq) {
              $sq->where('department', 'like', '%gsd%');
          });
    })
    ->whereIn('status', $statuses)
    ->orderBy('created_at', 'desc')
    ->get();
    
    // Service Requests - Filter by GSD department
    $serviceRequests = MisServiceRequest::where(function ($query) {
            $query->where('module', 'GSD')
                ->orWhere('department', 'GSD');
        })
        ->with('approver')
        ->whereIn('status', $statuses)
        ->orderBy('updated_at', 'desc')
        ->get();

    return view('admin-finance.gsd.job-orders', [
      'title' => 'GSD Job Orders',
      'role' => auth()->user()->position,
      'sidebar' => 'admin-finance',
      'materialRequests' => $materialRequests,
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

      if ($type == 'material') {
          $order = MaterialReq::findOrFail($id);
      } elseif ($type == 'qb') {
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

  // Create Service Request Form
  public function createServiceRequest()
  {
      abort_unless(auth()->user()->hasPermission('admin_finance.service_requests'), 403);

      return view('admin-finance.service-requests.create', [
          'title' => 'Create Service Request',
          'role' => auth()->user()->position,
          'sidebar' => 'admin-finance'
      ]);
  }

  // Store Service Request
  public function storeServiceRequest(Request $request)
  {
      abort_unless(auth()->user()->hasPermission('admin_finance.service_requests'), 403);

      $validated = $request->validate([
          'department' => 'required|in:GSD,MIS,HR,DTO',
          'requestor_name' => 'required|string|max:255',
          'date' => 'required|date',
          'nature_of_request' => 'required|string'
      ]);

      MisServiceRequest::create([
          'user_id' => auth()->id(),
          'module' => $validated['department'] === 'GSD' ? 'GSD' : 'MIS',
          'requestor_name' => $validated['requestor_name'],
          'date' => $validated['date'],
          'nature_of_request' => $validated['nature_of_request'],
          'department' => $validated['department'],
          'status' => 'to submit'
      ]);

      $departmentRoutes = [
          'GSD' => 'admin-finance.gsd.job-orders',
          'MIS' => 'admin-finance.mis.job-orders',
          'HR' => 'admin-finance.hr.job-orders',
          'DTO' => 'production.dto.job-request-form',
      ];

      $user = auth()->user();
      $canAccessProduction = $user->isSuperAdmin()
          || $user->division === 'All Divisions'
          || str_contains($user->division ?? '', 'Production')
          || $user->divisions()->where('division', 'Production Division')->exists();

      $redirectRoute = $validated['department'] === 'DTO' && ! $canAccessProduction
          ? 'admin-finance.service-requests.create'
          : $departmentRoutes[$validated['department']];

      return redirect()->route($redirectRoute)
          ->with('success', 'Service Request created successfully! View it in the ' . $validated['department'] . ' job orders.');
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

      $entryNo = 'JV-' . $jvRequest->jv_number;
      $entry = \App\Models\JournalEntry::where('entry_no', $entryNo)->first();

      if (!$entry) {
          $entry = \App\Models\JournalEntry::create([
              'entry_no' => $entryNo,
              'entry_type' => 'JV',
              'date' => $jvRequest->date,
              'reference' => 'JV #' . $jvRequest->jv_number,
              'memo' => $jvRequest->reason ?? 'Journal Voucher Request Approval',
              'currency' => 'PHP',
              'exchange_rate' => 1.0,
              'created_by' => auth()->id(),
              'status' => 'posted'
          ]);

          $arAccount = \App\Models\ChartOfAccount::where('code', '1200')->first();
          if (!$arAccount) {
              $arAccount = \App\Models\ChartOfAccount::where('name', 'like', '%Receivable%')->first()
                  ?? \App\Models\ChartOfAccount::where('type', 'Asset')->first();
          }

          if ($arAccount) {
              foreach ($jvRequest->items as $item) {
                  \App\Models\JournalEntryItem::create([
                      'journal_entry_id' => $entry->id,
                      'chart_of_account_id' => $arAccount->id,
                      'memo' => $item->customer_name . ' - ' . $item->reference_no,
                      'debit' => $item->amount,
                      'credit' => 0,
                  ]);
              }
          }
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

    private function getAccountBalanceAndTrack($namePattern, $type, &$trackedIds)
    {
        $account = \App\Models\ChartOfAccount::where('type', $type)
            ->where('name', 'like', $namePattern)
            ->first();

        if (!$account) {
            return 0.00;
        }

        $trackedIds[] = $account->id;

        $debitSum = $account->journalEntryItems()->sum('debit');
        $creditSum = $account->journalEntryItems()->sum('credit');

        if ($type === 'Asset' || $type === 'Expense') {
            return $debitSum - $creditSum;
        } else {
            return $creditSum - $debitSum;
        }
    }

    public function chartOfAccounts(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasPermission('admin_finance.accounting.chart_of_accounts') && !$user->hasPermission('admin_finance.accounting')) {
            abort(403, 'Unauthorized action.');
        }

        $tab = $request->query('tab', 'assets');
        if (!in_array($tab, ['assets', 'liabilities', 'equity', 'income'])) {
            $tab = 'assets';
        }

        $trackedIds = [];

        $balances = [
            // Assets
            'cash_on_hand' => $this->getAccountBalanceAndTrack('%Cash on Hand%', 'Asset', $trackedIds) + $this->getAccountBalanceAndTrack('%Undeposited Funds%', 'Asset', $trackedIds) ?: \App\Models\SalesInvoice::sum('total_amount'),
            'petty_cash' => $this->getAccountBalanceAndTrack('%Petty Cash%', 'Asset', $trackedIds) ?: \App\Models\PettyCashVoucher::withSum('items', 'amount')->get()->sum('items_sum_amount'),
            'bank_accounts' => $this->getAccountBalanceAndTrack('%Bank%', 'Asset', $trackedIds) + $this->getAccountBalanceAndTrack('%Cash in Bank%', 'Asset', $trackedIds),
            'receivables' => $this->getAccountBalanceAndTrack('%Receivable%', 'Asset', $trackedIds) + $this->getAccountBalanceAndTrack('%Trade/Accounts Receivable%', 'Asset', $trackedIds) ?: \App\Models\StatementOfAccount::sum('total_amount'),
            'inventory_raw_materials' => $this->getAccountBalanceAndTrack('%Raw Materials%', 'Asset', $trackedIds),
            'inventory_work_in_progress' => $this->getAccountBalanceAndTrack('%Work in Progress%', 'Asset', $trackedIds) + $this->getAccountBalanceAndTrack('%WIP%', 'Asset', $trackedIds),
            'inventory_finished_goods' => $this->getAccountBalanceAndTrack('%Finished Goods%', 'Asset', $trackedIds) + $this->getAccountBalanceAndTrack('%Inventory - Books%', 'Asset', $trackedIds) + $this->getAccountBalanceAndTrack('%Inventory - Consignment%', 'Asset', $trackedIds) ?: \App\Models\Book::sum(\DB::raw('stock * cost')),
            'fixed_assets' => $this->getAccountBalanceAndTrack('%Fixed Assets%', 'Asset', $trackedIds) + $this->getAccountBalanceAndTrack('%Equipment%', 'Asset', $trackedIds) + $this->getAccountBalanceAndTrack('%Property%', 'Asset', $trackedIds),
            'investments' => $this->getAccountBalanceAndTrack('%Investment%', 'Asset', $trackedIds),
            'deposits' => $this->getAccountBalanceAndTrack('%Deposit%', 'Asset', $trackedIds),

            // Liabilities
            'suppliers' => $this->getAccountBalanceAndTrack('%Supplier%', 'Liability', $trackedIds) + $this->getAccountBalanceAndTrack('%Accounts Payable%', 'Liability', $trackedIds) ?: \App\Models\PurchaseOrder::sum('total_amount'),
            'payables' => $this->getAccountBalanceAndTrack('%Payable%', 'Liability', $trackedIds),
            'loans' => $this->getAccountBalanceAndTrack('%Loan%', 'Liability', $trackedIds),
            'taxes' => $this->getAccountBalanceAndTrack('%Tax%', 'Liability', $trackedIds) + $this->getAccountBalanceAndTrack('%Withholding Tax Payable%', 'Liability', $trackedIds),
            'government_contributions' => $this->getAccountBalanceAndTrack('%Government%', 'Liability', $trackedIds) + $this->getAccountBalanceAndTrack('%Contribution%', 'Liability', $trackedIds) + $this->getAccountBalanceAndTrack('%SSS%', 'Liability', $trackedIds) + $this->getAccountBalanceAndTrack('%PhilHealth%', 'Liability', $trackedIds) + $this->getAccountBalanceAndTrack('%Pag-IBIG%', 'Liability', $trackedIds),
            'customer_deposits' => $this->getAccountBalanceAndTrack('%Customer Deposit%', 'Liability', $trackedIds),
            'unearned_revenue' => $this->getAccountBalanceAndTrack('%Unearned%', 'Liability', $trackedIds) + $this->getAccountBalanceAndTrack('%Deferred%', 'Liability', $trackedIds),

            // Equity
            'capital' => $this->getAccountBalanceAndTrack('%Capital%', 'Equity', $trackedIds),
            'retained_earnings' => $this->getAccountBalanceAndTrack('%Retained Earnings%', 'Equity', $trackedIds) + $this->getAccountBalanceAndTrack('%RE%', 'Equity', $trackedIds),
            'current_year_income' => $this->getAccountBalanceAndTrack('%Current Year%', 'Equity', $trackedIds) + $this->getAccountBalanceAndTrack('%Net Income%', 'Equity', $trackedIds) ?: \App\Models\SalesInvoice::sum('total_amount'),

            // Income (Publishing)
            'pub_book_sales' => $this->getAccountBalanceAndTrack('%Book Sales%', 'Income', $trackedIds) + $this->getAccountBalanceAndTrack('%Sales - Books%', 'Income', $trackedIds) ?: \App\Models\SalesInvoice::sum('total_amount'),
            'pub_royalties' => $this->getAccountBalanceAndTrack('%Royalties%', 'Income', $trackedIds) + $this->getAccountBalanceAndTrack('%Royalty%', 'Income', $trackedIds),
            'pub_rights_income' => $this->getAccountBalanceAndTrack('%Rights Income%', 'Income', $trackedIds) + $this->getAccountBalanceAndTrack('%Rights%', 'Income', $trackedIds),
            'pub_licensing' => $this->getAccountBalanceAndTrack('%Licensing%', 'Income', $trackedIds) + $this->getAccountBalanceAndTrack('%License%', 'Income', $trackedIds),
            'pub_ebooks' => $this->getAccountBalanceAndTrack('%E-book%', 'Income', $trackedIds) + $this->getAccountBalanceAndTrack('%Ebook%', 'Income', $trackedIds),

            // Income (Printing)
            'print_income' => $this->getAccountBalanceAndTrack('%Printing Income%', 'Income', $trackedIds) + $this->getAccountBalanceAndTrack('%Printing%', 'Income', $trackedIds),
            'print_layout' => $this->getAccountBalanceAndTrack('%Layout%', 'Income', $trackedIds),
            'print_design' => $this->getAccountBalanceAndTrack('%Design%', 'Income', $trackedIds),
            'print_binding' => $this->getAccountBalanceAndTrack('%Binding%', 'Income', $trackedIds),
            'print_lamination' => $this->getAccountBalanceAndTrack('%Lamination%', 'Income', $trackedIds),

            // Income (Marketing)
            'mkt_direct_sales' => $this->getAccountBalanceAndTrack('%Direct Sales%', 'Income', $trackedIds),
            'mkt_area_sales' => $this->getAccountBalanceAndTrack('%Area Sales%', 'Income', $trackedIds),
            'mkt_cob_sales' => $this->getAccountBalanceAndTrack('%COB%', 'Income', $trackedIds),
            'mkt_lazada' => $this->getAccountBalanceAndTrack('%Lazada%', 'Income', $trackedIds),
            'mkt_shopee' => $this->getAccountBalanceAndTrack('%Shopee%', 'Income', $trackedIds),
            'mkt_tiktok' => $this->getAccountBalanceAndTrack('%Tiktok%', 'Income', $trackedIds),
            'mkt_facebook' => $this->getAccountBalanceAndTrack('%Facebook%', 'Income', $trackedIds),
            'mkt_wholesale' => $this->getAccountBalanceAndTrack('%Wholesale%', 'Income', $trackedIds),
            'mkt_export' => $this->getAccountBalanceAndTrack('%Export%', 'Income', $trackedIds),
            'mkt_claret_media' => $this->getAccountBalanceAndTrack('%Claret Media%', 'Income', $trackedIds),

            // Income (Other)
            'oth_donations' => $this->getAccountBalanceAndTrack('%Donation%', 'Income', $trackedIds) + $this->getAccountBalanceAndTrack('%Donations%', 'Income', $trackedIds),
            'oth_grants' => $this->getAccountBalanceAndTrack('%Grant%', 'Income', $trackedIds) + $this->getAccountBalanceAndTrack('%Grants%', 'Income', $trackedIds),
            'oth_investments' => $this->getAccountBalanceAndTrack('%Other Investment%', 'Income', $trackedIds),
            'oth_interest_income' => $this->getAccountBalanceAndTrack('%Interest%', 'Income', $trackedIds),
            'oth_rental_income' => $this->getAccountBalanceAndTrack('%Rental%', 'Income', $trackedIds) + $this->getAccountBalanceAndTrack('%Rent%', 'Income', $trackedIds),
        ];

        // Also fetch any dynamic/uncategorized accounts from the database that don't match our pre-defined names
        $typeFilter = '';
        if ($tab === 'assets') {
            $typeFilter = 'Asset';
        } elseif ($tab === 'liabilities') {
            $typeFilter = 'Liability';
        } elseif ($tab === 'equity') {
            $typeFilter = 'Equity';
        } elseif ($tab === 'income') {
            $typeFilter = 'Income';
        }

        $uncategorizedAccounts = \App\Models\ChartOfAccount::where('type', $typeFilter)
            ->whereNotIn('id', $trackedIds)
            ->get()
            ->map(function($acc) use ($typeFilter) {
                $debit = $acc->journalEntryItems()->sum('debit');
                $credit = $acc->journalEntryItems()->sum('credit');
                if ($typeFilter === 'Asset' || $typeFilter === 'Expense') {
                    $acc->balance = $debit - $credit;
                } else {
                    $acc->balance = $credit - $debit;
                }
                return $acc;
            });

        // Query operational transaction details for interactive modals
        $salesInvoices = \App\Models\SalesInvoice::select('si_number', 'total_amount', 'status', 'customer_name', 'created_at')->latest()->get();
        $pettyCashVouchers = \App\Models\PettyCashVoucher::withSum('items', 'amount')->latest()->get();
        $statementOfAccounts = \App\Models\StatementOfAccount::select('soa_number', 'total_amount', 'status', 'created_at')->latest()->get();
        $books = \App\Models\Book::select('name', 'stock', 'cost')->where('stock', '>', 0)->get();
        $purchaseOrders = \App\Models\PurchaseOrder::select('po_number', 'total_amount', 'status', 'created_at')->latest()->get();

        return view('admin-finance.accounting.chart-of-accounts', [
            'title' => 'Chart of Accounts - ' . ucfirst($tab),
            'role' => $user->position,
            'sidebar' => 'admin-finance',
            'tab' => $tab,
            'balances' => $balances,
            'uncategorizedAccounts' => $uncategorizedAccounts,
            'salesInvoices' => $salesInvoices,
            'pettyCashVouchers' => $pettyCashVouchers,
            'statementOfAccounts' => $statementOfAccounts,
            'books' => $books,
            'purchaseOrders' => $purchaseOrders,
        ]);
    }

    public function salesManagement(Request $request)
    {
        $tab = $request->query('tab', 'bookstore');
        $user = auth()->user();

        // 1. Bookstore data
        $bookstoreDailySales = \App\Models\SalesOrder::whereIn('type', ['paid', 'calculator_pos'])
            ->whereDate('created_at', today())
            ->sum('total_amount') ?: 0.00;
        
        $bookstoreCashSales = \App\Models\SalesOrder::whereIn('type', ['paid', 'calculator_pos'])
            ->where('payment_method', 'cash')
            ->sum('total_amount') ?: 0.00;
            
        $bookstoreGcashSales = \App\Models\SalesOrder::whereIn('type', ['paid', 'calculator_pos'])
            ->whereIn('payment_method', ['gcash', 'qr_ph', 'ewallet', 'GCash'])
            ->sum('total_amount') ?: 0.00;
            
        $bookstoreCardSales = \App\Models\SalesOrder::whereIn('type', ['paid', 'calculator_pos'])
            ->whereIn('payment_method', ['credit_card', 'card', 'Credit Card'])
            ->sum('total_amount') ?: 0.00;
            
        $bookstoreChargeSales = \App\Models\SalesOrder::where('type', 'charge')
            ->sum('total_amount') ?: 0.00;

        // Bookstore Detail Lists
        $bookstoreDailyOrders = \App\Models\SalesOrder::whereIn('type', ['paid', 'calculator_pos'])
            ->latest()
            ->get();
        
        $bookstoreCashOrders = \App\Models\SalesOrder::whereIn('type', ['paid', 'calculator_pos'])
            ->where('payment_method', 'cash')
            ->latest()
            ->get();

        $bookstoreGcashOrders = \App\Models\SalesOrder::whereIn('type', ['paid', 'calculator_pos'])
            ->whereIn('payment_method', ['gcash', 'qr_ph', 'ewallet', 'GCash'])
            ->latest()
            ->get();

        $bookstoreCardOrders = \App\Models\SalesOrder::whereIn('type', ['paid', 'calculator_pos'])
            ->whereIn('payment_method', ['credit_card', 'card', 'Credit Card'])
            ->latest()
            ->get();

        $bookstoreChargeOrders = \App\Models\SalesOrder::where('type', 'charge')
            ->latest()
            ->get();

        // 2. E-Commerce Platform Data
        $ecomWebsiteSales = \App\Models\SalesOrder::where('platform', 'website')->sum('total_amount') ?: 0.00;
        $ecomShopeeSales = \App\Models\SalesOrder::where('platform', 'shopee')->sum('total_amount') ?: 0.00;
        $ecomLazadaSales = \App\Models\SalesOrder::where('platform', 'lazada')->sum('total_amount') ?: 0.00;
        $ecomFacebookSales = \App\Models\SalesOrder::where('platform', 'facebook')->sum('total_amount') ?: 0.00;
        $ecomTiktokSales = \App\Models\SalesOrder::where('platform', 'tiktok')->sum('total_amount') ?: 0.00;
        
        $ecomWebsiteOrders = \App\Models\SalesOrder::where('platform', 'website')->latest()->get();
        $ecomShopeeOrders = \App\Models\SalesOrder::where('platform', 'shopee')->latest()->get();
        $ecomLazadaOrders = \App\Models\SalesOrder::where('platform', 'lazada')->latest()->get();
        $ecomFacebookOrders = \App\Models\SalesOrder::where('platform', 'facebook')->latest()->get();
        $ecomTiktokOrders = \App\Models\SalesOrder::where('platform', 'tiktok')->latest()->get();

        // 3. Area Sales Data
        $areaRepSales = \App\Models\SalesOrder::whereNotNull('area_sales_staff_id')->sum('total_amount') ?: 0.00;
        $areaOrders = \App\Models\SalesOrder::whereNotNull('area_sales_staff_id')
            ->with(['createdBy', 'customer'])
            ->latest()
            ->get();

        return view('admin-finance.accounting.sales-management', [
            'title' => 'Sales Management - ' . ucfirst($tab),
            'role' => $user ? $user->position : 'Staff',
            'sidebar' => 'admin-finance',
            'tab' => $tab,
            
            // Bookstore metrics
            'bookstoreDailySales' => $bookstoreDailySales,
            'bookstoreCashSales' => $bookstoreCashSales,
            'bookstoreGcashSales' => $bookstoreGcashSales,
            'bookstoreCardSales' => $bookstoreCardSales,
            'bookstoreChargeSales' => $bookstoreChargeSales,
            
            // Bookstore lists
            'bookstoreDailyOrders' => $bookstoreDailyOrders,
            'bookstoreCashOrders' => $bookstoreCashOrders,
            'bookstoreGcashOrders' => $bookstoreGcashOrders,
            'bookstoreCardOrders' => $bookstoreCardOrders,
            'bookstoreChargeOrders' => $bookstoreChargeOrders,
            
            // Ecom metrics
            'ecomWebsiteSales' => $ecomWebsiteSales,
            'ecomShopeeSales' => $ecomShopeeSales,
            'ecomLazadaSales' => $ecomLazadaSales,
            'ecomFacebookSales' => $ecomFacebookSales,
            'ecomTiktokSales' => $ecomTiktokSales,
            
            // Ecom lists
            'ecomWebsiteOrders' => $ecomWebsiteOrders,
            'ecomShopeeOrders' => $ecomShopeeOrders,
            'ecomLazadaOrders' => $ecomLazadaOrders,
            'ecomFacebookOrders' => $ecomFacebookOrders,
            'ecomTiktokOrders' => $ecomTiktokOrders,

            // Area sales
            'areaRepSales' => $areaRepSales,
            'areaOrders' => $areaOrders,
        ]);
    }

    public function accountsReceivable(Request $request)
    {
        $user = auth()->user();
        
        $dbCustomers = \App\Models\Customer::orderBy('customer_name')->get();
        $customers = collect();

        foreach ($dbCustomers as $cust) {
            $unpaidOrders = \App\Models\SalesOrder::where('customer_id', $cust->customer_id)
                ->where('payment_status', '!=', 'paid')
                ->get();

            $outstanding = $unpaidOrders->sum('total_amount') ?: 0.00;
            
            $customers->push((object)[
                'customer_id' => $cust->customer_id,
                'customer_name' => $cust->customer_name,
                'company_name' => $cust->company_name ?: $cust->customer_name,
                'account_number' => $cust->account_number ?: 'ACC-' . str_pad($cust->customer_id, 4, '0', STR_PAD_LEFT),
                'credit_limit' => $cust->credit_limit ?: 0.00,
                'payment_terms' => $cust->payment_terms ?: 'Due on receipt',
                'outstanding_balance' => $outstanding,
                'credit_rating' => $cust->credit_limit >= 200000 ? 'AAA' : ($cust->credit_limit >= 100000 ? 'AA' : 'A'),
                'rep' => $cust->rep,
                'sales_rep' => $cust->rep === 'CLE' ? 'Xavier Almocera' : ($cust->rep === 'MKT' ? 'Kerwin Morfe' : 'N/A'),
                'main_phone' => $cust->mobile ?: ($cust->main_phone ?: 'N/A'),
                'main_email' => $cust->main_email ?: 'N/A',
                'billing_address' => $cust->billing_address ?: 'N/A',
                'interest_rate' => 1.5,
                'overdue_amount' => 0.00,
                'bad_debts' => 0.00,
                'accrued_interest' => 0.00
            ]);
        }

        return view('admin-finance.accounting.accounts-receivable', [
            'title' => 'Accounts Receivable Ledger',
            'role' => $user ? $user->position : 'Staff',
            'sidebar' => 'admin-finance',
            'customers' => $customers,
        ]);
    }

    public function updateCustomerRep(Request $request, $id)
    {
        $customer = \App\Models\Customer::findOrFail($id);
        
        $request->validate([
            'rep' => 'nullable|string|in:CLE,MKT'
        ]);

        $customer->rep = $request->rep ?: null;
        $customer->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Sales Representative updated successfully.'
        ]);
    }
}

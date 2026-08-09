<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductionController extends Controller
{
    public function dashboard()
    {
        $activeJobRequests = \Illuminate\Support\Facades\Schema::hasTable('job_requests')
            ? \App\Models\JobRequest::where('status', '!=', 'Completed')->count()
            : 0;

        $pendingPurchaseOrders = \Illuminate\Support\Facades\Schema::hasTable('purchase_orders')
            ? \App\Models\PurchaseOrder::where('status', '!=', 'completed')->count()
            : 0;

        $activePrintingJobs = \Illuminate\Support\Facades\Schema::hasTable('production_costings')
            ? \App\Models\ProductionCosting::count()
            : 0;

        $pendingPaymentRequests = \Illuminate\Support\Facades\Schema::hasTable('payment_requests')
            ? \App\Models\PaymentRequest::where('status', 'like', 'pending%')->count()
            : 0;

        $recentActivities = \Illuminate\Support\Facades\Schema::hasTable('activity_logs')
            ? \App\Models\ActivityLog::with('user')
                ->latest()
                ->take(5)
                ->get()
                ->map(function($log) {
                    $actionLower = strtolower($log->action);
                    $icon = 'las la-bell';
                    $color = 'secondary';
                    
                    if (str_contains($actionLower, 'purchase') || str_contains($actionLower, 'po')) {
                        $icon = 'las la-shopping-cart';
                        $color = 'success';
                    } elseif (str_contains($actionLower, 'job') || str_contains($actionLower, 'reconsignment') || str_contains($actionLower, 'delivery')) {
                        $icon = 'las la-truck';
                        $color = 'primary';
                    } elseif (str_contains($actionLower, 'pick') || str_contains($actionLower, 'print')) {
                        $icon = 'las la-print';
                        $color = 'warning';
                    } elseif (str_contains($actionLower, 'payment') || str_contains($actionLower, 'debit') || str_contains($actionLower, 'invoice') || str_contains($actionLower, 'si')) {
                        $icon = 'las la-money-bill-wave';
                        $color = 'info';
                    }
                    
                    $details = '';
                    if (!empty($log->description) && !str_starts_with(trim($log->description), '{')) {
                        $details = $log->description;
                    } elseif (!empty($log->details)) {
                        $parsed = is_string($log->details) ? json_decode($log->details, true) : $log->details;
                        if (is_array($parsed)) {
                            if (isset($parsed['so_number'])) {
                                $details = "SO #" . $parsed['so_number'] . (isset($parsed['action']) ? " (" . $parsed['action'] . ")" : (isset($parsed['marked_by']) ? " by " . $parsed['marked_by'] : ""));
                            } elseif (isset($parsed['po_number'])) {
                                $details = "PO #" . $parsed['po_number'];
                            } elseif (isset($parsed['pick_list_number'])) {
                                $details = "Pick List: " . $parsed['pick_list_number'];
                            } elseif (isset($parsed['gathered_at'])) {
                                $details = "Gathered at: " . date('M d, Y H:i', strtotime($parsed['gathered_at']));
                            } elseif (isset($parsed['packed_by'])) {
                                $details = "Packed by " . $parsed['packed_by'] . (isset($parsed['boxes_count']) ? " ({$parsed['boxes_count']} box" . ($parsed['boxes_count'] > 1 ? "es" : "") . ")" : "");
                            } elseif (isset($parsed['marked_by'])) {
                                $details = "Marked by " . $parsed['marked_by'];
                            } elseif (isset($parsed['description'])) {
                                $details = $parsed['description'];
                            } else {
                                $parts = [];
                                foreach ($parsed as $k => $v) {
                                    if (is_string($v) || is_numeric($v)) {
                                        $parts[] = ucwords(str_replace('_', ' ', $k)) . ': ' . $v;
                                    }
                                }
                                $details = implode(', ', $parts);
                            }
                        } else {
                            $details = $log->details;
                        }
                    }

                    if (empty($details) || str_starts_with(trim($details), '{')) {
                        $details = $log->user->name ?? 'System Action';
                    }
                    
                    return [
                        'title' => $log->action,
                        'desc' => $details,
                        'time' => $log->created_at->diffForHumans(),
                        'icon' => $icon,
                        'color' => $color
                    ];
                })
            : collect();

        return view('production.dashboard', [
            'title' => 'Production Dashboard',
            'role' => auth()->user()->position,
            'sidebar' => 'production',
            'stats' => [
                'active_job_requests' => $activeJobRequests,
                'pending_purchase_orders' => $pendingPurchaseOrders,
                'active_printing_jobs' => $activePrintingJobs,
                'pending_payment_requests' => $pendingPaymentRequests,
            ],
            'recentActivities' => $recentActivities
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
                ->whereIn('status', ['pending', 'logistics_assignment', 'logistics_assigned', 'completed'])
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

        $autoDebitQuery = \App\Models\AutoDebit::with('preparer');
        if ($pos === 'Director') {
            $autoDebitQuery->where('status', 'pending_director');
        } elseif ($pos === 'Super Admin') {
            $autoDebitQuery->whereIn('status', ['pending_director', 'pending_finance']);
        } else {
            $isApprover = str_contains($pos, 'Manager') || str_contains($pos, 'Supervisor');
            if ($isApprover) {
                $autoDebitQuery->where('status', 'pending_finance');
            } else {
                $autoDebitQuery->whereRaw('1 = 0');
            }
        }
        $pendingAutoDebits = $autoDebitQuery->latest()->get();

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
                'reference_no' => 'MAT-' . str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT),
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
            if (in_array($transfer->status, ['pending', 'logistics_assignment'])) {
                $myApprovals[] = [
                    'type' => 'Stock Transfer',
                    'id' => $transfer->id,
                    'reference_no' => 'ST-' . str_pad($transfer->id, 5, '0', STR_PAD_LEFT),
                    'submitted_by' => $transfer->createdBy->name ?? 'N/A',
                    'submitted_date' => $transfer->created_at,
                    'amount' => $transfer->quantity . ' units',
                    'attachment' => null,
                    'status' => $transfer->status === 'pending' ? 'pending approval' : 'pending assignment',
                    'description' => ($transfer->book->name ?? 'Unknown Book') . ' from ' . ($transfer->fromSite->name ?? 'N/A') . ' to ' . ($transfer->toSite->name ?? 'N/A'),
                    'original' => $transfer
                ];
            }
        }

        foreach ($pendingAutoDebits as $debit) {
            $myApprovals[] = [
                'type' => 'Auto Debit Letter',
                'id' => $debit->id,
                'reference_no' => 'AD-' . str_pad($debit->id, 5, '0', STR_PAD_LEFT),
                'submitted_by' => $debit->preparer->name ?? 'N/A',
                'submitted_date' => $debit->created_at,
                'amount' => '₱' . number_format($debit->amount, 2),
                'attachment' => null,
                'status' => $debit->status === 'pending_director' ? 'Pending Director' : 'Pending Finance',
                'original' => $debit
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

    public function executiveDashboard(Request $request)
    {
        $today = date('Y-m-d');
        $thisMonth = date('Y-m');

        // Core sales order check helper (paid / proof of payment / ecom_direct / POS / cash, excluding complimentary)
        $salesFilter = function($q) {
            $q->where('type', '!=', 'complimentary')
              ->where(function($sub) {
                $sub->whereNotNull('proof_of_payment')->where('proof_of_payment', '!=', '')
                   ->orWhere('type', 'ecom_direct')
                   ->orWhere('type', 'calculator_pos')
                   ->orWhere('payment_method', 'cash');
            });
        };

        // 1. Core Financial & Sales KPIs from real DB queries
        $todaysSales = (float) \App\Models\CashTransaction::where('category', 'Inflow')->whereDate('transaction_date', $today)->sum('amount');
        if (\Schema::hasTable('sales_orders')) {
            $todaysSales += (float) \App\Models\SalesOrder::whereDate('created_at', $today)->where($salesFilter)->sum('total_amount');
        }

        $thisMonthsRevenue = (float) \App\Models\CashTransaction::where('category', 'Inflow')->where('transaction_date', 'like', "{$thisMonth}%")->sum('amount');
        if (\Schema::hasTable('sales_orders')) {
            $thisMonthsRevenue += (float) \App\Models\SalesOrder::where('created_at', 'like', "{$thisMonth}%")->where($salesFilter)->sum('total_amount');
        }

        $totalExpenses = (float) \App\Models\CashTransaction::where('category', 'Outflow')->where('transaction_date', 'like', "{$thisMonth}%")->sum('amount');

        $netIncome = $thisMonthsRevenue - $totalExpenses;

        // Calculate dynamic cash position from General Ledger balances + unposted cash/POS sales orders
        $cashPosition = 0.00;
        if (\Schema::hasTable('chart_of_accounts') && \Schema::hasTable('journal_entry_items')) {
            $cashAccountIds = \App\Models\ChartOfAccount::where('type', 'Asset')
                ->where(function($q) {
                    $q->where('name', 'like', '%Cash%')
                      ->orWhere('name', 'like', '%Bank%')
                      ->orWhere('name', 'like', '%Undeposited%');
                })
                ->pluck('id');
            
            if ($cashAccountIds->isNotEmpty()) {
                $debits = \DB::table('journal_entry_items')
                    ->whereIn('chart_of_account_id', $cashAccountIds)
                    ->sum('debit');
                $credits = \DB::table('journal_entry_items')
                    ->whereIn('chart_of_account_id', $cashAccountIds)
                    ->sum('credit');
                $cashPosition = (float) ($debits - $credits);
            }
        }

        if (\Schema::hasTable('sales_orders')) {
            $unpostedOrders = \App\Models\SalesOrder::where('status', '!=', 'cancelled')
                ->where(function($q) {
                    $q->where('payment_status', 'paid')
                      ->orWhere('type', 'calculator_pos')
                      ->orWhere('payment_method', 'cash')
                      ->orWhere(function($sub) {
                          $sub->whereNotNull('proof_of_payment')->where('proof_of_payment', '!=', '');
                      });
                })
                ->get();
            
            foreach ($unpostedOrders as $order) {
                $hasJE = \Schema::hasTable('journal_entries') && \App\Models\JournalEntry::where('reference', $order->so_number)->exists();
                if (!$hasJE) {
                    $cashPosition += (float) $order->total_amount;
                }
            }
        }

        if ($cashPosition <= 0 && \Schema::hasTable('company_bank_accounts')) {
            $cashPosition = (float) \App\Models\CompanyBankAccount::sum('current_balance');
        }

        $outstandingReceivables = \Schema::hasTable('sales_invoices') ? (float) \DB::table('sales_invoices')->where('status', '!=', 'Paid')->sum('total_amount') : 0.00;

        $payablesDue = \Schema::hasTable('supplier_invoices') ? (float) \DB::table('supplier_invoices')->where('status', '!=', 'paid')->sum(\DB::raw('total_amount - amount_paid')) : 0.00;

        $productionCost = (float) \App\Models\ProductionCosting::sum('total_cogs');

        $inventoryValue = 0.00;
        if (\Schema::hasTable('site_inventories')) {
            $inventoryValue = (float) \DB::table('site_inventories')
                ->join('books', 'site_inventories.book_id', '=', 'books.id')
                ->sum(\DB::raw('site_inventories.quantity * books.price'));
        }

        $payrollThisMonth = (float) \App\Models\CashTransaction::where('transaction_type', 'Payroll')->where('transaction_date', 'like', "{$thisMonth}%")->sum('amount');

        $taxDue = \Schema::hasTable('supplier_invoices') ? (float) \DB::table('supplier_invoices')->sum('withholding_tax_amount') : 0.00;

        $donationIncome = (float) \App\Models\Donation::sum('amount');

        $investmentValuation = (float) \App\Models\Investment::sum('current_value');

        $totalBudgetAllocated = (float) \App\Models\DepartmentBudget::sum('allocated_budget');
        $totalBudgetActual = (float) \App\Models\DepartmentBudget::sum('actual_spend');
        $budgetUtilization = $totalBudgetAllocated > 0 ? round(($totalBudgetActual / $totalBudgetAllocated) * 100, 1) : 0.0;

        $forecastedCash = $cashPosition + ($outstandingReceivables - $payablesDue);

        // 2. Real Database Ranking Lists - Top Selling Books based on actual quantity and revenue sold
        $topSellingBooks = [];
        if (\Schema::hasTable('sales_order_items') && \Schema::hasTable('books')) {
            $topItems = \App\Models\SalesOrderItem::select(
                    'book_id',
                    \DB::raw('SUM(quantity) as total_units_sold'),
                    \DB::raw('SUM(subtotal) as total_revenue')
                )
                ->whereHas('order', function($q) use ($salesFilter) {
                    $q->where('status', '!=', 'cancelled')->where($salesFilter);
                })
                ->whereNotNull('book_id')
                ->groupBy('book_id')
                ->orderByDesc('total_revenue')
                ->take(5)
                ->with('book')
                ->get();

            foreach ($topItems as $item) {
                if ($item->book) {
                    $topSellingBooks[] = [
                        'name' => $item->book->name,
                        'sku' => $item->book->sku ?: 'N/A',
                        'units_sold' => (int) $item->total_units_sold,
                        'revenue' => (float) $item->total_revenue,
                    ];
                }
            }
        }

        // Fallback/Padding: If less than 5 books have sales, pad with other books ordered by stock descending but with 0 revenue/sales.
        if (count($topSellingBooks) < 5 && \Schema::hasTable('books')) {
            $excludeIds = isset($topItems) ? $topItems->pluck('book_id')->toArray() : [];
            $otherBooks = \App\Models\Book::whereNotIn('id', $excludeIds)
                ->orderBy('stock', 'desc')
                ->take(5 - count($topSellingBooks))
                ->get();
                
            foreach ($otherBooks as $bk) {
                $topSellingBooks[] = [
                    'name' => $bk->name,
                    'sku' => $bk->sku ?: 'N/A',
                    'units_sold' => 0,
                    'revenue' => 0.00,
                ];
            }
        }

        $worstSellingBooks = [];
        if (\Schema::hasTable('books')) {
            $worstSellingBooks = \App\Models\Book::orderBy('stock', 'asc')->take(5)->get()->map(function($bk) {
                return [
                    'name' => $bk->name,
                    'sku' => $bk->sku ?: 'N/A',
                    'stock_remaining' => (int) $bk->stock,
                    'velocity' => 'Low Stock',
                ];
            })->toArray();
        }

        $bestCustomers = [];
        if (\Schema::hasTable('sales_orders') && \Schema::hasTable('customers')) {
            $bestCustomers = \App\Models\SalesOrder::select(
                    'customer_id',
                    \DB::raw('COUNT(*) as total_orders'),
                    \DB::raw('SUM(total_amount) as total_revenue')
                )
                ->where($salesFilter)
                ->groupBy('customer_id')
                ->orderByDesc('total_revenue')
                ->take(5)
                ->with('customer')
                ->get()
                ->map(function($o) {
                    return [
                        'name' => $o->customer->customer_name ?? ($o->customer->company_name ?? 'Walk-in Customer'),
                        'orders' => $o->total_orders,
                        'revenue' => (float)$o->total_revenue,
                    ];
                })->toArray();
        }

        $mostOverdueCustomers = [];
        if (\Schema::hasTable('sales_invoices')) {
            $mostOverdueCustomers = \DB::table('sales_invoices')
                ->where('status', '!=', 'Paid')
                ->where('created_at', '<', now()->subDays(30))
                ->take(5)
                ->get()
                ->map(function($inv) {
                    return [
                        'name' => $inv->customer_name ?? 'Client Account',
                        'amount' => (float) $inv->total_amount,
                        'days_overdue' => 30,
                    ];
                })->toArray();
        }

        // 3. Dynamic Executive Risk Alerts from Real Database State
        $executiveAlerts = [];

        if (count($mostOverdueCustomers) > 0) {
            $executiveAlerts[] = [
                'type' => 'danger',
                'title' => 'Overdue AR Accounts',
                'desc' => count($mostOverdueCustomers) . ' customer account(s) have invoices past due.',
            ];
        }

        if ($payablesDue > 0) {
            $executiveAlerts[] = [
                'type' => 'warning',
                'title' => 'Unpaid Supplier Payables',
                'desc' => 'Total of ₱' . number_format($payablesDue, 2) . ' in supplier invoices pending payment.',
            ];
        }

        if ($budgetUtilization > 90) {
            $executiveAlerts[] = [
                'type' => 'danger',
                'title' => 'High Budget Burn Rate',
                'desc' => 'Corporate budget utilization is at ' . $budgetUtilization . '%.',
            ];
        }

        if (count($executiveAlerts) === 0) {
            $executiveAlerts[] = [
                'type' => 'success',
                'title' => 'System Operations Normal',
                'desc' => 'All production, treasury, and sales indicators are running healthy.',
            ];
        }

        // Fetch detailed list records for card drill-down modals
        $todaysCashInflows = \App\Models\CashTransaction::where('category', 'Inflow')->whereDate('transaction_date', $today)->get()->map(function($ct) {
            return (object) [
                'so_number' => 'CASH-IN-' . $ct->id,
                'customer_name' => $ct->description ?: 'Cash Inflow Transaction',
                'type' => $ct->transaction_type ?: 'Cash Inflow',
                'payment_method' => 'Cash',
                'status' => 'completed',
                'total_amount' => (float) $ct->amount,
                'created_at' => \Carbon\Carbon::parse($ct->transaction_date),
            ];
        });

        $todaysOrders = \Schema::hasTable('sales_orders') 
            ? \App\Models\SalesOrder::whereDate('created_at', $today)->where($salesFilter)->with('customer')->get()->map(function($so) {
                return (object) [
                    'so_number' => $so->so_number,
                    'customer_name' => $so->customer->customer_name ?? ($so->customer->company_name ?? 'Walk-in Customer'),
                    'type' => $so->type,
                    'payment_method' => $so->payment_method ?: 'Cash',
                    'status' => $so->status,
                    'total_amount' => (float) $so->total_amount,
                    'created_at' => $so->created_at,
                ];
            }) 
            : collect();

        $todaysSalesOrdersList = $todaysCashInflows->concat($todaysOrders)->sortByDesc('created_at')->values();

        $monthsCashInflows = \App\Models\CashTransaction::where('category', 'Inflow')->where('transaction_date', 'like', "{$thisMonth}%")->get()->map(function($ct) {
            return (object) [
                'so_number' => 'CASH-IN-' . $ct->id,
                'customer_name' => $ct->description ?: 'Cash Inflow Transaction',
                'type' => $ct->transaction_type ?: 'Cash Inflow',
                'payment_method' => 'Cash',
                'status' => 'completed',
                'total_amount' => (float) $ct->amount,
                'created_at' => \Carbon\Carbon::parse($ct->transaction_date),
            ];
        });

        $monthsOrders = \Schema::hasTable('sales_orders') 
            ? \App\Models\SalesOrder::where('created_at', 'like', "{$thisMonth}%")->where($salesFilter)->with('customer')->get()->map(function($so) {
                return (object) [
                    'so_number' => $so->so_number,
                    'customer_name' => $so->customer->customer_name ?? ($so->customer->company_name ?? 'Client Account'),
                    'type' => $so->type,
                    'payment_method' => $so->payment_method ?: 'Cash',
                    'status' => $so->status,
                    'total_amount' => (float) $so->total_amount,
                    'created_at' => $so->created_at,
                ];
            }) 
            : collect();

        $thisMonthsRevenueOrdersList = $monthsCashInflows->concat($monthsOrders)->sortByDesc('created_at')->values();

        $cashInflowsList = \App\Models\CashTransaction::where('category', 'Inflow')->get()->map(function($ct) {
            return (object) [
                'account_code' => 'CASH-REC-' . $ct->id,
                'bank_name' => $ct->description ?: 'Cash Collection / Receipt',
                'account_name' => $ct->transaction_type ?: 'Cash Inflow',
                'account_number' => 'Cash On Hand',
                'current_balance' => (float) $ct->amount,
            ];
        });

        $cashOrdersList = \Schema::hasTable('sales_orders') 
            ? \App\Models\SalesOrder::where('status', '!=', 'cancelled')
                ->where(function($q) {
                    $q->where('payment_status', 'paid')
                      ->orWhere('type', 'calculator_pos')
                      ->orWhere('payment_method', 'cash');
                })
                ->with('customer')
                ->get()
                ->map(function($so) {
                    return (object) [
                        'account_code' => $so->so_number,
                        'bank_name' => $so->customer->customer_name ?? ($so->customer->company_name ?? 'POS / Cash Customer'),
                        'account_name' => 'Cash Sales Order (' . strtoupper($so->type) . ')',
                        'account_number' => 'Direct Cash Payment',
                        'current_balance' => (float) $so->total_amount,
                    ];
                })
            : collect();

        $cashAccountsList = $cashInflowsList->concat($cashOrdersList)->values();

        $outstandingARList = \Schema::hasTable('sales_invoices') 
            ? \DB::table('sales_invoices')->where('status', '!=', 'Paid')->latest()->get() 
            : collect();

        $payablesDueList = \Schema::hasTable('supplier_invoices') 
            ? \DB::table('supplier_invoices')->where('status', '!=', 'paid')->latest()->get() 
            : collect();

        $productionCostList = \Schema::hasTable('production_costings') 
            ? \App\Models\ProductionCosting::latest()->get() 
            : collect();

        $inventoryValueList = \Schema::hasTable('site_inventories') 
            ? \DB::table('site_inventories')
                ->join('books', 'site_inventories.book_id', '=', 'books.id')
                ->select('site_inventories.*', 'books.name as book_name', 'books.price', \DB::raw('(site_inventories.quantity * books.price) as total_val'))
                ->get() 
            : collect();

        $payrollList = \App\Models\CashTransaction::where('transaction_type', 'Payroll')->latest()->get();

        $donationIncomeList = \Schema::hasTable('donations') 
            ? \App\Models\Donation::latest()->get() 
            : collect();

        return view('production.executive-dashboard.index', [
            'title' => 'Production Executive Dashboard',
            'role' => 'Production Manager',
            'sidebar' => 'production',
            'kpis' => [
                'todays_sales' => $todaysSales,
                'this_months_revenue' => $thisMonthsRevenue,
                'net_income' => $netIncome,
                'cash_position' => $cashPosition,
                'outstanding_receivables' => $outstandingReceivables,
                'payables_due' => $payablesDue,
                'production_cost' => $productionCost,
                'inventory_value' => $inventoryValue,
                'payroll_this_month' => $payrollThisMonth,
                'tax_due' => $taxDue,
                'donation_income' => $donationIncome,
                'investment_valuation' => $investmentValuation,
                'budget_utilization' => $budgetUtilization,
                'forecasted_cash' => $forecastedCash,
                'total_expenses' => $totalExpenses,
            ],
            'topSellingBooks' => $topSellingBooks,
            'worstSellingBooks' => $worstSellingBooks,
            'bestCustomers' => $bestCustomers,
            'mostOverdueCustomers' => $mostOverdueCustomers,
            'executiveAlerts' => $executiveAlerts,
            'todaysSalesOrdersList' => $todaysSalesOrdersList,
            'thisMonthsRevenueOrdersList' => $thisMonthsRevenueOrdersList,
            'cashAccountsList' => $cashAccountsList,
            'outstandingARList' => $outstandingARList,
            'payablesDueList' => $payablesDueList,
            'productionCostList' => $productionCostList,
            'inventoryValueList' => $inventoryValueList,
            'payrollList' => $payrollList,
            'donationIncomeList' => $donationIncomeList,
        ]);
    }
}


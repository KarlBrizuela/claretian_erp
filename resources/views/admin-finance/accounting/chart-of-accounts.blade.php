<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .hover-row {
            transition: background-color 0.2s ease;
        }

        .hover-row:hover {
            background-color: #fafafa !important;
        }

        .transition-icon {
            transition: transform 0.2s ease;
        }

        .cursor-pointer[aria-expanded="true"] .transition-icon {
            transform: rotate(180deg);
        }

        .bg-soft-success {
            background-color: rgba(40, 167, 69, 0.1);
        }

        .text-success {
            color: #28a745 !important;
        }

        /* Premium Dashboard KPI Cards Styling */
        .hover-card {
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .hover-card:hover {
            transform: translateY(-4px);
            background-color: #ffffff !important;
        }
        
        .assets-card:hover {
            border-color: #3b82f6 !important;
            box-shadow: 0 12px 24px -5px rgba(59, 130, 246, 0.12), 0 4px 12px -2px rgba(59, 130, 246, 0.08) !important;
        }
        .liabilities-card:hover {
            border-color: #f59e0b !important;
            box-shadow: 0 12px 24px -5px rgba(245, 158, 11, 0.12), 0 4px 12px -2px rgba(245, 158, 11, 0.08) !important;
        }
        .equity-card:hover {
            border-color: #8b5cf6 !important;
            box-shadow: 0 12px 24px -5px rgba(139, 92, 246, 0.12), 0 4px 12px -2px rgba(139, 92, 246, 0.08) !important;
        }
        .income-card:hover {
            border-color: #10b981 !important;
            box-shadow: 0 12px 24px -5px rgba(16, 185, 129, 0.12), 0 4px 12px -2px rgba(16, 185, 129, 0.08) !important;
        }
        .expenses-card:hover {
            border-color: #ef4444 !important;
            box-shadow: 0 12px 24px -5px rgba(239, 68, 68, 0.12), 0 4px 12px -2px rgba(239, 68, 68, 0.08) !important;
        }

        /* Maximize page width by overriding layout container padding */
        .content-body .container-fluid {
            padding-left: 15px !important;
            padding-right: 15px !important;
            max-width: 100% !important;
        }

        /* Modal Table Styling (using General Journal as reference) */
        .modal-content {
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        }

        .modal-header {
            background-color: #ffffff !important;
            border-bottom: 1px solid #f1f5f9 !important;
            padding: 16px 24px !important;
        }

        .modal-header .modal-title {
            font-size: 0.95rem !important;
            font-weight: 700 !important;
            color: #000000 !important;
        }

        .modal-body {
            padding: 20px 24px !important;
        }

        .modal-body table {
            margin-bottom: 0 !important;
            border: none !important;
        }

        .modal-body table thead th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            font-size: 0.72rem !important;
            letter-spacing: 0.8px !important;
            padding: 12px 16px !important;
            border-bottom: 2px solid #e2e8f0 !important;
            border-top: none !important;
        }

        .modal-body table tbody td {
            padding: 12px 16px !important;
            font-size: 0.82rem !important;
            color: #475569 !important;
            border-bottom: 1px solid #f1f5f9 !important;
            vertical-align: middle !important;
        }

        .modal-body table tbody tr {
            transition: all 0.15s ease-in-out !important;
        }

        .modal-body table tbody tr:hover {
            background-color: #f8fafc !important;
        }

        /* Modal Pagination Style Matching */
        .modal nav {
            padding: 12px 16px 0 16px !important;
            border-top: 1px solid #f1f5f9 !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
        }

        .modal .pagination {
            display: flex !important;
            gap: 5px !important;
            margin: 0 !important;
            padding: 0 !important;
            list-style: none !important;
            align-items: center !important;
        }

        .modal .pagination .page-item {
            margin: 0 !important;
            padding: 0 !important;
        }

        .modal .pagination .page-link {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 28px !important;
            height: 28px !important;
            padding: 0 8px !important;
            border-radius: 6px !important;
            font-size: 0.76rem !important;
            font-weight: 600 !important;
            color: #475569 !important;
            border: 1px solid #cbd5e1 !important;
            background-color: #ffffff !important;
            transition: all 0.15s ease-in-out !important;
            text-decoration: none !important;
            box-shadow: none !important;
            outline: none !important;
        }

        .modal .pagination .page-link:hover {
            background-color: #f8fafc !important;
            border-color: #94a3b8 !important;
            color: #0f172a !important;
        }

        .modal .pagination .page-item.active .page-link {
            background-color: #D9251C !important;
            border-color: #D9251C !important;
            color: #ffffff !important;
            box-shadow: none !important;
        }

        .modal .pagination .page-item.disabled .page-link {
            color: #94a3b8 !important;
            border-color: #e2e8f0 !important;
            background-color: #f8fafc !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
        }
    </style>
    @endpush

    <div class="container-fluid p-0">
        <!-- Top Title & Sleek Segmented Tab Switcher -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm p-2" style="border-radius: 8px; background-color: #ffffff; border: 1px solid #e2e8f0;">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex flex-wrap gap-1">
                            <a href="?tab=assets" class="btn btn-sm fw-bold px-3 py-2 d-flex align-items-center gap-2" style="border-radius: 6px; transition: all 0.2s; {{ $tab === 'assets' ? 'background-color: #D9251C; color: #ffffff;' : 'background-color: transparent; color: #475569; border: 1px solid transparent;' }}">
                                <i class="las la-wallet fs-16"></i> Assets
                            </a>
                            <a href="?tab=liabilities" class="btn btn-sm fw-bold px-3 py-2 d-flex align-items-center gap-2" style="border-radius: 6px; transition: all 0.2s; {{ $tab === 'liabilities' ? 'background-color: #D9251C; color: #ffffff;' : 'background-color: transparent; color: #475569; border: 1px solid transparent;' }}">
                                <i class="las la-credit-card fs-16"></i> Liabilities
                            </a>
                            <a href="?tab=equity" class="btn btn-sm fw-bold px-3 py-2 d-flex align-items-center gap-2" style="border-radius: 6px; transition: all 0.2s; {{ $tab === 'equity' ? 'background-color: #D9251C; color: #ffffff;' : 'background-color: transparent; color: #475569; border: 1px solid transparent;' }}">
                                <i class="las la-coins fs-16"></i> Equity
                            </a>
                            <a href="?tab=income" class="btn btn-sm fw-bold px-3 py-2 d-flex align-items-center gap-2" style="border-radius: 6px; transition: all 0.2s; {{ $tab === 'income' ? 'background-color: #D9251C; color: #ffffff;' : 'background-color: transparent; color: #475569; border: 1px solid transparent;' }}">
                                <i class="las la-chart-line fs-16"></i> Income
                            </a>
                            <a href="?tab=expenses" class="btn btn-sm fw-bold px-3 py-2 d-flex align-items-center gap-2" style="border-radius: 6px; transition: all 0.2s; {{ $tab === 'expenses' ? 'background-color: #D9251C; color: #ffffff;' : 'background-color: transparent; color: #475569; border: 1px solid transparent;' }}">
                                <i class="las la-file-invoice-dollar fs-16"></i> Expenses
                            </a>
                        </div>
                        <div>
                            <button class="btn btn-sm px-3 d-flex align-items-center gap-2 fw-bold" style="background-color: #ffffff; border: 1px solid #cbd5e1; color: #475569; height: 36px; border-radius: 6px;" onclick="window.print()">
                                <i class="las la-print fs-16"></i> Print Chart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Render the selected component only -->
        <div class="row">
            <div class="col-12">
                @if($tab === 'assets')
                    @include('admin-finance.accounting.chart-of-accounts.assets')
                @elseif($tab === 'liabilities')
                    @include('admin-finance.accounting.chart-of-accounts.liabilities')
                @elseif($tab === 'equity')
                    @include('admin-finance.accounting.chart-of-accounts.equity')
                @elseif($tab === 'income')
                    @include('admin-finance.accounting.chart-of-accounts.income')
                @elseif($tab === 'expenses')
                    @include('admin-finance.accounting.chart-of-accounts.expenses')
                @endif
            </div>
        </div>

        @if(isset($uncategorizedAccounts) && count($uncategorizedAccounts) > 0)
        @php
            $themeColor = '#3b82f6';
            $bgSoft = 'rgba(59, 130, 246, 0.08)';
            if ($tab === 'liabilities') {
                $themeColor = '#f59e0b';
                $bgSoft = 'rgba(245, 158, 11, 0.08)';
            } elseif ($tab === 'equity') {
                $themeColor = '#8b5cf6';
                $bgSoft = 'rgba(139, 92, 246, 0.08)';
            } elseif ($tab === 'income') {
                $themeColor = '#10b981';
                $bgSoft = 'rgba(16, 185, 129, 0.08)';
            } elseif ($tab === 'expenses') {
                $themeColor = '#ef4444';
                $bgSoft = 'rgba(239, 68, 68, 0.08)';
            }
        @endphp
        <!-- Uncategorized Database Accounts -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 8px; border: 1px solid #e2e8f0; background: #ffffff;">
                    <div class="card-header bg-white border-0 pt-3 pb-2 px-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold fs-16" style="color: #000000;">Additional {{ ucfirst($tab) }} Accounts</h5>
                            <p class="text-muted small mb-0">Other accounts registered in the database system.</p>
                        </div>
                    </div>
                    <div class="card-body p-3 pt-1">
                        <div class="row g-2">
                            @foreach($uncategorizedAccounts as $acc)
                            <div class="col-xl-3 col-md-4 col-sm-6">
                                <div class="card h-100 shadow-sm hover-card {{ $tab }}-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease;">
                                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: {{ $bgSoft }}; color: {{ $themeColor }};">
                                                    <i class="las la-file-invoice-dollar fs-20"></i>
                                                </div>
                                                <span class="badge status-badge {{ $acc->is_active ? 'bg-soft-success text-success' : 'bg-light text-secondary' }} px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; cursor: pointer; {{ $acc->is_active ? 'background-color: rgba(16, 185, 129, 0.1); color: #10b981;' : '' }}" data-type="coa" data-id="{{ $acc->id }}">
                                                    {{ $acc->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">{{ $acc->name }}</h6>
                                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Code: {{ $acc->code }}</p>
                                        </div>
                                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($acc->balance, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Modals for Card Details -->
    
    <!-- Cash on Hand Modal -->
    <div class="modal fade" id="cashOnHandModal" tabindex="-1" aria-labelledby="cashOnHandModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="cashOnHandModalLabel"><i class="las la-coins text-primary me-2 fs-20"></i>Cash on Hand - Sales Invoices Ledger</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Invoice No.</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salesInvoices as $si)
                                <tr>
                                    <td><span class="fw-bold text-dark">#{{ $si->si_number }}</span></td>
                                    <td>{{ $si->customer_name ?? 'N/A' }}</td>
                                    <td>{{ $si->created_at->format('M d, Y') }}</td>
                                    <td><span class="badge bg-success text-white">{{ ucfirst($si->status) }}</span></td>
                                    <td class="text-end fw-bold text-dark">₱{{ number_format($si->total_amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No sales invoice transactions recorded in the database.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Petty Cash Modal -->
    <div class="modal fade" id="pettyCashModal" tabindex="-1" aria-labelledby="pettyCashModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="pettyCashModalLabel"><i class="las la-cash-register text-primary me-2 fs-20"></i>Petty Cash Vouchers Ledger</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>PCV No.</th>
                                    <th>Pay To</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pettyCashVouchers as $pcv)
                                <tr>
                                    <td><span class="fw-bold text-dark">#{{ $pcv->pcv_number }}</span></td>
                                    <td>{{ $pcv->pay_to }}</td>
                                    <td>{{ date('M d, Y', strtotime($pcv->date)) }}</td>
                                    <td>
                                        <span class="badge {{ $pcv->status === 'completed' ? 'bg-success text-white' : 'bg-warning text-dark' }}">
                                            {{ ucfirst($pcv->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-dark">₱{{ number_format($pcv->items_sum_amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No petty cash vouchers recorded in the database.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Supplies Expense Modal -->
    <div class="modal fade" id="suppliesExpenseModal" tabindex="-1" aria-labelledby="suppliesExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="suppliesExpenseModalLabel"><i class="las la-archive text-danger me-2 fs-20"></i>Office Supplies Expenses Breakdown</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Item Name</th>
                                    <th>Item Price</th>
                                    <th>Stock Qty</th>
                                    <th class="text-end">Total Valuation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($officeSuppliesList as $supply)
                                <tr>
                                    <td><span class="fw-bold text-dark">{{ $supply->item_name }}</span></td>
                                    <td>₱{{ number_format($supply->item_price, 2) }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $supply->items_stock }} {{ $supply->unit ?? 'pcs' }}</span></td>
                                    <td class="text-end fw-bold text-danger">₱{{ number_format($supply->item_price * $supply->items_stock, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No office supply items recorded.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fixed Assets Expense Modal -->
    <div class="modal fade" id="fixedAssetsExpenseModal" tabindex="-1" aria-labelledby="fixedAssetsExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="fixedAssetsExpenseModalLabel"><i class="las la-tools text-danger me-2 fs-20"></i>Fixed Assets Expense Breakdown</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Asset Code</th>
                                    <th>Machine / Asset Name</th>
                                    <th>Category</th>
                                    <th>Purchase Date</th>
                                    <th class="text-end">Purchase Price</th>
                                    <th class="text-end">Current Book Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($fixedAssetsRecordsList ?? [] as $ast)
                                <tr>
                                    <td><span class="fw-bold font-monospace text-dark">{{ $ast->asset_code }}</span></td>
                                    <td>
                                        <span class="fw-bold text-dark d-block">{{ $ast->name }}</span>
                                        <span class="text-muted small">{{ $ast->location ?: 'Main Production Facility' }}</span>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $ast->category }}</span></td>
                                    <td class="small">{{ $ast->purchase_date ? $ast->purchase_date->format('M d, Y') : 'N/A' }}</td>
                                    <td class="text-end fw-bold text-dark">₱{{ number_format($ast->purchase_price, 2) }}</td>
                                    <td class="text-end fw-bold text-danger">₱{{ number_format($ast->current_value, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No fixed asset records logged.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Operational Expenses Modal -->
    <div class="modal fade" id="operationalExpensesModal" tabindex="-1" aria-labelledby="operationalExpensesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="operationalExpensesModalLabel"><i class="las la-file-invoice-dollar text-danger me-2 fs-20"></i>Operational Expenses Breakdown</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Title / Item</th>
                                    <th>Department</th>
                                    <th>Expense Date</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expensesRecordsList as $exp)
                                <tr>
                                    <td><span class="fw-bold text-dark">{{ $exp->expense_title ?? ($exp->title ?? 'Operational Expense') }}</span></td>
                                    <td><span class="badge bg-light text-dark border">{{ $exp->department->name ?? 'General' }}</span></td>
                                    <td>{{ date('M d, Y', strtotime($exp->expense_date ?? $exp->created_at)) }}</td>
                                    <td class="text-end fw-bold text-danger">₱{{ number_format($exp->amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No operational expenses recorded.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bank Accounts Modal -->
    <div class="modal fade" id="bankAccountsModal" tabindex="-1" aria-labelledby="bankAccountsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="bankAccountsModalLabel"><i class="las la-university text-primary me-2 fs-20"></i>Bank Accounts Ledger</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Account Code</th>
                                    <th>Bank & Account Name</th>
                                    <th>Account Number</th>
                                    <th>Type</th>
                                    <th class="text-end">Current Balance</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($companyBankAccounts as $acct)
                                <tr>
                                    <td><span class="fw-bold text-dark">{{ $acct->account_code }}</span></td>
                                    <td>
                                        <span class="fw-bold text-dark">{{ $acct->bank_name }}</span>
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $acct->account_name }}</small>
                                    </td>
                                    <td><span class="text-muted small fw-medium">{{ $acct->account_number }}</span></td>
                                    <td><span class="badge bg-light text-dark border">{{ $acct->account_type }}</span></td>
                                    <td class="text-end fw-bold text-dark">₱{{ number_format($acct->current_balance, 2) }}</td>
                                    <td><span class="badge bg-success text-white">Active</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No bank accounts registered in the database.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Receivables Modal -->
    <div class="modal fade" id="receivablesModal" tabindex="-1" aria-labelledby="receivablesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="receivablesModalLabel"><i class="las la-file-invoice-dollar text-primary me-2 fs-20"></i>Accounts Receivable - Statements of Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Reference No.</th>
                                    <th>Customer</th>
                                    <th>Type</th>
                                    <th>Created Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($statementOfAccounts as $soa)
                                <tr>
                                    <td><span class="fw-bold text-dark">#{{ $soa->soa_number }}</span></td>
                                    <td>{{ $soa->customer_name ?? 'N/A' }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $soa->type ?? 'Invoice' }}</span></td>
                                    <td>{{ $soa->created_at ? $soa->created_at->format('M d, Y') : 'N/A' }}</td>
                                    <td>
                                        @php
                                            $st = strtolower($soa->status ?? '');
                                            $isPaid = ($st === 'paid');
                                        @endphp
                                        <span class="badge {{ $isPaid ? 'bg-success text-white' : 'bg-danger text-white' }}">
                                            {{ $isPaid ? 'Paid' : 'Unpaid' }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-dark">₱{{ number_format($soa->total_amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No accounts receivable transactions recorded in the database.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Modal -->
    <div class="modal fade" id="inventoryModal" tabindex="-1" aria-labelledby="inventoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="inventoryModalLabel"><i class="las la-boxes text-primary me-2 fs-20"></i>Inventory Valuation (Finished Goods)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Book Title</th>
                                    <th class="text-center">Stock</th>
                                    <th class="text-end">Cost</th>
                                    <th class="text-end">Total Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($books as $book)
                                <tr>
                                    <td><span class="fw-bold text-dark">{{ $book->name }}</span></td>
                                    <td class="text-center">{{ $book->stock }}</td>
                                    <td class="text-end">₱{{ number_format($book->cost, 2) }}</td>
                                    <td class="text-end fw-bold text-dark">₱{{ number_format($book->stock * $book->cost, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No books in inventory found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Suppliers Modal -->
    <div class="modal fade" id="suppliersModal" tabindex="-1" aria-labelledby="suppliersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="suppliersModalLabel"><i class="las la-truck text-primary me-2 fs-20"></i>Suppliers Payable - Purchase Orders</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>PO Number</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseOrders as $po)
                                <tr>
                                    <td><span class="fw-bold text-dark">#{{ $po->po_number }}</span></td>
                                    <td>{{ $po->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-warning text-dark">{{ ucfirst($po->status) }}</span>
                                    </td>
                                    <td class="text-end fw-bold text-dark">₱{{ number_format($po->total_amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No purchase orders recorded in the database.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Generic Empty Account Modal -->
    <div class="modal fade" id="genericCoaModal" tabindex="-1" aria-labelledby="genericCoaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="genericModalTitle">Account Ledger</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="genericModalBody">
                    <!-- Dynamic -->
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function showGenericModal(title, description, balance = 0) {
            document.getElementById('genericModalTitle').innerText = title + ' Ledger';
            const numBalance = parseFloat(balance || 0);
            const formattedBalance = numBalance.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            const badgeClass = numBalance > 0 ? 'bg-soft-success text-success' : 'bg-light text-muted';
            const infoText = numBalance > 0 ? `Current Ledger Balance: ₱${formattedBalance}` : `Balance: ₱0.00 (No logged transactions)`;
            
            document.getElementById('genericModalBody').innerHTML = `
                <div class="text-center py-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-light mx-auto mb-3" style="width: 60px; height: 60px; color: #ff0000;">
                        <i class="las la-folder-open fs-32"></i>
                    </div>
                    <h6 class="fw-bold text-dark">${title}</h6>
                    <p class="text-muted small px-3 mb-4">${description}</p>
                    <div class="alert ${badgeClass} border-0 small d-inline-block px-3 py-2 rounded-pill fw-bold">
                        <i class="las la-info-circle me-1"></i> ${infoText}
                    </div>
                </div>
            `;
            const modal = new bootstrap.Modal(document.getElementById('genericCoaModal'));
            modal.show();
        }

        // --- CLIENT-SIDE TABLE PAGINATION FOR CARD LEDGER MODALS ---
        function initTablePagination(tableElement, itemsPerPage = 10) {
            const tbody = tableElement.querySelector('tbody');
            if (!tbody) return;
            
            const rows = Array.from(tbody.querySelectorAll('tr'));
            // Check if there are no items or only an empty row
            if (rows.length === 1 && rows[0].querySelector('td[colspan]')) return;
            if (rows.length <= itemsPerPage) return;
            
            const totalItems = rows.length;
            const totalPages = Math.ceil(totalItems / itemsPerPage);
            let currentPage = 1;
            
            // Create pagination container
            const nav = document.createElement('nav');
            nav.className = 'd-flex justify-content-between align-items-center mt-3';
            
            const info = document.createElement('div');
            info.className = 'small text-muted';
            
            const ul = document.createElement('ul');
            ul.className = 'pagination pagination-xs mb-0';
            
            nav.appendChild(info);
            nav.appendChild(ul);
            
            // Insert after table wrapper
            const wrapper = tableElement.closest('.table-responsive') || tableElement;
            wrapper.parentNode.appendChild(nav);
            
            function showPage(page) {
                currentPage = page;
                const start = (page - 1) * itemsPerPage;
                const end = start + itemsPerPage;
                
                rows.forEach((row, idx) => {
                    if (idx >= start && idx < end) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                info.textContent = `Showing ${start + 1} to ${Math.min(end, totalItems)} of ${totalItems} entries`;
                
                ul.innerHTML = '';
                
                // Prev
                const prevLi = document.createElement('li');
                prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
                prevLi.innerHTML = `<a class="page-link" href="#">&laquo;</a>`;
                prevLi.querySelector('a').onclick = (e) => {
                    e.preventDefault();
                    if (currentPage > 1) showPage(currentPage - 1);
                };
                ul.appendChild(prevLi);
                
                // Numbers
                for (let i = 1; i <= totalPages; i++) {
                    if (totalPages > 5) {
                        if (i !== 1 && i !== totalPages && Math.abs(i - currentPage) > 1) {
                            if (i === 2 || i === totalPages - 1) {
                                const dotsLi = document.createElement('li');
                                dotsLi.className = 'page-item disabled';
                                dotsLi.innerHTML = '<span class="page-link">...</span>';
                                ul.appendChild(dotsLi);
                            }
                            continue;
                        }
                    }
                    
                    const li = document.createElement('li');
                    li.className = `page-item ${currentPage === i ? 'active' : ''}`;
                    li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                    li.querySelector('a').onclick = (e) => {
                        e.preventDefault();
                        showPage(i);
                    };
                    ul.appendChild(li);
                }
                
                // Next
                const nextLi = document.createElement('li');
                nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
                nextLi.innerHTML = `<a class="page-link" href="#">&raquo;</a>`;
                nextLi.querySelector('a').onclick = (e) => {
                    e.preventDefault();
                    if (currentPage < totalPages) showPage(currentPage + 1);
                };
                ul.appendChild(nextLi);
            }
            
            showPage(1);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const modalTables = document.querySelectorAll('.modal .modal-body table');
            modalTables.forEach(table => {
                if (table.closest('#genericCoaModal')) return;
                initTablePagination(table, 10);
            });

            // AJAX status toggle handler
            $(document).on('click', '.status-badge', function(e) {
                e.stopPropagation(); // Prevent opening the card modal or triggering row clicks
                const $badge = $(this);
                const type = $badge.data('type');
                const id = $badge.data('id');

                if (!id) return;

                $badge.css('opacity', '0.5');

                $.ajax({
                    url: "{{ route('admin-finance.accounting.chart-of-accounts.toggle') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        type: type,
                        id: id
                    },
                    success: function(response) {
                        $badge.css('opacity', '1');
                        if (response.success) {
                            const isActive = response.is_active;
                            if (isActive) {
                                $badge.removeClass('bg-light text-secondary')
                                      .addClass('bg-soft-success text-success')
                                      .css({'background-color': 'rgba(16, 185, 129, 0.1)', 'color': '#10b981'})
                                      .text('Active');
                            } else {
                                $badge.removeClass('bg-soft-success text-success')
                                      .addClass('bg-light text-secondary')
                                      .css({'background-color': '', 'color': ''})
                                      .text('Inactive');
                            }
                            if (typeof showNotification === 'function') {
                                showNotification(response.message, 'success');
                            }
                        }
                    },
                    error: function(xhr) {
                        $badge.css('opacity', '1');
                        const err = xhr.responseJSON ? xhr.responseJSON.error : 'Failed to update status.';
                        if (typeof showNotification === 'function') {
                            showNotification(err, 'error');
                        }
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>

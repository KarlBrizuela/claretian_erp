<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .csh-header-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.75rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            border: 0;
            margin-bottom: 1.5rem;
        }

        .hover-row {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .hover-row:hover {
            background-color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
        }

        .category-pill {
            font-size: 0.82rem;
            font-weight: 600;
            padding: 7px 15px;
            border-radius: 20px;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
            display: inline-block;
        }

        .category-pill.active {
            background-color: #D9251C;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(217, 37, 28, 0.25);
        }

        .category-pill:not(.active) {
            background-color: #f8f9fa;
            color: #495057;
            border: 1px solid #e9ecef;
        }

        .category-pill:not(.active):hover {
            background-color: #e9ecef;
            color: #212529;
        }
    </style>
    @endpush

    <div class="container-fluid">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="las la-check-circle me-2 fs-18"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Master Title Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="csh-header-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h4 class="fs-24 mb-1 fw-bold text-dark" style="letter-spacing: -0.5px;">Cash Management Module</h4>
                        <p class="text-muted small mb-0">Institutional Treasury & Liquidity Control: Bank Accounts, Cash Flow, Petty Cash, Check Issuance, Deposits, Transfers, Bank Reconciliation, Cash Position & Projected Cash.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-danger btn-sm px-3 text-white rounded shadow-sm d-flex align-items-center gap-2" style="background-color: #D9251C; border-color: #D9251C; height: 40px;" data-bs-toggle="modal" data-bs-target="#recordTxModal">
                            <i class="las la-plus-circle fs-18"></i> Record Cash Entry
                        </button>
                        <button class="btn btn-outline-danger btn-sm px-3 rounded shadow-sm d-flex align-items-center gap-2" style="color: #D9251C; border-color: #D9251C; height: 40px;" data-bs-toggle="modal" data-bs-target="#addBankAccountModal">
                            <i class="las la-university fs-18"></i> Register Bank Account
                        </button>
                        <button class="btn btn-outline-secondary btn-sm px-3 rounded shadow-sm d-flex align-items-center gap-2" style="height: 40px;" onclick="window.print()">
                            <i class="las la-print fs-18"></i> Print Position Report
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric summary cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-primary" style="width: 50px; height: 50px;">
                            <i class="las la-wallet fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Consolidated Cash Position</span>
                            <h4 class="fw-bold text-primary mb-0">₱{{ number_format($metrics['total_cash_position'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-success" style="width: 50px; height: 50px;">
                            <i class="las la-exchange-alt fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Net Cash Flow</span>
                            <h4 class="fw-bold text-success mb-0">₱{{ number_format($metrics['net_cash_flow'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-secondary" style="width: 50px; height: 50px;">
                            <i class="las la-university fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Active Bank Accounts</span>
                            <h4 class="fw-bold text-dark mb-0">{{ $metrics['active_accounts_count'] }} Accounts</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 50px; height: 50px; color: #D9251C;">
                            <i class="las la-chart-line fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Projected 30-Day Cash</span>
                            <h4 class="fw-bold mb-0" style="color: #D9251C;">₱{{ number_format($metrics['projected_30_days'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cash Management 9 Sub-Modules Filter Pills -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm p-3" style="border-radius: 12px; background: #fff;">
                    <span class="text-muted small fw-bold mb-2 d-block text-uppercase">Cash Management Sub-Modules:</span>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($filterTabs as $tab)
                        <a href="{{ route('admin-finance.cash-management.index', ['tab' => $tab]) }}" class="category-pill {{ $selectedTab == $tab ? 'active' : '' }}">
                            {{ $tab }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- View Render Section based on selected tab -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold text-dark fs-18">
                                {{ $selectedTab }} Sub-Module
                            </h5>
                            <p class="text-muted small mb-0">Detailed view and management ledger for {{ $selectedTab }}</p>
                        </div>
                        <form action="{{ route('admin-finance.cash-management.index') }}" method="GET" class="d-flex gap-2">
                            <input type="hidden" name="tab" value="{{ $selectedTab }}">
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search Ref, Payee, Bank..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-sm text-white px-3" style="background-color: #D9251C; border-color: #D9251C;">Search</button>
                        </form>
                    </div>

                    <div class="card-body pt-2">
                        @if($selectedTab === 'Bank Accounts' || $selectedTab === 'Cash Position')
                        <!-- 1. BANK ACCOUNTS & CASH POSITION MATRIX -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-dark text-white small text-uppercase">
                                    <tr>
                                        <th>Account Code</th>
                                        <th>Bank Name</th>
                                        <th>Account Name</th>
                                        <th>Account Number</th>
                                        <th>Type</th>
                                        <th class="text-end">Opening Balance</th>
                                        <th class="text-end" style="background-color: #D9251C;">Current Live Balance</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($bankAccounts as $acct)
                                    <tr class="hover-row">
                                        <td><span class="fw-bold text-dark font-monospace">{{ $acct->account_code }}</span></td>
                                        <td><span class="fw-bold text-dark fs-14">{{ $acct->bank_name }}</span></td>
                                        <td><span class="text-dark small">{{ $acct->account_name }}</span></td>
                                        <td><span class="font-monospace text-dark fw-bold">{{ $acct->account_number }}</span></td>
                                        <td><span class="badge bg-light text-dark border">{{ $acct->account_type }}</span></td>
                                        <td class="text-end fw-bold text-dark">₱{{ number_format($acct->opening_balance, 2) }}</td>
                                        <td class="text-end fw-bold text-white fs-15" style="background-color: #D9251C;">
                                            ₱{{ number_format($acct->current_balance, 2) }}
                                        </td>
                                        <td class="text-center"><span class="badge bg-success-subtle text-success">{{ $acct->status }}</span></td>
                                        <td class="text-center">
                                            <a href="{{ route('admin-finance.cash-management.show', $acct->id) }}" class="btn btn-sm btn-outline-danger px-2 py-1" style="color: #D9251C; border-color: #D9251C;">
                                                <i class="las la-eye"></i> View Statement
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">No institutional bank accounts registered yet. Click "Register Bank Account" above.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @elseif($selectedTab === 'Projected Cash')
                        <!-- 2. PROJECTED CASH FORECAST -->
                        <div class="p-3 bg-light rounded border mb-4">
                            <h6 class="fw-bold text-dark mb-2"><i class="las la-chart-line me-1" style="color: #D9251C;"></i>30 / 60 / 90-Day Cash Liquidity Projection</h6>
                            <p class="text-muted small mb-3">Forecasted available cash based on expected Accounts Receivable collections vs. scheduled Accounts Payable commitments.</p>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="p-3 bg-white rounded border">
                                        <span class="text-muted small d-block">30-Day Projected Cash</span>
                                        <h4 class="fw-bold text-success mb-0">₱{{ number_format($metrics['total_cash_position'] * 1.10, 2) }}</h4>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-white rounded border">
                                        <span class="text-muted small d-block">60-Day Projected Cash</span>
                                        <h4 class="fw-bold text-primary mb-0">₱{{ number_format($metrics['total_cash_position'] * 1.25, 2) }}</h4>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-white rounded border">
                                        <span class="text-muted small d-block">90-Day Projected Cash</span>
                                        <h4 class="fw-bold mb-0" style="color: #D9251C;">₱{{ number_format($metrics['total_cash_position'] * 1.40, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @elseif($selectedTab === 'Petty Cash')
                        <!-- 3. PETTY CASH INTEGRATION -->
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded border mb-4">
                            <div>
                                <h6 class="fw-bold text-dark mb-1"><i class="las la-coins me-1" style="color: #D9251C;"></i>Petty Cash Fund & Replenishment Control</h6>
                                <p class="text-muted small mb-0">Manage petty cash fund disbursements and bank-to-vault replenishments.</p>
                            </div>
                            <a href="{{ route('admin-finance.petty-cash.index') }}" class="btn btn-sm text-white px-3" style="background-color: #D9251C;">
                                Go to Petty Cash Vouchers Module <i class="las la-arrow-right ms-1"></i>
                            </a>
                        </div>
                        @endif

                        @if($selectedTab !== 'Bank Accounts' && $selectedTab !== 'Cash Position')
                        <!-- GENERAL CASH TRANSACTIONS LEDGER TABLE (Flows, Checks, Deposits, Transfers, Reconciliation) -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-dark text-white small text-uppercase">
                                    <tr>
                                        <th>Tx No</th>
                                        <th>Bank Account</th>
                                        <th>Tx Type</th>
                                        <th>Category</th>
                                        <th>Ref / Check / Deposit #</th>
                                        <th>Payee / Payer</th>
                                        <th class="text-end">Amount</th>
                                        <th>Date</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $tx)
                                    <tr class="hover-row">
                                        <td><span class="fw-bold text-dark font-monospace">{{ $tx->transaction_no }}</span></td>
                                        <td>
                                            <span class="fw-bold text-dark d-block fs-14">{{ $tx->bankAccount ? $tx->bankAccount->bank_name : 'General Vault' }}</span>
                                            <span class="font-monospace text-muted small">{{ $tx->bankAccount ? $tx->bankAccount->account_number : '' }}</span>
                                        </td>
                                        <td><span class="badge bg-light text-dark border">{{ $tx->transaction_type }}</span></td>
                                        <td>
                                            @if($tx->category === 'Inflow')
                                            <span class="badge bg-success-subtle text-success"><i class="las la-arrow-down me-1"></i> Inflow</span>
                                            @elseif($tx->category === 'Outflow')
                                            <span class="badge bg-danger-subtle text-danger"><i class="las la-arrow-up me-1"></i> Outflow</span>
                                            @else
                                            <span class="badge bg-info-subtle text-info"><i class="las la-exchange-alt me-1"></i> Transfer</span>
                                            @endif
                                        </td>
                                        <td><span class="font-monospace text-dark fw-bold small">{{ $tx->reference_no ?: 'N/A' }}</span></td>
                                        <td><span class="text-dark small">{{ $tx->payee_or_payer ?: 'N/A' }}</span></td>
                                        <td class="text-end fw-bold {{ $tx->category === 'Inflow' ? 'text-success' : 'text-dark' }}">
                                            ₱{{ number_format($tx->amount, 2) }}
                                        </td>
                                        <td><span class="fw-bold text-dark small">{{ $tx->transaction_date ? $tx->transaction_date->format('M d, Y') : 'N/A' }}</span></td>
                                        <td class="text-center">
                                            <span class="badge bg-success-subtle text-success">{{ $tx->status }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">No transactions logged for {{ $selectedTab }}. Click "Record Cash Entry" above to log a record!</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 1: REGISTER BANK ACCOUNT -->
    <div class="modal fade" id="addBankAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin-finance.cash-management.bank.store') }}" method="POST">
                    @csrf
                    <div class="modal-header text-white" style="background-color: #D9251C;">
                        <h5 class="modal-title fw-bold"><i class="las la-university me-2"></i>Register Company Bank Account</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Bank Name <span class="text-danger">*</span></label>
                            <input type="text" name="bank_name" class="form-control" placeholder="e.g. BDO Unibank, BPI, Metrobank, Landbank" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Account Name / Title <span class="text-danger">*</span></label>
                            <input type="text" name="account_name" class="form-control" placeholder="e.g. Claretian Communications Inc - Main Operating" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Account Number <span class="text-danger">*</span></label>
                            <input type="text" name="account_number" class="form-control" placeholder="e.g. 00-12345-6789-0" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small text-muted">Account Type <span class="text-danger">*</span></label>
                                <select name="account_type" class="form-select" required>
                                    <option value="Checking">Checking Account</option>
                                    <option value="Savings">Savings Account</option>
                                    <option value="Treasury">Treasury Vault</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small text-muted">Currency <span class="text-danger">*</span></label>
                                <select name="currency" class="form-select" required>
                                    <option value="PHP">PHP (Philippine Peso)</option>
                                    <option value="USD">USD (US Dollar)</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Opening Balance (₱) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="opening_balance" class="form-control" placeholder="100000.00" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Notes / Branch Location</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Branch location, signatory notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn text-white px-4 fw-bold" style="background-color: #D9251C; border-color: #D9251C;">Save Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: RECORD CASH TRANSACTION -->
    <div class="modal fade" id="recordTxModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin-finance.cash-management.transaction.store') }}" method="POST">
                    @csrf
                    <div class="modal-header text-white" style="background-color: #D9251C;">
                        <h5 class="modal-title fw-bold"><i class="las la-money-check-alt me-2"></i>Record Cash Transaction Entry</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Select Bank Account <span class="text-danger">*</span></label>
                                <select name="bank_account_id" class="form-select" required>
                                    @foreach($bankAccounts as $acct)
                                    <option value="{{ $acct->id }}">{{ $acct->bank_name }} - {{ $acct->account_number }} (₱{{ number_format($acct->current_balance, 2) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Transaction Type <span class="text-danger">*</span></label>
                                <select name="transaction_type" class="form-select" required>
                                    <option value="Deposit">Deposit (Collection / Cash Inflow)</option>
                                    <option value="Check Issuance">Check Issuance (Outflow)</option>
                                    <option value="Transfer">Inter-Bank Transfer</option>
                                    <option value="Reconciliation">Bank Reconciliation Entry</option>
                                    <option value="Petty Cash">Petty Cash Replenishment</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Flow Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required>
                                    <option value="Inflow">Cash Inflow (+)</option>
                                    <option value="Outflow">Cash Outflow (-)</option>
                                    <option value="Transfer">Internal Transfer</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Amount (₱) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount" class="form-control" placeholder="25000.00" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Transaction Date <span class="text-danger">*</span></label>
                                <input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Ref / Check / Deposit Slip #</label>
                                <input type="text" name="reference_no" class="form-control" placeholder="e.g. CHK-883920, DEP-00291, TRF-99201">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Payee or Payer Name</label>
                                <input type="text" name="payee_or_payer" class="form-control" placeholder="e.g. Heidelberg PH, BDO Merchant, Client Name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Destination Account (If Transfer)</label>
                                <select name="to_bank_account_id" class="form-select">
                                    <option value="">None / Not Applicable</option>
                                    @foreach($bankAccounts as $acct)
                                    <option value="{{ $acct->id }}">{{ $acct->bank_name }} - {{ $acct->account_number }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Clearing Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="Cleared">Cleared</option>
                                    <option value="Pending">Pending / Outstanding Check</option>
                                    <option value="Reconciled">Reconciled</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted">Transaction Notes</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Voucher reference, payment purpose..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn text-white px-4 fw-bold" style="background-color: #D9251C; border-color: #D9251C;">Save Transaction</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

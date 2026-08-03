<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .coa-header-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.75rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            border: 0;
            margin-bottom: 1.5rem;
        }

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
    </style>
    @endpush

    <div class="container-fluid">
        <!-- Top Title & Overview Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="coa-header-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h4 class="fs-24 mb-1 fw-bold text-dark" style="letter-spacing: -0.5px;">Chart of Accounts - {{ ucfirst($tab) }}</h4>
                        <p class="text-muted small mb-0">CCFI Chart of Accounts containing {{ ucfirst($tab) }} accounts and their categories.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary btn-sm px-3 rounded shadow-sm d-flex align-items-center gap-2" style="height: 40px;" onclick="window.print()">
                            <i class="las la-print fs-18"></i> Print Chart
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Render the selected component only -->
        <div class="row">
            <div class="col-12">
                @if($tab === 'assets')
                    @include('admin-finance.accounting.chart-of-accounts.assets')
                {{-- @elseif($tab === 'liabilities')
                    @include('admin-finance.accounting.chart-of-accounts.liabilities')
                @elseif($tab === 'equity')
                    @include('admin-finance.accounting.chart-of-accounts.equity') --}}
                @elseif($tab === 'income')
                    @include('admin-finance.accounting.chart-of-accounts.income')
                @elseif($tab === 'expenses')
                    @include('admin-finance.accounting.chart-of-accounts.expenses')
                @endif
            </div>
        </div>

        @if(isset($uncategorizedAccounts) && count($uncategorizedAccounts) > 0)
        <!-- Uncategorized Database Accounts -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white border-0 pt-4 pb-2">
                        <h5 class="mb-0 fw-bold text-dark fs-18">Additional {{ ucfirst($tab) }} Accounts</h5>
                        <p class="text-muted small mb-0">Other accounts registered in the database system</p>
                    </div>
                    <div class="card-body pt-2">
                        <div class="row g-3">
                            @foreach($uncategorizedAccounts as $acc)
                            <div class="col-md-6 col-xl-4">
                                <div class="card h-100 border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important;">
                                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                                        <div class="d-flex align-items-center gap-3 mb-2">
                                            <span class="text-primary"><i class="las la-file-invoice-dollar fs-24"></i></span>
                                            <h6 class="mb-0 fw-bold text-dark fs-15">{{ $acc->name }}</h6>
                                        </div>
                                        <p class="text-muted small mb-3">Code: {{ $acc->code }}</p>
                                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-light">
                                            <h5 class="mb-0 fw-bold text-dark fs-16">₱{{ number_format($acc->balance, 2) }}</h5>
                                            <span class="badge {{ $acc->is_active ? 'bg-soft-success text-success' : 'bg-light text-secondary' }} px-2.5 py-1 rounded-pill small fw-bold">
                                                {{ $acc->is_active ? 'Active' : 'Inactive' }}
                                            </span>
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
        <div class="modal-dialog modal-lg modal-dialog-centered">
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
        <div class="modal-dialog modal-lg modal-dialog-centered">
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
        <div class="modal-dialog modal-lg modal-dialog-centered">
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
        <div class="modal-dialog modal-lg modal-dialog-centered">
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
        <div class="modal-dialog modal-lg modal-dialog-centered">
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
        <div class="modal-dialog modal-lg modal-dialog-centered">
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
        <div class="modal-dialog modal-lg modal-dialog-centered">
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
        <div class="modal-dialog modal-lg modal-dialog-centered">
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
        <div class="modal-dialog modal-lg modal-dialog-centered">
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
                prevLi.innerHTML = `<a class="page-link" href="#" style="border-radius: 4px; margin-right: 4px; padding: 4px 8px; font-size: 0.75rem;">&laquo;</a>`;
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
                                dotsLi.innerHTML = '<span class="page-link" style="border: none; padding: 4px 8px; font-size: 0.75rem;">...</span>';
                                ul.appendChild(dotsLi);
                            }
                            continue;
                        }
                    }
                    
                    const li = document.createElement('li');
                    li.className = `page-item ${currentPage === i ? 'active' : ''}`;
                    
                    let activeStyles = '';
                    if (currentPage === i) {
                        activeStyles = 'background-color: #D9251C; border-color: #D9251C; color: #fff;';
                    }
                    
                    li.innerHTML = `<a class="page-link" href="#" style="border-radius: 4px; margin-right: 4px; padding: 4px 8px; font-size: 0.75rem; ${activeStyles}">${i}</a>`;
                    li.querySelector('a').onclick = (e) => {
                        e.preventDefault();
                        showPage(i);
                    };
                    ul.appendChild(li);
                }
                
                // Next
                const nextLi = document.createElement('li');
                nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
                nextLi.innerHTML = `<a class="page-link" href="#" style="border-radius: 4px; padding: 4px 8px; font-size: 0.75rem;">&raquo;</a>`;
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
        });
    </script>
    @endpush
</x-app-layout>

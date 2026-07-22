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
                @elseif($tab === 'liabilities')
                    @include('admin-finance.accounting.chart-of-accounts.liabilities')
                @elseif($tab === 'equity')
                    @include('admin-finance.accounting.chart-of-accounts.equity')
                @elseif($tab === 'income')
                    @include('admin-finance.accounting.chart-of-accounts.income')
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
                                    <td><span class="badge bg-warning text-dark">{{ ucfirst($si->status) }}</span></td>
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
                                    <th>SOA Number</th>
                                    <th>Created Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($statementOfAccounts as $soa)
                                <tr>
                                    <td><span class="fw-bold text-dark">#{{ $soa->soa_number }}</span></td>
                                    <td>{{ $soa->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge {{ $soa->status === 'compiled' ? 'bg-info text-white' : 'bg-warning text-dark' }}">
                                            {{ ucfirst($soa->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-dark">₱{{ number_format($soa->total_amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No statement of accounts recorded in the database.</td>
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
        function showGenericModal(title, description) {
            document.getElementById('genericModalTitle').innerText = title + ' Ledger';
            document.getElementById('genericModalBody').innerHTML = `
                <div class="text-center py-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-light mx-auto mb-3" style="width: 60px; height: 60px; color: #ff0000;">
                        <i class="las la-folder-open fs-32"></i>
                    </div>
                    <h6 class="fw-bold text-dark">${title}</h6>
                    <p class="text-muted small px-3 mb-4">${description}</p>
                    <div class="alert bg-soft-success text-success border-0 small d-inline-block px-3 py-2 rounded-pill">
                        <i class="las la-info-circle me-1"></i> Balance: ₱0.00 (No logged transactions)
                    </div>
                </div>
            `;
            const modal = new bootstrap.Modal(document.getElementById('genericCoaModal'));
            modal.show();
        }
    </script>
    @endpush
</x-app-layout>

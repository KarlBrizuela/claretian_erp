<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .ap-header-card {
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
            font-size: 0.85rem;
            font-weight: 600;
            padding: 8px 16px;
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

        .ap-nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 600;
            padding: 12px 20px;
            font-size: 0.9rem;
            border-bottom: 3px solid transparent;
            border-radius: 0;
        }

        .ap-nav-tabs .nav-link.active {
            color: #D9251C;
            border-bottom: 3px solid #D9251C;
            background: transparent;
        }

        .badge-category {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 6px;
        }

        .cat-paper { background-color: rgba(13, 110, 253, 0.1); color: #0d6efd; }
        .cat-ink { background-color: rgba(111, 66, 193, 0.1); color: #6f42c1; }
        .cat-freight { background-color: rgba(253, 126, 20, 0.1); color: #fd7e14; }
        .cat-utilities { background-color: rgba(255, 193, 7, 0.15); color: #856404; }
        .cat-printers { background-color: rgba(32, 201, 151, 0.1); color: #20c997; }
        .cat-government { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; }
        .cat-services { background-color: rgba(13, 202, 240, 0.1); color: #0dcaf0; }
        .cat-default { background-color: rgba(108, 117, 125, 0.1); color: #6c757d; }
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
                <div class="ap-header-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h4 class="fs-24 mb-1 fw-bold text-dark" style="letter-spacing: -0.5px;">Accounts Payable Ledger</h4>
                        <p class="text-muted small mb-0">Manage supplier directory, Purchase Orders, Receiving Reports, Invoices, Payments, Withholding Tax, and 1099 / EWT tax reports.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-outline-danger btn-sm px-3 rounded shadow-sm d-flex align-items-center gap-2" style="height: 40px;" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                            <i class="las la-plus-circle fs-18"></i> Add Supplier
                        </button>
                        <button class="btn btn-danger btn-sm px-3 rounded shadow-sm d-flex align-items-center gap-2" style="height: 40px;" data-bs-toggle="modal" data-bs-target="#addInvoiceModal">
                            <i class="las la-file-invoice-dollar fs-18"></i> Record Invoice
                        </button>
                        <button class="btn btn-success btn-sm px-3 rounded shadow-sm d-flex align-items-center gap-2" style="height: 40px;" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                            <i class="las la-money-check-alt fs-18"></i> Record Payment
                        </button>
                        <button class="btn btn-outline-secondary btn-sm px-3 rounded shadow-sm d-flex align-items-center gap-2" style="height: 40px;" onclick="window.print()">
                            <i class="las la-print fs-18"></i> Print Overview
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
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-danger" style="width: 50px; height: 50px;">
                            <i class="las la-receipt fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Total A/P Balance</span>
                            <h4 class="fw-bold text-dark mb-0">₱{{ number_format($metrics['total_ap_balance'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-warning" style="width: 50px; height: 50px;">
                            <i class="las la-exclamation-triangle fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Total Overdue A/P</span>
                            <h4 class="fw-bold text-warning mb-0">₱{{ number_format($metrics['total_overdue_ap'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-info" style="width: 50px; height: 50px;">
                            <i class="las la-file-contract fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Total Withheld Tax (EWT)</span>
                            <h4 class="fw-bold text-info mb-0">₱{{ number_format($metrics['total_withheld_tax'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-success" style="width: 50px; height: 50px;">
                            <i class="las la-truck-loading fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Active Suppliers</span>
                            <h4 class="fw-bold text-success mb-0">{{ $metrics['active_suppliers_count'] }} Suppliers</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm p-3" style="border-radius: 12px; background: #fff;">
                    <span class="text-muted small fw-bold mb-2 d-block text-uppercase">Supplier Category Filter:</span>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin-finance.accounting.accounts-payable', ['category' => 'All']) }}" class="category-pill {{ $selectedCategory == 'All' ? 'active' : '' }}">
                            All Categories
                        </a>
                        @foreach($categories as $cat)
                        <a href="{{ route('admin-finance.accounting.accounts-payable', ['category' => $cat]) }}" class="category-pill {{ $selectedCategory == $cat ? 'active' : '' }}">
                            {{ $cat }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Master Tabs & Content -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white border-0 pt-3 pb-0">
                        <ul class="nav nav-tabs ap-nav-tabs" id="apTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="suppliers-tab" data-bs-toggle="tab" data-bs-target="#suppliers-pane" type="button" role="tab">
                                    <i class="las la-truck me-1 fs-18"></i> Suppliers Directory
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="po-tab" data-bs-toggle="tab" data-bs-target="#po-pane" type="button" role="tab">
                                    <i class="las la-shopping-cart me-1 fs-18"></i> Purchase Orders ({{ $purchaseOrders->count() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="rr-tab" data-bs-toggle="tab" data-bs-target="#rr-pane" type="button" role="tab">
                                    <i class="las la-boxes me-1 fs-18"></i> Receiving Reports ({{ $receivingReports->count() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="invoices-tab" data-bs-toggle="tab" data-bs-target="#invoices-pane" type="button" role="tab">
                                    <i class="las la-file-invoice-dollar me-1 fs-18"></i> Invoices & Due Dates ({{ $invoices->count() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments-pane" type="button" role="tab">
                                    <i class="las la-money-check-alt me-1 fs-18"></i> Payments ({{ $payments->count() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="ewt-tab" data-bs-toggle="tab" data-bs-target="#ewt-pane" type="button" role="tab">
                                    <i class="las la-calculator me-1 fs-18"></i> Withholding Tax / 1099 Reports
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body pt-3">
                        <div class="tab-content" id="apTabContent">

                            <!-- 1. SUPPLIERS DIRECTORY -->
                            <div class="tab-pane fade show active" id="suppliers-pane" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle" id="suppliersTable">
                                        <thead class="table-light text-muted small text-uppercase">
                                            <tr>
                                                <th>Supplier Code</th>
                                                <th>Company Name</th>
                                                <th>Category</th>
                                                <th>TIN</th>
                                                <th>Contact & Phone</th>
                                                <th>Terms</th>
                                                <th class="text-end">EWT %</th>
                                                <th class="text-end">Total Unpaid Invoices</th>
                                                <th class="text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($suppliers as $supp)
                                            @php
                                                $catClass = match($supp->category) {
                                                    'Paper Suppliers' => 'cat-paper',
                                                    'Ink Suppliers' => 'cat-ink',
                                                    'Freight' => 'cat-freight',
                                                    'Utilities' => 'cat-utilities',
                                                    'Outside Printers' => 'cat-printers',
                                                    'Government' => 'cat-government',
                                                    'Professional Services' => 'cat-services',
                                                    default => 'cat-default'
                                                };
                                                $unpaidInvoicesSum = $supp->invoices->where('status', '!=', 'paid')->sum(function($inv) {
                                                    return max(0, $inv->total_amount - $inv->amount_paid);
                                                });
                                            @endphp
                                            <tr class="hover-row">
                                                <td><span class="fw-bold text-dark">{{ $supp->supplier_code }}</span></td>
                                                <td>
                                                    <span class="fw-bold text-dark d-block fs-14">{{ $supp->company_name }}</span>
                                                    <span class="text-muted small">{{ $supp->address ?: 'No address specified' }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge-category {{ $catClass }}">{{ $supp->category }}</span>
                                                </td>
                                                <td><span class="text-muted fs-13">{{ $supp->tin ?: 'N/A' }}</span></td>
                                                <td>
                                                    <span class="d-block text-dark small fw-medium">{{ $supp->contact_person ?: 'N/A' }}</span>
                                                    <span class="text-muted small">{{ $supp->phone ?: ($supp->email ?: 'N/A') }}</span>
                                                </td>
                                                <td><span class="badge bg-light text-dark border">{{ $supp->terms ?: '30 Days' }}</span></td>
                                                <td class="text-end fw-bold text-primary">{{ number_format($supp->tax_rate ?: 1.00, 2) }}%</td>
                                                <td class="text-end fw-bold text-danger">₱{{ number_format($unpaidInvoicesSum, 2) }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-success-subtle text-success text-capitalize px-3 py-1">{{ $supp->status }}</span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="9" class="text-center py-4 text-muted">No suppliers found for the selected category.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- 2. PURCHASE ORDERS -->
                            <div class="tab-pane fade" id="po-pane" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light text-muted small text-uppercase">
                                            <tr>
                                                <th>PO Number</th>
                                                <th>Supplier</th>
                                                <th>Date</th>
                                                <th>Terms</th>
                                                <th class="text-end">Total Amount</th>
                                                <th class="text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($purchaseOrders as $po)
                                            <tr class="hover-row">
                                                <td>
                                                    <a href="javascript:void(0);" 
                                                       class="fw-bold text-danger view-po-details text-decoration-underline" 
                                                       data-id="{{ $po->id }}"
                                                       title="Click to view Purchase Order details">
                                                        {{ $po->po_number }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-dark d-block">{{ $po->supplier ? $po->supplier->company_name : ($po->vendor_name ?: 'N/A') }}</span>
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($po->date)->format('M d, Y') }}</td>
                                                <td>{{ $po->terms ?: 'Standard' }}</td>
                                                <td class="text-end fw-bold text-dark">{{ $po->currency_symbol }}{{ number_format($po->total_amount, 2) }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-info-subtle text-info text-capitalize px-3 py-1">{{ $po->status }}</span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">No purchase orders recorded yet.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- 3. RECEIVING REPORTS -->
                            <div class="tab-pane fade" id="rr-pane" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light text-muted small text-uppercase">
                                            <tr>
                                                <th>RR Number</th>
                                                <th>PO Reference</th>
                                                <th>Supplier</th>
                                                <th>Received Date</th>
                                                <th>Notes</th>
                                                <th class="text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($receivingReports as $rr)
                                            <tr class="hover-row">
                                                <td><span class="fw-bold text-dark">{{ $rr->rr_number }}</span></td>
                                                <td>
                                                    @if($rr->purchaseOrder)
                                                        <a href="javascript:void(0);" 
                                                           class="badge bg-danger-subtle text-danger border border-danger-subtle view-po-details" 
                                                           data-id="{{ $rr->purchase_order_id }}" 
                                                           title="Click to view Purchase Order details"
                                                           style="cursor: pointer;">
                                                            {{ $rr->purchaseOrder->po_number }}
                                                        </a>
                                                    @else
                                                        <span class="badge bg-light text-dark border">N/A</span>
                                                    @endif
                                                </td>
                                                <td><span class="fw-bold text-dark">{{ $rr->supplier ? $rr->supplier->company_name : 'N/A' }}</span></td>
                                                <td>{{ \Carbon\Carbon::parse($rr->received_date)->format('M d, Y') }}</td>
                                                <td><span class="text-muted small">{{ $rr->notes ?: 'None' }}</span></td>
                                                <td class="text-center">
                                                    <span class="badge bg-success-subtle text-success text-capitalize px-3 py-1">{{ $rr->status }}</span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">No receiving reports logged yet.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- 4. INVOICES & DUE DATES -->
                            <div class="tab-pane fade" id="invoices-pane" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light text-muted small text-uppercase">
                                            <tr>
                                                <th>Invoice No</th>
                                                <th>Supplier</th>
                                                <th>Invoice Date</th>
                                                <th>Due Date</th>
                                                <th class="text-end">Subtotal</th>
                                                <th class="text-end">EWT Withheld</th>
                                                <th class="text-end">Total Amount</th>
                                                <th class="text-end">Amount Paid</th>
                                                <th class="text-end">Balance</th>
                                                <th class="text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($invoices as $inv)
                                            @php
                                                $bal = max(0, $inv->total_amount - $inv->amount_paid);
                                                $statusClass = match($inv->status) {
                                                    'paid' => 'bg-success-subtle text-success',
                                                    'partially_paid' => 'bg-info-subtle text-info',
                                                    default => ($inv->is_overdue ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning')
                                                };
                                            @endphp
                                            <tr class="hover-row">
                                                <td><span class="fw-bold text-dark">{{ $inv->invoice_number }}</span></td>
                                                <td>
                                                    <span class="fw-bold text-dark d-block">{{ $inv->supplier ? $inv->supplier->company_name : 'N/A' }}</span>
                                                    <span class="text-muted small">{{ $inv->supplier ? $inv->supplier->category : '' }}</span>
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($inv->invoice_date)->format('M d, Y') }}</td>
                                                <td>
                                                    <span class="fw-bold {{ $inv->is_overdue ? 'text-danger' : 'text-dark' }}">
                                                        {{ \Carbon\Carbon::parse($inv->due_date)->format('M d, Y') }}
                                                    </span>
                                                    @if($inv->is_overdue && $bal > 0)
                                                    <span class="badge bg-danger text-white ms-1" style="font-size:0.65rem;">OVERDUE</span>
                                                    @endif
                                                </td>
                                                <td class="text-end text-muted">₱{{ number_format($inv->subtotal, 2) }}</td>
                                                <td class="text-end text-info">₱{{ number_format($inv->withholding_tax_amount, 2) }} ({{ $inv->withholding_tax_rate }}%)</td>
                                                <td class="text-end fw-bold text-dark">₱{{ number_format($inv->total_amount, 2) }}</td>
                                                <td class="text-end text-success">₱{{ number_format($inv->amount_paid, 2) }}</td>
                                                <td class="text-end fw-bold text-danger">₱{{ number_format($bal, 2) }}</td>
                                                <td class="text-center">
                                                    <span class="badge {{ $statusClass }} text-uppercase px-3 py-1">
                                                        {{ $inv->is_overdue && $inv->status === 'unpaid' ? 'overdue' : $inv->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="10" class="text-center py-4 text-muted">No supplier invoices recorded yet.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- 5. PAYMENTS -->
                            <div class="tab-pane fade" id="payments-pane" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light text-muted small text-uppercase">
                                            <tr>
                                                <th>Payment No</th>
                                                <th>Payment Date</th>
                                                <th>Supplier</th>
                                                <th>Invoice Reference</th>
                                                <th>Payment Method</th>
                                                <th>Reference No</th>
                                                <th class="text-end">EWT Deduction</th>
                                                <th class="text-end">Amount Paid</th>
                                                <th class="text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($payments as $pay)
                                            <tr class="hover-row">
                                                <td><span class="fw-bold text-dark">{{ $pay->payment_number }}</span></td>
                                                <td>{{ \Carbon\Carbon::parse($pay->payment_date)->format('M d, Y') }}</td>
                                                <td><span class="fw-bold text-dark">{{ $pay->supplier ? $pay->supplier->company_name : 'N/A' }}</span></td>
                                                <td><span class="badge bg-light text-dark border">{{ $pay->invoice ? $pay->invoice->invoice_number : 'N/A' }}</span></td>
                                                <td><span class="badge bg-primary-subtle text-primary">{{ $pay->payment_method }}</span></td>
                                                <td><span class="text-muted small">{{ $pay->reference_number ?: 'N/A' }}</span></td>
                                                <td class="text-end text-info">₱{{ number_format($pay->withholding_tax_amount, 2) }}</td>
                                                <td class="text-end fw-bold text-success">₱{{ number_format($pay->amount_paid, 2) }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-success-subtle text-success text-capitalize px-3 py-1">{{ $pay->status }}</span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="9" class="text-center py-4 text-muted">No supplier payments logged yet.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- 6. WITHHOLDING TAX & 1099 REPORTS -->
                            <div class="tab-pane fade" id="ewt-pane" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0">Expanded Withholding Tax (EWT / BIR 2307 / 1099 Equivalent Summary)</h6>
                                        <p class="text-muted small mb-0">Annual and quarterly breakdown of gross payments, tax base, and tax withheld per supplier.</p>
                                    </div>
                                    <button class="btn btn-outline-primary btn-sm px-3" onclick="window.print()">
                                        <i class="las la-file-export me-1"></i> Export 1099/EWT Report
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="table-light text-muted small text-uppercase">
                                            <tr>
                                                <th>Supplier Code</th>
                                                <th>Supplier / Payee</th>
                                                <th>Category</th>
                                                <th>TIN</th>
                                                <th class="text-center">EWT Rate</th>
                                                <th class="text-end">Gross Amount (Tax Base)</th>
                                                <th class="text-end">Total Tax Withheld</th>
                                                <th class="text-end">Total Net Paid</th>
                                                <th class="text-center">BIR 2307 / 1099 Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($ewtReports as $ewt)
                                            <tr>
                                                <td><span class="fw-bold text-dark">{{ $ewt->supplier_code }}</span></td>
                                                <td>
                                                    <span class="fw-bold text-dark d-block">{{ $ewt->company_name }}</span>
                                                </td>
                                                <td><span class="badge bg-light text-dark border">{{ $ewt->category }}</span></td>
                                                <td><span class="text-muted small">{{ $ewt->tin }}</span></td>
                                                <td class="text-center font-monospace text-primary fw-bold">{{ number_format($ewt->tax_rate, 2) }}%</td>
                                                <td class="text-end fw-bold text-dark">₱{{ number_format($ewt->gross_amount, 2) }}</td>
                                                <td class="text-end fw-bold text-info">₱{{ number_format($ewt->tax_withheld, 2) }}</td>
                                                <td class="text-end fw-bold text-success">₱{{ number_format($ewt->total_paid, 2) }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-success-subtle text-success px-3 py-1">Compliant</span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="9" class="text-center py-4 text-muted">No tax records available.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot class="table-light fw-bold">
                                            <tr>
                                                <td colspan="5" class="text-end">TOTALS:</td>
                                                <td class="text-end text-dark">₱{{ number_format($ewtReports->sum('gross_amount'), 2) }}</td>
                                                <td class="text-end text-info">₱{{ number_format($ewtReports->sum('tax_withheld'), 2) }}</td>
                                                <td class="text-end text-success">₱{{ number_format($ewtReports->sum('total_paid'), 2) }}</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 1: ADD SUPPLIER -->
    <div class="modal fade" id="addSupplierModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin-finance.accounting.accounts-payable.supplier.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold"><i class="las la-truck me-2"></i>Add New Supplier</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold small text-muted">Company Name <span class="text-danger">*</span></label>
                                <input type="text" name="company_name" class="form-control" placeholder="e.g. Pacific Paper Mills Inc." required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Contact Person</label>
                                <input type="text" name="contact_person" class="form-control" placeholder="e.g. John Doe">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Tax ID Number (TIN)</label>
                                <input type="text" name="tin" class="form-control" placeholder="e.g. 000-123-456-000">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="supplier@domain.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Phone Number</label>
                                <input type="text" name="phone" class="form-control" placeholder="+63 912 345 6789">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Withholding Tax Rate (%)</label>
                                <input type="number" step="0.01" name="tax_rate" class="form-control" value="1.00" placeholder="1.00">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Payment Terms</label>
                                <input type="text" name="terms" class="form-control" value="30 Days" placeholder="30 Days">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted">Business Address</label>
                                <textarea name="address" class="form-control" rows="2" placeholder="Full address details..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger px-4 fw-bold">Save Supplier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: RECORD INVOICE -->
    <div class="modal fade" id="addInvoiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin-finance.accounting.accounts-payable.invoice.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title fw-bold"><i class="las la-file-invoice-dollar me-2"></i>Record Supplier Invoice</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Supplier <span class="text-danger">*</span></label>
                                <select name="supplier_id" class="form-select" required>
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supp)
                                    <option value="{{ $supp->id }}">{{ $supp->company_name }} ({{ $supp->category }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Supplier Invoice No <span class="text-danger">*</span></label>
                                <input type="text" name="invoice_number" class="form-control" placeholder="e.g. INV-2026-0091" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Invoice Date <span class="text-danger">*</span></label>
                                <input type="date" name="invoice_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Due Date <span class="text-danger">*</span></label>
                                <input type="date" name="due_date" class="form-control" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Subtotal (₱) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="subtotal" class="form-control" placeholder="0.00" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Withholding Tax Rate % (Override default)</label>
                                <input type="number" step="0.01" name="withholding_tax_rate" class="form-control" placeholder="Default supplier rate">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Linked Purchase Order (Optional)</label>
                                <select name="purchase_order_id" class="form-select">
                                    <option value="">None</option>
                                    @foreach($purchaseOrders as $po)
                                    <option value="{{ $po->id }}">{{ $po->po_number }} - ₱{{ number_format($po->total_amount, 2) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Linked Receiving Report (Optional)</label>
                                <select name="receiving_report_id" class="form-select">
                                    <option value="">None</option>
                                    @foreach($receivingReports as $rr)
                                    <option value="{{ $rr->id }}">{{ $rr->rr_number }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted">Notes / Item Details</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Description of goods/services billed..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark px-4 fw-bold">Save Invoice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 3: RECORD PAYMENT -->
    <div class="modal fade" id="addPaymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin-finance.accounting.accounts-payable.payment.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold"><i class="las la-money-check-alt me-2"></i>Record Supplier Payment</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Supplier <span class="text-danger">*</span></label>
                                <select name="supplier_id" class="form-select" required>
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supp)
                                    <option value="{{ $supp->id }}">{{ $supp->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Pay Against Invoice (Optional)</label>
                                <select name="supplier_invoice_id" class="form-select">
                                    <option value="">Direct Payment / Advance</option>
                                    @foreach($invoices->where('status', '!=', 'paid') as $inv)
                                    <option value="{{ $inv->id }}">{{ $inv->invoice_number }} ({{ $inv->supplier ? $inv->supplier->company_name : '' }}) - Bal: ₱{{ number_format($inv->total_amount - $inv->amount_paid, 2) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Payment Method <span class="text-danger">*</span></label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="Check">Check</option>
                                    <option value="Bank Transfer">Bank Transfer / Wire</option>
                                    <option value="Cash">Cash</option>
                                    <option value="E-Wallet">E-Wallet / Online</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Amount Paid (₱) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount_paid" class="form-control" placeholder="0.00" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Check / Reference No.</label>
                                <input type="text" name="reference_number" class="form-control" placeholder="e.g. CHK-990210">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted">Notes</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Voucher / Disbursement remarks..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success px-4 fw-bold">Post Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 4: PURCHASE ORDER DETAILS -->
    <div class="modal fade" id="poDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold text-white"><i class="las la-file-invoice me-2"></i>Purchase Order Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="poModalBody">
                    <div class="text-center p-5">
                        <div class="spinner-border text-danger" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger px-4 fw-bold" onclick="printPoModalContent('poModalBody')">
                        <i class="las la-print me-1"></i> Print PO
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $(document).on('click', '.view-po-details', function(e) {
                e.preventDefault();
                const poId = $(this).data('id');
                if (!poId) return;

                const modalElement = document.getElementById('poDetailsModal');
                const modal = new bootstrap.Modal(modalElement);
                
                $('#poModalBody').html('<div class="text-center p-5"><div class="spinner-border text-danger" role="status"><span class="visually-hidden">Loading...</span></div></div>');
                modal.show();

                $.ajax({
                    url: `/production/logistic/purchase-order/${poId}`,
                    method: 'GET',
                    success: function(response) {
                        $('#poModalBody').html(response);
                    },
                    error: function() {
                        $('#poModalBody').html('<div class="alert alert-danger">Failed to load Purchase Order details.</div>');
                    }
                });
            });
        });

        function printPoModalContent(divId) {
            const content = document.getElementById(divId).innerHTML;
            const printWindow = window.open('', '', 'height=700,width=900');
            printWindow.document.write('<html><head><title>Print Purchase Order</title>');
            const styles = document.getElementsByTagName('style');
            for (let i = 0; i < styles.length; i++) {
                printWindow.document.write(styles[i].outerHTML);
            }
            printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">');
            printWindow.document.write('</head><body>');
            printWindow.document.write(content);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            setTimeout(() => {
                printWindow.print();
            }, 500);
        }
    </script>
    @endpush
</x-app-layout>

<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .ap-header-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.5rem 1.75rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            margin-bottom: 1.5rem;
        }

        .btn-ap-primary {
            background-color: #D9251C;
            border-color: #D9251C;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.8125rem;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
        }

        .btn-ap-primary:hover {
            background-color: #b91c1c;
            border-color: #b91c1c;
            color: #ffffff;
        }

        .btn-ap-outline {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            color: #334155;
            font-weight: 600;
            font-size: 0.8125rem;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
        }

        .btn-ap-outline:hover {
            background-color: #f8fafc;
            border-color: #94a3b8;
            color: #0f172a;
        }

        .btn-ap-success {
            background-color: #059669;
            border-color: #059669;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.8125rem;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
        }

        .btn-ap-success:hover {
            background-color: #047857;
            border-color: #047857;
            color: #ffffff;
        }

        .ap-kpi-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.25rem;
            transition: all 0.2s ease;
        }

        .ap-kpi-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }

        .kpi-icon-wrapper {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .category-segmented-bar {
            background: #f1f5f9;
            padding: 4px;
            border-radius: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }

        .category-pill {
            font-size: 0.8125rem;
            font-weight: 500;
            padding: 6px 14px;
            border-radius: 7px;
            text-decoration: none;
            color: #64748b;
            transition: all 0.15s ease;
            display: inline-block;
        }

        .category-pill.active {
            background-color: #ffffff;
            color: #0f172a !important;
            font-weight: 600;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .category-pill:not(.active):hover {
            color: #1e293b;
            background-color: rgba(255, 255, 255, 0.6);
        }

        .ap-nav-tabs {
            border-bottom: 1px solid #e2e8f0;
        }

        .ap-nav-tabs .nav-link {
            border: none;
            color: #64748b;
            font-weight: 500;
            padding: 10px 18px;
            font-size: 0.875rem;
            border-bottom: 2px solid transparent;
            border-radius: 0;
            transition: all 0.15s ease;
        }

        .ap-nav-tabs .nav-link:hover {
            color: #0f172a;
        }

        .ap-nav-tabs .nav-link.active {
            color: #D9251C;
            font-weight: 600;
            border-bottom: 2px solid #D9251C;
            background: transparent;
        }

        .badge-category {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 6px;
        }

        .cat-paper { background-color: #eff6ff; color: #1d4ed8; }
        .cat-ink { background-color: #faf5ff; color: #7e22ce; }
        .cat-freight { background-color: #fff7ed; color: #c2410c; }
        .cat-utilities { background-color: #fefce8; color: #a16207; }
        .cat-printers { background-color: #f0fdf4; color: #15803d; }
        .cat-government { background-color: #fef2f2; color: #b91c1c; }
        .cat-services { background-color: #ecfeff; color: #0e7490; }
        .cat-default { background-color: #f1f5f9; color: #475569; }

        .table-custom-header thead th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-size: 0.725rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            border-bottom: 1px solid #e2e8f0 !important;
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

        <!-- Clean Compact Title Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h4 class="fs-22 mb-1 fw-bold text-dark"><i class="las la-wallet me-2 text-danger"></i>Accounts Payable Ledger</h4>
                <p class="text-muted small mb-0">Manage supplier directory, purchase orders, invoices, settlements, and EWT tax tracking.</p>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <button class="btn btn-ap-outline d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                    <i class="las la-plus-circle fs-16"></i> Add Supplier
                </button>
                <button class="btn btn-ap-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addInvoiceModal">
                    <i class="las la-file-invoice-dollar fs-16"></i> Record Invoice
                </button>
                <button class="btn btn-ap-success d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                    <i class="las la-money-check-alt fs-16"></i> Record Payment
                </button>
            </div>
        </div>

        <!-- Metric summary cards -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="ap-kpi-card h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.725rem; letter-spacing: 0.5px;">Total A/P Balance</span>
                        <div class="kpi-icon-wrapper" style="background-color: #fef2f2; color: #dc2626;">
                            <i class="las la-receipt fs-20"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-dark mb-0 fs-20">₱{{ number_format($metrics['total_ap_balance'], 2) }}</h4>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="ap-kpi-card h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.725rem; letter-spacing: 0.5px;">Total Overdue A/P</span>
                        <div class="kpi-icon-wrapper" style="background-color: #fffbeb; color: #d97706;">
                            <i class="las la-exclamation-triangle fs-20"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-0 fs-20" style="color: #d97706;">₱{{ number_format($metrics['total_overdue_ap'], 2) }}</h4>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="ap-kpi-card h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.725rem; letter-spacing: 0.5px;">Total Withheld Tax (EWT)</span>
                        <div class="kpi-icon-wrapper" style="background-color: #f0f9ff; color: #0284c7;">
                            <i class="las la-file-contract fs-20"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-0 fs-20" style="color: #0284c7;">₱{{ number_format($metrics['total_withheld_tax'], 2) }}</h4>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="ap-kpi-card h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.725rem; letter-spacing: 0.5px;">Active Suppliers</span>
                        <div class="kpi-icon-wrapper" style="background-color: #f0fdf4; color: #16a34a;">
                            <i class="las la-truck-loading fs-20"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-0 fs-20" style="color: #16a34a;">{{ $metrics['active_suppliers_count'] }} <span class="fs-14 fw-normal text-muted">Suppliers</span></h4>
                </div>
            </div>
        </div>

        <!-- Master Tabs & Content -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border: 1px solid #e2e8f0 !important;">
                    <div class="card-header bg-white border-0 pt-3 pb-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                        <ul class="nav nav-tabs ap-nav-tabs mb-0" id="apTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="suppliers-tab" data-bs-toggle="tab" data-bs-target="#suppliers-pane" type="button" role="tab">
                                    <i class="las la-truck me-1 fs-18"></i> Suppliers Directory
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="invoices-tab" data-bs-toggle="tab" data-bs-target="#invoices-pane" type="button" role="tab">
                                    <i class="las la-file-invoice-dollar me-1 fs-18"></i> Invoices & Due Dates <span class="badge bg-light text-muted border rounded-pill ms-1">{{ $invoices->count() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments-pane" type="button" role="tab">
                                    <i class="las la-money-check-alt me-1 fs-18"></i> Payments <span class="badge bg-light text-muted border rounded-pill ms-1">{{ $payments->count() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="po-tab" data-bs-toggle="tab" data-bs-target="#po-pane" type="button" role="tab">
                                    <i class="las la-shopping-cart me-1 fs-18"></i> Purchase Orders <span class="badge bg-light text-muted border rounded-pill ms-1">{{ $purchaseOrders->count() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="rr-tab" data-bs-toggle="tab" data-bs-target="#rr-pane" type="button" role="tab">
                                    <i class="las la-boxes me-1 fs-18"></i> Receiving Reports <span class="badge bg-light text-muted border rounded-pill ms-1">{{ $receivingReports->count() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="ewt-tab" data-bs-toggle="tab" data-bs-target="#ewt-pane" type="button" role="tab">
                                    <i class="las la-calculator me-1 fs-18"></i> Withholding Tax
                                </button>
                            </li>
                        </ul>

                        <!-- Compact Category Dropdown Filter -->
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="text-muted small fw-bold text-nowrap"><i class="las la-filter me-1"></i>Category:</span>
                            <select class="form-select form-select-sm shadow-none" style="width: 180px; font-size: 0.8125rem; border-radius: 6px;" onchange="location = this.value;">
                                <option value="{{ route('admin-finance.accounting.accounts-payable', ['category' => 'All']) }}" {{ $selectedCategory == 'All' ? 'selected' : '' }}>All Categories</option>
                                @foreach($categories as $cat)
                                <option value="{{ route('admin-finance.accounting.accounts-payable', ['category' => $cat]) }}" {{ $selectedCategory == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="card-body pt-3">
                        <div class="tab-content" id="apTabContent">

                            <!-- 1. SUPPLIERS DIRECTORY -->
                            <div class="tab-pane fade show active" id="suppliers-pane" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle table-custom-header" id="suppliersTable">
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
                                                <th class="text-center" style="width: 100px;">Action</th>
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
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button type="button" class="btn btn-sm btn-outline-primary shadow-sm px-2 py-0" data-bs-toggle="modal" data-bs-target="#editSupplierModal-{{ $supp->id }}" title="Edit Supplier"><i class="las la-edit"></i></button>
                                                        <form action="{{ route('admin-finance.accounting.accounts-payable.supplier.destroy', $supp->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete supplier {{ $supp->company_name }}?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm px-2 py-0" title="Delete Supplier"><i class="las la-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="10" class="text-center py-4 text-muted">No suppliers found for the selected category.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- 2. PURCHASE ORDERS -->
                            <div class="tab-pane fade" id="po-pane" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle table-custom-header" id="poTable">
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
                                    <table class="table table-hover align-middle table-custom-header" id="rrTable">
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
                                    <table class="table table-hover align-middle table-custom-header" id="invoicesTable">
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
                                                <th class="text-center" style="width: 100px;">Action</th>
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
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button type="button" class="btn btn-sm btn-outline-primary shadow-sm px-2 py-0" data-bs-toggle="modal" data-bs-target="#editInvoiceModal-{{ $inv->id }}" title="Edit Invoice"><i class="las la-edit"></i></button>
                                                        <form action="{{ route('admin-finance.accounting.accounts-payable.invoice.destroy', $inv->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete invoice {{ $inv->invoice_number }}?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm px-2 py-0" title="Delete Invoice"><i class="las la-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="11" class="text-center py-4 text-muted">No supplier invoices recorded yet.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- 5. PAYMENTS -->
                            <div class="tab-pane fade" id="payments-pane" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle table-custom-header" id="paymentsTable">
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
                                                <th class="text-center" style="width: 80px;">Action</th>
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
                                                <td class="text-center">
                                                    <form action="{{ route('admin-finance.accounting.accounts-payable.payment.destroy', $pay->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete payment {{ $pay->payment_number }}?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm px-2 py-0" title="Delete Payment"><i class="las la-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="10" class="text-center py-4 text-muted">No supplier payments logged yet.</td>
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
                                    <table class="table table-bordered align-middle" id="ewtTable">
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
            <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
                <form action="{{ route('admin-finance.accounting.accounts-payable.invoice.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-dark text-white py-3">
                        <h5 class="modal-title fw-bold text-white"><i class="las la-file-invoice-dollar me-2 text-danger"></i>Record Supplier Invoice</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <!-- Supplier -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Supplier <span class="text-danger">*</span></label>
                                <select name="supplier_id" id="invoiceSupplierSelect" class="form-select" required>
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supp)
                                    <option value="{{ $supp->id }}" data-category="{{ $supp->category }}" data-tax-rate="{{ $supp->tax_rate ?: '1.00' }}">
                                        {{ $supp->company_name }} ({{ $supp->supplier_code }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Category -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Category <span class="text-danger">*</span></label>
                                <select name="category" id="invoiceCategorySelect" class="form-select" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Invoice Date -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Invoice Date <span class="text-danger">*</span></label>
                                <input type="date" name="invoice_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <!-- Due Date -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Due Date <span class="text-danger">*</span></label>
                                <input type="date" name="due_date" class="form-control" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                            </div>

                            <!-- Supplier Invoice No -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Supplier Invoice No <span class="text-danger">*</span></label>
                                <input type="text" name="invoice_number" class="form-control" placeholder="e.g. INV-2026-0091" required>
                            </div>

                            <!-- Subtotal -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Subtotal Amount (₱) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="subtotal" class="form-control fw-bold text-dark" placeholder="0.00" required>
                            </div>

                            <!-- Withholding Tax Rate -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Withholding Tax Rate (%)</label>
                                <input type="number" step="0.01" name="withholding_tax_rate" id="invoiceTaxRateInput" class="form-control" placeholder="Default supplier rate (e.g. 1.00)">
                            </div>

                            <!-- Linked PO -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Linked Purchase Order (Optional)</label>
                                <select name="purchase_order_id" class="form-select">
                                    <option value="">None</option>
                                    @foreach($purchaseOrders as $po)
                                    <option value="{{ $po->id }}">{{ $po->po_number }} - ₱{{ number_format($po->total_amount, 2) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Notes -->
                            <div class="col-12">
                                <label class="form-label fw-bold small text-dark">Notes / Description</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Description of goods/services billed..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary px-4 shadow-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger px-4 fw-bold shadow-sm"><i class="las la-save me-1"></i> Save Invoice</button>
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

    <!-- EDIT SUPPLIER MODALS -->
    @foreach($suppliers as $supp)
    <div class="modal fade" id="editSupplierModal-{{ $supp->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin-finance.accounting.accounts-payable.supplier.update', $supp->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title fw-bold text-white"><i class="las la-edit me-2 text-danger"></i>Edit Supplier: {{ $supp->company_name }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold small text-muted">Company Name <span class="text-danger">*</span></label>
                                <input type="text" name="company_name" class="form-control" value="{{ $supp->company_name }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ $supp->category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Contact Person</label>
                                <input type="text" name="contact_person" class="form-control" value="{{ $supp->contact_person }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Tax ID Number (TIN)</label>
                                <input type="text" name="tin" class="form-control" value="{{ $supp->tin }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Email Address</label>
                                <input type="email" name="email" class="form-control" value="{{ $supp->email }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="{{ $supp->phone }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Withholding Tax Rate (%)</label>
                                <input type="number" step="0.01" name="tax_rate" class="form-control" value="{{ $supp->tax_rate }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Payment Terms</label>
                                <input type="text" name="terms" class="form-control" value="{{ $supp->terms }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active" {{ $supp->status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $supp->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted">Business Address</label>
                                <textarea name="address" class="form-control" rows="2">{{ $supp->address }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger px-4 fw-bold">Update Supplier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    <!-- EDIT INVOICE MODALS -->
    @foreach($invoices as $inv)
    <div class="modal fade" id="editInvoiceModal-{{ $inv->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin-finance.accounting.accounts-payable.invoice.update', $inv->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-dark text-white py-3">
                        <h5 class="modal-title fw-bold text-white"><i class="las la-edit me-2 text-danger"></i>Edit Invoice: {{ $inv->invoice_number }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Supplier Invoice No <span class="text-danger">*</span></label>
                                <input type="text" name="invoice_number" class="form-control" value="{{ $inv->invoice_number }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Subtotal Amount (₱) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="subtotal" class="form-control fw-bold" value="{{ $inv->subtotal }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Invoice Date <span class="text-danger">*</span></label>
                                <input type="date" name="invoice_date" class="form-control" value="{{ \Carbon\Carbon::parse($inv->invoice_date)->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Due Date <span class="text-danger">*</span></label>
                                <input type="date" name="due_date" class="form-control" value="{{ \Carbon\Carbon::parse($inv->due_date)->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Withholding Tax Rate (%)</label>
                                <input type="number" step="0.01" name="withholding_tax_rate" class="form-control" value="{{ $inv->withholding_tax_rate }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-dark">Notes / Description</label>
                                <textarea name="notes" class="form-control" rows="2">{{ $inv->notes }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger px-4 fw-bold">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            if ($.fn.DataTable) {
                $('#suppliersTable').DataTable({ pageLength: 10, responsive: true });
                $('#invoicesTable').DataTable({ pageLength: 10, responsive: true });
                $('#paymentsTable').DataTable({ pageLength: 10, responsive: true });
                $('#poTable').DataTable({ pageLength: 10, responsive: true });
                $('#rrTable').DataTable({ pageLength: 10, responsive: true });
                $('#ewtTable').DataTable({ pageLength: 10, responsive: true });
            }

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

        document.addEventListener('DOMContentLoaded', function() {
            const suppSelect = document.getElementById('invoiceSupplierSelect');
            const catSelect = document.getElementById('invoiceCategorySelect');
            const taxInput = document.getElementById('invoiceTaxRateInput');

            if (suppSelect && catSelect) {
                suppSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (!selectedOption) return;
                    const cat = selectedOption.getAttribute('data-category');
                    const tax = selectedOption.getAttribute('data-tax-rate');

                    if (cat) {
                        catSelect.value = cat;
                    }
                    if (tax && taxInput && (!taxInput.value || taxInput.value === '1.00')) {
                        taxInput.value = tax;
                    }
                });

                catSelect.addEventListener('change', function() {
                    const selectedCat = this.value;
                    Array.from(suppSelect.options).forEach(opt => {
                        if (!opt.value) return;
                        const optCat = opt.getAttribute('data-category');
                        if (!selectedCat || optCat === selectedCat) {
                            opt.style.display = '';
                        } else {
                            opt.style.display = 'none';
                        }
                    });
                });
            }
        });
    </script>
    @endpush
</x-app-layout>

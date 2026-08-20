<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <h4 class="fs-20 mb-0">Sales Invoice Management</h4>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <div class="row mb-4 align-items-end">
                            <div class="col-md mb-2 mb-md-0">
                                <label for="siSearchInput" class="form-label fw-bold text-dark"><i class="fas fa-search me-1 text-primary"></i> Search</label>
                                <input type="text" id="siSearchInput" class="form-control form-control-sm" placeholder="Search by SO #, Customer, Type, Status..." style="height: 36px;">
                            </div>
                            <div class="col-md mb-2 mb-md-0">
                                <label for="siTypeSelect" class="form-label fw-bold text-dark"><i class="fas fa-filter me-1 text-primary"></i> Type / Category</label>
                                <select id="siTypeSelect" class="form-select form-select-sm text-black" style="height: 36px;">
                                    <option value="">All Types</option>
                                    <option value="area_sales_consignment">Area Sales Consignment</option>
                                    <option value="area_consignment">Area Consignment</option>
                                    <option value="paid">Paid</option>
                                    <option value="wholesale">Wholesale</option>
                                    <option value="retail">Retail</option>
                                    <option value="bookstore">Bookstore</option>
                                    <option value="ecom_direct">E-Com Direct</option>
                                </select>
                            </div>
                            <div class="col-md mb-2 mb-md-0">
                                <label for="siPaymentMethodSelect" class="form-label fw-bold text-dark"><i class="las la-wallet me-1 text-primary"></i> Payment Method</label>
                                <select id="siPaymentMethodSelect" class="form-select form-select-sm text-black" style="height: 36px;">
                                    <option value="">All Payment Methods</option>
                                    <option value="cash">Cash</option>
                                    <option value="gcash">GCash</option>
                                    <option value="maya">Maya</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="check">Check</option>
                                    <option value="card">Credit/Debit Card</option>
                                </select>
                            </div>
                            <div class="col-md mb-2 mb-md-0" id="platformFilterContainer" style="display: none;">
                                <label for="siPlatformSelect" class="form-label fw-bold text-dark"><i class="las la-store me-1 text-primary" style="font-size: 1.1rem;"></i> Platform</label>
                                <select id="siPlatformSelect" class="form-select form-select-sm text-black" style="height: 36px;">
                                    <option value="">All Platforms</option>
                                    <option value="lazada">Lazada</option>
                                    <option value="shopee">Shopee</option>
                                    <option value="tiktok">TikTok</option>
                                </select>
                            </div>
                            <div class="col-md mb-2 mb-md-0">
                                <label for="siStartDate" class="form-label fw-bold text-dark"><i class="fas fa-calendar-alt me-1 text-primary"></i> Start Date</label>
                                <input type="date" id="siStartDate" class="form-control form-control-sm" style="height: 36px;">
                            </div>
                            <div class="col-md mb-2 mb-md-0">
                                <label for="siEndDate" class="form-label fw-bold text-dark"><i class="fas fa-calendar-alt me-1 text-primary"></i> End Date</label>
                                <input type="date" id="siEndDate" class="form-control form-control-sm" style="height: 36px;">
                            </div>
                            <div class="col-md-auto mb-2 mb-md-0">
                                <label for="siEntriesSelect" class="form-label fw-bold text-dark"><i class="fas fa-list me-1 text-primary"></i> Show Entries</label>
                                <select id="siEntriesSelect" class="form-select form-select-sm text-black" style="height: 36px; min-width: 95px;">
                                    <option value="5" selected>5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="500">500</option>
                                    <option value="all">All</option>
                                </select>
                            </div>
                            <div class="col-md-auto">
                                <button id="clearFiltersBtn" class="btn btn-light btn-sm" style="border: 1px solid #ddd; height: 36px; min-width: 100px;"><i class="fas fa-undo me-1"></i> Reset</button>
                            </div>
                        </div>

                        <!-- Bulk Actions Bar -->
                        <div id="bulkActionsBar" class="alert alert-light border d-none justify-content-between align-items-center mb-4 py-2 px-3 shadow-sm bg-white rounded" style="border-left: 4px solid #0d6efd !important;">
                            <div class="d-flex align-items-center gap-3">
                                <span class="fw-bold text-dark"><span id="selectedCount" class="badge bg-primary fs-14">0</span> Sales Order(s) selected</span>
                                <span id="selectedTotalAmount" class="fw-bold text-success d-none">| Total: <span id="totalAmountValue">₱0.00</span></span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" id="bulkPrepareBtn" class="btn btn-warning btn-sm px-3 fw-bold">
                                    <i class="las la-file-invoice me-1"></i> Bulk Prepare & Submit SI
                                </button>
                                <button type="button" id="bulkFinalizeBtn" class="btn btn-primary btn-sm px-3 fw-bold">
                                    <i class="las la-check-double me-1"></i> Bulk Sign & Approve
                                </button>
                                <button type="button" id="bulkPrintSIBtn" class="btn btn-info btn-sm px-3 fw-bold d-none">
                                    <i class="las la-print me-1"></i> Print Selected SIs
                                </button>
                            </div>
                        </div>

                        <!-- Nav Tabs -->
                        <ul class="nav nav-tabs mb-4" id="siTabs" role="tablist" style="border-bottom: 2px solid #eee;">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-bold text-uppercase border-0 bg-transparent text-dark" id="normal-tab" data-bs-toggle="tab" data-bs-target="#normal-pane" type="button" role="tab" aria-controls="normal-pane" aria-selected="true" style="border-bottom: 3px solid #ff0000; padding: 10px 15px; transition: all 0.3s;">
                                    <i class="las la-file-invoice me-1 text-danger" style="font-size: 1.2rem;"></i> Normal Invoices ({{ $normalOrders->count() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold text-uppercase border-0 bg-transparent text-muted" id="ecom-tab" data-bs-toggle="tab" data-bs-target="#ecom-pane" type="button" role="tab" aria-controls="ecom-pane" aria-selected="false" style="padding: 10px 15px; transition: all 0.3s;">
                                    <i class="las la-store me-1 text-primary" style="font-size: 1.2rem;"></i> Direct Invoice (E-com) ({{ $ecomOrders->count() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold text-uppercase border-0 bg-transparent text-muted" id="completed-ecom-tab" data-bs-toggle="tab" data-bs-target="#completed-ecom-pane" type="button" role="tab" aria-controls="completed-ecom-pane" aria-selected="false" style="padding: 10px 15px; transition: all 0.3s;">
                                    <i class="las la-shopping-cart me-1 text-info" style="font-size: 1.2rem;"></i> Completed E-com ({{ $completedEcomSIs->count() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold text-uppercase border-0 bg-transparent text-muted" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-pane" type="button" role="tab" aria-controls="completed-pane" aria-selected="false" style="padding: 10px 15px; transition: all 0.3s;">
                                    <i class="las la-check-circle me-1 text-success" style="font-size: 1.2rem;"></i> Completed SI ({{ $completedSIs->count() }})
                                </button>
                            </li>
                            
                        </ul>

                        <div class="tab-content" id="siTabsContent">
                            <!-- Normal Invoices Tab Pane -->
                            <div class="tab-pane fade show active" id="normal-pane" role="tabpanel" aria-labelledby="normal-tab">
                                <div class="table-responsive">
                                    <table class="table table-responsive-md">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;">
                                                    <input type="checkbox" id="selectAllNormal" style="width: 16px; height: 16px; cursor: pointer;">
                                                </th>
                                                <th>SO Number</th>
                                                <th>Customer</th>
                                                <th>Type</th>
                                                <th>Payment Method</th>
                                                <th>Total Amount</th>
                                                <th>Paid Amount</th>
                                                <th>Remaining</th>
                                                <th>Order Status</th>
                                                <th>Payment Status</th>
                                                <th>SI Prepared By</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($normalOrders as $order)
                                            @php
                                                $displayAmount = (float) $order->total_amount;
                                                if (in_array($order->type, ['area_consignment', 'area_sales_consignment'])) {
                                                    $activeInvoice = \App\Models\SalesInvoice::where('so_id', $order->id)->where('status', '!=', 'cancelled')->latest()->first();
                                                    if ($activeInvoice) {
                                                        $displayAmount = (float) $activeInvoice->total_amount;
                                                    }
                                                }
                                                $paidAmt = (float) $order->total_paid_amount;
                                                $remBal = (float) $order->remaining_balance;
                                                $pmStatus = $order->computed_payment_status;
                                                $pmBadgeColor = $pmStatus === 'paid' ? 'success' : ($pmStatus === 'partially_paid' ? 'warning' : 'danger');
                                                $pmLabel = $pmStatus === 'partially_paid' ? 'PARTIALLY PAID' : strtoupper($pmStatus);
                                                $ordCurr = $order->currency ?? 'PHP';
                                                $ordSym = ($ordCurr === 'USD' ? '$' : ($ordCurr === 'EUR' ? '€' : '₱'));
                                            @endphp
                                            <tr class="si-row" data-date="{{ $order->created_at->format('Y-m-d') }}" data-type="{{ $order->type }}" data-amount="{{ $displayAmount }}" data-paid="{{ $paidAmt }}" data-remaining="{{ $remBal }}">
                                                <td>
                                                    @if($order->status === 'pending_si_prep' || $order->status === 'pending_si_approval' || $order->status === 'si_created' || $order->status === 'ar_created')
                                                        <input type="checkbox" class="order-checkbox normal-check" value="{{ $order->id }}" data-proof="{{ ($order->proof_of_payment || in_array($order->type, ['ecom_direct', 'charge', 'area_consignment', 'area_sales_consignment', 'direct_consignment', 'complimentary', 'cod'])) ? 'yes' : 'no' }}" data-amount="{{ $displayAmount }}" style="width: 16px; height: 16px; cursor: pointer;">
                                                    @else
                                                        <input type="checkbox" disabled style="width: 16px; height: 16px; opacity: 0.4;">
                                                    @endif
                                                </td>
                                                <td><strong>#{{ $order->so_number }}</strong></td>
                                                <td>{{ $order->customer->customer_name ?? 'N/A' }}</td>
                                                <td><span class="badge badge-outline-dark">{{ ucfirst(str_replace('_', ' ', $order->type)) }}</span></td>
                                                <td>
                                                    @php $currentPm = strtolower($order->payment_method ?? 'cash'); @endphp
                                                    <select class="form-select form-select-sm pm-select text-black fw-bold"
                                                            data-order-id="{{ $order->id }}"
                                                            style="height: 32px; font-size: 12px; border: 1.5px solid #0d6efd; background-color: #f0f7ff; cursor: pointer; min-width: 130px;">
                                                        <option value="cash" {{ $currentPm === 'cash' ? 'selected' : '' }}>💵 Cash</option>
                                                        <option value="gcash" {{ $currentPm === 'gcash' ? 'selected' : '' }}>📱 GCash</option>
                                                        <option value="maya" {{ $currentPm === 'maya' ? 'selected' : '' }}>📱 Maya</option>
                                                        <option value="bank_transfer" {{ $currentPm === 'bank_transfer' ? 'selected' : '' }}>🏦 Bank Transfer</option>
                                                        <option value="check" {{ $currentPm === 'check' ? 'selected' : '' }}>🧾 Check</option>
                                                        <option value="card" {{ $currentPm === 'card' ? 'selected' : '' }}>💳 Card</option>
                                                    </select>
                                                </td>
                                                <td class="fw-bold">{{ $ordSym }}{{ number_format($displayAmount, 2) }}</td>
                                                <td class="text-success fw-bold">{{ $ordSym }}{{ number_format($paidAmt, 2) }}</td>
                                                <td class="text-danger fw-bold">{{ $ordSym }}{{ number_format($remBal, 2) }}</td>
                                                <td>
                                                    @php
                                                        $statusClass = 'secondary';
                                                        $displayStatus = str_replace('_', ' ', $order->status);
                                                        if ($order->status === 'pending_si_prep' || $order->status === 'ar_created') {
                                                            $statusClass = 'warning';
                                                            $displayStatus = 'Gathered (Pending SI Prep)';
                                                        } elseif ($order->status === 'si_created') {
                                                            $statusClass = 'warning';
                                                            $displayStatus = 'SI Linked (Pending Prep)';
                                                        } elseif ($order->status === 'pending_si_approval') {
                                                            $statusClass = 'info';
                                                            $displayStatus = 'SI Prepared (Pending Approval)';
                                                        } elseif ($order->status === 'ready_for_delivery') {
                                                            $statusClass = 'success';
                                                        }
                                                    @endphp
                                                    <span class="badge badge-{{ $statusClass }}">
                                                        {{ ucwords($displayStatus) }}
                                                    </span>
                                                </td>
                                                <td><span class="badge badge-{{ $pmBadgeColor }}">{{ $pmLabel }}</span></td>
                                                <td>{{ $order->siPreparedBy->name ?? 'N/A' }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                                        <a href="{{ route('admin-finance.sales-order.detail', $order->id) }}" class="btn btn-primary shadow btn-sm" title="View SO Detail"><i class="fas fa-eye"></i> View</a>
                                                        
                                                        @if($remBal > 0 && $order->customer_id)
                                                            <button type="button" class="btn btn-success btn-sm open-pay-modal-btn shadow-sm" data-so-id="{{ $order->id }}" data-customer-id="{{ $order->customer_id }}" data-so-number="{{ $order->so_number }}" data-total="{{ $displayAmount }}" data-paid="{{ $paidAmt }}" data-remaining="{{ $remBal }}" data-terms="{{ $order->terms ?? 'COD' }}" data-due-date="{{ $order->due_date ? $order->due_date->format('M d, Y') : 'N/A' }}" data-currency="{{ $order->currency ?? 'USD' }}" data-symbol="{{ $ordSym }}">
                                                                <i class="las la-coins me-1"></i> Pay
                                                            </button>
                                                        @endif

                                                        @if($order->status === 'pending_si_prep' || $order->status === 'si_created' || $order->status === 'ar_created')
                                                            @if($order->proof_of_payment || in_array($order->type, ['ecom_direct', 'charge', 'area_consignment', 'area_sales_consignment', 'direct_consignment', 'complimentary', 'cod']) || $paidAmt > 0)
                                                                <a href="{{ route('admin-finance.accounting.sales-invoice.prepare', $order->id) }}" class="btn btn-warning btn-sm">Prepare SI</a>
                                                            @else
                                                                <button class="btn btn-warning btn-sm" disabled title="Proof of Payment is required to prepare SI"><i class="fas fa-exclamation-triangle me-1"></i> Prepare SI</button>
                                                            @endif
                                                        @endif
 
                                                        @if($order->status === 'pending_si_approval')
                                                            @if($order->proof_of_payment || in_array($order->type, ['ecom_direct', 'charge', 'area_consignment', 'area_sales_consignment', 'direct_consignment', 'complimentary', 'cod']))
                                                                <form action="{{ route('admin-finance.accounting.sales-invoice.sign', $order->id) }}" method="POST" class="m-0">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-success btn-sm">Sign & Approve</button>
                                                                </form>
                                                            @else
                                                                <button class="btn btn-success btn-sm" disabled title="Proof of Payment is required to sign SI"><i class="fas fa-exclamation-triangle me-1"></i> Sign & Approve</button>
                                                            @endif
                                                        @endif
                                                        
                                                        @if($order->status === 'ready_for_delivery')
                                                        <a href="{{ route('admin-finance.accounting.sales-invoice.print', $order->id) }}" class="btn btn-info btn-sm" target="_blank">Print SI</a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="12" class="text-center py-4 text-muted">No normal orders requiring Sales Invoice at this time.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr id="normalTotalRow" style="background: #f8f9fa; border-top: 2px solid #dee2e6;">
                                                <td colspan="5" class="text-end fw-bold" style="font-size: 14px;">TOTAL SUMMARY:</td>
                                                <td class="fw-bold text-primary" style="font-size: 14px;" id="normalTotalAmount">₱0.00</td>
                                                <td class="fw-bold text-success" style="font-size: 14px;" id="normalPaidAmount">₱0.00</td>
                                                <td class="fw-bold text-danger" style="font-size: 14px;" id="normalRemainingAmount">₱0.00</td>
                                                <td colspan="4"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3 px-2 py-2 border-top" id="normal-pagination">
                                    <div class="d-flex align-items-center gap-2 text-muted small">
                                        <span>Show</span>
                                        <select class="form-select form-select-sm entries-per-page-select" style="width: auto; height: 30px; padding: 2px 24px 2px 8px; font-size: 12px;" data-pane="normal-pane">
                                            <option value="5" selected>5</option>
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                            <option value="500">500</option>
                                            <option value="all">All</option>
                                        </select>
                                        <span>entries | Showing <span class="page-start">0</span> to <span class="page-end">0</span> of <span class="total-items">0</span> entries</span>
                                    </div>
                                    <nav>
                                        <ul class="pagination pagination-sm mb-0"></ul>
                                    </nav>
                                </div>
                            </div>

                            <!-- E-com Invoices Tab Pane -->
                            <div class="tab-pane fade" id="ecom-pane" role="tabpanel" aria-labelledby="ecom-tab">
                                <div class="table-responsive">
                                    <table class="table table-responsive-md">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;">
                                                    <input type="checkbox" id="selectAllEcom" style="width: 16px; height: 16px; cursor: pointer;">
                                                </th>
                                                <th>SO Number</th>
                                                <th>Platform</th>
                                                <th>Customer</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>SI Prepared By</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($ecomOrders as $order)
                                            <tr class="si-row" data-date="{{ $order->created_at->format('Y-m-d') }}" data-platform="{{ strtolower($order->ecom_platform) }}" data-amount="{{ $order->total_amount }}" data-type="{{ $order->type }}">
                                                <td>
                                                    <input type="checkbox"
                                                        class="order-checkbox ecom-check ecom-print-check"
                                                        value="{{ $order->id }}"
                                                        data-proof="{{ $order->proof_of_payment ? 'yes' : 'no' }}"
                                                        data-order-id="{{ $order->id }}"
                                                        data-amount="{{ $order->total_amount }}"
                                                        style="width: 16px; height: 16px; cursor: pointer;"
                                                    >
                                                </td>
                                                <td><strong>#{{ $order->so_number }}</strong></td>
                                                <td class="text-capitalize">
                                                    @if($order->ecom_platform === 'lazada')
                                                        <span class="badge bg-primary text-white"><i class="las la-shopping-bag me-1"></i> Lazada</span>
                                                    @elseif($order->ecom_platform === 'shopee')
                                                        <span class="badge bg-warning text-dark"><i class="las la-shopping-basket me-1"></i> Shopee</span>
                                                    @elseif($order->ecom_platform === 'tiktok')
                                                        <span class="badge bg-dark text-white"><i class="las la-music me-1"></i> TikTok</span>
                                                    @else
                                                        <span class="badge bg-secondary text-white">{{ $order->ecom_platform ?? 'E-commerce' }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $order->customer->customer_name ?? 'N/A' }}</td>
                                                @php
                                                    $ecomCurr = $order->currency ?? 'PHP';
                                                    $ecomSym = ($ecomCurr === 'USD' ? '$' : ($ecomCurr === 'EUR' ? '€' : '₱'));
                                                @endphp
                                                <td class="fw-bold">{{ $ecomSym }}{{ number_format($order->total_amount, 2) }}</td>
                                                <td>
                                                    @php
                                                        $statusClass = 'secondary';
                                                        $displayStatus = str_replace('_', ' ', $order->status);
                                                        
                                                        if ($order->status === 'pending_si_prep') {
                                                            $statusClass = 'warning';
                                                            $displayStatus = 'Gathered (Pending SI Prep)';
                                                        } elseif ($order->status === 'picking') {
                                                            $statusClass = 'primary';
                                                            $displayStatus = 'In Pick List (Picking)';
                                                        } elseif ($order->status === 'si_created') {
                                                            $statusClass = 'info';
                                                            $displayStatus = 'SI Created (Pending Signature)';
                                                        } elseif ($order->status === 'pending_si_approval') {
                                                            $statusClass = 'info';
                                                            $displayStatus = 'SI Prepared (Pending Approval)';
                                                        } elseif ($order->status === 'ready_for_delivery') {
                                                            $statusClass = 'success';
                                                        }
                                                    @endphp
                                                    <span class="badge badge-{{ $statusClass }}">
                                                        {{ ucwords($displayStatus) }}
                                                    </span>
                                                </td>
                                                <td>{{ $order->siPreparedBy->name ?? ($order->preparedBy->name ?? 'N/A') }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <a href="{{ route('admin-finance.sales-order.detail', $order->id) }}" class="btn btn-danger shadow btn-sm" title="View SO Detail"><i class="fas fa-eye me-1"></i> View</a>
                                                        <a href="{{ route('admin-finance.accounting.sales-invoice.print', $order->id) }}" class="btn btn-outline-primary btn-sm" target="_blank" title="Print SI"><i class="fas fa-print me-1"></i> Print SI</a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4 text-muted">No E-com direct orders requiring Sales Invoice at this time.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr id="ecomTotalRow" style="background: #f8f9fa; border-top: 2px solid #dee2e6;">
                                                <td colspan="4" class="text-end fw-bold" style="font-size: 14px;">Total Amount:</td>
                                                <td class="fw-bold text-success" style="font-size: 14px;" id="ecomTotalAmount">₱0.00</td>
                                                <td colspan="4"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3 px-2 py-2 border-top" id="ecom-pagination">
                                    <div class="d-flex align-items-center gap-2 text-muted small">
                                        <span>Show</span>
                                        <select class="form-select form-select-sm entries-per-page-select" style="width: auto; height: 30px; padding: 2px 24px 2px 8px; font-size: 12px;" data-pane="ecom-pane">
                                            <option value="5" selected>5</option>
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                            <option value="500">500</option>
                                            <option value="all">All</option>
                                        </select>
                                        <span>entries | Showing <span class="page-start">0</span> to <span class="page-end">0</span> of <span class="total-items">0</span> entries</span>
                                    </div>
                                    <nav>
                                        <ul class="pagination pagination-sm mb-0"></ul>
                                    </nav>
                                </div>
                            </div>

                            <!-- Completed SI Tab Pane -->
                            <div class="tab-pane fade" id="completed-pane" role="tabpanel" aria-labelledby="completed-tab">
                                <div class="table-responsive">
                                    <table class="table table-responsive-md">
                                        <thead>
                                            <tr>
                                                <th>SI Number</th>
                                                <th>SO Number</th>
                                                <th>Customer</th>
                                                <th>Type</th>
                                                <th>Payment Method</th>
                                                <th>Total Amount</th>
                                                <th>Paid Amount</th>
                                                <th>Remaining</th>
                                                <th>Order Status</th>
                                                <th>Payment Status</th>
                                                <th>Created Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($completedSIs as $si)
                                            @php
                                                $so = $si->salesOrder;
                                                $totalAmt = (float)($si->total_amount ?? ($so->total_amount ?? 0));
                                                $paidAmt = $so ? (float)$so->total_paid_amount : 0;
                                                $remBal = $so ? (float)$so->remaining_balance : max(0, $totalAmt - $paidAmt);
                                                $pmStatus = $so ? $so->computed_payment_status : ($remBal <= 0 ? 'paid' : 'unpaid');
                                                $pmBadgeColor = $pmStatus === 'paid' ? 'success' : ($pmStatus === 'partially_paid' ? 'warning' : 'danger');
                                                $pmLabel = $pmStatus === 'partially_paid' ? 'PARTIALLY PAID' : strtoupper($pmStatus);
                                                $siCurr = $so->currency ?? 'PHP';
                                                $siSym = ($siCurr === 'USD' ? '$' : ($siCurr === 'EUR' ? '€' : '₱'));
                                            @endphp
                                            <tr class="si-row" data-date="{{ $si->created_at->format('Y-m-d') }}" data-type="{{ $si->salesOrder->type ?? str_replace('_si', '', $si->transaction_type ?? 'area_consignment') }}" data-amount="{{ $totalAmt }}" data-paid="{{ $paidAmt }}" data-remaining="{{ $remBal }}">
                                                <td><strong>#{{ $si->si_number }}</strong></td>
                                                <td>#{{ $si->so_number }}</td>
                                                <td>{{ $si->customer_name ?? ($si->customer->customer_name ?? 'N/A') }}</td>
                                                <td><span class="badge badge-outline-dark">{{ ucfirst(str_replace('_', ' ', $si->transaction_type ?? 'area_consignment_si')) }}</span></td>
                                                <td>
                                                    @php $currentPm = strtolower($si->salesOrder->payment_method ?? 'cash'); @endphp
                                                    <select class="form-select form-select-sm pm-select text-black fw-bold"
                                                            data-order-id="{{ $si->so_id }}"
                                                            style="height: 32px; font-size: 12px; border: 1.5px solid #0d6efd; background-color: #f0f7ff; cursor: pointer; min-width: 130px;">
                                                        <option value="cash" {{ $currentPm === 'cash' ? 'selected' : '' }}>💵 Cash</option>
                                                        <option value="gcash" {{ $currentPm === 'gcash' ? 'selected' : '' }}>📱 GCash</option>
                                                        <option value="maya" {{ $currentPm === 'maya' ? 'selected' : '' }}>📱 Maya</option>
                                                        <option value="bank_transfer" {{ $currentPm === 'bank_transfer' ? 'selected' : '' }}>🏦 Bank Transfer</option>
                                                        <option value="check" {{ $currentPm === 'check' ? 'selected' : '' }}>🧾 Check</option>
                                                        <option value="card" {{ $currentPm === 'card' ? 'selected' : '' }}>💳 Card</option>
                                                    </select>
                                                </td>
                                                <td class="fw-bold">{{ $siSym }}{{ number_format($totalAmt, 2) }}</td>
                                                <td class="text-success fw-bold">{{ $siSym }}{{ number_format($paidAmt, 2) }}</td>
                                                <td class="text-danger fw-bold">{{ $siSym }}{{ number_format($remBal, 2) }}</td>
                                                <td><span class="badge bg-success text-white">Completed / Approved</span></td>
                                                <td><span class="badge badge-{{ $pmBadgeColor }}">{{ $pmLabel }}</span></td>
                                                <td>{{ $si->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                                        @if($remBal > 0 && $so && $so->customer_id)
                                                            <button type="button" class="btn btn-success btn-sm open-pay-modal-btn shadow-sm" data-so-id="{{ $so->id }}" data-customer-id="{{ $so->customer_id }}" data-so-number="{{ $so->so_number }}" data-total="{{ $totalAmt }}" data-paid="{{ $paidAmt }}" data-remaining="{{ $remBal }}" data-terms="{{ $so->terms ?? 'COD' }}" data-due-date="{{ $so->due_date ? $so->due_date->format('M d, Y') : 'N/A' }}" data-currency="{{ $so->currency ?? 'USD' }}" data-symbol="{{ $siSym }}">
                                                                <i class="las la-coins me-1"></i> Pay
                                                            </button>
                                                        @endif
                                                        <a href="{{ route('admin-finance.accounting.sales-invoice.print', $si->so_id) }}" class="btn btn-info btn-sm" target="_blank">
                                                            <i class="fas fa-print me-1"></i> Print SI
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="12" class="text-center py-4 text-muted">No completed Sales Invoices found.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr id="completedTotalRow" style="background: #f8f9fa; border-top: 2px solid #dee2e6;">
                                                <td colspan="5" class="text-end fw-bold" style="font-size: 14px;">TOTAL SUMMARY:</td>
                                                <td class="fw-bold text-primary" style="font-size: 14px;" id="completedTotalAmount">₱0.00</td>
                                                <td class="fw-bold text-success" style="font-size: 14px;" id="completedPaidAmount">₱0.00</td>
                                                <td class="fw-bold text-danger" style="font-size: 14px;" id="completedRemainingAmount">₱0.00</td>
                                                <td colspan="4"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3 px-2 py-2 border-top" id="completed-pagination">
                                    <div class="d-flex align-items-center gap-2 text-muted small">
                                        <span>Show</span>
                                        <select class="form-select form-select-sm entries-per-page-select" style="width: auto; height: 30px; padding: 2px 24px 2px 8px; font-size: 12px;" data-pane="completed-pane">
                                            <option value="5" selected>5</option>
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                            <option value="500">500</option>
                                            <option value="all">All</option>
                                        </select>
                                        <span>entries | Showing <span class="page-start">0</span> to <span class="page-end">0</span> of <span class="total-items">0</span> entries</span>
                                    </div>
                                    <nav>
                                        <ul class="pagination pagination-sm mb-0"></ul>
                                    </nav>
                                </div>
                            </div>

                            <!-- Completed E-com Invoices Tab Pane -->
                            <div class="tab-pane fade" id="completed-ecom-pane" role="tabpanel" aria-labelledby="completed-ecom-tab">
                                <div class="table-responsive">
                                    <table class="table table-responsive-md">
                                        <thead>
                                            <tr>
                                                <th>SI Number</th>
                                                <th>SO Number</th>
                                                <th>Platform</th>
                                                <th>Customer</th>
                                                <th>Total Amount</th>
                                                <th>Payment Status</th>
                                                <th>Created Date</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($completedEcomSIs as $si)
                                            @php
                                                $so = $si->salesOrder;
                                                $totalAmt = (float)($si->total_amount ?? ($so->total_amount ?? 0));
                                                $paidAmt = $so ? (float)$so->total_paid_amount : 0;
                                                $remBal = $so ? (float)$so->remaining_balance : max(0, $totalAmt - $paidAmt);
                                                $pmStatus = $so ? $so->computed_payment_status : ($remBal <= 0 ? 'paid' : 'unpaid');
                                                $pmBadgeColor = $pmStatus === 'paid' ? 'success' : ($pmStatus === 'partially_paid' ? 'warning' : 'danger');
                                                $pmLabel = $pmStatus === 'partially_paid' ? 'PARTIALLY PAID' : strtoupper($pmStatus);
                                                $platform = $so->ecom_platform ?? 'ecom';
                                                $siCurr = $so->currency ?? 'PHP';
                                                $siSym = ($siCurr === 'USD' ? '$' : ($siCurr === 'EUR' ? '€' : '₱'));
                                            @endphp
                                            <tr class="si-row" data-date="{{ $si->created_at->format('Y-m-d') }}" data-platform="{{ strtolower($platform) }}" data-amount="{{ $totalAmt }}" data-type="ecom_direct">
                                                <td><strong>#{{ $si->si_number }}</strong></td>
                                                <td>#{{ $si->so_number }}</td>
                                                <td class="text-capitalize">
                                                    @if(strtolower($platform) === 'lazada')
                                                        <span class="badge bg-primary text-white"><i class="las la-shopping-bag me-1"></i> Lazada</span>
                                                    @elseif(strtolower($platform) === 'shopee')
                                                        <span class="badge bg-warning text-dark"><i class="las la-shopping-basket me-1"></i> Shopee</span>
                                                    @elseif(strtolower($platform) === 'tiktok')
                                                        <span class="badge bg-dark text-white"><i class="las la-music me-1"></i> TikTok</span>
                                                    @else
                                                        <span class="badge bg-secondary text-white">{{ $platform ?: 'E-commerce' }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $si->customer_name ?? ($si->customer->customer_name ?? 'N/A') }}</td>
                                                <td class="fw-bold">{{ $siSym }}{{ number_format($totalAmt, 2) }}</td>
                                                <td><span class="badge badge-{{ $pmBadgeColor }}">{{ $pmLabel }}</span></td>
                                                <td>{{ $si->created_at->format('M d, Y') }}</td>
                                                <td class="text-end">
                                                    <div class="d-flex justify-content-end gap-2">
                                                        @if($so)
                                                            <a href="{{ route('admin-finance.sales-order.detail', $so->id) }}" class="btn btn-danger shadow btn-sm" title="View SO Detail"><i class="fas fa-eye me-1"></i> View</a>
                                                            <a href="{{ route('admin-finance.accounting.sales-invoice.print', $so->id) }}" class="btn btn-outline-primary btn-sm" target="_blank" title="Print SI"><i class="fas fa-print me-1"></i> Print SI</a>
                                                        @else
                                                            <span class="text-muted small">N/A</span>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4 text-muted">No completed E-commerce direct invoices at this time.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr id="completedEcomTotalRow" style="background: #f8f9fa; border-top: 2px solid #dee2e6;">
                                                <td colspan="4" class="text-end fw-bold" style="font-size: 14px;">TOTAL SUMMARY:</td>
                                                <td class="fw-bold text-success" style="font-size: 14px;" id="completedEcomTotalAmount">₱0.00</td>
                                                <td colspan="3"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3 px-2 py-2 border-top" id="completed-ecom-pagination">
                                    <div class="d-flex align-items-center gap-2 text-muted small">
                                        <span>Show</span>
                                        <select class="form-select form-select-sm entries-per-page-select" style="width: auto; height: 30px; padding: 2px 24px 2px 8px; font-size: 12px;" data-pane="completed-ecom-pane">
                                            <option value="5" selected>5</option>
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                            <option value="500">500</option>
                                            <option value="all">All</option>
                                        </select>
                                        <span>entries | Showing <span class="page-start">0</span> to <span class="page-end">0</span> of <span class="total-items">0</span> entries</span>
                                    </div>
                                    <nav>
                                        <ul class="pagination pagination-sm mb-0"></ul>
                                    </nav>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Styling JS script -->
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const tabElList = [].slice.call(document.querySelectorAll('button[data-bs-toggle="tab"]'))
                                tabElList.forEach(function(tabEl) {
                                    tabEl.addEventListener('shown.bs.tab', function(event) {
                                        // Reset classes
                                        tabElList.forEach(el => {
                                            el.classList.remove('text-dark', 'active');
                                            el.classList.add('text-muted');
                                            el.style.borderBottom = '3px solid transparent';
                                        });
                                        // Set active classes
                                        event.target.classList.add('text-dark', 'active');
                                        event.target.classList.remove('text-muted');
                                        if (event.target.id === 'normal-tab') {
                                            event.target.style.borderBottom = '3px solid #ff0000';
                                        } else if (event.target.id === 'ecom-tab') {
                                            event.target.style.borderBottom = '3px solid #0d6efd';
                                        } else if (event.target.id === 'completed-tab') {
                                            event.target.style.borderBottom = '3px solid #198754';
                                        } else if (event.target.id === 'completed-ecom-tab') {
                                            event.target.style.borderBottom = '3px solid #0dcaf0';
                                        }
                                    });
                                });
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('siSearchInput');
        const typeSelect = document.getElementById('siTypeSelect');
        const pmSelect = document.getElementById('siPaymentMethodSelect');
        const platformSelect = document.getElementById('siPlatformSelect');
        const startDateInput = document.getElementById('siStartDate');
        const endDateInput = document.getElementById('siEndDate');
        const entriesSelect = document.getElementById('siEntriesSelect');
        const clearBtn = document.getElementById('clearFiltersBtn');

        const pageState = {
            'normal-pane': 1,
            'ecom-pane': 1,
            'completed-pane': 1,
            'completed-ecom-pane': 1
        };
        let currentPageSize = 5;

        function getPageSize() {
            const val = entriesSelect ? entriesSelect.value : (currentPageSize || 5);
            return val === 'all' ? 999999 : (parseInt(val) || 5);
        }

        function syncEntriesDropdowns(val) {
            if (entriesSelect) entriesSelect.value = val;
            document.querySelectorAll('.entries-per-page-select').forEach(sel => {
                sel.value = val;
            });
        }

        function resetPageStates() {
            pageState['normal-pane'] = 1;
            pageState['ecom-pane'] = 1;
            pageState['completed-pane'] = 1;
            pageState['completed-ecom-pane'] = 1;
        }

        function filterAndPaginate() {
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            const selectedType = typeSelect ? typeSelect.value : '';
            const platform = platformSelect ? platformSelect.value : '';
            const selectedPm = pmSelect ? pmSelect.value.toLowerCase() : '';
            const pageSize = getPageSize();

            ['normal-pane', 'ecom-pane', 'completed-pane', 'completed-ecom-pane'].forEach(paneId => {
                const pane = document.getElementById(paneId);
                if (!pane) return;
                const rows = Array.from(pane.querySelectorAll('.si-row'));

                // 1. Determine matches
                const matchingRows = [];
                rows.forEach(row => {
                    let matchesSearch = true;
                    let matchesDate = true;
                    let matchesPlatform = true;
                    let matchesType = true;
                    let matchesPm = true;

                    if (query) {
                        const text = row.innerText.toLowerCase();
                        matchesSearch = text.includes(query);
                    }

                    if (selectedType) {
                        const rowType = row.getAttribute('data-type');
                        if (rowType && rowType !== selectedType) matchesType = false;
                    }

                    if (selectedPm) {
                        const rowPmSelect = row.querySelector('.pm-select');
                        const rowPm = rowPmSelect ? rowPmSelect.value.toLowerCase() : (row.getAttribute('data-pm') || '');
                        if (rowPm !== selectedPm) matchesPm = false;
                    }

                    const rowDateStr = row.getAttribute('data-date');
                    if (rowDateStr) {
                        if (startDateInput && startDateInput.value && rowDateStr < startDateInput.value) matchesDate = false;
                        if (endDateInput && endDateInput.value && rowDateStr > endDateInput.value) matchesDate = false;
                    }

                    if (platform) {
                        const rowPlatform = row.getAttribute('data-platform');
                        if (rowPlatform && rowPlatform !== platform) matchesPlatform = false;
                    }

                    if (matchesSearch && matchesType && matchesDate && matchesPlatform && matchesPm) {
                        matchingRows.push(row);
                    } else {
                        row.style.display = 'none';
                    }
                });

                // 2. Handle empty state
                const tbody = pane.querySelector('tbody');
                let noResultRow = tbody.querySelector('.no-results-row');
                if (matchingRows.length === 0 && rows.length > 0) {
                    if (!noResultRow) {
                        noResultRow = document.createElement('tr');
                        noResultRow.className = 'no-results-row';
                        const colCount = pane.querySelectorAll('thead th').length;
                        noResultRow.innerHTML = `<td colspan="${colCount}" class="text-center py-4 text-muted">No matching results found.</td>`;
                        tbody.appendChild(noResultRow);
                    }
                } else if (noResultRow) {
                    noResultRow.remove();
                }

                // 3. Update totals for matching rows
                let totalAmount = 0;
                let paidAmount = 0;
                let remainingAmount = 0;

                matchingRows.forEach(row => {
                    const amt = parseFloat(row.getAttribute('data-amount')) || 0;
                    const paid = parseFloat(row.getAttribute('data-paid')) || 0;
                    const rem = parseFloat(row.getAttribute('data-remaining')) || 0;
                    totalAmount += amt;
                    paidAmount += paid;
                    remainingAmount += rem;
                });

                const prefix = paneId.replace('-pane', '');
                const camelPrefix = paneId.replace(/-([a-z])/g, (g) => g[1].toUpperCase()).replace('Pane', '');
                const totEl = document.getElementById(camelPrefix + 'TotalAmount') || document.getElementById(prefix + 'TotalAmount');
                const paidEl = document.getElementById(camelPrefix + 'PaidAmount') || document.getElementById(prefix + 'PaidAmount');
                const remEl = document.getElementById(camelPrefix + 'RemainingAmount') || document.getElementById(prefix + 'RemainingAmount');

                if (totEl) totEl.textContent = '₱' + totalAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                if (paidEl) paidEl.textContent = '₱' + paidAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                if (remEl) remEl.textContent = '₱' + remainingAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                // 4. Paginate matching rows
                const totalMatching = matchingRows.length;
                const totalPages = Math.ceil(totalMatching / pageSize) || 1;
                if (pageState[paneId] > totalPages) pageState[paneId] = totalPages;
                if (pageState[paneId] < 1) pageState[paneId] = 1;

                const currPage = pageState[paneId];
                const startIndex = (currPage - 1) * pageSize;
                const endIndex = Math.min(startIndex + pageSize, totalMatching);

                matchingRows.forEach((row, index) => {
                    if (index >= startIndex && index < endIndex) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                // 5. Update pagination UI controls
                const pagWrapper = document.getElementById(prefix + '-pagination');
                if (pagWrapper) {
                    if (totalMatching === 0) {
                        pagWrapper.style.display = 'none';
                    } else {
                        pagWrapper.style.display = 'flex';
                        pagWrapper.querySelector('.page-start').textContent = totalMatching === 0 ? 0 : startIndex + 1;
                        pagWrapper.querySelector('.page-end').textContent = endIndex;
                        pagWrapper.querySelector('.total-items').textContent = totalMatching;

                        const ul = pagWrapper.querySelector('.pagination');
                        ul.innerHTML = '';

                        // Prev button
                        const prevLi = document.createElement('li');
                        prevLi.className = `page-item ${currPage === 1 ? 'disabled' : ''}`;
                        prevLi.innerHTML = `<a class="page-link" href="javascript:void(0)"><i class="fas fa-chevron-left"></i></a>`;
                        prevLi.addEventListener('click', () => {
                            if (currPage > 1) {
                                pageState[paneId]--;
                                filterAndPaginate();
                            }
                        });
                        ul.appendChild(prevLi);

                        // Page numbers
                        for (let i = 1; i <= totalPages; i++) {
                            if (totalPages <= 7 || i === 1 || i === totalPages || (i >= currPage - 1 && i <= currPage + 1)) {
                                const pageLi = document.createElement('li');
                                pageLi.className = `page-item ${i === currPage ? 'active' : ''}`;
                                pageLi.innerHTML = `<a class="page-link" href="javascript:void(0)">${i}</a>`;
                                pageLi.addEventListener('click', () => {
                                    pageState[paneId] = i;
                                    filterAndPaginate();
                                });
                                ul.appendChild(pageLi);
                            } else if (i === currPage - 2 || i === currPage + 2) {
                                const dotsLi = document.createElement('li');
                                dotsLi.className = 'page-item disabled';
                                dotsLi.innerHTML = `<span class="page-link">...</span>`;
                                ul.appendChild(dotsLi);
                            }
                        }

                        // Next button
                        const nextLi = document.createElement('li');
                        nextLi.className = `page-item ${currPage === totalPages ? 'disabled' : ''}`;
                        nextLi.innerHTML = `<a class="page-link" href="javascript:void(0)"><i class="fas fa-chevron-right"></i></a>`;
                        nextLi.addEventListener('click', () => {
                            if (currPage < totalPages) {
                                pageState[paneId]++;
                                filterAndPaginate();
                            }
                        });
                        ul.appendChild(nextLi);
                    }
                }
            });
        }

        // Calculate on page load
        filterAndPaginate();

        if (searchInput) searchInput.addEventListener('input', () => { resetPageStates(); filterAndPaginate(); });
        if (typeSelect) typeSelect.addEventListener('change', () => { resetPageStates(); filterAndPaginate(); });
        if (pmSelect) pmSelect.addEventListener('change', () => { resetPageStates(); filterAndPaginate(); });
        if (platformSelect) platformSelect.addEventListener('change', () => { resetPageStates(); filterAndPaginate(); });
        if (startDateInput) startDateInput.addEventListener('change', () => { resetPageStates(); filterAndPaginate(); });
        if (endDateInput) endDateInput.addEventListener('change', () => { resetPageStates(); filterAndPaginate(); });

        if (entriesSelect) {
            entriesSelect.addEventListener('change', function() {
                currentPageSize = this.value;
                syncEntriesDropdowns(this.value);
                resetPageStates();
                filterAndPaginate();
            });
        }

        document.querySelectorAll('.entries-per-page-select').forEach(sel => {
            sel.addEventListener('change', function() {
                currentPageSize = this.value;
                syncEntriesDropdowns(this.value);
                resetPageStates();
                filterAndPaginate();
            });
        });

        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                if (searchInput) searchInput.value = '';
                if (typeSelect) typeSelect.value = '';
                if (pmSelect) pmSelect.value = '';
                if (platformSelect) platformSelect.value = '';
                if (startDateInput) startDateInput.value = '';
                if (endDateInput) endDateInput.value = '';
                if (entriesSelect) entriesSelect.value = '5';
                currentPageSize = 5;
                syncEntriesDropdowns('5');
                resetPageStates();
                filterAndPaginate();
            });
        }

        // Payment Method interactive AJAX update
        document.querySelectorAll('.pm-select').forEach(select => {
            select.addEventListener('change', function () {
                const orderId = this.getAttribute('data-order-id');
                const paymentMethod = this.value;
                const origBg = this.style.backgroundColor;

                this.style.backgroundColor = '#fff3cd';

                fetch(`/admin-finance/sales-order/${orderId}/update-payment-method`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ payment_method: paymentMethod })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => { throw new Error(text || response.statusText); });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        this.style.backgroundColor = '#d1e7dd';
                        setTimeout(() => { this.style.backgroundColor = origBg; }, 1200);
                    } else {
                        alert('Failed to update payment method: ' + (data.message || 'Unknown error'));
                        this.style.backgroundColor = '#f8d7da';
                    }
                })
                .catch(err => {
                    console.error('Payment method error:', err);
                    this.style.backgroundColor = '#d1e7dd';
                    setTimeout(() => { this.style.backgroundColor = origBg; }, 1200);
                });
            });
        });

        // Tab switch visibility for platform filter
        const normalTab = document.getElementById('normal-tab');
        const ecomTab = document.getElementById('ecom-tab');
        const platformFilterContainer = document.getElementById('platformFilterContainer');

        if (normalTab && ecomTab && platformFilterContainer) {
            normalTab.addEventListener('shown.bs.tab', function () {
                platformFilterContainer.style.display = 'none';
                platformSelect.value = '';
                filterRows();
            });

            ecomTab.addEventListener('shown.bs.tab', function () {
                platformFilterContainer.style.display = 'block';
            });
        }

        // Checkbox variables & events
        const selectAllNormal = document.getElementById('selectAllNormal');
        const selectAllEcom = document.getElementById('selectAllEcom');
        const normalChecks = document.querySelectorAll('.normal-check');
        const ecomChecks = document.querySelectorAll('.ecom-check');
        const bulkActionsBar = document.getElementById('bulkActionsBar');
        const selectedCountEl = document.getElementById('selectedCount');
        const bulkFinalizeBtn = document.getElementById('bulkFinalizeBtn');

        function updateBulkBar() {
            const checkedCount = document.querySelectorAll('.order-checkbox:checked').length;
            selectedCountEl.textContent = checkedCount;
            if (checkedCount > 0) {
                bulkActionsBar.classList.remove('d-none');
                bulkActionsBar.classList.add('d-flex');
            } else {
                bulkActionsBar.classList.remove('d-flex');
                bulkActionsBar.classList.add('d-none');
            }

            // Calculate total amount of selected orders
            const totalAmountContainer = document.getElementById('selectedTotalAmount');
            const totalAmountValue = document.getElementById('totalAmountValue');
            if (totalAmountContainer && totalAmountValue) {
                let total = 0;
                document.querySelectorAll('.order-checkbox:checked').forEach(cb => {
                    const amt = parseFloat(cb.getAttribute('data-amount'));
                    if (!isNaN(amt)) total += amt;
                });
                if (checkedCount > 0 && total >= 0) {
                    totalAmountValue.textContent = '₱' + total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    totalAmountContainer.classList.remove('d-none');
                } else {
                    totalAmountContainer.classList.add('d-none');
                }
            }

            // Show print button only when e-com tab is active and items are checked
            const bulkPrintSIBtn = document.getElementById('bulkPrintSIBtn');
            if (bulkPrintSIBtn) {
                const ecomPaneActive = document.getElementById('ecom-pane') && document.getElementById('ecom-pane').classList.contains('show');
                const ecomCheckedPrintable = document.querySelectorAll('.ecom-print-check:checked').length;
                if (ecomPaneActive && ecomCheckedPrintable > 0) {
                    bulkPrintSIBtn.classList.remove('d-none');
                } else {
                    bulkPrintSIBtn.classList.add('d-none');
                }
            }
        }

        if (selectAllNormal) {
            selectAllNormal.addEventListener('change', function() {
                normalChecks.forEach(cb => {
                    if (!cb.disabled && cb.closest('tr').style.display !== 'none') {
                        cb.checked = selectAllNormal.checked;
                    }
                });
                updateBulkBar();
            });
        }

        if (selectAllEcom) {
            selectAllEcom.addEventListener('change', function() {
                ecomChecks.forEach(cb => {
                    if (cb.closest('tr').style.display !== 'none') {
                        cb.checked = selectAllEcom.checked;
                    }
                });
                updateBulkBar();
            });
        }

        document.querySelectorAll('.order-checkbox').forEach(cb => {
            cb.addEventListener('change', updateBulkBar);
        });

        // Print Selected SIs
        const bulkPrintSIBtn = document.getElementById('bulkPrintSIBtn');
        if (bulkPrintSIBtn) {
            bulkPrintSIBtn.addEventListener('click', function () {
                const selected = document.querySelectorAll('.ecom-print-check:checked');
                if (selected.length === 0) {
                    alert('Please select at least one e-com order to print.');
                    return;
                }
                const ids = Array.from(selected).map(cb => cb.getAttribute('data-order-id')).filter(id => id);
                if (ids.length > 0) {
                    const url = '{{ route("admin-finance.accounting.sales-invoice.bulk-print") }}?ids=' + ids.join(',');
                    window.open(url, '_blank');
                }
            });
        }

        const bulkPrepareBtn = document.getElementById('bulkPrepareBtn');

        function executeBulkProcess(actionType, buttonEl, btnOriginalHtml) {
            const selectedCheckboxes = document.querySelectorAll('.order-checkbox:checked');
            const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

            if (selectedIds.length === 0) {
                alert('Please select at least one sales order.');
                return;
            }

            // Double check if any selected orders are missing Proof of Payment
            let missingProofCount = 0;
            selectedCheckboxes.forEach(cb => {
                if (cb.getAttribute('data-proof') !== 'yes') {
                    missingProofCount++;
                }
            });

            const actionLabel = actionType === 'prepare' ? 'prepare & submit' : 'sign & approve';

            if (missingProofCount > 0) {
                if (!confirm(`Warning: ${missingProofCount} of the selected orders do NOT have a Proof of Payment attached. They will be skipped. Do you still want to proceed to ${actionLabel} the remaining ${selectedIds.length - missingProofCount} order(s)?`)) {
                    return;
                }
            } else if (!confirm(`Are you sure you want to ${actionLabel} the ${selectedIds.length} selected Sales Order(s)?`)) {
                return;
            }

            if (buttonEl) {
                buttonEl.disabled = true;
                buttonEl.innerHTML = '<i class="las la-spinner la-spin me-1"></i> Processing...';
            }

            fetch('{{ route("admin-finance.accounting.sales-invoice.bulk-finalize") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ids: selectedIds, action: actionType })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                    if (buttonEl) {
                        buttonEl.disabled = false;
                        buttonEl.innerHTML = btnOriginalHtml;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during bulk processing.');
                if (buttonEl) {
                    buttonEl.disabled = false;
                    buttonEl.innerHTML = btnOriginalHtml;
                }
            });
        }

        if (bulkPrepareBtn) {
            bulkPrepareBtn.addEventListener('click', function() {
                executeBulkProcess('prepare', bulkPrepareBtn, '<i class="las la-file-invoice me-1"></i> Bulk Prepare & Submit SI');
            });
        }

        if (bulkFinalizeBtn) {
            bulkFinalizeBtn.addEventListener('click', function() {
                executeBulkProcess('sign', bulkFinalizeBtn, '<i class="las la-check-double me-1"></i> Bulk Sign & Approve');
            });
        }
    });
    </script>

    <!-- Record Payment Modal -->
    <div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white"><i class="las la-money-bill-wave me-2"></i>Payment History & Record Installment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="recordPaymentForm">
                    <div class="modal-body">
                        <input type="hidden" id="paySoId">
                        <input type="hidden" id="payCustomerId">
                        
                        <div class="alert alert-light border mb-3">
                            <div class="row g-2 text-center text-md-start">
                                <div class="col-6 col-md-2 border-end">
                                    <span class="text-muted small d-block">Transaction #:</span>
                                    <strong id="paySoNumber" class="text-dark">SO-0000</strong>
                                </div>
                                <div class="col-6 col-md-2 border-end">
                                    <span class="text-muted small d-block">Terms:</span>
                                    <span id="payTerms" class="badge bg-info text-white fw-semibold">COD</span>
                                </div>
                                <div class="col-6 col-md-2 border-end">
                                    <span class="text-muted small d-block">Due Date:</span>
                                    <strong id="payDueDate" class="text-dark">N/A</strong>
                                </div>
                                <div class="col-6 col-md-2 border-end">
                                    <span class="text-muted small d-block">Grand Total:</span>
                                    <strong id="payTotalAmount" class="text-dark">₱0.00</strong>
                                </div>
                                <div class="col-6 col-md-2 border-end">
                                    <span class="text-muted small d-block">Already Paid:</span>
                                    <span id="payAlreadyPaid" class="text-success fw-bold">₱0.00</span>
                                </div>
                                <div class="col-6 col-md-2">
                                    <span class="text-muted small d-block">Remaining:</span>
                                    <strong id="payRemainingBalance" class="text-danger fs-16">₱0.00</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Payment History Breakdown Table -->
                        <div class="card mb-3 border">
                            <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                                <span class="fw-bold small text-dark"><i class="las la-history me-1 text-primary"></i> Previous Installments Log</span>
                                <span class="badge bg-secondary" id="payHistoryBadge">0 payments</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 180px; overflow-y: auto;">
                                    <table class="table table-sm table-striped table-bordered mb-0 align-middle" style="font-size: 11px;">
                                        <thead class="bg-light sticky-top">
                                            <tr>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Method</th>
                                                <th>Ref # / Check #</th>
                                                <th>Notes</th>
                                                <th>Proof</th>
                                                <th>Recorded By</th>
                                            </tr>
                                        </thead>
                                        <tbody id="payHistoryTableBody">
                                            <tr><td colspan="7" class="text-center py-2 text-muted"><i class="fas fa-spinner fa-spin me-1"></i> Loading payment history...</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- New Installment Entry Form -->
                        <div id="newPaymentFormFields">
                            <h6 class="fw-bold text-dark border-bottom pb-1 mb-3"><i class="las la-plus-circle me-1 text-success"></i> Add New Installment Payment</h6>

                            <div class="row g-2">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label fw-bold small text-dark">Payment Amount (<span class="pay-curr-symbol">₱</span>) <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text pay-curr-symbol">₱</span>
                                        <input type="number" step="0.01" min="0.01" id="payAmountInput" class="form-control fw-bold fs-15 text-primary" required placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label fw-bold small text-dark">Payment Method <span class="text-danger">*</span></label>
                                    <select id="payMethodSelect" class="form-select form-select-sm" required>
                                        <option value="cash">Cash</option>
                                        <option value="gcash">GCash</option>
                                        <option value="maya">Maya</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="check">Check</option>
                                        <option value="card">Credit / Debit Card</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label fw-bold small text-dark">Reference / Check # <span class="text-muted fw-normal">(Optional)</span></label>
                                    <input type="text" id="payRefInput" class="form-control form-control-sm" placeholder="e.g. Ref #123456 or Check #">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label fw-bold small text-dark">Notes / Remarks <span class="text-muted fw-normal">(Optional)</span></label>
                                    <input type="text" id="payNotesInput" class="form-control form-control-sm" placeholder="e.g. 1st installment payment">
                                </div>
                                <div class="col-md-12 mb-2">
                                    <label class="form-label fw-bold small text-dark">Proof of Payment <span class="text-muted fw-normal">(Optional - Image/PDF)</span></label>
                                    <input type="file" id="payProofInput" class="form-control form-control-sm" accept="image/*,.pdf">
                                </div>
                            </div>
                        </div>

                        <div id="fullyPaidNotice" class="alert alert-success d-none text-center py-2 mb-0">
                            <i class="las la-check-circle me-1 fs-16"></i> This order is fully paid. No further payments required.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success btn-sm px-4 fw-bold" id="submitPaymentBtn">
                            <i class="las la-check-circle me-1"></i> Submit Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        async function fetchPaymentHistory(customerId, soId) {
            const tableBody = document.getElementById('payHistoryTableBody');
            const badge = document.getElementById('payHistoryBadge');

            if (!tableBody) return;

            tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-2 text-muted"><i class="fas fa-spinner fa-spin me-1"></i> Loading history...</td></tr>';
            if (badge) badge.textContent = 'Loading...';

            try {
                const response = await fetch(`/marketing/customers/${customerId}/transactions/${soId}/payments`);
                const data = await response.json();

                if (!data.payments || data.payments.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-2 text-muted">No previous installments recorded.</td></tr>';
                    if (badge) badge.textContent = '0 payments';
                } else {
                    if (badge) badge.textContent = data.payments.length + ' payment(s)';
                    let rows = '';
                    data.payments.forEach(p => {
                        const proofTag = p.has_proof ? `<a href="${p.proof_url}" target="_blank" class="badge badge-xs bg-light text-primary border"><i class="las la-paperclip me-1"></i>View Proof</a>` : '<span class="text-muted small">None</span>';
                        rows += `<tr>
                            <td class="fw-bold">${p.date}</td>
                            <td class="text-success fw-bold">₱${p.amount.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                            <td><span class="badge bg-light text-dark border">${p.method}</span></td>
                            <td>${p.reference_number}</td>
                            <td>${p.notes}</td>
                            <td>${proofTag}</td>
                            <td><small class="text-muted">${p.recorded_by}</small></td>
                        </tr>`;
                    });
                    tableBody.innerHTML = rows;
                }
            } catch (error) {
                console.error('Error loading payment history:', error);
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-2 text-danger">Failed to load payment history.</td></tr>';
                if (badge) badge.textContent = 'Error';
            }
        }

        // Handle Open Pay Modal Button
        document.body.addEventListener('click', function(e) {
            const payBtn = e.target.closest('.open-pay-modal-btn');
            if (payBtn) {
                const soId = payBtn.dataset.soId;
                const customerId = payBtn.dataset.customerId;
                const soNumber = payBtn.dataset.soNumber;
                const totalAmount = parseFloat(payBtn.dataset.total) || 0;
                const paidAmount = parseFloat(payBtn.dataset.paid) || 0;
                const remainingBalance = parseFloat(payBtn.dataset.remaining) || 0;

                const terms = payBtn.dataset.terms || 'COD';
                const dueDate = payBtn.dataset.dueDate || 'N/A';

                const currSymbol = payBtn.dataset.symbol || (payBtn.dataset.currency === 'USD' ? '$' : (payBtn.dataset.currency === 'EUR' ? '€' : '₱'));

                document.getElementById('paySoId').value = soId;
                document.getElementById('payCustomerId').value = customerId;
                document.getElementById('paySoNumber').textContent = soNumber;
                document.getElementById('payTerms').textContent = terms;
                document.getElementById('payDueDate').textContent = dueDate;
                document.getElementById('payTotalAmount').textContent = currSymbol + totalAmount.toLocaleString(undefined, {minimumFractionDigits: 2});
                document.getElementById('payAlreadyPaid').textContent = currSymbol + paidAmount.toLocaleString(undefined, {minimumFractionDigits: 2});
                document.getElementById('payRemainingBalance').textContent = currSymbol + remainingBalance.toLocaleString(undefined, {minimumFractionDigits: 2});
                
                document.querySelectorAll('.pay-curr-symbol').forEach(el => {
                    el.textContent = currSymbol;
                });
                
                const formFields = document.getElementById('newPaymentFormFields');
                const submitBtn = document.getElementById('submitPaymentBtn');
                const notice = document.getElementById('fullyPaidNotice');

                if (remainingBalance <= 0) {
                    if (formFields) formFields.classList.add('d-none');
                    if (submitBtn) submitBtn.classList.add('d-none');
                    if (notice) notice.classList.remove('d-none');
                } else {
                    if (formFields) formFields.classList.remove('d-none');
                    if (submitBtn) submitBtn.classList.remove('d-none');
                    if (notice) notice.classList.add('d-none');

                    const payAmountInput = document.getElementById('payAmountInput');
                    payAmountInput.value = remainingBalance.toFixed(2);
                    payAmountInput.max = remainingBalance;
                    document.getElementById('payRefInput').value = '';
                    document.getElementById('payNotesInput').value = '';
                    const proofInput = document.getElementById('payProofInput');
                    if (proofInput) proofInput.value = '';
                }

                // Fetch payment history breakdown
                fetchPaymentHistory(customerId, soId);

                const payModalElement = document.getElementById('recordPaymentModal');
                const payModal = bootstrap.Modal.getInstance(payModalElement) || new bootstrap.Modal(payModalElement);
                payModal.show();
            }
        });

        // Handle Submit Payment Form
        document.getElementById('recordPaymentForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const soId = document.getElementById('paySoId').value;
            const customerId = document.getElementById('payCustomerId').value;
            const amount = parseFloat(document.getElementById('payAmountInput').value);
            const paymentMethod = document.getElementById('payMethodSelect').value;
            const referenceNumber = document.getElementById('payRefInput').value;
            const notes = document.getElementById('payNotesInput').value;
            const proofInput = document.getElementById('payProofInput');

            if (!soId || !customerId) return;

            const submitBtn = document.getElementById('submitPaymentBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Submitting...';

            const formData = new FormData();
            formData.append('amount', amount);
            formData.append('payment_method', paymentMethod);
            if (referenceNumber) formData.append('reference_number', referenceNumber);
            if (notes) formData.append('notes', notes);
            if (proofInput && proofInput.files[0]) {
                formData.append('proof_of_payment', proofInput.files[0]);
            }

            try {
                const response = await fetch(`/marketing/customers/${customerId}/transactions/${soId}/pay`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    alert('Payment recorded successfully!');
                    window.location.reload();
                } else {
                    alert(data.message || 'Error recording payment.');
                }
            } catch (error) {
                console.error('Error submitting payment:', error);
                alert('An error occurred while submitting payment.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="las la-check-circle me-1"></i> Submit Payment';
            }
        });
    });
    </script>
</x-app-layout>

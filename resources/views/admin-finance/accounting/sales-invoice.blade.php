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
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>SI Prepared By</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($normalOrders as $order)
                                            @php
                                                $displayAmount = $order->total_amount;
                                                if (in_array($order->type, ['area_consignment', 'area_sales_consignment'])) {
                                                    $activeInvoice = \App\Models\SalesInvoice::where('so_id', $order->id)->where('status', '!=', 'cancelled')->latest()->first();
                                                    if ($activeInvoice) {
                                                        $displayAmount = $activeInvoice->total_amount;
                                                    }
                                                }
                                            @endphp
                                            <tr class="si-row" data-date="{{ $order->created_at->format('Y-m-d') }}" data-type="{{ $order->type }}">
                                                <td>
                                                    @if($order->status === 'pending_si_prep' || $order->status === 'pending_si_approval' || $order->status === 'si_created' || $order->status === 'ar_created')
                                                        <input type="checkbox" class="order-checkbox normal-check" value="{{ $order->id }}" data-proof="{{ ($order->proof_of_payment || $order->type === 'ecom_direct') ? 'yes' : 'no' }}" data-amount="{{ $displayAmount }}" style="width: 16px; height: 16px; cursor: pointer;">
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
                                                <td>₱{{ number_format($displayAmount, 2) }}</td>
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
                                                <td>{{ $order->siPreparedBy->name ?? 'N/A' }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <a href="{{ route('admin-finance.sales-order.detail', $order->id) }}" class="btn btn-primary shadow btn-sm" title="View SO Detail"><i class="fas fa-eye"></i> View</a>
                                                        
                                                        @if($order->status === 'pending_si_prep' || $order->status === 'si_created' || $order->status === 'ar_created')
                                                            @if($order->type === 'ecom_direct' || $order->proof_of_payment)
                                                                <a href="{{ route('admin-finance.accounting.sales-invoice.prepare', $order->id) }}" class="btn btn-warning btn-sm">Prepare SI</a>
                                                            @else
                                                                <button class="btn btn-warning btn-sm" disabled title="Proof of Payment is required to prepare SI"><i class="fas fa-exclamation-triangle me-1"></i> Prepare SI</button>
                                                            @endif
                                                        @endif
 
                                                        @if($order->status === 'pending_si_approval')
                                                            @if($order->type === 'ecom_direct' || in_array($order->type, ['area_consignment', 'area_sales_consignment']) || $order->proof_of_payment)
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
                                                <td colspan="8" class="text-center py-4 text-muted">No normal orders requiring Sales Invoice at this time.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
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
                                                <td class="fw-bold">₱{{ number_format($order->total_amount, 2) }}</td>
                                                <td>
                                                    @php
                                                        $statusClass = 'secondary';
                                                        $displayStatus = str_replace('_', ' ', $order->status);
                                                        
                                                        if ($order->status === 'pending_si_prep') {
                                                            $statusClass = 'warning';
                                                            $displayStatus = 'Gathered (Pending SI Prep)';
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
                                                <td>{{ $order->siPreparedBy->name ?? 'N/A' }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <a href="{{ route('admin-finance.sales-order.detail', $order->id) }}" class="btn btn-primary shadow btn-sm" title="View SO Detail"><i class="fas fa-eye"></i> View</a>
                                                        
                                                        @if($order->status === 'pending_si_prep')
                                                            @if($order->type === 'ecom_direct' || $order->proof_of_payment)
                                                                <a href="{{ route('admin-finance.accounting.sales-invoice.prepare', $order->id) }}" class="btn btn-warning btn-sm">Prepare SI</a>
                                                            @else
                                                                <button class="btn btn-warning btn-sm" disabled title="Proof of Payment is required to prepare SI"><i class="fas fa-exclamation-triangle me-1"></i> Prepare SI</button>
                                                            @endif
                                                        @endif

                                                        @if($order->status === 'pending_si_approval')
                                                            @if($order->type === 'ecom_direct' || $order->proof_of_payment)
                                                                <form action="{{ route('admin-finance.accounting.sales-invoice.sign', $order->id) }}" method="POST" class="m-0">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-success btn-sm">Sign & Approve</button>
                                                                </form>
                                                            @else
                                                                <button class="btn btn-success btn-sm" disabled title="Proof of Payment is required to sign SI"><i class="fas fa-exclamation-triangle me-1"></i> Sign & Approve</button>
                                                            @endif
                                                        @endif
                                                        
                                                        @if($order->status === 'ready_for_delivery')
                                                        <a href="{{ route('admin-finance.accounting.sales-invoice.print', $order->id) }}" class="btn btn-info btn-sm" target="_blank"><i class="fas fa-print me-1"></i>Print SI</a>
                                                        @else
                                                        <a href="{{ route('admin-finance.accounting.sales-invoice.print', $order->id) }}" class="btn btn-outline-secondary btn-sm" target="_blank" title="Print SI (Draft)"><i class="fas fa-print me-1"></i>Print SI</a>
                                                        @endif
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
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Created Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($completedSIs as $si)
                                            <tr class="si-row" data-date="{{ $si->created_at->format('Y-m-d') }}" data-type="{{ $si->salesOrder->type ?? str_replace('_si', '', $si->transaction_type ?? 'area_consignment') }}">
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
                                                <td>₱{{ number_format($si->total_amount, 2) }}</td>
                                                <td><span class="badge bg-success text-white">Completed / Approved</span></td>
                                                <td>{{ $si->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <a href="{{ route('admin-finance.accounting.sales-invoice.print', $si->so_id) }}" class="btn btn-info btn-sm" target="_blank">
                                                            <i class="fas fa-print me-1"></i> Print SI
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4 text-muted">No completed Sales Invoices found.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
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
        const clearBtn = document.getElementById('clearFiltersBtn');

        function filterRows() {
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            const selectedType = typeSelect ? typeSelect.value : '';
            const platform = platformSelect ? platformSelect.value : '';
            const selectedPm = pmSelect ? pmSelect.value.toLowerCase() : '';

            document.querySelectorAll('.si-row').forEach(row => {
                let matchesSearch = true;
                let matchesDate = true;
                let matchesPlatform = true;
                let matchesType = true;
                let matchesPm = true;

                // Search query match
                if (query) {
                    const text = row.innerText.toLowerCase();
                    matchesSearch = text.includes(query);
                }

                // Type/Category match
                if (selectedType) {
                    const rowType = row.getAttribute('data-type');
                    if (rowType && rowType !== selectedType) {
                        matchesType = false;
                    }
                }

                // Payment Method match
                if (selectedPm) {
                    const rowPmSelect = row.querySelector('.pm-select');
                    const rowPm = rowPmSelect ? rowPmSelect.value.toLowerCase() : (row.getAttribute('data-pm') || '');
                    if (rowPm !== selectedPm) {
                        matchesPm = false;
                    }
                }

                // Date range match
                const rowDateStr = row.getAttribute('data-date');
                if (rowDateStr) {
                    if (startDateInput && startDateInput.value && rowDateStr < startDateInput.value) {
                        matchesDate = false;
                    }
                    if (endDateInput && endDateInput.value && rowDateStr > endDateInput.value) {
                        matchesDate = false;
                    }
                }

                // Platform match
                if (platform) {
                    const rowPlatform = row.getAttribute('data-platform');
                    if (rowPlatform && rowPlatform !== platform) {
                        matchesPlatform = false;
                    }
                }

                if (matchesSearch && matchesType && matchesDate && matchesPlatform && matchesPm) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            // Check if there are no visible rows in either table, show placeholder if empty
            const tables = document.querySelectorAll('table');
            tables.forEach(table => {
                const tbody = table.querySelector('tbody');
                if (!tbody) return;
                const rows = tbody.querySelectorAll('.si-row');
                const visibleRows = Array.from(rows).filter(r => r.style.display !== 'none');
                
                let noResultRow = tbody.querySelector('.no-results-row');
                if (visibleRows.length === 0 && rows.length > 0) {
                    if (!noResultRow) {
                        noResultRow = document.createElement('tr');
                        noResultRow.className = 'no-results-row';
                        const colCount = table.querySelectorAll('thead th').length;
                        noResultRow.innerHTML = `<td colspan="${colCount}" class="text-center py-4 text-muted">No matching results found.</td>`;
                        tbody.appendChild(noResultRow);
                    }
                } else if (noResultRow) {
                    noResultRow.remove();
                }
            });

            // Recalculate visible e-com total
            updateEcomTotal();
        }

        function updateEcomTotal() {
            const ecomTotalEl = document.getElementById('ecomTotalAmount');
            if (!ecomTotalEl) return;
            const ecomPane = document.getElementById('ecom-pane');
            if (!ecomPane) return;
            const visibleRows = ecomPane.querySelectorAll('.si-row');
            let total = 0;
            visibleRows.forEach(row => {
                if (row.style.display !== 'none') {
                    const amt = parseFloat(row.getAttribute('data-amount'));
                    if (!isNaN(amt)) total += amt;
                }
            });
            ecomTotalEl.textContent = '₱' + total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // Calculate on page load
        updateEcomTotal();

        if (searchInput) searchInput.addEventListener('input', filterRows);
        if (typeSelect) typeSelect.addEventListener('change', filterRows);
        if (pmSelect) pmSelect.addEventListener('change', filterRows);
        if (platformSelect) platformSelect.addEventListener('change', filterRows);
        if (startDateInput) startDateInput.addEventListener('change', filterRows);
        if (endDateInput) endDateInput.addEventListener('change', filterRows);

        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                if (searchInput) searchInput.value = '';
                if (typeSelect) typeSelect.value = '';
                if (pmSelect) pmSelect.value = '';
                if (platformSelect) platformSelect.value = '';
                if (startDateInput) startDateInput.value = '';
                if (endDateInput) endDateInput.value = '';
                filterRows();
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
</x-app-layout>

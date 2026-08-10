<x-app-layout :title="'Delivery Receipts'" :sidebar="'production'">
    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .nav-tabs .nav-link {
            color: #333;
            border: none;
            border-bottom: 3px solid transparent;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-right: 1rem;
        }
        .nav-tabs .nav-link:hover {
            border-bottom-color: #ff0000;
        }
        .nav-tabs .nav-link.active {
            background: transparent;
            color: #ff0000;
            border-bottom-color: #ff0000;
        }

        .table-status-badge {
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-in-transit { background: #cce5ff; color: #004085; }

        .dataTables_wrapper {
            font-size: 13px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #ff0000 !important;
            color: #fff !important;
            border-color: #ff0000 !important;
        }
    </style>
    @endpush

    @php
        $user = auth()->user();
        $canPrep = $user && ($user->isSuperAdmin() || 
            str_contains($user->position, 'Manager') || 
            str_contains($user->position, 'Supervisor') || 
            str_contains($user->position, 'Head') || 
            str_contains($user->position, 'Senior Logistics Staff') || 
            str_contains($user->position, 'Logistics Staff'));
            
        $canApprove = $user && ($user->isSuperAdmin() || 
            str_contains($user->position, 'Manager') || 
            str_contains($user->position, 'Supervisor') || 
            str_contains($user->position, 'Head') || 
            str_contains($user->position, 'Senior Logistics Staff'));
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="card">
                <ul class="nav nav-tabs border-bottom px-4 pt-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-pane" type="button" role="tab" aria-controls="pending-pane" aria-selected="true">
                            <i class="fas fa-hourglass-half me-2"></i>Pending DR Prep ({{ count($orders) }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-pane" type="button" role="tab" aria-controls="completed-pane" aria-selected="false">
                            <i class="fas fa-check-circle me-2"></i>Completed DRs ({{ count($completedOrders ?? []) }})
                        </button>
                    </li>
                </ul>

                <div class="card-header border-0 d-block d-sm-flex px-4 pt-3 pb-0">
                    <div>
                        <h4 class="fs-20 mb-0 text-black">Delivery Receipts Management</h4>
                    </div>
                    @if($canPrep)
                    <a href="{{ route('production.logistic.delivery-receipt') }}" class="btn btn-primary rounded d-flex align-items-center ms-auto" style="gap: 0.5rem; background: #ff0000; border: none;">
                        <i class="las la-plus"></i>
                        <span>Create New Receipt</span>
                    </a>
                    @endif
                </div>

                <div class="tab-content p-4">
                    <!-- Pending DR Prep Tab -->
                    <div class="tab-pane fade show active" id="pending-pane" role="tabpanel" aria-labelledby="pending-tab">
                        <!-- Single Line Filter Section -->
                        <div class="row g-2 align-items-center mb-4">
                            <div class="col-md-5 col-sm-12">
                                <input type="text" id="searchInput" class="form-control" placeholder="Search by SO # or Customer...">
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <select id="customerFilter" class="form-control">
                                    <option value="all">All Customers</option>
                                    @php
                                        $uniqueCustomers = $orders->map(function($order) {
                                            return $order->customer;
                                        })->filter()->unique('customer_id')->sortBy(function($c) {
                                            return $c->customer_name ?? $c->company_name ?? '';
                                        });
                                    @endphp
                                    @foreach($uniqueCustomers as $c)
                                        <option value="{{ $c->customer_id }}">{{ $c->customer_name ?? $c->company_name ?? 'Unknown' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <select id="statusFilter" class="form-control">
                                    <option value="all">All Status</option>
                                    <option value="pending_dr_prep">Pending Prep</option>
                                    <option value="pending_dr_approval">Pending Approval</option>
                                    <option value="ready_for_delivery">Ready for Delivery</option>
                                    <option value="si_created">Closed</option>
                                    <option value="reconsignment_pending">Reconsignment Pending</option>
                                    <option value="overdue">Overdue</option>
                                </select>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="drTable" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>SO Number</th>
                                        <th>Customer</th>
                                        <th>Total Amount</th>
                                        <th>Payment Terms</th>
                                        <th>Remaining Date</th>
                                        <th>Status</th>
                                        <th>Prepared By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    @php
                                        // Handle terms stored as '90 days', '30 days', etc.
                                        $termsMap = [
                                            'cash' => 0, 
                                            'cod' => 0, 
                                            '7_days' => 7, 
                                            '7 days' => 7,
                                            '7days' => 7,
                                            '15_days' => 15, 
                                            '15 days' => 15,
                                            '15days' => 15,
                                            '30_days' => 30, 
                                            '30 days' => 30,
                                            '30days' => 30,
                                            '60_days' => 60, 
                                            '60 days' => 60,
                                            '60days' => 60,
                                            '90_days' => 90, 
                                            '90 days' => 90,
                                            '90days' => 90,
                                            '90' => 90,
                                            '30' => 30,
                                            '7' => 7,
                                            '15' => 15,
                                            '60' => 60
                                        ];
                                        
                                        $termValue = strtolower(trim($order->terms ?? ''));
                                        $daysFromTerms = $termsMap[$termValue] ?? 0;
                                        
                                        // Only show calculation if payment terms are set (not cash/cod)
                                        if ($daysFromTerms > 0) {
                                            // Get the reference date
                                            $baseDateTime = $order->dr_prepared_at ?? $order->created_at;
                                            $baseDate = \Carbon\Carbon::parse($baseDateTime);
                                            
                                            // Add days to get due date
                                            $dueDate = $baseDate->copy()->addDays($daysFromTerms);
                                            
                                            // Get today at start of day
                                            $today = \Carbon\Carbon::today();
                                            
                                            // Calculate remaining days
                                            $interval = $today->diff($dueDate);
                                            $daysRemaining = (int)$interval->format('%r%a');
                                        } else {
                                            $daysRemaining = null;
                                            $dueDate = null;
                                        }

                                        $termsDisplay = match($order->terms) {
                                            'cash' => 'Cash',
                                            'cod' => 'COD',
                                            '7_days' => '7 Days',
                                            '15_days' => '15 Days',
                                            '30_days' => '30 Days',
                                            '60_days' => '60 Days',
                                            '90_days' => '90 Days',
                                            default => $order->terms
                                        };
                                    @endphp
                                    <tr data-so-number="{{ $order->so_number }}" data-customer="{{ $order->customer->customer_name ?? '' }}" data-customer-id="{{ $order->customer_id ?? '' }}" data-status="{{ $order->status }}" data-days-remaining="{{ $daysRemaining !== null ? $daysRemaining : '' }}">
                                        <td><strong>{{ $order->so_number }}</strong></td>
                                        <td>{{ $order->customer->customer_name ?? 'Unknown' }}</td>
                                        <td>₱{{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $termsDisplay }}</span>
                                        </td>
                                        <td>
                                            @if($daysRemaining !== null)
                                                <span class="@if($daysRemaining < 0) text-danger fw-bold @elseif($daysRemaining < 7) text-warning @else text-success @endif">
                                                    {{ $dueDate->format('M d, Y') }}
                                                    <br><small>{{ $daysRemaining < 0 ? abs($daysRemaining) . ' days overdue' : $daysRemaining . ' days' }}</small>
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($order->status === 'pending_dr_prep')
                                                <span class="table-status-badge status-pending">Pending Prep</span>
                                            @elseif($order->status === 'pending_dr_approval')
                                                <span class="table-status-badge status-in-transit">Pending Approval</span>
                                            @elseif($order->status === 'ready_for_delivery')
                                                <span class="table-status-badge status-completed">Ready for Delivery</span>
                                            @elseif($order->status === 'ar_created')
                                                <span class="table-status-badge bg-info text-white text-nowrap">Moved to AR</span>
                                            @elseif($order->status === 'cr_created')
                                                <span class="table-status-badge bg-success text-white text-nowrap">Moved to CR</span>
                                            @elseif($order->status === 'si_created')
                                                <span class="table-status-badge bg-secondary text-white">Closed</span>
                                            @elseif($order->status === 'reconsignment_pending')
                                                <span class="table-status-badge bg-warning text-dark text-nowrap">Reconsignment Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($order->drPreparedBy)
                                                <span class="fw-bold text-dark">{{ $order->drPreparedBy->name }}</span>
                                                <br><small class="text-muted">DR Approver</small>
                                            @else
                                                {{ $order->preparedBy->name ?? 'System' }}
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('production.logistic.delivery-receipt', $order->id) }}" class="btn btn-primary shadow btn-xs sharp" title="View/Create DR">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                @if(in_array($order->type, ['area_consignment', 'area_sales_consignment']))
                                                     <button type="button" class="btn btn-success shadow btn-xs sharp" title="Import Excel (Customer Name + Pick Qty)" data-bs-toggle="modal" data-bs-target="#importExcelModalDr{{ $order->id }}">
                                                         <i class="las la-file-excel"></i>
                                                     </button>
                                                     @php
                                                         $isMovedToAR = $order->status === 'ar_created' || $order->ar_prepared_at !== null;
                                                         $isMovedToCR = $order->status === 'cr_created' || $order->cr_prepared_at !== null;
                                                     @endphp
                                                     @if($order->type === 'area_sales_consignment')
                                                          <form action="{{ route('production.logistic.move-to-ar', $order->id) }}" method="POST" style="display:inline;">
                                                              @csrf
                                                              <button type="submit" class="btn btn-info shadow btn-xs sharp text-white" title="{{ $isMovedToAR ? 'Already Moved to AR' : 'Move to Acknowledgement Receipt (AR)' }}" {{ $isMovedToAR ? 'disabled' : '' }}>
                                                                  <i class="las la-file-signature"></i>
                                                              </button>
                                                          </form>
                                                      @elseif($order->type === 'area_consignment')
                                                          <form action="{{ route('production.logistic.move-to-cr', $order->id) }}" method="POST" style="display:inline;">
                                                              @csrf
                                                              <button type="submit" class="btn btn-success shadow btn-xs sharp" title="{{ $isMovedToCR ? 'Already Moved to CR' : 'Move to Consignment Receipt (CR)' }}" {{ $isMovedToCR ? 'disabled' : '' }}>
                                                                  <i class="las la-file-contract"></i>
                                                              </button>
                                                          </form>
                                                      @endif
                                                @endif
                                                 @if($canPrep)
                                                     <form action="{{ route('production.logistic.complete-dr', $order->id) }}" method="POST" style="display:inline;">
                                                         @csrf
                                                         <button type="submit" class="btn btn-success shadow btn-xs sharp" title="Complete DR & Move to Packing">
                                                             <i class="fas fa-check-circle"></i>
                                                         </button>
                                                     </form>
                                                 @endif

                                                 @if($order->status === 'pending_dr_prep' && $canPrep)
                                                    <form action="{{ route('production.logistic.mark-as-dr-prepared', $order->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-warning shadow btn-xs sharp" title="Mark as DR Prepared">
                                                            <i class="fas fa-file-invoice"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                @if($order->status === 'pending_dr_approval' && $canApprove)
                                                    <form action="{{ route('production.logistic.approve-dr', $order->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success shadow btn-xs sharp" title="Approve & Sign DR">
                                                            <i class="fas fa-signature"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Completed DRs Tab -->
                    <div class="tab-pane fade" id="completed-pane" role="tabpanel" aria-labelledby="completed-tab">
                        <div class="table-responsive">
                            <table class="table table-hover" id="completedDrTable" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>SO Number</th>
                                        <th>Customer</th>
                                        <th>Total Amount</th>
                                        <th>Payment Terms</th>
                                        <th>Remaining Date</th>
                                        <th>Status</th>
                                        <th>Prepared By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($completedOrders ?? [] as $order)
                                    @php
                                        $termsMap = [
                                            'cash' => 0, 
                                            'cod' => 0, 
                                            '7_days' => 7, 
                                            '7 days' => 7,
                                            '7days' => 7,
                                            '15_days' => 15,
                                            '15 days' => 15,
                                            '15days' => 15,
                                            '30_days' => 30, 
                                            '30 days' => 30,
                                            '30days' => 30,
                                            '60_days' => 60, 
                                            '60 days' => 60,
                                            '60days' => 60,
                                            '90_days' => 90, 
                                            '90 days' => 90,
                                            '90days' => 90,
                                            '90' => 90,
                                            '30' => 30,
                                            '7' => 7,
                                            '15' => 15,
                                            '60' => 60
                                        ];
                                        
                                        $termValue = strtolower(trim($order->terms ?? ''));
                                        $daysFromTerms = $termsMap[$termValue] ?? 0;
                                        if ($daysFromTerms === 0 && preg_match('/(\d+)\s*day/i', $termValue, $matches)) {
                                            $daysFromTerms = (int)$matches[1];
                                        }
                                        
                                        if ($daysFromTerms > 0) {
                                            $baseDateTime = $order->dr_prepared_at ?? $order->created_at;
                                            $baseDate = \Carbon\Carbon::parse($baseDateTime);
                                            $dueDate = $baseDate->copy()->addDays($daysFromTerms);
                                            $today = \Carbon\Carbon::today();
                                            $interval = $today->diff($dueDate);
                                            $daysRemaining = (int)$interval->format('%r%a');
                                        } else {
                                            $daysRemaining = null;
                                            $dueDate = null;
                                        }

                                        $termsDisplay = match($order->terms) {
                                            'cash' => 'Cash',
                                            'cod' => 'COD',
                                            '7_days' => '7 Days',
                                            '15_days' => '15 Days',
                                            '30_days' => '30 Days',
                                            '60_days' => '60 Days',
                                            '90_days' => '90 Days',
                                            default => $order->terms ?? 'Standard'
                                        };
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $order->so_number }}</strong></td>
                                        <td>{{ $order->customer->customer_name ?? 'Unknown' }}</td>
                                        <td>₱{{ number_format($order->total_amount, 2) }}</td>
                                        <td><span class="badge bg-info">{{ $termsDisplay }}</span></td>
                                        <td>
                                            @if($daysRemaining !== null)
                                                <span class="@if($daysRemaining < 0) text-danger fw-bold @elseif($daysRemaining < 7) text-warning @else text-success @endif">
                                                    {{ $dueDate->format('M d, Y') }}
                                                    <br><small>{{ $daysRemaining < 0 ? abs($daysRemaining) . ' days overdue' : $daysRemaining . ' days' }}</small>
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($order->status === 'ready_for_packing')
                                                <span class="table-status-badge bg-primary text-white">In Packing</span>
                                            @elseif($order->status === 'ready_for_delivery')
                                                <span class="table-status-badge status-completed">Ready for Delivery</span>
                                            @elseif($order->status === 'ar_created')
                                                <span class="table-status-badge bg-info text-white">Moved to AR</span>
                                            @elseif($order->status === 'cr_created')
                                                <span class="table-status-badge bg-success text-white">Moved to CR</span>
                                            @elseif($order->status === 'si_created')
                                                <span class="table-status-badge bg-warning text-dark">Moved to SI</span>
                                            @elseif($order->status === 'completed')
                                                <span class="table-status-badge bg-success text-white">Completed</span>
                                            @else
                                                <span class="table-status-badge bg-secondary text-white">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $order->preparedBy->name ?? 'System' }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('production.logistic.delivery-receipt', $order->id) }}" class="btn btn-primary shadow btn-xs sharp" title="View DR">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                @if(in_array($order->type, ['area_consignment', 'area_sales_consignment']))
                                                     <button type="button" class="btn btn-success shadow btn-xs sharp" title="Import Excel (Customer Name + Pick Qty)" data-bs-toggle="modal" data-bs-target="#importExcelModalDr{{ $order->id }}">
                                                         <i class="las la-file-excel"></i>
                                                     </button>
                                                     @php
                                                         $isMovedToAR = $order->status === 'ar_created' || $order->ar_prepared_at !== null;
                                                         $isMovedToCR = $order->status === 'cr_created' || $order->cr_prepared_at !== null;
                                                     @endphp
                                                     @if($order->type === 'area_sales_consignment')
                                                         <form action="{{ route('production.logistic.move-to-ar', $order->id) }}" method="POST" style="display:inline;">
                                                             @csrf
                                                             <button type="submit" class="btn btn-info shadow btn-xs sharp text-white" title="{{ $isMovedToAR ? 'Already Moved to AR' : 'Move to Acknowledgement Receipt (AR)' }}" {{ $isMovedToAR ? 'disabled' : '' }}>
                                                                 <i class="las la-file-signature"></i>
                                                             </button>
                                                         </form>
                                                     @elseif($order->type === 'area_consignment')
                                                         <form action="{{ route('production.logistic.move-to-cr', $order->id) }}" method="POST" style="display:inline;">
                                                             @csrf
                                                             <button type="submit" class="btn btn-success shadow btn-xs sharp" title="{{ $isMovedToCR ? 'Already Moved to CR' : 'Move to Consignment Receipt (CR)' }}" {{ $isMovedToCR ? 'disabled' : '' }}>
                                                                 <i class="las la-file-contract"></i>
                                                             </button>
                                                         </form>
                                                     @endif
                                                     <form action="{{ route('production.logistic.request-reconsignment', $order->id) }}" method="POST" style="display:inline;">
                                                         @csrf
                                                         <button type="submit" class="btn btn-warning shadow btn-xs sharp" title="Request Reconsignment">
                                                             <i class="las la-retweet"></i>
                                                         </button>
                                                     </form>
                                                     <form action="{{ route('production.logistic.return-consignment', $order->id) }}" method="POST" style="display:inline;">
                                                         @csrf
                                                         <button type="submit" class="btn btn-danger shadow btn-xs sharp" title="Return Consignment Stock">
                                                             <i class="las la-undo-alt"></i>
                                                         </button>
                                                     </form>
                                                 @endif

                                                <form action="{{ route('production.logistic.move-to-si', $order->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger shadow btn-xs sharp text-white" title="Move to Sales Invoice (SI)">
                                                        <i class="las la-file-invoice"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Import Excel Modals for DR --}}
    @foreach($orders->concat($completedOrders ?? [])->unique('id') as $order)
    @if(in_array($order->type, ['area_consignment', 'area_sales_consignment']))
    <div class="modal fade" id="importExcelModalDr{{ $order->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('production.logistic.delivery-receipt.import-excel') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title text-white">
                            <i class="las la-file-excel me-2"></i>
                            Import Excel into DR — <strong>{{ $order->so_number }}</strong>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info mb-3" style="font-size:0.85rem;">
                            <strong><i class="las la-info-circle me-1"></i> Steps:</strong>
                            <ol class="mb-0 mt-1 ps-3">
                                <li>Export <strong>{{ $order->so_number }}</strong> from Sales Orders.</li>
                                <li>Row 7 Col B: Fill in <strong>Customer Name</strong>.</li>
                                <li>Column G (Row 10+): Fill in <strong>Pick Qty</strong> per item.</li>
                                <li>Upload the updated Excel file below.</li>
                            </ol>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Upload Excel File <span class="text-danger">*</span></label>
                            <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls" required>
                            <small class="text-muted">Only .xlsx / .xls — must match SO <strong>{{ $order->so_number }}</strong>.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="las la-upload me-1"></i> Import to DR
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    @endforeach

    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            const drTable = $('#drTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 10,
                dom: 'rtip',
                columnDefs: [
                    { orderable: false, targets: -1 }
                ],
                language: {
                    zeroRecords: "No matching delivery receipts found"
                }
            });

            $('#completedDrTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 10,
                columnDefs: [
                    { orderable: false, targets: -1 }
                ],
                language: {
                    zeroRecords: "No completed delivery receipts found"
                }
            });

            // Custom DataTables filter matching Search, Customer, and Status
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'drTable') return true;

                const rowNode = drTable.row(dataIndex).node();
                const searchTerm = $('#searchInput').val().toLowerCase().trim();
                const currentCustomerId = $('#customerFilter').val();
                const currentStatusFilter = $('#statusFilter').val();

                const soNumber = ($(rowNode).data('so-number') || '').toString().toLowerCase();
                const customer = ($(rowNode).data('customer') || '').toString().toLowerCase();
                const customerId = ($(rowNode).data('customer-id') || '').toString();
                const status = ($(rowNode).data('status') || '').toString();
                const daysRemaining = ($(rowNode).data('days-remaining') || '').toString();

                // Search match
                const searchMatch = !searchTerm || soNumber.includes(searchTerm) || customer.includes(searchTerm);

                // Customer match
                const customerMatch = currentCustomerId === 'all' || customerId === currentCustomerId;

                // Status match
                let statusMatch = false;
                if (currentStatusFilter === 'all') {
                    statusMatch = true;
                } else if (currentStatusFilter === 'overdue') {
                    statusMatch = daysRemaining !== '' && parseInt(daysRemaining) < 0;
                } else {
                    statusMatch = status === currentStatusFilter;
                }

                return searchMatch && customerMatch && statusMatch;
            });

            // Trigger redraw when filter inputs change
            $('#searchInput').on('keyup change', function() {
                drTable.draw();
            });

            $('#customerFilter, #statusFilter').on('change', function() {
                drTable.draw();
            });
        });
    </script>
    @endpush
</x-app-layout>

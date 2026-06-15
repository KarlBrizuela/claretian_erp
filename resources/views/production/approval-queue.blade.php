<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .approval-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 2rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            border-left: 5px solid;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        }

        .stat-card.pending { border-left-color: #ffc107; }
        .stat-card.urgent { border-left-color: #dc3545; }
        .stat-card.recent { border-left-color: #17a2b8; }
        .stat-card.total { border-left-color: #6c757d; }

        .stat-card h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0 0 0.5rem 0;
            color: #333;
        }

        .stat-card p {
            margin: 0;
            color: #666;
            font-size: 0.95rem;
            font-weight: 500;
        }

        /* Unified Queue Navigation (Used for both/all tabs) */
        .queue-nav {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 0;
        }

        .queue-btn {
            padding: 1rem 1.75rem;
            cursor: pointer;
            border: none;
            background: transparent;
            color: #6c757d;
            font-weight: 600;
            font-size: 0.95rem;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
            position: relative;
            bottom: -2px;
        }

        .queue-btn.active {
            color: #ff0000;
            border-bottom-color: #ff0000;
            background: rgba(255, 0, 0, 0.05);
        }

        .queue-btn:hover { 
            color: #ff0000; 
            background: rgba(255, 0, 0, 0.03);
        }

        .table-responsive {
            margin-top: 1.5rem;
        }

        #approvalQueueTable {
            font-size: 0.9rem;
        }

        #approvalQueueTable thead th,
        #myApprovalsTable thead th,
        #mySubmissionsTable thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            font-size: 0.8rem;
            color: #495057;
            padding: 0.75rem 0.5rem;
            border: none;
        }

        #approvalQueueTable tbody td,
        #myApprovalsTable tbody td,
        #mySubmissionsTable tbody td {
            padding: 0.75rem 0.5rem;
            vertical-align: middle;
        }

        .document-type-badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            white-space: nowrap;
        }

        .type-sales-order { background-color: #cfe2ff; color: #084298; }
        .type-direct-invoice { background-color: #ceffbcff; color: #00991fff; }
        .type-expense { background-color: #fff3cd; color: #856404; }
        .type-job-order { background-color: #f8d7da; color: #721c24; }

        .btn-xs {
            padding: 0.35rem 0.65rem;
            font-size: 0.875rem;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .card-header {
            padding: 1.5rem 2rem;
            background: #fff;
            border-bottom: 1px solid #e9ecef;
        }

        .card-body {
            padding: 2rem;
        }

        /* Status badges matching admin-finance */
        .status-badge {
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
            display: inline-block;
            white-space: nowrap;
        }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-success { background-color: #d1e7dd; color: #0f5132; }
        .status-danger { background-color: #f8d7da; color: #721c24; }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .approval-stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            .stat-card {
                padding: 1.5rem;
            }

            .stat-card h3 {
                font-size: 2rem;
            }

            .card-body {
                padding: 1.5rem;
            }

            .queue-nav {
                flex-wrap: wrap;
            }

            .queue-btn {
                padding: 0.75rem 1.25rem;
                font-size: 0.875rem;
            }
        }

        @media (max-width: 768px) {
            .approval-stats {
                grid-template-columns: 1fr;
                gap: 1rem;
                margin-bottom: 1.5rem;
            }

            .stat-card {
                padding: 1.25rem;
            }

            .stat-card h3 {
                font-size: 1.75rem;
            }

            .card-header {
                padding: 1rem 1.5rem;
            }

            .card-body {
                padding: 1rem;
            }

            .queue-nav {
                gap: 0.25rem;
                margin-bottom: 1rem;
            }

            .queue-btn {
                padding: 0.65rem 1rem;
                font-size: 0.8rem;
                flex: 1;
                text-align: center;
            }

            .table-responsive {
                margin-top: 1rem;
            }

            #approvalQueueTable,
            #myApprovalsTable,
            #mySubmissionsTable {
                font-size: 0.8rem;
            }

            #approvalQueueTable thead th,
            #myApprovalsTable thead th,
            #mySubmissionsTable thead th {
                font-size: 0.7rem;
                padding: 0.75rem 0.5rem;
            }

            #approvalQueueTable tbody td,
            #myApprovalsTable tbody td,
            #mySubmissionsTable tbody td {
                padding: 0.75rem 0.5rem;
            }

            .document-type-badge {
                padding: 4px 10px;
                font-size: 0.7rem;
            }

            .btn-xs {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }

            .btn-sm {
                padding: 0.35rem 0.6rem;
                font-size: 0.8rem;
            }
        }
    </style>
    @endpush

    <div class="approval-stats">
        <div class="stat-card pending">
            <h3 id="pendingCount">{{ $salesOrders->count() + $pendingCashAdvances->count() + $pendingCctvRequests->count() }}</h3>
            <p>Pending Approvals</p>
        </div>
        <div class="stat-card urgent">
            <h3 id="urgentCount">0</h3>
            <p>Urgent (Overdue)</p>
        </div>
        <div class="stat-card recent">
            @php
                $recentSales = $salesOrders->where('created_at', '>=', now()->startOfDay())->count();
                $recentCash = $pendingCashAdvances->where('created_at', '>=', now()->startOfDay())->count();
                $recentCctv = $pendingCctvRequests->where('created_at', '>=', now()->startOfDay())->count();
                $recentTotal = $recentSales + $recentCash + $recentCctv;
            @endphp
            <h3 id="recentCount">{{ $recentTotal }}</h3>
            <p>Received Today</p>
        </div>
        <div class="stat-card total">
            <h3 id="totalCount">{{ $salesOrders->count() + $pendingCashAdvances->count() + $pendingCctvRequests->count() }}</h3>
            <p>Total Pending</p>
        </div>
    </div>

    <!-- Personal Activity Card (My Approvals & My Submissions) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="fs-20 mb-0 text-black">My Activity</h4>
                </div>
                <div class="card-body">
                    <div class="queue-nav">
                        <button class="queue-btn tab-trigger active" onclick="switchTab(this, 'my-approvals')">For Approval</button>
                        <button class="queue-btn tab-trigger" onclick="switchTab(this, 'my-submissions')">My Submissions</button>
                        <button class="queue-btn tab-trigger" onclick="switchTab(this, 'my-approved')">Approved</button>
                    </div>

                    <!-- My Approvals Tab Content -->
                    <div id="my-approvals-content" class="tab-section">
                        <div class="table-responsive">
                            <table id="myApprovalsTable" class="display table table-bordered" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Ref #</th>
                                        <th>User</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($myApprovals as $approval)
                                    <tr>
                                        <td>
                                            @php
                                                $typeClass = $approval['type'] === 'Sales Order' ? 'type-sales-order' : ($approval['type'] === 'CCTV' ? 'type-job-order' : 'badge-info');
                                            @endphp
                                            <span class="document-type-badge {{ $typeClass }}" @if($approval['type'] === 'Cash Advance') style="background-color: #e3f2fd; color: #0d47a1;" @endif>{{ $approval['type'] }}</span>
                                        </td>
                                        <td><strong>{{ $approval['reference_no'] }}</strong></td>
                                        <td>{{ $approval['submitted_by'] }}</td>
                                        <td>{{ $approval['submitted_date']->format('Y-m-d h:i A') }}</td>
                                        <td>{{ $approval['amount'] }}</td>
                                        <td>
                                            @php
                                                $status = $approval['status'];
                                                $badgeClass = 'status-pending';
                                            @endphp
                                            <span class="status-badge {{ $badgeClass }}">{{ ucwords(str_replace('_', ' ', $status)) }}</span>
                                        </td>
                                        <td>
                                            @if($approval['type'] === 'Sales Order')
                                                <a href="{{ $approval['url'] }}" class="btn btn-primary btn-sm"><i class="las la-eye"></i> Review</a>
                                            @elseif($approval['type'] === 'CCTV')
                                                <form action="{{ route('user.cctv-requests.update', $approval['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="Pending HR approval">
                                                    <button type="submit" class="btn btn-success btn-sm"><i class="las la-check"></i> Approve</button>
                                                </form>
                                                <form action="{{ route('user.cctv-requests.update', $approval['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="btn btn-danger btn-sm"><i class="las la-times"></i> Reject</button>
                                                </form>
                                            @else
                                                <button type="button" 
                                                        class="btn btn-primary btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#cashAdvanceModal"
                                                        data-id="{{ $approval['id'] }}"
                                                        data-name="{{ $approval['submitted_by'] }}"
                                                        data-amount="{{ $approval['amount'] }}"
                                                        data-purpose="{{ $approval['original']->purpose }}"
                                                        data-date="{{ $approval['original']->date_needed->format('M d, Y') }}"
                                                        data-original="{{ json_encode($approval['original']) }}">
                                                    <i class="las la-eye"></i> Review
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- My Submissions Tab Content -->
                    <div id="my-submissions-content" class="tab-section" style="display: none;">
                        <div class="table-responsive">
                            <table id="mySubmissionsTable" class="display table table-bordered" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Ref #</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mySubmissions as $submission)
                                    <tr>
                                        <td><span class="document-type-badge {{ $submission->type === 'Sales Order' ? 'type-sales-order' : 'badge-info' }}" @if($submission->type === 'Cash Advance') style="background-color: #e3f2fd; color: #0d47a1;" @endif>{{ $submission->type }}</span></td>
                                        <td><strong>{{ $submission->reference_no }}</strong></td>
                                        <td>{{ $submission->submitted_date->format('Y-m-d h:i A') }}</td>
                                        <td>{{ $submission->amount }}</td>
                                        <td>
                                            @php
                                                $status = $submission->status;
                                                $badgeClass = 'status-pending';
                                                if (in_array($status, ['approved', 'completed', 'picking', 'delivered'])) $badgeClass = 'status-success';
                                                elseif (in_array($status, ['rejected', 'cancelled'])) $badgeClass = 'status-danger';
                                                elseif (in_array($status, ['pending_admin_approval', 'pending_director_approval'])) $badgeClass = 'status-info';
                                            @endphp
                                            <span class="status-badge {{ $badgeClass }}">{{ ucwords(str_replace('_', ' ', $status)) }}</span>
                                        </td>
                                        <td>
                                            @if($submission->type === 'Sales Order')
                                                <a href="{{ $submission->url }}" class="btn btn-primary btn-sm"><i class="las la-eye"></i> View</a>
                                            @else
                                                <button type="button" class="btn btn-primary btn-sm"
                                                    data-bs-toggle="modal" data-bs-target="#cashAdvanceModal"
                                                    data-id="{{ $submission->id }}"
                                                    data-name="{{ $submission->prep_name }}"
                                                    data-amount="{{ $submission->amount }}"
                                                    data-purpose="{{ $submission->original->purpose ?? '' }}"
                                                    data-date="{{ $submission->original->date_needed ? \Carbon\Carbon::parse($submission->original->date_needed)->format('M d, Y') : '' }}"
                                                    data-status="{{ $submission->status }}"
                                                    data-original="{{ json_encode($submission->original) }}"
                                                    data-view-only="true">
                                                    <i class="las la-eye"></i> View
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- My Approved Tab Content -->
                    <div id="my-approved-content" class="tab-section" style="display: none;">
                        <div class="table-responsive">
                            <table id="myApprovedTable" class="display table table-bordered" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Ref #</th>
                                        <th>Submitted By</th>
                                        <th>Date</th>
                                        <th>Amount / Details</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($myApprovedRequests as $approved)
                                    <tr>
                                        <td><span class="document-type-badge badge-info" style="background-color: #e3f2fd; color: #0d47a1;">{{ $approved->type }}</span></td>
                                        <td><strong>{{ $approved->reference_no }}</strong></td>
                                        <td>{{ $approved->submitted_by }}</td>
                                        <td>{{ $approved->submitted_date->format('Y-m-d h:i A') }}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold text-dark">{{ $approved->amount }}</span>
                                                <small class="text-muted">{{ \Illuminate\Support\Str::limit($approved->original->purpose ?? '', 30) }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $status = $approved->status;
                                                $badgeClass = 'status-pending';
                                                if (in_array($status, ['approved', 'completed', 'picking', 'delivered'])) $badgeClass = 'status-success';
                                                elseif (in_array($status, ['rejected', 'cancelled'])) $badgeClass = 'status-danger';
                                                elseif (in_array($status, ['pending_admin_approval', 'pending_director_approval'])) $badgeClass = 'status-info';
                                            @endphp
                                            <span class="status-badge {{ $badgeClass }}">{{ ucwords(str_replace('_', ' ', $status)) }}</span>
                                        </td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-primary btn-xs"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#cashAdvanceModal"
                                                    data-id="{{ $approved->id }}"
                                                    data-name="{{ $approved->submitted_by }}"
                                                    data-amount="{{ $approved->amount }}"
                                                    data-purpose="{{ $approved->original->purpose }}"
                                                    data-date="{{ $approved->original->date_needed->format('M d, Y') }}"
                                                    data-status="{{ $approved->status }}">
                                                <i class="las la-eye"></i> View
                                            </button>
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

    <!-- Department Queue Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="fs-20 mb-0 text-black">Departmental Queue</h4>
                </div>
                <div class="card-body">
                    <div class="queue-nav">
                        <button class="queue-btn filter-trigger active" onclick="filterQueue(this, '')">All Records</button>
                        <button class="queue-btn filter-trigger" onclick="filterQueue(this, 'Sales Order')">Sales Orders</button>
                        <button class="queue-btn filter-trigger" onclick="filterQueue(this, 'Cash Advance')">Cash Advances</button>
                    </div>

                    <div class="table-responsive">
                        <table id="approvalQueueTable" class="display table table-bordered" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Ref #</th>
                                    <th>User</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesOrders as $order)
                                <tr data-type="Sales Order">
                                    <td><span class="document-type-badge type-sales-order">Sales Order</span></td>
                                    <td><strong>{{ $order->so_number }}</strong></td>
                                    <td>{{ $order->preparedBy->name ?? 'N/A' }}</td>
                                    <td>{{ $order->created_at->format('Y-m-d h:i A') }}</td>
                                    <td>₱{{ number_format($order->total_amount, 2) }}</td>
                                    <td><span class="status-badge status-pending">Pending Approval</span></td>
                                    <td>
                                        <a href="{{ route('production.sales-order.detail', $order->id) }}" class="btn btn-primary btn-sm"><i class="las la-eye"></i> Review</a>
                                    </td>
                                </tr>
                                @endforeach

                                @foreach($pendingCashAdvances as $advance)
                                <tr data-type="Cash Advance">
                                    <td><span class="document-type-badge badge-info" style="background-color: #e3f2fd; color: #0d47a1;">Cash Advance</span></td>
                                    <td><strong>CA-{{ str_pad($advance->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                    <td>{{ $advance->user->name ?? $advance->employee_name }}</td>
                                    <td>{{ $advance->created_at->format('Y-m-d h:i A') }}</td>
                                    <td>₱{{ number_format($advance->amount, 2) }}</td>
                                    <td><span class="status-badge status-pending">Pending Manager</span></td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-primary btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#cashAdvanceModal"
                                                data-id="{{ $advance->id }}"
                                                data-name="{{ $advance->user->name ?? $advance->employee_name }}"
                                                data-amount="₱{{ number_format($advance->amount, 2) }}"
                                                data-purpose="{{ $advance->purpose }}"
                                                data-date="{{ $advance->date_needed->format('M d, Y') }}"
                                                data-original="{{ json_encode($advance) }}">
                                            <i class="las la-eye"></i> Review
                                        </button>
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

    <!-- Cash Advance Approval Modal -->
    <div class="modal fade" id="cashAdvanceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 text-white position-relative" style="background: #dc3545; padding: 1.5rem 2rem;">
                    <div class="d-flex align-items-center">
                        <div class="icon-wrapper me-3" style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="las la-money-bill-wave fs-24"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-white" id="ca-modal-reference-header">CA-00000</h5>
                            <p class="mb-0 opacity-75 small">Cash Advance Review</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; top: 1.5rem; right: 1.5rem;"></button>
                </div>
                <div class="modal-body p-4" style="background: #f8f9fa;">
                    <div class="row g-4">
                        <!-- Employee Info -->
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 shadow-sm h-100 border-start border-4 border-primary">
                                <label class="text-uppercase small fw-bold text-muted mb-2 d-block">Employee Name</label>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light p-2 me-3">
                                        <i class="las la-user-tie text-primary fs-20"></i>
                                    </div>
                                    <h6 class="mb-0 fw-bold" id="ca-modal-name">---</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Amount Requested -->
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 shadow-sm h-100 border-start border-4 border-success">
                                <label class="text-uppercase small fw-bold text-muted mb-2 d-block">Amount Requested</label>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light p-2 me-3">
                                        <i class="las la-wallet text-success fs-20"></i>
                                    </div>
                                    <h6 class="mb-0 fw-bold text-success fs-18" id="ca-modal-amount">₱0.00</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Date Needed -->
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 shadow-sm h-100 border-start border-4 border-info">
                                <label class="text-uppercase small fw-bold text-muted mb-2 d-block">Date Needed</label>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light p-2 me-3">
                                        <i class="las la-calendar-check text-info fs-20"></i>
                                    </div>
                                    <h6 class="mb-0 fw-bold" id="ca-modal-date">---</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 shadow-sm h-100 border-start border-4 border-warning">
                                <label class="text-uppercase small fw-bold text-muted mb-2 d-block">Current Status</label>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light p-2 me-3">
                                        <i class="las la-info-circle text-warning fs-20"></i>
                                    </div>
                                    <div id="ca-modal-status">
                                        <span class="status-badge status-pending">Manager Review</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Details Section -->
                        <div class="col-12">
                            <div class="p-3 bg-white rounded-3 shadow-sm border">
                                <label class="text-uppercase small fw-bold text-muted mb-2 d-block">Request Details</label>
                                <div id="ca-modal-details" class="bg-light p-3 rounded-2" style="min-height: 80px;">
                                    <!-- Dynamic content here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rejection Reason Container (Floating Style) -->
                    <div id="rejection-reason-container" class="mt-4 p-3 rounded-3 shadow-sm border border-danger bg-white" style="display: none;">
                        <label class="fw-bold text-danger mb-2 d-block"><i class="las la-exclamation-circle me-1"></i> Reason for Rejection</label>
                        <textarea id="rejection_reason_input" class="form-control border-danger mb-3" rows="3" placeholder="Explain why this request is being rejected..."></textarea>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light btn-sm" onclick="toggleRejection()">Cancel</button>
                            <button type="button" class="btn btn-danger btn-sm px-3" onclick="submitRejection()">Confirm Rejection</button>
                        </div>
                    </div>
                </div>
                
                <!-- View-Only Footer (My Submissions) -->
                <div class="modal-footer border-0 p-4" id="footer-view-only" style="display:none;">
                    <button type="button" class="btn btn-secondary px-4 h-45" data-bs-dismiss="modal">Close</button>
                </div>

                <!-- Action Footer (For Approvers) -->
                <div class="modal-footer border-0 p-4" id="footer-actions">
                    <button type="button" class="btn btn-secondary px-4 h-45" data-bs-dismiss="modal">Close</button>
                    <div class="d-flex gap-2 ms-auto">
                        <button type="button" class="btn btn-outline-danger px-4 h-45" id="btn-show-reject" onclick="toggleRejection()">Reject</button>
                        <form action="" method="POST" id="ca-approve-form" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="pending_admin_approval">
                            <button type="submit" class="btn btn-success px-5 h-45 fw-bold shadow-sm">Approve Request</button>
                        </form>
                    </div>

                    <!-- Hidden Rejection Form -->
                    <form action="" method="POST" id="ca-reject-form" style="display: none;">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="rejected">
                        <input type="hidden" name="rejection_reason" id="hidden_rejection_reason">
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        // Global variables to hold table instances
        var queueTable;
        var myApprovalsTable;
        var mySubmissionsTable;
        var myApprovedTable;

        // Global function for bottom card tab switching
        function switchTab(btn, tabId) {
            $('.tab-section').hide();
            $('#' + tabId + '-content').show();
            
            $('.tab-trigger').removeClass('active');
            $(btn).addClass('active');

            // Re-draw tables to fix alignment in hidden tabs
            if (tabId === 'my-submissions' && mySubmissionsTable) mySubmissionsTable.columns.adjust().draw();
            if (tabId === 'my-approvals' && myApprovalsTable) myApprovalsTable.columns.adjust().draw();
            if (tabId === 'my-approved' && myApprovedTable) myApprovedTable.columns.adjust().draw();
        }

        // Global function for top card filtering
        function filterQueue(btn, filterValue) {
            // Update active state
            $('.filter-trigger').removeClass('active');
            $(btn).addClass('active');

            // Apply filter using DataTables
            if (queueTable) {
                if (filterValue === '') {
                    queueTable.search('').columns().search('').draw();
                } else {
                    queueTable.column(0).search(filterValue).draw();
                }
            }
        }

        $(document).ready(function() {
            // Initialize Tables
            queueTable = $('#approvalQueueTable').DataTable({
                order: [[3, 'desc']],
                pageLength: 25,
                columnDefs: [{ orderable: false, targets: -1 }]
            });

            myApprovalsTable = $('#myApprovalsTable').DataTable({
                order: [[3, 'desc']],
                pageLength: 25,
                columnDefs: [{ orderable: false, targets: -1 }]
            });

            mySubmissionsTable = $('#mySubmissionsTable').DataTable({
                order: [[2, 'desc']],
                pageLength: 25,
                columnDefs: [{ orderable: false, targets: -1 }]
            });

            myApprovedTable = $('#myApprovedTable').DataTable({
                order: [[3, 'desc']],
                pageLength: 25,
                columnDefs: [{ orderable: false, targets: -1 }]
            });

            // Cash Advance Modal Population
            $('#cashAdvanceModal').on('show.bs.modal', function (event) {
                var modal = $(this);
                var button = $(event.relatedTarget);
                var id = button.attr('data-id');
                var name = button.attr('data-name');
                var amount = button.attr('data-amount');
                var purpose = button.attr('data-purpose');
                var date = button.attr('data-date');
                var status = button.attr('data-status') || 'pending_supervisor_approval';
                var reference = 'CA-' + String(id).padStart(5, '0');

                modal.find('#ca-modal-name').text(name);
                modal.find('#ca-modal-amount').text(amount);
                modal.find('#ca-modal-date').text(date);
                modal.find('#ca-modal-reference-header').text(reference);

                let original = {};
                try {
                    original = JSON.parse(button.attr('data-original') || '{}');
                } catch(e) {}

                // Dynamic Details Logic
                const fieldLabels = {
                    'purpose': 'Purpose / Justification',
                    'date_needed': 'Date Needed',
                    'amount': 'Principal Amount',
                    'request_type': 'Classification',
                    'repayment_method': 'Repayment Method',
                    'is_liquidation_required': 'Liquidation Required',
                    'employee_name': 'Staff Name',
                    'department': 'Department'
                };
                const excludedFields = [
                    'id', 'user_id', 'created_at', 'updated_at', 'status', 'department_source',
                    'approved_by_manager', 'manager_approved_at',
                    'approved_by_admin', 'admin_approved_at',
                    'approved_by_director', 'director_approved_at',
                    'rejected_by', 'rejected_at', 'rejection_reason', 'amount'
                ];

                let detailsHtml = `<div class="table-responsive"><table class="table table-sm table-borderless mb-0"><tbody>`;
                Object.keys(original).forEach(key => {
                    const val = original[key];
                    if (!excludedFields.includes(key) && val !== null && val !== undefined) {
                        let displayVal = val;
                        if (typeof val === 'boolean' || val === 1 || val === 0) {
                            if (key.includes('is_')) displayVal = val ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-light text-dark">No</span>';
                        }
                        detailsHtml += `<tr><th class="py-1 text-muted small px-0" style="width: 40%;">${fieldLabels[key] || key.replace(/_/g, ' ').toUpperCase()}</th><td class="py-1 text-dark fs-13 px-0">${displayVal}</td></tr>`;
                    }
                });
                detailsHtml += `</tbody></table></div>`;
                modal.find('#ca-modal-details').html(detailsHtml);

                // Status Configuration matching admin-finance logic
                const statusConfig = {
                    'pending_supervisor_approval': { badge: 'warning', text: 'Manager Review' },
                    'pending_admin_approval': { badge: 'info', text: 'Finance Review (2nd Approval)' },
                    'pending_director_approval': { badge: 'primary', text: 'Final Review (Director)' },
                    'forwarded to accounting': { badge: 'info', text: 'Forwarded to Accounting' },
                    'approved': { badge: 'success', text: 'Approved' },
                    'rejected': { badge: 'danger', text: 'Rejected' },
                };
                const config = statusConfig[status] || { badge: 'secondary', text: status.replace('_', ' ') };
                modal.find('#ca-modal-status').html(`<span class="status-badge status-${config.badge === 'warning' ? 'pending' : (config.badge === 'danger' ? 'danger' : 'success')}">${config.text}</span>`);

                // Update Form Actions
                var actionUrl = '/employee/cash-advance/' + id;
                modal.find('#ca-approve-form').attr('action', actionUrl);
                modal.find('#ca-reject-form').attr('action', actionUrl);
                
                // Toggle footers: view-only shows only Close, actionable shows Reject + Approve
                var viewOnly = button.attr('data-view-only') === 'true';
                if (viewOnly || status === 'approved' || status === 'rejected') {
                    modal.find('#footer-view-only').show();
                    modal.find('#footer-actions').hide();
                } else {
                    modal.find('#footer-view-only').hide();
                    modal.find('#footer-actions').show();
                }

                // Reset rejection container
                $('#rejection-reason-container').hide();
                $('#rejection_reason_input').val('');
            });
        });

        function toggleRejection() {
            $('#rejection-reason-container').toggle();
        }

        function submitRejection() {
            const reason = $('#rejection_reason_input').val();
            if (!reason) {
                alert('Please provide a reason for rejection.');
                return;
            }
            $('#hidden_rejection_reason').val(reason);
            $('#ca-reject-form').submit();
        }
    </script>
    @endpush
</x-app-layout>

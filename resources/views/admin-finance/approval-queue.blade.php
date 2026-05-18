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

        /* Status badges matching others */
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

            /* Modal responsive adjustments */
            .modal-dialog {
                margin: 0.5rem;
            }

            .modal-header {
                padding: 1rem 1.25rem !important;
            }

            .modal-footer {
                padding: 1rem 1.25rem !important;
                flex-direction: column;
                gap: 0.5rem;
            }

            .modal-footer .d-flex {
                flex-direction: column;
                width: 100%;
            }

            .modal-footer .d-flex > div {
                width: 100%;
            }

            .modal-footer button,
            .modal-footer form {
                width: 100%;
            }

            .modal-footer .gap-2 {
                gap: 0.5rem !important;
            }
        }

        @media (max-width: 576px) {
            .approval-stats {
                gap: 0.75rem;
                margin-bottom: 1rem;
            }

            .stat-card {
                padding: 1rem;
            }

            .stat-card h3 {
                font-size: 1.5rem;
            }

            .stat-card p {
                font-size: 0.85rem;
            }

            .card-header h4 {
                font-size: 1rem;
            }

            .queue-btn {
                padding: 0.5rem 0.75rem;
                font-size: 0.75rem;
            }

            /* Stack table columns on very small screens */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            /* Ensure buttons don't wrap awkwardly */
            .d-flex.gap-2 {
                flex-wrap: wrap;
            }

            /* Modal adjustments for small screens */
            .modal-dialog {
                margin: 0.25rem;
                max-width: 100%;
            }

            .modal-body .row.g-2 {
                gap: 0.5rem !important;
            }

            .modal-body .col-md-6 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .info-card {
                padding: 0.5rem !important;
            }

            .icon-wrapper {
                width: 28px !important;
                height: 28px !important;
            }

            .icon-wrapper i {
                font-size: 1rem !important;
            }

            .modal-body label {
                font-size: 0.7rem !important;
            }

            .modal-body p,
            .modal-body .small {
                font-size: 0.8rem !important;
            }
        }
    </style>
    @endpush

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="approval-stats">
        <div class="stat-card pending">
            <h3 id="pendingCount">{{ count($myApprovals) }}</h3>
            <p>Pending Approvals</p>
        </div>
        <div class="stat-card urgent">
            <h3 id="urgentCount">0</h3>
            <p>Urgent (Overdue)</p>
        </div>
        <div class="stat-card recent">
            @php
                $recentCount = collect($myApprovals)->filter(function($item) {
                    $date = $item['submitted_date'] instanceof \Carbon\Carbon ? $item['submitted_date'] : \Carbon\Carbon::parse($item['submitted_date']);
                    return $date->isToday();
                })->count();
            @endphp
            <h3 id="recentCount">{{ $recentCount }}</h3>
            <p>Received Today</p>
        </div>
        <div class="stat-card total">
            <h3 id="totalCount">{{ count($myApprovals) }}</h3>
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
                                        <th>Amount/Info</th>
                                        <th>Attachment</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($myApprovals as $item)
                                    <tr>
                                        <td>
                                            @php
                                                $typeClass = 'type-sales-order';
                                                if($item['type'] !== 'Sales Order') $typeClass = 'type-job-order';
                                            @endphp
                                            <span class="document-type-badge {{ $typeClass }}">{{ $item['type'] }}</span>
                                        </td>
                                        <td><strong>{{ $item['reference_no'] }}</strong></td>
                                        <td>{{ $item['submitted_by'] }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item['submitted_date'])->timezone('Asia/Manila')->format('Y-m-d h:i A') }}</td>
                                        <td>
                                            @if(is_numeric($item['amount']))
                                                ₱ {{ number_format($item['amount'], 2) }}
                                            @else
                                                {{ $item['amount'] ?? 'N/A' }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($item['attachment'])
                                                <span class="text-primary"><i class="las la-paperclip"></i> Attachment</span>
                                            @else
                                                <span class="text-muted">None</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $status = $item['status'];
                                                $badgeClass = 'status-pending';
                                                if(in_array($status, ['approved', 'completed', 'picking', 'delivered'])) $badgeClass = 'status-success';
                                                elseif(in_array($status, ['rejected', 'cancelled'])) $badgeClass = 'status-danger';
                                            @endphp
                                            <span class="status-badge {{ $badgeClass }}">
                                                {{ ucwords(str_replace('_', ' ', $status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($item['type'] === 'Sales Order')
                                                @if($item['status'] === 'Pending AR')
                                                    <a href="{{ route('admin-finance.accounting.ar.prepare', $item['id']) }}" class="btn btn-success btn-sm"><i class="las la-file-invoice"></i> Issue AR</a>
                                                @else
                                                    <a href="{{ $item['url'] }}" class="btn btn-primary btn-xs"><i class="las la-eye"></i> Review</a>
                                                @endif
                                            @else
                                                <button type="button" 
                                                        class="btn btn-primary btn-xs view-details-btn" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#requestDetailsModal"
                                                        data-id="{{ $item['id'] }}"
                                                        data-type="{{ $item['type'] }}"
                                                        data-reference="{{ $item['reference_no'] }}"
                                                        data-submitted-by="{{ $item['submitted_by'] }}"
                                                        data-date="{{ \Carbon\Carbon::parse($item['submitted_date'])->timezone('Asia/Manila')->format('M. d, Y') }}"
                                                        data-department="{{ $item['department'] }}"
                                                        data-description="{{ $item['full_description'] ?? $item['description'] }}"
                                                        data-status="{{ $item['status'] }}"
                                                        data-amount="{{ $item['amount'] }}"
                                                        data-original="{{ json_encode($item['original']) }}"
                                                        title="Review">
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
                                        <th>Info / Amount</th>
                                        <th>Attachment</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mySubmissions as $submission)
                                    <tr>
                                        <td>
                                            @php
                                                $typeClass = 'type-sales-order';
                                                if($submission['type'] !== 'Sales Order') $typeClass = 'type-job-order';
                                            @endphp
                                            <span class="document-type-badge {{ $typeClass }}">{{ $submission['type'] }}</span>
                                        </td>
                                        <td><strong>{{ $submission['reference_no'] }}</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($submission['submitted_date'])->timezone('Asia/Manila')->format('Y-m-d h:i A') }}</td>
                                        <td>{{ $submission['detail'] }}</td>
                                        <td>
                                            @if($submission['attachment'])
                                                <span class="text-primary"><i class="las la-paperclip"></i> Attachment</span>
                                            @else
                                                <span class="text-muted">None</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $status = $submission['status'];
                                                $badgeClass = 'badge-warning';
                                                
                                                if (in_array($status, ['approved', 'completed', 'picking', 'delivered', 'ready_for_delivery'])) {
                                                    $badgeClass = 'badge-success';
                                                } elseif (in_array($status, ['rejected', 'cancelled'])) {
                                                    $badgeClass = 'badge-danger';
                                                }
                                            @endphp
                                            <span class="status-badge {{ $badgeClass }}">{{ ucwords(str_replace('_', ' ', $status)) }}</span>
                                        </td>
                                        <td>
                                            @if($submission['type'] === 'Sales Order')
                                                <a href="{{ $submission['url'] }}" class="btn btn-primary btn-sm"><i class="las la-eye"></i> View</a>
                                            @else
                                                <button type="button" 
                                                    class="btn btn-primary btn-xs view-details-btn" 
                                                    title="View"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#requestDetailsModal"
                                                    data-id="{{ $submission['id'] }}"
                                                    data-type="{{ $submission['type'] }}"
                                                    data-reference="{{ $submission['reference_no'] }}"
                                                    data-submitted-by="{{ auth()->user()->name }}"
                                                    data-date="{{ \Carbon\Carbon::parse($submission['submitted_date'])->timezone('Asia/Manila')->format('M. d, Y') }}"
                                                    data-department="N/A"
                                                    data-description="{{ $submission['detail'] }}"
                                                    data-status="{{ $submission['status'] }}"
                                                    data-original="{{ json_encode($submission['original']) }}"
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

                    <!-- My Approved Requests Tab Content -->
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
                                        <td>
                                            @php
                                                $typeClass = 'type-sales-order';
                                                if($approved['type'] !== 'Sales Order') $typeClass = 'type-job-order';
                                            @endphp
                                            <span class="document-type-badge {{ $typeClass }}">{{ $approved['type'] }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ $approved['reference_no'] }}</strong>
                                            @if($approved['attachment'])
                                                <a href="{{ asset('storage/' . $approved['attachment']) }}" target="_blank" class="ms-1 text-primary" title="View Attachment">
                                                    <i class="las la-paperclip"></i>
                                                </a>
                                            @endif
                                        </td>
                                        <td>{{ $approved['submitted_by'] ?? auth()->user()->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($approved['submitted_date'])->timezone('Asia/Manila')->format('Y-m-d h:i A') }}</td>
                                        <td>
                                            @php
                                                $parts = explode(' - ', $approved['detail'], 2);
                                                $amount = $parts[0];
                                                $desc = $parts[1] ?? '';
                                            @endphp
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold text-dark">{{ $amount }}</span>
                                                @if($desc)
                                                    <small class="text-muted">{{ $desc }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $status = $approved['status'];
                                                $badgeClass = 'status-pending';
                                                if (in_array($status, ['approved', 'completed', 'picking', 'delivered'])) $badgeClass = 'status-success';
                                                elseif (in_array($status, ['rejected', 'cancelled'])) $badgeClass = 'status-danger';
                                                elseif (in_array($status, ['pending_admin_approval', 'pending_director_approval'])) $badgeClass = 'status-info';
                                            @endphp
                                            <span class="status-badge {{ $badgeClass }}">{{ ucwords(str_replace('_', ' ', $status)) }}</span>
                                        </td>
                                        <td>
                                            @if($approved['type'] === 'Sales Order')
                                                <a href="{{ $approved['url'] }}" class="btn btn-primary btn-xs"><i class="las la-eye"></i> View</a>
                                            @else
                                                <button type="button" 
                                                    class="btn btn-primary btn-xs view-details-btn" 
                                                    title="View"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#requestDetailsModal"
                                                    data-id="{{ $approved['id'] }}"
                                                    data-type="{{ $approved['type'] }}"
                                                    data-reference="{{ $approved['reference_no'] }}"
                                                    data-submitted-by="{{ $approved['submitted_by'] ?? auth()->user()->name }}"
                                                    data-date="{{ \Carbon\Carbon::parse($approved['submitted_date'])->timezone('Asia/Manila')->format('M. d, Y') }}"
                                                    data-department="N/A"
                                                    data-description="{{ $approved['detail'] }}"
                                                    data-status="{{ $approved['status'] }}"
                                                    data-original="{{ json_encode($approved['original']) }}">
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
                </div>
            </div>
        </div>
    </div>

    <!-- Global Department / Shared Queue -->
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
                        <button class="queue-btn filter-trigger" onclick="filterQueue(this, 'Job Order')">Job Orders (MIS)</button>
                    </div>

                    <div class="table-responsive">
                        <table id="approvalQueueTable" class="display table table-bordered" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Reference #</th>
                                    <th>Submitted By</th>
                                    <th>Date</th>
                                    <th>Department</th>
                                    <th>Action / Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Unified Queue merging Sales Orders and Job Orders -->
                                @foreach($salesOrders as $order)
                                <tr data-type="Sales Order">
                                    <td><span class="document-type-badge type-sales-order">Sales Order</span></td>
                                    <td><strong>{{ $order->so_number }}</strong></td>
                                    <td>{{ $order->preparedBy->name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($order->created_at)->timezone('Asia/Manila')->format('Y-m-d h:i A') }}</td>
                                    <td>Sales</td>
                                    <td>
                                        @if($order->type === 'complimentary' && $order->status === 'picking')
                                            <a href="{{ route('admin-finance.accounting.ar.prepare', $order->id) }}" class="btn btn-success btn-sm"><i class="las la-file-invoice"></i> Issue AR</a>
                                        @else
                                            <span class="badge badge-warning">Pending Approval</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach

                                @foreach($pendingApprovals as $approval)
                                <tr data-type="Job Order">
                                    <td><span class="document-type-badge type-job-order">{{ $approval['type'] }} Request</span></td>
                                    <td><strong>{{ $approval['reference_no'] }}</strong></td>
                                    <td>{{ $approval['submitted_by'] }}</td>
                                    <td>{{ $approval['submitted_date'] }}</td>
                                    <td>{{ $approval['department'] }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge badge-warning">{{ ucwords(str_replace('_', ' ', $approval['status'])) }}</span>
                                            <button type="button" 
                                                    class="btn btn-primary btn-xs view-details-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#requestDetailsModal"
                                                    data-id="{{ $approval['id'] }}"
                                                    data-type="{{ $approval['type'] }}"
                                                    data-reference="{{ $approval['reference_no'] }}"
                                                    data-submitted-by="{{ $approval['submitted_by'] }}"
                                                    data-date="{{ $approval['submitted_date'] }}"
                                                    data-department="{{ $approval['department'] }}"
                                                    data-description="{{ $approval['full_description'] ?? $approval['description'] }}"
                                                    data-status="{{ $approval['status'] }}"
                                                    data-amount="{{ $approval['amount'] ?? 'N/A' }}"
                                                    data-original="{{ json_encode($approval['original'] ?? []) }}"
                                                    title="Review">
                                                <i class="las la-eye"></i> Review
                                            </button>
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

    <!-- Request Details Modal -->
    <div class="modal fade" id="requestDetailsModal" tabindex="-1" aria-labelledby="requestDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <!-- Header -->
                <div class="modal-header border-0 text-white position-relative" style="background: #dc3545; padding: 1.5rem 2rem;">
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-1" id="requestDetailsModalLabel">
                            <i class="las la-file-alt me-2"></i>Request Details
                        </h5>
                        <p class="mb-0 opacity-75 small" id="modalReferenceHeader"></p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-0">
                    <!-- Key Information Cards -->
                    <div class="p-3" style="background: #f8f9fa;">
                        <div class="row g-2">
                            <!-- Reference Card -->
                            <div class="col-md-6">
                                <div class="info-card p-2 rounded h-100 bg-white border">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-2" style="width: 32px; height: 32px; background: #f8f9fa; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="las la-hashtag" style="font-size: 1.1rem; color: #6c757d;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="text-muted mb-0 d-block" style="font-size: 0.75rem; font-weight: 600;">Reference Number</label>
                                            <p id="modalReference" class="fw-bold mb-0 small" style="color: #212529;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Card -->
                            <div class="col-md-6">
                                <div class="info-card p-2 rounded h-100 bg-white border">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-2" style="width: 32px; height: 32px; background: #f8f9fa; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="las la-info-circle" style="font-size: 1.1rem; color: #6c757d;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="text-muted mb-0 d-block" style="font-size: 0.75rem; font-weight: 600;">Status</label>
                                            <div id="modalStatus"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submitted By Card -->
                            <div class="col-md-6">
                                <div class="info-card p-2 rounded h-100 bg-white border">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-2" style="width: 32px; height: 32px; background: #f8f9fa; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="las la-user" style="font-size: 1.1rem; color: #6c757d;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="text-muted mb-0 d-block" style="font-size: 0.75rem; font-weight: 600;">Submitted By</label>
                                            <p id="modalSubmittedBy" class="mb-0 small fw-semibold" style="color: #212529;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Request Type Card -->
                            <div class="col-md-6">
                                <div class="info-card p-2 rounded h-100 bg-white border">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-2" style="width: 32px; height: 32px; background: #f8f9fa; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="las la-tag" style="font-size: 1.1rem; color: #6c757d;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="text-muted mb-0 d-block" style="font-size: 0.75rem; font-weight: 600;">Request Type</label>
                                            <p id="modalType" class="mb-0 small fw-semibold" style="color: #212529;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Department Card -->
                            <div class="col-md-6">
                                <div class="info-card p-2 rounded h-100 bg-white border">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-2" style="width: 32px; height: 32px; background: #f8f9fa; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="las la-building" style="font-size: 1.1rem; color: #6c757d;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="text-muted mb-0 d-block" style="font-size: 0.75rem; font-weight: 600;">Department</label>
                                            <p id="modalDepartment" class="mb-0 small fw-semibold" style="color: #212529;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Date Card -->
                            <div class="col-md-6">
                                <div class="info-card p-2 rounded h-100 bg-white border">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-2" style="width: 32px; height: 32px; background: #f8f9fa; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="las la-calendar" style="font-size: 1.1rem; color: #6c757d;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="text-muted mb-0 d-block" style="font-size: 0.75rem; font-weight: 600;">Date Submitted</label>
                                            <p id="modalDate" class="mb-0 small fw-semibold" style="color: #212529;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Details Section -->
                    <div class="px-4 pb-4" style="background: #f8f9fa;">
                        <div class="details-section p-4 rounded-3 bg-white border">
                            <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                                <div class="icon-wrapper me-2" style="width: 32px; height: 32px; background: #dc3545; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                    <i class="las la-clipboard-list text-white" style="font-size: 1.1rem;"></i>
                                </div>
                                <h6 class="fw-bold mb-0" style="color: #212529;">Request Details</h6>
                            </div>
                            <div id="modalDescription" class="p-3 rounded-2" style="background: #f8f9fa; min-height: 80px;"></div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer border-top bg-white" style="padding: 1.25rem 2rem;">
                    <div class="d-flex justify-content-between w-100 align-items-center">
                        <button type="button" class="btn btn-light px-4 py-2 fw-semibold border" data-bs-dismiss="modal">
                            <i class="las la-times me-1"></i>Close
                        </button>
                        <div class="d-flex gap-2">
                            <div id="modalRejectWrapper">
                                <form id="rejectForm" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="btn btn-danger px-4 py-2 fw-semibold" id="modalRejectBtn">
                                        <i class="las la-times-circle me-1"></i>Reject
                                    </button>
                                </form>
                            </div>
                            <form id="approveForm" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" id="modalStatusValue" value="Pending HR approval">
                                <button type="submit" class="btn btn-success px-4 py-2 fw-semibold">
                                    <i class="las la-check-circle me-1"></i>Approve Request
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        var queueTable;
        var myApprovalsTable;
        var mySubmissionsTable;
        var myApprovedTable;

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

        function filterQueue(btn, filterValue) {
            $('.filter-trigger').removeClass('active');
            $(btn).addClass('active');

            if (queueTable) {
                if (filterValue === '') {
                    queueTable.search('').columns().search('').draw();
                } else {
                    queueTable.column(0).search(filterValue).draw();
                }
            }
        }

        $(document).ready(function() {
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

            $('#requestDetailsModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                const id = button.attr('data-id');
                const type = button.attr('data-type');
                const reference = button.attr('data-reference');
                const submittedBy = button.attr('data-submitted-by');
                const date = button.attr('data-date');
                const department = button.attr('data-department');
                const description = button.attr('data-description');
                const status = button.attr('data-status') || '';
                
                let original = {};
                try {
                    original = JSON.parse(button.attr('data-original'));
                } catch(e) {}
                const baseUrl = '{{ url('') }}';

                $('#modalReference').text(reference);
                $('#modalReferenceHeader').text(reference);
                $('#modalType').text(type + ' Request');
                $('#modalSubmittedBy').text(submittedBy);
                $('#modalDate').text(date);
                $('#modalDepartment').text(department);
                
                if(status) {
                    const statusConfig = {
                        'pending approval': { badge: 'warning', text: 'Manager Review' },
                        'Pending HR approval': { badge: 'info', text: 'HR Review' },
                        'Pending Final Approval': { badge: 'primary', text: 'Final Review' },
                        'forwarded to accounting': { badge: 'info', text: 'Forwarded to Accounting' },
                        'received': { badge: 'success', text: 'Received' },
                        'completed': { badge: 'success', text: 'Completed' },
                        'rejected': { badge: 'danger', text: 'Rejected' },
                        'Pending AR': { badge: 'warning', text: 'Awaiting AR' },
                        'pending_supervisor_approval': { badge: 'warning', text: 'Manager Review' },
                        'pending_admin_approval': { badge: 'info', text: 'Finance Review (2nd Approval)' },
                        'pending_director_approval': { badge: 'primary', text: 'Final Review (Director)' }
                    };
                    const config = statusConfig[status] || statusConfig[status.toLowerCase()] || { 
                        badge: 'secondary', 
                        text: status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) 
                    };
                    $('#modalStatus').html(`<span class="badge badge-${config.badge}">${config.text}</span>`);
                }

                // --- Dynamic Field Rendering ---
                const fieldLabels = {
                    'amount': 'Requested Amount',
                    'purpose': 'Purpose / Justification',
                    'date_needed': 'Date Needed',
                    'date_of_incident': 'Date of Incident',
                    'time_of_incident': 'Time of Incident',
                    'hardcopy': 'Hardcopy Required',
                    'viewing': 'Viewing Required',
                    'request_date': 'Request Date',
                    'request_details': 'Request Details',
                    'request_type': 'Request Classification',
                    'customer_item_name': 'Customer Item Name',
                    'repayment_method': 'Repayment Method',
                    'is_liquidation_required': 'Liquidation Required',
                    'total_amount': 'Total Amount',
                    'remarks': 'Remarks / Notes',
                    'employee_name': 'Staff Name',
                    'employee_number': 'Employee ID',
                    'jv_number': 'Journal Voucher No.',
                    'date': 'Voucher Date',
                    'reason': 'Adjustment Reason',
                    'category': 'Voucher Category'
                };

                const excludedFields = [
                    'id', 'user_id', 'created_at', 'updated_at', 'status',
                    'approved_by_manager', 'manager_approved_at',
                    'approved_by_admin', 'admin_approved_at',
                    'approved_by_hr', 'hr_approved_at',
                    'approved_by_director', 'director_approved_at',
                    'rejected_by', 'rejected_at', 'rejection_reason',
                    'cctv_req_id', 'material_req_id', 'qb_req_id', 'service_req_id', 'undertime_req_id',
                    'prepared_by', 'signed_by_af_manager', 'customer_id', 'requested_by', 'requestor', 'items'
                ];

                let descriptionHtml = `<div class="table-responsive"><table class="table table-sm table-borderless mb-0"><tbody>`;
                
                // Add Amount prominently if it exists
                if (original.amount || original.total_amount) {
                    const amt = original.amount || original.total_amount;
                    descriptionHtml += `
                        <tr class="border-bottom">
                            <th class="py-2 text-muted small px-0" style="width: 35%;">AMOUNT</th>
                            <td class="py-2 fw-bold text-primary fs-15 text-end px-0">PhP ${parseFloat(amt).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                        </tr>`;
                }

                // Iterate through database fields
                Object.keys(original).forEach(key => {
                    const val = original[key];
                    if (!excludedFields.includes(key) && val !== null && val !== undefined && key !== 'amount' && key !== 'total_amount') {
                        let displayVal = val;

                        // Attachment handling: render a button instead of raw path
                        if (key === 'supporting_documents' || key === 'attachment') {
                            if (key === 'supporting_documents' && type === 'JV Request') {
                                // Use internal download route for JV Requests
                                displayVal = `<a href="${baseUrl}/admin-finance/credit-collection/jv-requests/${id}/download-supporting" class="btn btn-outline-primary btn-xxs"><i class="las la-download"></i> Download</a>`;
                            } else {
                                // Generic files saved in storage
                                displayVal = `<a href="${baseUrl}/storage/${val}" target="_blank" class="text-primary"><i class="las la-paperclip"></i> View</a>`;
                            }
                        }

                        // Formatting logic for booleans
                        if ((typeof val === 'boolean' || val === 1 || val === 0) && !(key === 'supporting_documents' || key === 'attachment')) {
                            if (key.includes('is_') || key === 'hardcopy' || key === 'viewing') {
                                displayVal = val ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-light text-dark">No</span>';
                            }
                        }

                        descriptionHtml += `
                            <tr>
                                <th class="py-1 text-muted small fw-semibold px-0" style="width: 35%;">${fieldLabels[key] || key.replace(/_/g, ' ').toUpperCase()}</th>
                                <td class="py-1 text-dark fs-13 text-end px-0">${displayVal}</td>
                            </tr>`;
                    }
                });

                // Special handling for nested items (e.g., QB Requests, JV Requests)
                if (type === 'QB' && original.items && original.items.length > 0) {
                    descriptionHtml += `</tbody></table></div>
                        <div class="mt-3 pt-2 border-top">
                            <h6 class="fw-bold fs-12 text-muted mb-2">COMPARISON DETAILS</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0 small">
                                    <thead class="table-light"><tr><th>From</th><th>To</th></tr></thead>
                                    <tbody>`;
                    original.items.forEach(item => {
                        descriptionHtml += `<tr><td>${item.from_value}</td><td>${item.to_value}</td></tr>`;
                    });
                    descriptionHtml += `</tbody></table></div></div>`;
                } else if (type === 'JV' && original.items && original.items.length > 0) {
                    descriptionHtml += `</tbody></table></div>
                        <div class="mt-3 pt-2 border-top">
                            <h6 class="fw-bold fs-12 text-muted mb-2">LINE ITEMS SUMMARY</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0 small">
                                    <thead class="table-light"><tr><th>Entity</th><th>Ref</th><th>Amount</th></tr></thead>
                                    <tbody>`;
                    original.items.forEach(item => {
                        descriptionHtml += `<tr><td>${item.customer_name}</td><td>${item.reference_no}</td><td class="text-end">₱${parseFloat(item.amount).toLocaleString(undefined, {minimumFractionDigits: 2})}</td></tr>`;
                    });
                    descriptionHtml += `</tbody></table></div></div>`;
                } else {
                    descriptionHtml += `</tbody></table></div>`;
                }
                
                $('#modalDescription').html(descriptionHtml);

                // Actions
                let approveUrl, rejectUrl;
                let approveStatus = 'approved'; 
                let method = 'PUT';

                if (type === 'Sales Order') {
                    approveUrl = `{{ url('admin-finance/sales-order') }}/${id}/approve`;
                    rejectUrl = `{{ url('admin-finance/sales-order') }}/${id}/reject`;
                    method = 'POST';
                } else if (type === 'CCTV') {
                    approveUrl = rejectUrl = `{{ url('admin-finance/mis/cctv-requests') }}/${id}`;
                    const nextStatusMap = { 'pending approval': 'Pending HR approval', 'Pending HR approval': 'Pending Final Approval', 'Pending Final Approval': 'completed' };
                    approveStatus = nextStatusMap[status] || 'completed';
                } else if (type === 'Material') {
                    approveUrl = rejectUrl = `{{ url('admin-finance/mis/material-requests') }}/${id}`;
                    const nextStatusMap = { 'pending approval': 'Pending Final Approval', 'Pending Final Approval': 'forwarded to accounting' };
                    approveStatus = nextStatusMap[status] || 'completed';
                } else if (type === 'Cash Advance') {
                    approveUrl = rejectUrl = `{{ url('employee/cash-advance') }}/${id}`;
                    const nextStatusMap = { 
                        'pending_supervisor_approval': 'pending_admin_approval', 
                        'pending_admin_approval': 'pending_director_approval',
                        'pending_director_approval': 'approved'
                    };
                    approveStatus = nextStatusMap[status] || 'approved';
                } else if (type === 'JV Request' || type === 'JV') {
                    // JV Requests live under Credit & Collection routes
                    approveUrl = `{{ url('admin-finance/credit-collection/jv-requests') }}/${id}/approve`;
                    rejectUrl = `{{ url('admin-finance/credit-collection/jv-requests') }}/${id}/reject`;
                    method = 'PUT';

                    // If awaiting manager approval, use the manager-approve endpoint
                    if (status === 'pending_manager_approval') {
                        approveUrl = `{{ url('admin-finance/credit-collection/jv-requests') }}/${id}/manager-approve`;
                    }
                    // default approve status for JV flows
                    approveStatus = 'approved';
                } else {
                    approveUrl = rejectUrl = `{{ url('admin-finance/mis') }}/${type.toLowerCase()}-requests/${id}`;
                    method = 'PUT';
                }

                $('#approveForm').attr('action', approveUrl).show();
                $('#rejectForm').attr('action', rejectUrl).show();
                $('#modalStatusValue').val(approveStatus);
                $('input[name="_method"]', '#approveForm').val(method);
                $('input[name="_method"]', '#rejectForm').val(method);
                
                var viewOnly = button.attr('data-view-only') === 'true';
                if (viewOnly || status === 'rejected' || status === 'completed') {
                    $('#approveForm').hide();
                    $('#rejectForm').hide();
                } else {
                    $('#approveForm').show();
                    $('#rejectForm').show();
                }
            });
        });
    </script>
    @endpush
</x-app-layout>

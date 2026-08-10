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
            <h3 id="pendingCount">{{ $salesOrders->count() + $pendingCashAdvances->count() + $pendingTransfers->count() + $pendingCctvRequests->count() }}</h3>
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
                $recentTransfers = $pendingTransfers->where('created_at', '>=', now()->startOfDay())->count();
                $recentCctv = $pendingCctvRequests->where('created_at', '>=', now()->startOfDay())->count();
                $recentTotal = $recentSales + $recentCash + $recentTransfers + $recentCctv;
            @endphp
            <h3 id="recentCount">{{ $recentTotal }}</h3>
            <p>Received Today</p>
        </div>
        <div class="stat-card total">
            <h3 id="totalCount">{{ $salesOrders->count() + $pendingCashAdvances->count() + $pendingTransfers->count() + $pendingCctvRequests->count() }}</h3>
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
                                        <th>Reference #</th>
                                        <th>Customer Name</th>
                                        <th>Submitted By</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Attachment</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salesOrders as $order)
                                    <tr>
                                        <td><span class="document-type-badge type-sales-order">Sales Order</span></td>
                                        <td><strong>{{ $order->so_number }}</strong></td>
                                        <td>{{ $order->customer?->customer_name ?? ($order->customer_representative ?: 'N/A') }}</td>
                                        <td>{{ $order->preparedBy->name ?? 'N/A' }}</td>
                                        <td>{{ optional($order->created_at)->format('Y-m-d h:i A') }}</td>
                                        <td>₱{{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                            @if($order->attachment)
                                                <a href="/storage/{{ $order->attachment }}" target="_blank" class="text-primary mb-1 d-block"><i class="las la-paperclip"></i> PO</a>
                                            @endif
                                            @if($order->proof_of_payment)
                                                <a href="/storage/{{ $order->proof_of_payment }}" target="_blank" class="text-success d-block"><i class="las la-paperclip"></i> Payment</a>
                                            @endif
                                            @if(!$order->attachment && !$order->proof_of_payment)
                                                <span class="text-muted">None</span>
                                            @endif
                                        </td>
                                        <td><span class="status-badge status-pending">Pending Approval</span></td>
                                        <td>
                                            <a href="{{ route('marketing.sales-orders.detail', $order->id) }}" class="btn btn-primary btn-sm"><i class="las la-eye"></i> Review</a>
                                        </td>
                                    </tr>
                                    @endforeach

                                    @foreach($pendingCashAdvances as $advance)
                                    <tr>
                                        <td><span class="document-type-badge badge-info" style="background-color: #e3f2fd; color: #0d47a1;">Cash Advance</span></td>
                                        <td><strong>CA-{{ str_pad($advance->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                        <td><span class="text-muted">N/A</span></td>
                                        <td>{{ $advance->user->name ?? $advance->employee_name }}</td>
                                        <td>{{ optional($advance->created_at)->format('Y-m-d h:i A') }}</td>
                                        <td>₱{{ number_format($advance->amount, 2) }}</td>
                                        <td><span class="text-muted">None</span></td>
                                        <td><span class="status-badge status-pending">Pending Manager</span></td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-primary btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#cashAdvanceApprovalModal"
                                                    data-id="{{ $advance->id }}"
                                                    data-name="{{ $advance->user->name ?? $advance->employee_name }}"
                                                    data-amount="₱{{ number_format($advance->amount, 2) }}"
                                                    data-purpose="{{ $advance->purpose }}"
                                                    data-date-needed="{{ optional($advance->date_needed)->format('M d, Y') }}">
                                                <i class="las la-eye"></i> Review
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach

                                    @foreach($pendingTransfers as $transfer)
                                    <tr>
                                        <td><span class="document-type-badge" style="background-color: #d4edda; color: #155724;">Stock Transfer</span></td>
                                        <td><strong>ST-{{ str_pad($transfer->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                        <td><span class="text-muted">N/A</span></td>
                                        <td>{{ $transfer->fromSite->name ?? 'N/A' }}</td>
                                        <td>{{ optional($transfer->created_at)->format('Y-m-d h:i A') }}</td>
                                        <td>{{ $transfer->quantity }} units</td>
                                        <td><span class="text-muted">None</span></td>
                                        <td><span class="status-badge status-pending">Pending Approval</span></td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-primary btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#stockTransferApprovalModal"
                                                    data-id="{{ $transfer->id }}"
                                                    data-from-site="{{ $transfer->fromSite->name }}"
                                                    data-to-site="{{ $transfer->toSite->name }}"
                                                    data-book-name="{{ $transfer->item_name }}"
                                                    data-quantity="{{ $transfer->quantity }}">
                                                <i class="las la-eye"></i> Review
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach

                                    @foreach($pendingCctvRequests as $req)
                                    <tr>
                                        <td><span class="document-type-badge type-job-order">CCTV</span></td>
                                        <td><strong>CCTV-{{ str_pad($req->cctv_req_id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                                        <td><span class="text-muted">N/A</span></td>
                                        <td>{{ $req->user->name ?? $req->requested_by }}</td>
                                        <td>{{ optional($req->created_at)->format('Y-m-d h:i A') }}</td>
                                        <td>N/A</td>
                                        <td>
                                            @if($req->attachment)
                                                <a href="/storage/{{ $req->attachment }}" target="_blank" class="text-primary"><i class="las la-paperclip"></i> View</a>
                                            @else
                                                <span class="text-muted">None</span>
                                            @endif
                                        </td>
                                        <td><span class="status-badge status-pending">Pending Manager</span></td>
                                        <td>
                                            <form action="{{ route('user.cctv-requests.update', $req->cctv_req_id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="Pending HR approval">
                                                <button type="submit" class="btn btn-success btn-sm"><i class="las la-check"></i> Approve</button>
                                            </form>
                                            <form action="{{ route('user.cctv-requests.update', $req->cctv_req_id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="btn btn-danger btn-sm"><i class="las la-times"></i> Reject</button>
                                            </form>
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
                                        <th>Customer Name</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mySubmissions as $submission)
                                    <tr>
                                        <td>
                                            @php
                                                $typeClass = $submission->type === 'Sales Order' ? 'type-sales-order' : 'badge-info';
                                            @endphp
                                            <span class="document-type-badge {{ $typeClass }}">{{ $submission->type }}</span>
                                        </td>
                                        <td><strong>{{ $submission->reference_no }}</strong></td>
                                        <td>{{ $submission->original->customer?->customer_name ?? ($submission->original->customer_representative ?? 'N/A') }}</td>
                                        <td>
                                            @php
                                                if (isset($submission->submitted_date) && $submission->submitted_date instanceof \Carbon\Carbon) {
                                                    echo $submission->submitted_date->format('Y-m-d h:i A');
                                                } elseif (is_string($submission->submitted_date) && $submission->submitted_date) {
                                                    echo $submission->submitted_date;
                                                } else {
                                                    echo '';
                                                }
                                            @endphp
                                        </td>
                                        <td>₱{{ number_format($submission->amount, 2) }}</td>
                                        <td>
                                            @php
                                                $status = $submission->status;
                                                $badgeClass = 'badge-warning';
                                                if (in_array($status, ['approved', 'completed', 'picking', 'delivered'])) $badgeClass = 'badge-success';
                                                elseif (in_array($status, ['rejected', 'cancelled'])) $badgeClass = 'badge-danger';
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ ucwords(str_replace('_', ' ', $status)) }}</span>
                                        </td>
                                        <td>
                                            @if($submission->type === 'Sales Order')
                                                <a href="{{ $submission->url }}" class="btn btn-primary btn-sm"><i class="las la-eye"></i> View</a>
                                            @else
                                                <button type="button" 
                                                    class="btn btn-primary btn-xs"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#cashAdvanceApprovalModal"
                                                data-id="{{ $submission->id }}"
                                                data-name="{{ auth()->user()->name }}"
                                                data-amount="₱{{ number_format($submission->amount, 2) }}"
                                                data-purpose="{{ $submission->original->purpose }}"
                                                data-date-needed="{{ optional($submission->original->date_needed)->format('M d, Y') }}"
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
                                        <th>Customer Name</th>
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
                                        <td>{{ $approved->original->customer?->customer_name ?? ($approved->original->customer_representative ?? 'N/A') }}</td>
                                        <td>{{ $approved->submitted_by }}</td>
                                        <td>
                                            @php
                                                if (isset($approved->submitted_date) && $approved->submitted_date instanceof \Carbon\Carbon) {
                                                    echo $approved->submitted_date->format('Y-m-d h:i A');
                                                } elseif (is_string($approved->submitted_date) && $approved->submitted_date) {
                                                    echo $approved->submitted_date;
                                                } else {
                                                    echo '';
                                                }
                                            @endphp
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold text-dark">₱{{ number_format($approved->amount, 2) }}</span>
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
                                            <button type="button" class="btn btn-primary btn-xs" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#cashAdvanceApprovalModal" 
                                                data-id="{{ $approved->id }}" 
                                                data-name="{{ $approved->submitted_by }}" 
                                                data-amount="₱{{ number_format($approved->amount, 2) }}" 
                                                data-purpose="{{ $approved->original->purpose }}" 
                                                data-date-needed="{{ optional($approved->original->date_needed)->format('M d, Y') }}"
                                                data-status="{{ $approved->status }}" 
                                                data-reference="{{ $approved->reference_no }}">
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
                        <button class="queue-btn filter-trigger" onclick="filterQueue(this, 'Stock Transfer')">Stock Transfers</button>
                    </div>

                    <div class="table-responsive">
                        <table id="approvalQueueTable" class="display table table-bordered" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Ref #</th>
                                    <th>Customer Name</th>
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
                                    <td>{{ $order->customer?->customer_name ?? ($order->customer_representative ?: 'N/A') }}</td>
                                    <td>{{ $order->preparedBy->name ?? 'N/A' }}</td>
                                    <td>{{ $order->created_at->format('Y-m-d h:i A') }}</td>
                                    <td>₱{{ number_format($order->total_amount, 2) }}</td>
                                    <td><span class="status-badge status-pending">Pending Approval</span></td>
                                    <td>
                                        <a href="{{ route('marketing.sales-orders.detail', $order->id) }}" class="btn btn-primary btn-sm"><i class="las la-eye"></i> Review</a>
                                    </td>
                                </tr>
                                @endforeach

                                @foreach($pendingCashAdvances as $advance)
                                <tr data-type="Cash Advance">
                                    <td><span class="document-type-badge badge-info" style="background-color: #e3f2fd; color: #0d47a1;">Cash Advance</span></td>
                                    <td><strong>CA-{{ str_pad($advance->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                    <td><span class="text-muted">N/A</span></td>
                                    <td>{{ $advance->user->name ?? $advance->employee_name }}</td>
                                    <td>{{ $advance->created_at->format('Y-m-d h:i A') }}</td>
                                    <td>₱{{ number_format($advance->amount, 2) }}</td>
                                    <td><span class="status-badge status-pending">Pending Manager</span></td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-primary btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#cashAdvanceApprovalModal"
                                                data-id="{{ $advance->id }}"
                                                data-name="{{ $advance->user->name ?? $advance->employee_name }}"
                                                data-amount="₱{{ number_format($advance->amount, 2) }}"
                                                data-purpose="{{ $advance->purpose }}"
                                                data-date-needed="{{ $advance->date_needed->format('M d, Y') }}"
                                                data-original="{{ json_encode($advance) }}">
                                            <i class="las la-eye"></i> Review
                                        </button>
                                    </td>
                                </tr>
                                @endforeach

                                @foreach($pendingTransfers as $transfer)
                                <tr data-type="Stock Transfer">
                                    <td><span class="document-type-badge" style="background-color: #d4edda; color: #155724;">Stock Transfer</span></td>
                                    <td><strong>ST-{{ str_pad($transfer->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                    <td><span class="text-muted">N/A</span></td>
                                    <td>{{ $transfer->fromSite->name ?? 'N/A' }}</td>
                                    <td>{{ $transfer->created_at->format('Y-m-d h:i A') }}</td>
                                    <td>{{ $transfer->quantity }} units</td>
                                    <td><span class="status-badge status-pending">Pending Approval</span></td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-primary btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#stockTransferApprovalModal"
                                                data-id="{{ $transfer->id }}"
                                                data-from-site="{{ $transfer->fromSite->name }}"
                                                data-to-site="{{ $transfer->toSite->name }}"
                                                data-book-name="{{ $transfer->item_name }}"
                                                data-quantity="{{ $transfer->quantity }}">
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

    </div>

    <!-- Cash Advance Approval Modal -->
    <div class="modal fade" id="cashAdvanceApprovalModal" tabindex="-1" aria-labelledby="cashAdvanceApprovalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <!-- Header -->
                <div class="modal-header border-0 text-white position-relative" style="background: #dc3545; padding: 1.5rem 2rem;">
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-1" id="cashAdvanceApprovalModalLabel">
                            <i class="las la-file-invoice-dollar me-2"></i>Cash Advance Details
                        </h5>
                        <p class="mb-0 opacity-75 small" id="ca-modal-reference-header"></p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-0">
                    <!-- Key Information Cards -->
                    <div class="p-3" style="background: #f8f9fa;">
                        <div class="row g-2">
                            <!-- Employee Card -->
                            <div class="col-md-6">
                                <div class="info-card p-2 rounded h-100 bg-white border">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-2" style="width: 32px; height: 32px; background: #f8f9fa; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="las la-user" style="font-size: 1.1rem; color: #6c757d;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="text-muted mb-0 d-block" style="font-size: 0.75rem; font-weight: 600;">Employee Name</label>
                                            <p id="ca-modal-name" class="fw-bold mb-0 small" style="color: #212529;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Amount Card -->
                            <div class="col-md-6">
                                <div class="info-card p-2 rounded h-100 bg-white border">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-2" style="width: 32px; height: 32px; background: #f8f9fa; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="las la-money-bill-wave" style="font-size: 1.1rem; color: #6c757d;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="text-muted mb-0 d-block" style="font-size: 0.75rem; font-weight: 600;">Amount Requested</label>
                                            <p id="ca-modal-amount" class="fw-bold mb-0 small text-primary"></p>
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
                                            <label class="text-muted mb-0 d-block" style="font-size: 0.75rem; font-weight: 600;">Date Needed</label>
                                            <p id="ca-modal-date" class="fw-bold mb-0 small" style="color: #212529;"></p>
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
                                            <label class="text-muted mb-0 d-block" style="font-size: 0.75rem; font-weight: 600;">Current Status</label>
                                            <div id="ca-modal-status"></div>
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
                            <div id="ca-modal-details" class="p-3 rounded-2" style="background: #f8f9fa; min-height: 80px;"></div>
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
                            <form action="" method="POST" id="ca-reject-form" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="rejected">
                                <button type="button" class="btn btn-danger px-4 py-2 fw-semibold" onclick="toggleRejection()">
                                    <i class="las la-times-circle me-1"></i>Reject
                                </button>
                                <div id="rejection-reason-container" class="mt-2" style="display:none; position: absolute; background: white; border: 1px solid #ccc; padding: 15px; z-index: 1000; width: 300px; bottom: 80px; right: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                    <label class="fw-bold mb-2 small">Reason for Rejection:</label>
                                    <textarea name="rejection_reason" class="form-control mb-3" rows="3" placeholder="Enter reason..."></textarea>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-danger btn-sm flex-grow-1">Confirm Reject</button>
                                        <button type="button" class="btn btn-light btn-sm flex-grow-1 border" onclick="toggleRejection()">Cancel</button>
                                    </div>
                                </div>
                            </form>
                            
                            <form action="" method="POST" id="ca-approve-form" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="pending_admin_approval">
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

    <!-- Stock Transfer Approval Modal -->
    <div class="modal fade" id="stockTransferApprovalModal" tabindex="-1" aria-labelledby="stockTransferApprovalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <!-- Header -->
                <div class="modal-header border-0 text-white position-relative" style="background: #28a745; padding: 1.5rem 2rem;">
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-1" id="stockTransferApprovalModalLabel">
                            <i class="las la-exchange-alt me-2"></i>Stock Transfer Approval
                        </h5>
                        <p class="mb-0 opacity-75 small" id="st-modal-reference-header"></p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body p-4">
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-uppercase">From Site:</label>
                                <div class="p-2 bg-light rounded">
                                    <span id="st-from-site" class="fw-bold text-dark"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-uppercase">To Site:</label>
                                <div class="p-2 bg-light rounded">
                                    <span id="st-to-site" class="fw-bold text-dark"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-uppercase">Book:</label>
                                <div class="p-2 bg-light rounded">
                                    <span id="st-book-name" class="fw-bold text-dark"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-uppercase">Quantity:</label>
                                <div class="p-2 bg-light rounded">
                                    <span id="st-quantity" class="fw-bold text-success" style="font-size: 1.2rem;"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info small">
                        <i class="las la-info-circle me-2"></i>
                        <strong>Note:</strong> This transfer will be automatically processed when approved. The stock will be deducted from the "From Site" and added to the "To Site".
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer border-top bg-white" style="padding: 1.25rem 2rem;">
                    <div class="d-flex justify-content-between w-100 align-items-center">
                        <button type="button" class="btn btn-light px-4 py-2 fw-semibold border" data-bs-dismiss="modal">
                            <i class="las la-times me-1"></i>Close
                        </button>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-danger px-4 py-2 fw-semibold" onclick="toggleStRejection()" id="st-reject-btn">
                                <i class="las la-times-circle me-1"></i>Reject
                            </button>
                            <div id="st-rejection-reason-container" class="mt-2" style="display:none; position: absolute; background: white; border: 1px solid #ccc; padding: 15px; z-index: 1000; width: 300px; bottom: 80px; right: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                <label class="fw-bold mb-2 small">Reason for Rejection:</label>
                                <textarea name="rejection_reason" class="form-control mb-3" rows="3" placeholder="Enter reason..."></textarea>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-danger btn-sm flex-grow-1" id="st-confirm-reject-btn">Confirm Reject</button>
                                    <button type="button" class="btn btn-light btn-sm flex-grow-1 border" onclick="toggleStRejection()">Cancel</button>
                                </div>
                            </div>
                            
                            <button type="button" class="btn btn-success px-4 py-2 fw-semibold" id="st-approve-btn">
                                <i class="las la-check-circle me-1"></i>Approve Transfer
                            </button>
                        </div>
                    </div>
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
            if (tabId === 'my-approved' && myApprovedTable) myApprovedTable.columns.adjust().draw();
            if (tabId === 'my-approvals' && myApprovalsTable) myApprovalsTable.columns.adjust().draw();
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
                pageLength: 10,
                columnDefs: [{ orderable: false, targets: -1 }]
            });

            myApprovalsTable = $('#myApprovalsTable').DataTable({
                order: [[3, 'desc']],
                pageLength: 10,
                columnDefs: [{ orderable: false, targets: -1 }]
            });

            mySubmissionsTable = $('#mySubmissionsTable').DataTable({
                order: [[2, 'desc']],
                pageLength: 10,
                columnDefs: [{ orderable: false, targets: -1 }]
            });

            myApprovedTable = $('#myApprovedTable').DataTable({
                order: [[3, 'desc']],
                pageLength: 10,
                columnDefs: [{ orderable: false, targets: -1 }]
            });

            // Modal Population
            $('#cashAdvanceApprovalModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var name = button.data('name');
                var amount = button.data('amount');
                var purpose = button.data('purpose');
                var dateNeeded = button.data('date-needed');
                var status = button.data('status') || 'pending_supervisor_approval';
                var reference = 'CA-' + String(id).padStart(5, '0');

                var modal = $(this);
                modal.find('#ca-modal-name').text(name);
                modal.find('#ca-modal-amount').text(amount);
                modal.find('#ca-modal-date').text(dateNeeded);
                modal.find('#ca-modal-reference-header').text(reference);

                let original = {};
                try {
                    original = typeof button.data('original') === 'string' ? JSON.parse(button.data('original')) : button.data('original');
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
                
                // Hide actions if view-only, completed, or rejected
                var viewOnly = button.data('view-only') === true || button.data('view-only') === 'true';
                if (viewOnly || status === 'approved' || status === 'rejected') {
                    modal.find('#ca-approve-form, #ca-reject-form').hide();
                } else {
                    modal.find('#ca-approve-form, #ca-reject-form').show();
                }

                // Reset rejection container
                $('#rejection-reason-container').hide();
            });

            // Stock Transfer Modal Population
            $('#stockTransferApprovalModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var fromSite = button.data('from-site');
                var toSite = button.data('to-site');
                var bookName = button.data('book-name');
                var quantity = button.data('quantity');
                var reference = 'ST-' + String(id).padStart(5, '0');

                var modal = $(this);
                modal.find('#st-from-site').text(fromSite);
                modal.find('#st-to-site').text(toSite);
                modal.find('#st-book-name').text(bookName);
                modal.find('#st-quantity').text(quantity + ' units');
                modal.find('#st-modal-reference-header').text(reference);

                // Store transfer ID for button handlers
                modal.data('transfer-id', id);

                // Reset rejection container
                $('#st-rejection-reason-container').hide();

                // Clear rejection reason textarea
                $('textarea[name="rejection_reason"]').val('');
            });

            // Stock Transfer Approve Button Handler
            $(document).on('click', '#st-approve-btn', function() {
                var transferId = $('#stockTransferApprovalModal').data('transfer-id');
                if (transferId) {
                    approveStockTransfer(transferId);
                }
            });

            // Stock Transfer Confirm Reject Button Handler
            $(document).on('click', '#st-confirm-reject-btn', function() {
                var transferId = $('#stockTransferApprovalModal').data('transfer-id');
                if (transferId) {
                    rejectStockTransfer(transferId);
                }
            });
        });

        function toggleRejection() {
            var container = $('#rejection-reason-container');
            container.toggle();
        }

        function toggleStRejection() {
            var container = $('#st-rejection-reason-container');
            container.toggle();
        }

        // Stock Transfer Approval AJAX Handler
        function approveStockTransfer(transferId) {
            $.ajax({
                url: '/stock-transfers/' + transferId + '/approve',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#stockTransferApprovalModal').modal('hide');
                        location.reload(); // Refresh page to update queue
                    }
                },
                error: function(xhr) {
                    let message = 'Error approving transfer';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    alert(message);
                }
            });
        }2

        // Stock Transfer Rejection AJAX Handler
        function rejectStockTransfer(transferId) {
            var reason = $('textarea[name="rejection_reason"]').val();
            $.ajax({
                url: '/stock-transfers/' + transferId + '/reject',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    'rejection_reason': reason
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#stockTransferApprovalModal').modal('hide');
                        location.reload(); // Refresh page to update queue
                    }
                },
                error: function(xhr) {
                    let message = 'Error rejecting transfer';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    alert(message);
                }
            });
        }
    </script>
    @endpush
</x-app-layout>

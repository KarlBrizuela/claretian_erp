<x-app-layout title="GSD Job Orders" role="Finance Manager" sidebar="admin-finance">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Job Order Approvals</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin-finance.gsd.job-orders', ['status' => 'all']) }}" class="btn btn-sm btn-{{ $currentStatus == 'all' ? 'primary' : 'outline-primary' }}">All</a>
                        <a href="{{ route('admin-finance.gsd.job-orders', ['status' => 'approved']) }}" class="btn btn-sm btn-{{ $currentStatus == 'approved' ? 'success' : 'outline-success' }}">Approved</a>
                        <a href="{{ route('admin-finance.gsd.job-orders', ['status' => 'ongoing']) }}" class="btn btn-sm btn-{{ $currentStatus == 'ongoing' ? 'info' : 'outline-info' }}">Ongoing</a>
                        <a href="{{ route('admin-finance.gsd.job-orders', ['status' => 'on_hold']) }}" class="btn btn-sm btn-{{ $currentStatus == 'on_hold' ? 'warning' : 'outline-warning' }}">On Hold</a>
                        <a href="{{ route('admin-finance.gsd.job-orders', ['status' => 'completed']) }}" class="btn btn-sm btn-{{ $currentStatus == 'completed' ? 'secondary' : 'outline-secondary' }}">Completed</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="gsdJobOrderTable" class="table table-bordered table-striped" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>Reference No.</th>
                                    <th>Type</th>
                                    <th>Requested By</th>
                                    <th>Date</th>
                                    <th>Details</th>
                                    <th>Approved By</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($qbRequests as $req)
                                <tr>
                                    <td><strong>QB-{{ str_pad($req->qb_req_id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                                    <td><span class="badge badge-primary">QB Request</span></td>
                                    <td>{{ $req->user->name ?? 'Unknown' }}</td>
                                    <td>{{ $req->created_at->format('M d, Y') }}</td>
                                    <td>{{ $req->customer_item_name }}</td>
                                    <td>{{ $req->approver->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $req->status === 'on_hold' ? 'warning' : ($req->status === 'ongoing' ? 'info' : ($req->status === 'completed' ? 'success' : 'secondary')) }} light">{{ ucfirst(str_replace('_', ' ', $req->status)) }}</span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-primary shadow btn-xs sharp view-details-btn"
                                            data-bs-toggle="modal" data-bs-target="#viewModalQB{{ $req->qb_req_id }}" title="View & Update">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                
                                <!-- QB Modal -->
                                <div class="modal fade" id="viewModalQB{{ $req->qb_req_id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">QB Request Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <p><strong>Reference:</strong> QB-{{ str_pad($req->qb_req_id, 4, '0', STR_PAD_LEFT) }}</p>
                                                        <p><strong>Requested By:</strong> {{ $req->user->name ?? 'Unknown' }}</p>
                                                        <p><strong>Date Submitted:</strong> {{ $req->created_at->format('M d, Y') }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><strong>Status:</strong> <span class="badge badge-{{ $req->status === 'on_hold' ? 'warning' : ($req->status === 'ongoing' ? 'info' : ($req->status === 'completed' ? 'success' : 'secondary')) }}">{{ ucfirst(str_replace('_', ' ', $req->status)) }}</span></p>
                                                        <p><strong>Approved By:</strong> {{ $req->approver->name ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                                <div class="mb-2"><strong>Customer Item:</strong> {{ $req->customer_item_name }}</div>
                                                @if($req->items && $req->items->count() > 0)
                                                <h6 class="mt-3">Changes:</h6>
                                                <table class="table table-bordered table-sm">
                                                    <thead><tr><th>From</th><th>To</th></tr></thead>
                                                    <tbody>
                                                        @foreach($req->items as $item)
                                                        <tr><td>{{ $item->from_value }}</td><td>{{ $item->to_value }}</td></tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                @endif

                                                <hr>
                                                <h6 class="mb-3">Update Status</h6>
                                                <form action="{{ route('admin-finance.gsd.job-orders.update-status', ['type' => 'qb', 'id' => $req->qb_req_id]) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="d-flex gap-2">
                                                        <button type="submit" name="status" value="ongoing" class="btn btn-info btn-sm" {{ $req->status === 'ongoing' ? 'disabled' : '' }}>Mark as Ongoing</button>
                                                        <button type="submit" name="status" value="on_hold" class="btn btn-warning btn-sm" {{ $req->status === 'on_hold' ? 'disabled' : '' }}>Mark On Hold</button>
                                                        <button type="submit" name="status" value="completed" class="btn btn-success btn-sm">Mark as Completed</button>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                @foreach($undertimeRequests as $req)
                                <tr>
                                    <td><strong>UND-{{ str_pad($req->undertime_req_id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                                    <td><span class="badge badge-warning">Undertime</span></td>
                                    <td>{{ $req->employee_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($req->date)->format('M d, Y') }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($req->reason, 30) }}</td>
                                    <td>{{ $req->approver->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $req->status === 'on_hold' ? 'warning' : ($req->status === 'ongoing' ? 'info' : ($req->status === 'completed' ? 'success' : 'secondary')) }} light">{{ ucfirst(str_replace('_', ' ', $req->status)) }}</span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-primary shadow btn-xs sharp view-details-btn"
                                            data-bs-toggle="modal" data-bs-target="#viewModalUndertime{{ $req->undertime_req_id }}" title="View & Update">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Undertime Modal -->
                                <div class="modal fade" id="viewModalUndertime{{ $req->undertime_req_id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Undertime Request Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <p><strong>Reference:</strong> UND-{{ str_pad($req->undertime_req_id, 4, '0', STR_PAD_LEFT) }}</p>
                                                        <p><strong>Employee:</strong> {{ $req->employee_name }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><strong>Status:</strong> <span class="badge badge-{{ $req->status === 'on_hold' ? 'warning' : ($req->status === 'ongoing' ? 'info' : ($req->status === 'completed' ? 'success' : 'secondary')) }}">{{ ucfirst(str_replace('_', ' ', $req->status)) }}</span></p>
                                                        <p><strong>Approved By:</strong> {{ $req->approver->name ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                                <div class="mb-2"><strong>Date:</strong> {{ $req->date }}</div>
                                                <div class="mb-2"><strong>Time:</strong> {{ $req->time_from }} - {{ $req->time_to }}</div>
                                                <div class="mb-2"><strong>Reason:</strong> <p>{{ $req->reason }}</p></div>

                                                <hr>
                                                <h6 class="mb-3">Update Status</h6>
                                                <form action="{{ route('admin-finance.gsd.job-orders.update-status', ['type' => 'undertime', 'id' => $req->undertime_req_id]) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="d-flex gap-2">
                                                        <button type="submit" name="status" value="ongoing" class="btn btn-info btn-sm" {{ $req->status === 'ongoing' ? 'disabled' : '' }}>Mark as Ongoing</button>
                                                        <button type="submit" name="status" value="on_hold" class="btn btn-warning btn-sm" {{ $req->status === 'on_hold' ? 'disabled' : '' }}>Mark On Hold</button>
                                                        <button type="submit" name="status" value="completed" class="btn btn-success btn-sm">Mark as Completed</button>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                @foreach($serviceRequests as $req)
                                <tr>
                                    <td><strong>SRV-{{ str_pad($req->service_req_id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                                    <td><span class="badge badge-info">Service Request</span></td>
                                    <td>{{ $req->requestor_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($req->date)->format('M d, Y') }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($req->nature_of_request, 30) }}</td>
                                    <td>{{ $req->approver->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $req->status === 'on_hold' ? 'warning' : ($req->status === 'ongoing' ? 'info' : ($req->status === 'completed' ? 'success' : 'secondary')) }} light">{{ ucfirst(str_replace('_', ' ', $req->status)) }}</span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-primary shadow btn-xs sharp view-details-btn"
                                            data-bs-toggle="modal" data-bs-target="#viewModalService{{ $req->service_req_id }}" title="View & Update">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Service Modal -->
                                <div class="modal fade" id="viewModalService{{ $req->service_req_id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Service Request Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <p><strong>Reference:</strong> SRV-{{ str_pad($req->service_req_id, 4, '0', STR_PAD_LEFT) }}</p>
                                                        <p><strong>Requestor:</strong> {{ $req->requestor_name }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><strong>Status:</strong> <span class="badge badge-{{ $req->status === 'on_hold' ? 'warning' : ($req->status === 'ongoing' ? 'info' : ($req->status === 'completed' ? 'success' : 'secondary')) }}">{{ ucfirst(str_replace('_', ' ', $req->status)) }}</span></p>
                                                        <p><strong>Approved By:</strong> {{ $req->approver->name ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                                <div class="mb-2"><strong>Date:</strong> {{ $req->date }}</div>
                                                <div class="mb-2"><strong>Nature of Request:</strong> <p>{{ $req->nature_of_request }}</p></div>

                                                <hr>
                                                <h6 class="mb-3">Update Status</h6>
                                                <form action="{{ route('admin-finance.gsd.job-orders.update-status', ['type' => 'service', 'id' => $req->service_req_id]) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="d-flex gap-2">
                                                        <button type="submit" name="status" value="ongoing" class="btn btn-info btn-sm" {{ $req->status === 'ongoing' ? 'disabled' : '' }}>Mark as Ongoing</button>
                                                        <button type="submit" name="status" value="on_hold" class="btn btn-warning btn-sm" {{ $req->status === 'on_hold' ? 'disabled' : '' }}>Mark On Hold</button>
                                                        <button type="submit" name="status" value="completed" class="btn btn-success btn-sm">Mark as Completed</button>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#gsdJobOrderTable').DataTable({
                order: [[3, 'desc']] // Order by Date
            });
        });
    </script>
    @endpush
</x-app-layout>

<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .nav-tabs { border-bottom: 2px solid #e0e0e0; margin-bottom: 2rem; }
        .nav-tabs .nav-link { font-weight: 600; color: #666; border: none; border-bottom: 3px solid transparent; }
        .nav-tabs .nav-link.active { color: #ff0000; border-bottom-color: #ff0000; background: transparent; }

        .section-title { font-size: 1.1rem; font-weight: 700; color: #333; text-transform: uppercase; margin: 1.5rem 0; text-align: center; }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                    <div class="document-title mb-0" style="font-size: 1.75rem; font-weight: 700; color: #333;">GSD JOB ORDERS</div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin-finance.gsd.job-orders', ['status' => 'all']) }}" class="btn btn-sm btn-{{ $currentStatus == 'all' ? 'primary' : 'outline-primary' }}">All</a>
                        <a href="{{ route('admin-finance.gsd.job-orders', ['status' => 'approved']) }}" class="btn btn-sm btn-{{ $currentStatus == 'approved' ? 'success' : 'outline-success' }}">Approved</a>
                        <a href="{{ route('admin-finance.gsd.job-orders', ['status' => 'ongoing']) }}" class="btn btn-sm btn-{{ $currentStatus == 'ongoing' ? 'info' : 'outline-info' }}">Ongoing</a>
                        <a href="{{ route('admin-finance.gsd.job-orders', ['status' => 'on_hold']) }}" class="btn btn-sm btn-{{ $currentStatus == 'on_hold' ? 'warning' : 'outline-warning' }}">On Hold</a>
                        <a href="{{ route('admin-finance.gsd.job-orders', ['status' => 'completed']) }}" class="btn btn-sm btn-{{ $currentStatus == 'completed' ? 'secondary' : 'outline-secondary' }}">Completed</a>
                    </div>
                </div>

                <div class="card-body">
                    <ul class="nav nav-tabs" id="jobTabs" role="tablist">
                        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#approvals">Job Order Approvals</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#material">Material Request</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#service">Service Request</a></li>
                    </ul>

                    <div class="tab-content">
                        <!-- Job Order Approvals Tab -->
                        <div class="tab-pane fade show active" id="approvals">
                            <div class="mt-4">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered">
                                        <thead class="table-light">
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
                                            @php
                                                $approvalStatuses = ['pending approval', 'Pending Final Approval', 'forwarded to accounting'];
                                                $allApprovals = [];
                                                
                                                // Combine material and service requests that need approval
                                                foreach ($materialRequests as $mat) {
                                                    if (in_array($mat->status, $approvalStatuses)) {
                                                        $allApprovals[] = (object)[
                                                            'type' => 'Material',
                                                            'reference' => 'MAT-' . str_pad($mat->material_req_id, 4, '0', STR_PAD_LEFT),
                                                            'requested_by' => $mat->requested_by,
                                                            'date' => $mat->request_date,
                                                            'details' => substr($mat->request_details, 0, 50),
                                                            'approved_by' => $mat->approved_by_manager ? 'Manager' : '-',
                                                            'status' => $mat->status,
                                                            'id' => $mat->material_req_id,
                                                            'model_type' => 'material'
                                                        ];
                                                    }
                                                }
                                                
                                                foreach ($serviceRequests as $svc) {
                                                    if (in_array($svc->status, $approvalStatuses)) {
                                                        $allApprovals[] = (object)[
                                                            'type' => 'Service',
                                                            'reference' => 'SRV-' . str_pad($svc->service_req_id, 4, '0', STR_PAD_LEFT),
                                                            'requested_by' => $svc->requestor_name,
                                                            'date' => $svc->date,
                                                            'details' => substr($svc->nature_of_request, 0, 50),
                                                            'approved_by' => $svc->approved_by ? 'Manager' : '-',
                                                            'status' => $svc->status,
                                                            'id' => $svc->service_req_id,
                                                            'model_type' => 'service'
                                                        ];
                                                    }
                                                }
                                            @endphp
                                            
                                            @forelse($allApprovals as $approval)
                                            <tr>
                                                <td><strong>{{ $approval->reference }}</strong></td>
                                                <td>
                                                    @if($approval->type === 'Material')
                                                        <span class="badge bg-primary">Material</span>
                                                    @else
                                                        <span class="badge bg-info">Service</span>
                                                    @endif
                                                </td>
                                                <td>{{ $approval->requested_by }}</td>
                                                <td>{{ \Carbon\Carbon::parse($approval->date)->format('m/d/Y') }}</td>
                                                <td>{{ $approval->details }}{{ strlen($approval->details) >= 50 ? '...' : '' }}</td>
                                                <td>{{ $approval->approved_by }}</td>
                                                <td>
                                                    @php
                                                        $statusClass = [
                                                            'pending approval' => 'warning',
                                                            'Pending Final Approval' => 'primary',
                                                            'forwarded to accounting' => 'info'
                                                        ][$approval->status] ?? 'secondary';
                                                    @endphp
                                                    <span class="badge bg-{{ $statusClass }}">{{ $approval->status }}</span>
                                                </td>
                                                <td>
                                                    <a href="#" class="btn btn-sm btn-outline-primary" title="View">
                                                        <i class="las la-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">No requests pending approval</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- Material Request Tab -->
                        <div class="tab-pane fade" id="material">
                            <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                                <div class="section-title mt-0 text-uppercase">Existing Material Requests</div>
                                <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#createMaterialModal">
                                    <i class="las la-plus me-1"></i> Create New Request
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover" id="materialTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Reference</th>
                                            <th>Requested By</th>
                                            <th>Date Requested</th>
                                            <th>Request Details</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($materialRequests as $req)
                                        <tr>
                                            <td><strong>MAT-{{ str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                                            <td>{{ $req->requested_by }}</td>
                                            <td>{{ $req->created_at->format('m/d/Y') }}</td>
                                            <td>{{ (isset($req->request_details) && strlen($req->request_details) > 40) ? substr($req->request_details, 0, 37) . '...' : $req->request_details }}</td>
                                            <td>
                                                @php
                                                    $statusClass = [
                                                        'Pending Final Approval' => 'primary',
                                                        'forwarded to accounting' => 'info',
                                                        'received' => 'success',
                                                        'rejected' => 'danger',
                                                        'pending approval' => 'warning',
                                                        'to submit' => 'secondary',
                                                        'ongoing' => 'info',
                                                        'on_hold' => 'warning',
                                                        'completed' => 'success'
                                                    ][$req->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $req->status)) }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-1">
                                                    <button type="button" class="btn btn-info sharp shadow view-material text-white" 
                                                        data-requested_by="{{ $req->requested_by }}"
                                                        data-request_date="{{ $req->created_at->format('m/d/Y') }}"
                                                        data-request_details="{{ $req->request_details }}"
                                                        data-status="{{ ucfirst(str_replace('_', ' ', $req->status)) }}"
                                                        title="View Details">
                                                        <i class="las la-eye"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No requests found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Service Request Tab -->
                        <div class="tab-pane fade" id="service">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="section-title mt-0 text-uppercase">Existing Service Requests</div>
                                <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#createServiceModal">
                                    <i class="las la-plus me-1"></i> Create New Request
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover" id="serviceTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Reference</th>
                                            <th>Requestor</th>
                                            <th>Date</th>
                                            <th>Nature of Request</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($serviceRequests as $req)
                                        <tr>
                                            <td><strong>SRV-{{ str_pad($req->service_req_id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                                            <td>{{ $req->requestor_name }}</td>
                                            <td>{{ \Carbon\Carbon::parse($req->date)->format('m/d/Y') }}</td>
                                            <td>{{ Str::limit($req->nature_of_request, 40) }}</td>
                                            <td>
                                                @php
                                                    $statusClass = [
                                                        'approved' => 'success',
                                                        'rejected' => 'danger',
                                                        'pending' => 'warning',
                                                        'completed' => 'info',
                                                        'ongoing' => 'info',
                                                        'on_hold' => 'warning'
                                                    ][$req->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $req->status)) }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-1">
                                                    <button type="button" class="btn btn-info sharp shadow view-service text-white" 
                                                        data-requestor_name="{{ $req->requestor_name }}"
                                                        data-date="{{ $req->date }}"
                                                        data-nature_of_request="{{ $req->nature_of_request }}"
                                                        data-status="{{ ucfirst(str_replace('_', ' ', $req->status)) }}"
                                                        title="View Details">
                                                        <i class="las la-eye"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No requests found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Material Modal -->
    <div class="modal fade" id="viewMaterialModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light text-dark">
                    <h5 class="modal-title fw-bold"><i class="las la-receipt me-2"></i>Material Request Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Requested By</label>
                            <p id="view_material_by" class="fs-5 fw-bold text-dark mb-0"></p>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Date Requested</label>
                            <p id="view_material_date" class="fs-5 fw-bold text-dark mb-0"></p>
                        </div>
                        <div class="col-md-12">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Status</label>
                            <div>
                                <span id="view_material_status" class="badge bg-secondary fs-6"></span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-light rounded-3 border">
                                <label class="small text-muted text-uppercase fw-bold mb-2 d-block">Request Details</label>
                                <div id="view_material_details" class="text-dark" style="white-space: pre-wrap;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Service Modal -->
    <div class="modal fade" id="viewServiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light text-dark">
                    <h5 class="modal-title fw-bold"><i class="las la-tools me-2"></i>Service Request Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-8">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Requestor</label>
                            <p id="view_svc_name" class="fs-5 fw-bold text-dark mb-0"></p>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Date</label>
                            <p id="view_svc_date" class="fs-5 fw-bold text-dark mb-0"></p>
                        </div>
                        <div class="col-md-12">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Status</label>
                            <div><span id="view_svc_status" class="badge fs-6"></span></div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-light rounded-3 border">
                                <label class="small text-muted text-uppercase fw-bold mb-2 d-block">Nature of Request</label>
                                <p id="view_svc_nature" class="mb-0 text-dark" style="white-space: pre-wrap;"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Material Request Modal -->
    <div class="modal fade" id="createMaterialModal" tabindex="-1" aria-labelledby="createMaterialModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="createMaterialForm" action="{{ route('admin-finance.gsd.material-requests.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createMaterialModalLabel">Create New Material Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold">Requestor's Name:</label>
                                <input type="text" name="requested_by" class="form-control" value="{{ old('requested_by') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Date:</label>
                                <input type="date" name="request_date" class="form-control" value="{{ old('request_date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="alert alert-info py-2 px-3 small border-0 shadow-sm" style="background-color: #e3f2fd; color: #0d47a1;">
                                    <i class="las la-info-circle me-1"></i>
                                    <strong>Instructions:</strong> Please specify the item name, quantity, and purpose for each item requested. Use a new line for each item.
                                </div>
                                <label class="form-label fw-bold">Request Details:</label>
                                <textarea name="request_details" class="form-control" rows="5" placeholder="e.g. Bond Paper (A4) - 5 reams - for office use&#10;2. Toner Cartridge (HP 85A) - 2 pcs - for printer in finance" required>{{ old('request_details') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save Material Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create Service Request Modal -->
    <div class="modal fade" id="createServiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="createServiceForm" action="{{ route('admin-finance.gsd.service-requests.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Create Service Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold">Requestor's Name:</label>
                                <input type="text" name="requestor_name" class="form-control" placeholder="Enter name" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Date:</label>
                                <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Nature of Request:</label>
                                <textarea name="nature_of_request" class="form-control" rows="5" placeholder="Specify the details of the service request..." required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4">Save Service Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // View Material Logic
            const viewMaterialModalEl = document.getElementById('viewMaterialModal');
            if (viewMaterialModalEl) {
                const viewMaterialModal = new bootstrap.Modal(viewMaterialModalEl);

                document.querySelectorAll('.view-material').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const data = this.dataset;
                        
                        document.getElementById('view_material_by').textContent = data.requested_by;
                        document.getElementById('view_material_date').textContent = data.request_date;
                        document.getElementById('view_material_details').textContent = data.request_details;
                        
                        const statusBadge = document.getElementById('view_material_status');
                        statusBadge.textContent = data.status;
                        statusBadge.className = 'badge fs-6 ' + (data.status.toLowerCase() === 'received' ? 'bg-success' : (data.status.toLowerCase() === 'completed' ? 'bg-success' : 'bg-warning text-white'));
                        
                        viewMaterialModal.show();
                    });
                });
            }

            // View Service Logic
            const viewServiceModalEl = document.getElementById('viewServiceModal');
            if (viewServiceModalEl) {
                const viewModal = new bootstrap.Modal(viewServiceModalEl);
                document.querySelectorAll('.view-service').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const data = this.dataset;
                        document.getElementById('view_svc_name').textContent = data.requestor_name;
                        document.getElementById('view_svc_date').textContent = data.date;
                        document.getElementById('view_svc_nature').textContent = data.nature_of_request;
                        
                        const statusBadge = document.getElementById('view_svc_status');
                        statusBadge.textContent = data.status;
                        statusBadge.className = 'badge ' + (data.status.toLowerCase() == 'approved' ? 'bg-success' : 'bg-secondary');
                        
                        viewModal.show();
                    });
                });
            }
        });
    </script>
    @endpush
</x-app-layout>

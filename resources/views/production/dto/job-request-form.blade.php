<x-app-layout :title="'Job Request Form'" :sidebar="'production'">
    <div class="row">
        <div class="col-xl-12 col-lg-12">


            <!-- Job Request History -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Job Request</h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addJobRequestModal">
                        <i class="las la-plus"></i> Add Job Request
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Job No.</th>
                                    <th>Project Title</th>
                                    <th>Date</th>
                                    <th>Due Date</th>
                                    <th>Department</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jobRequests as $request)
                                    <tr>
                                        <td>{{ $request->job_no }}</td>
                                        <td>{{ $request->project_title }}</td>
                                        <td>{{ $request->date ? \Carbon\Carbon::parse($request->date)->format('M d, Y') : '-' }}</td>
                                        <td>{{ $request->due_date ? \Carbon\Carbon::parse($request->due_date)->format('M d, Y') : '-' }}</td>
                                        <td>{{ $request->department->dept_name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $request->status == 'Completed' ? 'success' : ($request->status == 'Pending' ? 'warning' : 'info') }}">
                                                {{ $request->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info btn-view" 
                                                data-bs-toggle="modal" data-bs-target="#viewModal"
                                                data-data='{{ json_encode($request) }}'
                                                data-dept='{{ $request->department->dept_name ?? "" }}'>
                                                <i class="las la-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-warning btn-edit" 
                                                data-bs-toggle="modal" data-bs-target="#editModal"
                                                data-data='{{ json_encode($request) }}'>
                                                <i class="las la-edit"></i>
                                            </button>
                                            <form id="delete-form-{{ $request->id }}" action="{{ route('production.dto.job-request-form.destroy', $request->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="if(confirm('Are you sure you want to delete this item?')) document.getElementById('delete-form-{{ $request->id }}').submit();">
                                                <i class="las la-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No job requests found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{-- Pagination links can be added here if using paginate() in controller --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('modals')
    <!-- Add Job Request Modal -->
    <div class="modal fade" id="addJobRequestModal" tabindex="-1" role="dialog" aria-labelledby="addJobRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addJobRequestModalLabel">Add Job Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addJobRequestForm" class="basic-form" method="POST" action="{{ route('production.dto.job-request-form.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="customer-section" style="display: block;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="customer-details" style="background: none; padding: 0;">
                                        <h5>Job Information</h5>
                                        <div class="form-group">
                                            <label>JOB No.:</label>
                                            <input type="text" name="job_no" placeholder="Enter job number" required class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Project Title:</label>
                                            <input type="text" name="project_title" placeholder="Enter project title" required class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Specifications:</label>
                                            <textarea name="specifications" placeholder="Enter specifications" class="form-control"></textarea>
                                        </div>
                                         <div class="form-group">
                                            <label>Due date: <span class="text-danger">*</span></label>
                                            <input type="date" name="due_date" id="add_due_date" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="order-details" style="background: none; padding: 0;">
                                        <h5>Request Information</h5>
                                         <div class="form-group">
                                            <label>Date: <span class="text-danger">*</span></label>
                                            <input type="date" name="date" id="add_date" value="{{ date('Y-m-d') }}" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Department:</label>
                                            <select name="department" class="selectpicker form-control" data-live-search="true" title="Select Department">
                                                @foreach($departments as $dept)
                                                    <option value="{{ $dept->dept_id }}">{{ $dept->dept_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="las la-paper-plane"></i> Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel">View Job Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Job No.:</label>
                                <p id="view_job_no"></p>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Project Title:</label>
                                <p id="view_project_title"></p>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Specifications:</label>
                                <p id="view_specifications" style="white-space: pre-line;"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Due Date:</label>
                                <p id="view_due_date"></p>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Date Requested:</label>
                                <p id="view_date"></p>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Department:</label>
                                <p id="view_department"></p>
                            </div>
                             <div class="form-group">
                                <label class="font-weight-bold">Status:</label>
                                <p id="view_status"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Job Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Job No.:</label>
                                    <input type="text" name="job_no" id="edit_job_no" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Project Title:</label>
                                    <input type="text" name="project_title" id="edit_project_title" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Specifications:</label>
                                    <textarea name="specifications" id="edit_specifications" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                 <div class="form-group">
                                    <label>Due Date: <span class="text-danger">*</span></label>
                                    <input type="date" name="due_date" id="edit_due_date" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Date: <span class="text-danger">*</span></label>
                                    <input type="date" name="date" id="edit_date" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Department:</label>
                                    <select name="department" id="edit_department" class="form-control">
                                        <option value="">Select Department</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->dept_id }}">{{ $dept->dept_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Status:</label>
                                    <select name="status" id="edit_status" class="form-control">
                                        <option value="Pending">Pending</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endpush

    @push('styles')
    <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <style>

        .form-header .company-address {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.1rem;
        }

        .form-header .company-contact {
            font-size: 0.9rem;
            color: #666;
        }

        .form-header .document-title {
            text-align: center;
            font-size: 1.75rem;
            font-weight: 700;
            color: #333;
            margin-top: 1rem;
            letter-spacing: 1px;
        }

        .customer-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 1.5rem;
        }

        .customer-details,
        .order-details {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
        }

        .customer-details h5,
        .order-details h5 {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 0.75rem;
        }

        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.25rem;
            display: block;
            font-size: 0.9rem;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 0.5rem;
            font-size: 0.9rem;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 60px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 2px solid #e0e0e0;
        }

        @media print {
            .sidebar,
            .header,
            .form-actions {
                display: none;
            }

            .order-form {
                box-shadow: none;
            }
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="{{ asset('vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Edit Modal Populate
            $(document).on('click', '.btn-edit', function() {
                var data = $(this).data('data');
                $('#editForm').attr('action', '/production/dto/job-request-form/' + data.id);
                $('#edit_job_no').val(data.job_no);
                $('#edit_project_title').val(data.project_title);
                $('#edit_specifications').val(data.specifications);
                $('#edit_due_date').val(data.due_date);
                $('#edit_date').val(data.date);
                $('#edit_department').val(data.department_id);
                $('#edit_status').val(data.status);
            });

            // View Modal Populate
            $(document).on('click', '.btn-view', function() {
                var data = $(this).data('data');
                var dept = $(this).data('dept');
                $('#view_job_no').text(data.job_no);
                $('#view_project_title').text(data.project_title);
                $('#view_specifications').text(data.specifications || '-');
                $('#view_due_date').text(data.due_date);
                $('#view_date').text(data.date);
                $('#view_department').text(dept || 'N/A');
                $('#view_status').text(data.status);
            });
            
            // Fix for modals if data-toggle isn't working for some reason
            if (typeof $.fn.modal === 'function') {
                console.log('Bootstrap modal is loaded');
            } else {
                console.error('Bootstrap modal is NOT loaded');
            }

            // Date validation for Add Form
            $('#addJobRequestForm').on('submit', function(e) {
                const date = new Date($('#add_date').val());
                const dueDate = new Date($('#add_due_date').val());
                
                if (dueDate < date) {
                    e.preventDefault();
                    alert('Due Date cannot be earlier than the Request Date.');
                    return false;
                }
            });

            // Date validation for Edit Form
            $('#editForm').on('submit', function(e) {
                const date = new Date($('#edit_date').val());
                const dueDate = new Date($('#edit_due_date').val());
                
                if (dueDate < date) {
                    e.preventDefault();
                    alert('Due Date cannot be earlier than the Request Date.');
                    return false;
                }
            });
        });
    </script>
    @endpush
</x-app-layout>

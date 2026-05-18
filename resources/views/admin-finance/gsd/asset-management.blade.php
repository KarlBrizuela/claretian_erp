<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .asset-status-active { color: #28a745; font-weight: 600; }
        .asset-status-maintenance { color: #ffc107; font-weight: 600; }
        .asset-status-disposed { color: #dc3545; font-weight: 600; }

        .property-tag-preview {
            border: 2px solid #333; border-radius: 10px; padding: 15px;
            max-width: 250px; background: #fff; font-family: 'Courier New', Courier, monospace;
            margin-bottom: 20px;
        }

        .property-tag-preview div {
            margin-bottom: 5px; border-bottom: 1px dotted #ccc;
            display: flex; justify-content: space-between;
        }

        .property-tag-preview label { font-weight: bold; margin-right: 10px; font-size: 0.8rem; }
        .property-tag-preview span { font-size: 0.8rem; }

        /* Prevent table header wrapping */
        .table thead th {
            white-space: nowrap;
            vertical-align: middle;
        }
    </style>
    @endpush

    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Asset Management</h4>
                <p class="mb-0">Recording and tracking of company assets, equipment, and supplies</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAssetModal">
                <i class="fa fa-plus me-2"></i>Record New Asset
            </button>
        </div>
    </div>


    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mx-0 mb-3">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
            <strong>Error!</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example3" class="display min-w850 table table-responsive-md">
                            <thead>
                                <tr>
                                    <th>Property No.</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Acquired Date</th>
                                    <th>Department</th>
                                    <th>Checked By</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="assetTableBody">
                                @foreach($assets as $asset)
                                <tr>
                                    <td><strong>{{ $asset->property_code }}</strong></td>
                                    <td>{{ $asset->category }}</td>
                                    <td>{{ $asset->description }}</td>
                                    <td>{{ \Carbon\Carbon::parse($asset->acquisition_date)->format('M d, Y') }}</td>
                                    <td>{{ $asset->department }}</td>
                                    <td>{{ $asset->checked_by }}</td>
                                    <td><span class="asset-status-{{ strtolower(str_replace(' ', '-', $asset->status)) }}">{{ $asset->status }}</span></td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="javascript:void(0);" 
                                               class="btn btn-primary shadow btn-xs sharp me-1 edit-asset-btn"
                                               data-bs-toggle="modal" 
                                               data-bs-target="#editAssetModal"
                                               data-id="{{ $asset->asset_id }}"
                                               data-property-code="{{ $asset->property_code }}"
                                               data-category="{{ $asset->category }}"
                                               data-description="{{ $asset->description }}"
                                               data-acquisition-date="{{ $asset->acquisition_date }}"
                                               data-department="{{ $asset->department }}"
                                               data-checked-by="{{ $asset->checked_by }}"
                                               data-status="{{ $asset->status }}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a href="javascript:void(0);" 
                                               class="btn btn-danger shadow btn-xs sharp delete-asset-btn"
                                               data-bs-toggle="modal" 
                                               data-bs-target="#confirmDeleteModal"
                                               data-id="{{ $asset->asset_id }}"
                                               data-property-code="{{ $asset->property_code }}">
                                                <i class="fa fa-trash"></i>
                                            </a>
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

    @push('modals')
    <div class="modal fade" id="addAssetModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Record Asset / Supply</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="assetRecordForm" action="{{ route('admin-finance.gsd.asset-requests.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-black font-w500 text-uppercase small">Category</label>
                                    <select class="form-control" name="category" id="assetCategory" required>
                                        <option value="">Select Category</option>
                                        <option>Office Supplies</option>
                                        <option>Office Equipment</option>
                                        <option>Furniture & Fixtures</option>
                                        <option>Building/Real Estate</option>
                                        <option>Vehicles</option>
                                        <option>Computers/IT Assets</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-black font-w500 text-uppercase small">Acquired Date</label>
                                    <input type="date" class="form-control" name="acquisition_date" id="acquiredDate" required>
                                </div>
                            </div>
                            <div class="col-md-6 text-center d-flex flex-column align-items-center justify-content-center">
                                <p class="text-muted small mb-2">Property Tag Preview</p>
                                <div class="property-tag-preview">
                                    <div><label>PROP NO.</label><span id="preview-no">---</span></div>
                                    <div><label>CATEGORY</label><span id="preview-cat">---</span></div>
                                    <div><label>DESC</label><span id="preview-desc">---</span></div>
                                    <div><label>DATE</label><span id="preview-date">---</span></div>
                                    <div><label>DEPT</label><span id="preview-dept">---</span></div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-black font-w500 text-uppercase small">Description</label>
                            <textarea class="form-control" name="description" id="assetDescription" rows="3" placeholder="Detailed description..." required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 text-center d-flex flex-column justify-content-center">
                                <div class="mb-3">
                                    <label class="form-label text-black font-w500 text-uppercase small">Department</label>
                                    <select class="form-control" name="department" id="department" required>
                                        <option value="">Select Department</option>
                                        <option>Accounting</option>
                                        <option>DTO</option>
                                        <option>Marketing</option>
                                        <option>MIS</option>
                                        <option>Production</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-black font-w500 text-uppercase small">Checked By</label>
                                    <input type="text" class="form-control" name="checked_by" id="checkedBy" placeholder="Authorized Staff Name" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer text-center d-flex flex-row justify-content-center">
                        <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Asset Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Asset Modal -->
    <div class="modal fade" id="editAssetModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Asset Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editAssetForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-black font-w500 text-uppercase small">Property / Tag No.</label>
                                    <input type="text" class="form-control" name="property_code" id="edit_property_code" placeholder="Auto-generated if empty">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-black font-w500 text-uppercase small">Category</label>
                                    <select class="form-control" name="category" id="edit_category" required>
                                        <option value="">Select Category</option>
                                        <option>Office Supplies</option>
                                        <option>Office Equipment</option>
                                        <option>Furniture & Fixtures</option>
                                        <option>Building/Real Estate</option>
                                        <option>Vehicles</option>
                                        <option>Computers/IT Assets</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-black font-w500 text-uppercase small">Acquired Date</label>
                                    <input type="date" class="form-control" name="acquisition_date" id="edit_acquisition_date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-black font-w500 text-uppercase small">Status</label>
                                    <select class="form-control" name="status" id="edit_status" required>
                                        <option>Active</option>
                                        <option>Inactive</option>
                                        <option>Under Maintenance</option>
                                        <option>Disposed</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-black font-w500 text-uppercase small">Department</label>
                                    <select class="form-control" name="department" id="edit_department" required>
                                        <option value="">Select Department</option>
                                        <option>Accounting</option>
                                        <option>DTO</option>
                                        <option>Marketing</option>
                                        <option>MIS</option>
                                        <option>Production</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-black font-w500 text-uppercase small">Checked By</label>
                                    <input type="text" class="form-control" name="checked_by" id="edit_checked_by" placeholder="Authorized Staff Name" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-black font-w500 text-uppercase small">Description</label>
                            <textarea class="form-control" name="description" id="edit_description" rows="3" placeholder="Detailed description..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer text-center d-flex flex-row justify-content-center">
                        <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Asset Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="confirmDeleteModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete asset <strong id="delete_property_no"></strong>?</p>
                    <p class="text-danger small">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteAssetForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Confirm Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                    </div>
                    <h3 class="mb-3">Perfect!</h3>
                    <p class="text-muted fs-18">{{ session('success') }}</p>
                    <button type="button" class="btn btn-success px-5 btn-lg mt-3" data-bs-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>
    @endpush

    @push('scripts')
    <script>
        // Preview logic for Add Modal
        document.getElementById('assetCategory').addEventListener('change', e => document.getElementById('preview-cat').textContent = e.target.value || '---');
        document.getElementById('assetDescription').addEventListener('input', e => document.getElementById('preview-desc').textContent = e.target.value || '---');
        document.getElementById('acquiredDate').addEventListener('change', e => document.getElementById('preview-date').textContent = e.target.value || '---');
        document.getElementById('department').addEventListener('change', e => document.getElementById('preview-dept').textContent = e.target.value || '---');

        // Edit Modal Data Binding
        document.querySelectorAll('.edit-asset-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const form = document.getElementById('editAssetForm');
                form.action = `/admin-finance/gsd/asset-requests/${id}`;
                
                document.getElementById('edit_property_code').value = this.getAttribute('data-property-code');
                document.getElementById('edit_category').value = this.getAttribute('data-category');
                document.getElementById('edit_description').value = this.getAttribute('data-description');
                document.getElementById('edit_acquisition_date').value = this.getAttribute('data-acquisition-date');
                document.getElementById('edit_department').value = this.getAttribute('data-department');
                document.getElementById('edit_checked_by').value = this.getAttribute('data-checked-by');
                document.getElementById('edit_status').value = this.getAttribute('data-status');
            });
        });

        // Delete Modal Data Binding
        document.querySelectorAll('.delete-asset-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const propertyNo = this.getAttribute('data-property-code');
                const form = document.getElementById('deleteAssetForm');
                
                form.action = `/admin-finance/gsd/asset-requests/${id}`;
                document.getElementById('delete_property_no').textContent = propertyNo;
            });
        });
        // Show Success Modal if session exists
        @if(session('success'))
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        @endif
    </script>
    @endpush
</x-app-layout>

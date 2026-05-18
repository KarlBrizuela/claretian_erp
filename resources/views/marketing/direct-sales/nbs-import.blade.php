<x-app-layout :title="$title" :sidebar="$sidebar">
    <div class="row">
        <div class="col-xl-8 col-lg-10 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h4 class="card-title fw-bold mb-0 text-primary">
                        <i class="las la-file-import me-2"></i>NBS Purchase Order Import
                    </h4>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info border-0 bg-light-info mb-4">
                        <div class="d-flex align-items-center">
                            <i class="las la-info-circle fs-3 me-3 text-info"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Import Instructions</h6>
                                <p class="mb-0 small text-muted">Please upload the NBS PO export file in <strong>CSV format</strong>. The system will map items by their <strong>Description</strong> and customers by the <strong>Vendor Code</strong> (Account Number).</p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('marketing.nbs-import.process') }}" method="POST" enctype="multipart/form-data" id="importForm">
                        @csrf
                        <div class="upload-area p-5 border-2 rounded-3 text-center mb-4 cursor-pointer position-relative" id="dropZone" style="border: 2px dashed #dee2e6 !important;">
                            <input type="file" name="po_file" id="poFile" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" accept=".csv,.xls,.xlsx" required>
                            
                            <div id="uploadPlaceholder">
                                <div class="mb-3">
                                    <i class="las la-cloud-upload-alt fs-1 text-primary opacity-50"></i>
                                </div>
                                <h5 class="fw-bold">Drag and drop NBS PO file</h5>
                                <p class="text-muted">or click to browse from your computer</p>
                                <span class="badge bg-light text-dark px-3 mt-2">Accepted formats: .csv</span>
                            </div>

                            <div id="fileInfo" class="d-none">
                                <div class="mb-3">
                                    <i class="las la-file-excel fs-1 text-success"></i>
                                </div>
                                <h5 class="fw-bold" id="selectedFileName">filename.csv</h5>
                                <p class="text-muted" id="selectedFileSize">0 KB</p>
                                <button type="button" class="btn btn-link text-danger btn-sm" id="removeFile">Change File</button>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-3 fw-bold fs-5 shadow-sm" id="importBtn" disabled>
                                <span id="btnText">
                                    <i class="las la-check-circle me-2"></i>Process NBS Purchase Orders
                                </span>
                                <span id="btnLoading" class="d-none">
                                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    Importing... Please wait
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer bg-light border-0 py-3">
                    <p class="mb-0 small text-center text-muted">
                        <i class="las la-lock me-1"></i>Secure processing. Data will be mapped to existing book and customer records.
                    </p>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .upload-area:hover {
            border-color: #0d6efd !important;
            background-color: rgba(13, 110, 253, 0.02);
        }
        .bg-light-info {
            background-color: #e7f3ff;
            color: #004085;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const poFile = document.getElementById('poFile');
            const dropZone = document.getElementById('dropZone');
            const uploadPlaceholder = document.getElementById('uploadPlaceholder');
            const fileInfo = document.getElementById('fileInfo');
            const selectedFileName = document.getElementById('selectedFileName');
            const selectedFileSize = document.getElementById('selectedFileSize');
            const removeFile = document.getElementById('removeFile');
            const importBtn = document.getElementById('importBtn');

            poFile.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    selectedFileName.textContent = file.name;
                    selectedFileSize.textContent = (file.size / 1024).toFixed(2) + ' KB';
                    
                    uploadPlaceholder.classList.add('d-none');
                    fileInfo.classList.remove('d-none');
                    importBtn.disabled = false;
                    dropZone.style.borderColor = '#28a745';
                }
            });

            removeFile.addEventListener('click', function() {
                poFile.value = '';
                uploadPlaceholder.classList.remove('d-none');
                fileInfo.classList.add('d-none');
                importBtn.disabled = true;
                dropZone.style.borderColor = '#dee2e6';
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, e => {
                    e.preventDefault();
                    dropZone.style.borderColor = '#0d6efd';
                    dropZone.style.backgroundColor = 'rgba(13, 110, 253, 0.05)';
                });
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, e => {
                    e.preventDefault();
                    if (poFile.files.length === 0) {
                        dropZone.style.borderColor = '#dee2e6';
                        dropZone.style.backgroundColor = 'transparent';
                    }
                });
            });

            // Handle Form Submission Loading State
            const importForm = document.getElementById('importForm');
            const btnText = document.getElementById('btnText');
            const btnLoading = document.getElementById('btnLoading');

            importForm.addEventListener('submit', function() {
                importBtn.disabled = true;
                btnText.classList.add('d-none');
                btnLoading.classList.remove('d-none');
                dropZone.style.pointerEvents = 'none';
                dropZone.style.opacity = '0.6';
            });
        });
    </script>
    @endpush
</x-app-layout>

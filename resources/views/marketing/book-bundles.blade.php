<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        /* Custom Page Tabs Styling */
        .page-tabs {
            border-bottom: 2px solid #eee;
            margin-bottom: 1.5rem;
            display: flex;
            gap: 1.5rem;
            padding-left: 1rem;
        }
        .page-tabs .nav-link {
            font-size: 0.95rem;
            font-weight: 700;
            color: #666;
            border: none;
            background: transparent;
            padding: 12px 24px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.2s ease-in-out;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .page-tabs .nav-link:hover {
            color: #D9251C;
            background: transparent !important;
        }
        .page-tabs .nav-link.active {
            color: #D9251C;
            border-bottom-color: #D9251C;
            background: transparent !important;
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <!-- Navigation Tabs -->
            <ul class="nav page-tabs" id="bookMgmtTabs">
                <li class="nav-item">
                    <a class="nav-link" id="book-list-tab" href="{{ route('marketing.products') }}">
                        <i class="las la-list" style="font-size: 1.25rem;"></i>
                        <span>Book List</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="book-index-tab" href="{{ route('marketing.indices') }}">
                        <i class="las la-tag" style="font-size: 1.25rem;"></i>
                        <span>Book Index</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" id="book-bundle-tab" href="{{ route('marketing.bundles') }}">
                        <i class="las la-boxes" style="font-size: 1.25rem;"></i>
                        <span>Book Bundle</span>
                    </a>
                </li>
            </ul>

            <div class="card">
                <div class="card-header border-0 d-block d-sm-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h4 class="card-title mb-0">Book Bundle List</h4>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2 mt-3 mt-sm-0">
                        <!-- Bundle Search Form -->
                        <form action="{{ route('marketing.bundles') }}" method="GET" class="d-flex align-items-center gap-2">
                            <div style="width: 250px; height: 38px; display: flex; align-items: center; border: 1px solid #ced4da; border-radius: 4px; background-color: #f8f9fa; padding: 0 12px; box-sizing: border-box;">
                                <span class="las la-search text-muted me-2" style="font-size: 1.1rem; line-height: 1;"></span>
                                <input type="text" name="bundle_search" class="form-control" 
                                       placeholder="Search bundles..." value="{{ request('bundle_search') }}" 
                                       style="border: none !important; background: transparent !important; padding: 0 !important; height: 100%; font-size: 0.85rem; color: #333; outline: none !important; box-shadow: none !important;">
                                @if(request('bundle_search'))
                                    <a href="{{ route('marketing.bundles') }}" class="text-muted d-inline-flex align-items-center justify-content-center ms-2" title="Clear search" style="text-decoration: none;">
                                        <span class="las la-times-circle" style="color: #999; font-size: 1.25rem; cursor: pointer;"></span>
                                    </a>
                                @endif
                            </div>
                            <button type="submit" class="btn btn-danger text-white rounded d-inline-flex align-items-center justify-content-center gap-2" style="height: 38px; padding: 0 1.2rem; border: none; font-size: 0.85rem; font-weight: 500; background-color: #D9251C; box-shadow: 0 4px 6px rgba(217, 37, 28, 0.15);">
                                <span class="las la-search" style="font-size: 1rem; color: #fff;"></span>
                                <span>Search</span>
                            </button>
                        </form>

                        <a href="javascript:void(0);" id="addNewBundleBtn"
                            class="btn btn-primary rounded d-flex align-items-center"
                            style="gap: 0.5rem; padding: 0.5rem 1rem; height: 38px; min-height: 38px; line-height: 1.5; box-sizing: border-box; border: none; background: #ff0000; color: #ffffff; font-weight: 500;">
                            <i class="las la-plus" style="font-size: 1rem;"></i>
                            <span>Add New Bundle</span>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-responsive-md">
                            <thead>
                                <tr>
                                    <th>SKU</th>
                                    <th>Bundle Name</th>
                                    <th>Description</th>
                                    <th>Included Books</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bundles as $bundle)
                                <tr>
                                    <td><strong>#{{ $bundle->sku }}</strong></td>
                                    <td>{{ $bundle->name }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($bundle->description ?? 'N/A', 50) }}</td>
                                    <td>
                                        @foreach($bundle->books as $b)
                                            <span class="badge badge-outline-danger mb-1" style="display:inline-block; font-size:0.75rem;">
                                                {{ $b->name }} <strong class="text-dark">x{{ $b->pivot->quantity }}</strong>
                                            </span>
                                        @endforeach
                                    </td>
                                    <td>₱{{ number_format($bundle->price, 2) }}</td>
                                    <td>
                                        @if($bundle->stock > 0)
                                            <span class="badge badge-success">{{ $bundle->stock }} pcs</span>
                                        @else
                                            <span class="badge badge-danger">0 pcs</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($bundle->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-light">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="javascript:void(0);" class="btn btn-secondary shadow btn-xs sharp me-1 view-bundle-btn" 
                                               data-id="{{ $bundle->id }}"><i class="far fa-eye"></i></a>
                                            <a href="javascript:void(0);" class="btn btn-primary shadow btn-xs sharp me-1 edit-bundle-btn" 
                                               data-id="{{ $bundle->id }}"><i class="fas fa-pencil-alt"></i></a>
                                            <a href="javascript:void(0);" class="btn btn-danger shadow btn-xs sharp delete-bundle-btn"
                                               data-id="{{ $bundle->id }}"><i class="fa fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No bundles in the list.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                        <div class="text-muted small">
                            Showing {{ $bundles->firstItem() ?? 0 }} to {{ $bundles->lastItem() ?? 0 }} of {{ $bundles->total() }} entries
                        </div>
                        <div>
                            {{ $bundles->appends(['bundle_search' => request('bundle_search')])->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('modals')
    <!-- Add/Edit Bundle Modal -->
    <div class="modal" id="addBundleModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="addBundleForm">
                    @csrf
                    <input type="hidden" name="bundle_id" id="modal_bundle_id">
                    <div class="modal-header" style="background: #D9251C; color: #fff;">
                        <h5 class="modal-title text-white" id="addBundleModalTitle">Add New Book Bundle</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">BUNDLE NAME <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="name" id="bundle_name" required placeholder="e.g. Theological Starter Pack">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">SKU <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="sku" id="bundle_sku" required placeholder="e.g. BNDL-THEO-01">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">PRICE (₱) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="price" id="bundle_price" required placeholder="e.g. 1500.00">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">STOCK <span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control form-control-sm" name="stock" id="bundle_stock" required placeholder="e.g. 10">
                            </div>
                            <div class="col-md-6 mb-3 d-flex align-items-center">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="bundle_is_active" value="1" checked>
                                    <label class="form-check-label small fw-bold" for="bundle_is_active">ACTIVE ON POS</label>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label small fw-bold">DESCRIPTION</label>
                                <textarea class="form-control form-control-sm" name="description" id="bundle_description" rows="3" placeholder="Optional description..."></textarea>
                            </div>
                        </div>
                        
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0"><i class="las la-book me-2"></i>Bundle Books List</h6>
                            <button type="button" class="btn btn-xs btn-success text-white" id="addBundleItemRowBtn">
                                <i class="las la-plus"></i> Add Book
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="bundleItemsTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 70%">Book <span class="text-danger">*</span></th>
                                        <th style="width: 20%">Quantity <span class="text-danger">*</span></th>
                                        <th style="width: 10%" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="bundleItemsContainer">
                                    <!-- Dynamic rows go here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="saveBundleBtn" style="background: #D9251C; border-color: #D9251C;">Save Bundle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Bundle Modal -->
    <div class="modal" id="viewBundleModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-secondary">
                    <h5 class="modal-title text-white"><i class="las la-boxes me-2"></i>View Bundle Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="fw-bold small text-muted text-uppercase d-block">SKU</label>
                        <span id="view_bundle_sku" class="fw-bold"></span>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small text-muted text-uppercase d-block">Bundle Name</label>
                        <span id="view_bundle_name" class="fs-5 fw-bold"></span>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small text-muted text-uppercase d-block">Price</label>
                        <span id="view_bundle_price" class="text-success fw-bold fs-5"></span>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small text-muted text-uppercase d-block">Stock</label>
                        <span id="view_bundle_stock" class="fw-bold text-dark fs-5"></span>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small text-muted text-uppercase d-block">Status</label>
                        <span id="view_bundle_status"></span>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small text-muted text-uppercase d-block">Description</label>
                        <p id="view_bundle_description" class="bg-light p-2 rounded small"></p>
                    </div>
                    <hr>
                    <h6 class="fw-bold mb-3"><i class="las la-book me-2"></i>Included Books</h6>
                    <ul class="list-group" id="view_bundle_books_list">
                        <!-- Dynamic list items -->
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Bundle Confirmation Modal -->
    <div class="modal" id="deleteBundleModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white"><i class="fas fa-trash me-2"></i>Confirm Delete Bundle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this book bundle? This action cannot be undone.</p>
                    <input type="hidden" id="delete_bundle_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBundleBtn">Delete Bundle</button>
                </div>
            </div>
        </div>
    </div>
    @endpush

    @push('scripts')
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script>
        // Bundle modals: use direct DOM show/hide to avoid Bootstrap JS conflicts
        function showBundleModal(id) {
            // Hide any open bundle modals first
            ['addBundleModal', 'viewBundleModal', 'deleteBundleModal'].forEach(function(mid) {
                var m = document.getElementById(mid);
                if (m) { m.style.display = 'none'; m.removeAttribute('aria-modal'); m.setAttribute('aria-hidden', 'true'); }
            });
            // Remove stale backdrops
            document.querySelectorAll('.modal-backdrop').forEach(function(b) { b.remove(); });
            document.body.classList.remove('modal-open');

            var el = document.getElementById(id);
            if (!el) return;

            // CRITICAL: move modal to <body> to escape CSS transform stacking context
            if (el.parentNode !== document.body) {
                document.body.appendChild(el);
            }

            // Add backdrop
            var backdrop = document.createElement('div');
            backdrop.id = 'bundle-backdrop';
            backdrop.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1050;';
            document.body.appendChild(backdrop);
            document.body.classList.add('modal-open');

            el.style.cssText = 'display:block !important; position:fixed !important; top:0; left:0; width:100%; height:100%; overflow-x:hidden; overflow-y:auto; z-index:1060;';
            el.removeAttribute('aria-hidden');
            el.setAttribute('aria-modal', 'true');
        }

        function hideBundleModal(id) {
            var el = document.getElementById(id);
            if (el) {
                el.style.display = 'none';
                el.setAttribute('aria-hidden', 'true');
                el.removeAttribute('aria-modal');
            }
            var bd = document.getElementById('bundle-backdrop');
            if (bd) bd.remove();
            document.body.classList.remove('modal-open');
        }

        let bundleItemIndex = 0;
        const allBooks = @json($allBooks);

        // Helper to add bundle item row
        function addBundleItemRow(bookId = '', quantity = 1) {
            const container = document.getElementById('bundleItemsContainer');
            
            // Build options list
            let optionsHtml = '<option value="">Select Book...</option>';
            allBooks.forEach(book => {
                const selected = book.id == bookId ? 'selected' : '';
                optionsHtml += `<option value="${book.id}" ${selected}>${book.name} (₱${book.price})</option>`;
            });

            const rowId = `bundle-item-row-${bundleItemIndex}`;
            const tr = document.createElement('tr');
            tr.id = rowId;
            tr.innerHTML = `
                <td>
                    <select name="items[${bundleItemIndex}][book_id]" class="form-select form-select-sm select-book-item" required style="width: 100%;">
                        ${optionsHtml}
                    </select>
                </td>
                <td>
                    <input type="number" name="items[${bundleItemIndex}][quantity]" class="form-control form-control-sm" value="${quantity}" min="1" required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-xs btn-danger text-white remove-item-row">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;

            container.appendChild(tr);

            // Bind remove handler
            tr.querySelector('.remove-item-row').addEventListener('click', function() {
                tr.remove();
            });

            // Initialize select2 inside row if select2 is loaded and it's a function
            if (window.jQuery && typeof jQuery.fn.select2 === 'function') {
                const $select = jQuery(tr).find('.select-book-item');
                $select.select2({
                    dropdownParent: jQuery('#addBundleModal'),
                    width: '100%'
                });
            }

            bundleItemIndex++;
        }

        // Add item row event
        const addRowBtn = document.getElementById('addBundleItemRowBtn');
        if (addRowBtn) {
            addRowBtn.addEventListener('click', function() {
                addBundleItemRow();
            });
        }

        // Open Add Bundle modal via button click
        const addNewBundleBtn = document.getElementById('addNewBundleBtn');
        if (addNewBundleBtn) {
            addNewBundleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('modal_bundle_id').value = '';
                document.getElementById('addBundleForm').reset();
                document.getElementById('bundleItemsContainer').innerHTML = '';
                document.getElementById('addBundleModalTitle').innerText = 'Add New Book Bundle';
                bundleItemIndex = 0;
                showBundleModal('addBundleModal');
            });
        }

        // Wire up close buttons on bundle modals
        document.querySelectorAll('#addBundleModal [data-bs-dismiss="modal"], #viewBundleModal [data-bs-dismiss="modal"], #deleteBundleModal [data-bs-dismiss="modal"]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var modal = this.closest('.modal');
                if (modal) hideBundleModal(modal.id);
            });
        });

        // Clear dynamic rows and reset form when add bundle modal is dismissed
        const addBundleModalEl = document.getElementById('addBundleModal');
        const bundleForm = document.getElementById('addBundleForm');

        // Add/Edit Bundle form submit
        if (bundleForm) {
            bundleForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const saveBtn = document.getElementById('saveBundleBtn');
                saveBtn.disabled = true;

                const formData = new FormData(this);
                const payload = {
                    name: formData.get('name'),
                    sku: formData.get('sku'),
                    price: formData.get('price'),
                    stock: formData.get('stock'),
                    description: formData.get('description'),
                    is_active: formData.get('is_active') ? 1 : 0,
                    items: []
                };

                const rows = document.querySelectorAll('#bundleItemsContainer tr');
                let hasItems = false;
                rows.forEach(row => {
                    const bookSelect = row.querySelector('.select-book-item');
                    const qtyInput = row.querySelector('input[type="number"]');
                    if (bookSelect && qtyInput) {
                        const bookId = bookSelect.value;
                        const qty = qtyInput.value;
                        if (bookId && qty) {
                            hasItems = true;
                            payload.items.push({
                                book_id: bookId,
                                quantity: qty
                            });
                        }
                    }
                });

                if (!hasItems) {
                    window.showAlert('Please add at least one book to the bundle.', 'danger');
                    saveBtn.disabled = false;
                    return;
                }

                const bundleId = document.getElementById('modal_bundle_id').value;
                const url = bundleId ? `/marketing/book-bundles/${bundleId}/update` : "{{ route('marketing.bundles.store') }}";

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    window.showAlert(data.message, 'success');
                    hideBundleModal('addBundleModal');
                    setTimeout(() => location.reload(), 1500);
                })
                .catch(err => {
                    console.error('Save Bundle Error:', err);
                    let msg = 'Failed to save bundle.';
                    if(err.errors) msg = Object.values(err.errors).flat().join(' ');
                    else if (err.message) msg = err.message;
                    window.showAlert(msg, 'danger');
                    saveBtn.disabled = false;
                });
            });
        }

        // View Bundle Click Handlers
        document.querySelectorAll('.view-bundle-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                fetch(`/marketing/book-bundles/${id}/edit`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('view_bundle_sku').innerText = '#' + data.sku;
                        document.getElementById('view_bundle_name').innerText = data.name;
                        document.getElementById('view_bundle_price').innerText = '₱' + parseFloat(data.price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        document.getElementById('view_bundle_stock').innerText = data.stock + ' pcs';
                        
                        const statusEl = document.getElementById('view_bundle_status');
                        if (data.is_active) {
                            statusEl.className = 'badge badge-success';
                            statusEl.innerText = 'Active';
                        } else {
                            statusEl.className = 'badge badge-light';
                            statusEl.innerText = 'Inactive';
                        }

                        document.getElementById('view_bundle_description').innerText = data.description || 'No description provided.';
                        
                        const listContainer = document.getElementById('view_bundle_books_list');
                        listContainer.innerHTML = '';
                        data.books.forEach(b => {
                            const li = document.createElement('li');
                            li.className = 'list-group-item d-flex justify-content-between align-items-center py-2';
                            li.innerHTML = `
                                <span>${b.name}</span>
                                <span class="badge bg-danger rounded-pill">x${b.pivot.quantity}</span>
                            `;
                            listContainer.appendChild(li);
                        });

                        showBundleModal('viewBundleModal');
                    });
            });
        });

        // Edit Bundle Click Handlers
        document.querySelectorAll('.edit-bundle-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                fetch(`/marketing/book-bundles/${id}/edit`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('modal_bundle_id').value = data.id;
                        document.getElementById('bundle_name').value = data.name;
                        document.getElementById('bundle_sku').value = data.sku;
                        document.getElementById('bundle_price').value = data.price;
                        document.getElementById('bundle_stock').value = data.stock;
                        document.getElementById('bundle_is_active').checked = !!data.is_active;
                        document.getElementById('bundle_description').value = data.description ?? '';
                        
                        const container = document.getElementById('bundleItemsContainer');
                        container.innerHTML = '';
                        bundleItemIndex = 0;
                        
                        data.books.forEach(b => {
                            addBundleItemRow(b.id, b.pivot.quantity);
                        });

                        document.getElementById('addBundleModalTitle').innerText = "Edit Book Bundle";
                        showBundleModal('addBundleModal');
                    });
            });
        });

        // Delete Bundle Click Handlers
        document.querySelectorAll('.delete-bundle-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('delete_bundle_id').value = this.dataset.id;
                showBundleModal('deleteBundleModal');
            });
        });

        const confirmDeleteBundleBtn = document.getElementById('confirmDeleteBundleBtn');
        if (confirmDeleteBundleBtn) {
            confirmDeleteBundleBtn.addEventListener('click', function() {
                const id = document.getElementById('delete_bundle_id').value;
                this.disabled = true;

                fetch(`/marketing/book-bundles/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    hideBundleModal('deleteBundleModal');
                    window.showAlert(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                })
                .catch(err => {
                    console.error('Delete Bundle Error:', err);
                    window.showAlert('Failed to delete bundle.', 'danger');
                    this.disabled = false;
                });
            });
        }
    </script>
    @endpush
</x-app-layout>

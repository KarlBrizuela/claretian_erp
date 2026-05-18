<x-app-layout :title="'Inventory Overview'" :sidebar="'production'">
    <div class="container-fluid">
        <!-- Add Project Modal -->
        <div class="modal fade" id="addProjectSidebar">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Project</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group">
                                <label class="text-black font-w500">Project Name</label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="text-black font-w500">Deadline</label>
                                <div class="cal-icon">
                                    <input type="date" class="form-control">
                                    <i class="far fa-calendar-alt"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="text-black font-w500">Client Name</label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-primary">CREATE</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-xl-3 col-xxl-3 col-lg-6 col-sm-6">
                <div class="card card-bd">
                    <div class="bg-primary card-border"></div>
                    <div class="card-body box-style">
                        <div class="media align-items-center">
                            <div class="media-body me-3">
                                <h2 class="num-text text-black font-w700">{{ $totalBooks }}</h2>
                                <span class="fs-14">Master Book Records</span>
                            </div>
                            <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-primary-light">
                                <i class="las la-cube fs-36 text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-xxl-3 col-lg-6 col-sm-6">
                <div class="card card-bd">
                    <div class="bg-warning card-border"></div>
                    <div class="card-body box-style">
                        <div class="media align-items-center">
                            <div class="media-body me-3">
                                <h2 class="num-text text-black font-w700">{{ $lowStock }}</h2>
                                <span class="fs-14">Low Stock Items</span>
                            </div>
                            <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-warning-light">
                                <i class="las la-exclamation-triangle fs-36 text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-xxl-3 col-lg-6 col-sm-6">
                <div class="card card-bd">
                    <div class="bg-danger card-border"></div>
                    <div class="card-body box-style">
                        <div class="media align-items-center">
                            <div class="media-body me-3">
                                <h2 class="num-text text-black font-w700">{{ $outOfStock }}</h2>
                                <span class="fs-14">Out of Stock</span>
                            </div>
                            <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-danger-light">
                                <i class="las la-times-circle fs-36 text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-xxl-3 col-lg-6 col-sm-6">
                <div class="card card-bd">
                    <div class="bg-success card-border"></div>
                    <div class="card-body box-style">
                        <div class="media align-items-center">
                            <div class="media-body me-3">
                                <h2 class="num-text text-black font-w700">₱{{ number_format($inventoryValue, 2) }}</h2>
                                <span class="fs-14">Inventory Value</span>
                            </div>
                            <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-success-light">
                                <i class="las la-coins fs-36 text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Product Inventory Table -->
        <div class="row">
            <div class="col-xl-12 col-xxl-12">
                <div class="card">
                    <div class="card-header border-0">
                        <div>
                            <h4 class="fs-20 mb-0 text-black">Master Book Registry</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-responsive-md">
                                <thead>
                                    <tr>
                                        <th><strong>BOOK ID</strong></th>
                                        <th><strong>TITLE</strong></th>
                                        <th><strong>CATEGORY</strong></th>
                                        <th><strong>UNIT COST</strong></th>
                                        <th><strong>STOCK</strong></th>
                                        <th><strong>MAX STOCK</strong></th>
                                        <th><strong>STATUS</strong></th>
                                        <th><strong>ACTION</strong></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($books as $book)
                                    <tr>
                                        <td><strong>#{{ $book->sku }}</strong></td>
                                        <td>{{ $book->name }}</td>
                                        <td>{{ $book->category }}</td>
                                        <td>₱{{ number_format($book->cost, 2) }}</td>
                                        <td><strong>{{ $book->stock }}</strong></td>
                                        <td><strong>{{ $book->max_stock ?? 'N/A' }}</strong></td>
                                        <td>
                                            @if($book->stock == 0)
                                                <div class="d-flex align-items-center">
                                                    <i class="fa fa-circle text-danger me-1"></i> Out of Stock
                                                </div>
                                            @elseif($book->stock <= $book->reorder_point)
                                                <div class="d-flex align-items-center">
                                                    <i class="fa fa-circle text-warning me-1"></i> Low Stock
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center">
                                                    <i class="fa fa-circle text-success me-1"></i> In Stock
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="openStockManagementModal({{ $book->id }}, '{{ $book->name }}', {{ $book->stock }}, {{ $book->max_stock ?? 0 }})">
                                                <i class="las la-pen"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No master books found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="pagination-info">
                                Showing {{ $books->firstItem() ?? 0 }} to {{ $books->lastItem() ?? 0 }} of {{ $books->total() }} entries
                            </div>
                            <nav>
                                {{ $books->links() }}
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Stock Movements -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header border-0">
                        <div>
                            <h4 class="fs-20 mb-0 text-black">Recent Stock Movements</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-responsive-md">
                                <thead>
                                    <tr>
                                        <th style="width:80px;"><strong>ITEM ID</strong></th>
                                        <th><strong>BOOK TITLE</strong></th>
                                        <th><strong>TYPE</strong></th>
                                        <th><strong>QUANTITY</strong></th>
                                        <th><strong>DATE</strong></th>
                                        <th><strong>STATUS</strong></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentMovements as $transaction)
                                    <tr>
                                        <td><strong>#{{ $transaction->book->sku ?? $transaction->book_id }}</strong></td>
                                        <td>{{ $transaction->book->name ?? 'Unknown' }}</td>
                                        <td>
                                            @if($transaction->type == 'in')
                                                <span class="badge light badge-success">Stock In</span>
                                            @elseif($transaction->type == 'out')
                                                <span class="badge light badge-danger">Stock Out</span>
                                            @else
                                                <span class="badge light badge-warning">Adjustment</span>
                                            @endif
                                        </td>
                                        <td class="{{ $transaction->type == 'out' ? 'text-danger' : 'text-success' }}">
                                            {{ $transaction->type == 'out' ? '-' : '+' }}{{ $transaction->quantity }}
                                        </td>
                                        <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            @if($transaction->status == 'completed')
                                                <span class="badge light badge-success">Completed</span>
                                            @elseif($transaction->status == 'pending')
                                                <span class="badge light badge-warning">Pending</span>
                                            @else
                                                <span class="badge light badge-danger">Cancelled</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No recent movements.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="pagination-info">
                                Showing {{ $recentMovements->count() > 0 ? 1 : 0 }} to {{ $recentMovements->count() }} of {{ $totalMovements }} entries
                            </div>
                            <nav>
                                <!-- Pagination logic for movements if needed, or just link to full log -->
                                <div class="text-end">
                                    <a href="{{ route('production.inventory.received') }}" class="text-primary">View All Transactions <i class="las la-arrow-right"></i></a>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Management Modal -->
    <div class="modal fade" id="stockManagementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h6 class="modal-title m-0 text-white"><i class="las la-pen me-2"></i>Stock Management</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label font-w600">Book Name</label>
                        <input type="text" class="form-control" id="mgmtBookName" disabled>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-w600">Current Stock</label>
                            <input type="number" class="form-control" id="mgmtCurrentStock" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-w600">Max Stock</label>
                            <input type="number" class="form-control" id="mgmtMaxStock" disabled>
                        </div>
                    </div>

                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="addTab" data-bs-toggle="tab" data-bs-target="#addTabContent" type="button" role="tab" aria-controls="addTabContent" aria-selected="true">
                                <i class="las la-plus me-1"></i>Add Stock
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="editTab" data-bs-toggle="tab" data-bs-target="#editTabContent" type="button" role="tab" aria-controls="editTabContent" aria-selected="false">
                                <i class="las la-edit me-1"></i>Edit Stock
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content">
                        <!-- Add Stock Tab -->
                        <div class="tab-pane fade show active" id="addTabContent" role="tabpanel" aria-labelledby="addTab">
                            <div class="mb-3">
                                <label class="form-label font-w600">Quantity to Add *</label>
                                <input type="number" class="form-control" id="mgmtAddQuantity" placeholder="Enter quantity" min="1">
                                <small class="text-muted" id="mgmtAddWarning"></small>
                            </div>
                            <div class="alert alert-info" id="mgmtAddPreview" style="display:none;">
                                <small><strong>New Stock:</strong> <span id="mgmtAddNewStock">0</span></small>
                            </div>
                        </div>
                        
                        <!-- Edit Stock Tab -->
                        <div class="tab-pane fade" id="editTabContent" role="tabpanel" aria-labelledby="editTab">
                            <div class="mb-3">
                                <label class="form-label font-w600">Set Stock to *</label>
                                <input type="number" class="form-control" id="mgmtEditQuantity" placeholder="Enter new stock value" min="0">
                                <small class="text-muted" id="mgmtEditWarning"></small>
                            </div>
                            <div class="alert alert-info" id="mgmtEditPreview" style="display:none;">
                                <small><strong>Change from:</strong> <span id="mgmtEditOldStock">0</span> to <span id="mgmtEditNewStock">0</span></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-primary" id="mgmtSaveBtn" onclick="saveStockManagement()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

    <script>
        function showNotification(message, type = 'success') {
            const toastContainer = document.getElementById('toastContainer');
            const toastId = 'toast-' + Date.now();
            
            const bgColor = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-warning';
            const icon = type === 'success' ? 'la-check-circle' : type === 'error' ? 'la-exclamation-circle' : 'la-info-circle';
            
            const toastHTML = `
                <div id="${toastId}" class="toast show" role="alert" style="min-width: 300px; margin-bottom: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                    <div class="toast-header ${bgColor} text-white">
                        <i class="las ${icon} me-2"></i>
                        <strong class="me-auto">${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            `;
            
            toastContainer.insertAdjacentHTML('beforeend', toastHTML);
            
            const toastElement = document.getElementById(toastId);
            const bsToast = new bootstrap.Toast(toastElement);
            bsToast.show();
            
            // Remove toast from DOM after it's hidden
            toastElement.addEventListener('hidden.bs.toast', function() {
                toastElement.remove();
            });
        }

        let currentBookName = null;
        let currentStock = null;
        let maxStock = null;
        let currentBookId = null;

        function openStockManagementModal(bookId, bookName, stock, max) {
            currentBookId = bookId;
            currentBookName = bookName;
            currentStock = stock;
            maxStock = max;

            // Set book info
            document.getElementById('mgmtBookName').value = bookName;
            document.getElementById('mgmtCurrentStock').value = stock;
            document.getElementById('mgmtMaxStock').value = max || 'Not Set';
            
            // Reset all inputs
            document.getElementById('mgmtAddQuantity').value = '';
            document.getElementById('mgmtAddWarning').innerHTML = '';
            document.getElementById('mgmtAddPreview').style.display = 'none';

            const modal = new bootstrap.Modal(document.getElementById('stockManagementModal'));
            modal.show();

            // Add event listeners for real-time preview
            document.getElementById('mgmtAddQuantity').addEventListener('keyup', function() {
                const quantity = parseInt(this.value) || 0;
                const newStock = currentStock + quantity;
                const warning = document.getElementById('mgmtAddWarning');
                const preview = document.getElementById('mgmtAddPreview');

                if (quantity > 0) {
                    preview.style.display = 'block';
                    document.getElementById('mgmtAddNewStock').textContent = newStock;

                    if (maxStock && newStock > maxStock) {
                        warning.innerHTML = `<span class="text-danger"><i class="las la-exclamation-circle"></i> Warning: New stock (${newStock}) exceeds max stock (${maxStock})</span>`;
                        document.getElementById('mgmtSaveBtn').disabled = true;
                    } else {
                        warning.innerHTML = '';
                        document.getElementById('mgmtSaveBtn').disabled = false;
                    }
                } else {
                    preview.style.display = 'none';
                    warning.innerHTML = '';
                }
            });

            // Add event listeners for edit quantity
            document.getElementById('mgmtEditQuantity').addEventListener('keyup', function() {
                const newStock = parseInt(this.value);
                const warning = document.getElementById('mgmtEditWarning');
                const preview = document.getElementById('mgmtEditPreview');

                if (newStock >= 0) {
                    preview.style.display = 'block';
                    document.getElementById('mgmtEditOldStock').textContent = currentStock;
                    document.getElementById('mgmtEditNewStock').textContent = newStock;

                    if (maxStock && newStock > maxStock) {
                        warning.innerHTML = `<span class="text-danger"><i class="las la-exclamation-circle"></i> Warning: New stock (${newStock}) exceeds max stock (${maxStock})</span>`;
                        document.getElementById('mgmtSaveBtn').disabled = true;
                    } else {
                        warning.innerHTML = '';
                        document.getElementById('mgmtSaveBtn').disabled = false;
                    }
                } else {
                    preview.style.display = 'none';
                    warning.innerHTML = '';
                }
            });
        }

        function saveStockManagement() {
            const activeTab = document.querySelector('.nav-link.active');
            if (activeTab.id === 'addTab') {
                saveAddStock();
            } else if (activeTab.id === 'editTab') {
                saveEditStock();
            }
        }

        function saveAddStock() {
            const quantity = parseInt(document.getElementById('mgmtAddQuantity').value);

            if (!quantity || quantity < 1) {
                showNotification('Please enter a valid quantity', 'warning');
                return;
            }

            const newStock = currentStock + quantity;
            if (maxStock && newStock > maxStock) {
                showNotification(`Cannot add stock. New total (${newStock}) would exceed max stock (${maxStock})`, 'error');
                return;
            }

            // Send to backend
            fetch(`/production/inventory/update-stock/${currentBookId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    action: 'add',
                    quantity: quantity,
                    new_stock: newStock
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Stock added successfully!', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('stockManagementModal')).hide();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
            });
        }

        function saveEditStock() {
            const newStock = parseInt(document.getElementById('mgmtEditQuantity').value);

            if (newStock === null || newStock === undefined || isNaN(newStock)) {
                showNotification('Please enter a valid stock value', 'warning');
                return;
            }

            if (maxStock && newStock > maxStock) {
                showNotification(`Cannot set stock. New value (${newStock}) exceeds max stock (${maxStock})`, 'error');
                return;
            }

            // Send to backend
            fetch(`/production/inventory/update-stock/${currentBookId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    action: 'set',
                    new_stock: newStock
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Stock updated successfully!', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('stockManagementModal')).hide();
                } else {
                    showNotification('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
            });
        }
    </script>
</x-app-layout>
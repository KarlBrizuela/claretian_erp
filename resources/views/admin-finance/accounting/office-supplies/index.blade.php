<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fs-22 mb-1 fw-bold text-dark" style="letter-spacing: -0.5px;">Office Supplies Inventory</h4>
                            <p class="text-muted small mb-0">Manage accounting division office supplies, pricing, and stock levels.</p>
                        </div>
                        <button class="btn btn-primary btn-sm px-4 py-2 d-flex align-items-center gap-2 shadow-sm" 
                                style="background: #ff0000; border: none; border-radius: 8px; font-weight: 600; font-size: 0.9rem;"
                                data-bs-toggle="modal" data-bs-target="#addSupplyModal">
                            <i class="fas fa-plus"></i> Add New Item
                        </button>
                    </div>
                    
                    <div class="card-body">
                        <!-- Filters -->
                        <div class="row mb-4 align-items-center">
                            <div class="col-md-6">
                                <form action="{{ route('admin-finance.accounting.office-supplies.index') }}" method="GET" class="d-flex gap-2">
                                    <div class="input-group input-group-sm border rounded-pill px-3 py-1 bg-light" style="max-width: 400px; align-items: center;">
                                        <i class="fas fa-search text-muted me-2"></i>
                                        <input type="text" name="search" class="form-control border-0 bg-transparent" 
                                               placeholder="Search by item name..." value="{{ $search }}" style="outline: none; box-shadow: none;">
                                        @if($search)
                                            <a href="{{ route('admin-finance.accounting.office-supplies.index') }}" class="btn btn-link text-muted p-0 me-2" title="Clear search">
                                                <i class="fas fa-times-circle"></i>
                                            </a>
                                        @endif
                                    </div>
                                    <button type="submit" class="btn btn-dark btn-sm rounded-pill px-4" style="font-weight: 600;">Filter</button>
                                </form>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-hover table-responsive-md align-middle" style="border-collapse: separate; border-spacing: 0 8px;">
                                <thead>
                                    <tr class="text-secondary" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #f0f0f0;">
                                        <th class="ps-4">Item Name</th>
                                        <th>Item Price</th>
                                        <th>Items Stock</th>
                                        <th>Total Valuation</th>
                                        <th class="text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($supplies as $supply)
                                    <tr class="bg-white shadow-sm hover-row" style="border-radius: 8px; transition: transform 0.2s, box-shadow 0.2s;">
                                        <td class="ps-4 py-3 fw-bold text-dark fs-15">{{ $supply->item_name }}</td>
                                        <td class="py-3 text-dark font-w600">₱{{ number_format($supply->item_price, 2) }}</td>
                                        <td class="py-3">
                                            @if($supply->items_stock <= 5)
                                                <span class="badge badge-danger rounded-pill px-3 text-white fw-600" style="background-color: #dc3545;">
                                                    Low Stock ({{ $supply->items_stock }} {{ $supply->unit ?? 'pcs' }})
                                                </span>
                                            @else
                                                <span class="badge badge-success rounded-pill px-3 text-white fw-600" style="background-color: #28a745;">
                                                    {{ $supply->items_stock }} {{ $supply->unit ?? 'pcs' }} in stock
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-danger fw-bold fs-15">₱{{ number_format($supply->item_price * $supply->items_stock, 2) }}</td>
                                        <td class="text-end pe-4 py-3">
                                            <div class="d-flex justify-content-end gap-2 align-items-center">
                                                <button class="btn btn-success btn-sm text-white px-3 py-1.5 add-stock-btn shadow-sm"
                                                        style="border-radius: 6px; font-weight: 600; border: none; font-size: 0.82rem; background-color: #28a745;"
                                                        data-bs-toggle="modal" data-bs-target="#addStockModal"
                                                        data-id="{{ $supply->id }}"
                                                        data-name="{{ $supply->item_name }}"
                                                        data-stock="{{ $supply->items_stock }}">
                                                    <i class="fas fa-plus me-1"></i> Add Stock
                                                </button>

                                                <button class="btn btn-warning btn-sm text-dark px-3 py-1.5 edit-btn shadow-sm"
                                                        style="border-radius: 6px; font-weight: 600; border: none; font-size: 0.82rem;"
                                                        data-bs-toggle="modal" data-bs-target="#editSupplyModal"
                                                        data-id="{{ $supply->id }}"
                                                        data-name="{{ $supply->item_name }}"
                                                        data-price="{{ $supply->item_price }}"
                                                        data-stock="{{ $supply->items_stock }}"
                                                        data-unit="{{ $supply->unit ?? 'pcs' }}">
                                                    <i class="fas fa-edit me-1"></i> Edit
                                                </button>
                                                
                                                <button class="btn btn-outline-danger btn-sm px-3 py-1.5 shadow-sm"
                                                        style="border-radius: 6px; font-weight: 600; font-size: 0.82rem;"
                                                        onclick="confirmDelete({{ $supply->id }})">
                                                    <i class="fas fa-trash-alt me-1"></i> Delete
                                                </button>
                                                
                                                <form id="delete-form-{{ $supply->id }}" action="{{ route('admin-finance.accounting.office-supplies.destroy', $supply->id) }}" method="POST" class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <div class="mb-3"><i class="fas fa-box-open fs-40 text-light"></i></div>
                                            <span class="fs-15">No office supply items found matching your criteria.</span>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Links -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted small">
                                Showing {{ $supplies->firstItem() ?? 0 }} to {{ $supplies->lastItem() ?? 0 }} of {{ $supplies->total() }} items
                            </div>
                            <div>
                                {{ $supplies->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logs Card -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <h4 class="fs-22 mb-1 fw-bold text-dark" style="letter-spacing: -0.5px;">Stock Transaction History</h4>
                        <p class="text-muted small mb-0">Logs of all office supply stock additions and replenishments.</p>
                    </div>
                    <div class="card-body">
                        <!-- Log Filters -->
                        <form action="{{ route('admin-finance.accounting.office-supplies.index') }}" method="GET" class="row mb-4 g-3 align-items-end">
                            @if($search)
                                <input type="hidden" name="search" value="{{ $search }}">
                            @endif

                            <div class="col-md-4">
                                <label for="log_search" class="form-label fw-bold text-dark small"><i class="fas fa-search me-1 text-primary"></i> Search Item</label>
                                <input type="text" id="log_search" name="log_search" class="form-control form-control-sm border-light-subtle rounded-pill px-3" 
                                       placeholder="Search by item name..." value="{{ $log_search }}">
                            </div>

                            <div class="col-md-3">
                                <label for="log_start_date" class="form-label fw-bold text-dark small"><i class="fas fa-calendar-alt me-1 text-primary"></i> Start Date</label>
                                <input type="date" id="log_start_date" name="log_start_date" class="form-control form-control-sm border-light-subtle rounded-pill px-3" 
                                       value="{{ $log_start_date }}">
                            </div>

                            <div class="col-md-3">
                                <label for="log_end_date" class="form-label fw-bold text-dark small"><i class="fas fa-calendar-alt me-1 text-primary"></i> End Date</label>
                                <input type="date" id="log_end_date" name="log_end_date" class="form-control form-control-sm border-light-subtle rounded-pill px-3" 
                                       value="{{ $log_end_date }}">
                            </div>

                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-dark btn-sm rounded-pill px-4 flex-grow-1" style="font-weight: 600; height: 36px;">Filter</button>
                                @if($log_search || $log_start_date || $log_end_date)
                                    <a href="{{ route('admin-finance.accounting.office-supplies.index', ['search' => $search]) }}" 
                                       class="btn btn-light btn-sm rounded-pill px-3 d-flex align-items-center justify-content-center" 
                                       style="border: 1px solid #ddd; height: 36px;" title="Reset filters">
                                        <i class="fas fa-undo"></i>
                                    </a>
                                @endif
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover table-responsive-md align-middle" style="border-collapse: separate; border-spacing: 0 8px;">
                                <thead>
                                    <tr class="text-secondary" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #f0f0f0;">
                                        <th class="ps-4">Date & Time</th>
                                        <th>Item Name</th>
                                        <th>Qty Added</th>
                                        <th>Unit Price</th>
                                        <th>Prev Stock</th>
                                        <th>New Stock</th>
                                        <th>Total Expense</th>
                                        <th>Supplier</th>
                                        <th>Added By</th>
                                        <th class="pe-4">Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $log)
                                    @php
                                        $price = $log->unit_price > 0 ? $log->unit_price : ($log->officeSupply->item_price ?? 0);
                                        $totalCost = $log->quantity * $price;
                                    @endphp
                                    <tr class="bg-white shadow-sm" style="border-radius: 8px;">
                                        <td class="ps-4 py-3 text-dark small">{{ $log->created_at->format('Y-m-d h:i A') }}</td>
                                        <td class="py-3 fw-bold text-dark">{{ $log->item_name ?? ($log->officeSupply->item_name ?? 'Deleted Item') }}</td>
                                        <td class="py-3"><span class="text-success fw-bold">+{{ $log->quantity }}</span></td>
                                        <td class="py-3 text-dark fw-medium">₱{{ number_format($price, 2) }}</td>
                                        <td class="py-3 text-muted">{{ $log->previous_stock }}</td>
                                        <td class="py-3 fw-bold text-dark">{{ $log->new_stock }}</td>
                                        <td class="py-3 fw-bold text-danger">₱{{ number_format($totalCost, 2) }}</td>
                                        <td class="py-3 text-dark">{{ $log->supplier->company_name ?? 'N/A' }}</td>
                                        <td class="py-3 text-muted small">{{ $log->addedBy->name ?? 'N/A' }}</td>
                                        <td class="pe-4 py-3 text-muted small">{{ $log->notes ?: '—' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-5 text-muted">
                                            <div class="mb-3"><i class="fas fa-history fs-30 text-light"></i></div>
                                            <span>No stock transaction history logs found.</span>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Logs Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted small">
                                Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} logs
                            </div>
                            <div>
                                {{ $logs->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addSupplyModal" tabindex="-1" aria-labelledby="addSupplyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="addSupplyModalLabel">Add Office Supply Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin-finance.accounting.office-supplies.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="item_name" class="form-label fw-bold text-dark small">Item Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control border-light-subtle rounded px-3 py-2" id="item_name" name="item_name" placeholder="e.g. A4 Bond Paper" required>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="item_price" class="form-label fw-bold text-dark small">Item Price (₱) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" class="form-control border-light-subtle rounded px-3 py-2" id="item_price" name="item_price" placeholder="0.00" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="items_stock" class="form-label fw-bold text-dark small">Items Stock <span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control border-light-subtle rounded px-3 py-2" id="items_stock" name="items_stock" placeholder="0" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="unit" class="form-label fw-bold text-dark small">Unit <span class="text-danger">*</span></label>
                                <select class="form-select border-light-subtle rounded px-3 py-2" id="unit" name="unit" required>
                                    <option value="pcs">pcs (Pieces)</option>
                                    <option value="box">box (Box)</option>
                                    <option value="pack">pack (Pack)</option>
                                    <option value="ream">ream (Ream)</option>
                                    <option value="set">set (Set)</option>
                                    <option value="roll">roll (Roll)</option>
                                    <option value="pad">pad (Pad)</option>
                                    <option value="bundle">bundle (Bundle)</option>
                                    <option value="bottle">bottle (Bottle)</option>
                                    <option value="pair">pair (Pair)</option>
                                    <option value="cartridge">cartridge (Cartridge/Toner)</option>
                                    <option value="unit">unit (Unit)</option>
                                    <option value="kg">kg (Kilogram)</option>
                                    <option value="liter">liter (Liter)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-3">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal" style="font-weight: 600;">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4" style="background: #ff0000; border: none; font-weight: 600;">Save Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editSupplyModal" tabindex="-1" aria-labelledby="editSupplyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="editSupplyModalLabel">Edit Office Supply Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editSupplyForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="edit_item_name" class="form-label fw-bold text-dark small">Item Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control border-light-subtle rounded px-3 py-2" id="edit_item_name" name="item_name" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_item_price" class="form-label fw-bold text-dark small">Item Price (₱) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" class="form-control border-light-subtle rounded px-3 py-2" id="edit_item_price" name="item_price" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_unit" class="form-label fw-bold text-dark small">Unit <span class="text-danger">*</span></label>
                                <select class="form-select border-light-subtle rounded px-3 py-2" id="edit_unit" name="unit" required>
                                    <option value="pcs">pcs (Pieces)</option>
                                    <option value="box">box (Box)</option>
                                    <option value="pack">pack (Pack)</option>
                                    <option value="ream">ream (Ream)</option>
                                    <option value="set">set (Set)</option>
                                    <option value="roll">roll (Roll)</option>
                                    <option value="pad">pad (Pad)</option>
                                    <option value="bundle">bundle (Bundle)</option>
                                    <option value="bottle">bottle (Bottle)</option>
                                    <option value="pair">pair (Pair)</option>
                                    <option value="cartridge">cartridge (Cartridge/Toner)</option>
                                    <option value="unit">unit (Unit)</option>
                                    <option value="kg">kg (Kilogram)</option>
                                    <option value="liter">liter (Liter)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-3">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal" style="font-weight: 600;">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4" style="background: #ff0000; border: none; font-weight: 600;">Update Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Stock Modal -->
    <div class="modal fade" id="addStockModal" tabindex="-1" aria-labelledby="addStockModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="addStockModalLabel">Add Stock to Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addStockForm" action="" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">Item Name</label>
                            <input type="text" class="form-control border-0 bg-light rounded px-3 py-2 text-dark fw-bold" id="stock_item_name" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">Current Stock</label>
                            <input type="text" class="form-control border-0 bg-light rounded px-3 py-2 text-dark" id="stock_current_stock" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="supplier_id" class="form-label fw-bold text-dark small">Supplier <span class="text-danger">*</span></label>
                            <select class="form-select border-light-subtle rounded px-3 py-2" id="supplier_id" name="supplier_id" required>
                                <option value="">-- Select Supplier --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="quantity" class="form-label fw-bold text-dark small">Quantity to Add <span class="text-danger">*</span></label>
                            <input type="number" min="1" class="form-control border-light-subtle rounded px-3 py-2" id="quantity" name="quantity" placeholder="e.g. 50" required>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label fw-bold text-dark small">Remarks / Notes</label>
                            <input type="text" class="form-control border-light-subtle rounded px-3 py-2" id="notes" name="notes" placeholder="e.g. Replenishment">
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-3">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal" style="font-weight: 600;">Cancel</button>
                        <button type="submit" class="btn btn-success rounded-pill px-4" style="background: #28a745; border: none; font-weight: 600; color: #ffffff;">Add Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .hover-row:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.05) !important;
            cursor: default;
        }
        .btn-close {
            box-shadow: none !important;
        }
        .pagination {
            margin-bottom: 0;
        }
        .page-item.active .page-link {
            background-color: #ff0000 !important;
            border-color: #ff0000 !important;
            color: #ffffff !important;
        }
        .page-link {
            color: #ff0000;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Edit modal data population
            $('.edit-btn').on('click', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const price = $(this).data('price');
                const stock = $(this).data('stock');
                const unit = $(this).data('unit') || 'pcs';
                
                $('#edit_item_name').val(name);
                $('#edit_item_price').val(price);
                $('#edit_items_stock').val(stock);
                $('#edit_unit').val(unit);
                
                // Update form action URL dynamically
                const route = "{{ route('admin-finance.accounting.office-supplies.update', ':id') }}";
                $('#editSupplyForm').attr('action', route.replace(':id', id));
            });

            // Add Stock modal data population
            $('.add-stock-btn').on('click', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const stock = $(this).data('stock');
                
                $('#stock_item_name').val(name);
                $('#stock_current_stock').val(stock + ' in stock');
                $('#quantity').val('');
                $('#notes').val('');
                $('#supplier_id').val('');
                
                // Update form action URL dynamically
                const route = "{{ route('admin-finance.accounting.office-supplies.add-stock', ':id') }}";
                $('#addStockForm').attr('action', route.replace(':id', id));
            });
        });

        // Use custom bespoke modal system from app-layout
        function confirmDelete(id) {
            window.showConfirm("Are you sure you want to delete this office supply item?", function() {
                document.getElementById('delete-form-' + id).submit();
            });
        }
    </script>
    @endpush
</x-app-layout>

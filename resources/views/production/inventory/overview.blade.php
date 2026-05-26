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
        
        <!-- Inventory Management Tabs -->
        <div class="row mb-4">
            <div class="col-xl-12">
                <ul class="nav nav-tabs" role="tablist" style="border-bottom: 2px solid #e3e6f0;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="stocks-tab" data-bs-toggle="tab" data-bs-target="#stocks-content" type="button" role="tab" aria-controls="stocks-content" aria-selected="true">
                            <i class="las la-cubes me-2"></i>Stock Overview
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sites-tab" data-bs-toggle="tab" data-bs-target="#sites-content" type="button" role="tab" aria-controls="sites-content" aria-selected="false">
                            <i class="las la-map-marker me-2"></i>Sites
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Stock Overview Tab -->
            <div class="tab-pane fade show active" id="stocks-content" role="tabpanel" aria-labelledby="stocks-tab">
        
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

            <!-- Sites Tab Content -->
            <div class="tab-pane fade" id="sites-content" role="tabpanel" aria-labelledby="sites-tab">
    

                <!-- Sites List -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header border-0">
                                <h4 class="fs-20 mb-0 text-black">Warehouse/Sites Management</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-responsive-md">
                                        <thead>
                                            <tr>
                                                <th><strong>SITE NAME</strong></th>
                                                <th><strong>CODE</strong></th>
                                                <th><strong>LOCATION</strong></th>
                                                <th><strong>TOTAL INVENTORY</strong></th>
                                                <th><strong>TOTAL VALUE</strong></th>
                                                <th><strong>STATUS</strong></th>
                                                <th><strong>ACTION</strong></th>
                                            </tr>
                                        </thead>
                                        <tbody id="sitesTableBody">
                                            @forelse($sites ?? [] as $site)
                                            <tr>
                                                <td><strong>{{ $site->name }}</strong></td>
                                                <td>{{ $site->code ?? 'N/A' }}</td>
                                                <td>{{ $site->location ?? 'N/A' }}</td>
                                                <td>{{ $site->inventory->sum('quantity') }} items</td>
                                                <td>₱{{ number_format($site->getTotalInventoryValue(), 2) }}</td>
                                                <td>
                                                    @if($site->is_active)
                                                        <span class="badge light badge-success">Active</span>
                                                    @else
                                                        <span class="badge light badge-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewSiteInventory{{ $site->id }}" title="View Inventory">
                                                        <i class="las la-boxes"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editSiteModal{{ $site->id }}" title="Edit Site">
                                                        <i class="las la-pen"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No sites found. <a href="#" data-bs-toggle="modal" data-bs-target="#addSiteModal">Add a new site</a></td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock Transfers Section -->
                <div class="row mt-4">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header border-0">
                                <h4 class="fs-20 mb-0 text-black">Pending Stock Transfers</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-responsive-md">
                                        <thead>
                                            <tr>
                                                <th><strong>FROM</strong></th>
                                                <th><strong>TO</strong></th>
                                                <th><strong>BOOK</strong></th>
                                                <th><strong>QUANTITY</strong></th>
                                                <th><strong>STATUS</strong></th>
                                                <th><strong>ACTION</strong></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($pendingTransfers ?? [] as $transfer)
                                            <tr>
                                                <td>{{ $transfer->fromSite->name }}</td>
                                                <td>{{ $transfer->toSite->name }}</td>
                                                <td>{{ $transfer->book->name }}</td>
                                                <td><strong>{{ $transfer->quantity }}</strong></td>
                                                <td>
                                                    @if($transfer->status == 'pending')
                                                        <span class="badge light badge-warning">Pending</span>
                                                    @elseif($transfer->status == 'completed')
                                                        <span class="badge light badge-success">Completed</span>
                                                    @else
                                                        <span class="badge light badge-danger">Rejected</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($transfer->status == 'pending')
                                                        <button class="btn btn-xs btn-success" onclick="approveTransfer({{ $transfer->id }})">
                                                            <i class="las la-check"></i> Approve
                                                        </button>
                                                        <button class="btn btn-xs btn-danger" onclick="rejectTransfer({{ $transfer->id }})">
                                                            <i class="las la-times"></i> Reject
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No pending transfers</td>
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
    </div>

    <!-- Per-Site Modals -->
    @forelse($sites ?? [] as $site)
        <!-- View Site Inventory Modal -->
        <div class="modal fade" id="viewSiteInventory{{ $site->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h6 class="modal-title text-white">Inventory at {{ $site->name }}</h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @if($site->inventory->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Book Title</th>
                                            <th>Quantity</th>
                                            <th>Reorder Point</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($site->inventory as $inv)
                                        <tr>
                                            <td>{{ $inv->book->name ?? 'Unknown' }}</td>
                                            <td><strong>{{ $inv->quantity }}</strong></td>
                                            <td>{{ $inv->reorder_point ?? 'N/A' }}</td>
                                            <td>
                                                @if($inv->getStockStatus() == 'out_of_stock')
                                                    <span class="badge light badge-danger">Out of Stock</span>
                                                @elseif($inv->getStockStatus() == 'low_stock')
                                                    <span class="badge light badge-warning">Low Stock</span>
                                                @else
                                                    <span class="badge light badge-success">In Stock</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-xs btn-primary" data-bs-toggle="modal" data-bs-target="#transferStockModal" onclick="initTransferModal({{ $site->id }}, {{ $inv->book_id }}, '{{ $site->name }}', '{{ $inv->book->name }}', {{ $inv->quantity }})">
                                                    <i class="las la-exchange-alt"></i> Transfer
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">No inventory at this site</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>


        <!-- Edit Site Modal -->
        <div class="modal fade" id="editSiteModal{{ $site->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-white">
                        <h6 class="modal-title text-white">
                            <i class="las la-edit me-2"></i>
                            Edit {{ $site->name }}
                        </h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form class="editSiteForm" data-site-id="{{ $site->id }}">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label font-w600">Site Name *</label>
                                <input type="text" name="name" class="form-control" value="{{ $site->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-w600">Site Code</label>
                                <input type="text" name="code" class="form-control" value="{{ $site->code ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-w600">Location</label>
                                <input type="text" name="location" class="form-control" value="{{ $site->location ?? '' }}">
                            </div>
                            <div class="mb-0">
                                <label class="form-label font-w600">Description</label>
                                <textarea name="description" class="form-control" rows="3">{{ $site->description ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-warning">Update Site</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @empty
    @endforelse

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

    <!-- Add Site Modal -->
    <div class="modal fade" id="addSiteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h6 class="modal-title text-white"><i class="las la-plus me-2"></i>Add New Site/Warehouse</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="addSiteForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label font-w600">Site Name *</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g., Main Warehouse" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-w600">Site Code</label>
                            <input type="text" name="code" class="form-control" placeholder="e.g., WH-001">
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-w600">Location</label>
                            <input type="text" name="location" class="form-control" placeholder="e.g., Makati City">
                        </div>
                        <div class="mb-0">
                            <label class="form-label font-w600">Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Site description..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary">Add Site</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Transfer Stock Modal -->
    <div class="modal fade" id="transferStockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h6 class="modal-title text-white"><i class="las la-exchange-alt me-2"></i>Transfer Stock</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="transferStockForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label font-w600">From Site</label>
                            <input type="text" id="fromSiteName" class="form-control" disabled>
                            <input type="hidden" id="fromSiteId" name="from_site_id">
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-w600">Book</label>
                            <input type="text" id="bookName" class="form-control" disabled>
                            <input type="hidden" id="bookId" name="book_id">
                            <small class="text-muted" id="availableQty"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-w600">To Site *</label>
                            <select name="to_site_id" class="form-control default-select" required id="toSiteSelect">
                                <option value="">-- Select Destination Site --</option>
                                @foreach($sites ?? [] as $site)
                                    <option value="{{ $site->id }}">{{ $site->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-w600">Quantity to Transfer *</label>
                            <input type="number" name="quantity" class="form-control" placeholder="0" min="1" required id="transferQty">
                            <small class="text-muted">Available: <span id="maxTransferQty">0</span></small>
                        </div>
                        <div class="mb-0">
                            <label class="form-label font-w600">Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Transfer notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-warning">Request Transfer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Workflow Guide Modal -->
    <div class="modal fade" id="workflowGuideModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h6 class="modal-title text-white"><i class="las la-lightbulb me-2"></i>Multi-Site Inventory & Stock Transfer Workflow</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6 class="mb-3">📍 Step-by-Step Guide</h6>
                    
                    <div class="timeline-item mb-4">
                        <div class="timeline-marker bg-primary"><strong>1</strong></div>
                        <div class="timeline-content">
                            <h6 class="font-w600">Create Warehouse/Site</h6>
                            <p class="text-muted mb-0">Click "Add New Site" to create a new warehouse location (e.g., Main Warehouse, Lazada Warehouse, Bookstore). Set the name, code, location, and description.</p>
                        </div>
                    </div>

                    <div class="timeline-item mb-4">
                        <div class="timeline-marker bg-success"><strong>2</strong></div>
                        <div class="timeline-content">
                            <h6 class="font-w600">Add Stock to Site</h6>
                            <p class="text-muted mb-0">Click the <i class="las la-plus"></i> button on any site to add inventory. Select the book/product, quantity, and optional reorder point. This is your initial stock setup.</p>
                        </div>
                    </div>

                    <div class="timeline-item mb-4">
                        <div class="timeline-marker bg-warning"><strong>3</strong></div>
                        <div class="timeline-content">
                            <h6 class="font-w600">View Site Inventory</h6>
                            <p class="text-muted mb-0">Click the <i class="las la-boxes"></i> button to see all items in that warehouse and their stock levels (In Stock / Low Stock / Out of Stock).</p>
                        </div>
                    </div>

                    <div class="timeline-item mb-4">
                        <div class="timeline-marker bg-danger"><strong>4</strong></div>
                        <div class="timeline-content">
                            <h6 class="font-w600">Request Stock Transfer</h6>
                            <p class="text-muted mb-0">From the inventory view, click <i class="las la-exchange-alt"></i> on a low-stock item. Fill in the destination site and quantity. Submit to create a transfer request. Status: <span class="badge badge-warning">Pending</span></p>
                        </div>
                    </div>

                    <div class="timeline-item mb-4">
                        <div class="timeline-marker bg-success"><strong>5</strong></div>
                        <div class="timeline-content">
                            <h6 class="font-w600">Manager Approval</h6>
                            <p class="text-muted mb-0">Marketing Manager reviews pending transfers in the "Pending Stock Transfers" table. Click <strong>Approve</strong> to process the transfer or <strong>Reject</strong> to decline.</p>
                        </div>
                    </div>

                    <div class="timeline-item mb-0">
                        <div class="timeline-marker bg-info"><strong>6</strong></div>
                        <div class="timeline-content">
                            <h6 class="font-w600">Automatic Stock Update</h6>
                            <p class="text-muted mb-0">Once approved, stock is automatically deducted from the source site and added to the destination site. Status changes to <span class="badge badge-success">Completed</span></p>
                        </div>
                    </div>

                    <hr class="my-3">
                    <h6 class="mb-2">📌 Quick Reference</h6>
                    <ul class="text-muted small">
                        <li><strong>Lazada Manager:</strong> Can request stock transfers when inventory is low</li>
                        <li><strong>Marketing Manager:</strong> Approves or rejects transfer requests</li>
                        <li><strong>System:</strong> Automatically updates stock after approval (no manual entry needed)</li>
                        <li><strong>Audit Trail:</strong> All transfers are tracked with created_by, approved_by, and timestamps</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
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

        // Site Management Functions
        document.getElementById('addSiteForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('/production/sites/store', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Site added successfully!', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('addSiteModal')).hide();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
            });
        });

        // Edit Site Forms
        document.querySelectorAll('.editSiteForm').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const siteId = this.getAttribute('data-site-id');
                const formData = new FormData(this);
                
                fetch(`/production/sites/update/${siteId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        bootstrap.Modal.getInstance(document.getElementById(`editSiteModal${siteId}`)).hide();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification('Error: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred', 'error');
                });
            });
        });
        
        document.getElementById('transferStockForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('/production/sites/transfer', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Transfer request submitted!', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('transferStockModal')).hide();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
            });
        });

        function initTransferModal(siteId, bookId, siteName, bookName, quantity) {
            document.getElementById('fromSiteId').value = siteId;
            document.getElementById('bookId').value = bookId;
            document.getElementById('fromSiteName').value = siteName;
            document.getElementById('bookName').value = bookName;
            document.getElementById('maxTransferQty').textContent = quantity;
            document.getElementById('transferQty').setAttribute('max', quantity);
        }

        function approveTransfer(transferId) {
            if (confirm('Approve this transfer?')) {
                fetch(`/production/sites/approve-transfer/${transferId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Transfer approved!', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification('Error: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    showNotification('An error occurred', 'error');
                });
            }
        }

        function rejectTransfer(transferId) {
            if (confirm('Reject this transfer?')) {
                fetch(`/production/sites/reject-transfer/${transferId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Transfer rejected!', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification('Error: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    showNotification('An error occurred', 'error');
                });
            }
        }
    </script>
</x-app-layout>
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
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="transfer-workflow-tab" data-bs-toggle="tab" data-bs-target="#transfer-workflow-content" type="button" role="tab" aria-controls="transfer-workflow-content" aria-selected="false">
                            <i class="las la-random me-2"></i>Transfer Workflow
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
                                <div>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#transferStockModal" onclick="initTransferModalFromMaster()">
                                        <i class="las la-exchange-alt me-1"></i>Transfer Stock
                                    </button>
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
                                            @php
                                                // Get Main Warehouse inventory for this book
                                                $mainWarehouseQuantity = 0;
                                                if($mainWarehouse) {
                                                    $mainWarehouseQuantity = $mainWarehouse->inventory()
                                                        ->where('book_id', $book->id)
                                                        ->sum('quantity');
                                                }
                                            @endphp
                                            <tr>
                                                <td><strong>#{{ $book->sku }}</strong></td>
                                                <td>{{ $book->name }}</td>
                                                <td>{{ $book->category }}</td>
                                                <td>₱{{ number_format($book->cost, 2) }}</td>
                                                <td><strong>{{ $mainWarehouseQuantity }}</strong></td>
                                                <td><strong>{{ $book->max_stock ?? 'N/A' }}</strong></td>
                                                <td>
                                                    @if($mainWarehouseQuantity == 0)
                                                        <div class="d-flex align-items-center">
                                                            <i class="fa fa-circle text-danger me-1"></i> Out of Stock
                                                        </div>
                                                    @elseif($mainWarehouseQuantity <= ($book->reorder_point ?? 0))
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
                                                    <button class="btn btn-sm btn-danger" onclick="openStockManagementModal({{ $book->id }}, '{{ addslashes($book->name) }}', {{ $book->stock }}, {{ $book->max_stock ?? 0 }})">
                                                        <i class="las la-pen"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="8" class="text-center">No master books found.您
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
                                                <td colspan="6" class="text-center">No recent movements.您
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
                            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                                <h4 class="fs-20 mb-0 text-black">Warehouse/Sites Management</h4>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSiteModal">
                                    <i class="las la-plus me-2"></i>Add Site
                                </button>
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
                                                    <button class="btn btn-sm btn-danger" onclick="deleteSite({{ $site->id }}, '{{ $site->name }}')" title="Delete Site">
                                                        <i class="las la-trash"></i>
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
                                                <th><strong>APPROVER</strong></th>
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
                                                <td>{{ $transfer->approval_division ?? 'Production' }} Manager/Supervisor</td>
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
                                                    @if($transfer->status == 'pending' && $transfer->canBeApprovedBy(auth()->user()))
                                                        <button class="btn btn-xs btn-success" onclick="approveTransfer({{ $transfer->id }})">
                                                            <i class="las la-check"></i> Approve
                                                        </button>
                                                        <button class="btn btn-xs btn-danger" onclick="rejectTransfer({{ $transfer->id }})">
                                                            <i class="las la-times"></i> Reject
                                                        </button>
                                                    @elseif($transfer->status == 'pending')
                                                        <span class="text-muted small">Waiting for approval</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No pending transfers</td>
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

            <!-- Transfer Workflow Tab Content -->
            <div class="tab-pane fade" id="transfer-workflow-content" role="tabpanel" aria-labelledby="transfer-workflow-tab">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header border-0">
                                <div>
                                    <h4 class="fs-20 mb-0 text-black">Stock Transfer Workflow</h4>
                                    <small class="text-muted">Approval → Accounting → Logistics Assignment → Completion</small>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-responsive-md align-middle">
                                        <thead>
                                            <tr>
                                                <th><strong>REF</strong></th>
                                                <th><strong>FROM / TO</strong></th>
                                                <th><strong>BOOK</strong></th>
                                                <th><strong>QTY</strong></th>
                                                <th><strong>REQUESTED BY</strong></th>
                                                <th><strong>ASSIGNED LOGISTICS</strong></th>
                                                <th><strong>STATUS</strong></th>
                                                <th><strong>ACTION</strong></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($stockTransferWorkflow ?? [] as $transfer)
                                            <tr>
                                                <td><strong>ST-{{ str_pad($transfer->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                                <td>
                                                    <div>{{ $transfer->fromSite->name ?? 'N/A' }}</div>
                                                    <small class="text-muted">to {{ $transfer->toSite->name ?? 'N/A' }}</small>
                                                </td>
                                                <td>{{ $transfer->book->name ?? 'N/A' }}</td>
                                                <td><strong>{{ $transfer->quantity }}</strong></td>
                                                <td>{{ $transfer->createdBy->name ?? 'N/A' }}</td>
                                                <td>{{ $transfer->logisticsAssignedTo->name ?? 'Not assigned' }}</td>
                                                <td>
                                                    @if($transfer->status === 'pending')
                                                        <span class="badge light badge-warning">Manager/Supervisor Approval</span>
                                                    @elseif($transfer->status === 'accounting_review')
                                                        <span class="badge light badge-info">Accounting Review</span>
                                                    @elseif($transfer->status === 'logistics_assignment')
                                                        <span class="badge light badge-primary">For Logistics Assignment</span>
                                                    @elseif($transfer->status === 'logistics_assigned')
                                                        <span class="badge light badge-secondary">Assigned to Logistics</span>
                                                    @elseif($transfer->status === 'completed')
                                                        <span class="badge light badge-success">Completed</span>
                                                    @else
                                                        <span class="badge light badge-danger">Rejected</span>
                                                    @endif
                                                </td>
                                                <td style="min-width: 220px;">
                                                    @if($transfer->status === 'pending' && $transfer->canBeApprovedBy(auth()->user()))
                                                        <button class="btn btn-xs btn-success mb-1" onclick="approveTransfer({{ $transfer->id }})">
                                                            <i class="las la-check"></i> Approve
                                                        </button>
                                                        <button class="btn btn-xs btn-danger mb-1" onclick="rejectTransfer({{ $transfer->id }})">
                                                            <i class="las la-times"></i> Reject
                                                        </button>
                                                    @elseif($transfer->status === 'accounting_review' && ($isAccountingReviewer ?? false))
                                                        <button class="btn btn-xs btn-info" onclick="accountingApproveTransfer({{ $transfer->id }})">
                                                            <i class="las la-file-invoice"></i> Accounting Approve
                                                        </button>
                                                    @elseif($transfer->status === 'logistics_assigned' && $transfer->canBeCompletedBy(auth()->user()))
                                                        <button class="btn btn-xs btn-success" onclick="completeLogisticsTransfer({{ $transfer->id }})">
                                                            <i class="las la-check-double"></i> Mark Completed
                                                        </button>
                                                    @elseif(in_array($transfer->status, ['logistics_assignment', 'logistics_assigned']) && ($isLogisticsAssigner ?? false))
                                                        <div class="d-flex gap-1">
                                                            <select class="form-control form-control-sm" id="assignLogistics{{ $transfer->id }}">
                                                                <option value="">Select staff</option>
                                                                @foreach($logisticsUsers ?? [] as $logisticsUser)
                                                                    <option value="{{ $logisticsUser->id }}" {{ $transfer->logistics_assigned_to == $logisticsUser->id ? 'selected' : '' }}>
                                                                        {{ $logisticsUser->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <button class="btn btn-xs btn-primary" onclick="assignLogisticsTransfer({{ $transfer->id }})">
                                                                Assign
                                                            </button>
                                                        </div>
                                                    @else
                                                        <span class="text-muted small">No action available</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="8" class="text-center">No stock transfers found</td>
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

                    <div class="tab-content">
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
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white d-flex justify-content-between align-items-center" style="background-color: #dc3545;">
                    <h6 class="modal-title text-white m-0"><i class="las la-exchange-alt me-2"></i>Transfer Stock (Multiple Books)</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="transferStockForm">
                    @csrf
                    <div class="modal-body">
                        <!-- From Site Selection -->
                        <div class="mb-3">
                            <label class="form-label font-w600">From Site *</label>
                            <select name="from_site_id" class="form-control" required id="fromSiteSelect">
                                <option value="">-- Select Source Site --</option>
                                @foreach($sites ?? [] as $site)
                                    <option value="{{ $site->id }}">{{ $site->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Multiple Books to Transfer Section -->
                        <div class="mb-4">
                            <label class="form-label font-w600">Books to Transfer</label>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="transferBooksTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 40%;">Book</th>
                                            <th style="width: 25%;">Quantity</th>
                                            <th style="width: 25%;">Available</th>
                                            <th style="width: 10%;" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="transferBooksBody">
                                        <tr id="emptyBooksRow">
                                            <td colspan="4" class="text-center text-muted py-3">Select a source site above, then click "Add Book" to start.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Add Book Button -->
                        <div class="mb-4">
                            <button type="button" class="btn btn-primary" id="showAddBookBtn" disabled>
                                <i class="las la-plus me-1"></i>Add Book
                            </button>
                        </div>

                        <!-- Dynamic Add Book Form (Hidden by default) -->
                        <div id="addBookForm" style="display: none;" class="mb-4 p-3 bg-light rounded">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0"><i class="las la-book me-2"></i>Add New Book</h6>
                                <button type="button" class="btn-close" id="closeAddBookForm"></button>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-7">
                                    <label class="form-label font-w600">Select Book *</label>
                                    <select id="bookSelect" class="form-control">
                                        <option value="">-- Select a Book --</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label font-w600">Quantity *</label>
                                    <input type="number" id="bookQuantity" class="form-control" placeholder="Enter quantity" min="1" disabled>
                                </div>
                                <div class="col-12">
                                    <button type="button" id="confirmAddBookBtn" class="btn btn-success" style="display: block !important; width: 100% !important; max-width: 100% !important; min-width: 150px !important; padding: 0.75rem 1rem !important; font-size: 0.95rem !important; line-height: 1.2 !important; background-color: #68CF29 !important; border-color: #68CF29 !important; color: #000 !important; -webkit-text-fill-color: #000 !important; text-shadow: none !important; box-shadow: none !important; position: relative !important; z-index: 10000 !important;">
                                        ADD BOOK
                                    </button>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted">Available stock: <span id="selectedBookAvailable">0</span></small>
                                </div>
                            </div>
                        </div>

                        <!-- To Site -->
                        <div class="mb-3">
                            <label class="form-label font-w600">To Site *</label>
                            <select name="to_site_id" class="form-control" required id="toSiteSelect">
                                <option value="">-- Select Destination Site --</option>
                                @foreach($sites ?? [] as $site)
                                    <option value="{{ $site->id }}">{{ $site->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Notes -->
                        <div class="mb-0">
                            <label class="form-label font-w600">Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Transfer notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-danger" id="submitTransferBtn" disabled>
                            <i class="las la-check me-1"></i>Request Transfer (0 books)
                        </button>
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
                            <p class="text-muted mb-0">Click "Add New Site" to create a new warehouse location. Set the name, code, location, and description.</p>
                        </div>
                    </div>

                    <div class="timeline-item mb-4">
                        <div class="timeline-marker bg-success"><strong>2</strong></div>
                        <div class="timeline-content">
                            <h6 class="font-w600">Add Stock to Site</h6>
                            <p class="text-muted mb-0">Click the <i class="las la-plus"></i> button on any site to add inventory. Select the book, quantity, and optional reorder point.</p>
                        </div>
                    </div>

                    <div class="timeline-item mb-4">
                        <div class="timeline-marker bg-warning"><strong>3</strong></div>
                        <div class="timeline-content">
                            <h6 class="font-w600">View Site Inventory</h6>
                            <p class="text-muted mb-0">Click the <i class="las la-boxes"></i> button to see all items in that warehouse and their stock levels.</p>
                        </div>
                    </div>

                    <div class="timeline-item mb-4">
                        <div class="timeline-marker bg-danger"><strong>4</strong></div>
                        <div class="timeline-content">
                            <h6 class="font-w600">Request Stock Transfer</h6>
                            <p class="text-muted mb-0">Click the "Transfer Stock" button, select source site, add books to transfer, select destination site, and submit.</p>
                        </div>
                    </div>

                    <div class="timeline-item mb-4">
                        <div class="timeline-marker bg-success"><strong>5</strong></div>
                        <div class="timeline-content">
                            <h6 class="font-w600">Manager Approval</h6>
                            <p class="text-muted mb-0">Manager reviews pending transfers and clicks Approve or Reject.</p>
                        </div>
                    </div>

                    <div class="timeline-item mb-0">
                        <div class="timeline-marker bg-info"><strong>6</strong></div>
                        <div class="timeline-content">
                            <h6 class="font-w600">Automatic Stock Update</h6>
                            <p class="text-muted mb-0">Once approved, stock is automatically deducted from source and added to destination.</p>
                        </div>
                    </div>
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
            
            toastElement.addEventListener('hidden.bs.toast', function() {
                toastElement.remove();
            });
        }

        let currentBookName = null;
        let currentStock = null;
        let maxStock = null;
        let currentBookId = null;
        let stockMgmtAddHandler = null;
        let stockMgmtEditHandler = null;

        function openStockManagementModal(bookId, bookName, stock, max) {
            currentBookId = bookId;
            currentBookName = bookName;
            currentStock = stock;
            maxStock = max;

            document.getElementById('mgmtBookName').value = bookName;
            document.getElementById('mgmtCurrentStock').value = stock;
            document.getElementById('mgmtMaxStock').value = max || 'Not Set';
            
            document.getElementById('mgmtAddQuantity').value = '';
            document.getElementById('mgmtAddWarning').innerHTML = '';
            document.getElementById('mgmtAddPreview').style.display = 'none';
            document.getElementById('mgmtEditQuantity').value = '';
            document.getElementById('mgmtEditWarning').innerHTML = '';
            document.getElementById('mgmtEditPreview').style.display = 'none';

            if (stockMgmtAddHandler) {
                document.getElementById('mgmtAddQuantity').removeEventListener('input', stockMgmtAddHandler);
            }
            if (stockMgmtEditHandler) {
                document.getElementById('mgmtEditQuantity').removeEventListener('input', stockMgmtEditHandler);
            }

            stockMgmtAddHandler = function() {
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
            };

            stockMgmtEditHandler = function() {
                const newStock = parseInt(this.value);
                const warning = document.getElementById('mgmtEditWarning');
                const preview = document.getElementById('mgmtEditPreview');

                if (!isNaN(newStock) && newStock >= 0) {
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
            };

            document.getElementById('mgmtAddQuantity').addEventListener('input', stockMgmtAddHandler);
            document.getElementById('mgmtEditQuantity').addEventListener('input', stockMgmtEditHandler);

            const modal = new bootstrap.Modal(document.getElementById('stockManagementModal'));
            modal.show();
        }

        function saveStockManagement() {
            const activeTab = document.querySelector('#stockManagementModal .nav-link.active');
            if (activeTab && activeTab.id === 'addTab') {
                saveAddStock();
            } else if (activeTab && activeTab.id === 'editTab') {
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

            if (isNaN(newStock)) {
                showNotification('Please enter a valid stock value', 'warning');
                return;
            }

            if (maxStock && newStock > maxStock) {
                showNotification(`Cannot set stock. New value (${newStock}) exceeds max stock (${maxStock})`, 'error');
                return;
            }

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

        // Site Management Functions
        document.getElementById('addSiteForm')?.addEventListener('submit', function(e) {
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
        
        // Delete Site Function
        function deleteSite(siteId, siteName) {
            if (confirm(`Are you sure you want to delete "${siteName}"? This action cannot be undone.`)) {
                const deleteBtn = event.target.closest('button');
                const originalHTML = deleteBtn.innerHTML;
                deleteBtn.innerHTML = '<i class="las la-spinner la-spin"></i>';
                deleteBtn.disabled = true;

                fetch(`/production/sites/delete/${siteId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Site deleted successfully!', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification('Error: ' + data.message, 'error');
                        deleteBtn.innerHTML = originalHTML;
                        deleteBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while deleting the site', 'error');
                    deleteBtn.innerHTML = originalHTML;
                    deleteBtn.disabled = false;
                });
            }
        }
        
        // Stock Transfer Variables
        let selectedBooksMap = {};
        let siteBooks = {};
        let nextRowId = 1;

        // Store master books data for Main Warehouse
        const masterBooksData = [
            @forelse($allBooks ?? [] as $book)
                {
                    book_id: {{ $book->id }},
                    book: { name: '{{ addslashes($book->name ?? 'Unknown') }}' },
                    quantity: {{ $book->stock ?? 0 }}
                }{{ !$loop->last ? ',' : '' }}
            @empty
            @endforelse
        ];

        // Store sites inventory data from Blade
        const sitesInventoryData = {
            @foreach($sites ?? [] as $site)
                {{ $site->id }}: [
                    @foreach($site->inventory ?? [] as $inv)
                        {
                            book_id: {{ $inv->book_id }},
                            book: { name: '{{ addslashes($inv->book->name ?? 'Unknown') }}' },
                            quantity: {{ $inv->quantity }}
                        }{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ]{{ !$loop->last ? ',' : '' }}
            @endforeach
        };

        // Initialize transfer modal from master inventory
        window.initTransferModalFromMaster = function() {
            selectedBooksMap = {};
            nextRowId = 1;
            siteBooks = {}; // Clear cache
            
            document.getElementById('fromSiteSelect').value = '';
            document.getElementById('toSiteSelect').value = '';
            const notesTextarea = document.querySelector('textarea[name="notes"]');
            if (notesTextarea) notesTextarea.value = '';
            
            const addBookForm = document.getElementById('addBookForm');
            const showAddBookBtn = document.getElementById('showAddBookBtn');
            if (addBookForm) addBookForm.style.display = 'none';
            if (showAddBookBtn) {
                showAddBookBtn.disabled = true;
                showAddBookBtn.style.display = 'block';
            }
            
            const bookSelect = document.getElementById('bookSelect');
            const quantityInput = document.getElementById('bookQuantity');
            if (bookSelect) bookSelect.innerHTML = '<option value="">-- Select a Book --</option>';
            if (quantityInput) {
                quantityInput.value = '';
                quantityInput.disabled = true;
            }
            
            renderSelectedBooks();
            updateSubmitButton();
        };

        // Transfer Stock Functions
        document.getElementById('transferStockForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const bookIds = Object.keys(selectedBooksMap);
            if (bookIds.length === 0) {
                showNotification('Please add at least one book', 'error');
                return;
            }

            const fromSiteId = document.getElementById('fromSiteSelect').value;
            const toSiteId = document.getElementById('toSiteSelect').value;
            
            if (!fromSiteId) {
                showNotification('Please select source site', 'error');
                return;
            }
            
            if (!toSiteId) {
                showNotification('Please select destination site', 'error');
                return;
            }
            
            if (fromSiteId === toSiteId) {
                showNotification('Source and destination sites cannot be the same', 'error');
                return;
            }

            const transfers = bookIds.map(bookId => ({
                from_site_id: fromSiteId,
                to_site_id: toSiteId,
                book_id: parseInt(bookId),
                quantity: selectedBooksMap[bookId].quantity,
                notes: document.querySelector('textarea[name="notes"]')?.value || ''
            }));

            const submitBtn = document.getElementById('submitTransferBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="las la-spinner la-spin me-1"></i>Processing...';
            submitBtn.disabled = true;

            Promise.all(transfers.map(transfer => {
                const formData = new FormData();
                formData.append('from_site_id', transfer.from_site_id);
                formData.append('to_site_id', transfer.to_site_id);
                formData.append('book_id', transfer.book_id);
                formData.append('quantity', transfer.quantity);
                formData.append('notes', transfer.notes);

                return fetch('/production/sites/transfer', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                }).then(response => response.json());
            }))
            .then(results => {
                const allSuccessful = results.every(r => r.success);
                if (allSuccessful) {
                    showNotification(`${results.length} transfer request(s) submitted successfully!`, 'success');
                    bootstrap.Modal.getInstance(document.getElementById('transferStockModal')).hide();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    const failed = results.filter(r => !r.success).length;
                    showNotification(`${failed} transfer(s) failed. Please check stock availability.`, 'error');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });

        // When source site is selected, load its inventory
        document.getElementById('fromSiteSelect')?.addEventListener('change', function() {
            const siteId = this.value;
            const showAddBookBtn = document.getElementById('showAddBookBtn');
            
            if (siteId) {
                selectedBooksMap = {};
                nextRowId = 1;
                renderSelectedBooks();
                updateSubmitButton();
                
                loadBooksForSite(siteId);
                showAddBookBtn.disabled = false;
            } else {
                showAddBookBtn.disabled = true;
                const bookSelect = document.getElementById('bookSelect');
                if (bookSelect) bookSelect.innerHTML = '<option value="">-- Select a Book --</option>';
            }
        });

        function loadBooksForSite(siteId) {
            console.log('Loading books for site:', siteId);
            
            // Always fetch real-time data from server
            console.log('Fetching real-time books from server for site:', siteId);
            fetch(`/production/sites/${siteId}/inventory`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                return response.json();
            })
            .then(data => {
                console.log('Real-time books loaded from server:', data);
                if (data.success && data.inventory && Array.isArray(data.inventory)) {
                    siteBooks[siteId] = data.inventory;
                    populateBookSelect(siteId);
                } else {
                    const select = document.getElementById('bookSelect');
                    if (select) select.innerHTML = '<option value="">No books available</option>';
                    showNotification('No inventory found for this site', 'warning');
                }
            })
            .catch(error => {
                console.error('Error loading books:', error);
                showNotification('Could not load books: ' + error.message, 'error');
                const select = document.getElementById('bookSelect');
                if (select) select.innerHTML = '<option value="">Error loading books</option>';
            });
        }

        function populateBookSelect(siteId) {
            const select = document.getElementById('bookSelect');
            if (!select) return;
            
            const inventory = siteBooks[siteId] || [];
            console.log('Populating dropdown with inventory:', inventory);
            
            const availableBooks = inventory.filter(item => !selectedBooksMap[item.book_id]);
            
            select.innerHTML = '<option value="">-- Select a Book --</option>';
            
            if (availableBooks.length === 0) {
                if (inventory.length === 0) {
                    select.innerHTML = '<option value="">-- No books available --</option>';
                } else {
                    select.innerHTML = '<option value="">-- All books added --</option>';
                }
                return;
            }

            availableBooks.forEach(item => {
                const bookName = item.book && item.book.name ? item.book.name : 'Unknown Book';
                const option = document.createElement('option');
                option.value = item.book_id;
                option.textContent = `${bookName} (Available: ${item.quantity})`;
                option.dataset.available = item.quantity;
                option.dataset.name = bookName;
                select.appendChild(option);
                console.log('Added option:', bookName, 'Qty:', item.quantity);
            });

            select.onchange = function() {
                const selected = this.options[this.selectedIndex];
                const availableSpan = document.getElementById('selectedBookAvailable');
                const quantityInput = document.getElementById('bookQuantity');
                
                if (selected && selected.value) {
                    if (availableSpan) availableSpan.textContent = selected.dataset.available;
                    if (quantityInput) {
                        quantityInput.max = selected.dataset.available;
                        quantityInput.value = '';
                        quantityInput.disabled = false;
                    }
                } else {
                    if (availableSpan) availableSpan.textContent = '0';
                    if (quantityInput) {
                        quantityInput.max = 1;
                        quantityInput.disabled = true;
                    }
                }
            };
            
            if (select.onchange) select.onchange();
        }

        document.getElementById('showAddBookBtn')?.addEventListener('click', function() {
            const addBookForm = document.getElementById('addBookForm');
            const showAddBookBtn = document.getElementById('showAddBookBtn');
            addBookForm.style.display = 'block';
            showAddBookBtn.style.display = 'none';
            
            const bookSelect = document.getElementById('bookSelect');
            const quantityInput = document.getElementById('bookQuantity');
            if (bookSelect) bookSelect.value = '';
            if (quantityInput) {
                quantityInput.value = '';
                quantityInput.disabled = true;
            }
            const availableSpan = document.getElementById('selectedBookAvailable');
            if (availableSpan) availableSpan.textContent = '0';
        });

        document.getElementById('closeAddBookForm')?.addEventListener('click', function() {
            const addBookForm = document.getElementById('addBookForm');
            const showAddBookBtn = document.getElementById('showAddBookBtn');
            addBookForm.style.display = 'none';
            showAddBookBtn.style.display = 'block';
            
            const bookSelect = document.getElementById('bookSelect');
            const quantityInput = document.getElementById('bookQuantity');
            if (bookSelect) bookSelect.value = '';
            if (quantityInput) {
                quantityInput.value = '';
                quantityInput.disabled = true;
            }
        });

        document.getElementById('confirmAddBookBtn')?.addEventListener('click', function() {
            const bookSelect = document.getElementById('bookSelect');
            const quantityInput = document.getElementById('bookQuantity');

            if (!bookSelect || !bookSelect.value) {
                showNotification('Please select a book', 'error');
                return;
            }

            if (!quantityInput || !quantityInput.value || parseInt(quantityInput.value) < 1) {
                showNotification('Please enter a valid quantity', 'error');
                return;
            }

            const bookId = parseInt(bookSelect.value);
            const selectedOption = bookSelect.options[bookSelect.selectedIndex];
            const bookName = selectedOption.dataset.name;
            const available = parseInt(selectedOption.dataset.available);
            const quantity = parseInt(quantityInput.value);

            if (quantity > available) {
                showNotification(`Insufficient stock. Available: ${available}`, 'error');
                return;
            }

            addBookToTransfer(bookId, bookName, quantity, available);

            bookSelect.value = '';
            quantityInput.value = '';
            quantityInput.disabled = true;
            const availableSpan = document.getElementById('selectedBookAvailable');
            if (availableSpan) availableSpan.textContent = '0';
            
            const addBookForm = document.getElementById('addBookForm');
            const showAddBookBtn = document.getElementById('showAddBookBtn');
            if (addBookForm) addBookForm.style.display = 'none';
            if (showAddBookBtn) showAddBookBtn.style.display = 'block';
            
            const fromSiteId = document.getElementById('fromSiteSelect').value;
            if (fromSiteId) {
                populateBookSelect(parseInt(fromSiteId));
            }
        });

        function addBookToTransfer(bookId, bookName, quantity, available) {
            if (selectedBooksMap[bookId]) {
                showNotification('This book is already added', 'error');
                return;
            }

            selectedBooksMap[bookId] = {
                name: bookName,
                quantity: quantity,
                available: available,
                rowId: nextRowId++
            };

            renderSelectedBooks();
            updateSubmitButton();
            showNotification(`${bookName} added to transfer list`, 'success');
        }

        window.removeBookFromTransfer = function(bookId) {
            delete selectedBooksMap[bookId];
            renderSelectedBooks();
            updateSubmitButton();
            
            const fromSiteId = document.getElementById('fromSiteSelect').value;
            if (fromSiteId) {
                populateBookSelect(parseInt(fromSiteId));
            }
        };
        
        window.updateBookQuantity = function(bookId, newQuantity) {
            const book = selectedBooksMap[bookId];
            if (!book) return;
            
            if (newQuantity < 1) {
                showNotification('Quantity must be at least 1', 'error');
                return;
            }
            
            if (newQuantity > book.available) {
                showNotification(`Maximum quantity available: ${book.available}`, 'error');
                return;
            }
            
            book.quantity = newQuantity;
            renderSelectedBooks();
            updateSubmitButton();
        };

        function renderSelectedBooks() {
            const tbody = document.getElementById('transferBooksBody');
            if (!tbody) return;

            if (Object.keys(selectedBooksMap).length === 0) {
                tbody.innerHTML = '<tr id="emptyBooksRow"><td colspan="4" class="text-center text-muted py-3">No books added. Click "Add Book" to start.您</td></tr>';
                return;
            }

            tbody.innerHTML = Object.entries(selectedBooksMap).map(([bookId, book]) => `
                <tr data-book-id="${bookId}">
                    <td><strong>${escapeHtml(book.name)}</strong></td>
                    <td>
                        <input type="number" 
                               class="form-control form-control-sm" 
                               value="${book.quantity}" 
                               min="1" 
                               max="${book.available}"
                               style="width: 100px;"
                               onchange="updateBookQuantity(${bookId}, parseInt(this.value))">
                    </td>
                    <td><span class="badge bg-info">${book.available} available</span></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeBookFromTransfer(${bookId})" title="Remove">
                            <i class="las la-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function updateSubmitButton() {
            const submitBtn = document.getElementById('submitTransferBtn');
            const bookCount = Object.keys(selectedBooksMap).length;
            const fromSiteId = document.getElementById('fromSiteSelect').value;
            const toSiteId = document.getElementById('toSiteSelect').value;
            
            if (submitBtn) {
                submitBtn.disabled = bookCount === 0 || !fromSiteId || !toSiteId || fromSiteId === toSiteId;
                submitBtn.innerHTML = `<i class="las la-check me-1"></i>Request Transfer (${bookCount} book${bookCount !== 1 ? 's' : ''})`;
            }
        }
        
        document.getElementById('toSiteSelect')?.addEventListener('change', function() {
            updateSubmitButton();
        });

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        window.approveTransfer = function(transferId) {
            if (confirm('Approve this transfer?')) {
                fetch(`/stock-transfers/${transferId}/approve`, {
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
        };

        window.rejectTransfer = function(transferId) {
            if (confirm('Reject this transfer?')) {
                fetch(`/stock-transfers/${transferId}/reject`, {
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
        };

        window.accountingApproveTransfer = function(transferId) {
            if (confirm('Approve this transfer from Accounting and forward to Logistics?')) {
                fetch(`/stock-transfers/${transferId}/accounting-approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message || 'Transfer forwarded to Logistics!', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification('Error: ' + data.message, 'error');
                    }
                })
                .catch(() => {
                    showNotification('An error occurred', 'error');
                });
            }
        };

        window.assignLogisticsTransfer = function(transferId) {
            const select = document.getElementById(`assignLogistics${transferId}`);
            const logisticsUserId = select ? select.value : '';

            if (!logisticsUserId) {
                showNotification('Please select a logistics staff.', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('logistics_assigned_to', logisticsUserId);

            fetch(`/stock-transfers/${transferId}/assign-logistics`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message || 'Transfer assigned!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('Error: ' + data.message, 'error');
                }
            })
            .catch(() => {
                showNotification('An error occurred', 'error');
            });
        };

        window.completeLogisticsTransfer = function(transferId) {
            if (confirm('Mark this stock transfer as completed? This will move the stock now.')) {
                fetch(`/stock-transfers/${transferId}/complete`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message || 'Transfer completed!', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification('Error: ' + data.message, 'error');
                    }
                })
                .catch(() => {
                    showNotification('An error occurred', 'error');
                });
            }
        };
    </script>
</x-app-layout>

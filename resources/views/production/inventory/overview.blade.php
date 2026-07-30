<x-app-layout :title="'Inventory Overview'" :sidebar="'production'">
@push('styles')
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        /* Site Inventory Modal Extra Large & Single Scrollbar Enforcement */
        .site-inventory-modal .modal-dialog {
            max-width: 92vw !important;
            width: 1200px !important;
        }
        .site-inventory-modal .modal-body {
            max-height: 85vh !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
        }
        .site-inventory-modal .table-responsive,
        .site-inventory-modal .tab-content,
        .site-inventory-modal .tab-pane {
            max-height: none !important;
            height: auto !important;
            overflow: visible !important;
        }
        .site-inventory-modal .table {
            margin-bottom: 0 !important;
        }

        /* Select2 Bootstrap 5 & Modal Integration Styling */
        .select2-container--default .select2-selection--single {
            height: 42px !important;
            padding: 6px 12px !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.375rem !important;
            display: flex !important;
            align-items: center !important;
            background-color: #fff !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
            color: #495057 !important;
            padding-left: 0 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
            right: 8px !important;
        }
        .select2-dropdown {
            border-color: #ced4da !important;
            border-radius: 0.375rem !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            z-index: 10060 !important;
        }
        .select2-search--dropdown {
            padding: 8px !important;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        .select2-search--dropdown .select2-search__field {
            border: 1px solid #ced4da !important;
            border-radius: 0.25rem !important;
            padding: 6px 10px !important;
            outline: none !important;
            width: 100% !important;
            font-size: 14px !important;
            display: block !important;
            visibility: visible !important;
            height: 34px !important;
            box-sizing: border-box !important;
            opacity: 1 !important;
        }
        .select2-container--open .select2-search--dropdown {
            display: block !important;
        }
    </style>
@endpush
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
        
                <!-- Product Inventory Table with Sub-Tabs -->
                <div class="row">
                    <div class="col-xl-12 col-xxl-12">
                        <div class="card">
                            <div class="card-header border-0 d-block d-sm-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div class="d-flex align-items-center flex-wrap gap-3">
                                    <h4 class="fs-20 mb-0 text-black">Master Registry</h4>
                                    <ul class="nav nav-tabs card-header-tabs border-0" role="tablist" style="margin-bottom: -15px;">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active font-w600" id="registry-books-tab" data-bs-toggle="tab" data-bs-target="#registry-books-content" type="button" role="tab" aria-controls="registry-books-content" aria-selected="true">
                                                <i class="las la-book me-1"></i>Books
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link font-w600" id="registry-nonbooks-tab" data-bs-toggle="tab" data-bs-target="#registry-nonbooks-content" type="button" role="tab" aria-controls="registry-nonbooks-content" aria-selected="false">
                                                <i class="las la-gift me-1"></i>Non-Books
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link font-w600" id="registry-indices-tab" data-bs-toggle="tab" data-bs-target="#registry-indices-content" type="button" role="tab" aria-controls="registry-indices-content" aria-selected="false">
                                                <i class="las la-list me-1"></i>Indices
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link font-w600" id="registry-bundles-tab" data-bs-toggle="tab" data-bs-target="#registry-bundles-content" type="button" role="tab" aria-controls="registry-bundles-content" aria-selected="false">
                                                <i class="las la-boxes me-1"></i>Bundles
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="d-flex flex-wrap align-items-center gap-2 mt-3 mt-sm-0">
                                    <!-- Search Form -->
                                    <form action="{{ route('production.inventory.overview') }}" method="GET" class="d-flex align-items-center gap-2">
                                        <div style="width: 250px; height: 38px; display: flex; align-items: center; border: 1px solid #ced4da; border-radius: 4px; background-color: #f8f9fa; padding: 0 12px; box-sizing: border-box;">
                                            <span class="las la-search text-muted me-2" style="font-size: 1.1rem; line-height: 1;"></span>
                                            <input type="text" name="search" class="form-control" 
                                                   placeholder="Search registry..." value="{{ request('search') }}" 
                                                   style="border: none !important; background: transparent !important; padding: 0 !important; height: 100%; font-size: 0.85rem; color: #333; outline: none !important; box-shadow: none !important;">
                                            @if(request('search'))
                                                <a href="{{ route('production.inventory.overview') }}" class="text-muted d-inline-flex align-items-center justify-content-center ms-2" title="Clear search" style="text-decoration: none;">
                                                    <span class="las la-times-circle" style="color: #999; font-size: 1.25rem; cursor: pointer;"></span>
                                                </a>
                                            @endif
                                        </div>
                                        <button type="submit" class="btn btn-primary text-white rounded d-inline-flex align-items-center justify-content-center gap-2" style="height: 38px; padding: 0 1.2rem; border: none; font-size: 0.85rem; font-weight: 500; background-color: #D9251C; border-color: #D9251C; box-shadow: 0 4px 6px rgba(217, 37, 28, 0.15);">
                                            <span class="las la-search" style="font-size: 1rem; color: #fff;"></span>
                                            <span>Search</span>
                                        </button>
                                    </form>

                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#transferStockModal" onclick="initTransferModalFromMaster()" style="height: 38px; display: inline-flex; align-items: center;">
                                        <i class="las la-exchange-alt me-1"></i>Transfer Stock
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    
                                    <!-- Books Registry Tab Pane -->
                                    <div class="tab-pane fade show active" id="registry-books-content" role="tabpanel" aria-labelledby="registry-books-tab">
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
                                                {{ $books->appends(['search' => request('search')])->links() }}
                                        </div>
                                    </div>

                                    <!-- Non-Books Registry Tab Pane -->
                                    <div class="tab-pane fade" id="registry-nonbooks-content" role="tabpanel" aria-labelledby="registry-nonbooks-tab">
                                        <div class="table-responsive">
                                            <table class="table table-responsive-md">
                                                <thead>
                                                    <tr>
                                                        <th><strong>ITEM ID</strong></th>
                                                        <th><strong>NAME</strong></th>
                                                        <th><strong>CATEGORY</strong></th>
                                                        <th><strong>UNIT COST</strong></th>
                                                        <th><strong>STOCK</strong></th>
                                                        <th><strong>MAX STOCK</strong></th>
                                                        <th><strong>STATUS</strong></th>
                                                        <th><strong>ACTION</strong></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($nonBooks as $item)
                                                    @php
                                                        // Get Main Warehouse inventory for this non-book
                                                        $mainWarehouseQuantity = 0;
                                                        if($mainWarehouse) {
                                                            $mainWarehouseQuantity = $mainWarehouse->inventory()
                                                                ->where('book_id', $item->id)
                                                                ->sum('quantity');
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td><strong>#{{ $item->sku }}</strong></td>
                                                        <td>{{ $item->name }}</td>
                                                        <td>{{ $item->category }}</td>
                                                        <td>₱{{ number_format($item->cost, 2) }}</td>
                                                        <td><strong>{{ $mainWarehouseQuantity }}</strong></td>
                                                        <td><strong>{{ $item->max_stock ?? 'N/A' }}</strong></td>
                                                        <td>
                                                            @if($mainWarehouseQuantity == 0)
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fa fa-circle text-danger me-1"></i> Out of Stock
                                                                </div>
                                                            @elseif($mainWarehouseQuantity <= ($item->reorder_point ?? 0))
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
                                                            <button class="btn btn-sm btn-danger" onclick="openStockManagementModal({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->stock }}, {{ $item->max_stock ?? 0 }})">
                                                                <i class="las la-pen"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="8" class="text-center">No non-books found.</td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-4">
                                            <div class="pagination-info">
                                                Showing {{ $nonBooks->firstItem() ?? 0 }} to {{ $nonBooks->lastItem() ?? 0 }} of {{ $nonBooks->total() }} entries
                                            </div>
                                            <nav>
                                                {{ $nonBooks->appends(['search' => request('search')])->links() }}
                                            </nav>
                                        </div>
                                    </div>

                                    <!-- Indices Registry Tab Pane -->
                                    <div class="tab-pane fade" id="registry-indices-content" role="tabpanel" aria-labelledby="registry-indices-tab">
                                        <div class="table-responsive">
                                            <table class="table table-responsive-md">
                                                <thead>
                                                    <tr>
                                                        <th><strong>ID</strong></th>
                                                        <th><strong>BOOK TITLE</strong></th>
                                                        <th><strong>INDEX VALUE</strong></th>
                                                        <th><strong>STOCK</strong></th>
                                                        <th><strong>STATUS</strong></th>
                                                        <th><strong>ACTION</strong></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($indices as $index)
                                                    @php
                                                        $mainWarehouseQty = 0;
                                                        if($mainWarehouse) {
                                                            $mainWarehouseQty = $mainWarehouse->inventory()
                                                                ->where('book_index_id', $index->id)
                                                                ->sum('quantity');
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td><strong>#IDX-{{ str_pad($index->id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                                                        <td>{{ $index->book->name ?? 'N/A' }}</td>
                                                        <td><span class="badge badge-info light">{{ $index->index_value }}</span></td>
                                                        <td><strong>{{ $mainWarehouseQty }}</strong></td>
                                                        <td>
                                                            @if($mainWarehouseQty == 0)
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fa fa-circle text-danger me-1"></i> Out of Stock
                                                                </div>
                                                            @else
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fa fa-circle text-success me-1"></i> In Stock
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-danger" onclick="openIndexStockModal({{ $index->id }}, '{{ addslashes($index->book->name ?? 'N/A') }}', '{{ addslashes($index->index_value) }}', {{ $mainWarehouseQty }})">
                                                                <i class="las la-pen"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center">No book indices found.</td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-4">
                                            <div class="pagination-info">
                                                Showing {{ $indices->firstItem() ?? 0 }} to {{ $indices->lastItem() ?? 0 }} of {{ $indices->total() }} entries
                                            </div>
                                            <nav>
                                                {{ $indices->appends(['search' => request('search')])->links() }}
                                            </nav>
                                        </div>
                                    </div>

                                    <!-- Bundles Registry Tab Pane -->
                                    <div class="tab-pane fade" id="registry-bundles-content" role="tabpanel" aria-labelledby="registry-bundles-tab">
                                        <div class="table-responsive">
                                            <table class="table table-responsive-md">
                                                <thead>
                                                    <tr>
                                                        <th><strong>BUNDLE SKU</strong></th>
                                                        <th><strong>NAME</strong></th>
                                                        <th><strong>CONSTITUENT BOOKS</strong></th>
                                                        <th><strong>PRICE</strong></th>
                                                        <th><strong>STOCK</strong></th>
                                                        <th><strong>STATUS</strong></th>
                                                        <th><strong>ACTION</strong></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($bundles as $bundle)
                                                    @php
                                                        $mainWarehouseQty = 0;
                                                        if($mainWarehouse) {
                                                            $mainWarehouseQty = $mainWarehouse->inventory()
                                                                ->where('book_bundle_id', $bundle->id)
                                                                ->sum('quantity');
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td><strong>#{{ $bundle->sku ?? 'N/A' }}</strong></td>
                                                        <td>{{ $bundle->name }}</td>
                                                        <td>
                                                            <ul class="mb-0 list-unstyled">
                                                                @foreach($bundle->books as $b)
                                                                    <li><small>• {{ $b->name }} (Qty: {{ $b->pivot->quantity }})</small></li>
                                                                @endforeach
                                                            </ul>
                                                        </td>
                                                        <td>₱{{ number_format($bundle->price, 2) }}</td>
                                                        <td><strong>{{ $mainWarehouseQty }}</strong></td>
                                                        <td>
                                                            @if($mainWarehouseQty == 0)
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fa fa-circle text-danger me-1"></i> Out of Stock
                                                                </div>
                                                            @else
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fa fa-circle text-success me-1"></i> In Stock
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-danger" onclick="openBundleStockModal({{ $bundle->id }}, '{{ addslashes($bundle->name) }}', {{ $mainWarehouseQty }})">
                                                                <i class="las la-pen"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">No book bundles found.</td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-4">
                                            <div class="pagination-info">
                                                Showing {{ $bundles->firstItem() ?? 0 }} to {{ $bundles->lastItem() ?? 0 }} of {{ $bundles->total() }} entries
                                            </div>
                                            <nav>
                                                {{ $bundles->appends(['search' => request('search')])->links() }}
                                            </nav>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Stock Movements -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header border-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div>
                                    <h4 class="fs-20 mb-0 text-black">Recent Stock Movements</h4>
                                </div>
                                <ul class="nav nav-pills" id="movementFilterTabs" style="gap: 5px;">
                                    <li class="nav-item">
                                        <button type="button" class="nav-link active btn-sm py-1 px-3 movement-tab-btn" onclick="filterStockMovements('all', this)">All</button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link btn-sm py-1 px-3 movement-tab-btn" onclick="filterStockMovements('so', this)">Sales Order</button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link btn-sm py-1 px-3 movement-tab-btn" onclick="filterStockMovements('pos', this)">POS</button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link btn-sm py-1 px-3 movement-tab-btn" onclick="filterStockMovements('ecom', this)">E-Com</button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link btn-sm py-1 px-3 movement-tab-btn" onclick="filterStockMovements('transfer', this)">Stock Transfer</button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link btn-sm py-1 px-3 movement-tab-btn" onclick="filterStockMovements('stockin', this)">Stock In</button>
                                    </li>
                                </ul>
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
                                            @php
                                                $src = strtolower(($transaction->source ?? '') . ' ' . ($transaction->notes ?? '') . ' ' . ($transaction->reference_number ?? ''));
                                                $cat = 'so';
                                                if (str_contains($src, 'transfer') || str_contains($src, 'st-')) {
                                                    $cat = 'transfer';
                                                } elseif ($transaction->type == 'in') {
                                                    $cat = 'stockin';
                                                } elseif (str_contains($src, 'pos')) {
                                                    $cat = 'pos';
                                                } elseif (str_contains($src, 'e-com') || str_contains($src, 'ecom') || str_contains($src, 'shopee') || str_contains($src, 'lazada') || str_contains($src, 'tiktok') || str_contains($src, 'website')) {
                                                    $cat = 'ecom';
                                                } elseif ($transaction->sales_order_item_id || str_contains($src, 'so-') || str_contains($src, 'sales order') || $transaction->type == 'out') {
                                                    $cat = 'so';
                                                }
                                            @endphp
                                            <tr class="stock-movement-row" data-movement-type="{{ $cat }}">
                                                <td><strong>#{{ $transaction->book->sku ?? $transaction->book_id }}</strong></td>
                                                <td>{{ $transaction->book->name ?? 'Unknown' }}</td>
                                                <td>
                                                    @if($cat == 'transfer')
                                                        <span class="badge light" style="background-color: #cfe2ff; color: #084298; font-weight: 600;">Stock Transfer</span>
                                                    @elseif($cat == 'stockin')
                                                        <span class="badge light badge-success">Stock In</span>
                                                    @elseif($cat == 'pos')
                                                        <span class="badge light" style="background-color: #e0cffc; color: #5925dc; font-weight: 600;">POS</span>
                                                    @elseif($cat == 'ecom')
                                                        <span class="badge light" style="background-color: #cff4fc; color: #055160; font-weight: 600;">E-Com</span>
                                                    @elseif($cat == 'so')
                                                        <span class="badge light" style="background-color: #fff3cd; color: #664d03; font-weight: 600;">Sales Order</span>
                                                    @else
                                                        <span class="badge light badge-danger">Stock Out</span>
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
                                <div class="d-flex flex-wrap justify-content-between align-items-center mt-4 gap-2">
                                    <div class="pagination-info" id="movementPaginationInfo">
                                        Showing 0 to 0 of 0 entries
                                    </div>
                                    <nav>
                                        <ul class="pagination pagination-xs mb-0" id="movementPaginationControls">
                                            <!-- Dynamic Pagination Buttons -->
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                let currentMovementCategory = 'all';
                let currentMovementPage = 1;
                const movementsPerPage = 5;

                function filterStockMovements(category, btn) {
                    if (btn) {
                        document.querySelectorAll('#movementFilterTabs .nav-link').forEach(el => el.classList.remove('active'));
                        btn.classList.add('active');
                    }
                    currentMovementCategory = category;
                    currentMovementPage = 1;
                    updateMovementPagination();
                }

                function changeMovementPage(page) {
                    currentMovementPage = page;
                    updateMovementPagination();
                }

                function updateMovementPagination() {
                    const allRows = Array.from(document.querySelectorAll('.stock-movement-row'));
                    const filteredRows = allRows.filter(row => {
                        return currentMovementCategory === 'all' || row.getAttribute('data-movement-type') === currentMovementCategory;
                    });

                    allRows.forEach(row => row.style.display = 'none');

                    const totalFiltered = filteredRows.length;
                    const totalPages = Math.ceil(totalFiltered / movementsPerPage) || 1;

                    if (currentMovementPage > totalPages) currentMovementPage = totalPages;
                    if (currentMovementPage < 1) currentMovementPage = 1;

                    const startIndex = (currentMovementPage - 1) * movementsPerPage;
                    const endIndex = Math.min(startIndex + movementsPerPage, totalFiltered);

                    for (let i = startIndex; i < endIndex; i++) {
                        if (filteredRows[i]) {
                            filteredRows[i].style.display = '';
                        }
                    }

                    const infoEl = document.getElementById('movementPaginationInfo');
                    if (infoEl) {
                        const fromCount = totalFiltered > 0 ? startIndex + 1 : 0;
                        infoEl.textContent = `Showing ${fromCount} to ${endIndex} of ${totalFiltered} entries`;
                    }

                    const controlsEl = document.getElementById('movementPaginationControls');
                    if (controlsEl) {
                        let html = '';
                        const prevDisabled = currentMovementPage === 1 ? 'disabled' : '';
                        html += `<li class="page-item ${prevDisabled}"><button type="button" class="page-link" onclick="changeMovementPage(${currentMovementPage - 1})" ${prevDisabled}>Previous</button></li>`;

                        for (let p = 1; p <= totalPages; p++) {
                            const activeClass = p === currentMovementPage ? 'active' : '';
                            html += `<li class="page-item ${activeClass}"><button type="button" class="page-link" onclick="changeMovementPage(${p})">${p}</button></li>`;
                        }

                        const nextDisabled = currentMovementPage === totalPages || totalPages === 0 ? 'disabled' : '';
                        html += `<li class="page-item ${nextDisabled}"><button type="button" class="page-link" onclick="changeMovementPage(${currentMovementPage + 1})" ${nextDisabled}>Next</button></li>`;

                        controlsEl.innerHTML = html;
                    }
                }

                document.addEventListener('DOMContentLoaded', function() {
                    updateMovementPagination();
                });
                </script>
            </div>

            <!-- Sites Tab Content -->
            <div class="tab-pane fade" id="sites-content" role="tabpanel" aria-labelledby="sites-tab">
                <!-- Sites List -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                                <h4 class="fs-20 mb-0 text-black">Site Management</h4>
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
                                                <td>{{ $site->getTotalInventoryQuantity() }} items</td>
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
                                                <th><strong>ITEM</strong></th>
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
                                                <td>{{ $transfer->item_name }}</td>
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
                                                <th><strong>ITEM</strong></th>
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
                                                <td>{{ $transfer->item_name ?? 'N/A' }}</td>
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
        <div class="modal fade site-inventory-modal" id="viewSiteInventory{{ $site->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h6 class="modal-title text-white"><i class="las la-boxes me-2"></i>Inventory at {{ $site->name }}</h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4 py-3" style="max-height: 85vh !important; overflow-y: auto !important; overflow-x: hidden !important;">
                        @php
                            $booksInv = $site->inventory->filter(fn($inv) => !empty($inv->book_id) && empty($inv->book_index_id) && empty($inv->book_bundle_id));
                            $indicesInv = $site->inventory->filter(fn($inv) => !empty($inv->book_index_id));
                            $bundlesInv = $site->inventory->filter(fn($inv) => !empty($inv->book_bundle_id));
                        @endphp

                        @if($site->inventory->count() > 0)
                            <!-- Nav tabs inside modal -->
                            <ul class="nav nav-tabs nav-tabs-primary mb-3" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#site-books-{{ $site->id }}" type="button">
                                        <i class="las la-book me-1"></i> Books <span class="badge bg-primary text-white ms-1">{{ $booksInv->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#site-indices-{{ $site->id }}" type="button">
                                        <i class="las la-bookmark me-1"></i> Indices <span class="badge bg-info text-white ms-1">{{ $indicesInv->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#site-bundles-{{ $site->id }}" type="button">
                                        <i class="las la-boxes me-1"></i> Bundles <span class="badge bg-warning text-white ms-1">{{ $bundlesInv->count() }}</span>
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <!-- Books Tab -->
                                <div class="tab-pane fade show active" id="site-books-{{ $site->id }}">
                                    @if($booksInv->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm align-middle" id="site-books-table-{{ $site->id }}">
                                                <thead>
                                                    <tr>
                                                        <th>Book Title</th>
                                                        <th>Quantity</th>
                                                        <th>Reorder Point</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($booksInv as $inv)
                                                    <tr class="paginate-row">
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
                                        <div class="alert alert-light text-center my-3 text-muted">No books inventory at this site</div>
                                    @endif
                                </div>

                                <!-- Indices Tab -->
                                <div class="tab-pane fade" id="site-indices-{{ $site->id }}">
                                    @if($indicesInv->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm align-middle" id="site-indices-table-{{ $site->id }}">
                                                <thead>
                                                    <tr>
                                                        <th>Index Name</th>
                                                        <th>Quantity</th>
                                                        <th>Reorder Point</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($indicesInv as $inv)
                                                    <tr class="paginate-row">
                                                        <td>
                                                            {{ $inv->bookIndex->book->name ?? 'Unknown Book' }}
                                                            <span class="badge bg-info light text-info ms-1">{{ $inv->bookIndex->index_value ?? 'Index' }}</span>
                                                        </td>
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
                                        <div class="alert alert-light text-center my-3 text-muted">No index inventory at this site</div>
                                    @endif
                                </div>

                                <!-- Bundles Tab -->
                                <div class="tab-pane fade" id="site-bundles-{{ $site->id }}">
                                    @if($bundlesInv->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm align-middle" id="site-bundles-table-{{ $site->id }}">
                                                <thead>
                                                    <tr>
                                                        <th>Bundle Name</th>
                                                        <th>Quantity</th>
                                                        <th>Reorder Point</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($bundlesInv as $inv)
                                                    <tr class="paginate-row">
                                                        <td>
                                                            {{ $inv->bookBundle->name ?? 'Unknown Bundle' }}
                                                            <span class="badge bg-warning light text-warning ms-1">Bundle</span>
                                                        </td>
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
                                        <div class="alert alert-light text-center my-3 text-muted">No bundle inventory at this site</div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info text-center my-3">No inventory at this site</div>
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
                    <div class="mb-3">
                        <label class="form-label font-w600">Site *</label>
                        <select class="form-control" id="mgmtSiteSelect" onchange="onStockMgmtSiteChange()">
                            @foreach($sites ?? [] as $site)
                                <option value="{{ $site->id }}">{{ $site->name }}</option>
                            @endforeach
                        </select>
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

    <!-- Index Stock Management Modal -->
    <div class="modal fade" id="indexStockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h6 class="modal-title m-0 text-white"><i class="las la-pen me-2"></i>Index Stock Management</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label font-w600">Book Name</label>
                        <input type="text" class="form-control" id="mgmtIndexBookName" disabled>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-w600">Index Value</label>
                            <input type="text" class="form-control" id="mgmtIndexValue" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-w600">Current Stock</label>
                            <input type="number" class="form-control" id="mgmtIndexCurrentStock" disabled>
                        </div>
                    </div>

                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="idxAddTab" data-bs-toggle="tab" data-bs-target="#idxAddTabContent" type="button" role="tab" aria-controls="idxAddTabContent" aria-selected="true">
                                <i class="las la-plus me-1"></i>Add Stock
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="idxEditTab" data-bs-toggle="tab" data-bs-target="#idxEditTabContent" type="button" role="tab" aria-controls="idxEditTabContent" aria-selected="false">
                                <i class="las la-edit me-1"></i>Edit Stock
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="idxAddTabContent" role="tabpanel" aria-labelledby="idxAddTab">
                            <div class="mb-3">
                                <label class="form-label font-w600">Quantity to Add *</label>
                                <input type="number" class="form-control" id="mgmtIndexAddQuantity" placeholder="Enter quantity" min="1">
                            </div>
                            <div class="alert alert-info" id="mgmtIndexAddPreview" style="display:none;">
                                <small><strong>New Stock:</strong> <span id="mgmtIndexAddNewStock">0</span></small>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="idxEditTabContent" role="tabpanel" aria-labelledby="idxEditTab">
                            <div class="mb-3">
                                <label class="form-label font-w600">Set Stock to *</label>
                                <input type="number" class="form-control" id="mgmtIndexEditQuantity" placeholder="Enter new stock value" min="0">
                            </div>
                            <div class="alert alert-info" id="mgmtIndexEditPreview" style="display:none;">
                                <small><strong>Change from:</strong> <span id="mgmtIndexEditOldStock">0</span> to <span id="mgmtIndexEditNewStock">0</span></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-primary" id="mgmtIndexSaveBtn" onclick="saveIndexStockManagement()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bundle Stock Management Modal -->
    <div class="modal fade" id="bundleStockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h6 class="modal-title m-0 text-white"><i class="las la-pen me-2"></i>Bundle Stock Management</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label font-w600">Bundle Name</label>
                        <input type="text" class="form-control" id="mgmtBundleName" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-w600">Current Stock</label>
                        <input type="number" class="form-control" id="mgmtBundleCurrentStock" disabled>
                    </div>

                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="bndlAddTab" data-bs-toggle="tab" data-bs-target="#bndlAddTabContent" type="button" role="tab" aria-controls="bndlAddTabContent" aria-selected="true">
                                <i class="las la-plus me-1"></i>Add Stock
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="bndlEditTab" data-bs-toggle="tab" data-bs-target="#bndlEditTabContent" type="button" role="tab" aria-controls="bndlEditTabContent" aria-selected="false">
                                <i class="las la-edit me-1"></i>Edit Stock
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="bndlAddTabContent" role="tabpanel" aria-labelledby="bndlAddTab">
                            <div class="mb-3">
                                <label class="form-label font-w600">Quantity to Add *</label>
                                <input type="number" class="form-control" id="mgmtBundleAddQuantity" placeholder="Enter quantity" min="1">
                            </div>
                            <div class="alert alert-info" id="mgmtBundleAddPreview" style="display:none;">
                                <small><strong>New Stock:</strong> <span id="mgmtBundleAddNewStock">0</span></small>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="bndlEditTabContent" role="tabpanel" aria-labelledby="bndlEditTab">
                            <div class="mb-3">
                                <label class="form-label font-w600">Set Stock to *</label>
                                <input type="number" class="form-control" id="mgmtBundleEditQuantity" placeholder="Enter new stock value" min="0">
                            </div>
                            <div class="alert alert-info" id="mgmtBundleEditPreview" style="display:none;">
                                <small><strong>Change from:</strong> <span id="mgmtBundleEditOldStock">0</span> to <span id="mgmtBundleEditNewStock">0</span></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-primary" id="mgmtBundleSaveBtn" onclick="saveBundleStockManagement()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Site Modal -->
    <div class="modal fade" id="addSiteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h6 class="modal-title text-white"><i class="las la-plus me-2"></i>Add New Site</h6>
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
                            <input type="text" name="location" class="form-control" placeholder="e.g., Quezon City">
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
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
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
                            <label class="form-label font-w600">Items to Transfer</label>
                            <table class="table table-bordered" id="transferBooksTable" style="width:100%;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40%;">Item</th>
                                        <th style="width: 25%;">Quantity</th>
                                        <th style="width: 25%;">Available</th>
                                        <th style="width: 10%;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="transferBooksBody">
                                    <tr id="emptyBooksRow">
                                        <td colspan="4" class="text-center text-muted py-3">Select a source site above, then click an Add button to start.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Add Item Buttons -->
                        <div class="mb-4 d-flex flex-wrap gap-2" style="position: relative; z-index: 1;">
                            <button type="button" class="btn btn-primary" id="showAddBookBtn" disabled style="pointer-events: auto;">
                                <i class="las la-plus me-1"></i>Add Book
                            </button>
                            <button type="button" class="btn btn-info text-white" id="showAddIndexBtn" disabled style="pointer-events: auto;">
                                <i class="las la-plus me-1"></i>Add Index
                            </button>
                            <button type="button" class="btn btn-warning text-white" id="showAddBundleBtn" disabled style="pointer-events: auto;">
                                <i class="las la-plus me-1"></i>Add Bundle
                            </button>
                        </div>

                        <!-- Dynamic Add Item Form (Hidden by default) -->
                        <div id="addBookForm" style="display: none; position: relative; z-index: 1;" class="mb-4 p-3 bg-light rounded">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0" id="addBookFormTitle"><i class="las la-plus-circle me-2"></i>Add New Book</h6>
                                <button type="button" class="btn-close" id="closeAddBookForm"></button>
                            </div>
                            <div class="row g-3">
                                <!-- Hidden type select (value set by Javascript buttons) -->
                                <select id="itemTypeSelect" style="display: none !important;">
                                    <option value="book">Book / Non-Book</option>
                                    <option value="index">Book Index</option>
                                    <option value="bundle">Book Bundle</option>
                                </select>
                                
                                <div class="col-md-8">
                                    <label class="form-label font-w600" id="itemSelectLabel">Select Item *</label>
                                    <select id="bookSelect" class="form-control select2-single" style="width: 100%;">
                                        <option value="">-- Select an Item --</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label font-w600">Quantity *</label>
                                    <input type="number" id="bookQuantity" class="form-control" placeholder="Qty" min="1" disabled>
                                </div>
                                <div class="col-12">
                                    <button type="button" id="confirmAddBookBtn" class="btn btn-success w-100" style="padding: 0.75rem 1rem; font-size: 0.95rem; background-color: #68CF29; border-color: #68CF29; color: #000; font-weight: 600;">
                                        ADD TO TRANSFER
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
                            <h6 class="font-w600">Create Site</h6>
                            <p class="text-muted mb-0">Click "Add Site" to create a new site location. Set the name, code, location, and description.</p>
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
    <div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 99999; display: flex; flex-direction: column; gap: 10px;"></div>
@push('scripts')
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script>
        function showNotification(message, type = 'success') {
            let toastContainer = document.getElementById('toastContainer');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toastContainer';
                toastContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 99999; display: flex; flex-direction: column; gap: 10px;';
                document.body.appendChild(toastContainer);
            }
            
            const toastId = 'toast-' + Date.now();
            const bgColor = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-warning';
            const icon = type === 'success' ? 'la-check-circle' : type === 'error' ? 'la-exclamation-circle' : 'la-info-circle';
            
            const toastHTML = `
                <div id="${toastId}" class="toast show text-white ${bgColor}" role="alert" style="min-width: 280px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border: none;">
                    <div class="toast-header ${bgColor} text-white border-0">
                        <i class="las ${icon} me-2"></i>
                        <strong class="me-auto">${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body pt-0">
                        ${message}
                    </div>
                </div>
            `;
            
            toastContainer.insertAdjacentHTML('beforeend', toastHTML);
            
            const toastElement = document.getElementById(toastId);
            if (toastElement && typeof bootstrap !== 'undefined' && bootstrap.Toast) {
                try {
                    const bsToast = new bootstrap.Toast(toastElement);
                    bsToast.show();
                    toastElement.addEventListener('hidden.bs.toast', function() {
                        toastElement.remove();
                    });
                } catch(e) {}
            }
            
            setTimeout(() => {
                toastElement?.remove();
            }, 4000);
        }

        function closeModal(modalId) {
            const modalEl = document.getElementById(modalId);
            if (!modalEl) return;
            try {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const inst = bootstrap.Modal.getInstance(modalEl) || bootstrap.Modal.getOrCreateInstance(modalEl);
                    if (inst) inst.hide();
                }
                if (window.jQuery && typeof jQuery.fn.modal === 'function') {
                    $(modalEl).modal('hide');
                }
            } catch (e) {}
            modalEl.classList.remove('show');
            modalEl.style.display = 'none';
            document.body.classList.remove('modal-open');
            document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
        }

        let currentBookName = null;
        let currentStock = 0;
        let maxStock = null;
        let currentBookId = null;
        let globalBookMaxStock = null;
        let stockMgmtAddHandler = null;
        let stockMgmtEditHandler = null;

        function onStockMgmtSiteChange() {
            if (!currentBookId) return;
            const siteId = parseInt(document.getElementById('mgmtSiteSelect').value);
            if (!siteId) return;

            const inventory = sitesInventoryData[siteId] || [];
            const item = inventory.find(i => i.book_id === currentBookId);
            
            const stockVal = item ? item.quantity : 0;
            const siteMaxStock = (item && item.max_stock !== null) ? item.max_stock : (globalBookMaxStock || null);

            currentStock = stockVal;
            maxStock = siteMaxStock;

            document.getElementById('mgmtCurrentStock').value = currentStock;
            document.getElementById('mgmtMaxStock').value = maxStock !== null ? maxStock : 'Not Set';

            // Trigger inputs update to refresh previews/warnings
            const addQtyInput = document.getElementById('mgmtAddQuantity');
            if (addQtyInput && addQtyInput.value) {
                addQtyInput.dispatchEvent(new Event('input'));
            }
            const editQtyInput = document.getElementById('mgmtEditQuantity');
            if (editQtyInput && editQtyInput.value) {
                editQtyInput.dispatchEvent(new Event('input'));
            }
        }

        function openStockManagementModal(bookId, bookName, stock, max) {
            currentBookId = bookId;
            currentBookName = bookName;
            globalBookMaxStock = max;

            document.getElementById('mgmtBookName').value = bookName;
            
            // Default select site to "Main Warehouse" if it exists
            const siteSelect = document.getElementById('mgmtSiteSelect');
            if (siteSelect) {
                let mainWarehouseOption = [...siteSelect.options].find(opt => opt.text.trim() === 'Main Warehouse');
                if (mainWarehouseOption) {
                    siteSelect.value = mainWarehouseOption.value;
                } else if (siteSelect.options.length > 0) {
                    siteSelect.selectedIndex = 0;
                }
            }

            // Sync values for selected site
            onStockMgmtSiteChange();
            
            document.getElementById('mgmtAddQuantity').value = '';
            document.getElementById('mgmtAddWarning').innerHTML = '';
            document.getElementById('mgmtAddPreview').style.display = 'none';
            document.getElementById('mgmtEditQuantity').value = '';
            document.getElementById('mgmtEditWarning').innerHTML = '';
            document.getElementById('mgmtEditPreview').style.display = 'none';

            const saveBtn = document.getElementById('mgmtSaveBtn');
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = 'Save Changes';
            }

            // Force visual and functional reset of active tab to "Add Stock"
            const addTabBtn = document.getElementById('addTab');
            const editTabBtn = document.getElementById('editTab');
            const addTabPane = document.getElementById('addTabContent');
            const editTabPane = document.getElementById('editTabContent');

            if (addTabBtn && editTabBtn && addTabPane && editTabPane) {
                addTabBtn.classList.add('active');
                addTabBtn.setAttribute('aria-selected', 'true');
                editTabBtn.classList.remove('active');
                editTabBtn.setAttribute('aria-selected', 'false');

                addTabPane.classList.add('show', 'active');
                editTabPane.classList.remove('show', 'active');
            }

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
                        warning.innerHTML = `<span class="text-warning"><i class="las la-exclamation-triangle"></i> Notice: New stock (${newStock}) exceeds max stock limit (${maxStock})</span>`;
                    } else {
                        warning.innerHTML = '';
                    }
                    if (saveBtn) saveBtn.disabled = false;
                } else {
                    preview.style.display = 'none';
                    warning.innerHTML = '';
                    if (saveBtn) saveBtn.disabled = false;
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
                        warning.innerHTML = `<span class="text-warning"><i class="las la-exclamation-triangle"></i> Notice: New stock (${newStock}) exceeds max stock limit (${maxStock})</span>`;
                    } else {
                        warning.innerHTML = '';
                    }
                    if (saveBtn) saveBtn.disabled = false;
                } else {
                    preview.style.display = 'none';
                    warning.innerHTML = '';
                    if (saveBtn) saveBtn.disabled = false;
                }
            };

            document.getElementById('mgmtAddQuantity').addEventListener('input', stockMgmtAddHandler);
            document.getElementById('mgmtEditQuantity').addEventListener('input', stockMgmtEditHandler);

            const modal = new bootstrap.Modal(document.getElementById('stockManagementModal'));
            modal.show();
        }

        function saveStockManagement() {
            const addPane = document.getElementById('addTabContent');
            const editPane = document.getElementById('editTabContent');
            
            const isAddActive = addPane && (addPane.classList.contains('active') || addPane.classList.contains('show'));
            const isEditActive = editPane && (editPane.classList.contains('active') || editPane.classList.contains('show'));

            const addQtyVal = document.getElementById('mgmtAddQuantity')?.value;
            const editQtyVal = document.getElementById('mgmtEditQuantity')?.value;

            if (isAddActive || (addQtyVal && !editQtyVal)) {
                saveAddStock();
            } else if (isEditActive || editQtyVal) {
                saveEditStock();
            } else {
                saveAddStock();
            }
        }

        function saveAddStock() {
            const saveBtn = document.getElementById('mgmtSaveBtn');
            const originalText = saveBtn ? saveBtn.innerHTML : 'Save Changes';

            try {
                const quantityInput = document.getElementById('mgmtAddQuantity');
                const quantity = parseInt(quantityInput ? quantityInput.value : '');
                const siteId = document.getElementById('mgmtSiteSelect')?.value;

                if (!siteId) {
                    showNotification('Please select a site', 'warning');
                    return;
                }

                if (!quantity || isNaN(quantity) || quantity < 1) {
                    showNotification('Please enter a valid quantity to add', 'warning');
                    return;
                }

                const newStock = currentStock + quantity;



                if (!currentBookId) {
                    showNotification('No item selected', 'error');
                    return;
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

                if (saveBtn) {
                    saveBtn.disabled = true;
                    saveBtn.innerHTML = '<i class="las la-spinner la-spin me-1"></i>Saving...';
                }

                fetch(`/production/inventory/update-stock/${currentBookId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'add',
                        site_id: siteId,
                        quantity: quantity,
                        new_stock: newStock
                    })
                })
                .then(async response => {
                    const data = await response.json();
                    if (response.ok && data.success) {
                        showNotification(data.message || 'Stock added successfully!', 'success');
                        closeModal('stockManagementModal');
                        setTimeout(() => location.reload(), 200);
                    } else {
                        showNotification('Error: ' + (data.message || 'Failed to update stock'), 'error');
                        if (saveBtn) {
                            saveBtn.disabled = false;
                            saveBtn.innerHTML = originalText;
                        }
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showNotification('An error occurred while adding stock', 'error');
                    if (saveBtn) {
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = originalText;
                    }
                });

            } catch (err) {
                console.error('saveAddStock exception:', err);
                showNotification('An unexpected error occurred', 'error');
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = originalText;
                }
            }
        }

        function saveEditStock() {
            const saveBtn = document.getElementById('mgmtSaveBtn');
            const originalText = saveBtn ? saveBtn.innerHTML : 'Save Changes';

            try {
                const editInput = document.getElementById('mgmtEditQuantity');
                const newStock = parseInt(editInput ? editInput.value : '');
                const siteId = document.getElementById('mgmtSiteSelect')?.value;

                if (!siteId) {
                    showNotification('Please select a site', 'warning');
                    return;
                }

                if (isNaN(newStock) || newStock < 0) {
                    showNotification('Please enter a valid stock value', 'warning');
                    return;
                }



                if (!currentBookId) {
                    showNotification('No item selected', 'error');
                    return;
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

                if (saveBtn) {
                    saveBtn.disabled = true;
                    saveBtn.innerHTML = '<i class="las la-spinner la-spin me-1"></i>Saving...';
                }

                fetch(`/production/inventory/update-stock/${currentBookId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'set',
                        site_id: siteId,
                        new_stock: newStock
                    })
                })
                .then(async response => {
                    const data = await response.json();
                    if (response.ok && data.success) {
                        showNotification(data.message || 'Stock updated successfully!', 'success');
                        closeModal('stockManagementModal');
                        setTimeout(() => location.reload(), 200);
                    } else {
                        showNotification('Error: ' + (data.message || 'Failed to update stock'), 'error');
                        if (saveBtn) {
                            saveBtn.disabled = false;
                            saveBtn.innerHTML = originalText;
                        }
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showNotification('An error occurred while updating stock', 'error');
                    if (saveBtn) {
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = originalText;
                    }
                });

            } catch (err) {
                console.error('saveEditStock exception:', err);
                showNotification('An unexpected error occurred', 'error');
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = originalText;
                }
            }
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
                            book_id: {{ $inv->book_id ?? 'null' }},
                            book_index_id: {{ $inv->book_index_id ?? 'null' }},
                            book_bundle_id: {{ $inv->book_bundle_id ?? 'null' }},
                            book: { name: '{{ addslashes($inv->book->name ?? ($inv->bookIndex->index_value ?? ($inv->bookBundle->name ?? 'Unknown'))) }}' },
                            quantity: {{ $inv->quantity ?? 0 }},
                            reorder_point: {{ $inv->reorder_point ?? 'null' }},
                            max_stock: {{ $inv->max_stock ?? 'null' }}
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
            
            const itemTypeSelect = document.getElementById('itemTypeSelect');
            if (itemTypeSelect) itemTypeSelect.value = 'book';

            const addBookForm = document.getElementById('addBookForm');
            const showAddBookBtn = document.getElementById('showAddBookBtn');
            const showAddIndexBtn = document.getElementById('showAddIndexBtn');
            const showAddBundleBtn = document.getElementById('showAddBundleBtn');
            
            if (addBookForm) addBookForm.style.display = 'none';
            if (showAddBookBtn) {
                showAddBookBtn.disabled = true;
                showAddBookBtn.style.display = 'block';
            }
            if (showAddIndexBtn) {
                showAddIndexBtn.disabled = true;
                showAddIndexBtn.style.display = 'block';
            }
            if (showAddBundleBtn) {
                showAddBundleBtn.disabled = true;
                showAddBundleBtn.style.display = 'block';
            }
            
            const bookSelect = document.getElementById('bookSelect');
            const quantityInput = document.getElementById('bookQuantity');
            if (bookSelect) bookSelect.innerHTML = '<option value="">-- Select an Item --</option>';
            if (quantityInput) {
                quantityInput.value = '';
                quantityInput.disabled = true;
            }
            
            renderSelectedBooks();
            updateSubmitButton();
        };

        // Transfer Stock Functions — Batch Submit
        document.getElementById('transferStockForm')?.addEventListener('submit', function(e) {
            e.preventDefault();

            const keys = Object.keys(selectedBooksMap);
            if (keys.length === 0) {
                showNotification('Please add at least one item', 'error');
                return;
            }

            const fromSiteId = document.getElementById('fromSiteSelect').value;
            const toSiteId   = document.getElementById('toSiteSelect').value;

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

            // Build items array — books, indices & bundles all together
            const items = keys.map(key => {
                const item = selectedBooksMap[key];
                return {
                    type:     item.type,      // 'book' | 'index' | 'bundle'
                    item_id:  item.itemId,
                    quantity: item.quantity
                };
            });

            const submitBtn   = document.getElementById('submitTransferBtn');
            const originalTxt = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="las la-spinner la-spin me-1"></i>Submitting…';
            submitBtn.disabled  = true;

            fetch('/production/sites/transfer-batch', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept':       'application/json'
                },
                body: JSON.stringify({
                    from_site_id: fromSiteId,
                    to_site_id:   toSiteId,
                    notes:        document.querySelector('textarea[name="notes"]')?.value || '',
                    items:        items
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const skippedMsg = data.skipped > 0 ? ` (${data.skipped} skipped due to insufficient stock)` : '';
                    showNotification(`${data.created} item(s) transfer submitted!${skippedMsg}`, data.skipped > 0 ? 'warning' : 'success');
                    bootstrap.Modal.getInstance(document.getElementById('transferStockModal')).hide();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification(data.message || 'Transfer failed', 'error');
                    submitBtn.innerHTML = originalTxt;
                    submitBtn.disabled  = false;
                }
            })
            .catch(err => {
                console.error('Batch transfer error:', err);
                showNotification('Network error. Please try again.', 'error');
                submitBtn.innerHTML = originalTxt;
                submitBtn.disabled  = false;
            });
        });

        // When source site is selected, load its inventory
        document.getElementById('fromSiteSelect')?.addEventListener('change', function() {
            const siteId = this.value;
            const showAddBookBtn = document.getElementById('showAddBookBtn');
            const showAddIndexBtn = document.getElementById('showAddIndexBtn');
            const showAddBundleBtn = document.getElementById('showAddBundleBtn');
            
            if (siteId) {
                selectedBooksMap = {};
                nextRowId = 1;
                renderSelectedBooks();
                updateSubmitButton();
                
                loadBooksForSite(siteId);
                if (showAddBookBtn) showAddBookBtn.disabled = false;
                if (showAddIndexBtn) showAddIndexBtn.disabled = false;
                if (showAddBundleBtn) showAddBundleBtn.disabled = false;
            } else {
                if (showAddBookBtn) showAddBookBtn.disabled = true;
                if (showAddIndexBtn) showAddIndexBtn.disabled = true;
                if (showAddBundleBtn) showAddBundleBtn.disabled = true;
                const bookSelect = document.getElementById('bookSelect');
                if (bookSelect) bookSelect.innerHTML = '<option value="">-- Select an Item --</option>';
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

        document.getElementById('itemTypeSelect')?.addEventListener('change', function() {
            const siteId = document.getElementById('fromSiteSelect').value;
            if (siteId) {
                populateBookSelect(parseInt(siteId));
            }
        });

        function populateBookSelect(siteId) {
            const select = document.getElementById('bookSelect');
            if (!select) return;
            
            const itemType = document.getElementById('itemTypeSelect')?.value || 'book';
            const inventory = siteBooks[siteId] || [];
            console.log('Populating dropdown with inventory:', inventory, 'filtered by type:', itemType);
            
            // Filter by item type
            const typeFiltered = inventory.filter(item => item.type === itemType);
            
            // Filter out items already in the selectedBooksMap
            const availableItems = typeFiltered.filter(item => {
                const key = itemType + '_' + item.item_id;
                return !selectedBooksMap[key];
            });

            const placeholderText = `-- Select a ${itemType.charAt(0).toUpperCase() + itemType.slice(1)} --`;
            select.innerHTML = `<option value="">${placeholderText}</option>`;
            
            if (availableItems.length === 0) {
                if (typeFiltered.length === 0) {
                    select.innerHTML = `<option value="">-- No ${itemType}s available --</option>`;
                } else {
                    select.innerHTML = `<option value="">-- All ${itemType}s added --</option>`;
                }
            } else {
                availableItems.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.item_id;
                    option.textContent = `${item.name} (Available: ${item.quantity})`;
                    option.dataset.available = item.quantity;
                    option.dataset.name = item.name;
                    option.dataset.itemId = item.item_id;
                    option.dataset.type = item.type;
                    select.appendChild(option);
                });
            }

            // Initialize or Refresh Select2 (with integrated search)
            if (window.jQuery && typeof jQuery.fn.select2 === 'function') {
                const $select = $('#bookSelect');
                if ($select.data('select2')) {
                    $select.select2('destroy');
                }
                $select.select2({
                    dropdownParent: $('#transferStockModal'),
                    placeholder: placeholderText,
                    allowClear: true,
                    width: '100%',
                    minimumResultsForSearch: 0
                });

                $select.off('change.itemSelect').on('change.itemSelect', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const availableSpan = document.getElementById('selectedBookAvailable');
                    const quantityInput = document.getElementById('bookQuantity');
                    
                    if (selectedOption && selectedOption.value) {
                        if (availableSpan) availableSpan.textContent = selectedOption.dataset.available || '0';
                        if (quantityInput) {
                            quantityInput.max = selectedOption.dataset.available || 1;
                            quantityInput.value = '';
                            quantityInput.disabled = false;
                        }
                    } else {
                        if (availableSpan) availableSpan.textContent = '0';
                        if (quantityInput) {
                            quantityInput.max = 1;
                            quantityInput.value = '';
                            quantityInput.disabled = true;
                        }
                    }
                });
            } else {
                select.onchange = function() {
                    const selected = this.options[this.selectedIndex];
                    const availableSpan = document.getElementById('selectedBookAvailable');
                    const quantityInput = document.getElementById('bookQuantity');
                    
                    if (selected && selected.value) {
                        if (availableSpan) availableSpan.textContent = selected.dataset.available || '0';
                        if (quantityInput) {
                            quantityInput.max = selected.dataset.available || 1;
                            quantityInput.value = '';
                            quantityInput.disabled = false;
                        }
                    } else {
                        if (availableSpan) availableSpan.textContent = '0';
                        if (quantityInput) {
                            quantityInput.max = 1;
                            quantityInput.value = '';
                            quantityInput.disabled = true;
                        }
                    }
                };
                if (select.onchange) select.onchange();
            }
        }

        function openAddItemSubForm(type) {
            const addBookForm = document.getElementById('addBookForm');
            const showAddBookBtn = document.getElementById('showAddBookBtn');
            const showAddIndexBtn = document.getElementById('showAddIndexBtn');
            const showAddBundleBtn = document.getElementById('showAddBundleBtn');
            
            if (addBookForm) addBookForm.style.display = 'block';
            if (showAddBookBtn) showAddBookBtn.style.display = 'none';
            if (showAddIndexBtn) showAddIndexBtn.style.display = 'none';
            if (showAddBundleBtn) showAddBundleBtn.style.display = 'none';
            
            // Set type select value
            const itemTypeSelect = document.getElementById('itemTypeSelect');
            if (itemTypeSelect) itemTypeSelect.value = type;
            
            // Update title & select label
            const titleEl = document.getElementById('addBookFormTitle');
            const labelEl = document.getElementById('itemSelectLabel');

            if (type === 'book') {
                if (titleEl) titleEl.innerHTML = '<i class="las la-book me-2"></i>Add New Book';
                if (labelEl) labelEl.textContent = 'Select Book *';
            } else if (type === 'index') {
                if (titleEl) titleEl.innerHTML = '<i class="las la-pen me-2"></i>Add New Index';
                if (labelEl) labelEl.textContent = 'Select Index *';
            } else if (type === 'bundle') {
                if (titleEl) titleEl.innerHTML = '<i class="las la-cubes me-2"></i>Add New Bundle';
                if (labelEl) labelEl.textContent = 'Select Bundle *';
            }

            const bookSelect = document.getElementById('bookSelect');
            const quantityInput = document.getElementById('bookQuantity');
            
            if (bookSelect) bookSelect.value = '';
            if (quantityInput) {
                quantityInput.value = '';
                quantityInput.disabled = true;
            }
            const availableSpan = document.getElementById('selectedBookAvailable');
            if (availableSpan) availableSpan.textContent = '0';
            
            const fromSiteId = document.getElementById('fromSiteSelect').value;
            if (fromSiteId) {
                populateBookSelect(parseInt(fromSiteId));
            }
        }

        document.getElementById('showAddBookBtn')?.addEventListener('click', function() {
            openAddItemSubForm('book');
        });
        document.getElementById('showAddIndexBtn')?.addEventListener('click', function() {
            openAddItemSubForm('index');
        });
        document.getElementById('showAddBundleBtn')?.addEventListener('click', function() {
            openAddItemSubForm('bundle');
        });

        document.getElementById('closeAddBookForm')?.addEventListener('click', function() {
            const addBookForm = document.getElementById('addBookForm');
            const showAddBookBtn = document.getElementById('showAddBookBtn');
            const showAddIndexBtn = document.getElementById('showAddIndexBtn');
            const showAddBundleBtn = document.getElementById('showAddBundleBtn');
            
            if (addBookForm) addBookForm.style.display = 'none';
            if (showAddBookBtn) showAddBookBtn.style.display = 'block';
            if (showAddIndexBtn) showAddIndexBtn.style.display = 'block';
            if (showAddBundleBtn) showAddBundleBtn.style.display = 'block';
            
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
                showNotification('Please select an item', 'error');
                return;
            }

            if (!quantityInput || !quantityInput.value || parseInt(quantityInput.value) < 1) {
                showNotification('Please enter a valid quantity', 'error');
                return;
            }

            const itemId = parseInt(bookSelect.value);
            const selectedOption = bookSelect.options[bookSelect.selectedIndex];
            const bookName = selectedOption.dataset.name;
            const available = parseInt(selectedOption.dataset.available);
            const type = selectedOption.dataset.type;
            const quantity = parseInt(quantityInput.value);

            if (quantity > available) {
                showNotification(`Insufficient stock. Available: ${available}`, 'error');
                return;
            }

            addBookToTransfer(itemId, type, bookName, quantity, available);

            bookSelect.value = '';
            quantityInput.value = '';
            quantityInput.disabled = true;
            const availableSpan = document.getElementById('selectedBookAvailable');
            if (availableSpan) availableSpan.textContent = '0';
            
            const addBookForm = document.getElementById('addBookForm');
            const showAddBookBtn = document.getElementById('showAddBookBtn');
            const showAddIndexBtn = document.getElementById('showAddIndexBtn');
            const showAddBundleBtn = document.getElementById('showAddBundleBtn');
            if (addBookForm) addBookForm.style.display = 'none';
            if (showAddBookBtn) showAddBookBtn.style.display = 'block';
            if (showAddIndexBtn) showAddIndexBtn.style.display = 'block';
            if (showAddBundleBtn) showAddBundleBtn.style.display = 'block';
            
            const fromSiteId = document.getElementById('fromSiteSelect').value;
            if (fromSiteId) {
                populateBookSelect(parseInt(fromSiteId));
            }
        });

        function addBookToTransfer(itemId, type, bookName, quantity, available) {
            const key = type + '_' + itemId;
            if (selectedBooksMap[key]) {
                showNotification('This item is already added', 'error');
                return;
            }

            selectedBooksMap[key] = {
                itemId: itemId,
                type: type,
                name: bookName,
                quantity: quantity,
                available: available,
                rowId: nextRowId++
            };

            renderSelectedBooks();
            updateSubmitButton();
            showNotification(`${bookName} added to transfer list`, 'success');
        }

        window.removeBookFromTransfer = function(key) {
            delete selectedBooksMap[key];
            renderSelectedBooks();
            updateSubmitButton();
            
            const fromSiteId = document.getElementById('fromSiteSelect').value;
            if (fromSiteId) {
                populateBookSelect(parseInt(fromSiteId));
            }
        };
        
        window.updateBookQuantity = function(key, newQuantity) {
            const book = selectedBooksMap[key];
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
                tbody.innerHTML = '<tr id="emptyBooksRow"><td colspan="4" class="text-center text-muted py-3">No items added. Click "Add Book" to start.</td></tr>';
                return;
            }

            tbody.innerHTML = Object.entries(selectedBooksMap).map(([key, book]) => `
                <tr data-key="${key}">
                    <td><strong>${escapeHtml(book.name)} <span class="badge badge-xs bg-secondary text-uppercase">${book.type}</span></strong></td>
                    <td>
                        <input type="number" 
                               class="form-control form-control-sm" 
                               value="${book.quantity}" 
                               min="1" 
                               max="${book.available}"
                               style="width: 100px;"
                               onchange="updateBookQuantity('${key}', parseInt(this.value))">
                    </td>
                    <td><span class="badge bg-info">${book.available} available</span></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeBookFromTransfer('${key}')" title="Remove">
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
                submitBtn.innerHTML = `<i class="las la-check me-1"></i>Request Transfer (${bookCount} item${bookCount !== 1 ? 's' : ''})`;
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

        // --- BOOK INDEX STOCK MANAGEMENT ---
        let currentIndexId = null;
        let currentIndexStock = 0;

        window.openIndexStockModal = function(indexId, bookName, indexValue, stock) {
            console.log("openIndexStockModal called", {indexId, bookName, indexValue, stock});
            try {
                currentIndexId = indexId;
                currentIndexStock = stock;

                const bookNameInput = document.getElementById('mgmtIndexBookName');
                if (bookNameInput) bookNameInput.value = bookName;

                const indexValueInput = document.getElementById('mgmtIndexValue');
                if (indexValueInput) indexValueInput.value = indexValue;

                const currentStockInput = document.getElementById('mgmtIndexCurrentStock');
                if (currentStockInput) currentStockInput.value = stock;

                const addQtyInput = document.getElementById('mgmtIndexAddQuantity');
                if (addQtyInput) addQtyInput.value = '';

                const addPreview = document.getElementById('mgmtIndexAddPreview');
                if (addPreview) addPreview.style.display = 'none';

                const editQtyInput = document.getElementById('mgmtIndexEditQuantity');
                if (editQtyInput) editQtyInput.value = '';

                const editPreview = document.getElementById('mgmtIndexEditPreview');
                if (editPreview) editPreview.style.display = 'none';

                // Hook preview events
                if (addQtyInput) {
                    addQtyInput.oninput = function() {
                        const qty = parseInt(this.value) || 0;
                        const preview = document.getElementById('mgmtIndexAddPreview');
                        if (qty > 0) {
                            if (preview) preview.style.display = 'block';
                            const addNewStock = document.getElementById('mgmtIndexAddNewStock');
                            if (addNewStock) addNewStock.textContent = currentIndexStock + qty;
                        } else {
                            if (preview) preview.style.display = 'none';
                        }
                    };
                }

                if (editQtyInput) {
                    editQtyInput.oninput = function() {
                        const val = parseInt(this.value);
                        const preview = document.getElementById('mgmtIndexEditPreview');
                        if (!isNaN(val) && val >= 0) {
                            if (preview) preview.style.display = 'block';
                            const editOldStock = document.getElementById('mgmtIndexEditOldStock');
                            if (editOldStock) editOldStock.textContent = currentIndexStock;
                            const editNewStock = document.getElementById('mgmtIndexEditNewStock');
                            if (editNewStock) editNewStock.textContent = val;
                        } else {
                            if (preview) preview.style.display = 'none';
                        }
                    };
                }

                const modalEl = document.getElementById('indexStockModal');
                if (modalEl) {
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                    console.log("indexStockModal show called successfully");
                } else {
                    console.error("indexStockModal element not found in DOM");
                }
            } catch (err) {
                console.error("Error in openIndexStockModal:", err);
            }
        };

        window.saveIndexStockManagement = function() {
            const activeTab = document.querySelector('#indexStockModal .nav-link.active');
            let action = 'add';
            let qty = 0;
            let newStock = 0;

            if (activeTab && activeTab.id === 'idxAddTab') {
                action = 'add';
                qty = parseInt(document.getElementById('mgmtIndexAddQuantity').value);
                if (!qty || qty < 1) {
                    showNotification('Please enter a valid quantity to add', 'warning');
                    return;
                }
            } else {
                action = 'set';
                newStock = parseInt(document.getElementById('mgmtIndexEditQuantity').value);
                if (isNaN(newStock) || newStock < 0) {
                    showNotification('Please enter a valid new stock value', 'warning');
                    return;
                }
            }

            const saveBtn = document.getElementById('mgmtIndexSaveBtn');
            const originalText = saveBtn.innerHTML;
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="las la-spinner la-spin"></i> Saving...';

            fetch(`/production/inventory/update-index-stock/${currentIndexId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    action: action,
                    quantity: qty,
                    new_stock: newStock
                })
            })
            .then(async response => {
                const data = await response.json();
                if (response.ok && data.success) {
                    showNotification(data.message || 'Index stock updated successfully!', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('indexStockModal')).hide();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    let msg = data.message || 'Failed to update index stock';
                    if (data.errors) {
                        msg = Object.values(data.errors).flat().join(' ');
                    }
                    showNotification('Error: ' + msg, 'error');
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while saving index stock', 'error');
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
            });
        };

        // --- BOOK BUNDLE STOCK MANAGEMENT ---
        let currentBundleId = null;
        let currentBundleStock = 0;

        window.openBundleStockModal = function(bundleId, bundleName, stock) {
            console.log("openBundleStockModal called", {bundleId, bundleName, stock});
            try {
                currentBundleId = bundleId;
                currentBundleStock = stock;

                const bundleNameInput = document.getElementById('mgmtBundleName');
                if (bundleNameInput) bundleNameInput.value = bundleName;

                const currentStockInput = document.getElementById('mgmtBundleCurrentStock');
                if (currentStockInput) currentStockInput.value = stock;

                const addQtyInput = document.getElementById('mgmtBundleAddQuantity');
                if (addQtyInput) addQtyInput.value = '';

                const addPreview = document.getElementById('mgmtBundleAddPreview');
                if (addPreview) addPreview.style.display = 'none';

                const editQtyInput = document.getElementById('mgmtBundleEditQuantity');
                if (editQtyInput) editQtyInput.value = '';

                const editPreview = document.getElementById('mgmtBundleEditPreview');
                if (editPreview) editPreview.style.display = 'none';

                // Hook preview events
                if (addQtyInput) {
                    addQtyInput.oninput = function() {
                        const qty = parseInt(this.value) || 0;
                        const preview = document.getElementById('mgmtBundleAddPreview');
                        if (qty > 0) {
                            if (preview) preview.style.display = 'block';
                            const addNewStock = document.getElementById('mgmtBundleAddNewStock');
                            if (addNewStock) addNewStock.textContent = currentBundleStock + qty;
                        } else {
                            if (preview) preview.style.display = 'none';
                        }
                    };
                }

                if (editQtyInput) {
                    editQtyInput.oninput = function() {
                        const val = parseInt(this.value);
                        const preview = document.getElementById('mgmtBundleEditPreview');
                        if (!isNaN(val) && val >= 0) {
                            if (preview) preview.style.display = 'block';
                            const editOldStock = document.getElementById('mgmtBundleEditOldStock');
                            if (editOldStock) editOldStock.textContent = currentBundleStock;
                            const editNewStock = document.getElementById('mgmtBundleEditNewStock');
                            if (editNewStock) editNewStock.textContent = val;
                        } else {
                            if (preview) preview.style.display = 'none';
                        }
                    };
                }

                const modalEl = document.getElementById('bundleStockModal');
                if (modalEl) {
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                    console.log("bundleStockModal show called successfully");
                } else {
                    console.error("bundleStockModal element not found in DOM");
                }
            } catch (err) {
                console.error("Error in openBundleStockModal:", err);
            }
        };

        window.saveBundleStockManagement = function() {
            const activeTab = document.querySelector('#bundleStockModal .nav-link.active');
            let action = 'add';
            let qty = 0;
            let newStock = 0;

            if (activeTab && activeTab.id === 'bndlAddTab') {
                action = 'add';
                qty = parseInt(document.getElementById('mgmtBundleAddQuantity').value);
                if (!qty || qty < 1) {
                    showNotification('Please enter a valid quantity to add', 'warning');
                    return;
                }
            } else {
                action = 'set';
                newStock = parseInt(document.getElementById('mgmtBundleEditQuantity').value);
                if (isNaN(newStock) || newStock < 0) {
                    showNotification('Please enter a valid new stock value', 'warning');
                    return;
                }
            }

            const saveBtn = document.getElementById('mgmtBundleSaveBtn');
            const originalText = saveBtn.innerHTML;
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="las la-spinner la-spin"></i> Saving...';

            fetch(`/production/inventory/update-bundle-stock/${currentBundleId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    action: action,
                    quantity: qty,
                    new_stock: newStock
                })
            })
            .then(async response => {
                const data = await response.json();
                if (response.ok && data.success) {
                    showNotification(data.message || 'Bundle stock updated successfully!', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('bundleStockModal')).hide();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    let msg = data.message || 'Failed to update bundle stock';
                    if (data.errors) {
                        msg = Object.values(data.errors).flat().join(' ');
                    }
                    showNotification('Error: ' + msg, 'error');
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while saving bundle stock', 'error');
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
            });
        };

        // --- TAB STATE PERSISTENCE ---
        document.addEventListener('DOMContentLoaded', function() {
            // Page-level tabs configuration
            const pageTabs = ['stocks-tab', 'sites-tab', 'transfer-workflow-tab'];
            pageTabs.forEach(tabId => {
                const button = document.getElementById(tabId);
                button?.addEventListener('shown.bs.tab', function(event) {
                    localStorage.setItem('active_inventory_overview_tab', event.target.id);
                });
            });

            // Restore page-level tab
            const savedPageTabId = localStorage.getItem('active_inventory_overview_tab');
            if (savedPageTabId && pageTabs.includes(savedPageTabId)) {
                const tabButton = document.getElementById(savedPageTabId);
                if (tabButton) {
                    const tab = new bootstrap.Tab(tabButton);
                    tab.show();
                }
            }

            // Card-level nested registry tabs configuration
            const registryTabs = ['registry-books-tab', 'registry-nonbooks-tab', 'registry-indices-tab', 'registry-bundles-tab'];
            registryTabs.forEach(tabId => {
                const button = document.getElementById(tabId);
                button?.addEventListener('shown.bs.tab', function(event) {
                    localStorage.setItem('active_inventory_registry_tab', event.target.id);
                });
            });

            // Restore card-level nested registry tab
            const savedRegistryTabId = localStorage.getItem('active_inventory_registry_tab');
            if (savedRegistryTabId && registryTabs.includes(savedRegistryTabId)) {
                const tabButton = document.getElementById(savedRegistryTabId);
                if (tabButton) {
                    const tab = new bootstrap.Tab(tabButton);
                    tab.show();
                }
            }

            // Site Inventory Modal Client-side Pagination (Sliding Window)
            function initSiteTablePagination(tableId, pageSize = 10) {
                const table = document.getElementById(tableId);
                if (!table) return;
                const tbody = table.querySelector('tbody');
                if (!tbody) return;
                const rows = Array.from(tbody.querySelectorAll('tr.paginate-row'));
                if (rows.length === 0) return;

                let currentPage = 1;
                const totalPages = Math.ceil(rows.length / pageSize);

                let container = document.getElementById(tableId + '_pagination');
                if (!container) {
                    container = document.createElement('div');
                    container.id = tableId + '_pagination';
                    container.className = 'd-flex flex-wrap justify-content-between align-items-center mt-3 pt-2 border-top gap-2';
                    table.parentNode.appendChild(container);
                }

                function render() {
                    const start = (currentPage - 1) * pageSize;
                    const end = start + pageSize;

                    rows.forEach((row, idx) => {
                        row.style.display = (idx >= start && idx < end) ? '' : 'none';
                    });

                    const showingStart = Math.min(start + 1, rows.length);
                    const showingEnd = Math.min(end, rows.length);

                    let html = `<small class="text-muted fw-bold">Showing ${showingStart} to ${showingEnd} of ${rows.length} entries</small>`;
                    
                    if (totalPages > 1) {
                        html += `<ul class="pagination pagination-sm m-0 flex-wrap">`;
                        
                        // Previous button
                        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                            <button class="page-link py-1 px-2" type="button" data-page="${currentPage - 1}">Prev</button>
                        </li>`;

                        // Smart sliding window for page numbers
                        let pages = [];
                        if (totalPages <= 7) {
                            for (let i = 1; i <= totalPages; i++) pages.push(i);
                        } else {
                            pages.push(1);
                            if (currentPage > 3) {
                                pages.push('...');
                            }
                            
                            let startPage = Math.max(2, currentPage - 1);
                            let endPage = Math.min(totalPages - 1, currentPage + 1);

                            if (currentPage <= 3) {
                                endPage = 4;
                            }
                            if (currentPage >= totalPages - 2) {
                                startPage = totalPages - 3;
                            }

                            for (let i = startPage; i <= endPage; i++) {
                                pages.push(i);
                            }

                            if (currentPage < totalPages - 2) {
                                pages.push('...');
                            }
                            pages.push(totalPages);
                        }

                        pages.forEach(p => {
                            if (p === '...') {
                                html += `<li class="page-item disabled"><span class="page-link py-1 px-2">...</span></li>`;
                            } else {
                                html += `<li class="page-item ${currentPage === p ? 'active' : ''}">
                                    <button class="page-link py-1 px-2" type="button" data-page="${p}">${p}</button>
                                </li>`;
                            }
                        });

                        // Next button
                        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                            <button class="page-link py-1 px-2" type="button" data-page="${currentPage + 1}">Next</button>
                        </li>`;
                        
                        html += `</ul>`;
                    }

                    container.innerHTML = html;

                    container.querySelectorAll('button.page-link').forEach(btn => {
                        btn.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            const p = parseInt(this.getAttribute('data-page'));
                            if (p >= 1 && p <= totalPages) {
                                currentPage = p;
                                render();
                            }
                        });
                    });
                }

                render();
            }

            document.querySelectorAll('[id^="viewSiteInventory"]').forEach(modalEl => {
                const siteId = modalEl.id.replace('viewSiteInventory', '');
                
                function initAllTabs() {
                    initSiteTablePagination(`site-books-table-${siteId}`, 6);
                    initSiteTablePagination(`site-indices-table-${siteId}`, 6);
                    initSiteTablePagination(`site-bundles-table-${siteId}`, 6);
                }

                modalEl.addEventListener('shown.bs.modal', initAllTabs);

                modalEl.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tabBtn => {
                    tabBtn.addEventListener('shown.bs.tab', initAllTabs);
                });
            });
        });
    </script>
@endpush
</x-app-layout>

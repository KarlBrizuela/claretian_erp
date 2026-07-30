<x-app-layout :title="'Pick Lists'" :sidebar="'production'">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0 d-block d-sm-flex">
                    <div>
                        <h4 class="fs-24 mb-0 text-black">Pick Lists</h4>
                    </div>
                    <a href="{{ route('production.logistic.pick-list-management') }}" class="btn btn-primary rounded d-flex align-items-center" style="gap: 0.5rem; padding: 0.5rem 1rem; height: 38px; min-height: 38px; line-height: 1.5; box-sizing: border-box; border: none; background: #ff0000; color: #ffffff; font-weight: 500;">
                        <i class="las la-plus" style="font-size: 1rem; line-height: 1; margin: 0; padding: 0; background: transparent; border: none; box-shadow: none;"></i>
                        <span style="font-size: 0.875rem; white-space: nowrap;">Create New Pick List</span>
                    </a>
                </div>
                <div class="card-body">
                    <!-- Main Tabs Navigation -->
                    <ul class="nav nav-tabs mb-3" id="mainTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="all-pick-lists-tab" data-bs-toggle="tab" data-bs-target="#all-pick-lists" type="button" role="tab" aria-controls="all-pick-lists" aria-selected="true">
                                All Pick Lists
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ecom-tab" data-bs-toggle="tab" data-bs-target="#ecom-direct" type="button" role="tab" aria-controls="ecom-direct" aria-selected="false">
                                E-Commerce Direct <span class="badge bg-info ms-2">{{ $ecomByPlatform['lazada']->count() + $ecomByPlatform['shopee']->count() + $ecomByPlatform['tiktok']->count() }}</span>
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="mainTabsContent">
                        <!-- All Pick Lists Tab -->
                        <div class="tab-pane fade show active" id="all-pick-lists" role="tabpanel" aria-labelledby="all-pick-lists-tab">
                            <div class="table-responsive">
                                <table id="pickListsTable" class="display" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>Pick List Number</th>
                                            <th>Sales Order</th>
                                            <th>Customer</th>
                                            <th>Date Created</th>
                                            <th>Total Items</th>
                                            <th>Items Picked</th>
                                            <th>Status</th>
                                            <th>Prepared By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pickLists as $pickList)
                                        <tr>
                                            <td><strong>{{ $pickList->pick_list_number }}</strong></td>
                                            <td>{{ $pickList->salesOrder->so_number ?? 'N/A' }}</td>
                                            <td>{{ $pickList->salesOrder->customer->customer_name ?? 'Unknown' }}</td>
                                            <td>{{ $pickList->created_at->format('Y-m-d') }}</td>
                                            <td>{{ $pickList->pickListItems->sum('requested_qty') }}</td>
                                            <td>{{ $pickList->pickListItems->sum('picked_qty') }}</td>
                                            <td>
                                                @if($pickList->status === 'draft')
                                                    <span class="badge bg-secondary">Draft</span>
                                                @elseif($pickList->status === 'in_progress')
                                                    <span class="badge" style="background-color: #0dcaf0;">Picking</span>
                                                @elseif($pickList->status === 'completed')
                                                    <span class="badge bg-success">Completed</span>
                                                @endif
                                            </td>
                                            <td>{{ $pickList->preparedByUser->name ?? 'System' }}</td>
                                            <td>
                                                <div class="workflow-actions">
                                                    <a href="{{ route('production.logistic.pick-list-details', $pickList->id) }}" class="btn btn-danger shadow btn-xs sharp me-1" title="View Details">
                                                        <i class="las la-eye"></i>
                                                    </a>
                                                    <form action="{{ route('production.logistic.mark-as-gathered', $pickList->salesOrder->id ?? 0) }}" method="POST" style="display:inline;" onsubmit="return confirm('Mark as gathered?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success shadow btn-xs sharp me-1" title="Mark as Gathered">
                                                            <i class="las la-check"></i>
                                                        </button>
                                                    </form>
                                                    <a href="javascript:void(0);" class="btn btn-info shadow btn-xs sharp" title="Print" onclick="window.print();">
                                                        <i class="las la-print"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="9" class="text-center">No active pick lists.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- E-Commerce Direct Tab -->
                        <div class="tab-pane fade" id="ecom-direct" role="tabpanel" aria-labelledby="ecom-tab">
                            <!-- Sub-tabs for Platforms -->
                            <ul class="nav nav-tabs mb-3" id="ecomTabs" role="tablist" style="border-bottom: 2px solid #dee2e6;">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="lazada-tab" data-bs-toggle="tab" data-bs-target="#lazada-content" type="button" role="tab" aria-controls="lazada-content" aria-selected="true">
                                        <i class="las la-shopping-bag me-2"></i>Lazada <span class="badge bg-primary ms-2">{{ $ecomByPlatform['lazada']->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="shopee-tab" data-bs-toggle="tab" data-bs-target="#shopee-content" type="button" role="tab" aria-controls="shopee-content" aria-selected="false">
                                        <i class="las la-shopping-bag me-2"></i>Shopee <span class="badge bg-danger ms-2">{{ $ecomByPlatform['shopee']->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tiktok-tab" data-bs-toggle="tab" data-bs-target="#tiktok-content" type="button" role="tab" aria-controls="tiktok-content" aria-selected="false">
                                        <i class="las la-music me-2"></i>TikTok <span class="badge bg-dark ms-2">{{ $ecomByPlatform['tiktok']->count() }}</span>
                                    </button>
                                </li>
                            </ul>

                            <!-- Sub-tabs Content -->
                            <div class="tab-content" id="ecomTabsContent">
                                <!-- Lazada Tab -->
                                <div class="tab-pane fade show active" id="lazada-content" role="tabpanel" aria-labelledby="lazada-tab">
                                    <div class="table-responsive">
                                        <table id="lazadaTable" class="display ecom-table" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th>Pick List Number</th>
                                                    <th>Sales Order</th>
                                                    <th>Customer</th>
                                                    <th>Date Created</th>
                                                    <th>Total Items</th>
                                                    <th>Items Picked</th>
                                                    <th>Status</th>
                                                    <th>Prepared By</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($ecomByPlatform['lazada'] as $pickList)
                                                <tr>
                                                    <td><strong>{{ $pickList->pick_list_number }}</strong></td>
                                                    <td>{{ $pickList->salesOrder->so_number ?? 'N/A' }}</td>
                                                    <td>{{ $pickList->salesOrder->customer->customer_name ?? 'Unknown' }}</td>
                                                    <td>{{ $pickList->created_at->format('Y-m-d') }}</td>
                                                    <td>{{ $pickList->pickListItems->sum('requested_qty') }}</td>
                                                    <td>{{ $pickList->pickListItems->sum('picked_qty') }}</td>
                                                    <td>
                                                        @if($pickList->status === 'draft')
                                                            <span class="badge bg-secondary">Draft</span>
                                                        @elseif($pickList->status === 'in_progress')
                                                            <span class="badge" style="background-color: #0dcaf0;">Picking</span>
                                                        @elseif($pickList->status === 'completed')
                                                            <span class="badge bg-success">Completed</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $pickList->preparedByUser->name ?? 'System' }}</td>
                                                    <td>
                                                        <div class="workflow-actions">
                                                            <a href="{{ route('production.logistic.pick-list-details', $pickList->id) }}" class="btn btn-danger shadow btn-xs sharp me-1" title="View Details">
                                                                <i class="las la-eye"></i>
                                                            </a>
                                                            <form action="{{ route('production.logistic.mark-as-gathered', $pickList->salesOrder->id ?? 0) }}" method="POST" style="display:inline;" onsubmit="return confirm('Mark as gathered?');">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success shadow btn-xs sharp me-1" title="Mark as Gathered">
                                                                    <i class="las la-check"></i>
                                                                </button>
                                                            </form>
                                                            <a href="javascript:void(0);" class="btn btn-info shadow btn-xs sharp" title="Print" onclick="window.print();">
                                                                <i class="las la-print"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="9" class="text-center">No Lazada pick lists.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Shopee Tab -->
                                <div class="tab-pane fade" id="shopee-content" role="tabpanel" aria-labelledby="shopee-tab">
                                    <div class="table-responsive">
                                        <table id="shopeeTable" class="display ecom-table" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th>Pick List Number</th>
                                                    <th>Sales Order</th>
                                                    <th>Customer</th>
                                                    <th>Date Created</th>
                                                    <th>Total Items</th>
                                                    <th>Items Picked</th>
                                                    <th>Status</th>
                                                    <th>Prepared By</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($ecomByPlatform['shopee'] as $pickList)
                                                <tr>
                                                    <td><strong>{{ $pickList->pick_list_number }}</strong></td>
                                                    <td>{{ $pickList->salesOrder->so_number ?? 'N/A' }}</td>
                                                    <td>{{ $pickList->salesOrder->customer->customer_name ?? 'Unknown' }}</td>
                                                    <td>{{ $pickList->created_at->format('Y-m-d') }}</td>
                                                    <td>{{ $pickList->pickListItems->sum('requested_qty') }}</td>
                                                    <td>{{ $pickList->pickListItems->sum('picked_qty') }}</td>
                                                    <td>
                                                        @if($pickList->status === 'draft')
                                                            <span class="badge bg-secondary">Draft</span>
                                                        @elseif($pickList->status === 'in_progress')
                                                            <span class="badge" style="background-color: #0dcaf0;">Picking</span>
                                                        @elseif($pickList->status === 'completed')
                                                            <span class="badge bg-success">Completed</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $pickList->preparedByUser->name ?? 'System' }}</td>
                                                    <td>
                                                        <div class="workflow-actions">
                                                            <a href="{{ route('production.logistic.pick-list-details', $pickList->id) }}" class="btn btn-danger shadow btn-xs sharp me-1" title="View Details">
                                                                <i class="las la-eye"></i>
                                                            </a>
                                                            <form action="{{ route('production.logistic.mark-as-gathered', $pickList->salesOrder->id ?? 0) }}" method="POST" style="display:inline;" onsubmit="return confirm('Mark as gathered?');">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success shadow btn-xs sharp me-1" title="Mark as Gathered">
                                                                    <i class="las la-check"></i>
                                                                </button>
                                                            </form>
                                                            <a href="javascript:void(0);" class="btn btn-info shadow btn-xs sharp" title="Print" onclick="window.print();">
                                                                <i class="las la-print"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="9" class="text-center">No Shopee pick lists.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- TikTok Tab -->
                                <div class="tab-pane fade" id="tiktok-content" role="tabpanel" aria-labelledby="tiktok-tab">
                                    <div class="table-responsive">
                                        <table id="tiktokTable" class="display ecom-table" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th>Pick List Number</th>
                                                    <th>Sales Order</th>
                                                    <th>Customer</th>
                                                    <th>Date Created</th>
                                                    <th>Total Items</th>
                                                    <th>Items Picked</th>
                                                    <th>Status</th>
                                                    <th>Prepared By</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($ecomByPlatform['tiktok'] as $pickList)
                                                <tr>
                                                    <td><strong>{{ $pickList->pick_list_number }}</strong></td>
                                                    <td>{{ $pickList->salesOrder->so_number ?? 'N/A' }}</td>
                                                    <td>{{ $pickList->salesOrder->customer->customer_name ?? 'Unknown' }}</td>
                                                    <td>{{ $pickList->created_at->format('Y-m-d') }}</td>
                                                    <td>{{ $pickList->pickListItems->sum('requested_qty') }}</td>
                                                    <td>{{ $pickList->pickListItems->sum('picked_qty') }}</td>
                                                    <td>
                                                        @if($pickList->status === 'draft')
                                                            <span class="badge bg-secondary">Draft</span>
                                                        @elseif($pickList->status === 'in_progress')
                                                            <span class="badge" style="background-color: #0dcaf0;">Picking</span>
                                                        @elseif($pickList->status === 'completed')
                                                            <span class="badge bg-success">Completed</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $pickList->preparedByUser->name ?? 'System' }}</td>
                                                    <td>
                                                        <div class="workflow-actions">
                                                            <a href="{{ route('production.logistic.pick-list-details', $pickList->id) }}" class="btn btn-danger shadow btn-xs sharp me-1" title="View Details">
                                                                <i class="las la-eye"></i>
                                                            </a>
                                                            <form action="{{ route('production.logistic.mark-as-gathered', $pickList->salesOrder->id ?? 0) }}" method="POST" style="display:inline;" onsubmit="return confirm('Mark as gathered?');">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success shadow btn-xs sharp me-1" title="Mark as Gathered">
                                                                    <i class="las la-check"></i>
                                                                </button>
                                                            </form>
                                                            <a href="javascript:void(0);" class="btn btn-info shadow btn-xs sharp" title="Print" onclick="window.print();">
                                                                <i class="las la-print"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="9" class="text-center">No TikTok pick lists.</td>
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
    </div>

    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <style>
        .dataTables_wrapper {
            font-size: 13px;
        }
        
        #pickListsTable, #lazadaTable, #shopeeTable, #tiktokTable {
            font-size: 13px;
        }
        
        #pickListsTable thead th, #lazadaTable thead th, #shopeeTable thead th, #tiktokTable thead th {
            padding: 8px 10px;
            font-weight: 600;
            font-size: 13px;
        }
        
        #pickListsTable tbody td, #lazadaTable tbody td, #shopeeTable tbody td, #tiktokTable tbody td {
            padding: 6px 10px;
            vertical-align: middle;
        }
        
        .workflow-actions {
            display: flex;
            gap: 3px;
            align-items: center;
        }
        
        .workflow-actions .btn {
            padding: 2px 4px !important;
            font-size: 10px !important;
            min-width: 24px !important;
            width: 24px !important;
            height: 24px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        
        .workflow-actions .btn i {
            margin: 0 !important;
            font-size: 12px !important;
        }

        /* Tab styling improvements */
        .nav-tabs {
            border-bottom: 2px solid #dee2e6;
        }

        .nav-tabs .nav-link {
            color: #495057;
            border: none;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link:hover {
            border-bottom-color: #0dcaf0;
            color: #0dcaf0;
        }

        .nav-tabs .nav-link.active {
            color: #fff;
            background-color: #0dcaf0;
            border-bottom-color: #0dcaf0;
            border-radius: 4px 4px 0 0;
        }

        .nav-tabs .nav-link .badge {
            font-size: 11px;
            padding: 3px 6px;
        }

        #ecomTabs .nav-link {
            font-size: 14px;
            padding: 0.5rem 1rem;
        }

        #ecomTabs .nav-link.active {
            background-color: #0dcaf0;
            color: white;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        console.log('Pick Lists page loaded');
        
        $(document).ready(function() {
            console.log('Document ready - initializing DataTables');
            
            try {
                // Initialize main pick lists table
                const mainTable = $('#pickListsTable').DataTable({
                    order: [[3, 'desc']],
                    pageLength: 25,
                    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
                });
                console.log('Main table initialized:', mainTable);

                // Initialize Lazada table
                const lazadaTable = $('#lazadaTable').DataTable({
                    order: [[3, 'desc']],
                    pageLength: 25,
                    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
                });
                console.log('Lazada table initialized:', lazadaTable);

                // Initialize Shopee table
                const shopeeTable = $('#shopeeTable').DataTable({
                    order: [[3, 'desc']],
                    pageLength: 25,
                    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
                });
                console.log('Shopee table initialized:', shopeeTable);

                // Initialize TikTok table
                const tiktokTable = $('#tiktokTable').DataTable({
                    order: [[3, 'desc']],
                    pageLength: 25,
                    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
                });
                console.log('TikTok table initialized:', tiktokTable);

            } catch (error) {
                console.error('Error initializing DataTables:', error);
                console.error('Error message:', error.message);
                console.error('Error stack:', error.stack);
            }
            
            // Log all pick list data
            console.log('Pick list data:', {!! json_encode($pickLists ?? []) !!});
            console.log('E-com by platform:', {!! json_encode($ecomByPlatform ?? []) !!});
        });
    </script>
    @endpush
</x-app-layout>

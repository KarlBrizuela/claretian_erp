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
                            <button class="nav-link active" id="all-pick-lists-tab" data-bs-toggle="tab" data-toggle="tab" data-bs-target="#all-pick-lists" data-target="#all-pick-lists" type="button" role="tab" aria-controls="all-pick-lists" aria-selected="true">
                                All Pick Lists
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ecom-tab" data-bs-toggle="tab" data-toggle="tab" data-bs-target="#ecom-direct" data-target="#ecom-direct" type="button" role="tab" aria-controls="ecom-direct" aria-selected="false">
                                E-Commerce Direct <span class="badge bg-info ms-2">{{ $ecomByPlatform['lazada']->count() + $ecomByPlatform['shopee']->count() + $ecomByPlatform['tiktok']->count() + ($ecomByPlatform['cob']?->count() ?? 0) }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="complimentary-tab" data-bs-toggle="tab" data-toggle="tab" data-bs-target="#complimentary-pick-lists" data-target="#complimentary-pick-lists" type="button" role="tab" aria-controls="complimentary-pick-lists" aria-selected="false">
                                Complimentary <span class="badge ms-2" style="background-color: #6f42c1; color: #fff;">{{ $complimentaryPickLists->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="team-stocks-tab" data-bs-toggle="tab" data-toggle="tab" data-bs-target="#team-stocks-pick-lists" data-target="#team-stocks-pick-lists" type="button" role="tab" aria-controls="team-stocks-pick-lists" aria-selected="false">
                                Team Stock Transfers <span class="badge bg-danger ms-2">{{ isset($teamStockPickLists) ? $teamStockPickLists->count() : 0 }}</span>
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
                                            <th>Transaction Type</th>
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
                                            <td>
                                                <span class="badge bg-light text-dark border">{{ $pickList->salesOrder->transaction_type ?? 'N/A' }}</span>
                                            </td>
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
                                                    <a href="{{ route('production.logistic.shipping-label', $pickList->salesOrder?->id ?? $pickList->id) }}" target="_blank" class="btn btn-primary shadow btn-xs sharp me-1" title="Shipping Label">
                                                        <i class="las la-tag"></i>
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
                                            <td colspan="10" class="text-center">No active pick lists.</td>
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
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="cob-tab" data-bs-toggle="tab" data-bs-target="#cob-content" type="button" role="tab" aria-controls="cob-content" aria-selected="false">
                                        <i class="las la-building me-2"></i>COB <span class="badge ms-2" style="background-color: #6f42c1; color: #fff;">{{ $ecomByPlatform['cob']->count() }}</span>
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
                                                    <th>Transaction Type</th>
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
                                                    <td>
                                                        <span class="badge bg-light text-dark border">{{ $pickList->salesOrder->transaction_type ?? 'N/A' }}</span>
                                                    </td>
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
                                                            <a href="{{ route('production.logistic.shipping-label', $pickList->salesOrder?->id ?? $pickList->id) }}" target="_blank" class="btn btn-primary shadow btn-xs sharp me-1" title="Shipping Label">
                                                                <i class="las la-tag"></i>
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
                                                    <td colspan="10" class="text-center">No Lazada pick lists.</td>
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
                                                    <th>Transaction Type</th>
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
                                                    <td>
                                                        <span class="badge bg-light text-dark border">{{ $pickList->salesOrder->transaction_type ?? 'N/A' }}</span>
                                                    </td>
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
                                                            <a href="{{ route('production.logistic.shipping-label', $pickList->salesOrder?->id ?? $pickList->id) }}" target="_blank" class="btn btn-primary shadow btn-xs sharp me-1" title="Shipping Label">
                                                                <i class="las la-tag"></i>
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
                                                    <td colspan="10" class="text-center">No Shopee pick lists.</td>
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
                                                    <th>Transaction Type</th>
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
                                                    <td>
                                                        <span class="badge bg-light text-dark border">{{ $pickList->salesOrder->transaction_type ?? 'N/A' }}</span>
                                                    </td>
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
                                                            <a href="{{ route('production.logistic.shipping-label', $pickList->salesOrder?->id ?? $pickList->id) }}" target="_blank" class="btn btn-primary shadow btn-xs sharp me-1" title="Shipping Label">
                                                                <i class="las la-tag"></i>
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
                                                    <td colspan="10" class="text-center">No TikTok pick lists.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- COB Tab -->
                                <div class="tab-pane fade" id="cob-content" role="tabpanel" aria-labelledby="cob-tab">
                                    <div class="table-responsive">
                                        <table id="cobTable" class="display ecom-table" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th>Pick List Number</th>
                                                    <th>Sales Order</th>
                                                    <th>Transaction Type</th>
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
                                                @forelse($ecomByPlatform['cob'] as $pickList)
                                                <tr>
                                                    <td><strong>{{ $pickList->pick_list_number }}</strong></td>
                                                    <td>{{ $pickList->salesOrder->so_number ?? 'N/A' }}</td>
                                                    <td>
                                                        <span class="badge bg-light text-dark border">{{ $pickList->salesOrder->transaction_type ?? 'N/A' }}</span>
                                                    </td>
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
                                                            <a href="{{ route('production.logistic.shipping-label', $pickList->salesOrder?->id ?? $pickList->id) }}" target="_blank" class="btn btn-primary shadow btn-xs sharp me-1" title="Shipping Label">
                                                                <i class="las la-tag"></i>
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
                                                    <td colspan="10" class="text-center">No COB pick lists.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Complimentary Pick Lists Tab -->
                        <div class="tab-pane fade" id="complimentary-pick-lists" role="tabpanel" aria-labelledby="complimentary-tab">
                            <div class="table-responsive">
                                <table id="complimentaryPickListsTable" class="display" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>Pick List Number</th>
                                            <th>Sales Order</th>
                                            <th>Transaction Type</th>
                                            <th>Recipient / Customer</th>
                                            <th>Date Created</th>
                                            <th>Total Items</th>
                                            <th>Items Picked</th>
                                            <th>Status</th>
                                            <th>Prepared By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($complimentaryPickLists as $pickList)
                                        <tr>
                                            <td><strong>{{ $pickList->pick_list_number }}</strong></td>
                                            <td>{{ $pickList->salesOrder->so_number ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-light text-dark border">{{ $pickList->salesOrder->transaction_type ?? 'N/A' }}</span>
                                            </td>
                                            <td>{{ $pickList->salesOrder->customer->customer_name ?? 'Unknown' }}</td>
                                            <td>{{ $pickList->created_at->format('Y-m-d') }}</td>
                                            <td>{{ $pickList->pickListItems->sum('requested_qty') }}</td>
                                            <td>{{ $pickList->pickListItems->sum('picked_qty') }}</td>
                                            <td>
                                                @if($pickList->status === 'draft')
                                                    <span class="badge bg-secondary">Draft</span>
                                                @elseif($pickList->status === 'in_progress')
                                                    <span class="badge" style="background-color: #6f42c1; color: #fff;">Picking (Complimentary)</span>
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
                                                    <a href="{{ route('production.logistic.shipping-label', $pickList->salesOrder?->id ?? $pickList->id) }}" target="_blank" class="btn btn-primary shadow btn-xs sharp me-1" title="Shipping Label">
                                                        <i class="las la-tag"></i>
                                                    </a>
                                                    <form action="{{ route('production.logistic.mark-as-gathered', $pickList->salesOrder->id ?? 0) }}" method="POST" style="display:inline;" onsubmit="return confirm('Mark as gathered and send to Acknowledgement Receipt preparation?');">
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
                                            <td colspan="9" class="text-center py-4 text-muted">No active complimentary pick lists.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Team Stock Transfers Tab -->
                        <div class="tab-pane fade" id="team-stocks-pick-lists" role="tabpanel" aria-labelledby="team-stocks-tab">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle mb-0" style="width: 100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Transfer #</th>
                                            <th>Target Team</th>
                                            <th>Transferred By</th>
                                            <th class="text-center">Items Count</th>
                                            <th class="text-center">Total Pcs</th>
                                            <th>Date Created</th>
                                            <th>Remarks</th>
                                            <th>Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($teamStockPickLists ?? [] as $tt)
                                        <tr>
                                            <td class="fw-bold">{{ $tt->transfer_number }}</td>
                                            <td><span class="badge bg-danger">{{ $tt->team_name }}</span></td>
                                                    <td>{{ $tt->transferredByUser->name ?? 'N/A' }}</td>
                                            <td class="text-center">{{ $tt->items->count() }} item(s)</td>
                                            <td class="text-center fw-bold text-success">{{ number_format($tt->items->sum('quantity')) }} pcs</td>
                                            <td>{{ $tt->created_at->format('M d, Y h:i A') }}</td>
                                            <td><span class="text-dark fw-semibold">{{ $tt->notes ?: '—' }}</span></td>
                                            <td><span class="badge bg-warning text-dark">{{ ucwords(str_replace('_', ' ', $tt->status)) }}</span></td>
                                            <td class="text-end" style="white-space: nowrap;">
                                                <div class="d-flex align-items-center justify-content-end gap-1">
                                                    <button type="button" class="btn btn-xs btn-outline-danger" data-bs-toggle="modal" data-bs-target="#teamStockPickModal{{ $tt->id }}">
                                                        <i class="las la-eye me-1"></i>View Items
                                                    </button>
                                                    <form action="{{ route('production.logistic.team-stock-transfer.complete-pick', $tt->id) }}" method="POST" class="d-inline m-0">
                                                        @csrf
                                                        <button type="submit" class="btn btn-xs btn-success fw-bold" style="background-color: #28a745; border: none;">
                                                            <i class="las la-check me-1"></i>Complete Pick
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4 text-muted">No pending team stock transfers ready for picking.</td>
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
    @foreach($teamStockPickLists ?? [] as $tt)
    <div class="modal fade" id="teamStockPickModal{{ $tt->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white"><i class="las la-boxes me-2"></i>Team Stock Transfer Items ({{ $tt->transfer_number }})</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <small class="text-muted d-block mb-1">Target Sales Team:</small>
                            <span class="badge bg-danger fs-6">{{ $tt->team_name }}</span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block mb-1">Requested By:</small>
                            <strong>{{ $tt->transferredByUser->name ?? 'N/A' }}</strong>
                            <small class="d-block text-muted">{{ $tt->created_at->format('M d, Y h:i A') }}</small>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block mb-1">Remarks / Notes:</small>
                            <span class="fw-semibold text-dark">{{ $tt->notes ?: 'None' }}</span>
                        </div>
                    </div>

                    @if($tt->notes)
                    <div class="alert alert-warning border border-warning mb-3 py-2">
                        <strong class="text-dark"><i class="las la-comment-alt me-1"></i>Remarks / Notes:</strong> {{ $tt->notes }}
                    </div>
                    @else
                    <div class="alert alert-light border mb-3 py-2 text-muted">
                        <i class="las la-info-circle me-1"></i>No remarks or notes specified for this transfer.
                    </div>
                    @endif

                    <h6 class="fw-bold mb-2">Items to Pick from Main Warehouse:</h6>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Item Title</th>
                                    <th>Barcode</th>
                                    <th class="text-end">Price</th>
                                    <th>Type</th>
                                    <th class="text-center">Quantity to Pick & Transfer</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tt->items as $tItem)
                                @php
                                    $itemName = $tItem->bookIndex ? $tItem->bookIndex->display_name : ($tItem->book ? $tItem->book->name : ($tItem->bookBundle ? $tItem->bookBundle->name : 'N/A'));
                                    $itemType = $tItem->bookIndex ? 'Book Index' : ($tItem->bookBundle ? 'Book Bundle' : 'Book');

                                    // Barcode resolution
                                    $barcode = $tItem->bookIndex ? ($tItem->bookIndex->barcode ?: ($tItem->bookIndex->nbs_barcode ?: ($tItem->bookIndex->article ?: ($tItem->bookIndex->book?->barcode ?: $tItem->bookIndex->book?->sku))))
                                              : ($tItem->book ? ($tItem->book->barcode ?: ($tItem->book->nbs_barcode ?: ($tItem->book->sku ?: $tItem->book->isbn)))
                                              : ($tItem->bookBundle ? ($tItem->bookBundle->sku ?: '—') : '—'));

                                    // Price resolution
                                    $price = (float)($tItem->price > 0 ? $tItem->price 
                                            : ($tItem->bookIndex ? ($tItem->bookIndex->price ?: ($tItem->bookIndex->book?->price ?: 0))
                                            : ($tItem->book ? ($tItem->book->price ?: 0)
                                            : ($tItem->bookBundle ? ($tItem->bookBundle->price ?: 0) : 0))));
                                @endphp
                                <tr>
                                    <td class="fw-bold text-dark">{{ $itemName }}</td>
                                    <td>
                                        @if($barcode && $barcode !== '—')
                                            <span class="badge bg-light text-dark border font-monospace"><i class="las la-barcode me-1 text-danger"></i>{{ $barcode }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold">₱{{ number_format($price, 2) }}</td>
                                    <td><span class="badge bg-secondary">{{ $itemType }}</span></td>
                                    <td class="text-center fw-bold text-success">{{ number_format($tItem->quantity) }} pcs</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <form action="{{ route('production.logistic.team-stock-transfer.complete-pick', $tt->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success fw-bold">
                            <i class="las la-check me-1"></i>Complete Pick & Transfer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <style>
        .tab-pane {
            display: none;
        }
        .tab-pane.active, .tab-pane.show.active {
            display: block !important;
        }
        .dataTables_wrapper {
            padding: 1rem 0;
            font-size: 13px;
        }
        .dataTables_wrapper .dataTables_length {
            float: left;
            margin-bottom: 1rem;
        }
        .dataTables_wrapper .dataTables_length select {
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            border: 1px solid #ced4da;
            font-size: 0.875rem;
            background-color: #fff;
            cursor: pointer;
        }
        .dataTables_wrapper .dataTables_filter {
            float: right;
            margin-bottom: 1rem;
        }
        .dataTables_wrapper .dataTables_filter input {
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            border: 1px solid #ced4da;
            font-size: 0.875rem;
            margin-left: 0.5rem;
            outline: none;
            transition: border-color 0.2s;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #ff0000;
            box-shadow: 0 0 0 2px rgba(255, 0, 0, 0.1);
        }
        .dataTables_wrapper .dataTables_info {
            float: left;
            padding-top: 0.75rem;
            font-weight: 500;
            color: #6c757d;
        }
        .dataTables_wrapper .dataTables_paginate {
            float: right;
            padding-top: 0.75rem;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.35rem 0.75rem !important;
            margin-left: 4px !important;
            border-radius: 6px !important;
            border: 1px solid #dee2e6 !important;
            background: #fff !important;
            color: #333 !important;
            font-weight: 500 !important;
            cursor: pointer !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #ff0000 !important;
            color: #fff !important;
            border-color: #ff0000 !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5;
            cursor: not-allowed !important;
            background: #f8f9fa !important;
            color: #6c757d !important;
        }
        
        #pickListsTable, #lazadaTable, #shopeeTable, #tiktokTable, #complimentaryPickListsTable {
            font-size: 13px;
        }
        
        #pickListsTable thead th, #lazadaTable thead th, #shopeeTable thead th, #tiktokTable thead th, #complimentaryPickListsTable thead th {
            padding: 8px 10px;
            font-weight: 600;
            font-size: 13px;
        }
        
        #pickListsTable tbody td, #lazadaTable tbody td, #shopeeTable tbody td, #tiktokTable tbody td, #complimentaryPickListsTable tbody td {
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
            
            // Safely initialize DataTables on all tables with class .display
            $('table.display').each(function() {
                if ($.fn.DataTable.isDataTable(this)) return;
                try {
                    $(this).DataTable({
                        order: [],
                        pageLength: 10,
                        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                        searching: true,
                        paging: true,
                        info: true,
                        responsive: true,
                        autoWidth: false,
                        language: {
                            search: "Search:",
                            searchPlaceholder: "Search pick lists...",
                            lengthMenu: "Show _MENU_ entries",
                            info: "Showing _START_ to _END_ of _TOTAL_ entries",
                            paginate: {
                                first: "First",
                                last: "Last",
                                next: "Next",
                                previous: "Previous"
                            }
                        }
                    });
                } catch(e) {
                    console.warn('DataTable init warning in pick-list-list for:', this.id, e);
                }
            });

            // Fail-safe manual tab switching for mainTabs
            $(document).on('click', '#mainTabs .nav-link', function(e) {
                e.preventDefault();
                $('#mainTabs .nav-link').removeClass('active');
                $(this).addClass('active');
                const target = $(this).attr('data-bs-target') || $(this).attr('data-target');
                if (target) {
                    $('#mainTabsContent > .tab-pane').removeClass('show active').css('display', 'none');
                    $(target).addClass('show active').css('display', 'block');
                }
            });
        });
    </script>
    @endpush
</x-app-layout>

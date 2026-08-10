<x-app-layout :title="'Packing Management'" :sidebar="'production'">
    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }
        .status-ready { background-color: #fff3cd; color: #856404; }
        .status-in-progress { background-color: #cce5ff; color: #004085; }
        .status-packed { background-color: #d4edda; color: #155724; }
        .status-partial { background-color: #e2e3e5; color: #383d41; }

        /* Floating Sticky Bulk Action Bar at Bottom of Screen */
        .ready-bulk-floating-bar {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: #1e1e2d;
            color: #ffffff;
            padding: 10px 24px;
            border-radius: 50px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            z-index: 1060;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .ready-bulk-floating-bar.hidden {
            opacity: 0;
            visibility: hidden;
            transform: translate(-50%, 35px);
            pointer-events: none;
        }

        /* Platform badges for Completed Drop-off */
        .platform-badge { padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .platform-lazada { background: #0f146d; color: #fff; }
        .platform-shopee { background: #ee4d2d; color: #fff; }
        .platform-tiktok { background: #010101; color: #fff; }
        .platform-cob { background: #6f42c1; color: #fff; }
    </style>
    @endpush

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0 d-block d-sm-flex">
                    <div>
                        <h4 class="fs-24 mb-0 text-black">Packing Management</h4>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs" id="packingTabs" role="tablist" style="border-bottom: 2px solid #ddd; margin-bottom: 2rem;">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="packing-queue-tab" data-bs-toggle="tab" data-bs-target="#packing-queue-content" type="button" role="tab" aria-controls="packing-queue-content" aria-selected="true" style="font-weight: 600; color: #333;">
                                <i class="fas fa-boxes" style="margin-right: 0.5rem;"></i>Packing Queue
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ecom-tab" data-bs-toggle="tab" data-bs-target="#ecom-direct-content" type="button" role="tab" aria-controls="ecom-direct-content" aria-selected="false" style="font-weight: 600; color: #666;">
                                <i class="fas fa-shopping-bag" style="margin-right: 0.5rem;"></i>E-Commerce Direct <span class="badge bg-info" style="margin-left: 0.5rem;">{{ $ecomByPlatform['lazada']->count() + $ecomByPlatform['shopee']->count() + $ecomByPlatform['tiktok']->count() + ($ecomByPlatform['cob']?->count() ?? 0) }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ready-pickup-tab" data-bs-toggle="tab" data-bs-target="#ready-pickup-content" type="button" role="tab" aria-controls="ready-pickup-content" aria-selected="false" style="font-weight: 600; color: #666;">
                                <i class="fas fa-truck" style="margin-right: 0.5rem;"></i>Ready for Pickup/Drop-off <span class="badge bg-success" style="margin-left: 0.5rem;">{{ count($readyForPickupOrders) }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="complimentary-tab" data-bs-toggle="tab" data-bs-target="#complimentary-content" type="button" role="tab" aria-controls="complimentary-content" aria-selected="false" style="font-weight: 600; color: #666;">
                                <i class="fas fa-gift" style="margin-right: 0.5rem; color: #6f42c1;"></i>Complimentary <span class="badge" style="margin-left: 0.5rem; background-color: #6f42c1; color: #fff;">{{ $complimentaryPackingOrders->count() }}</span>
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Contents -->
                    <div class="tab-content" id="packingTabContent">
                        <!-- Packing Queue Tab -->
                        <div class="tab-pane fade show active" id="packing-queue-content" role="tabpanel" aria-labelledby="packing-queue-tab">
                            <!-- Bulk Action Toolbar -->
                            <div id="bulkActionToolbar" style="display: none; background-color: #e8f4f8; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; border-left: 4px solid #007bff;">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <span style="color: #333; font-weight: 500;">
                                        <span id="selectedCount">0</span> order(s) selected
                                    </span>
                                    <div class="d-flex gap-2">
                                        <button type="button" id="setReadyPickupBtn" class="btn btn-primary" style="background-color: #28a745; border: none; padding: 0.5rem 1.5rem;">
                                            <i class="fas fa-check" style="margin-right: 0.5rem;"></i>Mark Selected as Packed
                                        </button>
                                        <button type="button" id="clearSelectionBtn" class="btn btn-secondary" style="padding: 0.5rem 1.5rem;">
                                            Clear Selection
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table id="packingTable" class="display" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th style="width: 30px;"><input type="checkbox" id="selectAllCheckbox" style="cursor: pointer;"></th>
                                            <th>SO #</th>
                                            <th>Company</th>
                                            <th>SI Signed</th>
                                            <th>Total Items</th>
                                            <th>Packed Items</th>
                                            <th>Total Amount</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                @foreach($packingOrders as $order)
                                @php
                                    $packingData = json_decode($order->packing_data ?? '{}', true);
                                    $packedCount = count(array_filter($packingData, function($item) { return ($item['status'] ?? null) === 'Packed'; }));
                                    $totalItems = $order->items->count();
                                    
                                    if($packedCount === 0) {
                                        $statusClass = 'status-ready';
                                        $statusText = 'Ready for Packing';
                                    } elseif($packedCount === $totalItems && $totalItems > 0) {
                                        $statusClass = 'status-packed';
                                        $statusText = 'Fully Packed';
                                    } else {
                                        $statusClass = 'status-partial';
                                        $statusText = 'Partially Packed';
                                    }
                                    $isFullyPacked = ($packedCount === $totalItems && $totalItems > 0);
                                @endphp
                                <tr class="packing-row" data-order-id="{{ $order->id }}">
                                    <td><input type="checkbox" class="order-checkbox" data-order-id="{{ $order->id }}" data-so-number="{{ $order->so_number }}" style="cursor: pointer;" {{ !$isFullyPacked ? 'disabled' : '' }}></td>
                                    <td><strong>{{ $order->so_number }}</strong></td>
                                    <td>{{ $order->customer->customer_name ?? 'N/A' }}</td>
                                    <td>{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('M d, Y') : 'N/A' }}</td>
                                    <td>{{ $totalItems }}</td>
                                    <td><strong>{{ $packedCount }}/{{ $totalItems }}</strong></td>
                                    <td class="fw-bold">₱{{ number_format($order->items->sum('subtotal'), 2) }}</td>
                                    <td><span class="status-badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-danger shadow view-order-btn"
                                                    onclick="openPackingDetailsModal({{ $order->id }})"
                                                    data-order-id="{{ $order->id }}"
                                                    data-so-number="{{ $order->so_number }}"
                                                    data-customer="{{ $order->customer->customer_name ?? 'N/A' }}"
                                                    data-date="{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}"
                                                    data-signed="{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('Y-m-d') : '' }}"
                                                    title="View Details"
                                                    style="background: #ff0000; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-eye" style="font-size: 0.9rem; pointer-events: none;"></i>
                                            </button>
                                            <button type="button" class="btn btn-success shadow mark-packed-btn"
                                                    onclick="markOrderAsPackedAction({{ $order->id }}, '{{ $order->so_number }}')"
                                                    data-order-id="{{ $order->id }}"
                                                    data-so-number="{{ $order->so_number }}"
                                                    title="Mark as Packed"
                                                    style="background: {{ $isFullyPacked ? '#28a745' : '#ffc107' }}; color: {{ $isFullyPacked ? '#fff' : '#000' }}; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-check" style="font-size: 0.9rem; pointer-events: none;"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                            </div>
                        </div>

                        <!-- E-Commerce Direct Tab -->
                        <div class="tab-pane fade" id="ecom-direct-content" role="tabpanel" aria-labelledby="ecom-tab">
                            <!-- Platform Sub-tabs -->
                            <ul class="nav nav-tabs mb-3" id="ecomTabs" role="tablist" style="border-bottom: 2px solid #dee2e6;">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="lazada-ecom-tab" data-bs-toggle="tab" data-bs-target="#lazada-ecom-content" type="button" role="tab" aria-controls="lazada-ecom-content" aria-selected="true">
                                        <i class="las la-shopping-bag me-2"></i>Lazada <span class="badge bg-primary ms-2">{{ $ecomByPlatform['lazada']->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="shopee-ecom-tab" data-bs-toggle="tab" data-bs-target="#shopee-ecom-content" type="button" role="tab" aria-controls="shopee-ecom-content" aria-selected="false">
                                        <i class="las la-shopping-bag me-2"></i>Shopee <span class="badge bg-danger ms-2">{{ $ecomByPlatform['shopee']->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tiktok-ecom-tab" data-bs-toggle="tab" data-bs-target="#tiktok-ecom-content" type="button" role="tab" aria-controls="tiktok-ecom-content" aria-selected="false">
                                        <i class="las la-music me-2"></i>TikTok <span class="badge bg-dark ms-2">{{ $ecomByPlatform['tiktok']->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="cob-ecom-tab" data-bs-toggle="tab" data-bs-target="#cob-ecom-content" type="button" role="tab" aria-controls="cob-ecom-content" aria-selected="false">
                                        <i class="las la-building me-2" style="color: #6f42c1;"></i>COB <span class="badge ms-2" style="background-color: #6f42c1; color: #fff;">{{ $ecomByPlatform['cob']->count() }}</span>
                                    </button>
                                </li>
                            </ul>

                            <!-- Platform Sub-tabs Content -->
                            <div class="tab-content" id="ecomTabsContent">
                                <!-- Lazada Tab -->
                                <div class="tab-pane fade show active" id="lazada-ecom-content" role="tabpanel" aria-labelledby="lazada-ecom-tab">
                                    <div class="table-responsive">
                                        <table id="lazadaPackingTable" class="display" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 30px;"><input type="checkbox" class="ecom-select-all-checkbox" data-platform="lazada" style="cursor: pointer;"></th>
                                                    <th>SO #</th>
                                                    <th>Customer</th>
                                                    <th>SI Signed</th>
                                                    <th>Total Items</th>
                                                    <th>Packed Items</th>
                                                    <th>Total Amount</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($ecomByPlatform['lazada'] as $order)
                                                @php
                                                    $packingData = json_decode($order->packing_data ?? '{}', true);
                                                    $packedCount = count(array_filter($packingData, function($item) { return ($item['status'] ?? null) === 'Packed'; }));
                                                    $totalItems = $order->items->count();
                                                    
                                                    if($packedCount === 0) {
                                                        $statusClass = 'status-ready';
                                                        $statusText = 'Ready for Packing';
                                                    } elseif($packedCount === $totalItems && $totalItems > 0) {
                                                        $statusClass = 'status-packed';
                                                        $statusText = 'Fully Packed';
                                                    } else {
                                                        $statusClass = 'status-partial';
                                                        $statusText = 'Partially Packed';
                                                    }
                                                    $isFullyPacked = ($packedCount === $totalItems && $totalItems > 0);
                                                @endphp
                                                <tr class="packing-row" data-order-id="{{ $order->id }}">
                                                    <td><input type="checkbox" class="ecom-order-checkbox" data-order-id="{{ $order->id }}" data-so-number="{{ $order->so_number }}" style="cursor: pointer;" {{ !$isFullyPacked ? 'disabled' : '' }}></td>
                                                    <td><strong>{{ $order->so_number }}</strong></td>
                                                    <td>{{ $order->customer->customer_name ?? 'N/A' }}</td>
                                                    <td>{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('M d, Y') : 'N/A' }}</td>
                                                    <td>{{ $totalItems }}</td>
                                                    <td><strong>{{ $packedCount }}/{{ $totalItems }}</strong></td>
                                                    <td class="fw-bold">₱{{ number_format($order->items->sum('subtotal'), 2) }}</td>
                                                    <td><span class="status-badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <button type="button" class="btn btn-danger shadow view-order-btn"
                                                                    onclick="openPackingDetailsModal({{ $order->id }})"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    data-customer="{{ $order->customer->customer_name ?? 'N/A' }}"
                                                                    data-date="{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}"
                                                                    data-signed="{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('Y-m-d') : '' }}"
                                                                    title="View Details"
                                                                    style="background: #ff0000; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-eye" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-success shadow mark-packed-btn"
                                                                    onclick="markOrderAsPackedAction({{ $order->id }}, '{{ $order->so_number }}')"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    title="Mark as Packed"
                                                                    style="background: #28a745; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-check" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                        </div>
                                                @empty
                                                <tr>
                                                    <td colspan="9" class="text-center">No Lazada orders found</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Shopee Tab -->
                                <div class="tab-pane fade" id="shopee-ecom-content" role="tabpanel" aria-labelledby="shopee-ecom-tab">
                                    <div class="table-responsive">
                                        <table id="shopeePackingTable" class="display" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 30px;"><input type="checkbox" class="ecom-select-all-checkbox" data-platform="shopee" style="cursor: pointer;"></th>
                                                    <th>SO #</th>
                                                    <th>Customer</th>
                                                    <th>SI Signed</th>
                                                    <th>Total Items</th>
                                                    <th>Packed Items</th>
                                                    <th>Total Amount</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($ecomByPlatform['shopee'] as $order)
                                                @php
                                                    $packingData = json_decode($order->packing_data ?? '{}', true);
                                                    $packedCount = count(array_filter($packingData, function($item) { return ($item['status'] ?? null) === 'Packed'; }));
                                                    $totalItems = $order->items->count();
                                                    
                                                    if($packedCount === 0) {
                                                        $statusClass = 'status-ready';
                                                        $statusText = 'Ready for Packing';
                                                    } elseif($packedCount === $totalItems && $totalItems > 0) {
                                                        $statusClass = 'status-packed';
                                                        $statusText = 'Fully Packed';
                                                    } else {
                                                        $statusClass = 'status-partial';
                                                        $statusText = 'Partially Packed';
                                                    }
                                                    $isFullyPacked = ($packedCount === $totalItems && $totalItems > 0);
                                                @endphp
                                                <tr class="packing-row" data-order-id="{{ $order->id }}">
                                                    <td><input type="checkbox" class="ecom-order-checkbox" data-order-id="{{ $order->id }}" data-so-number="{{ $order->so_number }}" style="cursor: pointer;" {{ !$isFullyPacked ? 'disabled' : '' }}></td>
                                                    <td><strong>{{ $order->so_number }}</strong></td>
                                                    <td>{{ $order->customer->customer_name ?? 'N/A' }}</td>
                                                    <td>{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('M d, Y') : 'N/A' }}</td>
                                                    <td>{{ $totalItems }}</td>
                                                    <td><strong>{{ $packedCount }}/{{ $totalItems }}</strong></td>
                                                    <td class="fw-bold">₱{{ number_format($order->items->sum('subtotal'), 2) }}</td>
                                                    <td><span class="status-badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <button type="button" class="btn btn-danger shadow view-order-btn"
                                                                    onclick="openPackingDetailsModal({{ $order->id }})"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    data-customer="{{ $order->customer->customer_name ?? 'N/A' }}"
                                                                    data-date="{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}"
                                                                    data-signed="{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('Y-m-d') : '' }}"
                                                                    title="View Details"
                                                                    style="background: #ff0000; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-eye" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-success shadow mark-packed-btn"
                                                                    onclick="markOrderAsPackedAction({{ $order->id }}, '{{ $order->so_number }}')"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    title="Mark as Packed"
                                                                    style="background: #28a745; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-check" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="9" class="text-center">No Shopee orders found</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- TikTok Tab -->
                                <div class="tab-pane fade" id="tiktok-ecom-content" role="tabpanel" aria-labelledby="tiktok-ecom-tab">
                                    <div class="table-responsive">
                                        <table id="tiktokPackingTable" class="display" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 30px;"><input type="checkbox" class="ecom-select-all-checkbox" data-platform="tiktok" style="cursor: pointer;"></th>
                                                    <th>SO #</th>
                                                    <th>Customer</th>
                                                    <th>SI Signed</th>
                                                    <th>Total Items</th>
                                                    <th>Packed Items</th>
                                                    <th>Total Amount</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($ecomByPlatform['tiktok'] as $order)
                                                @php
                                                    $packingData = json_decode($order->packing_data ?? '{}', true);
                                                    $packedCount = count(array_filter($packingData, function($item) { return ($item['status'] ?? null) === 'Packed'; }));
                                                    $totalItems = $order->items->count();
                                                    
                                                    if($packedCount === 0) {
                                                        $statusClass = 'status-ready';
                                                        $statusText = 'Ready for Packing';
                                                    } elseif($packedCount === $totalItems && $totalItems > 0) {
                                                        $statusClass = 'status-packed';
                                                        $statusText = 'Fully Packed';
                                                    } else {
                                                        $statusClass = 'status-partial';
                                                        $statusText = 'Partially Packed';
                                                    }
                                                    $isFullyPacked = ($packedCount === $totalItems && $totalItems > 0);
                                                @endphp
                                                <tr class="packing-row" data-order-id="{{ $order->id }}">
                                                    <td><input type="checkbox" class="ecom-order-checkbox" data-order-id="{{ $order->id }}" data-so-number="{{ $order->so_number }}" style="cursor: pointer;" {{ !$isFullyPacked ? 'disabled' : '' }}></td>
                                                    <td><strong>{{ $order->so_number }}</strong></td>
                                                    <td>{{ $order->customer->customer_name ?? 'N/A' }}</td>
                                                    <td>{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('M d, Y') : 'N/A' }}</td>
                                                    <td>{{ $totalItems }}</td>
                                                    <td><strong>{{ $packedCount }}/{{ $totalItems }}</strong></td>
                                                    <td class="fw-bold">₱{{ number_format($order->items->sum('subtotal'), 2) }}</td>
                                                    <td><span class="status-badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <button type="button" class="btn btn-danger shadow view-order-btn"
                                                                    onclick="openPackingDetailsModal({{ $order->id }})"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    data-customer="{{ $order->customer->customer_name ?? 'N/A' }}"
                                                                    data-date="{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}"
                                                                    data-signed="{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('Y-m-d') : '' }}"
                                                                    title="View Details"
                                                                    style="background: #ff0000; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-eye" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-success shadow mark-packed-btn"
                                                                    onclick="markOrderAsPackedAction({{ $order->id }}, '{{ $order->so_number }}')"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    title="Mark as Packed"
                                                                    style="background: #28a745; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-check" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="9" class="text-center">No TikTok orders found</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- COB Tab -->
                                <div class="tab-pane fade" id="cob-ecom-content" role="tabpanel" aria-labelledby="cob-ecom-tab">
                                    <div class="table-responsive">
                                        <table id="cobPackingTable" class="display" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 30px;"><input type="checkbox" class="ecom-select-all-checkbox" data-platform="cob" style="cursor: pointer;"></th>
                                                    <th>SO #</th>
                                                    <th>Customer</th>
                                                    <th>SI Signed</th>
                                                    <th>Total Items</th>
                                                    <th>Packed Items</th>
                                                    <th>Total Amount</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($ecomByPlatform['cob'] as $order)
                                                @php
                                                    $packingData = json_decode($order->packing_data ?? '{}', true);
                                                    $packedCount = count(array_filter($packingData, function($item) { return ($item['status'] ?? null) === 'Packed'; }));
                                                    $totalItems = $order->items->count();
                                                    
                                                    if($packedCount === 0) {
                                                        $statusClass = 'status-ready';
                                                        $statusText = 'Ready for Packing';
                                                    } elseif($packedCount === $totalItems && $totalItems > 0) {
                                                        $statusClass = 'status-packed';
                                                        $statusText = 'Fully Packed';
                                                    } else {
                                                        $statusClass = 'status-partial';
                                                        $statusText = 'Partially Packed';
                                                    }
                                                    $isFullyPacked = ($packedCount === $totalItems && $totalItems > 0);
                                                @endphp
                                                <tr class="packing-row" data-order-id="{{ $order->id }}">
                                                    <td><input type="checkbox" class="ecom-order-checkbox" data-order-id="{{ $order->id }}" data-so-number="{{ $order->so_number }}" style="cursor: pointer;" {{ !$isFullyPacked ? 'disabled' : '' }}></td>
                                                    <td><strong>{{ $order->so_number }}</strong></td>
                                                    <td>{{ $order->customer->customer_name ?? 'N/A' }}</td>
                                                    <td>{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('M d, Y') : 'N/A' }}</td>
                                                    <td>{{ $totalItems }}</td>
                                                    <td><strong>{{ $packedCount }}/{{ $totalItems }}</strong></td>
                                                    <td class="fw-bold">₱{{ number_format($order->items->sum('subtotal'), 2) }}</td>
                                                    <td><span class="status-badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <button type="button" class="btn btn-danger shadow view-order-btn"
                                                                    onclick="openPackingDetailsModal({{ $order->id }})"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    data-customer="{{ $order->customer->customer_name ?? 'N/A' }}"
                                                                    data-date="{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}"
                                                                    data-signed="{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('Y-m-d') : '' }}"
                                                                    title="View Details"
                                                                    style="background: #ff0000; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-eye" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-success shadow mark-packed-btn"
                                                                    onclick="markOrderAsPackedAction({{ $order->id }}, '{{ $order->so_number }}')"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    title="Mark as Packed"
                                                                    style="background: #28a745; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-check" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="9" class="text-center">No COB orders found</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ready for Pickup Tab -->
                        <div class="tab-pane fade" id="ready-pickup-content" role="tabpanel" aria-labelledby="ready-pickup-tab">
                            <!-- Platform Sub-tabs -->
                            <ul class="nav nav-tabs mb-3" id="readyPickupPlatformTabs" role="tablist" style="border-bottom: 2px solid #dee2e6;">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="ready-all-tab" data-bs-toggle="tab" data-bs-target="#ready-all-content" type="button" role="tab" aria-controls="ready-all-content" aria-selected="true">
                                        <i class="fas fa-boxes me-2"></i>All Orders <span class="badge bg-secondary ms-2">{{ $readyForPickupOrders->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="ready-shopee-tab" data-bs-toggle="tab" data-bs-target="#ready-shopee-content" type="button" role="tab" aria-controls="ready-shopee-content" aria-selected="false">
                                        <i class="las la-shopping-bag me-2" style="color: #ee4d2d;"></i>Shopee <span class="badge bg-danger ms-2">{{ $readyByPlatform['shopee']->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="ready-tiktok-tab" data-bs-toggle="tab" data-bs-target="#ready-tiktok-content" type="button" role="tab" aria-controls="ready-tiktok-content" aria-selected="false">
                                        <i class="las la-music me-2"></i>TikTok <span class="badge bg-dark ms-2">{{ $readyByPlatform['tiktok']->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="ready-lazada-tab" data-bs-toggle="tab" data-bs-target="#ready-lazada-content" type="button" role="tab" aria-controls="ready-lazada-content" aria-selected="false">
                                        <i class="las la-shopping-bag me-2" style="color: #0f146d;"></i>Lazada <span class="badge bg-primary ms-2">{{ $readyByPlatform['lazada']->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="ready-cob-tab" data-bs-toggle="tab" data-bs-target="#ready-cob-content" type="button" role="tab" aria-controls="ready-cob-content" aria-selected="false">
                                        <i class="las la-building me-2" style="color: #6f42c1;"></i>COB <span class="badge ms-2" style="background-color: #6f42c1; color: #fff;">{{ $readyByPlatform['cob']->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="ready-completed-tab" data-bs-toggle="tab" data-bs-target="#ready-completed-content" type="button" role="tab" aria-controls="ready-completed-content" aria-selected="false">
                                        <i class="fas fa-check-double me-2" style="color: #17a2b8;"></i>Completed Drop-off <span class="badge ms-2" style="background-color: #17a2b8; color: #fff;">{{ count($completedDropoffOrders) }}</span>
                                    </button>
                                </li>
                            </ul>

                            <!-- Platform Sub-tabs Content -->
                            <div class="tab-content" id="readyPickupPlatformTabContent">
                                <!-- All Orders Tab -->
                                <div class="tab-pane fade show active" id="ready-all-content" role="tabpanel" aria-labelledby="ready-all-tab">
                                    <div class="table-responsive">
                                        <table id="readyForPickupTableAll" class="display" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 30px;"><input type="checkbox" class="ready-select-all-table-checkbox" style="cursor: pointer;"></th>
                                                    <th>SO #</th>
                                                    <th>Customer</th>
                                                    <th>SI Signed</th>
                                                    <th>Total Items</th>
                                                    <th>Packed Items</th>
                                                    <th>Total Amount</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($readyForPickupOrders as $order)
                                                @php
                                                    $packingData = json_decode($order->packing_data ?? '{}', true);
                                                    $packedCount = count(array_filter($packingData, function($item) { return ($item['status'] ?? null) === 'Packed'; }));
                                                    $totalItems = $order->items->count();
                                                    $statusClass = 'status-packed';
                                                    $statusText = 'Ready for Pickup/Drop-off';
                                                @endphp
                                                <tr>
                                                    <td><input type="checkbox" class="ready-order-checkbox" data-order-id="{{ $order->id }}" data-so-number="{{ $order->so_number }}" style="cursor: pointer;"></td>
                                                    <td><strong>{{ $order->so_number }}</strong></td>
                                                    <td>{{ $order->customer->customer_name ?? 'N/A' }}</td>
                                                    <td>{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('M d, Y') : 'N/A' }}</td>
                                                    <td>{{ $totalItems }}</td>
                                                    <td><strong>{{ $packedCount }}/{{ $totalItems }}</strong></td>
                                                    <td class="fw-bold">₱{{ number_format($order->items->sum('subtotal'), 2) }}</td>
                                                    <td><span class="status-badge {{ $statusClass }}" style="background-color: #d4edda; color: #155724;">{{ $statusText }}</span></td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <button type="button" class="btn btn-danger shadow view-order-btn"
                                                                    onclick="openPackingDetailsModal({{ $order->id }})"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    data-customer="{{ $order->customer->customer_name ?? 'N/A' }}"
                                                                    data-date="{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}"
                                                                    data-signed="{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('Y-m-d') : '' }}"
                                                                    title="View Details"
                                                                    style="background: #ff0000; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-eye" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-success shadow mark-gathered-btn"
                                                                    onclick="markOrderAsGatheredAction({{ $order->id }}, '{{ $order->so_number }}')"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    title="Mark as Gathered (Ready for Delivery)"
                                                                    style="background: #007bff; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-box-open" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="9" class="text-center" style="padding: 2rem;">
                                                        <p style="color: #999;">No orders ready for pickup yet</p>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Shopee Tab -->
                                <div class="tab-pane fade" id="ready-shopee-content" role="tabpanel" aria-labelledby="ready-shopee-tab">
                                    <div class="table-responsive">
                                        <table id="readyForPickupTableShopee" class="display" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 30px;"><input type="checkbox" class="ready-select-all-table-checkbox" style="cursor: pointer;"></th>
                                                    <th>SO #</th>
                                                    <th>Customer</th>
                                                    <th>SI Signed</th>
                                                    <th>Total Items</th>
                                                    <th>Packed Items</th>
                                                    <th>Total Amount</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($readyByPlatform['shopee'] as $order)
                                                @php
                                                    $packingData = json_decode($order->packing_data ?? '{}', true);
                                                    $packedCount = count(array_filter($packingData, function($item) { return ($item['status'] ?? null) === 'Packed'; }));
                                                    $totalItems = $order->items->count();
                                                    $statusClass = 'status-packed';
                                                    $statusText = 'Ready for Pickup/Drop-off';
                                                @endphp
                                                <tr>
                                                    <td><input type="checkbox" class="ready-order-checkbox" data-order-id="{{ $order->id }}" data-so-number="{{ $order->so_number }}" style="cursor: pointer;"></td>
                                                    <td><strong>{{ $order->so_number }}</strong></td>
                                                    <td>{{ $order->customer->customer_name ?? 'Shopee Customer' }}</td>
                                                    <td>{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('M d, Y') : 'N/A' }}</td>
                                                    <td>{{ $totalItems }}</td>
                                                    <td><strong>{{ $packedCount }}/{{ $totalItems }}</strong></td>
                                                    <td class="fw-bold">₱{{ number_format($order->items->sum('subtotal'), 2) }}</td>
                                                    <td><span class="status-badge {{ $statusClass }}" style="background-color: #d4edda; color: #155724;">{{ $statusText }}</span></td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <button type="button" class="btn btn-danger shadow view-order-btn"
                                                                    onclick="openPackingDetailsModal({{ $order->id }})"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    data-customer="{{ $order->customer->customer_name ?? 'N/A' }}"
                                                                    data-date="{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}"
                                                                    data-signed="{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('Y-m-d') : '' }}"
                                                                    title="View Details"
                                                                    style="background: #ff0000; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-eye" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-success shadow mark-gathered-btn"
                                                                    onclick="markOrderAsGatheredAction({{ $order->id }}, '{{ $order->so_number }}')"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    title="Mark as Gathered (Ready for Delivery)"
                                                                    style="background: #007bff; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-box-open" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="9" class="text-center" style="padding: 2rem;">
                                                        <p style="color: #999;">No Shopee orders ready for pickup</p>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- TikTok Tab -->
                                <div class="tab-pane fade" id="ready-tiktok-content" role="tabpanel" aria-labelledby="ready-tiktok-tab">
                                    <div class="table-responsive">
                                        <table id="readyForPickupTableTiktok" class="display" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 30px;"><input type="checkbox" class="ready-select-all-table-checkbox" style="cursor: pointer;"></th>
                                                    <th>SO #</th>
                                                    <th>Customer</th>
                                                    <th>SI Signed</th>
                                                    <th>Total Items</th>
                                                    <th>Packed Items</th>
                                                    <th>Total Amount</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($readyByPlatform['tiktok'] as $order)
                                                @php
                                                    $packingData = json_decode($order->packing_data ?? '{}', true);
                                                    $packedCount = count(array_filter($packingData, function($item) { return ($item['status'] ?? null) === 'Packed'; }));
                                                    $totalItems = $order->items->count();
                                                    $statusClass = 'status-packed';
                                                    $statusText = 'Ready for Pickup/Drop-off';
                                                @endphp
                                                <tr>
                                                    <td><input type="checkbox" class="ready-order-checkbox" data-order-id="{{ $order->id }}" data-so-number="{{ $order->so_number }}" style="cursor: pointer;"></td>
                                                    <td><strong>{{ $order->so_number }}</strong></td>
                                                    <td>{{ $order->customer->customer_name ?? 'TikTok Customer' }}</td>
                                                    <td>{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('M d, Y') : 'N/A' }}</td>
                                                    <td>{{ $totalItems }}</td>
                                                    <td><strong>{{ $packedCount }}/{{ $totalItems }}</strong></td>
                                                    <td class="fw-bold">₱{{ number_format($order->items->sum('subtotal'), 2) }}</td>
                                                    <td><span class="status-badge {{ $statusClass }}" style="background-color: #d4edda; color: #155724;">{{ $statusText }}</span></td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <button type="button" class="btn btn-danger shadow view-order-btn"
                                                                    onclick="openPackingDetailsModal({{ $order->id }})"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    data-customer="{{ $order->customer->customer_name ?? 'N/A' }}"
                                                                    data-date="{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}"
                                                                    data-signed="{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('Y-m-d') : '' }}"
                                                                    title="View Details"
                                                                    style="background: #ff0000; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-eye" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-success shadow mark-gathered-btn"
                                                                    onclick="markOrderAsGatheredAction({{ $order->id }}, '{{ $order->so_number }}')"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    title="Mark as Gathered (Ready for Delivery)"
                                                                    style="background: #007bff; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-box-open" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="9" class="text-center" style="padding: 2rem;">
                                                        <p style="color: #999;">No TikTok orders ready for pickup</p>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Lazada Tab -->
                                <div class="tab-pane fade" id="ready-lazada-content" role="tabpanel" aria-labelledby="ready-lazada-tab">
                                    <div class="table-responsive">
                                        <table id="readyForPickupTableLazada" class="display" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 30px;"><input type="checkbox" class="ready-select-all-table-checkbox" style="cursor: pointer;"></th>
                                                    <th>SO #</th>
                                                    <th>Customer</th>
                                                    <th>SI Signed</th>
                                                    <th>Total Items</th>
                                                    <th>Packed Items</th>
                                                    <th>Total Amount</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($readyByPlatform['lazada'] as $order)
                                                @php
                                                    $packingData = json_decode($order->packing_data ?? '{}', true);
                                                    $packedCount = count(array_filter($packingData, function($item) { return ($item['status'] ?? null) === 'Packed'; }));
                                                    $totalItems = $order->items->count();
                                                    $statusClass = 'status-packed';
                                                    $statusText = 'Ready for Pickup/Drop-off';
                                                @endphp
                                                <tr>
                                                    <td><input type="checkbox" class="ready-order-checkbox" data-order-id="{{ $order->id }}" data-so-number="{{ $order->so_number }}" style="cursor: pointer;"></td>
                                                    <td><strong>{{ $order->so_number }}</strong></td>
                                                    <td>{{ $order->customer->customer_name ?? 'Lazada Customer' }}</td>
                                                    <td>{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('M d, Y') : 'N/A' }}</td>
                                                    <td>{{ $totalItems }}</td>
                                                    <td><strong>{{ $packedCount }}/{{ $totalItems }}</strong></td>
                                                    <td class="fw-bold">₱{{ number_format($order->items->sum('subtotal'), 2) }}</td>
                                                    <td><span class="status-badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <button type="button" class="btn btn-danger shadow view-order-btn"
                                                                    onclick="openPackingDetailsModal({{ $order->id }})"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    data-customer="{{ $order->customer->customer_name ?? 'N/A' }}"
                                                                    data-date="{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}"
                                                                    data-signed="{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('Y-m-d') : '' }}"
                                                                    title="View Details"
                                                                    style="background: #ff0000; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-eye" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-primary shadow mark-gathered-btn"
                                                                    onclick="markOrderAsGatheredAction({{ $order->id }}, '{{ $order->so_number }}')"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    title="Mark as Gathered (Ready for Delivery)"
                                                                    style="background: #007bff; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-box-open" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="9" class="text-center" style="padding: 2rem;">
                                                        <p style="color: #999;">No Lazada orders ready for pickup</p>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- COB Tab -->
                                <div class="tab-pane fade" id="ready-cob-content" role="tabpanel" aria-labelledby="ready-cob-tab">
                                    <div class="table-responsive">
                                        <table id="readyForPickupTableCob" class="display" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 30px;"><input type="checkbox" class="ready-select-all-table-checkbox" style="cursor: pointer;"></th>
                                                    <th>SO #</th>
                                                    <th>Customer</th>
                                                    <th>SI Signed</th>
                                                    <th>Total Items</th>
                                                    <th>Packed Items</th>
                                                    <th>Total Amount</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($readyByPlatform['cob'] as $order)
                                                @php
                                                    $packingData = json_decode($order->packing_data ?? '{}', true);
                                                    $packedCount = count(array_filter($packingData, function($item) { return ($item['status'] ?? null) === 'Packed'; }));
                                                    $totalItems = $order->items->count();
                                                    $statusClass = 'status-packed';
                                                    $statusText = 'Ready for Pickup/Drop-off';
                                                @endphp
                                                <tr>
                                                    <td><input type="checkbox" class="ready-order-checkbox" data-order-id="{{ $order->id }}" data-so-number="{{ $order->so_number }}" style="cursor: pointer;"></td>
                                                    <td><strong>{{ $order->so_number }}</strong></td>
                                                    <td>{{ $order->customer->customer_name ?? 'COB Customer' }}</td>
                                                    <td>{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('M d, Y') : 'N/A' }}</td>
                                                    <td>{{ $totalItems }}</td>
                                                    <td><strong>{{ $packedCount }}/{{ $totalItems }}</strong></td>
                                                    <td class="fw-bold">₱{{ number_format($order->items->sum('subtotal'), 2) }}</td>
                                                    <td><span class="status-badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <button type="button" class="btn btn-danger shadow view-order-btn"
                                                                    onclick="openPackingDetailsModal({{ $order->id }})"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    data-customer="{{ $order->customer->customer_name ?? 'N/A' }}"
                                                                    data-date="{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}"
                                                                    data-signed="{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('Y-m-d') : '' }}"
                                                                    title="View Details"
                                                                    style="background: #ff0000; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-eye" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-primary shadow mark-gathered-btn"
                                                                    onclick="markOrderAsGatheredAction({{ $order->id }}, '{{ $order->so_number }}')"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-so-number="{{ $order->so_number }}"
                                                                    title="Mark as Gathered (Ready for Delivery)"
                                                                    style="background: #007bff; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-box-open" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="9" class="text-center" style="padding: 2rem;">
                                                        <p style="color: #999;">No COB orders ready for pickup</p>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                </div>

                                <!-- Completed Drop-off Sub-tab Content -->
                                <div class="tab-pane fade" id="ready-completed-content" role="tabpanel" aria-labelledby="ready-completed-tab">
                                    <div class="table-responsive">
                                        <table id="completedDropoffAllTable" class="display" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th>SO #</th>
                                                    <th>Platform</th>
                                                    <th>Customer</th>
                                                    <th>Total Items</th>
                                                    <th>Total Amount</th>
                                                    <th>Gathered By</th>
                                                    <th>Gathered At</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($completedDropoffOrders as $order)
                                                @php
                                                    $packingData = json_decode($order->packing_data ?? '{}', true);
                                                    $totalItems = $order->items->count();
                                                    $gatheredBy = $packingData['gathered_by'] ?? 'N/A';
                                                    $gatheredAt = isset($packingData['gathered_at']) ? \Carbon\Carbon::parse($packingData['gathered_at'])->format('M d, Y h:i A') : 'N/A';
                                                    $platformLabel = ucfirst($order->ecom_platform ?? 'N/A');
                                                    $platformClass = 'platform-' . strtolower($order->ecom_platform ?? 'default');
                                                @endphp
                                                <tr>
                                                    <td><strong>{{ $order->so_number }}</strong></td>
                                                    <td><span class="platform-badge {{ $platformClass }}">{{ $platformLabel }}</span></td>
                                                    <td>{{ $order->customer->customer_name ?? 'N/A' }}</td>
                                                    <td>{{ $totalItems }}</td>
                                                    <td class="fw-bold">₱{{ number_format($order->items->sum('subtotal'), 2) }}</td>
                                                    <td>{{ $gatheredBy }}</td>
                                                    <td>{{ $gatheredAt }}</td>
                                                    <td><span class="badge" style="background-color: #17a2b8; color: #fff;">Completed</span></td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger shadow view-order-btn"
                                                                onclick="openPackingDetailsModal({{ $order->id }})"
                                                                data-order-id="{{ $order->id }}"
                                                                data-so-number="{{ $order->so_number }}"
                                                                data-customer="{{ $order->customer->customer_name ?? 'N/A' }}"
                                                                data-date="{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}"
                                                                data-signed="{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('Y-m-d') : '' }}"
                                                                title="View Details"
                                                                style="background: #ff0000; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                            <i class="fas fa-eye" style="font-size: 0.9rem; pointer-events: none;"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="9" class="text-center" style="padding: 2rem;">
                                                        <p style="color: #999;">No completed drop-off orders</p>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Complimentary Tab -->
                        <div class="tab-pane fade" id="complimentary-content" role="tabpanel" aria-labelledby="complimentary-tab">
                            <div class="table-responsive">
                                <table id="complimentaryPackingTable" class="display" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>SO Number</th>
                                            <th>Recipient / Customer</th>
                                            <th>Date</th>
                                            <th>Total Qty</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($complimentaryPackingOrders as $order)
                                        @php
                                            $totalQty = $order->items->sum('quantity');
                                            $pData = json_decode($order->packing_data ?? '{}', true) ?: [];
                                            $pStatus = $pData['status'] ?? 'ready';
                                        @endphp
                                        <tr>
                                            <td><strong>{{ $order->so_number }}</strong></td>
                                            <td>{{ $order->customer->customer_name ?? 'Recipient' }}</td>
                                            <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                            <td>{{ $totalQty }} pcs</td>
                                            <td>
                                                <span class="badge" style="background-color: #6f42c1; color: #fff;">Complimentary (Ready to Pack)</span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-success shadow mark-packed-btn"
                                                        onclick="markOrderAsPackedAction({{ $order->id }}, '{{ $order->so_number }}')"
                                                        title="Mark as Packed (Send to Delivery Scheduling)"
                                                        style="background: #28a745; border: none; padding: 0.4rem 0.8rem; height: 36px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 500;">
                                                    <i class="fas fa-check-circle me-1" style="font-size: 0.9rem;"></i> Mark as Packed
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center" style="padding: 2rem;">
                                                <p style="color: #999;">No complimentary orders ready for packing.</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
    <!-- Floating Sticky Bulk Action Bar for Ready for Pickup -->
    <div id="readyBulkFloatingBar" class="ready-bulk-floating-bar hidden">
        <div class="d-flex align-items-center gap-2">
            <span class="badge rounded-pill px-3 py-2 fs-13" style="background:#007bff!important; color:#fff;">
                <span id="readyFloatingSelectedCount">0</span> selected
            </span>
            <span class="fw-medium text-white d-none d-sm-inline">order(s) selected</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3" onclick="clearReadySelections()">
                <i class="fas fa-times me-1"></i> Deselect
            </button>
            <button type="button" class="btn btn-sm btn-success rounded-pill px-4 fw-bold shadow-sm" id="btnMarkSelectedReadyDelivery" onclick="markSelectedReadyForDelivery()" style="background:#28a745; border-color:#28a745; font-size:0.875rem;">
                <i class="fas fa-truck me-1"></i> Mark Selected as Ready for Delivery
            </button>
        </div>
    </div>

    <!-- Order Detail Modal -->
    <div id="orderDetailModal" class="modal-backdrop-packing" style="display: none;">
        <div class="modal-content-packing">
            <div class="modal-header-packing">
                <h3 id="modalTitle" style="margin: 0; color: #000;">Packing Details</h3>
                <button type="button" class="modal-close-btn" id="closeDetailBtn" onclick="closePackingDetailsModal()" style="cursor: pointer; z-index: 10000; position: relative;">&times;</button>
            </div>
            <div class="modal-body-packing">
                <div class="order-info-section">
            <div class="order-info-box">
                <h5>Order Information</h5>
                <div class="form-group">
                    <label>Sales Order Number:</label>
                    <input type="text" id="detailSONumber" readonly>
                </div>
                <div class="form-group">
                    <label>Order Date:</label>
                    <input type="text" id="detailOrderDate" readonly>
                </div>
                <div class="form-group">
                    <label>Company:</label>
                    <input type="text" id="detailCustomerName" readonly>
                </div>
                <div class="form-group">
                    <label>Customer Name:</label>
                    <input type="text" id="detailRepresentative" readonly>
                </div>
                <div class="form-group">
                    <label>Contact:</label>
                    <input type="text" id="detailContact" readonly>
                </div>
                <div class="form-group">
                    <label>Remarks / Special Instructions:</label>
                    <div class="d-flex flex-column gap-2">
                        <textarea id="detailRemarks" class="form-control" style="background:#fff; font-weight:600;" placeholder="Enter remarks or special instructions..." rows="2"></textarea>
                        <button type="button" class="btn btn-sm btn-primary align-self-start mt-1" onclick="savePackingRemarksOnly()" style="background:#0d6efd; border:none; border-radius:6px; font-weight:600; padding: 0.4rem 1rem;">
                            <i class="fas fa-save me-1"></i>Save Remarks
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label>SI Signed Date:</label>
                    <input type="text" id="siSignedDate" readonly>
                </div>
                <div class="form-group">
                    <label>Packing Status:</label>
                    <select id="packingStatus">
                        <option value="not_started">Not Started</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="ready_for_pickup">Ready for Pickup</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Number of Boxes:</label>
                    <input type="number" id="packingBoxesCount" placeholder="Enter number of boxes" min="0">
                </div>
            </div>
            <div class="order-info-box" id="attachmentsSection" style="display: none;">
                <h5>Attachments - Packing Photos</h5>
                <div class="form-group">
                    <label>Upload Photo 1:</label>
                    <div style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <input type="file" id="packingPhoto1" accept="image/*" style="flex: 1; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                        <button type="button" id="cameraPhoto1Btn" class="btn" style="background: #007bff; color: white; padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer;">
                            <i class="fas fa-camera"></i> Camera
                        </button>
                    </div>
                    <div id="photo1Preview" style="display: none; margin-top: 0.5rem;">
                        <img id="photo1Img" src="" alt="Photo 1" style="max-width: 100%; max-height: 200px; border-radius: 4px; border: 1px solid #ddd;">
                    </div>
                </div>

                <div class="form-group">
                    <label>Upload Photo 2:</label>
                    <div style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <input type="file" id="packingPhoto2" accept="image/*" style="flex: 1; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                        <button type="button" id="cameraPhoto2Btn" class="btn" style="background: #007bff; color: white; padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer;">
                            <i class="fas fa-camera"></i> Camera
                        </button>
                    </div>
                    <div id="photo2Preview" style="display: none; margin-top: 0.5rem;">
                        <img id="photo2Img" src="" alt="Photo 2" style="max-width: 100%; max-height: 200px; border-radius: 4px; border: 1px solid #ddd;">
                    </div>
                </div>
            </div>
        </div>

                <!-- Packing Items Table -->
                <div id="barcodeScanMessage" class="barcode-scan-message visually-hidden" aria-live="polite">Ready to scan</div>

                <h5 style="margin-bottom: 1rem; margin-top: 1.5rem; font-weight: 600;">Items to Pack</h5>
                <div class="table-wrapper-packing">
                    <table class="packing-table">
                        <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Product</th>
                    <th style="width: 120px;">Qty to Pack</th>
                    <th style="width: 120px;">Unit Price</th>
                    <th style="width: 120px;">Subtotal</th>
                    <th style="width: 100px;">Packed Qty</th>
                    <th style="width: 120px;">Status</th>
                    <th style="width: 150px;">Notes</th>
                    <th style="width: 120px;">Packed Date</th>
                </tr>
            </thead>
            <tbody id="packingTableBody">
                <!-- Filled by JS -->
            </tbody>
        </table>
                </div>

        <!-- Summary Section -->
        <div class="order-info-section" style="margin-top: 1.5rem;">
            <div class="order-info-box">
                <h5>Packing Summary</h5>
                <div class="form-group">
                    <label>Total Items:</label>
                    <input type="text" id="totalItems" value="0" readonly>
                </div>
                <div class="form-group">
                    <label>Items Packed:</label>
                    <input type="text" id="itemsPacked" value="0" readonly>
                </div>
                <div class="form-group">
                    <label>Packing Progress:</label>
                    <div class="progress" style="height: 25px;">
                        <div id="packingProgressBar" class="progress-bar bg-warning" role="progressbar" style="width: 0%">
                            <span id="packingPercent">0%</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="order-info-box">
                <h5>Actions</h5>
                <div class="form-group">
                    <button type="button" class="btn btn-success" style="width: 100%; margin-bottom: 0.5rem; background: #ffc107; color: #000; border: none; padding: 0.75rem 2rem; border-radius: 6px; cursor: pointer; font-weight: 600;" id="savePackingBtn" onclick="savePackingData()">
                        <i class="las la-save"></i> Save Packing
                    </button>
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-outline-primary" style="width: 100%; margin-bottom: 0.5rem; border: 1px solid #0d6efd; color: #0d6efd; background: #fff; padding: 0.75rem 2rem; border-radius: 6px; cursor: pointer; font-weight: 600;" onclick="openPackingShippingLabel()">
                        <i class="fas fa-tag me-1"></i> Print / View Shipping Label
                    </button>
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-primary-custom close-modal-btn" style="width: 100%; cursor: pointer;" id="closeDetailsActionBtn" onclick="closePackingDetailsModal()">
                        <i class="las la-times"></i> Close Details
                    </button>
                </div>
            </div>
        </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        /* Modal Styles */
        .modal-backdrop-packing {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .modal-content-packing {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 95vw;
            max-height: 90vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header-packing {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #e9ecef;
            background: #f8f9fa;
        }

        .modal-close-btn {
            background: none;
            border: none;
            font-size: 1.8rem;
            cursor: pointer;
            color: #999;
            padding: 0;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close-btn:hover {
            color: #ff0000;
        }

        .modal-body-packing {
            padding: 2rem;
            overflow-y: auto;
            flex: 1;
        }

        .table-wrapper-packing {
            overflow-x: auto;
            margin-bottom: 1rem;
        }

        .order-info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .order-info-box {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid #ff0000;
        }

        .order-info-box h5 {
            color: #333;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #555;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.95rem;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #ff0000;
            box-shadow: 0 0 0 3px rgba(255,0,0,0.1);
        }

        .form-group input:readonly {
            background: #e9ecef;
            cursor: not-allowed;
        }

        .packing-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
            background: #fff;
        }

        .packing-table th {
            padding: 0.75rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.85rem;
            border: 1px solid #ddd;
            text-transform: uppercase;
        }

        .packing-table td {
            padding: 0.5rem;
            border: 1px solid #ddd;
        }

        .packing-table input[type="number"],
        .packing-table input[type="text"],
        .packing-table input[type="date"],
        .packing-table select {
            width: 100%;
            border: none;
            padding: 0.5rem;
            background: transparent;
            font-size: 0.9rem;
        }

        .packing-table input:focus,
        .packing-table select:focus {
            outline: 2px solid #ffc107;
            outline-offset: -2px;
            background: #fff;
        }

        .barcode-scan-message {
            color: #555;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .visually-hidden {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }

        .barcode-scan-message.success {
            color: #155724;
        }

        .barcode-scan-message.error {
            color: #b00020;
        }

        .packing-table tr.item-packed {
            background: #e8f5e9;
        }

        .packing-table tr.item-not-packed {
            background: #ffe5e5;
        }

        .packing-table tr.item-scanned {
            animation: scannedPulse 0.7s ease-out;
        }

        @keyframes scannedPulse {
            0% { box-shadow: inset 0 0 0 3px rgba(40, 167, 69, 0.8); }
            100% { box-shadow: inset 0 0 0 0 rgba(40, 167, 69, 0); }
        }

        .btn-primary-custom {
            background: #ff0000;
            color: #fff;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary-custom:hover {
            background: #ff3333;
            box-shadow: 0 4px 12px rgba(255,0,0,0.2);
        }

        .btn-secondary-custom {
            background: #6c757d;
            color: #fff;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }

        .btn-secondary-custom:hover {
            background: #5a6268;
        }

        .progress {
            background-color: #e9ecef;
            border-radius: 4px;
        }

        @media print {
            .sidebar, .header, .alert, .card-header { display: none !important; }
            .card { border: none !important; box-shadow: none !important; }
            .modal-backdrop-packing { background: rgba(0, 0, 0, 0) !important; z-index: 1 !important; }
            .modal-content-packing { max-width: 100% !important; box-shadow: none !important; animation: none !important; }
            body { background: #fff !important; }
        }

        .btn-xs {
            padding: 0.35rem 0.6rem !important;
            font-size: 0.8rem !important;
        }

        .btn-xs i {
            font-size: 1rem !important;
        }

        .gap-2 {
            gap: 0.5rem;
        }

        @media (max-width: 1200px) {
            .modal-content-packing {
                max-width: 98vw;
                max-height: 95vh;
            }
            .order-info-section {
                grid-template-columns: 1fr;
            }
            .packing-table {
                font-size: 0.85rem;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        let currentOrderId = null;
        let currentOrderItems = [];
        let barcodeScanTimer = null;
        let scannerBuffer = '';
        
        // Check if we have a preload order from picklist
        const preloadOrderId = {{ $preloadOrderId ? $preloadOrderId : 'null' }};
        console.log('Packing Management - Preload Order ID:', preloadOrderId);

        window.openPackingDetailsModal = function(orderId) {
            console.log('Opening details for order ID:', orderId);
            if (!orderId) return;
            currentOrderId = orderId;
            loadPackingOrder(orderId);
        };

        window.markOrderAsPackedAction = function(orderId, soNumber) {
            if (!orderId) return;
            if (confirm(`Mark ${soNumber} as packed and send directly to Delivery Scheduling?`)) {
                fetch('/production/logistic/packing/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        order_id: orderId,
                        packing_status: 'completed',
                        boxes_count: 1,
                        items: []
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`${soNumber} marked as packed and moved directly to Delivery Scheduling!`);
                        window.location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Failed to mark as packed'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error marking order as packed: ' + error.message);
                });
            }
        };

        window.markOrderAsGatheredAction = function(orderId, soNumber) {
            if (!orderId) return;
            if (confirm(`Mark ${soNumber} as gathered?`)) {
                markOrderAsGathered(orderId, soNumber);
            }
        };

        window.setReadyForPickupSingle = function(orderId, soNumber) {
            if (!orderId) return;
            if (confirm(`Set ${soNumber} as ready for pickup/drop-off?`)) {
                fetch('{{ route("production.logistic.packing.set-ready-for-pickup") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ order_ids: [orderId] })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while setting order as ready for pickup');
                });
            }
        };

        window.markSelectedReadyForDelivery = function() {
            const selectedCheckboxes = document.querySelectorAll('.ready-order-checkbox:checked');
            const orderIds = Array.from(selectedCheckboxes).map(cb => parseInt(cb.dataset.orderId));

            if (orderIds.length === 0) {
                alert('Please select at least one order using the checkboxes.');
                return;
            }

            if (confirm(`Mark ${orderIds.length} selected e-commerce order(s) as ready for delivery / gathered?`)) {
                fetch('{{ route("production.logistic.packing.mark-as-gathered") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ order_ids: orderIds })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Failed to process selected orders'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error processing orders: ' + error.message);
                });
            }
        };

        window.clearReadySelections = function() {
            $('.ready-order-checkbox, .ready-select-all-table-checkbox').prop('checked', false);
            updateReadySelectedCount();
        };

        // Checkbox sync event handlers
        $(document).on('change', '.ready-select-all-table-checkbox', function() {
            const table = $(this).closest('table');
            table.find('.ready-order-checkbox').prop('checked', this.checked);
            updateReadySelectedCount();
        });

        $(document).on('change', '.ready-order-checkbox', function() {
            updateReadySelectedCount();
        });

        function updateReadySelectedCount() {
            const count = $('.ready-order-checkbox:checked').length;
            const floatingCountEl = document.getElementById('readyFloatingSelectedCount');
            if (floatingCountEl) {
                floatingCountEl.textContent = count;
            }

            const floatingBar = document.getElementById('readyBulkFloatingBar');
            if (floatingBar) {
                if (count > 0) {
                    floatingBar.classList.remove('hidden');
                } else {
                    floatingBar.classList.add('hidden');
                }
            }
        }

        // Initialize DataTable and Event Listeners
        $(document).ready(function() {
            $('#packingTable').DataTable({
                order: [],
                pageLength: 25,
                responsive: true
            });

            // Initialize E-Com Packing Tables
            $('#lazadaPackingTable').DataTable({
                order: [],
                pageLength: 25,
                responsive: true
            });

            $('#shopeePackingTable').DataTable({
                order: [],
                pageLength: 25,
                responsive: true
            });

            $('#tiktokPackingTable').DataTable({
                order: [],
                pageLength: 25,
                responsive: true
            });

            $('#readyForPickupTableAll, #readyForPickupTableShopee, #readyForPickupTableTiktok, #readyForPickupTableLazada').DataTable({
                order: [],
                pageLength: 25,
                responsive: true
            });

            $('#complimentaryPackingTable').DataTable({
                order: [],
                pageLength: 25,
                responsive: true
            });

            // If preloadOrderId is set, auto-load that order
            if (preloadOrderId) {
                console.log('Auto-loading preload order:', preloadOrderId);
                currentOrderId = preloadOrderId;
                loadPackingOrder(preloadOrderId);
            }

            // Mark as Packed Button Click using event delegation
            $(document).on('click', '.mark-packed-btn', function(e) {
                e.stopPropagation();
                const btn = $(this).closest('.mark-packed-btn');
                const orderId = btn.attr('data-order-id') || btn.data('order-id');
                const soNumber = btn.attr('data-so-number') || btn.data('so-number');
                if (orderId && confirm(`Mark all items in ${soNumber} as packed?`)) {
                    markOrderAsPacked(orderId, soNumber);
                }
            });

            // Mark as Gathered Button Click
            $(document).on('click', '.mark-gathered-btn', function(e) {
                e.stopPropagation();
                const btn = $(this).closest('.mark-gathered-btn');
                const orderId = btn.attr('data-order-id') || btn.data('order-id');
                const soNumber = btn.attr('data-so-number') || btn.data('so-number');
                if (orderId && confirm(`Mark ${soNumber} as gathered?`)) {
                    markOrderAsGathered(orderId, soNumber);
                }
            });

            // View Order Button Click
            $(document).on('click', '.view-order-btn', function(e) {
                e.stopPropagation();
                const btn = $(this).closest('.view-order-btn');
                const orderId = btn.attr('data-order-id') || btn.data('order-id');
                if (orderId) {
                    currentOrderId = orderId;
                    console.log('Clicked view details for order:', currentOrderId);
                    loadPackingOrder(currentOrderId);
                }
            });

            // Close Detail Modal
            const closeDetailBtn = document.getElementById('closeDetailBtn');
            if (closeDetailBtn) {
                closeDetailBtn.addEventListener('click', function() {
                    closePackingDetailsModal();
                });
            }

            // Close modal button inside the modal
            document.querySelectorAll('.close-modal-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    closePackingDetailsModal();
                });
            });

            // Close modal when clicking outside
            const orderDetailModal = document.getElementById('orderDetailModal');
            if (orderDetailModal) {
                orderDetailModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closePackingDetailsModal();
                    }
                });
            }

            // Photo upload handlers
            const packingPhoto1 = document.getElementById('packingPhoto1');
            if (packingPhoto1) {
                packingPhoto1.addEventListener('change', function(e) {
                    handlePhotoUpload(e, 1);
                });
            }

            const packingPhoto2 = document.getElementById('packingPhoto2');
            if (packingPhoto2) {
                packingPhoto2.addEventListener('change', function(e) {
                    handlePhotoUpload(e, 2);
                });
            }

            // Camera buttons
            const cameraPhoto1Btn = document.getElementById('cameraPhoto1Btn');
            if (cameraPhoto1Btn) {
                cameraPhoto1Btn.addEventListener('click', function() {
                    openCamera(1);
                });
            }

            const cameraPhoto2Btn = document.getElementById('cameraPhoto2Btn');
            if (cameraPhoto2Btn) {
                cameraPhoto2Btn.addEventListener('click', function() {
                    openCamera(2);
                });
            }

            // Initialize bulk actions
            initializeBulkActions();
        });

        window.closePackingDetailsModal = function() {
            const modal = document.getElementById('orderDetailModal');
            if (modal) {
                modal.style.display = 'none';
            }
            currentOrderId = null;
            currentOrderItems = [];
            scannerBuffer = '';
        };

        function closePackingDetailsModal() {
            window.closePackingDetailsModal();
        }

        document.addEventListener('keydown', function(e) {
            if (document.getElementById('orderDetailModal').style.display === 'none' || !currentOrderItems.length) {
                return;
            }

            const tagName = (e.target.tagName || '').toLowerCase();
            const isEditable = tagName === 'input' || tagName === 'select' || tagName === 'textarea' || e.target.isContentEditable;

            if (isEditable) {
                return;
            }

            if (e.key === 'Enter') {
                if (scannerBuffer.trim()) {
                    e.preventDefault();
                    processPackingBarcode(scannerBuffer);
                    scannerBuffer = '';
                }
                return;
            }

            if (e.key.length !== 1) {
                return;
            }

            scannerBuffer += e.key;
            clearTimeout(barcodeScanTimer);
            barcodeScanTimer = setTimeout(() => {
                if (scannerBuffer.trim().length >= 6) {
                    processPackingBarcode(scannerBuffer);
                    scannerBuffer = '';
                }
            }, 250);
        });

        function markOrderAsPacked(orderId, soNumber) {
            // Fetch order data first
            fetch(`/production/logistic/packing/${orderId}/data`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP Error: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        console.error('Failed to load order:', data);
                        alert('Error: ' + (data.message || 'Could not load order data'));
                        return;
                    }

                    const order = data.order;
                    const packingItems = [];
                    const today = new Date().toISOString().split('T')[0];

                    // Create packing items with all qty marked as packed
                    order.items.forEach((item, index) => {
                        packingItems.push({
                            index: index,
                            packed_qty: item.quantity,
                            status: 'Packed',
                            notes: 'Auto-marked as packed',
                            packed_date: today,
                        });
                    });

                    const payload = {
                        order_id: orderId,
                        packing_status: 'completed',
                        boxes_count: null,
                        items: packingItems,
                    };

                    // Save packing data
                    fetch('/production/logistic/packing/save', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(`✓ All items in ${soNumber} marked as packed!`);
                            window.location.reload();
                        } else {
                            alert('Error: ' + (data.message || 'Failed to mark as packed'));
                        }
                    })
                    .catch(error => {
                        console.error('Error saving packing:', error);
                        alert('Error marking as packed: ' + error.message);
                    });
                })
                .catch(error => {
                    console.error('Error loading order:', error);
                    alert('Error loading order: ' + (error.message || 'Unknown error'));
                });
        }

        function markOrderAsGathered(orderId, soNumber) {
            fetch('/production/logistic/packing/mark-as-gathered', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    order_id: orderId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || `✓ ${soNumber} marked as gathered!`);
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to mark as gathered'));
                }
            })
            .catch(error => {
                console.error('Error marking as gathered:', error);
                alert('Error: ' + error.message);
            });
        }

        function loadPackingOrder(orderId, isCompleted = false) {
            // Fetch order data
            fetch(`/production/logistic/packing/${orderId}/data`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP Error: ${response.status} - ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        console.error('API returned error:', data);
                        alert('Error: ' + (data.message || 'Failed to load order data'));
                        return;
                    }

                    const order = data.order;
                    currentOrderItems = order.items;
                    
                    // Helper function to safely set input value
                    const setInputValue = (id, value) => {
                        const element = document.getElementById(id);
                        if (element) {
                            element.value = value || '';
                        } else {
                            console.warn(`Element with id "${id}" not found in DOM`);
                        }
                    };

                    // Populate order info
                    setInputValue('detailSONumber', order.so_number);
                    setInputValue('detailOrderDate', new Date(order.created_at).toLocaleDateString());
                    setInputValue('detailCustomerName', order.customer?.customer_name || 'N/A');
                    setInputValue('detailRepresentative', order.customer_representative || 'N/A');
                    setInputValue('detailContact', order.customer_contact || order.customer?.mobile || order.customer?.main_phone || 'N/A');
                    const packingData = order.packing_data ? (typeof order.packing_data === 'string' ? JSON.parse(order.packing_data) : order.packing_data) : {};
                    setInputValue('detailRemarks', order.remarks || (packingData.remarks || ''));
                    const signedDate = order.signed_at ? new Date(order.signed_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : (order.acct_approved_at ? new Date(order.acct_approved_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : 'Not Signed Yet');
                    setInputValue('siSignedDate', signedDate);
                    setInputValue('packingStatus', packingData.status || 'not_started');
                    setInputValue('packingBoxesCount', packingData.boxes_count || '');

                    // Show/hide "Ready for Pickup" status based on order type
                    const packingStatusSelect = document.getElementById('packingStatus');
                    if (packingStatusSelect) {
                        const readyPickupOption = packingStatusSelect.querySelector('option[value="ready_for_pickup"]');
                        if (readyPickupOption) {
                            if (order.type === 'ecom_direct') {
                                readyPickupOption.style.display = 'block';
                            } else {
                                readyPickupOption.style.display = 'none';
                            }
                        }
                    }

                    // Show/hide attachments section based on order type
                    const attachmentsSection = document.getElementById('attachmentsSection');
                    if (attachmentsSection) {
                        if (order.type === 'ecom_direct') {
                            attachmentsSection.style.display = 'block';
                        } else {
                            attachmentsSection.style.display = 'none';
                        }
                    }

                    // Display saved attachments if available
                    if (packingData.attachments) {
                        if (packingData.attachments.photo_1) {
                            const photo1Preview = document.getElementById('photo1Preview');
                            const photo1Img = document.getElementById('photo1Img');
                            if (photo1Preview && photo1Img) {
                                photo1Img.src = '/storage/' + packingData.attachments.photo_1;
                                photo1Preview.style.display = 'block';
                            }
                        }
                        if (packingData.attachments.photo_2) {
                            const photo2Preview = document.getElementById('photo2Preview');
                            const photo2Img = document.getElementById('photo2Img');
                            if (photo2Preview && photo2Img) {
                                photo2Img.src = '/storage/' + packingData.attachments.photo_2;
                                photo2Preview.style.display = 'block';
                            }
                        }
                    }

                    // Populate items table
                    let html = '';
                    let totalItems = 0;
                    let packedItems = 0;

                    order.items.forEach((item, index) => {
                        const itemKey = `item_${index}`;
                        const itemData = packingData[itemKey] || {};
                        
                        totalItems++;
                        if (itemData.status === 'Packed') packedItems++;

                        html += `
                            <tr id="packing_item_row_${index}">
                                <td>${index + 1}</td>
                                <td>${item.book?.name || 'N/A'}</td>
                                <td><input type="number" value="${item.quantity}" readonly style="width: 100%; border: none;"></td>
                                <td>₱${parseFloat(item.price).toFixed(2)}</td>
                                <td>₱${parseFloat(item.subtotal).toFixed(2)}</td>
                                <td><input type="number" id="packed_qty_${index}" min="0" max="${item.quantity}" value="${itemData.packed_qty || 0}" onchange="updatePackingCount()"></td>
                                <td>
                                    <select id="packed_status_${index}" onchange="handlePackingStatusChange()">
                                        <option value="Not Packed" ${itemData.status === 'Not Packed' ? 'selected' : ''}>Not Packed</option>
                                        <option value="In Progress" ${itemData.status === 'In Progress' ? 'selected' : ''}>In Progress</option>
                                        <option value="Packed" ${itemData.status === 'Packed' ? 'selected' : ''}>Packed</option>
                                    </select>
                                </td>
                                <td><input type="text" id="packed_notes_${index}" value="${itemData.notes || ''}" placeholder="Add notes..."></td>
                                <td><input type="date" id="packed_date_${index}" value="${itemData.packed_date || new Date().toISOString().split('T')[0]}"></td>
                            </tr>
                        `;
                    });

                    const packingTableBody = document.getElementById('packingTableBody');
                    if (packingTableBody) {
                        packingTableBody.innerHTML = html;
                    }

                    setInputValue('totalItems', totalItems);
                    updatePackingCount();

                    // Show detail modal
                    const orderDetailModal = document.getElementById('orderDetailModal');
                    if (orderDetailModal) {
                        orderDetailModal.style.display = 'flex';
                    }
                    
                    const modalTitle = document.getElementById('modalTitle');
                    if (modalTitle) {
                        modalTitle.textContent = `Packing Details - ${order.so_number}`;
                    }

                    scannerBuffer = '';
                    setBarcodeScanMessage('Ready to scan', 'neutral');
                    refreshPackingRowColors();
                    
                    // Disable inputs if completed
                    if (isCompleted) {
                        const packingStatusEl = document.getElementById('packingStatus');
                        const packingBoxesCountEl = document.getElementById('packingBoxesCount');
                        
                        if (packingStatusEl) packingStatusEl.disabled = true;
                        if (packingBoxesCountEl) packingBoxesCountEl.disabled = true;
                        
                        for (let i = 0; i < totalItems; i++) {
                            const qtyEl = document.getElementById(`packed_qty_${i}`);
                            const statusEl = document.getElementById(`packed_status_${i}`);
                            const notesEl = document.getElementById(`packed_notes_${i}`);
                            const dateEl = document.getElementById(`packed_date_${i}`);
                            
                            if (qtyEl) qtyEl.disabled = true;
                            if (statusEl) statusEl.disabled = true;
                            if (notesEl) notesEl.disabled = true;
                            if (dateEl) dateEl.disabled = true;
                        }
                        
                        const saveBtn = document.getElementById('savePackingBtn');
                        if (saveBtn) saveBtn.style.display = 'none';
                    } else {
                        const packingStatusEl = document.getElementById('packingStatus');
                        const packingBoxesCountEl = document.getElementById('packingBoxesCount');
                        
                        if (packingStatusEl) packingStatusEl.disabled = false;
                        if (packingBoxesCountEl) packingBoxesCountEl.disabled = false;
                        
                        for (let i = 0; i < totalItems; i++) {
                            const qtyEl = document.getElementById(`packed_qty_${i}`);
                            const statusEl = document.getElementById(`packed_status_${i}`);
                            const notesEl = document.getElementById(`packed_notes_${i}`);
                            const dateEl = document.getElementById(`packed_date_${i}`);
                            
                            if (qtyEl) qtyEl.disabled = false;
                            if (statusEl) statusEl.disabled = false;
                            if (notesEl) notesEl.disabled = false;
                            if (dateEl) dateEl.disabled = false;
                        }
                        
                        const saveBtn = document.getElementById('savePackingBtn');
                        if (saveBtn) saveBtn.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error loading order:', error);
                    alert('Error loading order data: ' + (error.message || 'Unknown error. Please check browser console and server logs.'));
                });
        }

        function updatePackingCount() {
            const totalItems = parseInt(document.getElementById('totalItems').value) || 0;
            let packedCount = 0;

            for (let i = 0; i < totalItems; i++) {
                const status = document.getElementById(`packed_status_${i}`).value;
                if (status === 'Packed') {
                    packedCount++;
                }
            }

            document.getElementById('itemsPacked').value = packedCount;
            const percent = totalItems > 0 ? Math.round((packedCount / totalItems) * 100) : 0;
            document.getElementById('packingProgressBar').style.width = percent + '%';
            document.getElementById('packingPercent').textContent = percent + '%';
            document.getElementById('packingStatus').value = packedCount === totalItems && totalItems > 0
                ? 'completed'
                : (packedCount > 0 ? 'in_progress' : 'not_started');
            refreshPackingRowColors();
        }

        function handlePackingStatusChange() {
            updatePackingCount();
        }

        function normalizeBarcode(value) {
            return (value || '').toString().trim().toLowerCase();
        }

        function getItemBarcodes(item) {
            const book = item.book || {};
            return [
                book.barcode,
                book.nbs_barcode,
                book.sku,
                book.item_code,
                item.isbn,
            ].map(normalizeBarcode).filter(Boolean);
        }

        function processPackingBarcode(rawBarcode) {
            clearTimeout(barcodeScanTimer);
            const barcode = normalizeBarcode(rawBarcode);

            if (!barcode || !currentOrderItems.length) {
                return;
            }

            const matchedIndex = currentOrderItems.findIndex(item => getItemBarcodes(item).includes(barcode));

            if (matchedIndex === -1) {
                setBarcodeScanMessage(`Barcode not found in this order: ${rawBarcode.trim()}`, 'error');
                return;
            }

            const item = currentOrderItems[matchedIndex];
            const qtyInput = document.getElementById(`packed_qty_${matchedIndex}`);
            const statusSelect = document.getElementById(`packed_status_${matchedIndex}`);
            const notesInput = document.getElementById(`packed_notes_${matchedIndex}`);
            const dateInput = document.getElementById(`packed_date_${matchedIndex}`);
            const row = document.getElementById(`packing_item_row_${matchedIndex}`);

            qtyInput.value = item.quantity;
            statusSelect.value = 'Packed';
            dateInput.value = new Date().toISOString().split('T')[0];
            if (!notesInput.value.trim()) {
                notesInput.value = 'Scanned by barcode';
            }

            updatePackingCount();
            row.classList.remove('item-scanned');
            void row.offsetWidth;
            row.classList.add('item-scanned');
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setBarcodeScanMessage(`${item.book.name} marked as packed`, 'success');
        }

        function refreshPackingRowColors() {
            const totalItems = parseInt(document.getElementById('totalItems').value) || 0;

            for (let i = 0; i < totalItems; i++) {
                const row = document.getElementById(`packing_item_row_${i}`);
                const status = document.getElementById(`packed_status_${i}`)?.value;

                if (!row) continue;

                row.classList.toggle('item-packed', status === 'Packed');
                row.classList.toggle('item-not-packed', status !== 'Packed');
            }
        }

        function setBarcodeScanMessage(message, type) {
            const messageBox = document.getElementById('barcodeScanMessage');
            messageBox.textContent = message;
            messageBox.classList.remove('success', 'error');

            if (type === 'success' || type === 'error') {
                messageBox.classList.add(type);
            }
        }

        function savePackingData() {
            if (!currentOrderId) {
                alert('No order selected');
                return;
            }

            const totalItems = parseInt(document.getElementById('totalItems').value) || 0;
            const packingItems = [];

            for (let i = 0; i < totalItems; i++) {
                packingItems.push({
                    index: i,
                    packed_qty: parseInt(document.getElementById(`packed_qty_${i}`).value) || 0,
                    status: document.getElementById(`packed_status_${i}`).value,
                    notes: document.getElementById(`packed_notes_${i}`).value,
                    packed_date: document.getElementById(`packed_date_${i}`).value,
                });
            }

            // Get photo attachments
            const photo1Input = document.getElementById('packingPhoto1');
            const photo2Input = document.getElementById('packingPhoto2');

            // Function to convert file to base64
            const fileToBase64 = (file) => {
                return new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onload = () => resolve(reader.result);
                    reader.onerror = error => reject(error);
                    reader.readAsDataURL(file);
                });
            };

            // Process attachments
            Promise.all([
                photo1Input && photo1Input.files.length > 0 ? fileToBase64(photo1Input.files[0]) : Promise.resolve(null),
                photo2Input && photo2Input.files.length > 0 ? fileToBase64(photo2Input.files[0]) : Promise.resolve(null)
            ])
            .then(([photo1Base64, photo2Base64]) => {
                const payload = {
                    order_id: currentOrderId,
                    packing_status: document.getElementById('packingStatus').value,
                    boxes_count: document.getElementById('packingBoxesCount').value,
                    remarks: document.getElementById('detailRemarks') ? document.getElementById('detailRemarks').value : '',
                    items: packingItems,
                    attachments: {
                        photo_1: photo1Base64,
                        photo_2: photo2Base64
                    }
                };

                fetch('/production/logistic/packing/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(payload)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Packing data saved successfully');
                        window.location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Failed to save packing data'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error saving packing data: ' + error.message);
                });
            })
            .catch(error => {
                console.error('Error converting files:', error);
                alert('Error processing photo attachments: ' + error.message);
            });
        }

        // Bulk Action Handling
        let selectedOrderIds = new Set();

        function initializeBulkActions() {
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            const setReadyPickupBtn = document.getElementById('setReadyPickupBtn');
            const clearSelectionBtn = document.getElementById('clearSelectionBtn');

            // Select All functionality using event delegation
            $(document).on('change', '#selectAllCheckbox', function() {
                const isChecked = this.checked;
                $('.order-checkbox').each(function() {
                    if (!this.disabled) {
                        this.checked = isChecked;
                        const orderId = this.dataset.orderId;
                        if (isChecked) {
                            selectedOrderIds.add(orderId);
                        } else {
                            selectedOrderIds.delete(orderId);
                        }
                    }
                });
                updateBulkActionToolbar();
            });

            // Individual checkbox handling using event delegation
            $(document).on('change', '.order-checkbox', function() {
                const orderId = this.dataset.orderId;
                if (this.checked) {
                    selectedOrderIds.add(orderId);
                } else {
                    selectedOrderIds.delete(orderId);
                }
                updateSelectAllCheckbox();
                updateBulkActionToolbar();
            });

            // Set as Ready for Pickup button
            if (setReadyPickupBtn) {
                setReadyPickupBtn.addEventListener('click', function() {
                    if (selectedOrderIds.size === 0) {
                        alert('Please select at least one fully packed order');
                        return;
                    }
                    
                    if (confirm(`Mark ${selectedOrderIds.size} order(s) as packed?`)) {
                        setOrdersAsReadyForPickup();
                    }
                });
            }

            // Clear Selection button
            if (clearSelectionBtn) {
                clearSelectionBtn.addEventListener('click', function() {
                    selectedOrderIds.clear();
                    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
                    if (selectAllCheckbox) selectAllCheckbox.checked = false;
                    $('.order-checkbox').prop('checked', false);
                    updateBulkActionToolbar();
                });
            }
        }

        function updateBulkActionToolbar() {
            const toolbar = document.getElementById('bulkActionToolbar');
            const selectedCount = document.getElementById('selectedCount');
            
            selectedCount.textContent = selectedOrderIds.size;
            
            if (selectedOrderIds.size > 0) {
                toolbar.style.display = 'flex';
            } else {
                toolbar.style.display = 'none';
            }
        }

        function updateSelectAllCheckbox() {
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            if (!selectAllCheckbox) return;

            const orderCheckboxes = $('.order-checkbox:not(:disabled)');
            const checkedCheckboxes = $('.order-checkbox:not(:disabled):checked');

            if (orderCheckboxes.length === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            } else if (checkedCheckboxes.length === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            } else if (checkedCheckboxes.length === orderCheckboxes.length) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = true;
            } else {
                selectAllCheckbox.indeterminate = true;
            }
        }

        function setOrdersAsReadyForPickup() {
            const orderIds = Array.from(selectedOrderIds);
            
            fetch('{{ route("production.logistic.packing.set-ready-for-pickup") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ order_ids: orderIds })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while setting orders as ready for pickup');
            });
        }

        function handlePhotoUpload(e, photoNumber) {
            const file = e.target.files[0];
            if (!file) return;

            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('Please select a valid image file');
                return;
            }

            // Create FileReader to display preview
            const reader = new FileReader();
            reader.onload = function(event) {
                displayPhotoPreview(event.target.result, photoNumber);
            };
            reader.readAsDataURL(file);
        }

        function displayPhotoPreview(imageSrc, photoNumber) {
            const previewDiv = document.getElementById(`photo${photoNumber}Preview`);
            const previewImg = document.getElementById(`photo${photoNumber}Img`);
            
            previewImg.src = imageSrc;
            previewDiv.style.display = 'block';
        }

        function openCamera(photoNumber) {
            // Check if getUserMedia is available
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert('Camera access is not supported on this device or browser');
                return;
            }

            // Create a temporary video element for camera capture
            const videoId = `tempVideoCamera${photoNumber}`;
            const existingVideo = document.getElementById(videoId);
            if (existingVideo) {
                existingVideo.remove();
            }

            const video = document.createElement('video');
            video.id = videoId;
            video.autoplay = true;
            video.playsinline = true;
            video.style.cssText = 'display:none;';
            document.body.appendChild(video);

            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
                .then(stream => {
                    video.srcObject = stream;
                    
                    // Create a modal for camera capture
                    createCameraModal(video, stream, photoNumber);
                })
                .catch(error => {
                    console.error('Error accessing camera:', error);
                    alert('Unable to access camera: ' + error.message);
                    video.remove();
                });
        }

        function createCameraModal(video, stream, photoNumber) {
            const modal = document.createElement('div');
            modal.id = `cameraModal${photoNumber}`;
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.9);
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                z-index: 10000;
            `;

            const canvas = document.createElement('canvas');
            canvas.id = `cameraCanvas${photoNumber}`;
            canvas.style.cssText = 'display:none;';
            
            const videoContainer = document.createElement('div');
            videoContainer.style.cssText = `
                width: 90vw;
                max-width: 600px;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            `;

            const cameraVideo = document.createElement('video');
            cameraVideo.srcObject = stream;
            cameraVideo.autoplay = true;
            cameraVideo.playsinline = true;
            cameraVideo.style.cssText = `
                width: 100%;
                height: auto;
                display: block;
                transform: scaleX(-1);
            `;

            videoContainer.appendChild(cameraVideo);

            const buttonContainer = document.createElement('div');
            buttonContainer.style.cssText = `
                display: flex;
                gap: 1rem;
                justify-content: center;
                margin-top: 2rem;
            `;

            const captureBtn = document.createElement('button');
            captureBtn.textContent = '📷 Capture Photo';
            captureBtn.style.cssText = `
                background: #28a745;
                color: white;
                border: none;
                padding: 0.75rem 2rem;
                border-radius: 6px;
                cursor: pointer;
                font-weight: 600;
                font-size: 1rem;
            `;

            const cancelBtn = document.createElement('button');
            cancelBtn.textContent = '✕ Cancel';
            cancelBtn.style.cssText = `
                background: #6c757d;
                color: white;
                border: none;
                padding: 0.75rem 2rem;
                border-radius: 6px;
                cursor: pointer;
                font-weight: 600;
                font-size: 1rem;
            `;

            captureBtn.addEventListener('click', function() {
                capturePhoto(cameraVideo, canvas, photoNumber, modal, stream);
            });

            cancelBtn.addEventListener('click', function() {
                closeCameraModal(modal, stream);
            });

            buttonContainer.appendChild(captureBtn);
            buttonContainer.appendChild(cancelBtn);

            modal.appendChild(videoContainer);
            modal.appendChild(buttonContainer);
            modal.appendChild(canvas);

            document.body.appendChild(modal);

            // Handle escape key to close modal
            const escapeHandler = (e) => {
                if (e.key === 'Escape') {
                    closeCameraModal(modal, stream);
                    document.removeEventListener('keydown', escapeHandler);
                }
            };
            document.addEventListener('keydown', escapeHandler);
        }

        function capturePhoto(video, canvas, photoNumber, modal, stream) {
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            // Flip the image horizontally (mirror effect)
            context.scale(-1, 1);
            context.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);

            // Convert to blob and set to file input
            canvas.toBlob(blob => {
                const file = new File([blob], `packing_photo_${photoNumber}_${Date.now()}.jpg`, { type: 'image/jpeg' });
                
                // Set the file to the input
                const fileInput = document.getElementById(`packingPhoto${photoNumber}`);
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;

                // Trigger change event to show preview
                const event = new Event('change', { bubbles: true });
                fileInput.dispatchEvent(event);

                // Close modal and stop stream
                closeCameraModal(modal, stream);
            }, 'image/jpeg', 0.9);
        }

        function closeCameraModal(modal, stream) {
            // Stop all tracks in the stream
            stream.getTracks().forEach(track => track.stop());

            // Remove modal and video element
            modal.remove();
            const videoId = modal.querySelector('video') ? `tempVideoCamera${modal.id.replace('cameraModal', '')}` : null;
            if (videoId) {
                const tempVideo = document.getElementById(videoId);
                if (tempVideo) tempVideo.remove();
            }
        }

        function openPackingShippingLabel() {
            if (!currentOrderId) {
                alert('Please select an order first.');
                return;
            }
            window.open('/production/logistic/shipping-label/' + currentOrderId, '_blank');
        }

        function savePackingRemarksOnly() {
            if (!currentOrderId) {
                alert('No order selected');
                return;
            }
            const remarks = document.getElementById('detailRemarks').value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            fetch('/production/logistic/packing/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    order_id: currentOrderId,
                    remarks: remarks,
                    packing_status: document.getElementById('packingStatus')?.value || 'not_started'
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Packing remarks saved successfully!');
                } else {
                    alert('Error saving remarks: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(err => alert('Error saving remarks: ' + err.message));
        }
    </script>
    @endpush

</x-app-layout>

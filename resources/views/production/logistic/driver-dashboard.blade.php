<x-app-layout :title="'Driver Dashboard'" :sidebar="'production'">
@push('styles')
    <style>
        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
            white-space: nowrap;
        }
        .status-ready_for_delivery { background-color: #e0f2ff; color: #004085; }
        .status-in_transit { background-color: #fff3cd; color: #856404; }
        .status-completed { background-color: #d4edda; color: #155724; }
        
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            border-left: 5px solid;
            transition: transform 0.2s;
            height: 100%;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        }
        .stat-card.total { border-left-color: #17a2b8; }
        .stat-card.ongoing { border-left-color: #ffc107; }
        .stat-card.ready { border-left-color: #007bff; }
        
        .stat-card h3 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0 0 0.5rem 0;
            color: #333;
        }
        .stat-card p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .order-table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            font-size: 0.8rem;
            color: #495057;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
        }
    </style>
    @endpush

    <div class="container-fluid">
        <!-- Dashboard Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <div class="p-3 bg-primary-light rounded-circle text-primary">
                            <i class="las la-truck-moving fs-30"></i>
                        </div>
                    </div>
                    <div>
                        <h2 class="font-w600 mb-0">Driver Dashboard</h2>
                        <p class="mb-0 text-muted">Manage your assigned deliveries and fulfill orders</p>
                    </div>
                </div>
            </div>
        </div>

        @php
            $dateLabel = 'Today';
            if (request('start_date') && request('end_date')) {
                $dateLabel = \Carbon\Carbon::parse(request('start_date'))->format('M d, Y') . ' - ' . \Carbon\Carbon::parse(request('end_date'))->format('M d, Y');
            } elseif (request('start_date')) {
                $dateLabel = 'From ' . \Carbon\Carbon::parse(request('start_date'))->format('M d, Y');
            } elseif (request('end_date')) {
                $dateLabel = 'Until ' . \Carbon\Carbon::parse(request('end_date'))->format('M d, Y');
            }
            $totalAssignedCount = ($assignedDeliveries->count() ?? 0) + (isset($allPickupRequests) ? $allPickupRequests->where('status', '!=', 'completed')->count() : 0);
            $activePickupsCount = isset($allPickupRequests) ? $allPickupRequests->where('status', '!=', 'completed')->count() : 0;
        @endphp
        <!-- Stats Overview -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="stat-card total">
                    <h3>{{ $totalAssignedCount }}</h3>
                    <p>Total Assigned {{ $dateLabel }}</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card ongoing">
                    <h3>{{ $assignedDeliveries->where('status', 'in_transit')->count() }}</h3>
                    <p>Ongoing Deliveries</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card ready">
                    <h3>{{ $activePickupsCount }}</h3>
                    <p>Logistics Service Orders</p>
                </div>
            </div>
        </div>

        <!-- Assigned Logistics Service Orders (Pickup / Pull Out / Special Delivery Requests) -->
        @if(isset($allPickupRequests) && $allPickupRequests->count() > 0)
        <div class="row mb-4">
            <div class="col-xl-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div>
                                <h4 class="fs-18 mb-0 font-w600 text-black"><i class="las la-clipboard-list me-2 text-danger"></i>Logistics Service Orders (Pickup / Pull Out / Delivery)</h4>
                                <p class="text-muted small mb-0">Assigned pickup, pull out, and special delivery requests</p>
                            </div>
                            <span class="badge bg-danger fs-12 px-3 py-2">{{ $allPickupRequests->where('status', '!=', 'completed')->count() }} Pending/Active</span>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="table-responsive">
                            <table class="table order-table display mb-0" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>REQ #</th>
                                        <th>TYPE</th>
                                        <th>CLIENT / CONTACT</th>
                                        <th>ADDRESS</th>
                                        <th>REQUESTED DATE</th>
                                        <th>ITEMS / DETAILS</th>
                                        <th>STATUS</th>
                                        <th class="text-end">ACTIONS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allPickupRequests as $req)
                                    <tr>
                                        <td class="align-middle">
                                            <span class="text-black font-w600">REQ-{{ str_pad($req->id, 5, '0', STR_PAD_LEFT) }}</span>
                                            <div class="text-muted small">{{ $req->created_at ? $req->created_at->format('M d, Y') : '' }}</div>
                                        </td>
                                        <td class="align-middle">
                                            @if($req->type === 'pickup')
                                                <span class="badge bg-warning text-dark"><i class="las la-truck me-1"></i>Pickup</span>
                                            @elseif($req->type === 'pull_out')
                                                <span class="badge bg-danger"><i class="las la-undo me-1"></i>Pull Out</span>
                                            @else
                                                <span class="badge bg-primary"><i class="las la-shipping-fast me-1"></i>Delivery</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <div class="me-2 p-2 bg-light rounded-circle">
                                                    <i class="las la-building text-danger"></i>
                                                </div>
                                                <span class="text-black font-w600">{{ $req->client_name }}</span>
                                            </div>
                                        </td>
                                        <td class="align-middle" style="max-width: 250px;">
                                            <div class="text-truncate" title="{{ $req->address }}">
                                                <i class="las la-map-marker me-1 text-muted"></i>
                                                {{ $req->address }}
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            @if($req->requested_date)
                                                <span class="badge bg-info">{{ \Carbon\Carbon::parse($req->requested_date)->format('M d, Y') }}</span>
                                            @else
                                                <span class="text-muted small">Not set</span>
                                            @endif
                                        </td>
                                        <td class="align-middle" style="max-width: 200px;">
                                            <div class="text-truncate small text-dark fw-semibold" title="{{ $req->items_details }}">
                                                {{ $req->items_details }}
                                            </div>
                                            @if($req->remarks)
                                                <div class="text-muted small text-truncate" title="{{ $req->remarks }}"><i class="las la-comment me-1"></i>{{ $req->remarks }}</div>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            @if($req->status === 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($req->status === 'approved')
                                                <span class="badge bg-primary">Approved / Assigned</span>
                                            @elseif($req->status === 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @else
                                                <span class="badge bg-warning text-dark">{{ ucwords(str_replace('_', ' ', $req->status)) }}</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                @if($req->status !== 'completed')
                                                <form action="{{ route('production.logistic.pickup-requests.complete', $req->id) }}" method="POST" onsubmit="return confirm('Mark this logistics service order as completed?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success shadow btn-xs sharp" title="Mark Complete">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Today's Deliveries -->
        <div class="row mb-4">
            <div class="col-xl-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <h4 class="fs-18 mb-0 font-w600">Today's Deliveries</h4>
                            <span class="badge bg-primary">{{ $todayDeliveries->count() }} Active</span>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="table-responsive">
                            <table class="table order-table display mb-0" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>Ref #</th>
                                        <th>Customer</th>
                                        <th>Delivery Address</th>
                                        <th>Delivery Date</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($todayDeliveries as $order)
                                    <tr>
                                        <td class="align-middle">
                                            <span class="text-black font-w600">{{ $order->so_number }}</span>
                                            <div class="text-muted small">{{ $order->created_at->format('M d, Y') }}</div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <div class="me-2 p-2 bg-light rounded-circle">
                                                    <i class="las la-user text-primary"></i>
                                                </div>
                                                <span class="text-black">{{ $order->customer->customer_name ?? 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td class="align-middle" style="max-width: 250px;">
                                            <div class="text-truncate" title="{{ $order->shipping_address ?? $order->billing_address ?? 'N/A' }}">
                                                <i class="las la-map-marker me-1 text-muted"></i>
                                                {{ $order->shipping_address ?? $order->billing_address ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            @if($order->delivery_date)
                                                <span class="badge bg-info">{{ \Carbon\Carbon::parse($order->delivery_date)->format('M d, Y') }}</span>
                                            @else
                                                <span class="text-muted small">Not set</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <span class="status-badge status-{{ $order->status }}">
                                                {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                @if($order->transaction_type === 'COD' && $order->riderCollection)
                                                    <a href="{{ route('rider.collections.show', $order->riderCollection->id) }}" class="btn btn-primary shadow btn-xs sharp" title="Record Collection">
                                                        <i class="fas fa-file-alt"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('production.logistic.view-delivery-form', $order->id) }}" class="btn btn-primary shadow btn-xs sharp" title="View Form">
                                                        <i class="fas fa-file-alt"></i>
                                                    </a>
                                                @endif
                                                
                                                @if($order->status !== 'completed')
                                                @php
                                                    $canMarkComplete = true;
                                                    $completeReason = 'Mark Complete';
                                                    if ($order->type === 'paid') {
                                                        $canMarkComplete = true;
                                                    } elseif ($order->transaction_type === 'COD') {
                                                        $collection = \App\Models\RiderCollection::where('sales_order_id', $order->id)->first();
                                                        if (!$collection) {
                                                            $canMarkComplete = false;
                                                            $completeReason = 'No collection created';
                                                        } elseif ($collection->status !== 'verified') {
                                                            $canMarkComplete = false;
                                                            $completeReason = 'Collection not verified by accounting';
                                                        }
                                                    } else {
                                                        $canMarkComplete = true;
                                                    }
                                                @endphp
                                                <form action="{{ route('production.logistic.mark-as-delivered', $order->id) }}" method="POST" onsubmit="return confirm('Mark this delivery as completed?');">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="btn btn-{{ $canMarkComplete ? 'success' : 'secondary disabled' }} shadow btn-xs sharp" 
                                                            {{ !$canMarkComplete ? 'disabled' : '' }}
                                                            title="{{ $completeReason }}">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="las la-box-open fs-50 mb-3 d-block opacity-25"></i>
                                                No deliveries scheduled for today.
                                            </div>
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

        <!-- All Deliveries -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 w-100">
                            <h4 class="fs-18 mb-0 font-w600">All Assigned Deliveries</h4>
                            <form action="{{ route('production.logistic.driver-dashboard') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="text-muted small text-nowrap">Filter Delivery Date:</span>
                                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}" onchange="this.form.submit()" style="width: auto;">
                                <span class="text-muted small">to</span>
                                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}" onchange="this.form.submit()" style="width: auto;">
                                @if(request('start_date') || request('end_date'))
                                    <a href="{{ route('production.logistic.driver-dashboard') }}" class="btn btn-sm btn-outline-danger py-1 px-3">Clear</a>
                                @endif
                            </form>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="table-responsive">
                            <table class="table order-table display mb-0" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>Ref #</th>
                                        <th>Customer</th>
                                        <th>Delivery Address</th>
                                        <th>Delivery Date</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($allDeliveries as $order)
                                    <tr>
                                        <td class="align-middle">
                                            <span class="text-black font-w600">{{ $order->so_number }}</span>
                                            <div class="text-muted small">{{ $order->created_at->format('M d, Y') }}</div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <div class="me-2 p-2 bg-light rounded-circle">
                                                    <i class="las la-user text-primary"></i>
                                                </div>
                                                <span class="text-black">{{ $order->customer->customer_name ?? 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td class="align-middle" style="max-width: 250px;">
                                            <div class="text-truncate" title="{{ $order->shipping_address ?? $order->billing_address ?? 'N/A' }}">
                                                <i class="las la-map-marker me-1 text-muted"></i>
                                                {{ $order->shipping_address ?? $order->billing_address ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            @if($order->delivery_date)
                                                <span class="badge bg-info">{{ \Carbon\Carbon::parse($order->delivery_date)->format('M d, Y') }}</span>
                                            @else
                                                <span class="text-muted small">Not set</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <span class="status-badge status-{{ $order->status }}">
                                                {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                @if($order->transaction_type === 'COD' && $order->riderCollection)
                                                    <a href="{{ route('rider.collections.show', $order->riderCollection->id) }}" class="btn btn-primary shadow btn-xs sharp" title="Record Collection">
                                                        <i class="fas fa-file-alt"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('production.logistic.view-delivery-form', $order->id) }}" class="btn btn-primary shadow btn-xs sharp" title="View Form">
                                                        <i class="fas fa-file-alt"></i>
                                                    </a>
                                                @endif
                                                
                                                @if($order->status !== 'completed')
                                                @php
                                                    $canMarkComplete = true;
                                                    $completeReason = 'Mark Complete';
                                                    if ($order->type === 'paid') {
                                                        $canMarkComplete = true;
                                                    } elseif ($order->transaction_type === 'COD') {
                                                        $collection = \App\Models\RiderCollection::where('sales_order_id', $order->id)->first();
                                                        if (!$collection) {
                                                            $canMarkComplete = false;
                                                            $completeReason = 'No collection created';
                                                        } elseif ($collection->status !== 'verified') {
                                                            $canMarkComplete = false;
                                                            $completeReason = 'Collection not verified by accounting';
                                                        }
                                                    } else {
                                                        $canMarkComplete = true;
                                                    }
                                                @endphp
                                                <form action="{{ route('production.logistic.mark-as-delivered', $order->id) }}" method="POST" onsubmit="return confirm('Mark this delivery as completed?');">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="btn btn-{{ $canMarkComplete ? 'success' : 'secondary disabled' }} shadow btn-xs sharp" 
                                                            {{ !$canMarkComplete ? 'disabled' : '' }}
                                                            title="{{ $completeReason }}">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="las la-box-open fs-50 mb-3 d-block opacity-25"></i>
                                                No assigned deliveries found.
                                            </div>
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
        </div>
    </div>
</x-app-layout>

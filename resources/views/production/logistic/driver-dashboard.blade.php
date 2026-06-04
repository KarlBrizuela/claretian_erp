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

        <!-- Stats Overview -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="stat-card total">
                    <h3>{{ $assignedDeliveries->count() }}</h3>
                    <p>Total Assigned Today</p>
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
                    <h3>{{ $assignedDeliveries->where('status', 'ready_for_delivery')->count() }}</h3>
                    <p>Ready for Pickup</p>
                </div>
            </div>
        </div>

        <!-- Assigned Deliveries -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h4 class="fs-18 mb-0 font-w600">My Assigned Deliveries</h4>
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
                                    @forelse($assignedDeliveries as $order)
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
                                                    
                                                    // PAID orders can always mark complete
                                                    // COD orders need verified collection
                                                    if ($order->type === 'paid') {
                                                        // PAID order - allow
                                                        $canMarkComplete = true;
                                                    } elseif ($order->transaction_type === 'COD') {
                                                        // COD order - check collection
                                                        $collection = \App\Models\RiderCollection::where('sales_order_id', $order->id)->first();
                                                        if (!$collection) {
                                                            $canMarkComplete = false;
                                                            $completeReason = 'No collection created';
                                                        } elseif ($collection->status !== 'verified') {
                                                            $canMarkComplete = false;
                                                            $completeReason = 'Collection not verified by accounting';
                                                        }
                                                    } else {
                                                        // Other types - allow
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
                                        <td colspan="5" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="las la-box-open fs-50 mb-3 d-block opacity-25"></i>
                                                No assigned deliveries found for today.
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
</x-app-layout>

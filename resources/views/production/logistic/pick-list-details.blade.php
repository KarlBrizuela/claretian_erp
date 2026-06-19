<x-app-layout :title="'Pick List Details'" :sidebar="'production'">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card pick-list-form">
                    <div class="form-header">
                        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                            <h2 class="document-title">PICK LIST DETAILS</h2>
                            <a href="{{ route('production.logistic.pick-list-list') }}" class="btn btn-secondary" style="background: #6c757d; border: none; color: white;">
                                <i class="las la-arrow-left me-2"></i>Back to List
                            </a>
                        </div>
                    </div>

                    <!-- Pick List Details Section -->
                    <div class="order-info-section">
                        <div class="order-info-box">
                            <h5>Order Information</h5>
                            <div class="form-group">
                                <label>Sales Order Number:</label>
                                <input type="text" value="{{ $pickList->salesOrder->so_number ?? 'N/A' }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Order Date:</label>
                                <input type="text" value="{{ $pickList->salesOrder->created_at->format('M d, Y') }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Customer:</label>
                                <input type="text" value="{{ $pickList->salesOrder->customer->customer_name ?? 'Unknown' }}" readonly>
                            </div>
                        </div>
                        <div class="order-info-box">
                            <h5>Pick List Information</h5>
                            <div class="form-group">
                                <label>Pick List Number:</label>
                                <input type="text" value="{{ $pickList->pick_list_number }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Status:</label>
                                <div style="padding-top: 0.5rem;">
                                    @if($pickList->status === 'draft')
                                        <span class="badge bg-secondary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Draft</span>
                                    @elseif($pickList->status === 'in_progress')
                                        <span class="badge bg-info" style="padding: 0.5rem 1rem; font-size: 0.875rem;">In Progress</span>
                                    @elseif($pickList->status === 'completed')
                                        <span class="badge bg-success" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Completed</span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Prepared By:</label>
                                <input type="text" value="{{ $pickList->preparedByUser->name ?? 'System' }}" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Pick List Items Table -->
                    <h5 style="margin-bottom: 1rem; font-weight: 600; margin-top: 2rem;">Pick List Items</h5>
                    <table class="pick-list-table" style="width: 100%; border-collapse: collapse;">
                        <thead style="background: #cc0000; color: white;">
                            <tr>
                                <th style="width: 50px; padding: 0.75rem; border: 1px solid #ddd;">#</th>
                                <th style="padding: 0.75rem; border: 1px solid #ddd; text-align: left;">Product</th>
                                <th style="width: 120px; padding: 0.75rem; border: 1px solid #ddd; text-align: center;">Requested Qty</th>
                                <th style="width: 120px; padding: 0.75rem; border: 1px solid #ddd; text-align: right;">Unit Price</th>
                                <th style="width: 120px; padding: 0.75rem; border: 1px solid #ddd; text-align: right;">Subtotal</th>
                                <th style="width: 120px; padding: 0.75rem; border: 1px solid #ddd; text-align: center;">Picked Qty</th>
                                <th style="width: 120px; padding: 0.75rem; border: 1px solid #ddd; text-align: center;">Status</th>
                                <th style="width: 150px; padding: 0.75rem; border: 1px solid #ddd; text-align: left;">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pickList->pickListItems as $item)
                            <tr>
                                <td style="padding: 0.75rem; border: 1px solid #ddd; text-align: center;">{{ $loop->iteration }}</td>
                                <td style="padding: 0.75rem; border: 1px solid #ddd;">{{ $item->salesOrderItem->book->name ?? 'Unknown' }}</td>
                                <td style="padding: 0.75rem; border: 1px solid #ddd; text-align: center;">{{ $item->requested_qty }}</td>
                                <td style="padding: 0.75rem; border: 1px solid #ddd; text-align: right;">₱{{ number_format($item->salesOrderItem->price, 2) }}</td>
                                <td style="padding: 0.75rem; border: 1px solid #ddd; text-align: right;">₱{{ number_format($item->salesOrderItem->subtotal, 2) }}</td>
                                <td style="padding: 0.75rem; border: 1px solid #ddd; text-align: center;">{{ $item->picked_qty }}</td>
                                <td style="padding: 0.75rem; border: 1px solid #ddd; text-align: center;">
                                    @if($item->status === 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($item->status === 'picked')
                                        <span class="badge bg-success">Picked</span>
                                    @elseif($item->status === 'short')
                                        <span class="badge bg-danger">Short</span>
                                    @endif
                                </td>
                                <td style="padding: 0.75rem; border: 1px solid #ddd;">{{ $item->notes ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" style="padding: 1rem; text-align: center; color: #999;">No items in this pick list.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Summary Section -->
                    <div class="order-info-section" style="margin-top: 2rem;">
                        <div class="order-info-box">
                            <h5>Summary</h5>
                            <div class="form-group">
                                <label>Total Items:</label>
                                <input type="text" value="{{ $pickList->pickListItems->count() }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Total Requested Qty:</label>
                                <input type="text" value="{{ $pickList->pickListItems->sum('requested_qty') }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Total Picked Qty:</label>
                                <input type="text" value="{{ $pickList->pickListItems->sum('picked_qty') }}" readonly>
                            </div>
                        </div>
                        <div class="order-info-box">
                            <h5>Financial Summary</h5>
                            <div class="form-group">
                                <label>Total Amount:</label>
                                <input type="text" value="₱{{ number_format($pickList->pickListItems->sum('salesOrderItem.subtotal'), 2) }}" readonly style="font-weight: bold; font-size: 1.1rem;">
                            </div>
                            <div class="form-group">
                                <label>Date Created:</label>
                                <input type="text" value="{{ $pickList->created_at->format('M d, Y h:i A') }}" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                        <a href="{{ route('production.logistic.pick-list-management', ['pickListId' => $pickList->id]) }}" class="btn btn-warning" style="background: #ffc107; border: none; color: #000; padding: 0.75rem 2rem; border-radius: 6px; cursor: pointer; font-weight: 600;">
                            <i class="las la-edit me-2"></i>Edit Pick List
                        </a>
                        <form action="{{ route('production.logistic.mark-as-gathered', $pickList->salesOrder->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Mark {{ $pickList->pick_list_number }} as gathered and move to Delivery Scheduling?');">
                            @csrf
                            <button type="submit" class="btn btn-success" style="background: #28a745; border: none; color: white; padding: 0.75rem 2rem; border-radius: 6px; cursor: pointer; font-weight: 600;">
                                <i class="las la-check-circle me-2"></i>Mark as Gathered
                            </button>
                        </form>
                        <button type="button" class="btn btn-info" style="background: #17a2b8; border: none; color: white; padding: 0.75rem 2rem; border-radius: 6px; cursor: pointer; font-weight: 600;" onclick="window.print()">
                            <i class="las la-print me-2"></i>Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .pick-list-form {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .form-header {
            background: linear-gradient(135deg, #cc0000, #ff0000);
            color: white;
            padding: 1.5rem;
            border-radius: 8px 8px 0 0;
        }
        .document-title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .order-info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            padding: 1.5rem;
            border-top: 1px solid #dee2e6;
        }
        .order-info-box {
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 6px;
        }
        .order-info-box h5 {
            font-weight: 600;
            margin-bottom: 1rem;
            color: #333;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #555;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.95rem;
        }
        .pick-list-table {
            font-size: 0.9rem;
            width: 100%;
        }
        .pick-list-table th {
            font-weight: 600;
        }
        .pick-list-table td {
            border: 1px solid #ddd;
        }
    </style>
    @endpush

    @push('scripts')
   
    @endpush
</x-app-layout>

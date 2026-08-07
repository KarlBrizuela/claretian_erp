<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .order-form { background: #fff; border-radius: 8px; padding: 2rem; box-shadow: 0 0 20px rgba(0, 0, 0, 0.05); }
        .form-header { margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid #e0e0e0; }
        .form-header .company-info { display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; }
        .form-header .company-logo { width: 60px; height: 60px; background: #ff0000; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 2rem; font-weight: bold; flex-shrink: 0; }
        .form-header .company-details { flex: 1; }
        .form-header .company-name { font-size: 1.25rem; font-weight: 700; color: #333; margin-bottom: 0.25rem; text-transform: uppercase; }
        .form-header .document-title { text-align: center; font-size: 1.75rem; font-weight: 700; color: #333; margin-top: 1rem; letter-spacing: 1px; }
        .customer-section { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 1.5rem; }
        .customer-details, .order-details { background: #f8f9fa; padding: 1.5rem; border-radius: 6px; }
        .order-table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; }
        .order-table thead { background: #ff0000; color: #fff; }
        .order-table th, .order-table td { padding: 0.75rem; border: 1px solid #ddd; }
        .order-table tfoot { background: #f8f9fa; font-weight: 600; }
        @media print { .sidebar, .header, .form-actions, .btn { display: none !important; } .content-body { margin-left: 0 !important; } }
    </style>
    @endpush

    <div class="row">
        <div class="col-12">
            <div class="card order-form">
                <!-- Form Header -->
                <div class="form-header">
                    <div class="company-info">
                        <div class="company-logo">C</div>
                        <div class="company-details">
                            <div class="company-name">CLARETIAN COMMUNICATIONS FOUNDATION INC.</div>
                            <div class="company-address">8 Mayumi St., UP Village, Diliman, Quezon City</div>
                            <div class="company-contact">Tel. No.: 921-3984</div>
                        </div>
                    </div>
                    <div class="document-title">SALES ORDER <span class="text-danger">#{{ $order->so_number }}</span></div>
                    <div class="text-center text-uppercase fw-bold text-secondary">{{ str_replace('_', ' ', $order->type) }}</div>
                </div>

                <!-- Customer and Order Details -->
                <div class="customer-section">
                    <div class="customer-details">
                        <h5 class="text-black fw-bold">Customer Information</h5>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="fw-bold text-dark" style="width: 140px;">Company:</td>
                                <td class="fw-bold text-black">{{ $order->customer->customer_name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">Customer Name:</td>
                                <td class="fw-bold text-black">{{ $order->customer_representative ?: ($order->customer->customer_name ?? 'Unknown Customer') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">Account No:</td>
                                <td class="text-black">{{ $order->customer->account_number ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">Address:</td>
                                <td class="text-black">{{ $order->customer->shipping_address ?? $order->customer->billing_address ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="order-details">
                        <h5 class="text-black fw-bold">Order Information</h5>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="fw-bold text-dark">Order Date:</td>
                                <td class="text-black">{{ $order->created_at->format('F d, Y') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">Status:</td>
                                <td><span class="badge bg-warning text-white">{{ strtoupper(str_replace('_', ' ', $order->status)) }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">Prepared By:</td>
                                <td class="text-black">{{ $order->preparedBy->name ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Attachments Section -->
                <div class="card mb-4 border-0 bg-light">
                    <div class="card-body p-3">
                        <h6 class="fw-bold text-dark mb-3"><i class="las la-paperclip me-1 text-primary"></i> Attachments</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted mb-1">Proof of Payment:</label>
                                <div>
                                    @if($order->proof_of_payment)
                                        <a href="{{ asset('storage/' . $order->proof_of_payment) }}" target="_blank" class="btn btn-sm btn-outline-success fw-bold">
                                            <i class="las la-receipt me-1"></i> View Proof of Payment
                                        </a>
                                    @else
                                        <span class="badge bg-warning text-dark p-2"><i class="fas fa-exclamation-triangle me-1"></i> No Proof of Payment attached</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted mb-1">Pick List Attachment:</label>
                                <div>
                                    @if($order->pick_list_attachment)
                                        <a href="{{ asset('storage/' . $order->pick_list_attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="las la-file-alt me-1"></i> View Pick List
                                        </a>
                                    @else
                                        <span class="text-muted small"><i class="las la-info-circle me-1"></i> None</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted mb-1">Other Attachments:</label>
                                <div>
                                    @if($order->attachment)
                                        <a href="{{ asset('storage/' . $order->attachment) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="las la-paperclip me-1"></i> View Document
                                        </a>
                                    @elseif($order->order_list_attachment)
                                        <a href="{{ asset('storage/' . $order->order_list_attachment) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="las la-list me-1"></i> View Order List
                                        </a>
                                    @else
                                        <span class="text-muted small"><i class="las la-info-circle me-1"></i> None</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <table class="order-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">QTY</th>
                            <th style="width: 100px;">UNIT</th>
                            <th>DESCRIPTION</th>
                            <th style="width: 130px;">UNIT PRICE</th>
                            <th style="width: 110px;">DISCOUNT</th>
                            <th style="width: 150px;">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        @php 
                            $itemName = $item->item_name ?? ($item->product?->name ?? ($item->book?->name ?? ($item->bundle?->name ?? null))); 
                        @endphp
                        @if($itemName)
                        <tr>
                            <td class="text-center">{{ (float)$item->quantity }}</td>
                            <td class="text-center text-uppercase">{{ $item->product?->unit ?? $item->book?->unit ?? 'pcs' }}</td>
                            <td>
                                <div class="fw-bold d-flex align-items-center gap-1 flex-wrap">
                                    <span>{{ $itemName }}</span>
                                    @if($item->bundle_id || $item->bundle)
                                        <span class="badge bg-purple text-white ms-1" style="background-color: #6f42c1; font-size: 10px; padding: 3px 6px;">Bundle</span>
                                    @elseif($item->book_index_id || $item->bookIndex)
                                        <span class="badge bg-info text-white ms-1" style="font-size: 10px; padding: 3px 6px;">Index</span>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $item->product?->sku ?? $item->book?->sku ?? $item->bundle?->sku ?? '-' }}</small>
                            </td>
                            <td class="text-end">₱{{ number_format($item->price, 2) }}</td>
                            <td class="text-center">
                                @if(($item->discount_value ?? 0) > 0 || ($item->discount_amount ?? 0) > 0)
                                    @if(($item->discount_type ?? 'percentage') === 'percentage' && ($item->discount_value ?? 0) > 0)
                                        {{ (float)$item->discount_value }}%
                                    @elseif(($item->discount_value ?? 0) > 0)
                                        ₱{{ number_format($item->discount_value, 2) }}
                                    @else
                                        ₱{{ number_format($item->discount_amount, 2) }}
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-end fw-bold">₱{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                    <tfoot>
                        @php
                            $itemsToRender = $order->items;
                            $totalSalesAmount = (float) $order->total_amount;
                            $itemsSubtotal = $itemsToRender->sum(function($item) {
                                return $item->amount ?? ($item->subtotal > 0 ? $item->subtotal : ($item->quantity * $item->price));
                            });
                            $discountAmount = (float) ($order->discount_amount ?? 0);
                            $discountPercentage = $order->discount_percentage ?? 0;
                            $freightCharges = $order->freight_charges ?? 0;
                            $serviceFee = $order->freight_option === 'freight_collect' ? 50 : 0;
                        @endphp
                        <tr>
                            <td colspan="5" class="text-end text-uppercase"><strong>Items Subtotal:</strong></td>
                            <td class="text-end fw-bold">₱{{ number_format($itemsSubtotal, 2) }}</td>
                        </tr>
                        @if($discountAmount > 0)
                        <tr>
                            <td colspan="5" class="text-end text-uppercase">
                                <strong>
                                    Discount
                                    @if($discountPercentage > 0)
                                        ({{ (float)$discountPercentage }}%)
                                    @endif:
                                </strong>
                            </td>
                            <td class="text-end fw-bold text-danger">- ₱{{ number_format($discountAmount, 2) }}</td>
                        </tr>
                        @endif
                        @if($freightCharges > 0)
                        <tr>
                            <td colspan="5" class="text-end text-uppercase"><strong>Freight Charges:</strong></td>
                            <td class="text-end fw-bold">₱{{ number_format($freightCharges, 2) }}</td>
                        </tr>
                        @endif
                        @if($serviceFee > 0)
                        <tr>
                            <td colspan="5" class="text-end text-uppercase"><strong>Service Fee:</strong></td>
                            <td class="text-end fw-bold">₱{{ number_format($serviceFee, 2) }}</td>
                        </tr>
                        @endif
                        <tr style="background: #f8f9fa;">
                            <td colspan="5" class="text-end text-uppercase"><strong>Grand Total:</strong></td>
                            <td class="text-end fw-bold fs-5 text-primary">₱{{ number_format($totalSalesAmount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Actions -->
                <div class="d-flex justify-content-between align-items-center mt-4 form-actions">
                    <div>
                        <button type="button" class="btn btn-dark" onclick="window.history.back()">
                            <i class="las la-arrow-left me-2"></i>Back to Queue
                        </button>
                    </div>
                    <div class="d-flex gap-2">
                        <form action="{{ route('production.sales-order.reject', $order->id) }}" method="POST" id="rejectForm">
                            @csrf
                            <input type="hidden" name="remarks" id="rejectRemarks">
                            <button type="button" class="btn btn-outline-danger" onclick="confirmReject()">
                                <i class="las la-times-circle me-2"></i>Reject Order
                            </button>
                        </form>
                        <form action="{{ route('production.sales-order.approve', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="las la-check-circle me-2"></i>Approve Order
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function confirmReject() {
            if (typeof showAppModal === 'function') {
                showAppModal('Reject Sales Order', 'Please provide a reason for rejection:', {
                    type: 'input',
                    placeholder: 'Enter rejection remarks...',
                    confirmText: 'Confirm Reject',
                    confirmClass: 'btn-danger',
                    onConfirm: function(remarks) {
                        if (remarks) {
                            document.getElementById('rejectRemarks').value = remarks;
                            document.getElementById('rejectForm').submit();
                        } else {
                            alert('Rejection reason is required.');
                        }
                    }
                });
            } else {
                let remarks = prompt('Please provide a reason for rejection:');
                if (remarks) {
                    document.getElementById('rejectRemarks').value = remarks;
                    document.getElementById('rejectForm').submit();
                }
            }
        }
    </script>
    @endpush
</x-app-layout>

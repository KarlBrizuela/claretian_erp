<x-app-layout :title="$title" :sidebar="$sidebar">
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Review Freight Quotation</h5>
                    </div>

                    <div class="card-body">
                        <!-- Sales Order Header - Prominent Display -->
                        @if($quotation->sales_order_id && $quotation->salesOrder)
                            <div class="alert alert-info border-2 mb-4" style="background-color: #e3f2fd; border-color: #2196F3;">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h5 class="mb-2"><i class="bi bi-file-earmark-arrow-right me-2"></i>📦 Sales Order: <strong>{{ $quotation->salesOrder->so_number }}</strong></h5>
                                        <p class="mb-1"><strong>Customer:</strong> {{ $quotation->salesOrder->customer->customer_name ?? 'N/A' }} ({{ $quotation->salesOrder->customer->company_name ?? '' }})</p>
                                        <p class="mb-1"><strong>Items:</strong> {{ $quotation->salesOrder->items ? $quotation->salesOrder->items->sum('quantity') : 0 }} units | <strong>Total:</strong> ₱ {{ number_format($quotation->salesOrder->items ? $quotation->salesOrder->items->sum(function($item) { return $item->quantity * $item->price; }) : 0, 2) }}</p>
                                        <p class="mb-0 text-muted"><strong>Delivery Address:</strong> {{ $quotation->salesOrder->billing_address ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <a href="{{ route('marketing.sales-orders.show', $quotation->sales_order_id) }}" class="btn btn-sm btn-primary" target="_blank">
                                            <i class="bi bi-eye me-1"></i>View Full SO
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Quotation Header Info -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <small class="text-muted">Quote Number</small>
                                        <h6 class="mb-0">{{ $quotation->quote_number }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <small class="text-muted">Requested By</small>
                                        <h6 class="mb-0">{{ $quotation->createdBy->name ?? 'System' }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <small class="text-muted">Created Date</small>
                                        <h6 class="mb-0">{{ $quotation->created_at->format('M d, Y') }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <small class="text-muted">Status</small>
                                        <h6 class="mb-0">
                                            @if($quotation->workflow_status === 'draft')
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span class="badge bg-success">Approved</span>
                                            @endif
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Shipment Details -->
                        <h6 class="border-bottom pb-2 mb-3"><strong>Shipment Details</strong></h6>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Origin (Pick-up)</h6>
                                        <p class="mb-1"><strong>Contact:</strong> {{ $quotation->origin_contact }}</p>
                                        <p class="mb-1"><strong>Province:</strong> {{ $quotation->origin_province }}</p>
                                        <p class="mb-0"><strong>Address:</strong><br> {{ $quotation->origin_address }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Destination (Delivery)</h6>
                                        <p class="mb-1"><strong>Contact:</strong> {{ $quotation->destination_contact }}</p>
                                        <p class="mb-1"><strong>Province:</strong> {{ $quotation->destination_province }}</p>
                                        <p class="mb-0"><strong>Address:</strong><br> {{ $quotation->destination_address }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p><strong>Service Mode:</strong> {{ $quotation->service_mode }}</p>

                        <!-- Cargo Items -->
                        <h6 class="border-bottom pb-2 mb-3"><strong>Cargo Items</strong></h6>

                        @if($quotation->cargo_items)
                            <div class="table-responsive mb-4">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Quantity</th>
                                            <th>Package Type</th>
                                            <th>Dimensions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $cargoItems = is_string($quotation->cargo_items) ? json_decode($quotation->cargo_items, true) : $quotation->cargo_items;
                                        @endphp
                                        @foreach($cargoItems as $item)
                                            <tr>
                                                <td>{{ $item['qty'] }}</td>
                                                <td>{{ $item['package_type'] ?? '-' }}</td>
                                                <td>{{ $item['dimensions'] ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <!-- FREIGHT QUOTATION FORM SECTION - PROMINENT & VISIBLE -->
                        @if($quotation->workflow_status !== 'approved')
                            <div class="card border-success border-3 mb-4" style="background-color: #f0fdf4;">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="bi bi-calculator me-2"></i> Set Freight Charges & Boxes</h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-warning mb-3">
                                        <i class="bi bi-exclamation-triangle me-2"></i>
                                        <strong>Fill in the fields below to set the freight quotation for this shipment.</strong>
                                    </div>

                                    <form id="approveQuotationForm" method="POST" action="{{ route('production.logistic.approve-freight-quotation', $quotation->id) }}">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold"> Number of Boxes: <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control form-control-lg @error('boxes_count') is-invalid @enderror" 
                                                       name="boxes_count" min="1" value="{{ old('boxes_count') }}" 
                                                       required placeholder="e.g., 5">
                                                <small class="text-muted d-block mt-1">How many boxes will this shipment require?</small>
                                                @error('boxes_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold"> Estimated Freight Charge (₱): <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control form-control-lg @error('estimated_freight') is-invalid @enderror" 
                                                       name="estimated_freight" step="0.01" min="0.01" 
                                                       value="{{ old('estimated_freight') }}" required 
                                                       placeholder="e.g., 5000.00">
                                                <small class="text-muted d-block mt-1">Total freight cost for this shipment</small>
                                                @error('estimated_freight')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Logistics Notes:</label>
                                            <textarea class="form-control @error('logistics_notes') is-invalid @enderror" 
                                                      name="logistics_notes" rows="3" 
                                                      placeholder="e.g., 5 boxes, sea freight via Manila Port, delivery in 7-10 days...">{{ old('logistics_notes') }}</textarea>
                                            <small class="text-muted d-block mt-1">Special handling, delays, or other notes</small>
                                            @error('logistics_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>

                                        <hr class="my-4">

                                        <!-- Action Buttons -->
                                        <div class="d-flex gap-2 justify-content-between">
                                            <button type="button" class="btn btn-outline-danger btn-lg" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                                <i class="bi bi-x-circle me-2"></i>Reject Quotation
                                            </button>
                                            <button type="submit" class="btn btn-success btn-lg" style="min-width: 250px;">
                                                <i class="bi bi-check-circle me-2"></i>✓ Approve & Quote
                                            </button>
                                        </div>
                                    </form>

                                    <!-- Reject Modal -->
                                    <div class="modal fade" id="rejectModal" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">Reject Quotation</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST" action="{{ route('production.logistic.reject-freight-quotation', $quotation->id) }}">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <label class="form-label">Reason for Rejection:</label>
                                                        <textarea class="form-control" name="rejection_reason" rows="4" required 
                                                                  placeholder="Explain why this quotation is being rejected..."></textarea>
                                                        <small class="text-muted d-block mt-2">This will be sent back to marketing</small>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Reject</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Already Approved - Show Summary -->
                            <div class="card border-success border-3 mb-4" style="background-color: #f0fdf4;">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="bi bi-check-circle me-2"></i>✓ Freight Quotation Approved</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-body text-center">
                                                    <small class="text-muted d-block">📦 Boxes</small>
                                                    <h5 class="mb-0 fw-bold">{{ $quotation->boxes_count }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="card border border-danger border-2">
                                                <div class="card-body text-center">
                                                    <small class="text-muted d-block">💰 Total Freight Charge</small>
                                                    <h5 class="mb-0 fw-bold text-danger" style="font-size: 1.3rem;">₱ {{ number_format($quotation->estimated_freight, 2) }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if($quotation->logistics_notes)
                                        <div class="alert alert-info">
                                            <strong>📝 Logistics Notes:</strong><br>
                                            {{ $quotation->logistics_notes }}
                                        </div>
                                    @endif

                                    <p class="text-muted small mt-3">
                                        ✓ Approved by <strong>{{ $quotation->respondedBy->name ?? 'System' }}</strong> on <strong>{{ $quotation->responded_at->format('M d, Y \a\t g:i A') }}</strong>
                                    </p>
                                </div>
                            </div>
                        @endif

                        <hr>

                        <!-- Linked Sales Order -->
                        @if($quotation->sales_order_id)
                            <h6 class="border-bottom pb-2 mb-3"><strong>Linked Sales Order</strong></h6>
                            
                            <div class="card mb-4 border-success">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <p><strong>SO Number:</strong> {{ $quotation->salesOrder->so_number ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Customer:</strong> {{ $quotation->salesOrder->customer->customer_name ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Status:</strong> 
                                                <span class="badge bg-info">{{ $quotation->salesOrder->status ?? 'DRAFT' }}</span>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- SO Items Table -->
                                    @if($quotation->salesOrder->items && $quotation->salesOrder->items->count() > 0)
                                        <h6 class="mt-3 mb-2"><small>Order Items:</small></h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-2">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 60px;">QTY</th>
                                                        <th>PRODUCT</th>
                                                        <th style="width: 100px;">UNIT PRICE</th>
                                                        <th style="width: 100px;">AMOUNT</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $soSubtotal = 0; @endphp
                                                    @foreach($quotation->salesOrder->items as $item)
                                                        @php
                                                            $itemAmount = $item->quantity * $item->price;
                                                            $soSubtotal += $itemAmount;
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $item->quantity }}</td>
                                                            <td>{{ $item->product->name ?? 'N/A' }}</td>
                                                            <td class="text-end">₱ {{ number_format($item->price, 2) }}</td>
                                                            <td class="text-end fw-bold">₱ {{ number_format($itemAmount, 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr>
                                                        <td colspan="3" class="text-end"><strong>Order Subtotal:</strong></td>
                                                        <td class="text-end fw-bold">₱ {{ number_format($soSubtotal, 2) }}</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    @endif

                                    <a href="{{ route('marketing.sales-orders.show', $quotation->sales_order_id) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="bi bi-eye me-1"></i>View Full Sales Order
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

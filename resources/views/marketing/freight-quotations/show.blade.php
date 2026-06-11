<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-file-earmark me-2"></i>{{ $quotation->quote_number }}</h5>
                        <span class="badge bg-light text-dark">
                            @php
                                $statusClass = [
                                    'draft' => 'primary',
                                    'pending_logistics' => 'warning',
                                    'approved' => 'success',
                                    'linked_to_so' => 'info',
                                ];
                            @endphp
                            {{ ucfirst(str_replace('_', ' ', $quotation->workflow_status)) }}
                        </span>
                    </div>

                    <div class="card-body">
                        <!-- Status Timeline -->
                        <div class="mb-4">
                            <div class="row text-center">
                                <div class="col-3">
                                    <div class="step-indicator {{ in_array($quotation->workflow_status, ['draft', 'pending_logistics', 'approved', 'linked_to_so']) ? 'completed' : '' }}">
                                        <div class="step-circle">1</div>
                                        <small>Draft</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="step-indicator {{ in_array($quotation->workflow_status, ['approved', 'linked_to_so']) ? 'completed' : ($quotation->workflow_status === 'pending_logistics' ? 'active' : '') }}">
                                        <div class="step-circle">2</div>
                                        <small>Logistics Review</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="step-indicator {{ $quotation->workflow_status === 'linked_to_so' ? 'completed' : ($quotation->workflow_status === 'approved' ? 'active' : '') }}">
                                        <div class="step-circle">3</div>
                                        <small>Approved</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="step-indicator {{ $quotation->workflow_status === 'linked_to_so' ? 'active' : '' }}">
                                        <div class="step-circle">4</div>
                                        <small>Sales Order</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Quotation Details -->
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

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p><strong>Service Mode:</strong> {{ $quotation->service_mode }}</p>
                                <p><strong>Created By:</strong> {{ $quotation->createdBy->name ?? 'N/A' }}</p>
                                <p><strong>Created Date:</strong> {{ $quotation->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="col-md-6">
                                @if($quotation->respondedBy)
                                    <p><strong>Reviewed By:</strong> {{ $quotation->respondedBy->name }}</p>
                                    <p><strong>Reviewed Date:</strong> {{ $quotation->responded_at->format('M d, Y') }}</p>
                                @endif
                            </div>
                        </div>

                        <hr>

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

                        @if($quotation->workflow_status === 'approved')
                            <hr>

                            <!-- Logistics Response -->
                            <h6 class="border-bottom pb-2 mb-3"><strong>Logistics Quotation</strong></h6>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <p><strong>Estimated Freight:</strong> <span class="text-danger">₱ {{ number_format($quotation->estimated_freight, 2) }}</span></p>
                                    <p><strong>Valuation Charge:</strong> <span class="text-danger">₱ {{ number_format($quotation->valuation_charge, 2) }}</span></p>
                                    <p><strong>Handling Fee:</strong> <span class="text-danger">₱ {{ number_format($quotation->handling_fee, 2) }}</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Boxes Count:</strong> {{ $quotation->boxes_count }}</p>
                                    <p><strong>Total Amount:</strong> <strong class="text-danger" style="font-size: 1.2rem;">₱ {{ number_format($quotation->total_amount, 2) }}</strong></p>
                                </div>
                            </div>

                            @if($quotation->logistics_notes)
                                <div class="alert alert-info">
                                    <strong>Logistics Notes:</strong><br>
                                    {{ $quotation->logistics_notes }}
                                </div>
                            @endif
                        @endif

                        <hr>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 justify-content-between flex-wrap">
                            <a href="{{ route('marketing.freight-quotations.list') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Back
                            </a>

                            @if($quotation->workflow_status === 'approved' && !$quotation->sales_order_id)
                                <form method="POST" action="{{ route('marketing.freight-quotations.create-so-directly', $quotation->id) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="bi bi-plus-circle me-1"></i>Create Sales Order
                                    </button>
                                </form>
                            @elseif($quotation->sales_order_id)
                                <a href="{{ route('marketing.sales-orders.show', $quotation->sales_order_id) }}" class="btn btn-info btn-lg">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>View Sales Order
                                </a>
                            @elseif($quotation->workflow_status === 'draft')
                                <p class="text-muted mb-0">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Waiting for logistics to review and quote the freight charges...
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .step-indicator {
            position: relative;
            padding: 10px 0;
        }
        .step-indicator.completed .step-circle {
            background: #28a745;
            color: white;
        }
        .step-indicator.active .step-circle {
            background: #ff9900;
            color: white;
            box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.3);
        }
        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 5px;
            background: #e0e0e0;
            font-weight: bold;
        }
    </style>
    @endpush
</x-app-layout>

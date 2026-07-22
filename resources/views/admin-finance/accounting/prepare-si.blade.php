<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .invoice-form {
            background: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }
        .form-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e0e0e0;
        }
        .form-header .company-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .form-header .company-logo {
            width: 60px; height: 60px;
            background: #ff0000; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 2rem; font-weight: bold;
        }
        .form-header .company-name {
            font-size: 1.25rem; font-weight: 700; color: #333;
            text-transform: uppercase;
        }
        .form-header .document-title {
            text-align: center; font-size: 1.75rem; font-weight: 700;
            color: #333; margin-top: 1rem;
        }
        .invoice-number {
            text-align: center; font-size: 1.25rem; font-weight: 700;
            color: #ff0000; margin-top: 0.5rem;
        }
        .customer-section {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 2rem; margin-bottom: 1.5rem;
        }
        .customer-details, .transaction-details {
            background: #f8f9fa; padding: 1rem; border-radius: 6px;
        }
        .invoice-table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; }
        .invoice-table thead { background: #ff0000; color: #fff; }
        .invoice-table th, .invoice-table td { padding: 0.75rem; border: 1px solid #ddd; }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12">
            <form action="{{ route('admin-finance.accounting.sales-invoice.store', $order->id) }}" method="POST">
                @csrf
                <div class="card invoice-form">
                    <div class="form-header">
                        <div class="company-info">
                            <div class="company-logo">C</div>
                            <div class="company-details">
                                <div class="company-name">CLARETIAN COMMUNICATIONS FOUNDATION INC.</div>
                                <div class="company-address">8 Mayumi St., UP Village, Diliman, Quezon City</div>
                                <div class="company-contact">Tel. No.: 921-3984</div>
                            </div>
                        </div>
                        <div class="document-title">PREPARE SALES INVOICE</div>
                        <div class="invoice-number">SO Ref: #{{ $order->so_number }}</div>
                    </div>

                    <div class="customer-section">
                        <div class="customer-details">
                            <h5 class="fw-bold mb-3">Customer Information</h5>
                            <div class="mb-2">
                                <label class="form-label fw-bold">Sold to:</label>
                                <input type="text" class="form-control" value="{{ $order->customer->customer_name ?? 'N/A' }}" readonly>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-bold">Address:</label>
                                <textarea class="form-control" rows="2" readonly>{{ $order->billing_address ?? ($order->customer->address ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="transaction-details">
                            <h5 class="fw-bold mb-3">Transaction Details</h5>
                            <div class="mb-2">
                                <label class="form-label fw-bold">Date:</label>
                                <input type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-bold">Terms:</label>
                                <input type="text" class="form-control" value="{{ $order->terms }}" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Attachments Display -->
                    <div class="card mb-4 border-light bg-light">
                        <div class="card-body">
                            <h5 class="fw-bold text-dark mb-3"><i class="las la-paperclip me-1"></i> Attachments</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted">Pick List:</label>
                                    <div>
                                        @if($order->pick_list_attachment)
                                            <a href="{{ asset('storage/' . $order->pick_list_attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="las la-file-alt me-1"></i> View Pick List
                                            </a>
                                        @else
                                            <span class="text-muted"><i class="las la-info-circle me-1"></i> No Pick List attached</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted">Proof of Payment:</label>
                                    <div>
                                        @if($order->proof_of_payment)
                                            <a href="{{ asset('storage/' . $order->proof_of_payment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="las la-receipt me-1"></i> View Proof of Payment
                                            </a>
                                        @else
                                            <span class="text-muted"><i class="las la-info-circle me-1"></i> No Proof of Payment attached</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table class="invoice-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">QTY</th>
                                <th>DESCRIPTION</th>
                                <th style="width: 120px;">ISBN</th>
                                <th style="width: 120px;">AREA</th>
                                <th style="width: 150px;">UNIT PRICE</th>
                                <th style="width: 150px;">AMOUNT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td class="text-center">{{ $item->quantity }} {{ $item->unit ?? 'pcs' }}</td>
                                <td>{{ $item->product->name ?? 'Unknown Product' }}</td>
                                <td>{{ $item->isbn ?? '-' }}</td>
                                <td>{{ $item->area ?? '-' }}</td>
                                <td class="text-end">₱{{ number_format($item->price, 2) }}</td>
                                <td class="text-end">₱{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-end">TOTAL AMOUNT</th>
                                <th class="text-end">₱{{ number_format($order->total_amount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="las la-info-circle me-2"></i>
                                By submitting, you are marking this Sales Invoice as <strong>Prepared by {{ auth()->user()->name }}</strong>.
                            </div>
                        </div>
                    </div>

                    <div class="form-actions d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-light" onclick="window.history.back()">Cancel</button>
                        
                        {{-- Direct Invoice Ecom: Show "Linked to Picklist" button --}}
                        @if($order->type === 'ecom_direct')
                            <a href="{{ route('production.logistic.pick-list-management', ['so_id' => $order->id]) }}" class="btn btn-info">
                                <i class="las la-link me-1"></i>Linked to Picklist
                            </a>
                        @endif
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="las la-save me-2"></i>Finalize & Submit for Manager Approval
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

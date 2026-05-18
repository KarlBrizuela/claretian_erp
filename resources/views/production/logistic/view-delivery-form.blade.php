<x-app-layout :title="$title" :sidebar="$sidebar">
    @push('styles')
    <style>
        .order-form { background: #fff; border-radius: 8px; padding: 2rem; box-shadow: 0 0 20px rgba(0, 0, 0, 0.05); max-width: 1000px; margin: 0 auto; }
        .form-header { margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid #e0e0e0; position: relative; }
        .form-header .company-info { display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; }
        .form-header .company-logo { width: 60px; height: 60px; background: #ff0000; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 2rem; font-weight: bold; flex-shrink: 0; }
        .form-header .company-details { flex: 1; }
        .form-header .company-name { font-size: 1.25rem; font-weight: 700; color: #333; margin-bottom: 0.25rem; text-transform: uppercase; }
        .form-header .document-title { text-align: center; font-size: 1.75rem; font-weight: 700; color: #333; margin-top: 1rem; letter-spacing: 1px; }
        .doc-type-badge { position: absolute; top: 0; right: 0; padding: 5px 15px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; font-size: 0.8rem; font-weight: 700; color: #666; }
        
        .customer-section { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 1.5rem; }
        .details-box { background: #f8f9fa; padding: 1.5rem; border-radius: 6px; }
        .details-box h5 { border-bottom: 1px solid #dee2e6; padding-bottom: 0.5rem; margin-bottom: 1rem; font-weight: 700; color: #333; }
        
        .order-table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; border: 1px solid #ddd; }
        .order-table thead { background: #ff0000; color: #fff; }
        .order-table th, .order-table td { padding: 0.75rem; border: 1px solid #ddd; }
        .order-table tfoot { background: #f8f9fa; font-weight: 600; }
        
        .signature-section { display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; margin-top: 3rem; }
        .signature-box { text-align: center; }
        .signature-line { border-top: 1px solid #333; margin-top: 3rem; padding-top: 0.5rem; font-weight: 600; color: #333; }
        
        @media print { 
            .sidebar, .header, .form-actions, .btn, .nav-header { display: none !important; } 
            .content-body { margin-left: 0 !important; padding: 0 !important; } 
            .order-form { box-shadow: none !important; padding: 0 !important; max-width: 100% !important; }
            .order-table thead { background: #eee !important; color: #333 !important; }
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-12">
            <div class="mb-4 form-actions">
                <a href="{{ route('production.logistic.driver-dashboard') }}" class="btn btn-dark btn-sm">
                    <i class="las la-arrow-left me-2"></i>Back to Dashboard
                </a>
                <button type="button" class="btn btn-primary btn-sm ms-2" onclick="window.print()">
                    <i class="las la-print me-2"></i>Print Form
                </button>
            </div>

            <div class="card order-form">
                <div class="doc-type-badge">{{ $order->so_number }}</div>
                
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
                    <div class="document-title">{{ $documentType }}</div>
                    @if($documentType === 'SALES INVOICE' || $documentType === 'DELIVERY RECEIPT')
                        <div class="text-center text-muted small fw-bold mb-1">NON-VAT REGISTERED</div>
                        <div class="text-center extra-small text-muted italic">"This document is not valid for claim of input taxes."</div>
                    @endif
                </div>

                <!-- Info Grid -->
                <div class="customer-section">
                    <div class="details-box">
                        <h5>Customer Information</h5>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="fw-bold text-dark" style="width: 120px;">Sold/Delivered to:</td>
                                <td class="text-black fw-bold">{{ $order->customer->customer_name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">Address:</td>
                                <td class="text-black">{{ $order->shipping_address ?? $order->customer->shipping_address ?? $order->customer->billing_address ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">Contact:</td>
                                <td class="text-black">{{ $order->customer->phone ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="details-box">
                        <h5>Document Details</h5>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="fw-bold text-dark" style="width: 120px;">Reference No:</td>
                                <td class="text-black fw-bold">{{ $order->so_number }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">Date:</td>
                                <td class="text-black">{{ now()->format('F d, Y') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">Order Type:</td>
                                <td class="text-black text-uppercase">{{ str_replace('_', ' ', $order->type) }}</td>
                            </tr>
                            @if($order->plate_number)
                            <tr>
                                <td class="fw-bold text-dark">Vehicle:</td>
                                <td class="text-black">{{ $order->plate_number }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- Items Table -->
                <table class="order-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;" class="text-center">QTY</th>
                            <th style="width: 100px;" class="text-center">UNIT</th>
                            <th>DESCRIPTION</th>
                            @if($documentType !== 'DELIVERY RECEIPT')
                                <th style="width: 150px;" class="text-center">UNIT PRICE</th>
                                <th style="width: 150px;" class="text-center">AMOUNT</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td class="text-center text-black fw-bold">{{ (float)$item->quantity }}</td>
                            <td class="text-center text-uppercase text-muted">{{ $item->book->unit ?? 'pcs' }}</td>
                            <td>
                                <div class="text-black fw-bold">{{ $item->book->name ?? $item->description ?? 'Unknown Item' }}</div>
                                <small class="text-muted">{{ $item->book->sku ?? 'N/A' }}</small>
                            </td>
                            @if($documentType !== 'DELIVERY RECEIPT')
                                <td class="text-end">₱{{ number_format($item->price, 2) }}</td>
                                <td class="text-end fw-bold">₱{{ number_format($item->subtotal, 2) }}</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                    @if($documentType !== 'DELIVERY RECEIPT')
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end text-uppercase"><strong>Grand Total:</strong></td>
                            <td class="text-end fw-bold fs-5">₱{{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>

                <!-- Remarks -->
                @if($order->remarks)
                <div class="mb-4 p-3 bg-light rounded italic">
                    <strong class="text-dark">Remarks:</strong> {{ $order->remarks }}
                </div>
                @endif

                <!-- Signatures -->
                <div class="signature-section">
                    <div class="signature-box">
                        <div class="signature-line">RELEASED BY / DRIVER</div>
                        <div class="small text-muted">{{ auth()->user()->name }}</div>
                    </div>
                    <div class="signature-box">
                        <div class="signature-line">RECEIVED BY / CUSTOMER</div>
                        <div class="small text-muted">Signature over Printed Name</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

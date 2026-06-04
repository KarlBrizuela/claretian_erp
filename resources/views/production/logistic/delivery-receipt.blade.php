<x-app-layout :title="'Delivery Receipt'" :sidebar="'production'">
    <div class="row">
        <div class="col-xl-12">
            <div class="card receipt-form">
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
                    <div class="document-title">DELIVERY RECEIPT</div>
                    <div class="text-center text-muted small fw-bold mb-1">NON-VAT REGISTERED</div>
                    <div class="text-center extra-small text-muted italic mb-2">"This document is not valid for claim of input taxes."</div>
                </div>

                <!-- Receipt Details -->
                <div class="form-info-row">
                    <div class="form-info-item">
                        <label>DR No.:</label>
                        <input type="text" class="form-control" id="receiptNumber" placeholder="Enter DR number" value="{{ $order ? 'DR-' . $order->so_number : '' }}" {{ $order ? 'readonly' : '' }}>
                    </div>
                    <div class="form-info-item">
                        <label>Date:</label>
                        <input type="date" class="form-control" id="receiptDate" value="{{ $order ? ($order->dr_prepared_at ? \Carbon\Carbon::parse($order->dr_prepared_at)->format('Y-m-d') : date('Y-m-d')) : date('Y-m-d') }}">
                    </div>
                    <div class="form-info-item">
                        <label>Sales Order:</label>
                        <input type="text" class="form-control" placeholder="Sales Order" value="{{ $order ? $order->so_number : '' }}" readonly>
                    </div>
                </div>

                @if($order)
                    <!-- Delivered To Section -->
                    <div class="form-group">
                        <label class="fw-bold">Delivered To:</label>
                        <input type="text" class="form-control" value="{{ $order->customer->customer_name ?? 'Unknown' }}" readonly>
                    </div>

                    <!-- Delivery Address -->
                    <div class="form-group">
                        <label class="fw-bold">Delivery Address:</label>
                        <textarea class="form-control" readonly>{{ $order->shipping_address ?: ($order->customer->shipping_address ?? $order->customer->billing_address ?? '') }}</textarea>
                    </div>

                    <!-- Items Table -->
                    <div class="table-responsive">
                        <table class="receipt-table">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">QUANTITY</th>
                                    <th>DESCRIPTION</th>
                                    <th style="width: 120px; text-align: right;">UNIT PRICE</th>
                                    <th style="width: 120px; text-align: right;">AMOUNT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($order->items as $item)
                                    <tr>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ $item->book->name ?? 'Unknown Item' }}</td>
                                        <td style="text-align: right;">₱{{ number_format($item->unit_price, 2) }}</td>
                                        <td style="text-align: right;">₱{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No items found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Total Amount -->
                    <div style="text-align: right; margin-bottom: 1.5rem; font-size: 1.1rem; font-weight: 600;">
                        <strong>Total Amount: ₱{{ number_format($order->total_amount, 2) }}</strong>
                    </div>
                @else
                    <!-- Empty Form for Creating New Receipt -->
                    <div class="form-group">
                        <label class="fw-bold">Delivered To:</label>
                        <select class="form-control" id="recipient">
                            <option value="">Select Customer</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="fw-bold">Delivery Address:</label>
                        <textarea class="form-control" rows="2" placeholder="Enter delivery address"></textarea>
                    </div>

                    <button type="button" class="btn btn-danger btn-sm mb-3" onclick="addRow()">+ Add Row</button>
                    
                    <div class="table-responsive">
                        <table class="receipt-table">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">QUANTITY</th>
                                    <th>DESCRIPTION</th>
                                    <th style="width: 120px; text-align: right;">UNIT PRICE</th>
                                    <th style="width: 120px; text-align: right;">AMOUNT</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="receiptTableBody">
                                <tr>
                                    <td><input type="number" class="qty-input" value="0"></td>
                                    <td><input type="text" placeholder="Product description"></td>
                                    <td><input type="number" class="price-input" value="0.00" step="0.01" style="text-align: right;"></td>
                                    <td><input type="number" class="amount-input" value="0.00" readonly style="text-align: right;"></td>
                                    <td class="text-center"><button type="button" class="btn btn-xs sharp btn-danger" onclick="removeRow(this)"><i class="fa fa-times"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif

                <!-- Signature Section -->
                <div class="signature-section">
                    <div class="signature-box">
                        <label>Prepared by:</label>
                        <input type="text" id="preparedBy" placeholder="Enter name" value="{{ $order && $order->preparedBy ? $order->preparedBy->name : 'Johndoe' }}">
                        <div style="text-align: center; padding-top: 2rem;">
                            <div style="border-top: 1px solid #333; width: 200px; margin: 0 auto; padding-top: 0.5rem;">SIGNATURE</div>
                        </div>
                    </div>
                    <div class="signature-box">
                        <label>Received by:</label>
                        <input type="text" id="receivedBy" placeholder="Enter name">
                        <div style="text-align: center; padding-top: 2rem;">
                            <div style="border-top: 1px solid #333; width: 200px; margin: 0 auto; padding-top: 0.5rem;">SIGNATURE</div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" class="btn btn-light" onclick="window.print()">
                        <i class="las la-print"></i> Print
                    </button>
                    @if(!$order)
                        <button type="button" class="btn btn-primary">
                            <i class="las la-save"></i> Save Receipt
                        </button>
                        <button type="button" class="btn btn-success">
                            <i class="las la-paper-plane"></i> Submit
                        </button>
                    @else
                        <a href="{{ route('production.logistic.delivery-receipt-list') }}" class="btn btn-secondary">
                            <i class="las la-arrow-left"></i> Back to List
                        </a>
                    @endif
                </div>

    <!-- Modal System -->
    <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" id="alertModalHeader">
                    <h5 class="modal-title" id="alertModalLabel">Notification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="alertModalBody">
                    <p id="alertModalMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="confirmModalMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmModalOkBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <style>
        .receipt-form {
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
            width: 60px;
            height: 60px;
            background: #ff0000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 2rem;
            font-weight: bold;
            flex-shrink: 0;
        }

        .form-header .company-details {
            flex: 1;
        }

        .form-header .company-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.25rem;
            text-transform: uppercase;
        }

        .form-header .company-address {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.1rem;
        }

        .form-header .company-contact {
            font-size: 0.9rem;
            color: #666;
        }

        .form-header .document-title {
            text-align: center;
            font-size: 1.75rem;
            font-weight: 700;
            color: #333;
            margin-top: 1rem;
            letter-spacing: 1px;
        }

        .form-info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding: 0.75rem;
            background: #f8f9fa;
            border-radius: 6px;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .form-info-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex: 1;
            min-width: 200px;
        }

        .form-info-item label {
            font-weight: 600;
            color: #333;
            margin: 0;
            min-width: 80px;
        }

        .form-info-item input,
        .form-info-item .form-control {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 0.5rem;
            flex: 1;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            display: block;
            font-size: 0.9rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 0.5rem;
            font-size: 0.9rem;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 60px;
        }

        .receipt-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }

        .receipt-table thead {
            background: #ff0000;
            color: #fff;
        }

        .receipt-table th {
            padding: 0.75rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            border: 1px solid #ddd;
        }

        .receipt-table td {
            padding: 0.5rem;
            border: 1px solid #ddd;
        }

        .receipt-table input[type="text"],
        .receipt-table input[type="number"] {
            width: 100%;
            border: none;
            padding: 0.5rem;
            background: transparent;
        }

        .receipt-table input[type="number"] {
            text-align: right;
        }

        .receipt-table input:focus {
            outline: 2px solid #ff0000;
            outline-offset: -2px;
            background: #fff;
        }

        .signature-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 2px solid #e0e0e0;
        }

        .signature-box {
            display: flex;
            flex-direction: column;
        }

        .signature-box label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            display: block;
        }

        .signature-box input {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 0.5rem;
            margin-bottom: 2rem;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 2px solid #e0e0e0;
        }

        .btn-add-row {
            background: #ff0000;
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-add-row:hover {
            background: #ff6666;
        }

        .btn-remove-row {
            background: #dc3545;
            color: #fff;
            border: none;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.85rem;
        }

        .btn-remove-row:hover {
            background: #c82333;
        }

        .notes-section {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
        }

        .notes-section label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            display: block;
        }

        .notes-section textarea {
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 0.5rem;
            min-height: 80px;
            resize: vertical;
        }

        @media print {
            .sidebar,
            .header,
            .form-actions,
            .btn-add-row {
                display: none;
            }

            .receipt-form {
                box-shadow: none;
            }
        }
    </style>
    @endpush
</x-app-layout>

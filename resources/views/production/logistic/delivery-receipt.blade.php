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

                    <!-- Terms -->
                    @if($order->terms)
                        <div class="form-group">
                            <label class="fw-bold">Terms:</label>
                            <textarea class="form-control" readonly style="min-height: 60px;">{{ $order->terms }}</textarea>
                        </div>
                    @endif

                    <!-- Delivery Receipt Items Table -->
                    @php
                        $isConsignment = $order && in_array($order->type, ['area_consignment', 'area_sales_consignment']);
                        $displayItems = ($deliveryReceipt && count($deliveryReceipt->items) > 0) ? $deliveryReceipt->items : ($order ? $order->items : []);
                        $calculatedTotal = 0;
                    @endphp

                    <form action="{{ route('production.logistic.delivery-receipt.update-pick-qty', $order->id) }}" method="POST" id="drPickQtyForm">
                        @csrf
                        <div class="my-3" style="width: 100%;">
                            <table class="receipt-table table border">
                                <thead>
                                    <tr>
                                        <th style="width: 110px; text-align: center;">{{ $isConsignment ? 'SENT QTY' : 'QUANTITY' }}</th>
                                        @if($isConsignment)
                                            <th style="width: 130px; text-align: center; background-color: #0d6efd !important; color: #fff;">PICK QTY</th>
                                        @endif
                                        <th>DESCRIPTION</th>
                                        <th style="width: 140px; text-align: right;">UNIT PRICE</th>
                                        <th style="width: 140px; text-align: right;">AMOUNT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($displayItems as $item)
                                        @php
                                            $qty = (int)($item->quantity ?? 0);
                                            $pickQty = (int)($item->customer_selected_qty ?? 0);
                                            $unitPrice = $item->unit_price ?? $item->price ?? 0;
                                            $rowAmount = ($isConsignment && $pickQty > 0 ? $pickQty : $qty) * $unitPrice;
                                            $calculatedTotal += $rowAmount;
                                        @endphp
                                        <tr>
                                            <td style="text-align: center;">{{ $qty }}</td>
                                            @if($isConsignment)
                                                <td style="text-align: center; background-color: #f0f7ff; padding: 4px;">
                                                    <input type="number" 
                                                           class="form-control form-control-sm text-center fw-bold pick-qty-input" 
                                                           name="pick_qty[{{ $item->id }}]" 
                                                           value="{{ $pickQty }}" 
                                                           min="0" 
                                                           max="{{ $qty }}" 
                                                           data-price="{{ $unitPrice }}"
                                                           data-qty="{{ $qty }}"
                                                           style="width: 90px; margin: 0 auto; color: #0d6efd; border-color: #0d6efd; background-color: #fff;">
                                                </td>
                                            @endif
                                            <td>{{ $item->book->name ?? ($item->product->name ?? ($item->product_name ?? 'Unknown Item')) }}</td>
                                            <td style="text-align: right;">₱{{ number_format($unitPrice, 2) }}</td>
                                            <td style="text-align: right; font-weight: 600;" class="row-amount-td">₱{{ number_format($rowAmount, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ $isConsignment ? 5 : 4 }}" class="text-center py-3 text-muted">No items found for this delivery receipt</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($isConsignment && count($displayItems) > 0)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <small class="text-muted"><i class="las la-info-circle me-1"></i> You can edit Pick Qty directly here and click Save Pick Qty to update the record.</small>
                                <button type="submit" class="btn btn-sm btn-primary px-3 shadow-sm">
                                    <i class="las la-save me-1"></i> Save Pick Qty
                                </button>
                            </div>
                        @endif
                    </form>

                    <!-- Total Amount -->
                    <div style="text-align: right; margin-bottom: 1.5rem; font-size: 1.1rem; font-weight: 600;">
                        <strong>Total Amount: <span id="drTotalAmountDisplay">₱{{ number_format($calculatedTotal > 0 ? $calculatedTotal : $order->total_amount, 2) }}</span></strong>
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
                        <div class="signature-input-wrapper">
                            <input type="text" id="receivedBy" class="received-by-input" placeholder="Enter name" style="width: 100%; display: block;">
                            <span class="signature-value-display" id="receivedByDisplay"></span>
                        </div>
                        <div style="text-align: center; padding-top: 2rem;">
                            <div style="border-top: 1px solid #333; width: 200px; margin: 0 auto; padding-top: 0.5rem;">SIGNATURE</div>
                        </div>
                    </div>
                </div>

                @if($order && in_array($order->type, ['area_consignment', 'area_sales_consignment']))
                <div class="card p-3 my-3 border bg-light">
                    <h6 class="fw-bold text-dark mb-2"><i class="las la-exchange-alt me-1 text-primary"></i> Move Order & Proof of Payment Options</h6>
                    <p class="small text-muted mb-3">Uploading Proof of Payment or moving the order will make it visible on the Acknowledgement Receipt or Consignment Receipt page.</p>

                    <div class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <form action="{{ route('production.logistic.upload-dr-pop', $order->id) }}" method="POST" enctype="multipart/form-data" class="d-flex gap-2">
                                @csrf
                                <input type="file" name="proof_of_payment" class="form-control form-control-sm" required accept=".pdf,.png,.jpg,.jpeg">
                                <button type="submit" class="btn btn-outline-info btn-sm text-nowrap"><i class="las la-upload me-1"></i> Upload POP</button>
                            </form>
                            @if($order->proof_of_payment)
                                <small class="text-success fw-bold mt-1 d-block"><i class="las la-check-circle me-1"></i> Proof of payment attached (Visible in Acknowledgement Receipt)</small>
                            @endif
                        </div>
                        <div class="col-md-6 d-flex gap-2 justify-content-md-end flex-wrap">
                            @php
                                $isMovedToAR = $order->status === 'ar_created' || $order->ar_prepared_at !== null;
                                $isMovedToCR = $order->status === 'cr_created' || $order->cr_prepared_at !== null;
                            @endphp

                            @if($order->type === 'area_consignment')
                                <form action="{{ route('production.logistic.move-to-ar', $order->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-info text-white btn-sm" {{ $isMovedToAR ? 'disabled' : '' }}>
                                        <i class="las la-file-signature me-1"></i> {{ $isMovedToAR ? 'Moved to AR' : 'Move to AR' }}
                                    </button>
                                </form>
                            @elseif($order->type === 'area_sales_consignment')
                                <form action="{{ route('production.logistic.move-to-cr', $order->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm" {{ $isMovedToCR ? 'disabled' : '' }}>
                                        <i class="las la-file-contract me-1"></i> {{ $isMovedToCR ? 'Moved to CR' : 'Move to CR' }}
                                    </button>
                                </form>
                            @endif

                             <form action="{{ route('production.logistic.move-to-si', $order->id) }}" method="POST" style="display:inline-block;">
                                 @csrf
                                 <button type="submit" class="btn btn-danger btn-sm text-white fw-bold">
                                     <i class="las la-file-invoice me-1"></i> Move to SI
                                 </button>
                             </form>
                        </div>
                    </div>
                </div>
                @endif

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
                        @if(in_array($order->type, ['area_consignment', 'area_sales_consignment']))
                            <form action="{{ route('production.logistic.request-reconsignment', $order->id) }}" method="POST" style="display:inline-block; margin-left: 0.5rem;">
                                @csrf
                                <button type="submit" class="btn btn-warning" {{ !in_array($order->status, ['pending_dr_prep', 'ready_for_delivery', 'ar_created', 'cr_created', 'si_created', 'pending_si_approval', 'pending_si_prep']) ? 'disabled' : '' }}>
                                    <i class="las la-retweet"></i> Reconsignment
                                </button>
                            </form>
                            <form action="{{ route('production.logistic.return-consignment', $order->id) }}" method="POST" style="display:inline-block; margin-left: 0.5rem;">
                                @csrf
                                <button type="submit" class="btn btn-danger" {{ !in_array($order->status, ['pending_dr_prep', 'ready_for_delivery', 'ar_created', 'cr_created', 'si_created', 'pending_si_approval', 'pending_si_prep']) ? 'disabled' : '' }}>
                                    <i class="las la-undo-alt"></i> Return
                                </button>
                            </form>
                        @endif
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
            position: absolute;
            left: -9999px;
            top: -9999px;
            opacity: 0;
            visibility: hidden;
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
            width: 100% !important;
            border-collapse: collapse !important;
            margin-bottom: 1.5rem !important;
            display: table !important;
        }

        .receipt-table thead {
            background: #ff0000 !important;
            color: #ffffff !important;
            display: table-header-group !important;
        }

        .receipt-table tbody {
            display: table-row-group !important;
        }

        .receipt-table tr {
            display: table-row !important;
        }

        .receipt-table th {
            padding: 0.75rem 1rem !important;
            text-align: left;
            font-weight: 700 !important;
            font-size: 0.9rem !important;
            border: 1px solid #dee2e6 !important;
            background-color: #ff0000 !important;
            color: #ffffff !important;
            display: table-cell !important;
        }

        .receipt-table td {
            padding: 0.75rem 1rem !important;
            border: 1px solid #dee2e6 !important;
            vertical-align: middle !important;
            color: #212529 !important;
            background-color: #ffffff !important;
            display: table-cell !important;
            font-size: 0.9rem !important;
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

        .signature-input-wrapper {
            display: block;
            position: relative;
            min-height: 40px;
        }

        .signature-value-display {
            display: none;
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
            * {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }

            /* Hide UI elements and buttons */
            .sidebar,
            .header,
            .form-actions,
            .btn-add-row,
            .btn-remove-row,
            #saveSelectionsBtn,
            #linkToSIBtn,
            .modal,
            button,
            .btn {
                display: none !important;
            }

            /* Hide only the blue info box for area consignment */
            div[style*="background: #e7f3ff"] {
                display: none !important;
            }

            /* Make form inputs transparent but visible */
            .form-info-item input,
            .form-group input,
            .form-group textarea,
            .signature-box input {
                border: none !important;
                padding: 0 !important;
                background: transparent !important;
                outline: none !important;
                color: #000 !important;
            }

            /* Show the table and make input fields look like text */
            .receipt-table {
                width: 100%;
                page-break-inside: avoid;
                display: table !important;
            }

            .receipt-table thead {
                display: table-header-group;
                page-break-inside: avoid;
            }

            .receipt-table tbody {
                display: table-row-group;
            }

            .receipt-table tr {
                display: table-row;
                page-break-inside: avoid;
            }

            .receipt-table th,
            .receipt-table td {
                display: table-cell;
                border: 1px solid #333 !important;
                padding: 0.5rem !important;
            }

            .receipt-table th {
                background: #ff0000 !important;
                color: #fff !important;
                font-weight: bold !important;
            }

            .receipt-table td {
                background: #fff !important;
            }

            /* Show table inputs as text */
            .receipt-table input[type="number"],
            .receipt-table input[type="text"] {
                border: none !important;
                padding: 0 !important;
                background: transparent !important;
                outline: none !important;
                color: #000 !important;
                font-family: inherit;
                width: auto;
                text-align: inherit;
            }

            /* Clean up receipt form */
            .receipt-form {
                box-shadow: none !important;
                padding: 1.5rem !important;
                max-width: 100%;
                margin: 0 !important;
                border: none !important;
            }

            .form-header {
                margin-bottom: 1.5rem;
                padding-bottom: 1rem;
                border-bottom: 2px solid #000;
                text-align: center;
            }

            /* Hide company logo in print */
            .form-header .company-info {
                display: block !important;
                width: 100% !important;
                text-align: center !important;
                flex-direction: column !important;
                justify-content: center !important;
                align-items: center !important;
                gap: 0 !important;
                flex: none !important;
            }

            .form-header .company-logo {
                display: none !important;
                visibility: hidden !important;
                position: fixed !important;
                left: -9999px !important;
                top: -9999px !important;
                width: 0 !important;
                height: 0 !important;
                border: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                opacity: 0 !important;
                overflow: hidden !important;
                clip: rect(0, 0, 0, 0) !important;
                z-index: -9999 !important;
            }

            .form-header .company-details {
                width: 100%;
                flex: none;
            }

            .form-header .company-name {
                font-size: 1.1rem !important;
                margin-bottom: 0.1rem !important;
            }

            .form-header .company-address,
            .form-header .company-contact {
                font-size: 0.8rem !important;
                margin: 0 !important;
            }

            .form-info-row {
                background: transparent !important;
                border: none !important;
                padding: 0.25rem 0 !important;
                margin-bottom: 0.25rem !important;
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 1rem;
            }

            .form-info-item {
                display: block;
                margin-bottom: 0;
            }

            .form-info-item label {
                display: block;
                font-weight: bold;
                margin-bottom: 0.25rem;
                min-width: auto;
            }

            .form-group {
                background: transparent !important;
                border: none !important;
                padding: 0.5rem 0 !important;
                margin-bottom: 0.5rem !important;
            }

            .form-group label {
                font-weight: bold;
            }

            /* Signature section */
            .signature-section {
                page-break-inside: avoid;
                margin-top: 2rem;
                border-top: 2px solid #000;
                padding-top: 1.5rem;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 2rem;
            }

            .signature-box {
                page-break-inside: avoid;
            }

            .signature-box label {
                font-weight: bold;
                display: block;
                margin-bottom: 0.5rem;
            }

            .signature-box input {
                border: none !important;
                background: transparent !important;
                padding: 0 !important;
                min-height: 40px;
                margin-bottom: 1rem;
                color: #000 !important;
                font-size: 0.95rem;
                display: none !important;
                width: 100% !important;
                -webkit-appearance: none;
                appearance: none;
                font-family: inherit;
            }

            .signature-input-wrapper {
                display: block;
                min-height: 40px;
                margin-bottom: 1rem;
                position: relative;
            }

            .signature-value-display {
                display: block !important;
                color: #000 !important;
                font-size: 0.95rem !important;
                font-family: inherit !important;
                min-height: 20px !important;
                visibility: visible !important;
                opacity: 1 !important;
                white-space: pre-wrap !important;
            }

            .signature-box div[style*="border-top"] {
                border-top: 1px solid #000 !important;
                text-align: center;
                padding-top: 0.5rem;
                font-size: 0.8rem;
                font-weight: bold;
            }

            /* Page layout */
            body,
            html {
                margin: 0;
                padding: 0;
            }

            .row,
            .col-xl-12 {
                margin: 0 !important;
                padding: 0 !important;
            }

            .card {
                margin: 0 !important;
                padding: 1.5rem !important;
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.pick-qty-input').forEach(input => {
                input.addEventListener('input', function() {
                    const row = this.closest('tr');
                    const price = parseFloat(this.dataset.price) || 0;
                    const sentQty = parseInt(this.dataset.qty) || 0;
                    let val = parseInt(this.value) || 0;

                    if (val > sentQty) {
                        val = sentQty;
                        this.value = sentQty;
                    } else if (val < 0) {
                        val = 0;
                        this.value = 0;
                    }

                    const effectiveQty = val > 0 ? val : sentQty;
                    const rowAmount = effectiveQty * price;

                    const amountTd = row.querySelector('.row-amount-td');
                    if (amountTd) {
                        amountTd.textContent = '₱' + rowAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    }

                    let total = 0;
                    document.querySelectorAll('.pick-qty-input').forEach(i => {
                        const p = parseFloat(i.dataset.price) || 0;
                        const q = parseInt(i.dataset.qty) || 0;
                        const v = parseInt(i.value) || 0;
                        const eff = v > 0 ? v : q;
                        total += eff * p;
                    });

                    const totalElem = document.getElementById('drTotalAmountDisplay');
                    if (totalElem) {
                        totalElem.textContent = '₱' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>

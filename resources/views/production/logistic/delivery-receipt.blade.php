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

                    <!-- Items Table for Area Consignment (Item Selection) -->
                    @if(in_array($order->type, ['area_consignment', 'area_sales_consignment']) && in_array($order->status, ['pending_dr_prep', 'ready_for_delivery', 'si_created', 'reconsignment_pending']))
                        <div style="background: #e7f3ff; border: 2px solid #0d6efd; border-radius: 6px; padding: 1rem; margin-bottom: 1.5rem;">
                            <h5 style="color: #0d6efd; margin-bottom: 0;">Area Consignment - Select Items to Purchase</h5>
                            <p style="color: #666; font-size: 0.9rem; margin-bottom: 0;">Select the quantity you want to purchase for each item below. Items not selected will be returned.</p>
                        </div>

                        <form id="areaConsignmentForm" method="POST" action="{{ route('production.logistic.link-consignment-to-si', $order->id) }}">
                            @csrf
                            <div class="table-responsive">
                                <table class="receipt-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 80px;">SENT QTY</th>
                                            <th>PRODUCT NAME</th>
                                            <th style="width: 120px; text-align: right;">UNIT PRICE</th>
                                            <th style="width: 120px;">SELECT QTY</th>
                                            <th style="width: 120px; text-align: right;">SUBTOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($order->items as $index => $item)
                                            @php
                                                $alreadyPurchasedQty = \App\Models\SalesInvoiceItem::whereHas('invoice', function($query) use ($order) {
                                                    $query->where('so_id', $order->id)->where('status', '!=', 'cancelled');
                                                })->where('book_id', $item->book_id)->sum('quantity');
                                                $remainingQty = max(0, $item->quantity - $alreadyPurchasedQty);
                                            @endphp
                                            <tr class="consignment-item" data-item-id="{{ $item->id }}" data-unit-price="{{ $item->price }}">
                                                <td style="text-align: center;">
                                                    {{ $item->quantity }}
                                                    @if($alreadyPurchasedQty > 0)
                                                        <br><small class="text-muted">({{ $remainingQty }} remaining)</small>
                                                    @endif
                                                </td>
                                                <td>{{ $item->book->name ?? 'Unknown Item' }}</td>
                                                <td style="text-align: right;">₱{{ number_format($item->price, 2) }}</td>
                                                <td>
                                                    <input type="number" 
                                                           class="form-control selected-qty" 
                                                           name="items[{{ $item->id }}][selected_qty]" 
                                                           min="0" 
                                                           max="{{ $remainingQty }}" 
                                                           value="0"
                                                           style="text-align: center;"
                                                           {{ ($remainingQty <= 0 || in_array($order->status, ['si_created', 'reconsignment_pending'])) ? 'disabled' : '' }}>
                                                </td>
                                                <td style="text-align: right; font-weight: 600;">
                                                    <span class="item-subtotal">₱0.00</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No items found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr style="background: #f8f9fa; font-weight: 600;">
                                            <td colspan="4" style="text-align: right; padding: 0.75rem;">
                                                <strong>TOTAL PURCHASE AMOUNT:</strong>
                                            </td>
                                            <td style="text-align: right; font-weight: 700; padding: 0.75rem;">
                                                <span id="consignmentTotal">₱0.00</span>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Hidden inputs to pass item selections -->
                            <input type="hidden" name="order_id" value="{{ $order->id }}">

                            <div class="form-actions" style="margin-top: 1.5rem;">
                                <button type="button" class="btn btn-light" onclick="window.print()">
                                    <i class="las la-print"></i> Print
                                </button>
                                <a href="{{ route('production.logistic.delivery-receipt-list') }}" class="btn btn-secondary">
                                    <i class="las la-arrow-left"></i> Back to List
                                </a>
                                <button type="button" class="btn btn-info" id="saveSelectionsBtn" style="display: none;">
                                    <i class="las la-save"></i> Save Selections
                                </button>
                                <button type="submit" class="btn btn-primary" id="linkToSIBtn" {{ in_array($order->status, ['si_created', 'reconsignment_pending']) ? 'disabled' : '' }}>
                                    <i class="las la-link"></i> Link to Sales Invoice
                                </button>
                                <button type="submit" class="btn btn-warning" id="reconsignmentBtn" formaction="{{ route('production.logistic.request-reconsignment', $order->id) }}" style="margin-left: 0.5rem;" {{ !in_array($order->status, ['pending_dr_prep', 'ready_for_delivery', 'si_created']) ? 'disabled' : '' }}>
                                    <i class="las la-retweet"></i> Reconsignment
                                </button>
                            </div>
                        </form>

                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const form = document.getElementById('areaConsignmentForm');
                            const items = document.querySelectorAll('.consignment-item');
                            const saveBtn = document.getElementById('saveSelectionsBtn');
                            const orderId = '{{ $order->id }}';
                            const storageKey = `consignment_selections_${orderId}`;
                            
                            // Service fee if freight_collect is selected
                            const serviceFee = {{ $order->freight_option === 'freight_collect' ? 50 : 0 }};

                            // Load saved selections from localStorage
                            function loadSavedSelections() {
                                const saved = localStorage.getItem(storageKey);
                                if (saved) {
                                    try {
                                        const selections = JSON.parse(saved);
                                        document.querySelectorAll('.selected-qty').forEach(input => {
                                            const itemId = input.closest('.consignment-item').dataset.itemId;
                                            if (selections[itemId]) {
                                                input.value = selections[itemId];
                                            }
                                        });
                                        updateTotals();
                                    } catch (e) {
                                        console.log('Error loading saved selections');
                                    }
                                }
                            }

                            // Save selections to localStorage
                            function saveSelectionsToStorage() {
                                const selections = {};
                                document.querySelectorAll('.consignment-item').forEach(row => {
                                    const itemId = row.dataset.itemId;
                                    const qty = parseInt(row.querySelector('.selected-qty').value) || 0;
                                    selections[itemId] = qty;
                                });
                                localStorage.setItem(storageKey, JSON.stringify(selections));
                            }

                            // Clear saved selections
                            function clearSavedSelections() {
                                localStorage.removeItem(storageKey);
                            }

                            function updateTotals() {
                                let grandTotal = 0;
                                let hasSelections = false;
                                items.forEach(row => {
                                    const selectedQtyInput = row.querySelector('.selected-qty');
                                    const unitPrice = parseFloat(row.dataset.unitPrice);
                                    const selectedQty = parseInt(selectedQtyInput.value) || 0;
                                    const subtotal = selectedQty * unitPrice;
                                    
                                    if (selectedQty > 0) hasSelections = true;
                                    row.querySelector('.item-subtotal').textContent = '₱' + subtotal.toFixed(2);
                                    grandTotal += subtotal;
                                });
                                
                                // Add service fee to grand total
                                const totalWithFee = grandTotal + serviceFee;
                                document.getElementById('consignmentTotal').textContent = '₱' + totalWithFee.toFixed(2);
                                
                                // Show Save button only when items are selected
                                saveBtn.style.display = hasSelections ? 'inline-block' : 'none';
                            }

                            // Update totals when quantities change
                            document.querySelectorAll('.selected-qty').forEach(input => {
                                input.addEventListener('change', function() {
                                    updateTotals();
                                    saveSelectionsToStorage(); // Auto-save on each change
                                });
                                input.addEventListener('input', updateTotals);
                            });

                            // Save Selections button handler
                            saveBtn.addEventListener('click', function(e) {
                                e.preventDefault();
                                let totalSelected = 0;
                                let selectedCount = 0;
                                
                                document.querySelectorAll('.selected-qty').forEach(input => {
                                    const qty = parseInt(input.value) || 0;
                                    if (qty > 0) {
                                        totalSelected += qty;
                                        selectedCount++;
                                    }
                                });
                                
                                if (selectedCount === 0) {
                                    alert('Please select at least 1 item');
                                    return;
                                }
                                
                                // Save to storage
                                saveSelectionsToStorage();
                                
                                // Show success message
                                const message = `✓ Saved! ${selectedCount} item(s) selected (${totalSelected} pcs total)`;
                                alert(message);
                            });

                            // Form submission validation
                            form.addEventListener('submit', function(e) {
                                let totalSelected = 0;
                                document.querySelectorAll('.selected-qty').forEach(input => {
                                    totalSelected += parseInt(input.value) || 0;
                                });

                                if (totalSelected === 0) {
                                    e.preventDefault();
                                    alert('Please select at least 1 item to purchase');
                                    return false;
                                }

                                // Clear saved selections after successful submission
                                clearSavedSelections();
                            });

                            // Load saved selections on page load
                            loadSavedSelections();

                            // Handle received-by and prepared-by fields for print
                            const receivedByInput = document.getElementById('receivedBy');
                            const receivedByDisplay = document.getElementById('receivedByDisplay');
                            const preparedByInput = document.getElementById('preparedBy');
                            
                            function updateReceivedByDisplay() {
                                if (receivedByInput && receivedByDisplay) {
                                    const value = receivedByInput.value;
                                    receivedByDisplay.textContent = value;
                                }
                            }
                            
                            if (receivedByInput && receivedByDisplay) {
                                receivedByInput.addEventListener('input', function() {
                                    updateReceivedByDisplay();
                                });
                                receivedByInput.addEventListener('change', function() {
                                    updateReceivedByDisplay();
                                });
                                // Initialize display
                                updateReceivedByDisplay();
                            }

                            if (preparedByInput) {
                                preparedByInput.addEventListener('input', function() {
                                    this.setAttribute('data-print-value', this.value);
                                });
                                preparedByInput.setAttribute('data-print-value', preparedByInput.value || '');
                            }

                            // Hide logo and show received-by display before print
                            window.addEventListener('beforeprint', function() {
                                const logo = document.querySelector('.company-logo');
                                if (logo) {
                                    logo.style.display = 'none';
                                    logo.style.visibility = 'hidden';
                                    logo.style.opacity = '0';
                                    logo.style.position = 'fixed';
                                    logo.style.left = '-9999px';
                                    logo.style.top = '-9999px';
                                }
                                
                                // Show received-by display in print
                                if (receivedByDisplay) {
                                    receivedByDisplay.style.display = 'block';
                                    updateReceivedByDisplay();
                                }
                                if (receivedByInput) {
                                    receivedByInput.style.display = 'none';
                                }
                            });

                            // Restore after print
                            window.addEventListener('afterprint', function() {
                                const logo = document.querySelector('.company-logo');
                                if (logo) {
                                    logo.style.display = '';
                                    logo.style.visibility = '';
                                    logo.style.opacity = '';
                                    logo.style.position = '';
                                    logo.style.left = '';
                                    logo.style.top = '';
                                }
                                
                                // Restore received-by input
                                if (receivedByDisplay) {
                                    receivedByDisplay.style.display = 'none';
                                }
                                if (receivedByInput) {
                                    receivedByInput.style.display = 'block';
                                }
                            });
                        });
</script>
                    @else
                        <!-- Regular Delivery Receipt Items Table -->
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
                    @endif

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
                        <div class="signature-input-wrapper">
                            <input type="text" id="receivedBy" class="received-by-input" placeholder="Enter name" style="width: 100%; display: block;">
                            <span class="signature-value-display" id="receivedByDisplay"></span>
                        </div>
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
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
            table-layout: fixed;
            min-width: 800px;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
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
    @endpush
</x-app-layout>

<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
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
                    <div class="document-title">SALES ORDER</div>
                </div>

                @php $isEdit = isset($order); @endphp
                <form id="soForm" action="{{ $isEdit ? route('marketing.sales-orders.update', $order->id) : route('marketing.sales-orders.store') }}" method="POST" enctype="multipart/form-data" class="form-section">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                    @endif
                    
                    <!-- Customer and Order Details -->
                    <div class="customer-section">
                        <div class="customer-details">
                            <h5>Customer Information</h5>
                            <div class="form-group">
                                <label>Customer:</label>
                                <select class="form-control" name="customer_id" id="customerSelect" required>
                                    <option value="" selected disabled>Select Customer...</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->customer_id }}" 
                                            data-address="{{ $customer->shipping_address ?? $customer->billing_address ?? 'No address found' }}"
                                            {{ ($isEdit && $order->customer_id == $customer->customer_id) ? 'selected' : '' }}>
                                            {{ $customer->customer_name }} ({{ $customer->company_name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Added Address Field -->
                            <div class="form-group">
                                <label>Address:</label>
                                <textarea class="form-control" name="billing_address" id="billingAddress" rows="2" placeholder="Customer address...">{{ $isEdit ? $order->billing_address : '' }}</textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>Transaction Type:</label>
                                <select class="form-control" name="type" required>
                                    <option value="paid" {{ ($isEdit && $order->type == 'paid') ? 'selected' : '' }}>Paid Transaction</option>
                                    <option value="charge" {{ ($isEdit && $order->type == 'charge') ? 'selected' : '' }}>Charge Transaction</option>
                                    <option value="area_consignment" {{ ($isEdit && $order->type == 'area_consignment') ? 'selected' : '' }}>Area Consignment</option>
                                    <option value="direct_consignment" {{ ($isEdit && $order->type == 'direct_consignment') ? 'selected' : '' }}>Direct Consignment</option>
                                    <option value="foreign" {{ ($isEdit && $order->type == 'foreign') ? 'selected' : '' }}>Foreign Order</option>
                                    <option value="complimentary" {{ ($isEdit && $order->type == 'complimentary') ? 'selected' : '' }}>Complimentary</option>
                                    <option value="cod" {{ ($isEdit && $order->type == 'cod') ? 'selected' : '' }}>Due on Receipt(COD)</option>
                                    <option value="evaluation" {{ ($isEdit && $order->type == 'evaluation') ? 'selected' : '' }}>Evaluation</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Remarks:</label>
                                <textarea class="form-control" name="remarks" rows="2" placeholder="Additional notes...">{{ $isEdit ? $order->remarks : '' }}</textarea>
                            </div>
                        </div>
                        <div class="order-details">
                            <h5>Order Information</h5>
                            <div class="form-group">
                                <label>Date:</label>
                                <input type="date" class="form-control" value="{{ $isEdit ? $order->created_at->format('Y-m-d') : date('Y-m-d') }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>S.O. #:</label>
                                <input type="text" class="form-control" name="so_number" value="{{ $isEdit ? $order->so_number : 'SO-'.date('Y').'-'.rand(1000,9999) }}" readonly>
                            </div>
                            
                            <!-- Added Terms and REF# -->
                            <div class="form-group">
                                <label>Terms:</label>
                                <input type="text" class="form-control" name="terms" placeholder="e.g. 30 Days" value="{{ $isEdit ? $order->terms : '' }}">
                            </div>
                            <div class="form-group">
                                <label>REF #:</label>
                                <input type="text" class="form-control" name="ref_number" placeholder="PO Reference..." value="{{ $isEdit ? $order->ref_number : '' }}">
                            </div>

                            <div class="form-group">
                                <label>Freight Option:</label>
                                <select class="form-control" name="freight_option">
                                    <option value="">Select Freight Option</option>
                                    <option value="freight_collect" {{ ($isEdit && $order->freight_option == 'freight_collect') ? 'selected' : '' }}>Freight Collect</option>
                                    <option value="freight_billing" {{ ($isEdit && $order->freight_option == 'freight_billing') ? 'selected' : '' }}>Freight Billing</option>
                                </select>
                            </div>

                            <div class="form-group" id="serviceFeeGroup" style="display: none;">
                                <label>Service Fee:</label>
                                <input type="number" class="form-control" name="service_fee" value="50.00" readonly>
                            </div>

                            <div class="form-group">
                                <label>Attachment:</label>
                                @if($isEdit && $order->attachment)
                                    <div class="mb-2">
                                        <a href="/storage/{{ $order->attachment }}" target="_blank" class="text-primary"><i class="bi bi-paperclip"></i> View Current Attachment</a>
                                    </div>
                                @endif
                                <!-- Premium Upload UI -->
                                <div class="upload-area p-3 border rounded-3 text-center bg-light cursor-pointer position-relative" id="uploadArea" style="border: 2px dashed #ccc !important; transition: all 0.3s ease;">
                                    <input type="file" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" name="attachment" id="attachmentInput" accept=".pdf,.jpg,.jpeg,.png">
                                    
                                    <div class="upload-content" id="uploadContent">
                                        <div class="mb-1">
                                            <i class="bi bi-cloud-arrow-up fs-3 text-primary"></i>
                                        </div>
                                        <h6 class="fw-bold mb-0">Upload File</h6>
                                    </div>

                                    <div class="file-preview d-none" id="filePreview">
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-file-earmark-text fs-4 text-primary"></i>
                                            <div class="text-start">
                                                <h6 class="fw-bold mb-0 text-dark" id="fileName" style="font-size: 0.8rem;">filename.pdf</h6>
                                            </div>
                                            <button type="button" class="btn btn-close btn-sm ms-1" id="removeFile"></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <button type="button" class="btn-add-row" id="addItemBtn">
                        <i class="las la-plus me-2"></i>Add Item
                    </button>

                    <table class="form-table" id="itemsTable">
                        <thead>
                            <tr>
                                <th style="width: 100px;">QTY</th>
                                <th style="width: 90px;">UNIT</th>
                                <th>DESCRIPTION / PRODUCT</th>
                                <th style="width: 120px;">ISBN</th>
                                <th style="width: 120px;">AREA</th> <!-- Added AREA -->
                                <th style="width: 150px;">UNIT PRICE</th>
                                <th style="width: 150px;">AMOUNT</th>
                                <th style="width: 80px;">ACTION</th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            <!-- Dynamic rows via JS -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" class="text-end text-uppercase"><strong>Items Subtotal:</strong></td>
                                <td class="text-end fw-bold fs-5" id="subtotalAmount">₱ 0.00</td>
                                <td></td>
                            </tr>
                            @if($isEdit && $order->freight_charges)
                            <tr class="bg-light">
                                <td colspan="6" class="text-end text-uppercase"><strong>Freight Charges:</strong></td>
                                <td class="text-end fw-bold fs-5" id="freightChargesDisplay">₱ {{ number_format($order->freight_charges, 2) }}</td>
                                <td></td>
                            </tr>
                            @endif
                            <tr id="serviceFeeTotalRow" style="display: none;">
                                <td colspan="6" class="text-end text-uppercase"><strong>Service Fee:</strong></td>
                                <td class="text-end fw-bold fs-5">₱ 50.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-end text-uppercase"><strong>Total Amount:</strong></td>
                                <td class="text-end fw-bold fs-5" id="grandTotal">₱ 0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="form-actions">
                        <a href="{{ route('marketing.sales-orders.list') }}" class="btn btn-light border">Cancel</a>
                        <button type="submit" class="btn btn-secondary px-4" id="saveDraftBtn" name="action" value="draft">
                            <i class="las la-save me-2"></i>{{ $isEdit ? 'Update as Draft' : 'Save as Draft' }}
                        </button>
                        <button type="submit" class="btn btn-primary px-4" id="saveBtn" name="action" value="submit">
                            <i class="las la-check me-2"></i>{{ $isEdit ? 'Update Sales Order' : 'Create Sales Order' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Hidden Product Options for JS clone -->
    <select id="productSource" class="d-none">
        <option value="" disabled selected>Select Product...</option>
        @foreach($products as $product)
            <option value="{{ $product->id }}" 
                    data-price="{{ $product->price }}" 
                    data-isbn="{{ $product->isbn ?? $product->barcode ?? $product->sku ?? '' }}">
                    {{ $product->name }}
            </option>
        @endforeach
    </select>

    @push('styles')
    <style>
        .order-form { background: #fff; border-radius: 8px; padding: 2rem; box-shadow: 0 0 20px rgba(0, 0, 0, 0.05); }
        .form-header { margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid #e0e0e0; }
        .form-header .company-info { display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; }
        .form-header .company-logo { width: 60px; height: 60px; background: #ff0000; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 2rem; font-weight: bold; flex-shrink: 0; }
        .form-header .company-details { flex: 1; }
        .form-header .company-name { font-size: 1.25rem; font-weight: 700; color: #333; margin-bottom: 0.25rem; text-transform: uppercase; }
        .form-header .company-address, .form-header .company-contact { font-size: 0.9rem; color: #666; margin-bottom: 0.1rem; }
        .form-header .document-title { text-align: center; font-size: 1.75rem; font-weight: 700; color: #333; margin-top: 1rem; letter-spacing: 1px; }
        
        .customer-section { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 1.5rem; }
        .customer-details, .order-details { background: #f8f9fa; padding: 1rem; border-radius: 6px; }
        .customer-details h5, .order-details h5 { font-weight: 600; color: #333; margin-bottom: 0.75rem; font-size: 0.95rem; }
        
        .form-group { margin-bottom: 0.75rem; }
        .form-group label { font-weight: 600; color: #333; margin-bottom: 0.25rem; display: block; font-size: 0.9rem; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; border: 1px solid #ddd; border-radius: 4px; padding: 0.5rem; font-size: 0.9rem; }
        
        .form-table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; }
        .form-table thead { background: #ff0000; color: #fff; }
        .form-table th { padding: 0.75rem; text-align: left; font-weight: 600; font-size: 0.9rem; border: 1px solid #ddd; }
        .form-table td { padding: 0.5rem; border: 1px solid #ddd; vertical-align: middle !important; }
        /* Adjusted inputs to blend in better inside table */
        .form-table input.qty-input, .form-table input.price-input, .form-table input.isbn-input, .form-table select { width: 100%; border: none; padding: 0.5rem; background: transparent; }
        .form-table input:focus, .form-table select:focus { outline: 2px solid #ff0000; outline-offset: -2px; background: #fff; }
        .form-table tfoot { background: #f8f9fa; font-weight: 600; }
        .form-table tfoot td { padding: 0.75rem; border-top: 2px solid #333; }
        
        .btn-add-row { background: #ff0000; color: #fff; border: none; padding: 0.5rem 1rem; border-radius: 4px; margin-bottom: 1rem; cursor: pointer; transition: background 0.3s; }
        .btn-add-row:hover { background: #ff6666; }
        
        .form-actions { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 2px solid #e0e0e0; }
        
        /* Selectpicker overrides */
        .bootstrap-select .btn { 
            background-color: transparent !important; 
            border: none !important; 
            padding: 0 0.5rem !important; /* Added left padding for text */
            height: 38px !important;
            font-size: 0.9rem !important;
            text-align: left !important;
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
        }
        .bootstrap-select .dropdown-toggle:focus { outline: none !important; }
        .bootstrap-select .filter-option {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
            text-align: left !important;
        }

        .product-select-td { 
            text-align: left; 
            vertical-align: middle !important;
        }
        
        /* Consistency across inputs */
        .form-table input, .form-table .bootstrap-select {
            height: 38px;
        }
        
        /* Remove button styling */
        .remove-row {
            background-color: #ff0000 !important;
            color: white !important;
            padding: 0.4rem 0.6rem !important;
            font-size: 0.9rem;
            border: none !important;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .remove-row:hover {
            background-color: #cc0000 !important;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addItemBtn = document.getElementById('addItemBtn');
            const itemsBody = document.getElementById('itemsBody');
            const productSource = document.getElementById('productSource');
            const grandTotalEl = document.getElementById('grandTotal');
            const customerSelect = document.getElementById('customerSelect');
            const billingAddress = document.getElementById('billingAddress');

            // Freight Option Handler
            const freightOptionSelect = document.querySelector('select[name="freight_option"]');
            const serviceFeeGroup = document.getElementById('serviceFeeGroup');
            const serviceFeeTotalRow = document.getElementById('serviceFeeTotalRow');

            function toggleServiceFee() {
                const shouldShowServiceFee = freightOptionSelect?.value === 'freight_collect';
                if (serviceFeeGroup) {
                    serviceFeeGroup.style.display = shouldShowServiceFee ? 'block' : 'none';
                }
                if (serviceFeeTotalRow) {
                    serviceFeeTotalRow.style.display = shouldShowServiceFee ? '' : 'none';
                }
            }
            
            if (freightOptionSelect) {
                freightOptionSelect.addEventListener('change', function() {
                    toggleServiceFee();
                    updateGrandTotal();
                });
                
                toggleServiceFee();
            }

            // Auto-fill address
            customerSelect.addEventListener('change', function() {
                const option = this.options[this.selectedIndex];
                const address = option.getAttribute('data-address');
                if(address && address !== 'No address found') {
                    billingAddress.value = address;
                } else {
                    billingAddress.value = '';
                }
            });

            // Upload UI Logic (Same as before)
            const uploadArea = document.getElementById('uploadArea');
            const attachmentInput = document.getElementById('attachmentInput');
            const uploadContent = document.getElementById('uploadContent');
            const filePreview = document.getElementById('filePreview');
            const fileName = document.getElementById('fileName');
            const removeFile = document.getElementById('removeFile');

            uploadArea.addEventListener('dragover', () => uploadArea.style.borderColor = '#0d6efd');
            uploadArea.addEventListener('dragleave', () => uploadArea.style.borderColor = '#ccc');
            attachmentInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    fileName.textContent = file.name;
                    uploadContent.classList.add('d-none');
                    filePreview.classList.remove('d-none');
                    uploadArea.classList.remove('bg-light');
                    uploadArea.classList.add('bg-white', 'border-primary');
                }
            });
            removeFile.addEventListener('click', function(e) {
                e.preventDefault();
                attachmentInput.value = '';
                uploadContent.classList.remove('d-none');
                filePreview.classList.add('d-none');
                uploadArea.classList.add('bg-light');
                uploadArea.classList.remove('bg-white', 'border-primary');
            });

            function calculateRow(row) {
                const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                const price = parseFloat(row.querySelector('.price-input').value) || 0;
                const subtotal = qty * price;
                row.querySelector('.subtotal-display').textContent = '₱ ' + subtotal.toFixed(2);
                updateGrandTotal();
            }

            function updateGrandTotal() {
                let total = 0;
                document.querySelectorAll('.subtotal-display').forEach(el => {
                    total += parseFloat(el.textContent.replace('₱ ', '')) || 0;
                });
                
                // Display items subtotal
                document.getElementById('subtotalAmount').textContent = '₱ ' + total.toFixed(2);
                
                // Add freight charges if they exist
                const freightChargesDisplay = document.getElementById('freightChargesDisplay');
                const freightCharges = freightChargesDisplay ? 
                    parseFloat(freightChargesDisplay.textContent.replace(/[^\d.-]/g, '')) : 0;
                const serviceFee = freightOptionSelect?.value === 'freight_collect' ? 50 : 0;
                
                const grandTotal = total + freightCharges + serviceFee;
                document.getElementById('grandTotal').textContent = '₱ ' + grandTotal.toFixed(2);
            }

            const existingItems = @json($isEdit ? $order->items : []);

            function addRow(data = null) {
                const tr = document.createElement('tr');
                const uniqueId = Date.now() + Math.random().toString(36).substring(7);
                
                // If data provided (edit mode), use those values, else default
                const qtyVal = data ? data.quantity : 1;
                const unitVal = data ? (data.unit || 'pcs') : 'pcs';
                const productId = data ? (data.book_id || data.product_id) : '';
                const isbnVal = data ? (data.isbn || '') : '';
                const priceVal = data ? data.price : '';
                const subtotalVal = data ? (data.quantity * data.price) : 0;

                tr.innerHTML = `
                    <td>
                        <input type="number" class="qty-input" name="items[new_${uniqueId}][quantity]" min="1" value="${qtyVal}" required style="width: 100%; text-align: center;">
                    </td>
                    <td>
                        <select class="form-control" name="items[new_${uniqueId}][unit]" style="border:none; text-align:center;">
                            <option value="pcs" ${unitVal === 'pcs' ? 'selected' : ''}>pcs</option>
                            <option value="set" ${unitVal === 'set' ? 'selected' : ''}>set</option>
                        </select>
                    </td>
                    <td class="product-select-td">
                        <select class="form-control product-select selectpicker" data-live-search="true" name="items[new_${uniqueId}][product_id]" required>
                            ${productSource.innerHTML}
                        </select>
                    </td>
                    <td>
                         <input type="text" class="isbn-input" name="items[new_${uniqueId}][isbn]" value="${isbnVal}" readonly style="width: 100%; border: none; background: transparent;">
                    </td>
                    <td>
                         <input type="text" class="area-input" name="items[new_${uniqueId}][area]" value="${data ? (data.area || '') : ''}" placeholder="Area..." style="width: 100%; border: none; background: transparent; height: 38px;">
                    </td>
                    <td>
                        <input type="number" class="price-input" name="items[new_${uniqueId}][price]" step="0.01" value="${priceVal}" required style="width: 100%; text-align: right; border: 1px solid #eee;">
                    </td>
                    <td class="subtotal-display fw-bold text-end pe-3">₱ ${subtotalVal.toFixed(2)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm remove-row"><i class="bi bi-trash me-1"></i>Remove</button>
                    </td>
                `;
                
                const select = tr.querySelector('.product-select');
                const priceInput = tr.querySelector('.price-input');
                const isbnInput = tr.querySelector('.isbn-input');
                const qtyInput = tr.querySelector('.qty-input');
                const removeBtn = tr.querySelector('.remove-row');

                // Set selected product if editing
                if (productId) {
                    select.value = productId;
                }

                select.addEventListener('change', function() {
                    const option = this.options[this.selectedIndex];
                    priceInput.value = option.dataset.price;
                    isbnInput.value = option.dataset.isbn; 
                    calculateRow(tr);
                });

                qtyInput.addEventListener('input', () => calculateRow(tr));
                priceInput.addEventListener('input', () => calculateRow(tr)); // Allow price edits to update total
                
                removeBtn.addEventListener('click', function() {
                    tr.remove();
                    updateGrandTotal();
                });

                itemsBody.appendChild(tr);

                // Initialize bootstrap-select for the new row
                if ($.fn.selectpicker) {
                    $(select).selectpicker();
                }

                updateGrandTotal();
            }

            addItemBtn.addEventListener('click', () => addRow());
            
            // Initialize rows
            if (existingItems.length > 0) {
                existingItems.forEach(item => {
                    addRow(item);
                });
            } else {
                addRow();
            }

            // Form submission feedback and validation
            const soForm = document.getElementById('soForm');
            const saveBtn = document.getElementById('saveBtn');

            soForm.addEventListener('submit', function(e) {
                // Check if items exist
                if (itemsBody.rows.length === 0) {
                    e.preventDefault();
                    alert('Please add at least one item to the order.');
                    return;
                }

                // Use jQuery to check select values as it plays better with selectpicker
                let allProductsSelected = true;
                $(itemsBody).find('select.product-select').each(function() {
                    if (!$(this).val()) {
                        allProductsSelected = false;
                    }
                });

                if (!allProductsSelected) {
                    e.preventDefault();
                    alert('Please select a product for all rows.');
                    return;
                }

                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="las la-spinner la-spin me-2"></i> {{ $isEdit ? "Updating..." : "Creating..." }}';
            });
        });
    </script>
    @endpush
</x-app-layout>

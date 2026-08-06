<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="bi bi-truck me-2"></i>Create Freight Quotation Request</h5>
                    </div>
                    <div class="card-body">
                        <form id="freightQuotationForm" method="POST" action="{{ route('marketing.freight-quotations.store') }}">
                            @csrf

                            <!-- Customer Selection Section -->
                            <h6 class="border-bottom pb-2 mb-3"><strong>Customer Information</strong></h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Customer:</label>
                                    <select class="form-control selectpicker @error('customer_id') is-invalid @enderror" 
                                            data-live-search="true" data-size="8" data-live-search-placeholder="Search customer..."
                                            name="customer_id" required>
                                        <option value="">Select Customer...</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->customer_id }}" {{ old('customer_id') == $customer->customer_id ? 'selected' : '' }}>
                                                {{ $customer->customer_name }} ({{ $customer->company_name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Transaction Type:</label>
                                    <select class="form-control @error('transaction_type') is-invalid @enderror" name="transaction_type" required>
                                        <option value="paid" {{ old('transaction_type', 'paid') === 'paid' ? 'selected' : '' }}>Paid Transaction</option>
                                        <option value="charge" {{ old('transaction_type') === 'charge' ? 'selected' : '' }}>Charge Transaction</option>
                                        <option value="area_consignment" {{ old('transaction_type') === 'area_consignment' ? 'selected' : '' }}>Area Consignment</option>
                                        <option value="area_sales_consignment" {{ old('transaction_type') === 'area_sales_consignment' ? 'selected' : '' }}>Area Sales Consignment</option>
                                        <option value="direct_consignment" {{ old('transaction_type') === 'direct_consignment' ? 'selected' : '' }}>Direct Consignment</option>
                                        <option value="foreign" {{ old('transaction_type') === 'foreign' ? 'selected' : '' }}>Foreign Order</option>
                                        <option value="complimentary" {{ old('transaction_type') === 'complimentary' ? 'selected' : '' }}>Complimentary</option>
                                        <option value="cod" {{ old('transaction_type') === 'cod' ? 'selected' : '' }}>Due on Receipt (COD)</option>
                                        <option value="evaluation" {{ old('transaction_type') === 'evaluation' ? 'selected' : '' }}>Evaluation</option>
                                    </select>
                                    @error('transaction_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <hr>

                            <!-- Shipment Details Section -->
                            <h6 class="border-bottom pb-2 mb-3"><strong>Shipment Details</strong></h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Origin Contact:</label>
                                    <input type="text" class="form-control @error('origin_contact') is-invalid @enderror" 
                                           name="origin_contact" placeholder="Contact person name" 
                                           value="{{ old('origin_contact') }}" required>
                                    @error('origin_contact')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Origin Province:</label>
                                    <input type="text" class="form-control @error('origin_province') is-invalid @enderror" 
                                           name="origin_province" placeholder="Province name" 
                                           value="{{ old('origin_province') }}" required>
                                    @error('origin_province')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Origin Address:</label>
                                <textarea class="form-control @error('origin_address') is-invalid @enderror" 
                                          name="origin_address" rows="2" placeholder="Full pickup address" 
                                          required>{{ old('origin_address') }}</textarea>
                                @error('origin_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Destination Contact:</label>
                                    <input type="text" class="form-control @error('destination_contact') is-invalid @enderror" 
                                           name="destination_contact" placeholder="Contact person name" 
                                           value="{{ old('destination_contact') }}" required>
                                    @error('destination_contact')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Destination Province:</label>
                                    <input type="text" class="form-control @error('destination_province') is-invalid @enderror" 
                                           name="destination_province" placeholder="Province name" 
                                           value="{{ old('destination_province') }}" required>
                                    @error('destination_province')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Destination Address:</label>
                                <textarea class="form-control @error('destination_address') is-invalid @enderror" 
                                          name="destination_address" rows="2" placeholder="Full delivery address" 
                                          required>{{ old('destination_address') }}</textarea>
                                @error('destination_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <hr>

                            <div class="mb-3">
                                <label class="form-label">Service Mode:</label>
                                <select class="form-control @error('service_mode') is-invalid @enderror" 
                                        name="service_mode" required>
                                    <option value="">Select Service Mode</option>
                                    <option value="Sea Freight" {{ old('service_mode') === 'Sea Freight' ? 'selected' : '' }}>Sea Freight</option>
                                    <option value="Air Freight" {{ old('service_mode') === 'Air Freight' ? 'selected' : '' }}>Air Freight</option>
                                    <option value="Land Freight" {{ old('service_mode') === 'Land Freight' ? 'selected' : '' }}>Land Freight</option>
                                    <option value="Mixed" {{ old('service_mode') === 'Mixed' ? 'selected' : '' }}>Mixed</option>
                                </select>
                                @error('service_mode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                 <label class="form-label">Forwarder:</label>
                                 <input type="text" class="form-control @error('forwarder') is-invalid @enderror" 
                                        name="forwarder" placeholder="Enter forwarder name (e.g., LBC, 2GO, J&T, AP Cargo, FedEx, DHL, etc.)..." 
                                        value="{{ old('forwarder') }}">
                                 @error('forwarder')<div class="invalid-feedback">{{ $message }}</div>@enderror
                             </div>

                            <div class="mb-3">
                                <label class="form-label">Freight Option:</label>
                                <select class="form-control @error('freight_option') is-invalid @enderror" 
                                        name="freight_option" id="freightOption">
                                    <option value="">Select Freight Option</option>
                                    <option value="freight_collect" {{ old('freight_option') === 'freight_collect' ? 'selected' : '' }}>Freight Collect</option>
                                    <option value="freight_billing" {{ old('freight_option') === 'freight_billing' ? 'selected' : '' }}>Freight Billing</option>
                                </select>
                                @error('freight_option')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="alert alert-info py-2 mb-3" id="serviceFeeNotice" style="display: none;">
                                <strong>Service Fee:</strong> ₱ 50.00
                            </div>

                            <hr>

                            <!-- Sales Order Items Section -->
                            <h6 class="border-bottom pb-2 mb-3"><strong>Sales Order Items (Optional)</strong></h6>
                            <p class="text-muted small">Add items that will be included in the Sales Order created from this quotation.</p>

                            <button type="button" class="btn btn-sm btn-danger mb-3" id="addSOItem" style="background: #ff0000; border: none; padding: 0.5rem 1rem;">
                                <i class="fas fa-plus me-1"></i>Add Item
                            </button>

                            <div class="table-responsive mb-3">
                                <table class="table table-sm table-bordered" id="soItemsTable">
                                    <thead class="table-primary">
                                        <tr>
                                            <th style="width: 100px;">QTY</th>
                                            <th>DESCRIPTION / PRODUCT</th>
                                            <th style="width: 120px;">UNIT PRICE</th>
                                            <th style="width: 130px;">DISCOUNT</th>
                                            <th style="width: 120px;">AMOUNT</th>
                                            <th style="width: 80px;">ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody id="soItemsBody">
                                        <!-- Dynamic rows via JS -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                            <td class="text-end fw-bold" id="soSubtotal">₱ 0.00</td>
                                            <td></td>
                                        </tr>
                                        <tr id="serviceFeeRow" style="display: none;">
                                            <td colspan="4" class="text-end"><strong>Service Fee:</strong></td>
                                            <td class="text-end fw-bold">₱ 50.00</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                            <td class="text-end fw-bold" id="soTotal">₱ 0.00</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Hidden Product Options for JS -->
                            <select id="productSource" class="d-none">
                                <option value="" disabled selected>Select Product...</option>
                                @if(isset($products))
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" 
                                                data-price="{{ $product->price }}" 
                                                data-name="{{ $product->name }}">
                                                {{ $product->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>

                            <!-- Action Buttons -->
                            <div class="form-actions mt-4 pt-3 border-top">
                                <a href="{{ route('marketing.freight-quotations.list') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-check me-1"></i>Submit for Logistics Review
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        /* Prevent bootstrap-select dropdown from overflowing viewport and hiding search box */
        .bootstrap-select .dropdown-menu {
            max-height: 360px !important;
            overflow: hidden !important;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.18) !important;
            border-radius: 6px !important;
        }
        .bootstrap-select .bs-searchbox {
            position: sticky !important;
            top: 0 !important;
            z-index: 1050 !important;
            background: #ffffff !important;
            padding: 8px 10px !important;
            border-bottom: 1px solid #e9ecef !important;
        }
        .bootstrap-select .bs-searchbox input {
            font-size: 0.9rem !important;
            padding: 0.4rem 0.75rem !important;
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
        }
        .bootstrap-select .dropdown-menu .inner {
            max-height: 280px !important;
            overflow-y: auto !important;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof $ !== 'undefined' && $.fn && $.fn.selectpicker) {
                $('select[name="customer_id"]').selectpicker({ size: 8, liveSearch: true });
            }
            // Cargo Items Logic
            const addBtn = document.getElementById('addCargoItem');
            const tbody = document.getElementById('cargoItemsBody');
            const table = document.getElementById('cargoItemsTable');

            if (addBtn && tbody) {
                addBtn.addEventListener('click', function() {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td><input type="number" class="form-control form-control-sm" name="cargo_qty[]" min="1" value="1" required></td>
                        <td><input type="text" class="form-control form-control-sm" name="cargo_package_type[]" placeholder="Box, Bag, Pallet, etc." required></td>
                        <td><input type="text" class="form-control form-control-sm" name="cargo_dimensions[]" placeholder="e.g., 50cm x 40cm x 30cm" required></td>
                        <td><button type="button" class="btn btn-sm btn-danger remove-row" title="Remove Item"><i class="fas fa-trash"></i></button></td>
                    `;
                    tbody.appendChild(row);
                    addRemoveListeners();
                });
            }

            function addRemoveListeners() {
                if (!tbody) return;
                document.querySelectorAll('#cargoItemsBody .remove-row').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (tbody.querySelectorAll('tr').length > 1) {
                            this.closest('tr').remove();
                        } else {
                            alert('You must have at least one cargo item');
                        }
                    });
                });
            }

            addRemoveListeners();

            // Sales Order Items Logic
            const addSOBtn = document.getElementById('addSOItem');
            const soItemsBody = document.getElementById('soItemsBody');
            const productSource = document.getElementById('productSource');
            const freightOption = document.getElementById('freightOption');
            const serviceFeeNotice = document.getElementById('serviceFeeNotice');
            const serviceFeeRow = document.getElementById('serviceFeeRow');
            const soTotal = document.getElementById('soTotal');

            // Freight Option Change Handler
            if (freightOption) {
                freightOption.addEventListener('change', function() {
                    const isFreightCollect = this.value === 'freight_collect';
                    if (serviceFeeRow) {
                        serviceFeeRow.style.display = isFreightCollect ? 'table-row' : 'none';
                    }
                    calculateSOSubtotal();
                });
                
                // Trigger on page load in case freight option has a value
                const event = new Event('change');
                freightOption.dispatchEvent(event);
            }

            function calculateSOSubtotal() {
                let total = 0;
                document.querySelectorAll('.so-item-amount').forEach(el => {
                    total += parseFloat(el.textContent.replace('₱ ', '')) || 0;
                });
                
                // Add service fee if freight collect is selected
                const isFreightCollect = freightOption && freightOption.value === 'freight_collect';
                const serviceFeeAmount = isFreightCollect ? 50 : 0;
                
                const finalTotal = total + serviceFeeAmount;
                
                document.getElementById('soSubtotal').textContent = '₱ ' + total.toFixed(2);
                document.getElementById('soTotal').textContent = '₱ ' + finalTotal.toFixed(2);
            }

            function calculateRow(row) {
                const qty = parseFloat(row.querySelector('.so-qty').value) || 0;
                const price = parseFloat(row.querySelector('.so-price').value) || 0;
                const discVal = parseFloat(row.querySelector('.so-discount-val')?.value) || 0;
                const discType = row.querySelector('.so-discount-type')?.value || 'percentage';

                const gross = qty * price;
                let dAmt = discType === 'percentage' ? gross * (discVal / 100) : discVal;
                const netSubtotal = Math.max(0, gross - dAmt);

                row.querySelector('.so-item-amount').textContent = '₱ ' + netSubtotal.toFixed(2);
                calculateSOSubtotal();
            }

            addSOBtn.addEventListener('click', function() {
                const row = document.createElement('tr');
                const uniqueId = Date.now() + Math.random().toString(36).substring(7);
                
                row.innerHTML = `
                    <td>
                        <input type="number" class="form-control form-control-sm so-qty" name="so_items[new_${uniqueId}][quantity]" min="1" value="1" required style="text-align: center;">
                    </td>
                    <td>
                        <select class="form-control form-control-sm so-product selectpicker" data-live-search="true" data-size="8" data-live-search-placeholder="Search product..." name="so_items[new_${uniqueId}][product_id]" required>
                            ${productSource.innerHTML}
                        </select>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm so-price" name="so_items[new_${uniqueId}][price]" step="0.01" min="0" required style="text-align: right;">
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-1">
                            <input type="number" step="any" min="0" class="form-control form-control-sm so-discount-val" name="so_items[new_${uniqueId}][discount_value]" placeholder="0" style="text-align: right; width: 55%;">
                            <select class="form-select form-select-sm so-discount-type px-1" name="so_items[new_${uniqueId}][discount_type]" style="width: 45%; font-size: 0.8rem;">
                                <option value="percentage">%</option>
                                <option value="amount">₱</option>
                            </select>
                        </div>
                    </td>
                    <td class="so-item-amount text-end fw-bold">₱ 0.00</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger so-remove-row" title="Remove Item" style="background: #ff0000; border: none; padding: 0.35rem 0.6rem;"><i class="fas fa-trash"></i></button>
                    </td>
                `;

                const qtyInput = row.querySelector('.so-qty');
                const priceInput = row.querySelector('.so-price');
                const discValInput = row.querySelector('.so-discount-val');
                const discTypeSelect = row.querySelector('.so-discount-type');
                const productSelect = row.querySelector('.so-product');
                const removeBtn = row.querySelector('.so-remove-row');

                qtyInput.addEventListener('input', () => calculateRow(row));
                priceInput.addEventListener('input', () => calculateRow(row));
                discValInput.addEventListener('input', () => calculateRow(row));
                discTypeSelect.addEventListener('change', () => calculateRow(row));
                
                productSelect.addEventListener('change', function() {
                    const option = this.options[this.selectedIndex];
                    priceInput.value = option.dataset.price || 0;
                    calculateRow(row);
                });

                if (typeof $ !== 'undefined' && $.fn && $.fn.selectpicker) {
                    $(productSelect).selectpicker({
                        size: 8,
                        liveSearch: true,
                        liveSearchPlaceholder: 'Search product...'
                    });
                }

                removeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    row.remove();
                    calculateSOSubtotal();
                });

                soItemsBody.appendChild(row);
                calculateSOSubtotal();
            });
        });
    </script>
    @endpush
</x-app-layout>

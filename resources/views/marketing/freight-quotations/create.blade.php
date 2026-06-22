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
                            <div class="mb-3">
                                <label class="form-label">Customer:</label>
                                <select class="form-control @error('customer_id') is-invalid @enderror" 
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
                                <strong>Service Fee:</strong> â‚± 50.00
                            </div>

                            <!-- Cargo Items Section -->
                            <!-- <h6 class="border-bottom pb-2 mb-3"><strong>Cargo Items</strong></h6>

                            <div class="table-responsive mb-3">
                                <table class="table table-sm table-bordered" id="cargoItemsTable">
                                    <thead class="table-danger">
                                        <tr>
                                            <th style="width: 100px;">Quantity</th>
                                            <th style="width: 150px;">Package Type</th>
                                            <th>Dimensions (L x W x H)</th>
                                            <th style="width: 80px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cargoItemsBody">
                                        <tr>
                                            <td><input type="number" class="form-control form-control-sm" name="cargo_qty[]" min="1" value="1" required></td>
                                            <td><input type="text" class="form-control form-control-sm" name="cargo_package_type[]" placeholder="Box, Bag, Pallet, etc." required></td>
                                            <td><input type="text" class="form-control form-control-sm" name="cargo_dimensions[]" placeholder="e.g., 50cm x 40cm x 30cm" required></td>
                                            <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="bi bi-trash"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div> -->

                            

                            <!-- <button type="button" class="btn btn-sm btn-outline-danger mb-3" id="addCargoItem">
                                <i class="bi bi-plus me-1"></i>Add Item
                            </button> -->

                            @error('cargo_qty')<div class="alert alert-danger">{{ $message }}</div>@enderror

                            <hr>

                            <!-- Sales Order Items Section -->
                            <h6 class="border-bottom pb-2 mb-3"><strong>Sales Order Items (Optional)</strong></h6>
                            <p class="text-muted small">Add items that will be included in the Sales Order created from this quotation.</p>

                            <button type="button" class="btn btn-sm btn-primary mb-3" id="addSOItem">
                                <i class="bi bi-plus me-1"></i>Add Item
                            </button>

                            <div class="table-responsive mb-3">
                                <table class="table table-sm table-bordered" id="soItemsTable">
                                    <thead class="table-primary">
                                        <tr>
                                            <th style="width: 120px;">QTY</th>
                                            <th>DESCRIPTION / PRODUCT</th>
                                            <th style="width: 120px;">UNIT PRICE</th>
                                            <th style="width: 120px;">AMOUNT</th>
                                            <th style="width: 80px;">ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody id="soItemsBody">
                                        <!-- Dynamic rows via JS -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                            <td class="text-end fw-bold" id="soSubtotal">₱ 0.00</td>
                                            <td></td>
                                        </tr>
                                        <tr id="serviceFeeRow" style="display: none;">
                                            <td colspan="3" class="text-end"><strong>Service Fee:</strong></td>
                                            <td class="text-end fw-bold">₱ 50.00</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                            <td class="text-end fw-bold" id="soTotal">â‚± 0.00</td>
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
                                    <i class="bi bi-check me-1"></i>Submit for Logistics Review
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                        <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="bi bi-trash"></i></button></td>
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
                const amount = qty * price;
                row.querySelector('.so-item-amount').textContent = '₱ ' + amount.toFixed(2);
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
                        <select class="form-control form-control-sm so-product" name="so_items[new_${uniqueId}][product_id]" required>
                            ${productSource.innerHTML}
                        </select>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm so-price" name="so_items[new_${uniqueId}][price]" step="0.01" min="0" required style="text-align: right;">
                    </td>
                    <td class="so-item-amount text-end fw-bold">₱ 0.00</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger so-remove-row"><i class="bi bi-trash"></i></button>
                    </td>
                `;

                const qtyInput = row.querySelector('.so-qty');
                const priceInput = row.querySelector('.so-price');
                const productSelect = row.querySelector('.so-product');
                const removeBtn = row.querySelector('.so-remove-row');

                qtyInput.addEventListener('input', () => calculateRow(row));
                priceInput.addEventListener('input', () => calculateRow(row));
                
                productSelect.addEventListener('change', function() {
                    const option = this.options[this.selectedIndex];
                    priceInput.value = option.dataset.price || 0;
                    calculateRow(row);
                });

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

<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .invoice-form { background: #fff; border-radius: 12px; padding: 2rem; box-shadow: 0 4px 24px rgba(0,0,0,0.06); }
        .form-header { margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid #e0e0e0; }
        .form-header .company-info { display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; }
        .form-header .company-logo { width: 60px; height: 60px; background: #ff0000; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 2rem; font-weight: bold; flex-shrink: 0; }
        .form-header .company-name { font-size: 1.25rem; font-weight: 700; color: #333; text-transform: uppercase; }
        .form-header .company-address, .form-header .company-contact { font-size: 0.9rem; color: #666; }
        .form-header .document-title { text-align: center; font-size: 1.75rem; font-weight: 700; color: #333; margin-top: 1rem; letter-spacing: 1px; }

        .customer-section { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 1.5rem; }
        .customer-details, .transaction-details { background: #f8f9fa; padding: 1.25rem; border-radius: 8px; }
        .customer-details h5, .transaction-details h5 { font-weight: 600; color: #333; margin-bottom: 0.75rem; font-size: 0.95rem; }

        .form-group { margin-bottom: 0.75rem; }
        .form-group label { font-weight: 600; color: #333; margin-bottom: 0.25rem; display: block; font-size: 0.9rem; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; border: 1px solid #ddd; border-radius: 6px; padding: 0.5rem 0.75rem; font-size: 0.9rem; }

        .attachments-section { margin-bottom: 1.5rem; padding: 1.5rem; background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-radius: 8px; border: 1px solid #42a5f5; }
        .attachments-section h5 { font-weight: 700; color: #1565c0; }

        .invoice-table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; }
        .invoice-table thead { background: linear-gradient(135deg, #cc0000, #ff0000); color: #fff; }
        .invoice-table th { padding: 0.75rem; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; border: 1px solid #ddd; }
        .invoice-table td { padding: 0.5rem; border: 1px solid #ddd; vertical-align: middle; }
        .invoice-table input, .invoice-table select { width: 100%; border: none; padding: 0.5rem; background: transparent; }
        .invoice-table input:focus, .invoice-table select:focus { outline: 2px solid #ff0000; outline-offset: -2px; background: #fff; }
        .invoice-table tfoot { background: #f8f9fa; font-weight: 600; }
        .invoice-table tfoot td { padding: 0.75rem; border-top: 2px solid #333; }

        .btn-add-row { background: linear-gradient(135deg, #cc0000, #ff3333); color: #fff; border: none; padding: 0.5rem 1.25rem; border-radius: 6px; margin-bottom: 1rem; cursor: pointer; transition: all 0.3s; font-weight: 600; }
        .btn-add-row:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(255,0,0,0.3); }

        .form-actions { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 2px solid #e0e0e0; }

        .platform-select { display: flex; gap: 0.75rem; margin-bottom: 1rem; }
        .platform-option { flex: 1; padding: 1rem; border: 2px solid #ddd; border-radius: 8px; text-align: center; cursor: pointer; transition: all 0.3s; background: #fff; }
        .platform-option:hover { border-color: #ff0000; background: #fff5f5; }
        .platform-option.active { border-color: #ff0000; background: #ff0000; color: #fff; }
        .platform-option .platform-name { font-weight: 700; font-size: 1rem; }
        .platform-option .platform-icon { font-size: 1.5rem; margin-bottom: 0.25rem; display: block; }

        .workflow-note { background: #e7f3ff; border-radius: 8px; border-left: 4px solid #0066cc; padding: 1rem 1.25rem; margin-bottom: 1.5rem; }

        .upload-box { border: 2px dashed #ccc; border-radius: 8px; padding: 1.25rem; text-align: center; transition: all 0.3s; cursor: pointer; position: relative; background: #fff; }
        .upload-box:hover { border-color: #1565c0; background: #e3f2fd; }
        .upload-box input[type="file"] { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
        .upload-box .upload-label { font-weight: 600; color: #555; }
        .upload-box .upload-icon { font-size: 2rem; color: #1565c0; display: block; margin-bottom: 0.5rem; }
        .upload-box.has-file { border-color: #28a745; background: #f0fff4; }
        .upload-box.has-file .upload-label { color: #28a745; }

        /* Invoice List */
        .invoices-list-section { margin-top: 2.5rem; }
        .status-badge { padding: 0.25rem 0.75rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .status-pending_mkt_approval { background: #fff3cd; color: #856404; }
        .status-pending_prod_approval { background: #e0f2ff; color: #004085; }
        .status-picking { background: #d1ecf1; color: #0c5460; }
        .status-pending_si_prep { background: #cce5ff; color: #004085; }
        .status-ready_for_delivery { background: #d4edda; color: #155724; }
        .status-completed { background: #c3e6cb; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .status-gathered { background: #d1ecf1; color: #0c5460; }

        .platform-badge { padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .platform-lazada { background: #0f146d; color: #fff; }
        .platform-shopee { background: #ee4d2d; color: #fff; }
        .platform-tiktok { background: #010101; color: #fff; }

        @media print { .sidebar, .header, .form-actions, .btn-add-row, .attachments-section, .invoices-list-section { display: none !important; } }
        @media (max-width: 768px) { .customer-section { grid-template-columns: 1fr; } .platform-select { flex-direction: column; } }
    </style>
    @endpush

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="las la-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="las la-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Please fix the following:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card invoice-form">
                <!-- Form Header -->
                <div class="form-header">
                    <div class="company-info">
                        <div class="company-logo">C</div>
                        <div>
                            <div class="company-name">CLARETIAN COMMUNICATIONS FOUNDATION INC.</div>
                            <div class="company-address">8 Mayumi St., UP Village, Diliman, Quezon City</div>
                            <div class="company-contact">Tel. No.: 921-3984</div>
                        </div>
                    </div>
                    <div class="document-title">DIRECT INVOICE (E-COM / LAZADA / SHOPEE / TIKTOK)</div>
                </div>

                <form id="diEcomForm" action="{{ route('marketing.direct-invoice.ecom.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- E-Com Platform Selection -->
                    <div class="workflow-note">
                        <label class="form-label font-w600 text-primary mb-2"><i class="las la-store me-1"></i> E-Com Platform *</label>
                        <div class="platform-select">
                            <label class="platform-option" id="opt-lazada">
                                <input type="radio" name="ecom_platform" value="lazada" class="d-none" {{ old('ecom_platform') == 'lazada' ? 'checked' : '' }}>
                                <span class="platform-icon">🛒</span>
                                <span class="platform-name">Lazada</span>
                            </label>
                            <label class="platform-option" id="opt-shopee">
                                <input type="radio" name="ecom_platform" value="shopee" class="d-none" {{ old('ecom_platform') == 'shopee' ? 'checked' : '' }}>
                                <span class="platform-icon">🧡</span>
                                <span class="platform-name">Shopee</span>
                            </label>
                            <label class="platform-option" id="opt-tiktok">
                                <input type="radio" name="ecom_platform" value="tiktok" class="d-none" {{ old('ecom_platform') == 'tiktok' ? 'checked' : '' }}>
                                <span class="platform-icon">🎵</span>
                                <span class="platform-name">TikTok</span>
                            </label>
                        </div>
                        <small class="text-muted"><i class="las la-info-circle me-1"></i> All E-com invoices route to <strong>Marketing Manager/Supervisor</strong> for approval, then to Logistics.</small>
                    </div>

                    <!-- Customer Info -->
                    <div class="customer-section">
                        <div class="customer-details">
                            <h5><i class="las la-user me-1"></i> Customer Information</h5>
                            <div class="form-group">
                                <label>Sold to: *</label>
                                <select class="form-control" name="customer_id" id="customerSelect" required>
                                    <option value="" disabled selected>Select Customer...</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->customer_id }}"
                                            data-address="{{ $customer->billing_address ?? $customer->shipping_address ?? '' }}"
                                            {{ old('customer_id') == $customer->customer_id ? 'selected' : '' }}>
                                            {{ $customer->customer_name }} {{ $customer->company_name ? '('.$customer->company_name.')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Address:</label>
                                <textarea class="form-control" name="billing_address" id="billingAddress" rows="2" placeholder="Customer address...">{{ old('billing_address') }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Remarks:</label>
                                <textarea class="form-control" name="remarks" rows="2" placeholder="Additional notes...">{{ old('remarks') }}</textarea>
                            </div>
                        </div>
                        <div class="transaction-details">
                            <h5><i class="las la-file-invoice me-1"></i> Transaction Details</h5>
                            <div class="form-group">
                                <label>Date:</label>
                                <input type="date" class="form-control" value="{{ date('Y-m-d') }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Platform Order ID:</label>
                                <input type="text" class="form-control" name="platform_order_id" placeholder="e.g., LZD-123456789" value="{{ old('platform_order_id') }}">
                            </div>
                            <div class="form-group">
                                <label>Terms:</label>
                                <input type="text" class="form-control" name="terms"  value="COD/Due on Receipt">
                            </div>
                            <div class="form-group">
                                <label>Prepared By:</label>
                                <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly style="background: #e9ecef;">
                            </div>
                        </div>
                    </div>

                    <!-- Attachments -->
                    <div class="attachments-section">
                        <h5><i class="las la-paperclip me-2"></i>Required Attachments</h5>
                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Pick List *</label>
                                <div class="upload-box" id="plUploadBox">
                                    <input type="file" name="pick_list" id="plFile" accept=".pdf,.jpg,.jpeg,.png,.xlsx,.xls,.csv" required>
                                    <span class="upload-icon"><i class="las la-clipboard-list"></i></span>
                                    <span class="upload-label" id="plLabel">Click or drag Pick List file here</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Shipping Label *</label>
                                <div class="upload-box" id="slUploadBox">
                                    <input type="file" name="shipping_label" id="slFile" accept=".pdf,.jpg,.jpeg,.png" required>
                                    <span class="upload-icon"><i class="las la-shipping-fast"></i></span>
                                    <span class="upload-label" id="slLabel">Click or drag Shipping Label here</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <button type="button" class="btn-add-row" id="addItemBtn">
                        <i class="las la-plus me-1"></i>Add Item
                    </button>
                    <table class="invoice-table" id="itemsTable">
                        <thead>
                            <tr>
                                <th style="width: 80px;">QTY</th>
                                <th style="width: 80px;">UNIT</th>
                                <th>DESCRIPTION / PRODUCT</th>
                                <th style="width: 150px;">UNIT PRICE</th>
                                <th style="width: 150px;">AMOUNT</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="invoiceTableBody">
                            <!-- Dynamic rows via JS -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end text-uppercase"><strong>Grand Total:</strong></td>
                                <td class="text-end fw-bold fs-5" id="grandTotal">₱ 0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>

                    <!-- Actions -->
                    <div class="form-actions">
                        <button type="button" class="btn btn-light border" onclick="window.print()">
                            <i class="las la-print me-1"></i> Print
                        </button>
                        <button type="submit" class="btn btn-success px-4" id="submitBtn">
                            <i class="las la-paper-plane me-1"></i> Submit Invoice for Approval
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Existing E-com Invoices List -->
    @if($invoices->count() > 0)
    <div class="row invoices-list-section">
        <div class="col-12">
            <div class="card p-4" style="border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.06);">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0"><i class="las la-shopping-cart me-2"></i>E-Com Invoices</h4>
                    <span class="badge bg-primary rounded-pill">{{ $invoices->count() }} invoices</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead style="background: #f8f9fa;">
                            <tr>
                                <th>Invoice #</th>
                                <th>Customer</th>
                                <th>Platform</th>
                                <th>Order ID</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Prepared By</th>
                                <th>Date</th>
                                <th>Attachments</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $inv)
                            <tr>
                                <td class="fw-bold">{{ $inv->so_number }}</td>
                                <td>{{ $inv->customer->customer_name ?? 'N/A' }}</td>
                                <td>
                                    <span class="platform-badge platform-{{ $inv->ecom_platform }}">
                                        {{ ucfirst($inv->ecom_platform ?? 'N/A') }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $inv->platform_order_id ?? '—' }}</td>
                                <td>
                                    <span class="status-badge status-{{ $inv->status }}">
                                        @php
                                            $displayStatus = str_replace('_', ' ', $inv->status);
                                            if ($inv->status == 'pending_si_prep') $displayStatus = 'Gathered (In SI Prep)';
                                            if ($inv->status == 'pending_dr_prep') $displayStatus = 'SI Signed (In DR Prep)';
                                        @endphp
                                        {{ ucwords($displayStatus) }}
                                    </span>
                                </td>
                                <td>₱{{ number_format($inv->total_amount, 2) }}</td>
                                <td>{{ $inv->preparedBy->name ?? 'N/A' }}</td>
                                <td>{{ $inv->created_at->format('M d, Y') }}</td>
                                <td>
                                    @if($inv->pick_list_attachment)
                                        <a href="/storage/{{ $inv->pick_list_attachment }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Pick List">
                                            <i class="las la-clipboard-list"></i> PL
                                        </a>
                                    @endif
                                    @if($inv->shipping_label_attachment)
                                        <a href="/storage/{{ $inv->shipping_label_attachment }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Shipping Label">
                                            <i class="las la-shipping-fast"></i> SL
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $canApprove = false;
                                        $userPos = auth()->user()->position ?? '';
                                        $isManager = str_contains($userPos, 'Manager') || str_contains($userPos, 'Supervisor') || $userPos === 'Super Admin';
                                        if ($isManager && $inv->status === 'pending_mkt_approval') {
                                            $canApprove = true;
                                        }
                                    @endphp
                                    @if($canApprove)
                                        <form action="{{ route('marketing.direct-invoice.ecom.approve', $inv->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Approve this invoice? It will be routed to Logistics for picking.')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="las la-check me-1"></i>Approve
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Hidden Product Options for JS -->
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addItemBtn = document.getElementById('addItemBtn');
            const tbody = document.getElementById('invoiceTableBody');
            const productSource = document.getElementById('productSource');
            const grandTotalEl = document.getElementById('grandTotal');
            const customerSelect = document.getElementById('customerSelect');
            const billingAddress = document.getElementById('billingAddress');

            // Platform selection
            document.querySelectorAll('.platform-option').forEach(opt => {
                const radio = opt.querySelector('input[type="radio"]');
                if (radio.checked) opt.classList.add('active');

                opt.addEventListener('click', function() {
                    document.querySelectorAll('.platform-option').forEach(o => o.classList.remove('active'));
                    this.classList.add('active');
                    radio.checked = true;
                });
            });

            // Auto-fill address on customer change
            customerSelect.addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];
                const addr = opt.getAttribute('data-address');
                billingAddress.value = (addr && addr !== '') ? addr : '';
            });

            // File upload UI
            function setupUpload(fileInput, box, label) {
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        label.textContent = this.files[0].name;
                        box.classList.add('has-file');
                    } else {
                        label.textContent = 'Click or drag file here';
                        box.classList.remove('has-file');
                    }
                });
            }
            setupUpload(document.getElementById('plFile'), document.getElementById('plUploadBox'), document.getElementById('plLabel'));
            setupUpload(document.getElementById('slFile'), document.getElementById('slUploadBox'), document.getElementById('slLabel'));

            // Row calculations
            function calculateRow(row) {
                const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                const price = parseFloat(row.querySelector('.price-input').value) || 0;
                const amount = qty * price;
                row.querySelector('.amount-display').textContent = '₱ ' + amount.toFixed(2);
                updateGrandTotal();
            }

            function updateGrandTotal() {
                let total = 0;
                document.querySelectorAll('.amount-display').forEach(el => {
                    total += parseFloat(el.textContent.replace('₱ ', '')) || 0;
                });
                grandTotalEl.textContent = '₱ ' + total.toFixed(2);
            }

            // Add item row
            let rowIndex = 0;
            function addRow() {
                const tr = document.createElement('tr');
                const idx = rowIndex++;

                tr.innerHTML = `
                    <td>
                        <input type="number" class="qty-input" name="items[${idx}][quantity]" min="1" value="1" required style="text-align:center;">
                    </td>
                    <td>
                        <select name="items[${idx}][unit]" style="border:none;">
                            <option value="pcs">pcs</option>
                            <option value="set">set</option>
                            <option value="box">box</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-control product-select" name="items[${idx}][product_id]" required style="border:none;">
                            ${productSource.innerHTML}
                        </select>
                    </td>
                    <td>
                        <input type="number" class="price-input" name="items[${idx}][price]" step="0.01" value="0" required style="text-align:right;">
                    </td>
                    <td class="amount-display fw-bold text-end pe-3">₱ 0.00</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-row border-0"><i class="fa fa-trash"></i></button>
                    </td>
                `;

                const select = tr.querySelector('.product-select');
                const priceInput = tr.querySelector('.price-input');
                const qtyInput = tr.querySelector('.qty-input');
                const removeBtn = tr.querySelector('.remove-row');

                select.addEventListener('change', function() {
                    const opt = this.options[this.selectedIndex];
                    priceInput.value = opt.dataset.price || 0;
                    calculateRow(tr);
                });

                qtyInput.addEventListener('input', () => calculateRow(tr));
                priceInput.addEventListener('input', () => calculateRow(tr));

                removeBtn.addEventListener('click', function() {
                    if (tbody.rows.length > 1) {
                        tr.remove();
                        updateGrandTotal();
                    }
                });

                tbody.appendChild(tr);
            }

            addItemBtn.addEventListener('click', addRow);
            addRow(); // Start with one row

            // Form validation
            document.getElementById('diEcomForm').addEventListener('submit', function(e) {
                if (tbody.rows.length === 0) {
                    e.preventDefault();
                    alert('Please add at least one item.');
                    return;
                }
                const platform = document.querySelector('input[name="ecom_platform"]:checked');
                if (!platform) {
                    e.preventDefault();
                    alert('Please select an E-com platform (Lazada, Shopee, or TikTok).');
                    return;
                }
                document.getElementById('submitBtn').disabled = true;
                document.getElementById('submitBtn').innerHTML = '<i class="las la-spinner la-spin me-1"></i> Submitting...';
            });
        });
    </script>
    @endpush
</x-app-layout>

<x-app-layout :title="'Sales Order'" :sidebar="'production'">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <!-- Form Section -->
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

                    <form id="salesOrderForm" class="form-section">
                        <!-- Customer and Order Details -->
                        <div class="customer-section">
                            <div class="customer-details">
                                <h5>Customer Information</h5>
                                <div class="form-group">
                                    <label>Customer:</label>
                                    <select name="customer" id="formCustomer" class="form-control" required onchange="loadCustomerDetails(this.value)">
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->customer_name }}" data-address="{{ $customer->billing_address ?? $customer->shipping_address ?? '' }}">
                                                {{ $customer->customer_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Address:</label>
                                    <textarea name="address" id="formAddress" placeholder="Enter customer address"></textarea>
                                </div>
                            </div>
                            <div class="order-details">
                                <h5>Order Information</h5>
                                <div class="form-group">
                                    <label>Date:</label>
                                    <input type="date" name="date" id="formDate">
                                </div>
                                <div class="form-group">
                                    <label>S.O. #:</label>
                                    <input type="text" name="so_number" id="formSONumber" placeholder="Enter Sales Order number">
                                </div>
                                <div class="form-group">
                                    <label>Terms:</label>
                                    <input type="text" name="terms" id="formTerms" placeholder="Enter terms">
                                </div>
                                <div class="form-group">
                                    <label>REF#:</label>
                                    <input type="text" name="ref_number" id="formRefNumber" placeholder="Enter reference number">
                                </div>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <button type="button" class="btn-add-row" onclick="addRow()">
                            <i class="las la-plus"></i> Add Row
                        </button>

                        <table class="form-table" id="itemsTable">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">QTY</th>
                                    <th>DESCRIPTION</th>
                                    <th style="width: 120px;">ISBN</th>
                                    <th style="width: 100px;">AREA</th>
                                    <th style="width: 100px;">UNIT PRICE</th>
                                    <th style="width: 100px;">AMOUNT</th>
                                    <th style="width: 80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                <tr>
                                    <td><input type="number" name="quantity[]" placeholder="Qty" min="0" oninput="calculateRowTotal(this)"></td>
                                    <td><input type="text" name="description[]" placeholder="Description"></td>
                                    <td><input type="text" name="isbn[]" placeholder="ISBN"></td>
                                    <td><input type="text" name="area[]" placeholder="Area"></td>
                                    <td><input type="number" name="unit_price[]" placeholder="Unit Price" min="0" step="0.01" oninput="calculateRowTotal(this)"></td>
                                    <td><input type="number" name="amount[]" placeholder="Amount" readonly></td>
                                    <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">Remove</button></td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="button" class="btn btn-light" onclick="resetGeneratedSO()">
                                <i class="las la-undo"></i> Reset
                            </button>
                            <button type="button" class="btn btn-primary" onclick="updateGeneratedSO()">
                                <i class="las la-check"></i> Generate Sales Order
                            </button>
                    </form>
                </div>

                <!-- Generated SO Section -->
                <div class="generated-so-section" id="generatedSOSection" style="display: none;">
                    <div class="d-flex gap-2 mb-3 report-actions">
                        <button type="button" class="btn btn-secondary" onclick="backToForm()">
                            <i class="las la-arrow-left"></i> Back
                        </button>
                        <button type="button" class="btn btn-success" onclick="printReport()">
                            <i class="las la-print"></i> Print Sales Order
                        </button>
                    </div>
                    <div class="generated-so" id="generatedSO">
                        <div class="so-header">
                            <h2 style="text-align: center; font-weight: bold; margin-bottom: 20px;">CLARETIAN COMMUNICATIONS FOUNDATION, INC.</h2>
                            <h3 style="text-align: center; font-weight: bold; margin-bottom: 30px;">SALES ORDER</h3>
                            
                            <div class="so-header-row">
                                <div class="so-header-left">
                                    <div class="so-field">
                                        <strong>SOLD TO:</strong>
                                        <span class="blank-line" id="reportCustomer">___________________________________</span>
                                    </div>
                                    <div class="so-field" style="display: flex;">
                                        <strong>ADDRESS:</strong>
                                        <span class="blank-line" id="reportAddress" style="flex: 1; border-bottom: 1px solid #000; min-height: 20px;"></span>
                                    </div>
                                </div>
                                <div class="so-header-right">
                                    <div class="so-field-right">
                                        <strong>DATE:</strong>
                                        <span class="blank-line" id="reportDate">_________________</span>
                                    </div>
                                    <div class="so-field-right">
                                        <strong>S.O. #:</strong>
                                        <span class="blank-line" id="reportSONumber">_________________</span>
                                    </div>
                                    <div class="so-field-right">
                                        <strong>TERMS:</strong>
                                        <span class="blank-line" id="reportTerms">_________________</span>
                                    </div>
                                    <div class="so-field-right">
                                        <strong>REF #:</strong>
                                        <span class="blank-line" id="reportRefNumber">_________________</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <table class="so-table">
                            <thead>
                                <tr>
                                    <th class="qty-col">QTY</th>
                                    <th class="description-col">DESCRIPTION</th>
                                    <th class="isbn-col">ISBN</th>
                                    <th class="area-col">AREA</th>
                                    <th class="unit-price-col">UNIT PRICE</th>
                                    <th class="amount-col">AMOUNT</th>
                                </tr>
                            </thead>
                            <tbody id="reportItemsTableBody">
                                <!-- Rows injected here -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" style="text-align: right; font-weight: bold;">TOTAL:</td>
                                    <td style="text-align: right; font-weight: bold;" id="reportTotalAmount">0.00</td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="so-footer">
                            <div class="so-footer-left">
                                <div class="so-footer-field">
                                    <strong>Prepared by:</strong>
                                    <span class="blank-line"></span>
                                </div>
                                <div class="so-footer-field">
                                    <strong>Checked by:</strong>
                                    <span class="blank-line"></span>
                                </div>
                                <div class="so-footer-field">
                                    <strong>Approved by:</strong>
                                    <span class="blank-line"></span>
                                </div>
                            </div>
                            <div class="so-footer-right">
                                <div class="so-footer-field" style="margin-top: 50px;">
                                    <span class="blank-line" style="width: 100%; min-width: 100px;"></span>
                                    <div style="text-align: center; margin-top: 5px;">Customer's Signature</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('styles')
    <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <style>
        /* Marketing Division Form Styles */
        .order-form {
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

        .customer-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 1.5rem;
        }

        .customer-details,
        .order-details {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
        }

        .customer-details h5,
        .order-details h5 {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 0.75rem;
        }

        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.25rem;
            display: block;
            font-size: 0.9rem;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
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

        .form-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }

        .form-table thead {
            background: #ff0000;
            color: #fff;
        }

        .form-table th {
            padding: 0.75rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            border: 1px solid #ddd;
        }

        .form-table td {
            padding: 0.5rem;
            border: 1px solid #ddd;
        }

        .form-table input[type="text"],
        .form-table input[type="number"] {
            width: 100%;
            border: none;
            padding: 0.5rem;
            background: transparent;
        }

        .form-table input[type="number"] {
            text-align: right;
        }

        .form-table input:focus {
            outline: 2px solid #ff0000;
            outline-offset: -2px;
            background: #fff;
        }

        .form-table tfoot {
            background: #f8f9fa;
            font-weight: 600;
        }

        .form-table tfoot td {
            padding: 0.75rem;
            border-top: 2px solid #333;
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

        /* Generated SO Section Styles */
        .generated-so-section {
            max-width: 1200px;
            margin: 0;
        }
        .generated-so {
            background: #fff;
            border: 1px solid #ddd;
            padding: 40px;
            margin-top: 30px;
            min-height: 800px;
            font-family: 'Times New Roman', serif;
        }
        .so-header {
            margin-bottom: 30px;
        }
        .so-header-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .so-header-left {
            width: 60%;
        }
        .so-header-right {
            width: 35%;
        }
        .so-field {
            margin-bottom: 15px;
        }
        .so-field strong {
            display: inline-block;
            width: 100px;
        }
        .so-field .blank-line {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 300px;
            padding: 0 5px;
        }
        .so-field-right {
            margin-bottom: 10px;
        }
        .so-field-right strong {
            display: inline-block;
            width: 80px;
        }
        .so-field-right .blank-line {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 200px;
            padding: 0 5px;
        }
        .so-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 11px;
        }
        .so-table th,
        .so-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .so-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .so-table input {
            width: 100%;
            border: none;
            background: transparent;
            padding: 3px;
            font-size: 11px;
        }
        .so-table .qty-col {
            width: 80px;
            text-align: center;
        }
        .so-table .description-col {
            width: 250px;
        }
        .so-table .isbn-col {
            width: 120px;
        }
        .so-table .area-col {
            width: 100px;
        }
        .so-table .unit-price-col {
            width: 100px;
            text-align: right;
        }
        .so-table .amount-col {
            width: 100px;
            text-align: right;
        }
        .so-footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        .so-footer-left {
            width: 60%;
        }
        .so-footer-right {
            width: 35%;
            text-align: right;
        }
        .so-footer-field {
            margin-bottom: 20px;
        }
        .so-footer-field strong {
            display: inline-block;
            width: 120px;
        }
        .so-footer-field .blank-line {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 250px;
            padding: 0 5px;
            margin-top: 40px;
        }
        .so-total {
            font-weight: bold;
            font-size: 14px;
            margin-top: 20px;
        }

        @media print {
            @page {
                size: landscape;
            }
            .sidebar,
            .header,
            .form-actions,
            .report-actions,
            .btn-add-row {
                display: none !important;
            }

            .order-form {
                display: none !important;
                box-shadow: none;
            }

            body {
                background-color: #fff;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        let rowCounter = 1;



        // DOMContentLoaded/Immediate initialization
        function initializeSalesOrder() {
            // Set default date to today
            const dateObj = new Date();
            const year = dateObj.getFullYear();
            const month = String(dateObj.getMonth() + 1).padStart(2, '0');
            const day = String(dateObj.getDate()).padStart(2, '0');
            const formDate = document.getElementById('formDate');
            if (formDate && !formDate.value) {
                formDate.value = `${year}-${month}-${day}`;
            }

            // Generate SO number with FORD prefix
            const formSONumber = document.getElementById('formSONumber');
            if (formSONumber && !formSONumber.value) {
                const randomNum = Math.floor(1000 + Math.random() * 9000);
                formSONumber.value = `FORD-SO-${year}${month}${day}-${randomNum}`;
            }

            // Auto-fill address on selection
            const customerSelect = document.getElementById('formCustomer');
            const addressTextarea = document.getElementById('formAddress');
            
            function updateAddress() {
                if (customerSelect && addressTextarea) {
                    const selectedOption = customerSelect.options[customerSelect.selectedIndex];
                    const address = selectedOption ? selectedOption.getAttribute('data-address') : '';
                    addressTextarea.value = address || '';
                }
            }

            if (customerSelect) {
                customerSelect.addEventListener('change', updateAddress);
                // Trigger once in case a customer is already selected
                updateAddress();
            }

            // jQuery fallback listener for bootstrap-select custom dropdown changes
            setTimeout(function() {
                if (typeof jQuery !== 'undefined') {
                    jQuery('#formCustomer').on('change', function() {
                        const selectedOption = jQuery(this).find('option:selected');
                        const address = selectedOption.attr('data-address') || '';
                        jQuery('#formAddress').val(address);
                    });
                }
            }, 500);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeSalesOrder);
        } else {
            initializeSalesOrder();
        }

        // Load customer details when selected
        function loadCustomerDetails(customerName) {
            const select = document.getElementById('formCustomer');
            if (select && select.selectedIndex >= 0) {
                const selectedOption = select.options[select.selectedIndex];
                const address = selectedOption ? selectedOption.getAttribute('data-address') : '';
                document.getElementById('formAddress').value = address || '';
            }
        }

        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            return date.getDate() + '-' + months[date.getMonth()] + '-' + date.getFullYear().toString().substr(-2);
        }

        function formatNumber(num) {
            return parseFloat(num || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        function calculateRowTotal(input) {
            const row = input.closest('tr');
            const qty = parseFloat(row.querySelector('input[name="quantity[]"]').value) || 0;
            const price = parseFloat(row.querySelector('input[name="unit_price[]"]').value) || 0;
            const amount = qty * price;
            
            row.querySelector('input[name="amount[]"]').value = amount > 0 ? parseFloat(amount).toFixed(2) : '';
        }

        function addRow() {
            rowCounter++;
            const tbody = document.getElementById('itemsTableBody');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td><input type="number" name="quantity[]" placeholder="Qty" min="0" oninput="calculateRowTotal(this)"></td>
                <td><input type="text" name="description[]" placeholder="Description"></td>
                <td><input type="text" name="isbn[]" placeholder="ISBN"></td>
                <td><input type="text" name="area[]" placeholder="Area"></td>
                <td><input type="number" name="unit_price[]" placeholder="Unit Price" min="0" step="0.01" oninput="calculateRowTotal(this)"></td>
                <td><input type="number" name="amount[]" placeholder="Amount" readonly></td>
                <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">Remove</button></td>
            `;
            tbody.appendChild(newRow);
        }

        function removeRow(button) {
            const tbody = document.getElementById('itemsTableBody');
            if (tbody.rows.length > 1) {
                button.closest('tr').remove();
            } else {
                alert('At least one row is required.');
            }
        }

        function updateGeneratedSO() {
            const customer = document.getElementById('formCustomer').value;
            if (!customer) {
                if(window.showAlert) window.showAlert('Please select a Customer.', 'warning');
                else alert('Please select a Customer.');
                return;
            }

            const date = document.getElementById('formDate').value;
            if (!date) {
                if(window.showAlert) window.showAlert('Please select the Order Date.', 'warning');
                else alert('Please select the Order Date.');
                return;
            }

            const tbody = document.getElementById('itemsTableBody');
            const rows = tbody.querySelectorAll('tr');
            let hasValidRow = false;
            let missingRowData = false;
            
            rows.forEach(row => {
                const qty = row.querySelector('input[name="quantity[]"]').value;
                const description = row.querySelector('input[name="description[]"]').value;
                const unitPrice = row.querySelector('input[name="unit_price[]"]').value;
                
                if (qty || description || unitPrice) {
                    if (!qty || !description || !unitPrice) {
                        missingRowData = true;
                    } else {
                        hasValidRow = true;
                    }
                }
            });

            if (missingRowData) {
                if(window.showAlert) window.showAlert('Please complete Quantity, Description, and Unit Price for all entered rows.', 'warning');
                else alert('Please complete Quantity, Description, and Unit Price for all entered rows.');
                return;
            }

            if (!hasValidRow) {
                if(window.showAlert) window.showAlert('Please add at least one item entry.', 'warning');
                else alert('Please add at least one item entry.');
                return;
            }

            document.querySelector('.order-form').style.display = 'none';
            document.getElementById('generatedSOSection').style.display = 'block';
            
            // Generate header
            document.getElementById('reportCustomer').textContent = customer;
            document.getElementById('reportAddress').textContent = document.getElementById('formAddress').value || '';
            
            document.getElementById('reportDate').textContent = formatDate(date);
            document.getElementById('reportSONumber').textContent = document.getElementById('formSONumber').value || '_________________';
            document.getElementById('reportTerms').textContent = document.getElementById('formTerms').value || '_________________';
            document.getElementById('reportRefNumber').textContent = document.getElementById('formRefNumber').value || '_________________';

            // Generate table
            const reportTbody = document.getElementById('reportItemsTableBody');
            reportTbody.innerHTML = '';
            
            let totalAmount = 0;
            
            rows.forEach(row => {
                const qty = row.querySelector('input[name="quantity[]"]').value || '';
                const description = row.querySelector('input[name="description[]"]').value || '';
                const isbn = row.querySelector('input[name="isbn[]"]').value || '';
                const area = row.querySelector('input[name="area[]"]').value || '';
                const unitPrice = parseFloat(row.querySelector('input[name="unit_price[]"]').value) || 0;
                const amount = parseFloat(row.querySelector('input[name="amount[]"]').value) || 0;
                
                totalAmount += amount;
                
                if (qty || description || isbn || area || unitPrice || amount) {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td style="text-align: center;">${qty}</td>
                        <td>${description}</td>
                        <td>${isbn}</td>
                        <td>${area}</td>
                        <td style="text-align: right;">${unitPrice > 0 ? formatNumber(unitPrice) : ''}</td>
                        <td style="text-align: right;">${amount > 0 ? formatNumber(amount) : ''}</td>
                    `;
                    reportTbody.appendChild(tr);
                }
            });
            
            // Minimum 10 blank rows for layout if empty or few items
            const rowsCount = reportTbody.querySelectorAll('tr').length;
            for (let i = rowsCount; i < 10; i++) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                `;
                reportTbody.appendChild(tr);
            }
            
            document.getElementById('reportTotalAmount').textContent = formatNumber(totalAmount);
            
            // Scroll to it
            document.getElementById('generatedSOSection').scrollIntoView({ behavior: 'smooth' });
        }

        function resetGeneratedSO() {
            document.getElementById('salesOrderForm').reset();
            document.getElementById('generatedSOSection').style.display = 'none';
            
            const tbody = document.getElementById('itemsTableBody');
            while (tbody.rows.length > 1) {
                tbody.deleteRow(1);
            }
        }

        function backToForm() {
            document.querySelector('.order-form').style.display = 'block';
            document.getElementById('generatedSOSection').style.display = 'none';
        }

        function printReport() {
            window.print();
        }
    </script>
    @endpush

</x-app-layout>

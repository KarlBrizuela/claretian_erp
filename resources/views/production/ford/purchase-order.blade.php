<x-app-layout :title="'Purchase Order'" :sidebar="'production'">
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
                        <div class="document-title">PURCHASE ORDER</div>
                    </div>

                    <form id="purchaseOrderForm" class="form-section">
                        <!-- Order Details -->
                        <div class="customer-section">
                            <div class="order-details">
                                <h5>Order Information</h5>
                                <div class="form-group">
                                    <label>Date:</label>
                                    <input type="date" name="date" id="formDate">
                                </div>
                                <div class="form-group">
                                    <label>PO No.:</label>
                                    <input type="text" name="po_number" id="formPONumber" placeholder="Enter PO number">
                                </div>
                                <div class="form-group">
                                    <label>Terms:</label>
                                    <input type="text" name="terms" id="formTerms" placeholder="e.g., CHARGE" value="CHARGE">
                                </div>
                                <div class="form-group">
                                    <label>Payment Schedule:</label>
                                    <input type="text" name="payment_schedule" id="formPaymentSchedule" placeholder="e.g., BY TT, ONE YEAR TO PAY" value="BY TT, ONE YEAR TO PAY">
                                </div>
                                <div class="form-group">
                                    <label>Payment Schedule (Line 2):</label>
                                    <input type="text" name="payment_schedule2" id="formPaymentSchedule2" placeholder="e.g., IN 12 EQUAL MONTHLY INSTALLMENTS" value="IN 12 EQUAL MONTHLY INSTALLMENTS">
                                </div>
                            </div>
                            <div class="customer-details">
                                <h5>Vendor Information</h5>
                                <div class="form-group">
                                    <label>Vendor Name:</label>
                                    <input type="text" name="vendor_name" id="formVendorName" placeholder="Enter vendor name">
                                </div>
                                <div class="form-group">
                                    <label>Contact Persons:</label>
                                    <input type="text" name="contact_persons" id="formContactPersons" placeholder="e.g., Fr. Alberto Rossa, CMF / Ms. Divine de Leon">
                                </div>
                                <div class="form-group">
                                    <label>Address:</label>
                                    <textarea name="vendor_address" id="formVendorAddress" placeholder="Enter vendor address"></textarea>
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
                                    <th style="width: 80px;">LANGUAGE</th>
                                    <th style="width: 60px;">FT</th>
                                    <th>DESCRIPTION</th>
                                    <th style="width: 80px;">QUANTITY</th>
                                    <th style="width: 100px;">UNIT PRICE (USD)</th>
                                    <th style="width: 100px;">TOTAL AMOUNT</th>
                                    <th style="width: 100px;">BINDINGS</th>
                                    <th style="width: 150px;">REMARKS</th>
                                    <th style="width: 80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                <tr>
                                    <td><input type="text" name="language[]" placeholder="Language"></td>
                                    <td><input type="text" name="ft[]" placeholder="FT"></td>
                                    <td><input type="text" name="description[]" placeholder="Description"></td>
                                    <td><input type="number" name="quantity[]" placeholder="Qty" min="0" oninput="calculateRowTotal(this)"></td>
                                    <td><input type="number" name="unit_price[]" placeholder="Unit Price" min="0" step="0.01" oninput="calculateRowTotal(this)"></td>
                                    <td><input type="number" name="total_amount[]" placeholder="Total" readonly></td>
                                    <td><input type="text" name="bindings[]" placeholder="Bindings"></td>
                                    <td><input type="text" name="remarks[]" placeholder="Remarks"></td>
                                    <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">Remove</button></td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="button" class="btn btn-light" onclick="resetGeneratedPO()">
                                <i class="las la-undo"></i> Reset
                            </button>
                            <button type="button" class="btn btn-primary" onclick="updateGeneratedPO()">
                                <i class="las la-check"></i> Generate Purchase Order
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Generated PO Section -->
                <div class="generated-po-section" id="generatedPOSection" style="display: none;">
                    <div class="d-flex gap-2 mb-3 report-actions">
                        <button type="button" class="btn btn-secondary" onclick="backToForm()">
                            <i class="las la-arrow-left"></i> Back
                        </button>
                        <button type="button" class="btn btn-success" onclick="printReport()">
                            <i class="las la-print"></i> Print PO
                        </button>
                    </div>
                    <div class="generated-po" id="generatedPO">
                        <div class="company-header">
                            <h2>CLARETIAN COMMUNICATIONS FOUNDATION INC.</h2>
                            <p>8 Mayumi St., U.P. Village, Diliman 1101 Quezon City, Philippines</p>
                            <p>C.P.O. Box 4 U.P. Diliman 1101 Quezon City, Philippines</p>
                            <p>Tel.: 921-3984 • Fax: 921-6205</p>
                        </div>

                        <div class="po-title">PURCHASE ORDER</div>

                        <div class="po-details">
                            <div class="po-details-left">
                                <div class="vendor-section">
                                    <div style="margin-bottom: 5px;">TO: <strong id="reportVendorName">____________________</strong></div>
                                    <div style="margin-left: 30px;">
                                        <div id="reportVendorAddress">_______________________</div>
                                        <div><strong id="reportContactPersons">____________________</strong></div>
                                    </div>
                                </div>
                            </div>
                            <div class="po-details-right">
                                <div style="margin-bottom: 5px;"><strong>DATE:</strong> <span id="reportDate">_____________</span></div>
                                <div style="margin-bottom: 5px;"><strong>PO NO.:</strong> <span id="reportPONumber">_____________</span></div>
                                <div style="margin-bottom: 5px;"><strong>TERMS:</strong> <span id="reportTerms">_____________</span></div>
                            </div>
                        </div>

                        <div style="margin-bottom: 10px;">
                            Please supply us with the following items, to be delivered to our office.
                        </div>

                        <table class="po-table">
                            <thead>
                                <tr>
                                    <th class="language-col">LANGUAGE</th>
                                    <th class="ft-col">FT</th>
                                    <th class="description-col">DESCRIPTION</th>
                                    <th class="quantity-col">QUANTITY</th>
                                    <th class="unit-price-col">UNIT PRICE (USD)</th>
                                    <th class="total-amount-col">TOTAL AMOUNT</th>
                                    <th class="bindings-col">BINDINGS</th>
                                    <th class="remarks-col">REMARKS</th>
                                </tr>
                            </thead>
                            <tbody id="reportItemsTableBody">
                                <!-- Rows injected here -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" style="text-align: right; border: 1px solid #000; padding: 5px; font-weight: bold;">TOTAL:</td>
                                    <td style="text-align: right; border: 1px solid #000; padding: 5px; font-weight: bold;" id="reportTotalAmount">0.00</td>
                                    <td colspan="2" style="border: 1px solid #000; padding: 5px;"></td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="po-footer">
                            <div class="po-footer-row">
                                <strong>PAYMENT SCHEDULE:</strong> <span id="reportPaymentSchedule">_______________________</span>
                            </div>
                            <div class="po-footer-row" style="padding-left: 155px;">
                                <span id="reportPaymentSchedule2">_______________________</span>
                            </div>
                            <div class="po-footer-row" style="margin-top: 15px;">
                                <strong>NOTE:</strong> KINDLY SECURE US THE NECESSARY COMMERCIAL INVOICE.
                            </div>
                        </div>

                        <div class="signature-section">
                            <div class="signature-row" style="display: flex; justify-content: space-between;">
                                <div class="signature-item" style="width: 40%;">
                                    <div style="color:red; font-weight:bold; font-style:italic;">Please sign & email / fax back</div>
                                    <div class="signature-line" style="border-top: 1px solid #000; margin-top: 40px; padding-top: 5px; text-align: center;">
                                        <strong>Signature over printed name</strong>
                                    </div>
                                </div>
                                <div class="signature-item text-right" style="width: 40%; text-align: right;">
                                    <div>Sincerely,</div>
                                    <div class="signature-line" style="border-top: 1px solid #000; margin-top: 40px; padding-top: 5px; text-align: center;">
                                        <strong>SR. ANNA MARIA R. VIOJAN, RMI</strong><br>
                                        Director Treasurer
                                    </div>
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

        /* Generated PO Section Styles (unchanged) */
        .generated-po-section {
            max-width: 1200px;
            margin: 0;
        }
        .generated-po {
            background: #fff;
            border: 1px solid #ddd;
            padding: 40px;
            margin-top: 30px;
            min-height: 800px;
            font-family: 'Times New Roman', serif;
        }
        .po-header {
            margin-bottom: 30px;
        }
        .company-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .company-header h2 {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .company-header p {
            margin: 2px 0;
            font-size: 12px;
        }
        .po-title {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            margin: 20px 0;
        }
        .po-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .po-details-left {
            width: 60%;
        }
        .po-details-right {
            width: 35%;
            text-align: right;
        }
        .vendor-section {
            margin: 20px 0;
        }
        .vendor-section strong {
            display: inline-block;
            width: 120px;
        }
        .po-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 11px;
        }
        .po-table th,
        .po-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        .po-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .po-table input {
            width: 100%;
            border: none;
            background: transparent;
            padding: 3px;
            font-size: 11px;
        }
        .po-table .language-col {
            width: 100px;
        }
        .po-table .ft-col {
            width: 60px;
        }
        .po-table .description-col {
            width: 200px;
        }
        .po-table .quantity-col {
            width: 80px;
            text-align: center;
        }
        .po-table .unit-price-col {
            width: 100px;
            text-align: right;
        }
        .po-table .total-amount-col {
            width: 100px;
            text-align: right;
        }
        .po-table .bindings-col {
            width: 100px;
        }
        .po-table .remarks-col {
            width: 150px;
        }
        .po-footer {
            margin-top: 30px;
        }
        .po-footer-row {
            margin-bottom: 10px;
        }
        .po-footer-row strong {
            display: inline-block;
            width: 150px;
        }
        .signature-section {
            margin-top: 40px;
        }
        .signature-box {
            margin-bottom: 30px;
        }
        .signature-box strong {
            display: block;
            margin-bottom: 10px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 50px;
            padding-top: 5px;
        }
        .signature-row {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .signature-item {
            width: 30%;
        }
        .blank-field {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 200px;
            padding: 0 5px;
            margin: 0 5px;
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

        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            return months[date.getMonth()] + ' ' + date.getDate() + ', ' + date.getFullYear();
        }

        function calculateRowTotal(input) {
            const row = input.closest('tr');
            const qty = parseFloat(row.querySelector('input[name="quantity[]"]').value) || 0;
            const price = parseFloat(row.querySelector('input[name="unit_price[]"]').value) || 0;
            const total = qty * price;
            
            row.querySelector('input[name="total_amount[]"]').value = total > 0 ? total.toFixed(2) : '';
        }

        function addRow() {
            rowCounter++;
            const tbody = document.getElementById('itemsTableBody');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td><input type="text" name="language[]" placeholder="Language"></td>
                <td><input type="text" name="ft[]" placeholder="FT"></td>
                <td><input type="text" name="description[]" placeholder="Description"></td>
                <td><input type="number" name="quantity[]" placeholder="Qty" min="0" oninput="calculateRowTotal(this)"></td>
                <td><input type="number" name="unit_price[]" placeholder="Unit Price" min="0" step="0.01" oninput="calculateRowTotal(this)"></td>
                <td><input type="number" name="total_amount[]" placeholder="Total" readonly></td>
                <td><input type="text" name="bindings[]" placeholder="Bindings"></td>
                <td><input type="text" name="remarks[]" placeholder="Remarks"></td>
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

        function updateGeneratedPO() {
            const date = document.getElementById('formDate').value;
            const vendorName = document.getElementById('formVendorName').value;

            if (!date || !vendorName) {
                if(window.showAlert) window.showAlert('Please enter the Date and Vendor Name.', 'warning');
                else alert('Please enter the Date and Vendor Name.');
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
            document.getElementById('generatedPOSection').style.display = 'block';
            
            // Generate header
            document.getElementById('reportVendorName').textContent = vendorName;
            document.getElementById('reportVendorAddress').textContent = document.getElementById('formVendorAddress').value || '_______________________';
            document.getElementById('reportContactPersons').textContent = document.getElementById('formContactPersons').value || '____________________';
            
            document.getElementById('reportDate').textContent = formatDate(date);
            document.getElementById('reportPONumber').textContent = document.getElementById('formPONumber').value || '_____________';
            document.getElementById('reportTerms').textContent = document.getElementById('formTerms').value || '_____________';
            
            document.getElementById('reportPaymentSchedule').textContent = document.getElementById('formPaymentSchedule').value || '_______________________';
            document.getElementById('reportPaymentSchedule2').textContent = document.getElementById('formPaymentSchedule2').value || '_______________________';

            // Generate table
            const tbody = document.getElementById('itemsTableBody');
            const reportTbody = document.getElementById('reportItemsTableBody');
            reportTbody.innerHTML = '';
            
            let totalAmount = 0;
            const rows = tbody.querySelectorAll('tr');
            
            rows.forEach(row => {
                const language = row.querySelector('input[name="language[]"]').value || '';
                const ft = row.querySelector('input[name="ft[]"]').value || '';
                const description = row.querySelector('input[name="description[]"]').value || '';
                const quantity = row.querySelector('input[name="quantity[]"]').value || '';
                const unitPrice = parseFloat(row.querySelector('input[name="unit_price[]"]').value) || 0;
                const totalAmountRow = parseFloat(row.querySelector('input[name="total_amount[]"]').value) || 0;
                const bindings = row.querySelector('input[name="bindings[]"]').value || '';
                const remarks = row.querySelector('input[name="remarks[]"]').value || '';
                
                totalAmount += totalAmountRow;
                
                if (language || ft || description || quantity || unitPrice || totalAmountRow || bindings || remarks) {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${language}</td>
                        <td style="text-align: center;">${ft}</td>
                        <td>${description}</td>
                        <td style="text-align: center;">${quantity}</td>
                        <td style="text-align: right;">${unitPrice > 0 ? unitPrice.toFixed(2) : ''}</td>
                        <td style="text-align: right;">${totalAmountRow > 0 ? totalAmountRow.toFixed(2) : ''}</td>
                        <td>${bindings}</td>
                        <td>${remarks}</td>
                    `;
                    reportTbody.appendChild(tr);
                }
            });
            
            // Minimum 5 blank rows
            const rowsCount = reportTbody.querySelectorAll('tr').length;
            for (let i = rowsCount; i < 5; i++) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                `;
                reportTbody.appendChild(tr);
            }
            
            document.getElementById('reportTotalAmount').textContent = totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            
            // Scroll to it
            document.getElementById('generatedPOSection').scrollIntoView({ behavior: 'smooth' });
        }

        function resetGeneratedPO() {
            document.getElementById('purchaseOrderForm').reset();
            document.getElementById('generatedPOSection').style.display = 'none';
            
            const tbody = document.getElementById('itemsTableBody');
            while (tbody.rows.length > 1) {
                tbody.deleteRow(1);
            }
        }

        function backToForm() {
            document.querySelector('.order-form').style.display = 'block';
            document.getElementById('generatedPOSection').style.display = 'none';
        }

        function printReport() {
            window.print();
        }
    </script>
    @endpush

</x-app-layout>

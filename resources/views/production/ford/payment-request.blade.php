<x-app-layout :title="'Payment Request'" :sidebar="'production'">
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
                        <div class="document-title">PAYMENT REQUEST</div>
                    </div>

                    <form id="paymentRequestForm" class="form-section">
                        <!-- Payment and Order Details -->
                        <div class="customer-section">
                            <div class="customer-details">
                                <h5>Payment Information</h5>
                                <div class="form-group">
                                    <label>Date:</label>
                                    <input type="date" name="date" id="formDate">
                                </div>
                                <div class="form-group">
                                    <label>Request for Payment to:</label>
                                    <input type="text" name="payment_to" id="formPaymentTo" placeholder="Enter recipient name">
                                </div>
                                <div class="form-group">
                                    <label>Payment for:</label>
                                    <input type="text" name="payment_for" id="formPaymentFor" placeholder="Enter payment purpose">
                                </div>
                            </div>
                            <div class="order-details">
                                <h5>Order Information</h5>
                                <div class="form-group">
                                    <label>Due Date:</label>
                                    <input type="date" name="due_date" id="formDueDate">
                                </div>
                                <div class="form-group">
                                    <label>P.O. #:</label>
                                    <input type="text" name="po_number" id="formPONumber" placeholder="Enter P.O. number">
                                </div>
                                <div class="form-group">
                                    <label>Item Receipt #:</label>
                                    <input type="text" name="item_receipt" id="formItemReceipt" placeholder="Enter item receipt number">
                                </div>
                            </div>
                        </div>

                        <!-- Payment Details Table -->
                        <button type="button" class="btn-add-row" onclick="addRow()">
                            <i class="las la-plus"></i> Add Row
                        </button>

                        <table class="form-table" id="paymentTable">
                            <thead>
                                <tr>
                                    <th style="width: 120px;">DATE</th>
                                    <th style="width: 150px;">REF. NO.</th>
                                    <th>PARTICULARS</th>
                                    <th style="width: 150px;">AMOUNT</th>
                                    <th style="width: 80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="paymentTableBody">
                                <tr>
                                    <td><input type="date" name="payment_date[]"></td>
                                    <td><input type="text" name="ref_no[]" placeholder="Ref. No."></td>
                                    <td><input type="text" name="particulars[]" placeholder="Particulars"></td>
                                    <td><input type="number" name="amount[]" placeholder="Amount" min="0" step="0.01"></td>
                                    <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">Remove</button></td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="button" class="btn btn-light" onclick="resetGeneratedLetter()">
                                <i class="las la-undo"></i> Reset
                            </button>
                            <button type="button" class="btn btn-primary" onclick="updateGeneratedLetter()">
                                <i class="las la-check"></i> Generate Letter
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="generated-letter-section mt-4" id="generatedLetterWrapper" style="display: none;">
                    <div class="card">
                        <div class="card-body">
                            <div class="generated-letter" id="generatedLetter">
                                <div class="memo-header text-center mb-4">
                                    <h3 class="font-weight-bold">PAYMENT REQUEST</h3>
                                </div>
                                
                                <div class="row mb-4">
                                    <div class="col-sm-6">
                                        <div class="mb-2"><strong>Date:</strong> <span id="lblDate" class="blank-field"></span></div>
                                        <div class="mb-2"><strong>Payment to:</strong> <span id="lblPaymentTo" class="blank-field"></span></div>
                                        <div class="mb-2"><strong>Payment for:</strong> <span id="lblPaymentFor" class="blank-field"></span></div>
                                    </div>
                                    <div class="col-sm-6 text-right">
                                        <div class="mb-2"><strong>Due Date:</strong> <span id="lblDueDate" class="blank-field"></span></div>
                                        <div class="mb-2"><strong>PO#:</strong> <span id="lblPONumber" class="blank-field"></span></div>
                                        <div class="mb-2"><strong>Item Receipt#:</strong> <span id="lblItemReceipt" class="blank-field"></span></div>
                                    </div>
                                </div>

                                <table class="payment-table w-100 mb-4">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Ref. No.</th>
                                            <th>Particulars</th>
                                            <th class="text-right">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody id="generatedTableBody">
                                        <!-- Populated via JS -->
                                    </tbody>
                                    <tfoot>
                                        <tr class="total-row">
                                            <td colspan="3" class="text-right"><strong>TOTAL:</strong></td>
                                            <td class="text-right"><strong id="lblTotalAmount">0.00</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                
                                <div class="signature-section d-flex justify-content-between mt-5">
                                    <div class="signature-box text-center">
                                        <div class="blank-line w-100 border-bottom border-dark mb-2" style="height:20px;"></div>
                                        <strong>Prepared By</strong>
                                    </div>
                                    <div class="signature-box text-center">
                                        <div class="blank-line w-100 border-bottom border-dark mb-2" style="height:20px;"></div>
                                        <strong>Checked By</strong>
                                    </div>
                                    <div class="signature-box text-center">
                                        <div class="blank-line w-100 border-bottom border-dark mb-2" style="height:20px;"></div>
                                        <strong>Approved By</strong>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 text-right report-actions">
                                <button type="button" class="btn btn-secondary" onclick="backToForm()"><i class="las la-arrow-left"></i> Back</button>
                                <button type="button" class="btn btn-primary" onclick="printLetter()"><i class="las la-print"></i> Print</button>
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

        /* Generated Letter Section Styles (unchanged) */
        .generated-letter-section {
            max-width: 1000px;
            margin: 0;
        }

        .generated-letter {
            background: #fff;
            border: 1px solid #ddd;
            padding: 40px;
            margin-top: 30px;
            min-height: 600px;
            font-family: 'Times New Roman', serif;
        }

        .memo-header {
            margin-bottom: 20px;
        }

        .memo-header-row {
            margin-bottom: 5px;
        }

        .memo-header-label {
            display: inline-block;
            width: 80px;
            font-weight: bold;
            color: #000;
        }

        .memo-header-value {
            display: inline-block;
            color: #000;
        }

        .memo-body {
            margin: 20px 0;
            line-height: 1.8;
        }

        .memo-body-text {
            margin-bottom: 15px;
        }

        .memo-footer {
            margin-top: 30px;
        }

        .memo-signature {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .memo-signature div {
            margin-bottom: 5px;
        }

        .blank-field {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 200px;
            padding: 0 5px;
            margin: 0 5px;
        }

        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .payment-table th,
        .payment-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .payment-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .payment-table input {
            width: 100%;
            border: none;
            background: transparent;
            padding: 5px;
        }

        .payment-table .date-col {
            width: 120px;
        }

        .payment-table .ref-no-col {
            width: 150px;
        }

        .payment-table .particulars-col {
            width: 300px;
        }

        .payment-table .amount-col {
            width: 150px;
            text-align: right;
        }

        .payment-table .total-row {
            background-color: #FFD700;
            font-weight: bold;
        }

        .remarks-section {
            margin: 20px 0;
        }

        .remarks-section ul {
            list-style: none;
            padding-left: 0;
        }

        .remarks-section li {
            margin-bottom: 10px;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .signature-box {
            width: 30%;
        }

        .signature-box div {
            margin-bottom: 5px;
        }

        .form-table .no-col {
            width: 50px;
            text-align: center;
        }

        .hr-line {
            border-top: 1px solid #000;
            margin: 20px 0;
        }

        @media print {
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
        // Function to format date nicely
        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            return months[date.getMonth()] + ' ' + date.getDate() + ', ' + date.getFullYear();
        }

        function addRow() {
            const tbody = document.getElementById('paymentTableBody');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td><input type="date" name="payment_date[]"></td>
                <td><input type="text" name="ref_no[]" placeholder="Ref. No."></td>
                <td><input type="text" name="particulars[]" placeholder="Particulars"></td>
                <td><input type="number" name="amount[]" placeholder="Amount" min="0" step="0.01"></td>
                <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">Remove</button></td>
            `;
            tbody.appendChild(newRow);
        }

        function removeRow(button) {
            const tbody = document.getElementById('paymentTableBody');
            if (tbody.rows.length > 1) {
                button.closest('tr').remove();
            } else {
                if(window.showAlert) {
                    window.showAlert('At least one row is required.', 'warning');
                } else {
                    alert('At least one row is required.');
                }
            }
        }

        function updateGeneratedLetter() {
            const dateVal = document.getElementById('formDate').value;
            const dueDateVal = document.getElementById('formDueDate').value;
            const paymentToVal = document.getElementById('formPaymentTo').value;

            if (!dateVal || !paymentToVal) {
                if(window.showAlert) window.showAlert('Please fill in Date and Payment to.', 'warning');
                else alert('Please fill in Date and Payment to.');
                return;
            }

            // Date validation logic
            if (dateVal && dueDateVal) {
                const currentDate = new Date(dateVal);
                const dueDate = new Date(dueDateVal);
                
                // Clear time components for accurate date-only comparison
                currentDate.setHours(0,0,0,0);
                dueDate.setHours(0,0,0,0);

                if (currentDate > dueDate) {
                    if(window.showAlert) {
                        window.showAlert('Error: Cannot process. The Date is past the Due Date.', 'error');
                    } else {
                        alert('Error: Cannot process. The Date is past the Due Date.');
                    }
                    return;
                }
            }

            const tbody = document.getElementById('paymentTableBody');
            const rows = tbody.querySelectorAll('tr');
            let hasValidRow = false;
            let missingRowData = false;
            
            rows.forEach(row => {
                const particulars = row.querySelector('input[name="particulars[]"]').value;
                const amount = row.querySelector('input[name="amount[]"]').value;
                
                if (particulars || amount) {
                    if (!particulars || !amount) {
                        missingRowData = true;
                    } else {
                        hasValidRow = true;
                    }
                }
            });

            if (missingRowData) {
                if(window.showAlert) window.showAlert('Please complete Particulars and Amount for all entered rows.', 'warning');
                else alert('Please complete Particulars and Amount for all entered rows.');
                return;
            }

            if (!hasValidRow) {
                if(window.showAlert) window.showAlert('Please add at least one item entry.', 'warning');
                else alert('Please add at least one item entry.');
                return;
            }

            // Populate labels
            document.getElementById('lblDate').textContent = dateVal ? formatDate(dateVal) : '_____________';
            document.getElementById('lblPaymentTo').textContent = document.getElementById('formPaymentTo').value || '_____________';
            document.getElementById('lblPaymentFor').textContent = document.getElementById('formPaymentFor').value || '_____________';
            
            document.getElementById('lblDueDate').textContent = dueDateVal ? formatDate(dueDateVal) : '_____________';
            document.getElementById('lblPONumber').textContent = document.getElementById('formPONumber').value || '_____________';
            document.getElementById('lblItemReceipt').textContent = document.getElementById('formItemReceipt').value || '_____________';

            // Populate table
            const generatedTbody = document.getElementById('generatedTableBody');
            generatedTbody.innerHTML = '';
            
            let totalAmount = 0;

            rows.forEach(row => {
                const date = row.querySelector('input[name="payment_date[]"]').value;
                const refNo = row.querySelector('input[name="ref_no[]"]').value;
                const particulars = row.querySelector('input[name="particulars[]"]').value;
                const amount = parseFloat(row.querySelector('input[name="amount[]"]').value) || 0;

                totalAmount += amount;

                if(date || refNo || particulars || amount > 0) {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${date ? formatDate(date) : ''}</td>
                        <td>${refNo}</td>
                        <td>${particulars}</td>
                        <td class="text-right">${amount.toFixed(2)}</td>
                    `;
                    generatedTbody.appendChild(tr);
                }
            });

            if(generatedTbody.innerHTML === '') {
                 generatedTbody.innerHTML = `<tr><td colspan="4" class="text-center">No particulars added</td></tr>`;
            }

            document.getElementById('lblTotalAmount').textContent = totalAmount.toFixed(2);
            document.querySelector('.order-form').style.display = 'none';
            document.getElementById('generatedLetterWrapper').style.display = 'block';
            
            // Scroll to preview
            document.getElementById('generatedLetterWrapper').scrollIntoView({ behavior: 'smooth' });
        }

        function resetGeneratedLetter() {
            document.getElementById('paymentRequestForm').reset();
            document.getElementById('generatedLetterWrapper').style.display = 'none';
            
            const tbody = document.getElementById('paymentTableBody');
            while (tbody.rows.length > 1) {
                tbody.deleteRow(1);
            }
        }

        function backToForm() {
            document.querySelector('.order-form').style.display = 'block';
            document.getElementById('generatedLetterWrapper').style.display = 'none';
        }

        function printLetter() {
            window.print();
        }
    </script>
    @endpush

</x-app-layout>

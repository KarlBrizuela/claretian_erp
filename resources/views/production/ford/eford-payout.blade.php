<x-app-layout :title="'E-FORD Payout'" :sidebar="'production'">
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
                        <div class="document-title">E-FORD SALES SUMMARY REPORT</div>
                    </div>

                    <form id="efordPayoutForm" class="form-section">
                        <!-- Report Period -->
                        <div class="customer-section">
                            <div class="order-details">
                                <h5>Report Information</h5>
                                <div class="form-group">
                                    <label>Period:</label>
                                    <input type="text" name="period" id="formPeriod" placeholder="e.g., January 1-31, 2025">
                                </div>
                            </div>
                        </div>

                        <!-- Sales Table -->
                        <button type="button" class="btn-add-row" onclick="addRow()">
                            <i class="las la-plus"></i> Add Row
                        </button>

                        <table class="form-table" id="salesTable">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">NO.</th>
                                    <th>ORDER NO.</th>
                                    <th>DATE</th>
                                    <th>SI NO.</th>
                                    <th>CUSTOMER</th>
                                    <th>AMOUNT</th>
                                    <th>FREIGHT</th>
                                    <th>GROSS SALES</th>
                                    <th>PAYMENT METHOD</th>
                                    <th style="width: 80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="salesTableBody">
                                <tr>
                                    <td style="text-align: center;">1</td>
                                    <td><input type="text" name="order_no[]" placeholder="ORDER No."></td>
                                    <td><input type="date" name="date[]"></td>
                                    <td><input type="text" name="si_no[]" placeholder="SI No."></td>
                                    <td><input type="text" name="customer[]" placeholder="Customer"></td>
                                    <td><input type="number" name="amount[]" placeholder="Amount" min="0" step="0.01" oninput="calculateRowGrossSales(this)"></td>
                                    <td><input type="number" name="freight[]" placeholder="Freight" min="0" step="0.01" oninput="calculateRowGrossSales(this)"></td>
                                    <td><input type="number" name="gross_sales[]" placeholder="Gross Sales" readonly></td>
                                    <td><input type="text" name="payment_method[]" placeholder="Payment Method"></td>
                                    <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">Remove</button></td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="button" class="btn btn-light" onclick="resetGeneratedReport()">
                                <i class="las la-undo"></i> Reset
                            </button>
                            <button type="button" class="btn btn-primary" onclick="updateGeneratedReport()">
                                <i class="las la-check"></i> Generate Report
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Generated Report Section -->
                <div class="generated-report-section" id="generatedReportSection" style="display: none;">
                    <div class="d-flex gap-2 mb-3 report-actions">
                        <button type="button" class="btn btn-secondary" onclick="backToForm()">
                            <i class="las la-arrow-left"></i> Back
                        </button>
                        <button type="button" class="btn btn-success" onclick="printReport()">
                            <i class="las la-print"></i> Print Report
                        </button>
                    </div>
                    <div class="generated-report" id="generatedReport">
                        <div class="report-header">
                            <h2>CLARETIAN COMMUNICATIONS FOUNDATION INC.</h2>
                            <h3>E-FORD SALES SUMMARY REPORT</h3>
                            <div>Period <span id="reportPeriod">_____________</span></div>
                        </div>

                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th class="no-col">NO.</th>
                                    <th class="order-no-col">ORDER NO.</th>
                                    <th class="date-col">DATE</th>
                                    <th class="si-no-col">SI NO.</th>
                                    <th class="customer-col">CUSTOMER</th>
                                    <th class="amount-col">AMOUNT</th>
                                    <th class="freight-col">FREIGHT</th>
                                    <th class="gross-sales-col">GROSS SALES</th>
                                    <th class="payment-method-col">PAYMENT METHOD</th>
                                </tr>
                            </thead>
                            <tbody id="reportSalesTableBody">
                                <!-- Rows injected here -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="total-section" style="text-align: right; border: 1px solid #000; padding: 8px;">TOTAL (PHP):</td>
                                    <td class="total-section" style="text-align: right; border: 1px solid #000; padding: 8px;" id="totalAmountPHP">0.00</td>
                                    <td class="total-section" style="text-align: right; border: 1px solid #000; padding: 8px;" id="totalFreightPHP">0.00</td>
                                    <td class="total-section" style="text-align: right; border: 1px solid #000; padding: 8px;" id="totalGrossSalesPHP">0.00</td>
                                    <td class="total-section" style="border: 1px solid #000; padding: 8px;"></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="total-section" style="text-align: right; border: 1px solid #000; padding: 8px;">TOTAL (USD):</td>
                                    <td class="total-section" style="text-align: right; border: 1px solid #000; padding: 8px;" id="totalAmountUSD">0.00</td>
                                    <td class="total-section" style="text-align: right; border: 1px solid #000; padding: 8px;" id="totalFreightUSD">0.00</td>
                                    <td class="total-section" style="text-align: right; border: 1px solid #000; padding: 8px;" id="totalGrossSalesUSD">0.00</td>
                                    <td class="total-section" style="border: 1px solid #000; padding: 8px;"></td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="signature-section" style="margin-top: 50px;">
                            <div class="signature-box" style="float: left; width: 30%;">
                                <div>Prepared by:</div>
                                <div style="border-bottom: 1px solid #000; margin-top: 40px; margin-bottom: 5px;"></div>
                                <strong>MICHELLE MACALABON</strong><br>
                                FORD Clerk
                            </div>
                            <div class="signature-box" style="float: left; width: 30%; margin-left: 5%;">
                                <div>Checked by:</div>
                                <div style="border-bottom: 1px solid #000; margin-top: 40px; margin-bottom: 5px;"></div>
                                <strong>CRISTINA J. GALANG</strong><br>
                                FORD Head
                            </div>
                            <div class="signature-box" style="float: right; width: 30%;">
                                <div>Noted by:</div>
                                <div style="border-bottom: 1px solid #000; margin-top: 40px; margin-bottom: 5px;"></div>
                                <strong>FR. DENNIS G. TAMAYO, CMF</strong><br>
                                Executive Director
                            </div>
                            <div style="clear: both;"></div>
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

        /* Generated Report Section Styles (unchanged) */
        .generated-report-section {
            max-width: 1200px;
            margin: 0;
        }
        .generated-report {
            background: #fff;
            border: 1px solid #ddd;
            padding: 40px;
            margin-top: 30px;
            min-height: 600px;
            font-family: 'Times New Roman', serif;
        }
        .report-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .report-header h2 {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .report-header h3 {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 12px;
        }
        .report-table th,
        .report-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .report-table th {
            background-color: #4A90E2;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }
        .report-table .no-col {
            width: 40px;
            text-align: center;
        }
        .report-table .order-no-col {
            width: 100px;
        }
        .report-table .date-col {
            width: 100px;
        }
        .report-table .si-no-col {
            width: 100px;
        }
        .report-table .customer-col {
            width: 200px;
        }
        .report-table .amount-col {
            width: 120px;
            text-align: right;
        }
        .report-table .freight-col {
            width: 100px;
            text-align: right;
        }
        .report-table .gross-sales-col {
            width: 120px;
            text-align: right;
        }
        .report-table .payment-method-col {
            width: 120px;
        }
        .total-section {
            background-color: #FFD700;
            font-weight: bold;
        }
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 30%;
        }
        .signature-box div {
            margin-bottom: 5px;
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

        // Function to format date
        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            return months[date.getMonth()] + ' ' + date.getDate() + ', ' + date.getFullYear();
        }

        // Function to calculate gross sales
        function calculateGrossSales(amount, freight) {
            const amt = parseFloat(amount) || 0;
            const frt = parseFloat(freight) || 0;
            return (amt + frt).toFixed(2);
        }

        // Function to add a new row
        function addRow() {
            rowCounter++;
            const tbody = document.getElementById('salesTableBody');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td style="text-align: center;">${rowCounter}</td>
                <td><input type="text" name="order_no[]" placeholder="ORDER No."></td>
                <td><input type="date" name="date[]"></td>
                <td><input type="text" name="si_no[]" placeholder="SI No."></td>
                <td><input type="text" name="customer[]" placeholder="Customer"></td>
                <td><input type="number" name="amount[]" placeholder="Amount" min="0" step="0.01" oninput="calculateRowGrossSales(this)"></td>
                <td><input type="number" name="freight[]" placeholder="Freight" min="0" step="0.01" oninput="calculateRowGrossSales(this)"></td>
                <td><input type="number" name="gross_sales[]" placeholder="Gross Sales" readonly></td>
                <td><input type="text" name="payment_method[]" placeholder="Payment Method"></td>
                <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">Remove</button></td>
            `;
            tbody.appendChild(newRow);
        }

        // Function to remove a row
        function removeRow(button) {
            const tbody = document.getElementById('salesTableBody');
            if (tbody.rows.length > 1) {
                button.closest('tr').remove();
                renumberRows();
                updateGeneratedReport();
            } else {
                alert('At least one row is required.');
            }
        }

        // Function to renumber rows
        function renumberRows() {
            const tbody = document.getElementById('salesTableBody');
            const rows = tbody.querySelectorAll('tr');
            rows.forEach((row, index) => {
                row.querySelector('td:first-child').textContent = index + 1;
            });
            rowCounter = rows.length;
        }

        // Function to calculate gross sales for a row
        function calculateRowGrossSales(input) {
            const row = input.closest('tr');
            const amount = row.querySelector('input[name="amount[]"]').value || 0;
            const freight = row.querySelector('input[name="freight[]"]').value || 0;
            const grossSales = calculateGrossSales(amount, freight);
            row.querySelector('input[name="gross_sales[]"]').value = grossSales;
        }

        // Function to update generated report
        function updateGeneratedReport() {
            const formPeriod = document.getElementById('formPeriod').value;
            if (!formPeriod) {
                if(window.showAlert) window.showAlert('Please enter the Report Period.', 'warning');
                else alert('Please enter the Report Period.');
                return;
            }

            const tbody = document.getElementById('salesTableBody');
            const rows = tbody.querySelectorAll('tr');
            let hasValidRow = false;
            let missingRowData = false;
            
            rows.forEach(row => {
                const customer = row.querySelector('input[name="customer[]"]').value;
                const amount = row.querySelector('input[name="amount[]"]').value;
                
                if (customer || amount) {
                    if (!customer || !amount) {
                        missingRowData = true;
                    } else {
                        hasValidRow = true;
                    }
                }
            });

            if (missingRowData) {
                if(window.showAlert) window.showAlert('Please complete Customer and Amount fields for all entered rows.', 'warning');
                else alert('Please complete Customer and Amount fields for all entered rows.');
                return;
            }

            if (!hasValidRow) {
                if(window.showAlert) window.showAlert('Please add at least one sales entry.', 'warning');
                else alert('Please add at least one sales entry.');
                return;
            }

            document.querySelector('.order-form').style.display = 'none';
            document.getElementById('generatedReportSection').style.display = 'block';
            
            // Update period
            document.getElementById('reportPeriod').textContent = formPeriod;
            
            // Update sales table
            const tbody = document.getElementById('salesTableBody');
            const reportTbody = document.getElementById('reportSalesTableBody');
            const rows = tbody.querySelectorAll('tr');
            
            // Clear existing rows in report table
            reportTbody.innerHTML = '';
            
            let totalAmount = 0;
            let totalFreight = 0;
            let totalGrossSales = 0;
            
            // Add rows from form to report
            rows.forEach((row, index) => {
                const orderNo = row.querySelector('input[name="order_no[]"]').value || '_____________';
                const date = row.querySelector('input[name="date[]"]').value;
                const formattedDate = date ? formatDate(date) : '_____________';
                const siNo = row.querySelector('input[name="si_no[]"]').value || '_____________';
                const customer = row.querySelector('input[name="customer[]"]').value || '_____________';
                const amount = row.querySelector('input[name="amount[]"]').value || '0';
                const freight = row.querySelector('input[name="freight[]"]').value || '0';
                const grossSales = row.querySelector('input[name="gross_sales[]"]').value || '0';
                const paymentMethod = row.querySelector('input[name="payment_method[]"]').value || '_____________';
                
                // Calculate totals
                totalAmount += parseFloat(amount) || 0;
                totalFreight += parseFloat(freight) || 0;
                totalGrossSales += parseFloat(grossSales) || 0;
                
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${orderNo}</td>
                    <td>${formattedDate}</td>
                    <td>${siNo}</td>
                    <td>${customer}</td>
                    <td style="text-align: right;">$${amount || '_____________'}</td>
                    <td style="text-align: right;">$${freight || '_____________'}</td>
                    <td style="text-align: right;">$${grossSales || '_____________'}</td>
                    <td>${paymentMethod}</td>
                `;
                reportTbody.appendChild(newRow);
            });
            
            // Update totals
            document.getElementById('totalAmountPHP').textContent = totalAmount.toFixed(2) || '_____________';
            document.getElementById('totalFreightPHP').textContent = totalFreight.toFixed(2) || '_____________';
            document.getElementById('totalGrossSalesPHP').textContent = totalGrossSales.toFixed(2) || '_____________';
            
            document.getElementById('totalAmountUSD').textContent = totalAmount.toFixed(2) || '_____________';
            document.getElementById('totalFreightUSD').textContent = totalFreight.toFixed(2) || '_____________';
            document.getElementById('totalGrossSalesUSD').textContent = totalGrossSales.toFixed(2) || '_____________';
        }

        // Function to reset generated report
        function resetGeneratedReport() {
            document.getElementById('efordPayoutForm').reset();
            document.getElementById('generatedReportSection').style.display = 'none';
            document.getElementById('reportPeriod').textContent = '_____________';
            const reportTbody = document.getElementById('reportSalesTableBody');
            reportTbody.innerHTML = '';
            
            document.getElementById('totalAmountPHP').textContent = '0.00';
            document.getElementById('totalFreightPHP').textContent = '0.00';
            document.getElementById('totalGrossSalesPHP').textContent = '0.00';
            document.getElementById('totalAmountUSD').textContent = '0.00';
            document.getElementById('totalFreightUSD').textContent = '0.00';
            document.getElementById('totalGrossSalesUSD').textContent = '0.00';

            // Reset table to 1 row
            const tbody = document.getElementById('salesTableBody');
            while (tbody.rows.length > 1) {
                tbody.deleteRow(1);
            }
            tbody.rows[0].querySelectorAll('input').forEach(input => input.value = '');
            rowCounter = 1;
        }

        // Function to go back to form
        function backToForm() {
            document.querySelector('.order-form').style.display = 'block';
            document.getElementById('generatedReportSection').style.display = 'none';
        }

        // Function to print report
        function printReport() {
            window.print();
        }

        // Auto-update on input change
        document.addEventListener('DOMContentLoaded', function() {
            const periodInput = document.getElementById('formPeriod');
            if (periodInput) {
                periodInput.addEventListener('input', updateGeneratedReport);
            }

            // Add event listeners to table inputs
            document.addEventListener('input', function(e) {
                if (e.target.matches('input[name="order_no[]"], input[name="date[]"], input[name="si_no[]"], input[name="customer[]"], input[name="amount[]"], input[name="freight[]"], input[name="payment_method[]"]')) {
                    updateGeneratedReport();
                }
            });
        });
    </script>
    @endpush
</x-app-layout>

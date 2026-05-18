<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
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
        }
        .form-header .company-details { flex: 1; }
        .form-header .company-name { font-size: 1.25rem; font-weight: 700; color: #333; margin-bottom: 0.25rem; text-transform: uppercase; }
        .form-header .document-title { text-align: center; font-size: 1.75rem; font-weight: 700; color: #333; margin-top: 1rem; letter-spacing: 1px; }
        
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
        .form-info-item { display: flex; align-items: center; gap: 0.75rem; flex: 1; min-width: 200px; }
        .form-info-item label { font-weight: 600; color: #333; margin: 0; min-width: 80px; }
        .form-info-item .form-control { border: 1px solid #ddd; border-radius: 4px; padding: 0.5rem; flex: 1; }
        
        .receipt-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }
        .receipt-table thead { background: #ff0000; color: #fff; }
        .receipt-table th { padding: 0.75rem; font-weight: 600; font-size: 0.9rem; border: 1px solid #ddd; }
        .receipt-table td { padding: 0.5rem; border: 1px solid #ddd; }
        .receipt-table input { width: 100%; border: none; padding: 0.5rem; background: transparent; }
        .receipt-table input:focus { outline: 2px solid #ff0000; background: #fff; }
        
        .signature-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 2px solid #e0e0e0;
        }
        .signature-box { display: flex; flex-direction: column; }
        .signature-box label { font-weight: 600; margin-bottom: 0.5rem; }
        .signature-box input { border: 1px solid #ddd; border-radius: 4px; padding: 0.5rem; margin-bottom: 2rem; }
        .signature-line { border-top: 1px solid #333; width: 200px; margin: 0 auto; padding-top: 0.5rem; text-align: center; font-size: 0.75rem; }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 2px solid #e0e0e0;
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12">
            <div class="card receipt-form">
                <div class="form-header">
                    <div class="company-info">
                        <div class="company-logo">C</div>
                        <div class="company-details">
                            <div class="company-name">CLARETIAN COMMUNICATIONS FOUNDATION INC.</div>
                            <div class="company-address">8 Mayumi St., UP Village, Diliman, Quezon City</div>
                        </div>
                    </div>
                    <div class="document-title">DELIVERY RECEIPT</div>
                    <div class="text-center text-muted small fw-bold mb-1">NON-VAT REGISTERED</div>
                    <div class="text-center extra-small text-muted italic mb-2">"This document is not valid for claim of input taxes."</div>
                </div>

                <div class="form-info-row">
                    <div class="form-info-item">
                        <label>DR No.:</label>
                        <input type="text" class="form-control" id="receiptNumber" placeholder="Enter DR number">
                    </div>
                    <div class="form-info-item">
                        <label>Date:</label>
                        <input type="date" class="form-control" id="receiptDate">
                    </div>
                    <div class="form-info-item">
                        <label>Sales Order:</label>
                        <select class="form-control" id="salesOrder" onchange="loadSalesOrderDetails(this.value)">
                            <option value="">Select Sales Order</option>
                            <option value="1">SO-2026-001</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Delivered To:</label>
                    <select class="form-control" id="recipient">
                        <option value="">Select Customer</option>
                        <option value="1">National Book Store</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Delivery Address:</label>
                    <textarea class="form-control" rows="2" placeholder="Enter delivery address"></textarea>
                </div>

                <button type="button" class="btn btn-danger btn-sm mb-3" onclick="addRow()">+ Add Row</button>
                
                <div class="table-responsive">
                    <table class="receipt-table">
                        <thead>
                            <tr>
                                <th style="width: 100px;">QUANTITY</th>
                                <th>DESCRIPTION</th>
                                <th style="width: 150px; text-align: right;">UNIT PRICE</th>
                                <th style="width: 150px; text-align: right;">AMOUNT</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="receiptTableBody">
                            <tr>
                                <td><input type="number" class="qty-input" value="0"></td>
                                <td><input type="text" placeholder="Product description"></td>
                                <td><input type="number" class="price-input" value="0.00" step="0.01"></td>
                                <td><input type="number" class="amount-input" value="0.00" readonly></td>
                                <td class="text-center"><button type="button" class="btn btn-xs sharp btn-danger" onclick="removeRow(this)"><i class="fa fa-times"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="signature-section">
                    <div class="signature-box">
                        <label>Prepared by:</label>
                        <input type="text" value="Johndoe">
                        <div class="signature-line">SIGNATURE</div>
                    </div>
                    <div class="signature-box">
                        <label>Received by:</label>
                        <input type="text">
                        <div class="signature-line">SIGNATURE</div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-light" onclick="window.print()">Print</button>
                    <button type="button" class="btn btn-primary" onclick="alert('Saved')">Save</button>
                    <button type="button" class="btn btn-success" onclick="alert('Submitted')">Submit</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('receiptDate').value = new Date().toISOString().split('T')[0];
            document.getElementById('receiptTableBody').addEventListener('input', function(e) {
                if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) {
                    const row = e.target.closest('tr');
                    const qty = row.querySelector('.qty-input').value;
                    const price = row.querySelector('.price-input').value;
                    row.querySelector('.amount-input').value = (qty * price).toFixed(2);
                }
            });
        });

        function addRow() {
            const tbody = document.getElementById('receiptTableBody');
            const newRow = tbody.rows[0].cloneNode(true);
            newRow.querySelectorAll('input').forEach(i => i.value = i.defaultValue);
            tbody.appendChild(newRow);
        }

        function removeRow(btn) {
            if (document.getElementById('receiptTableBody').rows.length > 1) btn.closest('tr').remove();
        }
    </script>
    @endpush
</x-app-layout>

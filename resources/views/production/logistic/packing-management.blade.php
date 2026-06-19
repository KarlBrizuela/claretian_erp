<x-app-layout :title="'Packing Management'" :sidebar="'production'">
    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }
        .status-ready { background-color: #fff3cd; color: #856404; }
        .status-in-progress { background-color: #cce5ff; color: #004085; }
        .status-packed { background-color: #d4edda; color: #155724; }
        .status-partial { background-color: #e2e3e5; color: #383d41; }
    </style>
    @endpush

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0 d-block d-sm-flex">
                    <div>
                        <h4 class="fs-24 mb-0 text-black">Packing Management</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="packingTable" class="display" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>SO #</th>
                                    <th>Customer</th>
                                    <th>SI Signed</th>
                                    <th>Total Items</th>
                                    <th>Packed Items</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($packingOrders as $order)
                                @php
                                    $packingData = json_decode($order->packing_data ?? '{}', true);
                                    $packedCount = count(array_filter($packingData, function($item) { return ($item['status'] ?? null) === 'Packed'; }));
                                    $totalItems = $order->items->count();
                                    
                                    if($packedCount === 0) {
                                        $statusClass = 'status-ready';
                                        $statusText = 'Ready for Packing';
                                    } elseif($packedCount === $totalItems && $totalItems > 0) {
                                        $statusClass = 'status-packed';
                                        $statusText = 'Fully Packed';
                                    } else {
                                        $statusClass = 'status-partial';
                                        $statusText = 'Partially Packed';
                                    }
                                @endphp
                                <tr>
                                    <td><strong>{{ $order->so_number }}</strong></td>
                                    <td>{{ $order->customer->customer_name ?? 'N/A' }}</td>
                                    <td>{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('M d, Y') : 'N/A' }}</td>
                                    <td>{{ $totalItems }}</td>
                                    <td><strong>{{ $packedCount }}/{{ $totalItems }}</strong></td>
                                    <td class="fw-bold">₱{{ number_format($order->items->sum('subtotal'), 2) }}</td>
                                    <td><span class="status-badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-danger shadow view-order-btn"
                                                    data-order-id="{{ $order->id }}"
                                                    data-so-number="{{ $order->so_number }}"
                                                    data-customer="{{ $order->customer->customer_name ?? 'N/A' }}"
                                                    data-date="{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}"
                                                    data-signed="{{ $order->signed_at ? \Carbon\Carbon::parse($order->signed_at)->format('Y-m-d') : '' }}"
                                                    title="View Details"
                                                    style="background: #ff0000; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-eye" style="font-size: 0.9rem;"></i>
                                            </button>
                                            <button type="button" class="btn btn-success shadow mark-packed-btn"
                                                    data-order-id="{{ $order->id }}"
                                                    data-so-number="{{ $order->so_number }}"
                                                    title="Mark as Packed"
                                                    style="background: #28a745; border: none; padding: 0.4rem 0.5rem; min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-check" style="font-size: 0.9rem;"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Detail Modal -->
    <div id="orderDetailModal" class="modal-backdrop-packing" style="display: none;">
        <div class="modal-content-packing">
            <div class="modal-header-packing">
                <h3 id="modalTitle" style="margin: 0; color: #000;">Packing Details</h3>
                <button type="button" class="modal-close-btn" id="closeDetailBtn">&times;</button>
            </div>
            <div class="modal-body-packing">
                <div class="order-info-section">
            <div class="order-info-box">
                <h5>Order Information</h5>
                <div class="form-group">
                    <label>Sales Order Number:</label>
                    <input type="text" id="detailSONumber" readonly>
                </div>
                <div class="form-group">
                    <label>Order Date:</label>
                    <input type="text" id="detailOrderDate" readonly>
                </div>
                <div class="form-group">
                    <label>Customer:</label>
                    <input type="text" id="detailCustomerName" readonly>
                </div>
            </div>
            <div class="order-info-box">
                <h5>Packing Information</h5>
                <div class="form-group">
                    <label>SI Signed Date:</label>
                    <input type="text" id="siSignedDate" readonly>
                </div>
                <div class="form-group">
                    <label>Packing Status:</label>
                    <select id="packingStatus" class="form-control">
                        <option value="not_started">Not Started</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Prepared By:</label>
                    <input type="text" id="preparedBy" value="{{ auth()->user()->name ?? 'N/A' }}" readonly>
                </div>
            </div>
        </div>

                <!-- Packing Items Table -->
                <div class="packing-scan-section">
                    <div class="form-group">
                        <label for="barcodeScannerInput">Barcode Scanner:</label>
                        <input type="text" id="barcodeScannerInput" class="form-control" placeholder="Scan book barcode here" autocomplete="off">
                    </div>
                    <div id="barcodeScanMessage" class="barcode-scan-message">Ready to scan</div>
                </div>

                <h5 style="margin-bottom: 1rem; margin-top: 1.5rem; font-weight: 600;">Items to Pack</h5>
                <div class="table-wrapper-packing">
                    <table class="packing-table">
                        <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Product</th>
                    <th style="width: 120px;">Qty to Pack</th>
                    <th style="width: 120px;">Unit Price</th>
                    <th style="width: 120px;">Subtotal</th>
                    <th style="width: 100px;">Packed Qty</th>
                    <th style="width: 120px;">Status</th>
                    <th style="width: 150px;">Notes</th>
                    <th style="width: 120px;">Packed Date</th>
                </tr>
            </thead>
            <tbody id="packingTableBody">
                <!-- Filled by JS -->
            </tbody>
        </table>
                </div>

        <!-- Summary Section -->
        <div class="order-info-section" style="margin-top: 1.5rem;">
            <div class="order-info-box">
                <h5>Packing Summary</h5>
                <div class="form-group">
                    <label>Total Items:</label>
                    <input type="text" id="totalItems" value="0" readonly>
                </div>
                <div class="form-group">
                    <label>Items Packed:</label>
                    <input type="text" id="itemsPacked" value="0" readonly>
                </div>
                <div class="form-group">
                    <label>Packing Progress:</label>
                    <div class="progress" style="height: 25px;">
                        <div id="packingProgressBar" class="progress-bar bg-warning" role="progressbar" style="width: 0%">
                            <span id="packingPercent">0%</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="order-info-box">
                <h5>Actions</h5>
                <div class="form-group">
                    <button type="button" class="btn btn-success" style="width: 100%; margin-bottom: 0.5rem; background: #ffc107; color: #000; border: none; padding: 0.75rem 2rem; border-radius: 6px; cursor: pointer; font-weight: 600;" id="savePackingBtn" onclick="savePackingData()">
                        <i class="las la-save"></i> Save Packing
                    </button>
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-primary-custom close-modal-btn" style="width: 100%;" id="closeDetailsActionBtn">
                        <i class="las la-times"></i> Close Details
                    </button>
                </div>
            </div>
        </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        /* Modal Styles */
        .modal-backdrop-packing {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .modal-content-packing {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 95vw;
            max-height: 90vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header-packing {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #e9ecef;
            background: #f8f9fa;
        }

        .modal-close-btn {
            background: none;
            border: none;
            font-size: 1.8rem;
            cursor: pointer;
            color: #999;
            padding: 0;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close-btn:hover {
            color: #ff0000;
        }

        .modal-body-packing {
            padding: 2rem;
            overflow-y: auto;
            flex: 1;
        }

        .table-wrapper-packing {
            overflow-x: auto;
            margin-bottom: 1rem;
        }

        .order-info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .order-info-box {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid #ff0000;
        }

        .order-info-box h5 {
            color: #333;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #555;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.95rem;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #ff0000;
            box-shadow: 0 0 0 3px rgba(255,0,0,0.1);
        }

        .form-group input:readonly {
            background: #e9ecef;
            cursor: not-allowed;
        }

        .packing-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
            background: #fff;
        }

        .packing-table th {
            padding: 0.75rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.85rem;
            border: 1px solid #ddd;
            text-transform: uppercase;
        }

        .packing-table td {
            padding: 0.5rem;
            border: 1px solid #ddd;
        }

        .packing-table input[type="number"],
        .packing-table input[type="text"],
        .packing-table input[type="date"],
        .packing-table select {
            width: 100%;
            border: none;
            padding: 0.5rem;
            background: transparent;
            font-size: 0.9rem;
        }

        .packing-table input:focus,
        .packing-table select:focus {
            outline: 2px solid #ffc107;
            outline-offset: -2px;
            background: #fff;
        }

        .packing-scan-section {
            background: #f8f9fa;
            border-left: 4px solid #ffc107;
            border-radius: 8px;
            padding: 1rem 1.25rem;
            margin-top: 1.5rem;
        }

        .packing-scan-section .form-group {
            margin-bottom: 0.5rem;
        }

        .barcode-scan-message {
            color: #555;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .barcode-scan-message.success {
            color: #155724;
        }

        .barcode-scan-message.error {
            color: #b00020;
        }

        .packing-table tr.item-packed {
            background: #e8f5e9;
        }

        .packing-table tr.item-not-packed {
            background: #ffe5e5;
        }

        .packing-table tr.item-scanned {
            animation: scannedPulse 0.7s ease-out;
        }

        @keyframes scannedPulse {
            0% { box-shadow: inset 0 0 0 3px rgba(40, 167, 69, 0.8); }
            100% { box-shadow: inset 0 0 0 0 rgba(40, 167, 69, 0); }
        }

        .btn-primary-custom {
            background: #ff0000;
            color: #fff;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary-custom:hover {
            background: #ff3333;
            box-shadow: 0 4px 12px rgba(255,0,0,0.2);
        }

        .btn-secondary-custom {
            background: #6c757d;
            color: #fff;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }

        .btn-secondary-custom:hover {
            background: #5a6268;
        }

        .progress {
            background-color: #e9ecef;
            border-radius: 4px;
        }

        @media print {
            .sidebar, .header, .alert, .card-header { display: none !important; }
            .card { border: none !important; box-shadow: none !important; }
            .modal-backdrop-packing { background: rgba(0, 0, 0, 0) !important; z-index: 1 !important; }
            .modal-content-packing { max-width: 100% !important; box-shadow: none !important; animation: none !important; }
            body { background: #fff !important; }
        }

        .btn-xs {
            padding: 0.35rem 0.6rem !important;
            font-size: 0.8rem !important;
        }

        .btn-xs i {
            font-size: 1rem !important;
        }

        .gap-2 {
            gap: 0.5rem;
        }

        @media (max-width: 1200px) {
            .modal-content-packing {
                max-width: 98vw;
                max-height: 95vh;
            }
            .order-info-section {
                grid-template-columns: 1fr;
            }
            .packing-table {
                font-size: 0.85rem;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        let currentOrderId = null;
        let currentOrderItems = [];
        let barcodeScanTimer = null;

        // Initialize DataTable
        $(document).ready(function() {
            $('#packingTable').DataTable({
                order: [[2, 'desc']],
                pageLength: 25,
                responsive: true
            });
        });

        // Mark as Packed Button Click
        document.querySelectorAll('.mark-packed-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const orderId = this.dataset.orderId;
                const soNumber = this.dataset.soNumber;
                if (confirm(`Mark all items in ${soNumber} as packed?`)) {
                    markOrderAsPacked(orderId, soNumber);
                }
            });
        });

        // View Order Button Click
        document.querySelectorAll('.view-order-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                currentOrderId = this.dataset.orderId;
                loadPackingOrder(currentOrderId);
            });
        });

        // Close Detail Modal
        document.getElementById('closeDetailBtn').addEventListener('click', function() {
            closePackingDetailsModal();
        });

        // Close modal button inside the modal
        document.querySelectorAll('.close-modal-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                closePackingDetailsModal();
            });
        });

        // Close modal when clicking outside
        document.getElementById('orderDetailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePackingDetailsModal();
            }
        });

        function closePackingDetailsModal() {
            document.getElementById('orderDetailModal').style.display = 'none';
            currentOrderId = null;
            currentOrderItems = [];
            document.getElementById('barcodeScannerInput').value = '';
        }

        const barcodeScannerInput = document.getElementById('barcodeScannerInput');
        barcodeScannerInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                processPackingBarcode(this.value);
            }
        });

        barcodeScannerInput.addEventListener('input', function() {
            clearTimeout(barcodeScanTimer);
            barcodeScanTimer = setTimeout(() => {
                if (this.value.trim().length >= 6) {
                    processPackingBarcode(this.value);
                }
            }, 250);
        });

        function markOrderAsPacked(orderId, soNumber) {
            // Fetch order data first
            fetch(`/production/logistic/packing/${orderId}/data`)
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert('Error loading order data');
                        return;
                    }

                    const order = data.order;
                    const packingItems = [];
                    const today = new Date().toISOString().split('T')[0];

                    // Create packing items with all qty marked as packed
                    order.items.forEach((item, index) => {
                        packingItems.push({
                            index: index,
                            packed_qty: item.quantity,
                            status: 'Packed',
                            notes: 'Auto-marked as packed',
                            packed_date: today,
                        });
                    });

                    const payload = {
                        order_id: orderId,
                        packing_status: 'completed',
                        items: packingItems,
                    };

                    // Save packing data
                    fetch('/production/logistic/packing/save', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(`✓ All items in ${soNumber} marked as packed!`);
                            window.location.reload();
                        } else {
                            alert('Error: ' + (data.message || 'Failed to mark as packed'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error marking as packed');
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading order data');
                });
        }

        function loadPackingOrder(orderId, isCompleted = false) {
            // Fetch order data
            fetch(`/production/logistic/packing/${orderId}/data`)
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert('Error loading order data');
                        return;
                    }

                    const order = data.order;
                    currentOrderItems = order.items;
                    
                    // Populate order info
                    document.getElementById('detailSONumber').value = order.so_number;
                    document.getElementById('detailOrderDate').value = new Date(order.created_at).toLocaleDateString();
                    document.getElementById('detailCustomerName').value = order.customer.customer_name;
                    document.getElementById('siSignedDate').value = order.signed_at ? new Date(order.signed_at).toLocaleDateString() : 'N/A';

                    // Get packing data from order
                    const packingData = order.packing_data ? JSON.parse(order.packing_data) : {};
                    document.getElementById('packingStatus').value = packingData.status || 'not_started';

                    // Populate items table
                    let html = '';
                    let totalItems = 0;
                    let packedItems = 0;

                    order.items.forEach((item, index) => {
                        const itemKey = `item_${index}`;
                        const itemData = packingData[itemKey] || {};
                        
                        totalItems++;
                        if (itemData.status === 'Packed') packedItems++;

                        html += `
                            <tr id="packing_item_row_${index}">
                                <td>${index + 1}</td>
                                <td>${item.book.name}</td>
                                <td><input type="number" value="${item.quantity}" readonly style="width: 100%; border: none;"></td>
                                <td>₱${parseFloat(item.price).toFixed(2)}</td>
                                <td>₱${parseFloat(item.subtotal).toFixed(2)}</td>
                                <td><input type="number" id="packed_qty_${index}" min="0" max="${item.quantity}" value="${itemData.packed_qty || 0}" onchange="updatePackingCount()"></td>
                                <td>
                                    <select id="packed_status_${index}" onchange="handlePackingStatusChange()">
                                        <option value="Not Packed" ${itemData.status === 'Not Packed' ? 'selected' : ''}>Not Packed</option>
                                        <option value="In Progress" ${itemData.status === 'In Progress' ? 'selected' : ''}>In Progress</option>
                                        <option value="Packed" ${itemData.status === 'Packed' ? 'selected' : ''}>Packed</option>
                                    </select>
                                </td>
                                <td><input type="text" id="packed_notes_${index}" value="${itemData.notes || ''}" placeholder="Add notes..."></td>
                                <td><input type="date" id="packed_date_${index}" value="${itemData.packed_date || new Date().toISOString().split('T')[0]}"></td>
                            </tr>
                        `;
                    });

                    document.getElementById('packingTableBody').innerHTML = html;
                    document.getElementById('totalItems').value = totalItems;
                    updatePackingCount();

                    // Show detail modal
                    document.getElementById('orderDetailModal').style.display = 'flex';
                    document.getElementById('modalTitle').textContent = `Packing Details - ${order.so_number}`;
                    document.getElementById('barcodeScannerInput').value = '';
                    setBarcodeScanMessage('Ready to scan', 'neutral');
                    refreshPackingRowColors();
                    setTimeout(() => document.getElementById('barcodeScannerInput').focus(), 100);
                    
                    // Disable inputs if completed
                    if (isCompleted) {
                        document.getElementById('packingStatus').disabled = true;
                        document.getElementById('barcodeScannerInput').disabled = true;
                        for (let i = 0; i < totalItems; i++) {
                            document.getElementById(`packed_qty_${i}`).disabled = true;
                            document.getElementById(`packed_status_${i}`).disabled = true;
                            document.getElementById(`packed_notes_${i}`).disabled = true;
                            document.getElementById(`packed_date_${i}`).disabled = true;
                        }
                        document.getElementById('savePackingBtn').style.display = 'none';
                    } else {
                        document.getElementById('packingStatus').disabled = false;
                        document.getElementById('barcodeScannerInput').disabled = false;
                        for (let i = 0; i < totalItems; i++) {
                            document.getElementById(`packed_qty_${i}`).disabled = false;
                            document.getElementById(`packed_status_${i}`).disabled = false;
                            document.getElementById(`packed_notes_${i}`).disabled = false;
                            document.getElementById(`packed_date_${i}`).disabled = false;
                        }
                        document.getElementById('savePackingBtn').style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading order data');
                });
        }

        function updatePackingCount() {
            const totalItems = parseInt(document.getElementById('totalItems').value) || 0;
            let packedCount = 0;

            for (let i = 0; i < totalItems; i++) {
                const status = document.getElementById(`packed_status_${i}`).value;
                if (status === 'Packed') {
                    packedCount++;
                }
            }

            document.getElementById('itemsPacked').value = packedCount;
            const percent = totalItems > 0 ? Math.round((packedCount / totalItems) * 100) : 0;
            document.getElementById('packingProgressBar').style.width = percent + '%';
            document.getElementById('packingPercent').textContent = percent + '%';
            document.getElementById('packingStatus').value = packedCount === totalItems && totalItems > 0
                ? 'completed'
                : (packedCount > 0 ? 'in_progress' : 'not_started');
            refreshPackingRowColors();
        }

        function handlePackingStatusChange() {
            updatePackingCount();
            focusBarcodeScanner();
        }

        function focusBarcodeScanner() {
            if (!barcodeScannerInput.disabled && document.getElementById('orderDetailModal').style.display !== 'none') {
                setTimeout(() => barcodeScannerInput.focus(), 50);
            }
        }

        function normalizeBarcode(value) {
            return (value || '').toString().trim().toLowerCase();
        }

        function getItemBarcodes(item) {
            const book = item.book || {};
            return [
                book.barcode,
                book.nbs_barcode,
                book.sku,
                book.item_code,
                item.isbn,
            ].map(normalizeBarcode).filter(Boolean);
        }

        function processPackingBarcode(rawBarcode) {
            clearTimeout(barcodeScanTimer);
            const barcode = normalizeBarcode(rawBarcode);

            if (!barcode || !currentOrderItems.length) {
                return;
            }

            const matchedIndex = currentOrderItems.findIndex(item => getItemBarcodes(item).includes(barcode));
            barcodeScannerInput.value = '';
            barcodeScannerInput.focus();

            if (matchedIndex === -1) {
                setBarcodeScanMessage(`Barcode not found in this order: ${rawBarcode.trim()}`, 'error');
                return;
            }

            const item = currentOrderItems[matchedIndex];
            const qtyInput = document.getElementById(`packed_qty_${matchedIndex}`);
            const statusSelect = document.getElementById(`packed_status_${matchedIndex}`);
            const notesInput = document.getElementById(`packed_notes_${matchedIndex}`);
            const dateInput = document.getElementById(`packed_date_${matchedIndex}`);
            const row = document.getElementById(`packing_item_row_${matchedIndex}`);

            qtyInput.value = item.quantity;
            statusSelect.value = 'Packed';
            dateInput.value = new Date().toISOString().split('T')[0];
            if (!notesInput.value.trim()) {
                notesInput.value = 'Scanned by barcode';
            }

            updatePackingCount();
            row.classList.remove('item-scanned');
            void row.offsetWidth;
            row.classList.add('item-scanned');
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setBarcodeScanMessage(`${item.book.name} marked as packed`, 'success');
        }

        function refreshPackingRowColors() {
            const totalItems = parseInt(document.getElementById('totalItems').value) || 0;

            for (let i = 0; i < totalItems; i++) {
                const row = document.getElementById(`packing_item_row_${i}`);
                const status = document.getElementById(`packed_status_${i}`)?.value;

                if (!row) continue;

                row.classList.toggle('item-packed', status === 'Packed');
                row.classList.toggle('item-not-packed', status !== 'Packed');
            }
        }

        function setBarcodeScanMessage(message, type) {
            const messageBox = document.getElementById('barcodeScanMessage');
            messageBox.textContent = message;
            messageBox.classList.remove('success', 'error');

            if (type === 'success' || type === 'error') {
                messageBox.classList.add(type);
            }
        }

        function savePackingData() {
            if (!currentOrderId) {
                alert('No order selected');
                return;
            }

            const totalItems = parseInt(document.getElementById('totalItems').value) || 0;
            const packingItems = [];

            for (let i = 0; i < totalItems; i++) {
                packingItems.push({
                    index: i,
                    packed_qty: parseInt(document.getElementById(`packed_qty_${i}`).value) || 0,
                    status: document.getElementById(`packed_status_${i}`).value,
                    notes: document.getElementById(`packed_notes_${i}`).value,
                    packed_date: document.getElementById(`packed_date_${i}`).value,
                });
            }

            const payload = {
                order_id: currentOrderId,
                packing_status: document.getElementById('packingStatus').value,
                items: packingItems,
            };

            fetch('/production/logistic/packing/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify(payload)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Packing data saved successfully');
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to save packing data'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving packing data');
            });
        }


    </script>
    @endpush

</x-app-layout>

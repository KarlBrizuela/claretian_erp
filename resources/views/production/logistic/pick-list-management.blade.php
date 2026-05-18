<x-app-layout :title="'Pick List Management'" :sidebar="'production'">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card pick-list-form">
                    <div class="form-header">
                        <h2 class="document-title">PICK LIST MANAGEMENT</h2>
                    </div>

                    {{-- Success/Error Messages --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="las la-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Orders Ready for Picking Queue -->
                    <div class="picking-queue-section mb-4">
                        <h5 style="font-weight: 700; color: #333; margin-bottom: 1rem;">
                            <i class="las la-clipboard-list me-2"></i>Orders Ready for Picking
                            <span class="badge bg-danger rounded-pill ms-2">{{ $pickingOrders->count() }}</span>
                        </h5>

                        @if($pickingOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" style="border: 1px solid #dee2e6;">
                                <thead style="background: linear-gradient(135deg, #cc0000, #ff0000); color: #fff;">
                                    <tr>
                                        <th style="padding: 0.75rem;">SO / Invoice #</th>
                                        <th>Customer</th>
                                        <th>Type</th>
                                        <th>Platform</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Prepared By</th>
                                        <th>Date</th>
                                        <th>Attachments</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pickingOrders as $order)
                                    @php
                                        $orderItemsJson = json_encode($order->items->map(function($item) {
                                            return [
                                                'product'  => $item->book->name ?? 'Unknown',
                                                'quantity' => $item->quantity,
                                                'price'    => $item->price,
                                                'subtotal' => $item->subtotal,
                                                'unit'     => $item->unit,
                                            ];
                                        })->values()->all());
                                    @endphp
                                    <tr>
                                        <td class="fw-bold">{{ $order->so_number }}</td>
                                        <td>{{ $order->customer->customer_name ?? 'N/A' }}</td>
                                        <td>
                                            @if($order->type === 'website_direct')
                                                <span class="badge bg-primary">Website</span>
                                                @if($order->transaction_subtype)
                                                    <span class="badge {{ $order->transaction_subtype === 'foreign' ? 'bg-purple' : 'bg-info' }}" style="{{ $order->transaction_subtype === 'foreign' ? 'background: #7b1fa2;' : '' }}">
                                                        {{ ucfirst($order->transaction_subtype) }}
                                                    </span>
                                                @endif
                                            @elseif($order->type === 'ecom_direct')
                                                <span class="badge bg-success">E-com</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $order->type)) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($order->ecom_platform)
                                                <span class="platform-badge platform-{{ $order->ecom_platform }}">
                                                    {{ ucfirst($order->ecom_platform) }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $order->items->count() }} items</span>
                                        </td>
                                        <td class="fw-bold">₱{{ number_format($order->total_amount, 2) }}</td>
                                        <td>{{ $order->preparedBy->name ?? 'N/A' }}</td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>
                                            @if($order->proof_of_payment)
                                                <a href="{{ asset('storage/'.$order->proof_of_payment) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Proof of Payment"><i class="las la-receipt"></i></a>
                                            @endif
                                            @if($order->order_list_attachment)
                                                <a href="{{ asset('storage/'.$order->order_list_attachment) }}" target="_blank" class="btn btn-sm btn-outline-info" title="Order List"><i class="las la-file-alt"></i></a>
                                            @endif
                                            @if($order->pick_list_attachment)
                                                <a href="{{ asset('storage/'.$order->pick_list_attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Pick List"><i class="las la-clipboard-list"></i></a>
                                            @endif
                                            @if($order->shipping_label_attachment)
                                                <a href="{{ asset('storage/'.$order->shipping_label_attachment) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Shipping Label"><i class="las la-shipping-fast"></i></a>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary view-order-btn"
                                                    data-order-id="{{ $order->id }}"
                                                    data-so-number="{{ $order->so_number }}"
                                                    data-customer="{{ $order->customer->customer_name ?? 'N/A' }}"
                                                    data-date="{{ $order->created_at->format('Y-m-d') }}"
                                                    data-items='{{ $orderItemsJson }}'
                                                    style="background: #ff0000; border: none;">
                                                <i class="las la-eye me-1"></i> View Items
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-5" style="background: #f8f9fa; border-radius: 8px;">
                            <i class="las la-inbox" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-2 mb-0">No orders ready for picking at this time.</p>
                        </div>
                        @endif
                    </div>

                    <!-- Order Details Panel (shown when clicking View Items) -->
                    <div id="orderDetailPanel" style="display: none;">
                        <hr>
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
                                <h5>Pick List Information</h5>
                                <div class="form-group">
                                    <label>Pick List Number:</label>
                                    <input type="text" id="pickListNumber" placeholder="Auto-generated" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Status:</label>
                                    <select id="pickListStatus" class="form-control">
                                        <option value="draft">Draft</option>
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

                        <!-- Pick List Items Table -->
                        <h5 style="margin-bottom: 1rem; font-weight: 600;">Pick List Items</h5>
                        <table class="pick-list-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Product</th>
                                    <th style="width: 120px;">Requested Qty</th>
                                    <th style="width: 120px;">Unit Price</th>
                                    <th style="width: 120px;">Subtotal</th>
                                    <th style="width: 120px;">Picked Qty</th>
                                    <th style="width: 120px;">Status</th>
                                    <th style="width: 150px;">Notes</th>
                                </tr>
                            </thead>
                            <tbody id="pickListTableBody">
                                <!-- Filled by JS -->
                            </tbody>
                        </table>

                        <!-- Summary Section -->
                        <div class="order-info-section" style="margin-top: 1.5rem;">
                            <div class="order-info-box">
                                <h5>Summary</h5>
                                <div class="form-group">
                                    <label>Total Items:</label>
                                    <input type="text" id="totalItems" value="0" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Items Picked:</label>
                                    <input type="text" id="itemsPicked" value="0" readonly>
                                </div>
                            </div>
                            <div class="order-info-box">
                                <h5>Actions</h5>
                                <div class="form-group">
                                    <button type="button" class="btn btn-secondary-custom" style="width: 100%; margin-bottom: 0.5rem;" onclick="window.print()">
                                        <i class="las la-print"></i> Print Pick List
                                    </button>
                                </div>
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary-custom" style="width: 100%;" id="closeDetailBtn">
                                        <i class="las la-times"></i> Close Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .pick-list-form {
            background: #fff;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        }

        .form-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e0e0e0;
        }

        .form-header .document-title {
            text-align: center;
            font-size: 1.75rem;
            font-weight: 700;
            color: #333;
            margin-top: 1rem;
            letter-spacing: 1px;
        }

        .order-info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 1.5rem;
        }

        .order-info-box {
            background: #f8f9fa;
            padding: 1.25rem;
            border-radius: 8px;
        }

        .order-info-box h5 {
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
        .form-group select {
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 0.5rem;
            font-size: 0.9rem;
        }

        .pick-list-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }

        .pick-list-table thead {
            background: linear-gradient(135deg, #cc0000, #ff0000);
            color: #fff;
        }

        .pick-list-table th {
            padding: 0.75rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.85rem;
            border: 1px solid #ddd;
            text-transform: uppercase;
        }

        .pick-list-table td {
            padding: 0.5rem;
            border: 1px solid #ddd;
        }

        .pick-list-table input[type="number"],
        .pick-list-table input[type="text"],
        .pick-list-table select {
            width: 100%;
            border: none;
            padding: 0.5rem;
            background: transparent;
        }

        .pick-list-table input:focus,
        .pick-list-table select:focus {
            outline: 2px solid #ff0000;
            outline-offset: -2px;
            background: #fff;
        }

        .platform-badge { padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .platform-lazada { background: #0f146d; color: #fff; }
        .platform-shopee { background: #ee4d2d; color: #fff; }
        .platform-tiktok { background: #010101; color: #fff; }

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

        @media print {
            .sidebar, .header, .picking-queue-section { display: none !important; }
            .pick-list-form { box-shadow: none; }
            #orderDetailPanel { display: block !important; }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const detailPanel = document.getElementById('orderDetailPanel');
            const pickListBody = document.getElementById('pickListTableBody');
            const closeDetailBtn = document.getElementById('closeDetailBtn');

            // View Items buttons
            document.querySelectorAll('.view-order-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    try {
                        const soNumber = this.dataset.soNumber;
                        const customer = this.dataset.customer;
                        const date = this.dataset.date;
                        const itemsJson = this.dataset.items;
                        
                        // Parse items from data attribute
                        const items = JSON.parse(itemsJson);

                        // Fill details
                        document.getElementById('detailSONumber').value = soNumber;
                        document.getElementById('detailOrderDate').value = date;
                        document.getElementById('detailCustomerName').value = customer;
                        document.getElementById('pickListNumber').value = 'PL-' + soNumber;

                        // Fill items table
                        pickListBody.innerHTML = '';
                        items.forEach((item, idx) => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${idx + 1}</td>
                                <td class="fw-bold">${item.product}</td>
                                <td style="text-align:center;">${item.quantity} ${item.unit || 'pcs'}</td>
                                <td style="text-align:right;">₱${parseFloat(item.price).toFixed(2)}</td>
                                <td style="text-align:right;">₱${parseFloat(item.subtotal).toFixed(2)}</td>
                                <td><input type="number" class="picked-qty" value="0" min="0" max="${item.quantity}" style="text-align:center;"></td>
                                <td>
                                    <select class="form-control" style="border:none;">
                                        <option value="pending">Pending</option>
                                        <option value="picked">Picked</option>
                                        <option value="short">Short</option>
                                    </select>
                                </td>
                                <td><input type="text" placeholder="Notes" style="border:none;"></td>
                            `;
                            pickListBody.appendChild(tr);
                        });

                        document.getElementById('totalItems').value = items.length;
                        document.getElementById('itemsPicked').value = 0;

                        // Show panel and scroll to it
                        detailPanel.style.display = 'block';
                        setTimeout(() => {
                            detailPanel.scrollIntoView({ behavior: 'smooth' });
                        }, 100);
                        
                    } catch (error) {
                        console.error('Error:', error);
                        console.error('Items data:', this.dataset.items);
                        alert('Error loading order items: ' + error.message);
                    }
                });
            });

            // Close detail panel
            if (closeDetailBtn) {
                closeDetailBtn.addEventListener('click', function() {
                    detailPanel.style.display = 'none';
                });
            }
        });
    </script>
    @endpush
</x-app-layout>

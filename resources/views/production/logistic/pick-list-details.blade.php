<x-app-layout :title="'Pick List Details'" :sidebar="'production'">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card pick-list-form">
                    <div class="form-header">
                        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                            <h2 class="document-title">PICK LIST DETAILS</h2>
                            <a href="{{ route('production.logistic.pick-list-list') }}" class="btn btn-secondary" style="background: #6c757d; border: none; color: white;">
                                <i class="las la-arrow-left me-2"></i>Back to List
                            </a>
                        </div>
                    </div>

                    {{-- Alerts --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
                            <i class="las la-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Pick List Details Section -->
                    <div class="order-info-section">
                        <div class="order-info-box">
                            <h5>Order Information</h5>
                            <div class="form-group">
                                <label>Sales Order Number:</label>
                                <input type="text" value="{{ $pickList->salesOrder?->so_number ?? 'N/A' }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Order Date:</label>
                                <input type="text" value="{{ optional(optional($pickList->salesOrder)->created_at)->format('M d, Y') ?? 'N/A' }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Company:</label>
                                <input type="text" value="{{ $pickList->salesOrder?->customer?->company_name ?: ($pickList->salesOrder?->customer?->customer_name ?? 'N/A') }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Customer Name:</label>
                                <input type="text" value="{{ $pickList->salesOrder?->customer_representative ?: ($pickList->salesOrder?->customer?->customer_name ?? 'Unknown') }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Contact:</label>
                                <input type="text" value="{{ $pickList->salesOrder?->customer_contact ?: ($pickList->salesOrder?->customer?->mobile ?: ($pickList->salesOrder?->customer?->main_phone ?: 'N/A')) }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Remarks / Notes:</label>
                                <div class="d-flex flex-column gap-2">
                                    <textarea id="pickListDetailsRemarks" class="form-control" style="background:#fff; font-weight:600;" rows="2" placeholder="Enter remarks or special instructions...">{{ $pickList->notes ?: ($pickList->salesOrder?->remarks ?: '') }}</textarea>
                                    <button type="button" class="btn btn-sm btn-primary align-self-start" onclick="savePickListDetailsRemarks()" style="background:#0d6efd; border:none; border-radius:6px; font-weight:600; padding: 0.5rem 1.25rem;">
                                        <i class="las la-save me-1"></i>Save Remarks
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="order-info-box">
                            <h5>Pick List Information</h5>
                            <div class="form-group">
                                <label>Pick List Number:</label>
                                <input type="text" value="{{ $pickList->pick_list_number }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Status:</label>
                                <div style="padding-top: 0.5rem;">
                                    @if($pickList->status === 'draft')
                                        <span class="badge bg-secondary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Draft</span>
                                    @elseif($pickList->status === 'in_progress')
                                        <span class="badge bg-info" style="padding: 0.5rem 1rem; font-size: 0.875rem;">In Progress</span>
                                    @elseif($pickList->status === 'completed')
                                        <span class="badge bg-success" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Completed</span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Prepared By:</label>
                                <input type="text" value="{{ $pickList->preparedByUser?->name ?? 'System' }}" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Pick List Items Table — Inline Editable -->
                    <div style="padding: 0 1.5rem 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; margin-top: 1.5rem;">
                            <h5 style="font-weight: 700; margin: 0;">Pick List Items</h5>
                            <button type="button" id="savePickedBtn" class="btn btn-success" onclick="savePickedItemsInline()" style="background: #28a745; border: none; color: white; padding: 0.6rem 1.75rem; border-radius: 6px; font-weight: 600;">
                                <i class="las la-save me-1"></i>Save Picked Items
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="pick-list-table" style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                                <thead style="background: #cc0000; color: white;">
                                    <tr>
                                        <th style="width: 50px; padding: 0.75rem; border: 1px solid #b30000;">#</th>
                                        <th style="padding: 0.75rem; border: 1px solid #b30000; text-align: left;">Product</th>
                                        <th style="width: 130px; padding: 0.75rem; border: 1px solid #b30000; text-align: center;">ISBN / SKU</th>
                                        <th style="width: 130px; padding: 0.75rem; border: 1px solid #b30000; text-align: center;">Barcode</th>
                                        <th style="width: 110px; padding: 0.75rem; border: 1px solid #b30000; text-align: center;">Requested Qty</th>
                                        <th style="width: 110px; padding: 0.75rem; border: 1px solid #b30000; text-align: right;">Unit Price</th>
                                        <th style="width: 110px; padding: 0.75rem; border: 1px solid #b30000; text-align: right;">Subtotal</th>
                                        <th style="width: 120px; padding: 0.75rem; border: 1px solid #b30000; text-align: center;">Picked Qty</th>
                                        <th style="width: 130px; padding: 0.75rem; border: 1px solid #b30000; text-align: center;">Status</th>
                                        <th style="width: 180px; padding: 0.75rem; border: 1px solid #b30000; text-align: left;">Notes</th>
                                    </tr>
                                </thead>
                                <tbody id="pickListItemsBody">
                                    @forelse($pickList->pickListItems as $item)
                                    @php
                                        $book = $item->salesOrderItem?->book;
                                        $isbn = $book?->sku ?? '';
                                        $barcode = $book?->barcode ?? '';
                                        $pickedQtyInt = (int) $item->picked_qty;
                                    @endphp
                                    <tr data-product="{{ $item->salesOrderItem?->item_name ?? ($item->salesOrderItem?->book?->name ?? 'Unknown') }}">
                                        <td style="padding: 0.6rem; border: 1px solid #ddd; text-align: center;">{{ $loop->iteration }}</td>
                                        <td style="padding: 0.6rem; border: 1px solid #ddd;">{{ $item->salesOrderItem?->item_name ?? ($item->salesOrderItem?->book?->name ?? 'Unknown') }}</td>
                                        <td style="padding: 0.6rem; border: 1px solid #ddd; text-align: center; font-size: 0.8rem; font-family: monospace;">{{ $isbn ?: '—' }}</td>
                                        <td style="padding: 0.6rem; border: 1px solid #ddd; text-align: center; font-size: 0.8rem; font-family: monospace;">{{ $barcode ?: '—' }}</td>
                                        <td style="padding: 0.6rem; border: 1px solid #ddd; text-align: center;">{{ (int) $item->requested_qty }}</td>
                                        <td style="padding: 0.6rem; border: 1px solid #ddd; text-align: right;">₱{{ number_format($item->salesOrderItem?->price ?? 0, 2) }}</td>
                                        <td style="padding: 0.6rem; border: 1px solid #ddd; text-align: right;">₱{{ number_format($item->salesOrderItem?->subtotal ?? 0, 2) }}</td>
                                        <td style="padding: 0.5rem; border: 1px solid #ddd; text-align: center;">
                                            <input type="number" class="picked-qty form-control form-control-sm text-center"
                                                   value="{{ $pickedQtyInt }}"
                                                   min="0"
                                                   step="1"
                                                   style="width: 80px; margin: 0 auto; font-weight: 600; border: 1px solid #aaa; border-radius: 4px;">
                                        </td>
                                        <td style="padding: 0.5rem; border: 1px solid #ddd; text-align: center;">
                                            <select class="status-select form-select form-select-sm" style="font-size: 0.82rem; border: 1px solid #aaa; border-radius: 4px;">
                                                <option value="pending" {{ $item->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="picked" {{ $item->status === 'picked' ? 'selected' : '' }}>Picked</option>
                                                <option value="short" {{ $item->status === 'short' ? 'selected' : '' }}>Short</option>
                                            </select>
                                        </td>
                                        <td style="padding: 0.5rem; border: 1px solid #ddd;">
                                            <input type="text" class="notes-input form-control form-control-sm"
                                                   value="{{ $item->notes ?? '' }}"
                                                   placeholder="Notes..."
                                                   style="border: 1px solid #aaa; border-radius: 4px; font-size: 0.85rem;">
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" style="padding: 1rem; text-align: center; color: #999;">No items in this pick list.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div class="order-info-section" style="margin-top: 1rem;">
                        <div class="order-info-box">
                            <h5>Summary</h5>
                            <div class="form-group">
                                <label>Total Items:</label>
                                <input type="text" value="{{ $pickList->pickListItems->count() }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Total Requested Qty:</label>
                                <input type="text" value="{{ $pickList->pickListItems->sum('requested_qty') }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Total Picked Qty:</label>
                                <input type="text" id="totalPickedDisplay" value="{{ $pickList->pickListItems->sum('picked_qty') }}" readonly>
                            </div>
                        </div>
                        <div class="order-info-box">
                            <h5>Financial Summary</h5>
                            <div class="form-group">
                                <label>Total Amount:</label>
                                <input type="text" value="₱{{ number_format($pickList->pickListItems->sum('salesOrderItem.subtotal'), 2) }}" readonly style="font-weight: bold; font-size: 1.1rem;">
                            </div>
                            <div class="form-group">
                                <label>Date Created:</label>
                                <input type="text" value="{{ optional($pickList->created_at)->format('M d, Y h:i A') ?? 'N/A' }}" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div style="margin-top: 1.5rem; padding: 0 1.5rem 1.5rem; display: flex; gap: 1rem; flex-wrap: wrap;">
                        @php
                            $isConsignmentOrder = $pickList->salesOrder && in_array($pickList->salesOrder->type, ['area_consignment', 'area_sales_consignment']);
                            $targetQueueText = $isConsignmentOrder ? 'Delivery Receipt (DR) Preparation' : 'Sales Invoice (SI) Preparation';
                        @endphp
                        <form action="{{ route('production.logistic.mark-as-gathered', $pickList->salesOrder->id ?? 0) }}" method="POST" style="display:inline;" onsubmit="return confirm('Mark {{ $pickList->pick_list_number }} as gathered and move to {{ $targetQueueText }}?');">
                            @csrf
                            <button type="submit" class="btn btn-success" style="background: #28a745; border: none; color: white; padding: 0.75rem 2rem; border-radius: 6px; cursor: pointer; font-weight: 600;">
                                <i class="las la-check-circle me-2"></i>Mark as Gathered
                            </button>
                        </form>
                        <a href="{{ route('production.logistic.shipping-label', $pickList->salesOrder?->id ?? $pickList->id) }}" target="_blank" class="btn btn-primary" style="background: #0d6efd; border: none; color: white; padding: 0.75rem 2rem; border-radius: 6px; cursor: pointer; font-weight: 600;">
                            <i class="las la-tag me-2"></i>Shipping Label
                        </a>
                        <button type="button" class="btn btn-info" style="background: #17a2b8; border: none; color: white; padding: 0.75rem 2rem; border-radius: 6px; cursor: pointer; font-weight: 600;" onclick="window.print()">
                            <i class="las la-print me-2"></i>Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .pick-list-form {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .form-header {
            background: linear-gradient(135deg, #cc0000, #ff0000);
            color: white;
            padding: 1.5rem;
            border-radius: 8px 8px 0 0;
        }
        .document-title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .order-info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            padding: 1.5rem;
            border-top: 1px solid #dee2e6;
        }
        .order-info-box {
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 6px;
        }
        .order-info-box h5 {
            font-weight: 600;
            margin-bottom: 1rem;
            color: #333;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #555;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.95rem;
        }
        .picked-qty:focus, .status-select:focus, .notes-input:focus {
            border-color: #cc0000 !important;
            outline: none;
            box-shadow: 0 0 0 2px rgba(204,0,0,0.15);
        }
        #pickListItemsBody tr:hover {
            background: #fff8f8;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        // Live update total picked qty as user types
        document.querySelectorAll('.picked-qty').forEach(input => {
            input.addEventListener('input', updateTotalPicked);
        });

        function updateTotalPicked() {
            let total = 0;
            document.querySelectorAll('.picked-qty').forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            const display = document.getElementById('totalPickedDisplay');
            if (display) display.value = total;
        }

        function savePickedItemsInline() {
            const orderId = {{ $pickList->salesOrder?->id ?? 'null' }};
            const soNumber = '{{ $pickList->salesOrder?->so_number }}';
            const rows = document.querySelectorAll('#pickListItemsBody tr');
            const pickedItems = [];
            let totalPicked = 0;
            let hasError = false;

            rows.forEach((row, idx) => {
                const pickedQtyInput = row.querySelector('.picked-qty');
                const statusSelect = row.querySelector('.status-select');
                const notesInput = row.querySelector('.notes-input');
                if (!pickedQtyInput) return; // empty state row

                const pickedQty = parseFloat(pickedQtyInput.value) || 0;
                const status = statusSelect.value;
                const notes = notesInput.value;
                const product = row.dataset.product || row.cells[1]?.innerText || '';

                if (status === 'picked' && pickedQty === 0) {
                    alert(`"${product}" is marked as Picked but has 0 quantity. Please update.`);
                    hasError = true;
                    return;
                }

                pickedItems.push({ product, picked_qty: pickedQty, status, notes, item_index: idx });
                totalPicked += pickedQty;
            });

            if (hasError) return;

            if (!confirm(`Save picked items for ${soNumber}?\n\nTotal picked: ${totalPicked}`)) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                alert('Security token not found. Please refresh the page.');
                return;
            }

            const saveBtn = document.getElementById('savePickedBtn');
            const originalHTML = saveBtn.innerHTML;
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="las la-spinner la-spin"></i> Saving...';

            const remarks = document.getElementById('pickListDetailsRemarks')?.value || '';

            fetch('/production/logistic/pick-list/save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ order_id: orderId, so_number: soNumber, picked_items: pickedItems, remarks })
            })
            .then(r => r.json())
            .then(data => {
                saveBtn.disabled = false;
                if (data.success) {
                    saveBtn.innerHTML = '<i class="las la-check-circle"></i> Saved!';
                    saveBtn.style.background = '#1a7a35';
                    setTimeout(() => {
                        saveBtn.innerHTML = originalHTML;
                        saveBtn.style.background = '';
                        window.location.reload();
                    }, 1800);
                } else {
                    saveBtn.innerHTML = originalHTML;
                    alert('Error: ' + (data.message || 'Failed to save picked items'));
                }
            })
            .catch(err => {
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalHTML;
                alert('Error saving: ' + err.message);
            });
        }

        function savePickListDetailsRemarks() {
            const remarks = document.getElementById('pickListDetailsRemarks').value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch('/production/logistic/pick-list/save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({
                    order_id: {{ $pickList->salesOrder?->id ?? 'null' }},
                    so_number: '{{ $pickList->salesOrder?->so_number }}',
                    remarks: remarks
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Remarks saved successfully!');
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to save remarks'));
                }
            })
            .catch(err => alert('Error saving remarks: ' + err.message));
        }
    </script>
    @endpush
</x-app-layout>

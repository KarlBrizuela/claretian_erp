<x-app-layout :title="'Area Sales - Team Stocks'" :sidebar="'marketing'">
    @push('styles')
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        .select2-container {
            width: 100% !important;
        }
        .select2-container .select2-selection--single {
            height: 38px !important;
            padding: 4px 8px;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            display: flex;
            align-items: center;
        }
        .select2-container .select2-selection--single .select2-selection__rendered {
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            line-height: 1.5 !important;
            padding-left: 0 !important;
            padding-right: 20px !important;
            color: #333;
        }
        .select2-container .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
        .select2-dropdown {
            z-index: 1070 !important;
            border: 1px solid #ced4da;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        .select2-results__option {
            font-size: 0.85rem !important;
            white-space: normal !important;
            word-wrap: break-word !important;
            word-break: break-word !important;
            padding: 8px 12px !important;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #D9251C !important;
            color: #fff !important;
        }
        .team-stocks-tabs-container {
            background: #f8f9fa;
            padding: 10px 10px 0 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .nav-tabs.modern-tabs {
            border-bottom: none;
            gap: 10px;
        }
        .nav-tabs.modern-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-size: 13px;
            letter-spacing: 0.5px;
            padding: 12px 25px;
            border-radius: 10px 10px 0 0;
            transition: all 0.3s ease;
            position: relative;
            background: transparent;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nav-tabs.modern-tabs .nav-link:hover {
            color: #D9251C;
            background: rgba(217, 37, 28, 0.05);
        }
        .nav-tabs.modern-tabs .nav-link.active {
            color: #D9251C;
            background: #fff;
            box-shadow: 0 -4px 10px rgba(0,0,0,0.05);
        }
        .nav-tabs.modern-tabs .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: #D9251C;
            border-radius: 3px 3px 0 0;
        }
    </style>
    @endpush

    <div class="container-fluid py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Team Summary Cards -->
        <div class="row mb-4">
            @foreach(['Team A', 'Team B', 'Team C', 'Book Sales', 'MIBF'] as $team)
            @php
                $teamCount = $teamStocks->where('team_name', $team)->sum('quantity');
                $teamItemsCount = $teamStocks->where('team_name', $team)->where('quantity', '>', 0)->count();
                $staffCount = $teamUsers->where('sales_team', $team)->count();
                $teamBadgeClass = 'bg-danger text-white';
            @endphp
            <div class="col-md-6 col-lg-4 col-xl-2-4 mb-3">
                <div class="card shadow-sm border-0" style="border-radius: 10px; background: #fff;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge {{ $teamBadgeClass }} px-3 py-2 fw-bold" style="font-size: 0.85rem;">{{ $team }}</span>
                            <span class="text-muted small fw-bold">{{ $staffCount }} Staff</span>
                        </div>
                        <div class="mt-3">
                            <h3 class="fw-bold mb-0 text-dark">{{ number_format($teamCount) }} <span class="fs-14 text-muted fw-normal">pcs in stock</span></h3>
                            <small class="text-muted">{{ $teamItemsCount }} distinct item title(s)</small>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Main Card with Header & Tabs -->
        <div class="card shadow-sm border-0 overflow-hidden" style="border-radius: 10px;">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0 flex-wrap gap-2">
                <div>
                    <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-boxes me-2 text-danger"></i>Area Sales — Team Stocks</h5>
                    <p class="text-muted small mb-0">Manage stock balances and transfers from Main Warehouse to Sales Teams (Team A, Team B, Team C, Book Sales, MIBF).</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <button class="btn btn-danger btn-sm px-3 shadow-sm d-inline-flex align-items-center justify-content-center gap-1" 
                            style="height: 36px; font-size: 0.85rem; background-color: #D9251C; border: none; font-weight: 600;" 
                            data-bs-toggle="modal" data-bs-target="#newTransferModal">
                        <i class="fas fa-exchange-alt me-1"></i> Transfer Stock to Team
                    </button>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="team-stocks-tabs-container">
                    <ul class="nav nav-tabs modern-tabs" id="teamStockTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold" id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventoryContent" type="button" role="tab">
                                <i class="fas fa-boxes"></i> TEAM STOCK INVENTORY
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold" id="history-tab" data-bs-toggle="tab" data-bs-target="#historyContent" type="button" role="tab">
                                <i class="fas fa-history"></i> STOCK TRANSFER HISTORY
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content p-4" id="teamStockTabContent">
                    <!-- Team Stock Inventory Tab -->
                    <div class="tab-pane fade show active" id="inventoryContent" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="fw-bold text-dark me-2 small">FILTER BY TEAM:</span>
                                <div class="btn-group btn-group-sm flex-wrap" role="group" id="teamFilterGroup">
                                    <button type="button" class="btn btn-outline-danger active" data-filter="all">All Teams</button>
                                    <button type="button" class="btn btn-outline-danger" data-filter="Team A">Team A</button>
                                    <button type="button" class="btn btn-outline-danger" data-filter="Team B">Team B</button>
                                    <button type="button" class="btn btn-outline-danger" data-filter="Team C">Team C</button>
                                    <button type="button" class="btn btn-outline-danger" data-filter="Book Sales">Book Sales</button>
                                    <button type="button" class="btn btn-outline-danger" data-filter="MIBF">MIBF</button>
                                </div>
                            </div>
                            <div style="width: 240px; height: 32px; display: flex; align-items: center; border: 1px solid #ced4da; border-radius: 4px; background-color: #fff; padding: 0 10px; box-sizing: border-box;">
                                <i class="fas fa-search text-muted me-2" style="font-size: 0.85rem;"></i>
                                <input type="text" id="inventorySearch" class="form-control" placeholder="Search product name..." style="border: none !important; background: transparent !important; padding: 0 !important; height: 100%; font-size: 0.82rem; color: #333; outline: none !important; box-shadow: none !important;">
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0" id="teamStockTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 120px;">TEAM</th>
                                        <th>PRODUCT DESCRIPTION</th>
                                        <th class="text-center" style="width: 160px;">QTY IN STOCK</th>
                                        <th class="text-end" style="width: 140px;">ACTIONS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($teamStocks as $stock)
                                    @php
                                        $tBadgeClass = 'bg-danger text-white';
                                    @endphp
                                    <tr class="stock-row" data-team="{{ $stock->team_name }}" data-name="{{ strtolower($stock->product_name) }}">
                                        <td>
                                            <span class="badge {{ $tBadgeClass }} font-w600">{{ $stock->team_name }}</span>
                                        </td>
                                        <td class="fw-bold text-dark">
                                            {{ $stock->product_name }}
                                            @if($stock->bookIndex)
                                                <small class="d-block text-muted fw-normal">ISBN/Article: {{ $stock->bookIndex->article_number ?: ($stock->bookIndex->isbn ?: 'N/A') }}</small>
                                            @elseif($stock->book)
                                                <small class="d-block text-muted fw-normal">Item Code: {{ $stock->book->item_code ?: 'N/A' }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center fw-bold">
                                            @if($stock->quantity > 0)
                                                <span class="text-success">{{ number_format($stock->quantity) }} pcs</span>
                                            @else
                                                <span class="text-danger">0 pcs</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-xs btn-outline-danger" 
                                                    onclick="openQuickTransferModal('{{ $stock->team_name }}', '{{ $stock->book_id }}', '{{ $stock->book_index_id }}', '{{ $stock->book_bundle_id }}', '{{ e($stock->product_name) }}')">
                                                + Add Stock
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            No stock record found for any team. Click <strong>Transfer Stock to Team</strong> above to transfer stock from Main Warehouse.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Stock Transfer History Tab -->
                    <div class="tab-pane fade" id="historyContent" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>TRANSFER #</th>
                                        <th>TARGET TEAM</th>
                                        <th>TRANSFERRED BY</th>
                                        <th class="text-center">ITEMS COUNT</th>
                                        <th>DATE & TIME</th>
                                        <th>STATUS</th>
                                        <th>REMARKS</th>
                                        <th class="text-end">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transfers as $tr)
                                    @php
                                        $tBadgeClass = 'bg-danger text-white';
                                    @endphp
                                    <tr>
                                        <td class="fw-bold text-dark">{{ $tr->transfer_number }}</td>
                                        <td><span class="badge {{ $tBadgeClass }}">{{ $tr->team_name }}</span></td>
                                        <td>{{ $tr->transferredByUser->name ?? 'System' }}</td>
                                        <td class="text-center">{{ $tr->items->count() }} item(s)</td>
                                        <td>{{ $tr->created_at->format('M d, Y h:i A') }}</td>
                                        <td>
                                            @if($tr->status === 'pending_mkt_approval')
                                                <span class="badge bg-warning text-dark">Pending Marketing Approval</span>
                                            @elseif($tr->status === 'pending_prod_approval')
                                                <span class="badge bg-info text-dark">Pending Production Approval</span>
                                            @elseif($tr->status === 'pending_picklist')
                                                <span class="badge bg-primary">Approved by Prod (Pending Pick)</span>
                                            @elseif($tr->status === 'approved' || $tr->status === 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($tr->status === 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucwords(str_replace('_', ' ', $tr->status)) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $tr->notes ?: '—' }}</td>
                                         <td class="text-end">
                                             <button type="button" class="btn btn-xs btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#transferDetailModal{{ $tr->id }}">
                                                 View Details
                                             </button>
                                             @if(auth()->check() && auth()->user()->isSuperAdmin())
                                                 <form action="{{ route('production.logistic.team-stock-transfer.delete', $tr->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Are you sure you want to delete Team Stock Transfer {{ $tr->transfer_number }}?');">
                                                     @csrf
                                                     @method('DELETE')
                                                     <button type="submit" class="btn btn-xs btn-danger fw-bold" style="background-color: #dc3545; border: none;" title="Delete Transfer">
                                                         <i class="fas fa-trash me-1"></i>Delete
                                                     </button>
                                                 </form>
                                             @endif
                                         </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">No stock transfers recorded yet.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Transfer Modal -->
    <div class="modal fade" id="newTransferModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 1200px;">
            <div class="modal-content">
                <form action="{{ route('marketing.area-sales.team-stocks.transfer') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title text-dark fw-bold">Transfer Stock from Main Warehouse to Team</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Target Sales Team <span class="text-danger">*</span></label>
                                <select name="team_name" id="modalTargetTeam" class="form-select" required>
                                    <option value="" disabled selected>Select Sales Team...</option>
                                    <option value="Team A">Team A</option>
                                    <option value="Team B">Team B</option>
                                    <option value="Team C">Team C</option>
                                    <option value="Book Sales">Book Sales</option>
                                    <option value="MIBF">MIBF</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Transfer Notes / Remarks</label>
                                <input type="text" name="notes" class="form-control" placeholder="Optional transfer details or purpose...">
                            </div>
                        </div>

                        <hr>
                        <input type="file" id="excelTransferInput" accept=".xlsx, .xls, .csv" style="display: none;" onchange="handleExcelImport(this)">
                        
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <h6 class="fw-bold text-dark mb-0">Select Items to Transfer</h6>
                            <div class="d-flex gap-2">
                                <a href="{{ route('marketing.area-sales.team-stocks.template') }}" class="btn btn-xs btn-outline-success d-inline-flex align-items-center gap-1" style="font-weight: 600;" title="Download Excel template containing all products">
                                    <i class="fas fa-file-excel"></i> Download Template
                                </a>
                                <button type="button" class="btn btn-xs btn-outline-primary d-inline-flex align-items-center gap-1" style="font-weight: 600;" onclick="triggerExcelImport()">
                                    <i class="fas fa-file-import"></i> Import Excel
                                </button>
                            </div>
                        </div>

                        <div id="excelImportStatus" style="display: none;"></div>

                        <div id="transferItemsContainer" style="max-height: 480px; min-height: 150px; overflow-y: auto; padding-right: 5px;">
                            <div class="transfer-item-row row g-2 mb-2 align-items-end">
                                <div class="col-md-7">
                                    <label class="form-label small fw-bold mb-1">Product Title / Code (Main Warehouse Stock)</label>
                                    <select name="items[0][product_id]" class="form-select product-select" required onchange="updateMaxQty(this)">
                                        <option value="" disabled selected>Select product...</option>
                                        @foreach($mainProducts as $prod)
                                        @php
                                            $pId = is_object($prod) ? $prod->id : ($prod['id'] ?? '');
                                            $pName = is_object($prod) ? $prod->name : ($prod['name'] ?? '');
                                            $pStock = is_object($prod) ? ($prod->stock ?? $prod->main_stock ?? 0) : ($prod['stock'] ?? $prod['main_stock'] ?? 0);
                                        @endphp
                                        <option value="{{ $pId }}" data-stock="{{ $pStock }}">
                                            {{ $pName }} (Main Stock: {{ number_format($pStock) }} pcs)
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold mb-1">Quantity to Transfer</label>
                                    <input type="number" name="items[0][quantity]" class="form-control qty-input" min="1" placeholder="Qty" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-danger w-100 remove-row-btn" onclick="removeTransferRow(this)" disabled>
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addTransferRow()">
                                + Add Another Item
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="triggerExcelImport()">
                                <i class="fas fa-file-import me-1"></i> Import Excel File
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger fw-bold" style="background-color: #D9251C; border: none;">Execute Stock Transfer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Transfer Detail Modals -->
    @foreach($transfers as $tr)
    <div class="modal fade" id="transferDetailModal{{ $tr->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark">Transfer Details: {{ $tr->transfer_number }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-1"><strong>Target Team:</strong> <span class="badge bg-secondary">{{ $tr->team_name }}</span></p>
                    <p class="mb-1"><strong>Transferred By:</strong> {{ $tr->transferredByUser->name ?? 'System' }}</p>
                    <p class="mb-1"><strong>Date:</strong> {{ $tr->created_at->format('M d, Y h:i A') }}</p>
                    <p class="mb-3"><strong>Notes:</strong> {{ $tr->notes ?: 'None' }}</p>
                    
                    <h6 class="fw-bold border-bottom pb-2">Items Transferred</h6>
                    <ul class="list-group list-group-flush">
                        @foreach($tr->items as $tItem)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="fw-bold text-dark">{{ $tItem->product_name }}</span>
                            <span class="badge bg-success rounded-pill px-3 py-2">+{{ number_format($tItem->quantity) }} pcs</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    @push('scripts')
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script>
        let transferRowIndex = 1;

        function initProductSelect2(selectEl) {
            if (window.jQuery && typeof jQuery.fn.select2 === 'function') {
                const $el = jQuery(selectEl);
                if ($el.data('select2')) {
                    $el.select2('destroy');
                }
                $el.select2({
                    dropdownParent: jQuery('#newTransferModal'),
                    width: '100%',
                    placeholder: 'Select product...',
                    allowClear: true
                }).on('change', function() {
                    updateMaxQty(this);
                });
            }
        }

        function updateMaxQty(selectElem) {
            if (!selectElem || !selectElem.options || selectElem.selectedIndex < 0) return;
            const selectedOption = selectElem.options[selectElem.selectedIndex];
            if (!selectedOption || !selectedOption.value) {
                const row = selectElem.closest('.transfer-item-row');
                const qtyInput = row ? row.querySelector('.qty-input') : null;
                if (qtyInput) {
                    qtyInput.removeAttribute('max');
                    qtyInput.placeholder = 'Qty';
                }
                return;
            }
            const stock = parseInt(selectedOption.dataset.stock || 0);
            const row = selectElem.closest('.transfer-item-row');
            const qtyInput = row ? row.querySelector('.qty-input') : null;
            if (qtyInput) {
                qtyInput.max = stock;
                qtyInput.placeholder = `Max: ${stock}`;
            }
        }

        function addTransferRow() {
            const container = document.getElementById('transferItemsContainer');
            const firstRow = container.querySelector('.transfer-item-row');
            const newRow = firstRow.cloneNode(true);

            // Clean up any cloned Select2 wrapper element
            const select2Wrapper = newRow.querySelector('.select2-container');
            if (select2Wrapper) {
                select2Wrapper.remove();
            }

            // Update field names & reset inputs
            const select = newRow.querySelector('.product-select');
            select.name = `items[${transferRowIndex}][product_id]`;
            select.removeAttribute('data-select2-id');
            select.classList.remove('select2-hidden-accessible');
            select.style.display = '';
            select.selectedIndex = 0;
            
            // Remove data-select2-id from options and reset selection state
            Array.from(select.options).forEach(opt => {
                opt.removeAttribute('data-select2-id');
                opt.selected = false;
            });
            if (select.options.length > 0) select.options[0].selected = true;

            // Remove any other cloned select2-id markers from the row's elements
            newRow.querySelectorAll('[data-select2-id]').forEach(el => {
                el.removeAttribute('data-select2-id');
            });

            const qty = newRow.querySelector('.qty-input');
            qty.name = `items[${transferRowIndex}][quantity]`;
            qty.value = '';
            qty.removeAttribute('max');
            qty.placeholder = 'Qty';

            const removeBtn = newRow.querySelector('.remove-row-btn');
            removeBtn.removeAttribute('disabled');

            container.appendChild(newRow);
            transferRowIndex++;
            updateRemoveButtons();

            // Initialize select2 on the newly appended row
            initProductSelect2(select);
        }

        function removeTransferRow(btn) {
            const row = btn.closest('.transfer-item-row');
            const container = document.getElementById('transferItemsContainer');
            if (container.querySelectorAll('.transfer-item-row').length > 1) {
                const select = row.querySelector('.product-select');
                if (select && window.jQuery && jQuery(select).data('select2')) {
                    jQuery(select).select2('destroy');
                }
                row.remove();
                updateRemoveButtons();
            }
        }

        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.transfer-item-row');
            rows.forEach((r, idx) => {
                const btn = r.querySelector('.remove-row-btn');
                if (rows.length === 1) {
                    btn.setAttribute('disabled', 'true');
                } else {
                    btn.removeAttribute('disabled');
                }
            });
        }

        function openQuickTransferModal(teamName, bookId, bookIndexId, bookBundleId, prodName) {
            document.getElementById('modalTargetTeam').value = teamName;
            
            let targetProductId = '';
            if (bookIndexId && bookIndexId !== '') targetProductId = 'index_' + bookIndexId;
            else if (bookBundleId && bookBundleId !== '') targetProductId = 'bundle_' + bookBundleId;
            else if (bookId && bookId !== '') targetProductId = 'book_' + bookId;

            const firstSelect = document.querySelector('#transferItemsContainer .product-select');
            if (firstSelect && targetProductId) {
                firstSelect.value = targetProductId;
                if (window.jQuery && jQuery(firstSelect).data('select2')) {
                    jQuery(firstSelect).val(targetProductId).trigger('change');
                } else {
                    updateMaxQty(firstSelect);
                }
            }

            const modalElement = document.getElementById('newTransferModal');
            const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
            modal.show();
        }

        function triggerExcelImport() {
            const modalElement = document.getElementById('newTransferModal');
            const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
            modal.show();
            const input = document.getElementById('excelTransferInput');
            if (input) input.click();
        }

        function handleExcelImport(input) {
            if (!input.files || !input.files[0]) return;
            const file = input.files[0];
            const formData = new FormData();
            formData.append('excel_file', file);

            const container = document.getElementById('transferItemsContainer');
            let alertBox = document.getElementById('excelImportStatus');
            if (!alertBox) {
                alertBox = document.createElement('div');
                alertBox.id = 'excelImportStatus';
                container.parentNode.insertBefore(alertBox, container);
            }
            alertBox.className = 'alert alert-info py-2 px-3 mb-3 small d-flex align-items-center justify-content-between';
            alertBox.innerHTML = '<span><i class="fas fa-spinner fa-spin me-2"></i> Reading and parsing Excel file...</span>';
            alertBox.style.display = 'flex';

            fetch("{{ route('marketing.area-sales.team-stocks.parse-excel') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                input.value = '';
                if (data.status === 'success' && data.items && data.items.length > 0) {
                    // Destroy select2 on existing rows before clearing
                    container.querySelectorAll('.product-select').forEach(s => {
                        if (window.jQuery && jQuery(s).data('select2')) {
                            jQuery(s).select2('destroy');
                        }
                    });
                    container.innerHTML = '';
                    transferRowIndex = 0;

                    data.items.forEach(item => {
                        createAndPopulateTransferRow(item.product_id, item.quantity, item.stock);
                    });

                    alertBox.className = 'alert alert-success py-2 px-3 mb-3 small d-flex align-items-center justify-content-between';
                    alertBox.innerHTML = `<span><i class="fas fa-check-circle me-2"></i> <strong>Import Success:</strong> Loaded ${data.count} product(s) with quantities to transfer. You can now execute stock transfer! ${data.skipped > 0 ? '(' + data.skipped + ' empty/invalid rows skipped)' : ''}</span><button type="button" class="btn-close btn-sm" onclick="this.parentElement.remove()"></button>`;
                } else {
                    alertBox.className = 'alert alert-danger py-2 px-3 mb-3 small d-flex align-items-center justify-content-between';
                    alertBox.innerHTML = `<span><i class="fas fa-exclamation-triangle me-2"></i> ${data.message || 'No valid products found in Excel.'}</span><button type="button" class="btn-close btn-sm" onclick="this.parentElement.remove()"></button>`;
                }
            })
            .catch(err => {
                console.error('Excel Import Error:', err);
                input.value = '';
                alertBox.className = 'alert alert-danger py-2 px-3 mb-3 small d-flex align-items-center justify-content-between';
                alertBox.innerHTML = `<span><i class="fas fa-exclamation-triangle me-2"></i> Error reading Excel file. Please try again.</span><button type="button" class="btn-close btn-sm" onclick="this.parentElement.remove()"></button>`;
            });
        }

        function createAndPopulateTransferRow(productId, quantity, maxStock) {
            const container = document.getElementById('transferItemsContainer');
            
            const rowDiv = document.createElement('div');
            rowDiv.className = 'transfer-item-row row g-2 mb-2 align-items-end';
            
            let optionsHtml = '<option value="" disabled>Select product...</option>';
            @foreach($mainProducts as $prod)
            @php
                $pId = is_object($prod) ? $prod->id : ($prod['id'] ?? '');
                $pName = is_object($prod) ? $prod->name : ($prod['name'] ?? '');
                $pStock = is_object($prod) ? ($prod->stock ?? $prod->main_stock ?? 0) : ($prod['stock'] ?? $prod['main_stock'] ?? 0);
            @endphp
            optionsHtml += `<option value="{{ $pId }}" data-stock="{{ $pStock }}" ${productId === '{{ $pId }}' ? 'selected' : ''}>{{ e($pName) }} (Main Stock: {{ number_format($pStock) }} pcs)</option>`;
            @endforeach

            rowDiv.innerHTML = `
                <div class="col-md-7">
                    <label class="form-label small fw-bold mb-1">Product Title / Code (Main Warehouse Stock)</label>
                    <select name="items[${transferRowIndex}][product_id]" class="form-select product-select" required>
                        ${optionsHtml}
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold mb-1">Quantity to Transfer</label>
                    <input type="number" name="items[${transferRowIndex}][quantity]" class="form-control qty-input" min="1" max="${maxStock}" value="${quantity}" placeholder="Max: ${maxStock}" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger w-100 remove-row-btn" onclick="removeTransferRow(this)">
                        Remove
                    </button>
                </div>
            `;

            container.appendChild(rowDiv);
            transferRowIndex++;
            updateRemoveButtons();

            const selectEl = rowDiv.querySelector('.product-select');
            initProductSelect2(selectEl);
        }

        // Filter functionality for Team Inventory Table
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Select2 on initial product select fields
            document.querySelectorAll('.product-select').forEach(function(select) {
                initProductSelect2(select);
            });

            // Re-adjust select2 width when modal opens
            if (window.jQuery) {
                jQuery('#newTransferModal').on('shown.bs.modal', function () {
                    jQuery('.product-select').each(function() {
                        if (jQuery(this).data('select2')) {
                            jQuery(this).select2('destroy');
                        }
                        initProductSelect2(this);
                    });
                });
            }

            const filterBtns = document.querySelectorAll('#teamFilterGroup button');
            const rows = document.querySelectorAll('#teamStockTable .stock-row');
            const searchInput = document.getElementById('inventorySearch');

            function filterRows() {
                const activeBtn = document.querySelector('#teamFilterGroup button.active');
                const selectedTeam = activeBtn ? activeBtn.dataset.filter : 'all';
                const searchVal = searchInput ? searchInput.value.toLowerCase().trim() : '';

                rows.forEach(row => {
                    const rowTeam = row.dataset.team;
                    const rowName = row.dataset.name;

                    const matchTeam = (selectedTeam === 'all' || rowTeam === selectedTeam);
                    const matchSearch = (!searchVal || rowName.includes(searchVal));

                    if (matchTeam && matchSearch) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    filterBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    filterRows();
                });
            });

            if (searchInput) {
                searchInput.addEventListener('input', filterRows);
            }
        });
    </script>
    @endpush
</x-app-layout>

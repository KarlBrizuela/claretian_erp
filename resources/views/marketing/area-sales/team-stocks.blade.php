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

                $teamLostCount = $transfers->filter(function($t) use ($team) {
                    $tName = strtolower(trim($t->team_name));
                    $targetName = strtolower(trim($team));
                    $cleanTarget = strtolower(trim(preg_replace('/^(site\s+|team\s+)+/i', '', $team)));
                    return $tName === $targetName || str_replace('site team ', '', $tName) === $cleanTarget || str_replace('team ', '', $tName) === $cleanTarget;
                })->flatMap->items->sum('lost_quantity');
            @endphp
            <div class="col-md-6 col-lg-4 col-xl-2 mb-3">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 10px; background: #fff;">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge {{ $teamBadgeClass }} px-3 py-2 fw-bold" style="font-size: 0.85rem;">{{ $team }}</span>
                                <span class="text-muted small fw-bold">{{ $staffCount }} Staff</span>
                            </div>
                            <div class="mt-2">
                                <h3 class="fw-bold mb-0 text-dark" style="font-size: 1.4rem;">{{ number_format($teamCount) }} <span class="fs-14 text-muted fw-normal">pcs in stock</span></h3>
                                <small class="text-muted d-block">{{ $teamItemsCount }} distinct item title(s)</small>
                            </div>
                        </div>
                        <div class="mt-2 pt-2 border-top d-flex justify-content-between align-items-center">
                            <span class="text-muted small fw-semibold">Lost Stock:</span>
                            @if($teamLostCount > 0)
                                <span class="badge bg-danger text-white fw-bold px-2 py-1" style="font-size: 0.78rem;" title="Total lost inventory recorded during returns">
                                    <i class="fas fa-exclamation-triangle me-1"></i>{{ number_format($teamLostCount) }} pcs lost
                                </span>
                            @else
                                <span class="badge bg-light text-muted border fw-normal px-2 py-1" style="font-size: 0.78rem;">
                                    0 lost
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            @php
                $grandTotalLost = $transfers->flatMap->items->sum('lost_quantity');
                $totalLostItemsRecorded = $transfers->flatMap->items->where('lost_quantity', '>', 0)->count();
            @endphp
            <div class="col-md-6 col-lg-4 col-xl-2 mb-3">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 10px; background: #fff; border-top: 3px solid #dc3545 !important;">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-danger text-white px-3 py-2 fw-bold" style="font-size: 0.85rem; background-color: #dc3545 !important;">
                                    <i class="fas fa-exclamation-circle me-1"></i>TOTAL LOST
                                </span>
                                <span class="text-muted small fw-bold">All Teams</span>
                            </div>
                            <div class="mt-2">
                                <h3 class="fw-bold mb-0 text-danger" style="font-size: 1.4rem;">{{ number_format($grandTotalLost) }} <span class="fs-14 text-muted fw-normal">pcs lost</span></h3>
                                <small class="text-muted d-block">{{ $totalLostItemsRecorded }} recorded return item(s)</small>
                            </div>
                        </div>
                        <div class="mt-2 pt-2 border-top d-flex justify-content-between align-items-center">
                            <span class="text-muted small fw-semibold">Lost Overview:</span>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold px-2 py-1" style="font-size: 0.78rem; background-color: #fee2e2;">
                                All Recorded Returns
                            </span>
                        </div>
                    </div>
                </div>
            </div>
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
                    <button class="btn btn-outline-danger btn-sm px-3 shadow-sm d-inline-flex align-items-center justify-content-center gap-1" 
                            style="height: 36px; font-size: 0.85rem; font-weight: 600;" 
                            data-bs-toggle="modal" data-bs-target="#returnStockModal">
                        <i class="fas fa-undo me-1"></i> Return Stock to Main Warehouse
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
                                        <th>TYPE</th>
                                        <th>TEAM</th>
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
                                        $isReturn = ($tr->transfer_type ?? '') === 'return' || str_starts_with($tr->transfer_number, 'TSR-');
                                    @endphp
                                    <tr>
                                        <td class="fw-bold text-dark">{{ $tr->transfer_number }}</td>
                                        <td>
                                            @if($isReturn)
                                                <span class="badge bg-warning text-dark"><i class="fas fa-undo me-1"></i>Return to Warehouse</span>
                                            @else
                                                <span class="badge bg-primary"><i class="fas fa-exchange-alt me-1"></i>Warehouse to Team</span>
                                            @endif
                                        </td>
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
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom py-3 px-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" style="font-size: 1.05rem;">
                            <i class="fas fa-file-alt me-2 text-secondary"></i>Transfer Details
                        </h5>
                        <small class="text-muted fw-semibold">{{ $tr->transfer_number }}</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Transfer Info Grid -->
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3 bg-light border h-100">
                                <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Transfer Type</small>
                                @if(($tr->transfer_type ?? 'transfer') === 'return')
                                    <span class="badge px-3 py-2 fw-bold" style="background-color: #0d6efd; font-size: 0.82rem;">
                                        <i class="fas fa-undo-alt me-1"></i>Return to Warehouse
                                    </span>
                                @else
                                    <span class="badge px-3 py-2 fw-bold" style="background-color: #198754; font-size: 0.82rem;">
                                        <i class="fas fa-truck me-1"></i>Transfer to Team
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3 bg-light border h-100">
                                <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Status</small>
                                @if($tr->status === 'completed' || $tr->status === 'approved')
                                    <span class="badge bg-success px-3 py-2 fw-bold" style="font-size: 0.82rem;"><i class="fas fa-check-circle me-1"></i>Completed</span>
                                @elseif($tr->status === 'pending' || $tr->status === 'pending_prod_approval')
                                    <span class="badge bg-warning text-dark px-3 py-2 fw-bold" style="font-size: 0.82rem;"><i class="fas fa-clock me-1"></i>{{ ucwords(str_replace('_', ' ', $tr->status)) }}</span>
                                @elseif($tr->status === 'rejected')
                                    <span class="badge bg-danger px-3 py-2 fw-bold" style="font-size: 0.82rem;"><i class="fas fa-times-circle me-1"></i>Rejected</span>
                                @else
                                    <span class="badge bg-secondary px-3 py-2 fw-bold" style="font-size: 0.82rem;">{{ ucwords(str_replace('_', ' ', $tr->status)) }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3 bg-light border h-100">
                                <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Target Team</small>
                                <span class="badge bg-danger text-white px-3 py-2 fw-bold" style="font-size: 0.82rem;">{{ $tr->team_name }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3 bg-light border h-100">
                                <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Transferred By</small>
                                <span class="fw-bold text-dark"><i class="fas fa-user me-1 text-secondary"></i>{{ $tr->transferredByUser->name ?? 'System' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3 bg-light border h-100">
                                <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Date & Time</small>
                                <span class="fw-semibold text-dark"><i class="fas fa-calendar-alt me-1 text-secondary"></i>{{ $tr->created_at->format('M d, Y — h:i A') }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3 bg-light border h-100">
                                <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Notes / Remarks</small>
                                <span class="fw-medium text-dark">{{ $tr->notes ?: ($tr->remarks ?: '—') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="fas fa-boxes me-1 text-secondary"></i>Items
                        <span class="badge bg-secondary ms-1 rounded-pill">{{ $tr->items->count() }}</span>
                    </h6>
                    <div class="table-responsive rounded-2 border">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                <tr>
                                    <th class="py-2 px-3 text-secondary small fw-bold" style="font-size: 0.75rem;">PRODUCT</th>
                                    <th class="py-2 px-3 text-center text-secondary small fw-bold" style="font-size: 0.75rem; width: 80px;">TYPE</th>
                                    @if(($tr->transfer_type ?? 'transfer') === 'return')
                                        <th class="py-2 px-3 text-center text-secondary small fw-bold" style="font-size: 0.75rem; width: 100px;">RETURNED</th>
                                        <th class="py-2 px-3 text-center text-secondary small fw-bold" style="font-size: 0.75rem; width: 100px;">LOST</th>
                                    @else
                                        <th class="py-2 px-3 text-center text-secondary small fw-bold" style="font-size: 0.75rem; width: 120px;">QTY</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tr->items as $tItem)
                                <tr>
                                    <td class="px-3">
                                        <div class="fw-bold text-dark">{{ $tItem->product_name }}</div>
                                        <small class="text-muted">
                                            @if($tItem->book_index_id && $tItem->bookIndex)
                                                <i class="fas fa-barcode me-1"></i>{{ $tItem->bookIndex->article_number ?: ($tItem->bookIndex->isbn ?: 'N/A') }}
                                            @elseif($tItem->book_id && $tItem->book)
                                                <i class="fas fa-barcode me-1"></i>{{ $tItem->book->isbn ?: ($tItem->book->item_code ?: 'N/A') }}
                                            @elseif($tItem->book_bundle_id && $tItem->bookBundle)
                                                <i class="fas fa-barcode me-1"></i>{{ $tItem->bookBundle->bundle_code ?: 'N/A' }}
                                            @endif
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        @if($tItem->book_bundle_id)
                                            <span class="badge bg-warning text-white px-2 py-1" style="font-size: 0.72rem;">Bundle</span>
                                        @elseif($tItem->book_index_id)
                                            <span class="badge bg-info text-white px-2 py-1" style="font-size: 0.72rem;">Index</span>
                                        @else
                                            <span class="badge bg-primary text-white px-2 py-1" style="font-size: 0.72rem;">Book</span>
                                        @endif
                                    </td>
                                    @if(($tr->transfer_type ?? 'transfer') === 'return')
                                        <td class="text-center">
                                            @if((int)$tItem->quantity > 0)
                                                <span class="badge bg-success rounded-pill px-3 py-2 fw-bold">+{{ number_format($tItem->quantity) }} pcs</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if((int)($tItem->lost_quantity ?? 0) > 0)
                                                <span class="badge bg-danger rounded-pill px-3 py-2 fw-bold">{{ number_format($tItem->lost_quantity) }} pcs</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    @else
                                        <td class="text-center">
                                            <span class="badge bg-success rounded-pill px-3 py-2 fw-bold">+{{ number_format($tItem->quantity) }} pcs</span>
                                        </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary Footer -->
                    @php
                        $totalTransferred = $tr->items->sum('quantity');
                        $totalLost = $tr->items->sum('lost_quantity');
                    @endphp
                    <div class="d-flex justify-content-end gap-4 mt-3 p-3 bg-light rounded-3 border">
                        @if(($tr->transfer_type ?? 'transfer') === 'return')
                            <div>
                                <span class="text-muted small fw-bold me-1">Total Returned:</span>
                                <span class="fw-bold text-success">{{ number_format($totalTransferred) }} pcs</span>
                            </div>
                            @if($totalLost > 0)
                            <div>
                                <span class="text-muted small fw-bold me-1">Total Lost:</span>
                                <span class="fw-bold text-danger">{{ number_format($totalLost) }} pcs</span>
                            </div>
                            @endif
                        @else
                            <div>
                                <span class="text-muted small fw-bold me-1">Total Items:</span>
                                <span class="fw-bold text-dark">{{ $tr->items->count() }}</span>
                            </div>
                            <div>
                                <span class="text-muted small fw-bold me-1">Total Qty:</span>
                                <span class="fw-bold text-success">{{ number_format($totalTransferred) }} pcs</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Return Stock Modal -->
    <div class="modal fade" id="returnStockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 1050px;">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('marketing.area-sales.team-stocks.return') }}" method="POST" id="returnStockForm">
                    @csrf
                    <div class="modal-header border-bottom bg-white py-3 px-4">
                        <h5 class="modal-title text-dark fw-bold" style="font-size: 1.1rem; color: #0f172a;">
                            Return Stock to Main Warehouse
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 bg-white">
                        <div class="row g-3 mb-3">
                            <div class="col-md-5">
                                <label class="form-label small fw-bold text-secondary mb-1">SELECT TEAM <span class="text-danger">*</span></label>
                                <select name="team_name" id="returnTeamSelect" class="form-select border-slate" required>
                                    <option value="" disabled selected>Select Sales Team...</option>
                                    <option value="Team A">Team A</option>
                                    <option value="Team B">Team B</option>
                                    <option value="Team C">Team C</option>
                                    <option value="Book Sales">Book Sales</option>
                                    <option value="MIBF">MIBF</option>
                                </select>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label small fw-bold text-secondary mb-1">REMARKS / NOTES</label>
                                <input type="text" name="notes" class="form-control border-slate" placeholder="Optional return remarks...">
                            </div>
                        </div>

                        <!-- Barcode Scanner Input Section -->
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-9">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white text-secondary border-end-0"><i class="fas fa-barcode"></i></span>
                                        <input type="text" id="returnBarcodeInput" class="form-control border-start-0" placeholder="Scan or type ISBN/barcode and press Enter..." disabled autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-outline-danger w-100 fw-semibold" id="btnScanReturnBarcode" disabled style="border-color: #D9251C; color: #D9251C;">
                                        <i class="fas fa-barcode me-1"></i>Scan / Add
                                    </button>
                                </div>
                            </div>
                            <div id="returnScanFeedback" class="mt-2 text-secondary small fw-medium">Select a team above to start barcode scanning...</div>
                        </div>

                        <!-- Items Table -->
                        <div class="table-responsive rounded-2 border" style="max-height: 360px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0" id="returnItemsTable">
                                <thead style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                    <tr>
                                        <th class="py-2 px-3 text-secondary uppercase small fw-bold">PRODUCT DESCRIPTION</th>
                                        <th class="py-2 px-3 text-center text-secondary uppercase small fw-bold" style="width: 130px;">AVAIL. QTY</th>
                                        <th class="py-2 px-3 text-center text-secondary uppercase small fw-bold" style="width: 140px;">RETURN QTY</th>
                                        <th class="py-2 px-3 text-center text-secondary uppercase small fw-bold" style="width: 140px;">LOST QTY</th>
                                        <th class="py-2 px-3 text-center text-secondary uppercase small fw-bold" style="width: 150px;">STATUS</th>
                                    </tr>
                                </thead>
                                <tbody id="returnItemsBody">
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted small">Please select a team above to view available stock for return.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Return Summary Bar -->
                        <div class="d-flex justify-content-between align-items-center mt-3 p-3 bg-light rounded-3 border flex-wrap gap-2">
                            <div>
                                <span class="text-secondary small fw-bold uppercase">SELECTED TEAM:</span>
                                <span id="summaryReturnTeamName" class="badge bg-danger ms-1 fw-semibold">None</span>
                            </div>
                            <div class="d-flex gap-4">
                                <div>
                                    <span class="text-secondary small fw-medium me-1">Total Items:</span>
                                    <span id="summaryTotalItems" class="fw-bold text-dark">0</span>
                                </div>
                                <div>
                                    <span class="text-secondary small fw-medium me-1">Returned:</span>
                                    <span id="summaryTotalReturned" class="fw-bold text-success">0 pcs</span>
                                </div>
                                <div>
                                    <span class="text-secondary small fw-medium me-1">Lost:</span>
                                    <span id="summaryTotalLost" class="fw-bold text-danger">0 pcs</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top py-3 px-4">
                        <button type="button" class="btn btn-light border px-4 fw-semibold text-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger px-4 fw-semibold" id="btnConfirmReturn" style="background-color: #D9251C; border: none;" disabled>
                            Confirm & Return Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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

        const allTeamStockData = @json($teamStockJsonData ?? []);

        function initProductSelect2(selectElement) {
            if (window.jQuery && typeof jQuery.fn.select2 === 'function') {
                jQuery(selectElement).select2({
                    dropdownParent: jQuery('#newTransferModal'),
                    width: '100%'
                });
            }
        }

        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.transfer-item-row');
            rows.forEach(row => {
                const btn = row.querySelector('.remove-row-btn');
                if (btn) btn.disabled = (rows.length <= 1);
            });
        }

        function addTransferRow() {
            const container = document.getElementById('transferItemsContainer');
            const firstRow = container.querySelector('.transfer-item-row');
            if (!firstRow) return;

            const newRow = firstRow.cloneNode(true);
            
            const selectEl = newRow.querySelector('.product-select');
            if (selectEl) {
                selectEl.name = `items[${transferRowIndex}][product_id]`;
                selectEl.selectedIndex = 0;
                if (window.jQuery && jQuery(selectEl).data('select2')) {
                    jQuery(selectEl).select2('destroy');
                    selectEl.removeAttribute('data-select2-id');
                    selectEl.querySelectorAll('option').forEach(opt => opt.removeAttribute('data-select2-id'));
                }
            }

            const qtyInput = newRow.querySelector('.qty-input');
            if (qtyInput) {
                qtyInput.name = `items[${transferRowIndex}][quantity]`;
                qtyInput.value = 1;
            }

            container.appendChild(newRow);
            transferRowIndex++;
            updateRemoveButtons();

            if (selectEl) {
                initProductSelect2(selectEl);
            }
        }

        function removeTransferRow(btn) {
            const rows = document.querySelectorAll('.transfer-item-row');
            if (rows.length > 1) {
                const row = btn.closest('.transfer-item-row');
                const selectEl = row.querySelector('.product-select');
                if (window.jQuery && selectEl && jQuery(selectEl).data('select2')) {
                    jQuery(selectEl).select2('destroy');
                }
                row.remove();
                updateRemoveButtons();
            }
        }

        function triggerExcelImport() {
            document.getElementById('excelTransferInput').click();
        }

        function handleExcelImport(input) {
            const file = input.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('excel_file', file);
            formData.append('_token', '{{ csrf_token() }}');

            const container = document.getElementById('transferItemsContainer');
            container.innerHTML = `
                <div class="text-center py-4 text-muted" id="excelLoadingState">
                    <div class="spinner-border text-danger spinner-border-sm me-2" role="status"></div>
                    Parsing Excel file and matching products...
                </div>
            `;

            fetch("{{ route('marketing.area-sales.team-stocks.parse-excel') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                input.value = '';
                if (data.status === 'success' && data.items && data.items.length > 0) {
                    container.innerHTML = '';
                    transferRowIndex = 0;

                    data.items.forEach(item => {
                        createAndPopulateTransferRow(item.product_id, item.quantity, item.stock);
                    });

                    let msg = `Successfully imported ${data.count} item(s) from Excel!`;
                    if (data.skipped > 0) {
                        msg += ` (${data.skipped} row(s) skipped/unmatched)`;
                    }
                    alert(msg);
                } else {
                    alert('Excel Import Error: ' + (data.message || 'No valid products found.'));
                    location.reload();
                }
            })
            .catch(err => {
                input.value = '';
                alert('Failed to import Excel file: ' + err.message);
                location.reload();
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

        // Return Stock Feature Logic
        function normalizeBarcode(bc) {
            return (bc || '').toString().toLowerCase().replace(/[\s\-\_\.]/g, '');
        }

        let currentReturnItems = [];

        function renderReturnTable(teamName) {
            const tbody = document.getElementById('returnItemsBody');
            const summaryTeam = document.getElementById('summaryReturnTeamName');
            const barcodeInput = document.getElementById('returnBarcodeInput');
            const scanBtn = document.getElementById('btnScanReturnBarcode') || document.getElementById('btnFocusReturnBarcode');
            const confirmBtn = document.getElementById('btnConfirmReturn');
            const feedbackEl = document.getElementById('returnScanFeedback');

            if (summaryTeam) summaryTeam.textContent = teamName || 'None';

            if (!teamName) {
                if (tbody) tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted small">Please select a team above to view available stock for return.</td></tr>';
                if (barcodeInput) barcodeInput.disabled = true;
                if (scanBtn) scanBtn.disabled = true;
                if (confirmBtn) confirmBtn.disabled = true;
                if (feedbackEl) feedbackEl.textContent = 'Select a team above to start barcode scanning...';
                return;
            }

            // Filter items for the selected team
            const rawTeam = teamName.trim();
            const cleanName = rawTeam.replace(/^(site\s+|team\s+)+/i, '').trim().toLowerCase();

            currentReturnItems = allTeamStockData.filter(item => {
                const itemTeam = (item.team_name || '').trim().toLowerCase();
                const itemClean = itemTeam.replace(/^(site\s+|team\s+)+/i, '').trim();
                return (itemTeam === rawTeam.toLowerCase() || itemClean === cleanName) && (item.available_qty > 0);
            });

            if (currentReturnItems.length === 0) {
                if (tbody) tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted small">No items with positive stock found under <strong>${teamName}</strong>.</td></tr>`;
                if (barcodeInput) barcodeInput.disabled = true;
                if (scanBtn) scanBtn.disabled = true;
                if (confirmBtn) confirmBtn.disabled = true;
                if (feedbackEl) feedbackEl.textContent = `No available stock under ${teamName}.`;
                updateReturnTotals();
                return;
            }

            if (barcodeInput) {
                barcodeInput.disabled = false;
                barcodeInput.value = '';
                barcodeInput.focus();
            }
            if (scanBtn) scanBtn.disabled = false;
            if (feedbackEl) feedbackEl.innerHTML = `<span class="text-success small fw-medium"><i class="fas fa-check-circle me-1"></i>Ready! ${currentReturnItems.length} product(s) loaded for ${teamName}. Scan barcode now.</span>`;

            let html = '';
            currentReturnItems.forEach((item, idx) => {
                const barcodesStr = (item.barcodes && item.barcodes.length > 0) ? item.barcodes.join(', ') : 'N/A';
                html += `
                    <tr id="return_row_${idx}" class="return-item-row" data-index="${idx}" data-product-id="${item.product_id}" data-max-qty="${item.available_qty}" style="transition: all 0.2s ease;">
                        <td class="py-2 px-3">
                            <div class="fw-semibold text-dark" style="font-size: 0.88rem;">${item.product_name}</div>
                            <small class="text-muted" style="font-size: 0.76rem;"><i class="fas fa-barcode me-1"></i>${barcodesStr}</small>
                            <input type="hidden" name="items[${idx}][product_id]" value="${item.product_id}">
                        </td>
                        <td class="text-center py-2 px-3 fw-bold text-secondary" style="font-size: 0.88rem;">
                            ${item.available_qty} pcs
                        </td>
                        <td class="text-center py-2 px-3">
                            <input type="number" name="items[${idx}][returned_qty]" class="form-control form-control-sm text-center fw-bold input-returned-qty border-slate" value="0" min="0" max="${item.available_qty}" oninput="onReturnQtyChanged(${idx})">
                        </td>
                        <td class="text-center py-2 px-3">
                            <input type="number" name="items[${idx}][lost_qty]" class="form-control form-control-sm text-center fw-bold text-danger input-lost-qty border-slate" value="0" min="0" max="${item.available_qty}" oninput="onReturnQtyChanged(${idx})">
                        </td>
                        <td class="text-center py-2 px-3 row-status-cell">
                            <span class="badge bg-light text-secondary border fw-medium">Pending</span>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
            updateReturnTotals();
        }

        function onReturnQtyChanged(idx) {
            const row = document.getElementById(`return_row_${idx}`);
            if (!row) return;

            const maxQty = parseInt(row.getAttribute('data-max-qty')) || 0;
            const retInput = row.querySelector('.input-returned-qty');
            const lostInput = row.querySelector('.input-lost-qty');
            const statusCell = row.querySelector('.row-status-cell');

            let retVal = parseInt(retInput.value) || 0;
            let lostVal = parseInt(lostInput.value) || 0;

            if (retVal < 0) retVal = 0;
            if (lostVal < 0) lostVal = 0;

            if (retVal + lostVal > maxQty) {
                alert(`Total returned (${retVal}) + lost (${lostVal}) exceeds available stock (${maxQty} pcs)!`);
                retVal = Math.min(retVal, maxQty);
                lostVal = Math.min(lostVal, maxQty - retVal);
                retInput.value = retVal;
                lostInput.value = lostVal;
            }

            if (retVal > 0 || lostVal > 0) {
                row.style.backgroundColor = '#f0fdf4';
                row.style.borderLeft = '3px solid #22c55e';
                let statusHtml = '';
                if (retVal > 0 && lostVal > 0) {
                    statusHtml = `<span class="badge bg-success text-white fw-medium"><i class="fas fa-check me-1"></i>Ret: ${retVal} | Lost: ${lostVal}</span>`;
                } else if (retVal > 0) {
                    statusHtml = `<span class="badge bg-success text-white fw-medium"><i class="fas fa-check me-1"></i>Returned (${retVal})</span>`;
                } else {
                    statusHtml = `<span class="badge bg-danger text-white fw-medium"><i class="fas fa-exclamation-circle me-1"></i>Lost (${lostVal})</span>`;
                }
                statusCell.innerHTML = statusHtml;
            } else {
                row.style.backgroundColor = '';
                row.style.borderLeft = '';
                statusCell.innerHTML = '<span class="badge bg-light text-secondary border fw-medium">Pending</span>';
            }

            updateReturnTotals();
        }

        function addReturnQty(idx, qty) {
            const row = document.getElementById(`return_row_${idx}`);
            if (!row) return;
            const retInput = row.querySelector('.input-returned-qty');
            retInput.value = (parseInt(retInput.value) || 0) + qty;
            onReturnQtyChanged(idx);
        }

        function addLostQty(idx, qty) {
            const row = document.getElementById(`return_row_${idx}`);
            if (!row) return;
            const lostInput = row.querySelector('.input-lost-qty');
            lostInput.value = (parseInt(lostInput.value) || 0) + qty;
            onReturnQtyChanged(idx);
        }

        function resetReturnRow(idx) {
            const row = document.getElementById(`return_row_${idx}`);
            if (!row) return;
            row.querySelector('.input-returned-qty').value = 0;
            row.querySelector('.input-lost-qty').value = 0;
            onReturnQtyChanged(idx);
        }

        function updateReturnTotals() {
            let totalReturned = 0;
            let totalLost = 0;
            const rows = document.querySelectorAll('#returnItemsBody .return-item-row');

            rows.forEach(row => {
                const retVal = parseInt(row.querySelector('.input-returned-qty')?.value) || 0;
                const lostVal = parseInt(row.querySelector('.input-lost-qty')?.value) || 0;
                totalReturned += retVal;
                totalLost += lostVal;
            });

            document.getElementById('summaryTotalItems').textContent = rows.length;
            document.getElementById('summaryTotalReturned').textContent = `${totalReturned} pcs`;
            document.getElementById('summaryTotalLost').textContent = `${totalLost} pcs`;

            const confirmBtn = document.getElementById('btnConfirmReturn');
            confirmBtn.disabled = (totalReturned === 0 && totalLost === 0);
        }

        function processReturnBarcodeScan(rawBarcode) {
            const input = (rawBarcode || '').trim();
            if (!input) {
                const bcInput = document.getElementById('returnBarcodeInput');
                if (bcInput) bcInput.focus();
                return;
            }

            const normalized = normalizeBarcode(input);
            const feedbackEl = document.getElementById('returnScanFeedback');
            let matchedIdx = -1;

            currentReturnItems.forEach((item, idx) => {
                if (matchedIdx !== -1) return;
                const normBarcodes = (item.barcodes || []).map(normalizeBarcode);
                const normName = normalizeBarcode(item.product_name);

                const isExactBarcode = normBarcodes.includes(normalized);
                const isPartialBarcode = normBarcodes.some(b => b.length > 3 && (b.includes(normalized) || normalized.includes(b)));
                const isNameMatch = normName.includes(normalized) || input.toLowerCase() === (item.product_name || '').toLowerCase();

                if (isExactBarcode || isPartialBarcode || isNameMatch) {
                    matchedIdx = idx;
                }
            });

            if (matchedIdx !== -1) {
                const row = document.getElementById(`return_row_${matchedIdx}`);
                const maxQty = parseInt(row.getAttribute('data-max-qty')) || 0;
                const retInput = row.querySelector('.input-returned-qty');
                const lostInput = row.querySelector('.input-lost-qty');
                const currentRet = parseInt(retInput.value) || 0;
                const currentLost = parseInt(lostInput.value) || 0;

                if (currentRet + currentLost < maxQty) {
                    retInput.value = currentRet + 1;
                    onReturnQtyChanged(matchedIdx);

                    row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    feedbackEl.innerHTML = `<span class="text-success small fw-medium"><i class="fas fa-check-circle me-1"></i>Scanned! "${currentReturnItems[matchedIdx].product_name}" (Returned Qty: ${currentRet + 1})</span>`;
                } else {
                    feedbackEl.innerHTML = `<span class="text-warning small fw-medium"><i class="fas fa-exclamation-triangle me-1"></i>Stock limit reached for "${currentReturnItems[matchedIdx].product_name}" (Max: ${maxQty} pcs).</span>`;
                }
            } else {
                feedbackEl.innerHTML = `<span class="text-danger small fw-medium"><i class="fas fa-times-circle me-1"></i>Barcode / Title "${input}" not found in available stock for this team!</span>`;
            }

            const barcodeInput = document.getElementById('returnBarcodeInput');
            if (barcodeInput) {
                barcodeInput.value = '';
                barcodeInput.focus();
            }
        }

        // Filter functionality for Team Inventory Table & Modal Listeners
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.product-select').forEach(function(select) {
                initProductSelect2(select);
            });

            if (window.jQuery) {
                jQuery('#newTransferModal').on('shown.bs.modal', function () {
                    jQuery('.product-select').each(function() {
                        if (jQuery(this).data('select2')) {
                            jQuery(this).select2('destroy');
                        }
                        initProductSelect2(this);
                    });
                });

                jQuery('#returnStockModal').on('shown.bs.modal', function () {
                    const teamSelect = document.getElementById('returnTeamSelect');
                    if (teamSelect.value) {
                        renderReturnTable(teamSelect.value);
                    }
                    const barcodeInput = document.getElementById('returnBarcodeInput');
                    if (barcodeInput && !barcodeInput.disabled) {
                        setTimeout(() => barcodeInput.focus(), 150);
                    }
                });
            }

            document.getElementById('returnTeamSelect')?.addEventListener('change', function() {
                renderReturnTable(this.value);
            });

            const barcodeInput = document.getElementById('returnBarcodeInput');
            const scanBtn = document.getElementById('btnScanReturnBarcode');

            const handleBarcodeEvent = function(e) {
                if (e.key === 'Enter' || e.keyCode === 13 || e.key === 'Tab' || e.keyCode === 9) {
                    e.preventDefault();
                    e.stopPropagation();
                    processReturnBarcodeScan(barcodeInput ? barcodeInput.value : '');
                    return false;
                }
            };

            if (barcodeInput) {
                barcodeInput.addEventListener('keydown', handleBarcodeEvent);
                barcodeInput.addEventListener('keypress', handleBarcodeEvent);
            }

            if (scanBtn) {
                scanBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    processReturnBarcodeScan(barcodeInput ? barcodeInput.value : '');
                });
            }

            document.getElementById('returnStockForm')?.addEventListener('submit', function(e) {
                if (document.activeElement === barcodeInput && barcodeInput.value.trim() !== '') {
                    e.preventDefault();
                    processReturnBarcodeScan(barcodeInput.value);
                    return false;
                }
            });

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

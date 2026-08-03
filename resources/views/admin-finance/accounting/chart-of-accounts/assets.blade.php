<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; transition: transform 0.2s;">
    <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: rgba(255, 0, 0, 0.1); color: #ff0000;">
                <i class="las la-wallet fs-24"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark fs-18">Assets</h5>
                <p class="text-muted small mb-0">Economic resources owned or controlled by the organization</p>
            </div>
        </div>
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold">8 Account Categories</span>
    </div>
    <div class="card-body pt-2">
        <div class="row g-3">
            <!-- Cash on Hand Card -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; transition: all 0.2s ease; border-left: 4px solid #ff0000 !important; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#cashOnHandModal">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="text-primary"><i class="las la-coins fs-24"></i></span>
                            <h6 class="mb-0 fw-bold text-dark fs-15">Cash on Hand</h6>
                        </div>
                        <p class="text-muted small mb-3">Current cash physically present in office registers and vaults</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-light">
                            <h5 class="mb-0 fw-bold text-dark fs-16">₱{{ number_format($balances['cash_on_hand'], 2) }}</h5>
                            <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold">Active</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Petty Cash Card -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; transition: all 0.2s ease; border-left: 4px solid #ff0000 !important; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#pettyCashModal">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="text-primary"><i class="las la-cash-register fs-24"></i></span>
                            <h6 class="mb-0 fw-bold text-dark fs-15">Petty Cash</h6>
                        </div>
                        <p class="text-muted small mb-3">Imprest fund maintained for minor operational disbursements</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-light">
                            <h5 class="mb-0 fw-bold text-dark fs-16">₱{{ number_format($balances['petty_cash'], 2) }}</h5>
                            <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold">Active</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bank Accounts Card -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; transition: all 0.2s ease; border-left: 4px solid #ff0000 !important; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#bankAccountsModal">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="text-primary"><i class="las la-university fs-24"></i></span>
                            <h6 class="mb-0 fw-bold text-dark fs-15">Bank Accounts</h6>
                        </div>
                        <p class="text-muted small mb-3">Savings, checking, and checking accounts in partner banks</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-light">
                            <h5 class="mb-0 fw-bold text-dark fs-16">₱{{ number_format($balances['bank_accounts'], 2) }}</h5>
                            <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold">Active</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Receivables Card -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; transition: all 0.2s ease; border-left: 4px solid #ff0000 !important; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#receivablesModal">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="text-primary"><i class="las la-file-invoice-dollar fs-24"></i></span>
                            <h6 class="mb-0 fw-bold text-dark fs-15">Receivables</h6>
                        </div>
                        <p class="text-muted small mb-3">Outstanding invoices and customer account balances</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-light">
                            <h5 class="mb-0 fw-bold text-dark fs-16">₱{{ number_format($balances['receivables'], 2) }}</h5>
                            <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold">Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory (Modal-triggered card) -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; transition: all 0.2s ease; border-left: 4px solid #ff0000 !important; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#inventoryModal">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="text-primary"><i class="las la-boxes fs-24"></i></span>
                            <h6 class="mb-0 fw-bold text-dark fs-15">Inventory</h6>
                        </div>
                        <p class="text-muted small mb-3">Valuation of raw materials, work in progress, and finished products</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-light">
                            <h5 class="mb-0 fw-bold text-dark fs-16">
                                ₱{{ number_format($balances['inventory_raw_materials'] + $balances['inventory_work_in_progress'] + $balances['inventory_finished_goods'], 2) }}
                            </h5>
                            <span class="badge bg-light text-secondary rounded-pill small px-2 py-0.5">3 Sub-accounts</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fixed Assets Card -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; transition: all 0.2s ease; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Fixed Assets', 'Long-term physical properties (offices, equipment, vehicles)')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="text-primary"><i class="las la-building fs-24"></i></span>
                            <h6 class="mb-0 fw-bold text-dark fs-15">Fixed Assets</h6>
                        </div>
                        <p class="text-muted small mb-3">Long-term physical properties (offices, equipment, vehicles)</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-light">
                            <h5 class="mb-0 fw-bold text-dark fs-16">₱{{ number_format($balances['fixed_assets'], 2) }}</h5>
                            <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold">Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Investments Card -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; transition: all 0.2s ease; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Investments', 'Equities, securities, and venture capital investments')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="text-primary"><i class="las la-chart-pie fs-24"></i></span>
                            <h6 class="mb-0 fw-bold text-dark fs-15">Investments</h6>
                        </div>
                        <p class="text-muted small mb-3">Equities, securities, and venture capital investments</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-light">
                            <h5 class="mb-0 fw-bold text-dark fs-16">₱{{ number_format($balances['investments'], 2) }}</h5>
                            <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold">Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deposits Card -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; transition: all 0.2s ease; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Deposits', 'Refundable rental, utility, or escrow security deposits')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="text-primary"><i class="las la-hand-holding-usd fs-24"></i></span>
                            <h6 class="mb-0 fw-bold text-dark fs-15">Deposits</h6>
                        </div>
                        <p class="text-muted small mb-3">Refundable rental, utility, or escrow security deposits</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-light">
                            <h5 class="mb-0 fw-bold text-dark fs-16">₱{{ number_format($balances['deposits'], 2) }}</h5>
                            <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold">Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

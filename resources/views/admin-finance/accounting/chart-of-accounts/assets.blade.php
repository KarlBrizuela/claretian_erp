<div class="card shadow-sm border-0 mb-4" style="border-radius: 8px; border: 1px solid #e2e8f0; background: #ffffff;">
    <div class="card-header bg-white border-0 pt-3 pb-2 px-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(59, 130, 246, 0.08); color: #3b82f6;">
                <i class="las la-wallet fs-20"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold fs-16" style="color: #000000;">Assets</h5>
                <p class="text-muted small mb-0">Economic resources owned or controlled by the organization.</p>
            </div>
        </div>
        <span class="badge bg-light text-dark border px-2.5 py-1.5 rounded-pill small fw-bold">8 Account Categories</span>
    </div>
    <div class="card-body p-3 pt-1">
        <div class="row g-2">
            <!-- Cash on Hand Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card assets-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#cashOnHandModal">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(59, 130, 246, 0.08); color: #3b82f6;">
                                    <i class="las la-coins fs-20"></i>
                                </div>
                                <span class="badge status-badge {{ ($accountDetails['1010']->is_active ?? 1) ? 'bg-soft-success text-success' : 'bg-light text-secondary' }} px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; cursor: pointer; {{ ($accountDetails['1010']->is_active ?? 1) ? 'background-color: rgba(16, 185, 129, 0.1); color: #10b981;' : '' }}" data-type="coa" data-id="{{ $accountDetails['1010']->id ?? '' }}">
                                    {{ ($accountDetails['1010']->is_active ?? 1) ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Cash on Hand</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Current cash physically present in office registers and vaults.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['cash_on_hand'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Petty Cash Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card assets-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#pettyCashModal">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(59, 130, 246, 0.08); color: #3b82f6;">
                                    <i class="las la-cash-register fs-20"></i>
                                </div>
                                <span class="badge status-badge {{ ($accountDetails['1015']->is_active ?? 1) ? 'bg-soft-success text-success' : 'bg-light text-secondary' }} px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; cursor: pointer; {{ ($accountDetails['1015']->is_active ?? 1) ? 'background-color: rgba(16, 185, 129, 0.1); color: #10b981;' : '' }}" data-type="coa" data-id="{{ $accountDetails['1015']->id ?? '' }}">
                                    {{ ($accountDetails['1015']->is_active ?? 1) ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Petty Cash</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Imprest fund maintained for minor operational disbursements.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['petty_cash'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bank Accounts Cards (Dynamic/Specific) -->
            @foreach($companyBankAccounts as $acct)
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card assets-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('{{ $acct->bank_name }}', '{{ $acct->account_name }} ({{ $acct->account_number }})', '{{ ($balances['bank_balances'][$acct->account_code] ?? 0.00) + $acct->current_balance }}')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(59, 130, 246, 0.08); color: #3b82f6;">
                                    <i class="las la-university fs-20"></i>
                                </div>
                                <span class="badge status-badge {{ ($acct->status === 'Active') ? 'bg-soft-success text-success' : 'bg-light text-secondary' }} px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; cursor: pointer; {{ ($acct->status === 'Active') ? 'background-color: rgba(16, 185, 129, 0.1); color: #10b981;' : '' }}" data-type="bank" data-id="{{ $acct->id }}">
                                    {{ ($acct->status === 'Active') ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">{{ $acct->bank_name }}</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">{{ $acct->account_name }} ({{ $acct->account_number }})</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format(($balances['bank_balances'][$acct->account_code] ?? 0.00) + $acct->current_balance, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            
            <!-- Receivables Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card assets-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#receivablesModal">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(59, 130, 246, 0.08); color: #3b82f6;">
                                    <i class="las la-file-invoice-dollar fs-20"></i>
                                </div>
                                <span class="badge status-badge {{ ($accountDetails['1200']->is_active ?? 1) ? 'bg-soft-success text-success' : 'bg-light text-secondary' }} px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; cursor: pointer; {{ ($accountDetails['1200']->is_active ?? 1) ? 'background-color: rgba(16, 185, 129, 0.1); color: #10b981;' : '' }}" data-type="coa" data-id="{{ $accountDetails['1200']->id ?? '' }}">
                                    {{ ($accountDetails['1200']->is_active ?? 1) ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Receivables</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Outstanding invoices and customer account balances.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['receivables'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Inventory Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card assets-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#inventoryModal">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(59, 130, 246, 0.08); color: #3b82f6;">
                                    <i class="las la-boxes fs-20"></i>
                                </div>
                                <span class="badge bg-light text-secondary px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; border: 1px solid #cbd5e1;">3 Sub-accounts</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Inventory</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Valuation of raw materials, work in progress, and finished products.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['inventory_raw_materials'] + $balances['inventory_work_in_progress'] + $balances['inventory_finished_goods'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Fixed Assets Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card assets-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Fixed Assets', 'Long-term physical properties (offices, equipment, vehicles)', '{{ $balances['fixed_assets'] }}')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(59, 130, 246, 0.08); color: #3b82f6;">
                                    <i class="las la-building fs-20"></i>
                                </div>
                                <span class="badge status-badge {{ ($accountDetails['1600']->is_active ?? 1) ? 'bg-soft-success text-success' : 'bg-light text-secondary' }} px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; cursor: pointer; {{ ($accountDetails['1600']->is_active ?? 1) ? 'background-color: rgba(16, 185, 129, 0.1); color: #10b981;' : '' }}" data-type="coa" data-id="{{ $accountDetails['1600']->id ?? '' }}">
                                    {{ ($accountDetails['1600']->is_active ?? 1) ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Fixed Assets</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Long-term physical properties (offices, equipment, vehicles).</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['fixed_assets'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Investments Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card assets-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Investments', 'Equities, securities, and venture capital investments', '{{ $balances['investments'] }}')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(59, 130, 246, 0.08); color: #3b82f6;">
                                    <i class="las la-chart-pie fs-20"></i>
                                </div>
                                <span class="badge status-badge {{ ($accountDetails['1700']->is_active ?? 1) ? 'bg-soft-success text-success' : 'bg-light text-secondary' }} px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; cursor: pointer; {{ ($accountDetails['1700']->is_active ?? 1) ? 'background-color: rgba(16, 185, 129, 0.1); color: #10b981;' : '' }}" data-type="coa" data-id="{{ $accountDetails['1700']->id ?? '' }}">
                                    {{ ($accountDetails['1700']->is_active ?? 1) ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Investments</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Equities, securities, and venture capital investments.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['investments'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Deposits Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card assets-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Deposits', 'Refundable rental, utility, or escrow security deposits', '{{ $balances['deposits'] }}')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(59, 130, 246, 0.08); color: #3b82f6;">
                                    <i class="las la-hand-holding-usd fs-20"></i>
                                </div>
                                <span class="badge status-badge {{ ($accountDetails['1800']->is_active ?? 1) ? 'bg-soft-success text-success' : 'bg-light text-secondary' }} px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; cursor: pointer; {{ ($accountDetails['1800']->is_active ?? 1) ? 'background-color: rgba(16, 185, 129, 0.1); color: #10b981;' : '' }}" data-type="coa" data-id="{{ $accountDetails['1800']->id ?? '' }}">
                                    {{ ($accountDetails['1800']->is_active ?? 1) ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Deposits</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Refundable rental, utility, or escrow security deposits.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['deposits'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

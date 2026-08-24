<div class="card shadow-sm border-0 mb-4" style="border-radius: 8px; border: 1px solid #e2e8f0; background: #ffffff;">
    <div class="card-header bg-white border-0 pt-3 pb-2 px-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(245, 158, 11, 0.08); color: #f59e0b;">
                <i class="las la-credit-card fs-20"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold fs-16" style="color: #000000;">Liabilities</h5>
                <p class="text-muted small mb-0">Financial debts or obligations arising during business operations.</p>
            </div>
        </div>
        <span class="badge bg-light text-dark border px-2.5 py-1.5 rounded-pill small fw-bold">7 Account Categories</span>
    </div>
    <div class="card-body p-3 pt-1">
        <div class="row g-2">
            <!-- Suppliers Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card liabilities-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#suppliersModal">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(245, 158, 11, 0.08); color: #f59e0b;">
                                    <i class="las la-truck fs-20"></i>
                                </div>
                                <span class="badge status-badge {{ ($accountDetails['2000']->is_active ?? 1) ? 'bg-soft-success text-success' : 'bg-light text-secondary' }} px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; cursor: pointer; {{ ($accountDetails['2000']->is_active ?? 1) ? 'background-color: rgba(16, 185, 129, 0.1); color: #10b981;' : '' }}" data-type="coa" data-id="{{ $accountDetails['2000']->id ?? '' }}">
                                    {{ ($accountDetails['2000']->is_active ?? 1) ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Suppliers</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Amounts owed to vendor partners for merchandise & raw materials.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['suppliers'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Payables Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card liabilities-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Payables', 'Short-term liabilities to creditors and operational suppliers', '{{ $balances['payables'] }}')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(245, 158, 11, 0.08); color: #f59e0b;">
                                    <i class="las la-file-invoice fs-20"></i>
                                </div>
                                <span class="badge status-badge {{ ($accountDetails['2200']->is_active ?? 1) ? 'bg-soft-success text-success' : 'bg-light text-secondary' }} px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; cursor: pointer; {{ ($accountDetails['2200']->is_active ?? 1) ? 'background-color: rgba(16, 185, 129, 0.1); color: #10b981;' : '' }}" data-type="coa" data-id="{{ $accountDetails['2200']->id ?? '' }}">
                                    {{ ($accountDetails['2200']->is_active ?? 1) ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Payables</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Short-term liabilities to creditors and operational suppliers.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['payables'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Loans Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card liabilities-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Loans', 'Long-term or short-term notes payable and bank lines of credit', '{{ $balances['loans'] }}')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(245, 158, 11, 0.08); color: #f59e0b;">
                                    <i class="las la-balance-scale fs-20"></i>
                                </div>
                                <span class="badge status-badge {{ ($accountDetails['2300']->is_active ?? 1) ? 'bg-soft-success text-success' : 'bg-light text-secondary' }} px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; cursor: pointer; {{ ($accountDetails['2300']->is_active ?? 1) ? 'background-color: rgba(16, 185, 129, 0.1); color: #10b981;' : '' }}" data-type="coa" data-id="{{ $accountDetails['2300']->id ?? '' }}">
                                    {{ ($accountDetails['2300']->is_active ?? 1) ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Loans</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Long-term or short-term notes payable and bank lines of credit.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['loans'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Taxes Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card liabilities-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Taxes', 'Accrued corporate taxes, VAT payable, and withholding taxes', '{{ $balances['taxes'] }}')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(245, 158, 11, 0.08); color: #f59e0b;">
                                    <i class="las la-percent fs-20"></i>
                                </div>
                                <span class="badge status-badge {{ ($accountDetails['2100']->is_active ?? 1) ? 'bg-soft-success text-success' : 'bg-light text-secondary' }} px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; cursor: pointer; {{ ($accountDetails['2100']->is_active ?? 1) ? 'background-color: rgba(16, 185, 129, 0.1); color: #10b981;' : '' }}" data-type="coa" data-id="{{ $accountDetails['2100']->id ?? '' }}">
                                    {{ ($accountDetails['2100']->is_active ?? 1) ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Taxes</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Accrued corporate taxes, VAT payable, and withholding taxes.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['taxes'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Government Contributions Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card liabilities-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Government Contributions', 'SSS, PhilHealth, Pag-IBIG premiums, and withholding payable', '{{ $balances['government_contributions'] }}')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(245, 158, 11, 0.08); color: #f59e0b;">
                                    <i class="las la-landmark fs-20"></i>
                                </div>
                                <span class="badge status-badge {{ ($accountDetails['2400']->is_active ?? 1) ? 'bg-soft-success text-success' : 'bg-light text-secondary' }} px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; cursor: pointer; {{ ($accountDetails['2400']->is_active ?? 1) ? 'background-color: rgba(16, 185, 129, 0.1); color: #10b981;' : '' }}" data-type="coa" data-id="{{ $accountDetails['2400']->id ?? '' }}">
                                    {{ ($accountDetails['2400']->is_active ?? 1) ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Government Contributions</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">SSS, PhilHealth, Pag-IBIG premiums, and withholding payable.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['government_contributions'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Customer Deposits Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card liabilities-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Customer Deposits', 'Funds received in advance from customers for orders yet to be fulfilled', '{{ $balances['customer_deposits'] }}')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(245, 158, 11, 0.08); color: #f59e0b;">
                                    <i class="las la-handshake fs-20"></i>
                                </div>
                                <span class="badge status-badge {{ ($accountDetails['2500']->is_active ?? 1) ? 'bg-soft-success text-success' : 'bg-light text-secondary' }} px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; cursor: pointer; {{ ($accountDetails['2500']->is_active ?? 1) ? 'background-color: rgba(16, 185, 129, 0.1); color: #10b981;' : '' }}" data-type="coa" data-id="{{ $accountDetails['2500']->id ?? '' }}">
                                    {{ ($accountDetails['2500']->is_active ?? 1) ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Customer Deposits</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Funds received in advance from customers for orders yet to be fulfilled.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['customer_deposits'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Unearned Revenue Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card liabilities-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Unearned Revenue', 'Deferred income for services or products to be delivered in the future', '{{ $balances['unearned_revenue'] }}')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(245, 158, 11, 0.08); color: #f59e0b;">
                                    <i class="las la-calendar-check fs-20"></i>
                                </div>
                                <span class="badge status-badge {{ ($accountDetails['2600']->is_active ?? 1) ? 'bg-soft-success text-success' : 'bg-light text-secondary' }} px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; cursor: pointer; {{ ($accountDetails['2600']->is_active ?? 1) ? 'background-color: rgba(16, 185, 129, 0.1); color: #10b981;' : '' }}" data-type="coa" data-id="{{ $accountDetails['2600']->id ?? '' }}">
                                    {{ ($accountDetails['2600']->is_active ?? 1) ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Unearned Revenue</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Deferred income for services or products to be delivered in the future.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['unearned_revenue'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

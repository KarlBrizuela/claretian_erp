<div class="card shadow-sm border-0 mb-4" style="border-radius: 8px; border: 1px solid #e2e8f0; background: #ffffff;">
    <div class="card-header bg-white border-0 pt-3 pb-2 px-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(239, 68, 68, 0.08); color: #ef4444;">
                <i class="las la-receipt fs-20"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold fs-16" style="color: #000000;">Expenses</h5>
                <p class="text-muted small mb-0">Operational disbursements, cost of sales, and administrative expenses incurred.</p>
            </div>
        </div>
        <span class="badge bg-light text-dark border px-2.5 py-1.5 rounded-pill small fw-bold">8 Account Categories</span>
    </div>
    <div class="card-body p-3 pt-1">
        <div class="row g-2">
            <!-- Fixed Assets Expense Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card expenses-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#fixedAssetsExpenseModal">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(239, 68, 68, 0.08); color: #ef4444;">
                                    <i class="las la-tools fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Fixed Assets Expense</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Production machinery, equipment acquisitions, and property valuations.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['exp_fixed_assets'] ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Supplies Expense Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card expenses-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#suppliesExpenseModal">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(239, 68, 68, 0.08); color: #ef4444;">
                                    <i class="las la-archive fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Supplies Expense</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Accounting division office paper, stationery, toner, and store supplies.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['exp_supplies'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Operational Expenses Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card expenses-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#operationalExpensesModal">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(239, 68, 68, 0.08); color: #ef4444;">
                                    <i class="las la-file-invoice-dollar fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Operational Expenses</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">General operating disbursements, office maintenance, and overheads.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['exp_operational'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Cost of Goods Sold (COGS) Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card expenses-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Cost of Goods Sold (COGS)', 'Direct raw materials, paper stock, ink, and publishing production costs', '{{ $balances['exp_cogs'] }}')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(239, 68, 68, 0.08); color: #ef4444;">
                                    <i class="las la-boxes fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Cost of Goods Sold (COGS)</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Direct raw materials, paper stock, ink, and publishing production costs.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['exp_cogs'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Payroll & Administrative Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card expenses-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Payroll & Administrative Expenses', 'Employee compensation, salaries, and personnel benefits', '{{ $balances['exp_payroll'] }}')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(239, 68, 68, 0.08); color: #ef4444;">
                                    <i class="las la-users-cog fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Payroll & Administrative</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Staff salaries, wages, employee benefits, and administrative allowances.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['exp_payroll'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Utilities & Communication Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card expenses-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Utilities & Communication', 'Electricity, water, internet, and communication utilities', '{{ $balances['exp_utilities'] }}')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(239, 68, 68, 0.08); color: #ef4444;">
                                    <i class="las la-bolt fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Utilities & Communication</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Electricity, power consumption, water, internet, and telecommunication.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['exp_utilities'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Marketing & Distribution Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card expenses-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Marketing & Distribution', 'Promotions, logistics, shipping, and advertising expenses', '{{ $balances['exp_marketing'] }}')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(239, 68, 68, 0.08); color: #ef4444;">
                                    <i class="las la-bullhorn fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Marketing & Distribution</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Book fair promotions, online ad spend, courier shipping, and logistics.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['exp_marketing'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Petty Cash Disbursements Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card expenses-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#pettyCashModal">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(239, 68, 68, 0.08); color: #ef4444;">
                                    <i class="las la-cash-register fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Petty Cash Disbursements</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Minor office operational expenses and imprest petty cash vouchers.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['exp_petty_cash'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

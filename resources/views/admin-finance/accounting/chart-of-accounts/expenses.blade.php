<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; transition: transform 0.2s;">
    <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: rgba(217, 37, 28, 0.1); color: #D9251C;">
                <i class="las la-receipt fs-24"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark fs-18">Expenses</h5>
                <p class="text-muted small mb-0">Operational disbursements, cost of sales, and administrative expenses incurred</p>
            </div>
        </div>
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold">8 Account Categories</span>
    </div>
    <div class="card-body pt-2">
        <div class="row g-3">
            <!-- Fixed Assets Expense Card -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; transition: all 0.2s ease; border-left: 4px solid #D9251C !important; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#fixedAssetsExpenseModal">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="text-danger"><i class="las la-tools fs-24"></i></span>
                            <h6 class="mb-0 fw-bold text-dark fs-15">Fixed Assets Expense</h6>
                        </div>
                        <p class="text-muted small mb-3">Production machinery, equipment acquisitions, and property valuations</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-light">
                            <h5 class="mb-0 fw-bold text-dark fs-16">₱{{ number_format($balances['exp_fixed_assets'] ?? 0, 2) }}</h5>
                            <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold">Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Supplies Expense Card -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; transition: all 0.2s ease; border-left: 4px solid #D9251C !important; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#suppliesExpenseModal">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="text-danger"><i class="las la-archive fs-24"></i></span>
                            <h6 class="mb-0 fw-bold text-dark fs-15">Supplies Expense</h6>
                        </div>
                        <p class="text-muted small mb-3">Accounting division office paper, stationery, toner, and store supplies</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-light">
                            <h5 class="mb-0 fw-bold text-dark fs-16">₱{{ number_format($balances['exp_supplies'], 2) }}</h5>
                            <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold">Active</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Operational Expenses Card -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; transition: all 0.2s ease; border-left: 4px solid #D9251C !important; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#operationalExpensesModal">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="text-danger"><i class="las la-file-invoice-dollar fs-24"></i></span>
                            <h6 class="mb-0 fw-bold text-dark fs-15">Operational Expenses</h6>
                        </div>
                        <p class="text-muted small mb-3">General operating disbursements, office maintenance, and overheads</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-light">
                            <h5 class="mb-0 fw-bold text-dark fs-16">₱{{ number_format($balances['exp_operational'], 2) }}</h5>
                            <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold">Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cost of Goods Sold (COGS) Card -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; transition: all 0.2s ease; border-left: 4px solid #D9251C !important; cursor: pointer;" onclick="showGenericModal('Cost of Goods Sold (COGS)', 'Direct material, printing, and production cost of sales', '{{ $balances['exp_cogs'] }}')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="text-danger"><i class="las la-boxes fs-24"></i></span>
                            <h6 class="mb-0 fw-bold text-dark fs-15">Cost of Goods Sold (COGS)</h6>
                        </div>
                        <p class="text-muted small mb-3">Direct raw materials, paper stock, ink, and publishing production costs</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-light">
                            <h5 class="mb-0 fw-bold text-dark fs-16">₱{{ number_format($balances['exp_cogs'], 2) }}</h5>
                            <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold">Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payroll & Administrative Card -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; transition: all 0.2s ease; border-left: 4px solid #D9251C !important; cursor: pointer;" onclick="showGenericModal('Payroll & Administrative Expenses', 'Employee compensation, salaries, and personnel benefits', '{{ $balances['exp_payroll'] }}')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="text-danger"><i class="las la-users-cog fs-24"></i></span>
                            <h6 class="mb-0 fw-bold text-dark fs-15">Payroll & Administrative</h6>
                        </div>
                        <p class="text-muted small mb-3">Staff salaries, wages, employee benefits, and administrative allowances</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-light">
                            <h5 class="mb-0 fw-bold text-dark fs-16">₱{{ number_format($balances['exp_payroll'], 2) }}</h5>
                            <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold">Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Utilities & Communication Card -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; transition: all 0.2s ease; border-left: 4px solid #D9251C !important; cursor: pointer;" onclick="showGenericModal('Utilities & Communication', 'Electricity, water, internet, and communication utilities', '{{ $balances['exp_utilities'] }}')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="text-danger"><i class="las la-bolt fs-24"></i></span>
                            <h6 class="mb-0 fw-bold text-dark fs-15">Utilities & Communication</h6>
                        </div>
                        <p class="text-muted small mb-3">Electricity, power consumption, water, internet, and telecommunication</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-light">
                            <h5 class="mb-0 fw-bold text-dark fs-16">₱{{ number_format($balances['exp_utilities'], 2) }}</h5>
                            <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold">Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Marketing & Distribution Card -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; transition: all 0.2s ease; border-left: 4px solid #D9251C !important; cursor: pointer;" onclick="showGenericModal('Marketing & Distribution', 'Promotions, logistics, shipping, and advertising expenses', '{{ $balances['exp_marketing'] }}')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="text-danger"><i class="las la-bullhorn fs-24"></i></span>
                            <h6 class="mb-0 fw-bold text-dark fs-15">Marketing & Distribution</h6>
                        </div>
                        <p class="text-muted small mb-3">Book fair promotions, online ad spend, courier shipping, and logistics</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-light">
                            <h5 class="mb-0 fw-bold text-dark fs-16">₱{{ number_format($balances['exp_marketing'], 2) }}</h5>
                            <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold">Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Petty Cash Disbursements Card -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; transition: all 0.2s ease; border-left: 4px solid #D9251C !important; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#pettyCashModal">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="text-danger"><i class="las la-cash-register fs-24"></i></span>
                            <h6 class="mb-0 fw-bold text-dark fs-15">Petty Cash Disbursements</h6>
                        </div>
                        <p class="text-muted small mb-3">Minor office operational expenses and imprest petty cash vouchers</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-light">
                            <h5 class="mb-0 fw-bold text-dark fs-16">₱{{ number_format($balances['exp_petty_cash'], 2) }}</h5>
                            <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold">Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; transition: transform 0.2s;">
    <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: rgba(255, 0, 0, 0.1); color: #ff0000;">
                <i class="las la-credit-card fs-24"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark fs-18">Liabilities</h5>
                <p class="text-muted small mb-0">Financial debts or obligations arising during business operations</p>
            </div>
        </div>
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold">7 Account Categories</span>
    </div>
    <div class="card-body pt-2">
        <div class="row g-3">
            <!-- Suppliers Card -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; transition: all 0.2s ease; border-left: 4px solid #ff0000 !important; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#suppliersModal">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="text-primary"><i class="las la-truck fs-24"></i></span>
                            <h6 class="mb-0 fw-bold text-dark fs-15">Suppliers</h6>
                        </div>
                        <p class="text-muted small mb-3">Amounts owed to vendor partners for merchandise & raw materials</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-light">
                            <h5 class="mb-0 fw-bold text-dark fs-16">₱{{ number_format($balances['suppliers'], 2) }}</h5>
                            <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold">Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payables Card -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; transition: all 0.2s ease; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Payables', 'Short-term liabilities to creditors and operational suppliers')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="text-primary"><i class="las la-file-invoice fs-24"></i></span>
                            <h6 class="mb-0 fw-bold text-dark fs-15">Payables</h6>
                        </div>
                        <p class="text-muted small mb-3">Short-term liabilities to creditors and operational suppliers</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-light">
                            <h5 class="mb-0 fw-bold text-dark fs-16">₱{{ number_format($balances['payables'], 2) }}</h5>
                            <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold">Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loans Card -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; transition: all 0.2s ease; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Loans', 'Long-term or short-term notes payable and bank lines of credit')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="text-primary"><i class="las la-balance-scale fs-24"></i></span>
                            <h6 class="mb-0 fw-bold text-dark fs-15">Loans</h6>
                        </div>
                        <p class="text-muted small mb-3">Long-term or short-term notes payable and bank lines of credit</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-light">
                            <h5 class="mb-0 fw-bold text-dark fs-16">₱{{ number_format($balances['loans'], 2) }}</h5>
                            <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold">Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Taxes Card -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; transition: all 0.2s ease; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Taxes', 'Accrued corporate taxes, VAT payable, and withholding taxes')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="text-primary"><i class="las la-percent fs-24"></i></span>
                            <h6 class="mb-0 fw-bold text-dark fs-15">Taxes</h6>
                        </div>
                        <p class="text-muted small mb-3">Accrued corporate taxes, VAT payable, and withholding taxes</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-light">
                            <h5 class="mb-0 fw-bold text-dark fs-16">₱{{ number_format($balances['taxes'], 2) }}</h5>
                            <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold">Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Government Contributions Card -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; transition: all 0.2s ease; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Government Contributions', 'SSS, PhilHealth, Pag-IBIG premiums, and withholding payable')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="text-primary"><i class="las la-landmark fs-24"></i></span>
                            <h6 class="mb-0 fw-bold text-dark fs-15">Government Contributions</h6>
                        </div>
                        <p class="text-muted small mb-3">SSS, PhilHealth, Pag-IBIG premiums, and withholding payable</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-light">
                            <h5 class="mb-0 fw-bold text-dark fs-16">₱{{ number_format($balances['government_contributions'], 2) }}</h5>
                            <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold">Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Deposits Card -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; transition: all 0.2s ease; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Customer Deposits', 'Funds received in advance from customers for orders yet to be fulfilled')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="text-primary"><i class="las la-handshake fs-24"></i></span>
                            <h6 class="mb-0 fw-bold text-dark fs-15">Customer Deposits</h6>
                        </div>
                        <p class="text-muted small mb-3">Funds received in advance from customers for orders yet to be fulfilled</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-light">
                            <h5 class="mb-0 fw-bold text-dark fs-16">₱{{ number_format($balances['customer_deposits'], 2) }}</h5>
                            <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold">Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unearned Revenue Card -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; transition: all 0.2s ease; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Unearned Revenue', 'Deferred income for services or products to be delivered in the future')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="text-primary"><i class="las la-calendar-check fs-24"></i></span>
                            <h6 class="mb-0 fw-bold text-dark fs-15">Unearned Revenue</h6>
                        </div>
                        <p class="text-muted small mb-3">Deferred income for services or products to be delivered in the future</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-light">
                            <h5 class="mb-0 fw-bold text-dark fs-16">₱{{ number_format($balances['unearned_revenue'], 2) }}</h5>
                            <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold">Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

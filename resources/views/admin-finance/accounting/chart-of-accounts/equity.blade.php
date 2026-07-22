<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; transition: transform 0.2s;">
    <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: rgba(255, 0, 0, 0.1); color: #ff0000;">
                <i class="las la-award fs-24"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark fs-18">3. Equity</h5>
                <p class="text-muted small mb-0">Owner's residual interest in the assets of the organization after deducting liabilities</p>
            </div>
        </div>
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold">3 Account Categories</span>
    </div>
    <div class="card-body pt-2">
        <div class="row g-3">
            <!-- Capital Card -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; transition: all 0.2s ease; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Capital', 'Paid-in contributions and owner investment funds')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="text-primary"><i class="las la-piggy-bank fs-24"></i></span>
                            <h6 class="mb-0 fw-bold text-dark fs-15">Capital</h6>
                        </div>
                        <p class="text-muted small mb-3">Paid-in contributions and owner investment funds</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-light">
                            <h5 class="mb-0 fw-bold text-dark fs-16">₱{{ number_format($balances['capital'], 2) }}</h5>
                            <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold">Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Retained Earnings Card -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; transition: all 0.2s ease; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Retained Earnings', 'Accumulated net earnings from previous fiscal periods, undistributed to owners')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="text-primary"><i class="las la-history fs-24"></i></span>
                            <h6 class="mb-0 fw-bold text-dark fs-15">Retained Earnings</h6>
                        </div>
                        <p class="text-muted small mb-3">Accumulated net earnings from previous fiscal periods, undistributed to owners</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-light">
                            <h5 class="mb-0 fw-bold text-dark fs-16">₱{{ number_format($balances['retained_earnings'], 2) }}</h5>
                            <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold">Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Year Income Card -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; transition: all 0.2s ease; border-left: 4px solid #ff0000 !important; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#cashOnHandModal">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="text-primary"><i class="las la-chart-line fs-24"></i></span>
                            <h6 class="mb-0 fw-bold text-dark fs-15">Current Year Income</h6>
                        </div>
                        <p class="text-muted small mb-3">Net profit or loss generated in the current active fiscal year</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-light">
                            <h5 class="mb-0 fw-bold text-dark fs-16">₱{{ number_format($balances['current_year_income'], 2) }}</h5>
                            <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold">Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

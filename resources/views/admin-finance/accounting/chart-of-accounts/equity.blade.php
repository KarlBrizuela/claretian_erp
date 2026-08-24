<div class="card shadow-sm border-0 mb-4" style="border-radius: 8px; border: 1px solid #e2e8f0; background: #ffffff;">
    <div class="card-header bg-white border-0 pt-3 pb-2 px-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(139, 92, 246, 0.08); color: #8b5cf6;">
                <i class="las la-award fs-20"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold fs-16" style="color: #000000;">Equity</h5>
                <p class="text-muted small mb-0">Owner's residual interest in the assets of the organization after deducting liabilities.</p>
            </div>
        </div>
        <span class="badge bg-light text-dark border px-2.5 py-1.5 rounded-pill small fw-bold">3 Account Categories</span>
    </div>
    <div class="card-body p-3 pt-1">
        <div class="row g-2">
            <!-- Capital Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card equity-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Capital', 'Paid-in contributions and owner investment funds', '{{ $balances['capital'] }}')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(139, 92, 246, 0.08); color: #8b5cf6;">
                                    <i class="las la-piggy-bank fs-20"></i>
                                </div>
                                <span class="badge status-badge {{ ($accountDetails['3100']->is_active ?? 1) ? 'bg-soft-success text-success' : 'bg-light text-secondary' }} px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; cursor: pointer; {{ ($accountDetails['3100']->is_active ?? 1) ? 'background-color: rgba(16, 185, 129, 0.1); color: #10b981;' : '' }}" data-type="coa" data-id="{{ $accountDetails['3100']->id ?? '' }}">
                                    {{ ($accountDetails['3100']->is_active ?? 1) ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Capital</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Paid-in contributions and owner investment funds.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['capital'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Retained Earnings Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card equity-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Retained Earnings', 'Accumulated net earnings from previous fiscal periods, undistributed to owners', '{{ $balances['retained_earnings'] }}')">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(139, 92, 246, 0.08); color: #8b5cf6;">
                                    <i class="las la-history fs-20"></i>
                                </div>
                                <span class="badge status-badge {{ ($accountDetails['3000']->is_active ?? 1) ? 'bg-soft-success text-success' : 'bg-light text-secondary' }} px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; cursor: pointer; {{ ($accountDetails['3000']->is_active ?? 1) ? 'background-color: rgba(16, 185, 129, 0.1); color: #10b981;' : '' }}" data-type="coa" data-id="{{ $accountDetails['3000']->id ?? '' }}">
                                    {{ ($accountDetails['3000']->is_active ?? 1) ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Retained Earnings</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Accumulated net earnings from previous fiscal periods, undistributed to owners.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['retained_earnings'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Current Year Income Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card equity-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#cashOnHandModal">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(139, 92, 246, 0.08); color: #8b5cf6;">
                                    <i class="las la-chart-line fs-20"></i>
                                </div>
                                <span class="badge status-badge {{ ($accountDetails['3200']->is_active ?? 1) ? 'bg-soft-success text-success' : 'bg-light text-secondary' }} px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; cursor: pointer; {{ ($accountDetails['3200']->is_active ?? 1) ? 'background-color: rgba(16, 185, 129, 0.1); color: #10b981;' : '' }}" data-type="coa" data-id="{{ $accountDetails['3200']->id ?? '' }}">
                                    {{ ($accountDetails['3200']->is_active ?? 1) ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Current Year Income</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Net profit or loss generated in the current active fiscal year.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['current_year_income'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

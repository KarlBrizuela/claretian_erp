<div class="card shadow-sm border-0 mb-4" style="border-radius: 8px; border: 1px solid #e2e8f0; background: #ffffff;">
    <div class="card-header bg-white border-0 pt-3 pb-2 px-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                <i class="las la-funnel-dollar fs-20"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold fs-16" style="color: #000000;">Income</h5>
                <p class="text-muted small mb-0">Revenues earned from core operational activities, grouped by department.</p>
            </div>
        </div>
        <span class="badge bg-light text-dark border px-2.5 py-1.5 rounded-pill small fw-bold">5 Revenue Groups</span>
    </div>
    <div class="card-body p-3 pt-1">
        
        <!-- Publishing Section -->
        <h6 class="text-dark fw-bold text-uppercase mb-2.5 mt-2" style="font-size: 0.78rem; letter-spacing: 0.5px;"><i class="las la-book-open me-1.5 fs-15"></i>Publishing Department</h6>
        <div class="row g-2 mb-3.5">
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Book Sales', 'Retail & wholesale book trades', {{ $balances['pub_book_sales'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-book fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Book Sales</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Retail & wholesale book trades.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['pub_book_sales'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Royalties', 'Author cuts & license allocations', {{ $balances['pub_royalties'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-pen-nib fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Royalties</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Author cuts & license allocations.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['pub_royalties'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Rights Income', 'Translation & foreign licensing', {{ $balances['pub_rights_income'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-globe fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Rights Income</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Translation & foreign licensing.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['pub_rights_income'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Licensing', 'IP branding authorizations', {{ $balances['pub_licensing'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-certificate fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Licensing</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">IP branding authorizations.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['pub_licensing'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('E-books', 'Digital publication downloads', {{ $balances['pub_ebooks'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-tablet fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">E-books</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Digital publication downloads.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['pub_ebooks'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Printing Services Section -->
        <h6 class="text-dark fw-bold text-uppercase mb-2.5 mt-3" style="font-size: 0.78rem; letter-spacing: 0.5px;"><i class="las la-print me-1.5 fs-15"></i>Printing Services</h6>
        <div class="row g-2 mb-3.5">
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Printing Income', 'Physical press job revenues', {{ $balances['print_income'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-print fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Printing Income</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Physical press job revenues.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['print_income'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Layout Income', 'Pre-press arrangement fees', {{ $balances['print_layout'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-columns fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Layout Income</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Pre-press arrangement fees.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['print_layout'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Design Income', 'Book graphic design works', {{ $balances['print_design'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-palette fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Design Income</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Book graphic design works.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['print_design'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Binding', 'Finishing & binding fees', {{ $balances['print_binding'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-book-reader fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Binding</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Finishing & binding fees.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['print_binding'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Lamination', 'Lamination cover treatments', {{ $balances['print_lamination'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-layer-group fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Lamination</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Lamination cover treatments.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['print_lamination'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Marketing Section -->
        <h6 class="text-dark fw-bold text-uppercase mb-2.5 mt-3" style="font-size: 0.78rem; letter-spacing: 0.5px;"><i class="las la-bullhorn me-1.5 fs-15"></i>Marketing Department</h6>
        <div class="row g-2 mb-3.5">
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('POS Sales', 'Point of Sale counter transactions', {{ $balances['mkt_pos_sales'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-cash-register fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">POS Sales</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Point of Sale counter transactions.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['mkt_pos_sales'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Sales Orders (SO)', 'Standard sales order trades', {{ $balances['mkt_so_sales'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-shopping-cart fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Sales Orders (SO)</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Standard sales order trades.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['mkt_so_sales'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('E-Commerce Direct', 'Direct website & e-com orders', {{ $balances['mkt_ecom_direct'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-globe-asia fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">E-Commerce Direct</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Direct website & e-com orders.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['mkt_ecom_direct'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Direct Sales', 'In-store walk-in sales', {{ $balances['mkt_direct_sales'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-store-alt fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Direct Sales</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">In-store walk-in sales.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['mkt_direct_sales'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Area Sales', 'Territory representative trades', {{ $balances['mkt_area_sales'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-map-marked-alt fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Area Sales</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Territory representative trades.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['mkt_area_sales'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('COB Sales', 'Online bookstore cart sales', {{ $balances['mkt_cob_sales'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-book-open fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">COB Sales</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Online bookstore cart sales.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['mkt_cob_sales'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Lazada', 'Lazada online shop revenues', {{ $balances['mkt_lazada'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="lab la-lazada fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Lazada</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Lazada online shop revenues.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['mkt_lazada'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Shopee', 'Shopee merchant revenues', {{ $balances['mkt_shopee'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-store fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Shopee</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Shopee merchant revenues.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['mkt_shopee'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Tiktok', 'Tiktok shop transactions', {{ $balances['mkt_tiktok'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-video fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Tiktok</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Tiktok shop transactions.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['mkt_tiktok'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Facebook', 'FB social shop transactions', {{ $balances['mkt_facebook'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="lab la-facebook-f fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Facebook</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">FB social shop transactions.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['mkt_facebook'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Wholesale', 'Bulk distributor accounts', {{ $balances['mkt_wholesale'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-truck-loading fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Wholesale</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Bulk distributor accounts.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['mkt_wholesale'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Export', 'International volume trades', {{ $balances['mkt_export'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-plane-departure fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Export</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">International volume trades.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['mkt_export'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Claret Media', 'Audio, video, & broadcast ads', {{ $balances['mkt_claret_media'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-microphone fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Claret Media</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Audio, video, & broadcast ads.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['mkt_claret_media'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Income by Payment Method Section -->
        <h6 class="text-dark fw-bold text-uppercase mb-2.5 mt-3" style="font-size: 0.78rem; letter-spacing: 0.5px;"><i class="las la-wallet me-1.5 fs-15"></i>Income by Payment Method</h6>
        <div class="row g-2 mb-3.5">
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Cash Income', 'Collected via physical cash payments', {{ $balances['mkt_pay_cash'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-money-bill-wave fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Cash Income</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Collected via cash payments.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['mkt_pay_cash'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('E-Wallet Income', 'GCash, Maya, & QR Ph payments', {{ $balances['mkt_pay_ewallet'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-wallet fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">E-Wallet Income</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">GCash, Maya, & QR Ph payments.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['mkt_pay_ewallet'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Bank & Check Income', 'Bank Wire Transfer & Cheque settlements', {{ $balances['mkt_pay_bank'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-university fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Bank & Check Income</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Bank Wire & Cheque settlements.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['mkt_pay_bank'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Card Income', 'Credit & Debit card transactions', {{ $balances['mkt_pay_card'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-credit-card fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Card Income</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Credit & Debit card transactions.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['mkt_pay_card'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Other Section -->
        <h6 class="text-dark fw-bold text-uppercase mb-2.5 mt-3" style="font-size: 0.78rem; letter-spacing: 0.5px;"><i class="las la-globe-asia me-1.5 fs-15"></i>Other Revenue Streams</h6>
        <div class="row g-2">
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Donations', 'Direct community contributions', {{ $balances['oth_donations'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between h-100">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-hand-holding-heart fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Donations</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Direct community contributions.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['oth_donations'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Grants', 'Special program project funding', {{ $balances['oth_grants'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between h-100">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-award fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Grants</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Special program project funding.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['oth_grants'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Investments', 'Mutual funds & equity gains', {{ $balances['oth_investments'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between h-100">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-chart-area fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Investments</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Mutual funds & equity gains.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['oth_investments'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Interest Income', 'Deposit yield bank interests', {{ $balances['oth_interest_income'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between h-100">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-percent fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Interest Income</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Deposit yield bank interests.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['oth_interest_income'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card border shadow-sm hover-card income-card" style="background-color: #ffffff; border-color: #e2e8f0; border-radius: 10px; transition: all 0.2s ease; cursor: pointer;" onclick="showGenericModal('Rental Income', 'Leased workspace & press hires', {{ $balances['oth_rental_income'] }})">
                    <div class="card-body p-3 d-flex flex-column justify-content-between h-100">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                                    <i class="las la-key fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="color: #000000; letter-spacing: -0.2px;">Rental Income</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4; min-height: 38px;">Leased workspace & press hires.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="text-muted small" style="font-size: 0.72rem;">Balance</span>
                            <span class="fw-bold fs-14" style="color: #0f172a;">₱{{ number_format($balances['oth_rental_income'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

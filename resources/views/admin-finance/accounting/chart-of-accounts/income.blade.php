<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; transition: transform 0.2s;">
    <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: rgba(255, 0, 0, 0.1); color: #ff0000;">
                <i class="las la-funnel-dollar fs-24"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark fs-18">Income</h5>
                <p class="text-muted small mb-0">Revenues earned from core operational activities, grouped by department</p>
            </div>
        </div>
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold">5 Revenue Groups</span>
    </div>
    <div class="card-body pt-2">
        
        <!-- Publishing Section -->
        <h6 class="text-primary fw-bold text-uppercase mb-3 mt-2"><i class="las la-book-open me-2 fs-18"></i>Publishing Department</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Book Sales', 'Retail & wholesale book trades', {{ $balances['pub_book_sales'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Book Sales</h6>
                            <span class="text-muted small">Retail & wholesale book trades</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['pub_book_sales'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Royalties', 'Author cuts & license allocations', {{ $balances['pub_royalties'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Royalties</h6>
                            <span class="text-muted small">Author cuts & license allocations</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['pub_royalties'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Rights Income', 'Translation & foreign licensing', {{ $balances['pub_rights_income'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Rights Income</h6>
                            <span class="text-muted small">Translation & foreign licensing</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['pub_rights_income'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Licensing', 'IP branding authorizations', {{ $balances['pub_licensing'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Licensing</h6>
                            <span class="text-muted small">IP branding authorizations</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['pub_licensing'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('E-books', 'Digital publication downloads', {{ $balances['pub_ebooks'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">E-books</h6>
                            <span class="text-muted small">Digital publication downloads</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['pub_ebooks'], 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Printing Services Section -->
        <h6 class="text-primary fw-bold text-uppercase mb-3"><i class="las la-print me-2 fs-18"></i>Printing Services</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Printing Income', 'Physical press job revenues', {{ $balances['print_income'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Printing Income</h6>
                            <span class="text-muted small">Physical press job revenues</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['print_income'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Layout Income', 'Pre-press arrangement fees', {{ $balances['print_layout'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Layout Income</h6>
                            <span class="text-muted small">Pre-press arrangement fees</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['print_layout'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Design Income', 'Book graphic design works', {{ $balances['print_design'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Design Income</h6>
                            <span class="text-muted small">Book graphic design works</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['print_design'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Binding', 'Finishing & binding fees', {{ $balances['print_binding'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Binding</h6>
                            <span class="text-muted small">Finishing & binding fees</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['print_binding'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Lamination', 'Lamination cover treatments', {{ $balances['print_lamination'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Lamination</h6>
                            <span class="text-muted small">Lamination cover treatments</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['print_lamination'], 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Marketing Section -->
        <h6 class="text-primary fw-bold text-uppercase mb-3"><i class="las la-bullhorn me-2 fs-18"></i>Marketing Department</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('POS Sales', 'Point of Sale counter transactions', {{ $balances['mkt_pos_sales'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">POS Sales</h6>
                            <span class="text-muted small">Point of Sale counter transactions</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['mkt_pos_sales'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Sales Orders (SO)', 'Standard sales order trades', {{ $balances['mkt_so_sales'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Sales Orders (SO)</h6>
                            <span class="text-muted small">Standard sales order trades</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['mkt_so_sales'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('E-Commerce Direct', 'Direct website & e-com orders', {{ $balances['mkt_ecom_direct'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">E-Commerce Direct</h6>
                            <span class="text-muted small">Direct website & e-com orders</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['mkt_ecom_direct'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Direct Sales', 'In-store walk-in sales', {{ $balances['mkt_direct_sales'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Direct Sales</h6>
                            <span class="text-muted small">In-store walk-in sales</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['mkt_direct_sales'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Area Sales', 'Territory representative trades', {{ $balances['mkt_area_sales'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Area Sales</h6>
                            <span class="text-muted small">Territory representative trades</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['mkt_area_sales'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('COB Sales', 'Online bookstore cart sales', {{ $balances['mkt_cob_sales'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">COB Sales</h6>
                            <span class="text-muted small">Online bookstore cart sales</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['mkt_cob_sales'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Lazada', 'Lazada online shop revenues', {{ $balances['mkt_lazada'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Lazada</h6>
                            <span class="text-muted small">Lazada online shop revenues</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['mkt_lazada'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Shopee', 'Shopee merchant revenues', {{ $balances['mkt_shopee'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Shopee</h6>
                            <span class="text-muted small">Shopee merchant revenues</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['mkt_shopee'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Tiktok', 'Tiktok shop transactions', {{ $balances['mkt_tiktok'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Tiktok</h6>
                            <span class="text-muted small">Tiktok shop transactions</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['mkt_tiktok'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Facebook', 'FB social shop transactions', {{ $balances['mkt_facebook'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Facebook</h6>
                            <span class="text-muted small">FB social shop transactions</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['mkt_facebook'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Wholesale', 'Bulk distributor accounts', {{ $balances['mkt_wholesale'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Wholesale</h6>
                            <span class="text-muted small">Bulk distributor accounts</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['mkt_wholesale'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Export', 'International volume trades', {{ $balances['mkt_export'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Export</h6>
                            <span class="text-muted small">International volume trades</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['mkt_export'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Claret Media', 'Audio, video, & broadcast ads', {{ $balances['mkt_claret_media'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Claret Media</h6>
                            <span class="text-muted small">Audio, video, & broadcast ads</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['mkt_claret_media'], 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Income by Payment Method Section -->
        <h6 class="text-primary fw-bold text-uppercase mb-3"><i class="las la-wallet me-2 fs-18"></i>Income by Payment Method</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Cash Income', 'Collected via physical cash payments', {{ $balances['mkt_pay_cash'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Cash Income</h6>
                            <span class="text-muted small">Collected via cash payments</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['mkt_pay_cash'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('E-Wallet Income', 'GCash, Maya, & QR Ph payments', {{ $balances['mkt_pay_ewallet'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">E-Wallet Income</h6>
                            <span class="text-muted small">GCash, Maya, & QR Ph payments</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['mkt_pay_ewallet'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Bank & Check Income', 'Bank Wire Transfer & Cheque settlements', {{ $balances['mkt_pay_bank'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Bank & Check Income</h6>
                            <span class="text-muted small">Bank Wire & Cheque settlements</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['mkt_pay_bank'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Card Income', 'Credit & Debit card transactions', {{ $balances['mkt_pay_card'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Card Income</h6>
                            <span class="text-muted small">Credit & Debit card transactions</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['mkt_pay_card'], 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Other Section -->
        <h6 class="text-primary fw-bold text-uppercase mb-3"><i class="las la-globe-asia me-2 fs-18"></i>Other Revenue Streams</h6>
        <div class="row g-3">
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Donations', 'Direct community contributions', {{ $balances['oth_donations'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Donations</h6>
                            <span class="text-muted small">Direct community contributions</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['oth_donations'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Grants', 'Special program project funding', {{ $balances['oth_grants'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Grants</h6>
                            <span class="text-muted small">Special program project funding</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['oth_grants'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Investments', 'Mutual funds & equity gains', {{ $balances['oth_investments'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Investments</h6>
                            <span class="text-muted small">Mutual funds & equity gains</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['oth_investments'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Interest Income', 'Deposit yield bank interests', {{ $balances['oth_interest_income'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Interest Income</h6>
                            <span class="text-muted small">Deposit yield bank interests</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['oth_interest_income'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ff0000 !important; cursor: pointer;" onclick="showGenericModal('Rental Income', 'Leased workspace & press hires', {{ $balances['oth_rental_income'] }})">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Rental Income</h6>
                            <span class="text-muted small">Leased workspace & press hires</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($balances['oth_rental_income'], 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
    <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-success text-success" style="width: 45px; height: 45px;">
                <i class="las la-shopping-basket fs-24"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark fs-18">E-Commerce Channels Ledger</h5>
                <p class="text-muted small mb-0">Revenues and transaction logs across website portals and integrated online merchant platforms</p>
            </div>
        </div>
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold">8 Accounts</span>
    </div>
    <div class="card-body pt-2">
        <div class="row g-3 mb-4">
            
            <!-- Website -->
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #28a745 !important; cursor: pointer;" onclick="showSalesLedgerModal('Website Store Sales', document.getElementById('websiteSalesTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Website</h6>
                            <span class="text-muted small">Direct portal retail checkout</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($ecomWebsiteSales, 2) }}</h5>
                    </div>
                </div>
            </div>

            <!-- Shopee -->
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #28a745 !important; cursor: pointer;" onclick="showSalesLedgerModal('Shopee Store Sales', document.getElementById('shopeeSalesTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Shopee</h6>
                            <span class="text-muted small">Shopee mall platform sales</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($ecomShopeeSales, 2) }}</h5>
                    </div>
                </div>
            </div>

            <!-- Lazada -->
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #28a745 !important; cursor: pointer;" onclick="showSalesLedgerModal('Lazada Store Sales', document.getElementById('lazadaSalesTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Lazada</h6>
                            <span class="text-muted small">Lazada flagship store sales</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($ecomLazadaSales, 2) }}</h5>
                    </div>
                </div>
            </div>

            <!-- Facebook -->
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #28a745 !important; cursor: pointer;" onclick="showSalesLedgerModal('Facebook Messenger Orders', document.getElementById('facebookSalesTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Facebook</h6>
                            <span class="text-muted small">Social inbox sales orders</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($ecomFacebookSales, 2) }}</h5>
                    </div>
                </div>
            </div>

            <!-- TikTok -->
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #28a745 !important; cursor: pointer;" onclick="showSalesLedgerModal('TikTok Shop Sales', document.getElementById('tiktokSalesTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">TikTok</h6>
                            <span class="text-muted small">TikTok Shop checkout orders</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($ecomTiktokSales, 2) }}</h5>
                    </div>
                </div>
            </div>

            <!-- Payment Gateway -->
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #28a745 !important; cursor: pointer;" onclick="showSalesLedgerModal('Payment Gateway Fees & Settlements', document.getElementById('paymentGatewayTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Payment Gateway</h6>
                            <span class="text-muted small">Gateway fees & merchant cuts</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱0.00</h5>
                    </div>
                </div>
            </div>

            <!-- Shipping -->
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #28a745 !important; cursor: pointer;" onclick="showSalesLedgerModal('Shipping Fees Collection Ledger', document.getElementById('shippingTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Shipping</h6>
                            <span class="text-muted small">Customer shipping fee collections</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱0.00</h5>
                    </div>
                </div>
            </div>

            <!-- Refunds -->
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #28a745 !important; cursor: pointer;" onclick="showSalesLedgerModal('E-Commerce Refunds Ledger', document.getElementById('ecomRefundsTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Refunds</h6>
                            <span class="text-muted small">Customer platform return refunds</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱0.00</h5>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- TEMPLATE CONTAINER - WEBSITE -->
<div id="websiteSalesTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th>Invoice No</th>
                    <th>Customer Name</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ecomWebsiteOrders as $order)
                <tr>
                    <td><span class="fw-bold text-dark">#{{ $order->order_number ?? 'WEB-'.$order->id }}</span></td>
                    <td>{{ $order->customer->customer_name ?? $order->customer->company_name ?? 'Online Buyer' }}</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td><span class="badge bg-success text-white">Paid</span></td>
                    <td class="text-end fw-bold text-dark">₱{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No website orders recorded in the database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - SHOPEE -->
<div id="shopeeSalesTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th>Shopee Order ID</th>
                    <th>Buyer Username</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ecomShopeeOrders as $order)
                <tr>
                    <td><span class="fw-bold text-dark">#{{ $order->tracking_number ?? 'SHP-'.$order->id }}</span></td>
                    <td>{{ $order->customer_name ?? 'shopee_buyer' }}</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td><span class="badge bg-success text-white">Delivered</span></td>
                    <td class="text-end fw-bold text-dark">₱{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No Shopee orders recorded in the database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - LAZADA -->
<div id="lazadaSalesTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th>Lazada Order ID</th>
                    <th>Buyer Name</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ecomLazadaOrders as $order)
                <tr>
                    <td><span class="fw-bold text-dark">#{{ $order->tracking_number ?? 'LAZ-'.$order->id }}</span></td>
                    <td>{{ $order->customer_name ?? 'Lazada Customer' }}</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td><span class="badge bg-success text-white">Delivered</span></td>
                    <td class="text-end fw-bold text-dark">₱{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No Lazada orders recorded in the database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - FACEBOOK -->
<div id="facebookSalesTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th>Messenger Reference</th>
                    <th>Customer Name</th>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ecomFacebookOrders as $order)
                <tr>
                    <td><span class="fw-bold text-dark">#{{ $order->order_number ?? 'FB-'.$order->id }}</span></td>
                    <td>{{ $order->customer->customer_name ?? $order->customer->company_name ?? 'FB Buyer' }}</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td><span class="badge bg-success text-white">Processed</span></td>
                    <td class="text-end fw-bold text-dark">₱{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No Facebook Messenger orders recorded in the database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - TIKTOK -->
<div id="tiktokSalesTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th>TikTok Order ID</th>
                    <th>Buyer Username</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ecomTiktokOrders as $order)
                <tr>
                    <td><span class="fw-bold text-dark">#{{ $order->tracking_number ?? 'TT-'.$order->id }}</span></td>
                    <td>{{ $order->customer_name ?? 'tiktok_buyer' }}</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td><span class="badge bg-success text-white">Delivered</span></td>
                    <td class="text-end fw-bold text-dark">₱{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No TikTok Shop orders recorded in the database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - PAYMENT GATEWAY -->
<div id="paymentGatewayTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th>Transaction ID</th>
                    <th>Gateway Name</th>
                    <th class="text-end">Gross Amount</th>
                    <th class="text-center">Gateway Fee</th>
                    <th class="text-end">Net Payout</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No gateway transactions recorded in the database.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - SHIPPING -->
<div id="shippingTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th>Courier Waybill</th>
                    <th>Order No</th>
                    <th class="text-end">Collected Shipping</th>
                    <th class="text-end">Actual Courier Charge</th>
                    <th class="text-end">Variance Surplus / (Deficit)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No shipping transactions recorded in the database.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - REFUNDS -->
<div id="ecomRefundsTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th>Refund Code</th>
                    <th>Order No</th>
                    <th>Channel</th>
                    <th>Refund Reason</th>
                    <th>Date Approved</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No return refunds recorded in the database.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

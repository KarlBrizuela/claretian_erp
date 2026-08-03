<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
    <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 45px; height: 45px;">
                <i class="las la-store-alt fs-24"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark fs-18">Bookstore Sales Ledger</h5>
                <p class="text-muted small mb-0">Revenues from physical bookstore walk-in cashier terminals, categorized by payment type</p>
            </div>
        </div>
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold">7 Accounts</span>
    </div>
    <div class="card-body pt-2">
        <div class="row g-3 mb-4">
            
            <!-- Daily Sales -->
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #D9251C !important; cursor: pointer;" onclick="showSalesLedgerModal('Daily Sales', document.getElementById('dailySalesTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Daily Sales</h6>
                            <span class="text-muted small">Total Bookstore sales today</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($bookstoreDailySales, 2) }}</h5>
                    </div>
                </div>
            </div>

            <!-- Cash -->
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #D9251C !important; cursor: pointer;" onclick="showSalesLedgerModal('Cash Sales', document.getElementById('cashSalesTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Cash</h6>
                            <span class="text-muted small">Physical cash counter collections</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($bookstoreCashSales, 2) }}</h5>
                    </div>
                </div>
            </div>

            <!-- GCash / QR PH -->
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #D9251C !important; cursor: pointer;" onclick="showSalesLedgerModal('GCash / QR PH / E-Wallets', document.getElementById('gcashSalesTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">GCash / QR PH / E-Wallets</h6>
                            <span class="text-muted small">Mobile e-wallet settlements</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($bookstoreGcashSales, 2) }}</h5>
                    </div>
                </div>
            </div>

            <!-- Credit Card -->
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #D9251C !important; cursor: pointer;" onclick="showSalesLedgerModal('Credit Card Payments', document.getElementById('creditCardSalesTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Credit Card</h6>
                            <span class="text-muted small">Merchant card terminal settlements</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($bookstoreCardSales, 2) }}</h5>
                    </div>
                </div>
            </div>

            <!-- Charge Account -->
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #D9251C !important; cursor: pointer;" onclick="showSalesLedgerModal('Charge Account Sales', document.getElementById('chargeAccountSalesTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Charge Account</h6>
                            <span class="text-muted small">Corporate credit accounts ledger</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($bookstoreChargeSales, 2) }}</h5>
                    </div>
                </div>
            </div>

            <!-- Discounts -->
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #D9251C !important; cursor: pointer;" onclick="showSalesLedgerModal('Discounts ledger', document.getElementById('discountsTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Discounts</h6>
                            <span class="text-muted small">Promotional & courtesy deductions</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱{{ number_format($bookstoreDiscountSales ?? 0, 2) }}</h5>
                    </div>
                </div>
            </div>

            <!-- Employee Purchases -->
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #D9251C !important; cursor: pointer;" onclick="showSalesLedgerModal('Employee Purchases', document.getElementById('employeePurchasesTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Employee Purchases</h6>
                            <span class="text-muted small">Staff bookstore purchases</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱0.00</h5>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- TEMPLATE CONTAINER - DAILY SALES -->
<div id="dailySalesTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th>Order No</th>
                    <th>Payment Method</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookstoreDailyOrders as $order)
                <tr>
                    <td><span class="fw-bold text-dark">#{{ $order->order_number ?? 'ORD-'.$order->id }}</span></td>
                    <td><span class="badge bg-light text-dark border">{{ strtoupper($order->payment_method ?? 'Cash') }}</span></td>
                    <td>{{ $order->created_at->format('M d, Y h:i A') }}</td>
                    <td><span class="badge bg-success text-white">Completed</span></td>
                    <td class="text-end fw-bold text-dark">₱{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No transactions logged today.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - CASH SALES -->
<div id="cashSalesTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th>Order No</th>
                    <th>Cashier</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookstoreCashOrders as $order)
                <tr>
                    <td><span class="fw-bold text-dark">#{{ $order->order_number ?? 'ORD-'.$order->id }}</span></td>
                    <td>Bookstore Terminal 1</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td><span class="badge bg-success text-white">Paid</span></td>
                    <td class="text-end fw-bold text-dark">₱{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No cash transactions logged in the database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - GCASH SALES -->
<div id="gcashSalesTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th>Order No</th>
                    <th>Ref ID</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookstoreGcashOrders as $order)
                <tr>
                    <td><span class="fw-bold text-dark">#{{ $order->order_number ?? 'ORD-'.$order->id }}</span></td>
                    <td>{{ $order->payment_reference ?? 'REF-'.rand(100000, 999999) }}</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td><span class="badge bg-success text-white">Settled</span></td>
                    <td class="text-end fw-bold text-dark">₱{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No mobile e-wallet transactions logged in the database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - CREDIT CARD SALES -->
<div id="creditCardSalesTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th>Order No</th>
                    <th>Auth Code</th>
                    <th>Date</th>
                    <th>Terminal</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookstoreCardOrders as $order)
                <tr>
                    <td><span class="fw-bold text-dark">#{{ $order->order_number ?? 'ORD-'.$order->id }}</span></td>
                    <td>{{ $order->payment_reference ?? 'AUTH-'.rand(1000, 9999) }}</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td>BDO POS Terminal</td>
                    <td class="text-end fw-bold text-dark">₱{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No card transactions logged in the database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - CHARGE ACCOUNT -->
<div id="chargeAccountSalesTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th>Order No</th>
                    <th>Client Account</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookstoreChargeOrders as $order)
                <tr>
                    <td><span class="fw-bold text-dark">#{{ $order->order_number ?? 'ORD-'.$order->id }}</span></td>
                    <td>{{ $order->customer->customer_name ?? $order->customer->company_name ?? 'Walk-in Corporate Account' }}</td>
                    <td>{{ $order->created_at->addDays(30)->format('M d, Y') }}</td>
                    <td><span class="badge bg-warning text-dark">Unpaid (Net 30)</span></td>
                    <td class="text-end fw-bold text-dark">₱{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No charge account transactions logged in the database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - RETURNS -->
<div id="returnsTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th>Return Code</th>
                    <th>Item Description</th>
                    <th>Return Date</th>
                    <th>Reason</th>
                    <th class="text-end">Refunded Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No return logs recorded in the database.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - DISCOUNTS -->
<div id="discountsTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th>Order No</th>
                    <th>Customer</th>
                    <th>Discount Type</th>
                    <th>Discount Rate</th>
                    <th>Order Total</th>
                    <th class="text-end">Discount Deducted</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookstoreDiscountOrders ?? [] as $order)
                @php
                    $pct = (float) ($order->discount_percentage ?? 0);
                    if ($pct > 0) {
                        $discountTypeLabel = 'Percentage';
                        $discountTypeBadge = 'bg-primary text-white';
                        $rateStr = number_format($pct, 2) . '%';
                    } elseif ((float)$order->discount_amount > 0) {
                        $discountTypeLabel = 'Fixed Amount';
                        $discountTypeBadge = 'bg-info text-white';
                        $origTotal = (float)$order->total_amount + (float)$order->discount_amount;
                        $calcPct = $origTotal > 0 ? (((float)$order->discount_amount / $origTotal) * 100) : 0;
                        $rateStr = $calcPct > 0 ? number_format($calcPct, 2) . '%' : 'Fixed Amount';
                    } else {
                        $discountTypeLabel = 'None';
                        $discountTypeBadge = 'bg-secondary text-white';
                        $rateStr = '0.00%';
                    }
                @endphp
                <tr>
                    <td><span class="fw-bold text-dark">#{{ $order->so_number }}</span></td>
                    <td>{{ $order->customer->customer_name ?? ($order->customer->company_name ?? 'Walk-in Customer') }}</td>
                    <td><span class="badge {{ $discountTypeBadge }} px-2 py-1">{{ $discountTypeLabel }}</span></td>
                    <td>{{ $rateStr }}</td>
                    <td>₱{{ number_format($order->total_amount + $order->discount_amount, 2) }}</td>
                    <td class="text-end fw-bold text-danger">₱{{ number_format($order->discount_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No discount records found in the database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - EMPLOYEE PURCHASES -->
<div id="employeePurchasesTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th>Emp ID</th>
                    <th>Employee Name</th>
                    <th>Department</th>
                    <th>Purchase Date</th>
                    <th class="text-end">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No staff purchase logs recorded in the database.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

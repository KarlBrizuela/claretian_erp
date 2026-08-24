<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border: 1px solid #e2e8f0; background: #ffffff;">
    <div class="card-header bg-white border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 45px; height: 45px;">
                <i class="las la-tags fs-24"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark fs-18">MIBF Sales Ledger</h5>
                <p class="text-muted small mb-0">Revenues from MIBF POS terminal transactions, categorized by payment type</p>
            </div>
        </div>
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold">6 Channels</span>
    </div>
    <div class="card-body px-4 pt-1 pb-4">
        <div class="row g-3">

            <!-- Cash Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;" onclick="showSalesLedgerModal('MIBF - Cash', document.getElementById('cashSalesTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                                    <i class="las la-coins fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Cash</h6>
                            <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">MIBF counter cash collections.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Balance</span>
                            <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 800 !important;">₱{{ number_format($mibfCashSales, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GCash Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;" onclick="showSalesLedgerModal('MIBF - GCash Payments', document.getElementById('gcashSalesTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                                    <i class="las la-mobile-alt fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">GCash</h6>
                            <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">GCash e-wallet transactions.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Balance</span>
                            <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 800 !important;">₱{{ number_format($mibfGcashSales, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Maya Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;" onclick="showSalesLedgerModal('MIBF - Maya Payments', document.getElementById('mayaSalesTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                                    <i class="las la-wallet fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Maya</h6>
                            <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Maya e-wallet transactions.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Balance</span>
                            <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 800 !important;">₱{{ number_format($mibfMayaSales, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Credit Card Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;" onclick="showSalesLedgerModal('MIBF - Credit Card Payments', document.getElementById('creditCardSalesTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                                    <i class="las la-credit-card fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Credit Card</h6>
                            <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Card terminal settlements.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Balance</span>
                            <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 800 !important;">₱{{ number_format($mibfCardSales, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Check Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;" onclick="showSalesLedgerModal('MIBF - Check Payments', document.getElementById('checkSalesTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                                    <i class="las la-money-check-alt fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Check</h6>
                            <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Check payment transactions.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Balance</span>
                            <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 800 !important;">₱{{ number_format($mibfCheckSales, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bank Transfer Card -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;" onclick="showSalesLedgerModal('MIBF - Bank Transfer Payments', document.getElementById('bankSalesTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width: 36px; height: 36px;">
                                    <i class="las la-university fs-20"></i>
                                </div>
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill small fw-bold" style="font-size: 0.7rem; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                            </div>
                            <h6 class="mb-1 fw-bold fs-14" style="letter-spacing: -0.2px; font-weight: 700 !important; color: #0f172a !important;">Bank Transfer</h6>
                            <p class="mb-3" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569 !important;">Direct bank transfer deposits.</p>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <span class="small" style="font-size: 0.72rem; color: #475569 !important; font-weight: 600;">Balance</span>
                            <span class="fw-bold fs-15" style="color: #0f172a !important; font-weight: 800 !important;">₱{{ number_format($mibfBankSales, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


<!-- TEMPLATE CONTAINER - CASH SALES -->
<div id="cashSalesTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th>Order No</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Remarks</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mibfCashOrders as $order)
                <tr>
                    <td><span class="fw-bold text-dark font-monospace">{{ $order->so_number }}</span></td>
                    <td>{{ $order->customer->customer_name ?? 'Walk-In Customer' }}</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td><small class="text-muted">{{ $order->remarks ?: '-' }}</small></td>
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
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th>Order No</th>
                    <th>Ref ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mibfGcashOrders as $order)
                <tr>
                    <td><span class="fw-bold text-dark font-monospace">{{ $order->so_number }}</span></td>
                    <td><span class="font-monospace text-muted">{{ $order->payment_reference ?? 'N/A' }}</span></td>
                    <td>{{ $order->customer->customer_name ?? 'Walk-In Customer' }}</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td class="text-end fw-bold text-dark">₱{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No GCash transactions logged in the database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - MAYA SALES -->
<div id="mayaSalesTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th>Order No</th>
                    <th>Ref ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mibfMayaOrders as $order)
                <tr>
                    <td><span class="fw-bold text-dark font-monospace">{{ $order->so_number }}</span></td>
                    <td><span class="font-monospace text-muted">{{ $order->payment_reference ?? 'N/A' }}</span></td>
                    <td>{{ $order->customer->customer_name ?? 'Walk-In Customer' }}</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td class="text-end fw-bold text-dark">₱{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No Maya transactions logged in the database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - CREDIT CARD SALES -->
<div id="creditCardSalesTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th>Order No</th>
                    <th>Ref ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mibfCardOrders as $order)
                <tr>
                    <td><span class="fw-bold text-dark font-monospace">{{ $order->so_number }}</span></td>
                    <td><span class="font-monospace text-muted">{{ $order->payment_reference ?? 'N/A' }}</span></td>
                    <td>{{ $order->customer->customer_name ?? 'Walk-In Customer' }}</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td class="text-end fw-bold text-dark">₱{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No credit card transactions logged in the database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - CHECK SALES -->
<div id="checkSalesTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th>Order No</th>
                    <th>Check Number</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mibfCheckOrders as $order)
                <tr>
                    <td><span class="fw-bold text-dark font-monospace">{{ $order->so_number }}</span></td>
                    <td><span class="font-monospace text-muted">{{ $order->payment_reference ?? 'N/A' }}</span></td>
                    <td>{{ $order->customer->customer_name ?? 'Walk-In Customer' }}</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td class="text-end fw-bold text-dark">₱{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No check transactions logged in the database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TEMPLATE CONTAINER - BANK TRANSFER SALES -->
<div id="bankSalesTemplate" style="display: none;">
    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th>Order No</th>
                    <th>Reference</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mibfBankOrders as $order)
                <tr>
                    <td><span class="fw-bold text-dark font-monospace">{{ $order->so_number }}</span></td>
                    <td><span class="font-monospace text-muted">{{ $order->payment_reference ?? 'N/A' }}</span></td>
                    <td>{{ $order->customer->customer_name ?? 'Walk-In Customer' }}</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td class="text-end fw-bold text-dark">₱{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No bank transfer transactions logged in the database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

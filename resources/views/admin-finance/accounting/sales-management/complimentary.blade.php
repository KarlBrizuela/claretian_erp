<div class="row">
    <!-- Summary Metric Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 5px solid #6f42c1 !important; background: #ffffff !important;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-1 font-weight-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Complimentary Orders</p>
                        <h3 class="fw-bold mb-0 text-dark" style="font-size: 1.75rem; color: #1e293b !important;">{{ $complimentaryOrders->count() }}</h3>
                    </div>
                    <div class="icon-circle p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #f3e8ff;">
                        <i class="las la-gift fs-24" style="color: #6f42c1;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 5px solid #e11d48 !important; background: #ffffff !important;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-1 font-weight-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Expense Valuation</p>
                        <h3 class="fw-bold mb-0 text-danger" style="font-size: 1.75rem; color: #e11d48 !important;">₱{{ number_format($complimentaryTotalValuation, 2) }}</h3>
                    </div>
                    <div class="icon-circle p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #ffe4e6;">
                        <i class="las la-wallet fs-24" style="color: #e11d48;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 5px solid #d97706 !important; background: #ffffff !important;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-1 font-weight-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Posted Expense Entries</p>
                        <h3 class="fw-bold mb-0 text-dark" style="font-size: 1.75rem; color: #1e293b !important;">{{ $complimentaryOrders->whereNotIn('status', ['draft', 'pending_mkt_approval', 'pending_acct_approval'])->count() }}</h3>
                    </div>
                    <div class="icon-circle p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #fef3c7;">
                        <i class="las la-file-invoice-dollar fs-24" style="color: #d97706;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 5px solid #16a34a !important; background: #ffffff !important;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-1 font-weight-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Completed Deliveries</p>
                        <h3 class="fw-bold mb-0 text-dark" style="font-size: 1.75rem; color: #1e293b !important;">{{ $complimentaryOrders->where('status', 'completed')->count() }}</h3>
                    </div>
                    <div class="icon-circle p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #dcfce7;">
                        <i class="las la-check-circle fs-24" style="color: #16a34a;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0 text-dark"><i class="las la-gift me-2 text-danger"></i>Complimentary & Donation Receipts Ledger</h5>
            <p class="text-muted small mb-0">List of complimentary orders. These orders generate Acknowledgement Receipts and are posted to Expense (COA 5100), not Sales Revenue.</p>
        </div>
        <div class="badge bg-soft-danger text-danger px-3 py-2 fw-bold" style="border-radius: 20px;">
            <i class="las la-info-circle me-1"></i> Non-Sales / Expense Category
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="complimentaryLedgerTable">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">SO # / Ref</th>
                        <th>Recipient / Customer</th>
                        <th>Items Summary</th>
                        <th>Valuation (Expense)</th>
                        <th>Created Date</th>
                        <th>Receipt Status</th>
                        <th class="pe-4 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($complimentaryOrders as $order)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold text-dark">{{ $order->so_number }}</span>
                            @if($order->ref_number)
                                <br><small class="text-muted">Ref: {{ $order->ref_number }}</small>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $order->customer->customer_name ?? 'Walk-in / Recipient' }}</div>
                            <small class="text-muted">{{ Str::limit($order->billing_address ?? ($order->customer->address ?? 'No address'), 35) }}</small>
                        </td>
                        <td>
                            <span class="badge bg-soft-secondary text-dark">
                                {{ $order->items->sum('quantity') }} items
                            </span>
                            <small class="text-muted d-block mt-1">
                                {{ Str::limit($order->items->map(fn($i) => ($i->product->name ?? $i->book->name ?? 'Item') . ' (' . $i->quantity . ')')->implode(', '), 40) }}
                            </small>
                        </td>
                        <td>
                            @php
                                $itemValuation = 0;
                                foreach($order->items as $i) {
                                    $c = ($i->book && $i->book->cost > 0) ? $i->book->cost : ($i->unit_price > 0 ? $i->unit_price : 0);
                                    $itemValuation += ($c * $i->quantity);
                                }
                                if ($itemValuation <= 0) $itemValuation = $order->total_amount;
                            @endphp
                            <span class="fw-bold text-danger">₱{{ number_format($itemValuation, 2) }}</span>
                            <small class="d-block text-muted" style="font-size: 0.75rem;">COA 5100 Expense</small>
                        </td>
                        <td>
                            <div class="text-dark small">{{ \Carbon\Carbon::parse($order->created_at)->timezone('Asia/Manila')->format('M d, Y') }}</div>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($order->created_at)->timezone('Asia/Manila')->format('h:i A') }}</small>
                        </td>
                        <td>
                            @if(in_array($order->status, ['ready_for_packing', 'ready_for_delivery', 'completed']))
                                <span class="badge bg-success"><i class="las la-check-circle me-1"></i> Posted to Expense</span>
                                <small class="d-block text-muted" style="font-size: 0.75rem;">COA 5100 Entry Created</small>
                            @elseif(in_array($order->status, ['picking', 'pending_ar_prep']))
                                <span class="badge bg-info text-white"><i class="las la-boxes me-1"></i> Logistics Picking</span>
                            @else
                                <span class="badge bg-secondary">{{ ucwords(str_replace('_', ' ', $order->status)) }}</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('admin-finance.sales-order.detail', $order->id) }}" class="btn btn-light btn-sm text-dark border">
                                <i class="las la-eye me-1"></i> View Order
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="las la-gift fs-48 mb-2 d-block text-secondary"></i>
                            No complimentary receipts found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .inv-card {
            background: #fff;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
    </style>
    @endpush

    <div class="container-fluid">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="las la-check-circle me-2 fs-18"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('admin-finance.investments.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                        <i class="las la-arrow-left me-1"></i> Back to Investments Ledger
                    </a>
                    <h4 class="fs-24 fw-bold text-dark mb-0">{{ $investment->name }}</h4>
                    <p class="text-muted small mb-0">Code: <span class="font-monospace fw-bold text-dark">{{ $investment->portfolio_code }}</span> | Class: <span class="badge bg-light text-dark border">{{ $investment->type }}</span></p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-danger btn-sm px-3 text-white rounded shadow-sm d-flex align-items-center gap-2" style="background-color: #D9251C; border-color: #D9251C; height: 40px;" data-bs-toggle="modal" data-bs-target="#recordPayoutModal">
                        <i class="las la-plus-circle fs-18"></i> Record Dividend / Interest
                    </button>
                    <button class="btn btn-outline-secondary btn-sm px-3 rounded shadow-sm d-flex align-items-center gap-2" style="height: 40px;" onclick="window.print()">
                        <i class="las la-print fs-18"></i> Print Profile
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left: Financial Performance & Details -->
            <div class="col-md-5 mb-4">
                <div class="inv-card mb-4">
                    <h6 class="fw-bold text-uppercase text-muted small mb-3">Portfolio Financial Summary</h6>
                    
                    <div class="p-3 rounded text-white mb-3" style="background-color: #D9251C;">
                        <span class="small text-white-50 d-block text-uppercase fw-bold">Current Market Valuation</span>
                        <h3 class="fw-bold mb-0">₱{{ number_format($investment->current_value, 2) }}</h3>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="p-2 border rounded bg-light">
                                <span class="text-muted small d-block">Principal Invested</span>
                                <span class="fw-bold text-dark fs-16">₱{{ number_format($investment->principal_amount, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded bg-light">
                                <span class="text-muted small d-block">Overall ROI (%)</span>
                                <span class="fw-bold fs-16" style="color: #D9251C;">{{ $investment->roi_percentage }}%</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded bg-light">
                                <span class="text-muted small d-block">Earned Dividends</span>
                                <span class="fw-bold text-success fs-16">₱{{ number_format($investment->total_dividends, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded bg-light">
                                <span class="text-muted small d-block">Earned Interest</span>
                                <span class="fw-bold text-success fs-16">₱{{ number_format($investment->total_interest, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h6 class="fw-bold text-dark small mb-3">Institution & Maturity Specs</h6>
                    <div class="mb-2">
                        <span class="text-muted small d-block">Institution / Fund Manager:</span>
                        <span class="fw-bold text-dark">{{ $investment->institution }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted small d-block">Stated Interest / Yield Rate:</span>
                        <span class="fw-bold text-dark">{{ $investment->interest_rate }}% p.a.</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted small d-block">Acquisition / Start Date:</span>
                        <span class="fw-bold text-dark">{{ $investment->acquisition_date ? $investment->acquisition_date->format('F d, Y') : 'N/A' }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted small d-block">Maturity Date & Status:</span>
                        @if($investment->maturity_date)
                            <span class="fw-bold text-dark d-block">{{ $investment->maturity_date->format('F d, Y') }}</span>
                            @if($investment->maturity_date->isPast())
                            <span class="badge bg-info-subtle text-info">Matured on {{ $investment->maturity_date->format('M d, Y') }}</span>
                            @else
                            <span class="badge bg-success-subtle text-success">Active (Matures in {{ $investment->maturity_date->diffInDays(now()) }} days)</span>
                            @endif
                        @else
                        <span class="text-muted small">Open-ended / No fixed maturity</span>
                        @endif
                    </div>
                    @if($investment->notes)
                    <div class="mt-3 p-2 bg-light rounded border">
                        <span class="text-muted small fw-bold d-block">Notes:</span>
                        <span class="small text-dark">{{ $investment->notes }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Right: Dividend & Interest Transaction Log -->
            <div class="col-md-7 mb-4">
                <div class="inv-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold text-dark mb-0">Dividend & Interest Payout History</h5>
                            <p class="text-muted small mb-0">Recorded earnings, distributions, and valuation updates</p>
                        </div>
                        <span class="badge text-white px-3 py-2 fs-14" style="background-color: #D9251C;">
                            Total Gains: ₱{{ number_format($investment->total_return, 2) }}
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-dark text-white small text-uppercase">
                                <tr>
                                    <th>Date</th>
                                    <th>Transaction Type</th>
                                    <th>Ref / Check No</th>
                                    <th class="text-end text-success">Payout Amount</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($investment->transactions as $tx)
                                <tr>
                                    <td><span class="fw-bold text-dark small">{{ $tx->transaction_date ? $tx->transaction_date->format('M d, Y') : 'N/A' }}</span></td>
                                    <td>
                                        @if($tx->transaction_type === 'Dividend')
                                        <span class="badge bg-success-subtle text-success">Dividend Payout</span>
                                        @elseif($tx->transaction_type === 'Interest')
                                        <span class="badge bg-info-subtle text-info">Interest Payment</span>
                                        @else
                                        <span class="badge bg-secondary-subtle text-secondary">{{ $tx->transaction_type }}</span>
                                        @endif
                                    </td>
                                    <td><span class="font-monospace text-muted small">{{ $tx->reference_no ?: 'N/A' }}</span></td>
                                    <td class="text-end fw-bold text-success">₱{{ number_format($tx->amount, 2) }}</td>
                                    <td><span class="text-muted small">{{ $tx->notes ?: 'N/A' }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No payouts or earnings recorded yet. Click "Record Dividend / Interest" above to log a transaction.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: RECORD DIVIDEND / INTEREST -->
    <div class="modal fade" id="recordPayoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin-finance.investments.transaction.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="investment_id" value="{{ $investment->id }}">
                    <div class="modal-header text-white" style="background-color: #D9251C;">
                        <h5 class="modal-title fw-bold"><i class="las la-hand-holding-usd me-2"></i>Record Earnings or Valuation</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Transaction Type <span class="text-danger">*</span></label>
                            <select name="transaction_type" class="form-select" required>
                                <option value="Dividend">Dividend Payout</option>
                                <option value="Interest">Interest Payment</option>
                                <option value="Valuation Update">Market Valuation Update</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Transaction / Payout Date <span class="text-danger">*</span></label>
                            <input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Amount (₱) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Reference / Check Number</label>
                            <input type="text" name="reference_no" class="form-control" placeholder="e.g. DIV-884920, CHK-99201">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Quarterly dividend distribution, quarterly interest..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn text-white px-4 fw-bold" style="background-color: #D9251C; border-color: #D9251C;">Save Transaction</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

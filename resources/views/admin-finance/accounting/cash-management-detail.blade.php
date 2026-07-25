<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .csh-card {
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
                    <a href="{{ route('admin-finance.cash-management.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                        <i class="las la-arrow-left me-1"></i> Back to Cash Management
                    </a>
                    <h4 class="fs-24 fw-bold text-dark mb-0">{{ $account->bank_name }} Statement</h4>
                    <p class="text-muted small mb-0">Code: <span class="font-monospace fw-bold text-dark">{{ $account->account_code }}</span> | Account No: <span class="font-monospace fw-bold text-dark">{{ $account->account_number }}</span> | Type: <span class="badge bg-light text-dark border">{{ $account->account_type }}</span></p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary btn-sm px-3 rounded shadow-sm d-flex align-items-center gap-2" style="height: 40px;" onclick="window.print()">
                        <i class="las la-print fs-18"></i> Print Statement
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left: Bank Account Summary & Reconciliation Worksheet -->
            <div class="col-md-5 mb-4">
                <div class="csh-card mb-4">
                    <h6 class="fw-bold text-uppercase text-muted small mb-3">Bank Account & Reconciliation Worksheet</h6>
                    
                    <div class="p-3 rounded text-white mb-3" style="background-color: #D9251C;">
                        <span class="small text-white-50 d-block text-uppercase fw-bold">Current Live Balance</span>
                        <h3 class="fw-bold mb-0">₱{{ number_format($account->current_balance, 2) }}</h3>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="p-2 border rounded bg-light">
                                <span class="text-muted small d-block">Opening Balance</span>
                                <span class="fw-bold text-dark fs-16">₱{{ number_format($account->opening_balance, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded bg-light">
                                <span class="text-muted small d-block">Currency / Status</span>
                                <span class="fw-bold text-dark fs-16">{{ $account->currency }} ({{ $account->status }})</span>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h6 class="fw-bold text-dark small mb-3">Bank Reconciliation Summary</h6>
                    @php
                        $clearedInflows = $account->transactions->where('category', 'Inflow')->where('status', 'Cleared')->sum('amount');
                        $clearedOutflows = $account->transactions->where('category', 'Outflow')->where('status', 'Cleared')->sum('amount');
                        $pendingChecks = $account->transactions->where('transaction_type', 'Check Issuance')->where('status', 'Pending')->sum('amount');
                    @endphp
                    <div class="mb-2 d-flex justify-content-between">
                        <span class="text-muted small">Cleared Cash Inflows:</span>
                        <span class="fw-bold text-success">₱{{ number_format($clearedInflows, 2) }}</span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <span class="text-muted small">Cleared Outflows & Checks:</span>
                        <span class="fw-bold text-dark">₱{{ number_format($clearedOutflows, 2) }}</span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <span class="text-muted small">Outstanding Checks (Pending Clearing):</span>
                        <span class="fw-bold text-danger">₱{{ number_format($pendingChecks, 2) }}</span>
                    </div>

                    @if($account->notes)
                    <div class="mt-3 p-2 bg-light rounded border">
                        <span class="text-muted small fw-bold d-block">Account Notes:</span>
                        <span class="small text-dark">{{ $account->notes }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Right: Bank Account Transactions Statement -->
            <div class="col-md-7 mb-4">
                <div class="csh-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold text-dark mb-0">Bank Account Statement Ledger</h5>
                            <p class="text-muted small mb-0">Itemized deposits, check issuances, transfers, and reconciliations</p>
                        </div>
                        <span class="badge text-white px-3 py-2 fs-14" style="background-color: #D9251C;">
                            Entries: {{ $account->transactions->count() }}
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-dark text-white small text-uppercase">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Ref / Check #</th>
                                    <th>Payee / Payer</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($account->transactions as $tx)
                                <tr>
                                    <td><span class="fw-bold text-dark small">{{ $tx->transaction_date ? $tx->transaction_date->format('M d, Y') : 'N/A' }}</span></td>
                                    <td><span class="badge bg-light text-dark border">{{ $tx->transaction_type }}</span></td>
                                    <td><span class="font-monospace text-dark fw-bold small">{{ $tx->reference_no ?: 'N/A' }}</span></td>
                                    <td><span class="text-dark small">{{ $tx->payee_or_payer ?: 'N/A' }}</span></td>
                                    <td class="text-end fw-bold {{ $tx->category === 'Inflow' ? 'text-success' : 'text-dark' }}">
                                        ₱{{ number_format($tx->amount, 2) }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success-subtle text-success">{{ $tx->status }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No transactions logged for this bank account yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

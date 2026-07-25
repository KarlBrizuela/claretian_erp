<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .inv-header-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.75rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            border: 0;
            margin-bottom: 1.5rem;
        }

        .hover-row {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .hover-row:hover {
            background-color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
        }

        .category-pill {
            font-size: 0.82rem;
            font-weight: 600;
            padding: 7px 15px;
            border-radius: 20px;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
            display: inline-block;
        }

        .category-pill.active {
            background-color: #D9251C;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(217, 37, 28, 0.25);
        }

        .category-pill:not(.active) {
            background-color: #f8f9fa;
            color: #495057;
            border: 1px solid #e9ecef;
        }

        .category-pill:not(.active):hover {
            background-color: #e9ecef;
            color: #212529;
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

        <!-- Master Title Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="inv-header-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h4 class="fs-24 mb-1 fw-bold text-dark" style="letter-spacing: -0.5px;">Investments Module</h4>
                        <p class="text-muted small mb-0">Standalone investment portfolio management tracking Time Deposits, Stocks, Mutual Funds, Bonds, Money Market, Dividend History, Interest, Maturity, and ROI Performance.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-danger btn-sm px-3 text-white rounded shadow-sm d-flex align-items-center gap-2" style="background-color: #D9251C; border-color: #D9251C; height: 40px;" data-bs-toggle="modal" data-bs-target="#addInvestmentModal">
                            <i class="las la-plus-circle fs-18"></i> Add Investment Asset
                        </button>
                        <button class="btn btn-outline-secondary btn-sm px-3 rounded shadow-sm d-flex align-items-center gap-2" style="height: 40px;" onclick="window.print()">
                            <i class="las la-print fs-18"></i> Print Portfolio Summary
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric summary cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 50px; height: 50px; color: #D9251C;">
                            <i class="las la-chart-pie fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Current Portfolio Value</span>
                            <h4 class="fw-bold text-dark mb-0">₱{{ number_format($metrics['total_current_val'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-primary" style="width: 50px; height: 50px;">
                            <i class="las la-wallet fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Principal Invested</span>
                            <h4 class="fw-bold text-primary mb-0">₱{{ number_format($metrics['total_principal'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-success" style="width: 50px; height: 50px;">
                            <i class="las la-hand-holding-usd fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Dividends & Interest</span>
                            <h4 class="fw-bold text-success mb-0">₱{{ number_format($metrics['total_dividends'] + $metrics['total_interest'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 50px; height: 50px; color: #D9251C;">
                            <i class="las la-percentage fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Overall Portfolio ROI</span>
                            <h4 class="fw-bold mb-0" style="color: #D9251C;">{{ $metrics['overall_roi_pct'] }}%</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Asset Classes & Sub-Modules Filter Pills -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm p-3" style="border-radius: 12px; background: #fff;">
                    <span class="text-muted small fw-bold mb-2 d-block text-uppercase">Investment Categories & Sub-Modules:</span>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin-finance.investments.index', ['type' => 'All']) }}" class="category-pill {{ $selectedType == 'All' ? 'active' : '' }}">
                            All Investments ({{ $metrics['total_items_count'] }})
                        </a>
                        @foreach($types as $t)
                        <a href="{{ route('admin-finance.investments.index', ['type' => $t]) }}" class="category-pill {{ $selectedType == $t ? 'active' : '' }}">
                            {{ $t }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Investment Portfolio Ledger Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold text-dark fs-18">Investment Portfolio Ledger</h5>
                            <p class="text-muted small mb-0">Track principal, market valuation, dividend payouts, maturity dates, and cumulative return</p>
                        </div>
                        <form action="{{ route('admin-finance.investments.index') }}" method="GET" class="d-flex gap-2">
                            <input type="hidden" name="type" value="{{ $selectedType }}">
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search Code, Name, Institution..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-sm text-white px-3" style="background-color: #D9251C; border-color: #D9251C;">Filter</button>
                        </form>
                    </div>

                    <div class="card-body pt-2">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-dark text-white small text-uppercase">
                                    <tr>
                                        <th>Code</th>
                                        <th>Investment Asset Name</th>
                                        <th>Asset Class</th>
                                        <th>Bank / Institution</th>
                                        <th>Acquisition & Maturity</th>
                                        <th class="text-end">Principal</th>
                                        <th class="text-end">Current Value</th>
                                        <th class="text-end text-success">Dividends & Interest</th>
                                        <th class="text-end" style="background-color: #D9251C;">Total Return</th>
                                        <th class="text-center" style="background-color: #e53935;">ROI %</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($investments as $inv)
                                    <tr class="hover-row">
                                        <td><span class="fw-bold text-dark font-monospace">{{ $inv->portfolio_code }}</span></td>
                                        <td>
                                            <span class="fw-bold text-dark d-block fs-14">{{ $inv->name }}</span>
                                            <span class="text-muted small">Yield: {{ $inv->interest_rate }}% p.a.</span>
                                        </td>
                                        <td><span class="badge bg-light text-dark border">{{ $inv->type }}</span></td>
                                        <td><span class="fw-bold text-dark small">{{ $inv->institution }}</span></td>
                                        <td>
                                            <span class="fw-bold text-dark d-block small">Start: {{ $inv->acquisition_date ? $inv->acquisition_date->format('M d, Y') : 'N/A' }}</span>
                                            <span class="text-muted small">
                                                @if($inv->maturity_date)
                                                Matures: {{ $inv->maturity_date->format('M d, Y') }}
                                                @else
                                                No fixed maturity
                                                @endif
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold text-dark">₱{{ number_format($inv->principal_amount, 2) }}</td>
                                        <td class="text-end fw-bold text-primary">₱{{ number_format($inv->current_value, 2) }}</td>
                                        <td class="text-end text-success fw-bold">₱{{ number_format($inv->total_dividends + $inv->total_interest, 2) }}</td>
                                        <td class="text-end fw-bold text-white" style="background-color: #D9251C;">
                                            ₱{{ number_format($inv->total_return, 2) }}
                                        </td>
                                        <td class="text-center fw-bold text-white" style="background-color: #e53935;">
                                            {{ $inv->roi_percentage }}%
                                        </td>
                                        <td class="text-center">
                                            @if($inv->status === 'Active')
                                            <span class="badge bg-success-subtle text-success">Active</span>
                                            @elseif($inv->status === 'Matured')
                                            <span class="badge bg-info-subtle text-info">Matured</span>
                                            @else
                                            <span class="badge bg-secondary-subtle text-secondary">{{ $inv->status }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin-finance.investments.show', $inv->id) }}" class="btn btn-sm btn-outline-danger px-2 py-1" style="color: #D9251C; border-color: #D9251C;">
                                                <i class="las la-eye"></i> View Profile
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="12" class="text-center py-4 text-muted">No investments recorded yet. Click "Add Investment Asset" above to add your first Time Deposit, Stock, Fund, or Bond asset.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: ADD INVESTMENT -->
    <div class="modal fade" id="addInvestmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin-finance.investments.store') }}" method="POST">
                    @csrf
                    <div class="modal-header text-white" style="background-color: #D9251C;">
                        <h5 class="modal-title fw-bold"><i class="las la-plus-circle me-2"></i>Add Investment Asset</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold small text-muted">Investment Asset Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. 5-Yr BDO Time Deposit, BPI Fixed Income Bond, COL Financial Bluechips" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Asset Class / Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-select" required>
                                    @foreach($types as $t)
                                    <option value="{{ $t }}">{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Institution / Bank / Broker <span class="text-danger">*</span></label>
                                <input type="text" name="institution" class="form-control" placeholder="e.g. BDO Unibank, Metrobank, Sun Life, COL Financial" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Interest / Yield Rate (% p.a.)</label>
                                <input type="number" step="0.01" name="interest_rate" class="form-control" placeholder="4.50">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Principal Invested Amount (₱) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="principal_amount" class="form-control" placeholder="100000.00" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Current Market Value (₱) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="current_value" class="form-control" placeholder="100000.00" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Acquisition / Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="acquisition_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Maturity Date (For Bonds/Time Deposits)</label>
                                <input type="date" name="maturity_date" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted">Investment Notes / Strategy</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Account numbers, terms, roll-over instructions..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn text-white px-4 fw-bold" style="background-color: #D9251C; border-color: #D9251C;">Save Investment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

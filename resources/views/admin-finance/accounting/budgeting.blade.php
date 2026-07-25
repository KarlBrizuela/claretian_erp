<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .bdg-header-card {
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
                <div class="bdg-header-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h4 class="fs-24 mb-1 fw-bold text-dark" style="letter-spacing: -0.5px;">Annual Budgeting & Variance Module</h4>
                        <p class="text-muted small mb-0">Divisional annual budget submissions and automated real-time ERP comparison: Budget vs. Actual vs. Variance vs. % Used vs. Year-End Forecast.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-danger btn-sm px-3 text-white rounded shadow-sm d-flex align-items-center gap-2" style="background-color: #D9251C; border-color: #D9251C; height: 40px;" data-bs-toggle="modal" data-bs-target="#submitBudgetModal">
                            <i class="las la-plus-circle fs-18"></i> Submit Annual Budget
                        </button>
                        <button class="btn btn-outline-secondary btn-sm px-3 rounded shadow-sm d-flex align-items-center gap-2" style="height: 40px;" onclick="window.print()">
                            <i class="las la-print fs-18"></i> Print Budget Report
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
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-primary" style="width: 50px; height: 50px;">
                            <i class="las la-wallet fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Total Corporate Budget</span>
                            <h4 class="fw-bold text-primary mb-0">₱{{ number_format($metrics['total_allocated'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 50px; height: 50px; color: #D9251C;">
                            <i class="las la-shopping-cart fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Total Actual Spend</span>
                            <h4 class="fw-bold text-dark mb-0">₱{{ number_format($metrics['total_actual'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light {{ $metrics['total_variance'] >= 0 ? 'text-success' : 'text-danger' }}" style="width: 50px; height: 50px;">
                            <i class="las {{ $metrics['total_variance'] >= 0 ? 'la-check-circle' : 'la-exclamation-circle' }} fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Overall Variance</span>
                            <h4 class="fw-bold mb-0 {{ $metrics['total_variance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                ₱{{ number_format($metrics['total_variance'], 2) }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 50px; height: 50px; color: #D9251C;">
                            <i class="las la-chart-line fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Year-End Forecast</span>
                            <h4 class="fw-bold mb-0" style="color: #D9251C;">₱{{ number_format($metrics['total_forecast'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Division Filter Pills -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm p-3" style="border-radius: 12px; background: #fff;">
                    <span class="text-muted small fw-bold mb-2 d-block text-uppercase">Divisions Filter:</span>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin-finance.budgeting.index', ['division' => 'All', 'year' => $fiscalYear]) }}" class="category-pill {{ $selectedDivision == 'All' ? 'active' : '' }}">
                            All Divisions ({{ $metrics['total_departments_count'] }})
                        </a>
                        @foreach(array_keys($divisions) as $div)
                        <a href="{{ route('admin-finance.budgeting.index', ['division' => $div, 'year' => $fiscalYear]) }}" class="category-pill {{ $selectedDivision == $div ? 'active' : '' }}">
                            {{ $div }} Division
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Budget vs Actual vs Variance Matrix Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold text-dark fs-18">Department Budget Performance Ledger (FY {{ $fiscalYear }})</h5>
                            <p class="text-muted small mb-0">Comparison of Allocated Budget, Realized Actual Spend, Variance, Burn Rate, and Forecasted Spend</p>
                        </div>
                        <form action="{{ route('admin-finance.budgeting.index') }}" method="GET" class="d-flex gap-2">
                            <input type="hidden" name="division" value="{{ $selectedDivision }}">
                            <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="2026" {{ $fiscalYear == 2026 ? 'selected' : '' }}>FY 2026</option>
                                <option value="2025" {{ $fiscalYear == 2025 ? 'selected' : '' }}>FY 2025</option>
                            </select>
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search Department..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-sm text-white px-3" style="background-color: #D9251C; border-color: #D9251C;">Search</button>
                        </form>
                    </div>

                    <div class="card-body pt-2">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-dark text-white small text-uppercase">
                                    <tr>
                                        <th>Budget Code</th>
                                        <th>Division</th>
                                        <th>Department Name</th>
                                        <th class="text-end">Annual Budget</th>
                                        <th class="text-end">Actual Spend</th>
                                        <th class="text-end">Variance (Remaining)</th>
                                        <th class="text-center" style="min-width: 140px;">Percentage Used</th>
                                        <th class="text-end" style="background-color: #D9251C;">Year-End Forecast</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($budgets as $bdg)
                                    @php
                                        $pct = min(100, max(0, $bdg->percentage_used));
                                        $isOver = $bdg->variance < 0;
                                    @endphp
                                    <tr class="hover-row">
                                        <td><span class="fw-bold text-dark font-monospace">{{ $bdg->budget_code }}</span></td>
                                        <td><span class="badge bg-light text-dark border">{{ $bdg->division }}</span></td>
                                        <td><span class="fw-bold text-dark fs-14">{{ $bdg->department }}</span></td>
                                        <td class="text-end fw-bold text-primary">₱{{ number_format($bdg->allocated_budget, 2) }}</td>
                                        <td class="text-end fw-bold text-dark">₱{{ number_format($bdg->actual_spend, 2) }}</td>
                                        <td class="text-end fw-bold {{ $isOver ? 'text-danger' : 'text-success' }}">
                                            ₱{{ number_format($bdg->variance, 2) }}
                                            <span class="d-block small fw-normal">{{ $isOver ? 'Over Budget' : 'Favorable' }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="progress" style="height: 10px;">
                                                <div class="progress-bar {{ $pct >= 90 ? 'bg-danger' : ($pct >= 75 ? 'bg-warning' : 'bg-success') }}" style="width: {{ $pct }}%;"></div>
                                            </div>
                                            <span class="small font-monospace fw-bold text-dark">{{ $bdg->percentage_used }}%</span>
                                        </td>
                                        <td class="text-end fw-bold text-white" style="background-color: #D9251C;">
                                            ₱{{ number_format($bdg->forecasted_spend, 2) }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success-subtle text-success">{{ $bdg->status }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin-finance.budgeting.show', $bdg->id) }}" class="btn btn-sm btn-outline-danger px-2 py-1" style="color: #D9251C; border-color: #D9251C;">
                                                <i class="las la-eye"></i> View Sheet
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4 text-muted">No department annual budgets submitted for FY {{ $fiscalYear }} yet. Click "Submit Annual Budget" above.</td>
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

    <!-- MODAL: SUBMIT ANNUAL DEPARTMENT BUDGET -->
    <div class="modal fade" id="submitBudgetModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin-finance.budgeting.store') }}" method="POST">
                    @csrf
                    <div class="modal-header text-white" style="background-color: #D9251C;">
                        <h5 class="modal-title fw-bold"><i class="las la-balance-scale me-2"></i>Submit Annual Department Budget</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Fiscal Year <span class="text-danger">*</span></label>
                                <select name="fiscal_year" class="form-select" required>
                                    <option value="2026">FY 2026</option>
                                    <option value="2027">FY 2027</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Division <span class="text-danger">*</span></label>
                                <select name="division" class="form-select" id="divSelect" required>
                                    @foreach(array_keys($divisions) as $div)
                                    <option value="{{ $div }}">{{ $div }} Division</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Department Name <span class="text-danger">*</span></label>
                                <input type="text" name="department" class="form-control" placeholder="e.g. Press & Printing Department" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Annual Allocated Budget (₱) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="allocated_budget" class="form-control" placeholder="1500000.00" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Initial Actual Spend (₱)</label>
                                <input type="number" step="0.01" name="actual_spend" class="form-control" placeholder="0.00">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted">Budget Justification & Notes</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Key department objectives, expansion plans, equipment maintenance allocations..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn text-white px-4 fw-bold" style="background-color: #D9251C; border-color: #D9251C;">Save Annual Budget</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .bdg-header-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.5rem 1.75rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            margin-bottom: 1.5rem;
        }

        .btn-bdg-primary {
            background-color: #D9251C;
            border-color: #D9251C;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.8125rem;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
        }

        .btn-bdg-primary:hover {
            background-color: #b91c1c;
            border-color: #b91c1c;
            color: #ffffff;
        }

        .bdg-kpi-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.25rem;
            transition: all 0.2s ease;
        }

        .bdg-kpi-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }

        .kpi-icon-wrapper {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .table-custom-header thead th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-size: 0.725rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            border-bottom: 1px solid #e2e8f0 !important;
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

        <!-- Master Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="bdg-header-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(217, 37, 28, 0.08); color: #D9251C;">
                            <i class="las la-wallet fs-24"></i>
                        </div>
                        <div>
                            <h4 class="fs-20 mb-1 fw-bold text-dark" style="letter-spacing: -0.3px;">Annual Budgeting Ledger</h4>
                            <p class="text-muted small mb-0">Divisional annual budget submissions, department allocations, and real-time variance tracking.</p>
                        </div>
                    </div>
                    <div>
                        <button class="btn btn-bdg-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#submitBudgetModal">
                            <i class="las la-plus-circle fs-16"></i> Submit Annual Budget
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric summary cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="bdg-kpi-card h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.725rem; letter-spacing: 0.5px;">Total Corporate Budget</span>
                        <div class="kpi-icon-wrapper" style="background-color: #f0fdf4; color: #16a34a;">
                            <i class="las la-coins fs-20"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-dark mb-0 fs-20">₱{{ number_format($metrics['total_allocated'], 2) }}</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bdg-kpi-card h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.725rem; letter-spacing: 0.5px;">Total Actual Spend</span>
                        <div class="kpi-icon-wrapper" style="background-color: #fef2f2; color: #D9251C;">
                            <i class="las la-shopping-cart fs-20"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-dark mb-0 fs-20">₱{{ number_format($metrics['total_actual'], 2) }}</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bdg-kpi-card h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.725rem; letter-spacing: 0.5px;">Remaining Variance</span>
                        <div class="kpi-icon-wrapper" style="background-color: #eff6ff; color: #2563eb;">
                            <i class="las la-check-circle fs-20"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-dark mb-0 fs-20">₱{{ number_format($metrics['total_variance'], 2) }}</h4>
                </div>
            </div>
        </div>

        <!-- Budget Ledger Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border: 1px solid #e2e8f0 !important;">
                    <div class="card-header bg-white border-0 pt-3 pb-2 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold text-dark mb-0"><i class="las la-list me-2 fs-18"></i>Department Budget Performance (FY {{ $fiscalYear }})</h6>
                        <form method="GET" action="{{ route('admin-finance.budgeting.index') }}" class="d-flex align-items-center gap-2">
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search department..." value="{{ $search ?? '' }}" style="width: 220px; border-radius: 6px;">
                            <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="las la-search"></i></button>
                        </form>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle table-custom-header">
                                <thead>
                                    <tr>
                                        <th>Budget Code</th>
                                        <th>Division</th>
                                        <th>Department Name</th>
                                        <th class="text-end">Annual Budget</th>
                                        <th class="text-end">Actual Spend</th>
                                        <th class="text-end">Variance (Remaining)</th>
                                        <th class="text-center" style="width: 100px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($budgets as $b)
                                    <tr>
                                        <td><span class="fw-bold text-dark">{{ $b->budget_code }}</span></td>
                                        <td><span class="badge bg-light text-dark border px-2 py-1">{{ $b->division }}</span></td>
                                        <td><span class="fw-bold text-dark">{{ $b->department }}</span></td>
                                        <td class="text-end fw-medium text-secondary">₱{{ number_format($b->allocated_budget, 2) }}</td>
                                        <td class="text-end fw-bold text-danger">₱{{ number_format($b->actual_spend, 2) }}</td>
                                        <td class="text-end fw-bold text-success">₱{{ number_format($b->remaining_variance, 2) }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('admin-finance.budgeting.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this department budget?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm border-0" title="Delete Budget">
                                                    <i class="las la-trash-alt fs-18"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">No department annual budgets submitted yet. Click "Submit Annual Budget" above.</td>
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

    <!-- Submit Annual Budget Modal -->
    <div class="modal fade" id="submitBudgetModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-dark"><i class="las la-plus-circle me-1 text-danger"></i> Submit Department Annual Budget</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin-finance.budgeting.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Fiscal Year *</label>
                                <input type="number" name="fiscal_year" class="form-control" value="{{ date('Y') }}" required style="border-radius: 8px;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Division *</label>
                                <select name="division" class="form-select" required style="border-radius: 8px;">
                                    <option value="Production Division">Production Division</option>
                                    <option value="Sales & Marketing Division">Sales & Marketing Division</option>
                                    <option value="Admin & Finance Division">Admin & Finance Division</option>
                                    <option value="Executive Division">Executive Division</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Department Name *</label>
                            <input type="text" name="department" class="form-control" placeholder="e.g., Printing & Editorial Dept" required style="border-radius: 8px;">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Allocated Budget (₱) *</label>
                                <input type="number" step="0.01" name="allocated_budget" class="form-control" placeholder="0.00" required style="border-radius: 8px;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Initial Actual Spend (₱)</label>
                                <input type="number" step="0.01" name="actual_spend" class="form-control" placeholder="0.00" value="0.00" style="border-radius: 8px;">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Notes / Remarks</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Optional budget notes..." style="border-radius: 8px;"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4">
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-bdg-primary px-4">Save Budget</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

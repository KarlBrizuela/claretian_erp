<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .bdg-card {
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
                    <a href="{{ route('admin-finance.budgeting.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                        <i class="las la-arrow-left me-1"></i> Back to Budgeting Matrix
                    </a>
                    <h4 class="fs-24 fw-bold text-dark mb-0">{{ $budget->department }}</h4>
                    <p class="text-muted small mb-0">Code: <span class="font-monospace fw-bold text-dark">{{ $budget->budget_code }}</span> | Division: <span class="badge bg-light text-dark border">{{ $budget->division }}</span> | Fiscal Year: <span class="fw-bold text-dark">FY {{ $budget->fiscal_year }}</span></p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-danger btn-sm px-3 text-white rounded shadow-sm d-flex align-items-center gap-2" style="background-color: #D9251C; border-color: #D9251C; height: 40px;" data-bs-toggle="modal" data-bs-target="#addLineItemModal">
                        <i class="las la-plus-circle fs-18"></i> Add Line-Item Category
                    </button>
                    <button class="btn btn-outline-secondary btn-sm px-3 rounded shadow-sm d-flex align-items-center gap-2" style="height: 40px;" onclick="window.print()">
                        <i class="las la-print fs-18"></i> Print Budget Sheet
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left: Financial Summary & Forecast -->
            <div class="col-md-5 mb-4">
                <div class="bdg-card mb-4">
                    <h6 class="fw-bold text-uppercase text-muted small mb-3">Department Financial Performance</h6>
                    
                    <div class="p-3 rounded text-white mb-3" style="background-color: #D9251C;">
                        <span class="small text-white-50 d-block text-uppercase fw-bold">Annual Allocated Budget</span>
                        <h3 class="fw-bold mb-0">₱{{ number_format($budget->allocated_budget, 2) }}</h3>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="p-2 border rounded bg-light">
                                <span class="text-muted small d-block">Actual Spend</span>
                                <span class="fw-bold text-dark fs-16">₱{{ number_format($budget->actual_spend, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded bg-light">
                                <span class="text-muted small d-block">Variance (Remaining)</span>
                                <span class="fw-bold fs-16 {{ $budget->variance >= 0 ? 'text-success' : 'text-danger' }}">₱{{ number_format($budget->variance, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded bg-light">
                                <span class="text-muted small d-block">Percentage Used</span>
                                <span class="fw-bold text-dark fs-16">{{ $budget->percentage_used }}%</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded bg-light">
                                <span class="text-muted small d-block">Year-End Forecast</span>
                                <span class="fw-bold fs-16" style="color: #D9251C;">₱{{ number_format($budget->forecasted_spend, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h6 class="fw-bold text-dark small mb-3">Burn Rate Gauge</h6>
                    <div class="progress mb-2" style="height: 15px;">
                        <div class="progress-bar {{ $budget->percentage_used >= 90 ? 'bg-danger' : ($budget->percentage_used >= 75 ? 'bg-warning' : 'bg-success') }}" style="width: {{ min(100, $budget->percentage_used) }}%;"></div>
                    </div>
                    <span class="small text-muted d-block text-center">{{ $budget->percentage_used }}% of total department budget consumed</span>

                    @if($budget->notes)
                    <div class="mt-3 p-2 bg-light rounded border">
                        <span class="text-muted small fw-bold d-block">Justification Notes:</span>
                        <span class="small text-dark">{{ $budget->notes }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Right: Line-Item Expenditure Categories -->
            <div class="col-md-7 mb-4">
                <div class="bdg-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold text-dark mb-0">Line-Item Expenditure Categories</h5>
                            <p class="text-muted small mb-0">Category-level breakdown of allocated vs actual spending</p>
                        </div>
                        <span class="badge text-white px-3 py-2 fs-14" style="background-color: #D9251C;">
                            Categories: {{ $budget->lineItems->count() }}
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-dark text-white small text-uppercase">
                                <tr>
                                    <th>Expense Category</th>
                                    <th class="text-end">Allocated</th>
                                    <th class="text-end">Actual Spend</th>
                                    <th class="text-end">Variance</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($budget->lineItems as $item)
                                @php
                                    $var = $item->allocated_amount - $item->actual_amount;
                                @endphp
                                <tr>
                                    <td><span class="fw-bold text-dark fs-14">{{ $item->account_category }}</span></td>
                                    <td class="text-end fw-bold text-primary">₱{{ number_format($item->allocated_amount, 2) }}</td>
                                    <td class="text-end fw-bold text-dark">₱{{ number_format($item->actual_amount, 2) }}</td>
                                    <td class="text-end fw-bold {{ $var >= 0 ? 'text-success' : 'text-danger' }}">₱{{ number_format($var, 2) }}</td>
                                    <td><span class="text-muted small">{{ $item->notes ?: 'N/A' }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No line-item expense categories recorded yet. Click "Add Line-Item Category" above.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: ADD LINE-ITEM CATEGORY -->
    <div class="modal fade" id="addLineItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin-finance.budgeting.line-item.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="department_budget_id" value="{{ $budget->id }}">
                    <div class="modal-header text-white" style="background-color: #D9251C;">
                        <h5 class="modal-title fw-bold"><i class="las la-plus-circle me-2"></i>Add Line-Item Category</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Account Expense Category <span class="text-danger">*</span></label>
                            <input type="text" name="account_category" class="form-control" placeholder="e.g. Raw Materials Paper, Machine Repair & Parts, Staff Salaries, Electricity" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Allocated Category Budget (₱) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="allocated_amount" class="form-control" placeholder="500000.00" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Actual Realized Spend (₱) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="actual_amount" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Notes / Details</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Category specifications or notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn text-white px-4 fw-bold" style="background-color: #D9251C; border-color: #D9251C;">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

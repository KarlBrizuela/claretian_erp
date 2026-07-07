<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fs-22 mb-1 fw-bold text-dark" style="letter-spacing: -0.5px;">Expenses Management</h4>
                            <p class="text-muted small mb-0">Record, track, and filter accounting division and departmental expenses.</p>
                        </div>
                        <button class="btn btn-primary btn-sm px-4 py-2 d-flex align-items-center gap-2 shadow-sm" 
                                style="background: #ff0000; border: none; border-radius: 8px; font-weight: 600; font-size: 0.9rem;"
                                data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                            <i class="fas fa-plus"></i> Record Expense
                        </button>
                    </div>
                    
                    <div class="card-body">
                        <!-- Filters -->
                        <form action="{{ route('admin-finance.accounting.expenses.index') }}" method="GET" class="row mb-4 g-3 align-items-end">
                            <div class="col-md-5">
                                <label for="search" class="form-label fw-bold text-dark small"><i class="fas fa-search me-1 text-primary"></i> Search Expense</label>
                                <div class="input-group input-group-sm border rounded-pill px-3 py-1 bg-light" style="align-items: center;">
                                    <input type="text" name="search" class="form-control border-0 bg-transparent" 
                                           placeholder="Search by expense title..." value="{{ $search }}" style="outline: none; box-shadow: none;">
                                    @if($search)
                                        <a href="{{ route('admin-finance.accounting.expenses.index', ['dept_id' => $dept_id]) }}" class="btn btn-link text-muted p-0 me-2" title="Clear search">
                                            <i class="fas fa-times-circle"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="col-md-5">
                                <label for="dept_id" class="form-label fw-bold text-dark small"><i class="fas fa-building me-1 text-primary"></i> Filter by Department</label>
                                <select name="dept_id" id="dept_id" class="form-select form-select-sm border rounded-pill px-3 bg-light" style="height: 38px;">
                                    <option value="">-- All Departments --</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->dept_id }}" {{ $dept_id == $dept->dept_id ? 'selected' : '' }}>{{ $dept->dept_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-dark btn-sm rounded-pill px-4 flex-grow-1" style="font-weight: 600; height: 38px;">Filter</button>
                                @if($search || $dept_id)
                                    <a href="{{ route('admin-finance.accounting.expenses.index') }}" 
                                       class="btn btn-light btn-sm rounded-pill px-3 d-flex align-items-center justify-content-center" 
                                       style="border: 1px solid #ddd; height: 38px;" title="Reset filters">
                                        <i class="fas fa-undo"></i>
                                    </a>
                                @endif
                            </div>
                        </form>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-hover table-responsive-md align-middle" style="border-collapse: separate; border-spacing: 0 8px;">
                                <thead>
                                    <tr class="text-secondary" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #f0f0f0;">
                                        <th class="ps-4">Date</th>
                                        <th>Expense Title</th>
                                        <th>Department</th>
                                        <th>Amount</th>
                                        <th>Recorded By</th>
                                        <th>Remarks</th>
                                        <th class="text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($expenses as $expense)
                                    <tr class="bg-white shadow-sm hover-row" style="border-radius: 8px; transition: transform 0.2s, box-shadow 0.2s;">
                                        <td class="ps-4 py-3 text-dark font-w600 small">{{ \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d') }}</td>
                                        <td class="py-3 fw-bold text-dark fs-15">{{ $expense->title }}</td>
                                        <td class="py-3">
                                            @if($expense->department)
                                                <span class="badge badge-light text-dark border px-3 rounded-pill" style="font-weight: 600;">
                                                    {{ $expense->department->dept_name }}
                                                </span>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-danger font-w600">₱{{ number_format($expense->amount, 2) }}</td>
                                        <td class="py-3 text-muted small">{{ $expense->addedBy->name ?? 'N/A' }}</td>
                                        <td class="py-3 text-muted small" style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $expense->notes }}">{{ $expense->notes ?: '—' }}</td>
                                        <td class="text-end pe-4 py-3">
                                            <div class="d-flex justify-content-end gap-2 align-items-center">
                                                <button class="btn btn-warning btn-sm text-dark px-3 py-1.5 edit-btn shadow-sm"
                                                        style="border-radius: 6px; font-weight: 600; border: none; font-size: 0.82rem;"
                                                        data-bs-toggle="modal" data-bs-target="#editExpenseModal"
                                                        data-id="{{ $expense->id }}"
                                                        data-title="{{ $expense->title }}"
                                                        data-amount="{{ $expense->amount }}"
                                                        data-date="{{ $expense->expense_date }}"
                                                        data-dept="{{ $expense->department_id }}"
                                                        data-notes="{{ $expense->notes }}">
                                                    <i class="fas fa-edit me-1"></i> Edit
                                                </button>
                                                
                                                <button class="btn btn-outline-danger btn-sm px-3 py-1.5 shadow-sm"
                                                        style="border-radius: 6px; font-weight: 600; font-size: 0.82rem;"
                                                        onclick="confirmDelete({{ $expense->id }})">
                                                    <i class="fas fa-trash-alt me-1"></i> Delete
                                                </button>
                                                
                                                <form id="delete-form-{{ $expense->id }}" action="{{ route('admin-finance.accounting.expenses.destroy', $expense->id) }}" method="POST" class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <div class="mb-3"><i class="fas fa-file-invoice-dollar fs-40 text-light"></i></div>
                                            <span class="fs-15">No expense records found matching your filters.</span>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Links -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted small">
                                Showing {{ $expenses->firstItem() ?? 0 }} to {{ $expenses->lastItem() ?? 0 }} of {{ $expenses->total() }} expenses
                            </div>
                            <div>
                                {{ $expenses->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="addExpenseModalLabel">Record Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin-finance.accounting.expenses.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold text-dark small">Expense Title / Description <span class="text-danger">*</span></label>
                            <input type="text" class="form-control border-light-subtle rounded px-3 py-2" id="title" name="title" placeholder="e.g. Broadband Subscription" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="amount" class="form-label fw-bold text-dark small">Amount (₱) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" class="form-control border-light-subtle rounded px-3 py-2" id="amount" name="amount" placeholder="0.00" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="expense_date" class="form-label fw-bold text-dark small">Expense Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control border-light-subtle rounded px-3 py-2" id="expense_date" name="expense_date" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="department_id" class="form-label fw-bold text-dark small">Department <span class="text-danger">*</span></label>
                            <select class="form-select border-light-subtle rounded px-3 py-2" id="department_id" name="department_id" required>
                                <option value="">-- Select Department --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->dept_id }}">{{ $dept->dept_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label fw-bold text-dark small">Remarks / Notes</label>
                            <textarea class="form-control border-light-subtle rounded px-3 py-2" id="notes" name="notes" rows="3" placeholder="Additional details..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-3">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal" style="font-weight: 600;">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4" style="background: #ff0000; border: none; font-weight: 600;">Record Expense</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="editExpenseModalLabel">Edit Expense Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editExpenseForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="edit_title" class="form-label fw-bold text-dark small">Expense Title / Description <span class="text-danger">*</span></label>
                            <input type="text" class="form-control border-light-subtle rounded px-3 py-2" id="edit_title" name="title" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_amount" class="form-label fw-bold text-dark small">Amount (₱) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" class="form-control border-light-subtle rounded px-3 py-2" id="edit_amount" name="amount" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_expense_date" class="form-label fw-bold text-dark small">Expense Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control border-light-subtle rounded px-3 py-2" id="edit_expense_date" name="expense_date" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_department_id" class="form-label fw-bold text-dark small">Department <span class="text-danger">*</span></label>
                            <select class="form-select border-light-subtle rounded px-3 py-2" id="edit_department_id" name="department_id" required>
                                <option value="">-- Select Department --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->dept_id }}">{{ $dept->dept_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_notes" class="form-label fw-bold text-dark small">Remarks / Notes</label>
                            <textarea class="form-control border-light-subtle rounded px-3 py-2" id="edit_notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-3">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal" style="font-weight: 600;">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4" style="background: #ff0000; border: none; font-weight: 600;">Update Expense</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .hover-row:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.05) !important;
            cursor: default;
        }
        .btn-close {
            box-shadow: none !important;
        }
        .pagination {
            margin-bottom: 0;
        }
        .page-item.active .page-link {
            background-color: #ff0000 !important;
            border-color: #ff0000 !important;
            color: #ffffff !important;
        }
        .page-link {
            color: #ff0000;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Edit modal data population
            $('.edit-btn').on('click', function() {
                const id = $(this).data('id');
                const title = $(this).data('title');
                const amount = $(this).data('amount');
                const date = $(this).data('date');
                const dept = $(this).data('dept');
                const notes = $(this).data('notes');
                
                $('#edit_title').val(title);
                $('#edit_amount').val(amount);
                $('#edit_expense_date').val(date);
                $('#edit_department_id').val(dept);
                $('#edit_notes').val(notes);
                
                // Update form action URL dynamically
                const route = "{{ route('admin-finance.accounting.expenses.update', ':id') }}";
                $('#editExpenseForm').attr('action', route.replace(':id', id));
            });
        });

        // Use custom bespoke modal system from app-layout
        function confirmDelete(id) {
            window.showConfirm("Are you sure you want to delete this expense record?", function() {
                document.getElementById('delete-form-' + id).submit();
            });
        }
    </script>
    @endpush
</x-app-layout>

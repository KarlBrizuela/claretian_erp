<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <h4 class="fs-20 mb-0">Sales Invoice Management</h4>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <div class="row mb-4 align-items-end">
                            <div class="col-md-4 mb-2 mb-md-0">
                                <label for="siSearchInput" class="form-label fw-bold text-dark"><i class="fas fa-search me-1 text-primary"></i> Search</label>
                                <input type="text" id="siSearchInput" class="form-control form-control-sm" placeholder="Search by SO #, Customer, Type, Status...">
                            </div>
                            <div class="col-md-3 mb-2 mb-md-0">
                                <label for="siStartDate" class="form-label fw-bold text-dark"><i class="fas fa-calendar-alt me-1 text-primary"></i> Start Date</label>
                                <input type="date" id="siStartDate" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3 mb-2 mb-md-0">
                                <label for="siEndDate" class="form-label fw-bold text-dark"><i class="fas fa-calendar-alt me-1 text-primary"></i> End Date</label>
                                <input type="date" id="siEndDate" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-2">
                                <button id="clearFiltersBtn" class="btn btn-light btn-sm w-100" style="border: 1px solid #ddd; height: 36px;"><i class="fas fa-undo me-1"></i> Reset</button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-responsive-md">
                                <thead>
                                    <tr>
                                        <th>SO Number</th>
                                        <th>Customer</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>SI Prepared By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orders as $order)
                                    <tr class="si-row" data-date="{{ $order->created_at->format('Y-m-d') }}">
                                        <td><strong>#{{ $order->so_number }}</strong></td>
                                        <td>{{ $order->customer->customer_name ?? 'N/A' }}</td>
                                        <td><span class="badge badge-outline-dark">{{ ucfirst($order->type) }}</span></td>
                                        <td>₱{{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                            @php
                                                $statusClass = 'secondary';
                                                $displayStatus = str_replace('_', ' ', $order->status);
                                                
                                                if ($order->status === 'pending_si_prep') {
                                                    $statusClass = 'warning';
                                                    $displayStatus = 'Gathered (Pending SI Prep)';
                                                } elseif ($order->status === 'si_created') {
                                                    $statusClass = 'info';
                                                    $displayStatus = 'SI Created (Pending Signature)';
                                                } elseif ($order->status === 'pending_si_approval') {
                                                    $statusClass = 'info';
                                                    $displayStatus = 'SI Prepared (Pending Approval)';
                                                } elseif ($order->status === 'ready_for_delivery') {
                                                    $statusClass = 'success';
                                                }
                                            @endphp
                                            <span class="badge badge-{{ $statusClass }}">
                                                {{ ucwords($displayStatus) }}
                                            </span>
                                        </td>
                                        <td>{{ $order->siPreparedBy->name ?? 'N/A' }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($order->proof_of_payment)
                                                    <a href="{{ route('admin-finance.accounting.sales-invoice.prepare', $order->id) }}" class="btn btn-primary shadow btn-sm" title="View Detail"><i class="fas fa-eye"></i> View</a>
                                                @else
                                                    <button class="btn btn-primary shadow btn-sm" disabled title="Proof of Payment is required to view"><i class="fas fa-eye"></i> View</button>
                                                @endif
                                                
                                                @if($order->status === 'pending_si_prep')
                                                    @if($order->proof_of_payment)
                                                        <a href="{{ route('admin-finance.accounting.sales-invoice.prepare', $order->id) }}" class="btn btn-warning btn-sm">Prepare SI</a>
                                                    @else
                                                        <button class="btn btn-warning btn-sm" disabled title="Proof of Payment is required to prepare SI"><i class="fas fa-exclamation-triangle me-1"></i> Prepare SI</button>
                                                    @endif
                                                @endif

                                                @if($order->status === 'pending_si_approval')
                                                    @if($order->proof_of_payment)
                                                        <form action="{{ route('admin-finance.accounting.sales-invoice.sign', $order->id) }}" method="POST" class="m-0">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success btn-sm">Sign & Approve</button>
                                                        </form>
                                                    @else
                                                        <button class="btn btn-success btn-sm" disabled title="Proof of Payment is required to sign SI"><i class="fas fa-exclamation-triangle me-1"></i> Sign & Approve</button>
                                                    @endif
                                                @endif
                                                
                                                @if($order->status === 'ready_for_delivery')
                                                <a href="{{ route('admin-finance.accounting.sales-invoice.print', $order->id) }}" class="btn btn-info btn-sm" target="_blank">Print SI</a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No orders requiring Sales Invoice at this time.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Area Consignment Sales Invoices Section -->
                @if($areaConsignmentSIs->count() > 0)
                <div class="card mt-4">
                    <div class="card-header border-0 pb-0">
                        <h4 class="fs-20 mb-0">Area Consignment Sales Invoices</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-responsive-md">
                                <thead>
                                    <tr>
                                        <th>SI Number</th>
                                        <th>SO Number</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Created Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($areaConsignmentSIs as $si)
                                    <tr class="si-row" data-date="{{ $si->created_at->format('Y-m-d') }}">
                                        <td><strong>#{{ $si->si_number }}</strong></td>
                                        <td>#{{ $si->so_number }}</td>
                                        <td>{{ $si->customer_name ?? ($si->customer->customer_name ?? 'N/A') }}</td>
                                        <td>₱{{ number_format($si->total_amount, 2) }}</td>
                                        <td>
                                            @php
                                                $statusClass = 'secondary';
                                                $displayStatus = ucfirst($si->status);
                                                
                                                if ($si->status === 'draft') {
                                                    $statusClass = 'warning';
                                                } elseif ($si->status === 'pending_approval') {
                                                    $statusClass = 'info';
                                                } elseif ($si->status === 'approved') {
                                                    $statusClass = 'success';
                                                }
                                            @endphp
                                            <span class="badge badge-{{ $statusClass }}">{{ $displayStatus }}</span>
                                        </td>
                                        <td>{{ $si->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <a href="{{ route('admin-finance.accounting.sales-invoice.print', $si->so_id) }}" class="btn btn-info btn-sm" target="_blank">
                                                    <i class="fas fa-print"></i> Print
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('siSearchInput');
        const startDateInput = document.getElementById('siStartDate');
        const endDateInput = document.getElementById('siEndDate');
        const clearBtn = document.getElementById('clearFiltersBtn');

        function filterRows() {
            const query = searchInput.value.toLowerCase().trim();

            document.querySelectorAll('.si-row').forEach(row => {
                let matchesSearch = true;
                let matchesDate = true;

                // Search query match
                if (query) {
                    const text = row.innerText.toLowerCase();
                    matchesSearch = text.includes(query);
                }

                // Date range match (string-based YYYY-MM-DD comparison is timezone independent)
                const rowDateStr = row.getAttribute('data-date');
                if (rowDateStr) {
                    if (startDateInput.value && rowDateStr < startDateInput.value) {
                        matchesDate = false;
                    }
                    if (endDateInput.value && rowDateStr > endDateInput.value) {
                        matchesDate = false;
                    }
                }

                if (matchesSearch && matchesDate) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            // Check if there are no visible rows in either table, show placeholder if empty
            const tables = document.querySelectorAll('table');
            tables.forEach(table => {
                const tbody = table.querySelector('tbody');
                if (!tbody) return;
                const rows = tbody.querySelectorAll('.si-row');
                const visibleRows = Array.from(rows).filter(r => r.style.display !== 'none');
                
                let noResultRow = tbody.querySelector('.no-results-row');
                if (visibleRows.length === 0 && rows.length > 0) {
                    if (!noResultRow) {
                        noResultRow = document.createElement('tr');
                        noResultRow.className = 'no-results-row';
                        const colCount = table.querySelectorAll('thead th').length;
                        noResultRow.innerHTML = `<td colspan="${colCount}" class="text-center py-4 text-muted">No matching results found.</td>`;
                        tbody.appendChild(noResultRow);
                    }
                } else if (noResultRow) {
                    noResultRow.remove();
                }
            });
        }

        searchInput.addEventListener('input', filterRows);
        startDateInput.addEventListener('change', filterRows);
        endDateInput.addEventListener('change', filterRows);

        clearBtn.addEventListener('click', function () {
            searchInput.value = '';
            startDateInput.value = '';
            endDateInput.value = '';
            filterRows();
        });
    });
    </script>
</x-app-layout>

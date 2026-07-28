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

                        <!-- Bulk Actions Bar -->
                        <div id="bulkActionsBar" class="alert alert-light border d-none justify-content-between align-items-center mb-4 py-2 px-3 shadow-sm bg-white rounded" style="border-left: 4px solid #0d6efd !important;">
                            <div>
                                <span class="fw-bold text-dark"><span id="selectedCount" class="badge bg-primary fs-14">0</span> Sales Order(s) selected</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" id="bulkPrepareBtn" class="btn btn-warning btn-sm px-3 fw-bold">
                                    <i class="las la-file-invoice me-1"></i> Bulk Prepare & Submit SI
                                </button>
                                <button type="button" id="bulkFinalizeBtn" class="btn btn-primary btn-sm px-3 fw-bold">
                                    <i class="las la-check-double me-1"></i> Bulk Sign & Approve
                                </button>
                            </div>
                        </div>

                        <!-- Nav Tabs -->
                        <ul class="nav nav-tabs mb-4" id="siTabs" role="tablist" style="border-bottom: 2px solid #eee;">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-bold text-uppercase border-0 bg-transparent text-dark" id="normal-tab" data-bs-toggle="tab" data-bs-target="#normal-pane" type="button" role="tab" aria-controls="normal-pane" aria-selected="true" style="border-bottom: 3px solid #ff0000; padding: 10px 15px; transition: all 0.3s;">
                                    <i class="las la-file-invoice me-1 text-danger" style="font-size: 1.2rem;"></i> Normal Invoices ({{ $normalOrders->count() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold text-uppercase border-0 bg-transparent text-muted" id="ecom-tab" data-bs-toggle="tab" data-bs-target="#ecom-pane" type="button" role="tab" aria-controls="ecom-pane" aria-selected="false" style="padding: 10px 15px; transition: all 0.3s;">
                                    <i class="las la-store me-1 text-primary" style="font-size: 1.2rem;"></i> Direct Invoice (E-com) ({{ $ecomOrders->count() }})
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="siTabsContent">
                            <!-- Normal Invoices Tab Pane -->
                            <div class="tab-pane fade show active" id="normal-pane" role="tabpanel" aria-labelledby="normal-tab">
                                <div class="table-responsive">
                                    <table class="table table-responsive-md">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;">
                                                    <input type="checkbox" id="selectAllNormal" style="width: 16px; height: 16px; cursor: pointer;">
                                                </th>
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
                                            @forelse($normalOrders as $order)
                                            <tr class="si-row" data-date="{{ $order->created_at->format('Y-m-d') }}">
                                                <td>
                                                    @if($order->status === 'pending_si_prep' || $order->status === 'pending_si_approval')
                                                        <input type="checkbox" class="order-checkbox normal-check" value="{{ $order->id }}" data-proof="{{ $order->proof_of_payment ? 'yes' : 'no' }}" style="width: 16px; height: 16px; cursor: pointer;">
                                                    @else
                                                        <input type="checkbox" disabled style="width: 16px; height: 16px; opacity: 0.4;">
                                                    @endif
                                                </td>
                                                <td><strong>#{{ $order->so_number }}</strong></td>
                                                <td>{{ $order->customer->customer_name ?? 'N/A' }}</td>
                                                <td><span class="badge badge-outline-dark">{{ ucfirst(str_replace('_', ' ', $order->type)) }}</span></td>
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
                                                        <a href="{{ route('admin-finance.sales-order.detail', $order->id) }}" class="btn btn-primary shadow btn-sm" title="View SO Detail"><i class="fas fa-eye"></i> View</a>
                                                        
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
                                                <td colspan="8" class="text-center py-4 text-muted">No normal orders requiring Sales Invoice at this time.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- E-com Invoices Tab Pane -->
                            <div class="tab-pane fade" id="ecom-pane" role="tabpanel" aria-labelledby="ecom-tab">
                                <div class="table-responsive">
                                    <table class="table table-responsive-md">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;">
                                                    <input type="checkbox" id="selectAllEcom" style="width: 16px; height: 16px; cursor: pointer;">
                                                </th>
                                                <th>SO Number</th>
                                                <th>Platform</th>
                                                <th>Customer</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>SI Prepared By</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($ecomOrders as $order)
                                            <tr class="si-row" data-date="{{ $order->created_at->format('Y-m-d') }}">
                                                <td>
                                                    @if($order->status === 'pending_si_prep' || $order->status === 'pending_si_approval')
                                                        <input type="checkbox" class="order-checkbox ecom-check" value="{{ $order->id }}" data-proof="{{ $order->proof_of_payment ? 'yes' : 'no' }}" style="width: 16px; height: 16px; cursor: pointer;">
                                                    @else
                                                        <input type="checkbox" disabled style="width: 16px; height: 16px; opacity: 0.4;">
                                                    @endif
                                                </td>
                                                <td><strong>#{{ $order->so_number }}</strong></td>
                                                <td class="text-capitalize">
                                                    @if($order->ecom_platform === 'lazada')
                                                        <span class="badge bg-primary text-white"><i class="las la-shopping-bag me-1"></i> Lazada</span>
                                                    @elseif($order->ecom_platform === 'shopee')
                                                        <span class="badge bg-warning text-dark"><i class="las la-shopping-basket me-1"></i> Shopee</span>
                                                    @elseif($order->ecom_platform === 'tiktok')
                                                        <span class="badge bg-dark text-white"><i class="las la-music me-1"></i> TikTok</span>
                                                    @else
                                                        <span class="badge bg-secondary text-white">{{ $order->ecom_platform ?? 'E-commerce' }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $order->customer->customer_name ?? 'N/A' }}</td>
                                                <td class="fw-bold">₱{{ number_format($order->total_amount, 2) }}</td>
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
                                                        <a href="{{ route('admin-finance.sales-order.detail', $order->id) }}" class="btn btn-primary shadow btn-sm" title="View SO Detail"><i class="fas fa-eye"></i> View</a>
                                                        
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
                                                <td colspan="8" class="text-center py-4 text-muted">No E-com direct orders requiring Sales Invoice at this time.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Styling JS script -->
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const tabElList = [].slice.call(document.querySelectorAll('button[data-bs-toggle="tab"]'))
                                tabElList.forEach(function(tabEl) {
                                    tabEl.addEventListener('shown.bs.tab', function(event) {
                                        // Reset classes
                                        tabElList.forEach(el => {
                                            el.classList.remove('text-dark', 'active');
                                            el.classList.add('text-muted');
                                            el.style.borderBottom = '3px solid transparent';
                                        });
                                        // Set active classes
                                        event.target.classList.add('text-dark', 'active');
                                        event.target.classList.remove('text-muted');
                                        if (event.target.id === 'normal-tab') {
                                            event.target.style.borderBottom = '3px solid #ff0000';
                                        } else {
                                            event.target.style.borderBottom = '3px solid #0d6efd';
                                        }
                                    });
                                });
                            });
                        </script>
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

        // Checkbox variables & events
        const selectAllNormal = document.getElementById('selectAllNormal');
        const selectAllEcom = document.getElementById('selectAllEcom');
        const normalChecks = document.querySelectorAll('.normal-check');
        const ecomChecks = document.querySelectorAll('.ecom-check');
        const bulkActionsBar = document.getElementById('bulkActionsBar');
        const selectedCountEl = document.getElementById('selectedCount');
        const bulkFinalizeBtn = document.getElementById('bulkFinalizeBtn');

        function updateBulkBar() {
            const checkedCount = document.querySelectorAll('.order-checkbox:checked').length;
            selectedCountEl.textContent = checkedCount;
            if (checkedCount > 0) {
                bulkActionsBar.classList.remove('d-none');
                bulkActionsBar.classList.add('d-flex');
            } else {
                bulkActionsBar.classList.remove('d-flex');
                bulkActionsBar.classList.add('d-none');
            }
        }

        if (selectAllNormal) {
            selectAllNormal.addEventListener('change', function() {
                normalChecks.forEach(cb => {
                    if (!cb.disabled && cb.closest('tr').style.display !== 'none') {
                        cb.checked = selectAllNormal.checked;
                    }
                });
                updateBulkBar();
            });
        }

        if (selectAllEcom) {
            selectAllEcom.addEventListener('change', function() {
                ecomChecks.forEach(cb => {
                    if (!cb.disabled && cb.closest('tr').style.display !== 'none') {
                        cb.checked = selectAllEcom.checked;
                    }
                });
                updateBulkBar();
            });
        }

        document.querySelectorAll('.order-checkbox').forEach(cb => {
            cb.addEventListener('change', updateBulkBar);
        });

        const bulkPrepareBtn = document.getElementById('bulkPrepareBtn');

        function executeBulkProcess(actionType, buttonEl, btnOriginalHtml) {
            const selectedCheckboxes = document.querySelectorAll('.order-checkbox:checked');
            const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

            if (selectedIds.length === 0) {
                alert('Please select at least one sales order.');
                return;
            }

            // Double check if any selected orders are missing Proof of Payment
            let missingProofCount = 0;
            selectedCheckboxes.forEach(cb => {
                if (cb.getAttribute('data-proof') !== 'yes') {
                    missingProofCount++;
                }
            });

            const actionLabel = actionType === 'prepare' ? 'prepare & submit' : 'sign & approve';

            if (missingProofCount > 0) {
                if (!confirm(`Warning: ${missingProofCount} of the selected orders do NOT have a Proof of Payment attached. They will be skipped. Do you still want to proceed to ${actionLabel} the remaining ${selectedIds.length - missingProofCount} order(s)?`)) {
                    return;
                }
            } else if (!confirm(`Are you sure you want to ${actionLabel} the ${selectedIds.length} selected Sales Order(s)?`)) {
                return;
            }

            if (buttonEl) {
                buttonEl.disabled = true;
                buttonEl.innerHTML = '<i class="las la-spinner la-spin me-1"></i> Processing...';
            }

            fetch('{{ route("admin-finance.accounting.sales-invoice.bulk-finalize") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ids: selectedIds, action: actionType })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                    if (buttonEl) {
                        buttonEl.disabled = false;
                        buttonEl.innerHTML = btnOriginalHtml;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during bulk processing.');
                if (buttonEl) {
                    buttonEl.disabled = false;
                    buttonEl.innerHTML = btnOriginalHtml;
                }
            });
        }

        if (bulkPrepareBtn) {
            bulkPrepareBtn.addEventListener('click', function() {
                executeBulkProcess('prepare', bulkPrepareBtn, '<i class="las la-file-invoice me-1"></i> Bulk Prepare & Submit SI');
            });
        }

        if (bulkFinalizeBtn) {
            bulkFinalizeBtn.addEventListener('click', function() {
                executeBulkProcess('sign', bulkFinalizeBtn, '<i class="las la-check-double me-1"></i> Bulk Sign & Approve');
            });
        }
    });
    </script>
</x-app-layout>

<x-app-layout :title="'Delivery Receipts'" :sidebar="'production'">
    @push('styles')
    <style>
        .nav-tabs .nav-link {
            color: #333;
            border: none;
            border-bottom: 3px solid transparent;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-right: 1rem;
        }
        .nav-tabs .nav-link:hover {
            border-bottom-color: #ff0000;
        }
        .nav-tabs .nav-link.active {
            background: transparent;
            color: #ff0000;
            border-bottom-color: #ff0000;
        }

        .table-status-badge {
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-in-transit { background: #cce5ff; color: #004085; }

        .filter-section {
            display: flex;
            gap: 1rem;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            min-width: 200px;
        }

        .status-filter-dropdown {
            min-width: 180px;
        }
    </style>
    @endpush

    @php
        $user = auth()->user();
        $canPrep = $user && ($user->isSuperAdmin() || 
            str_contains($user->position, 'Manager') || 
            str_contains($user->position, 'Supervisor') || 
            str_contains($user->position, 'Head') || 
            str_contains($user->position, 'Senior Logistics Staff') || 
            str_contains($user->position, 'Logistics Staff'));
            
        $canApprove = $user && ($user->isSuperAdmin() || 
            str_contains($user->position, 'Manager') || 
            str_contains($user->position, 'Supervisor') || 
            str_contains($user->position, 'Head') || 
            str_contains($user->position, 'Senior Logistics Staff'));
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="card">
                <ul class="nav nav-tabs border-bottom px-4 pt-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-pane" type="button" role="tab" aria-controls="pending-pane" aria-selected="true">
                            <i class="fas fa-hourglass-half me-2"></i>Pending DR Prep ({{ count($orders) }})
                        </button>
                    </li>
                </ul>

                <div class="card-header border-0 d-block d-sm-flex px-4 pt-3 pb-0">
                    <div>
                        <h4 class="fs-20 mb-0 text-black">Delivery Receipts Management</h4>
                    </div>
                    @if($canPrep)
                    <a href="{{ route('production.logistic.delivery-receipt') }}" class="btn btn-primary rounded d-flex align-items-center ms-auto" style="gap: 0.5rem; background: #ff0000; border: none;">
                        <i class="las la-plus"></i>
                        <span>Create New Receipt</span>
                    </a>
                    @endif
                </div>

                <div class="tab-content p-4">
                    <!-- Pending DR Prep Tab -->
                    <div class="tab-pane fade show active" id="pending-pane" role="tabpanel" aria-labelledby="pending-tab">
                        <!-- Filter Section -->
                        <div class="filter-section">
                            <div class="search-box">
                                <input type="text" id="searchInput" class="form-control" placeholder="Search by SO # or Customer...">
                            </div>
                            <!-- Customer Filter -->
                            <select id="customerFilter" class="form-control status-filter-dropdown" style="min-width: 220px;">
                                <option value="all">All Customers</option>
                                @php
                                    $uniqueCustomers = $orders->map(function($order) {
                                        return $order->customer;
                                    })->filter()->unique('customer_id')->sortBy(function($c) {
                                        return $c->customer_name ?? $c->company_name ?? '';
                                    });
                                @endphp
                                @foreach($uniqueCustomers as $c)
                                    <option value="{{ $c->customer_id }}">{{ $c->customer_name ?? $c->company_name ?? 'Unknown' }}</option>
                                @endforeach
                            </select>
                            <select id="statusFilter" class="form-control status-filter-dropdown">
                                <option value="all">All Status</option>
                                <option value="pending_dr_prep">Pending Prep</option>
                                <option value="pending_dr_approval">Pending Approval</option>
                                <option value="ready_for_delivery">Ready for Delivery</option>
                                <option value="si_created">Closed</option>
                                <option value="reconsignment_pending">Reconsignment Pending</option>
                                <option value="overdue">Overdue</option>
                            </select>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="drTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>SO Number</th>
                                        <th>Customer</th>
                                        <th>Total Amount</th>
                                        <th>Payment Terms</th>
                                        <th>Remaining Date</th>
                                        <th>Status</th>
                                        <th>Prepared By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orders as $order)
                                    @php
                                        // Handle terms stored as '90 days', '30 days', etc.
                                        $termsMap = [
                                            'cash' => 0, 
                                            'cod' => 0, 
                                            '7_days' => 7, 
                                            '7 days' => 7,
                                            '7days' => 7,
                                            '15_days' => 15, 
                                            '15 days' => 15,
                                            '15days' => 15,
                                            '30_days' => 30, 
                                            '30 days' => 30,
                                            '30days' => 30,
                                            '60_days' => 60, 
                                            '60 days' => 60,
                                            '60days' => 60,
                                            '90_days' => 90, 
                                            '90 days' => 90,
                                            '90days' => 90,
                                            '90' => 90,
                                            '30' => 30,
                                            '7' => 7,
                                            '15' => 15,
                                            '60' => 60
                                        ];
                                        
                                        $termValue = strtolower(trim($order->terms ?? ''));
                                        $daysFromTerms = $termsMap[$termValue] ?? 0;
                                        
                                        // Only show calculation if payment terms are set (not cash/cod)
                                        if ($daysFromTerms > 0) {
                                            // Get the reference date
                                            $baseDateTime = $order->dr_prepared_at ?? $order->created_at;
                                            $baseDate = \Carbon\Carbon::parse($baseDateTime);
                                            
                                            // Add days to get due date
                                            $dueDate = $baseDate->copy()->addDays($daysFromTerms);
                                            
                                            // Get today at start of day
                                            $today = \Carbon\Carbon::today();
                                            
                                            // Calculate remaining days
                                            $interval = $today->diff($dueDate);
                                            $daysRemaining = (int)$interval->format('%r%a');
                                        } else {
                                            $daysRemaining = null;
                                            $dueDate = null;
                                        }

                                        $termsDisplay = match($order->terms) {
                                            'cash' => 'Cash',
                                            'cod' => 'COD',
                                            '7_days' => '7 Days',
                                            '15_days' => '15 Days',
                                            '30_days' => '30 Days',
                                            '60_days' => '60 Days',
                                            '90_days' => '90 Days',
                                            default => $order->terms
                                        };
                                    @endphp
                                    <tr data-so-number="{{ $order->so_number }}" data-customer="{{ $order->customer->customer_name ?? '' }}" data-customer-id="{{ $order->customer_id ?? '' }}" data-status="{{ $order->status }}" data-days-remaining="{{ $daysRemaining !== null ? $daysRemaining : '' }}">
                                        <td><strong>{{ $order->so_number }}</strong></td>
                                        <td>{{ $order->customer->customer_name ?? 'Unknown' }}</td>
                                        <td>₱{{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $termsDisplay }}</span>
                                        </td>
                                        <td>
                                            @if($daysRemaining !== null)
                                                <span class="@if($daysRemaining < 0) text-danger fw-bold @elseif($daysRemaining < 7) text-warning @else text-success @endif">
                                                    {{ $dueDate->format('M d, Y') }}
                                                    <br><small>{{ $daysRemaining < 0 ? abs($daysRemaining) . ' days overdue' : $daysRemaining . ' days' }}</small>
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($order->status === 'pending_dr_prep')
                                                <span class="table-status-badge status-pending">Pending Prep</span>
                                            @elseif($order->status === 'pending_dr_approval')
                                                <span class="table-status-badge status-in-transit">Pending Approval</span>
                                            @elseif($order->status === 'ready_for_delivery')
                                                <span class="table-status-badge status-completed">Ready for Delivery</span>
                                            @elseif($order->status === 'si_created')
                                                <span class="table-status-badge bg-secondary text-white">Closed</span>
                                            @elseif($order->status === 'reconsignment_pending')
                                                <span class="table-status-badge bg-warning text-dark text-nowrap">Reconsignment Pending</span>
                                            @endif
                                        </td>
                                        <td>{{ $order->preparedBy->name ?? 'System' }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('production.logistic.delivery-receipt', $order->id) }}" class="btn btn-primary shadow btn-xs sharp" title="View/Create DR">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                @if($order->status === 'pending_dr_prep' && $canPrep)
                                                    <form action="{{ route('production.logistic.mark-as-dr-prepared', $order->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-warning shadow btn-xs sharp" title="Mark as DR Prepared">
                                                            <i class="fas fa-file-invoice"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                @if($order->status === 'pending_dr_approval' && $canApprove)
                                                    <form action="{{ route('production.logistic.approve-dr', $order->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success shadow btn-xs sharp" title="Approve & Sign DR">
                                                            <i class="fas fa-signature"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">No pending DR preparations.</td>
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const customerFilter = document.getElementById('customerFilter');
            const statusFilter = document.getElementById('statusFilter');
            const tableRows = document.querySelectorAll('#drTable tbody tr');

            // Search functionality
            searchInput.addEventListener('keyup', function() {
                filterTable();
            });

            // Customer filter functionality
            customerFilter.addEventListener('change', function() {
                filterTable();
            });

            // Status filter functionality
            statusFilter.addEventListener('change', function() {
                filterTable();
            });

            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const currentCustomerId = customerFilter.value;
                const currentStatusFilter = statusFilter.value;

                tableRows.forEach(row => {
                    const soNumber = row.dataset.soNumber.toLowerCase();
                    const customer = row.dataset.customer.toLowerCase();
                    const customerId = row.dataset.customerId;
                    const status = row.dataset.status;
                    const daysRemaining = row.dataset.daysRemaining;

                    // Check search term match
                    const searchMatch = soNumber.includes(searchTerm) || customer.includes(searchTerm);

                    // Check customer match
                    const customerMatch = currentCustomerId === 'all' || customerId === currentCustomerId;

                    // Check status match
                    let statusMatch = false;
                    if (currentStatusFilter === 'all') {
                        statusMatch = true;
                    } else if (currentStatusFilter === 'overdue') {
                        statusMatch = daysRemaining !== '' && parseInt(daysRemaining) < 0;
                    } else {
                        statusMatch = status === currentStatusFilter;
                    }

                    // Show row if all conditions match
                    if (searchMatch && customerMatch && statusMatch) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Check if there are any visible rows
                const visibleRows = Array.from(tableRows).filter(row => row.style.display !== 'none');
                const tbody = document.querySelector('#drTable tbody');
                const emptyMessage = tbody.querySelector('.empty-message');

                if (visibleRows.length === 0) {
                    if (!emptyMessage) {
                        const newRow = document.createElement('tr');
                        newRow.className = 'empty-message';
                        newRow.innerHTML = '<td colspan="8" class="text-center text-muted py-4">No records found.</td>';
                        tbody.appendChild(newRow);
                    }
                } else {
                    if (emptyMessage) {
                        emptyMessage.remove();
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>

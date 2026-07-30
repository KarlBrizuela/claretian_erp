<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }
        .status-draft { background-color: #e9ecef; color: #495057; }
        .status-pending_mkt_approval { background-color: #fff3cd; color: #856404; }
        .status-pending_prod_approval { background-color: #e0f2ff; color: #004085; }
        .status-picking { background-color: #d1ecf1; color: #0c5460; }
        .status-pending_si_prep { background-color: #cce5ff; color: #004085; }
        .status-pending_si_approval { background-color: #e2d9ff; color: #4b0082; }
        .status-pending_dr_prep { background-color: #d1f7f1; color: #006b5f; }
        .status-pending_dr_approval { background-color: #f3e5f5; color: #7b1fa2; }
        .status-ready_for_delivery { background-color: #d4edda; color: #155724; }
        .status-completed { background-color: #c3e6cb; color: #155724; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
        .status-gathered { background-color: #d1ecf1; color: #0c5460; }
        .status-pending_acct_approval { background-color: #fff3cd; color: #856404; }
        .workflow-actions { display: flex; flex-wrap: wrap; gap: 4px; }
        .workflow-actions .btn { padding: 4px 8px; font-size: 11px; }
        
        /* Draft status variants */
        .status-badge:has-text("Draft (Pending Freight)") {
            background-color: #e9ecef !important;
            color: #721c24 !important;
        }
        .status-badge:has-text("Draft (Freight Approved)") {
            background-color: #d4edda !important;
            color: #155724 !important;
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0 d-block d-sm-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="fs-20 mb-0 text-black">Sales Orders</h4>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2 mt-3 mt-sm-0">
                        <form method="GET" action="{{ route('marketing.sales-orders.list') }}" class="d-flex align-items-center">
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" class="form-control rounded-start" placeholder="Search orders..." value="{{ request('search') }}" style="height: 40px;">
                                <button type="submit" class="btn btn-primary" style="background: #ff0000; border-color: #ff0000; height: 40px;">
                                    <i class="las la-search"></i>
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('marketing.sales-orders.list') }}" class="btn btn-light d-flex align-items-center justify-content-center" style="height: 40px; border: 1px solid #dee2e6;">
                                        <i class="las la-times"></i>
                                    </a>
                                @endif
                            </div>
                        </form>
                        <a href="{{ route('marketing.sales-orders.create') }}" class="btn btn-primary rounded d-flex align-items-center" style="background: #ff0000; color: #ffffff; height: 40px; padding: 0 1.5rem;">
                            <i class="las la-plus me-2"></i>
                            <span>Create New Order</span>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="salesOrdersTable" class="display" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>Order Number</th>
                                    <th>Customer</th>
                                    <th>Order Date</th>
                                    <th>Platform/Source</th>
                                    <th>Total Amount</th>
                                    <th>Total Qty</th>
                                    <th>Items Picked</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td><strong>{{ $order->so_number }}</strong></td>
                                    <td>{{ $order->customer->customer_name ?? 'Unknown Customer' }}</td>
                                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                    @php
                                        $typeDisplay = str_replace('_', ' ', $order->type);
                                        if ($order->type == 'calculator_pos') $typeDisplay = 'direct POS';
                                        if ($order->type == 'ecom_direct') $typeDisplay = 'ECOM POS';
                                    @endphp
                                    <td class="text-uppercase {{ $order->type === 'paid' ? 'text-success' : 'text-primary' }}">{{ $typeDisplay }}</td>
                                    <td>₱{{ number_format($order->total_amount, 2) }}</td>
                                    <td><span class="badge bg-light text-dark fw-bold" title="{{ $order->items->count() }} line item(s)">{{ $order->items->sum('quantity') }} pcs</span></td>
                                    <td>
                                        @php
                                            $totalItems = $order->items->count();
                                            $pickedActivity = $order->activities()
                                                ->where('action', 'Pick list items saved')
                                                ->latest()
                                                ->first();
                                            $pickedCount = $pickedActivity ? count(json_decode($pickedActivity->details, true)) : 0;
                                        @endphp
                                        {{-- Only show items picked for non-PAID orders that are in picking/delivery phases --}}
                                        @if($order->type !== 'paid' && ($order->status == 'picking' || $order->status == 'ready_for_delivery' || $order->status == 'completed'))
                                            <span class="badge bg-info">{{ $pickedCount }}/{{ $totalItems }} picked</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="status-badge status-{{ $order->status }}">
                                            @php
                                                $displayStatus = str_replace('_', ' ', $order->status);
                                                if ($order->status == 'draft') {
                                                    if ($order->freight_charges && $order->freight_charges > 0) {
                                                        $displayStatus = 'Draft (Freight Approved)';
                                                    } else {
                                                        $displayStatus = 'Draft (Pending Freight)';
                                                    }
                                                }
                                                if ($order->status == 'pending_si_prep') $displayStatus = 'Gathered (In SI Prep)';
                                                if ($order->status == 'si_created') $displayStatus = 'SI Created';
                                                if ($order->status == 'pending_dr_prep') $displayStatus = 'SI Signed (In DR Prep)';
                                                if ($order->status == 'pending_mkt_approval') $displayStatus = 'Pending Marketing Approval';
                                                if ($order->status == 'pending_prod_approval') $displayStatus = 'Pending Production Approval';
                                            @endphp
                                            {{ ucwords($displayStatus) }}
                                        </span>
                                    </td>
                                     <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('marketing.sales-orders.detail', $order->id) }}" class="btn btn-primary shadow btn-xs sharp" title="View Order"><i class="fas fa-eye"></i></a>
                                            
                                            @if(in_array($order->type, ['calculator_pos', 'ecom_direct']))
                                                {{-- POS orders get Whole/Half print options --}}
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-info shadow btn-xs sharp dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Print Options">
                                                        <i class="fas fa-print"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><a class="dropdown-item" href="{{ route('marketing.sales-orders.print-invoice', $order->id) }}" target="_blank"><i class="fas fa-file-alt me-2"></i>Whole Page</a></li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item" href="{{ route('marketing.sales-orders.print-invoice', [$order->id, 'format' => 'half', 'half' => '1']) }}" target="_blank"><i class="fas fa-chevron-up me-2"></i>First Half (Part 1)</a></li>
                                                        <li><a class="dropdown-item" href="{{ route('marketing.sales-orders.print-invoice', [$order->id, 'format' => 'half', 'half' => '2']) }}" target="_blank"><i class="fas fa-chevron-down me-2"></i>Second Half (Part 2)</a></li>
                                                    </ul>
                                                </div>
                                            @else
                                                <a href="{{ route('marketing.sales-orders.print-invoice', $order->id) }}" target="_blank" class="btn btn-info shadow btn-xs sharp" title="Print Sales Invoice Form"><i class="fas fa-print"></i></a>
                                            @endif
                                            
                                            <!-- Edit Button -->
                                            @if($order->status == 'draft' || $order->status == 'mkt_approved')
                                                <a href="{{ route('marketing.sales-orders.edit', $order->id) }}" class="btn btn-warning shadow btn-xs sharp" title="Edit Order"><i class="fas fa-pencil-alt"></i></a>
                                                
                                                <!-- Delete Button -->
                                                <button type="button" class="btn btn-danger shadow btn-xs sharp" title="Delete Order" 
                                                    onclick="confirmDelete('{{ $order->id }}', '{{ $order->so_number }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <form id="delete-form-{{ $order->id }}" action="{{ route('marketing.sales-orders.destroy', $order->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            @endif

                                            <!-- Proceed to Final SO Button (for draft with freight charges) -->
                                            @if($order->status == 'draft' && $order->freight_charges && $order->freight_charges > 0)
                                                <form action="{{ route('marketing.sales-orders.proceed-to-final', $order->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success shadow btn-xs sharp" title="Proceed to Final SO with Freight (₱{{ number_format($order->freight_charges, 2) }})">
                                                        <i class="fas fa-arrow-right"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <!-- Export Excel Button — Area Sales Consignment only -->
                                            @if($order->type === 'area_sales_consignment')
                                                <a href="{{ route('marketing.sales-orders.export-single', $order->id) }}" class="btn btn-success shadow btn-xs sharp" title="Export to Excel">
                                                    <i class="fas fa-file-excel"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">No sales orders found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#salesOrdersTable').DataTable({
                order: [[2, 'desc']],
                paging: false, // Disable client-side paging since we use Laravel server-side pagination
                responsive: true,
                searching: false, // Remove search bar
                lengthChange: false, // Remove "Show entries"
                bInfo: false // Remove "Showing 1 to N of X entries" if desired (optional, but cleaner)
            });
        });

        function confirmDelete(id, soNumber) {
            if (typeof showAppModal === 'function') {
                showAppModal('Confirm Deletion', 'Are you sure you want to delete Sales Order <strong>' + soNumber + '</strong>?', {
                    type: 'confirm',
                    confirmText: 'Yes, Delete',
                    cancelText: 'Cancel',
                    onConfirm: function() {
                        document.getElementById('delete-form-' + id).submit();
                    }
                });
            } else {
                // Fallback
                if (confirm('Are you sure you want to delete Sales Order ' + soNumber + '?')) {
                    document.getElementById('delete-form-' + id).submit();
                }
            }
        }
    </script>
    @endpush
</x-app-layout>

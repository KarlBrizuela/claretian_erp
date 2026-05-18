<x-app-layout :title="'Delivery Receipts'" :sidebar="'production'">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0 d-block d-sm-flex">
                    <div>
                        <h4 class="fs-24 mb-0 text-black">Delivery Receipts</h4>
                    </div>
                    <a href="{{ route('production.logistic.delivery-receipt') }}" class="btn btn-primary rounded d-flex align-items-center" style="gap: 0.5rem; padding: 0.5rem 1rem; height: 38px; min-height: 38px; line-height: 1.5; box-sizing: border-box; border: none; background: #ff0000; color: #ffffff; font-weight: 500;">
                        <i class="las la-plus" style="font-size: 1rem; line-height: 1; margin: 0; padding: 0; background: transparent; border: none; box-shadow: none;"></i>
                        <span style="font-size: 0.875rem; white-space: nowrap;">Create New Receipt</span>
                    </a>
                </div>
                <div class="card-body">
                    <div class="dataTables_wrapper">
                        <div class="table-responsive">
                            <table id="deliveryReceiptsTable" class="display" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>DR Number</th>
                                        <th>Sales Order</th>
                                        <th>Customer</th>
                                        <th>Delivery Date</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                        <th>Prepared By</th>
                                        <th>Received By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orders as $order)
                                    <tr>
                                        <td><strong>DR-{{ $order->so_number }}</strong></td>
                                        <td>{{ $order->so_number }}</td>
                                        <td>{{ $order->customer->customer_name ?? 'Unknown' }}</td>
                                        <td>{{ $order->dr_prepared_at ? \Carbon\Carbon::parse($order->dr_prepared_at)->format('Y-m-d') : 'Pending' }}</td>
                                        <td>₱{{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                            @if($order->status === 'pending_dr_prep')
                                                <span class="status-badge status-pending">Pending Prep</span>
                                            @elseif($order->status === 'pending_dr_approval')
                                                <span class="status-badge status-in-transit">Pending Approval</span>
                                            @elseif($order->status === 'ready_for_delivery')
                                                <span class="status-badge status-delivered">Ready for Delivery</span>
                                            @else
                                                <span class="status-badge status-draft">{{ $order->status }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $order->preparedBy->name ?? 'System' }}</td>
                                        <td>-</td>
                                        <td>
                                            <div class="workflow-actions">
                                                <a href="{{ route('marketing.sales-orders.detail', $order->id) }}" class="btn btn-primary shadow btn-xs sharp" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                @if($order->status === 'pending_dr_prep')
                                                        <button type="submit" class="btn btn-warning shadow btn-xs sharp" title="Mark as DR Prepared">
                                                            <i class="fas fa-file-invoice"></i>
                                                        </button>
                                                    </form>
                                                    @endif

                                                    @if($order->status === 'pending_dr_approval')
                                                    <form action="{{ route('production.logistic.approve-dr', $order->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success shadow btn-xs sharp" title="Approve & Sign DR">
                                                            <i class="fas fa-signature"></i>
                                                        </button>
                                                    </form>
                                                    @endif

                                                    <a href="javascript:void(0);" class="btn btn-info shadow btn-xs sharp" title="Print DR" onclick="window.print()">
                                                        <i class="las la-print"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="9" class="text-center">No delivery receipts found.</td>
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
    </div>

    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <style>
        .dataTables_wrapper {
            font-size: 14px;
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }
        .status-draft {
            background-color: #e9ecef;
            color: #495057;
        }
        .status-in-progress {
            background-color: #cce5ff;
            color: #004085;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-delivered {
            background-color: #d4edda;
            color: #155724;
        }
        .status-in-transit {
            background-color: #cce5ff;
            color: #004085;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .workflow-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#deliveryReceiptsTable').DataTable({
                order: [[3, 'desc']], // Sort by date descending
                pageLength: 25,
                responsive: true
            });
        });
    </script>
    @endpush
</x-app-layout>

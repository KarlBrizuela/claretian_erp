<x-app-layout :title="'Pick Lists'" :sidebar="'production'">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0 d-block d-sm-flex">
                    <div>
                        <h4 class="fs-24 mb-0 text-black">Pick Lists</h4>
                    </div>
                    <a href="{{ route('production.logistic.pick-list-management') }}" class="btn btn-primary rounded d-flex align-items-center" style="gap: 0.5rem; padding: 0.5rem 1rem; height: 38px; min-height: 38px; line-height: 1.5; box-sizing: border-box; border: none; background: #ff0000; color: #ffffff; font-weight: 500;">
                        <i class="las la-plus" style="font-size: 1rem; line-height: 1; margin: 0; padding: 0; background: transparent; border: none; box-shadow: none;"></i>
                        <span style="font-size: 0.875rem; white-space: nowrap;">Create New Pick List</span>
                    </a>
                </div>
                <div class="card-body">
                    <div class="dataTables_wrapper">
                        <div class="table-responsive">
                            <table id="pickListsTable" class="display" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>Pick List Number</th>
                                        <th>Sales Order</th>
                                        <th>Customer</th>
                                        <th>Date Created</th>
                                        <th>Total Items</th>
                                        <th>Items Picked</th>
                                        <th>Status</th>
                                        <th>Prepared By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orders as $order)
                                    <tr>
                                        <td><strong>PL-{{ $order->so_number }}</strong></td>
                                        <td>{{ $order->so_number }}</td>
                                        <td>{{ $order->customer->customer_name ?? 'Unknown' }}</td>
                                        <td>{{ $order->acct_approved_at ? \Carbon\Carbon::parse($order->acct_approved_at)->format('Y-m-d') : $order->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $order->items->sum('quantity') }}</td>
                                        <td>-</td>
                                        <td><span class="status-badge status-in-progress">Picking</span></td>
                                        <td>{{ $order->preparedBy->name ?? 'System' }}</td>
                                        <td>
                                            <div class="workflow-actions">
                                                <a href="{{ route('marketing.sales-orders.detail', $order->id) }}" class="btn btn-primary shadow btn-xs sharp" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <form action="{{ route('production.logistic.mark-as-gathered', $order->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success shadow btn-xs sharp" title="Mark as Gathered">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <a href="javascript:void(0);" class="btn btn-info shadow btn-xs sharp" title="Print Pick List" onclick="window.print()">
                                                    <i class="las la-print"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No orders currently in picking status.</td>
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
        .status-short {
            background-color: #f8d7da;
            color: #721c24;
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
            $('#pickListsTable').DataTable({
                order: [[3, 'desc']], // Sort by date descending
                pageLength: 25,
                responsive: true
            });
        });
    </script>
    @endpush
</x-app-layout>

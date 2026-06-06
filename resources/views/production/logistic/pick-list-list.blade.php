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
                                @forelse($pickLists as $pickList)
                                <tr>
                                    <td><strong>{{ $pickList->pick_list_number }}</strong></td>
                                    <td>{{ $pickList->salesOrder->so_number ?? 'N/A' }}</td>
                                    <td>{{ $pickList->salesOrder->customer->customer_name ?? 'Unknown' }}</td>
                                    <td>{{ $pickList->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $pickList->pickListItems->sum('requested_qty') }}</td>
                                    <td>{{ $pickList->pickListItems->sum('picked_qty') }}</td>
                                    <td>
                                        @if($pickList->status === 'draft')
                                            <span class="badge bg-secondary">Draft</span>
                                        @elseif($pickList->status === 'in_progress')
                                            <span class="badge" style="background-color: #0dcaf0;">Picking</span>
                                        @elseif($pickList->status === 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @endif
                                    </td>
                                    <td>{{ $pickList->preparedByUser->name ?? 'System' }}</td>
                                    <td>
                                        <div class="workflow-actions">
                                            <a href="{{ route('production.logistic.pick-list-details', $pickList->id) }}" class="btn btn-danger shadow btn-xs sharp me-1" title="View Details">
                                                <i class="las la-eye"></i>
                                            </a>
                                            <form action="{{ route('production.logistic.mark-as-gathered', $pickList->salesOrder->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Mark as gathered?');">
                                                @csrf
                                                <button type="submit" class="btn btn-success shadow btn-xs sharp me-1" title="Mark as Gathered">
                                                    <i class="las la-check"></i>
                                                </button>
                                            </form>
                                            <a href="javascript:void(0);" class="btn btn-info shadow btn-xs sharp" title="Print" onclick="window.print();">
                                                <i class="las la-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">No active pick lists.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
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
            font-size: 13px;
        }
        
        #pickListsTable {
            font-size: 13px;
        }
        
        #pickListsTable thead th {
            padding: 8px 10px;
            font-weight: 600;
            font-size: 13px;
        }
        
        #pickListsTable tbody td {
            padding: 6px 10px;
            vertical-align: middle;
        }
        
        .workflow-actions {
            display: flex;
            gap: 3px;
            align-items: center;
        }
        
        .workflow-actions .btn {
            padding: 2px 4px !important;
            font-size: 10px !important;
            min-width: 24px !important;
            width: 24px !important;
            height: 24px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        
        .workflow-actions .btn i {
            margin: 0 !important;
            font-size: 12px !important;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        console.log('Pick Lists page loaded');
        
        $(document).ready(function() {
            console.log('Document ready - initializing DataTable');
            
            try {
                const table = $('#pickListsTable').DataTable({
                    order: [[3, 'desc']],
                    pageLength: 25,
                    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
                });
                console.log('DataTable initialized successfully:', table);
                console.log('Total rows:', table.rows().count());
            } catch (error) {
                console.error('Error initializing DataTable:', error);
                console.error('Error message:', error.message);
                console.error('Error stack:', error.stack);
            }
            
            // Log all pick list data
            console.log('Pick list data:', {!! json_encode($pickLists ?? []) !!});
        });
    </script>
    @endpush
</x-app-layout>

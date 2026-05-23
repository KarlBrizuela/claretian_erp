<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <h4 class="fs-20 mb-0">Sales Invoice Management</h4>
                    </div>
                    <div class="card-body">
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
                                    <tr>
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
                                                <a href="{{ route('admin-finance.accounting.sales-invoice.prepare', $order->id) }}" class="btn btn-primary shadow btn-sm" title="View Detail"><i class="fas fa-eye"></i> View</a>
                                                
                                                @if($order->status === 'pending_si_prep')
                                                <a href="{{ route('admin-finance.accounting.sales-invoice.prepare', $order->id) }}" class="btn btn-warning btn-sm">Prepare SI</a>
                                                @endif

                                                @if($order->status === 'pending_si_approval')
                                                <form action="{{ route('admin-finance.accounting.sales-invoice.sign', $order->id) }}" method="POST" class="m-0">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm">Sign & Approve</button>
                                                </form>
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
            </div>
        </div>
    </div>
</x-app-layout>

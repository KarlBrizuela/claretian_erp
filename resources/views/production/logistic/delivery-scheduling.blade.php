<x-app-layout :title="'Delivery Scheduling'" :sidebar="'production'">
@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
            white-space: nowrap;
            text-transform: uppercase;
        }
        .status-ready { background-color: #e0f2ff; color: #004085; }
        .status-cod { background-color: #fff3cd; color: #856404; }
        .status-charge { background-color: #d4edda; color: #155724; }
        .status-paid { background-color: #d1e7dd; color: #0f5132; }
        
        .scheduling-table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            font-size: 0.75rem;
            color: #495057;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
        }

        @media print {
            .btn, .dataTables_filter, .dataTables_length, .dataTables_info, .dataTables_paginate, .sidebar, .nav-header { display: none !important; }
            .card { border: none !important; box-shadow: none !important; }
            .card-header { display: none !important; }
            table { width: 100% !important; border-collapse: collapse !important; }
            th, td { border: 1px solid #ddd !important; padding: 8px !important; color: #000 !important; font-size: 10pt !important; }
            body { background: white !important; padding: 20px; }
        }
    </style>
    @endpush

    <div class="container-fluid">
        <!-- Scheduling Header -->
        <div class="row mb-4 align-items-center">
            <div class="col-sm-6">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <div class="p-3 bg-secondary-light rounded-circle text-secondary">
                            <i class="las la-calendar-check fs-30"></i>
                        </div>
                    </div>
                    <div>
                        <h2 class="font-w600 mb-0">Delivery Scheduling</h2>
                        <p class="mb-0 text-muted">Manifest management and driver assignments</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                <button class="btn btn-secondary shadow-sm" onclick="window.print()">
                    <i class="las la-print me-2"></i>Print Manifest
                </button>
            </div>
        </div>

        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="las la-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Scheduling Table -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h4 class="fs-18 mb-0 font-w600">Landtrip Manifest</h4>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="table-responsive">
                            <table id="deliveryTable" class="display table scheduling-table mb-0" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>SO Reference</th>
                                        <th>Client</th>
                                        <th>Address</th>
                                        <th>SI / DR #</th>
                                        <th>Assignment</th>
                                        <th>Remarks</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orders as $order)
                                    <tr>
                                        <td class="align-middle">
                                            <span class="text-black font-w600 d-block">{{ $order->so_number }}</span>
                                            <small class="text-muted">{{ $order->created_at->format('M d, Y') }}</small>
                                        </td>
                                        <td class="align-middle">
                                            <span class="text-black">{{ $order->customer->customer_name ?? 'N/A' }}</span>
                                        </td>
                                        <td class="align-middle" style="max-width: 200px;">
                                            <div class="text-truncate small text-muted" title="{{ $order->shipping_address ?? $order->billing_address ?? 'N/A' }}">
                                                {{ $order->shipping_address ?? $order->billing_address ?? 'Same as Billing' }}
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            @if($order->type == 'direct_consignment' || $order->type == 'consignment')
                                                <span class="text-info small font-w500">DR Only (Consignment)</span>
                                            @else
                                                <div class="small">
                                                    SI: <span class="badge bg-{{ $order->si_prepared_at ? 'success' : 'warning' }}">
                                                        {{ $order->si_prepared_at ? 'POSTED' : 'Pending' }}
                                                    </span>
                                                </div>
                                                <div class="small">
                                                    DR: <span class="badge bg-{{ $order->dr_prepared_at ? 'success' : 'warning' }}">
                                                        {{ $order->dr_prepared_at ? 'APPROVED' : 'Pending' }}
                                                    </span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            @if($order->driver)
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2 p-2 bg-light rounded text-black">
                                                        <i class="las la-user-tag"></i>
                                                    </div>
                                                    <div>
                                                        <span class="text-black font-w500 d-block small">{{ $order->driver }}</span>
                                                        <span class="text-muted extra-small d-block">{{ $order->plate_number }}</span>
                                                        <button class="btn btn-link btn-xs p-0 text-primary border-0" data-bs-toggle="modal" data-bs-target="#assignDriverModal{{ $order->id }}">Change</button>
                                                    </div>
                                                </div>
                                            @else
                                                <button class="btn btn-warning btn-xxs shadow-sm px-2" data-bs-toggle="modal" data-bs-target="#assignDriverModal{{ $order->id }}">
                                                    Assign Driver
                                                </button>
                                            @endif
                                            
                                            <!-- Assign Driver Modal -->
                                            <div class="modal fade" id="assignDriverModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0">
                                                        <div class="modal-header bg-secondary text-white border-0">
                                                            <h5 class="modal-title text-white">Assign Driver: {{ $order->so_number }}</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('production.logistic.assign-driver', $order->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-body p-4">
                                                                <div class="mb-3">
                                                                    <label class="form-label font-w500 text-black">Select Driver</label>
                                                                    <select name="driver_id" class="form-control default-select shadow-sm" required>
                                                                        <option value="">-- Choose Driver --</option>
                                                                        @foreach($drivers as $driver)
                                                                            <option value="{{ $driver->id }}" {{ (isset($order->driver_id) && $order->driver_id == $driver->id) ? 'selected' : '' }}>
                                                                                {{ $driver->first_name }} {{ $driver->last_name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="mb-0">
                                                                    <label class="form-label font-w500 text-black">Vehicle Plate Number</label>
                                                                    <input type="text" name="plate_number" class="form-control shadow-sm" value="{{ $order->plate_number ?? '' }}" placeholder="Ex: ABC 1234" required>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-secondary shadow">Update Assignment</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            @if($order->type == 'paid')
                                                <span class="status-badge status-paid">PAID</span>
                                            @elseif($order->terms == 'cod')
                                                <span class="status-badge status-cod">COD: ₱{{ number_format($order->total_amount, 2) }}</span>
                                            @else
                                                <span class="status-badge status-charge">{{ strtoupper($order->type ?? $order->terms ?? 'CHARGE') }}</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <span class="status-badge status-ready">Ready</span>
                                        </td>
                                        <td class="align-middle text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <form action="{{ route('production.logistic.mark-as-delivered', $order->id) }}" method="POST" onsubmit="return confirm('Confirm delivery completion?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success shadow btn-xs sharp" title="Mark Complete">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <a href="{{ route('production.logistic.print-transmittal', $order->id) }}" target="_blank" class="btn btn-info shadow btn-xs sharp" title="Print Transmittal">
                                                    <i class="las la-file-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="las la-clipboard-list fs-50 mb-3 d-block opacity-25"></i>
                                                No orders currently ready for delivery schedule.
                                            </div>
                                        </td>
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
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#deliveryTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 25,
                columnDefs: [{ orderable: false, targets: -1 }],
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>

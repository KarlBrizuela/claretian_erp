<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-truck me-2"></i>Freight Quotations</h5>
                        <a href="{{ route('marketing.freight-quotations.create') }}" class="btn btn-light btn-sm">
                            <i class="bi bi-plus me-1"></i>New Quotation
                        </a>
                    </div>
                    <div class="card-body">
                        <!-- Status Filters -->
                        <div class="mb-3 d-flex gap-2 flex-wrap">
                            <a href="{{ route('marketing.freight-quotations.list', ['status' => 'all']) }}" 
                               class="btn btn-sm {{ $currentStatus === 'all' ? 'btn-danger' : 'btn-outline-secondary' }}">
                                All
                            </a>
                            <a href="{{ route('marketing.freight-quotations.list', ['status' => 'draft']) }}" 
                               class="btn btn-sm {{ $currentStatus === 'draft' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                <i class="bi bi-pencil-square me-1"></i>Draft
                            </a>
                            <a href="{{ route('marketing.freight-quotations.list', ['status' => 'pending_logistics']) }}" 
                               class="btn btn-sm {{ $currentStatus === 'pending_logistics' ? 'btn-warning' : 'btn-outline-secondary' }}">
                                <i class="bi bi-hourglass-split me-1"></i>Pending Review
                            </a>
                            <a href="{{ route('marketing.freight-quotations.list', ['status' => 'approved']) }}" 
                               class="btn btn-sm {{ $currentStatus === 'approved' ? 'btn-success' : 'btn-outline-secondary' }}">
                                <i class="bi bi-check-circle me-1"></i>Approved
                            </a>
                            <a href="{{ route('marketing.freight-quotations.list', ['status' => 'linked_to_so']) }}" 
                               class="btn btn-sm {{ $currentStatus === 'linked_to_so' ? 'btn-info' : 'btn-outline-secondary' }}">
                                <i class="bi bi-link-45deg me-1"></i>Linked to SO
                            </a>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($quotations->isEmpty())
                            <div class="alert alert-info text-center py-4">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                <strong>No freight quotations found</strong>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Quote #</th>
                                            <th>Status</th>
                                            <th>Origin → Destination</th>
                                            <th>Mode</th>
                                            <th>Total Amount</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($quotations as $quotation)
                                            <tr>
                                                <td><strong>{{ $quotation->quote_number }}</strong></td>
                                                <td>
                                                    @php
                                                        $statusClass = [
                                                            'draft' => 'primary',
                                                            'pending_logistics' => 'warning',
                                                            'approved' => 'success',
                                                            'linked_to_so' => 'info',
                                                        ];
                                                        $statusIcon = [
                                                            'draft' => 'pencil-square',
                                                            'pending_logistics' => 'hourglass-split',
                                                            'approved' => 'check-circle',
                                                            'linked_to_so' => 'link-45deg',
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-{{ $statusClass[$quotation->workflow_status] ?? 'secondary' }}">
                                                        <i class="bi bi-{{ $statusIcon[$quotation->workflow_status] ?? '' }} me-1"></i>
                                                        {{ ucfirst(str_replace('_', ' ', $quotation->workflow_status)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small>
                                                        <strong>{{ $quotation->origin_province }}</strong><br>
                                                        ↓<br>
                                                        <strong>{{ $quotation->destination_province }}</strong>
                                                    </small>
                                                </td>
                                                <td>{{ $quotation->service_mode }}</td>
                                                <td>
                                                    @if($quotation->total_amount > 0)
                                                        <strong class="text-danger">₱ {{ number_format($quotation->total_amount, 2) }}</strong>
                                                    @else
                                                        <span class="text-muted">Pending quote</span>
                                                    @endif
                                                </td>
                                                <td><small>{{ $quotation->created_at->format('M d, Y') }}</small></td>
                                                <td>
                                                    <div class="d-flex gap-1 align-items-center">
                                                        <a href="{{ route('marketing.freight-quotations.show', $quotation->id) }}" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-eye me-1"></i>View
                                                        </a>
                                                        @if($quotation->workflow_status === 'approved' && !$quotation->sales_order_id)
                                                            <form method="POST" action="{{ route('marketing.freight-quotations.create-so-directly', $quotation->id) }}" style="display: inline;">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-success" title="Create Sales Order from this quotation">
                                                                    <i class="bi bi-plus-circle me-1"></i>Create SO
                                                                </button>
                                                            </form>
                                                        @elseif($quotation->sales_order_id)
                                                            <a href="{{ route('marketing.sales-orders.show', $quotation->sales_order_id) }}" 
                                                               class="btn btn-sm btn-info" title="View linked Sales Order">
                                                                <i class="bi bi-box-arrow-up-right me-1"></i>View SO
                                                            </a>
                                                        @endif
                                                        @if(!$quotation->sales_order_id)
                                                            <form method="POST" action="{{ route('marketing.freight-quotations.destroy', $quotation->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this freight quotation?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Quotation">
                                                                    <i class="bi bi-trash me-1"></i>Delete
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-3">
                                {{ $quotations->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout :title="$title" :sidebar="$sidebar">
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-inbox me-2"></i>Pending Freight Quotations from Marketing</h5>
                        <span class="badge bg-light text-dark">
                            @if($currentStatus === 'all')
                                All
                            @elseif($currentStatus === 'responded')
                                Responded
                            @else
                                Pending Review
                            @endif
                        </span>
                    </div>
                    <div class="card-body">
                        <!-- Status Filters -->
                        <div class="mb-3">
                            <a href="{{ route('production.logistic.pending-freight-quotations', ['status' => 'all']) }}" 
                               class="btn btn-sm {{ $currentStatus === 'all' ? 'btn-danger' : 'btn-outline-secondary' }}">
                                All
                            </a>
                            <a href="{{ route('production.logistic.pending-freight-quotations', ['status' => 'pending']) }}" 
                               class="btn btn-sm {{ $currentStatus === 'pending' ? 'btn-warning' : 'btn-outline-secondary' }}">
                                <i class="bi bi-hourglass-split me-1"></i>Pending Review
                            </a>
                            <a href="{{ route('production.logistic.pending-freight-quotations', ['status' => 'responded']) }}" 
                               class="btn btn-sm {{ $currentStatus === 'responded' ? 'btn-success' : 'btn-outline-secondary' }}">
                                <i class="bi bi-check-circle me-1"></i>Responded
                            </a>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('warning'))
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($quotations->isEmpty())
                            <div class="alert alert-info text-center py-4">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                <strong>No quotations to review</strong><br>
                                <small class="text-muted">Check back later</small>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Quote #</th>
                                            <th>From</th>
                                            <th>Origin → Destination</th>
                                            <th>Mode</th>
                                            <th>Items</th>
                                            <th>Status</th>
                                            <th>Submitted</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($quotations as $quotation)
                                            <tr>
                                                <td><strong>{{ $quotation->quote_number }}</strong></td>
                                                <td>
                                                    <small>{{ $quotation->createdBy->name ?? 'System' }}<br><span class="text-muted">{{ $quotation->createdBy->position ?? 'N/A' }}</span></small>
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
                                                    @php
                                                        $itemCount = is_array($quotation->cargo_items) ? count($quotation->cargo_items) : 0;
                                                        $totalQty = 0;
                                                        if (is_array($quotation->cargo_items)) {
                                                            foreach ($quotation->cargo_items as $item) {
                                                                $totalQty += $item['qty'] ?? 0;
                                                            }
                                                        }
                                                    @endphp
                                                    <span class="badge bg-info">{{ $itemCount }} items ({{ $totalQty }} units)</span>
                                                </td>
                                                <td>
                                                    @if($quotation->workflow_status === 'draft')
                                                        <span class="badge bg-warning">Pending Review</span>
                                                    @else
                                                        <span class="badge bg-success">Responded</span>
                                                    @endif
                                                </td>
                                                <td><small>{{ $quotation->created_at->format('M d, Y') }}</small></td>
                                                <td>
                                                    <a href="{{ route('production.logistic.show-freight-quotation', $quotation->id) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye me-1"></i>Review
                                                    </a>
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

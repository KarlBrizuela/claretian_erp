<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .billing-card {
            background: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }

        .document-title {
            text-align: center; 
            font-size: 1.75rem; 
            font-weight: 700;
            color: #333; 
            margin-top: 1rem; 
            text-transform: uppercase;
        }

        .btn-xs {
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            border-radius: 4px;
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12">
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card billing-card">
                <div class="document-title mb-4 d-flex justify-content-between align-items-center">
                    <span>RECONSIGNMENT REQUESTS</span>
                </div>

                <div class="card-body p-0">
                    <div class="px-0">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th>SO #</th>
                                        <th>Customer Name</th>
                                        <th>Date Requested</th>
                                        <th>Amount</th>
                                        <th class="text-center" style="width: 280px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reconsignmentRequests ?? [] as $req)
                                    @php
                                        // Calculate return books for this request
                                        $returnedBooks = [];
                                        foreach($req->items as $item) {
                                            $alreadyPurchasedQty = \App\Models\SalesInvoiceItem::whereHas('invoice', function($query) use ($req) {
                                                $query->where('so_id', $req->id)->where('status', '!=', 'cancelled');
                                            })->where('book_id', $item->book_id)->sum('quantity');
                                            $returnedQty = max(0, $item->quantity - $alreadyPurchasedQty);
                                            if ($returnedQty > 0) {
                                                $returnedBooks[] = [
                                                    'name' => $item->book->name ?? 'Unknown Book',
                                                    'isbn' => $item->book->isbn ?? 'N/A',
                                                    'qty' => $returnedQty,
                                                    'price' => $item->price
                                                ];
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td class="fw-bold text-primary">
                                            <button class="btn btn-xs btn-link p-0 me-2 text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#books-{{ $req->id }}" aria-expanded="false" aria-controls="books-{{ $req->id }}">
                                                <i class="las la-angle-down text-primary" style="font-size: 1.1rem; transition: transform 0.2s;"></i>
                                            </button>
                                            {{ $req->so_number }}
                                        </td>
                                        <td>{{ $req->customer ? ($req->customer->customer_name ?? $req->customer->company_name) : 'Unknown' }}</td>
                                        <td>{{ $req->updated_at->format('M d, Y') }}</td>
                                        <td class="fw-bold text-dark">₱ {{ number_format($req->total_amount, 2) }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <form action="{{ route('admin-finance.credit-collection.reconsignment.approve', $req->id) }}" method="POST" style="display:inline-flex; align-items:center; gap: 0.5rem; margin-bottom: 0;">
                                                    @csrf
                                                    <input type="text" name="terms" class="form-control form-control-sm px-2 py-1" placeholder="Terms" value="{{ $req->terms }}" style="width: 120px; font-size: 0.75rem; height: 28px;" title="Update Terms">
                                                    <button type="submit" class="btn btn-success btn-xs px-3 shadow d-flex align-items-center" title="Approve Request" style="height: 28px;">
                                                        <i class="las la-check me-1"></i> Go
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin-finance.credit-collection.reconsignment.reject', $req->id) }}" method="POST" style="display:inline; margin-bottom: 0;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-xs px-3 shadow d-flex align-items-center" title="Reject Request" style="height: 28px;">
                                                        <i class="las la-times me-1"></i> Reject
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="collapse" id="books-{{ $req->id }}">
                                        <td colspan="5" class="bg-light p-3 border-top-0">
                                            <div class="card border-0 shadow-sm rounded-3">
                                                <div class="card-body p-3">
                                                    <h6 class="fw-bold text-secondary mb-3"><i class="las la-book-open me-1"></i> Return Books (Remaining Consignment Qty)</h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered mb-0 bg-white">
                                                            <thead class="bg-light text-secondary">
                                                                <tr>
                                                                    <th>Book Title</th>
                                                                    <th class="text-center" style="width: 120px;">Qty to Return</th>
                                                                    <th class="text-end" style="width: 140px;">Unit Price</th>
                                                                    <th class="text-end" style="width: 160px;">Subtotal</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($returnedBooks as $book)
                                                                <tr>
                                                                    <td class="text-dark">{{ $book['name'] }}</td>
                                                                    <td class="text-center fw-bold text-danger">{{ $book['qty'] }}</td>
                                                                    <td class="text-end">₱{{ number_format($book['price'], 2) }}</td>
                                                                    <td class="text-end fw-bold text-dark">₱{{ number_format($book['qty'] * $book['price'], 2) }}</td>
                                                                </tr>
                                                                @empty
                                                                <tr>
                                                                    <td colspan="4" class="text-center py-2 text-muted italic">No returned books found. All items were purchased.</td>
                                                                </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted italic">
                                            <i class="las la-folder-open fs-2 d-block mb-2"></i>
                                            No pending reconsignment requests found.
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
</x-app-layout>

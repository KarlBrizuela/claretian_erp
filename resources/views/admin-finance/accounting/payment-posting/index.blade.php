<x-app-layout :title="$title" :role="$role" :sidebar="'admin-finance'">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Client Payment Posting Requests</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Nav Tabs --}}
                    <ul class="nav nav-tabs mb-4" id="postingTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                                Pending Posting
                                <span class="badge bg-warning ms-1 text-white">{{ $postings->filter(fn($p) => $p->status === 'pending')->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="posted-tab" data-bs-toggle="tab" data-bs-target="#posted" type="button" role="tab">
                                Posted Payments
                                <span class="badge bg-success ms-1 text-white">{{ $postings->filter(fn($p) => $p->status === 'posted')->count() }}</span>
                            </button>
                        </li>
                    </ul>

                    {{-- Tab Content --}}
                    <div class="tab-content" id="postingTabsContent">
                        {{-- Pending Tab --}}
                        <div class="tab-pane fade show active" id="pending" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-responsive-md table-hover">
                                    <thead>
                                        <tr>
                                            <th><strong>LETTER NO.</strong></th>
                                            <th><strong>DATE</strong></th>
                                            <th><strong>PREPARED BY</strong></th>
                                            <th><strong>TOTAL AMOUNT</strong></th>
                                            <th><strong>STATUS</strong></th>
                                            <th><strong>ACTIONS</strong></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($postings->filter(fn($p) => $p->status === 'pending') as $posting)
                                        <tr>
                                            <td><strong>PP-{{ str_pad($posting->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                            <td>{{ date('M d, Y', strtotime($posting->date)) }}</td>
                                            <td>{{ $posting->preparer->name ?? 'System' }}</td>
                                            <td><strong>₱ {{ number_format($posting->items->sum('amount') ?? 0, 2) }}</strong></td>
                                            <td><span class="badge light badge-warning">Pending</span></td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin-finance.accounting.payment-posting.show', $posting->id) }}" class="btn btn-primary btn-xs sharp" title="View Details"><i class="las la-eye"></i> View details</a>
                                                    <form action="{{ route('admin-finance.accounting.payment-posting.post', $posting->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Mark this letter as Posted?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-xs" title="Mark as Posted"><i class="las la-check-circle"></i> Post Payment</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4"><i class="las la-check-double la-2x d-block mb-2"></i>No pending payment posting requests.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Posted Tab --}}
                        <div class="tab-pane fade" id="posted" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-responsive-md table-hover">
                                    <thead>
                                        <tr>
                                            <th><strong>LETTER NO.</strong></th>
                                            <th><strong>DATE</strong></th>
                                            <th><strong>PREPARED BY</strong></th>
                                            <th><strong>TOTAL AMOUNT</strong></th>
                                            <th><strong>STATUS</strong></th>
                                            <th><strong>ACTIONS</strong></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($postings->filter(fn($p) => $p->status === 'posted') as $posting)
                                        <tr>
                                            <td><strong>PP-{{ str_pad($posting->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                            <td>{{ date('M d, Y', strtotime($posting->date)) }}</td>
                                            <td>{{ $posting->preparer->name ?? 'System' }}</td>
                                            <td><strong>₱ {{ number_format($posting->items->sum('amount') ?? 0, 2) }}</strong></td>
                                            <td><span class="badge light badge-success">Posted</span></td>
                                            <td>
                                                <a href="{{ route('admin-finance.accounting.payment-posting.show', $posting->id) }}" class="btn btn-primary btn-xs sharp" title="View Details"><i class="las la-eye"></i> View details</a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4"><i class="las la-history la-2x d-block mb-2"></i>No history of posted payments.</td>
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
</x-app-layout>

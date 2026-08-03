<x-app-layout :title="'Journal Entry Details'" :sidebar="'admin-finance'">
    <div class="row">
        <div class="col-xl-8">
            <div class="card h-auto">
                <div class="card-header border-0 pb-0 d-flex justify-content-between">
                    <div>
                        <h4 class="fs-20 mb-0 text-black">Entry #{{ $entry->entry_no }}</h4>
                        <span class="fs-14 text-muted">Posted on {{ date('M d, Y', strtotime($entry->date)) }}</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button onclick="window.print()" class="btn btn-primary rounded shadow-sm px-4 d-flex align-items-center justify-content-center" style="height: 40px !important; padding-top: 0 !important; padding-bottom: 0 !important;">
                            <i class="las la-print me-1"></i>Print
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h5 class="mb-2">Reference:</h5>
                            <p class="text-black">{{ $entry->reference ?? 'None' }}</p>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <h5 class="mb-2">Type:</h5>
                            <span class="badge light badge-primary">{{ $entry->entry_type ?? 'GJE' }}</span>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h5 class="mb-2">Memo / Description:</h5>
                        <p class="text-black">{{ $entry->memo ?? 'No description provided.' }}</p>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Account</th>
                                    <th class="text-end" style="width: 150px;">Debit</th>
                                    <th class="text-end" style="width: 150px;">Credit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($entry->items as $item)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $item->memo ?: $item->account->name }}</div>
                                        {{-- <div class="small text-muted">{{ $item->account->name }} - {{ $item->account->code }}</div> --}}
                                    </td>
                                    <td class="text-end">
                                        {{ $item->debit > 0 ? number_format($item->debit, 2) : '' }}
                                    </td>
                                    <td class="text-end">
                                        {{ $item->credit > 0 ? number_format($item->credit, 2) : '' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="fw-bold text-black" style="background: rgba(0,0,0,0.02)">
                                <tr>
                                    <td>TOTAL</td>
                                    <td class="text-end">₱ {{ number_format($entry->items->sum('debit'), 2) }}</td>
                                    <td class="text-end">₱ {{ number_format($entry->items->sum('credit'), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <div class="text-muted small">
                        Created by: {{ $entry->creator->name ?? 'System' }} | {{ $entry->created_at->format('M d, Y H:i') }}
                    </div>
                    <button type="button" class="btn btn-danger btn-xs rounded" 
                        data-toggle="modal" 
                        data-bs-toggle="modal" 
                        data-target="#deleteModal" 
                        data-bs-target="#deleteModal">
                        Delete Entry
                    </button>
                    
                    <!-- Delete Modal -->
                    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to delete journal entry <strong>{{ $entry->entry_no }}</strong>? This action will reverse all ledger postings and cannot be undone.
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal" data-dismiss="modal">Cancel</button>
                                    <form action="{{ route('accounting.journal.destroy', $entry->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Yes, Delete Entry</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
             <div class="card h-auto">
                <div class="card-header border-0 pb-0">
                    <h4 class="fs-20 mb-0 text-black">Audit Trail</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Status <span class="badge badge-success">{{ ucfirst($entry->status) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Currency <span>{{ $entry->currency }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Post ID <span>#{{ $entry->id }}</span>
                        </li>
                    </ul>
                </div>
             </div>
        </div>
    </div>
</x-app-layout>

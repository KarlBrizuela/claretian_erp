<x-app-layout :title="$title" :role="$role" :sidebar="'admin-finance'">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Approved Auto Debit Letters</h4>
                    <span class="badge bg-success px-3 py-2">Fully Approved — Ready for Processing</span>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover table-responsive-md">
                            <thead>
                                <tr>
                                    <th><strong>AD#</strong></th>
                                    <th><strong>DATE</strong></th>
                                    <th><strong>DEBIT DATE</strong></th>
                                    <th><strong>AMOUNT</strong></th>
                                    <th><strong>ITEM / REASON</strong></th>
                                    <th><strong>SOURCE / ORIGIN</strong></th>
                                    <th><strong>PREPARED BY</strong></th>
                                    <th><strong>DIRECTOR APPROVAL</strong></th>
                                    <th><strong>FINANCE APPROVAL</strong></th>
                                    <th><strong>ACTIONS</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($debits as $debit)
                                <tr>
                                    <td><strong>AD-{{ str_pad($debit->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                    <td>{{ date('M d, Y', strtotime($debit->date)) }}</td>
                                    <td>{{ date('M d, Y', strtotime($debit->debit_date)) }}</td>
                                    <td><strong class="text-danger">₱ {{ number_format($debit->amount, 2) }}</strong></td>
                                    <td>{{ Str::limit($debit->item_reason, 40) }}</td>
                                    <td>{{ Str::limit($debit->source_origin, 35) }}</td>
                                    <td>{{ $debit->preparer->name ?? '—' }}</td>
                                    <td>
                                        <small class="text-success">
                                            <i class="las la-check-circle"></i>
                                            {{ $debit->directorApprover->name ?? '—' }}<br>
                                            <span class="text-muted">{{ $debit->director_approved_at ? \Carbon\Carbon::parse($debit->director_approved_at)->format('M d, Y') : '' }}</span>
                                        </small>
                                    </td>
                                    <td>
                                        <small class="text-success">
                                            <i class="las la-check-double"></i>
                                            {{ $debit->financeApprover->name ?? '—' }}<br>
                                            <span class="text-muted">{{ $debit->finance_approved_at ? \Carbon\Carbon::parse($debit->finance_approved_at)->format('M d, Y') : '' }}</span>
                                        </small>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin-finance.accounting.auto-debits.show', $debit->id) }}" class="btn btn-primary btn-xs">
                                            <i class="las la-eye"></i> View Letter
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-5">
                                        <i class="las la-file-alt la-2x d-block mb-2"></i>
                                        No fully-approved Auto Debit letters yet.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $debits->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

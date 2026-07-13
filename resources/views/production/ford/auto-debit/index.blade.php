<x-app-layout :title="'Auto Debit'" :sidebar="'production'">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Auto Debit Letters</h4>
                    <a href="{{ route('production.ford.auto-debit.create') }}" class="btn btn-primary btn-sm">
                        <i class="las la-plus me-1"></i> Create New Auto Debit
                    </a>
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
                    <ul class="nav nav-tabs mb-4" id="autoDebitTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pending-director-tab" data-bs-toggle="tab" data-bs-target="#pending-director" type="button" role="tab">
                                Pending Director
                                <span class="badge bg-warning ms-1 text-white">{{ $debits->filter(fn($d) => $d->status === 'pending_director')->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pending-finance-tab" data-bs-toggle="tab" data-bs-target="#pending-finance" type="button" role="tab">
                                Pending Finance
                                <span class="badge bg-info ms-1 text-white">{{ $debits->filter(fn($d) => $d->status === 'pending_finance')->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab">
                                Approved
                                <span class="badge bg-success ms-1 text-white">{{ $debits->filter(fn($d) => $d->status === 'approved')->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab">
                                Rejected
                                <span class="badge bg-danger ms-1 text-white">{{ $debits->filter(fn($d) => $d->status === 'rejected')->count() }}</span>
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="autoDebitTabsContent">
                        {{-- Pending Director Tab --}}
                        <div class="tab-pane fade show active" id="pending-director" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>AD#</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Debit Date</th>
                                            <th>Item/Reason</th>
                                            <th>Prepared By</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($debits->filter(fn($d) => $d->status === 'pending_director') as $debit)
                                        <tr>
                                            <td><strong>AD-{{ str_pad($debit->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                            <td>{{ date('M d, Y', strtotime($debit->date)) }}</td>
                                            <td><strong>₱ {{ number_format($debit->amount, 2) }}</strong></td>
                                            <td>{{ date('M d, Y', strtotime($debit->debit_date)) }}</td>
                                            <td>{{ Str::limit($debit->item_reason, 40) }}</td>
                                            <td>{{ $debit->preparer->name ?? 'System' }}</td>
                                            <td><span class="badge light badge-warning">Pending Director</span></td>
                                            <td>
                                                <a href="{{ route('production.ford.auto-debit.show', $debit->id) }}" class="btn btn-primary btn-xs"><i class="las la-eye"></i> View</a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="8" class="text-center text-muted py-4">No pending Director approvals.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Pending Finance Tab --}}
                        <div class="tab-pane fade" id="pending-finance" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>AD#</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Debit Date</th>
                                            <th>Item/Reason</th>
                                            <th>Director Approved By</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($debits->filter(fn($d) => $d->status === 'pending_finance') as $debit)
                                        <tr>
                                            <td><strong>AD-{{ str_pad($debit->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                            <td>{{ date('M d, Y', strtotime($debit->date)) }}</td>
                                            <td><strong>₱ {{ number_format($debit->amount, 2) }}</strong></td>
                                            <td>{{ date('M d, Y', strtotime($debit->debit_date)) }}</td>
                                            <td>{{ Str::limit($debit->item_reason, 40) }}</td>
                                            <td>{{ $debit->directorApprover->name ?? '—' }}</td>
                                            <td><span class="badge light badge-info">Pending Finance Mgr</span></td>
                                            <td>
                                                <a href="{{ route('production.ford.auto-debit.show', $debit->id) }}" class="btn btn-primary btn-xs"><i class="las la-eye"></i> View</a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="8" class="text-center text-muted py-4">No pending Finance Manager/Supervisor approvals.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Approved Tab --}}
                        <div class="tab-pane fade" id="approved" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>AD#</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Debit Date</th>
                                            <th>Item/Reason</th>
                                            <th>Finance Approved By</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($debits->filter(fn($d) => $d->status === 'approved') as $debit)
                                        <tr>
                                            <td><strong>AD-{{ str_pad($debit->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                            <td>{{ date('M d, Y', strtotime($debit->date)) }}</td>
                                            <td><strong>₱ {{ number_format($debit->amount, 2) }}</strong></td>
                                            <td>{{ date('M d, Y', strtotime($debit->debit_date)) }}</td>
                                            <td>{{ Str::limit($debit->item_reason, 40) }}</td>
                                            <td>{{ $debit->financeApprover->name ?? '—' }}</td>
                                            <td><span class="badge light badge-success">Fully Approved</span></td>
                                            <td>
                                                <a href="{{ route('production.ford.auto-debit.show', $debit->id) }}" class="btn btn-primary btn-xs"><i class="las la-eye"></i> View</a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="8" class="text-center text-muted py-4">No fully approved letters.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Rejected Tab --}}
                        <div class="tab-pane fade" id="rejected" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>AD#</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Debit Date</th>
                                            <th>Item/Reason</th>
                                            <th>Prepared By</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($debits->filter(fn($d) => $d->status === 'rejected') as $debit)
                                        <tr>
                                            <td><strong>AD-{{ str_pad($debit->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                            <td>{{ date('M d, Y', strtotime($debit->date)) }}</td>
                                            <td><strong>₱ {{ number_format($debit->amount, 2) }}</strong></td>
                                            <td>{{ date('M d, Y', strtotime($debit->debit_date)) }}</td>
                                            <td>{{ Str::limit($debit->item_reason, 40) }}</td>
                                            <td>{{ $debit->preparer->name ?? 'System' }}</td>
                                            <td><span class="badge light badge-danger">Rejected</span></td>
                                            <td>
                                                <a href="{{ route('production.ford.auto-debit.show', $debit->id) }}" class="btn btn-primary btn-xs"><i class="las la-eye"></i> View</a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="8" class="text-center text-muted py-4">No rejected letters.</td></tr>
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

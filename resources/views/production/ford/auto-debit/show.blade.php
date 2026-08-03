<x-app-layout :title="$title" :role="$role" :sidebar="'production'">
    @push('styles')
    <style>
        .generated-letter-card {
            background: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
        }
        .generated-letter {
            background: #fff;
            border: 1px solid #ddd;
            padding: 40px;
            margin-top: 20px;
            min-height: 600px;
            font-family: 'Times New Roman', serif;
        }
        .memo-header {
            margin-bottom: 30px;
        }
        .memo-header-row {
            margin-bottom: 10px;
            display: flex;
            align-items: flex-start;
        }
        .memo-header-label {
            display: inline-block;
            width: 80px;
            font-weight: bold;
            color: #000;
        }
        .memo-header-value {
            display: inline-block;
            color: #000;
        }
        .memo-body {
            margin: 30px 0;
            line-height: 1.8;
            font-size: 1.05rem;
        }
        .memo-body-text {
            margin-bottom: 15px;
        }
        .memo-footer {
            margin-top: 40px;
        }
        .sig-line {
            border-bottom: 1px solid #000;
            margin-top: 40px;
            margin-bottom: 5px;
        }
        .approval-trail {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.25rem;
            margin-top: 1.5rem;
        }
        .approval-step {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid #dee2e6;
        }
        .approval-step:last-child {
            border-bottom: none;
        }
        .approval-step .step-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .step-icon.done { background: #d1e7dd; color: #0f5132; }
        .step-icon.pending { background: #fff3cd; color: #856404; }
        .step-icon.rejected { background: #f8d7da; color: #842029; }
        @media print {
            .sidebar-wrapper, .header, .print-actions { display: none !important; }
            .content-body { margin-left: 0 !important; padding: 0 !important; }
            .approval-trail, .print-actions { display: none !important; }
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12">
            <div class="card generated-letter-card">
                <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Auto Debit Letter — AD-{{ str_pad($debit->id, 5, '0', STR_PAD_LEFT) }}</h5>
                    @php
                        $statusColors = [
                            'pending_director' => 'warning',
                            'pending_finance' => 'info',
                            'approved' => 'success',
                            'rejected' => 'danger',
                        ];
                        $statusLabels = [
                            'pending_director' => 'Pending Director Approval',
                            'pending_finance' => 'Pending Finance Mgr/Supervisor Approval',
                            'approved' => 'Fully Approved',
                            'rejected' => 'Rejected',
                        ];
                        $color = $statusColors[$debit->status] ?? 'secondary';
                        $label = $statusLabels[$debit->status] ?? ucfirst($debit->status);
                    @endphp
                    <span class="badge bg-{{ $color }} px-3 py-2">{{ $label }}</span>
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

                    {{-- Official Letter --}}
                    <div class="generated-letter">
                        <div class="memo-header">
                            <div class="memo-header-row">
                                <span class="memo-header-label">DATE</span>
                                <span class="memo-header-value">: {{ date('F d, Y', strtotime($debit->date)) }}</span>
                            </div>
                            <div class="memo-header-row">
                                <span class="memo-header-label">TO</span>
                                <span class="memo-header-value">: BPI COMMONWEALTH AVE., Q.C.</span>
                            </div>
                            <div class="memo-header-row">
                                <span class="memo-header-label">FROM</span>
                                <span class="memo-header-value">: SR. ANNA MARIA R. VIOJAN, RMI / FR. DENNIS G. TAMAYO, CMF</span>
                            </div>
                        </div>

                        <div class="memo-body">
                            @php
                                function numberToWords($num) {
                                    $ones = ['', 'ONE', 'TWO', 'THREE', 'FOUR', 'FIVE', 'SIX', 'SEVEN', 'EIGHT', 'NINE',
                                             'TEN', 'ELEVEN', 'TWELVE', 'THIRTEEN', 'FOURTEEN', 'FIFTEEN', 'SIXTEEN',
                                             'SEVENTEEN', 'EIGHTEEN', 'NINETEEN'];
                                    $tens = ['', '', 'TWENTY', 'THIRTY', 'FORTY', 'FIFTY', 'SIXTY', 'SEVENTY', 'EIGHTY', 'NINETY'];
                                    if ($num == 0) return 'ZERO';
                                    $str = '';
                                    $numStr = number_format($num, 2, '.', '');
                                    $parts = explode('.', $numStr);
                                    $whole = (int) $parts[0];
                                    $cents = (int) $parts[1];
                                    if ($whole >= 1000000) { $str .= numberToWords(floor($whole / 1000000)) . ' MILLION '; $whole %= 1000000; }
                                    if ($whole >= 1000) { $str .= numberToWords(floor($whole / 1000)) . ' THOUSAND '; $whole %= 1000; }
                                    if ($whole >= 100) { $str .= $ones[floor($whole / 100)] . ' HUNDRED '; $whole %= 100; }
                                    if ($whole >= 20) { $str .= $tens[floor($whole / 10)] . ' '; $whole %= 10; }
                                    if ($whole > 0) $str .= $ones[$whole] . ' ';
                                    if ($cents > 0) $str .= 'AND ' . $cents . '/100 ';
                                    return trim($str);
                                }
                            @endphp
                            <div class="memo-body-text">
                                Please debit our Current Account Number <strong>3201-0268-07</strong> (Corporate Account - Claretian Communications Foundation, Inc.)
                                the amount of <strong>{{ numberToWords($debit->amount) }} PESOS</strong>
                                (P <strong>{{ number_format($debit->amount, 2) }}</strong>)
                                value on <span>{{ date('F d, Y', strtotime($debit->debit_date)) }}</span>;
                                representing <span>{{ $debit->item_reason }}</span> for <span>{{ $debit->source_origin }}</span>.
                            </div>
                            <div class="memo-body-text">
                                Thank you.
                            </div>
                        </div>

                        <div class="memo-footer">
                            <div style="display: flex; gap: 50px;">
                                <div style="flex: 1; text-align: center;">
                                    <div class="sig-line"></div>
                                    <strong>SR. ANNA MARIA R. VIOJAN, RMI</strong><br>
                                    Director Treasurer
                                </div>
                                <div style="flex: 1; text-align: center;">
                                    <div class="sig-line"></div>
                                    <strong>FR. DENNIS G. TAMAYO, CMF</strong><br>
                                    Executive Director
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Approval Trail --}}
                    <div class="approval-trail mt-4">
                        <h6 class="fw-bold mb-3"><i class="las la-check-double me-1"></i>Approval Trail</h6>
                        <div class="approval-step">
                            <div class="step-icon done">
                                <i class="las la-user-edit"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">Generated by</div>
                                <div class="text-muted small">{{ $debit->preparer->name ?? 'Unknown' }} — {{ $debit->created_at->format('M d, Y h:i A') }}</div>
                            </div>
                        </div>
                        <div class="approval-step">
                            <div class="step-icon {{ $debit->director_approved_by ? 'done' : ($debit->status === 'rejected' ? 'rejected' : 'pending') }}">
                                <i class="las la-{{ $debit->director_approved_by ? 'check' : ($debit->status === 'rejected' ? 'times' : 'clock') }}"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">Director Approval</div>
                                @if($debit->director_approved_by)
                                    <div class="text-muted small">Approved by {{ $debit->directorApprover->name ?? 'Unknown' }} — {{ $debit->director_approved_at ? \Carbon\Carbon::parse($debit->director_approved_at)->format('M d, Y h:i A') : '—' }}</div>
                                @elseif($debit->status === 'rejected')
                                    <div class="text-danger small">Rejected</div>
                                @else
                                    <div class="text-warning small">Waiting for Director approval</div>
                                @endif
                            </div>
                        </div>
                        <div class="approval-step">
                            <div class="step-icon {{ $debit->finance_approved_by ? 'done' : ($debit->status === 'rejected' ? 'rejected' : 'pending') }}">
                                <i class="las la-{{ $debit->finance_approved_by ? 'check-double' : ($debit->status === 'rejected' ? 'times' : 'clock') }}"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">Admin & Finance Manager/Supervisor Approval</div>
                                @if($debit->finance_approved_by)
                                    <div class="text-muted small">Approved by {{ $debit->financeApprover->name ?? 'Unknown' }} — {{ $debit->finance_approved_at ? \Carbon\Carbon::parse($debit->finance_approved_at)->format('M d, Y h:i A') : '—' }}</div>
                                @elseif($debit->status === 'rejected')
                                    <div class="text-danger small">Rejected</div>
                                @else
                                    <div class="text-muted small">{{ $debit->director_approved_by ? 'Waiting for Finance Manager/Supervisor approval' : 'Waiting for Director approval first' }}</div>
                                @endif
                            </div>
                        </div>
                        @if($debit->status === 'approved')
                        <div class="approval-step">
                            <div class="step-icon done">
                                <i class="las la-bank"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">Sent to Accounting</div>
                                <div class="text-success small">This Auto Debit letter has been fully approved and is now visible in the Accounting — Auto Debits page.</div>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Action Buttons --}}
                    <div class="print-actions d-flex justify-content-between align-items-center gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('production.ford.auto-debit') }}" class="btn btn-secondary rounded shadow-sm px-4">
                            <i class="las la-arrow-left me-1"></i>Back to List
                        </a>
                        <div class="d-flex gap-2">
                            @if($debit->status === 'pending_director' && (auth()->user()->position === 'Director' || auth()->user()->isSuperAdmin()))
                                <form action="{{ route('auto-debit.approve-director', $debit->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success text-white rounded shadow-sm px-4">
                                        <i class="las la-check me-1"></i>Approve as Director
                                    </button>
                                </form>
                                <form action="{{ route('auto-debit.reject', $debit->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger text-white rounded shadow-sm px-4" onclick="return confirm('Are you sure you want to reject this Auto Debit letter?')">
                                        <i class="las la-times me-1"></i>Reject
                                    </button>
                                </form>
                            @elseif($debit->status === 'pending_finance' && (str_contains(auth()->user()->position, 'Manager') || str_contains(auth()->user()->position, 'Supervisor') || auth()->user()->isSuperAdmin()))
                                <form action="{{ route('auto-debit.approve-finance', $debit->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success text-white rounded shadow-sm px-4">
                                        <i class="las la-check-double me-1"></i>Approve as Finance Manager/Supervisor
                                    </button>
                                </form>
                                <form action="{{ route('auto-debit.reject', $debit->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger text-white rounded shadow-sm px-4" onclick="return confirm('Are you sure you want to reject this Auto Debit letter?')">
                                        <i class="las la-times me-1"></i>Reject
                                    </button>
                                </form>
                            @endif
                            <button type="button" class="btn btn-light rounded shadow-sm px-4" onclick="window.print()">
                                <i class="las la-print me-1"></i>Print
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

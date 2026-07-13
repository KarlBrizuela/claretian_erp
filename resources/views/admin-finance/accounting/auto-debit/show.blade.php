<x-app-layout :title="$title" :role="$role" :sidebar="'admin-finance'">
    @push('styles')
    <style>
        .generated-letter-card { background: #fff; border-radius: 8px; padding: 2rem; box-shadow: 0 0 20px rgba(0,0,0,0.05); }
        .generated-letter { background: #fff; border: 1px solid #ddd; padding: 40px; margin-top: 20px; min-height: 600px; font-family: 'Times New Roman', serif; }
        .memo-header { margin-bottom: 30px; }
        .memo-header-row { margin-bottom: 10px; display: flex; align-items: flex-start; }
        .memo-header-label { display: inline-block; width: 80px; font-weight: bold; color: #000; }
        .memo-body { margin: 30px 0; line-height: 1.8; font-size: 1.05rem; }
        .memo-body-text { margin-bottom: 15px; }
        .sig-line { border-bottom: 1px solid #000; margin-top: 40px; margin-bottom: 5px; }
        .approval-trail { background: #f8f9fa; border-radius: 8px; padding: 1.25rem; margin-top: 1.5rem; }
        .approval-step { display: flex; align-items: center; gap: 1rem; padding: 0.75rem 0; border-bottom: 1px solid #dee2e6; }
        .approval-step:last-child { border-bottom: none; }
        .step-icon { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
        .step-icon.done { background: #d1e7dd; color: #0f5132; }
        .step-icon.pending { background: #fff3cd; color: #856404; }
        @media print {
            .sidebar-wrapper, .header, .print-actions, .approval-trail { display: none !important; }
            .content-body { margin-left: 0 !important; padding: 0 !important; }
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12">
            <div class="card generated-letter-card">
                <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Auto Debit Letter — AD-{{ str_pad($debit->id, 5, '0', STR_PAD_LEFT) }}</h5>
                    <span class="badge bg-success px-3 py-2">Fully Approved</span>
                </div>
                <div class="card-body">
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
                                function numberToWordsAF($num) {
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
                                    if ($whole >= 1000000) { $str .= numberToWordsAF(floor($whole / 1000000)) . ' MILLION '; $whole %= 1000000; }
                                    if ($whole >= 1000) { $str .= numberToWordsAF(floor($whole / 1000)) . ' THOUSAND '; $whole %= 1000; }
                                    if ($whole >= 100) { $str .= $ones[floor($whole / 100)] . ' HUNDRED '; $whole %= 100; }
                                    if ($whole >= 20) { $str .= $tens[floor($whole / 10)] . ' '; $whole %= 10; }
                                    if ($whole > 0) $str .= $ones[$whole] . ' ';
                                    if ($cents > 0) $str .= 'AND ' . $cents . '/100 ';
                                    return trim($str);
                                }
                            @endphp
                            <div class="memo-body-text">
                                Please debit our Current Account Number <strong>3201-0268-07</strong> (Corporate Account - Claretian Communications Foundation, Inc.)
                                the amount of <strong>{{ numberToWordsAF($debit->amount) }} PESOS</strong>
                                (P <strong>{{ number_format($debit->amount, 2) }}</strong>)
                                value on <span>{{ date('F d, Y', strtotime($debit->debit_date)) }}</span>;
                                representing <span>{{ $debit->item_reason }}</span> for <span>{{ $debit->source_origin }}</span>.
                            </div>
                            <div class="memo-body-text">Thank you.</div>
                        </div>

                        <div class="memo-footer">
                            <div style="display: flex; gap: 50px;">
                                <div style="flex: 1; text-align: center;">
                                    <div class="sig-line"></div>
                                    <strong>SR. ANNA MARIA R. VIOJAN, RMI</strong><br>Director Treasurer
                                </div>
                                <div style="flex: 1; text-align: center;">
                                    <div class="sig-line"></div>
                                    <strong>FR. DENNIS G. TAMAYO, CMF</strong><br>Executive Director
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Approval Trail --}}
                    <div class="approval-trail mt-4">
                        <h6 class="fw-bold mb-3"><i class="las la-check-double me-1"></i>Approval Trail</h6>
                        <div class="approval-step">
                            <div class="step-icon done"><i class="las la-user-edit"></i></div>
                            <div>
                                <div class="fw-semibold">Generated by</div>
                                <div class="text-muted small">{{ $debit->preparer->name ?? 'Unknown' }} — {{ $debit->created_at->format('M d, Y h:i A') }}</div>
                            </div>
                        </div>
                        <div class="approval-step">
                            <div class="step-icon done"><i class="las la-check"></i></div>
                            <div>
                                <div class="fw-semibold">Director Approval</div>
                                <div class="text-muted small">Approved by {{ $debit->directorApprover->name ?? 'Unknown' }} — {{ $debit->director_approved_at ? \Carbon\Carbon::parse($debit->director_approved_at)->format('M d, Y h:i A') : '—' }}</div>
                            </div>
                        </div>
                        <div class="approval-step">
                            <div class="step-icon done"><i class="las la-check-double"></i></div>
                            <div>
                                <div class="fw-semibold">Admin & Finance Manager/Supervisor Approval</div>
                                <div class="text-muted small">Approved by {{ $debit->financeApprover->name ?? 'Unknown' }} — {{ $debit->finance_approved_at ? \Carbon\Carbon::parse($debit->finance_approved_at)->format('M d, Y h:i A') : '—' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="print-actions d-flex justify-content-between gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('admin-finance.accounting.auto-debits.index') }}" class="btn btn-secondary rounded shadow-sm px-4">
                            <i class="las la-arrow-left me-1"></i>Back to List
                        </a>
                        <button type="button" class="btn btn-light rounded shadow-sm px-4" onclick="window.print()">
                            <i class="las la-print me-1"></i>Print Letter
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

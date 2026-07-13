<x-app-layout :title="$title" :role="$role" :sidebar="'admin-finance'">
    @push('styles')
    <style>
        .generated-letter-card {
            background: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }
        .generated-letter {
            background: #fff;
            padding: 20px;
            min-height: 500px;
            font-family: 'Times New Roman', serif;
        }
        .memo-header {
            margin-bottom: 20px;
            font-size: 1.1rem;
        }
        .memo-header-row {
            margin-bottom: 5px;
            display: flex;
        }
        .memo-header-label {
            font-weight: bold;
            width: 100px;
        }
        .memo-header-value {
            flex: 1;
        }
        .memo-body {
            margin: 20px 0;
            line-height: 1.8;
            font-size: 1.1rem;
        }
        .memo-body-text {
            margin-bottom: 15px;
        }
        .memo-footer {
            margin-top: 50px;
        }
        .memo-signature {
            margin-top: 30px;
            display: flex;
            gap: 50px;
            flex-wrap: wrap;
        }
        .signature-col {
            flex: 1;
            min-width: 200px;
            text-align: center;
        }
        .sig-line {
            border-bottom: 1px solid #000;
            margin-top: 40px;
            margin-bottom: 5px;
        }
        .status-badge-pending {
            background: #fff3cd; color: #856404; padding: 4px 12px; border-radius: 20px; font-weight: 600; font-size: 0.8rem;
        }
        .status-badge-posted {
            background: #d1e7dd; color: #0f5132; padding: 4px 12px; border-radius: 20px; font-weight: 600; font-size: 0.8rem;
        }
        @media print {
            .sidebar-wrapper, .header, .print-actions { display: none !important; }
            .content-body { margin-left: 0 !important; padding: 0 !important; }
            .generated-letter-card { box-shadow: none; padding: 0; }
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12">
            <div class="card generated-letter-card">
                <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Payment Posting Memo (PP-{{ str_pad($posting->id, 5, '0', STR_PAD_LEFT) }})</h5>
                    <div>
                        <span class="{{ $posting->status === 'posted' ? 'status-badge-posted' : 'status-badge-pending' }}">
                            {{ strtoupper($posting->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="generated-letter">
                        <div class="memo-header">
                            <div class="memo-header-row">
                                <span class="memo-header-label">DATE</span>
                                <span class="memo-header-value">: {{ date('F d, Y', strtotime($posting->date)) }}</span>
                            </div>
                            <div class="memo-header-row">
                                <span class="memo-header-label">TO</span>
                                <span class="memo-header-value">: ACCOUNTING DEPT.</span>
                            </div>
                            <div class="memo-header-row">
                                <span class="memo-header-label">FROM</span>
                                <span class="memo-header-value">: FOREIGN ORDER AND RIGHTS DEPT.</span>
                            </div>
                            <div class="memo-header-row">
                                <span class="memo-header-label">RE</span>
                                <span class="memo-header-value">: CLIENT'S PAYMENT FOR POSTING</span>
                            </div>
                        </div>

                        <div class="memo-body">
                            <div class="memo-body-text">
                                Please see attached copy of the deposited check / remittance as payment of the following client(s):
                            </div>
                            <table class="table table-bordered mt-3" style="border: 1px solid #000 !important; font-size: 1.05rem;">
                                <thead style="background: #f8f9fa;">
                                    <tr>
                                        <th style="border: 1px solid #000; text-align: center; width: 50px; font-weight: bold;">NO.</th>
                                        <th style="border: 1px solid #000; font-weight: bold;">CLIENT'S NAME</th>
                                        <th style="border: 1px solid #000; font-weight: bold;">BANK/DATE</th>
                                        <th style="border: 1px solid #000; font-weight: bold;">DOCUMENT NO.</th>
                                        <th style="border: 1px solid #000; text-align: right; font-weight: bold; width: 150px;">AMOUNT</th>
                                        <th style="border: 1px solid #000; text-align: center; font-weight: bold; width: 180px;" class="print-actions">PROOF OF PAYMENT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalAmount = 0; @endphp
                                    @foreach($posting->items as $index => $item)
                                    <tr>
                                        <td style="border: 1px solid #000; text-align: center;">{{ $index + 1 }}</td>
                                        <td style="border: 1px solid #000;">{{ $item->customer->customer_name ?? $item->client_name }}</td>
                                        <td style="border: 1px solid #000;">{{ $item->bank_date ?? '—' }}</td>
                                        <td style="border: 1px solid #000;">{{ $item->document_no ?? '—' }}</td>
                                        <td style="border: 1px solid #000; text-align: right;">₱ {{ number_format($item->amount, 2) }}</td>
                                        <td style="border: 1px solid #000; text-align: center;" class="print-actions">
                                            @if($item->proof_attachment)
                                                <a href="/storage/{{ $item->proof_attachment }}" target="_blank" class="btn btn-outline-info btn-xs rounded shadow-sm px-2 py-1">
                                                    <i class="las la-paperclip me-1"></i>View Attachment
                                                </a>
                                            @else
                                                <span class="text-muted small">No file</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @php $totalAmount += $item->amount; @endphp
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr style="background: #f8f9fa;">
                                        <td colspan="4" style="border: 1px solid #000; text-align: right; font-weight: bold;">TOTAL:</td>
                                        <td style="border: 1px solid #000; text-align: right; font-weight: bold;" class="text-danger">₱ {{ number_format($totalAmount, 2) }}</td>
                                        <td class="print-actions" style="border: 1px solid #000;"></td>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="memo-body-text mt-3">
                                For your appropriate action.
                            </div>
                        </div>

                        <div class="memo-footer">
                            <div class="memo-signature">
                                <div class="signature-col">
                                    <div>Prepared by:</div>
                                    <div class="sig-line"></div>
                                    <strong>MICHELLE MACALABON</strong><br>
                                    FORD Clerk
                                </div>
                                <div class="signature-col">
                                    <div>Noted by:</div>
                                    <div class="sig-line"></div>
                                    <strong>CRISTINA J. GALANG</strong><br>
                                    FORD Head
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="print-actions d-flex justify-content-between gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('admin-finance.accounting.payment-posting.index') }}" class="btn btn-primary rounded shadow-sm px-5 d-flex align-items-center justify-content-center" style="background: #ff0000; color: #ffffff; border: none; height: 35px !important; padding-top: 0 !important; padding-bottom: 0 !important;">
                            <i class="las la-arrow-left me-1"></i>Back to List
                        </a>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-light rounded shadow-sm px-5 d-flex align-items-center justify-content-center" style="height: 35px !important; padding-top: 0 !important; padding-bottom: 0 !important;" onclick="window.print()"><i class="las la-print me-1"></i>Print</button>
                            @if($posting->status === 'pending')
                            <form action="{{ route('admin-finance.accounting.payment-posting.post', $posting->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-success rounded shadow-sm px-5 d-flex align-items-center justify-content-center" style="height: 35px !important; padding-top: 0 !important; padding-bottom: 0 !important;">
                                    <i class="las la-check-circle me-1"></i>Mark as Posted
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

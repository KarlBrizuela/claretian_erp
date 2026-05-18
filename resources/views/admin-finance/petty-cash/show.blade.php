<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .voucher-form {
            background: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }
        .form-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e0e0e0;
        }
        .form-header .company-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .form-header .company-logo {
            width: 60px; height: 60px;
            background: #ff0000; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 2rem; font-weight: bold;
        }
        .form-header .company-name {
            font-size: 1.25rem; font-weight: 700; color: #333;
            text-transform: uppercase;
        }
        .form-header .document-title {
            text-align: center; font-size: 1.75rem; font-weight: 700;
            color: #333; margin-top: 1rem;
        }
        .voucher-table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; }
        .voucher-table thead { background: #ff0000; color: #fff; }
        .voucher-table th { padding: 0.75rem; border: 1px solid #ddd; }
        .voucher-table td { padding: 0.65rem 0.75rem; border: 1px solid #ddd; }
        .voucher-table tfoot { background: #f8f9fa; font-weight: 600; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem; background: #f8f9fa; padding: 1rem; border-radius: 6px; }
        .info-item label { font-size: 0.75rem; font-weight: 600; color: #888; text-transform: uppercase; margin-bottom: 0; }
        .info-item .value { font-size: 1rem; font-weight: 600; color: #333; }
        .signature-row { display: flex; gap: 2rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e0e0e0; }
        .signature-box { flex: 1; text-align: center; }
        .signature-box .sig-name { font-weight: 600; margin-top: 0.5rem; }
        .signature-box .sig-label { font-size: 0.8rem; color: #888; border-top: 1px solid #ccc; padding-top: 0.5rem; margin-top: 2rem; }
        .status-badge-open { background: #fff3cd; color: #856404; padding: 4px 12px; border-radius: 20px; font-weight: 600; font-size: 0.8rem; }
        .status-badge-liquidated { background: #d1e7dd; color: #0f5132; padding: 4px 12px; border-radius: 20px; font-weight: 600; font-size: 0.8rem; }
        @media print {
            .sidebar-wrapper, .header, .print-actions { display: none !important; }
            .content-body { margin-left: 0 !important; padding: 0 !important; }
            .voucher-form { box-shadow: none; }
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12">
            <div class="card voucher-form">
                <div class="form-header">
                    <div class="company-info">
                        <div class="company-logo">C</div>
                        <div>
                            <div class="company-name">CLARETIAN COMMUNICATIONS FOUNDATION INC.</div>
                            <div>8 Mayumi St., UP Village, Diliman, Quezon City | Tel. No.: 921-3984</div>
                        </div>
                        <div class="ms-auto text-end">
                            <span class="{{ $voucher->status === 'liquidated' ? 'status-badge-liquidated' : 'status-badge-open' }}">
                                {{ strtoupper($voucher->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="document-title">PETTY CASH VOUCHER</div>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <label>PCV No.</label>
                        <div class="value text-danger">{{ $voucher->pcv_number }}</div>
                    </div>
                    <div class="info-item">
                        <label>Pay To</label>
                        <div class="value">{{ $voucher->pay_to }}</div>
                    </div>
                    <div class="info-item">
                        <label>Date</label>
                        <div class="value">{{ date('F d, Y', strtotime($voucher->date)) }}</div>
                    </div>
                </div>

                <table class="voucher-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>PARTICULARS</th>
                            <th style="width: 160px;" class="text-end">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $grandTotal = 0; @endphp
                        @foreach($voucher->items as $i => $item)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $item->particulars }}</td>
                            <td class="text-end">{{ number_format($item->amount, 2) }}</td>
                            @php $grandTotal += $item->amount; @endphp
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-end"><strong>TOTAL</strong></td>
                            <td class="text-end text-danger fs-16"><strong>₱ {{ number_format($grandTotal, 2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>


                @if($voucher->journal_entry_id)
                <div class="mt-4 p-3 border rounded bg-light-success">
                    <small class="text-success"><i class="las la-check-circle me-1"></i>
                        Liquidated & Journalized — <a href="{{ route('accounting.journal.show', $voucher->journal_entry_id) }}" class="text-success fw-bold">View Journal Entry</a>
                    </small>
                </div>
                @endif

                <div class="print-actions d-flex justify-content-between gap-2 mt-4">
                    <a href="{{ route('admin-finance.petty-cash.index') }}" class="btn btn-primary rounded shadow-sm px-5 d-flex align-items-center justify-content-center" style="background: #ff0000; color: #ffffff; border: none; height: 35px !important; padding-top: 0 !important; padding-bottom: 0 !important;">
                        <i class="las la-arrow-left me-1"></i>Back to List
                    </a>
                    <button type="button" class="btn btn-light rounded shadow-sm px-5 d-flex align-items-center justify-content-center" style="height: 35px !important; padding-top: 0 !important; padding-bottom: 0 !important;" onclick="window.print()"><i class="las la-print me-1"></i>Print</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

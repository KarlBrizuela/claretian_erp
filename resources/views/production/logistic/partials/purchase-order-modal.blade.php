<div class="order-form p-0 shadow-none border-0">
    <div class="form-header">
        <div class="company-info">
            <div class="company-logo">C</div>
            <div class="company-details">
                <div class="company-name">CLARETIAN COMMUNICATIONS FOUNDATION, INC.</div>
                <div class="company-address">8 Mayumi St., UP Village, Diliman, Quezon City 1128</div>
                <div class="company-contact">Non-Vat Reg. TIN: 000-395-713-000</div>
                <div class="company-contact text-nowrap">Telephone: (02) 921-3984 | Fax: (02) 921-6205</div>
            </div>
        </div>
        <div class="document-title">PURCHASE ORDER</div>
    </div>

    <div class="customer-section">
        <div class="customer-details">
            <h5 class="fs-16">Vendor Information</h5>
            <p class="mb-1"><strong>Vendor:</strong> {{ $po->supplier->company_name }}</p>
            <p class="mb-1"><strong>Contact:</strong> {{ $po->supplier->contact_person }}</p>
            <p class="mb-0"><strong>Address:</strong> {{ $po->supplier->address ?? 'N/A' }}</p>
        </div>
        <div class="order-details">
            <h5 class="fs-16">Order Information</h5>
            <p class="mb-1"><strong>Date:</strong> {{ \Carbon\Carbon::parse($po->date)->format('F d, Y') }}</p>
            <p class="mb-1"><strong>P.O. No.:</strong> {{ $po->po_number }}</p>
            <p class="mb-1"><strong>Terms:</strong> {{ $po->terms ?? 'N/A' }}</p>
            <p class="mb-0"><strong>Status:</strong> <span class="badge badge-primary text-capitalize">{{ str_replace('_', ' ', $po->status) }}</span></p>
        </div>
    </div>

    <div class="table-responsive mt-3">
        <table class="table table-bordered">
            <thead class="bg-primary text-white">
                <tr>
                    <th style="width: 60px;">QTY</th>
                    <th>PRODUCT / DESCRIPTION</th>
                    <th style="width: 120px; text-align: right;">UNIT PRICE</th>
                    <th style="width: 120px; text-align: right;">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                @foreach($po->items as $item)
                <tr>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td>
                        <strong>{{ $item->product ? $item->product->name : $item->description }}</strong>
                        @if($item->isbn) <br><small class="text-muted">ISBN: {{ $item->isbn }}</small> @endif
                    </td>
                    <td class="text-end">{{ $po->source === 'ford' ? '$' : '₱' }}{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end">{{ $po->source === 'ford' ? '$' : '₱' }}{{ number_format($item->total_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-light">
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: 600;">TOTAL:</td>
                    <td style="text-align: right; font-weight: 600;">{{ $po->source === 'ford' ? '$' : '₱' }}{{ number_format($po->total_amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="signature-section mt-4">
        <div class="row">
            <div class="col-6">
                <strong>Prepared by:</strong>
                <div class="mt-4 pt-1 border-top">
                    {{ $po->preparedBy->name ?? 'System' }}
                </div>
            </div>
            <div class="col-6">
                <strong>Approved by:</strong>
                <div class="mt-2 text-center">
                    <strong>Fr. Louie R. Guades III, CMF</strong><br>
                    <small>Executive Director</small>
                </div>
            </div>
        </div>
    </div>
</div>

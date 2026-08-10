<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Invoice - {{ $order->so_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    @php
        $hideActions = request('hide_actions', false) || request('iframe', false);
        $format = request('format', 'whole');
        $halfPart = request('half', null); // 1 = first half of items, 2 = second half
    @endphp
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background-color: {{ $hideActions ? '#fff' : '#f4f6f9' }};
            color: #000;
            padding: {{ $hideActions ? '0' : '15px' }};
        }

        .invoice-box {
            background: #fff;
            max-width: 8.5in;
            width: 100%;
            min-height: 7in;
            margin: 0 auto;
            padding: 0.25in 0.35in;
            border: {{ $hideActions ? 'none' : '1px solid #ccc' }};
            box-shadow: {{ $hideActions ? 'none' : '0 4px 15px rgba(0, 0, 0, 0.1)' }};
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        @if($format === 'half')
        .header-section {
            padding-bottom: 3px !important;
            margin-bottom: 5px !important;
        }
        .header-logo {
            width: 45px !important;
            height: 45px !important;
        }
        .company-name {
            font-size: 10.5pt !important;
        }
        .company-subtitle, .company-address, .company-contact {
            font-size: 7.5pt !important;
            margin-top: 1px !important;
        }
        .doc-no {
            font-size: 9pt !important;
        }
        .doc-no span {
            font-size: 10pt !important;
        }
        .doc-title {
            font-size: 11pt !important;
        }
        .info-grid {
            margin-bottom: 5px !important;
            font-size: 8.5pt !important;
        }
        .items-table {
            margin-bottom: 5px !important;
            font-size: 8.5pt !important;
        }
        .items-table th {
            padding: 3px 5px !important;
            font-size: 7.5pt !important;
        }
        .items-table td {
            padding: 3px 5px !important;
        }
        .payment-sales-row {
            margin-bottom: 5px !important;
            font-size: 8.5pt !important;
        }
        .total-sales-box {
            font-size: 9pt !important;
        }
        .total-sales-box span {
            font-size: 10pt !important;
        }
        .conditions-bank-container {
            margin-bottom: 5px !important;
            font-size: 7pt !important;
        }
        .signatories-row {
            margin-top: 5px !important;
            margin-bottom: 5px !important;
            font-size: 8pt !important;
        }
        .sig-line {
            margin-top: 10px !important;
        }
        @endif

        .header-section {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            border-bottom: 2px solid #000;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }

        .header-logo-details {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-logo {
            width: 65px;
            height: 65px;
            object-fit: contain;
            filter: grayscale(100%) contrast(150%);
        }

        .company-name {
            font-size: 12.5pt;
            font-weight: 900;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin: 0;
            line-height: 1.1;
        }

        .company-subtitle {
            font-size: 8.5pt;
            font-weight: bold;
            color: #000;
            margin-top: 2px;
        }

        .company-address, .company-contact {
            font-size: 8pt;
            color: #000;
            margin-top: 1px;
            line-height: 1.2;
        }

        .header-right {
            text-align: right;
        }

        .doc-no {
            font-size: 10.5pt;
            font-weight: 800;
            margin-bottom: 2px;
        }

        .doc-no span {
            color: #000;
            font-size: 11.5pt;
        }

        .doc-title {
            font-size: 14pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 2px;
        }

        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 9.5pt;
        }

        .info-grid td {
            padding: 3px 4px;
            vertical-align: bottom;
        }

        .info-label {
            font-weight: bold;
            width: 70px;
            white-space: nowrap;
        }

        .info-value-line {
            border-bottom: 1.5px solid #000;
            padding-left: 5px;
            font-weight: 600;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 9.5pt;
        }

        .items-table th {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 5px 8px;
            text-transform: uppercase;
            font-weight: 900;
            font-size: 8.5pt;
        }

        .items-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: top;
        }

        .items-table tr:last-child td {
            border-bottom: 2px solid #000;
        }

        .payment-sales-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            font-size: 10pt;
            font-weight: bold;
        }

        .checkbox-group {
            display: flex;
            gap: 25px;
            align-items: center;
        }

        .custom-cb {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .cb-box {
            width: 16px;
            height: 16px;
            border: 2px solid #000;
            display: inline-block;
            text-align: center;
            line-height: 12px;
            font-size: 11pt;
            font-weight: 900;
        }

        .total-sales-box {
            font-size: 10.5pt;
            font-weight: 900;
        }

        .total-sales-box span {
            border-bottom: 2px solid #000;
            padding: 0 12px;
            font-size: 12pt;
            color: #000;
        }

        .conditions-bank-container {
            display: flex;
            gap: 15px;
            font-size: 7.5pt;
            margin-bottom: 12px;
            line-height: 1.35;
        }

        .conditions-block {
            flex: 1.2;
        }

        .bank-block {
            flex: 1;
        }

        .signatories-row {
            display: flex;
            justify-content: space-between;
            margin-top: 12px;
            margin-bottom: 12px;
            font-size: 8.5pt;
        }

        .sig-col {
            flex: 1;
            padding-right: 15px;
        }

        .sig-col:last-child {
            padding-right: 0;
        }

        .sig-line {
            border-bottom: 1.5px solid #000;
            margin-top: 18px;
            min-height: 16px;
            font-weight: bold;
            text-align: center;
        }

        .footer-notice {
            font-size: 7pt;
            color: #444;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }

        .actions-bar {
            max-width: 8.5in;
            margin: 0 auto 12px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 10px 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        body.preprinted-mode .invoice-box {
            padding-top: 0.95in !important;
        }

        /* Adjust right column (Date, Terms, Due Date) vertical alignment on pre-printed form */
        body.preprinted-mode .info-grid tr td:nth-child(3),
        body.preprinted-mode .info-grid tr td:nth-child(4) {
            padding-top: 18px !important;
        }

        /* Expand hidden table header height so line items start below the pre-printed table header */
        body.preprinted-mode .items-table thead tr {
            height: 68px !important;
        }

        body.preprinted-mode .items-table {
            min-height: 2.6in !important;
            margin-bottom: 5px !important;
        }

        /* Checkbox ✓ and Total Sales alignment */
        body.preprinted-mode .payment-sales-row {
            margin-top: 18px !important;
            margin-bottom: 5px !important;
        }

        body.preprinted-mode .cb-box {
            border-color: transparent !important;
            font-size: 13pt !important;
            line-height: 12px !important;
        }

        /* Signatories alignment */
        body.preprinted-mode .signatories-row {
            margin-top: 24px !important;
            padding-top: 5px !important;
        }

        body.preprinted-mode .sig-line {
            border-color: transparent !important;
            margin-top: 26px !important;
            font-size: 9pt !important;
            font-weight: bold !important;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .invoice-box {
                border: none;
                box-shadow: none;
                padding: 0.15in 0.25in;
                width: 8.5in;
                max-width: 8.5in;
                height: 7in;
                min-height: 7in;
                justify-content: space-between;
            }
            body.preprinted-mode .invoice-box {
                padding-top: 0.95in !important;
            }
            .actions-bar {
                display: none !important;
            }
            @page {
                size: portrait;
                margin: 0;
            }
        }
        body.preprinted-mode .header-logo-details,
        body.preprinted-mode .company-name,
        body.preprinted-mode .company-subtitle,
        body.preprinted-mode .company-address,
        body.preprinted-mode .company-contact,
        body.preprinted-mode .doc-title,
        body.preprinted-mode .info-label,
        body.preprinted-mode .items-table th,
        body.preprinted-mode .cb-text,
        body.preprinted-mode .conditions-bank-container,
        body.preprinted-mode .sig-label,
        body.preprinted-mode .footer-notice,
        body.preprinted-mode .doc-no,
        body.preprinted-mode .total-label {
            visibility: hidden !important;
        }
        body.preprinted-mode .header-section,
        body.preprinted-mode .info-value-line,
        body.preprinted-mode .items-table th,
        body.preprinted-mode .items-table td,
        body.preprinted-mode .cb-box,
        body.preprinted-mode .total-sales-box span,
        body.preprinted-mode .sig-line {
            border-color: transparent !important;
        }
    </style>
</head>
<body>

    @if(!$hideActions)
    <div class="actions-bar">
        <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
            <i class="las la-arrow-left me-1"></i> Back
        </a>
        <div class="d-flex align-items-center gap-3">
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="preprintedToggle" onchange="togglePreprintedMode(this)">
                <label class="form-check-label fw-bold small text-dark" for="preprintedToggle">Overlay on Pre-printed Paper Form</label>
            </div>
            <button onclick="window.print()" class="btn btn-danger btn-sm px-4 shadow-sm" style="background:#ff0000; border: none;">
                <i class="las la-print me-1"></i> Print / Save PDF
            </button>
        </div>
    </div>
    @endif

    @php
        $activeInvoice = null;
        if (in_array($order->type, ['area_consignment', 'area_sales_consignment'])) {
            $activeInvoice = \App\Models\SalesInvoice::where('so_id', $order->id)->where('status', '!=', 'cancelled')->latest()->first();
        }

        if ($activeInvoice) {
            $allItems = $activeInvoice->items;
            $totalSalesAmount = (float) $activeInvoice->total_amount;
        } else {
            $allItems = $order->items;
            $totalSalesAmount = (float) $order->total_amount;
        }

        // Split items if half parameter is set
        if ($halfPart) {
            $itemsArray = $allItems->values();
            $totalCount = $itemsArray->count();
            $midpoint = (int) ceil($totalCount / 2);
            if ($halfPart == '1') {
                $itemsToPrint = $itemsArray->slice(0, $midpoint)->values();
            } else {
                $itemsToPrint = $itemsArray->slice($midpoint)->values();
            }
            $halfLabel = $halfPart == '1' ? 'Part 1 of 2' : 'Part 2 of 2';
        } else {
            $itemsToPrint = $allItems;
            $halfLabel = null;
        }

        $isCash = in_array($order->payment_method, ['cash', 'gcash', 'paymaya', 'card', 'bank', 'check']) 
                  || in_array($order->type, ['calculator_pos', 'ecom_direct', 'paid']);
        $custName = ($order->customer?->customer_name && $order->customer->customer_name !== 'N/A') ? $order->customer->customer_name : 'Cash Customer';
        
        $rawAddr = $order->billing_address ?: ($order->shipping_address ?: ($order->customer?->billing_address ?? ''));
        $custAddress = ($rawAddr === 'N/A') ? '' : $rawAddr;
        
        $rawTin = $order->customer?->tin ?? '';
        $custTin = ($rawTin === 'N/A') ? '' : $rawTin;
        
        $termsVal = $order->terms ?: ($order->payment_method ? strtoupper($order->payment_method) : 'CASH');
        $orderDate = $order->created_at ? $order->created_at->format('m/d/Y') : date('m/d/Y');
        $dueDate = ($order->due_date && $order->due_date !== 'N/A') ? \Carbon\Carbon::parse($order->due_date)->format('m/d/Y') : '';
        $wht = (float) ($order->withholding_tax_amount ?? 0);
        $siNoDisplay = $activeInvoice->si_number ?? $order->so_number;
    @endphp

    <div class="invoice-box">
        <div>
            <!-- Header -->
            <div class="header-section">
                <div class="header-logo-details">
                    <img src="{{ asset('images/claeritian_logo.png') }}" alt="Logo" class="header-logo" onerror="this.src='https://via.placeholder.com/65?text=C'">
                    <div>
                        <h1 class="company-name">Claretian Communications Foundation, Inc.</h1>
                        <div class="company-subtitle">Non-Vat Reg. TIN: 000-395-713-00000</div>
                        <div class="company-address">8 Mayumi Street, U.P. Village, Diliman, 1101 Quezon City NCR, Second District Philippines</div>
                        <div class="company-contact">Tel: (02) 8921-3984 Fax: (02) 8921-6205</div>
                    </div>
                </div>
                <div class="header-right">
                    <div class="doc-no"><span class="doc-no-label">No. </span><span>{{ $siNoDisplay }}</span></div>
                    <div class="doc-title">Sales - Invoice</div>
                    @if(isset($halfLabel) && $halfLabel)
                        <div style="font-size: 8pt; color: #666; font-weight: bold;">{{ $halfLabel }}</div>
                    @endif
                </div>
            </div>

            <!-- Customer & Transaction Details -->
            <table class="info-grid">
                <tr>
                    <td class="info-label">Sold to:</td>
                    <td class="info-value-line" style="width: 55%;">{{ $custName }}</td>
                    <td class="info-label" style="padding-left: 15px;">Date:</td>
                    <td class="info-value-line">{{ $orderDate }}</td>
                </tr>
                <tr>
                    <td class="info-label">Address:</td>
                    <td class="info-value-line">{{ $custAddress }}</td>
                    <td class="info-label" style="padding-left: 15px;">Terms:</td>
                    <td class="info-value-line">{{ $termsVal }}</td>
                </tr>
                <tr>
                    <td class="info-label">TIN:</td>
                    <td class="info-value-line">{{ $custTin }}</td>
                    <td class="info-label" style="padding-left: 15px;">Due Date:</td>
                    <td class="info-value-line">{{ $dueDate }}</td>
                </tr>
            </table>

            <!-- Items Table -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 10%; text-align: center;">QTY</th>
                        <th style="width: 50%;">DESCRIPTION</th>
                        <th style="width: 12%; text-align: center;">AREA</th>
                        <th style="width: 14%; text-align: right;">UNIT PRICE</th>
                        <th style="width: 14%; text-align: right;">AMOUNT</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($itemsToPrint as $item)
                        @php
                            if ($item->bookIndex) {
                                $desc = $item->bookIndex->display_name;
                            } elseif ($item->bundle) {
                                $desc = '[BUNDLE] ' . $item->bundle->name;
                            } else {
                                $desc = $item->book?->name ?? ($item->product_name ?? 'Product Item');
                            }
                            $qty = (float) $item->quantity;
                            $price = (float) ($item->unit_price ?? $item->price);
                            $subtotal = (float) ($item->amount ?? ($item->subtotal > 0 ? $item->subtotal : ($qty * $price)));
                        @endphp
                        <tr>
                            <td style="text-align: center; font-weight: bold;">{{ $qty }}</td>
                            <td style="font-weight: 600;">{{ $desc }}</td>
                            <td style="text-align: center;">{{ ($item->area && $item->area !== 'N/A' && $item->area !== '-') ? $item->area : '' }}</td>
                            <td style="text-align: right;">₱{{ number_format($price, 2) }}</td>
                            <td style="text-align: right; font-weight: bold;">₱{{ number_format($subtotal, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #777;">No line items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            <!-- Payment Type Checkbox & Total Sales -->
            <div class="payment-sales-row">
                <div class="checkbox-group">
                    <div class="custom-cb">
                        <span class="cb-box">{{ $isCash ? '✓' : '' }}</span>
                        <span class="cb-text">CASH</span>
                    </div>
                    <div class="custom-cb">
                        <span class="cb-box">{{ !$isCash ? '✓' : '' }}</span>
                        <span class="cb-text">CHARGE</span>
                    </div>
                </div>
                <div class="totals-block text-end">
                    @if($wht > 0)
                        <div style="font-size: 8.5pt; font-weight: bold; margin-bottom: 2px;">
                            <span class="total-label">LESS: WITHHOLDING TAX: </span><span style="border-bottom: 1.5px solid #000; padding: 0 8px;">₱{{ number_format($wht, 2) }}</span>
                        </div>
                        <div style="font-size: 9pt; font-weight: bold; margin-bottom: 2px;">
                            <span class="total-label">TOTAL SALES: </span><span style="border-bottom: 1.5px solid #000; padding: 0 8px;">₱{{ number_format($totalSalesAmount, 2) }}</span>
                        </div>
                        <div class="total-sales-box" style="margin-top: 3px;">
                            <span class="total-label">TOTAL AMOUNT DUE: </span><span>₱{{ number_format(max(0, $totalSalesAmount - $wht), 2) }}</span>
                        </div>
                    @else
                        <div class="total-sales-box">
                            <span class="total-label">TOTAL SALES: </span><span>₱{{ number_format($totalSalesAmount, 2) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Conditions & Bank Accounts -->
            <div class="conditions-bank-container">
                <div class="conditions-block">
                    <strong>CONDITIONS:</strong> Parties submit themselves to the jurisdiction of the courts of Quezon City in any legal action arising from this transaction. Interest of 12% per annum is charged on all overdue accounts plus cost of collection and attorney's fees. Received merchandise in good order and condition.
                </div>
                <div class="bank-block">
                    <strong>Payments may be deposit thru the following bank accounts:</strong><br>
                    <span>RCBC - SA# 1-191-46138-6</span> &nbsp;&nbsp;&nbsp; <span>Metrobank SA# 186-3-18617805-0</span><br>
                    <span>BDO - SA# 3640009449</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span>BPI SA# 1993-0734-11</span>
                </div>
            </div>

            <!-- Signatories -->
            <div class="signatories-row">
                <div class="sig-col">
                    <div class="sig-label">Prepared by:</div>
                    <div class="sig-line">{{ $order->preparedBy?->name ?? '' }}</div>
                </div>
                <div class="sig-col">
                    <div class="sig-label">Approved by:</div>
                    <div class="sig-line">{{ $order->mktApprovedBy?->name ?? ($order->prodApprovedBy?->name ?? '') }}</div>
                </div>
                <div class="sig-col">
                    <div class="sig-label">Received by:</div>
                    <div class="sig-line">{{ $custName }}</div>
                </div>
            </div>

            <!-- Footer Notice -->
            <div class="footer-notice">
                <div class="bir-details">
                    <div>200 Pads (50x4) 60751-70750 | BIR Authority to Print No. OCN033AU20250000000745 (Date of ATP: 01-28-2026)</div>
                    <div>Looseleaf Permit No. LLAR-039-1022-00083 | Date Issued: October 03, 2022</div>
                    <div>TOPAZ PUBLISHING HAUS CO. | Tel: 822-3443 | Cell: 0945-548-2022 | 63-A Matahimik St., Teachers Village, Diliman Q.C.</div>
                    <div>NON VAT Reg. TIN: 004-720-224-00000 | Printer's Accreditation No. 039MP20240000000003 (Valid: Feb 05, 2024 - Feb 05, 2029)</div>
                </div>
                <div class="input-tax-notice" style="font-weight: bold; font-size: 8pt; color: #000; text-align: center; text-decoration: underline; margin-top: 4px;">
                    *THIS DOCUMENT IS NOT VALID FOR CLAIM OF INPUT TAXES*
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePreprintedMode(checkbox) {
            if (checkbox.checked) {
                document.body.classList.add('preprinted-mode');
            } else {
                document.body.classList.remove('preprinted-mode');
            }
        }
    </script>

</body>
</html>

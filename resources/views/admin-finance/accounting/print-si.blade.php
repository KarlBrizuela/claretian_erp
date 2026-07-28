<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Invoice - {{ $order->so_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            padding: 10px;
        }

        .invoice-container {
            background: #fff;
            max-width: 8.5in;
            height: 11in;
            margin: 0 auto;
            padding: 0.4in;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            page-break-after: avoid;
            display: flex;
            flex-direction: column;
        }

        .invoice-header {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin-bottom: 0.6rem;
            padding-bottom: 0.6rem;
            border-bottom: 2px solid #ff0000;
        }

        .company-logo {
            width: 50px;
            height: 50px;
            background: #ff0000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.8rem;
            font-weight: bold;
            flex-shrink: 0;
        }

        .company-info h1 {
            font-size: 0.85rem;
            font-weight: 700;
            color: #333;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .company-info p {
            margin: 0.1rem 0;
            color: #666;
            font-size: 0.7rem;
        }

        .invoice-title {
            text-align: center;
            margin: 0.4rem 0;
        }

        .invoice-title h2 {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
            letter-spacing: 1px;
            margin-bottom: 0.2rem;
        }

        .invoice-title p {
            color: #666;
            font-size: 0.7rem;
            margin: 0;
        }

        .invoice-number {
            text-align: center;
            font-size: 1rem;
            font-weight: 700;
            color: #ff0000;
            margin: 0.4rem 0;
            border-bottom: 1px solid #ddd;
            padding-bottom: 0.4rem;
        }

        .invoice-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.8rem;
            margin-bottom: 0.8rem;
        }

        .detail-section {
            background: #f8f9fa;
            padding: 0.6rem;
            border-radius: 4px;
        }

        .detail-section h5 {
            font-weight: 700;
            color: #333;
            margin-bottom: 0.4rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .detail-item {
            margin-bottom: 0.35rem;
            line-height: 1.2;
        }

        .detail-item label {
            font-weight: 600;
            color: #333;
            display: block;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .detail-item p {
            color: #555;
            margin: 0.1rem 0 0 0;
            font-size: 0.75rem;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0.6rem 0;
            font-size: 0.75rem;
            flex-grow: 1;
        }

        .items-table thead {
            background: #ff0000;
            color: #fff;
        }

        .items-table th {
            padding: 0.4rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: 1px solid #ff0000;
        }

        .items-table td {
            padding: 0.3rem 0.4rem;
            border: 1px solid #ddd;
            color: #333;
            font-size: 0.73rem;
        }

        .items-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .items-table .text-right {
            text-align: right;
        }

        .items-table tfoot tr {
            background-color: #f5f5f5;
            font-weight: 600;
        }

        .items-table tfoot th,
        .items-table tfoot td {
            padding: 0.4rem;
            border: 1px solid #ddd;
            text-align: right;
            font-size: 0.73rem;
        }

        .tax-notice {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 0.5rem;
            border-radius: 3px;
            margin: 0.4rem 0;
            font-size: 0.7rem;
            color: #333;
            text-align: center;
        }

        .signature-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 0.6rem;
            padding-top: 0.6rem;
            border-top: 1px solid #e0e0e0;
        }

        .signature-block {
            text-align: center;
        }

        .signature-block h6 {
            font-weight: 700;
            color: #333;
            margin-bottom: 1.2rem;
            font-size: 0.7rem;
            text-transform: uppercase;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-bottom: 0.3rem;
            height: 25px;
        }

        .signature-name {
            font-size: 0.65rem;
            color: #666;
            font-weight: 600;
        }

        .print-button {
            text-align: center;
            margin-top: 0.6rem;
            padding-top: 0.6rem;
            border-top: 1px solid #e0e0e0;
        }

        .print-button button {
            background: #ff0000;
            color: #fff;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 3px;
            font-weight: 600;
            font-size: 0.8rem;
            cursor: pointer;
            transition: background 0.3s;
        }

        .print-button button:hover {
            background: #ff3333;
        }

        @media print {
            body {
                background-color: #fff;
                padding: 0;
                margin: 0;
            }

            .invoice-container {
                box-shadow: none;
                padding: 0.3in;
                margin: 0;
                max-width: 100%;
                height: auto;
                page-break-after: avoid;
            }

            .print-button {
                display: none;
            }

            @page {
                margin: 0.3in;
                size: A4;
            }
        }

        /* Single page layout for printing */
        .single-page-invoice {
            page-break-after: avoid;
        }
    </style>
</head>
<body>
    <div class="invoice-container single-page-invoice">
        <!-- Header -->
        <div class="invoice-header">
            <div class="company-logo">C</div>
            <div class="company-info">
                <h1>Claretian Communications Foundation Inc.</h1>
                <p>8 Mayumi St., UP Village, Diliman, Quezon City</p>
                <p>Tel. No.: 921-3984</p>
            </div>
        </div>

        <!-- Invoice Title -->
        <div class="invoice-title">
            <h2>SALES INVOICE</h2>
            <p>NON-VAT REGISTERED</p>
            <p style="font-size: 0.85rem; font-style: italic; color: #999;">
                "This document is not valid for claim of input taxes."
            </p>
        </div>

        <!-- Invoice Number -->
        <div class="invoice-number">
            SI-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
        </div>

        <!-- Details Section -->
        <div class="invoice-details">
            <div class="detail-section">
                <h5>Customer Information</h5>
                <div class="detail-item">
                    <label>Sold To:</label>
                    <p>{{ $order->customer->customer_name ?? 'N/A' }}</p>
                </div>
                <div class="detail-item">
                    <label>Address:</label>
                    <p>{{ $order->billing_address ?? ($order->customer->address ?? 'N/A') }}</p>
                </div>
                <div class="detail-item">
                    <label>TIN:</label>
                    <p>{{ $order->customer->tin ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="detail-section">
                <h5>Transaction Details</h5>
                <div class="detail-item">
                    <label>Date:</label>
                    <p>{{ now()->format('F d, Y') }}</p>
                </div>
                <div class="detail-item">
                    <label>Terms:</label>
                    <p>{{ $order->terms ?? 'N/A' }}</p>
                </div>
                <div class="detail-item">
                    <label>Reference:</label>
                    <p>SO #{{ $order->so_number }}</p>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 80px;">QTY</th>
                    <th>DESCRIPTION</th>
                    <th style="width: 120px;">AREA</th>
                    <th style="width: 140px; text-align: right;">UNIT PRICE</th>
                    <th style="width: 140px; text-align: right;">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                @forelse($order->items as $item)
                <tr>
                    <td class="text-center">{{ $item->quantity }} {{ $item->unit ?? 'pcs' }}</td>
                    <td>{{ $item->product?->name ?? $item->book?->name ?? $item->bundle?->name ?? 'Unknown Product' }}</td>
                    <td>{{ $item->area ?? '-' }}</td>
                    <td class="text-right">₱{{ number_format($item->price, 2) }}</td>
                    <td class="text-right">₱{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">No items</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4">TOTAL AMOUNT DUE</th>
                    <th class="text-right">₱{{ number_format($order->total_amount, 2) }}</th>
                </tr>
            </tfoot>
        </table>

        <!-- Tax Notice -->
        <div class="tax-notice">
            <strong>NON-VAT REGISTERED ESTABLISHMENT</strong><br>
            This document is not valid for claim of input taxes.
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-block">
                <h6>Prepared By</h6>
                <div class="signature-line"></div>
                <div class="signature-name">
                    {{ $order->siPreparedBy->name ?? 'Staff Name' }}
                </div>
                <small>Accounting Staff</small>
            </div>
            <div class="signature-block">
                <h6>Manager Signature</h6>
                <div class="signature-line"></div>
                <div class="signature-name">
                    {{ $order->signedBy->name ?? 'Manager Name' }}
                </div>
                <small>Admin & Finance Manager</small>
            </div>
        </div>

        <!-- Print Button -->
        <div class="print-button">
            <button type="button" onclick="window.print()">
                <i class="las la-print"></i> Print Sales Invoice
            </button>
        </div>
    </div>
</body>
</html>

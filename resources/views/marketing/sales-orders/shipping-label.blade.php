<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> </title>
    <style>
        @page {
            size: letter; /* 8.5in x 11in */
            margin: 0 !important;
        }
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            width: 8.5in;
            height: 11in;
            overflow: hidden;
            font-family: 'Inter', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #fff;
            color: #1a1a1a;
        }
        .container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            width: 8.5in;
            height: 11in;
            box-sizing: border-box;
            padding: 0.25in;
            gap: 0.2in;
        }
        .label {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 0.25in;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
            background-color: #fff;
            position: relative;
            overflow: hidden;
        }
        /* Color Accent */
        .label::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #cc0000, #ff0000);
        }
        .header {
            display: flex;
            align-items: center;
            gap: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .logo-img {
            width: 50px;
            height: auto;
            flex-shrink: 0;
        }
        .company-info-block {
            flex-grow: 1;
        }
        .company-name {
            font-size: 11pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            color: #cc0000;
        }
        .company-sub {
            font-size: 8pt;
            margin: 1px 0;
            color: #666;
            line-height: 1.2;
        }
        .body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .section-title {
            font-size: 8pt;
            font-weight: 700;
            color: #999;
            text-transform: uppercase;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .section-title::after {
            content: "";
            flex-grow: 1;
            height: 1px;
            background: #f0f0f0;
        }
        .buyer-name {
            font-size: 16pt;
            font-weight: 900;
            margin-bottom: 12px;
            text-transform: uppercase;
            line-height: 1.1;
            color: #000;
        }
        .buyer-address {
            font-size: 11pt;
            margin-bottom: 15px;
            line-height: 1.4;
            font-weight: 500;
            color: #333;
            min-height: 3em;
        }
        .contact-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            background: #f8f9fa;
            padding: 8px 12px;
            border-radius: 6px;
        }
        .buyer-contact {
            font-size: 11pt;
            font-weight: 700;
            color: #000;
        }
        .website-label {
            font-size: 8pt;
            font-weight: 600;
            color: #cc0000;
            text-transform: lowercase;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #eee;
        }
        .order-ref {
            font-size: 9pt;
            font-weight: 600;
            color: #999;
        }
        .no-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .no-text {
            font-size: 12pt;
            font-weight: 800;
            color: #333;
        }
        .no-box {
            border: 2px solid #333;
            border-radius: 4px;
            width: 60px;
            height: 30px;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            .label {
                border-color: #000; /* Darker border for printing */
            }
            body {
                background: none;
            }
        }
        
        .no-print-header {
            background: #cc0000;
            color: white;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: sans-serif;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .btn-print {
            background: white;
            color: #cc0000;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 12px;
            transition: all 0.2s;
        }
        .btn-print:hover {
            background: #f0f0f0;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="no-print-header no-print">
        <div style="display:flex; align-items:center; gap:10px;">
            <img src="{{ asset('images/claeritian_logo.png') }}" style="height:30px; background:white; padding:2px; border-radius:4px;">
            <strong>Shipping Label Preview (2x2)</strong>
        </div>
        <button class="btn-print" onclick="window.print()">Print Labels Now</button>
    </div>

    <div class="container">
        @for($i = 0; $i < 4; $i++)
        <div class="label">
            <div class="header">
                <img src="{{ asset('images/claeritian_logo.png') }}" class="logo-img">
                <div class="company-info-block">
                    <p class="company-name">Clarentian Communications</p>
                    <p class="company-sub">8 Mayumi St., UP Village, Diliman, QC</p>
                    <p class="company-sub">Mobile: 0908 886 1897</p>
                </div>
            </div>
            <div class="body">
                <div class="section-title">Ship To</div>
                <div class="buyer-name">{{ $order->customer->customer_name ?? 'N/A' }}</div>
                <div class="buyer-address">
                    {{ $address ?? ($order->shipping_address ?: ($order->customer->shipping_address ?: $order->customer->billing_address)) }}
                </div>
                
                <div class="contact-row">
                    <div class="buyer-contact">
                        {{ $order->customer->main_phone ?: $order->customer->mobile ?: 'N/A' }}
                    </div>
                    <div class="website-label">https://claretianpublications.com/</div>
                </div>
            </div>
            <div class="footer">
                <div></div> <!-- Empty left side to keep NO. on the right -->
                <div class="no-group">
                    <div class="no-text">NO.</div>
                    <div class="no-box"></div>
                </div>
            </div>
        </div>
        @endfor
    </div>
</body>
</html>

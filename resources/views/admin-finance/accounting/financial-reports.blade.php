<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .rpt-header-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.75rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            border: 0;
            margin-bottom: 1.5rem;
        }

        .hover-row {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .hover-row:hover {
            background-color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
        }

        .category-pill {
            font-size: 0.82rem;
            font-weight: 600;
            padding: 7px 15px;
            border-radius: 20px;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
            display: inline-block;
        }

        .category-pill.active {
            background-color: #D9251C;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(217, 37, 28, 0.25);
        }

        .category-pill:not(.active) {
            background-color: #f8f9fa;
            color: #495057;
            border: 1px solid #e9ecef;
        }

        .category-pill:not(.active):hover {
            background-color: #e9ecef;
            color: #212529;
        }

        .statement-table th {
            background-color: #1e293b;
            color: #fff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.78rem;
        }
    </style>
    @endpush

    <div class="container-fluid">
        <!-- Master Title Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="rpt-header-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h4 class="fs-24 mb-1 fw-bold text-dark" style="letter-spacing: -0.5px;">Financial & Profitability Reports</h4>
                        <p class="text-muted small mb-0">Automated financial statements & profitability analysis: Balance Sheet, P&L, Cash Flow, Trial Balance, General/Subsidiary Ledgers, and Profitability by Product, Customer, Branch, & Salesperson.</p>
                    </div>
                    {{-- 
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-outline-danger btn-sm px-3 rounded shadow-sm d-flex align-items-center gap-2" style="color: #D9251C; border-color: #D9251C; height: 40px;" onclick="window.print()">
                            <i class="las la-print fs-18"></i> Print Statement
                        </button>
                        <button class="btn btn-danger btn-sm px-3 text-white rounded shadow-sm d-flex align-items-center gap-2" style="background-color: #D9251C; border-color: #D9251C; height: 40px;" onclick="alert('Exporting statement to Excel / PDF...')">
                            <i class="las la-file-download fs-18"></i> Export Report
                        </button>
                    </div>
                    --}}
                </div>
            </div>
        </div>

        <!-- Financial Overview Metric Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-primary" style="width: 50px; height: 50px;">
                            <i class="las la-wallet fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Total Corporate Assets</span>
                            <h4 class="fw-bold text-primary mb-0">₱{{ number_format($metrics['total_assets'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-success" style="width: 50px; height: 50px;">
                            <i class="las la-hand-holding-usd fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Total Revenue & Inflows</span>
                            <h4 class="fw-bold text-success mb-0">₱{{ number_format($metrics['total_revenue'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 50px; height: 50px; color: #D9251C;">
                            <i class="las la-file-invoice-dollar fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Total Expenses & Outflows</span>
                            <h4 class="fw-bold text-dark mb-0">₱{{ number_format($metrics['total_expenses'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 50px; height: 50px; color: #D9251C;">
                            <i class="las la-chart-pie fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Net Operating Profit</span>
                            <h4 class="fw-bold mb-0" style="color: #D9251C;">₱{{ number_format($metrics['net_profit'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 13 Report Categories Filter Pills -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm p-3" style="border-radius: 12px; background: #fff;">
                    <span class="text-muted small fw-bold mb-2 d-block text-uppercase">13 Automated Financial & Profitability Reports:</span>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($reportsList as $rpt)
                        <a href="{{ route('admin-finance.financial-reports.index', ['report' => $rpt, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="category-pill {{ $selectedReport == $rpt ? 'active' : '' }}">
                            {{ $rpt }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Statement & Report View Render Container -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold text-dark fs-18">
                                <i class="las la-file-alt me-2" style="color: #D9251C;"></i>{{ $selectedReport }} Statement
                            </h5>
                            <p class="text-muted small mb-0">Automatically generated from General Ledger & Sales module transactions</p>
                        </div>
                        <form action="{{ route('admin-finance.financial-reports.index') }}" method="GET" class="d-flex gap-2 align-items-center">
                            <input type="hidden" name="report" value="{{ $selectedReport }}">
                            <span class="text-muted small me-1">Period:</span>
                            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
                            <span class="text-muted small">to</span>
                            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
                            <button type="submit" class="btn btn-sm text-white px-3" style="background-color: #D9251C; border-color: #D9251C;">Generate</button>
                        </form>
                    </div>

                    <div class="card-body pt-3">
                        @if($selectedReport === 'Balance Sheet')
                        <!-- 1. BALANCE SHEET -->
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold text-uppercase border-bottom pb-2" style="color: #D9251C;">Assets (Current & Non-Current)</h6>
                                <table class="table table-bordered table-sm align-middle statement-table">
                                    <thead>
                                        <tr>
                                            <th>Current Assets Account</th>
                                            <th class="text-end">Balance (₱)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reportData['current_assets'] as $ca)
                                        <tr>
                                            <td>{{ $ca['account'] }}</td>
                                            <td class="text-end fw-bold">₱{{ number_format($ca['amount'], 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <table class="table table-bordered table-sm align-middle statement-table">
                                    <thead>
                                        <tr>
                                            <th>Non-Current Assets Account</th>
                                            <th class="text-end">Balance (₱)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reportData['non_current_assets'] as $nca)
                                        <tr>
                                            <td>{{ $nca['account'] }}</td>
                                            <td class="text-end fw-bold">₱{{ number_format($nca['amount'], 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <h6 class="fw-bold text-uppercase border-bottom pb-2 text-dark">Liabilities & Equity</h6>
                                <table class="table table-bordered table-sm align-middle statement-table">
                                    <thead>
                                        <tr>
                                            <th>Liabilities Account</th>
                                            <th class="text-end">Balance (₱)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reportData['liabilities'] as $liab)
                                        <tr>
                                            <td>{{ $liab['account'] }}</td>
                                            <td class="text-end fw-bold text-danger">₱{{ number_format($liab['amount'], 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <table class="table table-bordered table-sm align-middle statement-table">
                                    <thead>
                                        <tr>
                                            <th>Equity Account</th>
                                            <th class="text-end">Balance (₱)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reportData['equity'] as $eq)
                                        <tr>
                                            <td>{{ $eq['account'] }}</td>
                                            <td class="text-end fw-bold text-success">₱{{ number_format($eq['amount'], 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @elseif($selectedReport === 'Cash Flow')
                        <!-- CASH FLOW STATEMENT -->
                        <div class="row g-4">
                            <div class="col-md-8 mx-auto">
                                <div class="bg-white p-4 border rounded shadow-sm">
                                    <h5 class="fw-bold text-center text-uppercase mb-4 pb-2 border-bottom text-dark">
                                        Statement of Cash Flows
                                    </h5>
                                    
                                    <!-- 1. OPERATING ACTIVITIES -->
                                    <h6 class="fw-bold text-uppercase text-primary mb-2">Cash Flows from Operating Activities</h6>
                                    <table class="table table-sm statement-table align-middle mb-3">
                                        <tbody>
                                            @php $netOps = 0; @endphp
                                            @foreach($reportData['operating'] as $op)
                                            @php $netOps += $op['amount']; @endphp
                                            <tr>
                                                <td class="ps-3 text-dark fs-14">{{ $op['category'] }}</td>
                                                <td class="text-end fw-bold fs-14 @if($op['amount'] < 0) text-danger @else text-dark @endif">
                                                    @if($op['amount'] < 0)
                                                    (₱{{ number_format(abs($op['amount']), 2) }})
                                                    @else
                                                    ₱{{ number_format($op['amount'], 2) }}
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                            <tr class="table-light">
                                                <td class="fw-bold text-uppercase text-dark fs-14">Net Cash from Operating Activities</td>
                                                <td class="text-end fw-bold border-top text-primary fs-14">
                                                    @if($netOps < 0)
                                                    (₱{{ number_format(abs($netOps), 2) }})
                                                    @else
                                                    ₱{{ number_format($netOps, 2) }}
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- 2. INVESTING ACTIVITIES -->
                                    <h6 class="fw-bold text-uppercase text-warning mb-2">Cash Flows from Investing Activities</h6>
                                    <table class="table table-sm statement-table align-middle mb-3">
                                        <tbody>
                                            @php $netInv = 0; @endphp
                                            @foreach($reportData['investing'] as $inv)
                                            @php $netInv += $inv['amount']; @endphp
                                            <tr>
                                                <td class="ps-3 text-dark fs-14">{{ $inv['category'] }}</td>
                                                <td class="text-end fw-bold fs-14 @if($inv['amount'] < 0) text-danger @else text-dark @endif">
                                                    @if($inv['amount'] < 0)
                                                    (₱{{ number_format(abs($inv['amount']), 2) }})
                                                    @else
                                                    ₱{{ number_format($inv['amount'], 2) }}
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                            <tr class="table-light">
                                                <td class="fw-bold text-uppercase text-dark fs-14">Net Cash from Investing Activities</td>
                                                <td class="text-end fw-bold border-top text-warning fs-14">
                                                    @if($netInv < 0)
                                                    (₱{{ number_format(abs($netInv), 2) }})
                                                    @else
                                                    ₱{{ number_format($netInv, 2) }}
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- 3. FINANCING ACTIVITIES -->
                                    <h6 class="fw-bold text-uppercase text-info mb-2">Cash Flows from Financing Activities</h6>
                                    <table class="table table-sm statement-table align-middle mb-3">
                                        <tbody>
                                            @php $netFin = 0; @endphp
                                            @foreach($reportData['financing'] as $fin)
                                            @php $netFin += $fin['amount']; @endphp
                                            <tr>
                                                <td class="ps-3 text-dark fs-14">{{ $fin['category'] }}</td>
                                                <td class="text-end fw-bold text-dark fs-14">₱{{ number_format($fin['amount'], 2) }}</td>
                                            </tr>
                                            @endforeach
                                            <tr class="table-light">
                                                <td class="fw-bold text-uppercase text-dark fs-14">Net Cash from Financing Activities</td>
                                                <td class="text-end fw-bold border-top text-info fs-14">₱{{ number_format($netFin, 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- Summary / Cash Reconciliation -->
                                    <h6 class="fw-bold text-uppercase text-dark mb-2 border-top pt-3">Cash Reconciliation Summary</h6>
                                    <table class="table table-sm statement-table align-middle mb-2">
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold text-dark fs-14">Net Increase / (Decrease) in Cash</td>
                                                <td class="text-end fw-bold fs-14 @if($reportData['summary']['net_change'] < 0) text-danger @else text-success @endif">
                                                    @if($reportData['summary']['net_change'] < 0)
                                                    (₱{{ number_format(abs($reportData['summary']['net_change']), 2) }})
                                                    @else
                                                    ₱{{ number_format($reportData['summary']['net_change'], 2) }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-dark fs-14">Cash at Beginning of Period</td>
                                                <td class="text-end fw-bold text-dark fs-14">₱{{ number_format($reportData['summary']['beginning'], 2) }}</td>
                                            </tr>
                                            <tr class="table-dark text-white">
                                                <td class="fw-bold text-uppercase fs-14">Cash at End of Period</td>
                                                <td class="text-end fw-bold fs-14 text-success">₱{{ number_format($reportData['summary']['ending'], 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @elseif($selectedReport === 'Income Statement')
                        <!-- INCOME STATEMENT -->
                        <div class="row g-4">
                            <div class="col-md-8 mx-auto">
                                <div class="bg-white p-4 border rounded shadow-sm">
                                    <h5 class="fw-bold text-center text-uppercase mb-4 pb-2 border-bottom text-dark">
                                        Income Statement
                                    </h5>
                                    
                                    <!-- 1. REVENUE -->
                                    <h6 class="fw-bold text-uppercase text-primary mb-2">Revenue</h6>
                                    <table class="table table-sm statement-table align-middle mb-4">
                                        <tbody>
                                            @php $totalRev = 0; @endphp
                                            @foreach($reportData['revenue'] as $rev)
                                            @php $totalRev += $rev['amount']; @endphp
                                            <tr>
                                                <td class="ps-3 text-dark fs-14">{{ $rev['category'] }}</td>
                                                <td class="text-end fw-bold text-dark fs-14">₱{{ number_format($rev['amount'], 2) }}</td>
                                            </tr>
                                            @endforeach
                                            <tr class="table-light">
                                                <td class="fw-bold text-uppercase text-dark fs-14">Total Revenue</td>
                                                <td class="text-end fw-bold border-top text-primary fs-14">₱{{ number_format($totalRev, 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- 2. COGS -->
                                    <h6 class="fw-bold text-uppercase text-warning mb-2">Cost of Goods Sold (COGS)</h6>
                                    <table class="table table-sm statement-table align-middle mb-4">
                                        <tbody>
                                            @php $totalCogs = 0; @endphp
                                            @foreach($reportData['cogs'] as $cogs)
                                            @php $totalCogs += $cogs['amount']; @endphp
                                            <tr>
                                                <td class="ps-3 text-dark fs-14">{{ $cogs['category'] }}</td>
                                                <td class="text-end fw-bold text-danger fs-14">₱{{ number_format($cogs['amount'], 2) }}</td>
                                            </tr>
                                            @endforeach
                                            <tr class="table-light">
                                                <td class="fw-bold text-uppercase text-dark fs-14">Total Cost of Goods Sold</td>
                                                <td class="text-end fw-bold border-top text-danger fs-14">₱{{ number_format($totalCogs, 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- Gross Profit -->
                                    @php $grossProfit = $totalRev - $totalCogs; @endphp
                                    <div class="d-flex justify-content-between align-items-center p-2 mb-4 bg-light border rounded">
                                        <span class="fw-bold text-uppercase fs-15 text-dark">Gross Profit</span>
                                        <span class="fw-bold fs-15 text-success">₱{{ number_format($grossProfit, 2) }}</span>
                                    </div>

                                    <!-- 3. OPERATING EXPENSES -->
                                    <h6 class="fw-bold text-uppercase text-danger mb-2">Operating Expenses</h6>
                                    <table class="table table-sm statement-table align-middle mb-4">
                                        <tbody>
                                            @php $totalOpex = 0; @endphp
                                            @foreach($reportData['operating_expenses'] as $opex)
                                            @php $totalOpex += $opex['amount']; @endphp
                                            <tr>
                                                <td class="ps-3 text-dark fs-14">{{ $opex['category'] }}</td>
                                                <td class="text-end fw-bold text-danger fs-14">₱{{ number_format($opex['amount'], 2) }}</td>
                                            </tr>
                                            @endforeach
                                            <tr class="table-light">
                                                <td class="fw-bold text-uppercase text-dark fs-14">Total Operating Expenses</td>
                                                <td class="text-end fw-bold border-top text-danger fs-14">₱{{ number_format($totalOpex, 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- Net Income -->
                                    @php $netIncome = $grossProfit - $totalOpex; @endphp
                                    <div class="d-flex justify-content-between align-items-center p-3 mb-2 bg-dark text-white rounded">
                                        <span class="fw-bold text-uppercase fs-16">Net Income / (Loss)</span>
                                        <span class="fw-bold fs-16 text-success">₱{{ number_format($netIncome, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @elseif($selectedReport === 'Profit by Product')
                        <!-- 2. PROFIT BY PRODUCT -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-dark text-white small text-uppercase">
                                    <tr>
                                        <th>SKU</th>
                                        <th>Product / Book Title</th>
                                        <th class="text-center">Units Sold</th>
                                        <th class="text-end">Gross Revenue</th>
                                        <th class="text-end">COGS</th>
                                        <th class="text-end" style="background-color: #D9251C;">Net Profit</th>
                                        <th class="text-center">Profit Margin %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reportData as $row)
                                    <tr class="hover-row">
                                        <td><span class="fw-bold text-dark font-monospace">{{ $row['sku'] }}</span></td>
                                        <td><span class="fw-bold text-dark fs-14">{{ $row['name'] }}</span></td>
                                        <td class="text-center fw-bold text-dark">{{ number_format($row['sales_qty']) }}</td>
                                        <td class="text-end fw-bold text-success">₱{{ number_format($row['revenue'], 2) }}</td>
                                        <td class="text-end text-muted">₱{{ number_format($row['cogs'], 2) }}</td>
                                        <td class="text-end fw-bold text-white" style="background-color: #D9251C;">
                                            ₱{{ number_format($row['profit'], 2) }}
                                        </td>
                                        <td class="text-center fw-bold text-dark">{{ $row['margin_pct'] }}%</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @elseif($selectedReport === 'Profit by Sales Channel')
                        <!-- 3. PROFIT BY SALES CHANNEL -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-dark text-white small text-uppercase">
                                    <tr>
                                        <th>Sales Channel</th>
                                        <th class="text-end">Revenue</th>
                                        <th class="text-end">Expenses</th>
                                        <th class="text-end" style="background-color: #D9251C;">Net Profit</th>
                                        <th class="text-center">Operating Margin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reportData as $ch)
                                    <tr class="hover-row">
                                        <td><span class="fw-bold text-dark fs-14">{{ $ch['channel'] }}</span></td>
                                        <td class="text-end fw-bold text-success">₱{{ number_format($ch['revenue'], 2) }}</td>
                                        <td class="text-end text-muted">₱{{ number_format($ch['expenses'], 2) }}</td>
                                        <td class="text-end fw-bold text-white" style="background-color: #D9251C;">
                                            ₱{{ number_format($ch['profit'], 2) }}
                                        </td>
                                        <td class="text-center fw-bold text-dark">{{ $ch['margin'] }}%</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @elseif($selectedReport === 'Profit by Customer')
                        <!-- 4. PROFIT BY CUSTOMER -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-dark text-white small text-uppercase">
                                    <tr>
                                        <th>Customer / Account Name</th>
                                        <th>Customer Type</th>
                                        <th class="text-end">Customer Sales Revenue</th>
                                        <th class="text-end">Direct Cost</th>
                                        <th class="text-end" style="background-color: #D9251C;">Net Profit Contribution</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reportData as $cust)
                                    <tr class="hover-row">
                                        <td><span class="fw-bold text-dark fs-14">{{ $cust['customer'] }}</span></td>
                                        <td><span class="badge bg-light text-dark border">{{ $cust['type'] }}</span></td>
                                        <td class="text-end fw-bold text-success">₱{{ number_format($cust['revenue'], 2) }}</td>
                                        <td class="text-end text-muted">₱{{ number_format($cust['cost'], 2) }}</td>
                                        <td class="text-end fw-bold text-white" style="background-color: #D9251C;">
                                            ₱{{ number_format($cust['net_profit'], 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @elseif($selectedReport === 'Sales Reports')
                        <!-- SALES REPORTS TABLE -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-dark text-white small text-uppercase">
                                    <tr>
                                        <th>Date</th>
                                        <th>Order Number</th>
                                        <th>Customer / Payee</th>
                                        <th>Sales Channel</th>
                                        <th>Status</th>
                                        <th class="text-end" style="background-color: #D9251C;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $grandTotal = 0; @endphp
                                    @forelse($reportData as $sale)
                                    @php $grandTotal += $sale->effective_amount; @endphp
                                    <tr class="hover-row">
                                        <td><span class="text-dark">{{ \Carbon\Carbon::parse($sale->effective_date)->format('M d, Y g:i A') }}</span></td>
                                        <td><span class="fw-bold text-dark">{{ $sale->so_number }}</span></td>
                                        <td><span class="text-dark">{{ $sale->customer->customer_name ?? 'Walk-In Customer' }}</span></td>
                                        <td>
                                            @php
                                                $channelDisplay = 'Standard SO';
                                                if ($sale->type === 'calculator_pos') $channelDisplay = 'Direct POS';
                                                elseif ($sale->type === 'ecom_direct') $channelDisplay = 'E-Com Direct';
                                                elseif ($sale->type === 'area_sales_consignment') $channelDisplay = 'NBS Consignment';
                                            @endphp
                                            <span class="badge bg-light text-dark border">{{ $channelDisplay }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = 'bg-warning text-dark';
                                                if ($sale->status === 'completed') $statusClass = 'bg-success text-white';
                                                elseif ($sale->status === 'cancelled') $statusClass = 'bg-danger text-white';
                                            @endphp
                                            <span class="badge {{ $statusClass }}">{{ strtoupper(str_replace('_', ' ', $sale->status)) }}</span>
                                        </td>
                                        <td class="text-end fw-bold text-dark">₱{{ number_format($sale->effective_amount, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No sales transactions found in the selected period.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                @if($reportData->isNotEmpty())
                                <tfoot>
                                    <tr class="table-dark text-white fw-bold">
                                        <td colspan="5" class="text-uppercase text-end">Total Sales</td>
                                        <td class="text-end" style="background-color: #D9251C;">₱{{ number_format($grandTotal, 2) }}</td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>

                        @elseif($selectedReport === 'Expense Reports')
                        <!-- EXPENSE REPORTS TABLE -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-dark text-white small text-uppercase">
                                    <tr>
                                        <th>Date</th>
                                        <th>Expense Title / Description</th>
                                        <th>Charged Department</th>
                                        <th>Added By</th>
                                        <th class="text-end" style="background-color: #D9251C;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $grandExpenseTotal = 0; @endphp
                                    @forelse($reportData as $exp)
                                    @php $grandExpenseTotal += $exp->amount; @endphp
                                    <tr class="hover-row">
                                        <td><span class="text-dark">{{ \Carbon\Carbon::parse($exp->expense_date)->format('M d, Y') }}</span></td>
                                        <td>
                                            <span class="fw-bold text-dark fs-14">{{ $exp->title }}</span>
                                            @if($exp->notes)
                                            <small class="text-muted d-block">{{ $exp->notes }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $exp->department->name ?? 'General Admin' }}</span>
                                        </td>
                                        <td><span class="text-dark">{{ $exp->added_by ?: 'System' }}</span></td>
                                        <td class="text-end fw-bold text-danger">₱{{ number_format($exp->amount, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No expense transactions found in the selected period.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                @if($reportData->isNotEmpty())
                                <tfoot>
                                    <tr class="table-dark text-white fw-bold">
                                        <td colspan="4" class="text-uppercase text-end">Total Expenses</td>
                                        <td class="text-end" style="background-color: #D9251C;">₱{{ number_format($grandExpenseTotal, 2) }}</td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>

                        @elseif($selectedReport === 'Profit by Salesperson')
                        <!-- 5. PROFIT BY SALESPERSON -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-dark text-white small text-uppercase">
                                    <tr>
                                        <th>Sales Executive Name</th>
                                        <th>Assigned Sales Territory</th>
                                        <th class="text-end">Sales</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reportData as $sp)
                                    <tr class="hover-row">
                                        <td><span class="fw-bold text-dark fs-14">{{ $sp['salesperson'] }}</span></td>
                                        <td><span class="badge bg-light text-dark border">{{ $sp['territory'] }}</span></td>
                                        <td class="text-end fw-bold text-success">₱{{ number_format($sp['achieved'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @else
                        <!-- GENERIC FINANCIAL STATEMENT VIEW (P&L, Cash Flow, Trial Balance, GL, Subsidiary, Sales, Expenses, Department) -->
                        <div class="p-4 bg-light rounded border text-center my-3">
                            <i class="las la-check-circle fs-40 text-success mb-2"></i>
                            <h5 class="fw-bold text-dark">Automated {{ $selectedReport }} Statement</h5>
                            <p class="text-muted small mb-3">Live statement compiled from General Ledger & Chart of Accounts for {{ $startDate }} to {{ $endDate }}.</p>
                            <button class="btn btn-danger btn-sm text-white px-4 fw-bold" style="background-color: #D9251C;" onclick="window.print()">
                                Print Full {{ $selectedReport }} Document
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

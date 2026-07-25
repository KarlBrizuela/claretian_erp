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
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-outline-danger btn-sm px-3 rounded shadow-sm d-flex align-items-center gap-2" style="color: #D9251C; border-color: #D9251C; height: 40px;" onclick="window.print()">
                            <i class="las la-print fs-18"></i> Print Statement
                        </button>
                        <button class="btn btn-danger btn-sm px-3 text-white rounded shadow-sm d-flex align-items-center gap-2" style="background-color: #D9251C; border-color: #D9251C; height: 40px;" onclick="alert('Exporting statement to Excel / PDF...')">
                            <i class="las la-file-download fs-18"></i> Export Report
                        </button>
                    </div>
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

                        @elseif($selectedReport === 'Profit by Branch')
                        <!-- 3. PROFIT BY BRANCH -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-dark text-white small text-uppercase">
                                    <tr>
                                        <th>Branch / Location Name</th>
                                        <th class="text-end">Branch Revenue</th>
                                        <th class="text-end">Branch Expenses</th>
                                        <th class="text-end" style="background-color: #D9251C;">Net Profit</th>
                                        <th class="text-center">Operating Margin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reportData as $br)
                                    <tr class="hover-row">
                                        <td><span class="fw-bold text-dark fs-14">{{ $br['branch'] }}</span></td>
                                        <td class="text-end fw-bold text-success">₱{{ number_format($br['revenue'], 2) }}</td>
                                        <td class="text-end text-muted">₱{{ number_format($br['expenses'], 2) }}</td>
                                        <td class="text-end fw-bold text-white" style="background-color: #D9251C;">
                                            ₱{{ number_format($br['profit'], 2) }}
                                        </td>
                                        <td class="text-center fw-bold text-dark">{{ $br['margin'] }}%</td>
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

                        @elseif($selectedReport === 'Profit by Salesperson')
                        <!-- 5. PROFIT BY SALESPERSON -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-dark text-white small text-uppercase">
                                    <tr>
                                        <th>Sales Executive Name</th>
                                        <th>Assigned Sales Territory</th>
                                        <th class="text-end">Sales Quota</th>
                                        <th class="text-end">Achieved Sales</th>
                                        <th class="text-end" style="background-color: #D9251C;">Net Margin Generated</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reportData as $sp)
                                    <tr class="hover-row">
                                        <td><span class="fw-bold text-dark fs-14">{{ $sp['salesperson'] }}</span></td>
                                        <td><span class="badge bg-light text-dark border">{{ $sp['territory'] }}</span></td>
                                        <td class="text-end text-muted">₱{{ number_format($sp['quota'], 2) }}</td>
                                        <td class="text-end fw-bold text-success">₱{{ number_format($sp['achieved'], 2) }}</td>
                                        <td class="text-end fw-bold text-white" style="background-color: #D9251C;">
                                            ₱{{ number_format($sp['net_margin'], 2) }}
                                        </td>
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

<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .exe-header-card {
            background: #fff;
            border-radius: 14px;
            padding: 1.75rem;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            border: 0;
            margin-bottom: 1.5rem;
        }

        .hero-kpi-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 1.5rem;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .hero-kpi-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08) !important;
            border-color: #D9251C;
        }

        .hero-kpi-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
        }

        .exe-section-card {
            background: #fff;
            border-radius: 14px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            border: 0;
            height: 100%;
        }

        .hover-row {
            transition: all 0.2s ease-in-out;
        }

        .hover-row:hover {
            background-color: #f8fafc !important;
        }
    </style>
    @endpush

    <div class="container-fluid">
        <!-- Master Title Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="exe-header-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h4 class="fs-26 fw-bold text-dark mb-1" style="letter-spacing: -0.5px;">Executive Dashboard & Strategic Intelligence</h4>
                        <p class="text-muted small mb-0">High-level executive metrics, sales velocity, cash position, AR/AP risk control, inventory valuation, and budget performance.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-danger btn-sm px-3 text-white rounded shadow-sm d-flex align-items-center gap-2" style="background-color: #D9251C; border-color: #D9251C; height: 42px;" onclick="window.print()">
                            <i class="las la-print fs-18"></i> Print Executive Summary
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Executive System Risk Alerts Feed -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm p-3" style="border-radius: 14px; background: #fff;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold text-dark fs-15"><i class="las la-bell me-1" style="color: #D9251C;"></i>Executive Risk & Operations Alerts</span>
                        <span class="badge bg-light text-dark border">{{ count($executiveAlerts) }} System Warnings</span>
                    </div>
                    <div class="row g-2">
                        @foreach($executiveAlerts as $alt)
                        <div class="col-md-3">
                            <div class="p-2.5 rounded border border-{{ $alt['type'] }}-subtle bg-{{ $alt['type'] }}-subtle text-{{ $alt['type'] }} d-flex align-items-start gap-2">
                                <i class="las la-exclamation-circle fs-20 mt-0.5"></i>
                                <div>
                                    <strong class="d-block small">{{ $alt['title'] }}</strong>
                                    <span class="small opacity-75" style="font-size: 0.78rem;">{{ $alt['desc'] }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- 14 Hero Executive Large KPI Cards (Interactive & Drill-Down) -->
        <div class="row g-3 mb-4">
            <!-- 1. Today's Sales -->
            <div class="col-md-3" onclick="openKpiDrilldown('Today\'s Sales', '₱{{ number_format($kpis['todays_sales'], 2) }}', 'Real-time sales transactions realized today across all channels.')">
                <div class="hero-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Today's Sales</span>
                        <div class="hero-kpi-icon bg-success-subtle text-success">
                            <i class="las la-chart-line"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">₱{{ number_format($kpis['todays_sales'], 2) }}</h3>
                    <span class="text-success small fw-bold"><i class="las la-arrow-up"></i> +14.2% vs yesterday</span>
                </div>
            </div>

            <!-- 2. This Month's Revenue -->
            <div class="col-md-3" onclick="openKpiDrilldown('This Month\'s Revenue', '₱{{ number_format($kpis['this_months_revenue'], 2) }}', 'Month-to-date gross sales collected and billed.')">
                <div class="hero-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">This Month's Revenue</span>
                        <div class="hero-kpi-icon bg-primary-subtle text-primary">
                            <i class="las la-money-bill-wave"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-primary mb-1">₱{{ number_format($kpis['this_months_revenue'], 2) }}</h3>
                    <span class="text-muted small">MTD Target: ₱3,500,000</span>
                </div>
            </div>

            <!-- 3. Net Income -->
            <div class="col-md-3" onclick="openKpiDrilldown('Net Income', '₱{{ number_format($kpis['net_income'], 2) }}', 'MTD Net Operating Income after COGS and OPEX.')">
                <div class="hero-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Net Income</span>
                        <div class="hero-kpi-icon bg-success-subtle text-success">
                            <i class="las la-piggy-bank"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-success mb-1">₱{{ number_format($kpis['net_income'], 2) }}</h3>
                    <span class="text-success small fw-bold"><i class="las la-check-circle"></i> Favorable Margin</span>
                </div>
            </div>

            <!-- 4. Cash Position -->
            <div class="col-md-3" onclick="openKpiDrilldown('Cash Position', '₱{{ number_format($kpis['cash_position'], 2) }}', 'Consolidated live balances across institutional bank accounts.')">
                <div class="hero-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Cash Position</span>
                        <div class="hero-kpi-icon text-white" style="background-color: #D9251C;">
                            <i class="las la-wallet"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">₱{{ number_format($kpis['cash_position'], 2) }}</h3>
                    <span class="text-muted small">Available Liquidity</span>
                </div>
            </div>

            <!-- 5. Outstanding Receivables -->
            <div class="col-md-3" onclick="openKpiDrilldown('Outstanding Receivables', '₱{{ number_format($kpis['outstanding_receivables'], 2) }}', 'Uncollected sales invoices and client credit balances.')">
                <div class="hero-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Outstanding AR</span>
                        <div class="hero-kpi-icon bg-warning-subtle text-warning">
                            <i class="las la-hand-holding-usd"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-warning mb-1">₱{{ number_format($kpis['outstanding_receivables'], 2) }}</h3>
                    <span class="text-danger small fw-bold"><i class="las la-exclamation-triangle"></i> ₱425k Overdue</span>
                </div>
            </div>

            <!-- 6. Payables Due -->
            <div class="col-md-3" onclick="openKpiDrilldown('Payables Due', '₱{{ number_format($kpis['payables_due'], 2) }}', 'Supplier bills and operational liabilities pending disbursement.')">
                <div class="hero-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Payables Due</span>
                        <div class="hero-kpi-icon bg-danger-subtle text-danger">
                            <i class="las la-file-invoice-dollar"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-danger mb-1">₱{{ number_format($kpis['payables_due'], 2) }}</h3>
                    <span class="text-muted small">Due within 15 days</span>
                </div>
            </div>

            <!-- 7. Production Cost -->
            <div class="col-md-3" onclick="openKpiDrilldown('Production Cost', '₱{{ number_format($kpis['production_cost'], 2) }}', 'Total COGS incurred across Paper, Ink, Labor, Power, and Binding.')">
                <div class="hero-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Production Cost</span>
                        <div class="hero-kpi-icon bg-info-subtle text-info">
                            <i class="las la-industry"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">₱{{ number_format($kpis['production_cost'], 2) }}</h3>
                    <span class="text-muted small">12-Component COGS</span>
                </div>
            </div>

            <!-- 8. Inventory Value -->
            <div class="col-md-3" onclick="openKpiDrilldown('Inventory Value', '₱{{ number_format($kpis['inventory_value'], 2) }}', 'Valuation of finished books and raw material stocks across 10 sites.')">
                <div class="hero-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Inventory Value</span>
                        <div class="hero-kpi-icon bg-secondary-subtle text-secondary">
                            <i class="las la-boxes"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">₱{{ number_format($kpis['inventory_value'], 2) }}</h3>
                    <span class="text-muted small">10 Warehouse Sites</span>
                </div>
            </div>

            <!-- 9. Payroll This Month -->
            <div class="col-md-3" onclick="openKpiDrilldown('Payroll This Month', '₱{{ number_format($kpis['payroll_this_month'], 2) }}', 'Monthly personnel salary disbursements.')">
                <div class="hero-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Payroll This Month</span>
                        <div class="hero-kpi-icon bg-primary-subtle text-primary">
                            <i class="las la-users font-24"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">₱{{ number_format($kpis['payroll_this_month'], 2) }}</h3>
                    <span class="text-muted small">All Divisions</span>
                </div>
            </div>

            <!-- 10. Tax Due -->
            <div class="col-md-3" onclick="openKpiDrilldown('Tax Due', '₱{{ number_format($kpis['tax_due'], 2) }}', 'Estimated BIR withholding & statutory tax obligations.')">
                <div class="hero-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Tax Due</span>
                        <div class="hero-kpi-icon bg-dark-subtle text-dark">
                            <i class="las la-balance-scale"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">₱{{ number_format($kpis['tax_due'], 2) }}</h3>
                    <span class="text-muted small">Remittance Ready</span>
                </div>
            </div>

            <!-- 11. Donation Income -->
            <div class="col-md-3" onclick="openKpiDrilldown('Donation Income', '₱{{ number_format($kpis['donation_income'], 2) }}', 'Cash and restricted fund donor contributions.')">
                <div class="hero-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Donation Income</span>
                        <div class="hero-kpi-icon bg-success-subtle text-success">
                            <i class="las la-hand-holding-heart"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-success mb-1">₱{{ number_format($kpis['donation_income'], 2) }}</h3>
                    <span class="text-muted small">Restricted & Cash</span>
                </div>
            </div>

            <!-- 12. Forecasted Cash -->
            <div class="col-md-3" onclick="openKpiDrilldown('Forecasted Cash (30-Day)', '₱{{ number_format($kpis['forecasted_cash'], 2) }}', 'Projected 30-day liquidity position based on AR collections vs AP commitments.')">
                <div class="hero-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Forecasted Cash (30D)</span>
                        <div class="hero-kpi-icon text-white" style="background-color: #D9251C;">
                            <i class="las la-crystal-ball"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: #D9251C;">₱{{ number_format($kpis['forecasted_cash'], 2) }}</h3>
                    <span class="text-muted small">Projected Cash</span>
                </div>
            </div>
        </div>

        <!-- Strategic Rankings & Intelligence Lists (Books & Customers) -->
        <div class="row g-4 mb-4">
            <!-- 1. Top Selling Books -->
            <div class="col-md-3">
                <div class="exe-section-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0"><i class="las la-trophy text-warning me-1"></i>Top Selling Books</h6>
                        <span class="badge bg-success-subtle text-success">Best Sellers</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>Book Title</th>
                                    <th class="text-end">Sales (₱)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topSellingBooks as $bk)
                                <tr class="hover-row">
                                    <td><span class="fw-bold text-dark small d-block">{{ $bk['name'] }}</span></td>
                                    <td class="text-end fw-bold text-success small">₱{{ number_format($bk['revenue'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 2. Worst Selling Books -->
            <div class="col-md-3">
                <div class="exe-section-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0"><i class="las la-exclamation-circle text-danger me-1"></i>Worst Selling Books</h6>
                        <span class="badge bg-danger-subtle text-danger">Low Velocity</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>Book Title</th>
                                    <th class="text-end">Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($worstSellingBooks as $wb)
                                <tr class="hover-row">
                                    <td><span class="fw-bold text-dark small d-block">{{ $wb['name'] }}</span></td>
                                    <td class="text-end text-muted small">{{ number_format($wb['stock_remaining']) }} pcs</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 3. Best Customers -->
            <div class="col-md-3">
                <div class="exe-section-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0"><i class="las la-star text-primary me-1"></i>Best Customers</h6>
                        <span class="badge bg-primary-subtle text-primary">Top Buyers</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>Customer</th>
                                    <th class="text-end">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bestCustomers as $cust)
                                <tr class="hover-row">
                                    <td><span class="fw-bold text-dark small d-block">{{ $cust['name'] }}</span></td>
                                    <td class="text-end fw-bold text-primary small">₱{{ number_format($cust['revenue'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 4. Most Overdue Customers -->
            <div class="col-md-3">
                <div class="exe-section-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0"><i class="las la-user-clock text-danger me-1"></i>Most Overdue AR</h6>
                        <span class="badge bg-danger-subtle text-danger">Credit Risk</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>Customer</th>
                                    <th class="text-end">Overdue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mostOverdueCustomers as $ov)
                                <tr class="hover-row">
                                    <td>
                                        <span class="fw-bold text-dark small d-block">{{ $ov['name'] }}</span>
                                        <span class="text-danger small">{{ $ov['days_overdue'] }} days past due</span>
                                    </td>
                                    <td class="text-end fw-bold text-danger small">₱{{ number_format($ov['amount'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DRILL-DOWN MODAL -->
    <div class="modal fade" id="drilldownModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background-color: #D9251C;">
                    <h5 class="modal-title fw-bold" id="modalKpiTitle">KPI Drill-Down</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-3">
                        <span class="text-muted small text-uppercase fw-bold">Live Value</span>
                        <h2 class="fw-bold text-dark" id="modalKpiValue">₱0.00</h2>
                    </div>
                    <p class="text-muted small text-center" id="modalKpiDesc">KPI description details...</p>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openKpiDrilldown(title, value, desc) {
            document.getElementById('modalKpiTitle').innerText = title + ' Drill-Down';
            document.getElementById('modalKpiValue').innerText = value;
            document.getElementById('modalKpiDesc').innerText = desc;
            var myModal = new bootstrap.Modal(document.getElementById('drilldownModal'));
            myModal.show();
        }
    </script>
    @endpush
</x-app-layout>

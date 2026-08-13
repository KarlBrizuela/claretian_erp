@php
    $user = auth()->user();
    
    // Check division access (for users in other divisions who might have cross-division permissions)
    $hasMarketingDivision = $user->divisions()->where('division', 'Marketing Division')->exists() || $user->division === 'Marketing Division' || $user->isSuperAdmin();
    $hasProductionDivision = $user->divisions()->where('division', 'Production Division')->exists() || $user->division === 'Production Division' || $user->isSuperAdmin();
    $hasAdminFinanceDivision = $user->divisions()->where('division', 'Admin & Finance Division')->exists() || $user->division === 'Admin & Finance Division' || $user->isSuperAdmin();
    
    // Marketing Permissions
    $hasDashM = $user->hasPermission('marketing.dashboard') && $hasMarketingDivision;
    $hasCust = $user->hasPermission('marketing.customers') && $hasMarketingDivision;
    $hasAreaS = $user->hasPermission('marketing.area_sales') && $hasMarketingDivision;
    $hasDirectS = $user->hasPermission('marketing.direct_sales') && $hasMarketingDivision;
    $hasAds = $user->hasPermission('marketing.ads_promo') && $hasMarketingDivision;
    $hasEcomM = $user->hasPermission('marketing.ecom') && $hasMarketingDivision;
    $hasBook = $user->hasPermission('marketing.book_mgmt') && $hasMarketingDivision;
    $hasSupp = $user->hasPermission('marketing.supplier_mgmt') && $hasMarketingDivision;
    $hasAppM = $user->hasPermission('marketing.approval_queue') && $hasMarketingDivision;
    $hasReqM = $user->hasPermission('marketing.my_requests') && $hasMarketingDivision;

    // Production Permissions
    $hasDashP = $user->hasPermission('production.dashboard') && $hasProductionDivision;
    $hasInv = $user->hasPermission('production.inventory') && $hasProductionDivision;
    $hasLog = $user->hasPermission('production.logistic') && $hasProductionDivision;
    $hasDTO = $user->hasPermission('production.dto') && $hasProductionDivision;
    $hasFORD = $user->hasPermission('production.ford') && $hasProductionDivision;
    $hasPrint = $user->hasPermission('production.printing') && $hasProductionDivision;
    $hasAppP = $user->hasPermission('production.approval_queue') && $hasProductionDivision;
    $hasReqP = $user->hasPermission('production.my_requests') && $hasProductionDivision;

    // Admin & Finance Permissions
    $hasDashAF = $user->hasPermission('admin_finance.dashboard') && $hasAdminFinanceDivision;
    $hasMIS = $user->hasPermission('admin_finance.mis') && $hasAdminFinanceDivision;
    $hasGSD = $user->hasPermission('admin_finance.gsd') && $hasAdminFinanceDivision;
    $hasCC = $user->hasPermission('admin_finance.credit_collection') && $hasAdminFinanceDivision;
    $hasAcc = $user->hasPermission('admin_finance.accounting') && $hasAdminFinanceDivision;
    $hasPettyCash = true; // All users have access to petty cash
    $hasFreightVoucher = true; // All users have access to freight voucher
    $hasHR = $user->hasPermission('admin_finance.hr') && $hasAdminFinanceDivision;
    $hasAppAF = $user->hasPermission('admin_finance.approval_queue') && $hasAdminFinanceDivision;
    $hasReqAF = $user->hasPermission('admin_finance.my_requests') && $hasAdminFinanceDivision;
    $hasServiceRequestsAF = $user->hasPermission('admin_finance.service_requests') && $hasAdminFinanceDivision;
    $hasChartOfAccounts = ($user->hasPermission('admin_finance.accounting.chart_of_accounts') || $user->hasPermission('admin_finance.accounting')) && $hasAdminFinanceDivision;
@endphp

<nav class="modern-nav-menu">
    {{-- Unified Dashboards / Common items --}}
    @if($hasDashM || $hasDashP || $hasDashAF)
    <a href="{{ $hasDashM ? route('marketing.dashboard') : ($hasDashP ? route('production.dashboard') : route('admin-finance.dashboard')) }}" class="modern-nav-item" data-page="dashboard">
        <div class="modern-nav-icon"><i class="flaticon-381-home-2"></i></div>
        <span class="modern-nav-label">Dashboard</span>
    </a>
    @endif

    {{-- Unified Requests/Approvals --}}
    @if($hasReqM || $hasReqP || $hasReqAF)
    <a href="{{ $hasReqM ? route('marketing.my-requests') : ($hasReqP ? route('production.my-requests') : route('admin-finance.my-requests')) }}" class="modern-nav-item">
        <div class="modern-nav-icon"><i class="las la-envelope-open-text"></i></div>
        <span class="modern-nav-label">My Requests</span>
    </a>
    @endif

    @if($hasAppM || $hasAppP || $hasAppAF)
    <a href="{{ $hasAppM ? route('marketing.approval-queue') : ($hasAppP ? route('production.approval-queue') : route('admin-finance.approval-queue')) }}" class="modern-nav-item">
        <div class="modern-nav-icon"><i class="las la-clipboard-check"></i></div>
        <span class="modern-nav-label">Approval Queue</span>
    </a>
    @endif

    {{-- Marketing Sections --}}
    @if($hasCust)
    <a href="{{ route('marketing.customers') }}" class="modern-nav-item"><div class="modern-nav-icon"><i class="las la-users"></i></div><span class="modern-nav-label">Customer Management</span></a>
    <a href="{{ route('marketing.companies') }}" class="modern-nav-item"><div class="modern-nav-icon"><i class="las la-building"></i></div><span class="modern-nav-label">Company Management</span></a>
    @endif
    
    @if($hasAreaS)
    <div class="modern-nav-group">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-map-marked-alt"></i></div><span class="modern-nav-label">Area Sales</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu">
            <a href="{{ route('marketing.sales-orders.list') }}" class="modern-nav-subitem">Sales Orders List</a>
            <a href="{{ route('marketing.direct-invoice.website') }}" class="modern-nav-subitem">Direct Invoice (Website)</a>
            <a href="{{ route('marketing.direct-invoice.ecom') }}" class="modern-nav-subitem">Direct Invoice (E-com)</a>
            <a href="{{ route('marketing.acknowledgement-receipt') }}" class="modern-nav-subitem">Acknowledgement Receipt</a>
            <a href="{{ route('marketing.credit-memo') }}" class="modern-nav-subitem">Credit Memo Form</a>
            <a href="{{ route('marketing.proof-of-payment') }}" class="modern-nav-subitem">Proof of Payment</a>
        </div>
    </div>
    @endif

    @if($hasDirectS)
    <div class="modern-nav-group">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-store"></i></div><span class="modern-nav-label">Direct Sales</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu">
            <a href="{{ route('marketing.pos.sale') }}" class="modern-nav-subitem">POS System</a>
            <a href="{{ route('marketing.pos.products') }}" class="modern-nav-subitem">POS Products</a>
            <a href="{{ route('marketing.nbs-import.index') }}" class="modern-nav-subitem">NBS PO Import</a>
        </div>
    </div>
    @endif

    @if($hasAds)
    <div class="modern-nav-group">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-ad"></i></div><span class="modern-nav-label">Ads and Promo</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu">
            <a href="{{ route('marketing.ads-promo.crpr') }}" class="modern-nav-subitem">Marketing Plan Itinerary Budget</a>
            <a href="{{ route('marketing.ads-promo.sponsors') }}" class="modern-nav-subitem">List of Sponsors</a>
        </div>
    </div>
    @endif

    @if($hasEcomM)
    <div class="modern-nav-group">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-shopping-cart"></i></div><span class="modern-nav-label">E-Com</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu"><a href="{{ route('marketing.ecom.pos') }}" class="modern-nav-subitem">E-Commerce POS</a></div>
    </div>
    @endif

    @if($hasBook)
    <div class="modern-nav-group {{ request()->is('marketing/book-list*', 'marketing/non-books*', 'marketing/consignment*', 'marketing/book-indices*', 'marketing/book-bundles*') ? 'active' : '' }}">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-book"></i></div><span class="modern-nav-label">Book Management</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu">
            <a href="{{ route('marketing.products') }}" class="modern-nav-subitem">Book List (Master)</a>
            <a href="{{ route('marketing.non-books') }}" class="modern-nav-subitem">Non-Books</a>
            <a href="{{ route('marketing.indices') }}" class="modern-nav-subitem">Book Index</a>
            <a href="{{ route('marketing.bundles') }}" class="modern-nav-subitem">Book Bundles</a>
            <a href="{{ route('marketing.consignment.index') }}" class="modern-nav-subitem">Consignment Management</a>
        </div>
    </div>
    @endif

    @if($hasSupp)
    <a href="{{ route('marketing.suppliers') }}" class="modern-nav-item"><div class="modern-nav-icon"><i class="las la-truck-loading"></i></div><span class="modern-nav-label">Supplier Management</span></a>
    @endif

    {{-- Production Sections --}}
    @if($hasInv)
    <a href="{{ route('production.inventory.master') }}" class="modern-nav-item {{ request()->routeIs('production.inventory.master') ? 'active' : '' }}">
        <div class="modern-nav-icon"><i class="las la-boxes"></i></div>
        <span class="modern-nav-label">Master Inventory</span>
    </a>
    <div class="modern-nav-group {{ (request()->is('production/inventory*') && !request()->routeIs('production.inventory.master')) ? 'active' : '' }}">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-warehouse"></i></div><span class="modern-nav-label">Inventory Management</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu">
            <a href="{{ route('production.inventory.overview') }}" class="modern-nav-subitem">Inventory Overview</a>
            <a href="{{ route('production.inventory.add-stock') }}" class="modern-nav-subitem">Add Stock</a>
            <a href="{{ route('production.inventory.received') }}" class="modern-nav-subitem">Received Items</a>
        </div>
    </div>
    @endif

    <div class="modern-nav-group {{ request()->is('production/executive-dashboard*') ? 'active' : '' }}">
        <a href="{{ route('production.executive-dashboard.index') }}" class="modern-nav-item">
            <div class="modern-nav-icon"><i class="las la-chart-bar"></i></div>
            <span class="modern-nav-label">Executive Dashboard</span>
        </a>
    </div>

    <div class="modern-nav-group {{ request()->is('production/costing*') ? 'active' : '' }}">
        <a href="{{ route('production.costing.index') }}" class="modern-nav-item">
            <div class="modern-nav-icon"><i class="las la-calculator"></i></div>
            <span class="modern-nav-label">Production Costing</span>
        </a>
    </div>

    <div class="modern-nav-group {{ request()->is('production/assets*') ? 'active' : '' }}">
        <a href="{{ route('production.assets.index') }}" class="modern-nav-item">
            <div class="modern-nav-icon"><i class="las la-tools"></i></div>
            <span class="modern-nav-label">Fixed Assets</span>
        </a>
    </div>

    @if($hasLog)
    <div class="modern-nav-group">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-boxes"></i></div><span class="modern-nav-label">Logistics</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu">
            <a href="{{ route('production.logistic.pick-list-list') }}" class="modern-nav-subitem">Pick Lists</a>
            <a href="{{ route('production.logistic.packing-management') }}" class="modern-nav-subitem">Packing</a>
            <a href="{{ route('production.logistic.pickup-requests.index') }}" class="modern-nav-subitem">Logistics Service Order</a>
            <a href="{{ route('production.logistic.delivery-scheduling') }}" class="modern-nav-subitem">Delivery Scheduling</a>
            <a href="{{ route('production.logistic.driver-dashboard') }}" class="modern-nav-subitem">Driver Dashboard</a>
            <a href="{{ route('production.logistic.delivery-tracking') }}" class="modern-nav-subitem">Delivery Tracking</a>
            <a href="{{ route('production.logistic.purchase-order-list') }}" class="modern-nav-subitem">Purchase Orders</a>
            <a href="{{ route('production.logistic.receiving-report-list') }}" class="modern-nav-subitem">Receiving Reports</a>
        </div>
    </div>
    @endif

    @if($hasFORD)
    <div class="modern-nav-group">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-warehouse"></i></div><span class="modern-nav-label">FORD</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu">
            <a href="{{ route('production.ford.client-payment-posting') }}" class="modern-nav-subitem">Client Payment posting</a>
            <a href="{{ route('production.ford.request-for-quotation') }}" class="modern-nav-subitem">Request for Quotation</a>
        </div>
    </div>
    @endif

    {{-- Admin & Finance Sections --}}
    @if($hasAcc)
    <div class="modern-nav-group">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-calculator"></i></div><span class="modern-nav-label">Accounting</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu">
            <a href="{{ route('accounting.journal.index') }}" class="modern-nav-subitem">General Journal</a>
            <a href="{{ route('admin-finance.accounting.sales-invoice') }}" class="modern-nav-subitem">Sales Invoice</a>
            <a href="{{ route('admin-finance.accounting.complimentary-receipt') }}" class="modern-nav-subitem">Complimentary Receipt</a>
            <a href="{{ route('admin-finance.check-voucher') }}" class="modern-nav-subitem">Check Voucher</a>
            <a href="{{ route('admin-finance.petty-cash.index', ['sidebar' => 'unified']) }}" class="modern-nav-subitem">Petty Cash Voucher</a>
            <a href="{{ route('admin-finance.freight-voucher.index', ['sidebar' => 'unified']) }}" class="modern-nav-subitem">Freight Voucher</a>
            <a href="{{ route('admin-finance.accounting.payment-requests') }}" class="modern-nav-subitem">Payment Requests</a>
            <a href="{{ route('admin-finance.accounting.eford-payouts') }}" class="modern-nav-subitem">E-FORD Payouts</a>
            <a href="{{ route('admin-finance.accounting.ecom-payouts.index') }}" class="modern-nav-subitem">E-com Payouts</a>
            <a href="{{ route('admin-finance.accounting.office-supplies.index') }}" class="modern-nav-subitem">Office Supplies</a>
            <a href="{{ route('admin-finance.accounting.expenses.index') }}" class="modern-nav-subitem">Expenses</a>
        </div>
    </div>
    @endif

    <div class="modern-nav-group {{ request()->is('admin-finance/accounting/investments*') ? 'active' : '' }}">
        <a href="{{ route('admin-finance.investments.index') }}" class="modern-nav-item">
            <div class="modern-nav-icon"><i class="las la-chart-pie"></i></div>
            <span class="modern-nav-label">Investments</span>
        </a>
    </div>

    <div class="modern-nav-group {{ request()->is('admin-finance/donations*') ? 'active' : '' }}">
        <a href="{{ route('admin-finance.donations.index') }}" class="modern-nav-item">
            <div class="modern-nav-icon"><i class="las la-hand-holding-heart"></i></div>
            <span class="modern-nav-label">Donations</span>
        </a>
    </div>

    <div class="modern-nav-group {{ request()->is('admin-finance/budgeting*') ? 'active' : '' }}">
        <a href="{{ route('admin-finance.budgeting.index') }}" class="modern-nav-item">
            <div class="modern-nav-icon"><i class="las la-balance-scale"></i></div>
            <span class="modern-nav-label">Budgeting</span>
        </a>
    </div>

    <div class="modern-nav-group {{ request()->is('admin-finance/cash-management*') ? 'active' : '' }}">
        <a href="{{ route('admin-finance.cash-management.index') }}" class="modern-nav-item">
            <div class="modern-nav-icon"><i class="las la-money-check-alt"></i></div>
            <span class="modern-nav-label">Cash Management</span>
        </a>
    </div>

    <div class="modern-nav-group {{ request()->is('admin-finance/financial-reports*') ? 'active' : '' }}">
        <a href="{{ route('admin-finance.financial-reports.index') }}" class="modern-nav-item">
            <div class="modern-nav-icon"><i class="las la-file-alt"></i></div>
            <span class="modern-nav-label">Financial Reports</span>
        </a>
    </div>

    @if($hasCC)
    <div class="modern-nav-group">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-money-bill-wave"></i></div><span class="modern-nav-label">Credit and Collection</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu">
            <a href="{{ route('admin-finance.credit-collection.billing') }}" class="modern-nav-subitem">Billing</a>
            <a href="{{ route('admin-finance.credit-collection.reconsignment.index') }}" class="modern-nav-subitem">Reconsignments</a>
            <a href="{{ route('admin-finance.credit-collection.reports') }}" class="modern-nav-subitem">Reports</a>
            <a href="{{ route('admin-finance.credit-collection.invoice') }}" class="modern-nav-subitem">Invoice</a>
        </div>
    </div>
    @endif

    @if($hasGSD)
    <div class="modern-nav-group">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-tools"></i></div><span class="modern-nav-label">GSD</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu">
            <a href="{{ route('admin-finance.gsd.asset-management') }}" class="modern-nav-subitem">Asset Management</a>
            <a href="{{ route('admin-finance.gsd.job-orders') }}" class="modern-nav-subitem">Job Orders</a>
        </div>
    </div>
    @endif

    @if($hasMIS)
    <div class="modern-nav-group">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-server"></i></div><span class="modern-nav-label">MIS</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu"><a href="{{ route('admin-finance.mis.job-orders') }}" class="modern-nav-subitem">Job Orders</a></div>
    </div>
    @endif

    @if($hasHR)
    <div class="modern-nav-group">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-user-tie"></i></div><span class="modern-nav-label">HR</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu"><a href="{{ route('admin-finance.hr.job-orders') }}" class="modern-nav-subitem">Job Orders</a></div>
    </div>
    @endif

    @if($hasServiceRequestsAF)
    <a href="{{ route('admin-finance.service-requests.create') }}" class="modern-nav-item">
        <div class="modern-nav-icon"><i class="las la-tools"></i></div>
        <span class="modern-nav-label">Create Service Request</span>
    </a>
    @endif

    @if($hasPettyCash || $hasFreightVoucher)
    <div class="modern-nav-group">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-calculator"></i></div><span class="modern-nav-label">Finance</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu">
            @if($hasPettyCash)
            <a href="{{ route('admin-finance.petty-cash.index', ['sidebar' => 'unified']) }}" class="modern-nav-subitem">Petty Cash Voucher</a>
            @endif
            @if($hasFreightVoucher)
            <a href="{{ route('admin-finance.freight-voucher.index', ['sidebar' => 'unified']) }}" class="modern-nav-subitem">Freight Voucher</a>
            @endif
        </div>
    </div>
    @endif

    @if($hasChartOfAccounts)
    <div class="modern-nav-group {{ request()->is('admin-finance/chart-of-accounts*') ? 'active' : '' }}">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="chart-of-accounts">
            <div class="modern-nav-icon"><i class="las la-sitemap"></i></div>
            <span class="modern-nav-label">Chart of Accounts</span>
            <i class="modern-nav-arrow las la-chevron-right"></i>
        </a>
        <div class="modern-nav-submenu" data-submenu="chart-of-accounts">
            <a href="{{ route('admin-finance.accounting.chart-of-accounts', ['tab' => 'assets']) }}" class="modern-nav-subitem {{ request()->query('tab') == 'assets' ? 'active' : '' }}">Assets</a>
            {{-- <a href="{{ route('admin-finance.accounting.chart-of-accounts', ['tab' => 'liabilities']) }}" class="modern-nav-subitem {{ request()->query('tab') == 'liabilities' ? 'active' : '' }}">Liabilities</a> --}}
            {{-- <a href="{{ route('admin-finance.accounting.chart-of-accounts', ['tab' => 'equity']) }}" class="modern-nav-subitem {{ request()->query('tab') == 'equity' ? 'active' : '' }}">Equity</a> --}}
            <a href="{{ route('admin-finance.accounting.chart-of-accounts', ['tab' => 'income']) }}" class="modern-nav-subitem {{ request()->query('tab') == 'income' ? 'active' : '' }}">Income</a>
            <a href="{{ route('admin-finance.accounting.chart-of-accounts', ['tab' => 'expenses']) }}" class="modern-nav-subitem {{ request()->query('tab') == 'expenses' ? 'active' : '' }}">Expenses</a>
        </div>
    </div>
    @endif

    @if($hasChartOfAccounts)
    <div class="modern-nav-group {{ request()->is('admin-finance/sales-management*') ? 'active' : '' }}">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="sales-management">
            <div class="modern-nav-icon"><i class="las la-chart-bar"></i></div>
            <span class="modern-nav-label">Sales Management</span>
            <i class="modern-nav-arrow las la-chevron-right"></i>
        </a>
        <div class="modern-nav-submenu" data-submenu="sales-management">
            <a href="{{ route('admin-finance.accounting.sales-management', ['tab' => 'bookstore']) }}" class="modern-nav-subitem {{ request()->query('tab') == 'bookstore' ? 'active' : '' }}">Bookstore</a>
            <a href="{{ route('admin-finance.accounting.sales-management', ['tab' => 'areasales']) }}" class="modern-nav-subitem {{ request()->query('tab') == 'areasales' ? 'active' : '' }}">Area Sales</a>
            <a href="{{ route('admin-finance.accounting.sales-management', ['tab' => 'ecom']) }}" class="modern-nav-subitem {{ request()->query('tab') == 'ecom' ? 'active' : '' }}">E-Commerce</a>
            <a href="{{ route('admin-finance.accounting.sales-management', ['tab' => 'wholesale']) }}" class="modern-nav-subitem {{ request()->query('tab') == 'wholesale' ? 'active' : '' }}">Wholesale</a>
        </div>
    </div>
    @endif

    @if($hasChartOfAccounts)
    <div class="modern-nav-group {{ request()->is('admin-finance/accounts-receivable*') ? 'active' : '' }}">
        <a href="{{ route('admin-finance.accounting.accounts-receivable') }}" class="modern-nav-item">
            <div class="modern-nav-icon"><i class="las la-file-invoice-dollar"></i></div>
            <span class="modern-nav-label">Accounts Receivable</span>
        </a>
    </div>
    @endif

    @if($hasChartOfAccounts)
    <div class="modern-nav-group {{ request()->is('admin-finance/accounts-payable*') ? 'active' : '' }}">
        <a href="{{ route('admin-finance.accounting.accounts-payable') }}" class="modern-nav-item">
            <div class="modern-nav-icon"><i class="las la-receipt"></i></div>
            <span class="modern-nav-label">Accounts Payable</span>
        </a>
    </div>
    @endif

</nav>

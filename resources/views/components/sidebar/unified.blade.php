@php
    $user = auth()->user();
    
    // Marketing Permissions
    $hasDashM = $user->hasPermission('marketing.dashboard');
    $hasCust = $user->hasPermission('marketing.customers');
    $hasAreaS = $user->hasPermission('marketing.area_sales');
    $hasDirectS = $user->hasPermission('marketing.direct_sales');
    $hasAds = $user->hasPermission('marketing.ads_promo');
    $hasEcomM = $user->hasPermission('marketing.ecom');
    $hasBook = $user->hasPermission('marketing.book_mgmt');
    $hasSupp = $user->hasPermission('marketing.supplier_mgmt');
    $hasAppM = $user->hasPermission('marketing.approval_queue');
    $hasReqM = $user->hasPermission('marketing.my_requests');

    // Production Permissions
    $hasDashP = $user->hasPermission('production.dashboard');
    $hasInv = $user->hasPermission('production.inventory');
    $hasLog = $user->hasPermission('production.logistic');
    $hasDTO = $user->hasPermission('production.dto');
    $hasFORD = $user->hasPermission('production.ford');
    $hasPrint = $user->hasPermission('production.printing');
    $hasAppP = $user->hasPermission('production.approval_queue');
    $hasReqP = $user->hasPermission('production.my_requests');

    // Admin & Finance Permissions
    $hasDashAF = $user->hasPermission('admin_finance.dashboard');
    $hasMIS = $user->hasPermission('admin_finance.mis');
    $hasGSD = $user->hasPermission('admin_finance.gsd');
    $hasCC = $user->hasPermission('admin_finance.credit_collection');
    $hasAcc = $user->hasPermission('admin_finance.accounting');
    $hasHR = $user->hasPermission('admin_finance.hr');
    $hasAppAF = $user->hasPermission('admin_finance.approval_queue');
    $hasReqAF = $user->hasPermission('admin_finance.my_requests');
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
    <div class="modern-nav-group {{ request()->is('marketing/book-list*', 'marketing/consignment*') ? 'active' : '' }}">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-book"></i></div><span class="modern-nav-label">Book Management</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu">
            <a href="{{ route('marketing.products') }}" class="modern-nav-subitem">Book List (Master)</a>
            <a href="{{ route('marketing.consignment.index') }}" class="modern-nav-subitem">Consignment Management</a>
        </div>
    </div>
    @endif

    @if($hasSupp)
    <a href="{{ route('marketing.suppliers') }}" class="modern-nav-item"><div class="modern-nav-icon"><i class="las la-truck-loading"></i></div><span class="modern-nav-label">Supplier Management</span></a>
    @endif

    {{-- Production Sections --}}
    @if($hasInv)
    <div class="modern-nav-group">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-warehouse"></i></div><span class="modern-nav-label">Inventory Management</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu">
            <a href="{{ route('production.inventory.overview') }}" class="modern-nav-subitem">Inventory Overview</a>
            <a href="{{ route('production.inventory.add-stock') }}" class="modern-nav-subitem">Add Stock</a>
            <a href="{{ route('production.inventory.received') }}" class="modern-nav-subitem">Received Items</a>
        </div>
    </div>
    @endif

    @if($hasLog)
    <div class="modern-nav-group">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-boxes"></i></div><span class="modern-nav-label">Logistics</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu">
            <a href="{{ route('production.logistic.pick-list-list') }}" class="modern-nav-subitem">Pick Lists</a>
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
            <a href="{{ route('admin-finance.check-voucher') }}" class="modern-nav-subitem">Check Voucher</a>
            <a href="{{ route('admin-finance.petty-cash.index') }}" class="modern-nav-subitem">Petty Cash Voucher</a>
        </div>
    </div>
    @endif

    @if($hasCC)
    <div class="modern-nav-group">
        <a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle"><div class="modern-nav-icon"><i class="las la-money-bill-wave"></i></div><span class="modern-nav-label">Credit and Collection</span><i class="modern-nav-arrow las la-chevron-right"></i></a>
        <div class="modern-nav-submenu">
            <a href="{{ route('admin-finance.credit-collection.billing') }}" class="modern-nav-subitem">Billing</a>
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

</nav>

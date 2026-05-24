@php
    $user = auth()->user();
    $isSuperAdmin = $user->isSuperAdmin();
    
    // Check permissions for specific modules
    $hasDashboard = $user->hasPermission('marketing.dashboard');
    $hasCustomers = $user->hasPermission('marketing.customers');
    $hasAreaSales = $user->hasPermission('marketing.area_sales');
    $hasDirectSales = $user->hasPermission('marketing.direct_sales');
    $hasAdsAndPromo = $user->hasPermission('marketing.ads_promo');
    $hasEcom = $user->hasPermission('marketing.ecom');
    $hasBookMgmt = $user->hasPermission('marketing.book_mgmt');
    $hasSupplierMgmt = $user->hasPermission('marketing.supplier_mgmt');
    $hasPettyCashVoucher = true; // All users have access to petty cash
    $hasFreightVoucher = true; // All users have access to freight voucher
    $hasApprovalQueue = $user->hasPermission('marketing.approval_queue');
    $hasMyRequests = $user->hasPermission('marketing.my_requests');
@endphp

<nav class="modern-nav-menu">
	@if($hasDashboard)
	<a href="{{ route('marketing.dashboard') }}" class="modern-nav-item {{ request()->routeIs('marketing.dashboard') ? 'active' : '' }}" data-page="dashboard">
		<div class="modern-nav-icon">
			<i class="flaticon-381-home-2"></i>
		</div>
		<span class="modern-nav-label">Dashboard</span>
	</a>
    @endif

    @if($hasMyRequests)
    <a href="{{ route('marketing.my-requests') }}" class="modern-nav-item {{ request()->routeIs('marketing.my-requests') ? 'active' : '' }}">
        <div class="modern-nav-icon">
            <i class="las la-envelope-open-text"></i>
        </div>
        <span class="modern-nav-label">My Requests</span>
    </a>
    @endif

    @if($hasApprovalQueue)
    <a href="{{ route('marketing.approval-queue') }}" class="modern-nav-item {{ request()->routeIs('marketing.approval-queue') ? 'active' : '' }}">
        <div class="modern-nav-icon">
            <i class="las la-clipboard-check"></i>
        </div>
        <span class="modern-nav-label">Approval Queue</span>
    </a>
    @endif
	
	<!-- Customer Management -->
	@if($hasCustomers)
	<a href="{{ route('marketing.customers') }}" class="modern-nav-item {{ request()->routeIs('marketing.customers') ? 'active' : '' }}" data-page="customer-management">
		<div class="modern-nav-icon">
			<i class="las la-users"></i>
		</div>
		<span class="modern-nav-label">Customer Management</span>
	</a>
	@endif

	<!-- Area Sales -->
	@if($hasAreaSales)
	<div class="modern-nav-group {{ request()->is('marketing/sales-orders*', 'marketing/sales-order*', 'marketing/direct-invoice*', 'marketing/acknowledgement-receipt*', 'marketing/credit-memo*', 'marketing/proof-of-payment*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="area-sales">
			<div class="modern-nav-icon">
				<i class="las la-map-marked-alt"></i>
			</div>
			<span class="modern-nav-label">Area Sales</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu">
			<a href="{{ route('marketing.sales-orders.list') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.sales-orders.list', 'marketing.sales-orders.create') ? 'active' : '' }}">Sales Orders List</a>

			<a href="{{ route('marketing.direct-invoice.website') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.direct-invoice.website') ? 'active' : '' }}">Direct Invoice (Website)</a>
			<a href="{{ route('marketing.direct-invoice.ecom') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.direct-invoice.ecom') ? 'active' : '' }}">Direct Invoice (E-com)</a>
			<a href="{{ route('marketing.acknowledgement-receipt') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.acknowledgement-receipt') ? 'active' : '' }}">Acknowledgement Receipt</a>
			<a href="{{ route('marketing.credit-memo') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.credit-memo') ? 'active' : '' }}">Credit Memo Form</a>
			<a href="{{ route('marketing.proof-of-payment') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.proof-of-payment') ? 'active' : '' }}">Proof of Payment</a>
			<!-- Sales Reports and Territory Management removed -->
		</div>
	</div>
	@endif

	<!-- Direct Sales -->
	@if($hasDirectSales)
	<div class="modern-nav-group {{ request()->is('marketing/pos-sale*', 'marketing/pos-products*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="direct-sales">
			<div class="modern-nav-icon">
				<i class="las la-store"></i>
			</div>
			<span class="modern-nav-label">Direct Sales</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu">
			<a href="{{ route('marketing.pos.sale') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.pos.sale') ? 'active' : '' }}">POS System</a>
			<a href="{{ route('marketing.pos.products') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.pos.products') ? 'active' : '' }}">POS Products</a>
			<a href="{{ route('marketing.nbs-import.index') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.nbs-import.index') ? 'active' : '' }}">NBS PO Import</a>
		</div>
	</div>
	@endif

	<!-- Ads and Promo -->
	@if($hasAdsAndPromo)
	<div class="modern-nav-group {{ request()->is('marketing/ads-promo*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="ads-promo">
			<div class="modern-nav-icon">
				<i class="las la-ad"></i>
			</div>
			<span class="modern-nav-label">Ads and Promo</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu">
			<!-- Campaigns and Promotions removed -->
			<a href="{{ route('marketing.ads-promo.crpr') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.ads-promo.crpr') ? 'active' : '' }}">Marketing Plan Itinerary Budget</a>
			<a href="{{ route('marketing.ads-promo.sponsors') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.ads-promo.sponsors') ? 'active' : '' }}">List of Sponsors</a>
		</div>
	</div>
	@endif

	<!-- E-Com -->
	@if($hasEcom)
	<div class="modern-nav-group {{ request()->is('marketing/ecom-pos*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="ecom">
			<div class="modern-nav-icon">
				<i class="las la-shopping-cart"></i>
			</div>
			<span class="modern-nav-label">E-Com</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu">
			<a href="{{ route('marketing.ecom.pos') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.ecom.pos') ? 'active' : '' }}">E-Commerce POS</a>
		</div>
	</div>
	@endif

	<!-- Book Management (Master) -->
	@if($hasBookMgmt)
	<div class="modern-nav-group {{ request()->is('marketing/book-list*', 'marketing/consignment*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="book-management">
			<div class="modern-nav-icon">
				<i class="las la-book"></i>
			</div>
			<span class="modern-nav-label">Book Management</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu">
			<a href="{{ route('marketing.products') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.products') ? 'active' : '' }}">Book List (Master)</a>
			<a href="{{ route('marketing.consignment.index') }}" class="modern-nav-subitem {{ request()->routeIs('marketing.consignment.index') ? 'active' : '' }}">Consignment Management</a>
		</div>
	</div>
	@endif

	<!-- Supplier Management -->
	@if($hasSupplierMgmt)
	<a href="{{ route('marketing.suppliers') }}" class="modern-nav-item {{ request()->routeIs('marketing.suppliers') ? 'active' : '' }}" data-page="supplier">
		<div class="modern-nav-icon">
			<i class="las la-truck-loading"></i>
		</div>
		<span class="modern-nav-label">Supplier Management</span>
	</a>
	@endif

	<!-- Finance -->
	@if($hasPettyCashVoucher || $hasFreightVoucher)
	<div class="modern-nav-group {{ request()->is('admin-finance/petty-cash*', 'admin-finance/freight-voucher*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="finance">
			<div class="modern-nav-icon">
				<i class="las la-calculator"></i>
			</div>
			<span class="modern-nav-label">Finance</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu" data-submenu="finance">
			@if($hasPettyCashVoucher)
			<a href="{{ route('admin-finance.petty-cash.index') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.petty-cash.*') ? 'active' : '' }}">Petty Cash Voucher</a>
			@endif
			@if($hasFreightVoucher)
			<a href="{{ route('admin-finance.freight-voucher.index') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.freight-voucher.*') ? 'active' : '' }}">Freight Voucher</a>
			@endif
		</div>
	</div>
	@endif

</nav>

@push('scripts')
<script>
    // Sidebar behavior is now centrally managed in app.blade.php
</script>
@endpush


@php
    $user = auth()->user();
    $isSuperAdmin = $user->isSuperAdmin();
    
    // Check permissions for specific modules
    $hasDashboard = $user->hasPermission('production.dashboard');
    $hasInventory = $user->hasPermission('production.inventory');
    $hasLogistics = $user->hasPermission('production.logistic');
    $hasDTO = $user->hasPermission('production.dto');
    $hasFORD = $user->hasPermission('production.ford');
    $hasPrinting = $user->hasPermission('production.printing');
    $hasPettyCashVoucher = true; // All users have access to petty cash
    $hasFreightVoucher = true; // All users have access to freight voucher
    $hasApprovalQueue = $user->hasPermission('production.approval_queue');
    $hasMyRequests = $user->hasPermission('production.my_requests');
@endphp

<nav class="modern-nav-menu">
	@if($hasDashboard)
	<a href="{{ route('production.dashboard') }}" class="modern-nav-item {{ request()->routeIs('production.dashboard') ? 'active' : '' }}" data-page="dashboard">
		<div class="modern-nav-icon">
			<i class="flaticon-381-home-2"></i>
		</div>
		<span class="modern-nav-label">Dashboard</span>
	</a>
    @endif
    
    @if($hasMyRequests)
    <a href="{{ route('production.my-requests') }}" class="modern-nav-item {{ request()->routeIs('production.my-requests') ? 'active' : '' }}">
        <div class="modern-nav-icon">
            <i class="las la-envelope-open-text"></i>
        </div>
        <span class="modern-nav-label">My Requests</span>
    </a>
    @endif

    @if($hasApprovalQueue)
    <a href="{{ route('production.approval-queue') }}" class="modern-nav-item {{ request()->routeIs('production.approval-queue') ? 'active' : '' }}">
        <div class="modern-nav-icon">
            <i class="las la-clipboard-check"></i>
        </div>
        <span class="modern-nav-label">Approval Queue</span>
    </a>
    @endif
	
	<!-- Inventory Management -->
	@if($hasInventory)
	<div class="modern-nav-group {{ request()->is('production/inventory*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="inventory">
			<div class="modern-nav-icon">
				<i class="las la-warehouse"></i>
			</div>
			<span class="modern-nav-label">Inventory Management</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu" data-submenu="inventory">
			<a href="{{ route('production.inventory.overview') }}" class="modern-nav-subitem {{ request()->routeIs('production.inventory.overview') ? 'active' : '' }}">Inventory Overview</a>
			<a href="{{ route('production.inventory.add-stock') }}" class="modern-nav-subitem {{ request()->routeIs('production.inventory.add-stock') ? 'active' : '' }}">Add Stock</a>
			<a href="{{ route('production.inventory.received') }}" class="modern-nav-subitem {{ request()->routeIs('production.inventory.received') ? 'active' : '' }}">Received Items</a>
		</div>
	</div>
	@endif

	<!-- Logistics -->
	@if($hasLogistics)
	<div class="modern-nav-group {{ request()->is('production/logistic*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="logistics">
			<div class="modern-nav-icon">
				<i class="las la-boxes"></i>
			</div>
			<span class="modern-nav-label">Logistics</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu" data-submenu="logistics">
			<a href="{{ route('production.logistic.pick-list-list') }}" class="modern-nav-subitem {{ request()->routeIs('production.logistic.pick-list-list') ? 'active' : '' }}">Pick Lists</a>
			<a href="{{ route('production.logistic.delivery-scheduling') }}" class="modern-nav-subitem {{ request()->routeIs('production.logistic.delivery-scheduling') ? 'active' : '' }}">Delivery Scheduling</a>
			<a href="{{ route('production.logistic.delivery-receipt-list') }}" class="modern-nav-subitem {{ request()->routeIs('production.logistic.delivery-receipt-list') ? 'active' : '' }}">Delivery Receipts</a>
			<a href="{{ route('production.logistic.driver-dashboard') }}" class="modern-nav-subitem {{ request()->routeIs('production.logistic.driver-dashboard') ? 'active' : '' }}">Driver Dashboard</a>
			<a href="{{ route('production.logistic.delivery-tracking') }}" class="modern-nav-subitem {{ request()->routeIs('production.logistic.delivery-tracking') ? 'active' : '' }}">Delivery Tracking</a>
			<a href="{{ route('production.logistic.purchase-order-list') }}" class="modern-nav-subitem {{ request()->routeIs('production.logistic.purchase-order-list') ? 'active' : '' }}">Purchase Orders</a>
			<a href="{{ route('production.logistic.receiving-report-list') }}" class="modern-nav-subitem {{ request()->routeIs('production.logistic.receiving-report-list') ? 'active' : '' }}">Receiving Reports</a>
		</div>
	</div>
	@endif

	<!-- DTO -->
	@if($hasDTO)
	<div class="modern-nav-group {{ request()->is('production/dto*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="dto">
			<div class="modern-nav-icon">
				<i class="las la-truck"></i>
			</div>
			<span class="modern-nav-label">DTO</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu" data-submenu="dto">
			<a href="{{ route('production.dto.job-request-form') }}" class="modern-nav-subitem {{ request()->routeIs('production.dto.job-request-form') ? 'active' : '' }}">Job Request Form</a>
		</div>
	</div>
	@endif

	<!-- FORD -->
	@if($hasFORD)
	<div class="modern-nav-group {{ request()->is('production/ford*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="ford">
			<div class="modern-nav-icon">
				<i class="las la-warehouse"></i>
			</div>
			<span class="modern-nav-label">FORD</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu" data-submenu="ford">
			<a href="{{ route('production.ford.auto-debit') }}" class="modern-nav-subitem {{ request()->routeIs('production.ford.auto-debit') ? 'active' : '' }}">Auto Debit</a>
			<a href="{{ route('production.ford.client-payment-posting') }}" class="modern-nav-subitem {{ request()->routeIs('production.ford.client-payment-posting') ? 'active' : '' }}">Client Payment for posting</a>
			<a href="{{ route('production.ford.eford-payout') }}" class="modern-nav-subitem {{ request()->routeIs('production.ford.eford-payout') ? 'active' : '' }}">E-FORD Payout</a>
			<a href="{{ route('production.ford.payment-request') }}" class="modern-nav-subitem {{ request()->routeIs('production.ford.payment-request') ? 'active' : '' }}">Payment Request</a>
			<a href="{{ route('production.ford.purchase-order') }}" class="modern-nav-subitem {{ request()->routeIs('production.ford.purchase-order') ? 'active' : '' }}">Purchase Order</a>
			<a href="{{ route('production.ford.request-for-quotation') }}" class="modern-nav-subitem {{ request()->routeIs('production.ford.request-for-quotation') ? 'active' : '' }}">Request for Quotation</a>
			<a href="{{ route('production.ford.sales-order') }}" class="modern-nav-subitem {{ request()->routeIs('production.ford.sales-order') ? 'active' : '' }}">Sales Order</a>
			<a href="{{ route('production.ford.transmittal') }}" class="modern-nav-subitem {{ request()->routeIs('production.ford.transmittal') ? 'active' : '' }}">Transmittal</a>
		</div>
	</div>
	@endif

	<!-- Printing Services -->
	@if($hasPrinting)
	<div class="modern-nav-group {{ request()->is('production/printing*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="printing">
			<div class="modern-nav-icon">
				<i class="las la-print"></i>
			</div>
			<span class="modern-nav-label">Printing Services</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu" data-submenu="printing">
			<a href="{{ route('production.printing.request-payment-to-printer') }}" class="modern-nav-subitem {{ request()->routeIs('production.printing.request-payment-to-printer') ? 'active' : '' }}">Request Payment to Printer</a>
		</div>
	</div>
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

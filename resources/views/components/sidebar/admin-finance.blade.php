@php
    $user = auth()->user();
    $isSuperAdmin = $user->isSuperAdmin();
    
    // Check permissions for specific modules
    $hasDashboard = $user->hasPermission('admin_finance.dashboard');
    $hasMIS = $user->hasPermission('admin_finance.mis');
    $hasGSD = $user->hasPermission('admin_finance.gsd');
    $hasCreditCollection = $user->hasPermission('admin_finance.credit_collection');
    $hasAccounting = $user->hasPermission('admin_finance.accounting');
    $hasHR = $user->hasPermission('admin_finance.hr');
    $hasApprovalQueue = $user->hasPermission('admin_finance.approval_queue');
    $hasMyRequests = $user->hasPermission('admin_finance.my_requests');
@endphp

<nav class="modern-nav-menu">
	@if($hasDashboard)
	<a href="{{ route('admin-finance.dashboard') }}" class="modern-nav-item {{ request()->routeIs('admin-finance.dashboard') ? 'active' : '' }}" data-page="dashboard">
		<div class="modern-nav-icon">
			<i class="flaticon-381-home-2"></i>
		</div>
		<span class="modern-nav-label">Dashboard</span>
	</a>
    @endif

    @if($hasMyRequests)
	<a href="{{ route('admin-finance.my-requests') }}" class="modern-nav-item {{ request()->routeIs('admin-finance.my-requests') ? 'active' : '' }}" data-page="my-requests">
		<div class="modern-nav-icon">
			<i class="las la-envelope-open-text"></i>
		</div>
		<span class="modern-nav-label">My Requests</span>
	</a>
    @endif

    @if($hasApprovalQueue)
	<a href="{{ route('admin-finance.approval-queue') }}" class="modern-nav-item {{ request()->routeIs('admin-finance.approval-queue') ? 'active' : '' }}" data-page="approval-queue">
		<div class="modern-nav-icon">
			<i class="las la-clipboard-check"></i>
		</div>
		<span class="modern-nav-label">Approval Queue</span>
	</a>
    @endif

	<!-- Accounting -->
    @if($hasAccounting)
	<div class="modern-nav-group {{ request()->is('admin-finance/accounting*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="accounting">
			<div class="modern-nav-icon">
				<i class="las la-calculator"></i>
			</div>
			<span class="modern-nav-label">Accounting</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu" data-submenu="accounting">
			<a href="{{ route('accounting.journal.index') }}" class="modern-nav-subitem {{ request()->routeIs('accounting.journal.*') ? 'active' : '' }}">General Journal</a>
			<a href="{{ route('admin-finance.accounting.sales-invoice') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.sales-invoice') ? 'active' : '' }}">Sales Invoice</a>
			<a href="{{ route('admin-finance.check-voucher') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.check-voucher') ? 'active' : '' }}">Check Voucher</a>
			<a href="{{ route('admin-finance.petty-cash.index') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.petty-cash.*') ? 'active' : '' }}">Petty Cash Voucher</a>
			<a href="{{ route('admin-finance.accounting.materials-requisition') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.materials-requisition') ? 'active' : '' }}">Materials/Supplies Requisition</a>
			<a href="{{ route('admin-finance.accounting.material-requests.incoming') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.material-requests.incoming') ? 'active' : '' }}">Material Requests</a>
			<a href="{{ route('admin-finance.accounting.expense-management') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.expense-management') ? 'active' : '' }}">Cash Advance Liquidation</a>
		</div>
	</div>
    @endif

	<!-- Credit and Collection -->
    @if($hasCreditCollection)
	<div class="modern-nav-group {{ request()->is('admin-finance/credit-collection*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="credit-collection">
			<div class="modern-nav-icon">
				<i class="las la-money-bill-wave"></i>
			</div>
			<span class="modern-nav-label">Credit and Collection</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu" data-submenu="credit-collection">
			<a href="{{ route('admin-finance.credit-collection.billing') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.credit-collection.billing') ? 'active' : '' }}">Billing</a>
			<a href="{{ route('admin-finance.credit-collection.reports') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.credit-collection.reports') ? 'active' : '' }}">Reports</a>
			<a href="{{ route('admin-finance.credit-collection.invoice') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.credit-collection.invoice') ? 'active' : '' }}">Invoice</a>
		</div>
	</div>
    @endif

	<!-- GSD -->
    @if($hasGSD)
	<div class="modern-nav-group {{ request()->is('admin-finance/gsd*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="gsd">
			<div class="modern-nav-icon">
				<i class="las la-tools"></i>
			</div>
			<span class="modern-nav-label">GSD</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu" data-submenu="gsd">
			<a href="{{ route('admin-finance.gsd.asset-management') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.gsd.asset-management') ? 'active' : '' }}">Asset Management</a>
			<a href="{{ route('admin-finance.gsd.job-orders') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.gsd.job-orders') ? 'active' : '' }}">Job Orders</a>
		</div>
	</div>
    @endif

	<!-- HR -->
    @if($hasHR)
	<div class="modern-nav-group {{ request()->is('admin-finance/hr*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="hr">
			<div class="modern-nav-icon">
				<i class="las la-user-tie"></i>
			</div>
			<span class="modern-nav-label">HR</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu" data-submenu="hr">
			<a href="{{ route('admin-finance.hr.job-orders') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.hr.job-orders') ? 'active' : '' }}">Job Orders</a>
		</div>
	</div>
    @endif

	<!-- MIS -->
    @if($hasMIS)
	<div class="modern-nav-group {{ request()->is('admin-finance/mis*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="mis">
			<div class="modern-nav-icon">
				<i class="las la-server"></i>
			</div>
			<span class="modern-nav-label">MIS</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu" data-submenu="mis">
			<a href="{{ route('admin-finance.mis.job-orders') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.mis.job-orders') ? 'active' : '' }}">Job Orders</a>
		</div>
	</div>
    @endif

</nav>

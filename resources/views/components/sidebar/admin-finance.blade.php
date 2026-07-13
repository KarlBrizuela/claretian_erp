@php
    $user = auth()->user();
    $isSuperAdmin = $user->isSuperAdmin();
    
    // Check permissions for specific modules
    $hasDashboard = $user->hasPermission('admin_finance.dashboard');
    $hasMIS = $user->hasPermission('admin_finance.mis') || $user->hasPermission('admin_finance.mis.job_orders');
    $hasGSD = $user->hasPermission('admin_finance.gsd') || $user->hasAnyPermission(['admin_finance.gsd.asset_management', 'admin_finance.gsd.job_orders']);
    $hasCreditCollection = $user->hasPermission('admin_finance.credit_collection') || $user->hasAnyPermission(['admin_finance.credit_collection.billing', 'admin_finance.credit_collection.reports', 'admin_finance.credit_collection.invoice']);
    $hasAccounting = $user->hasPermission('admin_finance.accounting') || $user->hasAnyPermission(['admin_finance.accounting.general_journal', 'admin_finance.accounting.sales_invoice', 'admin_finance.accounting.check_voucher', 'admin_finance.accounting.materials_requisition', 'admin_finance.accounting.material_requests', 'admin_finance.accounting.cash_advance_liquidation', 'admin_finance.accounting.cod_collections', 'admin_finance.accounting.office_supplies', 'admin_finance.accounting.expenses']);
    $hasPettyCashVoucher = $user->hasPermission('admin_finance.petty_cash_voucher');
    $hasHR = $user->hasPermission('admin_finance.hr') || $user->hasPermission('admin_finance.hr.job_orders');
    $hasApprovalQueue = $user->hasPermission('admin_finance.approval_queue');
    $hasMyRequests = $user->hasPermission('admin_finance.my_requests');
    $hasServiceRequests = $user->hasPermission('admin_finance.service_requests');
    $hasFreightVoucher = $user->hasPermission('admin_finance.freight_voucher');
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
	<div class="modern-nav-group {{ request()->is('admin-finance/accounting*', 'admin-finance/cashier*') ? 'active' : '' }}">
		<a href="javascript:void(0)" class="modern-nav-item modern-nav-toggle" data-group="accounting">
			<div class="modern-nav-icon">
				<i class="las la-calculator"></i>
			</div>
			<span class="modern-nav-label">Accounting</span>
			<i class="modern-nav-arrow las la-chevron-right"></i>
		</a>
		<div class="modern-nav-submenu" data-submenu="accounting">
			@if($user->hasPermission('admin_finance.accounting.general_journal'))
			<a href="{{ route('accounting.journal.index') }}" class="modern-nav-subitem {{ request()->routeIs('accounting.journal.*') ? 'active' : '' }}">General Journal</a>
			@endif
			@if($user->hasPermission('admin_finance.accounting.sales_invoice'))
			<a href="{{ route('admin-finance.accounting.sales-invoice') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.sales-invoice') ? 'active' : '' }}">Sales Invoice</a>
			@endif
			@if($user->hasPermission('admin_finance.accounting.check_voucher'))
			<a href="{{ route('admin-finance.check-voucher') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.check-voucher') ? 'active' : '' }}">Check Voucher</a>
			@endif
			@if($user->hasPermission('admin_finance.accounting.materials_requisition'))
			<a href="{{ route('admin-finance.accounting.materials-requisition') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.materials-requisition') ? 'active' : '' }}">Materials/Supplies Requisition</a>
			@endif
			@if($user->hasPermission('admin_finance.accounting.material_requests'))
			<a href="{{ route('admin-finance.accounting.material-requests.incoming') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.material-requests.incoming') ? 'active' : '' }}">Material Requests</a>
			@endif
			@if($user->hasPermission('admin_finance.accounting.cash_advance_liquidation'))
			<a href="{{ route('admin-finance.accounting.expense-management') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.expense-management') ? 'active' : '' }}">Cash Advance Liquidation</a>
			@endif
			@if($user->hasPermission('admin_finance.accounting.cod_collections'))
			<a href="{{ route('cashier.collections.index') }}" class="modern-nav-subitem {{ request()->routeIs('cashier.collections.*') ? 'active' : '' }}">COD Collections Verification</a>
			@endif
			@if($user->hasPermission('admin_finance.accounting.cashier') || $user->hasPermission('admin_finance.accounting'))
			<a href="{{ route('admin-finance.accounting.cashier.index') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.cashier.*') ? 'active' : '' }}">Cashier</a>
			@endif
			@if($user->hasPermission('admin_finance.accounting.payment_posting') || $user->hasPermission('admin_finance.accounting'))
			<a href="{{ route('admin-finance.accounting.payment-posting.index') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.payment-posting.*') ? 'active' : '' }}">Payment Posting</a>
			@endif
			@if($user->hasPermission('admin_finance.accounting.auto_debit') || $user->hasPermission('admin_finance.accounting'))
			<a href="{{ route('admin-finance.accounting.auto-debits.index') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.auto-debits.*') ? 'active' : '' }}">Auto Debits</a>
			@endif
			@if($user->hasPermission('admin_finance.accounting'))
			<a href="{{ route('admin-finance.accounting.payment-requests') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.payment-requests') ? 'active' : '' }}">Payment Requests</a>
			<a href="{{ route('admin-finance.accounting.eford-payouts') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.eford-payouts*') ? 'active' : '' }}">E-FORD Payouts</a>
			@endif
			@if($user->hasPermission('admin_finance.accounting.office_supplies') || $user->hasPermission('admin_finance.accounting'))
			<a href="{{ route('admin-finance.accounting.office-supplies.index') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.office-supplies.*') ? 'active' : '' }}">Office Supplies</a>
			@endif
			@if($user->hasPermission('admin_finance.accounting.expenses') || $user->hasPermission('admin_finance.accounting'))
			<!-- <a href="{{ route('admin-finance.accounting.expenses.index') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.accounting.expenses.*') ? 'active' : '' }}">Expenses</a> -->
			@endif
		</div>
	</div>
    @endif

	<!-- Petty Cash Voucher (Finance) -->
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
			@if($user->hasPermission('admin_finance.credit_collection.billing'))
			<a href="{{ route('admin-finance.credit-collection.billing') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.credit-collection.billing') ? 'active' : '' }}">Billing</a>
			@endif
			@if($user->hasPermission('admin_finance.credit_collection.reports'))
			<a href="{{ route('admin-finance.credit-collection.reports') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.credit-collection.reports') ? 'active' : '' }}">Reports</a>
			@endif
			@if($user->hasPermission('admin_finance.credit_collection.invoice'))
			<a href="{{ route('admin-finance.credit-collection.invoice') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.credit-collection.invoice') ? 'active' : '' }}">Invoice</a>
			@endif
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
			@if($user->hasPermission('admin_finance.gsd.asset_management'))
			<a href="{{ route('admin-finance.gsd.asset-management') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.gsd.asset-management') ? 'active' : '' }}">Asset Management</a>
			@endif
			@if($user->hasPermission('admin_finance.gsd.job_orders'))
			<a href="{{ route('admin-finance.gsd.job-orders') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.gsd.job-orders') ? 'active' : '' }}">Job Orders</a>
			@endif
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
			@if($user->hasPermission('admin_finance.hr.job_orders'))
			<a href="{{ route('admin-finance.hr.job-orders') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.hr.job-orders') ? 'active' : '' }}">Job Orders</a>
			@endif
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
			@if($user->hasPermission('admin_finance.mis.job_orders'))
			<a href="{{ route('admin-finance.mis.job-orders') }}" class="modern-nav-subitem {{ request()->routeIs('admin-finance.mis.job-orders') ? 'active' : '' }}">Job Orders</a>
			@endif
		</div>
	</div>
    @endif

	<!-- Service Requests -->
	@if($hasServiceRequests)
	<a href="{{ route('admin-finance.service-requests.create') }}" class="modern-nav-item {{ request()->routeIs('admin-finance.service-requests.create') ? 'active' : '' }}" data-page="service-requests">
		<div class="modern-nav-icon">
			<i class="las la-tools"></i>
		</div>
		<span class="modern-nav-label">Create Service Request</span>
	</a>
	@endif

</nav>

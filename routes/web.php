<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\Marketing\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Production\InventoryController;
use App\Http\Controllers\Production\SiteController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\AdminFinanceController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
  // Dashboard
  Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
  Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
  Route::post('/profile/change-password', [App\Http\Controllers\ProfileController::class, 'changePassword'])->name('profile.change-password');
  Route::post('/profile/test-email', [App\Http\Controllers\ProfileController::class, 'testEmail'])->name('profile.test-email');

  // Universal Storage Fallback (Fix for servers without symlink support)
  Route::get('/storage/{path}', [FileController::class, 'serve'])->where('path', '.*');

  // Division Dashboards
  Route::get('/marketing', [MarketingController::class, 'dashboard'])->name('marketing.dashboard')->middleware('division.access:marketing');
  Route::get('/production', [DashboardController::class, 'production'])->name('production.dashboard')->middleware('division.access:production');

  // Production Division Routes - Protected by division.access:production middleware
  Route::middleware(['division.access:production'])->group(function () {
    Route::get('/production/approval-queue', [App\Http\Controllers\ProductionController::class, 'approvalQueue'])->name('production.approval-queue');
    Route::get('/production/my-requests', [App\Http\Controllers\ProductionController::class, 'myRequests'])->name('production.my-requests');
    Route::get('/production/sales-order/{id}/review', [App\Http\Controllers\ProductionController::class, 'reviewSalesOrder'])->name('production.sales-order.detail');
    Route::post('/production/sales-order/{id}/approve', [App\Http\Controllers\ProductionController::class, 'approveSalesOrder'])->name('production.sales-order.approve');
    Route::post('/production/sales-order/{id}/reject', [App\Http\Controllers\ProductionController::class, 'rejectSalesOrder'])->name('production.sales-order.reject');
    
    // Production Inventory Management
    Route::prefix('production/inventory')->name('production.inventory.')->group(function () {
      Route::get('/overview', [InventoryController::class, 'overview'])->name('overview');
      Route::get('/add-stock', [InventoryController::class, 'addStock'])->name('add-stock');
      Route::post('/store-stock', [InventoryController::class, 'storeStock'])->name('store-stock');
      Route::get('/received', [InventoryController::class, 'received'])->name('received');
      Route::get('/get-product-details/{id}', [InventoryController::class, 'getProductDetails'])->name('get-product-details');
      Route::put('/update-transaction/{id}', [InventoryController::class, 'updateTransaction'])->name('update-transaction');
      Route::delete('/destroy-transaction/{id}', [InventoryController::class, 'destroyTransaction'])->name('destroy-transaction');
      Route::delete('/destroy-product/{id}', [InventoryController::class, 'destroyProduct'])->name('destroy-product');
      Route::post('/process-add-stock', [InventoryController::class, 'processAddStock'])->name('process-add-stock');
      Route::post('/update-stock/{bookId}', [InventoryController::class, 'updateStockDirectly'])->name('update-stock');
    });

    // Site Management Routes
    Route::prefix('production/sites')->name('production.sites.')->group(function () {
      Route::post('/store', [App\Http\Controllers\Production\SiteController::class, 'store'])->name('store');
      Route::post('/add-stock', [App\Http\Controllers\Production\SiteController::class, 'addStock'])->name('add-stock');
      Route::post('/update/{id}', [App\Http\Controllers\Production\SiteController::class, 'updateSite'])->name('update');
      Route::post('/delete/{id}', [App\Http\Controllers\Production\SiteController::class, 'deleteSite'])->name('delete');
      Route::get('/{siteId}/inventory', [App\Http\Controllers\Production\SiteController::class, 'getInventory'])->name('inventory');
      Route::post('/transfer', [App\Http\Controllers\Production\SiteController::class, 'transfer'])->name('transfer');
      Route::post('/approve-transfer/{id}', [App\Http\Controllers\Production\SiteController::class, 'approveTransfer'])->name('approve-transfer');
      Route::post('/reject-transfer/{id}', [App\Http\Controllers\Production\SiteController::class, 'rejectTransfer'])->name('reject-transfer');
    });

    // Production Logistic Management
    Route::prefix('production/logistic')->name('production.logistic.')->group(function () {
      Route::get('/pick-lists', [App\Http\Controllers\Production\LogisticController::class, 'pickListList'])->name('pick-list-list');
      Route::get('/pick-lists/{id}', [App\Http\Controllers\Production\LogisticController::class, 'showPickList'])->name('pick-list-details');
      Route::get('/pick-list-management', [App\Http\Controllers\Production\LogisticController::class, 'pickListManagement'])->name('pick-list-management'); // Keep for form
      Route::post('/pick-list/save', [App\Http\Controllers\Production\LogisticController::class, 'savePickedItems'])->name('pick-list.save');
      Route::post('/mark-as-gathered/{id?}', [App\Http\Controllers\Production\LogisticController::class, 'markAsGathered'])->name('mark-as-gathered');
      Route::get('/pick-list/{id}/delete', [App\Http\Controllers\Production\LogisticController::class, 'deletePickList'])->name('pick-list-delete');
      
      // Packing Management
      Route::get('/packing-management', [App\Http\Controllers\Production\LogisticController::class, 'packingManagement'])->name('packing-management');
      Route::get('/packing/{id}/data', [App\Http\Controllers\Production\LogisticController::class, 'getPackingOrderData'])->name('packing-order-data');
      Route::post('/packing/save', [App\Http\Controllers\Production\LogisticController::class, 'savePackingData'])->name('packing.save');

      // Delivery & Fleet management
      Route::get('/delivery-scheduling', [App\Http\Controllers\Production\LogisticController::class, 'deliveryScheduling'])->name('delivery-scheduling');
      Route::post('/delivery-scheduling/{id}/delivered', [App\Http\Controllers\Production\LogisticController::class, 'markAsDelivered'])->name('mark-as-delivered');
      Route::post('/delivery-scheduling/{id}/assign-driver', [App\Http\Controllers\Production\LogisticController::class, 'assignDriver'])->name('assign-driver');
      Route::get('/delivery-scheduling/{id}/transmittal', [App\Http\Controllers\Production\LogisticController::class, 'printTransmittal'])->name('print-transmittal');
      Route::get('/driver-dashboard', [App\Http\Controllers\Production\LogisticController::class, 'driverDashboard'])->name('driver-dashboard');
      Route::get('/delivery-tracking', [App\Http\Controllers\Production\LogisticController::class, 'deliveryTracking'])->name('delivery-tracking');
      
      // Delivery Receipts (Restored)
      Route::get('/delivery-receipt-list', [App\Http\Controllers\Production\LogisticController::class, 'deliveryReceiptList'])->name('delivery-receipt-list');
      Route::get('/delivery-receipt/{id?}', [App\Http\Controllers\Production\LogisticController::class, 'deliveryReceipt'])->name('delivery-receipt');
      Route::post('/mark-as-dr-prepared/{id}', [App\Http\Controllers\Production\LogisticController::class, 'markAsDRPrepared'])->name('mark-as-dr-prepared');
      Route::post('/approve-dr/{id}', [App\Http\Controllers\Production\LogisticController::class, 'approveDR'])->name('approve-dr');

      // Purchase Orders
      Route::get('/purchase-order-list', [App\Http\Controllers\Production\LogisticController::class, 'purchaseOrderList'])->name('purchase-order-list');
      Route::get('/purchase-order', [App\Http\Controllers\Production\LogisticController::class, 'purchaseOrder'])->name('purchase-order');
      Route::get('/purchase-order/{id}', [App\Http\Controllers\Production\LogisticController::class, 'showPurchaseOrder'])->name('purchase-order.show');
      Route::post('/purchase-order', [App\Http\Controllers\Production\LogisticController::class, 'storePurchaseOrder'])->name('purchase-order.store');

      // Receiving Reports
      Route::get('/receiving-report-list', [App\Http\Controllers\Production\LogisticController::class, 'receivingReportList'])->name('receiving-report-list');
      Route::get('/receiving-report/create/{po_id?}', [App\Http\Controllers\Production\LogisticController::class, 'createReceivingReport'])->name('receiving-report.create');
      Route::get('/receiving-report/{id}', [App\Http\Controllers\Production\LogisticController::class, 'showReceivingReport'])->name('receiving-report.show');
      Route::post('/receiving-report', [App\Http\Controllers\Production\LogisticController::class, 'storeReceivingReport'])->name('receiving-report.store');
      
      // Freight Quotation
      Route::get('/freight-quotation', [App\Http\Controllers\Production\LogisticController::class, 'freightQuotation'])->name('freight-quotation');
      Route::post('/freight-quotation', [App\Http\Controllers\Production\LogisticController::class, 'storeFreightQuotation'])->name('freight-quotation.store');
      
      // Pending Freight Quotations from Marketing
      Route::get('/freight-quotations', [App\Http\Controllers\Production\LogisticController::class, 'pendingFreightQuotations'])->name('pending-freight-quotations');
      Route::get('/freight-quotations/{freightQuotation}', [App\Http\Controllers\Production\LogisticController::class, 'showFreightQuotation'])->name('show-freight-quotation');
      Route::post('/freight-quotations/{freightQuotation}/approve', [App\Http\Controllers\Production\LogisticController::class, 'approveFreightQuotation'])->name('approve-freight-quotation');
      Route::post('/freight-quotations/{freightQuotation}/reject', [App\Http\Controllers\Production\LogisticController::class, 'rejectFreightQuotation'])->name('reject-freight-quotation');
      
      // Delivery Form View
      Route::get('/delivery-form/{id}', [App\Http\Controllers\Production\LogisticController::class, 'viewDeliveryForm'])->name('view-delivery-form');
    });

    // Production DTO Management
    Route::prefix('production/dto')->name('production.dto.')->group(function () {
      Route::get('/job-request-form', [App\Http\Controllers\Production\DTOController::class, 'jobRequestForm'])->name('job-request-form');
      Route::post('/job-request-form', [App\Http\Controllers\Production\DTOController::class, 'storeJobRequest'])->name('job-request-form.store');
      Route::put('/job-request-form/{id}', [App\Http\Controllers\Production\DTOController::class, 'updateJobRequest'])->name('job-request-form.update');
      Route::delete('/job-request-form/{id}', [App\Http\Controllers\Production\DTOController::class, 'destroyJobRequest'])->name('job-request-form.destroy');
    });

    Route::prefix('production/ford')->name('production.ford.')->group(function () {
      Route::get('/auto-debit', [App\Http\Controllers\Production\FORDController::class, 'autoDebit'])->name('auto-debit');
      Route::get('/client-payment-posting', [App\Http\Controllers\Production\FORDController::class, 'clientPaymentPosting'])->name('client-payment-posting');
      Route::get('/eford-payout', [App\Http\Controllers\Production\FORDController::class, 'eFordPayout'])->name('eford-payout');
      Route::get('/payment-request', [App\Http\Controllers\Production\FORDController::class, 'paymentRequest'])->name('payment-request');
      Route::get('/purchase-order', [App\Http\Controllers\Production\FORDController::class, 'purchaseOrder'])->name('purchase-order');
      Route::get('/request-for-quotation', [App\Http\Controllers\Production\FORDController::class, 'requestForQuotation'])->name('request-for-quotation');
      Route::get('/sales-order', [App\Http\Controllers\Production\FORDController::class, 'salesOrder'])->name('sales-order');
      Route::post('/sales-order/{id}/approve', [App\Http\Controllers\Production\FORDController::class, 'approveSalesOrder'])->name('sales-order.approve');
      Route::get('/transmittal', [App\Http\Controllers\Production\FORDController::class, 'transmittal'])->name('transmittal');
    });

    // Production Printing Services
    Route::prefix('production/printing')->name('production.printing.')->group(function () {
      Route::get('/request-payment-to-printer', [App\Http\Controllers\Production\PrintingServicesController::class, 'requestPaymentToPrinter'])->name('request-payment-to-printer');
    });
  });

  // User Management - Accessible to all authenticated users
  Route::get('/super-admin/users', [SuperAdminController::class, 'users'])->name('users.index');
  Route::post('/super-admin/users', [SuperAdminController::class, 'store'])->name('users.store');
  Route::put('/super-admin/users/{id}', [SuperAdminController::class, 'update'])->name('users.update');
  Route::delete('/super-admin/users/{id}', [SuperAdminController::class, 'destroy'])->name('users.destroy');

  // Super Admin Pages - Protected by super.admin middleware (Roles, Divisions, Settings only)
  Route::middleware(['super.admin'])->group(function () {
    Route::get('/super-admin/roles', [SuperAdminController::class, 'roles'])->name('roles.index');
    Route::put('/super-admin/roles/{id}', [SuperAdminController::class, 'updateRole'])->name('roles.update');
    Route::put('/super-admin/roles/{id}/permissions', [SuperAdminController::class, 'updateRolePermissions'])->name('roles.permissions.update');
    Route::get('/super-admin/divisions', [SuperAdminController::class, 'divisions'])->name('divisions.index');
    Route::get('/super-admin/settings', [SuperAdminController::class, 'settings'])->name('settings.index');
    Route::post('/super-admin/payment-settings', [App\Http\Controllers\POSController::class, 'savePaymentSettings'])->name('settings.payment.save');
    // Maintenance: Clear Data (Super Admin only)
    Route::get('/super-admin/maintenance/clear-data', [App\Http\Controllers\MaintenanceController::class, 'showClearData'])->name('admin.maintenance.clear-data');
    Route::post('/super-admin/maintenance/clear-data', [App\Http\Controllers\MaintenanceController::class, 'clearData'])->name('admin.maintenance.clear-data.post');
  });

  // Marketing Pages - Protected by division.access:marketing middleware
  Route::middleware(['division.access:marketing'])->group(function () {
    Route::resource('marketing/customers', CustomerController::class)->names([
        'index' => 'marketing.customers',
        'create' => 'marketing.customers.create',
        'store' => 'marketing.customers.store',
        'show' => 'marketing.customers.show',
        'edit' => 'marketing.customers.edit',
        'update' => 'marketing.customers.update',
        'destroy' => 'marketing.customers.destroy',
    ]);
    Route::get('/marketing/customers/{customer}/history', [CustomerController::class, 'getTransactionHistory'])->name('marketing.customers.history');
    Route::post('/marketing/customers/{customer}/manual-status', [CustomerController::class, 'updateManualStatus'])->name('marketing.customers.update-status');
    Route::get('/marketing/approval-queue', [MarketingController::class, 'approvalQueue'])->name('marketing.approval-queue');
    Route::get('/marketing/my-requests', [MarketingController::class, 'myRequests'])->name('marketing.my-requests');
    
    // Book Management (Master Registry)
    Route::get('/marketing/book-list', [MarketingController::class, 'products'])->name('marketing.products');
    Route::post('/marketing/book-list/store-book', [MarketingController::class, 'storeBook'])->name('marketing.books.store');
    Route::get('/marketing/book-list/{id}/edit-book', [MarketingController::class, 'editBook'])->name('marketing.books.edit');
    Route::post('/marketing/book-list/{id}/update-book', [MarketingController::class, 'updateBook'])->name('marketing.books.update');
    Route::delete('/marketing/book-list/{id}', [MarketingController::class, 'destroyBook'])->name('marketing.books.destroy');

    // POS Listed Products
    Route::post('/marketing/pos-products/store', [MarketingController::class, 'storeProduct'])->name('marketing.products.store');
    Route::get('/marketing/pos-products/{id}/edit', [MarketingController::class, 'editProduct'])->name('marketing.products.edit');
    Route::post('/marketing/pos-products/{id}/update', [MarketingController::class, 'updateProduct'])->name('marketing.products.update');
    Route::delete('/marketing/pos-products/{id}', [MarketingController::class, 'destroyProduct'])->name('marketing.products.destroy');

    // Dynamic Categories
    Route::get('/marketing/book-categories', [MarketingController::class, 'getCategories'])->name('marketing.categories.index');
    Route::post('/marketing/book-categories', [MarketingController::class, 'storeCategory'])->name('marketing.categories.store');
    Route::get('/marketing/book-categories/{id}/subcategories', [MarketingController::class, 'getSubcategories'])->name('marketing.categories.subcategories');
    Route::delete('/marketing/book-categories/{id}', [MarketingController::class, 'destroyCategory'])->name('marketing.categories.destroy');

    // Consignment Management
    Route::prefix('marketing/consignment')->name('marketing.consignment.')->group(function () {
        Route::get('/', [App\Http\Controllers\Marketing\ConsignmentController::class, 'index'])->name('index');
        Route::post('/owners', [App\Http\Controllers\Marketing\ConsignmentController::class, 'storeOwner'])->name('owners.store');
        Route::get('/owners/{id}', [App\Http\Controllers\Marketing\ConsignmentController::class, 'getOwnerDetails'])->name('owners.show');
        Route::put('/owners/{id}', [App\Http\Controllers\Marketing\ConsignmentController::class, 'updateOwner'])->name('owners.update');
        Route::delete('/owners/{id}', [App\Http\Controllers\Marketing\ConsignmentController::class, 'destroyOwner'])->name('owners.destroy');
        Route::post('/books/{id}/update', [App\Http\Controllers\Marketing\ConsignmentController::class, 'updateBookConsignment'])->name('books.update');
        Route::post('/owners/{id}/settle', [App\Http\Controllers\Marketing\ConsignmentController::class, 'settle'])->name('owners.settle');
    });

    // Area Sales
    Route::get('/marketing/sales-orders/list', [MarketingController::class, 'salesOrdersList'])->name('marketing.sales-orders.list');
    Route::get('/marketing/sales-orders/create', [MarketingController::class, 'createSalesOrder'])->name('marketing.sales-orders.create');
    Route::post('/marketing/sales-orders/store', [MarketingController::class, 'storeSalesOrder'])->name('marketing.sales-orders.store');
    Route::get('/marketing/sales-orders', function() { return redirect()->route('marketing.sales-orders.list'); });
    Route::get('/marketing/sales-orders/{id}/edit', [MarketingController::class, 'editSalesOrder'])->name('marketing.sales-orders.edit');
    Route::put('/marketing/sales-orders/{id}', [MarketingController::class, 'updateSalesOrder'])->name('marketing.sales-orders.update');
    Route::post('/marketing/sales-orders/{id}/approve', [MarketingController::class, 'approveSalesOrder'])->name('marketing.sales-orders.approve');
    Route::post('/marketing/sales-orders/{id}/proceed-to-final', [MarketingController::class, 'proceedToFinalSalesOrder'])->name('marketing.sales-orders.proceed-to-final');
    Route::delete('/marketing/sales-orders/{id}', [MarketingController::class, 'destroySalesOrder'])->name('marketing.sales-orders.destroy');
    Route::get('/marketing/sales-order/{id?}', [MarketingController::class, 'salesOrderDetail'])->name('marketing.sales-orders.detail');
    Route::get('/marketing/sales-orders/{id}/shipping-label', [MarketingController::class, 'shippingLabel'])->name('marketing.sales-orders.shipping-label');
    Route::get('/marketing/direct-invoice-website', [MarketingController::class, 'directInvoiceWebsite'])->name('marketing.direct-invoice.website');
    Route::post('/marketing/direct-invoice-website', [MarketingController::class, 'storeDirectInvoice'])->name('marketing.direct-invoice.website.store');
    Route::get('/marketing/direct-invoice-website/list', [MarketingController::class, 'directInvoiceList'])->name('marketing.direct-invoice.website.list');
    Route::post('/marketing/direct-invoice-website/{id}/approve', [MarketingController::class, 'approveDirectInvoice'])->name('marketing.direct-invoice.website.approve');
    Route::get('/marketing/direct-invoice-ecom', [MarketingController::class, 'directInvoiceEcom'])->name('marketing.direct-invoice.ecom');
    Route::post('/marketing/direct-invoice-ecom', [MarketingController::class, 'storeDirectInvoiceEcom'])->name('marketing.direct-invoice.ecom.store');
    Route::post('/marketing/direct-invoice-ecom/{id}/approve', [MarketingController::class, 'approveDirectInvoiceEcom'])->name('marketing.direct-invoice.ecom.approve');
    
    // Freight Quotations (Area Sales)
    Route::prefix('marketing/freight-quotations')->name('marketing.freight-quotations.')->group(function () {
        Route::get('/', [App\Http\Controllers\Marketing\FreightQuotationController::class, 'list'])->name('list');
        Route::get('/create', [App\Http\Controllers\Marketing\FreightQuotationController::class, 'create'])->name('create');
        Route::post('/store', [App\Http\Controllers\Marketing\FreightQuotationController::class, 'store'])->name('store');
        Route::get('/{freightQuotation}', [App\Http\Controllers\Marketing\FreightQuotationController::class, 'show'])->name('show');
        Route::post('/{freightQuotation}/create-so-directly', [App\Http\Controllers\Marketing\FreightQuotationController::class, 'createSalesOrderFromApprovedQuotation'])->name('create-so-directly');
        Route::get('/{freightQuotation}/proceed-to-so', [App\Http\Controllers\Marketing\FreightQuotationController::class, 'proceedToSalesOrder'])->name('proceed-to-so');
        Route::post('/{freightQuotation}/create-so', [App\Http\Controllers\Marketing\FreightQuotationController::class, 'createSalesOrderFromQuotation'])->name('create-so');
        Route::get('/{freightQuotation}/logistics-response', [App\Http\Controllers\Marketing\FreightQuotationController::class, 'viewLogisticsResponse'])->name('logistics-response');
    });
    
    Route::get('/marketing/sales-orders/{id}', [MarketingController::class, 'salesOrderDetail'])->name('marketing.sales-orders.show');
    
    Route::get('/marketing/acknowledgement-receipt', [MarketingController::class, 'acknowledgementReceipt'])->name('marketing.acknowledgement-receipt');
    Route::get('/marketing/credit-memo', [MarketingController::class, 'creditMemo'])->name('marketing.credit-memo');
    Route::get('/marketing/proof-of-payment', [MarketingController::class, 'proofOfPayment'])->name('marketing.proof-of-payment');
    Route::get('/marketing/sales-invoice', [MarketingController::class, 'salesInvoice'])->name('marketing.sales-invoice');
    Route::get('/marketing/pick-list-management', [MarketingController::class, 'pickListManagement'])->name('marketing.pick-list.management');
    Route::get('/marketing/pick-lists', [MarketingController::class, 'pickLists'])->name('marketing.pick-lists');
    Route::get('/marketing/delivery-receipt', [MarketingController::class, 'deliveryReceipt'])->name('marketing.delivery-receipt');
    Route::get('/marketing/delivery-receipt-list', [MarketingController::class, 'deliveryReceiptList'])->name('marketing.delivery-receipt.list');
    Route::get('/marketing/order-fulfillment', [MarketingController::class, 'orderFulfillment'])->name('marketing.order-fulfillment');
    Route::get('/marketing/packing-scheduling', [MarketingController::class, 'packingScheduling'])->name('marketing.packing-scheduling');
    Route::get('/marketing/delivery-scheduling', [MarketingController::class, 'deliveryScheduling'])->name('marketing.delivery-scheduling');
    Route::get('/marketing/delivery-tracking', [MarketingController::class, 'deliveryTracking'])->name('marketing.delivery-tracking');
    Route::get('/marketing/sales-reports', [MarketingController::class, 'salesReports'])->name('marketing.sales-reports');
    Route::get('/marketing/territory-management', [MarketingController::class, 'territoryManagement'])->name('marketing.territory-management');


    // Direct Sales
    Route::get('/marketing/pos-sale', [MarketingController::class, 'posSale'])->name('marketing.pos.sale');
    Route::get('/marketing/pos-products', [MarketingController::class, 'posProducts'])->name('marketing.pos.products');

    // NBS PO Import
    Route::get('/marketing/nbs-import', [App\Http\Controllers\Marketing\NBSImportController::class, 'index'])->name('marketing.nbs-import.index');
    Route::post('/marketing/nbs-import/process', [App\Http\Controllers\Marketing\NBSImportController::class, 'process'])->name('marketing.nbs-import.process');
    Route::get('/marketing/nbs-consignment-receipt/{id}', [App\Http\Controllers\Marketing\NBSImportController::class, 'viewReceipt'])->name('marketing.nbs-consignment-receipt');

    // POS Order Processing
    Route::post('/marketing/pos/process-order', [App\Http\Controllers\POSController::class, 'processOrder'])->name('marketing.pos.process-order');
    Route::get('/marketing/pos/orders', [App\Http\Controllers\POSController::class, 'getOrders'])->name('marketing.pos.orders');
    Route::get('/marketing/pos/orders/{id}', [App\Http\Controllers\POSController::class, 'getOrderDetails'])->name('marketing.pos.order-details');
    Route::get('/marketing/pos/payment-settings', [App\Http\Controllers\POSController::class, 'getPaymentSettings'])->name('marketing.pos.payment-settings');
    Route::post('/marketing/pos/lookup-barcode', [App\Http\Controllers\POSController::class, 'lookupByBarcode'])->name('marketing.pos.lookup-barcode');
    Route::post('/marketing/pos/process-ecom-order', [App\Http\Controllers\POSController::class, 'processEcomOrder'])->name('marketing.pos.process-ecom-order');

    // E-Com
    Route::get('/marketing/ecom-pos', [MarketingController::class, 'ecomPos'])->name('marketing.ecom.pos');

    // Ads and Promo
    Route::get('/marketing/ads-promo/crpr', [MarketingController::class, 'crpr'])->name('marketing.ads-promo.crpr');
    Route::get('/marketing/ads-promo/sponsors', [MarketingController::class, 'sponsors'])->name('marketing.ads-promo.sponsors');
    Route::post('/marketing/ads-promo/sponsors/store', [MarketingController::class, 'storeSponsor'])->name('marketing.ads-promo.sponsors.store');
    Route::post('/marketing/ads-promo/sponsors/{id}/update', [MarketingController::class, 'updateSponsor'])->name('marketing.ads-promo.sponsors.update');
    Route::delete('/marketing/ads-promo/sponsors/{id}', [MarketingController::class, 'destroySponsor'])->name('marketing.ads-promo.sponsors.destroy');

    // Suppliers & Purchases
    Route::get('/marketing/suppliers', [SupplierController::class, 'index'])->name('marketing.suppliers');
    Route::resource('suppliers', SupplierController::class)->except(['index']);
    Route::get('/marketing/purchase-orders', [MarketingController::class, 'purchaseOrders'])->name('marketing.purchase-orders');
  });

  Route::post('/employee/cash-advance/store', [App\Http\Controllers\EmployeeCashAdvanceController::class, 'store'])->name('employee.cash-advance.store');
  Route::put('/employee/cash-advance/{id}', [App\Http\Controllers\EmployeeCashAdvanceController::class, 'update'])->name('employee.cash-advance.update');
    
  // Admin & Finance Division - Protected by division.access:admin-finance middleware
  Route::middleware(['division.access:admin-finance'])->prefix('admin-finance')->group(function () {
    Route::get('/', [App\Http\Controllers\AdminFinanceController::class, 'dashboard'])->name('admin-finance.dashboard');
    Route::get('/my-requests', [App\Http\Controllers\AdminFinanceController::class, 'myRequests'])->name('admin-finance.my-requests');
    Route::get('/approval-queue', [App\Http\Controllers\AdminFinanceController::class, 'approvalQueue'])->name('admin-finance.approval-queue');
    Route::get('/memo-list', [App\Http\Controllers\AdminFinanceController::class, 'memoList'])->name('admin-finance.memo-list');
    Route::get('/sales-order/{id}/review', [App\Http\Controllers\AdminFinanceController::class, 'reviewSalesOrder'])->name('admin-finance.sales-order.detail');
    Route::post('/sales-order/{id}/approve', [App\Http\Controllers\AdminFinanceController::class, 'approveSalesOrder'])->name('admin-finance.sales-order.approve');
    Route::post('/sales-order/{id}/reject', [App\Http\Controllers\AdminFinanceController::class, 'rejectSalesOrder'])->name('admin-finance.sales-order.reject');

    // Accounting
    Route::prefix('accounting')->group(function () {
      Route::get('/sales-invoice', [App\Http\Controllers\AdminFinanceController::class, 'salesInvoice'])->name('admin-finance.accounting.sales-invoice');
      Route::get('/sales-invoice/{id}/prepare', [App\Http\Controllers\AdminFinanceController::class, 'prepareSalesInvoice'])->name('admin-finance.accounting.sales-invoice.prepare');
      Route::post('/sales-invoice/{id}/store', [App\Http\Controllers\AdminFinanceController::class, 'storeSalesInvoice'])->name('admin-finance.accounting.sales-invoice.store');
      Route::post('/sales-invoice/{id}/sign', [App\Http\Controllers\AdminFinanceController::class, 'signSalesInvoice'])->name('admin-finance.accounting.sales-invoice.sign');
      Route::get('/sales-invoice/{id}/print', [App\Http\Controllers\AdminFinanceController::class, 'printSalesInvoice'])->name('admin-finance.accounting.sales-invoice.print');
      Route::get('/acknowledgement-receipt/{id}/prepare', [App\Http\Controllers\AdminFinanceController::class, 'prepareAR'])->name('admin-finance.accounting.ar.prepare');
      Route::post('/acknowledgement-receipt/{id}/store', [App\Http\Controllers\AdminFinanceController::class, 'storeAR'])->name('admin-finance.accounting.ar.store');
      Route::get('/check-voucher', [App\Http\Controllers\AdminFinanceController::class, 'checkVoucherIndex'])->name('admin-finance.check-voucher');
      Route::get('/check-voucher/create', [App\Http\Controllers\AdminFinanceController::class, 'checkVoucher'])->name('admin-finance.check-voucher.create');
      Route::post('/check-voucher', [App\Http\Controllers\AdminFinanceController::class, 'storeCheckVoucher'])->name('admin-finance.check-voucher.store');
      Route::get('/check-voucher/{id}', [App\Http\Controllers\AdminFinanceController::class, 'showCheckVoucher'])->name('admin-finance.check-voucher.show');

      Route::get('/materials-requisition', [App\Http\Controllers\AdminFinanceController::class, 'materialsRequisition'])->name('admin-finance.accounting.materials-requisition');
      Route::post('/materials-requisition', [App\Http\Controllers\AdminFinanceController::class, 'storeMaterialRequisition'])->name('admin-finance.accounting.materials-requisition.store');
      Route::get('/materials-requisition/{id}', [App\Http\Controllers\AdminFinanceController::class, 'showMaterialRequisition'])->name('admin-finance.accounting.materials-requisition.show');
      Route::delete('/materials-requisition/{id}', [App\Http\Controllers\AdminFinanceController::class, 'destroyMaterialRequisition'])->name('admin-finance.accounting.materials-requisition.destroy');
      Route::get('/material-requests', [App\Http\Controllers\AdminFinanceController::class, 'materialRequestsIncoming'])->name('admin-finance.accounting.material-requests.incoming');
      Route::get('/expense-management', [App\Http\Controllers\AdminFinanceController::class, 'expenseManagement'])->name('admin-finance.accounting.expense-management');
      Route::get('/cash-advance/create', [App\Http\Controllers\AdminFinanceController::class, 'createCashAdvance'])->name('admin-finance.accounting.cash-advance.create');
      
      // General Journal Entry
      Route::prefix('journal')->name('accounting.journal.')->group(function () {
          Route::get('/', [App\Http\Controllers\Accounting\JournalEntryController::class, 'index'])->name('index');
          Route::get('/create', [App\Http\Controllers\Accounting\JournalEntryController::class, 'create'])->name('create');
          Route::post('/', [App\Http\Controllers\Accounting\JournalEntryController::class, 'store'])->name('store');
          Route::get('/{id}', [App\Http\Controllers\Accounting\JournalEntryController::class, 'show'])->name('show');
          Route::delete('/{id}', [App\Http\Controllers\Accounting\JournalEntryController::class, 'destroy'])->name('destroy');
      });
    });

    // Credit Collection
    Route::prefix('credit-collection')->group(function () {
      Route::get('/billing', [App\Http\Controllers\AdminFinanceController::class, 'billing'])->name('admin-finance.credit-collection.billing');
      Route::get('/billing/create/{id}', [App\Http\Controllers\AdminFinanceController::class, 'createAccountStatement'])->name('admin-finance.credit-collection.billing.create');
      Route::post('/billing/store', [App\Http\Controllers\AdminFinanceController::class, 'storeAccountStatement'])->name('admin-finance.credit-collection.billing.store');
      Route::post('/billing/{id}/update-status', [App\Http\Controllers\AdminFinanceController::class, 'updateStatementStatus'])->name('admin-finance.credit-collection.billing.update-status');
      Route::get('/billing/edit/{id}', [App\Http\Controllers\AdminFinanceController::class, 'editAccountStatement'])->name('admin-finance.credit-collection.billing.edit');
      Route::get('/billing/{id}', [App\Http\Controllers\AdminFinanceController::class, 'showAccountStatement'])->name('admin-finance.credit-collection.billing.show');
      Route::post('/billing/compile', [App\Http\Controllers\AdminFinanceController::class, 'compileStatements'])->name('admin-finance.credit-collection.billing.compile');

      Route::get('/freight-billing/create', [App\Http\Controllers\AdminFinanceController::class, 'createFreightBill'])->name('admin-finance.credit-collection.freight-billing.create');
      Route::post('/freight-billing/store', [App\Http\Controllers\AdminFinanceController::class, 'storeFreightBill'])->name('admin-finance.credit-collection.freight-billing.store');
      Route::post('/freight-billing/{id}/update-status', [App\Http\Controllers\AdminFinanceController::class, 'updateFreightBillStatus'])->name('admin-finance.credit-collection.freight-billing.update-status');
      Route::delete('/freight-billing/{id}', [App\Http\Controllers\AdminFinanceController::class, 'destroyFreightBill'])->name('admin-finance.credit-collection.freight-billing.destroy');
      Route::post('/freight-billing/compile', [App\Http\Controllers\AdminFinanceController::class, 'compileFreightBills'])->name('admin-finance.credit-collection.freight-billing.compile');

      Route::get('/reports', [App\Http\Controllers\AdminFinanceController::class, 'reports'])->name('admin-finance.credit-collection.reports');
      Route::get('/invoice', [App\Http\Controllers\AdminFinanceController::class, 'invoice'])->name('admin-finance.credit-collection.invoice');

      // JV Requests
      Route::get('/jv-requests', [AdminFinanceController::class, 'jvRequests'])->name('admin-finance.credit-collection.jv-requests.index');
      Route::get('/jv-requests/create', [AdminFinanceController::class, 'createJvRequest'])->name('admin-finance.credit-collection.jv-requests.create');
      Route::post('/jv-requests/store', [AdminFinanceController::class, 'storeJvRequest'])->name('admin-finance.credit-collection.jv-requests.store');
      Route::get('/jv-requests/{id}', [AdminFinanceController::class, 'showJvRequest'])->name('admin-finance.credit-collection.jv-requests.show');
      Route::put('/jv-requests/{id}/approve', [AdminFinanceController::class, 'approveJvRequest'])->name('admin-finance.credit-collection.jv-requests.approve');
      Route::put('/jv-requests/{id}/manager-approve', [AdminFinanceController::class, 'managerApproveJvRequest'])->name('admin-finance.credit-collection.jv-requests.manager-approve');
      Route::put('/jv-requests/{id}/reject', [AdminFinanceController::class, 'rejectJvRequest'])->name('admin-finance.credit-collection.jv-requests.reject');
      Route::get('/jv-requests/{id}/print', [AdminFinanceController::class, 'printJvRequest'])->name('admin-finance.credit-collection.jv-requests.print');
      Route::get('/jv-requests/{id}/print-summary', [AdminFinanceController::class, 'printSummaryRequest'])->name('admin-finance.credit-collection.jv-requests.print-summary');
      Route::get('/jv-requests/{id}/prepare-adjustment', [AdminFinanceController::class, 'prepareAdjustmentRequest'])->name('admin-finance.credit-collection.jv-requests.prepare-adjustment');
      Route::post('/jv-requests/{id}/update-adjustment', [AdminFinanceController::class, 'updateAdjustmentRequest'])->name('admin-finance.credit-collection.jv-requests.update-adjustment');
      Route::get('/jv-requests/{id}/download-supporting', [AdminFinanceController::class, 'downloadSupportingDocuments'])->name('admin-finance.credit-collection.jv-requests.download-supporting');
    });

    // Expenses
    Route::prefix('expenses')->group(function () {
      Route::get('/cash-advance-liquidation', [App\Http\Controllers\AdminFinanceController::class, 'cashAdvanceLiquidation'])->name('admin-finance.expenses.cash-advance-liquidation');
      Route::post('/cash-advance-liquidation', [App\Http\Controllers\AdminFinanceController::class, 'storeLiquidation'])->name('admin-finance.expenses.cash-advance-liquidation.store');
    });

    // GSD
    Route::prefix('gsd')->group(function () {
      Route::get('/asset-management', [App\Http\Controllers\Admin\GSD\AssetController::class, 'index'])->name('admin-finance.gsd.asset-management');
      Route::resource('/asset-requests', App\Http\Controllers\Admin\GSD\AssetController::class)->names('admin-finance.gsd.asset-requests');
      Route::get('/job-orders', [App\Http\Controllers\AdminFinanceController::class, 'gsdJobOrders'])->name('admin-finance.gsd.job-orders');
      Route::put('/job-orders/{type}/{id}/update-status', [App\Http\Controllers\AdminFinanceController::class, 'gsdUpdateJobOrderStatus'])->name('admin-finance.gsd.job-orders.update-status');
    });

    // HR
    Route::prefix('hr')->group(function () {
      Route::get('/job-orders', [App\Http\Controllers\AdminFinanceController::class, 'hrJobOrders'])->name('admin-finance.hr.job-orders');
    });

    // MIS
    Route::prefix('mis')->group(function () {
      Route::get('/job-orders', [App\Http\Controllers\AdminFinanceController::class, 'jobOrders'])->name('admin-finance.mis.job-orders');
      Route::resource('/cctv-requests', App\Http\Controllers\Admin\MIS\CCTVReqController::class)->names('admin-finance.mis.cctv-requests');
      Route::resource('/material-requests', App\Http\Controllers\Admin\MIS\MaterialReqController::class)->names('admin-finance.mis.material-requests');
      Route::resource('/qb-requests', App\Http\Controllers\Admin\MIS\QBReqController::class)->names('admin-finance.mis.qb-requests');
      Route::resource('/undertime-requests', App\Http\Controllers\Admin\MIS\UndertimeReqController::class)->names('admin-finance.mis.undertime-requests');
      Route::resource('/service-requests', App\Http\Controllers\Admin\MIS\ServiceReqController::class)->names('admin-finance.mis.service-requests');
    });
  });
});

// Petty Cash Vouchers - Accessible by permission only, not division-restricted
Route::prefix('admin-finance')->group(function () {
  Route::get('/petty-cash', [App\Http\Controllers\Accounting\PettyCashController::class, 'index'])->name('admin-finance.petty-cash.index');
  Route::get('/petty-cash/create', [App\Http\Controllers\Accounting\PettyCashController::class, 'create'])->name('admin-finance.petty-cash.create');
  Route::post('/petty-cash', [App\Http\Controllers\Accounting\PettyCashController::class, 'store'])->name('admin-finance.petty-cash.store');
  Route::get('/petty-cash/summary', [App\Http\Controllers\Accounting\PettyCashController::class, 'summary'])->name('admin-finance.petty-cash.summary');
  Route::post('/petty-cash/liquidate', [App\Http\Controllers\Accounting\PettyCashController::class, 'liquidate'])->name('admin-finance.petty-cash.liquidate');
  Route::get('/petty-cash/{id}', [App\Http\Controllers\Accounting\PettyCashController::class, 'show'])->name('admin-finance.petty-cash.show');
  Route::delete('/petty-cash/{id}', [App\Http\Controllers\Accounting\PettyCashController::class, 'destroy'])->name('admin-finance.petty-cash.destroy');
});

// Freight Vouchers - Accessible by permission only, not division-restricted
Route::prefix('admin-finance')->group(function () {
  Route::get('/freight-voucher', [App\Http\Controllers\Accounting\FreightVoucherController::class, 'index'])->name('admin-finance.freight-voucher.index');
  Route::get('/freight-voucher/create', [App\Http\Controllers\Accounting\FreightVoucherController::class, 'create'])->name('admin-finance.freight-voucher.create');
  Route::post('/freight-voucher', [App\Http\Controllers\Accounting\FreightVoucherController::class, 'store'])->name('admin-finance.freight-voucher.store');
  Route::get('/freight-voucher/{id}', [App\Http\Controllers\Accounting\FreightVoucherController::class, 'show'])->name('admin-finance.freight-voucher.show');
  Route::delete('/freight-voucher/{id}', [App\Http\Controllers\Accounting\FreightVoucherController::class, 'destroy'])->name('admin-finance.freight-voucher.destroy');
});

// COD Payment & Rider Collections - Protected Routes
Route::middleware(['auth'])->group(function () {
  // Rider Collection Routes
  Route::prefix('rider/collections')->name('rider.collections.')->group(function () {
    Route::get('/', [App\Http\Controllers\RiderCollectionController::class, 'index'])->name('index');
    // Specific routes must come BEFORE generic {id} route
    Route::get('/awaiting/handover', [App\Http\Controllers\RiderCollectionController::class, 'awaitingHandover'])->name('awaiting-handover');
    Route::get('/daily/summary', [App\Http\Controllers\RiderCollectionController::class, 'dailySummary'])->name('daily-summary');
    // Generic routes last
    Route::get('/{id}', [App\Http\Controllers\RiderCollectionController::class, 'show'])->name('show');
    Route::post('/{id}/record', [App\Http\Controllers\RiderCollectionController::class, 'recordCollection'])->name('record');
    Route::post('/{id}/hand-over', [App\Http\Controllers\RiderCollectionController::class, 'handOver'])->name('hand-over');
    Route::post('/{id}/evaluation-selection', [App\Http\Controllers\RiderCollectionController::class, 'recordEvaluationSelection'])->name('evaluation-selection');
  });

  // Cashier Payment Verification Routes (Admin & Finance)
  Route::prefix('admin-finance/cashier')->name('cashier.')->middleware('division.access:admin-finance')->group(function () {
    Route::get('/collections', [App\Http\Controllers\Accounting\CashierPaymentController::class, 'index'])->name('collections.index');
    Route::get('/collections/{id}', [App\Http\Controllers\Accounting\CashierPaymentController::class, 'show'])->name('collections.show');
    Route::post('/collections/{id}/verify', [App\Http\Controllers\Accounting\CashierPaymentController::class, 'verify'])->name('collections.verify');
    Route::post('/collections/{id}/reject', [App\Http\Controllers\Accounting\CashierPaymentController::class, 'reject'])->name('collections.reject');
    Route::get('/daily-report', [App\Http\Controllers\Accounting\CashierPaymentController::class, 'dailyReport'])->name('daily-report');
    Route::get('/export', [App\Http\Controllers\Accounting\CashierPaymentController::class, 'exportForAccounting'])->name('export');
  });
});

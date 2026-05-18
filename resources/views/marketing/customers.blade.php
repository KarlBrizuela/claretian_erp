<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        /* Detailed Customer Form Styles */
        .customer-modal-header-info {
            padding: 1rem 1.5rem;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .customer-tab-container {
            display: flex;
            min-height: 500px;
        }

        .customer-nav-tabs {
            width: 180px;
            border-right: 1px solid #dee2e6;
            background: #f1f1f1;
            padding-top: 1rem;
        }

        .customer-nav-tabs .nav-link {
            border: none;
            border-radius: 0;
            text-align: left;
            padding: 10px 15px;
            color: #444;
            font-weight: 500;
            font-size: 0.9rem;
            border-left: 4px solid transparent;
        }

        .customer-nav-tabs .nav-link:hover {
            background: #e9e9e9;
        }

        .customer-nav-tabs .nav-link.active {
            background: #fff;
            color: #000;
            border-left-color: #ff0000;
            margin-right: -1px;
            border-bottom: 1px solid #dee2e6;
            border-top: 1px solid #dee2e6;
        }

        .customer-tab-content {
            flex: 1;
            padding: 1.5rem;
            background: #fff;
        }

        .form-row-custom {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
            gap: 1rem;
        }

        .form-row-custom label {
            width: 135px;
            text-align: right;
            font-size: 0.85rem;
            font-weight: 600;
            color: #555;
            margin-bottom: 0;
        }

        .form-row-custom .form-control-sm,
        .form-row-custom .form-select-sm {
            flex: 1;
            border-radius: 2px;
            border: 1px solid #ccc;
        }

        .section-divider {
            border-bottom: 1px solid #eee;
            margin: 1.5rem 0 1rem;
            font-weight: 700;
            font-size: 0.85rem;
            color: #333;
            text-transform: uppercase;
            padding-bottom: 5px;
        }

        .address-box-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .address-box {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #fafafa;
        }

        .address-box textarea {
            width: 100%;
            height: 80px;
            border: 1px solid #eee;
            padding: 5px;
            font-size: 0.85rem;
        }

        .credit-card-section {
            border: 1px solid #dee2e6;
            padding: 1rem;
            border-radius: 4px;
            margin-top: 1rem;
        }

        .credit-card-label {
            position: relative;
            top: -22px;
            left: 10px;
            background: #fff;
            padding: 0 5px;
            font-weight: 700;
            font-size: 0.8rem;
            color: #666;
            display: inline-block;
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header border-0 d-block d-sm-flex">
                    <div>
                        <h4 class="fs-20 mb-0 text-black">Customer List</h4>
                    </div>
                    <div class="d-flex align-items-center mt-3 mt-sm-0">
                        <input type="text" class="form-control me-3" placeholder="Search customers..."
                            id="customerSearch" style="max-width: 300px;">
                        <a href="javascript:void(0);"
                            class="btn btn-primary rounded d-flex align-items-center" data-bs-toggle="modal"
                            data-bs-target="#addCustomerModal"
                            style="gap: 0.5rem; padding: 0.5rem 1rem; height: 38px; min-height: 38px; line-height: 1.5; box-sizing: border-box; border: none; background: #ff0000; color: #ffffff; font-weight: 500;">
                            <i class="las la-plus"
                                style="font-size: 1rem; line-height: 1; margin: 0; padding: 0; background: transparent; border: none; box-shadow: none;"></i>
                            <span style="font-size: 0.875rem; white-space: nowrap;">Add New Customer</span>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-responsive-md">
                            <thead>
                                <tr>
                                    <th><strong>CUSTOMER ID</strong></th>
                                    <th><strong>CUSTOMER NAME</strong></th>
                                    <th><strong>PHONE NUMBER</strong></th>
                                    <th><strong>EMAIL</strong></th>
                                    <th><strong>ADDRESS</strong></th>
                                    <th><strong>STATUS</strong></th>
                                    <th><strong>ACTION</strong></th>
                                </tr>
                            </thead>
                            <tbody id="customerTableBody">
                              @forelse($customers as $customer)
                               <tr>
                                  <td><strong>{{$customer->customer_id}}</strong></td>
                                  <td>
                                    <div class="d-flex align-items-center">
                                      <span class="w-space-no" style="font-size: 13px;">{{$customer->customer_name}}</span>
                                    </div>
                                  </td>
                                  <td>{{$customer->mobile}}</td>
                                  <td>{{$customer->main_email}}</td>
                                  <td>{{$customer->shipping_address}}</td>
                                  <td>
                                      <div class="d-flex flex-column">
                                        @if($customer->is_bad_client)
                                          <span class="badge light badge-danger"><i class="fa fa-circle text-danger me-1"></i> Bad Client</span>
                                        @else
                                          <span class="badge light badge-success"><i class="fa fa-circle text-success me-1"></i> Good Client</span>
                                        @endif
                                        <small class="text-muted mt-1">Balance: ₱{{ number_format($customer->balance, 2) }}</small>
                                      </div>
                                  </td>
                                  <td>
                                      <div class="d-flex">
                                          @if(auth()->user()->position === 'Super Admin')
                                          <a href="javascript:void(0);" class="btn btn-primary shadow btn-xs sharp me-1 edit-customer-btn"
                                              data-customer-id="{{$customer->customer_id}}">
                                              <i class="fas fa-pencil-alt"></i></a>
                                          @endif
                                          <a href="javascript:void(0);" class="btn btn-info shadow btn-xs sharp me-1 view-history-btn"
                                              data-customer-id="{{$customer->customer_id}}"
                                              title="Transaction History"><i class="las la-history"></i></a>
                                          @if(auth()->user()->position === 'Super Admin')
                                          <a href="javascript:void(0);" class="btn btn-danger shadow btn-xs sharp delete-customer-btn"
                                              data-customer-id="{{$customer->customer_id}}">
                                              <i class="fa fa-trash"></i></a>
                                          @endif
                                      </div>
                                  </td>
                              </tr>
                              @empty
                              <tr>
                                  <td colspan="7" class="text-center">No customers found.</td>  
                              </tr>
                              @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('modals')
    <!-- Add Customer Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCustomerModalLabel">New Customer Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Global Header Info -->
                <div class="customer-modal-header-info">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <div class="form-row-custom">
                                <label>CUSTOMER NAME <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="custNameInput" required>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="d-flex align-items-center gap-3">
                                <div class="form-row-custom mb-0">
                                    <label style="width: auto;">OPENING BALANCE</label>
                                    <input type="number" class="form-control form-control-sm" style="width: 100px;"
                                        id="openingBalance">
                                </div>
                                <div class="form-row-custom mb-0">
                                    <label style="width: auto;">AS OF</label>
                                    <input type="date" class="form-control form-control-sm" id="asOfDate">
                                </div>
                                <div class="form-row-custom mb-0">
                                    <label style="width: auto;">CURRENCY</label>
                                    <select class="form-select form-select-sm" style="width: 150px;"
                                        id="currencySelect">
                                        <option value="PHP">Philippine peso</option>
                                        <option value="USD">US Dollar</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-body p-0">
                    <div class="customer-tab-container">
                        <!-- Vertical Tabs -->
                        <div class="nav flex-column nav-pills customer-nav-tabs" id="customer-tabs" role="tablist"
                            aria-orientation="vertical">
                            <button class="nav-link active" id="tab-address-link" data-bs-toggle="pill"
                                data-bs-target="#tab-address" type="button" role="tab">Address Info</button>
                            <button class="nav-link" id="tab-payment-link" data-bs-toggle="pill"
                                data-bs-target="#tab-payment" type="button" role="tab">Payment Settings</button>
                            <button class="nav-link" id="tab-additional-link" data-bs-toggle="pill"
                                data-bs-target="#tab-additional" type="button" role="tab">Additional Info</button>
                        </div>

                        <!-- Tab Content -->
                        <div class="tab-content customer-tab-content" id="customer-tabs-content">
                            <!-- Address Info Tab -->
                            <div class="tab-pane fade show active" id="tab-address" role="tabpanel">
                                <form id="addressInfoForm">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <div class="form-row-custom">
                                                <label>COMPANY NAME</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="companyName">
                                            </div>
                                            <div class="form-row-custom">
                                                <label>FULL NAME</label>
                                                <div class="d-flex gap-1 flex-1">
                                                    <input type="text" class="form-control form-control-sm"
                                                        style="width: 60px;" placeholder="Mr/Ms" id="titleName">
                                                    <input type="text" class="form-control form-control-sm"
                                                        placeholder="First" id="firstName">
                                                    <input type="text" class="form-control form-control-sm"
                                                        style="width: 40px;" placeholder="M.I" id="middleName">
                                                    <input type="text" class="form-control form-control-sm"
                                                        placeholder="Last" id="lastName">
                                                </div>
                                            </div>
                                            <div class="form-row-custom mt-3">
                                                <label>JOB TITLE</label>
                                                <input type="text" class="form-control form-control-sm" id="jobTitle">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <!-- Contact Methods Column 1 -->
                                        <div class="col-md-6">
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>Main Phone</option>
                                                    <option>Home Phone</option>
                                                </select>
                                                <input type="tel" class="form-control form-control-sm" id="mainPhone">
                                            </div>
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>Work Phone</option>
                                                </select>
                                                <input type="tel" class="form-control form-control-sm" id="workPhone">
                                            </div>
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>Mobile <span class="text-danger">*</span></option>
                                                </select>
                                                <input type="tel" class="form-control form-control-sm" id="mobilePhone" placeholder="Required" required>
                                            </div>
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>Fax</option>
                                                </select>
                                                <input type="tel" class="form-control form-control-sm" id="faxPhone">
                                            </div>
                                        </div>
                                        <!-- Contact Methods Column 2 -->
                                        <div class="col-md-6">
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>Main Email</option>
                                                </select>
                                                <input type="email" class="form-control form-control-sm" id="mainEmail">
                                            </div>
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>CC Email</option>
                                                </select>
                                                <input type="email" class="form-control form-control-sm" id="ccEmail">
                                            </div>
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>Website</option>
                                                </select>
                                                <input type="text" class="form-control form-control-sm" id="website">
                                            </div>
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>Other 1</option>
                                                </select>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="otherContact">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="section-divider">Address Details</div>
                                    <div class="address-box-container">
                                        <div>
                                            <label class="small fw-bold mb-1">INVOICE/BILL TO</label>
                                            <div class="address-box">
                                                <textarea id="invoiceAddress"
                                                    placeholder="Enter billing address..."></textarea>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="mb-1">
                                                <label class="small fw-bold mb-0">SHIP TO <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="address-box">
                                                <textarea id="shipToAddress"
                                                    placeholder="Enter shipping address... (Required)" required></textarea>
                                            </div>
                                            <div class="mt-2">
                                                <input type="checkbox" id="defaultShipping"> <label
                                                    for="defaultShipping" class="small">Default shipping address</label>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Payment Settings Tab -->
                            <div class="tab-pane fade" id="tab-payment" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-row-custom">
                                            <label>ACCOUNT NO.</label>
                                            <input type="text" class="form-control form-control-sm" id="accountNo">
                                        </div>
                                        <div class="form-row-custom">
                                            <label>PAYMENT TERMS</label>
                                            <select class="form-select form-select-sm" id="paymentTerms">
                                                <option>Net 15</option>
                                                <option>Net 30</option>
                                                <option>Net 60</option>
                                                <option>Due on receipt</option>
                                            </select>
                                        </div>
                                        <div class="form-row-custom">
                                            <label>PREFERRED DELIVERY METHOD</label>
                                            <select class="form-select form-select-sm" id="deliveryMethod">
                                                <option>Lazada</option>
                                                <option>Shopee</option>
                                                <option>Main Warehouse</option>
                                            </select>
                                        </div>
                                        <div class="form-row-custom">
                                            <label>PREFERRED PAYMENT METHOD</label>
                                            <select class="form-select form-select-sm" id="paymentMethod">
                                                <option>Check</option>
                                                <option>Cash</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-row-custom">
                                            <label>CREDIT LIMIT</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">PHP</span>
                                                <input type="number" class="form-control" id="creditLimitInput">
                                            </div>
                                        </div>
                                        <div class="form-row-custom">
                                            <label>PRICE LEVEL</label>
                                            <select class="form-select form-select-sm" id="priceLevel">
                                                <option>Standard</option>
                                                <option>Wholesale</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="credit-card-section">
                                    <div class="credit-card-label">CREDIT CARD INFORMATION</div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <div class="form-row-custom">
                                                <label>CREDIT CARD NO.</label>
                                                <input type="text" class="form-control form-control-sm" id="ccNo">
                                            </div>
                                            <div class="form-row-custom">
                                                <label>EXP. DATE</label>
                                                <div class="d-flex gap-1 align-items-center flex-1">
                                                    <input type="text" class="form-control form-control-sm"
                                                        style="width: 50px;" id="ccExpMonth">
                                                    <span>/</span>
                                                    <input type="text" class="form-control form-control-sm"
                                                        style="width: 70px;" id="ccExpYear">
                                                </div>
                                            </div>
                                            <div class="form-row-custom">
                                                <label>NAME ON CARD</label>
                                                <input type="text" class="form-control form-control-sm" id="ccName">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-row-custom">
                                                <label>ADDRESS</label>
                                                <input type="text" class="form-control form-control-sm" id="ccAddress">
                                            </div>
                                            <div class="form-row-custom">
                                                <label>ZIP/POSTAL CODE</label>
                                                <input type="text" class="form-control form-control-sm" id="ccZip">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Info Tab -->
                            <div class="tab-pane fade" id="tab-additional" role="tabpanel">
                                <form id="additionalInfoForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-row-custom">
                                                <label>CUSTOMER TYPE</label>
                                                <select class="form-select form-select-sm" id="custType">
                                                    <option>TEAM A</option>
                                                    <option>TEAM B</option>
                                                    <option>TEAM C</option>
                                                </select>
                                            </div>
                                            <div class="form-row-custom">
                                                <label>REP</label>
                                                <select class="form-select form-select-sm" id="custRep">
                                                    <option>CLE</option>
                                                    <option>MKT</option>
                                                </select>
                                            </div>
                                            <div class="form-row-custom">
                                                <label>CLASS</label>
                                                <select class="form-select form-select-sm" id="custClass">
                                                    <option>LAG</option>
                                                    <option>MNL</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="section-divider mt-0">CUSTOM FIELDS</div>
                                            <div class="form-row-custom">
                                                <label>CONTACT PERSON</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="customContactPerson">
                                            </div>
                                            <div class="form-row-custom">
                                                <label>CUSTOMER</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="customCustField">
                                            </div>
                                            <div class="text-end mt-4">
                                                <button type="button" class="btn btn-sm btn-light border">Define
                                                    Fields</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <div>
                        <input type="checkbox" id="customerInactive"> <label for="customerInactive"
                            class="small mb-0">Customer is inactive</label>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary px-4" id="saveCustomerBtn">OK</button>
                        <button type="button" class="btn btn-light px-4 border" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction History Modal -->
    <div class="modal fade" id="transactionHistoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title text-white"><i class="las la-history me-2"></i>Transaction History - <span id="historyCustomerName"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="mb-0">Current Balance: <span class="text-primary" id="historyBalance">₱0.00</span></h4>
                            <div id="historyStatusBadge" class="mt-1"></div>
                        </div>
                        @if(auth()->user()->position === 'Super Admin')
                        <div class="d-flex align-items-center gap-2">
                            <label class="small fw-bold mb-0">Override Status:</label>
                            <select class="form-select form-select-sm" id="manualStatusOverride" style="width: 150px;">
                                <option value="">Auto (System)</option>
                                <option value="good">Force Good</option>
                                <option value="bad">Force Bad</option>
                            </select>
                            <button class="btn btn-primary btn-xs" id="updateManualStatusBtn">Apply</button>
                        </div>
                        @endif
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr class="bg-light">
                                    <th>Date</th>
                                    <th>Transaction #</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="historyTableBody">
                                <!-- Populated by AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Customer Modal -->
    <div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCustomerModalLabel">Edit Customer Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Global Header Info -->
                <div class="customer-modal-header-info">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <div class="form-row-custom">
                                <label>CUSTOMER NAME <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="editCustNameInput" required>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="d-flex align-items-center gap-3">
                                <div class="form-row-custom mb-0">
                                    <label style="width: auto;">OPENING BALANCE</label>
                                    <input type="number" class="form-control form-control-sm" style="width: 100px;"
                                        id="editOpeningBalance">
                                </div>
                                <div class="form-row-custom mb-0">
                                    <label style="width: auto;">AS OF</label>
                                    <input type="date" class="form-control form-control-sm" id="editAsOfDate">
                                </div>
                                <div class="form-row-custom mb-0">
                                    <label style="width: auto;">CURRENCY</label>
                                    <select class="form-select form-select-sm" style="width: 150px;"
                                        id="editCurrencySelect">
                                        <option value="PHP">Philippine peso</option>
                                        <option value="USD">US Dollar</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-body p-0">
                    <div class="customer-tab-container">
                        <!-- Vertical Tabs -->
                        <div class="nav flex-column nav-pills customer-nav-tabs" id="edit-customer-tabs" role="tablist"
                            aria-orientation="vertical">
                            <button class="nav-link active" id="edit-tab-address-link" data-bs-toggle="pill"
                                data-bs-target="#edit-tab-address" type="button" role="tab">Address Info</button>
                            <button class="nav-link" id="edit-tab-payment-link" data-bs-toggle="pill"
                                data-bs-target="#edit-tab-payment" type="button" role="tab">Payment Settings</button>
                            <button class="nav-link" id="edit-tab-additional-link" data-bs-toggle="pill"
                                data-bs-target="#edit-tab-additional" type="button" role="tab">Additional Info</button>
                        </div>

                        <!-- Tab Content -->
                        <div class="tab-content customer-tab-content" id="edit-customer-tabs-content">
                            <!-- Address Info Tab -->
                            <div class="tab-pane fade show active" id="edit-tab-address" role="tabpanel">
                                <form id="editAddressInfoForm">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <div class="form-row-custom">
                                                <label>COMPANY NAME</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="editCompanyName">
                                            </div>
                                            <div class="form-row-custom">
                                                <label>FULL NAME</label>
                                                <div class="d-flex gap-1 flex-1">
                                                    <input type="text" class="form-control form-control-sm"
                                                        style="width: 60px;" placeholder="Mr/Ms" id="editTitleName">
                                                    <input type="text" class="form-control form-control-sm"
                                                        placeholder="First" id="editFirstName">
                                                    <input type="text" class="form-control form-control-sm"
                                                        style="width: 40px;" placeholder="M.I" id="editMiddleName">
                                                    <input type="text" class="form-control form-control-sm"
                                                        placeholder="Last" id="editLastName">
                                                </div>
                                            </div>
                                            <div class="form-row-custom mt-3">
                                                <label>JOB TITLE</label>
                                                <input type="text" class="form-control form-control-sm" id="editJobTitle">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <!-- Contact Methods Column 1 -->
                                        <div class="col-md-6">
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>Main Phone</option>
                                                    <option>Home Phone</option>
                                                </select>
                                                <input type="tel" class="form-control form-control-sm" id="editMainPhone">
                                            </div>
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>Work Phone</option>
                                                </select>
                                                <input type="tel" class="form-control form-control-sm" id="editWorkPhone">
                                            </div>
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>Mobile <span class="text-danger">*</span></option>
                                                </select>
                                                <input type="tel" class="form-control form-control-sm" id="editMobilePhone" placeholder="Required" required>
                                            </div>
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>Fax</option>
                                                </select>
                                                <input type="tel" class="form-control form-control-sm" id="editFaxPhone">
                                            </div>
                                        </div>
                                        <!-- Contact Methods Column 2 -->
                                        <div class="col-md-6">
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>Main Email</option>
                                                </select>
                                                <input type="email" class="form-control form-control-sm" id="editMainEmail">
                                            </div>
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>CC Email</option>
                                                </select>
                                                <input type="email" class="form-control form-control-sm" id="editCcEmail">
                                            </div>
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>Website</option>
                                                </select>
                                                <input type="text" class="form-control form-control-sm" id="editWebsite">
                                            </div>
                                            <div class="form-row-custom">
                                                <select class="form-select form-select-sm" style="width: 120px;">
                                                    <option>Other 1</option>
                                                </select>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="editOtherContact">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="section-divider">Address Details</div>
                                    <div class="address-box-container">
                                        <div>
                                            <label class="small fw-bold mb-1">INVOICE/BILL TO</label>
                                            <div class="address-box">
                                                <textarea id="editInvoiceAddress"
                                                    placeholder="Enter billing address..."></textarea>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="mb-1">
                                                <label class="small fw-bold mb-0">SHIP TO <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="address-box">
                                                <textarea id="editShipToAddress"
                                                    placeholder="Enter shipping address... (Required)" required></textarea>
                                            </div>
                                            <div class="mt-2">
                                                <input type="checkbox" id="editDefaultShipping"> <label
                                                    for="editDefaultShipping" class="small">Default shipping address</label>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Payment Settings Tab -->
                            <div class="tab-pane fade" id="edit-tab-payment" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-row-custom">
                                            <label>ACCOUNT NO.</label>
                                            <input type="text" class="form-control form-control-sm" id="editAccountNo">
                                        </div>
                                        <div class="form-row-custom">
                                            <label>PAYMENT TERMS</label>
                                            <select class="form-select form-select-sm" id="editPaymentTerms">
                                                <option>Net 15</option>
                                                <option>Net 30</option>
                                                <option>Net 60</option>
                                                <option>Due on receipt</option>
                                            </select>
                                        </div>
                                        <div class="form-row-custom">
                                            <label>PREFERRED DELIVERY METHOD</label>
                                            <select class="form-select form-select-sm" id="editDeliveryMethod">
                                                <option>Lazada</option>
                                                <option>Shopee</option>
                                                <option>Main Warehouse</option>
                                            </select>
                                        </div>
                                        <div class="form-row-custom">
                                            <label>PREFERRED PAYMENT METHOD</label>
                                            <select class="form-select form-select-sm" id="editPaymentMethod">
                                                <option>Check</option>
                                                <option>Cash</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-row-custom">
                                            <label>CREDIT LIMIT</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">PHP</span>
                                                <input type="number" class="form-control" id="editCreditLimitInput">
                                            </div>
                                        </div>
                                        <div class="form-row-custom">
                                            <label>PRICE LEVEL</label>
                                            <select class="form-select form-select-sm" id="editPriceLevel">
                                                <option>Standard</option>
                                                <option>Wholesale</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="credit-card-section">
                                    <div class="credit-card-label">CREDIT CARD INFORMATION</div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <div class="form-row-custom">
                                                <label>CREDIT CARD NO.</label>
                                                <input type="text" class="form-control form-control-sm" id="editCcNo">
                                            </div>
                                            <div class="form-row-custom">
                                                <label>EXP. DATE</label>
                                                <div class="d-flex gap-1 align-items-center flex-1">
                                                    <input type="text" class="form-control form-control-sm"
                                                        style="width: 50px;" id="editCcExpMonth">
                                                    <span>/</span>
                                                    <input type="text" class="form-control form-control-sm"
                                                        style="width: 70px;" id="editCcExpYear">
                                                </div>
                                            </div>
                                            <div class="form-row-custom">
                                                <label>NAME ON CARD</label>
                                                <input type="text" class="form-control form-control-sm" id="editCcName">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-row-custom">
                                                <label>ADDRESS</label>
                                                <input type="text" class="form-control form-control-sm" id="editCcAddress">
                                            </div>
                                            <div class="form-row-custom">
                                                <label>ZIP/POSTAL CODE</label>
                                                <input type="text" class="form-control form-control-sm" id="editCcZip">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Info Tab -->
                            <div class="tab-pane fade" id="edit-tab-additional" role="tabpanel">
                                <form id="editAdditionalInfoForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-row-custom">
                                                <label>CUSTOMER TYPE</label>
                                                <select class="form-select form-select-sm" id="editCustType">
                                                    <option>TEAM A</option>
                                                    <option>TEAM B</option>
                                                </select>
                                            </div>
                                            <div class="form-row-custom">
                                                <label>REP</label>
                                                <select class="form-select form-select-sm" id="editCustRep">
                                                    <option>CLE</option>
                                                    <option>MKT</option>
                                                </select>
                                            </div>
                                            <div class="form-row-custom">
                                                <label>CLASS</label>
                                                <select class="form-select form-select-sm" id="editCustClass">
                                                    <option>LAG</option>
                                                    <option>MNL</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="section-divider mt-0">CUSTOM FIELDS</div>
                                            <div class="form-row-custom">
                                                <label>CONTACT PERSON</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="editCustomContactPerson">
                                            </div>
                                            <div class="form-row-custom">
                                                <label>CUSTOMER</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="editCustomCustField">
                                            </div>
                                            <div class="text-end mt-4">
                                                <button type="button" class="btn btn-sm btn-light border">Define
                                                    Fields</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div>
                            <input type="checkbox" id="editCustomerInactive"> <label for="editCustomerInactive"
                                class="small mb-0">Customer is inactive</label>
                        </div>
                        @if(auth()->user()->position === 'Super Admin')
                        <div class="d-flex align-items-center gap-2 border-start ps-3">
                            <label class="small fw-bold mb-0">Manual Status:</label>
                            <select class="form-select form-select-sm" id="editManualStatus" style="width: 120px;">
                                <option value="">Auto</option>
                                <option value="good">Good</option>
                                <option value="bad">Bad</option>
                            </select>
                        </div>
                        @endif
                    </div>
                    <div>
                        <input type="hidden" id="editCustomerId">
                        <button type="button" class="btn btn-primary px-4" id="updateCustomerBtn">Save Changes</button>
                        <button type="button" class="btn btn-light px-4 border" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-light px-4 border">Help</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">
                        <i class="fas fa-exclamation-triangle text-danger me-2"></i>Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to delete this customer? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="fas fa-trash me-1"></i>Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Save Changes Modal -->
    <div class="modal fade" id="confirmSaveModal" tabindex="-1" aria-labelledby="confirmSaveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmSaveModalLabel">
                        <i class="fas fa-question-circle text-primary me-2"></i>Confirm Changes
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to save the changes to this customer?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmSaveBtn">
                        <i class="fas fa-check me-1"></i>Yes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pt-0">
                    <div class="mb-3">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="mb-2">Success!</h4>
                    <p class="text-muted mb-0" id="successMessage">Customer updated successfully!</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pt-0">
                    <button type="button" class="btn btn-primary px-4" id="successOkBtn">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pt-0">
                    <div class="mb-3">
                        <i class="fas fa-times-circle text-danger" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="mb-2">Error</h4>
                    <p class="text-muted mb-0" id="errorMessage">An error occurred.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pt-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endpush

    @push('scripts')
    <script>
        // Authorization check
        const userPosition = '{{ auth()->user()->position ?? "" }}';
        const isAdmin = userPosition === 'Super Admin';

        // Search Functionality
        document.getElementById('customerSearch')?.addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('#customerTableBody tr:not(#noResultsRow)');
            
            let visibleCount = 0;
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                const isMatch = text.includes(query);
                row.style.display = isMatch ? '' : 'none';
                if (isMatch) visibleCount++;
            });
            
            // Handle "No results" message
            let noResultsRow = document.getElementById('noResultsRow');
            if (visibleCount === 0 && query !== '') {
                if (!noResultsRow) {
                    const tbody = document.getElementById('customerTableBody');
                    noResultsRow = document.createElement('tr');
                    noResultsRow.id = 'noResultsRow';
                    noResultsRow.innerHTML = `<td colspan="7" class="text-center text-muted py-4">No customers matching "${this.value}" found.</td>`;
                    tbody.appendChild(noResultsRow);
                } else {
                    noResultsRow.style.display = '';
                    noResultsRow.querySelector('td').textContent = `No customers matching "${this.value}" found.`;
                }
            } else if (noResultsRow) {
                noResultsRow.style.display = 'none';
            }
        });

        // Save Customer via AJAX
        document.getElementById('saveCustomerBtn')?.addEventListener('click', async function() {
            const btn = this;
            btn.disabled = true;
            btn.textContent = 'Saving...';

            const data = {
                customer_name: document.getElementById('custNameInput')?.value || '',
                company_name: document.getElementById('companyName')?.value || '',
                opening_balance: document.getElementById('openingBalance')?.value || null,
                opening_balance_date: document.getElementById('asOfDate')?.value || null,
                currency_code: document.getElementById('currencySelect')?.value || null,
                title: document.getElementById('titleName')?.value || null,
                first_name: document.getElementById('firstName')?.value || null,
                middle_initial: document.getElementById('middleName')?.value || null,
                last_name: document.getElementById('lastName')?.value || null,
                job_title: document.getElementById('jobTitle')?.value || null,
                main_phone: document.getElementById('mainPhone')?.value || null,
                work_phone: document.getElementById('workPhone')?.value || null,
                mobile: document.getElementById('mobilePhone')?.value || null,
                fax: document.getElementById('faxPhone')?.value || null,
                main_email: document.getElementById('mainEmail')?.value || null,
                cc_email: document.getElementById('ccEmail')?.value || null,
                website: document.getElementById('website')?.value || null,
                other_contact: document.getElementById('otherContact')?.value || null,
                billing_address: document.getElementById('invoiceAddress')?.value || null,
                shipping_address: document.getElementById('shipToAddress')?.value || null,
                is_default_shipping: document.getElementById('defaultShipping')?.checked ? 1 : 0,
                account_number: document.getElementById('accountNo')?.value || null,
                payment_terms: document.getElementById('paymentTerms')?.value || null,
                preferred_delivery_method: document.getElementById('deliveryMethod')?.value || null,
                preferred_payment_method: document.getElementById('paymentMethod')?.value?.toLowerCase() || null,
                credit_limit: document.getElementById('creditLimitInput')?.value || null,
                price_level: document.getElementById('priceLevel')?.value?.toLowerCase() || null,
                card_number_last4: document.getElementById('ccNo')?.value?.slice(-4) || null,
                card_exp_month: document.getElementById('ccExpMonth')?.value || null,
                card_exp_year: document.getElementById('ccExpYear')?.value || null,
                card_name: document.getElementById('ccName')?.value || null,
                card_billing_address: document.getElementById('ccAddress')?.value || null,
                card_zip: document.getElementById('ccZip')?.value || null,
                customer_type: document.getElementById('custType')?.value || null,
                rep: document.getElementById('custRep')?.value || null,
                class: document.getElementById('custClass')?.value || null,
                custom_contact_person: document.getElementById('customContactPerson')?.value || null,
                custom_customer_field: document.getElementById('customCustField')?.value || null,
                is_inactive: document.getElementById('customerInactive')?.checked ? 1 : 0,
            };

            try {
                const response = await fetch('{{ route("marketing.customers.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    bootstrap.Modal.getInstance(document.getElementById('addCustomerModal')).hide();
                    
                    // Show success modal
                    document.getElementById('successMessage').textContent = 'Customer saved successfully!';
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                    
                    // Add reload functionality to OK button if not already there
                    document.getElementById('successOkBtn').onclick = function() {
                        window.location.reload();
                    };
                } else {
                    let errorMsg = result.message || 'Failed to save customer.';
                    if (result.errors) {
                        errorMsg = Object.values(result.errors).flat().join('\n');
                    }
                    
                    // Show error modal
                    document.getElementById('errorMessage').textContent = errorMsg;
                    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                    errorModal.show();
                }
            } catch (error) {
                // Show error modal
                document.getElementById('errorMessage').textContent = 'An error occurred: ' + error.message;
                const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
            } finally {
                btn.disabled = false;
                btn.textContent = 'Save Customer';
            }
        });



        // Open Edit Modal and fetch customer data
        document.querySelectorAll('.edit-customer-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                if (!isAdmin) {
                    alert('You do not have permission to edit customers. Only Super Admin can edit.');
                    return;
                }
                const customerId = this.dataset.customerId;
                
                try {
                    const response = await fetch(`/marketing/customers/${customerId}/edit`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to fetch customer data');
                    }

                    const customer = await response.json();

                    // Populate the edit modal with customer data
                    document.getElementById('editCustomerId').value = customer.customer_id;
                    document.getElementById('editCustNameInput').value = customer.customer_name || '';
                    document.getElementById('editOpeningBalance').value = customer.opening_balance || '';
                    document.getElementById('editAsOfDate').value = customer.opening_balance_date || '';
                    document.getElementById('editCurrencySelect').value = customer.currency_code || 'PHP';
                    document.getElementById('editCompanyName').value = customer.company_name || '';
                    document.getElementById('editTitleName').value = customer.title || '';
                    document.getElementById('editFirstName').value = customer.first_name || '';
                    document.getElementById('editMiddleName').value = customer.middle_initial || '';
                    document.getElementById('editLastName').value = customer.last_name || '';
                    document.getElementById('editJobTitle').value = customer.job_title || '';
                    document.getElementById('editMainPhone').value = customer.main_phone || '';
                    document.getElementById('editWorkPhone').value = customer.work_phone || '';
                    document.getElementById('editMobilePhone').value = customer.mobile || '';
                    document.getElementById('editFaxPhone').value = customer.fax || '';
                    document.getElementById('editMainEmail').value = customer.main_email || '';
                    document.getElementById('editCcEmail').value = customer.cc_email || '';
                    document.getElementById('editWebsite').value = customer.website || '';
                    document.getElementById('editOtherContact').value = customer.other_contact || '';
                    document.getElementById('editInvoiceAddress').value = customer.billing_address || '';
                    document.getElementById('editShipToAddress').value = customer.shipping_address || '';
                    document.getElementById('editDefaultShipping').checked = customer.is_default_shipping == 1;
                    document.getElementById('editAccountNo').value = customer.account_number || '';
                    document.getElementById('editPaymentTerms').value = customer.payment_terms || 'Net 15';
                    document.getElementById('editDeliveryMethod').value = customer.preferred_delivery_method || 'Lazada';
                    
                    // Handle payment method (capitalize first letter for select option)
                    const paymentMethod = customer.preferred_payment_method || 'check';
                    document.getElementById('editPaymentMethod').value = paymentMethod.charAt(0).toUpperCase() + paymentMethod.slice(1);
                    
                    document.getElementById('editCreditLimitInput').value = customer.credit_limit || '';
                    
                    // Handle price level (capitalize first letter for select option)
                    const priceLevel = customer.price_level || 'standard';
                    document.getElementById('editPriceLevel').value = priceLevel.charAt(0).toUpperCase() + priceLevel.slice(1);
                    
                    document.getElementById('editCcNo').value = customer.card_number_last4 ? '****' + customer.card_number_last4 : '';
                    document.getElementById('editCcExpMonth').value = customer.card_exp_month || '';
                    document.getElementById('editCcExpYear').value = customer.card_exp_year || '';
                    document.getElementById('editCcName').value = customer.card_name || '';
                    document.getElementById('editCcAddress').value = customer.card_billing_address || '';
                    document.getElementById('editCcZip').value = customer.card_zip || '';
                    document.getElementById('editCustType').value = customer.customer_type || 'TEAM A';
                    document.getElementById('editCustRep').value = customer.rep || 'CLE';
                    document.getElementById('editCustClass').value = customer.class || 'LAG';
                    document.getElementById('editCustomContactPerson').value = customer.custom_contact_person || '';
                    document.getElementById('editCustomCustField').value = customer.custom_customer_field || '';
                    document.getElementById('editCustomerInactive').checked = customer.is_inactive == 1;
                    if (document.getElementById('editManualStatus')) {
                        document.getElementById('editManualStatus').value = customer.manual_status || '';
                    }

                    // Show the edit modal
                    const editModal = new bootstrap.Modal(document.getElementById('editCustomerModal'));
                    editModal.show();

                } catch (error) {
                    // Show error modal
                    document.getElementById('errorMessage').textContent = 'Error fetching customer data: ' + error.message;
                    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                    errorModal.show();
                }
            });
        });

        // Show confirm modal before saving
        document.getElementById('updateCustomerBtn')?.addEventListener('click', function() {
            // Show confirm modal
            const confirmModal = new bootstrap.Modal(document.getElementById('confirmSaveModal'));
            confirmModal.show();
        });

        // Update Customer via AJAX after confirmation
        document.getElementById('confirmSaveBtn')?.addEventListener('click', async function() {
            const btn = this;
            const updateBtn = document.getElementById('updateCustomerBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving...';

            // Hide confirm modal
            bootstrap.Modal.getInstance(document.getElementById('confirmSaveModal')).hide();

            const customerId = document.getElementById('editCustomerId').value;

            const data = {
                customer_name: document.getElementById('editCustNameInput')?.value || '',
                company_name: document.getElementById('editCompanyName')?.value || '',
                opening_balance: document.getElementById('editOpeningBalance')?.value || null,
                opening_balance_date: document.getElementById('editAsOfDate')?.value || null,
                currency_code: document.getElementById('editCurrencySelect')?.value || null,
                title: document.getElementById('editTitleName')?.value || null,
                first_name: document.getElementById('editFirstName')?.value || null,
                middle_initial: document.getElementById('editMiddleName')?.value || null,
                last_name: document.getElementById('editLastName')?.value || null,
                job_title: document.getElementById('editJobTitle')?.value || null,
                main_phone: document.getElementById('editMainPhone')?.value || null,
                work_phone: document.getElementById('editWorkPhone')?.value || null,
                mobile: document.getElementById('editMobilePhone')?.value || null,
                fax: document.getElementById('editFaxPhone')?.value || null,
                main_email: document.getElementById('editMainEmail')?.value || null,
                cc_email: document.getElementById('editCcEmail')?.value || null,
                website: document.getElementById('editWebsite')?.value || null,
                other_contact: document.getElementById('editOtherContact')?.value || null,
                billing_address: document.getElementById('editInvoiceAddress')?.value || null,
                shipping_address: document.getElementById('editShipToAddress')?.value || null,
                is_default_shipping: document.getElementById('editDefaultShipping')?.checked ? 1 : 0,
                account_number: document.getElementById('editAccountNo')?.value || null,
                payment_terms: document.getElementById('editPaymentTerms')?.value || null,
                preferred_delivery_method: document.getElementById('editDeliveryMethod')?.value || null,
                preferred_payment_method: document.getElementById('editPaymentMethod')?.value?.toLowerCase() || null,
                credit_limit: document.getElementById('editCreditLimitInput')?.value || null,
                price_level: document.getElementById('editPriceLevel')?.value?.toLowerCase() || null,
                card_number_last4: document.getElementById('editCcNo')?.value?.replace('****', '')?.slice(-4) || null,
                card_exp_month: document.getElementById('editCcExpMonth')?.value || null,
                card_exp_year: document.getElementById('editCcExpYear')?.value || null,
                card_name: document.getElementById('editCcName')?.value || null,
                card_billing_address: document.getElementById('editCcAddress')?.value || null,
                card_zip: document.getElementById('editCcZip')?.value || null,
                customer_type: document.getElementById('editCustType')?.value || null,
                rep: document.getElementById('editCustRep')?.value || null,
                class: document.getElementById('editCustClass')?.value || null,
                custom_contact_person: document.getElementById('editCustomContactPerson')?.value || null,
                custom_customer_field: document.getElementById('editCustomCustField')?.value || null,
                is_inactive: document.getElementById('editCustomerInactive')?.checked ? 1 : 0,
                manual_status: document.getElementById('editManualStatus')?.value || null,
            };

            try {
                const response = await fetch(`/marketing/customers/${customerId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    // Hide edit modal
                    bootstrap.Modal.getInstance(document.getElementById('editCustomerModal')).hide();
                    
                    // Show success modal
                    document.getElementById('successMessage').textContent = 'Customer updated successfully!';
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();

                    // Optional: Refresh page on OK button click
                    document.getElementById('successOkBtn').onclick = function() {
                        window.location.reload();
                    };
                } else {
                    let errorMsg = result.message || 'Failed to update customer.';
                    if (result.errors) {
                        errorMsg = Object.values(result.errors).flat().join('\n');
                    }
                    // Show error modal
                    document.getElementById('errorMessage').textContent = errorMsg;
                    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                    errorModal.show();
                }
            } catch (error) {
                // Show error modal
                document.getElementById('errorMessage').textContent = 'An error occurred: ' + error.message;
                const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check me-1"></i>Yes';
            }
        });

        // View Transaction History
        document.querySelectorAll('.view-history-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const customerId = this.dataset.customerId;
                const historyModal = new bootstrap.Modal(document.getElementById('transactionHistoryModal'));
                const tableBody = document.getElementById('historyTableBody');
                
                tableBody.innerHTML = '<tr><td colspan="5" class="text-center"><i class="fas fa-spinner fa-spin me-2"></i>Loading...</td></tr>';
                historyModal.show();

                try {
                    const response = await fetch(`/marketing/customers/${customerId}/history`);
                    const data = await response.json();

                    document.getElementById('historyCustomerName').textContent = data.customer_name;
                    document.getElementById('historyBalance').textContent = '₱' + data.balance.toLocaleString(undefined, {minimumFractionDigits: 2});
                    
                    const statusBadge = document.getElementById('historyStatusBadge');
                    if (data.is_bad_client) {
                        statusBadge.innerHTML = '<span class="badge light badge-danger">Bad Client</span>';
                    } else {
                        statusBadge.innerHTML = '<span class="badge light badge-success">Good Client</span>';
                    }

                    if (document.getElementById('manualStatusOverride')) {
                        document.getElementById('manualStatusOverride').value = data.manual_status || '';
                    }

                    let rows = '';
                    if (data.history.length === 0) {
                        rows = '<tr><td colspan="5" class="text-center">No transactions found.</td></tr>';
                    } else {
                        data.history.forEach(order => {
                            const statusColor = order.payment_status === 'paid' ? 'success' : 'danger';
                            const overdueTag = order.is_overdue ? '<br><span class="badge badge-xs light badge-danger">OVERDUE</span>' : '';
                            
                            rows += `
                                <tr>
                                    <td>${order.date}</td>
                                    <td>${order.so_number}</td>
                                    <td>₱${order.total_amount.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                                    <td>${order.due_date}${overdueTag}</td>
                                    <td><span class="badge badge-xs light badge-${statusColor}">${order.payment_status.toUpperCase()}</span></td>
                                </tr>
                            `;
                        });
                    }
                    tableBody.innerHTML = rows;
                    
                    // Store current customer ID for override button
                    document.getElementById('updateManualStatusBtn')?.setAttribute('data-customer-id', customerId);

                } catch (error) {
                    tableBody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error loading history.</td></tr>';
                }
            });
        });

        // Update Manual Status from History Modal
        document.getElementById('updateManualStatusBtn')?.addEventListener('click', async function() {
            const customerId = this.getAttribute('data-customer-id');
            const manualStatus = document.getElementById('manualStatusOverride').value;
            const btn = this;

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            try {
                const response = await fetch(`/marketing/customers/${customerId}/manual-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ manual_status: manualStatus })
                });

                if (response.ok) {
                    location.reload();
                } else {
                    alert('Failed to update status.');
                }
            } catch (error) {
                alert('An error occurred.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = 'Apply';
            }
        });

        let customerIdToDelete = null;

        // Open Confirm Delete Modal
        document.querySelectorAll('.delete-customer-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!isAdmin) {
                    alert('You do not have permission to delete customers. Only Super Admin can delete.');
                    return;
                }
                customerIdToDelete = this.dataset.customerId;
                const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
                confirmDeleteModal.show();
            });
        });

        // Delete Customer via AJAX
        document.getElementById('confirmDeleteBtn')?.addEventListener('click', async function() {
            if (!customerIdToDelete) return;

            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Deleting...';

            try {
                const response = await fetch(`/marketing/customers/${customerIdToDelete}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                // Hide confirm modal
                bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal')).hide();

                if (response.ok) {
                    // Show success modal
                    document.getElementById('successMessage').textContent = 'Customer deleted successfully!';
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                    
                    document.getElementById('successOkBtn').onclick = function() {
                        window.location.reload();
                    };
                } else {
                    // Show error modal
                    document.getElementById('errorMessage').textContent = result.message || 'Failed to delete customer.';
                    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                    errorModal.show();
                }
            } catch (error) {
                // Hide confirm modal if still open
                const modalEl = document.getElementById('confirmDeleteModal');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();

                // Show error modal
                document.getElementById('errorMessage').textContent = 'An error occurred: ' + error.message;
                const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-trash me-1"></i>Delete';
                customerIdToDelete = null;
            }
        });

        // Customer Search Functionality
        document.getElementById('customerSearch')?.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#customerTableBody tr:not(#noResultsRow)');
            let hasVisibleRows = false;
            let totalActualRows = 0;
            
            rows.forEach(row => {
                // Check if this is the generic "No customers found" row from the backend
                if (row.cells.length === 1 && row.textContent.includes('No customers found')) {
                    return; 
                }
                
                totalActualRows++;
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                    hasVisibleRows = true;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Only handle "no results" logic if we actually have customer rows
            if (totalActualRows > 0) {
                let noResultsRow = document.getElementById('noResultsRow');
                if (!hasVisibleRows) {
                    if (!noResultsRow) {
                        noResultsRow = document.createElement('tr');
                        noResultsRow.id = 'noResultsRow';
                        noResultsRow.innerHTML = '<td colspan="7" class="text-center py-4">No customers match your search.</td>';
                        document.getElementById('customerTableBody').appendChild(noResultsRow);
                    }
                    noResultsRow.style.display = '';
                } else if (noResultsRow) {
                    noResultsRow.style.display = 'none';
                }
            }
        });
    </script>
    @endpush
</x-app-layout>

<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
    <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-warning text-warning" style="width: 45px; height: 45px;">
                <i class="las la-building fs-24"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark fs-18">Wholesale Key Accounts Ledger</h5>
                <p class="text-muted small mb-0">Sales ledgers and performance history mapped by major retail bookstore chains and independent outlets</p>
            </div>
        </div>
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold">5 Accounts</span>
    </div>
    <div class="card-body pt-2">
        <div class="row g-3 mb-4">
            
            <!-- National Book Store -->
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ffc107 !important; cursor: pointer;" onclick="showSalesLedgerModal('Key Account: National Book Store', document.getElementById('nbsTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">National Book Store</h6>
                            <span class="text-muted small">NBS branch network sales</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱0.00</h5>
                    </div>
                </div>
            </div>

            <!-- Pandayan -->
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ffc107 !important; cursor: pointer;" onclick="showSalesLedgerModal('Key Account: Pandayan Bookshop', document.getElementById('pandayanTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Pandayan Bookshop</h6>
                            <span class="text-muted small">Pandayan branch network sales</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱0.00</h5>
                    </div>
                </div>
            </div>

            <!-- SM Store -->
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ffc107 !important; cursor: pointer;" onclick="showSalesLedgerModal('Key Account: SM Store (Homeworld)', document.getElementById('smTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">SM Store</h6>
                            <span class="text-muted small">SM department store sales</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱0.00</h5>
                    </div>
                </div>
            </div>

            <!-- Other Chains -->
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ffc107 !important; cursor: pointer;" onclick="showSalesLedgerModal('Key Account: Other Bookstore Chains', document.getElementById('otherChainsTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Other Chains</h6>
                            <span class="text-muted small">Rex, National, Goodwill outlets</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱0.00</h5>
                    </div>
                </div>
            </div>

            <!-- Independent Bookstores -->
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm hover-row" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #ffc107 !important; cursor: pointer;" onclick="showSalesLedgerModal('Key Account: Independent Bookstores', document.getElementById('indieTemplate').innerHTML)">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-14">Independent Bookstores</h6>
                            <span class="text-muted small">Local private book dealerships</span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-15">₱0.00</h5>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- TEMPLATE - NATIONAL BOOK STORE -->
<div id="nbsTemplate" style="display: none;">
    <div class="row mb-3 pb-3 border-bottom align-items-center g-2">
        <div class="col-md-4">
            <span class="text-muted small d-block">Account Name</span>
            <strong class="text-dark">National Book Store Inc.</strong>
        </div>
        <div class="col-md-4">
            <span class="text-muted small d-block">Payment Terms</span>
            <strong class="text-dark">Net 60 Days (Rebates: 3%)</strong>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="text-muted small d-block">Outstanding Balance</span>
            <strong class="text-dark fs-16">₱0.00</strong>
        </div>
    </div>

    <ul class="nav nav-tabs modal-tabs mb-3" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#nbs-orders" type="button" role="tab">Orders & Delivery</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nbs-invoices" type="button" role="tab">Invoices & Aging</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nbs-collections" type="button" role="tab">Collections & Rebates</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nbs-returns" type="button" role="tab">Returns & Credit</button>
        </li>
    </ul>

    <div class="tab-content pt-2">
        <!-- Orders -->
        <div class="tab-pane fade show active" id="nbs-orders" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover table-sm small">
                    <thead class="table-light">
                        <tr>
                            <th>Ref Number</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th class="text-end">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No wholesale orders recorded in the database.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Invoices & Aging -->
        <div class="tab-pane fade" id="nbs-invoices" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover table-sm small">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice Number</th>
                            <th>Date</th>
                            <th>Aging Category</th>
                            <th>Outstanding</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No wholesale invoices recorded in the database.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Collections & Rebates -->
        <div class="tab-pane fade" id="nbs-collections" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover table-sm small">
                    <thead class="table-light">
                        <tr>
                            <th>Collection Receipt</th>
                            <th>Payment Date</th>
                            <th>Deductions / Fee</th>
                            <th>Volume Rebate Given</th>
                            <th class="text-end">Net Collected</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No collections recorded in the current period.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Returns & Credit Notes -->
        <div class="tab-pane fade" id="nbs-returns" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover table-sm small">
                    <thead class="table-light">
                        <tr>
                            <th>CN Reference</th>
                            <th>Date</th>
                            <th>Deduction Reason</th>
                            <th class="text-end">Value Credited</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No returned items or credit notes recorded.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- TEMPLATE - PANDAYAN BOOKSHOP -->
<div id="pandayanTemplate" style="display: none;">
    <div class="row mb-3 pb-3 border-bottom align-items-center g-2">
        <div class="col-md-4">
            <span class="text-muted small d-block">Account Name</span>
            <strong class="text-dark">Pandayan Bookshop Inc.</strong>
        </div>
        <div class="col-md-4">
            <span class="text-muted small d-block">Payment Terms</span>
            <strong class="text-dark">Net 45 Days (Rebates: 2.0%)</strong>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="text-muted small d-block">Outstanding Balance</span>
            <strong class="text-dark fs-16">₱0.00</strong>
        </div>
    </div>

    <ul class="nav nav-tabs modal-tabs mb-3" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pandayan-orders" type="button" role="tab">Orders & Delivery</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#pandayan-invoices" type="button" role="tab">Invoices & Aging</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#pandayan-collections" type="button" role="tab">Collections</button>
        </li>
    </ul>

    <div class="tab-content pt-2">
        <div class="tab-pane fade show active" id="pandayan-orders" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover table-sm small">
                    <thead class="table-light">
                        <tr>
                            <th>Ref Number</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th class="text-end">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No wholesale orders recorded in the database.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="pandayan-invoices" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover table-sm small">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice Number</th>
                            <th>Date</th>
                            <th>Aging Category</th>
                            <th>Outstanding</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No wholesale invoices recorded in the database.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="pandayan-collections" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover table-sm small">
                    <thead class="table-light">
                        <tr>
                            <th>Collection Receipt</th>
                            <th>Payment Date</th>
                            <th>Deductions</th>
                            <th>Rebates</th>
                            <th class="text-end">Net Collected</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No collections recorded in the current period.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- TEMPLATE - SM STORE -->
<div id="smTemplate" style="display: none;">
    <div class="row mb-3 pb-3 border-bottom align-items-center g-2">
        <div class="col-md-4">
            <span class="text-muted small d-block">Account Name</span>
            <strong class="text-dark">SM Store (Homeworld network)</strong>
        </div>
        <div class="col-md-4">
            <span class="text-muted small d-block">Payment Terms</span>
            <strong class="text-dark">Net 60 Days (Rebates: 1.5%)</strong>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="text-muted small d-block">Outstanding Balance</span>
            <strong class="text-dark fs-16">₱0.00</strong>
        </div>
    </div>

    <ul class="nav nav-tabs modal-tabs mb-3" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#sm-orders" type="button" role="tab">Orders</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#sm-invoices" type="button" role="tab">Invoices & Aging</button>
        </li>
    </ul>

    <div class="tab-content pt-2">
        <div class="tab-pane fade show active" id="sm-orders" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover table-sm small">
                    <thead class="table-light">
                        <tr>
                            <th>Ref Number</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-end">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No wholesale orders recorded in the database.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="sm-invoices" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover table-sm small">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice Number</th>
                            <th>Date</th>
                            <th>Aging Category</th>
                            <th>Outstanding</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No wholesale invoices recorded in the database.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- TEMPLATE - OTHER CHAINS -->
<div id="otherChainsTemplate" style="display: none;">
    <div class="row mb-3 pb-3 border-bottom align-items-center g-2">
        <div class="col-md-4">
            <span class="text-muted small d-block">Account Name</span>
            <strong class="text-dark">Other Bookstore Chains (Rex, Goodwill, etc.)</strong>
        </div>
        <div class="col-md-4">
            <span class="text-muted small d-block">Payment Terms</span>
            <strong class="text-dark">Net 30 Days</strong>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="text-muted small d-block">Outstanding Balance</span>
            <strong class="text-dark fs-16">₱0.00</strong>
        </div>
    </div>

    <ul class="nav nav-tabs modal-tabs mb-3" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#other-orders" type="button" role="tab">Orders</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#other-invoices" type="button" role="tab">Invoices & Aging</button>
        </li>
    </ul>

    <div class="tab-content pt-2">
        <div class="tab-pane fade show active" id="other-orders" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover table-sm small">
                    <thead class="table-light">
                        <tr>
                            <th>Ref Number</th>
                            <th>Chain Partner</th>
                            <th>Date</th>
                            <th class="text-end">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No wholesale orders recorded in the database.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="other-invoices" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover table-sm small">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice Number</th>
                            <th>Partner</th>
                            <th>Aging Category</th>
                            <th>Outstanding</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No wholesale invoices recorded in the database.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- TEMPLATE - INDIE BOOKSTORES -->
<div id="indieTemplate" style="display: none;">
    <div class="row mb-3 pb-3 border-bottom align-items-center g-2">
        <div class="col-md-4">
            <span class="text-muted small d-block">Account Name</span>
            <strong class="text-dark">Independent Private Bookdealers</strong>
        </div>
        <div class="col-md-4">
            <span class="text-muted small d-block">Payment Terms</span>
            <strong class="text-dark">Consignment/Net 30 Days</strong>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="text-muted small d-block">Outstanding Balance</span>
            <strong class="text-dark fs-16">₱0.00</strong>
        </div>
    </div>

    <ul class="nav nav-tabs modal-tabs mb-3" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#indie-orders" type="button" role="tab">Consignments</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#indie-invoices" type="button" role="tab">Invoices & Aging</button>
        </li>
    </ul>

    <div class="tab-content pt-2">
        <div class="tab-pane fade show active" id="indie-orders" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover table-sm small">
                    <thead class="table-light">
                        <tr>
                            <th>Ref Number</th>
                            <th>Independent Dealer</th>
                            <th>Date</th>
                            <th class="text-end">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No independent dealer consignments recorded.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="indie-invoices" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover table-sm small">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice Number</th>
                            <th>Dealer Partner</th>
                            <th>Aging Category</th>
                            <th>Outstanding</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No wholesale invoices recorded in the database.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

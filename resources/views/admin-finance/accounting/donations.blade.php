<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .don-header-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.75rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            border: 0;
            margin-bottom: 1.5rem;
        }

        .hover-row {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .hover-row:hover {
            background-color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
        }

        .category-pill {
            font-size: 0.82rem;
            font-weight: 600;
            padding: 7px 15px;
            border-radius: 20px;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
            display: inline-block;
        }

        .category-pill.active {
            background-color: #D9251C;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(217, 37, 28, 0.25);
        }

        .category-pill:not(.active) {
            background-color: #f8f9fa;
            color: #495057;
            border: 1px solid #e9ecef;
        }

        .category-pill:not(.active):hover {
            background-color: #e9ecef;
            color: #212529;
        }
    </style>
    @endpush

    <div class="container-fluid">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="las la-check-circle me-2 fs-18"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Master Title Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="don-header-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h4 class="fs-24 mb-1 fw-bold text-dark" style="letter-spacing: -0.5px;">Donations Module</h4>
                        <p class="text-muted small mb-0">Manage Donor Database, Cash/Book/Equipment Contributions, Restricted Funds, Supported Projects, Acknowledgement Receipts, Tax Docs & Campaign Tracking.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-danger btn-sm px-3 text-white rounded shadow-sm d-flex align-items-center gap-2" style="background-color: #D9251C; border-color: #D9251C; height: 40px;" data-bs-toggle="modal" data-bs-target="#recordDonationModal">
                            <i class="las la-plus-circle fs-18"></i> Record Donation
                        </button>
                        <button class="btn btn-outline-danger btn-sm px-3 rounded shadow-sm d-flex align-items-center gap-2" style="color: #D9251C; border-color: #D9251C; height: 40px;" data-bs-toggle="modal" data-bs-target="#addDonorModal">
                            <i class="las la-user-plus fs-18"></i> Register Donor
                        </button>
                        <button class="btn btn-outline-secondary btn-sm px-3 rounded shadow-sm d-flex align-items-center gap-2" style="height: 40px;" data-bs-toggle="modal" data-bs-target="#addCampaignModal">
                            <i class="las la-flag fs-18"></i> Create Campaign
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric summary cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 50px; height: 50px; color: #D9251C;">
                            <i class="las la-hand-holding-usd fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Total Cash Donations</span>
                            <h4 class="fw-bold text-dark mb-0">₱{{ number_format($metrics['total_cash_raised'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-primary" style="width: 50px; height: 50px;">
                            <i class="las la-gift fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">In-Kind (Books/Equip)</span>
                            <h4 class="fw-bold text-primary mb-0">{{ number_format($metrics['total_in_kind_count']) }} Donated</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-success" style="width: 50px; height: 50px;">
                            <i class="las la-users fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Registered Donors</span>
                            <h4 class="fw-bold text-success mb-0">{{ number_format($metrics['active_donors_count']) }} Active</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 50px; height: 50px; color: #D9251C;">
                            <i class="las la-bullhorn fs-28"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Campaign Raised Funds</span>
                            <h4 class="fw-bold mb-0" style="color: #D9251C;">₱{{ number_format($metrics['total_campaign_raised'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Donations Sub-Modules Filter Pills -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm p-3" style="border-radius: 12px; background: #fff;">
                    <span class="text-muted small fw-bold mb-2 d-block text-uppercase">Donation Sub-Modules & Views:</span>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($filterTabs as $tab)
                        <a href="{{ route('admin-finance.donations.index', ['tab' => $tab]) }}" class="category-pill {{ $selectedTab == $tab ? 'active' : '' }}">
                            {{ $tab }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Sub-Module View Render -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold text-dark fs-18">
                                {{ $selectedTab === 'All' ? 'Master Donations Ledger' : $selectedTab }}
                            </h5>
                            <p class="text-muted small mb-0">Record of donor contributions, fund allocation, receipts, and compliance</p>
                        </div>
                        <form action="{{ route('admin-finance.donations.index') }}" method="GET" class="d-flex gap-2">
                            <input type="hidden" name="tab" value="{{ $selectedTab }}">
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search No, Donor, Project..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-sm text-white px-3" style="background-color: #D9251C; border-color: #D9251C;">Search</button>
                        </form>
                    </div>

                    <div class="card-body pt-2">
                        @if($selectedTab === 'Donor Database' || $selectedTab === 'Reports by Donor')
                        <!-- DONOR DATABASE TABLE -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-dark text-white small text-uppercase">
                                    <tr>
                                        <th>Donor Code</th>
                                        <th>Donor Name</th>
                                        <th>Type</th>
                                        <th>Contact Email & Phone</th>
                                        <th>Tax ID</th>
                                        <th class="text-center">Recurring Status</th>
                                        <th class="text-end">Total Cash Donated</th>
                                        <th class="text-center">Donations Count</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($donors as $dnr)
                                    <tr class="hover-row">
                                        <td><span class="fw-bold text-dark font-monospace">{{ $dnr->donor_code }}</span></td>
                                        <td><span class="fw-bold text-dark fs-14">{{ $dnr->name }}</span></td>
                                        <td><span class="badge bg-light text-dark border">{{ $dnr->type }}</span></td>
                                        <td>
                                            <span class="d-block text-dark small">{{ $dnr->email ?: 'N/A' }}</span>
                                            <span class="text-muted small">{{ $dnr->phone ?: 'N/A' }}</span>
                                        </td>
                                        <td><span class="font-monospace text-muted small">{{ $dnr->tax_id ?: 'N/A' }}</span></td>
                                        <td class="text-center">
                                            @if($dnr->is_recurring)
                                            <span class="badge bg-success-subtle text-success"><i class="las la-sync me-1"></i> Recurring Donor</span>
                                            @else
                                            <span class="badge bg-secondary-subtle text-secondary">One-time / Ad-hoc</span>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold text-success">₱{{ number_format($dnr->total_donated_cash, 2) }}</td>
                                        <td class="text-center fw-bold text-dark">{{ $dnr->total_donations_count }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-success-subtle text-success">{{ $dnr->status }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">No donors registered in database yet. Click "Register Donor" above.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @elseif($selectedTab === 'Campaign Tracking')
                        <!-- CAMPAIGN TRACKING TABLE -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-dark text-white small text-uppercase">
                                    <tr>
                                        <th>Campaign Code</th>
                                        <th>Campaign Title</th>
                                        <th>Start & End Date</th>
                                        <th class="text-end">Target Goal</th>
                                        <th class="text-end text-success">Raised Amount</th>
                                        <th class="text-center">Fund Progress</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($campaigns as $cmp)
                                    @php
                                        $tgt = max(1, $cmp->target_amount);
                                        $pct = min(100, round(($cmp->raised_amount / $tgt) * 100, 1));
                                    @endphp
                                    <tr class="hover-row">
                                        <td><span class="fw-bold text-dark font-monospace">{{ $cmp->campaign_code }}</span></td>
                                        <td><span class="fw-bold text-dark fs-14">{{ $cmp->title }}</span></td>
                                        <td>
                                            <span class="d-block small text-dark">{{ $cmp->start_date ? $cmp->start_date->format('M d, Y') : 'N/A' }}</span>
                                            <span class="text-muted small">to {{ $cmp->end_date ? $cmp->end_date->format('M d, Y') : 'Ongoing' }}</span>
                                        </td>
                                        <td class="text-end fw-bold text-dark">₱{{ number_format($cmp->target_amount, 2) }}</td>
                                        <td class="text-end fw-bold text-success">₱{{ number_format($cmp->raised_amount, 2) }}</td>
                                        <td class="text-center">
                                            <div class="progress" style="height: 10px; min-width: 100px;">
                                                <div class="progress-bar" style="width: {{ $pct }}%; background-color: #D9251C;"></div>
                                            </div>
                                            <span class="small font-monospace fw-bold text-dark">{{ $pct }}%</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success-subtle text-success">{{ $cmp->status }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">No active fundraising campaigns recorded. Click "Create Campaign" above.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @else
                        <!-- DONATIONS LEDGER TABLE -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-dark text-white small text-uppercase">
                                    <tr>
                                        <th>Donation No</th>
                                        <th>Donor Name</th>
                                        <th>Type</th>
                                        <th>Details / Description</th>
                                        <th class="text-end">Value / Amount</th>
                                        <th>Restriction / Purpose</th>
                                        <th>Supported Project</th>
                                        <th>Ack. Receipt No</th>
                                        <th>Tax Certificate</th>
                                        <th>Date</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($donations as $don)
                                    <tr class="hover-row">
                                        <td><span class="fw-bold text-dark font-monospace">{{ $don->donation_no }}</span></td>
                                        <td>
                                            <span class="fw-bold text-dark d-block fs-14">{{ $don->donor ? $don->donor->name : 'Anonymous' }}</span>
                                            <span class="text-muted small">{{ $don->donor ? $don->donor->type : '' }}</span>
                                        </td>
                                        <td>
                                            @if($don->donation_type === 'Cash')
                                            <span class="badge bg-success-subtle text-success">Cash</span>
                                            @elseif($don->donation_type === 'Books')
                                            <span class="badge bg-info-subtle text-info">Book Contribution</span>
                                            @else
                                            <span class="badge bg-primary-subtle text-primary">Equipment</span>
                                            @endif
                                        </td>
                                        <td><span class="text-dark small">{{ $don->item_description ?: 'Direct Cash Grant' }}</span></td>
                                        <td class="text-end fw-bold text-success">₱{{ number_format($don->amount, 2) }}</td>
                                        <td>
                                            @if($don->is_restricted)
                                            <span class="badge bg-warning-subtle text-warning d-block mb-1">Restricted</span>
                                            <span class="text-muted small">{{ $don->restricted_fund_purpose }}</span>
                                            @else
                                            <span class="badge bg-light text-dark border">Unrestricted</span>
                                            @endif
                                        </td>
                                        <td><span class="fw-bold text-dark small">{{ $don->project_supported ?: 'General Ministry Fund' }}</span></td>
                                        <td><span class="font-monospace text-dark small fw-bold">{{ $don->receipt_number }}</span></td>
                                        <td>
                                            @if($don->tax_doc_issued)
                                            <span class="badge bg-success-subtle text-success font-monospace">{{ $don->tax_cert_number }}</span>
                                            @else
                                            <span class="text-muted small">Not Issued</span>
                                            @endif
                                        </td>
                                        <td><span class="fw-bold text-dark small">{{ $don->donation_date ? $don->donation_date->format('M d, Y') : 'N/A' }}</span></td>
                                        <td class="text-center">
                                            <a href="{{ route('admin-finance.donations.show', $don->id) }}" class="btn btn-sm btn-outline-danger px-2 py-1" style="color: #D9251C; border-color: #D9251C;">
                                                <i class="las la-file-invoice"></i> View Receipt
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-4 text-muted">No donations recorded matching your filter. Click "Record Donation" above to record a contribution!</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 1: REGISTER DONOR -->
    <div class="modal fade" id="addDonorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin-finance.donations.donor.store') }}" method="POST">
                    @csrf
                    <div class="modal-header text-white" style="background-color: #D9251C;">
                        <h5 class="modal-title fw-bold"><i class="las la-user-plus me-2"></i>Register New Donor</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Donor Name / Institution <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. San Miguel Foundation, Bro. Juan dela Cruz, St. Peter Parish" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Donor Category <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="Individual">Individual Donor</option>
                                <option value="Corporate">Corporate Donor</option>
                                <option value="Foundation">Foundation / Grantor</option>
                                <option value="Religious/Parish">Religious / Parish / Diocese</option>
                            </select>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small text-muted">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="donor@example.com">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small text-muted">Phone Number</label>
                                <input type="text" name="phone" class="form-control" placeholder="0917-000-0000">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Tax Identification Number (TIN)</label>
                            <input type="text" name="tax_id" class="form-control" placeholder="123-456-789-000">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_recurring" value="1" id="recurringCheck">
                            <label class="form-check-label fw-bold small" for="recurringCheck">Pledged Recurring Donor (Monthly/Annual)</label>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn text-white px-4 fw-bold" style="background-color: #D9251C; border-color: #D9251C;">Save Donor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: RECORD DONATION -->
    <div class="modal fade" id="recordDonationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin-finance.donations.donation.store') }}" method="POST">
                    @csrf
                    <div class="modal-header text-white" style="background-color: #D9251C;">
                        <h5 class="modal-title fw-bold"><i class="las la-hand-holding-usd me-2"></i>Record Donation Entry</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Select Donor <span class="text-danger">*</span></label>
                                <select name="donor_id" class="form-select" required>
                                    @foreach($donors as $dnr)
                                    <option value="{{ $dnr->id }}">{{ $dnr->name }} ({{ $dnr->type }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Link to Campaign (Optional)</label>
                                <select name="campaign_id" class="form-select">
                                    <option value="">General Contribution / No Campaign</option>
                                    @foreach($campaigns as $cmp)
                                    <option value="{{ $cmp->id }}">{{ $cmp->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Donation Type <span class="text-danger">*</span></label>
                                <select name="donation_type" class="form-select" required>
                                    <option value="Cash">Cash / Cheque / Bank Transfer</option>
                                    <option value="Books">Book Donation (Publications)</option>
                                    <option value="Equipment">Equipment Donation (Machinery/Computers)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Amount / Estimated Value (₱) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount" class="form-control" placeholder="10000.00" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Donation Date <span class="text-danger">*</span></label>
                                <input type="date" name="donation_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted">In-Kind Description / Items Details</label>
                                <input type="text" name="item_description" class="form-control" placeholder="e.g. 500 copies of Bible Stories, Heidelberg Press Parts, 10 Desktop Computers">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Project Supported</label>
                                <input type="text" name="project_supported" class="form-control" placeholder="e.g. Prison Ministry Bibles, Mission School Printing, Seminary Books">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Restricted Fund Purpose (If Restricted)</label>
                                <input type="text" name="restricted_fund_purpose" class="form-control" placeholder="e.g. Earmarked exclusively for Bibles Printing">
                            </div>
                            <div class="col-md-6 d-flex align-items-center pt-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_restricted" value="1" id="restrCheck">
                                    <label class="form-check-label fw-bold small" for="restrCheck">Restricted Fund (Earmarked Purpose)</label>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-center pt-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="tax_doc_issued" value="1" id="taxCertCheck" checked>
                                    <label class="form-check-label fw-bold small" for="taxCertCheck">Issue Donation Tax Deduction Certificate</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn text-white px-4 fw-bold" style="background-color: #D9251C; border-color: #D9251C;">Issue Receipt & Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 3: CREATE CAMPAIGN -->
    <div class="modal fade" id="addCampaignModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin-finance.donations.campaign.store') }}" method="POST">
                    @csrf
                    <div class="modal-header text-white" style="background-color: #D9251C;">
                        <h5 class="modal-title fw-bold"><i class="las la-flag me-2"></i>Create Fundraising Campaign</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Campaign Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. 2026 Bible Distribution Drive, Printing Press Upgrade Fund" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Target Fundraising Goal (₱) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="target_amount" class="form-control" placeholder="500000.00" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small text-muted">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small text-muted">Target End Date</label>
                                <input type="date" name="end_date" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Campaign Description</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Campaign objectives and beneficiaries..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn text-white px-4 fw-bold" style="background-color: #D9251C; border-color: #D9251C;">Launch Campaign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

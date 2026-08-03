<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .don-header-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.5rem 1.75rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            margin-bottom: 1.5rem;
        }

        .btn-don-primary {
            background-color: #D9251C;
            border-color: #D9251C;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.8125rem;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
        }

        .btn-don-primary:hover {
            background-color: #b91c1c;
            border-color: #b91c1c;
            color: #ffffff;
        }

        .don-kpi-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.25rem;
            transition: all 0.2s ease;
        }

        .don-kpi-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }

        .kpi-icon-wrapper {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .table-custom-header thead th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-size: 0.725rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            border-bottom: 1px solid #e2e8f0 !important;
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

        <!-- Master Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="don-header-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(217, 37, 28, 0.08); color: #D9251C;">
                            <i class="las la-hand-holding-heart fs-24"></i>
                        </div>
                        <div>
                            <h4 class="fs-20 mb-1 fw-bold text-dark" style="letter-spacing: -0.3px;">Donations Module</h4>
                            <p class="text-muted small mb-0">Record and track donor contributions, cash gifts, in-kind donations, and restricted funds.</p>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-don-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#recordDonationModal">
                            <i class="las la-plus-circle fs-16"></i> Record Donation
                        </button>
                        <button class="btn btn-outline-danger btn-sm px-3 rounded shadow-sm d-flex align-items-center gap-2" style="color: #D9251C; border-color: #D9251C; height: 38px;" data-bs-toggle="modal" data-bs-target="#registerDonorModal">
                            <i class="las la-user-plus fs-16"></i> Register Donor
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric summary cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="don-kpi-card h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.725rem; letter-spacing: 0.5px;">Total Cash Donations</span>
                        <div class="kpi-icon-wrapper" style="background-color: #f0fdf4; color: #16a34a;">
                            <i class="las la-hand-holding-usd fs-20"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-dark mb-0 fs-20">₱{{ number_format($metrics['total_cash_raised'], 2) }}</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="don-kpi-card h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.725rem; letter-spacing: 0.5px;">In-Kind Contributions</span>
                        <div class="kpi-icon-wrapper" style="background-color: #eff6ff; color: #2563eb;">
                            <i class="las la-gift fs-20"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-dark mb-0 fs-20">{{ $metrics['total_in_kind_count'] }} <span class="fs-14 fw-normal text-muted">Donations</span></h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="don-kpi-card h-100 position-relative" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#donorsListModal" title="Click to view Donor List">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.725rem; letter-spacing: 0.5px;">Registered Donors <i class="las la-external-link-alt ms-1 text-danger"></i></span>
                        <div class="kpi-icon-wrapper" style="background-color: #fef2f2; color: #D9251C;">
                            <i class="las la-users fs-20"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-end">
                        <h4 class="fw-bold text-dark mb-0 fs-20">{{ $metrics['active_donors_count'] }} <span class="fs-14 fw-normal text-muted">Active</span></h4>
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2 py-1 small">Click to View List</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Donations Ledger Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border: 1px solid #e2e8f0 !important;">
                    <div class="card-header bg-white border-0 pt-3 pb-2 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold text-dark mb-0"><i class="las la-list me-2 fs-18"></i>Donations Ledger</h6>
                        <form method="GET" action="{{ route('admin-finance.donations.index') }}" class="d-flex align-items-center gap-2">
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search donor, receipt..." value="{{ $search ?? '' }}" style="width: 220px; border-radius: 6px;">
                            <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="las la-search"></i></button>
                        </form>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle table-custom-header">
                                <thead>
                                    <tr>
                                        <th>Donation No</th>
                                        <th>Donor Name</th>
                                        <th>Type</th>
                                        <th class="text-end">Amount / Value</th>
                                        <th>Purpose / Details</th>
                                        <th>Date</th>
                                        <th class="text-center" style="width: 100px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($donations as $don)
                                    <tr>
                                        <td><span class="fw-bold text-dark">{{ $don->donation_no }}</span></td>
                                        <td>
                                            <span class="fw-bold text-dark">{{ $don->donor->name ?? 'Anonymous' }}</span>
                                            @if($don->donor && $don->donor->type)
                                                <small class="text-muted d-block" style="font-size: 0.75rem;">({{ $don->donor->type }})</small>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-light text-dark border px-2 py-1">{{ $don->donation_type }}</span></td>
                                        <td class="text-end fw-bold text-dark">₱{{ number_format($don->amount, 2) }}</td>
                                        <td>
                                            <span class="text-muted small">
                                                {{ $don->restricted_fund_purpose ?: ($don->item_description ?: ($don->project_supported ?: 'General Fund')) }}
                                            </span>
                                        </td>
                                        <td><span class="text-muted small">{{ $don->donation_date ? \Carbon\Carbon::parse($don->donation_date)->format('M d, Y') : 'N/A' }}</span></td>
                                        <td class="text-center">
                                            <form action="{{ route('admin-finance.donations.destroy', $don->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this donation record?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm border-0" title="Delete Donation">
                                                    <i class="las la-trash-alt fs-18"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">No donation records found. Click "Record Donation" above to add a contribution!</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Simple Record Donation Modal -->
    <div class="modal fade" id="recordDonationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-dark"><i class="las la-plus-circle me-1 text-danger"></i> Record Contribution</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin-finance.donations.donation.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Select Donor *</label>
                            <select name="donor_id" class="form-select" required style="border-radius: 8px;">
                                <option value="" disabled selected>Select Donor...</option>
                                @foreach($donors as $dnr)
                                    <option value="{{ $dnr->id }}">{{ $dnr->name }} ({{ $dnr->type }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Donation Type *</label>
                                <select name="donation_type" class="form-select" required style="border-radius: 8px;">
                                    <option value="Cash">Cash / Transfer</option>
                                    <option value="Books">Books / Print Materials</option>
                                    <option value="Equipment">Equipment / Asset</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Amount / Value (₱) *</label>
                                <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required style="border-radius: 8px;">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Date *</label>
                            <input type="date" name="donation_date" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius: 8px;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Purpose / Item Details</label>
                            <textarea name="item_description" class="form-control" rows="2" placeholder="e.g., Book donation for library or General Cash Fund..." style="border-radius: 8px;"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4">
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-don-primary px-4">Save Donation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Register Donor Modal -->
    <div class="modal fade" id="registerDonorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-dark"><i class="las la-user-plus me-1 text-danger"></i> Register Donor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin-finance.donations.donor.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Donor Name / Organization *</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g., St. Paul Educational Trust" required style="border-radius: 8px;">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Donor Type *</label>
                                <select name="type" class="form-select" required style="border-radius: 8px;">
                                    <option value="Individual">Individual</option>
                                    <option value="Corporate">Corporate</option>
                                    <option value="Institutional">Institutional</option>
                                    <option value="Foundation">Foundation</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="donor@example.com" style="border-radius: 8px;">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="e.g., 0917-123-4567" style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4">
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-don-primary px-4">Register Donor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Registered Donors List Modal -->
    <div class="modal fade" id="donorsListModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold text-dark"><i class="las la-users me-1 text-danger"></i> Registered Donors Management</h5>
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#registerDonorModal">
                            <i class="las la-plus-circle me-1"></i> Register New Donor
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-custom-header">
                            <thead>
                                <tr>
                                    <th>Donor Code</th>
                                    <th>Donor Name</th>
                                    <th>Type</th>
                                    <th>Email / Contact</th>
                                    <th class="text-end">Total Donated</th>
                                    <th>Registered Date</th>
                                    <th class="text-center" style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($donors as $dnr)
                                <tr>
                                    <td><span class="fw-bold text-dark">{{ $dnr->donor_code ?: 'DNR-'.$dnr->id }}</span></td>
                                    <td><span class="fw-bold text-dark">{{ $dnr->name }}</span></td>
                                    <td><span class="badge bg-light text-dark border px-2 py-1">{{ $dnr->type }}</span></td>
                                    <td>
                                        <small class="text-muted d-block">{{ $dnr->email ?: 'N/A' }}</small>
                                        <small class="text-muted">{{ $dnr->phone ?: '' }}</small>
                                    </td>
                                    <td class="text-end fw-bold text-dark">₱{{ number_format($dnr->total_donated ?: 0, 2) }}</td>
                                    <td><span class="text-muted small">{{ $dnr->created_at ? $dnr->created_at->format('M d, Y') : 'N/A' }}</span></td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button class="btn btn-outline-primary btn-sm border-0" data-bs-toggle="modal" data-bs-target="#editDonorModal_{{ $dnr->id }}" title="Edit Donor">
                                                <i class="las la-edit fs-18"></i>
                                            </button>
                                            <form action="{{ route('admin-finance.donations.donor.destroy', $dnr->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete donor \'{{ $dnr->name }}\'?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm border-0" title="Delete Donor">
                                                    <i class="las la-trash-alt fs-18"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No donors registered yet. Click "Register New Donor" to add one!</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Donor Modals -->
    @foreach($donors as $dnr)
    <div class="modal fade" id="editDonorModal_{{ $dnr->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-dark"><i class="las la-edit me-1 text-primary"></i> Edit Donor Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin-finance.donations.donor.update', $dnr->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Donor Name / Organization *</label>
                            <input type="text" name="name" class="form-control" value="{{ $dnr->name }}" required style="border-radius: 8px;">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Donor Type *</label>
                                <select name="type" class="form-select" required style="border-radius: 8px;">
                                    <option value="Individual" {{ $dnr->type == 'Individual' ? 'selected' : '' }}>Individual</option>
                                    <option value="Corporate" {{ $dnr->type == 'Corporate' ? 'selected' : '' }}>Corporate</option>
                                    <option value="Institutional" {{ $dnr->type == 'Institutional' ? 'selected' : '' }}>Institutional</option>
                                    <option value="Foundation" {{ $dnr->type == 'Foundation' ? 'selected' : '' }}>Foundation</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $dnr->email }}" style="border-radius: 8px;">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="{{ $dnr->phone }}" style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4">
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px;">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</x-app-layout>

<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .don-card {
            background: #fff;
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .receipt-box {
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            padding: 1.5rem;
            background-color: #f8fafc;
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

        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('admin-finance.donations.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                        <i class="las la-arrow-left me-1"></i> Back to Donations Ledger
                    </a>
                    <h4 class="fs-24 fw-bold text-dark mb-0">Donation Entry: {{ $donation->donation_no }}</h4>
                    <p class="text-muted small mb-0">Official Acknowledgement Receipt & Tax Certificate Summary</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn text-white btn-sm px-3 rounded shadow-sm d-flex align-items-center gap-2" style="background-color: #D9251C; border-color: #D9251C; height: 40px;" onclick="window.print()">
                        <i class="las la-print fs-18"></i> Print Official Receipt
                    </button>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-9 mb-4">
                <div class="don-card">
                    <!-- OFFICIAL ACKNOWLEDGEMENT RECEIPT HEADER -->
                    <div class="d-flex justify-content-between align-items-start pb-3 border-bottom mb-4">
                        <div>
                            <h4 class="fw-bold mb-1" style="color: #D9251C;">CLARETIAN PUBLICATIONS</h4>
                            <p class="text-muted small mb-0">Official Donation Acknowledgement Receipt</p>
                            <span class="badge bg-light text-dark border font-monospace mt-1">Receipt No: {{ $donation->receipt_number }}</span>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold text-dark d-block">Date Issued:</span>
                            <span class="text-dark">{{ $donation->donation_date ? $donation->donation_date->format('F d, Y') : 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- DONOR INFORMATION & CONTRIBUTION VALUE -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded border">
                                <span class="text-muted small d-block text-uppercase fw-bold">Received From Donor:</span>
                                <h5 class="fw-bold text-dark mb-1">{{ $donation->donor ? $donation->donor->name : 'Anonymous Donor' }}</h5>
                                <span class="badge bg-secondary-subtle text-secondary me-2">{{ $donation->donor ? $donation->donor->type : 'Individual' }}</span>
                                @if($donation->donor && $donation->donor->tax_id)
                                <span class="small font-monospace text-muted">TIN: {{ $donation->donor->tax_id }}</span>
                                @endif
                                <div class="mt-2 text-muted small">
                                    <span>Email: {{ $donation->donor ? ($donation->donor->email ?: 'N/A') : 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 rounded text-white" style="background-color: #D9251C;">
                                <span class="small text-white-50 d-block text-uppercase fw-bold">Total Contribution Amount / Value:</span>
                                <h2 class="fw-bold mb-0">₱{{ number_format($donation->amount, 2) }}</h2>
                                <span class="small text-white-50">Type: {{ $donation->donation_type }} Donation</span>
                            </div>
                        </div>
                    </div>

                    <!-- DONATION SPECIFICATIONS -->
                    <div class="receipt-box mb-4">
                        <h6 class="fw-bold text-dark mb-3"><i class="las la-info-circle me-1"></i>Donation Details & Allocation</h6>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Contribution Type:</span>
                                <span class="fw-bold text-dark fs-15">{{ $donation->donation_type }}</span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Project Supported:</span>
                                <span class="fw-bold text-dark fs-15">{{ $donation->project_supported ?: 'General Ministry & Educational Fund' }}</span>
                            </div>
                            <div class="col-12">
                                <span class="text-muted small d-block">In-Kind Description / Items Donated:</span>
                                <span class="fw-bold text-dark fs-15">{{ $donation->item_description ?: 'Monetary Contribution' }}</span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Fund Restriction Status:</span>
                                @if($donation->is_restricted)
                                <span class="badge bg-warning-subtle text-warning fs-14">Restricted Fund</span>
                                <p class="text-muted small mb-0 mt-1">Purpose: {{ $donation->restricted_fund_purpose }}</p>
                                @else
                                <span class="badge bg-success-subtle text-success fs-14">Unrestricted Fund</span>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Linked Campaign:</span>
                                <span class="fw-bold text-dark fs-15">{{ $donation->campaign ? $donation->campaign->title : 'General / Independent Contribution' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- TAX DOCUMENTATION CERTIFICATE STATUS -->
                    <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <span class="fw-bold text-dark d-block">Donation Tax Deduction Certificate Status</span>
                            <span class="text-muted small">Issued for donor's tax deduction documentation</span>
                        </div>
                        <div>
                            @if($donation->tax_doc_issued)
                            <span class="badge bg-success-subtle text-success px-3 py-2 fs-14"><i class="las la-certificate me-1"></i> Issued: {{ $donation->tax_cert_number }}</span>
                            @else
                            <span class="badge bg-secondary-subtle text-secondary px-3 py-2 fs-14">Certificate Not Issued</span>
                            @endif
                        </div>
                    </div>

                    <div class="row pt-4 text-center border-top">
                        <div class="col-md-6 mb-3">
                            <span class="text-muted small d-block">Received & Prepared By:</span>
                            <div class="mt-4 pt-2 border-top mx-auto" style="width: 200px;">
                                <span class="fw-bold text-dark d-block">Finance Manager</span>
                                <span class="text-muted small">Claretian Publications</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <span class="text-muted small d-block">Approved By:</span>
                            <div class="mt-4 pt-2 border-top mx-auto" style="width: 200px;">
                                <span class="fw-bold text-dark d-block">Executive Director</span>
                                <span class="text-muted small">Claretian Communications</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

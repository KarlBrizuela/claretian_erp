<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .detail-card {
            background: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .info-item label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #666;
            margin-bottom: 0.25rem;
        }

        .info-item p {
            font-size: 1rem;
            color: #333;
            margin-bottom: 0;
            padding: 0.5rem;
            background: #f8f9fa;
            border-radius: 4px;
            border: 1px solid #e9ecef;
        }

        .attachment-list {
            list-style: none;
            padding: 0;
        }

        .attachment-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            margin-bottom: 0.5rem;
            transition: all 0.2s;
        }

        .attachment-item:hover {
            border-color: #ff0000;
            background: #fff5f5;
        }

        .attachment-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .attachment-icon {
            font-size: 1.5rem;
            color: #ff0000;
        }

        .action-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-10 mx-auto">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-0">Account Statement Request</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin-finance.credit-collection.billing') }}">Billing List</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Request Details</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('admin-finance.credit-collection.billing') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="las la-arrow-left me-1"></i> Back to List
                </a>
            </div>

            <div class="detail-card">
                <div class="section-title">
                    <i class="las la-file-contract text-primary"></i>
                    Section 1: Contract Information
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Contract Number / Request ID</label>
                        <p>CONT-2026-0042</p>
                    </div>
                    <div class="info-item">
                        <label>Source Department</label>
                        <p>Ads & Promo</p>
                    </div>
                    <div class="info-item col-12">
                        <label>Customer Name</label>
                        <p>ABC Corporation</p>
                    </div>
                    <div class="info-item">
                        <label>Contact Person</label>
                        <p>John Doe</p>
                    </div>
                    <div class="info-item">
                        <label>Address</label>
                        <p>123 Business Ave, Quezon City, Metro Manila</p>
                    </div>
                    <div class="info-item">
                        <label>Products / Services Selected</label>
                        <p>Inside Back (Full Color) 8.5x11 - Monthly Magazine</p>
                    </div>
                    <div class="info-item">
                        <label>Contract Rates / Prices</label>
                        <p>₱ 25,000.00 / insertion</p>
                    </div>
                    <div class="info-item">
                        <label>Contract Period</label>
                        <p>Jan 01, 2026 - Dec 31, 2026</p>
                    </div>
                    <div class="info-item">
                        <label>Remarks from Ads & Promo</label>
                        <p>Client requested priority placement for the first quarter of 2026.</p>
                    </div>
                </div>
            </div>

            <div class="detail-card">
                <div class="section-title">
                    <i class="las la-paperclip text-primary"></i>
                    Section 2: Attachments
                </div>
                <ul class="attachment-list">
                    <li class="attachment-item">
                        <div class="attachment-info">
                            <i class="las la-file-pdf attachment-icon"></i>
                            <div>
                                <div class="fw-bold small">Signed Contract.pdf</div>
                                <div class="text-muted extra-small">Uploaded: Feb 01, 2026</div>
                            </div>
                        </div>
                        <button class="btn btn-outline-primary btn-xs"><i class="las la-download"></i> Download</button>
                    </li>
                    <li class="attachment-item">
                        <div class="attachment-info">
                            <i class="las la-file-image attachment-icon"></i>
                            <div>
                                <div class="fw-bold small">Advertisement Material - Jan2026.jpeg</div>
                                <div class="text-muted extra-small">Uploaded: Feb 02, 2026</div>
                            </div>
                        </div>
                        <button class="btn btn-outline-primary btn-xs"><i class="las la-download"></i> Download</button>
                    </li>
                </ul>
            </div>

            <div class="detail-card bg-light">
                <div class="section-title">
                    <i class="las la-cog text-primary"></i>
                    Section 3: Actions
                </div>
                <div class="action-footer">
                    <div class="text-muted small">
                        <i class="las la-info-circle me-1"></i> Review all contract details and materials above. Clicking "Prepare Account Statement" will open the preparation editor.
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin-finance.credit-collection.billing.create', $id) }}" class="btn btn-primary d-flex align-items-center">
                            <i class="las la-file-invoice me-2 fs-5"></i> Prepare Account Statement
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

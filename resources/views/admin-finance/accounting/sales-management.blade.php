<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .coa-header-card {
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
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
            border-left-width: 6px !important;
        }

        .transition-icon {
            transition: transform 0.2s ease;
        }

        .cursor-pointer[aria-expanded="true"] .transition-icon {
            transform: rotate(180deg);
        }

        .bg-soft-success {
            background-color: rgba(40, 167, 69, 0.1);
        }

        .text-success {
            color: #28a745 !important;
        }

        .bg-soft-primary {
            background-color: rgba(217, 37, 28, 0.08);
        }

        .text-primary {
            color: #D9251C !important;
        }
        
        .bg-soft-info {
            background-color: rgba(23, 162, 184, 0.1);
        }

        .text-info {
            color: #17a2b8 !important;
        }

        .bg-soft-warning {
            background-color: rgba(255, 193, 7, 0.15);
        }

        .text-warning {
            color: #ffc107 !important;
        }

        .modal-tabs .nav-link {
            border: none;
            color: #666;
            font-weight: 500;
            padding: 10px 15px;
            font-size: 0.85rem;
            border-bottom: 2px solid transparent;
            border-radius: 0;
        }

        .modal-tabs .nav-link.active {
            color: #D9251C;
            border-bottom: 2px solid #D9251C;
            background: transparent;
        }
    </style>
    @endpush

    <div class="container-fluid">
        <!-- Top Title & Overview Header -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="coa-header-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h4 class="fs-24 mb-1 fw-bold text-dark" style="letter-spacing: -0.5px;">Sales & Receipts Ledger - 
                            @if($tab === 'bookstore') Bookstore 
                            @elseif($tab === 'areasales') Area Sales 
                            @elseif($tab === 'ecom') E-Commerce 
                            @elseif($tab === 'wholesale') Wholesale 
                            @elseif($tab === 'complimentary') Complimentary Receipt 
                            @else {{ ucfirst($tab) }} @endif
                        </h4>
                        <p class="text-muted small mb-0">CCFI Sales Management ledger containing {{ ucfirst($tab) }} accounts, receipts tracking, and performance logs.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs Bar -->
        @if($tab !== 'complimentary')
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body p-2">
                        <ul class="nav nav-pills nav-fill gap-2">
                            <li class="nav-item">
                                <a class="nav-link py-2 fw-bold {{ $tab === 'bookstore' ? 'active bg-danger text-white' : 'text-dark' }}" href="{{ route('admin-finance.accounting.sales-management', ['tab' => 'bookstore']) }}">
                                    <i class="las la-store me-1"></i> Bookstore
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2 fw-bold {{ $tab === 'areasales' ? 'active bg-danger text-white' : 'text-dark' }}" href="{{ route('admin-finance.accounting.sales-management', ['tab' => 'areasales']) }}">
                                    <i class="las la-map-marked-alt me-1"></i> Area Sales
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2 fw-bold {{ $tab === 'ecom' ? 'active bg-danger text-white' : 'text-dark' }}" href="{{ route('admin-finance.accounting.sales-management', ['tab' => 'ecom']) }}">
                                    <i class="las la-shopping-cart me-1"></i> E-Commerce
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2 fw-bold {{ $tab === 'wholesale' ? 'active bg-danger text-white' : 'text-dark' }}" href="{{ route('admin-finance.accounting.sales-management', ['tab' => 'wholesale']) }}">
                                    <i class="las la-boxes me-1"></i> Wholesale
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Render the selected component only -->
        <div class="row">
            <div class="col-12">
                @if($tab === 'bookstore')
                    @include('admin-finance.accounting.sales-management.bookstore')
                @elseif($tab === 'areasales')
                    @include('admin-finance.accounting.sales-management.areasales')
                @elseif($tab === 'ecom')
                    @include('admin-finance.accounting.sales-management.ecom')
                @elseif($tab === 'wholesale')
                    @include('admin-finance.accounting.sales-management.wholesale')
                @elseif($tab === 'complimentary')
                    @include('admin-finance.accounting.sales-management.complimentary')
                @endif
            </div>
        </div>
    </div>

    <!-- Generic Ledger Detail Modal -->
    <div class="modal fade" id="salesLedgerModal" tabindex="-1" aria-labelledby="salesLedgerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="salesLedgerModalLabel">Account Ledger</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="salesLedgerModalBody">
                    <!-- Loaded dynamically via Javascript -->
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // --- CLIENT-SIDE TABLE PAGINATION FOR CARD LEDGER MODALS ---
        function initTablePagination(tableElement, itemsPerPage = 5) {
            const tbody = tableElement.querySelector('tbody');
            if (!tbody) return;
            
            const rows = Array.from(tbody.querySelectorAll('tr'));
            if (rows.length === 1 && rows[0].querySelector('td[colspan]')) return;
            if (rows.length <= itemsPerPage) return;
            
            const totalItems = rows.length;
            const totalPages = Math.ceil(totalItems / itemsPerPage);
            let currentPage = 1;
            
            // Create pagination container
            const nav = document.createElement('nav');
            nav.className = 'd-flex justify-content-between align-items-center mt-3';
            
            const info = document.createElement('div');
            info.className = 'small text-muted';
            
            const ul = document.createElement('ul');
            ul.className = 'pagination pagination-xs mb-0';
            
            nav.appendChild(info);
            nav.appendChild(ul);
            
            const wrapper = tableElement.closest('.table-responsive') || tableElement;
            wrapper.parentNode.appendChild(nav);
            
            function showPage(page) {
                currentPage = page;
                const start = (page - 1) * itemsPerPage;
                const end = start + itemsPerPage;
                
                rows.forEach((row, idx) => {
                    if (idx >= start && idx < end) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                info.textContent = `Showing ${start + 1} to ${Math.min(end, totalItems)} of ${totalItems} entries`;
                ul.innerHTML = '';
                
                // Prev
                const prevLi = document.createElement('li');
                prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
                prevLi.innerHTML = `<a class="page-link" href="#" style="border-radius: 4px; margin-right: 4px; padding: 4px 8px; font-size: 0.75rem;">&laquo;</a>`;
                prevLi.querySelector('a').onclick = (e) => {
                    e.preventDefault();
                    if (currentPage > 1) showPage(currentPage - 1);
                };
                ul.appendChild(prevLi);
                
                // Numbers
                for (let i = 1; i <= totalPages; i++) {
                    if (totalPages > 5) {
                        if (i !== 1 && i !== totalPages && Math.abs(i - currentPage) > 1) {
                            if (i === 2 || i === totalPages - 1) {
                                const dotsLi = document.createElement('li');
                                dotsLi.className = 'page-item disabled';
                                dotsLi.innerHTML = '<span class="page-link" style="border: none; padding: 4px 8px; font-size: 0.75rem;">...</span>';
                                ul.appendChild(dotsLi);
                            }
                            continue;
                        }
                    }
                    
                    const li = document.createElement('li');
                    li.className = `page-item ${currentPage === i ? 'active' : ''}`;
                    let activeStyles = currentPage === i ? 'background-color: #D9251C; border-color: #D9251C; color: #fff;' : '';
                    li.innerHTML = `<a class="page-link" href="#" style="border-radius: 4px; margin-right: 4px; padding: 4px 8px; font-size: 0.75rem; ${activeStyles}">${i}</a>`;
                    li.querySelector('a').onclick = (e) => {
                        e.preventDefault();
                        showPage(i);
                    };
                    ul.appendChild(li);
                }
                
                // Next
                const nextLi = document.createElement('li');
                nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
                nextLi.innerHTML = `<a class="page-link" href="#" style="border-radius: 4px; padding: 4px 8px; font-size: 0.75rem;">&raquo;</a>`;
                nextLi.querySelector('a').onclick = (e) => {
                    e.preventDefault();
                    if (currentPage < totalPages) showPage(currentPage + 1);
                };
                ul.appendChild(nextLi);
            }
            
            showPage(1);
        }

        // Dynamically instantiate a modal from JavaScript to display custom details
        function showSalesLedgerModal(title, contentHtml) {
            document.getElementById('salesLedgerModalLabel').innerText = title;
            const body = document.getElementById('salesLedgerModalBody');
            body.innerHTML = contentHtml;

            // Initialize pagination on any table inside the newly loaded content
            const tables = body.querySelectorAll('table');
            tables.forEach(table => {
                initTablePagination(table, 5);
            });

            const modal = new bootstrap.Modal(document.getElementById('salesLedgerModal'));
            modal.show();
        }
    </script>
    @endpush
</x-app-layout>

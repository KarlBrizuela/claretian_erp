<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        /* Select2 POS Theme Fixes */
        .select2-container--default .select2-selection--single {
            height: 48px;
            border: 2px solid #eef0f2;
            border-radius: 10px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        .select2-container--default .select2-selection--single:focus,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #ff0000;
            outline: none;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 48px;
            padding-left: 15px;
            font-weight: 600;
            color: #333;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px;
        }
        .select2-dropdown {
            border: 2px solid #ff0000;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .select2-search--dropdown {
            padding: 10px;
        }
        .select2-search--dropdown .select2-search__field {
            border: 1px solid #eef0f2;
            border-radius: 5px;
            padding: 8px;
        }
        .select2-results__option--highlighted[aria-selected] {
            background-color: #ff0000;
        }
        .pos-container { display: flex; gap: 1rem; height: auto; min-height: calc(100vh - 200px); align-items: flex-start; }
        .pos-products-panel { flex: 1; display: flex; flex-direction: column; background: #fff; border-radius: 10px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        
        /* Category Tabs - Retained and styled */
        .pos-category-tabs { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; border-bottom: 2px solid #e0e0e0; }
        .pos-category-tab { padding: 0.75rem 1.5rem; background: transparent; border: none; border-bottom: 3px solid transparent; cursor: pointer; font-size: 15px; font-weight: 600; color: #666; transition: all 0.3s ease; }
        .pos-category-tab:hover { color: #ff0000; background: #fff5f5; border-radius: 6px 6px 0 0; }
        .pos-category-tab.active { color: #ff0000; border-bottom-color: #ff0000; }

        .pos-product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1.5rem; overflow-y: auto; padding: 0.5rem; }
        
        .pos-product-card { 
            background: #fff; 
            border: 2px solid #eef0f2; 
            border-radius: 12px; 
            padding: 1.25rem; 
            cursor: pointer; 
            text-align: center; 
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            position: relative;
        }
        .pos-product-card:hover { 
            border-color: #ff0000; 
            box-shadow: 0 8px 25px rgba(255, 0, 0, 0.15); 
            transform: translateY(-5px); 
        }
        .pos-product-card img { 
            width: 100%; 
            height: 120px; 
            object-fit: cover; 
            border-radius: 8px; 
            margin-bottom: 1rem; 
            background: #f8f9fa; /* Fallback bg */
        }
        .pos-product-card h6 { 
            font-size: 14px; 
            font-weight: 600; 
            margin: 0.5rem 0; 
            color: #333;
            height: 40px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .pos-product-card .price { 
            font-size: 18px; 
            font-weight: 700; 
            color: #ff0000; 
            margin-top: 0.5rem;
        }

        .pos-cart-panel { width: 450px; background: #f8f9fa; border-radius: 12px; padding: 1.5rem; border: 1px solid #dee2e6; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; flex-direction: column; }
        .pos-form-group { margin-bottom: 1.25rem; }
        .pos-form-group label { font-weight: 600; color: #333; margin-bottom: 0.5rem; display: block; }
        .pos-cart-items { flex: 1; overflow-y: auto; margin-bottom: 1.5rem; min-height: 200px; max-height: 500px; }
        
        .cart-item-card {
            background: #fff;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            border: 1px solid #eef0f2;
            transition: all 0.2s ease;
        }
        .cart-item-card:hover { border-color: #ff0000; }
        
        .pos-total-section {
            background: #fff;
            border-radius: 10px;
            padding: 1.5rem;
            border: 2px solid #dee2e6;
            margin-bottom: 1.5rem;
        }

        .pos-payment-btn-primary { background: #ff0000; color: white; border: none; padding: 18px; border-radius: 10px; font-weight: bold; width: 100%; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(255,0,0,0.3); }
        .pos-payment-btn-primary:hover { background: #cc0000; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(255,0,0,0.4); }

        /* Checkout Modal Styles */
        .checkout-summary { background: #f8f9fa; padding: 1rem; border-radius: 10px; margin-bottom: 1rem; border: 1px solid #eef0f2; }
        .checkout-summary-row { display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #eef0f2; font-size: 0.9rem; }
        .checkout-summary-row:last-child { border-bottom: none; font-size: 1.1rem; font-weight: 800; color: #ff0000; padding-top: 0.5rem; }
        
        .payment-method-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; margin-bottom: 1rem; }
        .payment-method-card { 
            padding: 1rem; border: 2px solid #eef0f2; border-radius: 10px; cursor: pointer; text-align: center; transition: all 0.3s ease; background: #fff;
        }
        .payment-method-card:hover { border-color: #ff0000; transform: translateY(-2px); box-shadow: 0 3px 10px rgba(255,0,0,0.1); }
        .payment-method-card.active { border-color: #ff0000; background: #fff5f5; }
        .payment-method-card i { font-size: 1.75rem; color: #ff0000; margin-bottom: 0.25rem; display: block; }
        .payment-method-card span { font-weight: 700; color: #333; font-size: 0.85rem; }
        
        .payment-details-section { 
            background: #fff; border-radius: 10px; padding: 1rem; border: 2px solid #ff0000; margin-top: 0.5rem; animation: slideDown 0.3s ease-out;
        }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        
        #cashChange { font-size: 1.1rem; font-weight: 600; margin-top: 0.25rem; display: block; color: #333; }
        #cashChange .amount { font-weight: 800; }
        
        .barcode-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            z-index: 9999;
            animation: slideInRight 0.3s ease-out;
            border-left: 4px solid #28a745;
        }
        .barcode-notification.error {
            border-left-color: #dc3545;
        }
        .barcode-notification.warning {
            border-left-color: #ffc107;
        }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        /* Hidden Barcode Scanner Input - Functionality only, no UI */
        #barcodeScanner {
            position: absolute;
            left: -9999px;
            top: -9999px;
            width: 1px;
            height: 1px;
            opacity: 0;
            border: none;
            padding: 0;
        }
    </style>
    @endpush

    <!-- Hidden Barcode Scanner Input -->
    <input type="text" id="barcodeScanner" autofocus placeholder="Barcode Scanner Ready...">

    <div class="pos-container">

        <!-- Left Panel: Product Selection -->
        <div class="pos-products-panel">
            <div class="pos-category-tabs">
                <button class="pos-category-tab active" onclick="switchCategory('books')">Books</button>
                <button class="pos-category-tab" onclick="switchCategory('non-books')">Non-Books</button>
            </div>
            <div class="mb-4">
                <input type="text" class="form-control form-control-lg" id="productSearch" placeholder="Search products..." onkeyup="filterProducts()">
            </div>
            <div class="pos-product-grid" id="productGrid">
                <!-- Products will be loaded dynamically -->
            </div>
        </div>

        <!-- Right Panel: Cart -->
        <div class="pos-cart-panel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Current Order</h4>
                <button class="btn btn-sm btn-outline-danger" onclick="clearCart()">Clear</button>
            </div>

            <div class="pos-form-group">
                <label>Customer *</label>
                <select id="customerSelect" class="form-control">
                    <option value="">Walking Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->customer_id }}">{{ $customer->customer_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="pos-cart-items" id="cartItems">
                <div class="text-center text-muted p-5">
                    <i class="las la-shopping-cart" style="font-size: 4rem; opacity: 0.2;"></i>
                    <p class="mt-2">Cart is empty</p>
                </div>
            </div>

            <div class="pos-total-section">
                <div class="d-flex justify-content-between mb-2 text-muted"><span>Subtotal</span><span id="subtotal">₱0.00</span></div>
                <div class="d-flex justify-content-between mt-3 pt-3 border-top"><h4 class="mb-0">Total</h4><h4 id="total" class="text-primary mb-0">₱0.00</h4></div>
            </div>
            
            <button class="pos-payment-btn-primary" onclick="openCheckoutModal()">
                <i class="las la-cash-register me-2"></i>CHECKOUT
            </button>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div class="modal fade" id="checkoutModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-2">
                    <h6 class="modal-title m-0 text-white"><i class="las la-wallet me-2"></i>Payment Details</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="checkout-summary">
                        <div class="checkout-summary-row"><span>Subtotal</span><span id="modalSubtotal">₱0.00</span></div>
                        <div class="checkout-summary-row"><span>Total Payable</span><span id="modalTotal">₱0.00</span></div>
                    </div>

                    <h6 class="mb-2 font-w700 mt-2" style="font-size: 0.85rem;">Select Payment Method</h6>
                    <div class="payment-method-grid" id="paymentMethodGrid">
                        <!-- Payment methods will be loaded dynamically -->
                    </div>

                    <div id="methodDetails">
                        <!-- Cash Details -->
                        <div id="cashDetails" class="payment-details-section">
                            <label class="form-label font-w600">Cash Received</label>
                            <input type="number" class="form-control form-control-lg" id="cashReceived" placeholder="Enter amount..." min="0" step="0.01" oninput="calculateChange()" autofocus>
                            <span id="cashChange" class="text-muted mt-2">Change: ₱0.00</span>
                        </div>
                        <!-- Digital Payment Details (Hidden) -->
                        <div id="refDetails" class="payment-details-section" style="display:none;">
                            <div id="paymentNumberDisplay" class="mb-3" style="display:none;">
                                <p class="mb-1"><strong id="paymentNumberLabel">Number:</strong> <span id="paymentNumberValue"></span></p>
                            </div>
                            <div id="qrCodeContainer" class="text-center mb-3" style="display:none;">
                                <img id="qrCodeImage" src="" alt="QR Code" style="max-width: 200px; border-radius: 10px;">
                            </div>
                            <div id="accountDetails" class="mb-3" style="display:none;">
                                <p class="mb-1"><strong>Bank:</strong> <span id="bankName"></span></p>
                                <p class="mb-1"><strong>Account Name:</strong> <span id="accountName"></span></p>
                                <p class="mb-1"><strong>Account Number:</strong> <span id="accountNumber"></span></p>
                            </div>
                            <label class="form-label font-w600" id="refLabel">Reference Number</label>
                            <input type="text" class="form-control form-control-lg" id="refNumber" placeholder="Enter Reference #">
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-sm px-4 font-w700" onclick="processCheckout()">PROCESS PAYMENT</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Customer Registration Modal -->
    <div class="modal fade" id="quickCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h6 class="modal-title m-0 text-white"><i class="las la-user-plus me-2"></i>Quick Customer Registration</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-danger small mb-3"><strong>Notice:</strong> Walking customers are not allowed. Please register the customer details before proceeding to checkout.</p>
                    <form id="quickCustomerForm">
                        <div class="mb-3">
                            <label class="form-label font-w600">Customer Name *</label>
                            <input type="text" class="form-control" name="customer_name" required placeholder="Full Name">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-w600">Mobile Number</label>
                                <input type="tel" class="form-control" name="mobile" placeholder="09xxxxxxxxx">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-w600">Email Address</label>
                                <input type="email" class="form-control" name="main_email" placeholder="email@example.com">
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label font-w600">Billing Address</label>
                            <textarea class="form-control" name="billing_address" rows="2" placeholder="Full Address"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm px-4" id="saveQuickCustomerBtn">REGISTER & CHECKOUT</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Validation Errors Modal -->
    <div class="modal fade" id="validationErrorsModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h6 class="modal-title m-0 text-white"><i class="fas fa-exclamation-triangle me-2"></i>Registration Errors</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="fw-bold">Please correct the following errors:</p>
                    <ul id="modalErrorList" class="text-danger mb-0"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Fix Errors</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script>
        const products = @json($products);

        let cart = [];
        let currentCategory = 'books';
        let subtotalAmt = 0, taxAmt = 0, totalAmt = 0;
        let selectedPaymentMethod = 'cash';
        let paymentSettings = {};
        let taxRate = 0.12; // Default 12%

        // Show barcode notification
        function showBarcodeNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `barcode-notification ${type}`;
            notification.innerHTML = `<i class="las la-barcode" style="margin-right: 0.5rem;"></i><span>${message}</span>`;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        function renderProducts() {
            const grid = document.getElementById('productGrid');
            const search = document.getElementById('productSearch').value.toLowerCase();
            const filtered = products.filter(p => 
                p.category === currentCategory && 
                (p.name.toLowerCase().includes(search) ||
                 (p.barcode && p.barcode.toLowerCase().includes(search)) ||
                 (p.sku && p.sku.toLowerCase().includes(search)))
            );
            
            grid.innerHTML = filtered.map(p => `
                <div class="pos-product-card" onclick="addToCart(${p.id})">
                    <img src="${p.image}" alt="${p.name}">
                    <h6>${p.name}</h6>
                    <div class="price">₱${p.price.toLocaleString(undefined, {minimumFractionDigits: 2})}</div>
                </div>
            `).join('');
        }

        function switchCategory(category) {
            currentCategory = category;
            
            // Update UI tabs
            document.querySelectorAll('.pos-category-tab').forEach(tab => {
                tab.classList.remove('active');
                if (tab.innerText.toLowerCase().includes(category.replace('-', ''))) {
                    tab.classList.add('active');
                }
            });
            
            renderProducts();
        }

        function addToCart(id) {
            console.log('📥 addToCart called with ID:', id);
            const product = products.find(p => p.id === id);
            console.log('🔎 Found product:', product);
            
            const existing = cart.find(item => item.id === id);
            console.log('🔄 Existing item in cart:', existing);
            
            if (existing) {
                existing.qty++;
                console.log('📈 Increased quantity to:', existing.qty);
            } else {
                cart.push({ ...product, qty: 1 });
                console.log('🆕 Added new item to cart');
            }
            console.log('🛒 Cart contents:', cart);
            renderCart();
        }

        function renderCart() {
            console.log('🎨 renderCart called, cart items:', cart.length);
            const container = document.getElementById('cartItems');
            if (cart.length === 0) {
                console.log('📭 Cart is empty');
                container.innerHTML = `
                    <div class="text-center text-muted p-5">
                        <i class="las la-shopping-cart" style="font-size: 4rem; opacity: 0.2;"></i>
                        <p class="mt-2">Cart is empty</p>
                    </div>`;
            } else {
                console.log('📝 Rendering cart items...');
                container.innerHTML = cart.map((item, index) => `
                    <div class="cart-item-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1" style="font-size: 0.9rem;">${item.name}</h6>
                                <div class="text-primary font-w600">₱${item.price.toLocaleString(undefined, {minimumFractionDigits: 2})}</div>
                            </div>
                            <div class="d-flex flex-column align-items-end">
                                <button class="btn btn-xs btn-outline-danger mb-2" onclick="removeItem(${index})">&times;</button>
                                <div class="input-group input-group-sm" style="width: 120px;">
                                    <button class="btn btn-outline-secondary" type="button" onclick="updateQty(${index}, -1)">-</button>
                                    <input type="number" class="form-control text-center px-0 qty-input" value="${item.qty}" min="1" oninput="updateQtyDirect(${index}, this.value)" autofocus>
                                    <button class="btn btn-outline-secondary" type="button" onclick="updateQty(${index}, 1)">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
                console.log('✅ Cart rendered successfully');
            }
            calculateTotals();
        }

        function updateQty(index, change) {
            if (cart[index].qty + change > 0) {
                cart[index].qty += change;
            } else {
                cart.splice(index, 1);
            }
            renderCart();
        }

        function updateQtyDirect(index, value) {
            const qty = parseInt(value);
            if (qty && qty > 0) {
                cart[index].qty = qty;
                calculateTotals();
            }
        }

        function removeItem(index) {
            cart.splice(index, 1);
            renderCart();
        }

        function clearCart() {
            cart = [];
            renderCart();
        }

        function openCheckoutModal() {
            if (cart.length === 0) return window.showAlert('Cart is empty!', 'error');
            
            // Check for Walking Customer
            const customerId = document.getElementById('customerSelect').value;
            if (!customerId) {
                const quickModal = new bootstrap.Modal(document.getElementById('quickCustomerModal'));
                quickModal.show();
                return;
            }

            document.getElementById('modalSubtotal').textContent = `₱${subtotalAmt.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
            document.getElementById('modalTotal').textContent = `₱${totalAmt.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
            
            // Reset modal state
            document.getElementById('cashReceived').value = '';
            document.getElementById('cashChange').textContent = 'Change: ₱0.00';
            document.getElementById('refNumber').value = '';
            
            new bootstrap.Modal(document.getElementById('checkoutModal')).show();
            
            // Focus on cash input after modal is shown
            setTimeout(() => {
                document.getElementById('cashReceived').focus();
            }, 300);
        }

        function selectMethod(card, method) {
            document.querySelectorAll('.payment-method-card').forEach(c => c.classList.remove('active'));
            card.classList.add('active');
            selectedPaymentMethod = method;
            
            const cashDetails = document.getElementById('cashDetails');
            const refDetails = document.getElementById('refDetails');
            const refLabel = document.getElementById('refLabel');
            const qrCodeContainer = document.getElementById('qrCodeContainer');
            const qrCodeImage = document.getElementById('qrCodeImage');
            const accountDetails = document.getElementById('accountDetails');
            const paymentNumberDisplay = document.getElementById('paymentNumberDisplay');
            const paymentNumberLabel = document.getElementById('paymentNumberLabel');
            const paymentNumberValue = document.getElementById('paymentNumberValue');
            
            if (method === 'cash') {
                cashDetails.style.display = 'block';
                refDetails.style.display = 'none';
                // Focus on cash input
                setTimeout(() => document.getElementById('cashReceived').focus(), 100);
            } else {
                cashDetails.style.display = 'none';
                refDetails.style.display = 'block';
                // Focus on reference input
                setTimeout(() => document.getElementById('refNumber').focus(), 100);
                
                // Hide all details first
                qrCodeContainer.style.display = 'none';
                accountDetails.style.display = 'none';
                paymentNumberDisplay.style.display = 'none';
                
                // Show payment number and QR code if available
                if (method === 'gcash') {
                    if (paymentSettings.gcash?.number) {
                        paymentNumberLabel.textContent = 'GCash Number:';
                        paymentNumberValue.textContent = paymentSettings.gcash.number;
                        paymentNumberDisplay.style.display = 'block';
                    }
                    if (paymentSettings.gcash?.qr) {
                        qrCodeImage.src = paymentSettings.gcash.qr;
                        qrCodeContainer.style.display = 'block';
                    }
                    refLabel.textContent = 'GCash Reference Number';
                } else if (method === 'paymaya') {
                    if (paymentSettings.paymaya?.number) {
                        paymentNumberLabel.textContent = 'PayMaya Number:';
                        paymentNumberValue.textContent = paymentSettings.paymaya.number;
                        paymentNumberDisplay.style.display = 'block';
                    }
                    if (paymentSettings.paymaya?.qr) {
                        qrCodeImage.src = paymentSettings.paymaya.qr;
                        qrCodeContainer.style.display = 'block';
                    }
                    refLabel.textContent = 'PayMaya Reference Number';
                } else if (method === 'bank') {
                    if (paymentSettings.bank?.qr) {
                        qrCodeImage.src = paymentSettings.bank.qr;
                        qrCodeContainer.style.display = 'block';
                    }
                    if (paymentSettings.bank?.name) {
                        document.getElementById('bankName').textContent = paymentSettings.bank.name;
                        document.getElementById('accountName').textContent = paymentSettings.bank.accountName || '';
                        document.getElementById('accountNumber').textContent = paymentSettings.bank.accountNumber || '';
                        accountDetails.style.display = 'block';
                    }
                    refLabel.textContent = 'Bank Transfer Reference Number';
                } else if (method === 'card') {
                    refLabel.textContent = 'Card Reference Number';
                } else if (method === 'check') {
                    refLabel.textContent = 'Check Number';
                }
            }
        }

        function calculateChange() {
            const received = parseFloat(document.getElementById('cashReceived').value) || 0;
            const change = Math.max(0, received - totalAmt);
            const changeEl = document.getElementById('cashChange');
            
            if (received < totalAmt) {
                changeEl.innerHTML = `Shortage: <span class="amount text-danger">₱${(totalAmt - received).toLocaleString(undefined, {minimumFractionDigits: 2})}</span>`;
            } else {
                changeEl.innerHTML = `Change: <span class="amount text-muted">₱${change.toLocaleString(undefined, {minimumFractionDigits: 2})}</span>`;
            }
        }

        function processCheckout() {
            if (cart.length === 0) {
                return window.showAlert('Cart is empty!', 'error');
            }

            // Validate payment details
            let cashReceived = null;
            let paymentReference = null;
            
            if (selectedPaymentMethod === 'cash') {
                cashReceived = parseFloat(document.getElementById('cashReceived').value) || 0;
                if (cashReceived < totalAmt) {
                    return window.showAlert('Insufficient cash received!', 'error');
                }
            } else {
                paymentReference = document.getElementById('refNumber').value.trim();
                if (!paymentReference) {
                    return window.showAlert('Please enter reference number!', 'error');
                }
            }

            // Prepare order data
            const orderData = {
                customer_id: document.getElementById('customerSelect').value || null,
                payment_method: selectedPaymentMethod,
                payment_reference: paymentReference,
                cash_received: cashReceived,
                items: cart.map(item => ({
                    product_id: item.id,
                    quantity: item.qty,
                    price: item.price
                })),
                subtotal: subtotalAmt,
                tax: taxAmt,
                total: totalAmt
            };

            // Send to backend
            fetch('{{ route('marketing.pos.process-order') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(orderData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.showAlert(`Order ${data.order.order_number} processed successfully!`, 'success');
                    bootstrap.Modal.getInstance(document.getElementById('checkoutModal')).hide();
                    cart = [];
                    renderCart();
                    // Reset form
                    document.getElementById('cashReceived').value = '';
                    document.getElementById('refNumber').value = '';
                    document.getElementById('cashChange').textContent = 'Change: ₱0.00';
                } else {
                    window.showAlert('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.showAlert('Failed to process order. Please try again.', 'error');
            });
        }

        function calculateTotals() {
            subtotalAmt = cart.reduce((sum, item) => sum + (item.qty * item.price), 0);
            taxAmt = 0;
            totalAmt = subtotalAmt;
            document.getElementById('subtotal').textContent = `₱${subtotalAmt.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
            document.getElementById('total').textContent = `₱${totalAmt.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
        }

        // Load payment settings from database
        function loadPaymentSettings() {
            fetch('{{ route('marketing.pos.payment-settings') }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.settings) {
                        paymentSettings = data.settings;
                        
                        // Update tax rate if configured
                        if (paymentSettings.posConfig || paymentSettings.pos_config) {
                            const posConfig = paymentSettings.posConfig || paymentSettings.pos_config;
                            taxRate = (posConfig.taxRate || 12) / 100;
                        }
                        
                        // Render payment methods
                        renderPaymentMethods();
                    }
                })
                .catch(error => {
                    console.error('Error loading payment settings:', error);
                    // Fallback to default payment methods
                    renderPaymentMethods();
                });
        }

        // Render payment method cards
        function renderPaymentMethods() {
            const grid = document.getElementById('paymentMethodGrid');
            let methods = [
                { id: 'cash', icon: 'la-money-bill-wave', label: 'Cash', available: true }
            ];
            
            // Add digital payment methods if configured
            if (paymentSettings.gcash?.number || paymentSettings.gcash?.qr) {
                methods.push({ id: 'gcash', icon: 'la-mobile-alt', label: 'GCash', available: true });
            }
            if (paymentSettings.paymaya?.number || paymentSettings.paymaya?.qr) {
                methods.push({ id: 'paymaya', icon: 'la-mobile-alt', label: 'PayMaya', available: true });
            }
            if (paymentSettings.bank?.name) {
                methods.push({ id: 'bank', icon: 'la-university', label: 'Bank Transfer', available: true });
            }
            
            // Always show card and check options
            methods.push({ id: 'card', icon: 'la-credit-card', label: 'Card', available: true });
            methods.push({ id: 'check', icon: 'la-money-check', label: 'Check', available: true });
            
            grid.innerHTML = methods.map((method, index) => `
                <div class="payment-method-card ${index === 0 ? 'active' : ''}" onclick="selectMethod(this, '${method.id}')">
                    <i class="las ${method.icon}"></i>
                    <span>${method.label}</span>
                </div>
            `).join('');
        }

        function filterProducts() {
            renderProducts();
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadPaymentSettings();
            renderProducts();

            // Initialize Select2
            $('#customerSelect').select2({
                placeholder: "Search for a customer...",
                allowClear: true,
                width: '100%'
            });

            // Barcode Scanner Handler - Multiple event types for compatibility
            const barcodeInput = document.getElementById('barcodeScanner');
            let processingBarcode = false;

            // Method 1: Monitor the input field directly
            barcodeInput.addEventListener('input', function(e) {
                const value = barcodeInput.value.trim();
                console.log('📥 Input detected:', value);
            });

            // Method 2: Keydown event (more reliable than keypress)
            barcodeInput.addEventListener('keydown', function(e) {
                console.log('⌨️ Key pressed:', e.key, 'Code:', e.code);
                
                if (e.key === 'Enter' && barcodeInput.value.trim().length > 0) {
                    e.preventDefault();
                    processBarcode(barcodeInput.value.trim());
                }
            });

            // Method 3: Watch for rapid input (typical barcode scanner behavior)
            let lastInputTime = 0;
            let inputBuffer = '';
            
            barcodeInput.addEventListener('keypress', function(e) {
                const now = Date.now();
                
                // If more than 100ms since last keystroke, it's a new scan
                if (now - lastInputTime > 100) {
                    inputBuffer = '';
                }
                
                lastInputTime = now;
                inputBuffer += e.key;
                
                // If we detect a long barcode (typical scanner has 12+ characters)
                if (inputBuffer.length > 10 && e.key === 'Enter') {
                    processBarcode(inputBuffer.replace('Enter', '').trim());
                }
            });

            function processBarcode(barcode) {
                if (processingBarcode) return;
                processingBarcode = true;
                
                console.log('🔍 Processing barcode:', barcode);
                
                if (barcode && barcode.length > 3) {
                    let product = null;
                    
                    console.log('📊 Searching in', products.length, 'products...');
                    
                    for (let i = 0; i < products.length; i++) {
                        const p = products[i];
                        
                        if ((p.barcode && p.barcode.toString() === barcode) || 
                            (p.sku && p.sku.toString() === barcode)) {
                            product = p;
                            console.log('✅ MATCH FOUND:', p.name);
                            break;
                        }
                    }
                    
                    if (product) {
                        console.log('🛒 Adding product ID:', product.id, 'Name:', product.name);
                        addToCart(product.id);
                        showBarcodeNotification(`✓ ${product.name} added to cart!`, 'success');
                    } else {
                        console.log('❌ No product found for barcode:', barcode);
                        showBarcodeNotification('⚠ Product not found', 'error');
                    }
                }
                
                // Clear input
                barcodeInput.value = '';
                inputBuffer = '';
                barcodeInput.focus();
                processingBarcode = false;
            }

            // Keep barcode input focused - but not when modal is open or typing in inputs
            barcodeInput.focus();
            document.addEventListener('click', function(e) {
                // Don't steal focus if clicking on an input field or if modal is shown
                const checkoutModal = document.getElementById('checkoutModal');
                const isModalOpen = checkoutModal && checkoutModal.classList.contains('show');
                const clickedElement = e.target;
                const isInputField = clickedElement.tagName === 'INPUT' || clickedElement.tagName === 'SELECT' || clickedElement.tagName === 'TEXTAREA';
                
                if (!processingBarcode && !isModalOpen && !isInputField) {
                    barcodeInput.focus();
                }
            });
            
            // Log initial product load
            console.log('🎉 POS Products loaded:', products.length, 'items');
            console.log('📊 First product details:', products[0]);
            products.forEach((p, idx) => {
                console.log(`Product ${idx}:`, {
                    id: p.id,
                    name: p.name,
                    barcode: p.barcode,
                    sku: p.sku,
                    price: p.price,
                    category: p.category
                });
            });

            // Quick Customer Registration
            const quickCustForm = document.getElementById('quickCustomerForm');
            const saveQuickBtn = document.getElementById('saveQuickCustomerBtn');
            const quickModalEl = document.getElementById('quickCustomerModal');
            const errorModal = new bootstrap.Modal(document.getElementById('validationErrorsModal'));

            saveQuickBtn?.addEventListener('click', async function() {
                saveQuickBtn.disabled = true;
                saveQuickBtn.textContent = 'Registering...';

                const formData = new FormData(quickCustForm);
                const data = {};
                formData.forEach((value, key) => data[key] = value);

                try {
                    const response = await fetch('/marketing/customers', {
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
                        // Success - result.customer should contain the new customer object
                        // Note: CustomerController@store currently returns ['message' => '...'] 
                        // I should verify if it returns the ID or just a message.
                        // Looking at CustomerController.php:87-89, it returns only a message.
                        // I might need to update the controller to return the customer data for convenience.
                        
                        // Let's re-fetch customers or handle the newly created one. 
                        // For now, let's assume we can at least refresh the list or wait for the user to select.
                        // Actually, I'll update the controller to return the customer object.
                        
                        window.showAlert('Customer registered successfully!', 'success');
                        
                        // Close quick modal
                        bootstrap.Modal.getInstance(quickModalEl).hide();
                        
                        // If we have the customer data, select it. 
                        if (result.customer) {
                            const $select = $('#customerSelect');
                            const newOption = new Option(result.customer.customer_name, result.customer.customer_id, true, true);
                            $select.append(newOption).trigger('change');
                        } else {
                            // Fallback: reload page or manually fetch latest
                            location.reload(); 
                        }

                        // Proceed to checkout modal
                        setTimeout(() => openCheckoutModal(), 500);

                    } else if (response.status === 422) {
                        // Validation errors
                        const errorList = document.getElementById('modalErrorList');
                        errorList.innerHTML = '';
                        Object.values(result.errors).flat().forEach(err => {
                            const li = document.createElement('li');
                            li.textContent = err;
                            errorList.appendChild(li);
                        });
                        errorModal.show();
                    } else {
                        window.showAlert(result.message || 'Error occurred', 'error');
                    }
                } catch (err) {
                    console.error(err);
                    window.showAlert('Failed to connect to server', 'error');
                } finally {
                    saveQuickBtn.disabled = false;
                    saveQuickBtn.textContent = 'REGISTER & CHECKOUT';
                }
            });
        });
    </script>
    @endpush
</x-app-layout>

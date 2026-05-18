<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .pos-container { display: flex; gap: 1rem; height: auto; min-height: calc(100vh - 200px); align-items: flex-start; }
        .pos-products-panel { flex: 1; display: flex; flex-direction: column; background: #fff; border-radius: 10px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
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

        .pos-cart-panel { width: 450px; background: #f8f9fa; border-radius: 12px; padding: 1.5rem; border: 1px solid #dee2e6; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .pos-form-group { margin-bottom: 1.25rem; }
        .pos-form-group label { font-weight: 600; color: #333; margin-bottom: 0.5rem; display: block; }
        .pos-cart-items { flex: 1; overflow-y: auto; margin-bottom: 1.5rem; min-height: 200px; max-height: 400px; }
        
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
    </style>
    @endpush

    <div class="pos-container">
        <!-- Left Panel: Product Selection -->
        <div class="pos-products-panel">
            <div class="mb-4">
                <input type="text" class="form-control form-control-lg" placeholder="Search products..." id="productSearch" onkeyup="filterProducts()">
            </div>
            <div class="pos-product-grid" id="productGrid">
                <!-- Products will be loaded dynamically -->
            </div>
        </div>

        <!-- Right Panel: Cart & Checkout -->
        <div class="pos-cart-panel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Online Order Cart</h4>
                <button class="btn btn-sm btn-outline-danger" onclick="clearCart()">Clear</button>
            </div>
            
            <div class="pos-form-group">
                <label>Platform *</label>
                <select class="form-control" id="platformSelect">
                    <option value="lazada">Lazada</option>
                    <option value="shopee">Shopee</option>
                    <option value="tiktok">TikTok</option>
                    <option value="website">Website</option>
                    <option value="facebook">Facebook</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="pos-form-group">
                <label>Customer *</label>
                <select class="form-control" id="customerSelect">
                    <option value="">Select Customer</option>
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
                <div class="d-flex justify-content-between mb-2 text-muted"><span>Tax (12%)</span><span id="tax">₱0.00</span></div>
                <div class="d-flex justify-content-between mt-3 pt-3 border-top"><h4 class="mb-0">Total</h4><h4 id="total" class="text-primary mb-0">₱0.00</h4></div>
            </div>

            <button class="pos-payment-btn-primary" onclick="openCheckoutModal()">
                PROCESS ORDER
            </button>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div class="modal fade" id="checkoutModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-2">
                    <h6 class="modal-title m-0 text-white"><i class="las la-shopping-basket me-2"></i>Payment Details</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="checkout-summary">
                        <div class="checkout-summary-row"><span>Subtotal</span><span id="modalSubtotal">₱0.00</span></div>
                        <div class="checkout-summary-row"><span>VAT (12%)</span><span id="modalTax">₱0.00</span></div>
                        <div class="checkout-summary-row"><span>Grand Total</span><span id="modalTotal">₱0.00</span></div>
                    </div>

                    <h6 class="mb-2 font-w700 mt-2" style="font-size: 0.85rem;">Payment Channel</h6>
                    <div class="payment-method-grid">
                        <div class="payment-method-card active" onclick="selectMethod(this, 'cod')">
                            <i class="las la-truck"></i>
                            <span>COD</span>
                        </div>
                        <div class="payment-method-card" onclick="selectMethod(this, 'gcash')">
                            <i class="las la-mobile-alt"></i>
                            <span>GCash</span>
                        </div>
                        <div class="payment-method-card" onclick="selectMethod(this, 'lazada')">
                            <i class="lab la-lazada"></i>
                            <span>Lazada Pay</span>
                        </div>
                        <div class="payment-method-card" onclick="selectMethod(this, 'shopee')">
                            <i class="lab la-shopeepay"></i>
                            <span>ShopeePay</span>
                        </div>
                         <div class="payment-method-card" onclick="selectMethod(this, 'paymaya')">
                            <i class="las la-wallet"></i>
                            <span>PayMaya</span>
                        </div>
                        <div class="payment-method-card" onclick="selectMethod(this, 'bank')">
                            <i class="las la-university"></i>
                            <span>Bank Transfer</span>
                        </div>
                        <div class="payment-method-card" onclick="selectMethod(this, 'check')">
                            <i class="las la-money-check"></i>
                            <span>Check</span>
                        </div>
                    </div>

                    <div id="methodDetails">
                        <div id="refDetails" class="payment-details-section">
                            <label class="form-label font-w600" id="refLabel">Order Notes</label>
                            <input type="text" class="form-control form-control-lg" id="refNumber" placeholder="Notes (Optional)">
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-sm px-4 font-w700" onclick="confirmOrder()">PLACE ORDER</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white"><i class="las la-check-circle me-2"></i>Success</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <h4 class="mb-3">Order Placed Successfully!</h4>
                    <p class="mb-2">Order Number: <span id="successOrderNumber" class="font-weight-bold text-primary"></span></p>
                    <p class="text-muted">Payment Status: <span id="successPaymentStatus"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const products = @json($products);
        let cart = [];

        function renderProducts() {
            const grid = document.getElementById('productGrid');
            const searchTerm = document.getElementById('productSearch').value.toLowerCase();
            
            const filtered = products.filter(p => 
                p.name.toLowerCase().includes(searchTerm) || 
                (p.category && p.category.toLowerCase().includes(searchTerm))
            );
            
            grid.innerHTML = filtered.map(p => {
                // Badges removed as per request
                return `
                <div class="pos-product-card" onclick="addToCart(${p.id})">
                    <img src="${p.image}" alt="${p.name}">
                    <h6>${p.name}</h6>
                    <div class="price">₱${p.price.toLocaleString(undefined, {minimumFractionDigits: 2})}</div>
                </div>
            `}).join('');
        }
        
        function filterProducts() {
            renderProducts();
        }

        function addToCart(id) {
            const product = products.find(p => p.id === id);
            const existing = cart.find(item => item.id === id);
            
            if (existing) {
                existing.qty++;
            } else {
                cart.push({ ...product, qty: 1 });
            }
            renderCart();
        }

        let currentSubtotal = 0;
        let currentTax = 0;
        let currentTotal = 0;
        let selectedMethodName = 'cod';

        function openCheckoutModal() {
            if (cart.length === 0) return alert('Your cart is empty');
            
            const customerId = document.getElementById('customerSelect').value;
             if (!customerId) return alert('Please select a customer');
            
            document.getElementById('modalSubtotal').textContent = `₱${currentSubtotal.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
            document.getElementById('modalTax').textContent = `₱${currentTax.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
            document.getElementById('modalTotal').textContent = `₱${currentTotal.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
            
            // Reset reference/notes field based on current method
            selectMethod(document.querySelector('.payment-method-card.active') || document.querySelector('.payment-method-card'), selectedMethodName);
            
            new bootstrap.Modal(document.getElementById('checkoutModal')).show();
        }

        function selectMethod(el, method) {
            selectedMethodName = method;
            document.querySelectorAll('.payment-method-card').forEach(c => c.classList.remove('active'));
            el.classList.add('active');
            
            const refLabel = document.getElementById('refLabel');
            const refInput = document.getElementById('refNumber');
            
            if (method === 'cod') {
                refLabel.textContent = 'Order Notes (Optional)';
                refInput.placeholder = 'Notes (Optional)';
            } else if (method === 'check') {
                refLabel.textContent = 'Check Number';
                refInput.placeholder = 'Enter Check Number';
            } else {
                refLabel.textContent = method.toUpperCase() + ' Reference #';
                refInput.placeholder = 'Enter Reference Number';
            }
        }

        function confirmOrder() {
            const customerId = document.getElementById('customerSelect').value;
            const platform = document.getElementById('platformSelect').value;
            const refNumber = document.getElementById('refNumber').value;
            
            if (selectedMethodName !== 'cod' && !refNumber) {
                return alert('Please enter a payment reference number');
            }
            
            const orderData = {
                customer_id: customerId,
                platform: platform,
                payment_method: selectedMethodName,
                items: cart.map(item => ({
                    product_id: item.id,
                    quantity: item.qty,
                    price: item.price
                })),
                subtotal: currentSubtotal,
                tax: currentTax,
                total: currentTotal,
                notes: selectedMethodName === 'cod' ? refNumber : null,
                payment_reference: selectedMethodName !== 'cod' ? refNumber : null
            };

            fetch("{{ route('marketing.pos.process-ecom-order') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(orderData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('checkoutModal')).hide();
                    
                    document.getElementById('successOrderNumber').textContent = data.order.order_number;
                    document.getElementById('successPaymentStatus').textContent = data.order.payment_status.toUpperCase();
                    
                    new bootstrap.Modal(document.getElementById('successModal')).show();
                    
                    clearCart();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing the order');
            });
        }

        function renderCart() {
            const container = document.getElementById('cartItems');
            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-muted p-5">
                        <i class="las la-shopping-cart" style="font-size: 4rem; opacity: 0.2;"></i>
                        <p class="mt-2">Cart is empty</p>
                    </div>`;
            } else {
                container.innerHTML = cart.map((item, index) => `
                    <div class="cart-item-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1" style="font-size: 0.9rem;">${item.name}</h6>
                                <div class="text-primary font-w600">₱${item.price.toLocaleString(undefined, {minimumFractionDigits: 2})}</div>
                            </div>
                            <div class="d-flex flex-column align-items-end">
                                <button class="btn btn-xs btn-outline-danger mb-2" onclick="removeItem(${index})">&times;</button>
                                <div class="input-group input-group-sm" style="width: 80px;">
                                    <button class="btn btn-outline-secondary" type="button" onclick="updateQty(${index}, -1)">-</button>
                                    <input type="text" class="form-control text-center px-0" value="${item.qty}" readonly>
                                    <button class="btn btn-outline-secondary" type="button" onclick="updateQty(${index}, 1)">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
            }
            
            updateTotals();
        }
        
        function updateQty(index, change) {
            if (cart[index].qty + change > 0) {
                cart[index].qty += change;
            } else {
                cart.splice(index, 1);
            }
            renderCart();
        }

        function updateTotals() {
            currentSubtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            const taxRate = 0.12; // 12% tax
            
            // Shipping Fee removed
            
            currentTax = currentSubtotal * taxRate;
            currentTotal = currentSubtotal + currentTax;

            document.getElementById('subtotal').textContent = `₱${currentSubtotal.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
            document.getElementById('tax').textContent = `₱${currentTax.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
            document.getElementById('total').textContent = `₱${currentTotal.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
        }

        function removeItem(index) {
            cart.splice(index, 1);
            renderCart();
        }

        function clearCart() {
            cart = [];
            renderCart();
            updateTotals();
        }

        // Initialize grid
        document.addEventListener('DOMContentLoaded', renderProducts);
    </script>
    @endpush
</x-app-layout>

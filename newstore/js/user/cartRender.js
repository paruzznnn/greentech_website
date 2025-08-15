// Object Literal --------------------------------------
export const ShoppingCart = {
    cartItems: [],
    CART_STORAGE_KEY: 'shoppingCart',
    // SHIPPING_COST: 50.00,
    SHIPPING_COST: 0,
    VAT_RATE: 0.07,
    currentDiscount: 0,

    init() {
        this.loadCart();
        this.renderCart();
        this.setupEventListeners();
    },

    loadCart() {
        const storedCart = localStorage.getItem(this.CART_STORAGE_KEY);
        if (storedCart) {
            this.cartItems = JSON.parse(storedCart);
        }
    },

    saveCart() {
        localStorage.setItem(this.CART_STORAGE_KEY, JSON.stringify(this.cartItems));
    },

    setupEventListeners() {
        const cartContent = document.getElementById('cartContent');
        if (cartContent) {
            cartContent.addEventListener('click', (e) => {
                const target = e.target;
                const productId = target.dataset.id;
                const action = target.dataset.action;

                if (target.classList.contains('remove-item-button')) {
                    this.removeItem(productId);
                } else if (action === 'decrease') {
                    this.updateQuantity(productId, -1);
                } else if (action === 'increase') {
                    this.updateQuantity(productId, 1);
                } else if (target.id === 'applyDiscountCodeBtn') {
                    this.applyDiscountCode();
                } else if (target.id === 'applyCouponBtn') {
                    this.applyCoupon();
                } else if (target.classList.contains('checkout-button')) {
                    this.proceedToCheckout();
                }
            });

            cartContent.addEventListener('change', (e) => {
                if (e.target.classList.contains('quantity-input')) {
                    const productId = e.target.dataset.id;
                    const newQuantity = parseInt(e.target.value);
                    const currentItem = this.cartItems.find(item => item.id === productId);
                    if (currentItem) {
                        this.updateQuantity(productId, newQuantity - currentItem.quantity);
                    }
                }
            });
        }
    },

    createCartItemCard(item) {
        const card = document.createElement('div');
        card.className = 'cart-item-card';
        card.innerHTML = `
            <img src="${item.imageUrl}" alt="${item.name}">
            <div class="item-details">
                <div class="item-title">
                    <h3 class="item-name">${item.name}</h3>
                    <span class="remove-item-button" data-id="${item.id}">ลบ</span>
                </div>
                <p class="item-price">฿${item.price}</p>
                <div class="quantity-control">
                    <button type="button" data-id="${item.id}" data-action="decrease">-</button>
                    <input type="number" value="${item.quantity}" min="1" data-id="${item.id}" class="quantity-input">
                    <button type="button" data-id="${item.id}" data-action="increase">+</button>
                </div>
                <div class="item-total-price">
                    ฿${(item.price * item.quantity)}
                </div>
            </div>
        `;
        return card;
    },

    createCoupon() {

    },

    renderCart() {
        const cartContent = document.getElementById('cartContent');
        if (!cartContent) return;

        if (this.cartItems.length === 0) {
            cartContent.innerHTML = '<p class="empty-cart-message">ตะกร้าสินค้าของคุณว่างเปล่า</p>';
        } else {

            // <button type="button" id="applyCouponBtn" class="apply-coupon-button">ใช้คูปอง</button>
            // <div class="input-group">
            //     <input type="text" id="discountCodeInput" placeholder="กรอกรหัสส่วนลด">
            //     <p id="discountMessage" class="discount-message"></p>
            // </div>
            // <button type="button" id="applyDiscountCodeBtn" class="apply-discount-code-button">ใช้รหัส</button>

            cartContent.innerHTML = `
                <div id="cartItemsList" class="cart-items-list"></div>
                <div id="bottomSummaryGrid" class="bottom-summary-grid">
                    <div id="discountCodeCard" class="discount-code-card summary-card">
                        <div>
                            <h2>ใช้คูปอง</h2>
                        </div>
                        <div>
                            <h2>รหัสส่วนลด</h2>
                        </div>
                    </div>
                    <div id="orderSummary" class="summary-card">
                        <h2 class="summary-title">สรุปคำสั่งซื้อ</h2>
                        <div class="summary-row"><span>จำนวนสินค้าทั้งหมด</span><span id="totalItemsCount">0 ชิ้น</span></div>
                        <div class="summary-row"><span>ยอดรวมสินค้า</span><span id="subtotal">฿0.00</span></div>
                        <div class="summary-row"><span>ค่าจัดส่ง</span><span id="shippingCost">฿0.00</span></div>
                        <div class="summary-row"><span>ส่วนลด</span><span id="discountAmountDisplay">฿0.00</span></div>
                        <div class="summary-row"><span>ภาษีมูลค่าเพิ่ม (VAT 7%)</span><span id="vatAmount">฿0.00</span></div>
                        <div class="summary-total"><span>ยอดชำระทั้งหมด</span><span id="totalAmount">฿0.00</span></div>
                        <button type="button" class="checkout-button">ซื้อสินค้า</button>
                    </div>
                </div>`;

            let cartItemsList = document.getElementById('cartItemsList');
            cartItemsList.innerHTML = '';
            this.cartItems.forEach(item => cartItemsList.appendChild(this.createCartItemCard(item)));
            this.calculateSummary();
        }
    },

    addToCart(product) {
        const existingItem = this.cartItems.find(item => item.id === product.id);
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            this.cartItems.push({
                id: product.id,
                name: product.name,
                price: product.price,
                quantity: 1,
                imageUrl: product.imageUrl
            });
        }
        this.saveCart();
        this.renderCart();
        alert(`เพิ่มสินค้า "${product.name}" ลงตะกร้าแล้ว!`);
    },

    updateQuantity(productId, change) {
        const item = this.cartItems.find(item => item.id === productId);
        if (item) {
            const newQuantity = item.quantity + change;
            if (newQuantity >= 1) {
                item.quantity = newQuantity;
            } else if (newQuantity === 0) {
                this.removeItem(productId);
                return;
            }
            this.saveCart();
            this.renderCart();
        }
    },

    removeItem(productId) {
        if (confirm('คุณแน่ใจหรือไม่ที่จะลบสินค้านี้ออกจากตะกร้า?')) {
            this.cartItems = this.cartItems.filter(item => item.id !== productId);
            this.currentDiscount = 0;
            this.saveCart();
            this.renderCart();
            alert('ลบสินค้าออกจากตะกร้าเรียบร้อยแล้ว!');
        }
    },

    calculateSummary() {
        const summary = this.cartItems.reduce((acc, item) => {
            acc.subtotal += item.price * item.quantity;
            acc.totalItems += item.quantity;
            return acc;
        }, {
            subtotal: 0,
            totalItems: 0
        });

        const totalAfterDiscount = Math.max(0, summary.subtotal - this.currentDiscount);
        const vatAmount = totalAfterDiscount * this.VAT_RATE;
        const totalAmount = totalAfterDiscount + vatAmount + this.SHIPPING_COST;

        document.getElementById('totalItemsCount').textContent = `${summary.totalItems} ชิ้น`;
        document.getElementById('subtotal').textContent = `฿${summary.subtotal.toFixed(2)}`;
        document.getElementById('shippingCost').textContent = `฿${this.SHIPPING_COST.toFixed(2)}`;
        document.getElementById('discountAmountDisplay').textContent = `- ฿${this.currentDiscount.toFixed(2)}`;
        document.getElementById('vatAmount').textContent = `฿${vatAmount.toFixed(2)}`;
        document.getElementById('totalAmount').textContent = `฿${totalAmount.toFixed(2)}`;
    },

    applyDiscountCode() {
        const discountCodeInput = document.getElementById('discountCodeInput');
        const discountMessage = document.getElementById('discountMessage');
        const code = discountCodeInput.value.trim().toUpperCase();

        this.currentDiscount = 0;
        discountMessage.textContent = '';
        discountMessage.className = 'discount-message';

        if (this.cartItems.length === 0) {
            discountMessage.textContent = 'กรุณาเพิ่มสินค้าในตะกร้าก่อนใช้รหัสส่วนลด';
            discountMessage.classList.add('error');
            this.calculateSummary();
            return;
        }

        const subtotal = this.cartItems.reduce((sum, item) => sum + item.price * item.quantity, 0);

        if (code === 'DISCOUNT100') {
            this.currentDiscount = 100.00;
            discountMessage.textContent = 'ใช้รหัสส่วนลดสำเร็จ! ได้รับส่วนลด ฿100';
            discountMessage.classList.add('success');
        } else if (code === 'SAVE10PERCENT') {
            this.currentDiscount = subtotal * 0.10;
            discountMessage.textContent = 'ใช้รหัสส่วนลดสำเร็จ! ได้รับส่วนลด 10%';
            discountMessage.classList.add('success');
        } else {
            discountMessage.textContent = 'รหัสส่วนลดไม่ถูกต้องหรือไม่สามารถใช้ได้';
            discountMessage.classList.add('error');
        }
        this.calculateSummary();
    },

    applyCoupon() {
        if (this.cartItems.length === 0) {
            const discountMessage = document.getElementById('discountMessage');
            discountMessage.textContent = 'กรุณาเพิ่มสินค้าในตะกร้าก่อนใช้คูปอง';
            discountMessage.classList.add('error');
            this.calculateSummary();
            return;
        }

        const couponValue = 50.00;
        this.currentDiscount = couponValue;
        const discountMessage = document.getElementById('discountMessage');
        discountMessage.textContent = `ใช้คูปองสำเร็จ! ได้รับส่วนลด ฿${couponValue.toFixed(2)}`;
        discountMessage.className = 'discount-message success';
        this.calculateSummary();
    },

    proceedToCheckout() {
        if (this.cartItems.length === 0) {
            alert('ตะกร้าสินค้าของคุณว่างเปล่า กรุณาเพิ่มสินค้าก่อนดำเนินการชำระเงิน');
            return;
        }

        const subtotal = this.cartItems.reduce((sum, item) => sum + item.price * item.quantity, 0);
        const totalAfterDiscount = Math.max(0, subtotal - this.currentDiscount);
        const vatAmount = totalAfterDiscount * this.VAT_RATE;
        const totalAmount = totalAfterDiscount + vatAmount + this.SHIPPING_COST;

        const orderSummary = {
            items: this.cartItems,
            subtotal: subtotal.toFixed(2),
            discount: this.currentDiscount.toFixed(2),
            vat: vatAmount.toFixed(2),
            shipping: this.SHIPPING_COST.toFixed(2),
            totalAmount: totalAmount.toFixed(2),
            createdAt: new Date().toISOString()
        };

        localStorage.setItem('orderProduct', JSON.stringify(orderSummary));

        alert('สรุปคำสั่งซื้อถูกบันทึกเรียบร้อยแล้ว!');
        
        this.cartItems = [];
        this.currentDiscount = 0;
        this.saveCart();
        this.renderCart();

        // ส่งไปหน้าอื่นได้ เช่น redirect หรือแสดงสรุปคำสั่งซื้อ
        // window.location.href = '/order-summary.html';
    }


};

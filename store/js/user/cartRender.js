import { redirectGet } from '../formHandler.js';

const CartApp = {
    cartItems: [{
        id: 1,
        name: "แทรนดาร์ เอเอ็มเอฟ เมอร์เคียว",
        size: "",
        price: 21.6,
        qty: 4,
        maxQty: 10,
        image: "https://www.trandar.com//public/shop_img/687a1a94a6f10_Trandar_AMF_Mercure.jpg"
    },
    {
        id: 2,
        name: "แทรนดาร์ เอเอ็มเอฟ ไฟน์ เฟรสโค",
        size: "",
        price: 24,
        qty: 1,
        maxQty: 5,
        image: "https://www.trandar.com//public/shop_img/687a1aa984ae2_Trandar_AMF_Fine_Fresko.jpg"
    },
    {
        id: 3,
        name: "แทรนดาร์ เอเอ็มเอฟ สตาร์",
        size: "",
        price: 33.25,
        qty: 1,
        maxQty: 3,
        image: "https://www.trandar.com//public/shop_img/687a1a756ce6a_Trandar_AMF_Star.jpg"
    },
    {
        id: 4,
        name: "แทรนดาร์ ทีบาร์ ที15",
        size: "",
        price: 3,
        qty: 1,
        maxQty: 20,
        image: "https://www.trandar.com//public/shop_img/687b2f5b393b2_497eeb6fc69f5635590f41fc078dff98.jpg"
    },
    {
        id: 5,
        name: "แทรนดาร์ ทีบาร์ ที24",
        size: "",
        price: 11.7,
        qty: 1,
        maxQty: 10,
        image: "https://www.trandar.com//public/shop_img/687b31d91b97e_T24.png"
    }
    ],
    coupons: [{
        code: "SAVE10",
        type: "percent",
        value: 10,
        label: "ลด 10%"
    },
    {
        code: "FREESHIP",
        type: "shipping",
        value: 0,
        label: "ส่งฟรี"
    },
    {
        code: "DISCOUNT50",
        type: "fixed",
        value: 50,
        label: "ลด 50 บาท"
    }
    ],
    services: [{
        name: "giftWrap",
        label: "ห่อของขวัญ",
        price: 20
    },
    {
        name: "insurance",
        label: "ประกันสินค้า",
        price: 50
    },
    {
        name: "expressDelivery",
        label: "จัดส่งด่วน",
        price: 100
    }
    ],
    shipping: [{
        value: "delivery",
        label: "จัดส่ง",
        checked: true
    },
    {
        value: "pickup",
        label: "รับเองที่สาขา",
        checked: false
    }
    ],
    shippingOptionsData: {
        delivery: [{
            value: "lalamove",
            name: "Lalamove",
            price: 500
        },
        {
            value: "truck4",
            name: "รถบรรทุก 4 ล้อ",
            price: 500
        },
        {
            value: "truck6",
            name: "รถบรรทุก 6 ล้อ",
            price: 1000
        }
        ],
        pickup: [{
            value: "branch1",
            name: "แทรนดาร์ อินเตอร์เนชั่นแนล",
            price: 0
        },
        {
            value: "branch2",
            name: "Allable",
            price: 0
        }
        ]
    },
    viewMode: "list",
    selectedServices: [],
    selectedShippingType: "delivery",
    selectedShippingOptions: {},
    appliedCoupon: {},

    // =================== SUMMARY ===================
    calculateSummary() {
        let subtotal = this.cartItems.reduce((sum, item) => sum + item.qty * item.price, 0);
        let shipping = this.selectedShippingOptions?.price || 0;
        let discount = 0;
        if (this.appliedCoupon?.code) {
            if (this.appliedCoupon.type === "percent") discount = subtotal * (this.appliedCoupon.value / 100);
            else if (this.appliedCoupon.type === "fixed") discount = this.appliedCoupon.value;
            else if (this.appliedCoupon.type === "shipping") shipping = 0;
        }
        const service = this.selectedServices.reduce((sum, s) => sum + s.price, 0);
        const tax = (subtotal - discount + shipping + service) * 0.07;
        const total = subtotal - discount + shipping + service + tax;
        return {
            subtotal,
            discount,
            shipping,
            service,
            tax,
            total
        };
    },

    // =================== STORAGE ===================
    saveToStorage() {
        const summary = this.calculateSummary();
        const data = {
            cartItems: this.cartItems,
            appliedCoupon: this.appliedCoupon,
            selectedServices: this.selectedServices,
            selectedShippingType: this.selectedShippingType,
            selectedShippingOptions: this.selectedShippingOptions,
            viewMode: this.viewMode,
            shipping: this.shipping,
            summary
        };
        localStorage.setItem("cartAppData", JSON.stringify(data));
    },

    loadFromStorage() {
        const data = localStorage.getItem("cartAppData");
        if (!data) return;
        try {
            const parsed = JSON.parse(data);
            this.cartItems = parsed.cartItems || this.cartItems;
            this.appliedCoupon = parsed.appliedCoupon || {};
            this.selectedServices = parsed.selectedServices || [];
            this.selectedShippingType = parsed.selectedShippingType || "delivery";
            this.selectedShippingOptions = parsed.selectedShippingOptions || {};
            this.viewMode = parsed.viewMode || "list";
            this.shipping = parsed.shipping || this.shipping;
        } catch (e) {
            console.error("localStorage ผิดพลาด", e);
        }
    },

    // =================== RENDER CART ===================
    renderCart() {
        const list = document.getElementById("cartItemList");
        list.innerHTML = "";
        list.className = `cart-item-list ${this.viewMode}-mode`;
        this.cartItems.forEach(item => {
            const div = document.createElement("div");
            div.className = "cart-item";
            div.dataset.id = item.id;
            if (this.viewMode === "list") {
                div.innerHTML = `
                            <div class="cart-item-info">
                                <img src="${item.image}" alt="${item.name}" class="item-image">
                                <div class="item-details">
                                    <p class="item-name">${item.name}</p>
                                    <p class="item-size">${item.size}</p>
                                    <p class="">จำนวนคงเหลือ ${item.maxQty}</p>
                                    <button class="item-remove" data-id="${item.id}">Remove</button>
                                </div>
                            </div>
                            <div class="item-actions">
                                <div class="item-quantity">
                                    <button class="quantity-button" data-action="decrease" data-id="${item.id}">-</button>
                                    <input type="number" class="quantity-input" data-id="${item.id}" value="${item.qty}" min="1" max="${item.maxQty}">
                                    <button class="quantity-button" data-action="increase" data-id="${item.id}">+</button>
                                </div>
                                <span class="item-price">${(item.qty * item.price).toFixed(2)}</span>
                            </div>`;
            } else {
                div.innerHTML = `
                        <img src="${item.image}" alt="${item.name}" class="item-image">
                        <p class="item-name">${item.name}</p>
                        <p class="item-size">${item.size}</p>
                        <p class="">จำนวนคงเหลือ ${item.maxQty}</p>
                        <p class="item-price">${(item.qty * item.price).toFixed(2)}</p>
                        <div class="item-quantity">
                            <button class="quantity-button" data-action="decrease" data-id="${item.id}">-</button>
                            <input type="number" class="quantity-input" data-id="${item.id}" value="${item.qty}" min="1" max="${item.maxQty}">
                            <button class="quantity-button" data-action="increase" data-id="${item.id}">+</button>
                        </div>
                        <button class="item-remove" data-id="${item.id}">Remove</button>`;
            }
            list.appendChild(div);
        });
        this.renderSummary();
        this.renderCouponList();
        this.renderShipping();
        this.renderDeliveryOptions();
        this.renderServiceOptions();
    },

    updateItemDisplay(item) {
        const div = document.querySelector(`.cart-item[data-id='${item.id}']`);
        if (!div) return;
        const input = div.querySelector(".quantity-input");
        if (input) input.value = item.qty;
        const priceSpan = div.querySelector(".item-price");
        if (priceSpan) priceSpan.innerText = (item.qty * item.price).toFixed(2);
        this.renderSummary();
    },

    // =================== EVENTS ===================
    bindQuantityEvents() {
        const list = document.getElementById("cartItemList");
        list.addEventListener("click", (e) => {
            const btn = e.target.closest(".quantity-button");
            if (!btn) return;
            const id = parseInt(btn.dataset.id);
            const item = this.cartItems.find(i => i.id === id);
            if (!item) return;
            if (btn.dataset.action === "increase" && item.qty < item.maxQty) item.qty++;
            if (btn.dataset.action === "decrease" && item.qty > 1) item.qty--;
            this.updateItemDisplay(item);
            this.saveToStorage();
        });

        list.addEventListener("input", (e) => {
            const input = e.target.closest(".quantity-input");
            if (!input) return;
            const id = parseInt(input.dataset.id);
            const item = this.cartItems.find(i => i.id === id);
            if (!item) return;
            let value = parseInt(input.value) || 1;
            const min = parseInt(input.min) || 1;
            const max = parseInt(input.max) || 1000;
            if (value < min) value = min;
            if (value > max) value = max;
            input.value = value;
            item.qty = value;
            this.updateItemDisplay(item);
            this.saveToStorage();
        });
    },

    bindRemoveEvents() {
        const list = document.getElementById("cartItemList");
        list.addEventListener("click", (e) => {
            const btn = e.target.closest(".item-remove");
            if (!btn) return;
            const id = parseInt(btn.dataset.id);
            this.cartItems = this.cartItems.filter(i => i.id !== id);
            btn.closest(".cart-item").remove();
            this.renderSummary();
            this.saveToStorage();
        });
    },

    bindCouponEvents() {
        document.getElementById("applyDiscount").onclick = () => {
            const code = document.getElementById("discountCode").value.trim().toUpperCase();
            this.applyCoupon(code);
        };
    },

    bindCheckoutEvents() {
        document.getElementById("checkoutOrders").onclick = () => {
        this.saveToStorage();
        redirectGet(pathConfig.BASE_WEB + '/user/checkout/');
    };
    },

    bindEvents() {
        this.bindQuantityEvents();
        this.bindRemoveEvents();
        this.bindCouponEvents();
        this.bindCheckoutEvents();
    },

    bindViewToggle() {
        const listBtn = document.getElementById("listModeBtn");
        const gridBtn = document.getElementById("gridModeBtn");
        const setActive = (mode) => {
            this.viewMode = mode;
            listBtn.classList.toggle("active", mode === "list");
            gridBtn.classList.toggle("active", mode === "grid");
            this.renderCart();
        };
        listBtn.onclick = () => setActive("list");
        gridBtn.onclick = () => setActive("grid");
        setActive(this.viewMode);
    },

    // =================== COUPONS ===================
    renderCouponList() {
        const container = document.getElementById("couponList");
        container.innerHTML = "";
        this.coupons.forEach(coupon => {
            const btn = document.createElement("button");
            btn.className = "coupon-btn";
            btn.classList.toggle("active", this.appliedCoupon?.code === coupon.code);
            btn.innerText = coupon.label;
            btn.onclick = () => this.applyCoupon(coupon.code);
            container.appendChild(btn);
        });

        const msg = document.getElementById("discountMessage");
        if (this.appliedCoupon?.code) {
            msg.innerText = `ใช้คูปอง ${this.appliedCoupon.label} แล้ว`;
            msg.style.color = "green";
        } else {
            msg.innerText = "";
        }
    },

    applyCoupon(code) {
        const coupon = this.coupons.find(c => c.code === code);
        const msg = document.getElementById("discountMessage");
        if (coupon) {
            this.appliedCoupon = coupon;
            msg.innerText = `ใช้คูปอง ${coupon.label} แล้ว`;
            msg.style.color = "green";
        } else {
            this.appliedCoupon = {};
            msg.innerText = "คูปองไม่ถูกต้อง";
            msg.style.color = "red";
        }
        this.saveToStorage();
        this.renderCouponList();
        this.renderSummary();
    },

    // =================== SUMMARY ===================
    renderSummary() {
        const {
            subtotal,
            discount,
            shipping,
            service,
            tax,
            total
        } = this.calculateSummary();
        document.getElementById("summaryDetails").innerHTML = `
                    <div class="summary-row"><span>รวม</span><span>${subtotal.toFixed(2)}</span></div>
                    <div class="summary-row"><span>ส่วนลด</span><span>-${discount.toFixed(2)}</span></div>
                    <div class="summary-row"><span>จัดส่ง</span><span>${shipping.toFixed(2)}</span></div>
                    <div class="summary-row"><span>บริการเสริม</span><span>${service.toFixed(2)}</span></div>
                    <div class="summary-row"><span>ภาษามูลค่าเพิ่ม 7%</span><span>${tax.toFixed(2)}</span></div>
                    <div class="summary-row summary-subtotal"><span>ทั้งหมด</span><span>${total.toFixed(2)}</span></div>
                `;
    },

    // =================== SHIPPING ===================
    renderShipping() {
        const container = document.getElementById("shipping");
        container.innerHTML = "";
        this.shipping.forEach(item => {
            const checked = this.selectedShippingType === item.value ? "checked" : "";
            const label = document.createElement("label");
            label.classList.add("shipping-label");
            label.innerHTML = `<input type="radio" name="shipping" value="${item.value}" ${checked}> ${item.label}`;
            container.appendChild(label);
        });
        container.querySelectorAll('input[name="shipping"]').forEach(radio => {
            radio.onchange = (e) => {
                this.selectedShippingType = e.target.value;
                const options = this.shippingOptionsData[this.selectedShippingType] || [];
                this.selectedShippingOptions = options.length ? options[0] : {};
                this.saveToStorage();
                this.renderDeliveryOptions(this.selectedShippingType);
                this.renderSummary();
            };
        });
    },

    renderDeliveryOptions(method) {
        const container = document.getElementById("deliveryOptions");
        container.innerHTML = "";
        const shippingMethod = method || this.selectedShippingType || "delivery";
        const options = this.shippingOptionsData[shippingMethod] || [];
        if (!options.length) this.selectedShippingOptions = {};
        else if (!this.selectedShippingOptions || !options.some(o => o.name === this.selectedShippingOptions.name)) this.selectedShippingOptions = options[0];
        const selectedOption = this.selectedShippingOptions;
        options.forEach(opt => {
            const label = document.createElement("label");
            label.classList.add("delivery-label");
            const checked = selectedOption?.name === opt.name ? "checked" : "";
            label.innerHTML = `<input type="radio" name="deliveryOption" value="${opt.name}" ${checked}> ${opt.name} (+${opt.price})`;
            container.appendChild(label);
        });
        container.querySelectorAll('input[name="deliveryOption"]').forEach(input => {
            input.onchange = (e) => {
                const selected = options.find(o => o.name === e.target.value);
                if (selected) this.selectedShippingOptions = selected;
                this.saveToStorage();
                this.renderSummary();
            };
        });
    },

    // =================== SERVICE ===================
    renderServiceOptions() {
        const container = document.querySelector(".service-section .service-options");
        if (!container) return;
        container.innerHTML = "";
        this.services.forEach(service => {
            const label = document.createElement("label");
            label.classList.add("service-label");
            const isChecked = this.selectedServices.some(s => s.name === service.name) ? "checked" : "";
            label.innerHTML = `<input type="checkbox" name="service" value="${service.name}" data-price="${service.price}" ${isChecked}> ${service.label} (+${service.price})`;
            container.appendChild(label);
        });
        this.bindServiceEvents();
    },

    bindServiceEvents() {
        document.querySelectorAll('input[name="service"]').forEach(input => {
            input.onchange = (e) => {
                const price = parseFloat(e.target.dataset.price);
                const label = e.target.closest("label")?.innerText.trim();
                if (e.target.checked) {
                    if (!this.selectedServices.some(s => s.name === e.target.value)) {
                        this.selectedServices.push({
                            name: e.target.value,
                            price,
                            label
                        });
                    }
                } else {
                    this.selectedServices = this.selectedServices.filter(s => s.name !== e.target.value);
                }
                this.saveToStorage();
                this.renderSummary();
            };
        });
    },

    // =================== INIT ===================
    init() {
        this.loadFromStorage();
        this.renderServiceOptions();
        this.renderCart();
        this.bindViewToggle();
        this.bindEvents();
    }
};

document.addEventListener("DOMContentLoaded", () => {
CartApp.init();
});


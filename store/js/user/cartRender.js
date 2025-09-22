import { redirectGet } from '../centerHandler.js';

const CartApp = {
    cartItems: [{
        id: 1,
        name: "แทรนดาร์ เอเอ็มเอฟ เมอร์เคียว",
        size: "",
        price: 21.6,
        qty: 4,
        maxQty: 10,
        weight: 100.2,
        image: "https://www.trandar.com//public/shop_img/687a1a94a6f10_Trandar_AMF_Mercure.jpg"
    }, {
        id: 2,
        name: "แทรนดาร์ เอเอ็มเอฟ ไฟน์ เฟรสโค",
        size: "",
        price: 24,
        qty: 1,
        maxQty: 5,
        weight: 1.5,
        image: "https://www.trandar.com//public/shop_img/687a1aa984ae2_Trandar_AMF_Fine_Fresko.jpg"
    }, {
        id: 3,
        name: "แทรนดาร์ เอเอ็มเอฟ สตาร์",
        size: "",
        price: 33.25,
        qty: 1,
        maxQty: 3,
        weight: 2.1,
        image: "https://www.trandar.com//public/shop_img/687a1a756ce6a_Trandar_AMF_Star.jpg"
    }, {
        id: 4,
        name: "แทรนดาร์ ทีบาร์ ที15",
        size: "",
        price: 3,
        qty: 1,
        maxQty: 20,
        weight: 0.8,
        image: "https://www.trandar.com//public/shop_img/687b2f5b393b2_497eeb6fc69f5635590f41fc078dff98.jpg"
    }, {
        id: 5,
        name: "แทรนดาร์ ทีบาร์ ที24",
        size: "",
        price: 11.7,
        qty: 1,
        maxQty: 10,
        weight: 0.5,
        image: "https://www.trandar.com//public/shop_img/687b31d91b97e_T24.png"
    }],
    coupons: [],
    services: [{
        name: "giftWrap",
        label: "ห่อของขวัญ",
        price: 20
    }, {
        name: "insurance",
        label: "ประกันสินค้า",
        price: 50
    }, {
        name: "expressDelivery",
        label: "จัดส่งด่วน",
        price: 100
    }],
    shipping: [{
        value: "delivery",
        label: "จัดส่ง",
        checked: true
    }, {
        value: "pickup",
        label: "รับเองที่สาขา",
        checked: false
    }],
    shippingOptionsData: {
        delivery: [{
            value: "lalamove",
            name: "Lalamove",
            price: 500,
            capacity: 1000
        }, {
            value: "truck4",
            name: "รถบรรทุก 4 ล้อ",
            price: 500,
            capacity: 9500
        }, {
            value: "truck6",
            name: "รถบรรทุก 6 ล้อ",
            price: 1000,
            capacity: 15000
        }
            // {
            //     value: "truck",
            //     name: "ติดต่อเจ้าหน้าที่",
            //     price: 0,
            //     capacity: 20000
            // }
        ],
        pickup: [{
            value: "branch1",
            name: "แทรนดาร์ อินเตอร์เนชั่นแนล",
            price: 0,
            timeSlots: ["09:00-12:00", "13:00-16:00", "16:00-18:00"]
        }, {
            value: "branch2",
            name: "แทรนดาร์ ร่มเกล้า",
            price: 0,
            timeSlots: ["10:00-14:00", "14:00-17:00"]
        }]
    },
    viewMode: "list",
    selectedServices: [],
    selectedShippingType: "delivery",
    selectedShippingOptions: {},
    appliedCoupon: {},

    // =================== SUMMARY ===================
    calculateSummary() {
        let subtotal = this.cartItems.reduce((sum, item) => sum + item.qty * item.price, 0);
        let shipping = this.selectedShippingType === 'delivery' || this.selectedShippingType === 'pickup' ? (this.selectedShippingOptions.price || 0) : 0;
        let discount = 0;
        if (this.appliedCoupon?.code) {
            if (this.appliedCoupon.type === "percent") discount = subtotal * (this.appliedCoupon.value / 100);
            else if (this.appliedCoupon.type === "fixed") discount = this.appliedCoupon.value;
            else if (this.appliedCoupon.type === "shipping") shipping = 0;
        }
        const service = this.selectedServices.reduce((sum, s) => sum + s.price, 0);
        const tax = (subtotal - discount + shipping + service) * 0.07;
        const total = subtotal - discount + shipping + service + tax;

        const totalWeight = this.cartItems.reduce((sum, item) => sum + item.qty * (item.weight || 0), 0);

        return {
            subtotal,
            discount,
            shipping,
            service,
            tax,
            total,
            totalWeight
        };
    },

    calculateItemWeight(item) {
        return (item.weight * item.qty).toFixed(2);
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
            summary: {
                ...summary,
                totalWeight: summary.totalWeight
            }
        };
        localStorage.setItem("cartAppData", JSON.stringify(data));
    },

    loadFromStorage() {
        const cart = localStorage.getItem("cartAppData");
        const coupon = localStorage.getItem("couponsAppData");

        try {

            if (cart) {
                const parsedCart = JSON.parse(cart);
                this.cartItems = parsedCart.cartItems || this.cartItems;
                this.appliedCoupon = parsedCart.appliedCoupon || {};
                this.selectedServices = parsedCart.selectedServices || [];
                this.selectedShippingType = parsedCart.selectedShippingType || "delivery";
                this.selectedShippingOptions = parsedCart.selectedShippingOptions || {};
                this.viewMode = parsedCart.viewMode || "list";
                this.shipping = parsedCart.shipping || this.shipping;
            }

            if (coupon) {
                const parsedCoupon = JSON.parse(coupon);
                this.coupons = parsedCoupon;
            }

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
            const itemWeight = this.calculateItemWeight(item);
            if (this.viewMode === "list") {
                div.innerHTML = `
                    <div class="cart-item-info">
                        <img src="${item.image}" alt="${item.name}" class="item-image">
                        <div class="item-details">
                            <p class="item-name">${item.name}</p>
                            <p class="item-size">${item.size}</p>
                            <p class="">จำนวนคงเหลือ ${item.maxQty}</p>
                            <p class="item-weight">น้ำหนัก: ${itemWeight} กก.</p>
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
                    <p class="item-weight">น้ำหนัก: ${itemWeight} กก.</p>
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
        this.renderTotalWeight();
    },

    updateItemDisplay(item) {
        const div = document.querySelector(`.cart-item[data-id='${item.id}']`);
        if (!div) return;
        const input = div.querySelector(".quantity-input");
        if (input) input.value = item.qty;
        const priceSpan = div.querySelector(".item-price");
        if (priceSpan) priceSpan.innerText = (item.qty * item.price).toFixed(2);
        const weightSpan = div.querySelector(".item-weight");
        if (weightSpan) weightSpan.innerText = `น้ำหนัก: ${this.calculateItemWeight(item)} กก.`;
        this.renderSummary();
        this.renderTotalWeight();
        this.renderDeliveryOptions();
        this.saveToStorage();
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
            this.renderTotalWeight();
            this.renderDeliveryOptions();
            this.saveToStorage();
        });
    },

    bindCouponEvents() {
        document.getElementById("applyDiscount").onclick = () => {
            const code = document.getElementById("discountCode").value.trim().toUpperCase();
            this.applyCoupon(code);
        };
    },

    // =================== EVENTS ===================
    bindCheckoutEvents() {
        document.getElementById("checkoutOrders").onclick = () => {
            if (this.selectedShippingType === "pickup") {
                // MODIFIED: Check for both pickupDate and timeSlot
                if (!this.selectedShippingOptions?.pickupDate || !this.selectedShippingOptions?.timeSlot) {
                    // MODIFIED: Updated alert message
                    alert("กรุณาเลือกวันที่ สาขา และช่วงเวลาในการรับสินค้าให้ครบถ้วน");
                    return; // หยุดการทำงาน
                }
            }
            this.saveToStorage();
            redirectGet(pathConfig.BASE_WEB + 'user/checkout/');
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
            total,
        } = this.calculateSummary();
        document.getElementById("summaryDetails").innerHTML = `
            <div class="summary-row"><span>รวม</span><span>${subtotal.toFixed(2)}</span></div>
            <div class="summary-row"><span>ส่วนลด</span><span>-${discount.toFixed(2)}</span></div>
            <div class="summary-row"><span>จัดส่ง</span><span>${shipping.toFixed(2)}</span></div>
            <div class="summary-row"><span>บริการเสริม</span><span>${service.toFixed(2)}</span></div>
            <div class="summary-row"><span>ภาษามูลค่าเพิ่ม 7%</span><span>${tax.toFixed(2)}</span></div>
            <div class="summary-row summary-total"><span>ทั้งหมด</span><span>${total.toFixed(2)}</span></div>
        `;
    },

    // =================== TOTAL WEIGHT ===================
    renderTotalWeight() {
        const totalWeight = this.cartItems.reduce((sum, item) => sum + item.qty * (item.weight || 0), 0);
        const weightDisplay = document.getElementById("totalWeightDisplay");
        if (weightDisplay) {
            weightDisplay.innerText = `${totalWeight.toFixed(2)} กก.`;
        }
        this.getRecommendedShipping();
    },

    // =================== SHIPPING RECOMMENDATION ===================
    getRecommendedShipping() {
        const recElement = document.getElementById("shippingRecommendation");
        if (!recElement) return;

        const shippingType = this.selectedShippingType;

        if (shippingType === "pickup") {
            recElement.style.display = 'none';
            return;
        }

        recElement.style.display = 'block';
        const totalWeight = this.cartItems.reduce((sum, item) => sum + item.qty * (item.weight || 0), 0);
        let recommendation = "";

        const deliveryOptions = this.shippingOptionsData.delivery.sort((a, b) => b.capacity - a.capacity);

        if (totalWeight > deliveryOptions[0].capacity) {
            recommendation = "กรุณาติดต่อเจ้าหน้าที่เพื่อสอบถามค่าขนส่งสำหรับสินค้าขนาดใหญ่";
        } else {
            for (const option of deliveryOptions) {
                if (totalWeight <= option.capacity) {
                    recommendation = `แนะนำให้จัดส่งด้วย ${option.name}`;
                }
            }
        }

        if (!recommendation) {
            recommendation = "ไม่พบตัวเลือกการจัดส่งที่เหมาะสม";
        }

        recElement.innerHTML = `<p class="recommendation-text">คำแนะนำ: ${recommendation}</p>`;
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
                this.selectedShippingOptions = {};
                this.saveToStorage();
                this.renderDeliveryOptions();
                this.renderPickupOptions();
                this.renderSummary();
                this.getRecommendedShipping();
            };
        });
        this.getRecommendedShipping();
        this.renderPickupOptions();
    },

    renderDeliveryOptions() {
        const container = document.getElementById("deliveryOptions");
        container.innerHTML = "";

        if (this.selectedShippingType !== "delivery") {
            return;
        }

        const options = this.shippingOptionsData.delivery || [];
        const totalWeight = this.cartItems.reduce((sum, item) => sum + item.qty * (item.weight || 0), 0);

        const currentSelected = this.selectedShippingOptions;
        if (currentSelected?.capacity && totalWeight > currentSelected.capacity) {
            this.selectedShippingOptions = {};
        }

        let recommendedOption = null;
        const sortedOptions = [...options].sort((a, b) => a.capacity - b.capacity);
        const suitableOption = sortedOptions.find(opt => totalWeight <= opt.capacity);

        if (suitableOption) {
            recommendedOption = suitableOption;
        } else {
            recommendedOption = sortedOptions[sortedOptions.length - 1];
        }

        if (!this.selectedShippingOptions.name) {
            this.selectedShippingOptions = recommendedOption;
        }

        if (!options.length) {
            container.innerHTML = `<p>ไม่มีตัวเลือกการจัดส่ง</p>`;
        } else {
            options.forEach(opt => {
                const isTooHeavy = totalWeight > opt.capacity;
                const label = document.createElement("label");
                label.classList.add("delivery-label");
                const disabled = isTooHeavy ? "disabled" : "";
                const checked = this.selectedShippingOptions?.value === opt.value && !isTooHeavy ? "checked" : "";
                label.innerHTML = `<input type="radio" name="deliveryOption" value="${opt.value}" ${checked} ${disabled}> ${opt.name} (+${opt.price})`;
                container.appendChild(label);
            });
        }

        container.querySelectorAll('input[name="deliveryOption"]').forEach(input => {
            input.onchange = (e) => {
                const selected = options.find(o => o.value === e.target.value);
                if (selected) this.selectedShippingOptions = selected;
                this.saveToStorage();
                this.renderSummary();
            };
        });
        this.renderSummary();
    },

    // =================== PICKUP OPTIONS ===================
    renderPickupOptions() {
        const container = document.getElementById("pickupOptions");
        container.innerHTML = "";

        if (this.selectedShippingType !== "pickup") {
            container.style.display = 'none';
            return;
        }

        container.style.display = 'block';

        const options = this.shippingOptionsData.pickup || [];

        if (options.length === 0) {
            container.innerHTML = `<p class="alert alert-warning">ไม่พบตัวเลือกสาขา</p>`;
            return;
        }

        // =================== START: ADDED CODE ===================
        // Render วันที่
        const dateContainer = document.createElement("div");
        const dateLabel = document.createElement("label");
        dateLabel.setAttribute("for", "pickupDateSelect");
        dateLabel.className = "form-label";
        dateLabel.innerHTML = `<strong>เลือกวันที่รับสินค้า:</strong>`;

        const dateInput = document.createElement("input");
        dateInput.type = "date";
        dateInput.id = "pickupDateSelect";
        dateInput.className = "form-input mb-2";

        // Set min date to today to prevent selecting past dates
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', today);

        // Set the value from storage if it exists
        if (this.selectedShippingOptions?.pickupDate) {
            dateInput.value = this.selectedShippingOptions.pickupDate;
        }

        // Event listener สำหรับการเลือกวันที่
        dateInput.onchange = (e) => {
            // Create selectedShippingOptions if it doesn't exist
            if (!this.selectedShippingOptions) {
                this.selectedShippingOptions = {};
            }
            this.selectedShippingOptions.pickupDate = e.target.value;
            this.saveToStorage();
        };

        dateContainer.appendChild(dateLabel);
        dateContainer.appendChild(dateInput);
        container.appendChild(dateContainer);
        // =================== END: ADDED CODE ===================

        // Render สาขา
        const branchSelect = document.createElement("select");
        branchSelect.id = "pickupBranchSelect";
        branchSelect.className = "form-input mb-2";
        branchSelect.innerHTML = `<option value="">เลือกสาขา</option>`;

        options.forEach(branch => {
            const option = document.createElement("option");
            option.value = branch.value;
            option.innerText = branch.name;
            if (this.selectedShippingOptions?.value === branch.value) {
                option.selected = true;
            }
            branchSelect.appendChild(option);
        });

        const branchContainer = document.createElement("div");
        const branchLabel = document.createElement("label");
        branchLabel.setAttribute("for", "pickupBranchSelect");
        branchLabel.className = "form-label";
        branchLabel.innerHTML = `<strong>เลือกสาขา:</strong>`;
        branchContainer.appendChild(branchLabel);
        branchContainer.appendChild(branchSelect);
        container.appendChild(branchContainer);

        // Render ช่วงเวลา
        const timeSlotContainer = document.createElement("div");
        timeSlotContainer.id = "pickupTimeSlotContainer";
        container.appendChild(timeSlotContainer);

        // Event listener สำหรับการเปลี่ยนสาขา
        branchSelect.onchange = (e) => {
            const selectedBranch = options.find(b => b.value === e.target.value);
            // Preserve the selected date, but reset the time slot
            this.selectedShippingOptions = {
                ...selectedBranch,
                pickupDate: this.selectedShippingOptions?.pickupDate || "",
                timeSlot: ""
            };
            this.saveToStorage();
            this.renderPickupTimeSlots();
            this.renderSummary();
        };

        this.renderPickupTimeSlots();
    },

    renderPickupTimeSlots() {
        const container = document.getElementById("pickupTimeSlotContainer");
        container.innerHTML = "";

        // Check the selectedShippingOptions object for a timeSlots array
        const selectedTimeSlots = this.selectedShippingOptions?.timeSlots;

        if (!selectedTimeSlots || selectedTimeSlots.length === 0) {
            container.style.display = 'none';
            return;
        }

        container.style.display = 'block';

        const timeSlotSelect = document.createElement("select");
        timeSlotSelect.id = "pickupTimeSlotSelect";
        timeSlotSelect.className = "form-input mb-3";
        timeSlotSelect.innerHTML = `<option value="">เลือกช่วงเวลา</option>`;

        selectedTimeSlots.forEach(slot => {
            const option = document.createElement("option");
            option.value = slot;
            option.innerText = slot;
            if (this.selectedShippingOptions?.timeSlot === slot) {
                option.selected = true;
            }
            timeSlotSelect.appendChild(option);
        });

        const timeSlotLabel = document.createElement("label");
        timeSlotLabel.setAttribute("for", "pickupTimeSlotSelect");
        timeSlotLabel.className = "form-label";
        timeSlotLabel.innerHTML = `<strong>เลือกช่วงเวลา:</strong>`;

        container.appendChild(timeSlotLabel);
        container.appendChild(timeSlotSelect);

        // Event listener สำหรับการเลือกช่วงเวลา
        timeSlotSelect.onchange = (e) => {
            // Update the timeSlot property on the existing selectedShippingOptions object
            this.selectedShippingOptions.timeSlot = e.target.value;
            this.saveToStorage();
            this.renderSummary();
        };
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


<?php include '../../routes.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-STORE</title>
    <?php include '../../inc-meta.php'; ?>
    <link href="../../css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../../css/user/template-cart.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../../inc-cdn.php'; ?>

</head>

<body>

    <?php include '../../template/head-bar.php'; ?>
    <main>
        <div id="sections_root_cart" class="section-space">
            <div class="container">
                <div class="row">
                    <div class="col-md-8">
                        <section>
                            <div class="cart-items-section">
                                <div class="cart-view-toggle">
                                    <h5>ตะกร้าสินค้า</h5>
                                    <div>
                                        <button id="listModeBtn">List</button>
                                        <button id="gridModeBtn">Grid</button>
                                    </div>
                                </div>
                                <div id="cartItemList" class="cart-item-list list-mode"></div>
                            </div>
                        </section>
                    </div>
                    <div class="col-md-4">
                        <aside>
                            <div class="summary-section">
                                <h2 class="summary-title">สรุป</h2>
                                <div id="summaryDetails" class="summary-details"></div>
                                <button class="checkout-button">ไปที่การชำระเงิน</button>
                                <p class="terms-text">
                                    การสั่งซื้อของคุณถือเป็นการยอมรับ
                                    <a href="#" class="terms-link">ข้อกำหนดการให้บริการ</a> และ
                                    <a href="#" class="terms-link">นโยบายความเป็นส่วนตัว</a>
                                    ของเรา กรุณาตรวจสอบข้อมูลการสั่งซื้อให้ถูกต้อง
                                    เวลาจัดส่งเป็นการประมาณการณ์และอาจมีการเปลี่ยนแปลงตามความพร้อมของสินค้า.
                                </p>
                            </div>
                        </aside>
                        <aside>
                            <div class="delivery-section">
                                <h3 class="delivery-title">วิธีรับสินค้า</h3>
                                <div id="shipping"></div>
                                <div class="delivery-options" id="deliveryOptions"></div>
                            </div>
                        </aside>
                        <aside>
                            <div class="service-section">
                                <h3 class="service-title">บริการเสริม</h3>
                                <div class="service-options"></div>
                            </div>
                        </aside>
                        <aside>
                            <div class="discount-section">
                                <p id="discountMessage"></p>
                                <h3 class="discount-title">ใช้คูปอง</h3>
                                <div id="couponList"></div>
                                <h3 class="discount-title">รหัสส่วนลด</h3>
                                <div class="discount-code-group">
                                    <input type="text" id="discountCode" class="discount-input" placeholder="กรอกรหัสส่วนลด">
                                </div>
                                <button id="applyDiscount" class="discount-apply-btn">ใช้รหัส</button>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        </div>
    </main>


    <script>
        const CartApp = {
            cartItems: [
                { id: 1, name: "Haldiram's Sev Bhujia", size: "200g", price: 21.6, qty: 4, image: "https://placehold.co/80x80/E5E7EB/2B2B2B?text=Snack" },
                { id: 2, name: "NutriChoice Digestive", size: "200g", price: 24, qty: 1, image: "https://placehold.co/80x80/E5E7EB/2B2B2B?text=Biscuit" },
                { id: 3, name: "Cadbury 5 Star Chocolate", size: ".98 / lb", price: 33.25, qty: 1, image: "https://placehold.co/80x80/E5E7EB/2B2B2B?text=Chocolate" },
                { id: 4, name: "Onion Flavour Potato", size: "250g", price: 3, qty: 1, image: "https://placehold.co/80x80/E5E7EB/2B2B2B?text=Chips" },
                { id: 5, name: "Salted Instant Popcorn", size: "100g", price: 11.7, qty: 1, image: "https://placehold.co/80x80/E5E7EB/2B2B2B?text=Popcorn" }
            ],
            coupons: [
                { code: "SAVE10", type: "percent", value: 10, label: "ลด 10%" },
                { code: "FREESHIP", type: "shipping", value: 0, label: "ส่งฟรี" },
                { code: "DISCOUNT50", type: "fixed", value: 50, label: "ลด 50 บาท" }
            ],
            services: [
                { name: "giftWrap", label: "ห่อของขวัญ", price: 20 },
                { name: "insurance", label: "ประกันสินค้า", price: 50 },
                { name: "expressDelivery", label: "จัดส่งด่วน", price: 100 }
            ],
            appliedCoupon: null,
            viewMode: "list",
            shipping: [
                { value:"delivery", label:"จัดส่ง", checked:true },
                { value:"pickup", label:"รับเองที่สาขา", checked:false }
            ],
            deliveryOptionsData: {
                delivery: [
                    { name: "Lalamove", price: 50 },
                    { name: "รถบรรทุก 4 ล้อ", price: 150 },
                    { name: "รถบรรทุก 6 ล้อ", price: 250 }
                ],
                pickup: [
                    { name: "สาขาอโศก", price: 0 },
                    { name: "สาขาเซ็นทรัล", price: 0 },
                    { name: "สาขาเอ็มควอเทียร์", price: 0 }
                ]
            },
            selectedDeliveryPrice: 50,
            selectedServices: [],

            // Render cart items
            renderCart() {
                const list = document.getElementById("cartItemList");
                list.innerHTML = "";
                list.className = `cart-item-list ${this.viewMode}-mode`;

                this.cartItems.forEach(item => {
                    const div = document.createElement("div");
                    div.className = "cart-item";

                    if(this.viewMode === "list") {
                        div.innerHTML = `
                            <div class="cart-item-info">
                                <img src="${item.image}" alt="${item.name}" class="item-image">
                                <div class="item-details">
                                    <p class="item-name">${item.name}</p>
                                    <p class="item-size">${item.size}</p>
                                    <button class="item-remove" data-id="${item.id}">Remove</button>
                                </div>
                            </div>
                            <div class="item-actions">
                                <div class="item-quantity">
                                    <button class="quantity-button" data-action="decrease" data-id="${item.id}">-</button>
                                    <span class="quantity-value">${item.qty}</span>
                                    <button class="quantity-button" data-action="increase" data-id="${item.id}">+</button>
                                </div>
                                <span class="item-price">${(item.qty * item.price).toFixed(2)}</span>
                            </div>
                        `;
                    } else {
                        div.innerHTML = `
                            <img src="${item.image}" alt="${item.name}" class="item-image">
                            <p class="item-name">${item.name}</p>
                            <p class="item-size">${item.size}</p>
                            <p class="item-price">$${(item.qty * item.price).toFixed(2)}</p>
                            <div class="item-quantity">
                                <button class="quantity-button" data-action="decrease" data-id="${item.id}">-</button>
                                <span class="quantity-value">${item.qty}</span>
                                <button class="quantity-button" data-action="increase" data-id="${item.id}">+</button>
                            </div>
                            <button class="item-remove" data-id="${item.id}">Remove</button>
                        `;
                    }

                    list.appendChild(div);
                });

                this.bindEvents();
                this.renderSummary();
                this.renderCouponList();
                this.renderShipping();          // Render shipping radio
                this.renderDeliveryOptions();   // Render delivery options
                this.renderServiceOptions();
            },

            bindEvents() {
                document.querySelectorAll(".quantity-button").forEach(btn => {
                    btn.addEventListener("click", () => {
                        const id = parseInt(btn.dataset.id);
                        const action = btn.dataset.action;
                        const item = this.cartItems.find(i => i.id === id);
                        if(!item) return;
                        if(action === "increase") item.qty++;
                        if(action === "decrease" && item.qty > 1) item.qty--;
                        this.renderCart();
                    });
                });

                document.querySelectorAll(".item-remove").forEach(btn => {
                    btn.addEventListener("click", () => {
                        const id = parseInt(btn.dataset.id);
                        this.cartItems = this.cartItems.filter(i => i.id !== id);
                        this.renderCart();
                    });
                });

                document.getElementById("applyDiscount").addEventListener("click", () => {
                    const code = document.getElementById("discountCode").value.trim().toUpperCase();
                    this.applyCoupon(code);
                });
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

                listBtn.addEventListener("click", () => setActive("list"));
                gridBtn.addEventListener("click", () => setActive("grid"));

                setActive(this.viewMode);
            },

            renderCouponList() {
                const couponContainer = document.getElementById("couponList");
                couponContainer.innerHTML = "";
                this.coupons.forEach(coupon => {
                    const btn = document.createElement("button");
                    btn.className = "coupon-btn";
                    btn.innerText = coupon.label;
                    btn.addEventListener("click", () => this.applyCoupon(coupon.code));
                    couponContainer.appendChild(btn);
                });
            },

            applyCoupon(code) {
                const coupon = this.coupons.find(c => c.code === code);
                const msg = document.getElementById("discountMessage");
                if(coupon){
                    this.appliedCoupon = coupon;
                    msg.innerText = `ใช้คูปอง ${coupon.label} แล้ว`;
                    msg.style.color = "green";
                } else {
                    this.appliedCoupon = null;
                    msg.innerText = "คูปองไม่ถูกต้อง";
                    msg.style.color = "red";
                }
                this.renderSummary();
            },

            renderSummary() {
                let subtotal = this.cartItems.reduce((sum, item) => sum + item.qty * item.price, 0);
                let shipping = 0;
                const deliveryMethod = document.querySelector('input[name="shipping"]:checked')?.value || "delivery";
                if(deliveryMethod === "delivery") shipping = this.selectedDeliveryPrice;

                let discount = 0;
                if(this.appliedCoupon){
                    if(this.appliedCoupon.type === "percent") discount = subtotal * (this.appliedCoupon.value / 100);
                    else if(this.appliedCoupon.type === "fixed") discount = this.appliedCoupon.value;
                    else if(this.appliedCoupon.type === "shipping") shipping = 0;
                }

                const serviceFee = this.selectedServices.reduce((sum,s) => sum + s.price, 0);
                const tax = (subtotal - discount + shipping + serviceFee) * 0.18;
                const total = subtotal - discount + shipping + serviceFee + tax;

                document.getElementById("summaryDetails").innerHTML = `
                    <div class="summary-row"><span>Item Subtotal</span><span>${subtotal.toFixed(2)}</span></div>
                    <div class="summary-row"><span>Discount</span><span>-${discount.toFixed(2)}</span></div>
                    <div class="summary-row"><span>Shipping Fee</span><span>${shipping.toFixed(2)}</span></div>
                    <div class="summary-row"><span>Service Fee</span><span>${serviceFee.toFixed(2)}</span></div>
                    <div class="summary-row"><span>Tax Vat 18%</span><span>${tax.toFixed(2)}</span></div>
                    <div class="summary-row summary-subtotal"><span>Total</span><span>${total.toFixed(2)}</span></div>
                `;
            },

            // Render shipping radio buttons (delivery / pickup)
            renderShipping() {
                const container = document.getElementById("shipping");
                container.innerHTML = "";

                this.shipping.forEach(item => {
                    const label = document.createElement("label");
                    label.classList.add("shipping-label");
                    label.innerHTML = `
                        <input type="radio" name="shipping" value="${item.value}" ${item.checked ? "checked" : ""}>
                        ${item.label}
                    `;
                    container.appendChild(label);
                });

                // bind change
                document.querySelectorAll('input[name="shipping"]').forEach(radio => {
                    radio.addEventListener("change", (e) => {
                        this.renderDeliveryOptions(e.target.value);
                        this.renderSummary();
                    });
                });
            },

            // Render delivery options based on shipping method
            renderDeliveryOptions(method) {
                const container = document.getElementById("deliveryOptions");
                container.innerHTML = "";

                const deliveryMethod = method || document.querySelector('input[name="shipping"]:checked')?.value || "delivery";
                const options = this.deliveryOptionsData[deliveryMethod];

                options.forEach((opt, idx) => {
                    const label = document.createElement("label");
                    label.classList.add("delivery-label");
                    label.innerHTML = `
                        <input type="radio" name="deliveryOption" value="${opt.name}" ${idx === 0 ? "checked" : ""}>
                        ${opt.name} (+${opt.price})
                    `;
                    container.appendChild(label);
                });

                this.selectedDeliveryPrice = options[0].price;

                document.querySelectorAll('input[name="deliveryOption"]').forEach(input => {
                    input.addEventListener("change", (e) => {
                        const selected = options.find(o => o.name === e.target.value);
                        if(selected){
                            this.selectedDeliveryPrice = selected.price;
                            this.renderSummary();
                        }
                    });
                });
            },

            renderServiceOptions() {
                const container = document.querySelector(".service-section .service-options");
                container.innerHTML = "";

                this.services.forEach(service => {
                    const label = document.createElement("label");
                    label.classList.add("service-label");
                    label.innerHTML = `
                        <input type="checkbox" name="service" value="${service.name}" data-price="${service.price}">
                        ${service.label} (+${service.price})
                    `;
                    container.appendChild(label);
                });

                this.bindServiceEvents();
            },

            bindServiceEvents() {
                document.querySelectorAll('input[name="service"]').forEach(input => {
                    input.addEventListener("change", (e) => {
                        const price = parseFloat(e.target.dataset.price);
                        if(e.target.checked){
                            this.selectedServices.push({ name: e.target.value, price });
                        } else {
                            this.selectedServices = this.selectedServices.filter(s => s.name !== e.target.value);
                        }
                        this.renderSummary();
                    });
                });
            },

            init() {
                this.renderServiceOptions();
                this.renderCart();
                this.bindViewToggle();
            }
        };

        CartApp.init();
    </script>


</body>

</html>
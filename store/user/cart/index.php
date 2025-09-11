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
                <!-- <section>
                    <div id="timeline-container"></div>
                    <script src="<?php echo $GLOBALS['BASE_WEB']; ?>/js/user/timeLineBuilder.js?v=<?php echo time(); ?>"></script>
                </section> -->
                <div class="row">
                    <div class="col-md-8">
                        <section>
                            <div class="cart-items-section">
                                <div class="cart-view-toggle">
                                    <h2 class="cart-title"><i class="bi bi-cart3"></i> <span>ตะกร้าสินค้า</span></h2>
                                    <!-- <h5><i class="bi bi-cart3"></i> <span>ตะกร้าสินค้า</span></h5> -->
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
                            <div class="summary-section">
                                <h2 class="summary-title">สรุป</h2>
                                <div id="summaryDetails" class="summary-details"></div>
                                <button id="checkoutOrders" class="checkout-button">ไปที่การชำระเงิน</button>
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
    <?php include '../../template/footer-bar.php'; ?>

    <script>
        const CartApp = {
            cartItems: [{
                    id: 1,
                    name: "แทรนดาร์ เอเอ็มเอฟ เมอร์เคียว",
                    size: "",
                    price: 21.6,
                    qty: 4,
                    image: "https://www.trandar.com//public/shop_img/687a1a94a6f10_Trandar_AMF_Mercure.jpg"
                },
                {
                    id: 2,
                    name: "แทรนดาร์ เอเอ็มเอฟ ไฟน์ เฟรสโค",
                    size: "",
                    price: 24,
                    qty: 1,
                    image: "https://www.trandar.com//public/shop_img/687a1aa984ae2_Trandar_AMF_Fine_Fresko.jpg"
                },
                {
                    id: 3,
                    name: "แทรนดาร์ เอเอ็มเอฟ สตาร์",
                    size: "",
                    price: 33.25,
                    qty: 1,
                    image: "https://www.trandar.com//public/shop_img/687a1a756ce6a_Trandar_AMF_Star.jpg"
                },
                {
                    id: 4,
                    name: "แทรนดาร์ ทีบาร์ ที15",
                    size: "",
                    price: 3,
                    qty: 1,
                    image: "https://www.trandar.com//public/shop_img/687b2f5b393b2_497eeb6fc69f5635590f41fc078dff98.jpg"
                },
                {
                    id: 5,
                    name: "แทรนดาร์ ทีบาร์ ที24",
                    size: "",
                    price: 11.7,
                    qty: 1,
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
            deliveryOptionsData: {
                delivery: [{
                        name: "Lalamove",
                        price: 500
                    },
                    {
                        name: "รถบรรทุก 4 ล้อ",
                        price: 500
                    },
                    {
                        name: "รถบรรทุก 6 ล้อ",
                        price: 1000
                    }
                ],
                pickup: [{
                        name: "แทรนดาร์ อินเตอร์เนชั่นแนล",
                        price: 0
                    },
                    // { name: "สาขาเซ็นทรัล", price: 0 },
                    // { name: "สาขาเอ็มควอเทียร์", price: 0 }
                ]
            },
            appliedCoupon: null,
            viewMode: "list",
            selectedShippingName: "delivery",
            selectedDeliveryPrice: 50,
            selectedDeliveryName: "Lalamove",
            selectedServices: [],

            // --- saveToStorage()
            saveToStorage() {
                const data = {
                    cartItems: this.cartItems,
                    appliedCoupon: this.appliedCoupon,
                    selectedServices: this.selectedServices,
                    selectedDeliveryPrice: this.selectedDeliveryPrice,
                    selectedDeliveryName: this.selectedDeliveryName,
                    selectedShippingName: this.selectedShippingName, // เก็บ shipping
                    viewMode: this.viewMode,
                    shipping: this.shipping
                };
                localStorage.setItem("cartAppData", JSON.stringify(data));
            },

            // --- loadFromStorage()
            loadFromStorage() {
                const data = localStorage.getItem("cartAppData");
                if (data) {
                    try {
                        const parsed = JSON.parse(data);
                        this.cartItems = parsed.cartItems || this.cartItems;
                        this.appliedCoupon = parsed.appliedCoupon || null;
                        this.selectedServices = parsed.selectedServices || [];
                        this.selectedDeliveryPrice = parsed.selectedDeliveryPrice ?? 50;
                        this.selectedDeliveryName = parsed.selectedDeliveryName || "Lalamove";
                        this.selectedShippingName = parsed.selectedShippingName || "delivery"; // โหลด shipping
                        this.viewMode = parsed.viewMode || "list";
                        this.shipping = parsed.shipping || this.shipping;
                    } catch (e) {
                        console.error("โหลด localStorage ผิดพลาด", e);
                    }
                }
            },

            // Render cart items
            renderCart() {
                const list = document.getElementById("cartItemList");
                list.innerHTML = "";
                list.className = `cart-item-list ${this.viewMode}-mode`;

                this.cartItems.forEach(item => {
                    const div = document.createElement("div");
                    div.className = "cart-item";

                    if (this.viewMode === "list") {
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
                this.renderShipping(); // Render shipping radio
                this.renderDeliveryOptions(); // Render delivery options
                this.renderServiceOptions();
            },

            bindEvents() {
                document.querySelectorAll(".quantity-button").forEach(btn => {
                    btn.addEventListener("click", () => {
                        const id = parseInt(btn.dataset.id);
                        const action = btn.dataset.action;
                        const item = this.cartItems.find(i => i.id === id);
                        if (!item) return;
                        if (action === "increase") item.qty++;
                        if (action === "decrease" && item.qty > 1) item.qty--;
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

                document.getElementById("checkoutOrders").addEventListener("click", () => {
                    this.saveToStorage();
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

                    // ถ้าเป็น coupon ที่ใช้อยู่ ให้แสดง active
                    if (this.appliedCoupon && this.appliedCoupon.code === coupon.code) {
                        btn.classList.add("active");
                    }

                    btn.innerText = coupon.label;
                    btn.addEventListener("click", () => this.applyCoupon(coupon.code));
                    couponContainer.appendChild(btn);
                });

                // แสดงข้อความคูปอง
                const msg = document.getElementById("discountMessage");
                if (this.appliedCoupon) {
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
                    this.appliedCoupon = null;
                    msg.innerText = "คูปองไม่ถูกต้อง";
                    msg.style.color = "red";
                }
                this.saveToStorage(); // ✅ บันทึกไป localStorage ทุกครั้ง
                this.renderCouponList();
                this.renderSummary();
            },

            renderSummary() {
                let subtotal = this.cartItems.reduce((sum, item) => sum + item.qty * item.price, 0);
                let shipping = 0;
                const deliveryMethod = document.querySelector('input[name="shipping"]:checked')?.value || "delivery";
                if (deliveryMethod === "delivery") shipping = this.selectedDeliveryPrice;

                let discount = 0;
                if (this.appliedCoupon) {
                    if (this.appliedCoupon.type === "percent") discount = subtotal * (this.appliedCoupon.value / 100);
                    else if (this.appliedCoupon.type === "fixed") discount = this.appliedCoupon.value;
                    else if (this.appliedCoupon.type === "shipping") shipping = 0;
                }

                const serviceFee = this.selectedServices.reduce((sum, s) => sum + s.price, 0);
                const tax = (subtotal - discount + shipping + serviceFee) * 0.07;
                const total = subtotal - discount + shipping + serviceFee + tax;

                document.getElementById("summaryDetails").innerHTML = `
                    <div class="summary-row"><span>รวม</span><span>${subtotal.toFixed(2)}</span></div>
                    <div class="summary-row"><span>ส่วนลด</span><span>-${discount.toFixed(2)}</span></div>
                    <div class="summary-row"><span>จัดส่ง</span><span>${shipping.toFixed(2)}</span></div>
                    <div class="summary-row"><span>บริการเสริม</span><span>${serviceFee.toFixed(2)}</span></div>
                    <div class="summary-row"><span>ภาษามูลค่าเพิ่ม 7%</span><span>${tax.toFixed(2)}</span></div>
                    <div class="summary-row summary-subtotal"><span>ทั้งหมด</span><span>${total.toFixed(2)}</span></div>
                `;
            },

            // --- renderShipping()
            renderShipping() {
                const container = document.getElementById("shipping");
                container.innerHTML = "";

                this.shipping.forEach(item => {
                    const checked = this.selectedShippingName === item.value ? "checked" : "";
                    const label = document.createElement("label");
                    label.classList.add("shipping-label");
                    label.innerHTML = `
                        <input type="radio" name="shipping" value="${item.value}" ${checked}>
                        ${item.label}
                    `;
                    container.appendChild(label);
                });

                // bind change
                document.querySelectorAll('input[name="shipping"]').forEach(radio => {
                    radio.addEventListener("change", (e) => {
                        this.selectedShippingName = e.target.value; // อัพเดทค่า
                        this.saveToStorage(); // บันทึกทุกครั้ง
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

                    // ถ้า localStorage มีชื่อ selectedDeliveryName ให้เช็คตัวนั้น
                    const checked = this.selectedDeliveryName === opt.name ? "checked" : (idx === 0 && !this.selectedDeliveryName ? "checked" : "");
                    
                    label.innerHTML = `
                        <input type="radio" name="deliveryOption" value="${opt.name}" ${checked}>
                        ${opt.name} (+${opt.price})
                    `;
                    container.appendChild(label);
                });

                // set selectedDeliveryPrice ตาม checked
                const selectedOption = options.find(o => o.name === this.selectedDeliveryName) || options[0];
                this.selectedDeliveryPrice = selectedOption.price;
                this.selectedDeliveryName = selectedOption.name;

                document.querySelectorAll('input[name="deliveryOption"]').forEach(input => {
                    input.addEventListener("change", (e) => {
                        const selected = options.find(o => o.name === e.target.value);
                        if (selected) {
                            this.selectedDeliveryPrice = selected.price;
                            this.selectedDeliveryName = selected.name;
                            this.saveToStorage(); // บันทึกทุกครั้ง
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

                    // ถ้า localStorage มี selectedServices ให้ checked ตัวนั้น
                    const isChecked = this.selectedServices.some(s => s.name === service.name) ? "checked" : "";

                    label.innerHTML = `
                        <input type="checkbox" name="service" value="${service.name}" data-price="${service.price}" ${isChecked}>
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
                        if (e.target.checked) {
                            // เพิ่มลง selectedServices
                            if (!this.selectedServices.some(s => s.name === e.target.value)) {
                                this.selectedServices.push({ name: e.target.value, price });
                            }
                        } else {
                            this.selectedServices = this.selectedServices.filter(s => s.name !== e.target.value);
                        }
                        this.saveToStorage(); // บันทึกทุกครั้ง
                        this.renderSummary();
                    });
                });
            },

            init() {
                this.loadFromStorage();
                this.renderServiceOptions();
                this.renderCart();
                this.bindViewToggle();
            }
        };

        CartApp.init();
    </script>


</body>

</html>
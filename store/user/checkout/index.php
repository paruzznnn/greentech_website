<?php include '../../routes.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-STORE</title>
    <?php include '../../inc-meta.php'; ?>
    <link href="../../css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../../css/user/template-checkout.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../../inc-cdn.php'; ?>
</head>

<body>

    <?php include '../../template/head-bar.php'; ?>
    <main>
        <div id="sections_root_checkout" class="section-space">
            <div class="container">
                <div class="row">
                    <div class="col-md-8">
                        <div id="accordion-items" class="accordion-section"></div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkout-card">
                            <h5>รายละเอียดคำสั่งซื้อ</h5>
                            <div id="order-details"></div>
                        </div>
                        <div class="summary-card" id="order-summary">
                            <h5>สรุปคำสั่งซื้อ</h5>
                            <div id="summary-items"></div>
                            <!-- <button id="confirmOrders" disabled>ยันยืนคำสั่งซื้อ</button> -->
                            <p class="terms-text">
                                การสั่งซื้อของคุณถือเป็นการยอมรับ
                                <a href="#" class="terms-link">ข้อกำหนดการให้บริการ</a> และ
                                <a href="#" class="terms-link">นโยบายความเป็นส่วนตัว</a>
                                ของเรา กรุณาตรวจสอบข้อมูลการสั่งซื้อให้ถูกต้อง
                                เวลาจัดส่งเป็นการประมาณการณ์และอาจมีการเปลี่ยนแปลงตามความพร้อมของสินค้า.
                            </p>
                            <button id="backCheckoutOrders">กลับตะกร้าสินค้า</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include '../../template/footer-bar.php'; ?>

    <!-- Modal: Add/Edit Address -->
    <div id="address-modal" class="store-modal">
        <div class="store-modal-content">
            <span class="modal-close store-close-modal">&times;</span>
            <h3 id="modal-title"><i class="bi bi-house"></i> <span>Add Address</span></h3>
            <form id="address-form">
                <input type="text" id="addr-title" class="form-input" placeholder="Title (Home/Office/etc)" required>
                <input type="text" id="addr-name" class="form-input" placeholder="Full Name" required>
                <input type="text" id="addr-line1" class="form-input" placeholder="Address Line" required>
                <input type="text" id="addr-phone" class="form-input" placeholder="Phone Number" required>

                <div style="margin-top:10px; text-align:right;">
                    <button type="button" id="modal-cancel">Cancel</button>
                    <button type="submit" id="modal-save">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- <script>
        const CheckoutApp = {
            accordions: [{
                    icon: '<i class="bi bi-person-vcard"></i>',
                    title: "ผู้รับสินค้า",
                    content: ""
                },
                {
                    icon: '<i class="bi bi-geo-alt"></i>',
                    title: "ที่อยู่จัดส่ง",
                    content: ""
                },
                {
                    icon: '<i class="bi bi-credit-card"></i>',
                    title: "วิธีการชำระเงิน",
                    content: ""
                },
                {
                    icon: '<i class="bi bi-pen"></i>',
                    title: "จดบันทึก",
                    content: ""
                }
            ],
            billing: {
                first_name: "กิตตินันท์ธนัช",
                last_name: "สีแก้วน้ำใส",
                phone_number: "0838945256"
            },
            addresses: [{
                title: "Home",
                name: "kitti",
                line1: "102 Pat.",
                phone: "0970727598",
                default: true
            }],
            cartItems: [],
            selectedShippingType: "delivery",
            selectedShippingOptions: {},
            selectedServices: [],
            appliedCoupon: {},
            summary: {},

            saveToStorage() {
                const checkoutData = {
                    billing: {
                        first_name: document.getElementById("first_name").value,
                        last_name: document.getElementById("last_name").value,
                        phone_number: document.getElementById("phone_number").value
                    },
                    addresses: this.selectedShippingType === "pickup" ? [] : this.addresses,
                    selectedShippingOptions: this.selectedShippingOptions,
                    selectedServices: this.selectedServices,
                    appliedCoupon: this.appliedCoupon,
                    paymentMethod: document.querySelector('input[name="payment"]:checked')?.value || null,
                    orderNotes: document.getElementById("order-notes").value,
                    cartItems: this.cartItems,
                    summary: this.summary
                };
                localStorage.setItem("checkoutAppData", JSON.stringify(checkoutData));
                const savedData = JSON.parse(localStorage.getItem("checkoutAppData"));
                this.sendOrderToServer(savedData);
            },

            sendOrderToServer(data) {
                fetch("https://your-server-api.com/orders", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify(data)
                    })
                    .then(res => res.json())
                    .then(response => {
                        console.log("Server response:", response);
                        alert("สั่งซื้อสำเร็จ! ข้อมูลในเครื่องถูกลบแล้ว");
                        // สามารถ redirect ไปหน้า Thank you page หรือ clear cart ได้
                        // ลบ localStorage หลังสั่งซื้อสำเร็จ
                        // localStorage.removeItem("checkoutAppData");
                    })
                    .catch(err => {
                        console.error("Error sending order:", err);
                        alert("เกิดข้อผิดพลาดในการส่งข้อมูลไป server");
                    });
            },

            loadFromStorage() {
                const data = localStorage.getItem("cartAppData");
                if (!data) return;
                try {
                    const parsed = JSON.parse(data);
                    this.cartItems = parsed.cartItems || this.cartItems;
                    this.selectedShippingType = parsed.selectedShippingType || "delivery";
                    this.appliedCoupon = parsed.appliedCoupon || {};
                    this.selectedServices = parsed.selectedServices || [];
                    this.selectedShippingOptions = parsed.selectedShippingOptions || {};
                    this.summary = parsed.summary || {};
                } catch (e) {
                    console.error("localStorage ผิดพลาด", e);
                }
            },

            renderAccordion() {
                const container = document.getElementById("accordion-items");
                let html = "";
                this.accordions.forEach((acc, i) => {
                    html += `
                    <div class="checkout-card">
                        <div class="checkout-step-header">${acc.icon} : ${acc.title}</div>
                        <div class="checkout-panel ${i === 0 ? 'active' : ''}" id="panel-${i}">${acc.content}</div>
                    </div>`;
                });
                container.innerHTML = html;
                // Step 1: Billing
                let billingHtml = `
                    <div>
                        <div class="row">
                            <div class="col-md-6"> 
                                <div class="form-group">
                                    <label for="first_name" class="form-label"><span>ชื่อ</span>:</label>
                                    <input type="text" id="first_name" class="form-input" value="${this.billing.first_name}" placeholder="" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="last_name" class="form-label"><span>นามสกุล</span>:</label>
                                    <input type="text" id="last_name" class="form-input" value="${this.billing.last_name}" placeholder="" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone_number" class="form-label"><span>เบอร์โทร</span>:</label>
                                    <input type="text" id="phone_number" class="form-input" value="${this.billing.phone_number}" placeholder="" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="error-message" id="error-0"></div>
                    <div class="step-buttons"><button class="next-btn" data-next="1" data-step="0">Next</button></div>
                `;
                document.getElementById("panel-0").innerHTML = billingHtml;
                // Step 2: Delivery
                let deliveryHtml = `
                    <div id="delivery-address-container"></div>
                    <div class="error-message" id="error-1"></div>
                    <div class="step-buttons">
                        <button class="back-btn" data-back="0">Back</button>
                        <button class="next-btn" data-next="2" data-step="1">Next</button>
                    </div>
                `;
                document.getElementById("panel-1").innerHTML = deliveryHtml;
                this.renderAddresses();
                // Step 3: Payment
                let paymentHtml = `
                    <div class="payment-methods">
                        <label><input type="radio" name="payment" value="card" required checked> Credit / Debit Card</label>
                        <label><input type="radio" name="payment" value="paypal"> PayPal</label>
                        <label><input type="radio" name="payment" value="cod"> Cash on Delivery</label>
                    </div>
                    <div class="error-message" id="error-2"></div>
                    <div class="step-buttons">
                        <button class="back-btn" data-back="1">Back</button>
                        <button class="next-btn" data-next="3" data-step="2">Next</button>
                    </div>
                `;
                document.getElementById("panel-2").innerHTML = paymentHtml;
                // Step 4: Notes
                let notesHtml = `
                    <textarea id="order-notes" class="form-input" placeholder="Additional notes (optional)"></textarea>
                    <div class="error-message" id="error-3"></div>
                    <div class="step-buttons">
                        <button class="back-btn" data-back="2">Back</button>
                        <button id="place-order-btn" data-step="3">Place Order</button>
                    </div>
                `;
                document.getElementById("panel-3").innerHTML = notesHtml;
                // Step navigation
                container.querySelectorAll(".next-btn").forEach(btn => {
                    btn.onclick = () => {
                        const step = parseInt(btn.dataset.step);
                        if (!this.validateStep(step)) return;
                        let next = parseInt(btn.dataset.next);
                        const totalSteps = this.accordions.length;
                        container.querySelectorAll(".checkout-panel").forEach(p => p.classList.remove("active"));
                        if (next >= totalSteps) {
                            document.getElementById("panel-3").classList.add("active");
                        } else {
                            document.getElementById("panel-" + next).classList.add("active");
                        }
                    };
                });
                container.querySelectorAll(".back-btn").forEach(btn => {
                    btn.onclick = () => {
                        let back = btn.dataset.back;
                        container.querySelectorAll(".checkout-panel").forEach(p => p.classList.remove("active"));
                        document.getElementById("panel-" + back).classList.add("active");
                    };
                });
                document.getElementById("place-order-btn").onclick = () => {
                    const step = 3;
                    if (!this.validateStep(step)) return;
                    this.saveToStorage();
                };
            },

            validateStep(stepIndex) {
                const panel = document.getElementById(`panel-${stepIndex}`);
                const errorBox = document.getElementById(`error-${stepIndex}`);
                const inputs = panel.querySelectorAll("input[required], textarea[required]");
                let valid = true;
                errorBox.innerText = ""; // clear error
                inputs.forEach(input => {
                    if (!input.value.trim()) {
                        input.style.border = "1px solid red";
                        valid = false;
                    } else {
                        input.style.border = "";
                    }
                });
                if (!valid) {
                    errorBox.innerText = "กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน";
                }
                return valid;
            },

            renderAddresses() {
                const container = document.getElementById("delivery-address-container");
                container.innerHTML = "";
                if (this.selectedShippingType === "pickup") {
                    container.innerHTML = `<p><strong>Pickup at store branch</strong></p>`;
                    return;
                }
                const grid = document.createElement("div");
                grid.classList.add("checkout-address-card-grid");
                this.addresses.forEach((addr, i) => {
                    const card = document.createElement("div");
                    card.className = "checkout-address-card" + (addr.default ? " active" : "");
                    card.innerHTML = `
                        <div class="address-header">
                            <h3>${addr.title}</h3>
                            <div style="display: flex; gap: 10px;">
                                <span class="label-text">ค่าเริ่มต้น</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" class="set-default-toggle" ${addr.default ? "checked" : ""}/>
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                        <p>${addr.name}</p>
                        <p>${addr.line1}</p>
                        <p>${addr.phone}</p>
                        <div class="checkout-address-actions">
                            <button class="edit-btn"><i class="bi bi-pencil-square"></i></button>
                            <button class="delete-btn"><i class="bi bi-trash3"></i></button>
                        </div>
                    `;
                    card.addEventListener("click", (e) => {
                        if (e.target.tagName === "BUTTON" || e.target.closest(".toggle-switch")) return;
                        this.addresses.forEach(a => a.selected = false);
                        this.addresses[i].selected = true;
                        grid.querySelectorAll(".checkout-address-card").forEach(c => c.classList.remove("active"));
                        card.classList.add("active");
                    });
                    card.querySelector(".edit-btn").addEventListener("click", () => {
                        this.openAddressModal(addr, i);
                    });
                    card.querySelector(".delete-btn").addEventListener("click", () => {
                        if (confirm("Are you sure to delete this address?")) {
                            this.addresses.splice(i, 1);
                            this.renderAddresses();
                        }
                    });
                    card.querySelector(".set-default-toggle").addEventListener("change", (e) => {
                        if (e.target.checked) {
                            this.addresses.forEach(a => a.default = false);
                            addr.default = true;
                            this.renderAddresses();
                        }
                    });
                    grid.appendChild(card);
                });

                const addBtn = document.createElement("button");
                addBtn.id = "add-address-btn";
                addBtn.innerHTML = `<i class="bi bi-house-add"></i> <span>เพิ่มที่อยู่</span>`;
                grid.appendChild(addBtn);
                container.appendChild(grid);
                document.getElementById("add-address-btn").addEventListener("click", () => {
                    this.openAddressModal();
                });
            },

            openAddressModal(addr = null, index = null) {
                const modal = document.getElementById("address-modal");
                const title = document.getElementById("modal-title");
                const form = document.getElementById("address-form");
                if (addr) {
                    title.innerText = "Edit Address";
                    document.getElementById("addr-title").value = addr.title;
                    document.getElementById("addr-name").value = addr.name;
                    document.getElementById("addr-line1").value = addr.line1;
                    document.getElementById("addr-phone").value = addr.phone;
                } else {
                    title.innerText = "Add Address";
                    form.reset();
                }
                modal.style.display = "flex";
                form.onsubmit = (e) => {
                    e.preventDefault();
                    const newAddr = {
                        title: document.getElementById("addr-title").value,
                        name: document.getElementById("addr-name").value,
                        line1: document.getElementById("addr-line1").value,
                        phone: document.getElementById("addr-phone").value,
                        default: addr ? addr.default : false
                    };
                    if (addr) {
                        this.addresses[index] = newAddr;
                    } else {
                        this.addresses.push(newAddr);
                    }
                    this.renderAddresses();
                    modal.style.display = "none";
                };
                document.getElementById("modal-cancel").onclick = () => modal.style.display = "none";
                document.querySelector(".modal-close").onclick = () => modal.style.display = "none";
            },

            renderOrderDetails() {
                const container = document.getElementById("order-details");
                if (!container) return;
                let html = `
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>สินค้า</th>
                            <th>จำนวน</th>
                            <th>ราคา</th>
                        </tr>
                    </thead>
                    <tbody>`;
                const items = Array.isArray(this.cartItems) ? this.cartItems : [];
                let totalPrice = 0;
                if (items.length > 0) {
                    items.forEach(item => {
                        const itemTotal = (item.price || 0) * (item.qty || 0);
                        totalPrice += itemTotal;
                        html += `
                        <tr>
                            <td class="product-name">${item.name || "-"}</td>
                            <td class="text-center">${item.qty || 0}</td>
                            <td class="text-right">${itemTotal.toFixed(2)}</td>
                        </tr>`;
                    });
                } else {
                    html += `<tr><td colspan="3" class="text-center">ไม่มีสินค้าในตะกร้า</td></tr>`;
                }
                html += `</tbody>`;
                html += `
                <tfoot>
                    <tr>
                        <td colspan="2"><strong>รวม</strong></td>
                        <td class="text-right"><strong>${totalPrice.toFixed(2)}</strong></td>
                    </tr>
                </tfoot>
                </table>`;
                const services = Array.isArray(this.selectedServices) ? this.selectedServices : [];
                html += `<div class="order-section"><strong>บริการเสริม:</strong> `;
                html += services.length > 0 ? services.map(s => s.label || s.name).join(", ") : "ไม่มีบริการเสริม";
                html += `</div>`;
                html += `<div class="order-section"><strong>คูปอง:</strong> `;
                html += this.appliedCoupon && this.appliedCoupon.label ? this.appliedCoupon.label : "ไม่มีคูปองส่วนลด";
                html += `</div>`;
                html += `<div class="order-section"><strong>การจัดส่ง:</strong> `;
                if (this.selectedShippingOptions) {
                    html += `${this.selectedShippingOptions.name} ${this.appliedCoupon?.type === 'shipping' ? '' : '(+' + this.selectedShippingOptions.price + ')'}`;
                } else {
                    html += "ไม่มีการจัดส่ง";
                }
                html += `</div>`;
                container.innerHTML = html;
            },

            renderSummary() {
                const summaryItems = document.getElementById("summary-items");
                if (!summaryItems) return;
                if (!this.summary) {
                    summaryItems.innerHTML = `<div class="summary-row">ไม่มีข้อมูลสรุป</div>`;
                    return;
                }
                const {
                    subtotal = 0, discount = 0, shipping = 0, serviceFee = 0, tax = 0, total = 0
                } = this.summary;
                summaryItems.innerHTML = `
                    <div class="summary-row"><span>รวม</span><span>${subtotal.toFixed(2)}</span></div>
                    <div class="summary-row"><span>ส่วนลด</span><span>-${discount.toFixed(2)}</span></div>
                    <div class="summary-row"><span>จัดส่ง</span><span>${shipping.toFixed(2)}</span></div>
                    <div class="summary-row"><span>บริการเสริม</span><span>${serviceFee.toFixed(2)}</span></div>
                    <div class="summary-row"><span>ภาษามูลค่าเพิ่ม 7%</span><span>${tax.toFixed(2)}</span></div>
                    <div class="summary-row total"><span>ทั้งหมด</span><span>${total.toFixed(2)}</span></div>
                `;
            },

            init() {
                this.loadFromStorage();
                this.renderAccordion();
                this.renderOrderDetails();
                this.renderSummary();

                // ===== Reload =====
                window.addEventListener("storage", (e) => {
                    if (e.key === "cartAppData") {
                        this.loadFromStorage();
                        this.renderOrderDetails();
                        this.renderSummary();
                    }
                });
            }
        };
        document.addEventListener("DOMContentLoaded", () => {
            CheckoutApp.init();
        });
    </script> -->

    <script>
        const CheckoutApp = {
            accordions: [{
                    icon: '<i class="bi bi-person-vcard"></i>',
                    title: "ผู้รับสินค้า",
                    content: ""
                },
                {
                    icon: '<i class="bi bi-geo-alt"></i>',
                    title: "ที่อยู่จัดส่ง",
                    content: ""
                },
                {
                    icon: '<i class="bi bi-credit-card"></i>',
                    title: "วิธีการชำระเงิน",
                    content: ""
                },
                {
                    icon: '<i class="bi bi-pen"></i>',
                    title: "จดบันทึก",
                    content: ""
                }
            ],

            billing: {
                first_name: "กิตตินันท์ธนัช",
                last_name: "สีแก้วน้ำใส",
                phone_number: "0838945256"
            },

            addresses: [{
                title: "Home",
                name: "kitti",
                line1: "102 Pat.",
                phone: "0970727598",
                default: true
            }],

            cartItems: [],
            selectedShippingType: "delivery",
            selectedShippingOptions: {},
            selectedServices: [],
            appliedCoupon: {},
            summary: {},

            // 🔹 เก็บ index ของ address ที่เลือกไว้
            selectedAddressIndex: null,

            saveToStorage() {
                const checkoutData = {
                    billing: {
                        first_name: document.getElementById("first_name").value,
                        last_name: document.getElementById("last_name").value,
                        phone_number: document.getElementById("phone_number").value
                    },
                    // 🔹 ใช้ selectedAddressIndex
                    addresses: this.selectedShippingType === "pickup" ?
                        [] :
                        (this.selectedAddressIndex !== null ? [this.addresses[this.selectedAddressIndex]] : []),
                    selectedShippingOptions: this.selectedShippingOptions,
                    selectedServices: this.selectedServices,
                    appliedCoupon: this.appliedCoupon,
                    paymentMethod: document.querySelector('input[name="payment"]:checked')?.value || null,
                    orderNotes: document.getElementById("order-notes").value,
                    cartItems: this.cartItems,
                    summary: this.summary
                };
                localStorage.setItem("checkoutAppData", JSON.stringify(checkoutData));
                const savedData = JSON.parse(localStorage.getItem("checkoutAppData"));
                this.sendOrderToServer(savedData);
            },

            sendOrderToServer(data) {
                fetch("https://your-server-api.com/orders", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify(data)
                    })
                    .then(res => res.json())
                    .then(response => {
                        console.log("Server response:", response);
                        alert("สั่งซื้อสำเร็จ! ข้อมูลในเครื่องถูกลบแล้ว");
                        // localStorage.removeItem("checkoutAppData");
                    })
                    .catch(err => {
                        console.error("Error sending order:", err);
                        alert("เกิดข้อผิดพลาดในการส่งข้อมูลไป server");
                    });
            },

            loadFromStorage() {
                const data = localStorage.getItem("cartAppData");
                if (!data) return;
                try {
                    const parsed = JSON.parse(data);
                    this.cartItems = parsed.cartItems || this.cartItems;
                    this.selectedShippingType = parsed.selectedShippingType || "delivery";
                    this.appliedCoupon = parsed.appliedCoupon || {};
                    this.selectedServices = parsed.selectedServices || [];
                    this.selectedShippingOptions = parsed.selectedShippingOptions || {};
                    this.summary = parsed.summary || {};
                } catch (e) {
                    console.error("localStorage ผิดพลาด", e);
                }
            },

            renderAccordion() {
                const container = document.getElementById("accordion-items");
                let html = "";
                this.accordions.forEach((acc, i) => {
                    html += `
                <div class="checkout-card">
                    <div class="checkout-step-header">${acc.icon} : ${acc.title}</div>
                    <div class="checkout-panel ${i === 0 ? 'active' : ''}" id="panel-${i}">${acc.content}</div>
                </div>`;
                });
                container.innerHTML = html;

                // Step 1: Billing
                document.getElementById("panel-0").innerHTML = `
                <div>
                    <div class="row">
                        <div class="col-md-6"> 
                            <div class="form-group">
                                <label for="first_name" class="form-label">ชื่อ:</label>
                                <input type="text" id="first_name" class="form-input" value="${this.billing.first_name}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="last_name" class="form-label">นามสกุล:</label>
                                <input type="text" id="last_name" class="form-input" value="${this.billing.last_name}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone_number" class="form-label">เบอร์โทร:</label>
                                <input type="text" id="phone_number" class="form-input" value="${this.billing.phone_number}" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="error-message" id="error-0"></div>
                <div class="step-buttons"><button class="next-btn" data-next="1" data-step="0">Next</button></div>
            `;

                // Step 2: Delivery
                document.getElementById("panel-1").innerHTML = `
                <div id="delivery-address-container"></div>
                <div class="error-message" id="error-1"></div>
                <div class="step-buttons">
                    <button class="back-btn" data-back="0">Back</button>
                    <button class="next-btn" data-next="2" data-step="1">Next</button>
                </div>
            `;
                this.renderAddresses();

                // Step 3: Payment
                document.getElementById("panel-2").innerHTML = `
                <div class="payment-methods">
                    <label><input type="radio" name="payment" value="card" required checked> Credit / Debit Card</label>
                    <label><input type="radio" name="payment" value="paypal"> PayPal</label>
                    <label><input type="radio" name="payment" value="cod"> Cash on Delivery</label>
                </div>
                <div class="error-message" id="error-2"></div>
                <div class="step-buttons">
                    <button class="back-btn" data-back="1">Back</button>
                    <button class="next-btn" data-next="3" data-step="2">Next</button>
                </div>
            `;

                // Step 4: Notes
                document.getElementById("panel-3").innerHTML = `
                <textarea id="order-notes" class="form-input" placeholder="Additional notes (optional)"></textarea>
                <div class="error-message" id="error-3"></div>
                <div class="step-buttons">
                    <button class="back-btn" data-back="2">Back</button>
                    <button id="place-order-btn" data-step="3">Place Order</button>
                </div>
            `;

                // Step navigation
                container.querySelectorAll(".next-btn").forEach(btn => {
                    btn.onclick = () => {
                        const step = parseInt(btn.dataset.step);
                        if (!this.validateStep(step)) return;
                        let next = parseInt(btn.dataset.next);
                        container.querySelectorAll(".checkout-panel").forEach(p => p.classList.remove("active"));
                        document.getElementById("panel-" + next).classList.add("active");
                    };
                });

                container.querySelectorAll(".back-btn").forEach(btn => {
                    btn.onclick = () => {
                        let back = btn.dataset.back;
                        container.querySelectorAll(".checkout-panel").forEach(p => p.classList.remove("active"));
                        document.getElementById("panel-" + back).classList.add("active");
                    };
                });

                document.getElementById("place-order-btn").onclick = () => {
                    if (!this.validateStep(3)) return;
                    this.saveToStorage();
                };
            },

            validateStep(stepIndex) {
                const panel = document.getElementById(`panel-${stepIndex}`);
                const errorBox = document.getElementById(`error-${stepIndex}`);
                const inputs = panel.querySelectorAll("input[required], textarea[required]");
                let valid = true;
                errorBox.innerText = "";
                inputs.forEach(input => {
                    if (!input.value.trim()) {
                        input.style.border = "1px solid red";
                        valid = false;
                    } else {
                        input.style.border = "";
                    }
                });
                if (!valid) {
                    errorBox.innerText = "กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน";
                }
                return valid;
            },

            renderAddresses() {
                const container = document.getElementById("delivery-address-container");
                container.innerHTML = "";
                if (this.selectedShippingType === "pickup") {
                    container.innerHTML = `<p><strong>Pickup at store branch</strong></p>`;
                    return;
                }

                const grid = document.createElement("div");
                grid.classList.add("checkout-address-card-grid");

                // 🔹 ถ้า selectedAddressIndex ยังไม่ถูกเซ็ต → ใช้ค่า default
                if (this.selectedAddressIndex === null) {
                    const defaultIndex = this.addresses.findIndex(a => a.default);
                    this.selectedAddressIndex = defaultIndex !== -1 ? defaultIndex : 0;
                }

                this.addresses.forEach((addr, i) => {
                    const isActive = i === this.selectedAddressIndex;
                    const card = document.createElement("div");
                    card.className = "checkout-address-card" + (isActive ? " active" : "");
                    card.innerHTML = `
                    <div class="address-header">
                        <h3>${addr.title}</h3>
                        <div style="display: flex; gap: 10px;">
                            <span class="label-text">ค่าเริ่มต้น</span>
                            <label class="toggle-switch">
                                <input type="checkbox" class="set-default-toggle" ${addr.default ? "checked" : ""}/>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                    <p>${addr.name}</p>
                    <p>${addr.line1}</p>
                    <p>${addr.phone}</p>
                    <div class="checkout-address-actions">
                        <button class="edit-btn"><i class="bi bi-pencil-square"></i></button>
                        <button class="delete-btn"><i class="bi bi-trash3"></i></button>
                    </div>
                `;

                    // เลือกการ์ด
                    card.addEventListener("click", (e) => {
                        if (e.target.tagName === "BUTTON" || e.target.closest(".toggle-switch")) return;
                        this.selectedAddressIndex = i;
                        grid.querySelectorAll(".checkout-address-card").forEach(c => c.classList.remove("active"));
                        card.classList.add("active");
                    });

                    // edit
                    card.querySelector(".edit-btn").addEventListener("click", () => {
                        this.openAddressModal(addr, i);
                    });

                    // delete
                    card.querySelector(".delete-btn").addEventListener("click", () => {
                        if (confirm("Are you sure to delete this address?")) {
                            this.addresses.splice(i, 1);
                            if (this.selectedAddressIndex === i) this.selectedAddressIndex = null;
                            this.renderAddresses();
                        }
                    });

                    // set default
                    card.querySelector(".set-default-toggle").addEventListener("change", (e) => {
                        if (e.target.checked) {
                            this.addresses.forEach(a => a.default = false);
                            addr.default = true;
                            this.selectedAddressIndex = i;
                            this.renderAddresses();
                        }
                    });

                    grid.appendChild(card);
                });

                const addBtn = document.createElement("button");
                addBtn.id = "add-address-btn";
                addBtn.innerHTML = `<i class="bi bi-house-add"></i> <span>เพิ่มที่อยู่</span>`;
                grid.appendChild(addBtn);
                container.appendChild(grid);

                document.getElementById("add-address-btn").addEventListener("click", () => {
                    this.openAddressModal();
                });
            },

            openAddressModal(addr = null, index = null) {
                const modal = document.getElementById("address-modal");
                const title = document.getElementById("modal-title");
                const form = document.getElementById("address-form");
                if (addr) {
                    title.innerText = "Edit Address";
                    document.getElementById("addr-title").value = addr.title;
                    document.getElementById("addr-name").value = addr.name;
                    document.getElementById("addr-line1").value = addr.line1;
                    document.getElementById("addr-phone").value = addr.phone;
                } else {
                    title.innerText = "Add Address";
                    form.reset();
                }
                modal.style.display = "flex";
                form.onsubmit = (e) => {
                    e.preventDefault();
                    const newAddr = {
                        title: document.getElementById("addr-title").value,
                        name: document.getElementById("addr-name").value,
                        line1: document.getElementById("addr-line1").value,
                        phone: document.getElementById("addr-phone").value,
                        default: addr ? addr.default : false
                    };
                    if (addr) {
                        this.addresses[index] = newAddr;
                    } else {
                        this.addresses.push(newAddr);
                    }
                    this.renderAddresses();
                    modal.style.display = "none";
                };
                document.getElementById("modal-cancel").onclick = () => modal.style.display = "none";
                document.querySelector(".modal-close").onclick = () => modal.style.display = "none";
            },

            renderOrderDetails() {
                const container = document.getElementById("order-details");
                if (!container) return;
                let html = `
            <table class="order-table">
                <thead>
                    <tr>
                        <th>สินค้า</th>
                        <th>จำนวน</th>
                        <th>ราคา</th>
                    </tr>
                </thead>
                <tbody>`;
                const items = Array.isArray(this.cartItems) ? this.cartItems : [];
                let totalPrice = 0;
                if (items.length > 0) {
                    items.forEach(item => {
                        const itemTotal = (item.price || 0) * (item.qty || 0);
                        totalPrice += itemTotal;
                        html += `
                    <tr>
                        <td class="product-name">${item.name || "-"}</td>
                        <td class="text-center">${item.qty || 0}</td>
                        <td class="text-right">${itemTotal.toFixed(2)}</td>
                    </tr>`;
                    });
                } else {
                    html += `<tr><td colspan="3" class="text-center">ไม่มีสินค้าในตะกร้า</td></tr>`;
                }
                html += `</tbody>
            <tfoot>
                <tr>
                    <td colspan="2"><strong>รวม</strong></td>
                    <td class="text-right"><strong>${totalPrice.toFixed(2)}</strong></td>
                </tr>
            </tfoot>
            </table>`;
                container.innerHTML = html;
            },

            renderSummary() {
                const summaryItems = document.getElementById("summary-items");
                if (!summaryItems) return;
                if (!this.summary) {
                    summaryItems.innerHTML = `<div class="summary-row">ไม่มีข้อมูลสรุป</div>`;
                    return;
                }
                const {
                    subtotal = 0, discount = 0, shipping = 0, serviceFee = 0, tax = 0, total = 0
                } = this.summary;
                summaryItems.innerHTML = `
                <div class="summary-row"><span>รวม</span><span>${subtotal.toFixed(2)}</span></div>
                <div class="summary-row"><span>ส่วนลด</span><span>-${discount.toFixed(2)}</span></div>
                <div class="summary-row"><span>จัดส่ง</span><span>${shipping.toFixed(2)}</span></div>
                <div class="summary-row"><span>บริการเสริม</span><span>${serviceFee.toFixed(2)}</span></div>
                <div class="summary-row"><span>ภาษามูลค่าเพิ่ม 7%</span><span>${tax.toFixed(2)}</span></div>
                <div class="summary-row total"><span>ทั้งหมด</span><span>${total.toFixed(2)}</span></div>
            `;
            },

            init() {
                this.loadFromStorage();
                this.renderAccordion();
                this.renderOrderDetails();
                this.renderSummary();

                window.addEventListener("storage", (e) => {
                    if (e.key === "cartAppData") {
                        this.loadFromStorage();
                        this.renderOrderDetails();
                        this.renderSummary();
                    }
                });
            }
        };

        document.addEventListener("DOMContentLoaded", () => {
            CheckoutApp.init();
        });
    </script>


</body>

</html>
<?php include '../../routes.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Checkout</title>
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
                    <!-- Left: Steps -->
                    <div class="col-md-8">
                        <div id="accordion-items" class="accordion-section"></div>
                    </div>

                    <!-- Right: Order Details + Summary -->
                    <div class="col-md-4">
                        <div class="checkout-card" id="order-details-card">
                            <h5>รายละเอียดคำสั่งซื้อ</h5>
                            <div id="order-details-table"></div>
                        </div>

                        <div class="summary-card" id="order-summary">
                            <h5>สรุปคำสั่งซื้อ</h5>
                            <div id="summary-items"></div>
                            <div class="summary-row">
                                <span>Shipping:</span><span id="summary-shipping">$5.00</span>
                            </div>
                            <div class="summary-row total">
                                <span>Total:</span><span id="summary-total"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include '../../template/footer-bar.php'; ?>

    <!-- Modal: Add/Edit Address -->
    <div id="address-modal" class="modal">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h3 id="modal-title">Add Address</h3>
            <form id="address-form">
                <input type="text" id="addr-title" placeholder="Title (Home/Office/etc)" required>
                <input type="text" id="addr-name" placeholder="Full Name" required>
                <input type="text" id="addr-line1" placeholder="Address Line" required>
                <input type="text" id="addr-phone" placeholder="Phone Number" required>
                <div style="margin-top:10px; text-align:right;">
                    <button type="button" id="modal-cancel">Cancel</button>
                    <button type="submit" id="modal-save">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const CheckoutApp = {
            deliveryType: "delivery",
            addresses: [{
                title: "Home",
                name: "John Doe",
                line1: "123 Main St",
                phone: "0812345678",
                default: true
            }],
            cartItems: [],
            accordions: [{
                    title: "ผู้รับสินค้า",
                    content: ""
                },
                {
                    title: "ที่อยู่จัดส่ง",
                    content: ""
                },
                {
                    title: "วิธีการชำระเงิน",
                    content: ""
                },
                {
                    title: "จดบันทึก",
                    content: ""
                }
            ],

            // --- saveToStorage()
            saveToStorage() {
                // const data = {
                //     cartItems: this.cartItems,
                //     appliedCoupon: this.appliedCoupon,
                //     selectedServices: this.selectedServices,
                //     selectedDeliveryPrice: this.selectedDeliveryPrice,
                //     selectedDeliveryName: this.selectedDeliveryName,
                //     selectedShippingName: this.selectedShippingName, // เก็บ shipping
                //     viewMode: this.viewMode,
                //     shipping: this.shipping
                // };
                // localStorage.setItem("checkoutAppData", JSON.stringify(data));
            },

            // --- loadFromStorage()
            loadFromStorage() {
                const data = localStorage.getItem("cartAppData");
                if (data) {
                    try {
                        const parsed = JSON.parse(data);
                        console.log('parsed', parsed);
                        
                        this.cartItems = parsed.cartItems || this.cartItems;
                        // this.appliedCoupon = parsed.appliedCoupon || null;
                        // this.selectedServices = parsed.selectedServices || [];
                        // this.selectedDeliveryPrice = parsed.selectedDeliveryPrice ?? 50;
                        // this.selectedDeliveryName = parsed.selectedDeliveryName || "Lalamove";
                        // this.selectedShippingName = parsed.selectedShippingName || "delivery"; // โหลด shipping
                        // this.viewMode = parsed.viewMode || "list";
                        // this.shipping = parsed.shipping || this.shipping;
                    } catch (e) {
                        console.error("โหลด localStorage ผิดพลาด", e);
                    }
                }
            },

            renderAccordion() {
                const container = document.getElementById("accordion-items");
                let html = "";
                this.accordions.forEach((acc, i) => {
                    html += `
                    <div class="checkout-card">
                        <div class="checkout-step-header">${i+1}: ${acc.title}</div>
                        <div class="checkout-panel ${i === 0 ? 'active' : ''}" id="panel-${i}">${acc.content}</div>
                    </div>`;
                });
                container.innerHTML = html;

                // Step 1: Billing
                let billingHtml = `
                    <div id="billing-form" class="billing-form">
                        <input type="text" id="billing-name" placeholder="Full Name" required>
                        <input type="text" id="billing-line1" placeholder="Billing Address Line" required>
                        <input type="text" id="billing-phone" placeholder="Phone Number" required>
                    </div>
                    <div class="error-message" id="error-0"></div>
                    <div class="step-buttons"><button class="next-btn" data-next="1" data-step="0">Next</button></div>
                `;
                document.getElementById("panel-0").innerHTML = billingHtml;

                // Step 2: Delivery
                let deliveryHtml = `
                    <div>
                        <button id="add-address-btn">Add New Address</button>
                    </div>
                    <div id="delivery-address-container"></div>
                    <div class="error-message" id="error-1"></div>
                    <div class="step-buttons">
                        <button class="back-btn" data-back="0">Back</button>
                        <button class="next-btn" data-next="2" data-step="1">Next</button>
                    </div>
                `;
                document.getElementById("panel-1").innerHTML = deliveryHtml;
                this.renderAddresses();

                document.getElementById("add-address-btn").addEventListener("click", () => {
                    this.openAddressModal();
                });

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
                    <textarea id="order-notes" placeholder="Additional notes (optional)" style="width:100%; height:80px; margin-bottom:10px;"></textarea>
                    <div class="error-message" id="error-3"></div>
                    <div class="step-buttons">
                        <button class="back-btn" data-back="2">Back</button>
                        <button class="next-btn" data-next="4" data-step="3">Place Order</button>
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
                            document.getElementById("panel-0").classList.add("active");
                            console.log("🔄 กลับไป Step 1");
                        } else {
                            document.getElementById("panel-" + next).classList.add("active");
                            console.log("➡️ ไป Step " + (next + 1));
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
                    errorBox.innerText = "⚠️ กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน";
                }
                return valid;
            },

            // Address & Modal เหมือนเดิม
            renderAddresses() {
                const container = document.getElementById("delivery-address-container");
                container.innerHTML = "";

                if (this.deliveryType === "pickup") {
                    container.innerHTML = `<p><strong>Pickup at store branch</strong></p>`;
                    return;
                }

                const grid = document.createElement("div");
                grid.classList.add("checkout-address-card-grid");

                this.addresses.forEach((addr, i) => {
                    const card = document.createElement("div");
                    card.className = "checkout-address-card" + (addr.default ? " active" : "");
                    card.innerHTML = `
                        <h3>${addr.title}</h3>
                        <p>${addr.name}</p>
                        <p>${addr.line1}</p>
                        <p>${addr.phone}</p>
                        <div class="checkout-address-actions">
                            <button class="edit-btn">Edit</button>
                            <button class="delete-btn">Delete</button>
                            <button class="set-default-btn" ${addr.default?'disabled':''}>${addr.default?'Default':'Set Default'}</button>
                        </div>
                    `;

                    card.addEventListener("click", (e) => {
                        if (e.target.tagName === "BUTTON") return;
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
                    card.querySelector(".set-default-btn").addEventListener("click", () => {
                        this.addresses.forEach(a => a.default = false);
                        addr.default = true;
                        this.renderAddresses();
                    });

                    grid.appendChild(card);
                });

                container.appendChild(grid);
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
                const container = document.getElementById("order-details-table");
                let html = `<table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="text-align:left;">สินค้า</th>
                        <th>จำนวน</th>
                        <th>รวม</th>
                    </tr>
                </thead>
                <tbody>`;
                this.cartItems.forEach(item => {
                    const itemTotal = item.price * item.qty;
                    html += `<tr>
                        <td style="text-align:left;">${item.name}</td>
                        <td style="text-align:center;">${item.qty}</td>
                        <td style="text-align:right;">$${itemTotal.toFixed(2)}</td>
                    </tr>`;
                });
                html += `</tbody></table>`;
                container.innerHTML = html;
            },

            updateSummary() {
                const summaryItems = document.getElementById("summary-items");
                summaryItems.innerHTML = "";
                let subtotal = 0;
                // this.cartItems.forEach(item => {
                //     const itemTotal = item.price * item.qty;
                //     subtotal += itemTotal;
                //     summaryItems.innerHTML += `<div class="summary-row"><span>${item.name} x ${item.qty}</span><span>$${itemTotal.toFixed(2)}</span></div>`;
                // });
                const shipping = 5;
                document.getElementById("summary-shipping").innerText = `$${shipping.toFixed(2)}`;
                document.getElementById("summary-total").innerText = `$${(subtotal + shipping).toFixed(2)}`;
            },

            init() {
                this.loadFromStorage();
                this.renderAccordion();
                this.renderOrderDetails();
                this.updateSummary();
            },

        };

        document.addEventListener("DOMContentLoaded", () => {
            CheckoutApp.init();
        });
    </script>

</body>

</html>
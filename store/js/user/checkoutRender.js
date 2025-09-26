import { redirectGet, redirectPostForm } from '../formHandler.js';


const CheckoutApp = {
    lang: 'th',
    cartItems: [],
    summary: {},
    billing: {
        first_name: "",
        last_name: "",
        phone_number: ""
    },
    addresses: [
        {
            detail: "102 ซอย พัฒนาการ 40",
            provinces: "กรุงเทพมหานคร",
            districts: "สวนหลวง",
            subdistricts: "สวนหลวง",
            postalCode: 10250,
            default: true
        }
    ],
    branches: [
        {
            value: "branch1",
            name: "แทรนดาร์ อินเตอร์เนชั่นแนล",
            detail: "102 ซอย พัฒนาการ 40",
            provinces: "กรุงเทพมหานคร",
            districts: "สวนหลวง",
            subdistricts: "สวนหลวง",
            postalCode: 10250,
            timeSlots: ["10:00 - 12:00", "13:00 - 15:00", "16:00 - 18:00"]
        },
        {
            value: "branch2",
            name: "แทรนดาร์ ร่มเกล้า",
            detail: "102 ซอย พัฒนาการ 40",
            provinces: "กรุงเทพมหานคร",
            districts: "วัฒนา",
            subdistricts: "คลองเตย",
            postalCode: 10110,
            timeSlots: ["11:00 - 13:00", "14:00 - 16:00"]
        },
    ],
    paymentMethods: [
        { label: 'โอนเงิน', value: 'bank_transfer', checked: true },
        { label: 'บัตรเครดิต', value: 'credit_card', checked: false }
    ],
    selectedShippingType: 'delivery',
    selectedAddressIndex: null,
    selectedShippingOptions: {},
    provincesData: [],
    districtsData: [],
    subdistrictsData: [],
    provinceActive: '',
    districtActive: '',

    accordions: [
        { icon: '<i class="bi bi-person-vcard"></i>', title: 'ข้อมูลผู้สั่งซื้อ', content: '' },
        { icon: '<i class="bi bi-geo-alt"></i>', title: 'ที่อยู่จัดส่ง', content: '' },
        { icon: '<i class="bi bi-credit-card"></i>', title: 'วิธีการชำระเงิน', content: '' },
        { icon: '<i class="bi bi-pen"></i>', title: 'บันทึกเพิ่มเติม', content: '' }
    ],

    /* ========== STORAGE ========== */
    saveToStorage() {
        const checkoutData = {
            billing: {
                first_name: document.getElementById("first_name").value,
                last_name: document.getElementById("last_name").value,
                phone_number: document.getElementById("phone_number").value
            },
            addresses: this.selectedShippingType === "pickup" ?
                [this.branches.find(branch => branch.value === this.selectedShippingOptions.value)] :
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

    async sendOrderToServer(data) {
        data.action = "payOrder";
        try {
            const response = await fetch(window.AppConfig.BASE_WEB + "service/user/checkout-action.php", {
                method: "POST",
                headers: {
                    'Authorization': 'Bearer my_secure_token_123',
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(data)
            });
            const responseData = await response.json();

            const savedData = JSON.parse(localStorage.getItem("checkoutAppData")) || {};
            savedData.order_id = responseData.order_id;
            localStorage.setItem("checkoutAppData", JSON.stringify(savedData));

            redirectGet(window.AppConfig.BASE_WEB + 'user/');
        } catch (err) {
            console.error("Error sending order:", err);
            alert("เกิดข้อผิดพลาดในการส่งข้อมูลไป server");
        }
    },

    /* ========== RENDER & EVENTS ========== */
    renderAll() {
        this.renderAccordion();
        this.renderOrderDetails();
        this.renderSummary();
    },

    bindEvents() {
        this.bindPopulate();
        this.bindbackCartEvents();
        this.bindEventsPlaceOrder();
    },

    renderAccordion() {
        const container = document.getElementById("accordion-items");
        if (!container) return;

        const html = this.accordions.map((acc, i) => `
            <div class="checkout-card">
                <div class="checkout-step-header">${acc.icon} : ${acc.title}</div>
                <div class="checkout-panel ${i === 0 ? 'active' : ''}" id="panel-${i}">
                    ${this.getPanelContent(i)}
                </div>
            </div>
        `).join('');

        container.innerHTML = html;
        this.bindPanelNavigation(container);
        this.renderAddresses();
    },

    getPanelContent(stepIndex) {
        switch (stepIndex) {
            case 0:
                return `
                    <div class="row">
                        <div class="col-md-6"><label>ชื่อ:</label><input type="text" id="first_name" class="form-input" value="${this.billing.first_name || ''}" required></div>
                        <div class="col-md-6"><label>นามสกุล:</label><input type="text" id="last_name" class="form-input" value="${this.billing.last_name || ''}" required></div>
                        <div class="col-md-6"><label>เบอร์โทร:</label><input type="text" id="phone_number" class="form-input" value="${this.billing.phone_number || ''}" required></div>
                    </div>
                    <div class="error-message" id="error-0"></div>
                    <div class="step-buttons"><button class="next-btn" data-next="1" data-step="0">Next</button></div>
                `;
            case 1:
                return `
                    <div id="delivery-address-container"></div>
                    <div class="error-message" id="error-1"></div>
                    <div class="step-buttons">
                        <button class="back-btn" data-back="0">Back</button>
                        <button class="next-btn" data-next="2" data-step="1">Next</button>
                    </div>
                `;
            case 2:
                return `
                    <div class="payment-methods">
                        ${this.paymentMethods.map(m => `<label><input type="radio" name="payment" value="${m.value}" ${m.checked ? "checked" : ""} required>${m.label}</label>`).join("")}
                    </div>
                    <div class="error-message" id="error-2"></div>
                    <div class="step-buttons">
                        <button class="back-btn" data-back="1">Back</button>
                        <button class="next-btn" data-next="3" data-step="2">Next</button>
                    </div>
                `;
            case 3:
                return `
                    <textarea id="order-notes" class="form-input" placeholder="จดบันทึกความจำ (ไม่บังคับ)"></textarea>
                    <div class="error-message" id="error-3"></div>
                    <div class="step-buttons">
                        <button class="back-btn" data-back="2">Back</button>
                        <button id="place-order-step" class="confirmOrders">ยืนยันการสั่งซื้อ</button>
                    </div>
                `;
            default:
                return '';
        }
    },

    bindPanelNavigation(container) {
        container.querySelectorAll(".next-btn").forEach(btn => {
            btn.onclick = () => {
                const step = parseInt(btn.dataset.step);
                if (!this.validateStep(step)) return;
                const next = parseInt(btn.dataset.next);
                container.querySelectorAll(".checkout-panel").forEach(p => p.classList.remove("active"));
                document.getElementById(`panel-${next}`).classList.add("active");
            };
        });

        container.querySelectorAll(".back-btn").forEach(btn => {
            btn.onclick = () => {
                const back = parseInt(btn.dataset.back);
                container.querySelectorAll(".checkout-panel").forEach(p => p.classList.remove("active"));
                document.getElementById(`panel-${back}`).classList.add("active");
            };
        });
    },

    validateStep(stepIndex) {
        const panel = document.getElementById(`panel-${stepIndex}`);
        const errorBox = document.getElementById(`error-${stepIndex}`);
        let valid = true;
        errorBox.innerText = "";

        switch (stepIndex) {
            case 0:
                const billingInputs = panel.querySelectorAll("#first_name, #last_name, #phone_number");
                billingInputs.forEach(input => {
                    if (!input.value.trim()) {
                        input.style.border = "1px solid red";
                        valid = false;
                    } else {
                        input.style.border = "";
                    }
                });
                if (!valid) {
                    errorBox.innerText = "กรุณากรอกข้อมูลชื่อ, นามสกุล และเบอร์โทร";
                }
                break;
            case 1:
                if (this.selectedShippingType === "delivery" && this.selectedAddressIndex === null) {
                    valid = false;
                    errorBox.innerText = "กรุณาเลือกที่อยู่จัดส่ง";
                } else if (this.selectedShippingType === "pickup" && (!this.selectedShippingOptions || !this.selectedShippingOptions.value)) {
                    valid = false;
                    errorBox.innerText = "กรุณาเลือกสาขาสำหรับรับสินค้า";
                }
                break;
            case 2:
                const paymentRadio = panel.querySelector('input[name="payment"]:checked');
                if (!paymentRadio) {
                    valid = false;
                    errorBox.innerText = "กรุณาเลือกวิธีการชำระเงิน";
                }
                break;
            default:
                valid = false;
        }
        return valid;
    },

    renderAddresses() {
        const container = document.getElementById("delivery-address-container");
        if (!container) return;
        container.innerHTML = "";

        if (this.selectedShippingType === "pickup") {
            const selectedBranch = this.branches.find(branch => branch.value === this.selectedShippingOptions.value);
            if (selectedBranch) {
                container.innerHTML = `
                    <div class="checkout-address-card active">
                        <div class="address-header">
                            <span class="label-text">รับสินค้าที่สาขา</span>
                        </div>
                        <p><strong>${selectedBranch.name}</strong></p>
                        <p>${selectedBranch.detail}</p>
                        <p>${selectedBranch.subdistricts} ${selectedBranch.districts} ${selectedBranch.provinces} ${selectedBranch.postalCode}</p>
                        <p><strong>ช่วงเวลาที่นัดรับ:</strong> ${this.selectedShippingOptions.timeSlot}</p>
                    </div>
                `;
            } else {
                container.innerHTML = `<p>ไม่พบข้อมูลสาขาที่เลือก</p>`;
            }
            return;
        }

        const grid = document.createElement("div");
        grid.className = "checkout-address-card-grid";

        if (this.selectedAddressIndex === null && this.addresses.length) {
            const defaultIndex = this.addresses.findIndex(a => a.default);
            this.selectedAddressIndex = defaultIndex !== -1 ? defaultIndex : 0;
        }

        this.addresses.forEach((addr, i) => {
            const card = document.createElement("div");
            card.className = `checkout-address-card${i === this.selectedAddressIndex ? " active" : ""}`;
            card.innerHTML = `
                <div class="address-header">
                    <div style="display:flex;gap:10px;">
                        <span class="label-text">จัดส่งตามที่อยู่นี้</span>
                        <label class="toggle-switch">
                            <input type="checkbox" class="set-default-toggle" ${addr.default ? "checked" : ""} ${this.addresses.length === 1 ? "disabled" : ""}/>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
                <p>${addr.detail}</p>
                <p>${addr.provinces} ${addr.districts} ${addr.subdistricts} ${addr.postalCode}</p>
                <div class="checkout-address-actions">
                    <button class="edit-btn"><i class="bi bi-pencil-square"></i></button>
                    <button class="delete-btn" ${this.addresses.length === 1 ? "disabled" : ""}><i class="bi bi-trash3"></i></button>
                </div>`;

            card.addEventListener("click", e => {
                if (e.target.tagName === "BUTTON" || e.target.closest(".toggle-switch")) return;
                this.selectedAddressIndex = i;
                grid.querySelectorAll(".checkout-address-card").forEach(c => c.classList.remove("active"));
                card.classList.add("active");
            });

            card.querySelector(".edit-btn").addEventListener("click", () => this.openAddressModal(addr, i));

            if (this.addresses.length > 1) {
                card.querySelector(".delete-btn").addEventListener("click", () => {
                    if (confirm("Are you sure to delete this address?")) {
                        this.addresses.splice(i, 1);
                        this.selectedAddressIndex = this.addresses.length ? 0 : null;
                        this.renderAddresses();
                    }
                });

                card.querySelector(".set-default-toggle").addEventListener("change", e => {
                    if (!e.target.checked) {
                        const checkedCount = this.addresses.filter(a => a.default).length;
                        if (checkedCount <= 1) {
                            e.target.checked = true;
                            return;
                        }
                    } else {
                        this.addresses.forEach(a => a.default = false);
                        addr.default = true;
                        this.selectedAddressIndex = i;
                        this.renderAddresses();
                    }
                });
            }

            grid.appendChild(card);
        });

        const addBtn = document.createElement("button");
        addBtn.id = "add-address-btn";
        addBtn.innerHTML = `<i class="bi bi-house-add"></i> <span>เพิ่มที่อยู่</span>`;
        addBtn.addEventListener("click", () => this.openAddressModal());
        grid.appendChild(addBtn);

        container.appendChild(grid);
    },

    openAddressModal(addr = null, index = null) {
        const modal = document.getElementById("address-modal");
        const form = document.getElementById("address-form");
        const provinceEl = document.getElementById("province");
        const districtEl = document.getElementById("district");
        const subdistrictEl = document.getElementById("subdistrict");
        const postalEl = document.getElementById("postalCode");

        form.reset();
        districtEl.disabled = true;
        subdistrictEl.disabled = true;

        if (this.provincesData.length > 0) {
            this.populateProvinces();
        } else {
            console.warn("Location data is not loaded yet.");
            return;
        }

        if (addr) {
            document.getElementById("address_detail").value = addr.detail || "";

            const provinceObj = this.provincesData.find(p => (this.lang === 'en' ? p.provinceNameEn : p.provinceNameTh) === addr.provinces);
            if (provinceObj) {
                provinceEl.value = provinceObj.provinceCode;
                this.provinceActive = provinceEl.value;
                this.populateDistricts();
                districtEl.disabled = false;
            }

            const districtObj = this.districtsData.find(d => (this.lang === 'en' ? d.districtNameEn : d.districtNameTh) === addr.districts);
            if (districtObj) {
                districtEl.value = districtObj.districtCode;
                this.districtActive = districtEl.value;
                this.populateSubDistricts();
                subdistrictEl.disabled = false;
            }

            const subObj = this.subdistrictsData.find(s => (this.lang === 'en' ? s.subdistrictNameEn : s.subdistrictNameTh) === addr.subdistricts);
            if (subObj) {
                subdistrictEl.value = subObj.subdistrictCode;
            }

            postalEl.value = addr.postalCode || "";
        }

        modal.style.display = "flex";

        form.onsubmit = e => {
            e.preventDefault();
            const newAddr = {
                detail: document.getElementById("address_detail").value,
                provinces: provinceEl.selectedOptions[0]?.text || "",
                districts: districtEl.selectedOptions[0]?.text || "",
                subdistricts: subdistrictEl.selectedOptions[0]?.text || "",
                postalCode: postalEl.value || "",
                default: addr ? addr.default : false
            };
            if (index !== null) {
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

    async loadData(dataType) {
        try {
            const response = await fetch(`${window.AppConfig.BASE_WEB}locales/${dataType}.json`, {
                method: "GET",
                headers: {
                    'Authorization': 'Bearer my_secure_token_123',
                    "Content-Type": "application/json"
                }
            });
            return await response.json();
        } catch (err) {
            console.error(`Error fetching ${dataType}:`, err);
            return [];
        }
    },

    populateDropdown(elementId, data, valueKey, textKey, defaultEn, defaultTh) {
        const element = document.getElementById(elementId);

        // Check if the dropdown is for subdistricts to add the postal code
        if (elementId === 'subdistrict') {
            const options = data.map(item =>
                `<option value="${item[valueKey]}" data-code="${item.postalCode}">${item[textKey]}</option>`
            );
            element.innerHTML = `<option value="">${this.lang === 'en' ? defaultEn : defaultTh}</option>` + options.join('');
        } else {
            const options = data.map(item =>
                `<option value="${item[valueKey]}">${item[textKey]}</option>`
            );
            element.innerHTML = `<option value="">${this.lang === 'en' ? defaultEn : defaultTh}</option>` + options.join('');
        }
    },

    populateProvinces() {
        this.populateDropdown('province', this.provincesData, 'provinceCode', this.lang === 'en' ? 'provinceNameEn' : 'provinceNameTh', 'Select Province', 'เลือกจังหวัด');
    },

    populateDistricts() {
        const filteredData = this.districtsData.filter(d => d.provinceCode == this.provinceActive);
        this.populateDropdown('district', filteredData, 'districtCode', this.lang === 'en' ? 'districtNameEn' : 'districtNameTh', 'Select District', 'เลือกอำเภอ/เขต');
    },

    populateSubDistricts() {
        const filteredData = this.subdistrictsData.filter(s => s.districtCode == this.districtActive);
        this.populateDropdown('subdistrict', filteredData, 'subdistrictCode', this.lang === 'en' ? 'subdistrictNameEn' : 'subdistrictNameTh', 'Select Subdistrict', 'เลือกตำบล/แขวง');
    },

    bindPopulate() {
        const province = document.getElementById("province");
        const district = document.getElementById("district");
        const subdistrict = document.getElementById("subdistrict");
        const postal = document.getElementById("postalCode");

        if (province) {
            province.onchange = e => {
                this.provinceActive = e.target.value;
                this.populateDistricts();
                district.value = "";
                subdistrict.value = "";
                if (postal) postal.value = "";
                district.disabled = !this.provinceActive;
                subdistrict.disabled = true;
            };
        }

        if (district) {
            district.onchange = e => {
                this.districtActive = e.target.value;
                this.populateSubDistricts();
                subdistrict.value = "";
                if (postal) postal.value = "";
                subdistrict.disabled = !this.districtActive;
            };
        }

        if (subdistrict) {
            subdistrict.onchange = e => {
                const option = e.target.selectedOptions[0];
                if (option && postal) postal.value = option.dataset.code || "";
            };
        }
    },

    renderOrderDetails() {
        const container = document.getElementById("order-details");
        if (!container) return;
        let html = `<table class="order-table"><thead><tr><th>สินค้า</th><th>จำนวน</th><th>ราคา</th></tr></thead><tbody>`;
        const items = Array.isArray(this.cartItems) ? this.cartItems : [];
        if (items.length > 0) items.forEach(item => {
            const itemTotal = (item.price || 0) * (item.qty || 0);
            html += `<tr><td>${item.name || '-'}</td><td>${item.qty || 0}</td><td>${itemTotal.toFixed(2)}</td></tr>`;
        });
        else html += `<tr><td colspan="3">ไม่มีสินค้าในตะกร้า</td></tr>`;
        html += `</tbody></table>`;
        container.innerHTML = html;
    },

    bindbackCartEvents() {
        document.getElementById("backCart").onclick = () => {
            redirectGet(window.AppConfig.BASE_WEB + 'user/cart/');
        };
    },

    renderSummary() {
        const summaryItems = document.getElementById("summary-items");
        if (!summaryItems) return;
        if (!this.summary) {
            summaryItems.innerHTML = '<div>ไม่มีข้อมูลสรุป</div>';
            return;
        }
        const { subtotal = 0, discount = 0, shipping = 0, service = 0, tax = 0, total = 0 } = this.summary;
        summaryItems.innerHTML = `
            <div class="summary-row"><span>รวม</span><span>${subtotal.toFixed(2)}</span></div>
            <div class="summary-row"><span>ส่วนลด</span><span>-${discount.toFixed(2)}</span></div>
            <div class="summary-row"><span>จัดส่ง</span><span>${shipping.toFixed(2)}</span></div>
            <div class="summary-row"><span>บริการเสริม</span><span>${service.toFixed(2)}</span></div>
            <div class="summary-row"><span>ภาษามูลค่าเพิ่ม 7%</span><span>${tax.toFixed(2)}</span></div>
            <div class="summary-row total"><span>ทั้งหมด</span><span>${total.toFixed(2)}</span></div>`;
    },

    bindEventsPlaceOrder() {
        const elements = document.querySelectorAll("#place-order-btn, #place-order-step");
        
        elements.forEach(element => {
            element.onclick = () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });

                const steps = ['Billing', 'Delivery', 'Payment'];
                for (let i = 0; i < steps.length; i++) {
                    if (!this.validateStep(i)) {
                        console.log(`Validation failed for Step ${i}: ${steps[i]}`);
                        document.getElementById(`panel-${i}`).classList.add("active");
                        return;
                    }
                }

                console.log("All steps are valid. Proceeding to save data and place order.");
                this.saveToStorage();
            };
        });
    },

    async init() {
        this.loadFromStorage();
        [this.provincesData, this.districtsData, this.subdistrictsData] = await Promise.all([
            this.loadData('provinces'),
            this.loadData('districts'),
            this.loadData('subdistricts')
        ]);

        this.renderAll();
        this.bindEvents();

        window.addEventListener("storage", (e) => {
            if (e.key === "cartAppData") {
                this.loadFromStorage();
                this.renderAll();
            }
        });
    }
};

document.addEventListener("DOMContentLoaded", () =>
    CheckoutApp.init()
);

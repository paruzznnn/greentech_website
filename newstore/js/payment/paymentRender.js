
import { formatDateToYYYYMMDD, formatPrice } from '../formHandler.js';

export async function fetchAddressData(req, call) {
    try {
        const params = new URLSearchParams({
            action: req
        });
        const url = call + params.toString();
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer my_secure_token_123',
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        return { data: [] };
    }
}

export async function fetchProvincesData(call) {
    try {
        const url = call;
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer my_secure_token_123',
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        return { data: [] };
    }
}

export async function fetchDistrictsData(call) {
    try {
        const url = call;
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer my_secure_token_123',
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        return { data: [] };
    }
}

export async function fetchSubdistricts(call) {
    try {
        const url = call;
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer my_secure_token_123',
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        return { data: [] };
    }
}

function generateQRPromptpay(phoneNumber, amount, url) {
    return fetch(url, {
        method: 'POST',
        headers: {
            'Authorization': 'Bearer my_secure_token_123',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            action: 'getQRPromptPay',
            phone: phoneNumber,
            amount: amount
        })
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            return data;
        })
        .catch(error => {
            console.error('Fetch error:', error);
            throw error;
        });
}

export const CheckoutUI = {
    lang: null,
    QRCodeUrl: null,
    shippingCost: 1000.00,
    ORDER_STORAGE_KEY: 'orderProduct',
    BankNumber: '3201137028',
    PromptPayNumber: '0988971593',
    totalPrice: null,
    countProduct: null,

    orderItems: [],
    addressData: [],
    provincesData: [],
    districtsData: [],
    subdistrictsData: [],

    provinceActive: null,
    districtActive: null,
    postalCodeActive: null,

    deliveryMethodNames: {
        shipping: 'จัดส่งถึงที่อยู่',
        pickup: 'รับหน้าร้าน'
    },

    paymentMethodNames: {
        bank_transfer: 'โอนเงินผ่านธนาคาร',
        promptpay: 'พร้อมเพย์',
        cod: 'เก็บเงินปลายทาง'
    },

    selectors: {
        shippingSection: document.getElementById('shippingAddressFormSection'),
        pickupSection: document.getElementById('pickupAddressFormSection'),
        bankSection: document.getElementById('bankTransferSection'),
        promptpaySection: document.getElementById('promptpaySection'),
        deliveryService: document.getElementById('deliveryService'),

        orderCode: document.getElementById('order-code'),
        orderDate: document.getElementById('order-date'),

        subtotal: document.getElementById('subtotal'),
        vatAmount: document.getElementById('vat-amount'),
        totalAmount: document.getElementById('total-amount'),

        shippingCostValue: document.getElementById('shipping-cost-value'),
        discountValue: document.getElementById('discount-value'),

        //====== INPUT FROM ======================================
        deliveryRadios: document.querySelectorAll('.delivery input[name="delivery_option"]'),
        paymentRadios: document.querySelectorAll('.payment input[name="payment_method"]'),
        orderInput: document.getElementById('order_id'),
        productInput: document.getElementById('product_item'),
        subtotalInput: document.getElementById('sub_total'),

        shippingAmountInput: document.getElementById('shipping_amount'),
        discountAmountInput: document.getElementById('discount_amount'),
        vatAmountInput: document.getElementById('vat_amount'),
        totalAmountInput: document.getElementById('total_amount'),

        selectedPaymentMethod: document.getElementById('selected-payment-method'),
        selectedDeliveryMethod: document.getElementById('selected-delivery-method'),

        //====== Default Shipping Address =================================
        selectedFullname: null,
        selectedPhoneNumber: null,
        selectedAddressDetail: null,

        selectedProvince: null,
        selectedDistrict: null,
        selectedSubdistrict: null,
        selectedPostalCode: null
    },

    init(provinces, districts, subdistricts, address, apiUrl) {

        this.lang = 'th';
        this.QRCodeUrl = apiUrl;
        this.loadOrder();

        this.radioActive();
        this.radioEvents();
        this.initEvents();

        this.renderProductItem();
        this.renderPayment();

        this.addressData = address;
        this.provincesData = provinces;
        this.districtsData = districts;
        this.subdistrictsData = subdistricts;

        this.populateProvinces();
        this.populateDistricts();
        this.populateSubDistricts();

    },

    loadOrder() {
        const storedOrder = localStorage.getItem(this.ORDER_STORAGE_KEY);
        if (storedOrder) {
            this.orderItems = JSON.parse(storedOrder);
        }
    },

    renderProductItem() {
        const orderItems = this.orderItems?.items || [];
        let productHtml = '';
        orderItems.forEach(item => {
            productHtml += `
            <div class="product-item">
                <div>
                    <img src="${item.imageUrl}" alt="" class="product-img">
                </div>
                <div style="text-align: end;">${formatPrice("THB", parseFloat(item.price))}</div>
                <div style="text-align: end;">${item.quantity}</div>
                <div style="text-align: end;">${formatPrice("THB", (parseFloat(item.price) * item.quantity))}</div>
            </div>
            `;
        });
        document.getElementById("order-product").innerHTML = productHtml;
        this.selectors.productInput.value = JSON.stringify(orderItems);
    },

    renderPayment() {
        this.selectors.subtotal.textContent = formatPrice("THB", parseFloat(this.orderItems.subtotal));
        this.selectors.vatAmount.textContent = formatPrice("THB", parseFloat(this.orderItems.vat));
        this.selectors.discountValue.textContent = formatPrice("THB", parseFloat(this.orderItems.discount));
        this.selectors.orderCode.textContent = this.orderItems.orderId;
        this.selectors.orderDate.textContent = formatDateToYYYYMMDD(this.orderItems.createdAt);

        this.selectors.orderInput.value = this.orderItems.orderId;

        this.selectors.subtotalInput.value = parseFloat(this.orderItems.subtotal).toFixed(2);
        this.selectors.vatAmountInput.value = parseFloat(this.orderItems.vat).toFixed(2);
        this.selectors.discountAmountInput.value = parseFloat(this.orderItems.discount).toFixed(2);
        
    },

    renderShipping() {
        let shippingHTML = `
        <div class="section-header">
            <div>
                <p>กรอกข้อมูลที่อยู่จัดส่ง</p>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 0.8rem;">ตามการตั้งค่า</span>
                <label class="toggle-switch">
                    <input type="checkbox" id="setupShipping"/>
                    <span class="slider"></span>
                </label>
            </div>
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label for="full_name" class="form-label">ชื่อ-นามสกุล:</label>
                <input type="text" id="full_name" name="full_name" class="form-input" value="" required>
            </div>
            <div class="form-group">
                <label for="phone_number" class="form-label">เบอร์โทรศัพท์:</label>
                <input type="tel" id="phone_number" name="phone_number" class="form-input" value="" required>
            </div>
            <div class="full-width form-group">
                <label for="address_detail" class="form-label">ที่อยู่:</label>
                <textarea id="address_detail" name="address_detail" class="form-input" style="min-height: 60px !important;" required></textarea>
            </div>
            <div class="form-group">
                <label for="province" class="form-label">จังหวัด:</label>
                <select id="province" name="province" class="form-input" required>
                    <option value="">เลือกจังหวัด</option>
                </select>
            </div>
            <div class="form-group">
                <label for="district" class="form-label">อำเภอ/เขต</label>
                <select id="district" name="district" class="form-input" required disabled>
                    <option value="">เลือกอำเภอ/เขต</option>
                </select>
            </div>
            <div class="form-group">
                <label for="subdistrict" class="form-label">ตำบล/แขวง</label>
                <select id="subdistrict" name="subdistrict" class="form-input" required disabled>
                    <option value="">เลือกตำบล/แขวง</option>
                </select>
            </div>
            <div class="form-group">
                <label for="postalCode" class="form-label">รหัสไปรษณีย์:</label>
                <input type="text" id="postalCode" name="postalCode" class="form-input" value="" readonly>
            </div>
        </div>
        `;
        this.selectors.pickupSection.innerHTML = '';
        this.selectors.shippingSection.innerHTML = shippingHTML;

        // ===== REBIND THE NEWLY CREATED DOM ELEMENTS TO `this.selectors` =====
        this.selectors.selectedFullname = document.getElementById('full_name');
        this.selectors.selectedPhoneNumber = document.getElementById('phone_number');
        this.selectors.selectedAddressDetail = document.getElementById('address_detail');

        this.selectors.selectedProvince = document.getElementById('province');
        this.selectors.selectedDistrict = document.getElementById('district');
        this.selectors.selectedSubdistrict = document.getElementById('subdistrict');
        this.selectors.selectedPostalCode = document.getElementById('postalCode');

        // ===== LOAD =====
        this.populateProvinces();
        this.populateDistricts();
        this.populateSubDistricts();

    },

    renderPickup() {
        let pickupHTML = `
        <div class="section-header">
            <div>
                <p>กรอกข้อมูลผู้มาติดต่อ</p>
            </div>
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label for="full_name" class="form-label">ชื่อ-นามสกุล:</label>
                <input type="text" id="full_name" name="full_name" class="form-input" value="" required>
            </div>
            <div class="form-group">
                <label for="phone_number" class="form-label">เบอร์โทรศัพท์:</label>
                <input type="tel" id="phone_number" name="phone_number" class="form-input" value="" required>
            </div>
        </div>
        `;
        this.selectors.shippingSection.innerHTML = '';
        this.selectors.pickupSection.innerHTML = pickupHTML;
    },

    renderBankTransfer() {
        let bankHTML = `
        <div style="display: grid; grid-template-columns: 1fr 2fr; align-items: center;">
            <div>
                <img src="../bankPay.png" style="width: 100%;" alt="" />
            </div>
            <div>
                <h6>บจก.แทรนดาร์ อินเตอร์เนชั่นแนล</h6>
                <p>ธ.กรุงศรีอยุธยา <span>320-1-13702-8</span> <i class="bi bi-copy"></i></p>
                <div class="mt-1" style="border-top: 2px dashed #cccccc;">
                    <div class="mt-1" style="font-size: 0.9rem;">ท่านสามารถส่งหลักฐานการชำระเงินภายหลังได้</div>
                    <p style="font-size: 0.8rem; color: #666;">(ถ้ามีการสั่งซื้อแล้วจะมีเจ้าหน้าที่ติดต่อท่านเพื่อยืนยันคำสั่งซื้อ)</p>
                </div>
            </div>
        </div>
        `;

        // <span>${this.BankNumber}</span>

        this.selectors.promptpaySection.innerHTML = '';
        this.selectors.bankSection.innerHTML = bankHTML;
    },

    renderPromptpay() {
        let promptpayHTML = `
        <div style="display: grid; grid-template-columns: 1fr 2fr; align-items: center;">
            <div>
                <div id="qr-placeholder">กำลังโหลด...</div>
            </div>
            <div>
                <div>
                    <img src="../promptPay.png" style="width: 28%;" alt="" />
                </div>
                <h6>บจก.แทรนดาร์ อินเตอร์เนชั่นแนล</h6>
                <div class="mt-1" style="border-top: 2px dashed #cccccc;">
                    <div class="mt-1" style="font-size: 0.9rem;">ท่านสามารถส่งหลักฐานการชำระเงินภายหลังได้</div>
                    <p style="font-size: 0.8rem; color: #666;">(ถ้ามีการสั่งซื้อแล้วจะมีเจ้าหน้าที่ติดต่อท่านเพื่อยืนยันคำสั่งซื้อ)</p>
                </div>
            </div>
        </div>
        `;

        this.selectors.bankSection.innerHTML = '';
        this.selectors.promptpaySection.innerHTML = promptpayHTML;

        generateQRPromptpay(this.PromptPayNumber, this.totalPrice, this.QRCodeUrl)
            .then(data => {

                if (data.qrCodeImageBase64) {
                    const qrPlaceholder = this.selectors.promptpaySection.querySelector('#qr-placeholder');
                    if (qrPlaceholder) {
                        qrPlaceholder.innerHTML = `<img src="${data.qrCodeImageBase64}" style="width: 100%;" alt="QR Code" />`;
                    }
                } else {
                    this.selectors.promptpaySection.querySelector('#qr-placeholder').innerHTML = 'ไม่พบ QR Code';
                }
            })
            .catch(error => {
                console.error('error load QR:', error);
            });
    },

    populateProvinces() {
        const provinceOption = this.provincesData.map(item => {
            const provinceName = this.lang === 'en' ? item.provinceNameEn : item.provinceNameTh;
            return `<option value="${item.provinceCode}" data-code="${item.provinceCode}">${provinceName}</option>`;
        });
        const defaultOption = `<option value="">${this.lang === 'en' ? 'Select Province' : 'เลือกจังหวัด'}</option>`;
        this.selectors.selectedProvince.innerHTML = defaultOption + provinceOption.join('');
    },

    populateDistricts() {
        if (this.provinceActive) {
            this.selectors.selectedDistrict.removeAttribute("disabled");
        } else {
            this.selectors.selectedDistrict.setAttribute("disabled", true);
        }
        const districtOption = this.districtsData
            .filter(item => item.provinceCode == this.provinceActive)
            .map(item => {
                const districtName = this.lang === 'en' ? item.districtNameEn : item.districtNameTh;
                return `<option value="${item.districtCode}" data-code="${item.districtCode}">${districtName}</option>`;
            });
        const defaultOption = `<option value="">${this.lang === 'en' ? 'Select District' : 'เลือกอำเภอ/เขต'}</option>`;
        this.selectors.selectedDistrict.innerHTML = defaultOption + districtOption.join('');
    },

    populateSubDistricts() {
        if (this.districtActive) {
            this.selectors.selectedSubdistrict.removeAttribute("disabled");
        } else {
            this.selectors.selectedSubdistrict.setAttribute("disabled", true);
        }
        const subdistrictOption = this.subdistrictsData
            .filter(item => item.districtCode == this.districtActive)
            .map(item => {
                const subdistrictName = this.lang === 'en' ? item.subdistrictNameEn : item.subdistrictNameTh;
                return `<option value="${item.subdistrictCode}" data-code="${item.postalCode}">${subdistrictName}</option>`;
            });
        const defaultOption = `<option value="">${this.lang === 'en' ? 'Select Subdistrict' : 'เลือกตำบล/แขวง'}</option>`;
        this.selectors.selectedSubdistrict.innerHTML = defaultOption + subdistrictOption.join('');
    },

    populatePostalCode() {
        this.selectors.selectedPostalCode.value = this.postalCodeActive;
    },

    updateShipping(isChecked) {

        if (isChecked && this.addressData) {

            this.selectors.selectedFullname.setAttribute("readonly", true);
            this.selectors.selectedPhoneNumber.setAttribute("readonly", true);
            this.selectors.selectedAddressDetail.setAttribute("readonly", true);

            this.selectors.selectedFullname.value = this.addressData.fullname;
            this.selectors.selectedPhoneNumber.value = this.addressData.phoneNumber;
            this.selectors.selectedAddressDetail.value = this.addressData.addressDetail;

            this.populateProvinces();
            const province = this.provincesData.find(p => p.provinceCode == this.addressData.province_id);
            if (province) {
                this.selectors.selectedProvince.value = province.provinceCode;
                this.provinceActive = province.provinceCode
            }
            this.populateDistricts();
            const district = this.districtsData.find(d => d.districtCode == this.addressData.district_id);
            if (district) {
                this.selectors.selectedDistrict.value = district.districtCode;
                this.districtActive = district.districtCode;
            }
            this.populateSubDistricts();
            const subdistrict = this.subdistrictsData.find(s => s.subdistrictCode == this.addressData.sub_district_id);
            if (subdistrict) {
                this.selectors.selectedSubdistrict.value = subdistrict.subdistrictCode;
                this.postalCodeActive = subdistrict.postalCode;
            }
            this.populatePostalCode();

            this.selectors.selectedProvince.setAttribute("readonly", true);
            this.selectors.selectedDistrict.setAttribute("readonly", true);
            this.selectors.selectedSubdistrict.setAttribute("readonly", true);
            this.selectors.selectedPostalCode.setAttribute("readonly", true);


        } else {

            this.selectors.selectedFullname.value = '';
            this.selectors.selectedPhoneNumber.value = '';
            this.selectors.selectedAddressDetail.value = '';

            this.selectors.selectedProvince.value = null;
            this.selectors.selectedDistrict.value = null;
            this.selectors.selectedSubdistrict.value = null;
            this.selectors.selectedPostalCode.value = null;

            this.provinceActive = null;
            this.districtActive = null;
            this.postalCodeActive = null;

            this.populateProvinces();
            this.populateDistricts();
            this.populateSubDistricts();

            this.selectors.selectedFullname.removeAttribute("readonly");
            this.selectors.selectedPhoneNumber.removeAttribute("readonly");
            this.selectors.selectedAddressDetail.removeAttribute("readonly");

            this.selectors.selectedProvince.removeAttribute("readonly");
            this.selectors.selectedDistrict.removeAttribute("readonly");
            this.selectors.selectedSubdistrict.removeAttribute("readonly");
            // this.selectors.selectedPostalCode.removeAttribute("readonly");

        }

    },

    updateDelivery(value) {
        let transportCost = 0;
        switch (value) {
            case "shipping":
                this.renderShipping();
                this.selectors.deliveryService.style.display = 'block';
                this.selectors.shippingSection.style.display = 'block';
                this.selectors.pickupSection.style.display = 'none';
                transportCost = this.shippingCost;
                break;
            case "pickup":
                this.renderPickup();
                this.selectors.deliveryService.style.display = 'none';
                this.selectors.pickupSection.style.display = 'block';
                this.selectors.shippingSection.style.display = 'none';
                transportCost = 0;
                break;
            default:
                break;
        }

        this.selectors.selectedDeliveryMethod.textContent = this.deliveryMethodNames[value];
        this.selectors.shippingCostValue.textContent = formatPrice("THB", parseFloat(transportCost));

        const total = parseFloat(this.orderItems?.totalAmount || 0);
        const finalAmount = total + transportCost;

        this.selectors.totalAmount.textContent = formatPrice("THB", parseFloat(finalAmount));
        this.totalPrice = parseFloat(finalAmount);
        this.selectors.shippingAmountInput.value = transportCost;
    },

    updatePaymentSummary(value) {
        switch (value) {
            case "bank_transfer":
                this.renderBankTransfer();
                this.selectors.bankSection.style.display = 'block';
                this.selectors.promptpaySection.style.display = 'none';
                break;
            case "promptpay":
                this.renderPromptpay();
                this.selectors.promptpaySection.style.display = 'block';
                this.selectors.bankSection.style.display = 'none';
                break;
            default:
                break;
        }

        this.selectors.selectedPaymentMethod.textContent = this.paymentMethodNames[value];
    },

    radioEvents() {
        this.selectors.deliveryRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                this.updateDelivery(radio.value);
            });
        });

        this.selectors.paymentRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                this.updatePaymentSummary(radio.value);
            });
        });
    },

    radioActive() {
        const initialDelivery = document.querySelector('input[name="delivery_option"]:checked');
        const initialPayment = document.querySelector('input[name="payment_method"]:checked');
        if (initialDelivery) {
            this.updateDelivery(initialDelivery.value);
            document.querySelectorAll('.selection-card.delivery').forEach(card => card.classList.remove('active'));
            const selectedCard = initialDelivery.closest('.selection-card.delivery');
            if (selectedCard) selectedCard.classList.add('active');
        }
        if (initialPayment) {
            this.updatePaymentSummary(initialPayment.value);
            document.querySelectorAll('.selection-card.payment').forEach(card => card.classList.remove('active'));
            const selectedCard = initialPayment.closest('.selection-card.payment');
            if (selectedCard) selectedCard.classList.add('active');
        }
    },

    handleContainerClick(event, className, inputName, updateFn) {
        const target = event.target.closest(`.selection-card.${className}`);
        if (target) {
            const radio = target.querySelector(`input[name="${inputName}"]`);
            if (radio && !radio.checked) {
                radio.checked = true;
                radio.dispatchEvent(new Event('change', {
                    bubbles: true
                }));
                document.querySelectorAll(`.selection-card.${className}`).forEach(card => card.classList.remove('active'));
                target.classList.add('active');
            }
        }
    },

    initEvents() {
        document.addEventListener('click', (event) => {
            if (event.target.closest('.selection-card.delivery')) {
                this.handleContainerClick(event, 'delivery', 'delivery_option', this.updateDelivery.bind(this));
            }
            if (event.target.closest('.selection-card.payment')) {
                this.handleContainerClick(event, 'payment', 'payment_method', this.updatePaymentSummary.bind(this));
            }
        });

        document.addEventListener('change', (event) => {


            if (event.target.closest('#setupShipping')) {

                if (this.addressData == null || this.addressData == undefined) {
                    event.target.checked = !event.target.checked;
                    alert('ไม่มีข้อมูลที่ตั้งค่าไว้');
                    return;
                }

                if (event.target.type === 'checkbox') {
                    const isChecked = event.target.checked;
                    this.updateShipping(isChecked);
                }
            }

            if (event.target.closest('#province')) {
                const selectedOption = event.target.closest('#province').options[event.target.closest('#province').selectedIndex];
                const value = selectedOption.value;
                const dataCode = selectedOption.dataset.code;
                this.provinceActive = dataCode;
                this.districtActive = null;
                this.postalCodeActive = null;
                this.populateDistricts();
                this.populateSubDistricts();
                this.populatePostalCode();

                const setupShippingCheckbox = document.querySelector('#setupShipping');
                if (setupShippingCheckbox && setupShippingCheckbox.checked) {
                    setupShippingCheckbox.checked = false;
                    this.updateShipping(false);
                }
            }

            if (event.target.closest('#district')) {
                const selectedOption = event.target.closest('#district').options[event.target.closest('#district').selectedIndex];
                const value = selectedOption.value;
                const dataCode = selectedOption.dataset.code;
                this.districtActive = dataCode;
                this.populateSubDistricts();

                const setupShippingCheckbox = document.querySelector('#setupShipping');
                if (setupShippingCheckbox && setupShippingCheckbox.checked) {
                    setupShippingCheckbox.checked = false;
                    this.updateShipping(false);
                }
            }

            if (event.target.closest('#subdistrict')) {
                const selectedOption = event.target.closest('#subdistrict').options[event.target.closest('#subdistrict').selectedIndex];
                const value = selectedOption.value;
                const dataCode = selectedOption.dataset.code;
                this.postalCodeActive = dataCode;
                this.populatePostalCode();

                const setupShippingCheckbox = document.querySelector('#setupShipping');
                if (setupShippingCheckbox && setupShippingCheckbox.checked) {
                    setupShippingCheckbox.checked = false;
                    this.updateShipping(false);
                }
            }
        });

    }

};
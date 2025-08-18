
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

export const CheckoutUI = {
    lang: null,
    baseTotal: 1500.00,
    shippingFee: 50.00,
    ORDER_STORAGE_KEY: 'orderProduct',
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
        deliveryRadios: document.querySelectorAll('.delivery input[name="delivery_option"]'),
        paymentRadios: document.querySelectorAll('.payment input[name="payment_method"]'),
        shippingSection: document.getElementById('shippingAddressFormSection'),
        shippingCostValue: document.getElementById('shipping-cost-value'),
        totalAmount: document.getElementById('total-amount'),
        selectedPaymentMethod: document.getElementById('selected-payment-method'),
        selectedDeliveryMethod: document.getElementById('selected-delivery-method'),

        //Shipping Address
        selectedFullname: document.getElementById('full_name'),
        selectedPhoneNumber: document.getElementById('phone_number'),
        selectedAddressDetail: document.getElementById('address_detail'),

        selectedProvince: document.getElementById('province'),
        selectedDistrict: document.getElementById('district'),
        selectedSubdistrict: document.getElementById('subdistrict'),
        selectedPostalCode: document.getElementById('postalCode')
    },


    init(provinces, districts, subdistricts, address) {

        this.lang = 'th';

        this.initRadioEvents();
        this.initializeUI();
        this.initClickEvents();

        this.loadOrder();
        this.renderProductItem();

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
        if(this.provinceActive){
            this.selectors.selectedDistrict.disabled = false;
        }else{
            this.selectors.selectedDistrict.disabled = true;
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
        if(this.districtActive){
            this.selectors.selectedSubdistrict.disabled = false;
        }else{
            this.selectors.selectedSubdistrict.disabled = true;
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
        if (isChecked) {

            this.selectors.selectedFullname.setAttribute("readonly", "");
            this.selectors.selectedPhoneNumber.setAttribute("readonly", "");
            this.selectors.selectedAddressDetail.setAttribute("readonly", "");

            this.selectors.selectedFullname.value = this.addressData.fullname;
            this.selectors.selectedPhoneNumber.value = this.addressData.phoneNumber;
            this.selectors.selectedAddressDetail.value = this.addressData.addressDetail;

            this.populateProvinces();
            const province = this.provincesData.find(p => p.provinceCode == this.addressData.province_id);
            if(province){
                this.selectors.selectedProvince.value = province.provinceCode;
                this.provinceActive = province.provinceCode
            }
            this.populateDistricts();
            const district = this.districtsData.find(d => d.districtCode == this.addressData.district_id);
            if(district){
                this.selectors.selectedDistrict.value = district.districtCode;
                this.districtActive = district.districtCode;
            }
            this.populateSubDistricts();
            const subdistrict = this.subdistrictsData.find(s => s.subdistrictCode == this.addressData.sub_district_id);
            if(subdistrict){
                this.selectors.selectedSubdistrict.value = subdistrict.subdistrictCode;
                this.postalCodeActive = subdistrict.postalCode;
            }
            this.populatePostalCode();

            this.selectors.selectedProvince.setAttribute("readonly", "");
            this.selectors.selectedDistrict.setAttribute("readonly", "");
            this.selectors.selectedSubdistrict.setAttribute("readonly", "");
            this.selectors.selectedPostalCode.setAttribute("readonly", "");

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

    updateDeliveryUI(value) {
        const isShipping = value === 'shipping';
        const cost = isShipping ? this.shippingFee : 0;

        this.selectors.shippingSection.style.display = isShipping ? 'block' : 'none';
        this.selectors.shippingCostValue.textContent = `${cost.toFixed(2)} บาท`;
        this.selectors.totalAmount.textContent = `${(this.baseTotal + cost).toFixed(2)} บาท`;
        this.selectors.selectedDeliveryMethod.textContent = this.deliveryMethodNames[value] || '-';
    },

    updatePaymentSummary(value) {
        this.selectors.selectedPaymentMethod.textContent = this.paymentMethodNames[value] || '-';
    },

    initRadioEvents() {
        this.selectors.deliveryRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                this.updateDeliveryUI(radio.value);
            });
        });

        this.selectors.paymentRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                this.updatePaymentSummary(radio.value);
            });
        });
    },

    initializeUI() {
        const initialDelivery = document.querySelector('input[name="delivery_option"]:checked');
        const initialPayment = document.querySelector('input[name="payment_method"]:checked');

        if (initialDelivery) {
            this.updateDeliveryUI(initialDelivery.value);
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

    initClickEvents() {
        document.addEventListener('click', (event) => {
            if (event.target.closest('.selection-card.delivery')) {
                this.handleContainerClick(event, 'delivery', 'delivery_option', this.updateDeliveryUI.bind(this));
            }

            if (event.target.closest('.selection-card.payment')) {
                this.handleContainerClick(event, 'payment', 'payment_method', this.updatePaymentSummary.bind(this));
            }
        });

        document.addEventListener('change', (event) => {
            if (event.target.closest('#setupShipping')) {
                if (event.target.type === 'checkbox') {
                    const isChecked = event.target.checked;
                    this.updateShipping(isChecked);
                }
            }

            if(event.target.closest('#province')){
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

            if(event.target.closest('#district')){
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
            
            if(event.target.closest('#subdistrict')){
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

function formatPrice(currency, price) {
    return Number(price).toLocaleString("th-TH", {
    style: "currency",
    currency: currency || "THB",
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
    });
}
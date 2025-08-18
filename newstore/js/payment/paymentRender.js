
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
    baseTotal: 1500.00,
    shippingFee: 50.00,
    ORDER_STORAGE_KEY: 'orderProduct',
    orderItems: [],

    provincesData: [],
    districtsData: [],
    subdistrictsData: [],
    lang: null,
    provinceActive: null,
    districtActive: null,
    subdistrictActive: null,

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
        selectedProvince: document.getElementById('province'),
        selectedDistrict: document.getElementById('district'),
        selectedSubdistrict: document.getElementById('subdistrict')
    },


    init(provinces, districts, subdistricts) {

        this.initRadioEvents();
        this.initializeUI();
        this.initClickEvents();

        this.loadOrder();
        // this.renderProduct();

        this.lang = 'th';
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

    // renderProduct() {
    //     const orderItems = this.orderItems.items;
    //     let productHtml = '';
    //     orderItems.forEach(item => {
    //         productHtml += `
    //         <div class="product-item">
    //             <div>
    //                 <img src="${item.imageUrl}" alt="" class="product-img">
    //             </div>
    //             <div style="text-align: end;">${formatPrice("THB", parseFloat(item.price))}</div>
    //             <div style="text-align: end;">${item.quantity}</div>
    //             <div style="text-align: end;">${formatPrice("THB", (parseFloat(item.price) * item.quantity))}</div>
    //         </div>
    //         `;
    //     });
    //     document.getElementById("order-product").innerHTML = productHtml;
    // },

    populateProvinces() {
        const provinceOption = this.provincesData.map(item => {
            const provinceName = this.lang === 'en' ? item.provinceNameEn : item.provinceNameTh;
            return `<option value="${item.id}" data-code="${item.provinceCode}">${provinceName}</option>`;
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
            return `<option value="${item.id}">${districtName}</option>`;
        });
        const defaultOption = `<option value="">${this.lang === 'en' ? 'Select District' : 'เลือกอำเภอ/เขต'}</option>`;
        this.selectors.selectedDistrict.innerHTML = defaultOption + districtOption.join('');
    },

    populateSubDistricts() {
        const subdistrictOption = this.subdistrictsData
        .filter(item => item.provinceCode == 10)
        .map(item => {
            const subdistrictName = this.lang === 'en' ? item.subdistrictNameEn : item.subdistrictNameTh;
            return `<option value="${item.id}" data-code="">${subdistrictName}</option>`;
        });
        const defaultOption = `<option value="">${this.lang === 'en' ? 'Select Subdistrict' : 'เลือกตำบล/แขวง'}</option>`;
        this.selectors.selectedSubdistrict.innerHTML = defaultOption + subdistrictOption.join('');
    },

    updateShipping(isChecked) {
        // console.log('isChecked', isChecked);
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
            // const target = event.target;
            // if (target.closest('#setupShipping')) {
            //     if (target.type === 'checkbox') {
            //         const isChecked = target.checked;
            //         this.updateShipping(isChecked);
            //     }
            // }

            if(event.target.closest('#province')){
                const selectedOption = event.target.closest('#province').options[event.target.closest('#province').selectedIndex];
                const value = selectedOption.value;
                const dataCode = selectedOption.dataset.code;
                this.provinceActive = dataCode;
                this.populateDistricts();
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
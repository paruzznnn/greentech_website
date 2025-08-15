export const CheckoutUI = {
    baseTotal: 1500.00,
    shippingFee: 50.00,

    selectors: {
        deliveryRadios: document.querySelectorAll('.delivery input[name="delivery_option"]'),
        paymentRadios: document.querySelectorAll('.payment input[name="payment_method"]'),
        shippingSection: document.getElementById('shippingAddressFormSection'),
        shippingCostValue: document.getElementById('shipping-cost-value'),
        totalAmount: document.getElementById('total-amount'),
        selectedPaymentMethod: document.getElementById('selected-payment-method'),
        selectedDeliveryMethod: document.getElementById('selected-delivery-method')
    },

    deliveryMethodNames: {
        shipping: 'จัดส่งถึงที่อยู่',
        pickup: 'รับหน้าร้าน'
    },

    paymentMethodNames: {
        bank_transfer: 'โอนเงินผ่านธนาคาร',
        promptpay: 'พร้อมเพย์',
        cod: 'เก็บเงินปลายทาง'
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
    },

    init() {
        this.initRadioEvents();
        this.initializeUI();
        this.initClickEvents();
    }
};
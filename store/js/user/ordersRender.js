
const OrderSummaryRenderer = {
    cartItems: [],
    services: [],
    discounts: {},
    shipping: {},
    summary: {},
    orderId: '',
    customer: {},
    paymentStatus: 'รอดำเนินการ',

    formatPrice(value) {
        return Number(value).toLocaleString('th-TH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    },

    loadData() {
        const dataRaw = localStorage.getItem("checkoutAppData");
        if (!dataRaw) return;
        const data = JSON.parse(dataRaw);

        this.cartItems = data.cartItems || [];
        this.services = data.selectedServices || [];
        this.discounts = data.appliedCoupon || {};
        this.shipping = data.selectedShippingOptions || {};
        this.summary = data.summary || {};
        this.orderId = data.order_id || '#N/A';
        this.paymentStatus = data.paymentStatus || 'รอดำเนินการ';

        // ถ้า appliedCoupon เป็น shipping ให้ตั้ง shipping.price = 0
        if (this.discounts.type === 'shipping' && this.shipping) {
            this.shipping.price = 0;
        }

        const addr = data.addresses?.[0] || {};
        this.customer = {
            name: `${data.billing?.first_name || ''} ${data.billing?.last_name || ''}`,
            phone: data.billing?.phone_number || '',
            address: `${addr.detail || ''}, ${addr.subdistricts || ''} ${addr.districts || ''}, ${addr.provinces || ''} ${addr.postalCode || ''}`
        };
    },

    renderItems() {
        const tbody = document.getElementById('order-items-tbody');
        tbody.innerHTML = '';
        if (!this.cartItems.length) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center">ไม่มีสินค้า</td></tr>';
            return;
        }
        this.cartItems.forEach(item => {
            const total = item.price * item.qty;
            const row = document.createElement('tr');
            row.innerHTML = `
                    <td>${item.name}</td>
                    <td class="text-center">${item.qty}</td>
                    <td class="text-right">${this.formatPrice(item.price)}</td>
                    <td class="text-right">${this.formatPrice(total)}</td>`;
            tbody.appendChild(row);
        });
    },

    renderServices() {
        const list = document.getElementById('order-services-list');
        list.innerHTML = '';
        if (!this.services.length) {
            list.innerHTML = '<li class="text-center">ไม่มีบริการเสริม</li>';
            return;
        }
        this.services.forEach(s => {
            const li = document.createElement('li');
            li.innerHTML = `<span>${s.label}</span><span>${this.formatPrice(s.price)}</span>`;
            list.appendChild(li);
        });
    },

    renderDiscounts() {
        const list = document.getElementById('order-discounts-list');
        list.innerHTML = '';
        const {
            label = '', type = '', value = 0
        } = this.discounts || {};

        if (!label) {
            list.innerHTML = '<li class="text-center">ไม่มีส่วนลด</li>';
            return;
        }

        // กรณี shipping coupon แสดง label แต่ไม่แสดงราคา
        let display;
        if (type === 'percent') {
            display = `- ${value}%`;
        } else if (type === 'shipping') {
            display = this.formatPrice(0);
        } else {
            display = `- ${this.formatPrice(value)}`;
        }

        const li = document.createElement('li');
        li.innerHTML = `<span>${label}</span><span>${display}</span>`;
        list.appendChild(li);
    },

    renderShipping() {
        const list = document.getElementById('order-shipping-list');
        list.innerHTML = '';
        if (this.shipping && Object.keys(this.shipping).length) {
            const li = document.createElement('li');
            li.innerHTML = `<span>${this.shipping.name}</span><span>${this.formatPrice(this.shipping.price)}</span>`;
            list.appendChild(li);
            return;
        }
        list.innerHTML = '<li class="text-center">ไม่มีค่าจัดส่ง</li>';
    },

    renderTotals() {
        const list = document.getElementById('order-totals-list');
        list.innerHTML = '';
        const {
            subtotal = 0, service = 0, discount = 0, shipping = 0, tax = 0, total = 0
        } = this.summary;
        const totals = [{
            label: 'ยอดรวมสินค้า:',
            value: subtotal
        },
        {
            label: 'รวมบริการเสริม:',
            value: service
        },
        {
            label: 'ส่วนลด:',
            value: -discount
        },
        {
            label: 'ค่าจัดส่ง:',
            value: shipping
        },
        {
            label: 'ภาษีมูลค่าเพิ่ม:',
            value: tax
        },
        {
            label: 'ยอดชำระทั้งหมด:',
            value: total,
            isTotal: true
        }
        ];
        totals.forEach(item => {
            const li = document.createElement('li');
            if (item.isTotal) li.className = 'rcp-total';
            li.innerHTML = `<span>${item.label}</span><span>${this.formatPrice(item.value)}</span>`;
            list.appendChild(li);
        });
    },

    updateUI() {
        document.getElementById('order-id').textContent = this.orderId;
        document.getElementById('customer-name').textContent = this.customer.name;
        document.getElementById('customer-phone').textContent = this.customer.phone;
        document.getElementById('customer-address').textContent = this.customer.address;
        document.getElementById('order-date').textContent = new Date().toLocaleDateString('th-TH', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
        document.getElementById('payment-status').textContent = this.paymentStatus;
    },

    bindPayEvents() {
        document.getElementById("payOrders").onclick = () => {
            window.location.href = window.AppConfig.BASE_WEB + 'user/payment/?id=' + this.orderId;
            // redirectGet(pathConfig.BASE_WEB + 'user/payment/');
        };
    },

    init() {
        this.loadData();
        this.renderItems();
        this.renderServices();
        this.renderDiscounts();
        this.renderShipping();
        this.renderTotals();
        this.updateUI();
        this.bindPayEvents();
    }
};

document.addEventListener('DOMContentLoaded', () => {
    OrderSummaryRenderer.init();
});

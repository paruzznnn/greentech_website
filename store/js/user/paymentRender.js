
const PaymentApp = {
    currentOrderId: null,
    orders: {},
    bankAccounts: {
        "krungsri_bank": {
            name: "ธนาคารกรุงศรีอยุธยา",
            accountName: "บริษัท แทรนดาร์ อินเตอร์เนชั่นแนล จำกัด",
            accountNumber: "320-1-13702-8"
        },
        "promptpay": {
            name: "QR code",
            accountName: "บริษัท แทรนดาร์ อินเตอร์เนชั่นแนล จำกัด",
            accountNumber: "320-1-13702-8"
        }
    },

    loadOrder() {
        const url = window.AppConfig.BASE_WEB + "service/user/payment-data.php?action=getOrdersItems";
        fetch(url, {
            method: "GET",
            headers: {
                'Authorization': 'Bearer my_secure_token_123',
                "Content-Type": "application/json"
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.data) {
                    this.orders = data.data;
                } else {
                    console.error('API response error:', data.message);
                }
            })
            .catch(err => {
                console.error('Failed to load orders:', err);
            });
    },

    toggleUIElements(showOrder) {
        const toggle = (id, display) => document.getElementById(id).style.display = display;
        document.querySelector('.rcp-company-logo').style.display = showOrder ? 'none' : 'flex';
        toggle('bank-details', showOrder ? 'flex' : 'none');
        toggle('order-summary', showOrder ? 'block' : 'none');
        toggle('proof-preview-container', showOrder ? 'flex' : 'none');
        toggle('rcp-action-buttons', showOrder ? 'flex' : 'none');
    },

    fetchOrder() {
        const orderId = document.getElementById('order-id-input').value.trim().toUpperCase();
        const order = this.orders[orderId];

        if (!order) {
            alert('ไม่พบ Order ID นี้');
            this.toggleUIElements(false);
            return;
        }

        this.currentOrderId = orderId;

        this.toggleUIElements(true);
        this.renderBankInfo(order);
        this.renderOrderSummary(order);
    },

    renderBankInfo(order) {
        const paymentFirst = Array.isArray(order.payments) ? order.payments[0] : Object.values(order.payments || {})[0];
        const bankLogoEl = document.getElementById('bank-logo');

        if (paymentFirst) {
            bankLogoEl.src = (paymentFirst.type === "promptpay" && paymentFirst.pic) ?
                paymentFirst.pic :
                window.AppConfig.BASE_WEB + paymentFirst.pic;
            bankLogoEl.style.display = 'block';

            // reset proof preview
            document.getElementById('proof-img-preview').style.display = 'none';
            document.getElementById('proof-img-preview').src = "";
            document.getElementById('proof-placeholder').style.display = 'block';

            const bankInfo = this.bankAccounts[paymentFirst.type];
            if (bankInfo) {
                document.getElementById('bank-name').textContent = bankInfo.name;
                document.getElementById('account-name').textContent = bankInfo.accountName;
                document.getElementById('account-number').textContent = bankInfo.accountNumber;
            }
        } else {
            bankLogoEl.style.display = 'none';
        }
    },

    renderOrderSummary(order) {
        const container = document.getElementById('order-items');
        container.innerHTML = "";

        const {
            orderId,
            subtotal,
            discount,
            shipping,
            service,
            vat,
            total
        } = order;

        container.innerHTML += `
                <div class="rcp-order-row"><span class="rcp-order-label">รหัสออเดอร์:</span> <span class="rcp-order-value">${orderId}</span></div>
                <div class="rcp-order-row"><span class="rcp-order-label">ยอดรวม:</span> <span class="rcp-order-value">${this.formatPrice(subtotal)}</span></div>
                <div class="rcp-order-row"><span class="rcp-order-label">ส่วนลด:</span> <span class="rcp-order-value">${this.formatPrice(discount)}</span></div>
                <div class="rcp-order-row"><span class="rcp-order-label">ค่าส่ง:</span> <span class="rcp-order-value">${this.formatPrice(shipping)}</span></div>
                <div class="rcp-order-row"><span class="rcp-order-label">ค่าบริการ:</span> <span class="rcp-order-value">${this.formatPrice(service)}</span></div>
                <div class="rcp-order-row"><span class="rcp-order-label">VAT:</span> <span class="rcp-order-value">${this.formatPrice(vat)}</span></div>
                <h4 class="rcp-order-total">ยอดสุทธิ: ${this.formatPrice(total)}</h4>
            `;
    },

    previewFile(event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.getElementById('proof-img-preview');
            img.src = e.target.result;
            img.style.display = 'block';
            document.getElementById('proof-placeholder').style.display = 'none';
        };
        reader.readAsDataURL(file);
    },

    saveProof() {
        const files = document.getElementById('proof-file-input').files;
        if (files.length > 0) {
            const formData = new FormData();
            formData.append('proof', files[0]); // แนบไฟล์ (name = proof)

            // ถ้ามี orderId หรือข้อมูลอื่นก็เพิ่มได้
            formData.append('action', "uploadSlip");
            formData.append('orderId', this.currentOrderId);

            fetch(window.AppConfig.BASE_WEB + "service/user/payment-action.php", {
                method: "POST",
                body: formData,
                headers: {
                    // ห้ามใส่ "Content-Type" เองเวลาใช้ FormData
                    'Authorization': 'Bearer my_secure_token_123'
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status) {
                        alert('หลักฐานการชำระเงินถูกส่งไปยังเซิร์ฟเวอร์เรียบร้อยแล้ว');
                    }
                })
                .catch(err => {
                    console.error("Upload error:", err);
                    alert('เกิดข้อผิดพลาดในการอัปโหลดไฟล์');
                });
        } else {
            alert('กรุณาแนบไฟล์หลักฐานการชำระเงิน');
        }
    },

    formatPrice(value) {
        return Number(value || 0).toLocaleString('th-TH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    },

    copyAccountNumber() {
        let accountNumber = document.getElementById('account-number').textContent.trim();
        if (!accountNumber) {
            alert('ยังไม่มีเลขบัญชีให้คัดลอก');
            return;
        }
        // เอาเฉพาะตัวเลข
        accountNumber = accountNumber.replace(/\D/g, '');
        navigator.clipboard.writeText(accountNumber)
            .then(() => {
                alert(`คัดลอกเลขบัญชีเรียบร้อยแล้ว: ${accountNumber}`);
            })
            .catch(err => {
                console.error('ไม่สามารถคัดลอกได้:', err);
            });
    },

    bindEvents() {
        document.getElementById('fetch-order-btn').addEventListener('click', () => this.fetchOrder());
        document.getElementById('proof-preview-container').addEventListener('click', () => document.getElementById('proof-file-input').click());
        document.getElementById('proof-file-input').addEventListener('change', e => this.previewFile(e));
        document.getElementById('save-proof-btn').addEventListener('click', () => this.saveProof());
        document.getElementById('copy-account-btn').addEventListener('click', () => this.copyAccountNumber());
    },

    init() {
        this.loadOrder();
        this.bindEvents();
    }
};

document.addEventListener('DOMContentLoaded', () => 
    PaymentApp.init()
);
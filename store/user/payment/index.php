<?php include '../../routes.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-STORE</title>
    <?php include '../../inc-meta.php'; ?>
    <link href="../../css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../../css/user/template-payment.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../../inc-cdn.php'; ?>

</head>

<body>
    <?php include '../../template/head-bar.php'; ?>
    <main>
        <div id="sections_root_payment" class="section-space">
            <div class="container">
                <div class="rcp-card">
                    <div class="rcp-payment-container">

                        <div class="rcp-payment-section">
                            <div class="rcp-company-logo">
                                <img src="http://localhost:3000/trandar_website/store/trandar_logo.png" alt="Company Logo">
                                <p class="terms-text">
                                    การสั่งซื้อของคุณถือเป็นการยอมรับ
                                    <a href="#" class="terms-link">ข้อกำหนดการให้บริการ</a> และ
                                    <a href="#" class="terms-link">นโยบายความเป็นส่วนตัว</a> กรุณาตรวจสอบข้อมูลการสั่งซื้อให้ถูกต้อง
                                </p>
                            </div>

                            <div class="rcp-bank-details" id="bank-details" style="display:none;">
                                <img id="bank-logo" src="" alt="Bank Logo">
                                <div class="rcp-bank-details-info">
                                    <p id="bank-name"></p>
                                    <p><strong>ชื่อบัญชี:</strong> <span id="account-name"></span></p>
                                    <p><strong>เลขที่บัญชี:</strong> <span id="account-number"></span></p>
                                </div>
                            </div>

                            <div class="rcp-order-summary" id="order-summary" style="display:none;">
                                <h4>สรุปคำสั่งซื้อ</h4>
                                <div id="order-items"></div>
                                <div class="rcp-order-total" id="order-total"></div>
                            </div>

                            <div class="rcp-footer-notes">
                                <p>ใบสั่งซื้อนี้ถูกสร้างขึ้นโดยระบบอัตโนมัติ กรุณาเก็บไว้เป็นหลักฐาน</p>
                            </div>

                        </div>

                        <div class="rcp-payment-proof-section">
                            <h3>กรอกหมายเลขใบสั่งซื้อ</h3>
                            <input type="text" id="order-id-input" class="form-input" placeholder="กรอกเลข Order ID" style="width:100%;padding:0.5rem;margin-bottom:1rem;">
                            <button id="fetch-order-btn" class="rcp-btn rcp-btn-orange" style="width:100%;">ดึงข้อมูลคำสั่งซื้อ</button>

                            <div class="rcp-file-input-container" id="proof-preview-container" style="margin-top:1rem; display:none; justify-content: center;">
                                <div class="rcp-proof-preview">
                                    <img id="proof-img-preview" src="" alt="Proof Preview" class="rcp-proof-img" style="display:none;">
                                    <span id="proof-placeholder">สลิปรูปภาพหลักฐานการชำระเงิน</span>
                                </div>
                                <input type="file" id="proof-file-input" accept="image/*" style="display:none;">
                            </div>

                            <div class="rcp-action-buttons" id="rcp-action-buttons" style="display:none;">
                                <button id="save-proof-btn" class="rcp-btn rcp-btn-green">บันทึกการแนบไฟล์</button>
                                <a href="#" class="rcp-btn rcp-btn-secondary">กลับหน้าหลัก</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include '../../template/footer-bar.php'; ?>

    <script>
        const PaymentApp = {
            orders: {},
            bankAccounts: {
                "krungsri_bank": {
                    name: "ธนาคารกรุงศรีอยุธยา",
                    accountName: "บริษัท แทรนดาร์ อินเตอร์เนชั่นแนล จำกัด",
                    accountNumber: "987-6-54321-0"
                },
                "promptpay": {
                    name: "พร้อมเพย์",
                    accountName: "บริษัท แทรนดาร์ อินเตอร์เนชั่นแนล จำกัด",
                    accountNumber: "-"
                }
            },

            loadOrder() {
                const params = new URLSearchParams({
                    action: 'getOrdersItems'
                });

                fetch(`${pathConfig.BASE_WEB}service/user/payment-data.php?${params.toString()}`, {
                        method: "GET",
                        headers: {
                            'Authorization': 'Bearer my_secure_token_123',
                            "Content-Type": "application/json"
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                        this.orders = res.data;
                    })
                    .catch(err => console.error(err));
            },

            fetchOrder() {
                const orderId = document.getElementById('order-id-input').value.trim().toUpperCase();
                const order = this.orders[orderId];

                const bankDetailsEl = document.getElementById('bank-details');
                const orderSummaryEl = document.getElementById('order-summary');
                const proofContainerEl = document.getElementById('proof-preview-container');
                const actionButtonsEl = document.getElementById('rcp-action-buttons');
                const companyLogoEl = document.querySelector('.rcp-company-logo');

                if (!order) {
                    alert('ไม่พบ Order ID นี้');
                    bankDetailsEl.style.display = 'none';
                    orderSummaryEl.style.display = 'none';
                    proofContainerEl.style.display = 'none';
                    actionButtonsEl.style.display = 'none';
                    companyLogoEl.style.display = 'flex';
                    return;
                }

                companyLogoEl.style.display = 'none';
                bankDetailsEl.style.display = 'flex';
                proofContainerEl.style.display = 'flex';
                actionButtonsEl.style.display = 'flex';

                let paymentFirst = null;
                if (Array.isArray(order.payments)) {
                    paymentFirst = order.payments[0];
                } else if (typeof order.payments === 'object') {
                    const keys = Object.keys(order.payments);
                    if (keys.length > 0) paymentFirst = order.payments[keys[0]];
                }

                const bankLogoEl = document.getElementById('bank-logo');
                const proofImgPreview = document.getElementById('proof-img-preview');
                const proofPlaceholder = document.getElementById('proof-placeholder');

                if (paymentFirst) {
                    if (paymentFirst.type === "promptpay" && paymentFirst.pic) {

                        bankLogoEl.src = paymentFirst.pic;
                        bankLogoEl.style.display = 'block';
                        proofImgPreview.src = "";
                        proofImgPreview.style.display = 'none';
                        proofPlaceholder.style.display = 'block';
                    } else {

                        bankLogoEl.src = pathConfig.BASE_WEB + paymentFirst.pic;
                        bankLogoEl.style.display = 'block';
                        proofImgPreview.src = "";
                        proofImgPreview.style.display = 'none';
                        proofPlaceholder.style.display = 'block';
                    }

                    const bankInfo = this.bankAccounts[paymentFirst.type];
                    if (bankInfo) {
                        document.getElementById('bank-name').textContent = bankInfo.name;
                        document.getElementById('account-name').textContent = bankInfo.accountName;
                        document.getElementById('account-number').textContent = bankInfo.accountNumber;
                    }

                } else {
                    bankLogoEl.style.display = 'none';
                }

                this.renderOrderSummary(order || []);
            },

            renderOrderSummary(items) {
                const orderItemsEl = document.getElementById('order-items');
                orderItemsEl.innerHTML = '';

                console.log('items', items);
                

                // let total = 0;
                // items.forEach(item => {
                //     const div = document.createElement('div');
                //     div.className = 'rcp-order-item';
                //     div.innerHTML = `<span>${item.name} x ${item.qty}</span><span>${this.formatPrice(item.qty * item.price)}</span>`;
                //     orderItemsEl.appendChild(div);
                //     total += item.qty * item.price;
                // });
                // document.getElementById('order-total').textContent = 'รวมทั้งหมด: ' + this.formatPrice(total);

                document.getElementById('order-summary').style.display = 'block';
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
                    alert('หลักฐานการชำระเงินถูกบันทึกเรียบร้อยแล้ว');
                } else {
                    alert('กรุณาแนบไฟล์หลักฐานการชำระเงิน');
                }
            },

            formatPrice(value) {
                return Number(value).toLocaleString('th-TH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + ' บาท';
            },

            // แยกส่วนการผูก Event Listener ออกมาเป็นฟังก์ชันใหม่
            bindEvents() {
                document.getElementById('fetch-order-btn').addEventListener('click', () => this.fetchOrder());
                document.getElementById('proof-preview-container').addEventListener('click', () => document.getElementById('proof-file-input').click());
                document.getElementById('proof-file-input').addEventListener('change', e => this.previewFile(e));
                document.getElementById('save-proof-btn').addEventListener('click', () => this.saveProof());
            },

            init() {
                this.loadOrder();
                this.bindEvents(); // เรียกใช้ฟังก์ชัน bindEvents ที่แยกไว้
            }
        };

        document.addEventListener('DOMContentLoaded', () => PaymentApp.init());
    </script>
</body>

</html>
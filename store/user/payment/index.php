<?php include '../../routes.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-STORE - Upload Payment Proof</title>
    <?php include '../../inc-meta.php'; ?>
    <link href="../../css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../../inc-cdn.php'; ?>
    <style>
        /* === Card & Container === */
        .rcp-card {
            background-color: #fff;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .rcp-payment-container {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .rcp-payment-section,
        .rcp-payment-proof-section {
            flex: 1;
        }

        .rcp-company-logo,
        .rcp-bank-details {
            text-align: center;
            margin-top: 1rem;
        }

        .rcp-company-logo img,
        .rcp-bank-details img {
            width: 120px;
            margin-bottom: 0.5rem;
        }

        .rcp-bank-details-info p {
            margin: 0.2rem 0;
            font-size: 0.95rem;
        }

        .rcp-proof-preview {
            width: 100%;
            max-width: 400px;
            height: 200px;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f9fafb;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .rcp-proof-preview:hover {
            background-color: #f3f4f6;
        }

        .rcp-proof-img {
            max-width: 100%;
            max-height: 100%;
            border-radius: 6px;
        }

        .rcp-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 700;
            text-align: center;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
        }

        .rcp-btn-orange {
            background-color: #ff9800;
            color: #fff;
        }

        .rcp-btn-orange:hover {
            background-color: #f18b20;
        }

        .rcp-btn-green {
            background-color: #4caf50;
            color: #fff;
        }

        .rcp-btn-green:hover {
            background-color: #45a049;
        }

        .rcp-btn-secondary {
            background-color: #e5e7eb;
            color: #374151;
        }

        .rcp-btn-secondary:hover {
            background-color: #d1d5db;
        }

        .rcp-order-summary {
            margin-top: 1rem;
            border-top: 1px solid #e5e7eb;
            padding-top: 1rem;
        }

        .rcp-order-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .rcp-order-total {
            font-weight: 700;
            text-align: right;
            margin-top: 0.5rem;
        }

        .rcp-footer-notes {
            text-align: center;
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 1rem;
        }

        .terms-text {
            font-size: 0.9rem;
            line-height: 1.5;
            text-align: left;
            color: #4b5563;
        }

        .terms-link {
            color: #2563eb;
            text-decoration: underline;
        }

        .rcp-action-buttons {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-start;
            gap: 1rem;
            margin-top: 2rem;
        }

        @media (min-width:640px) {
            .rcp-payment-container {
                flex-direction: row;
            }

            .rcp-payment-section,
            .rcp-payment-proof-section {
                flex-basis: 50%;
            }
        }
    </style>
</head>

<body>
    <?php include '../../template/head-bar.php'; ?>
    <main>
        <div id="sections_root_payment" class="section-space">
            <div class="container">
                <div class="rcp-card">
                    <div class="rcp-payment-container">

                        <!-- Payment Section -->
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
                        </div>

                        <!-- Proof Section -->
                        <div class="rcp-payment-proof-section">
                            <h3>กรอกข้อมูลและแนบหลักฐาน</h3>
                            <input type="text" id="order-id-input" class="form-input" placeholder="กรอกเลข Order ID" style="width:100%;padding:0.5rem;margin-bottom:1rem;">
                            <button id="fetch-order-btn" class="rcp-btn rcp-btn-orange" style="width:100%;">ดึงข้อมูลคำสั่งซื้อ</button>

                            <div class="rcp-file-input-container" id="proof-preview-container" style="margin-top:1rem; display:none; justify-content: center;">
                                <div class="rcp-proof-preview">
                                    <img id="proof-img-preview" src="" alt="Proof Preview" class="rcp-proof-img" style="display:none;">
                                    <span id="proof-placeholder">กรุณาเลือกไฟล์รูปภาพ</span>
                                </div>
                                <input type="file" id="proof-file-input" accept="image/*" style="display:none;">
                            </div>

                            <div class="rcp-action-buttons" id="rcp-action-buttons" style="display:none;">
                                <button id="save-proof-btn" class="rcp-btn rcp-btn-green">บันทึกการแนบไฟล์</button>
                                <a href="#" class="rcp-btn rcp-btn-secondary">กลับหน้าหลัก</a>
                            </div>
                        </div>

                    </div>

                    <div class="rcp-footer-notes">
                        <p>ใบเสร็จนี้ถูกสร้างขึ้นโดยระบบอัตโนมัติ กรุณาเก็บไว้เป็นหลักฐาน</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include '../../template/footer-bar.php'; ?>

    <script>
        const PaymentApp = {
            orders: {},

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
                        this.orders = res.data; // คาดว่า res.data เป็น object key=orderId
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
                    companyLogoEl.style.display = 'block';
                    return;
                }

                companyLogoEl.style.display = 'none';
                bankDetailsEl.style.display = 'block';
                proofContainerEl.style.display = 'flex';
                actionButtonsEl.style.display = 'flex';

                // ดึง payments ตัวแรก (รองรับ array หรือ object)
                let paymentFirst = null;
                if (Array.isArray(order.payments)) {
                    paymentFirst = order.payments[0];
                } else if (typeof order.payments === 'object') {
                    const keys = Object.keys(order.payments);
                    if (keys.length > 0) paymentFirst = order.payments[keys[0]];
                }

                // แสดงรูปหลักฐานแทน bank-logo
                const bankLogoEl = document.getElementById('bank-logo');
                if (paymentFirst && paymentFirst.pic) {
                    bankLogoEl.src = paymentFirst.pic;
                    bankLogoEl.style.display = 'block';
                } else {
                    bankLogoEl.style.display = 'none';
                }

                this.renderOrderSummary(order.items || []);
            },


            renderOrderSummary(items) {
                const orderItemsEl = document.getElementById('order-items');
                orderItemsEl.innerHTML = '';
                let total = 0;
                items.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'rcp-order-item';
                    div.innerHTML = `<span>${item.name} x ${item.qty}</span><span>${this.formatPrice(item.qty * item.price)}</span>`;
                    orderItemsEl.appendChild(div);
                    total += item.qty * item.price;
                });
                document.getElementById('order-total').textContent = 'รวมทั้งหมด: ' + this.formatPrice(total);
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
                    // สามารถต่อยอด fetch POST อัพโหลดไฟล์ไป PHP ได้
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

            init() {
                this.loadOrder();
                document.getElementById('fetch-order-btn').addEventListener('click', () => this.fetchOrder());
                document.getElementById('proof-preview-container').addEventListener('click', () => document.getElementById('proof-file-input').click());
                document.getElementById('proof-file-input').addEventListener('change', e => this.previewFile(e));
                document.getElementById('save-proof-btn').addEventListener('click', () => this.saveProof());
            }
        };

        document.addEventListener('DOMContentLoaded', () => PaymentApp.init());
    </script>

</body>

</html>
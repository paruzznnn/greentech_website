<?php include '../../routes.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-STORE</title>
    <?php include '../../inc-meta.php'; ?>
    <link href="../../css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../../inc-cdn.php'; ?>
    <style>
        /* --- Card Container --- */
        .rcp-card {
            background-color: #fff;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        /* --- Header --- */
        .rcp-header {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
            gap: 1rem;
        }

        .rcp-header-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1f2937;
        }

        .rcp-header-subtitle {
            font-size: 1rem;
            color: #6b7280;
        }

        .rcp-logo img {
            max-width: 120px;
            height: auto;
        }

        /* --- Details Section --- */
        .rcp-details {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
            font-size: 0.875rem;
            color: #4b5563;
            margin-bottom: 1.5rem;
        }

        .rcp-details strong {
            font-weight: 600;
        }

        .rcp-right-align {
            text-align: left;
        }

        /* --- Items Section --- */
        .rcp-items-section h3,
        .rcp-services-section h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.75rem;
        }

        .rcp-items-table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            font-size: 0.875rem;
        }

        .rcp-items-table th,
        .rcp-items-table td {
            padding: 0.75rem;
            text-align: left;
            border: 1px solid #e5e7eb;
        }

        .rcp-items-table th {
            background-color: #f9fafb;
            font-weight: 600;
        }

        .rcp-items-table td:nth-child(2) {
            text-align: center;
        }

        .rcp-items-table td:nth-child(3),
        .rcp-items-table td:nth-child(4) {
            text-align: right;
        }

        /* --- Services Section --- */
        #order-services-list {
            list-style: none;
            padding: 0;
            margin: 0;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            font-size: 0.875rem;
        }

        #order-services-list li {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 1rem;
            border-bottom: 1px solid #e5e7eb;
            color: #4b5563;
        }

        #order-services-list li:last-child {
            border-bottom: none;
        }

        #order-services-list li.text-center {
            text-align: center;
            justify-content: center;
        }

        /* --- Totals Section --- */
        .rcp-summary-content {
            width: 100%;
            max-width: 320px;
            margin-left: auto;
            margin-top: 1rem;
        }

        #order-totals-list {
            padding: 0.5rem 1rem !important;
        }

        #order-totals-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            font-size: 0.875rem;
            color: #4b5563;
        }

        #order-totals-list li.rcp-total {
            font-size: 1.125rem;
            font-weight: 700;
            color: #1f2937;
            padding-top: 0.75rem;
            border-top: 2px dashed #d1d5db;
        }

        /* --- Footer Notes --- */
        .rcp-footer-notes {
            text-align: center;
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 1.5rem;
        }

        /* --- Action Buttons --- */
        .rcp-action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 1.5rem;
            justify-content: center;
        }

        .rcp-action-buttons button {
            background-color: #2563eb;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 0.5rem 1rem;
            cursor: pointer;
            font-size: 0.875rem;
            transition: background-color 0.3s ease;
        }

        .rcp-action-buttons button:hover {
            background-color: #1d4ed8;
        }

        @media (max-width: 480px) {
            .rcp-action-buttons {
                flex-direction: column;
                width: 100%;
            }
            .rcp-action-buttons button {
                width: 100%;
            }
        }

        /* --- Responsive Media Queries --- */
        @media (min-width: 640px) {
            .rcp-header {
                flex-direction: row;
                align-items: center;
            }

            .rcp-details {
                grid-template-columns: repeat(2, 1fr);
                gap: 2rem;
            }

            .rcp-right-align {
                text-align: right;
            }
        }

        @media (min-width: 1024px) {
            .rcp-card {
                padding: 2rem;
            }

            .rcp-header-title {
                font-size: 2rem;
            }

            .rcp-items-table th,
            .rcp-items-table td {
                padding: 1rem;
            }

            .rcp-summary-content {
                max-width: 400px;
            }
        }
    </style>
</head>

<body>
    <?php include '../../template/head-bar.php'; ?>
    <main>
        <div id="sections_root_orders" class="section-space">
            <div class="container">
                <div class="rcp-card">
                    <div class="rcp-card-body">

                        <!-- Header -->
                        <div class="rcp-header">
                            <div>
                                <h1 class="rcp-header-title">ใบเสร็จการสั่งซื้อ</h1>
                                <p class="rcp-header-subtitle">ขอบคุณสำหรับการสั่งซื้อของคุณ!</p>
                            </div>
                            <div class="rcp-logo">
                                <img src="http://localhost:3000/trandar_website/store/trandar_logo.png" alt="">
                            </div>
                        </div>

                        <!-- Details -->
                        <div class="rcp-details">
                            <div>
                                <p><strong>หมายเลขออเดอร์:</strong> <span id="order-id">#123456789</span></p>
                                <p><strong>วันที่:</strong> 16 กันยายน 2568</p>
                                <p><strong>สถานะการชำระเงิน:</strong> ชำระเงินเรียบร้อยแล้ว</p>
                            </div>
                            <div class="rcp-right-align">
                                <p><strong>ลูกค้า:</strong> สมชาย ใจดี</p>
                                <p><strong>ที่อยู่จัดส่ง:</strong> 123 ถนนสุขสบาย แขวงดีจัง เขตสบายดี กรุงเทพฯ 10110</p>
                                <p><strong>เบอร์โทรศัพท์:</strong> 081-234-5678</p>
                            </div>
                        </div>

                        <!-- Items -->
                        <div class="rcp-items-section">
                            <h3>รายละเอียดสินค้า</h3>
                            <div class="rcp-table-responsive">
                                <table class="rcp-items-table">
                                    <thead>
                                        <tr>
                                            <th>สินค้า</th>
                                            <th class="text-center">จำนวน</th>
                                            <th class="text-right">ราคาต่อหน่วย</th>
                                            <th class="text-right">ราคารวม</th>
                                        </tr>
                                    </thead>
                                    <tbody id="order-items-tbody"></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Services -->
                        <div class="rcp-services-section">
                            <h3>บริการเสริม</h3>
                            <ul id="order-services-list">
                                <li class="text-center">ไม่มีบริการเสริม</li>
                            </ul>
                        </div>

                        <!-- Totals -->
                        <div class="rcp-summary-content">
                            <ul id="order-totals-list"></ul>
                        </div>

                        <!-- Footer -->
                        <div class="rcp-footer-notes">
                            <p>ใบเสร็จนี้ถูกสร้างขึ้นโดยระบบอัตโนมัติ กรุณาเก็บไว้เป็นหลักฐาน</p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="rcp-action-buttons">
                            <button onclick="window.location.href='../../index.php'">กลับไปหน้าหลัก</button>
                            <button onclick="window.location.href='order-list.php'">ไปที่รายการสั่งซื้อ</button>
                            <button onclick="document.getElementById('payment-file').click()">แนบไฟล์การชำระเงิน</button>
                            <input type="file" id="payment-file" style="display:none" onchange="handlePaymentFile(event)">
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include '../../template/footer-bar.php'; ?>

    <script>
        const OrderSummaryRenderer = {
            cartItems: [],
            services: [],
            summary: {},
            orderId: '',

            formatPrice(value) {
                return Number(value).toLocaleString('th-TH', {
                    style: 'currency',
                    currency: 'THB'
                });
            },

            loadFromStorage() {
                const data = localStorage.getItem("checkoutAppData");
                if (!data) return;
                try {
                    const parsed = JSON.parse(data);
                    this.cartItems = parsed.cartItems || [];
                    this.services = parsed.services || [];
                    this.summary = parsed.summary || {};
                    this.orderId = parsed.order_id || '#N/A';
                } catch (e) {
                    console.error(e);
                }
            },

            renderItems() {
                const tbody = document.getElementById('order-items-tbody');
                tbody.innerHTML = '';
                if (!this.cartItems.length) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center">ไม่มีสินค้า</td></tr>';
                    return;
                }
                this.cartItems.forEach(item => {
                    const row = document.createElement('tr');
                    const total = item.price * item.qty;
                    row.innerHTML = `<td>${item.name}</td><td class="text-center">${item.qty}</td><td class="text-right">${this.formatPrice(item.price)}</td><td class="text-right">${this.formatPrice(total)}</td>`;
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
                this.services.forEach(service => {
                    const li = document.createElement('li');
                    li.innerHTML = `<span>${service.name}</span><span>${this.formatPrice(service.price)}</span>`;
                    list.appendChild(li);
                });
            },

            renderTotals() {
                const list = document.getElementById('order-totals-list');
                list.innerHTML = '';

                let subtotalItems = this.summary.subtotal || 0;
                let subtotalServices = this.services.reduce((acc, s) => acc + s.price, 0);
                let totalSubtotal = subtotalItems + subtotalServices;

                const {
                    discount = 0,
                    shipping = 0,
                    tax = 0
                } = this.summary;

                const total = totalSubtotal - discount + shipping + tax;

                const totals = [
                    { label: 'ยอดรวมสินค้า:', value: subtotalItems },
                    { label: 'รวมบริการเสริม:', value: subtotalServices },
                    { label: 'ส่วนลด:', value: -discount },
                    { label: 'ค่าจัดส่ง:', value: shipping },
                    { label: 'ภาษีมูลค่าเพิ่ม:', value: tax },
                    { label: 'ยอดชำระทั้งหมด:', value: total, isTotal: true }
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
            },

            init() {
                this.loadFromStorage();
                this.renderItems();
                this.renderServices();
                this.renderTotals();
                this.updateUI();
            }
        };

        function handlePaymentFile(event) {
            const file = event.target.files[0];
            if (file) {
                alert('คุณได้แนบไฟล์: ' + file.name);
                // สามารถส่งไฟล์ไป server ด้วย fetch หรือ form ตามต้องการ
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            // ตัวอย่าง mock data สำหรับทดสอบ
            const mockData = {
                cartItems: [
                    { name: 'เสื้อยืดลายสวย (ขนาด: M, สี: เทา)', price: 590, qty: 1 },
                    { name: 'กางเกงยีนส์สุดเท่ (ขนาด: L, สี: ดำ)', price: 1250, qty: 1 }
                ],
                services: [
                    { name: 'บริการห่อของขวัญ', price: 50 },
                    { name: 'บริการจัดส่งด่วน', price: 100 }
                ],
                summary: { subtotal: 1840, discount: 0, shipping: 50, tax: 0 },
                order_id: 'ORDER-987654321'
            };
            localStorage.setItem('checkoutAppData', JSON.stringify(mockData));

            OrderSummaryRenderer.init();
        });
    </script>
</body>

</html>

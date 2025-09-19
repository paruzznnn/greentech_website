<?php include '../../routes.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-STORE</title>
    <?php include '../../inc-meta.php'; ?>
    <link href="../../css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../../css/user/template-orders.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../../inc-cdn.php'; ?>
</head>

<body>
    <?php include '../../template/head-bar.php'; ?>
    <main>
        <div id="sections_root_orders" class="section-space">
            <div class="container">
                <div class="rcp-card">
                    <div class="rcp-card-body">
                        <div class="rcp-header">
                            <div>
                                <h1 class="rcp-header-title">เอกสารการสั่งซื้อ</h1>
                                <p class="rcp-header-subtitle">ขอบคุณสำหรับการสั่งซื้อของคุณ!</p>
                            </div>
                            <div class="rcp-logo">
                                <img src="http://localhost:3000/trandar_website/store/trandar_logo.png" alt="Logo">
                            </div>
                        </div>
                        <div class="rcp-details">
                            <div>
                                <p><strong>หมายเลขออเดอร์:</strong> <span id="order-id">#N/A</span></p>
                                <p><strong>วันที่:</strong> <span id="order-date"></span></p>
                                <p><strong>สถานะการชำระเงิน:</strong> <span id="payment-status"></span></p>
                            </div>
                            <div class="rcp-right-align">
                                <p><strong>ลูกค้า:</strong> <span id="customer-name"></span></p>
                                <p><strong>ที่อยู่จัดส่ง:</strong> <span id="customer-address"></span></p>
                                <p><strong>เบอร์โทรศัพท์:</strong> <span id="customer-phone"></span></p>
                            </div>
                        </div>
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
                        <div class="rcp-billing-summary">
                            <div class="rcp-services-section">
                                <h3>บริการเสริม</h3>
                                <ul id="order-services-list" class="rcp-list"></ul>
                                <h3>ส่วนลด</h3>
                                <ul id="order-discounts-list" class="rcp-list"></ul>
                                <h3>การจัดส่ง</h3>
                                <ul id="order-shipping-list" class="rcp-list"></ul>
                            </div>
                            <div class="rcp-summary-content">
                                <h3>สรุปยอด</h3>
                                <ul id="order-totals-list"></ul>
                            </div>
                        </div>
                        <div class="rcp-footer-notes">
                            <p>ใบเสร็จนี้ถูกสร้างขึ้นโดยระบบอัตโนมัติ กรุณาเก็บไว้เป็นหลักฐาน</p>
                        </div>
                        <div class="rcp-action-buttons">
                            <a href="<?php echo $GLOBALS['BASE_WEB']; ?>">กลับไปหน้าหลัก</a>
                            <a href="<?php echo $GLOBALS['BASE_WEB']; ?>user/">ไปที่รายการสั่งซื้อ</a>
                            <a href="#" type="button" id="payOrders" >การชำระเงิน</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include '../../template/footer-bar.php'; ?>
    <script type="module" src="<?php echo $GLOBALS['BASE_WEB']; ?>js/user/ordersRender.js?v=<?php echo time(); ?>"></script>
</body>

</html>
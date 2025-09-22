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
                            </div>

                            <div class="rcp-bank-details" id="bank-details">
                                <h5>ช่องทางการชำระเงินที่ท่านเลือก</h5>
                                <img id="bank-logo" src="" alt="Bank Logo">
                                <div class="rcp-bank-details-info">
                                    <p id="bank-name"></p>
                                    <p><strong>ชื่อบัญชี:</strong> <span id="account-name"></span></p>
                                    <p>
                                        <strong>เลขที่บัญชี:</strong> <span id="account-number"></span>
                                        <i id="copy-account-btn" class="far fa-clone"></i>
                                    </p>
                                </div>
                            </div>

                            <div class="rcp-order-summary" id="order-summary">
                                <div id="order-items"></div>
                            </div>

                            <div class="rcp-footer-notes">
                                <p>
                                    ขอบคุณที่สั่งซื้อกับเรา ใบสั่งซื้อของคุณกำลังอยู่ในกระบวนการตรวจสอบและจัดเตรียมสินค้า
                                    คุณสามารถตรวจสอบสถานะคำสั่งซื้อและรายละเอียดอื่น ๆ ได้ที่หน้าประวัติคำสั่งซื้อ
                                    หากมีข้อสงสัย กรุณาติดต่อฝ่ายบริการลูกค้า
                                </p>
                            </div>

                        </div>

                        <div class="rcp-payment-proof-section">
                            <h5><i class="bi bi-box-seam"></i> กรอกหมายสั่งใบสั่งซื้อ</h5>
                            <input type="text" id="order-id-input" class="form-input rcp-input" value="<?php echo isset($_GET['id']) ? $_GET['id'] : ''; ?>" placeholder="กรอกเลข Order ID">
                            <button id="fetch-order-btn" class="rcp-btn rcp-btn-orange rcp-full-width">ดึงข้อมูลคำสั่งซื้อ</button>

                            <div class="rcp-file-input-container" id="proof-preview-container">
                                <div class="rcp-proof-preview">
                                    <img id="proof-img-preview" src="" alt="Proof Preview" class="rcp-proof-img">
                                    <span id="proof-placeholder">สลิปรูปภาพหลักฐานการชำระเงิน</span>
                                </div>
                                <input type="file" id="proof-file-input" accept="image/*" class="rcp-hidden">
                            </div>

                            <p class="terms-text">
                                การสั่งซื้อของคุณถือเป็นการยอมรับ
                                <a href="<?php echo $GLOBALS['BASE_WEB']; ?>" class="terms-link">ข้อกำหนดการให้บริการ</a> และ
                                <a href="<?php echo $GLOBALS['BASE_WEB']; ?>" class="terms-link">นโยบายความเป็นส่วนตัว</a> กรุณาตรวจสอบข้อมูลการสั่งซื้อให้ถูกต้อง
                            </p>

                            <div class="rcp-action-buttons" id="rcp-action-buttons">
                                <button id="save-proof-btn" class="rcp-btn rcp-btn-green">บันทึกการแนบไฟล์</button>
                                <a href="<?php echo $GLOBALS['BASE_WEB']; ?>" class="rcp-btn rcp-btn-secondary">กลับหน้าหลัก</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include '../../template/footer-bar.php'; ?>
    <script type="module" src="<?php echo $GLOBALS['BASE_WEB']; ?>js/user/paymentRender.js?v=<?php echo time(); ?>"></script>
</body>

</html>
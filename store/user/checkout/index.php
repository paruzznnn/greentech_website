<?php include '../../routes.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-STORE</title>
    <?php include '../../inc-meta.php'; ?>
    <link href="../../css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../../css/user/template-checkout.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../../inc-cdn.php'; ?>
</head>

<body>

    <?php include '../../template/head-bar.php'; ?>

    <div id="address-modal" class="store-modal">
        <div class="store-modal-content">
            <span class="modal-close store-close-modal">&times;</span>
            <!-- <h3 id="modal-title"><span>Add Address</span></h3> -->
            <form id="address-form">
                <!-- <input type="text" id="addr-title" class="form-input" placeholder="Title (Home/Office/etc)" required>
                <input type="text" id="addr-name" class="form-input" placeholder="Full Name" required> -->
                <!-- <input type="text" id="addr-line" class="form-input" placeholder="Address Line" required> -->
                <!-- <input type="text" id="addr-phone" class="form-input" placeholder="Phone Number" required> -->

                <div class="form-group">
                    <label for="province" class="form-label">จังหวัด:</label>
                    <select id="province" name="province" class="form-input" required>
                        <option value="">เลือกจังหวัด</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="district" class="form-label">อำเภอ/เขต</label>
                    <select id="district" name="district" class="form-input" required disabled>
                        <option value="">เลือกอำเภอ/เขต</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="subdistrict" class="form-label">ตำบล/แขวง</label>
                    <select id="subdistrict" name="subdistrict" class="form-input" required disabled>
                        <option value="">เลือกตำบล/แขวง</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="postalCode" class="form-label">รหัสไปรษณีย์:</label>
                    <input type="text" id="postalCode" name="postalCode" class="form-input" value="" readonly>
                </div>

                <div class="full-width form-group">
                    <label for="address_detail" class="form-label">ที่อยู่:</label>
                    <textarea id="address_detail" name="address_detail" class="form-input" style="min-height: 60px !important;" required></textarea>
                </div>

                <div style="margin-top:10px; text-align:right;">
                    <button type="button" id="modal-cancel">Cancel</button>
                    <button type="submit" id="modal-save">Save</button>
                </div>
            </form>
        </div>
    </div>

    <main>
        <div id="sections_root_checkout" class="section-space">
            <div class="container">
                <div class="row">
                    <div class="col-md-8">
                        <div id="accordion-items" class="accordion-section"></div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkout-card">
                            <h5>รายละเอียดคำสั่งซื้อ</h5>
                            <div id="order-details"></div>
                        </div>
                        <div class="summary-card" id="order-summary">
                            <h5>สรุปคำสั่งซื้อ</h5>
                            <div id="summary-items"></div>
                            <!-- <button id="confirmOrders" disabled>ยันยืนคำสั่งซื้อ</button> -->
                            <p class="terms-text">
                                การสั่งซื้อของคุณถือเป็นการยอมรับ
                                <a href="#" class="terms-link">ข้อกำหนดการให้บริการ</a> และ
                                <a href="#" class="terms-link">นโยบายความเป็นส่วนตัว</a>
                                ของเรา กรุณาตรวจสอบข้อมูลการสั่งซื้อให้ถูกต้อง
                                เวลาจัดส่งเป็นการประมาณการณ์และอาจมีการเปลี่ยนแปลงตามความพร้อมของสินค้า.
                            </p>
                            <button id="backCart">กลับตะกร้าสินค้า</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include '../../template/footer-bar.php'; ?>
    <script type="module" src="<?php echo $GLOBALS['BASE_WEB']; ?>js/user/checkoutRender.js?v=<?php echo time(); ?>"></script>

</body>

</html>
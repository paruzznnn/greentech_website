<?php include '../../routes.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-STORE</title>
    <?php include '../../inc-meta.php'; ?>
    <link href="../../css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../../css/user/template-cart.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../../inc-cdn.php'; ?>
</head>

<body>

    <?php include '../../template/head-bar.php'; ?>

    <!-- <div id="address-modal" class="store-modal">
        <div class="store-modal-content">
            <span class="modal-close store-close-modal">&times;</span>
            <form id="address-form">
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
    </div> -->

    <main>
        <div id="sections_root_cart" class="section-space">
            <div class="container">
                <div class="row">
                    <div class="col-md-8">
                        <section>
                            <div class="cart-items-section">
                                <div class="cart-view-toggle">
                                    <h2 class="cart-title"><i class="bi bi-cart3"></i> <span>ตะกร้าสินค้า</span></h2>
                                    <div>
                                        <button id="listModeBtn"><i class="bi bi-list-task"></i></button>
                                        <button id="gridModeBtn"><i class="bi bi-grid-3x3-gap-fill"></i></button>
                                    </div>
                                </div>
                                <div id="cartItemList" class="cart-item-list list-mode"></div>
                            </div>
                        </section>
                    </div>
                    <div class="col-md-4">
                        <aside>
                            <div class="delivery-section">
                                <h3 class="delivery-title">วิธีรับสินค้า</h3>
                                <div id="shipping"></div>
                                <div class="delivery-options" id="deliveryOptions"></div>
                                <div class="pickup-options" id="pickupOptions"></div>
                                <div id="shippingRecommendation" class="alert alert-info"></div>
                                <div class="summary-weight-card">
                                    <p><strong>น้ำหนักรวม:</strong> <span id="totalWeightDisplay">0.00 กก.</span></p>
                                </div>
                            </div>
                        </aside>
                        <aside>
                            <div class="service-section">
                                <h3 class="service-title">บริการเสริม</h3>
                                <div class="service-options"></div>
                            </div>
                        </aside>
                        <aside>
                            <div class="summary-section">
                                <h2 class="summary-title">สรุป</h2>
                                <div id="summaryDetails" class="summary-details"></div>
                                <button id="checkoutOrders" class="checkout-button">สั่งซื้อรายการสินค้า</button>
                                <!-- <button id="" class="">ขอใบเสนอราคา</button> -->
                                <p class="terms-text">
                                    การสั่งซื้อของคุณถือเป็นการยอมรับ
                                    <a href="<?php echo $GLOBALS['BASE_WEB']; ?>" class="terms-link">ข้อกำหนดการให้บริการ</a> และ
                                    <a href="<?php echo $GLOBALS['BASE_WEB']; ?>" class="terms-link">นโยบายความเป็นส่วนตัว</a>
                                    ของเรา กรุณาตรวจสอบข้อมูลการสั่งซื้อให้ถูกต้อง
                                    เวลาจัดส่งเป็นการประมาณการณ์และอาจมีการเปลี่ยนแปลงตามความพร้อมของสินค้า.
                                </p>
                            </div>
                        </aside>
                        <aside>
                            <div class="discount-section">
                                <p id="discountMessage"></p>
                                <h3 class="discount-title">ใช้คูปอง</h3>
                                <a href="<?php echo $GLOBALS['BASE_WEB'];?>user/coupon/">
                                    <i class="bi bi-tags"></i>
                                    เก็บคูปองส่วนลดที่นี้
                                </a>
                                <div id="couponList"></div>
                                <h3 class="discount-title">รหัสส่วนลด</h3>
                                <div class="discount-code-group">
                                    <input type="text" id="discountCode" class="discount-input" placeholder="กรอกรหัสส่วนลด">
                                </div>
                                <button id="applyDiscount" class="discount-apply-btn">ใช้รหัส</button>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include '../../template/footer-bar.php'; ?>
    <script type="module" src="<?php echo $GLOBALS['BASE_WEB']; ?>js/user/cartRender.js?v=<?php echo time(); ?>"></script>

</body>

</html>
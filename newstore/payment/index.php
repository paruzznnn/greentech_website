<?php include '../routes.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-STORE</title>
    <?php include '../inc-meta.php'; ?>
    <link href="../css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../css/template-payment.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../inc-cdn.php'; ?>
</head>

<body>

    <?php include '../template/head-bar.php'; ?>
    <main>
        <div id="section_root_payment">
            <section id="section_payment" class="section-space">
                <div class="container">
                    <form id="formOrder" data-url="<?php echo $BASE_WEB ?>service/payment/payment-data.php" data-redir="<?php echo $BASE_WEB ?>user/" data-type="pay">
                        <div class="row">

                            <div class="col-md-6 col-sm-12">

                                <!-- ข้อมูลสั่งซื้อ -->
                                <div class="section-card">
                                    <h5><span>เลขที่สั่งซื้อ:</span> <span id="order-code"></span></h5>
                                    <div><span>วันที่สั่งซื้อ:</span> <span id="order-date"></span></div>
                                </div>

                                <!-- เลือกวิธีรับสินค้า -->
                                <div class="section-card">
                                    <h5 class="section-header">เลือกวิธีรับสินค้า</h5>
                                    <div class="selection-options-grid">
                                        <div class="selection-card delivery">
                                            <input class="hidden-radio" type="radio" name="delivery_option" id="delivery_shipping" value="shipping" checked>
                                            <label for="delivery_shipping">
                                                <i class="fas fa-truck"></i>
                                                <span>จัดส่งโดยขนส่งพัสดุ</span>
                                            </label>
                                        </div>
                                        <div class="selection-card delivery">
                                            <input class="hidden-radio" type="radio" name="delivery_option" id="delivery_pickup" value="pickup">
                                            <label for="delivery_pickup">
                                                <i class="fas fa-truck-pickup"></i>
                                                <!-- <i class="bi bi-person-walking"></i> -->
                                                <span>รับสินค้าที่สาขา</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- ที่อยู่จัดส่ง -->
                                <div id="shippingAddressFormSection" class="section-card">
                                    <div class="section-header">
                                        <div>
                                            <p>กรอกข้อมูลที่อยู่จัดส่ง</p>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <span style="font-size: 0.8rem;">ตามการตั้งค่า</span>
                                            <label class="toggle-switch">
                                                <input type="checkbox" id="setupShipping"/>
                                                <span class="slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-grid">
                                        <div class="form-group">
                                            <label for="full_name" class="form-label">ชื่อ-นามสกุล:</label>
                                            <input type="text" id="full_name" name="full_name" class="form-input" value="">
                                        </div>
                                        <div class="form-group">
                                            <label for="phone_number" class="form-label">เบอร์โทรศัพท์:</label>
                                            <input type="tel" id="phone_number" name="phone_number" class="form-input" value="">
                                        </div>
                                        <div class="full-width form-group">
                                            <label for="address_detail" class="form-label">ที่อยู่:</label>
                                            <textarea id="address_detail" name="address_detail" class="form-input" style="min-height: 60px !important;"></textarea>
                                        </div>
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
                                    </div>
                                </div>

                                <!-- เลือกการชำระเงิน -->
                                <div class="section-card">
                                    <h5 class="section-header">เลือกบริการชำระเงิน</h5>
                                    <div class="payment-grid">
                                        <div class="selection-card payment">
                                            <input class="hidden-radio" type="radio" name="payment_method" id="payment_bank_transfer" value="bank_transfer" checked>
                                            <label for="payment_bank_transfer">
                                                <i class="fas fa-landmark"></i>
                                                <span>โอนเงินผ่านธนาคาร</span>
                                            </label>
                                        </div>
                                        <div class="selection-card payment">
                                            <input class="hidden-radio" type="radio" name="payment_method" id="payment_promptpay" value="promptpay">
                                            <label for="payment_promptpay">
                                                <i class="bi bi-qr-code-scan"></i>
                                                <span>สแกน QR Code</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>

                            <!-- รายการสินค้า + สรุป -->
                            <div class="col-md-6 col-sm-12">
                                <div class="section-card">
                                    <h5 class="section-header">รายการสินค้า</h5>
                                    <div class="product-list-header">
                                        <div>สินค้า</div>
                                        <div style="text-align: end;">ราคา</div>
                                        <div style="text-align: end;">จำนวน</div>
                                        <div style="text-align: end;">ราคารวม</div>
                                    </div>
                                    <div id="order-product" class="product-items-container"></div>
                                </div>

                                <!-- <div class="section-card">
                                    <h5 class="section-header">คูปอง</h5>
                                </div> -->

                                <!-- สรุปยอดรวม -->
                                <div class="summary-card section-card">
                                    <h5 class="section-header">สรุปยอดรวม</h5>
                                    <div class="summary-details">
                                        <div class="summary-row">
                                            <span class="summary-label">ราคารวมสินค้า</span>
                                            <span class="summary-value" id="subtotal">-</span>
                                        </div>
                                        <div class="summary-row">
                                            <span class="summary-label">ภาษีมูลค่าเพิ่ม (VAT 7%)</span>
                                            <span class="summary-value" id="vat-amount">-</span>
                                        </div>
                                        <div class="summary-row">
                                            <span class="summary-label">ค่าจัดส่ง</span>
                                            <span class="summary-value" id="shipping-cost-value">-</span>
                                        </div>
                                        <div class="summary-row">
                                            <span class="summary-label">ส่วนลด</span>
                                            <span class="summary-value" id="discount-value">-</span>
                                        </div>
                                        <div class="summary-row">
                                            <span class="summary-label">วิธีรับสินค้า</span>
                                            <span class="summary-value" id="selected-delivery-method">-</span>
                                        </div>
                                        <div class="summary-row">
                                            <span class="summary-label">วิธีชำระเงิน</span>
                                            <span class="summary-value" id="selected-payment-method">-</span>
                                        </div>
                                        <div class="total-row summary-row">
                                            <span class="total-label">ยอดรวมทั้งหมด</span>
                                            <span class="total-value" id="total-amount">-</span>
                                        </div>
                                    </div>
                                    <button id="confirm-order" type="submit">ยืนยันการสั่งซื้อ</button>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </main>
    <?php include '../template/footer-bar.php'; ?>


    <script type="module">
        const timeVersion = "<?= time() ?>";
        const baseWeb = `${pathConfig.BASE_WEB}`;

        Promise.all([
            import(`${baseWeb}js/formHandler.js?v=${timeVersion}`),
            import(`${baseWeb}js/payment/paymentRender.js?v=${timeVersion}`)
        ])
        .then(async ([formModule, paymentModule]) => {
            
            const formOrder = document.querySelector("#formOrder");
            formOrder?.addEventListener("submit", formModule.handleFormSubmit);

            const address = await paymentModule.fetchAddressData("getAddress", baseWeb + 'service/payment/address-data.php?');
            const provinces = await paymentModule.fetchProvincesData(baseWeb + 'locales/provinces.json');
            const districts = await paymentModule.fetchDistrictsData(baseWeb + 'locales/districts.json');
            const subdistricts = await paymentModule.fetchSubdistricts(baseWeb + 'locales/subdistricts.json');

            paymentModule.CheckoutUI.init(
                provinces,
                districts,
                subdistricts,
                address.data[0]
            );
        })
        .catch((e) => console.error("Module import failed", e));
    </script>



</body>

</html>
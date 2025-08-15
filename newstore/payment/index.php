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
                                    <div><span>เลขที่สั่งซื้อ:</span> <span>#ORD-20250814-12345</span></div>
                                    <div><span>วันที่สั่งซื้อ:</span> <span>14 สิงหาคม 2568</span></div>
                                </div>

                                <!-- เลือกวิธีรับสินค้า -->
                                <div class="section-card">
                                    <h2 class="section-header">เลือกวิธีรับสินค้า</h2>
                                    <div class="selection-options-grid">
                                        <div class="selection-card delivery">
                                            <input class="hidden-radio" type="radio" name="delivery_option" id="delivery_shipping" value="shipping" checked>
                                            <label for="delivery_shipping">
                                                <h5>จัดส่งถึงที่อยู่</h5>
                                                <span>จัดส่งโดยขนส่งพัสดุ</span>
                                            </label>
                                        </div>
                                        <div class="selection-card delivery">
                                            <input class="hidden-radio" type="radio" name="delivery_option" id="delivery_pickup" value="pickup">
                                            <label for="delivery_pickup">
                                                <h5>รับหน้าร้าน</h5>
                                                <span>รับสินค้าที่สาขา</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- ที่อยู่จัดส่ง -->
                                <div id="shippingAddressFormSection" class="section-card">
                                    <h2 class="section-header">กรอกข้อมูลที่อยู่จัดส่ง</h2>
                                    <form class="form-grid">
                                        <div>
                                            <label for="full_name" class="form-label">ชื่อ-นามสกุล:</label>
                                            <input type="text" id="full_name" name="full_name" class="form-input" value="">
                                        </div>
                                        <div>
                                            <label for="phone_number" class="form-label">เบอร์โทรศัพท์:</label>
                                            <input type="tel" id="phone_number" name="phone_number" class="form-input" value="">
                                        </div>
                                        <div class="full-width">
                                            <label for="address_line1" class="form-label">ที่อยู่:</label>
                                            <input type="text" id="address_line1" name="address_line1" class="form-input" value="">
                                        </div>
                                        <div class="full-width">
                                            <label for="address_line2" class="form-label">แขวง/เขต:</label>
                                            <input type="text" id="address_line2" name="address_line2" class="form-input" value="">
                                        </div>
                                        <div>
                                            <label for="province" class="form-label">จังหวัด:</label>
                                            <input type="text" id="province" name="province" class="form-input" value="">
                                        </div>
                                        <div>
                                            <label for="postal_code" class="form-label">รหัสไปรษณีย์:</label>
                                            <input type="text" id="postal_code" name="postal_code" class="form-input" value="">
                                        </div>
                                    </form>
                                </div>

                                <!-- เลือกการชำระเงิน -->
                                <div class="section-card">
                                    <h2 class="section-header">เลือกบริการชำระเงิน</h2>
                                    <div class="payment-grid">
                                        <div class="selection-card payment">
                                            <input class="hidden-radio" type="radio" name="payment_method" id="payment_bank_transfer" value="bank_transfer" checked>
                                            <label for="payment_bank_transfer">
                                                <h5>โอนเงินผ่านธนาคาร</h5>
                                                <span>โอนโดยตรงจากบัญชีธนาคาร</span>
                                            </label>
                                        </div>

                                        <div class="selection-card payment">
                                            <input class="hidden-radio" type="radio" name="payment_method" id="payment_promptpay" value="promptpay">
                                            <label for="payment_promptpay">
                                                <h5>พร้อมเพย์</h5>
                                                <span>สแกน QR Code</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- รายการสินค้า + สรุป -->
                            <div class="col-md-6 col-sm-12">
                                <div class="section-card">
                                    <h2 class="section-header">รายการสินค้า</h2>
                                    <div class="product-list-header">
                                        <div>สินค้า</div>
                                        <div style="text-align: end;">จำนวน</div>
                                        <div style="text-align: end;">ราคารวม</div>
                                    </div>

                                    <div class="product-items-container">
                                        <div class="product-item">
                                            <div><img src="https://placehold.co/80x80/E0E7FF/6366F1?text=Product+A" alt="Product A"></div>
                                            <div style="text-align: end;">1</div>
                                            <div style="text-align: end;">500.00 บาท</div>
                                        </div>
                                        <div class="product-item">
                                            <div><img src="https://placehold.co/80x80/DBEAFE/3B82F6?text=Product+B" alt="Product B"></div>
                                            <div style="text-align: end;">1</div>
                                            <div style="text-align: end;">800.00 บาท</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- สรุปยอดรวม -->
                                <div class="summary-card section-card">
                                    <h2 class="section-header">สรุปยอดรวม</h2>
                                    <div class="summary-details">
                                        <div class="summary-row">
                                            <span class="summary-label">ราคารวมสินค้า</span>
                                            <span class="summary-value">1,500.00 บาท</span>
                                        </div>
                                        <div class="summary-row">
                                            <span class="summary-label" id="shipping-cost-label">ค่าจัดส่ง</span>
                                            <span class="summary-value" id="shipping-cost-value">50.00 บาท</span>
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
                                            <span class="total-value" id="total-amount">1,550.00 บาท</span>
                                        </div>
                                    </div>
                                    <button type="submit">ยืนยันการสั่งซื้อ</button>
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
        .then(([formModule, paymentModule]) => {
            
            const formOrder = document.querySelector("#formOrder");
            formOrder?.addEventListener("submit", formModule.handleFormSubmit);

            paymentModule.CheckoutUI.init();
        })
        .catch((e) => console.error("Module import failed", e));
    </script>



</body>

</html>
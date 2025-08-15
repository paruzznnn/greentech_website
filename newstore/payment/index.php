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

    <?php //include '../template/head-bar.php'; ?>
    <!-- <main>
        <div id="sections_root_payment">
            <section id="sections_payment" class="section-space">
                
            </section>
        </div>
    </main> -->
    <?php //include '../template/footer-bar.php'; ?>

    <?php include '../template/head-bar.php'; ?>
        <main>
            <div id="section_root_payment">
                <section id="section_payment" class="section-space">
                    <div class="container">
                        <div class="main-container">
                            <div class="grid-layout">
                            
                                <div class="column-left">

                                    <div class="section-card bg-light">
                                        <div class="order-info-item">
                                            <span class="font-semibold">เลขที่สั่งซื้อ:</span> <span class="text-dark">#ORD-20250814-12345</span>
                                        </div>
                                        <div class="order-info-item">
                                            <span class="font-semibold">วันที่สั่งซื้อ:</span> <span class="text-dark">14 สิงหาคม 2568</span>
                                        </div>
                                    </div>
                                    
                                    <div class="section-card">
                                        <h2 class="section-header">
                                            เลือกวิธีรับสินค้า
                                        </h2>
                                        <div class="selection-options-grid">
                                            <div class="col-item">
                                                <div class="form-check">
                                                    <input class="hidden-radio selection-radio" type="radio" name="delivery_option" id="delivery_shipping" value="shipping" checked>
                                                    <label class="form-check-label" for="delivery_shipping">
                                                        <div class="selection-card">
                                                            
                                                            <h3>จัดส่งถึงที่อยู่</h3>
                                                            <p>จัดส่งโดยขนส่งพัสดุ</p>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-item">
                                                <div class="form-check">
                                                    <input class="hidden-radio selection-radio" type="radio" name="delivery_option" id="delivery_pickup" value="pickup">
                                                    <label class="form-check-label" for="delivery_pickup">
                                                        <div class="selection-card">
                                                            
                                                            
                                                            <h3>รับหน้าร้าน</h3>
                                                            <p>รับสินค้าที่สาขา</p>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="shippingAddressFormSection" class="section-card">
                                        <h2 class="section-header">
                                            
                                            กรอกข้อมูลที่อยู่จัดส่ง
                                        </h2>
                                        <form class="form-grid">
                                            <div>
                                                <label for="full_name" class="form-label">ชื่อ-นามสกุล:</label>
                                                <input type="text" id="full_name" name="full_name" class="form-input" placeholder="ชื่อ-นามสกุล" value="ชัยพร ใจดี">
                                            </div>
                                            <div>
                                                <label for="phone_number" class="form-label">เบอร์โทรศัพท์:</label>
                                                <input type="tel" id="phone_number" name="phone_number" class="form-input" placeholder="08X-XXX-XXXX" value="081-234-5678">
                                            </div>
                                            <div class="full-width">
                                                <label for="address_line1" class="form-label">ที่อยู่ (บ้านเลขที่, หมู่, ซอย, ถนน):</label>
                                                <input type="text" id="address_line1" name="address_line1" class="form-input" placeholder="123/45 ถนนสุขุมวิท ซอย 20" value="123/45 ถนนสุขุมวิท ซอย 20">
                                            </div>
                                            <div class="full-width">
                                                <label for="address_line2" class="form-label">แขวง/ตำบล, เขต/อำเภอ:</label>
                                                <input type="text" id="address_line2" name="address_line2" class="form-input" placeholder="แขวงคลองเตย เขตคลองเตย" value="แขวงคลองเตย เขตคลองเตย">
                                            </div>
                                            <div>
                                                <label for="province" class="form-label">จังหวัด:</label>
                                                <input type="text" id="province" name="province" class="form-input" placeholder="กรุงเทพมหานคร" value="กรุงเทพมหานคร">
                                            </div>
                                            <div>
                                                <label for="postal_code" class="form-label">รหัสไปรษณีย์:</label>
                                                <input type="text" id="postal_code" name="postal_code" class="form-input" placeholder="10110" value="10110">
                                            </div>
                                        </form>
                                    </div>

                                    <div class="section-card">
                                        <h2 class="section-header">
                                            เลือกบริการชำระเงิน
                                        </h2>
                                        <div class="selection-options-grid payment-grid-3-cols">

                                            <div class="col-item">
                                                <div class="form-check">
                                                    <input class="hidden-radio selection-radio" type="radio" name="payment_method" id="payment_bank_transfer" value="bank_transfer">
                                                    <label class="form-check-label" for="payment_bank_transfer">
                                                        <div class="selection-card">
                                                            <h3>โอนเงินผ่านธนาคาร</h3>
                                                            <p>โอนโดยตรงจากบัญชีธนาคาร</p>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="col-item">
                                                <div class="form-check">
                                                    <input class="hidden-radio selection-radio" type="radio" name="payment_method" id="payment_promptpay" value="promptpay">
                                                    <label class="form-check-label" for="payment_promptpay">
                                                        <div class="selection-card">
                                                            <h3>พร้อมเพย์</h3>
                                                            <p>สแกน QR Code</p>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="col-item">
                                                <div class="form-check">
                                                    <input class="hidden-radio selection-radio" type="radio" name="payment_method" id="payment_cod" value="cod">
                                                    <label class="form-check-label" for="payment_cod">
                                                        <div class="selection-card">
                                                            <h3>เก็บเงินปลายทาง</h3>
                                                            <p>ชำระเงินสดเมื่อได้รับสินค้า</p>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div class="column-right">

                                    <div class="section-card">
                                        <h2 class="section-header">
                                            <svg class="icon-medium" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                            </svg>
                                            รายการสินค้า
                                        </h2>

                                        <div class="product-list-header">
                                            <div>สินค้า</div>
                                            <div>จำนวน</div>
                                            <div>ราคารวม</div>
                                        </div>
                                        
                                        <div class="product-items-container">
                                            
                                            <div class="product-item">
                                                <div class="product-img-wrapper">
                                                    <img src="https://placehold.co/80x80/E0E7FF/6366F1?text=Product+A" alt="Product Image" class="product-img">
                                                </div>
                                                <div class="product-details">
                                                    <p class="title">เสื้อยืดคอตตอน 100%</p>
                                                    <p class="info">สี: ขาว, ไซส์: M</p>
                                                    <div class="price-mobile">
                                                        <span class="qty">จำนวน: 1</span>
                                                        <span class="amount">500.00 บาท</span>
                                                    </div>
                                                </div>
                                                <div class="product-qty-price-desktop">
                                                    <span class="qty">1</span>
                                                    <span class="amount">500.00 บาท</span>
                                                </div>
                                            </div>
                                            
                                            <div class="product-item">
                                                <div class="product-img-wrapper">
                                                    <img src="https://placehold.co/80x80/DBEAFE/3B82F6?text=Product+B" alt="Product Image" class="product-img">
                                                </div>
                                                <div class="product-details">
                                                    <p class="title">กางเกงยีนส์เดนิม</p>
                                                    <p class="info">สี: น้ำเงินเข้ม, ไซส์: L</p>
                                                    <div class="price-mobile">
                                                        <span class="qty">จำนวน: 1</span>
                                                        <span class="amount">800.00 บาท</span>
                                                    </div>
                                                </div>
                                                <div class="product-qty-price-desktop">
                                                    <span class="qty">1</span>
                                                    <span class="amount">800.00 บาท</span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="summary-card">
                                        <h2 class="section-header">
                                            <svg class="icon-medium" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            สรุปยอดรวม
                                        </h2>
                                        <div class="summary-details">
                                            <div class="summary-row">
                                                <span class="summary-label">ราคารวมสินค้า</span>
                                                <span class="summary-value">1,500.00 บาท</span>
                                            </div>
                                            <div class="summary-row">
                                                <span class="summary-label" id="shipping-cost-label">ค่าจัดส่ง</span>
                                                <span class="summary-value" id="shipping-cost-value">50.00 บาท</span>
                                            </div>
                                            <div class="total-row summary-row">
                                                <span class="total-label">ยอดรวมทั้งหมด</span>
                                                <span class="total-value" id="total-amount">1,550.00 บาท</span>
                                            </div>
                                        </div>

                                        <button class="btn-primary-custom">
                                            ยืนยันการสั่งซื้อ
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    <?php include '../template/footer-bar.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const deliveryOptionRadios = document.querySelectorAll('input[name="delivery_option"]');
            const shippingAddressFormSection = document.getElementById('shippingAddressFormSection');
            const shippingCostLabel = document.getElementById('shipping-cost-label');
            const shippingCostValue = document.getElementById('shipping-cost-value');
            const totalAmount = document.getElementById('total-amount');
            const baseTotal = 1500.00; // Example: total product cost

            function updateDeliveryOption(selectedOption) {
                if (selectedOption === 'shipping') {
                    shippingAddressFormSection.style.display = 'block'; // Show the form
                    shippingCostLabel.textContent = 'ค่าจัดส่ง';
                    shippingCostValue.textContent = '50.00 บาท';
                    totalAmount.textContent = (baseTotal + 50.00).toFixed(2) + ' บาท';
                } else { // pickup
                    shippingAddressFormSection.style.display = 'none'; // Hide the form
                    shippingCostLabel.textContent = 'ค่าจัดส่ง';
                    shippingCostValue.textContent = '0.00 บาท'; // No shipping cost for pickup
                    totalAmount.textContent = baseTotal.toFixed(2) + ' บาท';
                }
            }

            // Handle delivery option radio changes
            deliveryOptionRadios.forEach(radio => {
                radio.addEventListener('change', (event) => {
                    // Remove highlight from all delivery option cards first
                    document.querySelectorAll('.delivery-option-radio + label .selection-card').forEach(card => {
                        card.classList.remove('active-selection');
                    });
                    // Add highlight to the selected card
                    // NOTE: The 'active-selection' class will no longer apply visual changes after the CSS modifications.
                    const selectedCard = event.target.nextElementSibling.querySelector('.selection-card');
                    selectedCard.classList.add('active-selection');

                    updateDeliveryOption(event.target.value);
                });
            });

            // Set initial state for delivery option on load
            const initialDeliveryOption = document.querySelector('input[name="delivery_option"]:checked');
            if (initialDeliveryOption) {
                initialDeliveryOption.nextElementSibling.querySelector('.selection-card').classList.add('active-selection');
                updateDeliveryOption(initialDeliveryOption.value);
            }


            const paymentRadioButtons = document.querySelectorAll('input[name="payment_method"]');
            paymentRadioButtons.forEach(radio => {
                radio.addEventListener('change', (event) => {
                    // Remove highlight from all payment cards first
                    document.querySelectorAll('.payment-radio + label .selection-card').forEach(card => {
                        card.classList.remove('active-selection');
                    });

                    // Add highlight to the selected card
                    // NOTE: The 'active-selection' class will no longer apply visual changes after the CSS modifications.
                    const selectedCard = event.target.nextElementSibling.querySelector('.selection-card');
                    selectedCard.classList.add('active-selection');
                });
            });

            // Set initial state for the checked payment radio button on load
            const initialCheckedPayment = document.querySelector('input[name="payment_method"]:checked');
            if (initialCheckedPayment) {
                initialCheckedPayment.nextElementSibling.querySelector('.selection-card').classList.add('active-selection');
            }
        });
    </script>

</body>

</html>
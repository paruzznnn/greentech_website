<?php include '../routes.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-STORE</title>
    <?php include '../inc-meta.php'; ?>
    <link href="../css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../css/partner/template-partner.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../inc-cdn.php'; ?>

</head>

<body>
    <?php include '../template/head-bar.php'; ?>
    <main>
        <div id="sections_root_partner_list">
            <div class="container">
                <section class="section-space">
                    <div class="partner_cover_photo">
                        <img src="https://www.trandar.com//public/img/6891aee0f22e6.jpg" alt="Partner Cover" />
                    </div>
                </section>
                <section class="section-space">
                    <div style="background-color:#ffffff; padding:0.5rem; border-radius:4px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div id="showing-entries" class="showing-entries"></div>
                            <div class="controls">
                                <button id="gridViewBtn" class="active"><i class="bi bi-grid-3x3-gap-fill"></i></button>
                                <button id="listViewBtn"><i class="bi bi-list-task"></i></button>
                            </div>
                        </div>
                        <div id="stores-container" class="stores-container grid"></div>
                        <div id="pagination" class="pagination"></div>
                    </div>
                </section>
                <section class="section-space">
                    <div class="features-container">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="far fa-clock"></i></div>
                            <h3 class="feature-title">ตะกร้าราคาถูกที่สุด</h3>
                            <p class="feature-description">
                                รับคำสั่งซื้อของคุณส่งถึงหน้าประตูบ้านของคุณโดยเร็วที่สุดจากจุดรับสินค้า FreshCart ใกล้บ้านคุณ
                            </p>
                        </div>

                        <div class="feature-card">
                            <div class="feature-icon"><i class="fas fa-gift"></i></div>
                            <h3 class="feature-title">ราคาและข้อเสนอที่ดีที่สุด</h3>
                            <p class="feature-description">
                                ราคาถูกพร้อมข้อเสนอสุดพิเศษ
                            </p>
                        </div>

                        <div class="feature-card">
                            <div class="feature-icon"><i class="fas fa-box"></i></div>
                            <h3 class="feature-title">สินค้าหลากหลาย</h3>
                            <p class="feature-description">
                                เลือกจากผลิตภัณฑ์มากกว่า 5,000 รายการในหมวดหมู่
                            </p>
                        </div>

                        <div class="feature-card">
                            <div class="feature-icon"><i class="fas fa-desktop"></i></div>
                            <h3 class="feature-title">ช้อปกับเว็บไซต์ของเรา</h3>
                            <p class="feature-description">
                                ช้อปได้ทุกที่ด้วยเว็บไซต์ของเราสำหรับแท็บเล็ตและมือถือ ติดตามคำสั่งซื้อแบบเรียลไทม์ รับการอัปเดตฟีเจอร์ล่าสุด
                            </p>
                        </div>

                        <div class="feature-card">
                            <div class="feature-icon"><i class="fas fa-truck"></i></div>
                            <h3 class="feature-title">ต้องการช้อปปิ้งวันนี้ใช่ไหม?</h3>
                            <p class="feature-description">
                                เลือกจากตัวเลือกการจัดส่งด่วนหรือการรับสินค้าแบบด่วน
                            </p>
                        </div>

                        <div class="feature-card">
                            <div class="feature-icon"><i class="fas fa-sync-alt"></i></div>
                            <h3 class="feature-title">การคืนสินค้า/คืนเงินที่ง่ายดาย</h3>
                            <p class="feature-description">
                                ไม่พอใจสินค้าใช่ไหม? ส่งคืนสินค้าถึงหน้าบ้านและรับเงินคืนภายในไม่กี่ชั่วโมง ไม่มีคำถามใดๆ <a href="#">policy</a>
                            </p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>
    <?php include '../template/footer-bar.php';?>
    <script type="module" src="<?php echo $GLOBALS['BASE_WEB'];?>js/partner/partnerListRender.js?v=<?php echo time(); ?>"></script>

</body>

</html>
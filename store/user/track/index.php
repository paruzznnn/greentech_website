<?php include '../../routes.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-STORE</title>
    <?php include '../../inc-meta.php'; ?>
    <link href="../../css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../../css/user/template-track.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../../inc-cdn.php'; ?>

</head>

<body>
    <?php include '../../template/head-bar.php'; ?>
    <main>
        <div id="sections_root_track" class="section-space">
            <div class="container">
                <section>
                    <div class="large-steps" id="large-steps-container"></div>
                </section>
                <hr class="dashed-line">
                <section style="background: #ffffff; padding: 1rem;">
                    <div class="section-header">
                        <h2 style="font-size: 18px; color: #1f2937; font-weight: bold;">ที่อยู่ในการจัดส่ง</h2>
                        <div class="text-right small-text">
                            <p>ขนส่งบริษัทแทรนดาร์</p>
                            <p>TH253025994610B</p>
                            <p>คนขับรถ: เปรมชัย, <a href="">0970727598</a></p>
                        </div>
                    </div>
                </section>
                <hr class="dashed-line">
                <section style="background: #ffffff; padding: 1rem;">
                    <div class="shipping-info">
                        <div class="address-box">
                            <p class="font-semibold" style="color:#1f2937;">กิตตินันท์ธนัช สีแก้วน้ำใส</p>
                            <p class="small-text">0838945256</p>
                            <p class="small-text">โครงการลาดกระบังประชา 114/14 แขวงลำผักชี เขตหนองจอก จังหวัดกรุงเทพมหานคร 10530</p>
                        </div>
                        <div class="timeline-container-wrapper">
                            <div class="timeline" id="timeline-container"></div>
                            <div id="view-more-timeline"></div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>
    <?php include '../../template/footer-bar.php'; ?>
    <script type="module" src="<?php echo $GLOBALS['BASE_WEB']; ?>js/user/trackRender.js?v=<?php echo time(); ?>"></script>
</body>

</html>
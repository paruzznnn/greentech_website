<?php include '../../routes.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-STORE</title>
    <?php include '../../inc-meta.php'; ?>
    <link href="../../css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../../css/user/template-coupon.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../../inc-cdn.php'; ?>
</head>

<body>
    <?php include '../../template/head-bar.php'; ?>
    <main>
        <div id="sections_root_coupon" class="section-space">
            <div class="container">
                <section>
                    <div id="coupon-containe"></div>
                </section>
                <section>
                    <div id="couponpro-containe"></div>
                </section>
                <section>
                    <div id="store-containe"></div>
                </section>
            </div>
        </div>
    </main>
    <?php include '../../template/footer-bar.php'; ?>
    <script type="module" src="<?php echo $GLOBALS['BASE_WEB']; ?>js/user/couponRender.js?v=<?php echo time(); ?>"></script>
</body>

</html>
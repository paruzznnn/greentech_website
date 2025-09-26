<?php include '../../routes.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-STORE</title>
    <?php include '../../inc-meta.php'; ?>
    <link href="../../css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../../css/partner/template-partner.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../../inc-cdn.php'; ?>
</head>

<body>
    <?php include '../../template/head-bar.php'; ?>
    <main>
        <div id="sections_root_partner_detail" class="section-space">
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        <aside>
                            <div class="sidebar">
                                <div class="stores-card-info" id="storeContainer"></div>
                            </div>
                        </aside>
                    </div>
                    <div class="col-md-9">
                        <section>
                            <div class="partner_cover_photo">
                                <img id="partner_banner"/>
                            </div>
                        </section>
                        <section>
                            <div id="mainContent"></div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include '../../template/footer-bar.php'; ?>
    <script type="module" src="<?php echo $GLOBALS['BASE_WEB'];?>js/partner/partnerDetailRender.js?v=<?php echo time(); ?>"></script>
</body>

</html>
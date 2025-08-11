<?php include '../routes.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-STORE</title>
    <?php include '../inc-meta.php'; ?>
    <link href="../css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../css/template-partner.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../inc-cdn.php'; ?>
</head>

<body>
    <?php include '../template/head-bar.php'; ?>
    <main>
        <div id="sections_root_partner">
            <section id="sections_partner_cover_photo" class="section-space">
                <div class="container">
                    <div class="partner-banner">
                        <img class="partner-background" src="https://www.trandar.com//public/img/688b3c1b70d9b.jpg" alt="" />
                        <div class="partner-banner-overlay">
                            <div class="partner-logo-wrapper">
                                <img class="partner-logo" src="https://www.trandar.com//public/img/logo_688c431f30bf3.png" alt="" />
                            </div>
                            <div class="partner-banner-text">
                                <h1>Trandar Acoustics</h1>
                                <p>BRAND</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section id="sections_partner_layout" class="section-space-partner">
                <div class="container">
                    <div class="partner-tab-wrapper">
                        <div class="partner-left-menu">
                            <div id="dynamic-left-menu"></div>
                        </div>
                        <div class="partner-right-content">
                            <div class="partner-tab-buttons" id="tab-buttons"></div>
                            <div id="tab-contents"></div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
    <?php include '../template/footer-bar.php'; ?>

    <script type="module">

        import { 
            initTabs,
            fetchPartnerData
        } from '/newstore/js/partner/partnerRender.js?v=<?php echo time()?>';


        document.addEventListener('DOMContentLoaded', async () => {
            const tabs = await fetchPartnerData("getPartnerTabItems");
            const menuData = await fetchPartnerData("getPartnerMenuItems");
            const menuItems = await fetchPartnerData("getPartnerMenuArticleItems");

            const tabButtonsContainer = document.getElementById('tab-buttons');
            const tabContentsContainer = document.getElementById('tab-contents');
            const dynamicMenu = document.getElementById('dynamic-left-menu');

            initTabs({
                tabs,
                menuData,
                tabButtonsContainer,
                tabContentsContainer,
                dynamicMenu,
                parentSelector: '#articleMenu', 
                menuItems
            });

        });

    </script>

</body>

</html>
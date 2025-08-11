<?php include '../routes.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>E-STORE</title>
    <?php include '../inc-meta.php'; ?>
    <link href="../css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../css/template-product.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../inc-cdn.php'; ?>

</head>
<body>
    <?php include '../template/head-bar.php'; ?>
    <main>
        <div id="sections_root_products">
            <section id="sections_search_products" class="section-space-search">
                <div class="container">
                    <div class="row">
                        <div class="col-md-3">
                            <ul id="categoryMenu" class="search-box-menu"></ul>
                        </div>
                        <div class="col-md-9">
                            <div class="row" id="card-container"></div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
    <?php include '../template/footer-bar.php';?>

    <script type="module">
        import(`${pathConfig.BASE_WEB}js/product/productRender.js?v=<?= time() ?>`)
        .then(async ({ 
            initCardUI, 
            fetchCategoryData, 
            buildCategoryMenu
        }) => {

            initCardUI({
            containerId: 'card-container',
            apiUrl: pathConfig.BASE_WEB + 'service/product/product-data.php?action=getProductItems',
            authToken: 'my_secure_token_123',
            BASE_WEB: pathConfig.BASE_WEB
            });

            const service = pathConfig.BASE_WEB + 'service/product/category-data.php?';
            const menuItems = await fetchCategoryData("getCategoryItems", service);
            if(menuItems){
                buildCategoryMenu('#categoryMenu', menuItems.data);
            }
            
        })
        .catch((e) => console.error("Module import failed", e));
    </script>

</body>

</html>
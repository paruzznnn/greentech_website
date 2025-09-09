<?php include '../../routes.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>E-STORE</title>
    <?php include '../../inc-meta.php'; ?>
    <link href="../../css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../../css/product/template-product.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../../inc-cdn.php'; ?>
</head>

<body>

    <?php include '../../template/head-bar.php'; ?>
    <main>
        <div id="sections_root_product_detail">
            <section id="sections_products_deatil" class="section-space-search">
                <div class="container">
                    <div id="product-detail-container-vibrant"></div>
                    <!-- <div id="product-similar-container" class="owl-carousel owl-theme"></div> -->
                </div>
            </section>
        </div>
    </main>
    <?php include '../../template/footer-bar.php'; ?>

    <script type="module">
        import(`${pathConfig.BASE_WEB}js/product/productDetailRender.js?v=<?= time() ?>`)
        .then(async ({ 
            fetchProductData,
            ProductDetailModule,
            createProductSimilarHTML
        }) => {

            let reqProduct = {
                productId: <?php echo json_encode($_GET['id']); ?>
            };

            const service = pathConfig.BASE_WEB + 'service/product/product-data.php?';
            const productDeatilItems = await fetchProductData("getProductDetailItems", service, reqProduct);
            // const productSimilarItems = await fetchProductData("getProductSimilarItems", service, reqProduct);
            if(productDeatilItems.data){
                ProductDetailModule.init(
                    "#product-detail-container-vibrant", 
                    productDeatilItems.data[0], 
                    pathConfig.BASE_WEB
                );
            }
            // if(productSimilarItems.data){
                // createProductSimilarHTML("#product-similar-container", productSimilarItems.data);
            // }
            
        })
        .catch((e) => console.error("Module import failed", e));
    </script>

</body>

</html>
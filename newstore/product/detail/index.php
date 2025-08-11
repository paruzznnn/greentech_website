<?php include '../../routes.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>E-STORE</title>
    <?php include '../../inc-meta.php'; ?>
    <link href="../../css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../../css/template-product.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../../inc-cdn.php'; ?>
</head>

<body>

    <?php include '../../template/head-bar.php'; ?>
    <main>
        <div id="sections_root_product_detail">
            <section id="sections_products_deatil" class="section-space-search">
                <div class="container">
                    <div id="product-detail-container-vibrant"></div>
                    <div id="product-similar-container" class="owl-carousel owl-theme"></div>
                </div>
            </section>
        </div>
    </main>
    <?php include '../../template/footer-bar.php'; ?>

    <script type="module">
        // console.log('AppConfig BASE_WEB', AppConfig.BASE_WEB);
        import {
            fetchProductData,
            createProductDetailHTML,
            initProductDetailLogic,
            createProductSimilarHTML
        } from '../../js/product/productDetailRender.js?v=<?php echo time(); ?>';


        document.addEventListener("DOMContentLoaded", async () => {
            const productDeatilItems = await fetchProductData("getProductDetailItems");
            const productSimilarItems = await fetchProductData("getProductSimilarItems");

            if(productDeatilItems.data){
                createProductDetailHTML("#product-detail-container-vibrant", productDeatilItems.data);
                initProductDetailLogic("#product-detail-container-vibrant", productDeatilItems.data.images);
            }

            if(productSimilarItems.data){
                createProductSimilarHTML("#product-similar-container", productSimilarItems.data);
            }
            
        });
    </script>

</body>

</html>
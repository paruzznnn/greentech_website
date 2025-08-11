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
        <div id="sections_root_product_list">
            <section id="sections_products_list" class="section-space-search">
                <div class="container">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="search-menu-product-list">

                                <!-- <button id="clearFiltersBtn" class="btn btn-warning mb-3 w-100">ล้างค่า Filter</button> -->

                                <!-- <h5>Price Range Filter (Slider)</h5> -->
                                

                                <!-- <div>
                                    <input type="range" id="minPrice" min="0" max="10000" step="100" value="0" class="range-slider" />
                                    <input type="range" id="maxPrice" min="0" max="10000" step="100" value="10000" class="range-slider" />
                                </div> -->

                                <div>
                                    <!-- <label for="minPrice">ราคาต่ำสุด: <span id="minPriceLabel">0</span></label>
                                    <label for="maxPrice">ราคาสูงสุด: <span id="maxPriceLabel">10000</span></label> -->
                                    เรทราคา: <span id="minPriceLabel">0</span> - <span id="maxPriceLabel">10000</span>
                                </div>
                                <div id="priceRangeContainer" class="mt-3">
                                    <div id="priceRangeSelected"></div>
                                    <input type="range" id="minPrice" min="0" max="10000" step="100" value="0" class="range-slider" style=" border: none !important;" />
                                    <input type="range" id="maxPrice" min="0" max="10000" step="100" value="10000" class="range-slider" style=" border: none !important;" />
                                </div>
                                

                                <div class="filter-group">
                                    <h6>สี (Color)</h6>
                                    <label><input type="checkbox" class="filter-color" value="Red" /> แดง</label><br />
                                    <label><input type="checkbox" class="filter-color" value="Blue" /> น้ำเงิน</label><br />
                                    <label><input type="checkbox" class="filter-color" value="Green" /> เขียว</label><br />
                                </div>

                                <div class="filter-group">
                                    <h6>ขนาด (Size)</h6>
                                    <label><input type="checkbox" class="filter-size" value="S" /> S</label><br />
                                    <label><input type="checkbox" class="filter-size" value="M" /> M</label><br />
                                    <label><input type="checkbox" class="filter-size" value="L" /> L</label><br />
                                </div>

                                <div class="filter-group">
                                    <h6>วัสดุ (Material)</h6>
                                    <label><input type="checkbox" class="filter-material" value="Cotton" /> ฝ้าย</label><br />
                                    <label><input type="checkbox" class="filter-material" value="Leather" /> หนัง</label><br />
                                    <label><input type="checkbox" class="filter-material" value="Plastic" /> พลาสติก</label><br />
                                </div>

                            </div>
                        </div>

                        <div class="col-md-9">

                            <div>
                                <!-- <input id="searchInput" class="form-control mb-3" placeholder="ค้นหา..." /> -->
                                <div class="row" id="cardContainer"></div>
                                <div class="text-center mt-3">
                                    <button id="prevBtn" class="btn btn-sm me-2">ย้อนกลับ</button>
                                    <span id="pageInfo"></span>
                                    <button id="nextBtn" class="btn btn-sm ms-2">ถัดไป</button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
    <?php include '../../template/footer-bar.php'; ?>

    <script type="module">
        import(`${pathConfig.BASE_WEB}js/product/productListRender.js?v=<?= time() ?>`)
        .then(async ({ 
            initCardUI
        }) => {

            initCardUI({
                containerId: 'cardContainer',
                // searchInputId: 'searchInput',
                pageInfoId: 'pageInfo',
                prevButtonId: 'prevBtn',
                nextButtonId: 'nextBtn',
                minPriceInputId: 'minPrice',
                maxPriceInputId: 'maxPrice',
                minPriceLabelId: 'minPriceLabel',
                maxPriceLabelId: 'maxPriceLabel',
                priceRangeSelectedId: 'priceRangeSelected',
                // clearFiltersBtnId: 'clearFiltersBtn',
                apiUrl: pathConfig.BASE_WEB + 'service/product/product-data.php?action=getProductListItems&gategoryId=<?php echo (int) ($_GET['id'] ?? 0); ?>',
                authToken: 'my_secure_token_123',
                BASE_WEB: pathConfig.BASE_WEB
            });
            
        })
        .catch((e) => console.error("Module import failed", e));
    </script>

</body>

</html>
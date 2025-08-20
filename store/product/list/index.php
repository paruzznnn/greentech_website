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
                                <div>
                                    เรทราคา: <span id="minPriceLabel">0</span> - <span id="maxPriceLabel">10000</span>
                                </div>
                                <div id="priceRangeContainer" class="mt-3">
                                    <div id="priceRangeSelected"></div>
                                    <input type="range" id="minPrice" min="0" max="10000" step="100" value="0" class="range-slider" style=" border: none !important;" />
                                    <input type="range" id="maxPrice" min="0" max="10000" step="100" value="10000" class="range-slider" style=" border: none !important;" />
                                </div>

                                <!-- <div id="filters-container"></div> -->

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

    <!-- <script>
        const filters = [{
                label: "สี (Color)",
                className: "filter-color",
                options: [{
                        value: "Red",
                        label: "แดง"
                    },
                    {
                        value: "Blue",
                        label: "น้ำเงิน"
                    },
                    {
                        value: "Green",
                        label: "เขียว"
                    },
                ],
            },
            {
                label: "ขนาด (Size)",
                className: "filter-size",
                options: [{
                        value: "S",
                        label: "S"
                    },
                    {
                        value: "M",
                        label: "M"
                    },
                    {
                        value: "L",
                        label: "L"
                    },
                ],
            },
            {
                label: "วัสดุ (Material)",
                className: "filter-material",
                options: [{
                        value: "Cotton",
                        label: "ฝ้าย"
                    },
                    {
                        value: "Leather",
                        label: "หนัง"
                    },
                    {
                        value: "Plastic",
                        label: "พลาสติก"
                    },
                ],
            },
        ];

        const container = document.getElementById("filters-container");

        filters.forEach(group => {
            const groupDiv = document.createElement("div");
            groupDiv.className = "filter-group";

            const title = document.createElement("h6");
            title.textContent = group.label;
            groupDiv.appendChild(title);

            group.options.forEach(opt => {
                const label = document.createElement("label");
                const checkbox = document.createElement("input");

                checkbox.type = "checkbox";
                checkbox.className = group.className;
                checkbox.value = opt.value;

                label.appendChild(checkbox);
                label.append(" " + opt.label);
                groupDiv.appendChild(label);
                groupDiv.appendChild(document.createElement("br"));
            });

            container.appendChild(groupDiv);
        });
    </script> -->

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
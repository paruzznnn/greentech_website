<?php include '../routes.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-STORE</title>
    <?php include '../inc-meta.php'; ?>
    <link href="../css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../css/product/template-product.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../inc-cdn.php'; ?>
</head>

<body>
    <?php include '../template/head-bar.php'; ?>
    <main>
        <div id="sections_root_product_list" class="section-space">
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
                            <div id="mainContent"></div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include '../template/footer-bar.php'; ?>

    <script>
        const StoreApp = {
            // ==========================
            // Data
            // ==========================
            store: {
                menu: [
                    {
                        icon: "bi bi-box-seam",
                        text: "Products",
                        link: "#"
                    }
                ],
                categories: [
                    {
                        name: "วัสดุอะคูสติก",
                        sub: {
                            "sub1": "แผ่นดูดซับเสียง",
                            "sub2": "ผนังกันเสียง",
                            "sub3": "ฝ้าเพดานกันเสียง"
                        }
                    },
                    {
                        name: "อุปกรณ์ตกแต่ง",
                        sub: {
                            "sub4": "โฟมกันเสียง",
                            "sub5": "ไม้ตกแต่งผนัง"
                        }
                    }
                ]
            },

            product: [{
                    name: "แทรนดาร์ เอเอ็มเอฟ เมอร์เคียว",
                    logo: "https://www.trandar.com//public/shop_img/687a1a94a6f10_Trandar_AMF_Mercure.jpg"
                },
                {
                    name: "แผ่นกันเสียง แทรนดาร์ ซาวด์บอร์ด 10 mm.",
                    logo: "https://www.trandar.com//public/shop_img/687a3420a32fa_Zound_Borad_223.png"
                },
                {
                    name: "Trandar  dBphon S50",
                    logo: "https://www.trandar.com//public/shop_img/687dcff11c5df_dbPhon2.png"
                },
                {
                    name: "แทรนดาร์ ทีบาร์ ที15",
                    logo: "https://www.trandar.com//public/shop_img/687b2f5b393b2_497eeb6fc69f5635590f41fc078dff98.jpg"
                },
                {
                    name: "แทรนดาร์ ไฮเทค วอลล์",
                    logo: "https://www.trandar.com//public/shop_img/687dde881193e_render_HITECWALL_Png_2366.png"
                },
                {
                    name: "Trandar Prime Coat",
                    logo: "https://www.trandar.com//public/shop_img/687e1c0470c40_trandar_prime_coat_new_label.jpg"
                },
                {
                    name: "Trandar Seamless Acoustics Solution",
                    logo: "https://www.trandar.com//public/shop_img/687df83a53198_seamless_acoustic-01.jpg"
                },
                {
                    name: "Trandar Acoustics Plaster Solution",
                    logo: "https://www.trandar.com//public/shop_img/687dfa2242223_system_Acoustic_plaster.png"
                },
                {
                    name: "แทรนดาร์ ซิวาน่า",
                    logo: "https://www.trandar.com//public/shop_img/6883502b859d7_ZIVANA_25_mm._full.jpg"
                }
            ],

            filters: [
                { id: "1", name: "แทรนดาร์ อะคูสติก", code: "tdi", checked: false },
                { id: "2", name: "Allable", code: "alb", checked: false },
                { id: "3", name: "Netizen", code: "ntz", checked: false },
                { id: "4", name: "Origami Platform", code: "ori", checked: false },
            ],

            pages: {
                "Products": `
                <div style="background:#fff; padding:0.5rem; border-radius:4px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div id="showing-entries" class="showing-entries"></div>
                        <div class="controls">
                            <button id="gridViewBtn" class="active"><i class="bi bi-grid-3x3-gap-fill"></i></button>
                            <button id="listViewBtn"><i class="bi bi-list-task"></i></button>
                        </div>
                    </div>
                    <div id="stores-container" class="stores-container grid"></div>
                    <div id="pagination" class="pagination"></div>
                </div>`
            },

            currentPage: 1,
            itemsPerPage: 8,
            viewMode: "grid",

            init() {
                this.renderMenu();
                this.renderPriceRange();
                this.renderCategories();
                this.renderSearchfilter();
                this.bindStoreEvents();

                // Active Shop เป็นค่าเริ่มต้น
                const firstMenu = document.querySelector('#stores-menu li a');
                if (firstMenu) {
                    firstMenu.classList.add("active");
                    firstMenu.click();
                }
            },

            renderMenu() {
                const container = document.getElementById("storeContainer");
                let menuHTML = `<div><ul id="stores-menu">`;
                this.store.menu.forEach(item => {
                    menuHTML += `<li><a href="${item.link}"><i class="${item.icon}"></i> ${item.text}</a></li>`;
                });
                menuHTML += `</ul></div>`;
                container.innerHTML += menuHTML;
            },

            renderPriceRange() {
                const container = document.getElementById("storeContainer");
                const priceHTML = `
                    <div>
                        <div>
                            เรทราคา: <span id="minPriceLabel">0</span> - <span id="maxPriceLabel">10000</span>
                        </div>
                        <div id="priceRangeContainer" class="mt-3">
                            <div id="priceRangeSelected"></div>
                            <input type="range" id="minPrice" min="0" max="10000" step="100" value="0" class="range-slider" />
                            <input type="range" id="maxPrice" min="0" max="10000" step="100" value="10000" class="range-slider" />
                        </div>
                    </div>
                `;
                container.innerHTML += priceHTML;
            },

            renderCategories() {
                const container = document.getElementById("storeContainer");
                let catHTML = `<div><h6>หมวดหมู่</h6><ul>`;

                this.store.categories.forEach(cat => {
                    let subHTML = "";

                    if (cat.sub && Object.keys(cat.sub).length) {
                        subHTML = `<ul>`;
                        Object.entries(cat.sub).forEach(([key, value]) => {
                            subHTML += `<li class="cat-sub-menu"><a href="#" data-id="${key}">${value}</a></li>`;
                        });
                        subHTML += `</ul>`;
                    }

                    catHTML += `
                        <li>
                            <div class="cat-header">${cat.name}</div>
                            ${subHTML}
                        </li>`;
                });

                catHTML += `</ul></div>`;
                container.innerHTML += catHTML;

                container.addEventListener("click", e => {
                    if (e.target.classList.contains("cat-header")) {
                        e.target.parentElement.classList.toggle("open");
                    }
                });
            },

            renderSearchfilter() {
                const container = document.getElementById("storeContainer");

                let filterHTML = `
                    <div class="search-filter">
                        <input type="text" id="searchInput" placeholder="Search Stores..." class="form-control mb-2" />
                `;

                this.filters.forEach(f => {
                    filterHTML += `
                        <div class="form-check">
                            <input 
                                class="form-check-input filter-checkbox" 
                                type="checkbox" 
                                id="filter-${f.id}" 
                                value="${f.code}" 
                                ${f.checked ? 'checked' : ''}>
                            <label class="form-check-label" for="filter-${f.id}">
                                ${f.name}
                            </label>
                        </div>
                    `;
                });

                filterHTML += `</div>`;
                container.innerHTML += filterHTML;
            },

            bindStoreEvents() {
                const mainContent = document.getElementById("mainContent");
                document.querySelectorAll("#stores-menu li a").forEach(link => {
                    link.addEventListener("click", e => {
                        e.preventDefault();
                        document.querySelectorAll("#stores-menu li a").forEach(a => a.classList.remove("active"));
                        link.classList.add("active");
                        const text = link.innerText.trim();
                        if (this.pages[text]) {
                            mainContent.innerHTML = this.pages[text];
                            if (text === "Products") {
                                this.cacheShopElements();
                                this.bindShopControls();
                                this.renderProduct();
                            }
                        }
                    });
                });

                // -------------------------
                // Price Range
                // -------------------------
                const minInput = document.getElementById("minPrice");
                const maxInput = document.getElementById("maxPrice");
                const minLabel = document.getElementById("minPriceLabel");
                const maxLabel = document.getElementById("maxPriceLabel");
                const priceRangeSelected = document.getElementById("priceRangeSelected");

                const maxPrice = parseInt(maxInput.max);

                const updateRange = () => {
                    let minValue = parseInt(minInput.value);
                    let maxValue = parseInt(maxInput.value);

                    if(minValue > maxValue) {
                        if(document.activeElement === minInput) {
                            minValue = maxValue;
                            minInput.value = minValue;
                        } else {
                            maxValue = minValue;
                            maxInput.value = maxValue;
                        }
                    }

                    const minPercent = (minValue / maxPrice) * 100;
                    const maxPercent = (maxValue / maxPrice) * 100;

                    priceRangeSelected.style.left = minPercent + '%';
                    priceRangeSelected.style.width = (maxPercent - minPercent) + '%';

                    minLabel.textContent = minValue;
                    maxLabel.textContent = maxValue;
                };

                updateRange();

                [minInput, maxInput].forEach(slider => {
                    slider.addEventListener("input", updateRange);
                });

                // -------------------------
                // Checkbox Filter
                // -------------------------
                document.querySelectorAll(".filter-checkbox").forEach(checkbox => {
                    checkbox.addEventListener("change", e => {
                        console.log("Checkbox changed:", {
                            id: e.target.id,
                            value: e.target.value,
                            checked: e.target.checked
                        });
                    });
                });

                // -------------------------
                // Category Click
                // -------------------------
                document.querySelectorAll(".cat-sub-menu a").forEach(link => {
                    link.addEventListener("click", e => {
                        e.preventDefault();
                        const key = e.target.dataset.id;   // ดึง key ที่เก็บใน data-id
                        const label = e.target.innerText; // ชื่อ category
                        console.log("Category clicked:", { key, label });
                    });
                });
            },

            cacheShopElements() {
                this.containers = {
                    storesContainer: document.getElementById('stores-container'),
                    gridBtn: document.getElementById("gridViewBtn"),
                    listBtn: document.getElementById("listViewBtn"),
                    pagination: document.getElementById("pagination"),
                    showingEntries: document.getElementById("showing-entries")
                };
            },

            bindShopControls() {
                this.containers.gridBtn.addEventListener("click", () => this.switchView('grid'));
                this.containers.listBtn.addEventListener("click", () => this.switchView('list'));
            },

            handleCardClick(store) {

                // console.log('Cart');
                
                const url = pathConfig.BASE_WEB + "product/detail/";
                window.location.href = url;
            },

            renderProduct() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                const end = start + this.itemsPerPage;
                const paginatedStores = this.product.slice(start, end);

                this.containers.storesContainer.innerHTML = "";
                paginatedStores.forEach(store => {
                    // const categoriesHtml = store.categories.map(cat => `<span class="category-tag">${cat}</span>`).join('');
                    const storeElement = document.createElement("div");
                    storeElement.classList.add("stores-card-product");
                    storeElement.innerHTML = `
                        <img src="${store.logo}" alt="${store.name}" class="store-logo-product" />
                        <div>
                            <p class="stores-name-product">${store.name}</p>
                        </div>
                    `;
                    storeElement.addEventListener("click", () => this.handleCardClick(store));
                    this.containers.storesContainer.appendChild(storeElement);
                });

                this.containers.showingEntries.innerText = `Showing ${start+1} to ${Math.min(end,this.product.length)} of ${this.product.length} entries`;
                this.renderPagination();
            },

            renderPagination() {
                this.containers.pagination.innerHTML = "";
                const totalPages = Math.ceil(this.product.length / this.itemsPerPage);

                const prevBtn = `<button ${this.currentPage===1?"disabled":""} onclick="StoreApp.changePage(${this.currentPage-1})">Prev</button>`;
                this.containers.pagination.insertAdjacentHTML("beforeend", prevBtn);

                for (let i = 1; i <= totalPages; i++) {
                    const pageBtn = `<button class="${i===this.currentPage?"active":""}" onclick="StoreApp.changePage(${i})">${i}</button>`;
                    this.containers.pagination.insertAdjacentHTML("beforeend", pageBtn);
                }

                const nextBtn = `<button ${this.currentPage===totalPages?"disabled":""} onclick="StoreApp.changePage(${this.currentPage+1})">Next</button>`;
                this.containers.pagination.insertAdjacentHTML("beforeend", nextBtn);
            },

            changePage(page) {
                this.currentPage = page;
                this.renderProduct();
            },

            switchView(mode) {
                if (mode === 'grid') {
                    this.containers.storesContainer.classList.remove("list");
                    this.containers.storesContainer.classList.add("grid");
                    this.containers.gridBtn.classList.add("active");
                    this.containers.listBtn.classList.remove("active");
                    this.itemsPerPage = 8;
                } else {
                    this.containers.storesContainer.classList.remove("grid");
                    this.containers.storesContainer.classList.add("list");
                    this.containers.listBtn.classList.add("active");
                    this.containers.gridBtn.classList.remove("active");
                    this.itemsPerPage = 6;
                }
                this.currentPage = 1;
                this.renderProduct();
            }
        };

        // start app
        StoreApp.init();
    </script>


</body>

</html>
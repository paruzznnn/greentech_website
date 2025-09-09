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
                        <aside class="sidebar">
                            <div class="stores-card-info" id="storeContainer"></div>
                        </aside>
                    </div>
                    <div class="col-md-9">
                        <section>
                            <div class="partner_cover_photo">
                                <img src="https://www.trandar.com//public/img/6891aee0f22e6.jpg" alt="Partner Cover" />
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

    <script>
        const StoreApp = {
            // ==========================
            // Data
            // ==========================
            store: {
                name: "แทรนดาร์ อะคูสติก",
                logo: "https://www.trandar.com//public/img/logo_688c431f30bf3.png",
                description: "วัสดุดูดซับเสียง ระบบ ผนัง เพดาน หลังคา กันเสียง",
                menu: [{
                        icon: "bi bi-shop",
                        text: "Shop",
                        link: "#"
                    },
                    {
                        icon: "bi bi-gift",
                        text: "Deals",
                        link: "#"
                    },
                    {
                        icon: "bi bi-geo-alt",
                        text: "Buy It Again",
                        link: "#"
                    },
                    {
                        icon: "bi bi-star",
                        text: "Reviews",
                        link: "#"
                    },
                    {
                        icon: "bi bi-grid",
                        text: "Article",
                        link: "#"
                    },
                    {
                        icon: "bi bi-telephone",
                        text: "Contact",
                        link: "#"
                    },
                    {
                        icon: "bi bi-clipboard",
                        text: "Policy",
                        link: "#"
                    },
                ],
                categories: [{
                        name: "วัสดุอะคูสติก",
                        sub: ["แผ่นดูดซับเสียง", "ผนังกันเสียง", "ฝ้าเพดานกันเสียง"]
                    },
                    {
                        name: "อุปกรณ์ตกแต่ง",
                        sub: ["โฟมกันเสียง", "ไม้ตกแต่งผนัง"]
                    }
                ]
            },

            stores: [{
                    name: "แทรนดาร์ เอเอ็มเอฟ เมอร์เคียว",
                    logo: "https://www.trandar.com//public/shop_img/687a1a94a6f10_Trandar_AMF_Mercure.jpg",
                    // categories: ["Organic", "Groceries", "Butcher Shop"],
                    // delivery: "Pickup available",
                    // distance: "7.5 mi away"
                },
                {
                    name: "แผ่นกันเสียง แทรนดาร์ ซาวด์บอร์ด 10 mm.",
                    logo: "https://www.trandar.com//public/shop_img/687a3420a32fa_Zound_Borad_223.png",
                    // categories: ["Alcohol", "Groceries"],
                    // delivery: "Delivery",
                    // distance: "7.2 mi away"
                },
                {
                    name: "Trandar  dBphon S50",
                    logo: "https://www.trandar.com//public/shop_img/687dcff11c5df_dbPhon2.png",
                    // categories: ["Groceries", "Bakery", "Deli"],
                    // delivery: "Delivery by 10:30pm",
                    // distance: "9.3 mi away"
                },
                {
                    name: "แทรนดาร์ ทีบาร์ ที15",
                    logo: "https://www.trandar.com//public/shop_img/687b2f5b393b2_497eeb6fc69f5635590f41fc078dff98.jpg",
                    // categories: ["Meal Kits", "Prepared Meals", "Organic"],
                    // delivery: "Delivery",
                    // distance: "40.5 mi away"
                },
                {
                    name: "แทรนดาร์ ไฮเทค วอลล์",
                    logo: "https://www.trandar.com//public/shop_img/687dde881193e_render_HITECWALL_Png_2366.png",
                    // categories: ["Snacks", "Beverages"],
                    // delivery: "Pickup only",
                    // distance: "2.3 mi away"
                },
                {
                    name: "Trandar Prime Coat",
                    logo: "https://www.trandar.com//public/shop_img/687e1c0470c40_trandar_prime_coat_new_label.jpg",
                    // categories: ["Fruits", "Vegetables"],
                    // delivery: "Same day delivery",
                    // distance: "5.1 mi away"
                },
                {
                    name: "Trandar Seamless Acoustics Solution",
                    logo: "https://www.trandar.com//public/shop_img/687df83a53198_seamless_acoustic-01.jpg",
                    // categories: ["Dairy", "Groceries"],
                    // delivery: "Pickup & Delivery",
                    // distance: "6.7 mi away"
                },
                {
                    name: "Trandar Acoustics Plaster Solution",
                    logo: "https://www.trandar.com//public/shop_img/687dfa2242223_system_Acoustic_plaster.png",
                    // categories: ["Snacks", "Soft Drinks"],
                    // delivery: "Instant delivery",
                    // distance: "1.8 mi away"
                },
                {
                    name: "แทรนดาร์ ซิวาน่า",
                    logo: "https://www.trandar.com//public/shop_img/6883502b859d7_ZIVANA_25_mm._full.jpg",
                    // categories: ["Organic", "Health Products"],
                    // delivery: "Delivery",
                    // distance: "8.4 mi away"
                }
            ],

            pages: {
                "Shop": `
            <div style="background:#fff; padding:0.5rem; border-radius:4px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div id="showing-entries" class="showing-entries"></div>
                    <div class="controls">
                        <button id="gridViewBtn" class="active">Grid</button>
                        <button id="listViewBtn">List</button>
                    </div>
                </div>
                <div id="stores-container" class="stores-container grid"></div>
                <div id="pagination" class="pagination"></div>
            </div>
        `,
                "Deals": `<div style="background:#fff; padding:1rem; border-radius:4px;"><h4><i class="bi bi-gift"></i> Deals</h4><p>โปรโมชั่นและส่วนลดพิเศษ...</p></div>`,
                "Buy It Again": `<div style="background:#fff; padding:1rem; border-radius:4px;"><h4><i class="bi bi-geo-alt"></i> Buy It Again</h4><p>สินค้าที่คุณเคยสั่งซื้อ...</p></div>`,
                "Reviews": `<div style="background:#fff; padding:1rem; border-radius:4px;"><h4><i class="bi bi-star"></i> Reviews</h4><p>รีวิวจากลูกค้า...</p></div>`,
                "Article": `<div style="background:#fff; padding:1rem; border-radius:4px;"><h4><i class="bi bi-grid"></i> Article</h4><p>บทความและข่าวสาร...</p></div>`,
                "Contact": `<div style="background:#fff; padding:1rem; border-radius:4px;"><h4><i class="bi bi-telephone"></i> Contact</h4><p>ติดต่อร้านค้า เบอร์โทร อีเมล แผนที่...</p></div>`,
                "Policy": `<div style="background:#fff; padding:1rem; border-radius:4px;"><h4><i class="bi bi-clipboard"></i> Policy</h4><p>นโยบายการซื้อขายและการรับประกัน...</p></div>`
            },

            currentPage: 1,
            itemsPerPage: 8,
            viewMode: "grid",

            init() {
                this.renderStoreInfo();
                this.renderMenu();
                this.renderCategories();
                this.bindMenuEvents();

                // Active Shop เป็นค่าเริ่มต้น
                const firstMenu = document.querySelector('#stores-menu li a');
                if (firstMenu) {
                    firstMenu.classList.add("active");
                    firstMenu.click();
                }
            },

            renderStoreInfo() {
                const container = document.getElementById("storeContainer");
                container.innerHTML = `
            <div>
                <img src="${this.store.logo}" alt="${this.store.name}" class="stores-logo-info">
            </div>
            <div style="border-bottom: 1px solid #dddddd;">
                <h5>${this.store.name}</h5>
                <p>${this.store.description}</p>
            </div>
        `;
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

            renderCategories() {
                const container = document.getElementById("storeContainer");
                let catHTML = `<div><h6>หมวดหมู่</h6><ul>`;
                this.store.categories.forEach(cat => {
                    catHTML += `
                <li>
                    <div class="cat-header">${cat.name}</div>
                    ${cat.sub && cat.sub.length ? `<ul>${cat.sub.map(sub => `<li class="cat-sub-menu"><a href="#">${sub}</a></li>`).join("")}</ul>` : ""}
                </li>`;
                });
                catHTML += `</ul></div>`;
                container.innerHTML += catHTML;

                // toggle sub menu
                document.querySelectorAll(".cat-header").forEach(header => {
                    header.addEventListener("click", () => {
                        header.parentElement.classList.toggle("open");
                    });
                });
            },

            bindMenuEvents() {
                const mainContent = document.getElementById("mainContent");
                document.querySelectorAll("#stores-menu li a").forEach(link => {
                    link.addEventListener("click", e => {
                        e.preventDefault();

                        document.querySelectorAll("#stores-menu li a").forEach(a => a.classList.remove("active"));
                        link.classList.add("active");

                        const text = link.innerText.trim();
                        if (this.pages[text]) {
                            mainContent.innerHTML = this.pages[text];
                            if (text === "Shop") {
                                this.cacheShopElements();
                                this.bindShopControls();
                                this.renderStores();
                            }
                        }
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
                // const url = "/partner/detail/?name=" + encodeURIComponent(store.name);
                // window.location.href = url;
            },

            renderStores() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                const end = start + this.itemsPerPage;
                const paginatedStores = this.stores.slice(start, end);

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

                    // <div class="category-container-product">${categoriesHtml}</div>
                    // <div class="details-container-product">
                    //     <p class="delivery-info-product">${store.delivery}</p>
                    //     <p class="distance-info-product">${store.distance}</p>
                    // </div>

                    storeElement.addEventListener("click", () => this.handleCardClick(store));
                    this.containers.storesContainer.appendChild(storeElement);
                });

                this.containers.showingEntries.innerText = `Showing ${start+1} to ${Math.min(end,this.stores.length)} of ${this.stores.length} entries`;
                this.renderPagination();
            },

            renderPagination() {
                this.containers.pagination.innerHTML = "";
                const totalPages = Math.ceil(this.stores.length / this.itemsPerPage);

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
                this.renderStores();
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
                this.renderStores();
            }
        };

        // start app
        StoreApp.init();
    </script>


</body>

</html>
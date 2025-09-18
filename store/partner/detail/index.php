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
            storeData: {
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
                    }
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

            pages: {
                "Shop": {
                    template: `
                    <div style="background:#fff; padding:0.5rem; border-radius:4px;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div id="showing-entries" class="showing-entries"></div>
                            <div class="controls">
                                <button id="gridViewBtn" class="active">Grid</button>
                                <button id="listViewBtn">List</button>
                            </div>
                        </div>
                        <div id="stores-container" class="stores-container grid"></div>
                        <div id="pagination" class="pagination mt-2"></div>
                    </div>`,
                    render() {
                        // Cache elements
                        this.containers = {
                            storesContainer: document.getElementById('stores-container'),
                            gridBtn: document.getElementById("gridViewBtn"),
                            listBtn: document.getElementById("listViewBtn"),
                            pagination: document.getElementById("pagination"),
                            showingEntries: document.getElementById("showing-entries")
                        };
                        this.bindShopControls();
                        this.renderStores();
                    }
                },
                "Deals": {
                    template: `<div style="background:#fff; padding:1rem; border-radius:4px;">Deals content...</div>`,
                    render: null
                },
                "Buy It Again": {
                    template: `<div style="background:#fff; padding:1rem; border-radius:4px;">Buy It Again content...</div>`,
                    render: null
                },
                "Reviews": {
                    template: `<div style="background:#fff; padding:1rem; border-radius:4px;">Reviews content...</div>`,
                    render: null
                },
                "Article": {
                    template: `<div style="background:#fff; padding:1rem; border-radius:4px;">Article content...</div>`,
                    render: null
                },
                "Contact": {
                    template: `<div style="background:#fff; padding:1rem; border-radius:4px;">Contact content...</div>`,
                    render: null
                },
                "Policy": {
                    template: `<div style="background:#fff; padding:1rem; border-radius:4px;">Policy content...</div>`,
                    render: null
                }
            },

            currentPage: 1,
            itemsPerPage: 8,
            viewMode: "grid",
            containers: {},

            // ==========================
            // Methods
            // ==========================
            init() {
                this.renderStoreInfo();
                this.renderMenu();
                this.renderCategories();
                this.bindMenuEvents();

                const firstMenu = document.querySelector('#stores-menu li a');
                if (firstMenu) {
                    firstMenu.classList.add("active");
                    firstMenu.click();
                }
            },

            renderStoreInfo() {
                const container = document.getElementById("storeContainer");
                container.innerHTML = `
                    <div><img src="${this.storeData.logo}" alt="${this.storeData.name}" class="stores-logo-info"></div>
                    <div style="border-bottom: 1px solid #ddd;">
                        <h5>${this.storeData.name}</h5>
                        <p>${this.storeData.description}</p>
                    </div>
                `;
            },

            renderMenu() {
                const container = document.getElementById("storeContainer");
                let menuHTML = `<ul id="stores-menu">`;
                this.storeData.menu.forEach(item => {
                    menuHTML += `<li><a href="${item.link}"><i class="${item.icon}"></i> ${item.text}</a></li>`;
                });
                menuHTML += `</ul>`;
                container.innerHTML += menuHTML;
            },

            renderCategories() {
                const container = document.getElementById("storeContainer");
                let catHTML = `<div><h6>หมวดหมู่</h6><ul>`;
                this.storeData.categories.forEach(cat => {
                    catHTML += `
                    <li>
                        <div class="cat-header">${cat.name}</div>
                        ${cat.sub && cat.sub.length ? `<ul>${cat.sub.map(sub => `<li class="cat-sub-menu"><a href="#">${sub}</a></li>`).join("")}</ul>` : ""}
                    </li>`;
                });
                catHTML += `</ul></div>`;
                container.innerHTML += catHTML;

                // Toggle sub menu
                document.querySelectorAll(".cat-header").forEach(header => {
                    header.addEventListener("click", () => header.parentElement.classList.toggle("open"));
                });
            },

            bindMenuEvents() {
                const mainContent = document.getElementById("mainContent");
                document.querySelectorAll("#stores-menu li a").forEach(link => {
                    link.addEventListener("click", e => {
                        e.preventDefault();
                        document.querySelectorAll("#stores-menu li a").forEach(a => a.classList.remove("active"));
                        link.classList.add("active");

                        const key = link.innerText.trim();
                        if (this.pages[key]) {
                            mainContent.innerHTML = this.pages[key].template;
                            if (typeof this.pages[key].render === "function") {
                                this.pages[key].render.call(this);
                            }
                        }
                    });
                });
            },

            bindShopControls() {
                this.containers.gridBtn.addEventListener("click", () => this.switchView('grid'));
                this.containers.listBtn.addEventListener("click", () => this.switchView('list'));
            },

            renderStores() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                const end = start + this.itemsPerPage;
                const paginatedStores = this.stores.slice(start, end);

                this.containers.storesContainer.innerHTML = "";
                paginatedStores.forEach(store => {
                    const storeEl = document.createElement("div");
                    storeEl.classList.add("stores-card-product");
                    storeEl.innerHTML = `
                        <img src="${store.logo}" alt="${store.name}" class="store-logo-product" />
                        <div><p class="stores-name-product">${store.name}</p></div>
                    `;
                    storeEl.addEventListener("click", () => this.handleCardClick(store));
                    this.containers.storesContainer.appendChild(storeEl);
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
            },

            handleCardClick(store) {
                console.log("Clicked store:", store.name);
                // สามารถ redirect หรือแสดงรายละเอียดเพิ่มเติมได้
            }
        };

        // Start app
        StoreApp.init();
    </script>



</body>

</html>
const StoreApp = {

    storeData: {},
    stores: [
        {
            name: "แทรนดาร์ เอเอ็มเอฟ เมอร์เคียว",
            logo: "https://www.trandar.com/public/shop_img/687a1a94a6f10_Trandar_AMF_Mercure.jpg"
        },
        {
            name: "แผ่นกันเสียง แทรนดาร์ ซาวด์บอร์ด 10 mm.",
            logo: "https://www.trandar.com/public/shop_img/687a3420a32fa_Zound_Borad_223.png"
        },
        {
            name: "Trandar  dBphon S50",
            logo: "https://www.trandar.com/public/shop_img/687dcff11c5df_dbPhon2.png"
        },
        {
            name: "แทรนดาร์ ทีบาร์ ที15",
            logo: "https://www.trandar.com/public/shop_img/687b2f5b393b2_497eeb6fc69f5635590f41fc078dff98.jpg"
        },
        {
            name: "แทรนดาร์ ไฮเทค วอลล์",
            logo: "https://www.trandar.com/public/shop_img/687dde881193e_render_HITECWALL_Png_2366.png"
        },
        {
            name: "Trandar Prime Coat",
            logo: "https://www.trandar.com/public/shop_img/687e1c0470c40_trandar_prime_coat_new_label.jpg"
        },
        {
            name: "Trandar Seamless Acoustics Solution",
            logo: "https://www.trandar.com/public/shop_img/687df83a53198_seamless_acoustic-01.jpg"
        },
        {
            name: "Trandar Acoustics Plaster Solution",
            logo: "https://www.trandar.com/public/shop_img/687dfa2242223_system_Acoustic_plaster.png"
        },
        {
            name: "แทรนดาร์ ซิวาน่า",
            logo: "https://www.trandar.com/public/shop_img/6883502b859d7_ZIVANA_25_mm._full.jpg"
        }
    ],
    pages: {
        "Shop": {
            render() {
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
        }
    },

    currentPage: 1,
    itemsPerPage: 8,
    viewMode: "grid",
    containers: {},

    loadStore() {
        const params = new URLSearchParams({
            action: 'getPartnerStore',
            store_id: window.AppConfig.STORE_ID,
        });

        const url = window.AppConfig.BASE_WEB + "service/partner/partner-data.php?" + params.toString();
        fetch(url, {
            method: "GET",
            headers: {
                'Authorization': 'Bearer my_secure_token_123',
                'Content-Type': 'application/json'
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.data) {
                    const storeRaw = data.data[0];

                    this.storeData = {
                        name: storeRaw.name || "ชื่อร้านไม่ระบุ",
                        logo: storeRaw.logo_url || "",
                        description: storeRaw.description || "กันเสียง ดูดซับเสียง",
                        menu: [
                            { icon: '<i class="bi bi-shop"></i>', text: "Shop", link: "#" },
                            { icon: '<i class="bi bi-gift"></i>', text: "Deals", link: "#" },
                            { icon: '<i class="bi bi-geo-alt"></i>', text: "Buy It Again", link: "#" },
                            { icon: '<i class="bi bi-star"></i>', text: "Reviews", link: "#" },
                            { icon: '<i class="bi bi-grid"></i>', text: "Article", link: "#" },
                            { icon: '<i class="bi bi-telephone"></i>', text: "Contact", link: "#" },
                            { icon: '<i class="bi bi-clipboard"></i>', text: "Policy", link: "#" },
                        ],
                        categories: storeRaw.categories || [
                            {
                                name: "วัสดุอะคูสติก",
                                sub: ["แผ่นดูดซับเสียง", "ผนังกันเสียง", "ฝ้าเพดานกันเสียง"]
                            },
                            {
                                name: "อุปกรณ์ตกแต่ง",
                                sub: ["โฟมกันเสียง", "ไม้ตกแต่งผนัง"]
                            }
                        ]
                    };

                    this.renderStoreInfo();
                    this.renderMenu();
                    this.renderCategories();
                    this.bindMenuEvents();

                    document.getElementById('partner_banner').src = storeRaw.banner_url;

                    // Auto-click first menu after data is ready
                    const firstMenu = document.querySelector('#stores-menu li a');
                    if (firstMenu) {
                        firstMenu.classList.add("active");
                        firstMenu.click();
                    }

                } else {
                    console.error('API response error:', data.message);
                }
            })
            .catch(err => {
                console.error('Failed to load stores:', err);
            });
    },

    loadProduct() {
        // Placeholder for product loading logic
    },

    renderStoreInfo() {
        const container = document.getElementById("storeContainer");
        container.innerHTML = `
            <div><img src="${this.storeData.logo}" alt="${this.storeData.name}" class="stores-logo-info" onerror="this.src='fallback.jpg'" /></div>
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
            menuHTML += `<li><a href="${item.link}" data-key="${item.text}">${item.icon} ${item.text}</a></li>`;
        });
        menuHTML += `</ul>`;
        container.innerHTML += menuHTML;
    },

    renderCategories() {
        const container = document.getElementById("storeContainer");
        const catHTML = `
            <div><h6>หมวดหมู่</h6><ul>
                ${this.storeData.categories.map(cat => `
                    <li>
                        <div class="cat-header">${cat.name}</div>
                        ${cat.sub ? `<ul>${cat.sub.map(sub => `<li class="cat-sub-menu"><a href="#">${sub}</a></li>`).join("")}</ul>` : ""}
                    </li>`).join("")}
            </ul></div>`;
        container.innerHTML += catHTML;

        document.querySelectorAll(".cat-header").forEach(header => {
            header.addEventListener("click", () => header.parentElement.classList.toggle("open"));
        });
    },

    getPageTemplate(pageName) {
        if (pageName === "Shop") {
            return `
                <div style="background:#fff; padding:0.5rem; border-radius:4px;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div id="showing-entries" class="showing-entries"></div>
                        <div class="controls">
                            <button id="gridViewBtn" class="active"><i class="bi bi-grid-3x3-gap-fill"></i></button>
                            <button id="listViewBtn"><i class="bi bi-list-task"></i></button>
                        </div>
                    </div>
                    <div id="stores-container" class="stores-container grid"></div>
                    <div id="pagination" class="pagination mt-2"></div>
                </div>`;
        } else {
            return `<div style="background:#fff; padding:1rem; border-radius:4px;">${pageName} content...</div>`;
        }
    },

    bindMenuEvents() {
        const mainContent = document.getElementById("mainContent");
        document.querySelectorAll("#stores-menu li a").forEach(link => {
            link.addEventListener("click", e => {
                e.preventDefault();
                document.querySelectorAll("#stores-menu li a").forEach(a => a.classList.remove("active"));
                link.classList.add("active");

                const key = link.dataset.key;
                mainContent.innerHTML = this.getPageTemplate(key);
                if (typeof this.pages[key]?.render === "function") {
                    this.pages[key].render.call(this);
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
            const card = document.createElement("div");
            card.classList.add("stores-card-product");
            card.innerHTML = `
                <img src="${store.logo}" alt="${store.name}" class="store-logo-product" onerror="this.src='fallback.jpg'" />
                <div><p class="stores-name-product">${store.name}</p></div>
            `;
            card.addEventListener("click", () => this.handleCardClick(store));
            this.containers.storesContainer.appendChild(card);
        });

        this.containers.showingEntries.innerText = `Showing ${start + 1} to ${Math.min(end, this.stores.length)} of ${this.stores.length} entries`;
        this.renderPagination();
    },

    renderPagination() {
        const totalPages = Math.ceil(this.stores.length / this.itemsPerPage);
        this.containers.pagination.innerHTML = "";

        const createBtn = (label, page, disabled = false, active = false) => {
            const btn = document.createElement("button");
            btn.textContent = label;
            if (disabled) btn.disabled = true;
            if (active) btn.classList.add("active");
            btn.addEventListener("click", () => this.changePage(page));
            return btn;
        };

        this.containers.pagination.appendChild(createBtn("Prev", this.currentPage - 1, this.currentPage === 1));

        for (let i = 1; i <= totalPages; i++) {
            this.containers.pagination.appendChild(createBtn(i, i, false, i === this.currentPage));
        }

        this.containers.pagination.appendChild(createBtn("Next", this.currentPage + 1, this.currentPage === totalPages));
    },

    changePage(page) {
        this.currentPage = page;
        this.renderStores();
    },

    switchView(mode) {
        this.viewMode = mode;
        const container = this.containers.storesContainer;

        if (mode === 'grid') {
            container.classList.remove("list");
            container.classList.add("grid");
            this.containers.gridBtn.classList.add("active");
            this.containers.listBtn.classList.remove("active");
            this.itemsPerPage = 8;
        } else {
            container.classList.remove("grid");
            container.classList.add("list");
            this.containers.listBtn.classList.add("active");
            this.containers.gridBtn.classList.remove("active");
            this.itemsPerPage = 6;
        }

        this.currentPage = 1;
        this.renderStores();
    },

    handleCardClick(store) {
        alert(`คลิกที่: ${store.name}`);
    },

    init() {
        this.loadStore();
    }

};

document.addEventListener("DOMContentLoaded", () => {
    StoreApp.init();
});

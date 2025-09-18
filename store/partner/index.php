<?php include '../routes.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-STORE</title>
    <?php include '../inc-meta.php'; ?>
    <link href="../css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../css/partner/template-partner.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../inc-cdn.php'; ?>

</head>

<body>
    <?php include '../template/head-bar.php'; ?>
    <main>
        <div id="sections_root_partner_list">
            <div class="container">
                <section class="section-space">
                    <div class="partner_cover_photo">
                        <img src="https://www.trandar.com//public/img/6891aee0f22e6.jpg" alt="Partner Cover" />
                    </div>
                </section>
                <section class="section-space">
                    <div style="background-color:#ffffff; padding:0.5rem; border-radius:4px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div id="showing-entries" class="showing-entries"></div>
                            <div class="controls">
                                <button id="gridViewBtn" class="active"><i class="bi bi-grid-3x3-gap-fill"></i></button>
                                <button id="listViewBtn"><i class="bi bi-list-task"></i></button>
                            </div>
                        </div>
                        <div id="stores-container" class="stores-container grid"></div>
                        <div id="pagination" class="pagination"></div>
                    </div>
                </section>
                <section class="section-space">
                    <div class="features-container">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="far fa-clock"></i></div>
                            <h3 class="feature-title">ตะกร้าราคาถูกที่สุด</h3>
                            <p class="feature-description">
                                รับคำสั่งซื้อของคุณส่งถึงหน้าประตูบ้านของคุณโดยเร็วที่สุดจากจุดรับสินค้า FreshCart ใกล้บ้านคุณ
                            </p>
                        </div>

                        <div class="feature-card">
                            <div class="feature-icon"><i class="fas fa-gift"></i></div>
                            <h3 class="feature-title">ราคาและข้อเสนอที่ดีที่สุด</h3>
                            <p class="feature-description">
                                ราคาถูกพร้อมข้อเสนอสุดพิเศษ
                            </p>
                        </div>

                        <div class="feature-card">
                            <div class="feature-icon"><i class="fas fa-box"></i></div>
                            <h3 class="feature-title">สินค้าหลากหลาย</h3>
                            <p class="feature-description">
                                เลือกจากผลิตภัณฑ์มากกว่า 5,000 รายการในหมวดหมู่
                            </p>
                        </div>

                        <div class="feature-card">
                            <div class="feature-icon"><i class="fas fa-desktop"></i></div>
                            <h3 class="feature-title">ช้อปกับเว็บไซต์ของเรา</h3>
                            <p class="feature-description">
                                ช้อปได้ทุกที่ด้วยเว็บไซต์ของเราสำหรับแท็บเล็ตและมือถือ ติดตามคำสั่งซื้อแบบเรียลไทม์ รับการอัปเดตฟีเจอร์ล่าสุด
                            </p>
                        </div>

                        <div class="feature-card">
                            <div class="feature-icon"><i class="fas fa-truck"></i></div>
                            <h3 class="feature-title">ต้องการช้อปปิ้งวันนี้ใช่ไหม?</h3>
                            <p class="feature-description">
                                เลือกจากตัวเลือกการจัดส่งด่วนหรือการรับสินค้าแบบด่วน
                            </p>
                        </div>

                        <div class="feature-card">
                            <div class="feature-icon"><i class="fas fa-sync-alt"></i></div>
                            <h3 class="feature-title">การคืนสินค้า/คืนเงินที่ง่ายดาย</h3>
                            <p class="feature-description">
                                ไม่พอใจสินค้าใช่ไหม? ส่งคืนสินค้าถึงหน้าบ้านและรับเงินคืนภายในไม่กี่ชั่วโมง ไม่มีคำถามใดๆ <a href="#">policy</a>
                            </p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>
    <?php include '../template/footer-bar.php'; ?>

    <script>
        const StoreApp = {
            stores: [{
                    name: "แทรนดาร์ อะคูสติก",
                    logo: "https://www.trandar.com//public/img/logo_688c431f30bf3.png",
                    categories: ["Organic", "Groceries", "Butcher Shop"],
                    delivery: "Pickup available",
                    distance: "7.5 mi away"
                },
                {
                    name: "Allable",
                    logo: "https://www.allable.co.th/public/img/logo-ALLABLE-06.png",
                    categories: ["Alcohol", "Groceries"],
                    delivery: "Delivery",
                    distance: "7.2 mi away"
                },
                {
                    name: "Origami Platform",
                    logo: "https://www.origami.life/images/ogm_logo.png?v=1757301396093",
                    categories: ["Groceries", "Bakery", "Deli"],
                    delivery: "Delivery by 10:30pm",
                    distance: "9.3 mi away"
                },
                {
                    name: "Devrev",
                    logo: "https://mms.businesswire.com/media/20240809282033/en/2211847/5/devrev4905logowikcom.jpg?download=1",
                    categories: ["Meal Kits", "Prepared Meals", "Organic"],
                    delivery: "Delivery",
                    distance: "40.5 mi away"
                },
                {
                    name: "Netizen",
                    logo: "https://www.allable.co.th/public/img/netizen-logo.png",
                    categories: ["Snacks", "Beverages"],
                    delivery: "Pickup only",
                    distance: "2.3 mi away"
                },
                {
                    name: "Huawei",
                    logo: "https://www.netizen.co.th/wp-content/uploads/2025/06/Huawei-Logo-1536x864.png",
                    categories: ["Fruits", "Vegetables"],
                    delivery: "Same day delivery",
                    distance: "5.1 mi away"
                },
                {
                    name: "SAP",
                    logo: "https://www.netizen.co.th/wp-content/uploads/2025/06/Logo-SAP-Platinum-Partner-United-VARs-02-1-1.png",
                    categories: ["Dairy", "Groceries"],
                    delivery: "Pickup & Delivery",
                    distance: "6.7 mi away"
                },
                {
                    name: "AWS",
                    logo: "https://www.netizen.co.th/wp-content/uploads/2025/06/Amazon_Web_Services-Logo.wine_-1536x1024.png",
                    categories: ["Snacks", "Soft Drinks"],
                    delivery: "Instant delivery",
                    distance: "1.8 mi away"
                },
                {
                    name: "PTT DIGITAL",
                    logo: "https://www.netizen.co.th/wp-content/uploads/2025/06/PTT-Digital_logo_transparent.png",
                    categories: ["Organic", "Health Products"],
                    delivery: "Delivery",
                    distance: "8.4 mi away"
                }
            ],
            currentPage: 1,
            itemsPerPage: 8, 
            containers: {
                storesContainer: document.getElementById('stores-container'),
                gridBtn: document.getElementById("gridViewBtn"),
                listBtn: document.getElementById("listViewBtn"),
                pagination: document.getElementById("pagination"),
                showingEntries: document.getElementById("showing-entries")
            },

            init() {
                this.containers.gridBtn.addEventListener("click", () => this.switchView('grid'));
                this.containers.listBtn.addEventListener("click", () => this.switchView('list'));
                this.renderStores();
            },

            handleCardClick(store) {
                const url = pathConfig.BASE_WEB + `partner/detail/?name=${encodeURIComponent(store.name)}`;
                window.location.href = url;
            },

            renderStores() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                const end = start + this.itemsPerPage;
                const paginatedStores = this.stores.slice(start, end);

                this.containers.storesContainer.innerHTML = "";

                paginatedStores.forEach(store => {
                    const categoriesHtml = store.categories.map(cat => `<span class="category-tag">${cat}</span>`).join('');
                    const storeElement = document.createElement("div");
                    storeElement.classList.add("stores-card");

                    storeElement.innerHTML = `
                        <img src="${store.logo}" alt="${store.name}" class="store-logo" />
                        <div>
                            <h2 class="stores-name">${store.name}</h2>
                            <div class="category-container">${categoriesHtml}</div>
                            <div class="details-container">
                                <p class="delivery-info">${store.delivery}</p>
                                <p class="distance-info">${store.distance}</p>
                            </div>
                        </div>
                    `;

                    // ใช้ฟังก์ชันแยก
                    storeElement.addEventListener("click", () => this.handleCardClick(store));

                    this.containers.storesContainer.appendChild(storeElement);
                });

                this.containers.showingEntries.innerText =
                    `Showing ${start + 1} to ${Math.min(end, this.stores.length)} of ${this.stores.length} entries`;
                this.renderPagination();
            },

            renderPagination() {
                this.containers.pagination.innerHTML = "";
                const totalPages = Math.ceil(this.stores.length / this.itemsPerPage);

                const prevBtn = `<button ${this.currentPage === 1 ? "disabled" : ""} onclick="StoreApp.changePage(${this.currentPage - 1})">Prev</button>`;
                this.containers.pagination.insertAdjacentHTML("beforeend", prevBtn);

                for (let i = 1; i <= totalPages; i++) {
                    const pageBtn = `<button class="${i === this.currentPage ? "active" : ""}" onclick="StoreApp.changePage(${i})">${i}</button>`;
                    this.containers.pagination.insertAdjacentHTML("beforeend", pageBtn);
                }

                const nextBtn = `<button ${this.currentPage === totalPages ? "disabled" : ""} onclick="StoreApp.changePage(${this.currentPage + 1})">Next</button>`;
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

        StoreApp.init();
    </script>


</body>

</html>
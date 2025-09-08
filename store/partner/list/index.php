<?php include '../../routes.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-STORE</title>
    <?php include '../../inc-meta.php'; ?>
    <link href="../../css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../../inc-cdn.php'; ?>

    <style>
        /* ==========================
           Cover Photo
        =========================== */
        .partner_cover_photo img {
            width: 100%;
            max-height: 200px;
            object-fit: cover;
            border-radius: 3px;
            border: 1px solid #ddd;
            display: block;
        }

        /* ==========================
           Controls
        =========================== */
        .controls {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .controls button {
            padding: 0.1rem 0.3rem;
            border: 1px solid #ddd;
            background: #f3f4f6;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: background 0.2s;
            font-size: 0.9rem;
        }

        .controls button:hover {
            background: #e5e7eb;
        }

        .controls button.active {
            background: #FF9800;
            color: white;
            border-color: #FF9800;
        }

        /* ==========================
           Stores Container
        =========================== */
        .stores-container {
            display: grid;
            gap: 0.7rem;
        }

        .stores-container.grid {
            grid-template-columns: repeat(4, 1fr);
        }

        .stores-container.list {
            grid-template-columns: repeat(3, 1fr);
        }

        /* ==========================
           Store Card
        =========================== */
        .stores-card {
            background: #ffffff;
            border-radius: 3px;
            padding: 1.5rem;
            border: 1px solid #eaeaea;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: transform 0.3s ease, border 0.3s ease, box-shadow 0.3s ease;
        }

        .stores-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border: 1px solid #ff9902;
        }

        .store-logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: contain;
            margin-bottom: 0.75rem;
            border: 2px solid #e5e7eb;
            background: white;
        }

        .stores-name {
            font-size: 1.2rem;
            font-weight: bold;
            color: #111827;
            margin-bottom: 0.25rem;
        }

        .category-container {
            margin-top: 0.5rem;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.5rem;
        }

        .category-tag {
            background-color: #e5e7eb;
            color: #374151;
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            white-space: nowrap;
        }

        .details-container {
            margin-top: 1rem;
            color: #4b5563;
            text-align: center;
        }

        .delivery-info,
        .distance-info {
            font-size: 0.875rem;
            font-weight: 500;
        }

        .distance-info {
            margin-top: 0.25rem;
            color: #2563eb;
        }

        /* ==========================
           List View Layout
        =========================== */
        .stores-container.list .stores-card {
            flex-direction: row;
            align-items: center;
            text-align: left;
        }

        .stores-container.list .store-logo {
            margin: 0;
            margin-right: 1rem;
        }

        .stores-container.list .stores-name,
        .stores-container.list .category-container,
        .stores-container.list .details-container {
            text-align: left;
        }

        .stores-container.list .category-container {
            justify-content: flex-start;
        }

        /* ==========================
           Pagination
        =========================== */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .pagination button {
            padding: 0.1rem 0.6rem;
            border: 1px solid #ddd;
            background: #fff;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background 0.2s, color 0.2s, border-color 0.2s;
        }

        .pagination button:hover {
            background: #f3f4f6;
        }

        .pagination button.active {
            background: #FF9800;
            color: white;
            border-color: #FF9800;
        }

        .pagination button:disabled {
            background: #f3f4f6;
            cursor: not-allowed;
            color: #9ca3af;
        }

        /* ==========================
           Showing Entries
        =========================== */
        .showing-entries {
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: #374151;
        }

        /* ==========================
           Responsive
        =========================== */
        @media (max-width: 1024px) {

            .stores-container.grid,
            .stores-container.list {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 640px) {

            .stores-container.grid,
            .stores-container.list {
                grid-template-columns: 1fr;
            }

            .stores-container.list .stores-card {
                flex-direction: row;
            }
        }
    </style>
</head>

<body>
    <?php include '../../template/head-bar.php'; ?>

    <main>
        <div id="sections_root_partner_list">
            <!-- Cover Photo -->
            <section id="sections_partner_cover_photo" class="section-space">
                <div class="container">
                    <div class="partner_cover_photo">
                        <img src="https://www.trandar.com//public/img/6891aee0f22e6.jpg" alt="Partner Cover" />
                    </div>
                </div>
            </section>

            <!-- Partner Store List -->
            <section id="sections_partner_list" class="section-space">
                <div class="container">
                    <div style="background-color:#ffffff; padding:0.5rem; border-radius:4px;">
                        <h5>แบรนด์ชั้นนำที่เข้ามาร่วมกับเราและอีกมากมาย</h5>
                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Showing Entries -->
                            <div id="showing-entries" class="showing-entries"></div>
                            <!-- View Mode Buttons -->
                            <div class="controls">
                                <button id="gridViewBtn" class="active">Grid</button>
                                <button id="listViewBtn">List</button>
                            </div>
                        </div>

                        <!-- Stores Container -->
                        <div id="stores-container" class="stores-container grid"></div>

                        <!-- Pagination -->
                        <div id="pagination" class="pagination"></div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <?php include '../../template/footer-bar.php'; ?>

    <script>
        const stores = [{
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
        ];

        const storesContainer = document.getElementById('stores-container');
        const gridBtn = document.getElementById("gridViewBtn");
        const listBtn = document.getElementById("listViewBtn");
        const pagination = document.getElementById("pagination");
        const showingEntries = document.getElementById("showing-entries");

        let currentPage = 1;
        let itemsPerPage = 8; // Default Grid mode

        function renderStores() {
            storesContainer.innerHTML = "";
            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const paginatedStores = stores.slice(start, end);

            paginatedStores.forEach(store => {
                const categoriesHtml = store.categories.map(cat => `<span class="category-tag">${cat}</span>`).join('');
                const storeElement = `
                    <article class="stores-card">
                        <img src="${store.logo}" alt="${store.name}" class="store-logo" />
                        <div>
                            <h2 class="stores-name">${store.name}</h2>
                            <div class="category-container">${categoriesHtml}</div>
                            <div class="details-container">
                                <p class="delivery-info">${store.delivery}</p>
                                <p class="distance-info">${store.distance}</p>
                            </div>
                        </div>
                    </article>
                `;
                storesContainer.insertAdjacentHTML('beforeend', storeElement);
            });

            showingEntries.innerText = `Showing ${start + 1} to ${Math.min(end, stores.length)} of ${stores.length} entries`;

            renderPagination();
        }

        function renderPagination() {
            pagination.innerHTML = "";
            const totalPages = Math.ceil(stores.length / itemsPerPage);

            const prevBtn = `<button ${currentPage === 1 ? "disabled" : ""} onclick="changePage(${currentPage - 1})">Prev</button>`;
            pagination.insertAdjacentHTML("beforeend", prevBtn);

            for (let i = 1; i <= totalPages; i++) {
                const pageBtn = `<button class="${i === currentPage ? "active" : ""}" onclick="changePage(${i})">${i}</button>`;
                pagination.insertAdjacentHTML("beforeend", pageBtn);
            }

            const nextBtn = `<button ${currentPage === totalPages ? "disabled" : ""} onclick="changePage(${currentPage + 1})">Next</button>`;
            pagination.insertAdjacentHTML("beforeend", nextBtn);
        }

        function changePage(page) {
            currentPage = page;
            renderStores();
        }

        gridBtn.addEventListener("click", () => {
            storesContainer.classList.remove("list");
            storesContainer.classList.add("grid");
            gridBtn.classList.add("active");
            listBtn.classList.remove("active");
            itemsPerPage = 8; // Grid mode
            currentPage = 1;
            renderStores();
        });

        listBtn.addEventListener("click", () => {
            storesContainer.classList.remove("grid");
            storesContainer.classList.add("list");
            listBtn.classList.add("active");
            gridBtn.classList.remove("active");
            itemsPerPage = 6; // List mode
            currentPage = 1;
            renderStores();
        });

        renderStores();
    </script>
</body>

</html>
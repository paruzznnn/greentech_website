<?php include '../routes.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-STORE</title>
    <?php include '../inc-meta.php'; ?>
    <link href="../css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../inc-cdn.php'; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
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

        .showing-entries {
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: #374151;
        }

        .user-card-info {
            display: flex;
            gap: 15px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #fff;
            flex-direction: column;
            margin-bottom: 10px;
        }

        .stores-logo-info {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: contain;
            margin-bottom: 0.75rem;
            border: 2px solid #e5e7eb;
            background: white;
        }

        #user-menu {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        #user-menu li a {
            font-size: 14px;
            padding: 5px 10px;
            text-decoration: none;
            color: black;
            display: block;
            border-radius: 0.5rem;
        }

        #user-menu li a:hover {
            background-color: #FF9800;
            padding: 5px 10px;
            color: #ffffff;
        }

        #user-menu li a.active {
            font-weight: bold;
            color: #FF9800;
            background-color: #fff7eb;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar li {
            margin-bottom: 5px;
        }

        .tab-order-navigation {
            display: flex;
            flex-wrap: nowrap;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
            overflow-x: auto;
            overflow-y: hidden;
            scrollbar-width: thin;    
            scrollbar-color: #9ca3af #f3f4f6;
            -webkit-overflow-scrolling: touch;
        }

        .tab-order-navigation::-webkit-scrollbar {
            height: 6px;
        }
        .tab-order-navigation::-webkit-scrollbar-track {
            background: #f3f4f6;
        }
        .tab-order-navigation::-webkit-scrollbar-thumb {
            background-color: #9ca3af;
            border-radius: 9999px;
        }

        .tab-order-button {
            min-width: 120px;
            padding: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #6b7280;
            cursor: pointer;
            background: none;
            border: none;
            transition: color 0.3s, background-color 0.3s;
            border-bottom: 2px solid transparent;
        }

        .tab-order-button:hover {
            color: #4b5563;
            background-color: #f9fafb;
        }

        .tab-order-button.active {
            background-color: #ffffff;
            color: #2563eb;
            border-bottom: 2px solid #2563eb;
        }

        .order-card {
            background-color: #f9fafb;
            padding: 1.5rem;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            margin-bottom: 1rem;
        }

        .order-header {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        @media (min-width: 576px) {
            .order-header {
                flex-direction: row;
                align-items: center;
            }
        }

        .order-info {
            display: flex;
            flex-direction: column;
        }

        .status-badge {
            text-align: center;
            width: 100px;
            font-size: 0.75rem;
            line-height: 1;
            padding: 0.25rem 0.625rem;
            border-radius: 9999px;
            font-weight: 600;
            color: #ffffff;
            margin-top: 0.5rem;
        }

        @media (min-width: 576px) {
            .status-badge {
                margin-top: 0;
            }
        }

        .status-badge.pending {
            background-color: #facc15;
        }

        .status-badge.shipped {
            background-color: #60a5fa;
        }

        .status-badge.delivered {
            background-color: #34d399;
        }

        .status-badge.cancelled {
            background-color: #ef4444;
        }

        .status-badge.finished {
            background-color: #22c55e;
        }

        .status-badge.return {
            background-color: #f97316;
        }

        .product-summary {
            font-size: 0.95rem;
            color: #4b5563;
        }

        .product-summary ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .product-summary li {
            padding: 0.25rem 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .product-summary li:last-child {
            border-bottom: none;
        }

        .product-summary .view-all-link {
            display: inline-block;
            margin-top: 0.5rem;
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }

        .product-total-price {
            text-align: right;
            font-size: 1rem;
            font-weight: 600;
            color: #1f2937;
            margin-top: 1rem;
        }

        /* --- New Grid & List View Styles --- */
        .order-view-container {
            display: flex;
            flex-direction: column;
        }
        
        .list-view .order-card {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem 1.5rem;
        }
        
        .list-view .order-header {
            margin-bottom: 0;
        }

        .list-view .product-summary {
            flex-grow: 1;
            padding: 0;
            min-width: 150px;
            margin-left: 2rem;
        }

        .list-view .product-summary ul {
            display: flex;
            flex-wrap: nowrap;
            gap: 1rem;
            align-items: center;
        }
        
        .list-view .product-summary li {
            border-bottom: none;
        }
        
        .grid-view .order-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
        }

        .grid-view .order-card {
            margin-bottom: 0;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        /* Ensure responsive layout in list view */
        @media (max-width: 767px) {
            .list-view .order-card {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            .list-view .product-summary {
                padding: 0;
            }
        }
    </style>

</head>

<body>
    <?php include '../template/head-bar.php'; ?>
    <main>
        <div id="sections_root_user" class="section-space">
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        <aside>
                            <div class="sidebar">
                                <div class="user-card-info" id="userContainer"></div>
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
        const userApp = {
            userData: {
                name: "กิตตินันท์ธนัช สีแก้วน้ำใส",
                profile: "https://placehold.co/60x60/888/fff?text=User",
                menu: [{
                    icon: '<i class="bi bi-person-circle"></i>',
                    text: "Account",
                    link: "#"
                }, {
                    icon: '<i class="bi bi-box-seam"></i>',
                    text: "Orders",
                    link: "#"
                }, {
                    icon: '<i class="bi bi-box-arrow-right"></i>',
                    text: "Logout",
                    link: "#"
                }]
            },

            data: {
                orders: [{
                    id: 'ORD12346',
                    status: 'pending',
                    statusText: 'ที่ต้องชำระ',
                    products: [{
                        name: 'แทรนดาร์ เอเอ็มเอฟ เมอร์เคียว',
                        quantity: 2,
                        price: '1500.00',
                        image: 'https://www.trandar.com//public/shop_img/687a1a94a6f10_Trandar_AMF_Mercure.jpg'
                    }, {
                        name: 'แทรนดาร์ เอเอ็มเอฟ ไฟน์ เฟรสโค',
                        quantity: 1,
                        price: '350.00',
                        image: 'https://www.trandar.com//public/shop_img/687a1aa984ae2_Trandar_AMF_Fine_Fresko.jpg'
                    }]
                }, {
                    id: 'ORD12347',
                    status: 'shipped',
                    statusText: 'จัดเตรียมสินค้า',
                    products: [{
                        name: 'แทรนดาร์ เอเอ็มเอฟ สตาร์',
                        quantity: 1,
                        price: '2200.00',
                        image: 'https://www.trandar.com//public/shop_img/687a1a756ce6a_Trandar_AMF_Star.jpg'
                    }]
                }, {
                    id: 'ORD12348',
                    status: 'delivered',
                    statusText: 'กำลังส่งมอบ',
                    products: [{
                        name: 'แทรนดาร์ ทีบาร์ ที15',
                        quantity: 1,
                        price: '3500.00',
                        image: 'https://www.trandar.com//public/shop_img/687b2f5b393b2_497eeb6fc69f5635590f41fc078dff98.jpg'
                    }]
                }, {
                    id: 'ORD12349',
                    status: 'cancelled',
                    statusText: 'ยกเลิกสินค้า',
                    products: [{
                        name: 'แทรนดาร์ ทีบาร์ ที24',
                        quantity: 1,
                        price: '4500.00',
                        image: 'https://www.trandar.com//public/shop_img/687b31d91b97e_T24.png'
                    }]
                }, {
                    id: 'ORD12350',
                    status: 'finished',
                    statusText: 'ส่งสินค้าสำเร็จ',
                    products: [{
                        name: 'แทรนดาร์ ไพรม์โคท',
                        quantity: 1,
                        price: '2800.00',
                        image: 'https://www.trandar.com//public/shop_img/687e1c0470c40_trandar_prime_coat_new_label.jpg'
                    }]
                }, {
                    id: 'ORD12351',
                    status: 'return',
                    statusText: 'คืนสินค้า',
                    products: [{
                        name: 'เครื่องฟอกอากาศ',
                        quantity: 1,
                        price: '5900.00',
                        image: 'https://placehold.co/80x80/fd7e14/ffffff?text=Product'
                    }]
                }, {
                    id: 'ORD12352',
                    status: 'pending',
                    statusText: 'ที่ต้องชำระ',
                    products: [{
                        name: 'สมาร์ทโฟน',
                        quantity: 1,
                        price: '12000.00',
                        image: 'https://placehold.co/80x80/3498db/ffffff?text=Product'
                    }]
                },
                {
                    id: 'ORD12353',
                    status: 'pending',
                    statusText: 'ที่ต้องชำระ',
                    products: [{
                        name: 'นาฬิกาข้อมือ',
                        quantity: 1,
                        price: '3500.00',
                        image: 'https://placehold.co/80x80/20c997/ffffff?text=Product'
                    }, {
                        name: 'กางเกงยีนส์เดนิม',
                        quantity: 2,
                        price: '1500.00',
                        image: 'https://placehold.co/80x80/007bff/ffffff?text=Product'
                    }, {
                        name: 'เสื้อยืดลายมินิมอลสีขาว',
                        quantity: 1,
                        price: '350.00',
                        image: 'https://placehold.co/80x80/28a745/ffffff?text=Product'
                    }, {
                        name: 'รองเท้าวิ่งสีดำ',
                        quantity: 1,
                        price: '2200.00',
                        image: 'https://placehold.co/80x80/ffc107/ffffff?text=Product'
                    }]
                }]
            },

            pages: {
                "Account": {
                    template: `
                    <div style="background:#fff; padding:1rem; border-radius:4px;">
                        <h4 class="fw-semibold mb-3">ข้อมูลบัญชี</h4>
                        <div id="account-container"></div>
                    </div>`,
                    render() {
                        this.containers = {
                            accountContainer: document.getElementById('account-container')
                        };
                        this.renderAccount();
                    }
                },
                "Orders": {
                    template: `
                    <div style="background:#fff; padding:1rem; border-radius:4px;">
                        <div id="orders-container"></div>
                    </div>`,
                    render() {
                        this.containers = {
                            ordersContainer: document.getElementById('orders-container')
                        };
                        this.renderListOrders();
                    }
                },
                "Logout": {
                    template: `
                <div style="background:#fff; padding:1rem; border-radius:4px;">
                    ออกจากระบบ
                </div>`,
                    render: null
                }
            },

            // Pagination state and settings
            pagination: {
                currentPage: 1,
                itemsPerPage: 5,
                totalPages: 0,
                totalItems: 0,
                filteredData: []
            },

            renderUserCard() {
                const container = document.getElementById("userContainer");
                container.innerHTML = `
                    <div class="user-card-info">
                        <img src="${this.userData.profile}" alt="User profile" class="stores-logo-info">
                        <h5 class="fw-semibold">${this.userData.name}</h5>
                    </div>
                    <div class="sidebar-menu">
                        <ul id="user-menu"></ul>
                    </div>
                `;
                this.renderMenu();
            },

            renderMenu() {
                const menuContainer = document.getElementById("user-menu");
                let menuHTML = '';
                this.userData.menu.forEach(item => {
                    menuHTML += `<li><a href="${item.link}">${item.icon} ${item.text}</a></li>`;
                });
                menuContainer.innerHTML = menuHTML;
            },

            bindMenuEvents() {
                const mainContent = document.getElementById("mainContent");
                document.querySelectorAll("#user-menu li a").forEach(link => {
                    link.addEventListener("click", e => {
                        e.preventDefault();
                        document.querySelectorAll("#user-menu li a").forEach(a => a.classList.remove("active"));
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

            init() {
                this.renderUserCard();
                this.bindMenuEvents();

                const firstMenu = document.querySelector('#user-menu li a');
                if (firstMenu) {
                    firstMenu.classList.add("active");
                    firstMenu.click();
                }
            },

            renderAccount() {
                this.containers.accountContainer.innerHTML = `
                <p>หน้าข้อมูลบัญชี</p>
            `;
            },

            renderListOrders() {
                const tabMap = {
                    'all': {
                        text: 'ทั้งหมด',
                        icon: '<i class="bi bi-card-list"></i>'
                    },
                    'pending': {
                        text: 'ที่ต้องชำระ',
                        icon: '<i class="bi bi-clock-history"></i>'
                    },
                    'shipped': {
                        text: 'จัดเตรียมสินค้า',
                        icon: '<i class="bi bi-bag-check"></i>'
                    },
                    'delivered': {
                        text: 'กำลังส่งมอบ',
                        icon: '<i class="bi bi-truck"></i>'
                    },
                    'finished': {
                        text: 'ส่งสินค้าสำเร็จ',
                        icon: '<i class="bi bi-check-circle"></i>'
                    },
                    'cancelled': {
                        text: 'ยกเลิกสินค้า',
                        icon: '<i class="bi bi-x-circle"></i>'
                    },
                    'return': {
                        text: 'คืนสินค้า',
                        icon: '<i class="bi bi-arrow-return-left"></i>'
                    }
                };

                let tabButtonsHTML = '';
                for (const key in tabMap) {
                    const value = tabMap[key];
                    const isActive = key === 'all' ? 'active' : '';
                    tabButtonsHTML += `<button data-tab-key="${key}" class="tab-order-button ${isActive}">${value.icon} ${value.text}</button>`;
                }

                this.containers.ordersContainer.innerHTML = `
                <div>
                    <h4 class="header mb-3">รายการคำสั่งซื้อของคุณ</h4>
                    <div class="tab-order-navigation">
                        ${tabButtonsHTML}
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <div id="showingEntries" class="showing-entries"></div>
                        <div class="controls">
                            <button id="gridBtn" class="active"><i class="bi bi-grid-3x3-gap-fill"></i></button>
                            <button id="listBtn"><i class="bi bi-list-task"></i></button>
                        </div>
                    </div>
                    <div id="tab-order-content">
                        <div class="order-view-container grid-view" id="order-list-container"></div>
                    </div>
                    <div id="paginationContainer" class="pagination"></div>
                </div>
                `;

                this.bindTabEvents();
                this.bindViewToggleEvents();
                this.filterAndRenderOrders('all');
            },

            renderOrderCard(order) {
                const totalItems = order.products.length;
                const productToDisplay = order.products[0];
                const totalPrice = order.products.reduce((sum, product) => {
                    const price = parseFloat(product.price.replace(/,/g, ''));
                    return sum + (price * product.quantity);
                }, 0).toFixed(2);

                let productListHTML = '';
                if (productToDisplay) {
                    productListHTML = `
                        <ul>
                            <li>
                                <div class="d-flex align-items-center" style="gap:10px;">
                                    <img src="${productToDisplay.image}" alt="${productToDisplay.name}" width="50" height="50" class="rounded border">
                                    <div>
                                        <span class="fw-semibold">${productToDisplay.name}</span>
                                        <br>
                                        <span class="text-muted small">x${productToDisplay.quantity}</span>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    `;
                }

                let trackLink = '';
                if (totalItems > 1) {
                    trackLink += `
                        <a href="#" class="view-all-link">ดูสินค้าทั้งหมด ${totalItems} รายการ</a>
                    `;
                }

                return `
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-info">
                                <span class="text-muted small">หมายเลขคำสั่งซื้อ</span>
                                <span class="fw-semibold text-dark">#${order.id}</span>
                            </div>
                            <span class="status-badge ${order.status}">${order.statusText}</span>
                        </div>
                        <div class="product-summary">
                            ${productListHTML}
                            <div class="d-flex justify-content-between">
                                <a href="#" class="view-all-link">ติดตาม</a>
                                ${trackLink}
                            </div>
                        </div>
                        <div class="product-total-price">
                            <span>ยอดรวม: ${totalPrice} บาท</span>
                        </div>
                    </div>
                `;
            },
            
            filterAndRenderOrders(status) {
                let filteredOrders = (status === 'all')
                    ? this.data.orders
                    : this.data.orders.filter(order => order.status === status);
                
                this.pagination.filteredData = filteredOrders;
                this.pagination.totalItems = filteredOrders.length;
                this.pagination.totalPages = Math.ceil(this.pagination.totalItems / this.pagination.itemsPerPage);

                this.renderCurrentPage();
            },

            renderCurrentPage() {
                const {
                    currentPage,
                    itemsPerPage,
                    filteredData
                } = this.pagination;

                const start = (currentPage - 1) * itemsPerPage;
                const end = start + itemsPerPage;
                const currentData = filteredData.slice(start, end);

                const orderCardsHTML = currentData.map(order => this.renderOrderCard(order)).join('');
                const container = document.getElementById('order-list-container');
                container.innerHTML = `<div class="order-cards">${orderCardsHTML}</div>`;
                
                this.renderPaginationControls();
                this.renderShowingEntries();
            },

            renderPaginationControls() {
                const {
                    currentPage,
                    totalPages
                } = this.pagination;
                const container = document.getElementById('paginationContainer');
                container.innerHTML = ''; // Clear previous buttons

                if (totalPages <= 1) {
                    return;
                }

                // Previous button
                const prevBtn = document.createElement('button');
                prevBtn.textContent = 'ย้อนกลับ';
                prevBtn.disabled = currentPage === 1;
                prevBtn.addEventListener('click', () => {
                    this.pagination.currentPage--;
                    this.renderCurrentPage();
                });
                container.appendChild(prevBtn);

                // Numbered pages
                for (let i = 1; i <= totalPages; i++) {
                    const pageBtn = document.createElement('button');
                    pageBtn.textContent = i;
                    pageBtn.classList.toggle('active', i === currentPage);
                    pageBtn.addEventListener('click', () => {
                        this.pagination.currentPage = i;
                        this.renderCurrentPage();
                    });
                    container.appendChild(pageBtn);
                }

                // Next button
                const nextBtn = document.createElement('button');
                nextBtn.textContent = 'ถัดไป';
                nextBtn.disabled = currentPage === totalPages;
                nextBtn.addEventListener('click', () => {
                    this.pagination.currentPage++;
                    this.renderCurrentPage();
                });
                container.appendChild(nextBtn);
            },

            renderShowingEntries() {
                const {
                    currentPage,
                    itemsPerPage,
                    totalItems
                } = this.pagination;

                const startEntry = (totalItems > 0) ? (currentPage - 1) * itemsPerPage + 1 : 0;
                const endEntry = Math.min(startEntry + itemsPerPage - 1, totalItems);
                const showingEntriesText = `แสดง ${startEntry} ถึง ${endEntry} จาก ${totalItems} รายการ`;

                document.getElementById('showingEntries').textContent = showingEntriesText;
            },

            resetPagination() {
                this.pagination.currentPage = 1;
            },

            bindTabEvents() {
                const tabButtons = document.querySelectorAll('.tab-order-navigation .tab-order-button');
                tabButtons.forEach(button => {
                    button.addEventListener('click', () => {
                        tabButtons.forEach(btn => btn.classList.remove('active'));
                        button.classList.add('active');
                        const tabKey = button.getAttribute('data-tab-key');
                        this.resetPagination();
                        this.filterAndRenderOrders(tabKey);
                    });
                });
            },

            bindViewToggleEvents() {
                const gridBtn = document.getElementById('gridBtn');
                const listBtn = document.getElementById('listBtn');
                const orderViewContainer = document.getElementById('order-list-container');

                gridBtn.addEventListener('click', () => {
                    gridBtn.classList.add('active');
                    listBtn.classList.remove('active');
                    orderViewContainer.classList.add('grid-view');
                    orderViewContainer.classList.remove('list-view');
                });

                listBtn.addEventListener('click', () => {
                    listBtn.classList.add('active');
                    gridBtn.classList.remove('active');
                    orderViewContainer.classList.add('list-view');
                    orderViewContainer.classList.remove('grid-view');
                });
            }
        };
        userApp.init();
    </script>

</body>

</html>
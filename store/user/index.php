<?php include '../routes.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-STORE</title>
    <?php include '../inc-meta.php'; ?>
    <link href="../css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../css/user/template-user.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../inc-cdn.php'; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
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

        .tab-order-content {
            padding-top: 1.5rem;
            display: none;
        }

        .tab-order-content.active {
            display: block;
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
            padding: 0 2rem;
        }

        .list-view .product-summary ul {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
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
                profile: "",
                menu: [{
                    icon: "",
                    text: "Account",
                    link: "#"
                }, {
                    icon: '<i class="bi bi-shop"></i>',
                    text: "Orders",
                    link: "#"
                }, {
                    icon: "",
                    text: "Logout",
                    link: "#"
                }]
            },

            data: {
                orders: [{
                    id: 'ORD12346',
                    status: 'pending',
                    statusText: 'Pending',
                    products: [{
                        name: 'กางเกงยีนส์เดนิม',
                        quantity: 2,
                        price: '1,500.00',
                        image: 'https://placehold.co/80x80/007bff/ffffff?text=Product'
                    }, {
                        name: 'เสื้อยืดลายมินิมอลสีขาว',
                        quantity: 1,
                        price: '350.00',
                        image: 'https://placehold.co/80x80/28a745/ffffff?text=Product'
                    }]
                }, {
                    id: 'ORD12347',
                    status: 'shipped',
                    statusText: 'Shipped',
                    products: [{
                        name: 'รองเท้าวิ่งสีดำ',
                        quantity: 1,
                        price: '2,200.00',
                        image: 'https://placehold.co/80x80/ffc107/ffffff?text=Product'
                    }]
                }, {
                    id: 'ORD12348',
                    status: 'delivered',
                    statusText: 'Delivered',
                    products: [{
                        name: 'นาฬิกาข้อมือ',
                        quantity: 1,
                        price: '3,500.00',
                        image: 'https://placehold.co/80x80/20c997/ffffff?text=Product'
                    }]
                }, {
                    id: 'ORD12349',
                    status: 'cancelled',
                    statusText: 'Cancelled',
                    products: [{
                        name: 'กระเป๋าเดินทาง',
                        quantity: 1,
                        price: '4,500.00',
                        image: 'https://placehold.co/80x80/dc3545/ffffff?text=Product'
                    }]
                }, {
                    id: 'ORD12350',
                    status: 'finished',
                    statusText: 'Finished',
                    products: [{
                        name: 'หูฟังไร้สาย',
                        quantity: 1,
                        price: '2,800.00',
                        image: 'https://placehold.co/80x80/28a745/ffffff?text=Product'
                    }]
                }, {
                    id: 'ORD12351',
                    status: 'return',
                    statusText: 'Return',
                    products: [{
                        name: 'เครื่องฟอกอากาศ',
                        quantity: 1,
                        price: '5,900.00',
                        image: 'https://placehold.co/80x80/fd7e14/ffffff?text=Product'
                    }]
                }, {
                    id: 'ORD12352',
                    status: 'pending',
                    statusText: 'Pending',
                    products: [{
                        name: 'สมาร์ทโฟน',
                        quantity: 1,
                        price: '12,000.00',
                        image: 'https://placehold.co/80x80/3498db/ffffff?text=Product'
                    }]
                },
                {
                    id: 'ORD12353',
                    status: 'pending',
                    statusText: 'Pending',
                    products: [{
                        name: 'นาฬิกาข้อมือ',
                        quantity: 1,
                        price: '3,500.00',
                        image: 'https://placehold.co/80x80/20c997/ffffff?text=Product'
                    }, {
                        name: 'กางเกงยีนส์เดนิม',
                        quantity: 2,
                        price: '1,500.00',
                        image: 'https://placehold.co/80x80/007bff/ffffff?text=Product'
                    }, {
                        name: 'เสื้อยืดลายมินิมอลสีขาว',
                        quantity: 1,
                        price: '350.00',
                        image: 'https://placehold.co/80x80/28a745/ffffff?text=Product'
                    }, {
                        name: 'รองเท้าวิ่งสีดำ',
                        quantity: 1,
                        price: '2,200.00',
                        image: 'https://placehold.co/80x80/ffc107/ffffff?text=Product'
                    }]
                }]
            },

            pages: {
                "Account": {
                    template: `
                    <div style="background:#fff; padding:1rem; border-radius:4px;">
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

            renderMenu() {
                const container = document.getElementById("userContainer");
                let menuHTML = `<ul id="user-menu">`;
                this.userData.menu.forEach(item => {
                    menuHTML += `<li><a href="${item.link}">${item.icon} ${item.text}</a></li>`;
                });
                menuHTML += `</ul>`;
                container.innerHTML = menuHTML;
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
                this.renderMenu();
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
                let tabContentsHTML = '';
                let isFirstTab = true;

                for (const key in tabMap) {
                    if (tabMap.hasOwnProperty(key)) {
                        const value = tabMap[key];
                        const isActive = isFirstTab ? 'active' : '';
                        tabButtonsHTML += `<button data-tab-key="${key}" class="tab-order-button ${isActive}">${value.icon} ${value.text}</button>`;

                        let filteredOrders = [];
                        if (key === 'all') {
                            filteredOrders = this.data.orders;
                        } else {
                            filteredOrders = this.data.orders.filter(order => order.status === key);
                        }

                        let orderCardsHTML = filteredOrders.map(order => this.renderOrderCard(order)).join('');
                        tabContentsHTML += `<div data-content-key="${key}" class="tab-order-content ${isActive}"><div class="order-view-container grid-view"><div class="order-cards">${orderCardsHTML}</div></div></div>`;

                        isFirstTab = false;
                    }
                }

                this.containers.ordersContainer.innerHTML = `
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="header mb-0">รายการคำสั่งซื้อของคุณ</h5>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary btn-sm active" id="gridBtn"><i class="bi bi-grid-3x3-gap-fill"></i></button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="listBtn"><i class="bi bi-list-task"></i></button>
                        </div>
                    </div>
                    <div class="tab-order-navigation">
                        ${tabButtonsHTML}
                    </div>
                    <div id="tab-order-content">
                        ${tabContentsHTML}
                    </div>
                </div>
            `;
                this.bindTabEvents();
                this.bindViewToggleEvents();
            },

            renderOrderCard(order) {
                const totalItems = order.products.length;
                let productListHTML = '';
                let displayCount = 3;

                if (totalItems > 0) {
                    const productsToDisplay = order.products.slice(0, displayCount);
                    productListHTML = `<ul>` + productsToDisplay.map(product => `
                        <li>${product.name} (x${product.quantity})</li>
                    `).join('') + `</ul>`;

                    if (totalItems > displayCount) {
                        productListHTML += `<a href="#" class="view-all-link">ดูสินค้าทั้งหมด ${totalItems} รายการ</a>`;
                    }
                } else {
                    productListHTML = `<ul><li>ไม่มีรายการสินค้า</li></ul>`;
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
                    </div>
                </div>
                `;
            },
            
            bindTabEvents() {
                const tabButtons = document.querySelectorAll('.tab-order-navigation .tab-order-button');
                const tabContents = document.querySelectorAll('#tab-order-content .tab-order-content');

                tabButtons.forEach(button => {
                    button.addEventListener('click', () => {
                        tabButtons.forEach(btn => btn.classList.remove('active'));
                        tabContents.forEach(content => content.classList.remove('active'));

                        button.classList.add('active');
                        const tabKey = button.getAttribute('data-tab-key');
                        document.querySelector(`[data-content-key="${tabKey}"]`).classList.add('active');
                    });
                });
            },

            bindViewToggleEvents() {
                const gridBtn = document.getElementById('gridBtn');
                const listBtn = document.getElementById('listBtn');
                const orderViewContainers = document.querySelectorAll('.order-view-container');

                gridBtn.addEventListener('click', () => {
                    gridBtn.classList.add('active');
                    listBtn.classList.remove('active');
                    orderViewContainers.forEach(container => {
                        container.classList.add('grid-view');
                        container.classList.remove('list-view');
                    });
                });

                listBtn.addEventListener('click', () => {
                    listBtn.classList.add('active');
                    gridBtn.classList.remove('active');
                    orderViewContainers.forEach(container => {
                        container.classList.add('list-view');
                        container.classList.remove('grid-view');
                    });
                });
            }
        };
        userApp.init();
    </script>

</body>

</html>
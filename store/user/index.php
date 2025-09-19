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
                }, 
                {
                    icon: '<i class="bi bi-box-seam"></i>',
                    text: "Orders",
                    link: "#"
                }, 
                {
                    icon: '<i class="bi bi-receipt"></i>',
                    text: "Coupon",
                    link: "#"
                },
                {
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
                        <img src="${this.userData.profile}" alt="User profile" class="user-logo-info">
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
                                <a href="${pathConfig.BASE_WEB}user/track/" class="view-all-link">ติดตาม</a>
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
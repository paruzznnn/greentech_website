<?php include '../routes.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-STORE</title>
    <?php include '../inc-meta.php'; ?>
    <link href="../css/admin/template-admin.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../inc-cdn.php'; ?>
</head>

<body>
    <div class="mobile-sidebar-overlay" id="mobileSidebarOverlay"></div>

    <div class="mobile-sidebar" id="mobileSidebar">
        <div class="mobile-sidebar-header">
            <h5 class="mobile-sidebar-title">เมนู Admin</h5>
            <button type="button" class="mobile-sidebar-close-btn" aria-label="Close Mobile Menu">&times;</button>
        </div>
        <nav class="mobile-sidebar-nav" id="mobileSidebarNav"></nav>
    </div>

    <header class="admin-header">
        <div class="logo-container">
            <button class="mobile-sidebar-toggle-btn" id="mobileSidebarToggleBtn" aria-label="Toggle Mobile Sidebar">
                <i class="bi bi-list"></i>
            </button>
            <button class="desktop-sidebar-toggle-btn" id="desktopSidebarToggleBtn" aria-label="Toggle Desktop Sidebar">
                <i class="bi bi-list"></i>
            </button>
            <h1>Admin Dashboard</h1>
        </div>

        <div class="user-menu" id="userMenu">
            <button class="user-menu-toggle" id="userMenuToggle" aria-expanded="false">
                <img src="https://placehold.co/32x32/ffffff/000000?text=KS" alt="User Avatar">
                <span class="user-name">Admin</span>
            </button>
            <div class="user-dropdown" id="userDropdown">
                <a href="#">โปรไฟล์</a>
                <a href="#">ข้อความ</a>
                <div class="divider"></div>
                <a href="#" class="logout-item">ออกจากระบบ</a>
            </div>
        </div>
    </header>

    <div class="wrapper" id="wrapper">
        <aside class="sidebar" id="desktopSidebar">
            <h6 class="sidebar-heading">
                <span>เมนูหลัก</span>
            </h6>
            <nav class="sidebar-nav" id="sidebarNav"></nav>
        </aside>

        <main class="main-content">

        </main>
    </div>

    <script>

        // ข้อมูลเมนูหลัก
        const MENU_DATA = [{
                icon: '<i class="bi bi-speedometer2"></i>',
                title: "แดชบอร์ด",
                link: "#dashboard"
            },
            {
                icon: '<i class="bi bi-people"></i>',
                title: "ผู้ใช้งาน",
                link: "#users",
                subMenu: [{
                        icon: '<i class="bi bi-person-lines-fill"></i>',
                        title: "สมาชิกทั้งหมด",
                        link: "#users/all"
                    },
                    {
                        icon: '<i class="bi bi-person-plus"></i>',
                        title: "เพิ่มสมาชิกใหม่",
                        link: "#users/add"
                    },
                    {
                        icon: '<i class="bi bi-people-fill"></i>',
                        title: "กลุ่มผู้ใช้",
                        link: "#users/groups",
                        subMenu: [{
                                icon: '<i class="bi bi-shield-check"></i>',
                                title: "Admin",
                                link: "#users/groups/admin"
                            },
                            {
                                icon: '<i class="bi bi-shield"></i>',
                                title: "Moderator",
                                link: "#users/groups/moderator"
                            },
                            {
                                icon: '<i class="bi bi-person-circle"></i>',
                                title: "Customer",
                                link: "#users/groups/customer"
                            }
                        ]
                    }
                ]
            },
            {
                icon: '<i class="bi bi-box-seam"></i>',
                title: "สินค้า",
                link: "#products",
                subMenu: [{
                        icon: '<i class="bi bi-grid"></i>',
                        title: "สินค้าทั้งหมด",
                        link: "#products/all"
                    },
                    {
                        icon: '<i class="bi bi-plus-circle"></i>',
                        title: "เพิ่มสินค้าใหม่",
                        link: "#products/add"
                    },
                    {
                        icon: '<i class="bi bi-tags"></i>',
                        title: "หมวดหมู่สินค้า",
                        link: "#products/categories"
                    }
                ]
            },
            {
                icon: '<i class="bi bi-graph-up"></i>',
                title: "รายงาน",
                link: "#reports"
            },
            {
                icon: '<i class="bi bi-gear"></i>',
                title: "การตั้งค่า",
                link: "#settings"
            },
            {
                icon: '<i class="bi bi-clock-history"></i>',
                title: "ประวัติ",
                link: "#history"
            }
        ];

        /**
         * Object สำหรับจัดการ Admin Dashboard
         */
        const AdminDashboard = {
            // Properties
            elements: {},
            isInitialized: false,
            resizeTimer: null,

            /**
             * เริ่มต้นระบบ
             */
            init() {
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', () => this.setup());
                } else {
                    this.setup();
                }
            },

            /**
             * ตั้งค่าเริ่มต้น
             */
            setup() {
                try {
                    this.cacheElements();
                    this.renderMenus();
                    this.bindEvents();
                    this.isInitialized = true;
                    console.info('✅ Admin Dashboard initialized successfully');
                } catch (error) {
                    console.error('❌ Error initializing Admin Dashboard:', error);
                }
            },

            /**
             * เก็บ DOM elements ไว้ในแคช
             */
            cacheElements() {
                this.elements = {
                    wrapper: document.getElementById('wrapper'),

                    // Desktop Sidebar
                    desktopSidebar: document.getElementById('desktopSidebar'),
                    desktopSidebarToggleBtn: document.getElementById('desktopSidebarToggleBtn'),
                    sidebarNav: document.getElementById('sidebarNav'),

                    // Mobile Sidebar
                    mobileSidebar: document.getElementById('mobileSidebar'),
                    mobileSidebarToggleBtn: document.getElementById('mobileSidebarToggleBtn'),
                    mobileSidebarOverlay: document.getElementById('mobileSidebarOverlay'),
                    mobileSidebarCloseBtn: document.querySelector('#mobileSidebar .mobile-sidebar-close-btn'),
                    mobileNav: document.getElementById('mobileSidebarNav'),

                    // User Dropdown
                    userMenuToggle: document.getElementById('userMenuToggle'),
                    userDropdown: document.getElementById('userDropdown')
                };

                // ตรวจสอบ elements ที่จำเป็น
                const requiredElements = ['wrapper'];
                const missingElements = requiredElements.filter(key => !this.elements[key]);

                if (missingElements.length > 0) {
                    console.warn('⚠️ Missing required elements:', missingElements);
                }
            },

            /**
             * สร้างเมนูทั้งหมด
             */
            renderMenus() {
                // สร้างเมนู Desktop
                if (this.elements.sidebarNav) {
                    this.renderMenu(MENU_DATA, this.elements.sidebarNav, false);
                }

                // สร้างเมนู Mobile
                if (this.elements.mobileNav) {
                    this.renderMenu(MENU_DATA, this.elements.mobileNav, true);
                }
            },

            /**
             * สร้างเมนูแบบ recursive
             */
            renderMenu(menuItems, parentElement, isMobile = false) {
                const fragment = document.createDocumentFragment();

                menuItems.forEach(item => {
                    const menuItem = this.createMenuItem(item, isMobile);
                    fragment.appendChild(menuItem);
                });

                parentElement.appendChild(fragment);
            },

            /**
             * สร้าง menu item แต่ละตัว
             */
            createMenuItem(item, isMobile) {
                const li = document.createElement('li');
                li.classList.add('nav-item');

                const a = document.createElement('a');
                a.classList.add('sidebar-link');
                a.href = item.link || '#';
                a.setAttribute('data-title', item.title);

                if (item.subMenu) {
                    li.classList.add('has-sub-menu');
                    a.setAttribute('data-action', 'toggle-sub-menu');

                    const iconHTML = item.icon || '';
                    const toggleIcon = isMobile ?
                        '<i class="bi bi-chevron-right toggle-icon"></i>' :
                        '<i class="bi bi-chevron-down toggle-icon"></i>';

                    a.innerHTML = `
                <span class="sidebar-text">
                    ${iconHTML}
                    <span class="menu-title">${item.title}</span>
                    ${toggleIcon}
                </span>
            `;

                    // สร้าง submenu
                    const subUl = document.createElement('ul');
                    subUl.classList.add('sidebar-sub-menu');
                    this.renderMenu(item.subMenu, subUl, isMobile);

                    li.appendChild(a);
                    li.appendChild(subUl);
                } else {
                    const iconHTML = item.icon || '';
                    a.innerHTML = `
                <span class="sidebar-text">
                    ${iconHTML}
                    <span class="menu-title">${item.title}</span>
                </span>
            `;
                    li.appendChild(a);
                }

                return li;
            },

            /**
             * ผูก event listeners ทั้งหมด
             */
            bindEvents() {
                this.bindDesktopSidebarEvents();
                this.bindMobileSidebarEvents();
                this.bindUserDropdownEvents();
                this.bindSubMenuEvents();
                this.bindWindowEvents();
            },

            /**
             * ผูก events สำหรับ Desktop Sidebar
             */
            bindDesktopSidebarEvents() {
                const {
                    desktopSidebarToggleBtn,
                    wrapper,
                    desktopSidebar
                } = this.elements;

                if (!desktopSidebarToggleBtn || !wrapper) {
                    console.warn('⚠️ Desktop sidebar elements not found');
                    return;
                }

                // Toggle sidebar
                desktopSidebarToggleBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.toggleDesktopSidebar();
                });

                // Click outside to close
                if (desktopSidebar) {
                    document.addEventListener('click', (e) => {
                        if (this.isDesktopView() && this.isDesktopSidebarOpen()) {
                            if (!desktopSidebar.contains(e.target) &&
                                !desktopSidebarToggleBtn.contains(e.target)) {
                                this.closeDesktopSidebar();
                            }
                        }
                    });
                }
            },

            /**
             * ผูก events สำหรับ Mobile Sidebar
             */
            bindMobileSidebarEvents() {
                const {
                    mobileSidebarToggleBtn,
                    mobileSidebarCloseBtn,
                    mobileSidebarOverlay
                } = this.elements;

                // Open mobile sidebar
                if (mobileSidebarToggleBtn) {
                    mobileSidebarToggleBtn.addEventListener('click', () => {
                        this.openMobileSidebar();
                    });
                }

                // Close mobile sidebar
                if (mobileSidebarCloseBtn) {
                    mobileSidebarCloseBtn.addEventListener('click', () => {
                        this.closeMobileSidebar();
                    });
                }

                // Overlay click to close
                if (mobileSidebarOverlay) {
                    mobileSidebarOverlay.addEventListener('click', () => {
                        this.closeMobileSidebar();
                    });
                }
            },

            /**
             * ผูก events สำหรับ User Dropdown
             */
            bindUserDropdownEvents() {
                const {
                    userMenuToggle,
                    userDropdown
                } = this.elements;

                if (!userMenuToggle || !userDropdown) {
                    return;
                }

                // Toggle dropdown
                userMenuToggle.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.toggleUserDropdown();
                });

                // Click outside to close
                document.addEventListener('click', (e) => {
                    if (!userMenuToggle.contains(e.target) &&
                        !userDropdown.contains(e.target)) {
                        this.closeUserDropdown();
                    }
                });
            },

            /**
             * ผูก events สำหรับ Sub Menu
             */
            bindSubMenuEvents() {
                // ใช้ event delegation
                document.addEventListener('click', (e) => {
                    const toggleBtn = e.target.closest('[data-action="toggle-sub-menu"]');
                    if (toggleBtn) {
                        e.preventDefault();
                        this.toggleSubMenu(toggleBtn);
                    }
                });
            },

            /**
             * ผูก events สำหรับ Window
             */
            bindWindowEvents() {
                // Resize handler
                window.addEventListener('resize', () => {
                    clearTimeout(this.resizeTimer);
                    this.resizeTimer = setTimeout(() => {
                        this.handleWindowResize();
                    }, 250);
                });

                // Escape key handler
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        this.closeAllMenus();
                    }
                });
            },

            /**
             * Toggle Desktop Sidebar
             */
            toggleDesktopSidebar() {
                if (!this.elements.wrapper) return;

                const isOpen = this.isDesktopSidebarOpen();

                if (isOpen) {
                    this.closeDesktopSidebar();
                } else {
                    this.openDesktopSidebar();
                }
            },

            /**
             * เปิด Desktop Sidebar
             */
            openDesktopSidebar() {
                if (this.elements.wrapper) {
                    this.elements.wrapper.classList.remove('toggled');
                }
            },

            /**
             * ปิด Desktop Sidebar
             */
            closeDesktopSidebar() {
                if (this.elements.wrapper) {
                    this.elements.wrapper.classList.add('toggled');
                    this.closeAllSubMenus();
                }
            },

            /**
             * เปิด Mobile Sidebar
             */
            openMobileSidebar() {
                document.body.classList.add('mobile-sidebar-open');
                this.closeAllSubMenus(this.elements.mobileSidebar);
            },

            /**
             * ปิด Mobile Sidebar
             */
            closeMobileSidebar() {
                document.body.classList.remove('mobile-sidebar-open');
            },

            /**
             * Toggle User Dropdown
             */
            toggleUserDropdown() {
                const {
                    userMenuToggle,
                    userDropdown
                } = this.elements;
                if (!userMenuToggle || !userDropdown) return;

                const isOpen = userDropdown.classList.contains('show');

                if (isOpen) {
                    this.closeUserDropdown();
                } else {
                    this.openUserDropdown();
                }
            },

            /**
             * เปิด User Dropdown
             */
            openUserDropdown() {
                const {
                    userMenuToggle,
                    userDropdown
                } = this.elements;
                if (!userMenuToggle || !userDropdown) return;

                userDropdown.classList.add('show');
                userMenuToggle.setAttribute('aria-expanded', 'true');
            },

            /**
             * ปิด User Dropdown
             */
            closeUserDropdown() {
                const {
                    userMenuToggle,
                    userDropdown
                } = this.elements;
                if (!userMenuToggle || !userDropdown) return;

                userDropdown.classList.remove('show');
                userMenuToggle.setAttribute('aria-expanded', 'false');
            },

            /**
             * Toggle Sub Menu
             */
            toggleSubMenu(toggleBtn) {
                const parentNavItem = toggleBtn.closest('.nav-item');
                const subMenu = parentNavItem?.querySelector('.sidebar-sub-menu');

                if (!subMenu) {
                    console.warn('⚠️ Sub-menu not found for toggle button');
                    return;
                }

                const canToggle = this.canToggleSubMenu();
                if (!canToggle) return;

                const isOpen = subMenu.classList.contains('show');

                if (isOpen) {
                    this.closeSubMenu(subMenu, toggleBtn);
                } else {
                    this.openSubMenu(subMenu, toggleBtn);
                }
            },

            /**
             * เปิด Sub Menu
             */
            openSubMenu(subMenu, toggleBtn) {
                subMenu.classList.add('show');
                toggleBtn.classList.add('open');
            },

            /**
             * ปิด Sub Menu
             */
            closeSubMenu(subMenu, toggleBtn) {
                subMenu.classList.remove('show');
                toggleBtn.classList.remove('open');
            },

            /**
             * ปิด Sub Menu ทั้งหมด
             */
            closeAllSubMenus(container = document) {
                const allSubMenus = container.querySelectorAll('.sidebar-sub-menu');
                const allToggleBtns = container.querySelectorAll('[data-action="toggle-sub-menu"]');

                allSubMenus.forEach(subMenu => subMenu.classList.remove('show'));
                allToggleBtns.forEach(btn => btn.classList.remove('open'));
            },

            /**
             * ปิดเมนูทั้งหมด
             */
            closeAllMenus() {
                this.closeMobileSidebar();
                this.closeUserDropdown();
                this.closeAllSubMenus();
            },

            /**
             * จัดการเมื่อหน้าจอเปลี่ยนขนาด
             */
            handleWindowResize() {
                if (this.isDesktopView()) {
                    this.closeMobileSidebar();
                }
            },

            /**
             * ตรวจสอบว่าเป็นมุมมอง Desktop หรือไม่
             */
            isDesktopView() {
                return window.innerWidth >= 768;
            },

            /**
             * ตรวจสอบว่า Desktop Sidebar เปิดอยู่หรือไม่
             */
            isDesktopSidebarOpen() {
                return this.elements.wrapper && !this.elements.wrapper.classList.contains('toggled');
            },

            /**
             * ตรวจสอบว่า Mobile Sidebar เปิดอยู่หรือไม่
             */
            isMobileSidebarOpen() {
                return document.body.classList.contains('mobile-sidebar-open');
            },

            /**
             * ตรวจสอบว่าสามารถ toggle sub menu ได้หรือไม่
             */
            canToggleSubMenu() {
                if (this.isDesktopView()) {
                    return this.isDesktopSidebarOpen();
                } else {
                    return this.isMobileSidebarOpen();
                }
            },

            /**
             * เพิ่มเมนูใหม่แบบ dynamic
             */
            addMenuItem(menuItem, parentId = null) {
                // TODO: Implement dynamic menu addition
                console.info('Adding menu item:', menuItem);
            },

            /**
             * ลบเมนู
             */
            removeMenuItem(itemId) {
                // TODO: Implement menu item removal
                console.info('Removing menu item:', itemId);
            },

            /**
             * รีเฟรชเมนู
             */
            refreshMenu() {
                if (this.elements.sidebarNav) {
                    this.elements.sidebarNav.innerHTML = '';
                    this.renderMenu(MENU_DATA, this.elements.sidebarNav, false);
                }

                if (this.elements.mobileNav) {
                    this.elements.mobileNav.innerHTML = '';
                    this.renderMenu(MENU_DATA, this.elements.mobileNav, true);
                }

                this.bindSubMenuEvents();
                console.info('✅ Menu refreshed');
            },

            /**
             * รีเซ็ตสถานะทั้งหมด
             */
            reset() {
                this.closeAllMenus();
                if (this.elements.wrapper) {
                    this.elements.wrapper.classList.add('toggled');
                }
                console.info('🔄 Admin Dashboard reset');
            }
        };

        // เริ่มต้นระบบอัตโนมัติ
        AdminDashboard.init();

        // Export สำหรับใช้งานภายนอก
        if (typeof module !== 'undefined' && module.exports) {
            module.exports = AdminDashboard;
        }

        // เพิ่มใน global scope สำหรับการ debug
        window.AdminDashboard = AdminDashboard;
    </script>

</body>

</html>
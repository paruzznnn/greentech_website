<!-- header 1 -->
<header class="header-store1">
    <nav style="padding: 1rem 0rem;">
        <div class="container">
            <div class="nav-store1">
                <div class="nav-store1-box-logo">
                    <div id="menu-open-store1">
                        <i class="fas fa-bars"></i>
                    </div>
                    <div id="menu-close-store1" class="hidden">
                        <i class="fas fa-times"></i>
                    </div>
                    <div><img src="<?php echo $BASE_WEB ?>trandar_logo.png" alt="Logo"></div>
                </div>
                <div class="nav-store1-box-menu">
                    <div id="langButtons" class="lang-buttons-container">
                        <i class="bi bi-globe"></i>
                        <button data-lang="en" type="button" class="btn-lang">English</button>
                        <button data-lang="th" type="button" class="btn-lang">Thai</button>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>

<!-- header 2 -->
<header class="header-store2">
    <nav class="pt-3 pb-3">
        <div class="container">
            <div class="nav-store2">
                <div id="menu-open-store2" class="nav-store2-box-menu">
                    <i class="bi bi-border-width"></i>
                </div>
                <div id="menu-close-store2" class="nav-store2-box-menu hidden">
                    <i class="fas fa-times"></i>
                </div>
                <div class="nav-store2-box-logo">
                    <a href="<?php echo $BASE_WEB ?>">
                        <img src="<?php echo $BASE_WEB ?>trandar_logo.png" alt="Logo">
                    </a>
                </div>
                <div class="nav-store2-box-menu">
                    <div id="menu1-open-store1">
                        <i class="bi bi-person-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>


<!-- Notify -->
<div id="notificationContainer" class="notification-container">
    <div id="notificationMessage" class="notification-message"></div>
</div>


<!-- sidenav 1 -->
<aside id="sidenav-store1" class="sidenav">
    <div>
        <ul id="menuListContainerAdmin1"></ul>
    </div>
</aside>

<!-- sidenav 2 -->
<aside id="sidenav-store2" class="sidenav">
    <div class="pt-1">
        <ul id="menuListContainerAdmin2"></ul>
    </div>
</aside>
<div id="overlay-store2"></div>

<nav id="breadcrumb-box">
    <div class="container">
        <ul id="breadcrumb-list">
        </ul>
    </div>
</nav>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const breadcrumbList = document.getElementById('breadcrumb-list');
    const breadcrumbBox = document.getElementById('breadcrumb-box');

    const pathSegments = window.location.pathname
        .split('/')
        .filter(segment => segment !== '');

    // ตรวจสอบว่าอยู่หน้า admin root หรือไม่
    const isAdminRoot = (
        pathSegments.length === 3 &&
        pathSegments[0] === 'trandar_website' &&
        pathSegments[1] === 'store' &&
        pathSegments[2] === 'admin'
    );

    // ซ่อน breadcrumb ถ้าอยู่หน้า admin root
    if (isAdminRoot) {
        breadcrumbBox.style.display = 'none';
        return;
    } else {
        breadcrumbBox.style.display = 'block';
    }

    // กรอง segment ที่ไม่จำเป็น
    const filteredSegments = pathSegments.filter(
        segment => segment !== 'trandar_website' && segment !== 'store'
    );

    // เริ่มต้น breadcrumb ด้วย 'Admin'
    function createBreadcrumbItem(text, url = null, isLast = false) {
        const li = document.createElement('li');
        li.className = 'flex items-center';

        const displayText = text.replace(/-/g, ' ');
        const dataLang = text;

        if (url && !isLast) {
            const a = document.createElement('a');
            a.href = pathConfig.BASE_WEB + url;
            a.textContent = displayText;
            a.setAttribute('data-lang', dataLang);
            li.appendChild(a);
        } else {
            const span = document.createElement('span');
            span.className = 'text-gray-500';
            span.textContent = displayText;
            span.setAttribute('data-lang', dataLang);
            li.appendChild(span);
        }

        if (!isLast) {
            const separator = document.createElement('span');
            separator.className = 'mx-2';
            separator.innerHTML = '<i class="bi bi-chevron-right"></i>';
            li.appendChild(separator);
        }

        return li;
    }

    // เริ่มต้นที่ Admin
    breadcrumbList.appendChild(
        createBreadcrumbItem('admin', '/admin', false)
    );

    // ประมวลผล breadcrumb ต่อจาก admin
    let currentPath = '/admin';
    filteredSegments.slice(1).forEach((segment, index, arr) => {
        currentPath += '/' + segment;
        const isLast = index === arr.length - 1;
        breadcrumbList.appendChild(
            createBreadcrumbItem(segment, currentPath, isLast)
        );
    });
});
</script>



<script type="module">
    Promise.all([
            import(`${pathConfig.BASE_WEB}js/formHandler.js?v=<?php echo time(); ?>`),
            import(`${pathConfig.BASE_WEB}js/menuBuilder.js?v=<?php echo time(); ?>`)
        ])
        .then(async ([formHandler, menuBuilder]) => {
            const {
                handleFormSubmit
            } = formHandler;
            const {
                fetchHeader,
                buildLinkmenuSlideAdmin
            } = menuBuilder;

            const service = pathConfig.BASE_WEB + 'service/admin/header-data.php?';
            const menuData = await fetchHeader("getMenuHeaderSideItems", service);

            if (menuData) {
                buildLinkmenuSlideAdmin(menuData);
            }

            // ============= Responsive ==================
            const checkDeviceSize = () => {
                const width = window.innerWidth;
                if (width > 480) {
                    leftSlideClose();
                } else {
                    leftSlideCloseAdmin();
                }

                document.body.classList.remove("is-mobile", "is-tablet", "is-desktop");
                if (width <= 480) {
                    document.body.classList.add("is-mobile");
                } else if (width <= 768) {
                    document.body.classList.add("is-tablet");
                } else {
                    document.body.classList.add("is-desktop");
                }
            };

            // =============== Menu Slide Functions =======================
            const leftSlide = () => {
                document.getElementById("sidenav-store2")?.classList.add("open");
                document.getElementById("overlay-store2")?.classList.add("active");
                document.getElementById("menu-open-store2")?.classList.add("hidden");
                document.getElementById("menu-close-store2")?.classList.remove("hidden");
            };

            const leftSlideClose = () => {
                document.getElementById("sidenav-store2")?.classList.remove("open");
                document.getElementById("overlay-store2")?.classList.remove("active");
                document.getElementById("menu-open-store2")?.classList.remove("hidden");
                document.getElementById("menu-close-store2")?.classList.add("hidden");
            };

            const leftSlideAdmin = () => {
                document.getElementById("sidenav-store1")?.classList.add("open");
                document.getElementById("menu-open-store1")?.classList.add("hidden");
                document.getElementById("menu-close-store1")?.classList.remove("hidden");
            };

            const leftSlideCloseAdmin = () => {
                document.getElementById("sidenav-store1")?.classList.remove("open");
                document.getElementById("menu-open-store1")?.classList.remove("hidden");
                document.getElementById("menu-close-store1")?.classList.add("hidden");
            }

            // ============ Right menu =================
            document.getElementById("menu-open-store1")?.addEventListener("click", leftSlideAdmin);
            document.getElementById("menu1-open-store1")?.addEventListener("click", leftSlideAdmin);
            document.getElementById("menu-close-store1")?.addEventListener("click", leftSlideCloseAdmin);

            // ============ Left menu ===================
            document.getElementById("menu-open-store2")?.addEventListener("click", leftSlide);
            document.getElementById("menu-close-store2")?.addEventListener("click", leftSlideClose);
            document.getElementById("overlay-store2")?.addEventListener("click", leftSlideClose);

            checkDeviceSize();
            const handleResize = () => {
                checkDeviceSize();
            };

            document.addEventListener("click", (e) => {
                const menu = document.getElementById("sidenav-store1");
                const menuOpen = document.getElementById("menu-open-store1");

                // เช็คว่าคลิกอยู่นอกเมนู และไม่ใช่ปุ่มเปิดเมนู
                if (
                    menu && 
                    !menu.contains(e.target) && 
                    !menuOpen.contains(e.target)
                ) {
                    leftSlideCloseAdmin();
                }
            });

            window.addEventListener("resize", handleResize);

        })
        .catch((e) => {
            console.error("One or more module imports failed", e);
        });
</script>
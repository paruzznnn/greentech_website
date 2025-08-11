<?php include '../routes.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>E-STORE</title>
    <?php include '../inc-meta.php'; ?>
    <link href="../css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../css/template-product.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../inc-cdn.php'; ?>

</head>
<body>
    <?php include '../template/head-bar.php'; ?>
    <main>
        <div id="sections_root_products">
            <section id="sections_search_products" class="section-space-search">
                <div class="container">
                    <div class="row">
                        <div class="col-md-3">
                            <ul id="myMenu" class="search-box-menu"></ul>
                        </div>
                        <div class="col-md-9">
                            <div class="row" id="card-container"></div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
    <?php include '../template/footer-bar.php';?>
    <script type="module">
        import { initCardUI } from '../js/product/productRender.js?v=<?php echo time();?>';
        initCardUI({
        containerId: 'card-container',
        apiUrl: '../service/product/product-data.php?action=getProductItems',
        authToken: 'my_secure_token_123'
        });
    </script>
    <script>
        const menuData = [{
                label: "หมวดสินค้า",
                href: "#",
                icon: ''
            },
            {
                label: "วัสดุดูดซับเสียง",
                href: "#",
                icon: '',
                children: [{
                        label: "Trandar Acoustics Mineral Fiber",
                        href: "#",
                        icon: ""
                    },
                    {
                        label: "Trandar Acoustics Soft Fiber",
                        href: "#",
                        icon: ""
                    },
                    {
                        label: "Trandar ZIVANA",
                        href: "#",
                        icon: ""
                    },
                    {
                        label: "Trandar Seamless Acoustics",
                        href: "#",
                        icon: ""
                    },
                    {
                        label: "Trandar Solo",
                        href: "#",
                        icon: ""
                    },
                    {
                        label: "Trandar AFIBUS",
                        href: "#",
                        icon: ""
                    }
                ]
            },
            {
                label: "วัสดุกันเสียง",
                href: "#",
                icon: '',
                children: [{
                        label: "ตัวอย่าง A",
                        href: "#",
                        icon: ""
                    },
                    {
                        label: "ตัวอย่าง B",
                        href: "#",
                        icon: ""
                    }
                ]
            },
            {
                label: "โครงคร่าวฝ้า",
                href: "#",
                icon: '',
                children: [{
                        label: "อื่นๆ 1",
                        href: "#",
                        icon: ""
                    },
                    {
                        label: "อื่นๆ 2",
                        href: "#",
                        icon: ""
                    }
                ]
            },
            {
                label: "Trandar Solution",
                href: "#",
                icon: '',
                children: [{
                        label: "อื่นๆ 1",
                        href: "#",
                        icon: ""
                    },
                    {
                        label: "อื่นๆ 2",
                        href: "#",
                        icon: ""
                    }
                ]
            },
            {
                label: "อื่นๆ",
                href: "#",
                icon: '',
                children: [{
                        label: "อื่นๆ 1",
                        href: "#",
                        icon: ""
                    },
                    {
                        label: "อื่นๆ 2",
                        href: "#",
                        icon: ""
                    }
                ]
            }
        ];
        function createMenu(parentSelector, menuItems) {
            let html = '';

            for (const item of menuItems) {

                let iconHtml = '';
                if (item.icon && item.icon.trim() !== '') {
                    iconHtml = '<span class="icon">' + item.icon + '</span> ';
                }

                let hasChildren = false;
                if (item.children && item.children.length > 0) {
                    hasChildren = true;
                }

                let className = 'menu-item';
                if (hasChildren) {
                    className += ' has-children';
                }

                html += '<li class="' + className + '">';

                if (item.label && item.label.trim() !== '') {
                    html += '<a href="' + (item.href ? item.href : '#') + '" class="menu-link">';
                    html += item.label + ' ' + iconHtml;
                    html += '</a>';
                } else {
                    if (iconHtml !== '') {
                        html += '<span class="menu-link">' + iconHtml + '</span>';
                    }
                }

                if (hasChildren) {
                    html += '<ul class="submenu">';
                    for (const child of item.children) {
                        let childIconHtml = '';
                        if (child.icon && child.icon.trim() !== '') {
                            childIconHtml = '<span class="icon">' + child.icon + '</span> ';
                        }
                        html += '<li>';
                        html += '<a href="' + (child.href ? child.href : '#') + '">' + childIconHtml + child.label + '</a>';
                        html += '</li>';
                    }
                    html += '</ul>';
                }

                html += '</li>';
            }

            document.querySelector(parentSelector).innerHTML = html;

            // เพิ่ม event สำหรับเปิดปิด submenu
            // const menuItemsWithChildren = document.querySelectorAll(parentSelector + ' .has-children > .menu-link');
            // menuItemsWithChildren.forEach(link => {
            //     link.addEventListener('click', function(e) {
            //         e.preventDefault();
            //         const submenu = this.nextElementSibling;
            //         if (submenu) {
            //             if (submenu.classList.contains('open')) {
            //                 submenu.classList.remove('open');
            //             } else {
            //                 submenu.classList.add('open');
            //             }
            //         }
            //     });
            // });
        }
        createMenu('#myMenu', menuData);
    </script>

</body>

</html>
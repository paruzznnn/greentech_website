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
        <div id="sections_root_partner">
            <section id="sections_partner_cover_photo" class="section-space">
                <div class="container">
                    <div class="partner-banner">
                        <img class="partner-background" src="https://www.trandar.com//public/img/688b3c1b70d9b.jpg" alt="" />
                        <div class="partner-banner-overlay">
                            <div class="partner-logo-wrapper">
                                <img class="partner-logo" src="https://www.trandar.com//public/img/logo_688c431f30bf3.png" alt="" />
                            </div>
                            <div class="partner-banner-text">
                                <h1>Trandar Acoustics</h1>
                                <p>BRAND</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section id="sections_partner_layout" class="section-space-partner">
                <div class="container">
                    <div class="partner-tab-wrapper">
                        <div class="partner-left-menu">
                            <div id="dynamic-left-menu"></div>
                        </div>
                        <div class="partner-right-content">
                            <div class="partner-tab-buttons" id="tab-buttons"></div>
                            <div id="tab-contents"></div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
    <?php include '../template/footer-bar.php'; ?>

    <script type="module">
        import(`${pathConfig.BASE_WEB}js/partner/partnerRender.js?v=<?= time() ?>`)
        .then(async ({ 
            initTabs, 
            fetchPartnerData
        }) => {

            const service = pathConfig.BASE_WEB + 'service/partner/partner-data.php?';
            const tabs = await fetchPartnerData("getPartnerTabItems", service);
            const menuData = await fetchPartnerData("getPartnerMenuItems", service);
            const menuItems = await fetchPartnerData("getPartnerMenuArticleItems", service);

            const tabButtonsContainer = document.getElementById('tab-buttons');
            const tabContentsContainer = document.getElementById('tab-contents');
            const dynamicMenu = document.getElementById('dynamic-left-menu');

            initTabs({
                tabs,
                menuData,
                tabButtonsContainer,
                tabContentsContainer,
                dynamicMenu,
                parentSelector: '#articleMenu', 
                menuItems
            });
 
        })
        .catch((e) => console.error("Module import failed", e));
    </script>

    <!-- <script>
        export function initTabs({
            tabs,
            menuData,
            tabButtonsContainer,
            tabContentsContainer,
            dynamicMenu,
            parentSelector,
            menuItems
        }) {
            function createTabButtons() {
                tabs.forEach((tab, index) => {
                    const button = document.createElement('button');
                    button.className = 'partner-tab-button';
                    if (index === 0) button.classList.add('active');
                    button.id = `${tab.id}-button`;
                    button.textContent = tab.label;
                    button.addEventListener('click', (e) => openTab(e, tab.id));
                    tabButtonsContainer.appendChild(button);
                });
            }

            function createTabContents() {
                tabs.forEach((tab, index) => {
                    const contentDiv = document.createElement('div');
                    contentDiv.className = 'partner-tab-content';
                    if (index === 0) contentDiv.classList.add('active');
                    contentDiv.id = tab.id;
                    contentDiv.innerHTML = tab.content || '';
                    tabContentsContainer.appendChild(contentDiv);
                });
            }

            function openTab(evt, tabId) {
                tabContentsContainer.querySelectorAll('.partner-tab-content').forEach(content =>
                    content.classList.remove('active')
                );

                tabButtonsContainer.querySelectorAll('.partner-tab-button').forEach(btn =>
                    btn.classList.remove('active')
                );

                const contentToShow = tabContentsContainer.querySelector(`#${tabId}`);
                const buttonToActivate = tabButtonsContainer.querySelector(`#${tabId}-button`);

                if (contentToShow) contentToShow.classList.add('active');
                if (buttonToActivate) buttonToActivate.classList.add('active');

                updateMenu(tabId);
            }

            function updateMenu(tabId) {
                const menuItem = menuData.find(item => item.id === tabId);
                if (menuItem) {
                    dynamicMenu.innerHTML = menuItem.content || '';

                    // หลัง menu render แล้ว ค่อย build menu-specific component
                    requestAnimationFrame(() => {
                        buildTab1HtmlMenu(); 
                        buildTab2HtmlMenu();
                    });
                }
            }

            // Build ส่วนย่อยใน tab1 เท่านั้น
            function buildTab1HtmlMenu() {
                const el = document.getElementById('tab1-contact');
                if (el) {
                    el.innerHTML = `
                        <div></div>
                    `;
                }
            }

            function buildTab1HtmlContents(){

                const tabAbout = document.getElementById('tab1-about');
                if (tabAbout) {
                    tabAbout.innerHTML = `
                        <h4>เกี่ยวกับ</h4>
                        <p>
                        บริษัท แทรนดาร์ อินเตอร์เนชั่นแนล จำกัดได้ก่อตั้งขึ้นเมื่อวันที่ 1 มีนาคม 2531 
                        เราเป็นผู้เชี่ยวชาญด้านระบบฝ้าดูดซับเสียง ผนังกั้นเสียงและฝ้าอะคูสติกทุกชนิด 
                        เรามีทีมงานและผู้เชี่ยวชาญที่พร้อมให้คำปรึกษาในการออกแบบและติดตั้ง 
                        พร้อมทั้งผลิตและจำหน่ายสินค้าอะคูสติก แผ่นฝ้าดูดซับเสียง ผนังดูดซับเสียง โซลูชั่นผนังกันเสียง ฝ้ากันเสียง  
                        ที่ได้มาตรฐานจากทั้งในและต่างประเทศ รวมถึงการให้บริการที่มีประสิทธิภาพจากแทรนดาร์ อะคูสติก
                        </p>
                    `;
                }

                const tabReview = document.getElementById('tab1-review');
                if (tabReview) {
                    tabReview.innerHTML = `
                        <h4>คะแนนรีวิว : 0 (ยังไม่มีรีวิว)</h4>
                        <div>ให้คะแนนโปรไฟล์เพจนี้</div>
                        <div>
                            <i class="bi bi-star"></i>
                            <i class="bi bi-star"></i>
                            <i class="bi bi-star"></i>
                            <i class="bi bi-star"></i>
                            <i class="bi bi-star"></i>
                        </div>
                        <div>แบ่งปันประสบการณ์ผ่านรีวิวของคุณ...</div>
                        <a href="#" class="add-review">เขียนรีวิว</a>
                    `;
                }

                const tabArticle = document.getElementById('tab1-article');
                if (tabArticle) {
                    const articles = [
                        {
                            image: "https://www.trandar.com//public/news_img/Template%20for%20facebook-%E0%B8%9D%E0%B9%89%E0%B8%B2%E0%B8%96%E0%B8%A5%E0%B9%88%E0%B8%A1.png",
                            title: "การเดินทางสู่ความยั่งยืน: บทบาทของเทคโนโลยีสีเขียว",
                            content: "ในยุคที่โลกกำลังเผชิญกับความท้าทายด้านสิ่งแวดล้อม..."
                        },
                        {
                            image: "https://www.trandar.com/public/news_img/1751014970229.jpg",
                            title: "อนาคตของการทำงาน: การปรับตัวในยุคดิจิทัล",
                            content: "การเปลี่ยนแปลงทางดิจิทัลได้พลิกโฉมวิธีการทำงานของเรา..."
                        },
                        {
                            image: "https://www.trandar.com/public/news_img/1751897702840.jpg",
                            title: "ศิลปะแห่งการทำอาหาร: เคล็ดลับจากเชฟมืออาชีพ",
                            content: "การทำอาหารไม่ใช่แค่การเตรียมอาหาร แต่เป็นศิลปะ..."
                        }
                    ];

                    let htmlArticle = `<h4>บทความใหม่</h4><div class="articles-wrapper">`;
                    articles.forEach(article => {
                        htmlArticle += `
                            <div class="article-card">
                                <img src="${article.image}" alt="${article.title}" class="article-img" />
                                <div class="article-content">
                                    <h3 class="article-title">${article.title}</h3>
                                    <p>${article.content}</p>
                                </div>
                            </div>
                        `;
                    });
                    htmlArticle += '</div>';
                    tabArticle.innerHTML = htmlArticle;
                }

            }

            function buildTab2HtmlMenu() {
                const el = document.getElementById('tab2-article-menu');
                if (el) {

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

                }
            }

            function buildTab2HtmlContents(){
                const tabArticle = document.getElementById('tab2-article');
                if (tabArticle) {
                    const articles = [
                        {
                            image: "https://www.trandar.com//public/news_img/Template%20for%20facebook-%E0%B8%9D%E0%B9%89%E0%B8%B2%E0%B8%96%E0%B8%A5%E0%B9%88%E0%B8%A1.png",
                            title: "การเดินทางสู่ความยั่งยืน: บทบาทของเทคโนโลยีสีเขียว",
                            content: "ในยุคที่โลกกำลังเผชิญกับความท้าทายด้านสิ่งแวดล้อม..."
                        },
                        {
                            image: "https://www.trandar.com/public/news_img/1751014970229.jpg",
                            title: "อนาคตของการทำงาน: การปรับตัวในยุคดิจิทัล",
                            content: "การเปลี่ยนแปลงทางดิจิทัลได้พลิกโฉมวิธีการทำงานของเรา..."
                        },
                        {
                            image: "https://www.trandar.com/public/news_img/1751897702840.jpg",
                            title: "ศิลปะแห่งการทำอาหาร: เคล็ดลับจากเชฟมืออาชีพ",
                            content: "การทำอาหารไม่ใช่แค่การเตรียมอาหาร แต่เป็นศิลปะ..."
                        }
                    ];

                    let htmlArticle = `<h4>บทความ</h4><div class="articles-wrapper">`;
                    articles.forEach(article => {
                        htmlArticle += `
                            <div class="article-card">
                                <img src="${article.image}" alt="${article.title}" class="article-img" />
                                <div class="article-content">
                                    <h3 class="article-title">${article.title}</h3>
                                    <p>${article.content}</p>
                                </div>
                            </div>
                        `;
                    });
                    htmlArticle += '</div>';
                    tabArticle.innerHTML = htmlArticle;
                }
            }

            // เริ่มการทำงาน
            createTabButtons();
            createTabContents();

            // รอให้ DOM ใน tab render เสร็จ แล้วค่อย build element ย่อยภายใน
            requestAnimationFrame(() => {
                buildTab1HtmlContents();
                buildTab2HtmlContents();
            });

            // เปิด tab แรก
            const firstButton = tabButtonsContainer.querySelector('.partner-tab-button');
            if (firstButton) firstButton.click();
        }
    </script> -->

</body>

</html>
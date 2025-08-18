<?php
// ตรวจสอบภาษาจาก URL ถ้ามี `?lang=en` หรือ `?lang=cn` จะกำหนดตามค่าที่ได้รับ
// ถ้าไม่มี หรือเป็นค่าอื่น จะกำหนดให้เป็นภาษาไทย
$lang = 'th'; // กำหนดค่าเริ่มต้นเป็นภาษาไทย
if (isset($_GET['lang'])) {
    if ($_GET['lang'] === 'en') {
        $lang = 'en';
    } elseif ($_GET['lang'] === 'cn') {
        $lang = 'cn';
    }
}

// กำหนดชื่อคอลัมน์ที่ต้องการดึงข้อมูลตามภาษาที่เลือก
$subjectColumn = 'subject_news';
if ($lang === 'en') {
    $subjectColumn = 'subject_news_en';
} elseif ($lang === 'cn') {
    $subjectColumn = 'subject_news_cn';
}

// ดึงข่าว 3 รายการล่าสุดจาก dn_news
// require_once('../lib/connect.php'); // ต้องแน่ใจว่าไฟล์นี้มีการเรียกใช้และเชื่อมต่อฐานข้อมูลถูกต้อง
$newsList = [];

// สร้างคำสั่ง SQL โดยใช้ตัวแปร $subjectColumn ที่เรากำหนดไว้
$sql = "SELECT news_id, {$subjectColumn} FROM dn_news ORDER BY date_create DESC LIMIT 3";

// ตรวจสอบว่า $conn ถูกกำหนดและมีการเชื่อมต่อก่อนเรียกใช้
if (isset($conn)) {
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // ดึงข้อมูลมาเก็บใน $newsList
            $newsList[] = $row;
        }
    }
}

$isProtocol = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
$isFile = ($isProtocol === 'http') ? '.php' : '';

// ข้อมูลเมนูสำหรับ Navbar ทั้งสามภาษา
$navbarItems = [
    'th' => [
        ['icon' => '', 'text' => 'หน้าแรก', 'translate' => 'Home', 'link' => 'index' . $isFile],
        ['icon' => '', 'text' => 'เกี่ยวกับเรา', 'translate' => 'About_us', 'link' => 'about' . $isFile],
        ['icon' => '', 'text' => 'บริการ', 'translate' => 'Service_t', 'link' => 'service' . $isFile],
        ['icon' => '', 'text' => 'สินค้า', 'translate' => 'product', 'link' => 'shop' . $isFile],
        ['icon' => '', 'text' => 'insul', 'translate' => 'insul', 'link' => '#', 'isDropdown' => true, 'id' => 'dropdown3'],
        ['icon' => '', 'text' => 'ผลงาน', 'translate' => 'project', 'link' => 'project' . $isFile],
        ['icon' => '', 'text' => 'บทความ', 'translate' => 'blog', 'link' => '#', 'isDropdown' => true, 'id' => 'dropdown4'],
        ['icon' => '', 'text' => 'ข่าว', 'translate' => 'News', 'link' => 'news' . $isFile],
        ['icon' => '', 'text' => 'ติดต่อเรา', 'translate' => 'Contact_us', 'link' => 'contact' . $isFile],
    ],
    'en' => [
        ['icon' => '', 'text' => 'Home', 'translate' => 'Home', 'link' => 'index' . $isFile],
        ['icon' => '', 'text' => 'About us', 'translate' => 'About_us', 'link' => 'about' . $isFile],
        ['icon' => '', 'text' => 'Service', 'translate' => 'Service_t', 'link' => 'service' . $isFile],
        ['icon' => '', 'text' => 'Product', 'translate' => 'product', 'link' => 'shop' . $isFile],
        ['icon' => '', 'text' => 'insul', 'translate' => 'insul', 'link' => '#', 'isDropdown' => true, 'id' => 'dropdown3'],
        ['icon' => '', 'text' => 'Projects', 'translate' => 'project', 'link' => 'project' . $isFile],
        ['icon' => '', 'text' => 'Articles', 'translate' => 'blog', 'link' => '#', 'isDropdown' => true, 'id' => 'dropdown4'],
        ['icon' => '', 'text' => 'News', 'translate' => 'News', 'link' => 'news' . $isFile],
        ['icon' => '', 'text' => 'Contact us', 'translate' => 'Contact_us', 'link' => 'contact' . $isFile],
    ],
    'cn' => [
        ['icon' => '', 'text' => '主页', 'translate' => 'Home', 'link' => 'index' . $isFile],
        ['icon' => '', 'text' => '关于我们', 'translate' => 'About_us', 'link' => 'about' . $isFile],
        ['icon' => '', 'text' => '服务', 'translate' => 'Service_t', 'link' => 'service' . $isFile],
        ['icon' => '', 'text' => '产品', 'translate' => 'product', 'link' => 'shop' . $isFile],
        ['icon' => '', 'text' => 'insul', 'translate' => 'insul', 'link' => '#', 'isDropdown' => true, 'id' => 'dropdown3'],
        ['icon' => '', 'text' => '项目', 'translate' => 'project', 'link' => 'project' . $isFile],
        ['icon' => '', 'text' => '文章', 'translate' => 'blog', 'link' => '#', 'isDropdown' => true, 'id' => 'dropdown4'],
        ['icon' => '', 'text' => '新闻', 'translate' => 'News', 'link' => 'news' . $isFile],
        ['icon' => '', 'text' => '联系我们', 'translate' => 'Contact_us', 'link' => 'contact' . $isFile],
    ],
];

// ข้อมูลเมนูย่อย (Dropdown) สำหรับ Navbar ทั้งสามภาษา
$dropdownItems = [
    'dropdown3' => [
        'th' => [
            ['icon' => '', 'text' => 'INSUL Software', 'translate' => 'INSUL Software', 'link' => 'INSULSoftware' . $isFile],
            ['icon' => '', 'text' => 'Download', 'translate' => 'Download', 'link' => 'Download' . $isFile],
            ['icon' => '', 'text' => 'Instructions', 'translate' => 'Instructions', 'link' => 'Instructions' . $isFile],
        ],
        'en' => [
            ['icon' => '', 'text' => 'INSUL Software', 'translate' => 'INSUL Software', 'link' => 'INSULSoftware' . $isFile],
            ['icon' => '', 'text' => 'Download', 'translate' => 'Download', 'link' => 'Download' . $isFile],
            ['icon' => '', 'text' => 'Instructions', 'translate' => 'Instructions', 'link' => 'Instructions' . $isFile],
        ],
        'cn' => [
            ['icon' => '', 'text' => 'INSUL 软件', 'translate' => 'INSUL Software', 'link' => 'INSULSoftware' . $isFile],
            ['icon' => '', 'text' => '下载', 'translate' => 'Download', 'link' => 'Download' . $isFile],
            ['icon' => '', 'text' => '使用说明', 'translate' => 'Instructions', 'link' => 'Instructions' . $isFile],
        ],
    ],
    'dropdown4' => [
        'th' => [
            ['icon' => '', 'text' => 'บทความ', 'translate' => 'blog', 'link' => 'Blog' . $isFile],
            ['icon' => '', 'text' => 'ความรู้ด้านเสียง', 'translate' => 'Design&Idia', 'link' => 'idia' . $isFile],
            ['icon' => '', 'text' => 'วีดีโอ', 'translate' => 'video', 'link' => 'Video' . $isFile],
        ],
        'en' => [
            ['icon' => '', 'text' => 'Articles', 'translate' => 'blog', 'link' => 'Blog' . $isFile],
            ['icon' => '', 'text' => 'Acoustics Knowledge', 'translate' => 'Design&Idia', 'link' => 'idia' . $isFile],
            ['icon' => '', 'text' => 'Video', 'translate' => 'video', 'link' => 'Video' . $isFile],
        ],
        'cn' => [
            ['icon' => '', 'text' => '文章', 'translate' => 'blog', 'link' => 'Blog' . $isFile],
            ['icon' => '', 'text' => '声学知识', 'translate' => 'Design&Idia', 'link' => 'idia' . $isFile],
            ['icon' => '', 'text' => '视频', 'translate' => 'video', 'link' => 'Video' . $isFile],
        ],
    ],
];
?>

<style>
/* CSS styles from the original code */
/* ------------------------------------------------------------- */
/* CSS สำหรับ Desktop Menu โดยเฉพาะ (ใหม่) */
/* ------------------------------------------------------------- */
.navbar-desktop {
    background-color: #ff9900;
    position: relative;
    z-index: 999;
    padding: 6px 0;
}

.desktop-menu-container {
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: visible;
    white-space: nowrap;
    gap: 35px;
}

.desktop-menu-item {
    position: relative;
    display: inline-block;
    color: #ffffff;
    text-decoration: none;
    padding: 10px 15px;
    font-size: 20px;
    transition: background-color 0.3s;
}

.desktop-menu-item:hover {
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: 4px;
}

.desktop-dropdown-content {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background-color: #fff;
    box-shadow: 0px 8px 16px rgba(0,0,0,0.1);
    z-index: 1000;
    min-width: 180px;
    border-radius: 4px;
    style="text-decoration: none;"
}

.desktop-menu-item:hover .desktop-dropdown-content {
    display: block;
}

.desktop-dropdown-content a {
    color: #565656;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
    text-align: left;
    white-space: normal;
}

.desktop-dropdown-content a:hover {
    background-color: #f1f1f1;
}

/* ------------------------------------------------------------- */
/* CSS สำหรับ Mobile Menu โดยเฉพาะ (ใหม่) */
/* ------------------------------------------------------------- */
.navbar-mobile-container {
    display: none;
    position: relative;
    background-color: #ff9900;
}

.mobile-header {
    display: flex;
    align-items: center;
    padding: 15px;
}

.hamburger {
    font-size: 28px;
    cursor: pointer;
    color: #fff;
}

.mobile-slide-out-menu {
    background-color: #ff9900;
    position: fixed;
    top: 0;
    left: 0;
    width: 250px;
    height: 100%;
    overflow-y: auto;
    z-index: 9999;
    transform: translateX(-100%);
    transition: transform 0.3s ease-in-out;
    padding-top: 60px;
}

.mobile-slide-out-menu.open {
    transform: translateX(0);
}

.mobile-slide-out-menu a {
    color: #fff;
    text-decoration: none;
    padding: 15px 25px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    font-size: 18px;
    display: block;
}
.mobile-slide-out-menu a:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.mobile-slide-out-menu .dropdown-toggle {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.mobile-slide-out-menu .dropdown-content {
    display: none;
    background-color: rgba(255, 255, 255, 0.1);
    padding-left: 20px;
}

.mobile-slide-out-menu .dropdown-content.show {
    display: block;
}

.mobile-slide-out-menu .dropdown-content a {
    padding: 10px 25px;
    font-size: 16px;
    background-color: rgba(255, 255, 255, 0.1);
    border-bottom: none;
}

.close-btn {
    position: absolute;
    top: 15px;
    right: 25px;
    font-size: 36px;
    color: #fff;
    cursor: pointer;
}

/* ------------------------------------------------------------- */
/* Media Query เพื่อสลับการแสดงผลเมนู */
/* ------------------------------------------------------------- */
@media (max-width: 1024px) {
    .navbar-desktop {
        display: none;
    }
    .navbar-mobile-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1em;
    }
}
@media (min-width: 1025px) {
    .navbar-mobile-container {
        display: none;
    }
}


/* ------------------------------------------------------------- */
/* CSS ของ News Ticker (ไม่แตะต้อง) */
/* ------------------------------------------------------------- */
#navbar-news {
    position: relative;
    z-index: 998;
}
.news-ticker {
    position: relative;
    background-color: #ffffffff;
    color: #555;
    font-weight: bold;
    z-index: 998;
    white-space: nowrap;
    font-size: 24px;
    border-radius: 0px 70px 10px 0px;
}

@media (max-width: 768px) {
    .text-ticker {
        font-size: 18px;
        padding: 10px 20px 15px 5px;
    }
}

@media (max-width: 480px) {
    .text-ticker {
        font-size: 14px;
        padding: 8px 15px 10px 5px;
    }
}
a {
    color: #ffffff;
}
</style>

<div class="navbar-desktop">
    <div class="container">
        <div class="desktop-menu-container">
            <?php foreach ($navbarItems[$lang] as $item): ?>
                <?php if (isset($item['isDropdown']) && $item['isDropdown']): ?>
                    <div class="desktop-menu-item" style="text-decoration: none;">
                        <a href ="<?php echo $item['link']; ?>" style="text-decoration: none;">
                            <span data-translate="<?php echo $item['translate']; ?>" lang="th" style="text-decoration: none;">
                                <?php echo $item['text']; ?>
                            </span>
                            <span class="dropdown-icon" style="text-decoration: none;">
                                <i class="fas fa-caret-down"></i>
                            </span>
                        </a>
                        <div class="desktop-dropdown-content">
                            <?php foreach ($dropdownItems[$item['id']][$lang] as $dropdownItem): ?>
                                <a href="<?php echo $dropdownItem['link']; ?>">
                                    <i class="<?php echo $dropdownItem['icon']; ?>"></i>
                                    <span data-translate="<?php echo $dropdownItem['translate']; ?>" lang="th">
                                        <?php echo $dropdownItem['text']; ?>
                                    </span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?php echo $item['link']; ?>" class="desktop-menu-item">
                        <i class="<?php echo $item['icon']; ?>"></i>
                        <span data-translate="<?php echo $item['translate']; ?>" lang="th">
                            <?php echo $item['text']; ?>
                        </span>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
    <div class="navbar-mobile-container" style="padding:15px;">
        
    </div>

<script>
// JavaScript สำหรับ Mobile Menu
const mobileMenu = document.getElementById("mobileMenu");
const hamburger = document.querySelector(".hamburger");

// ฟังก์ชันสำหรับสลับสถานะเมนู (เปิด/ปิด)
function toggleMobileNav() {
    mobileMenu.classList.toggle("open");
}

function toggleMobileDropdown(id) {
    const dropdown = document.getElementById(id + "_mobile");
    if (dropdown) {
        dropdown.classList.toggle("show");
    }
}

document.addEventListener('click', function(event) {
    const isClickInsideMenu = mobileMenu.contains(event.target);
    const isClickOnHamburger = hamburger.contains(event.target);
    const closeBtn = document.querySelector(".close-btn");

    if (mobileMenu.classList.contains("open") && !isClickInsideMenu && !isClickOnHamburger && !closeBtn.contains(event.target)) {
        toggleMobileNav();
    }
});
</script>

<div id="navbar-news">
    <div style="margin-left:10%;">
        <div class="news-ticker">
            <span class="text-ticker">
                <span class="blinking-icon"></span>
                <?php
                // แสดงข้อความ "ข่าวประจำวัน" ตามภาษาที่เลือก
                $newsText = [
                    'th' => 'ข่าวประจำวัน',
                    'en' => 'Daily News',
                    'cn' => '每日新闻'
                ];
                echo $newsText[$lang] ?? 'ข่าวประจำวัน'; // ใช้ ?? เพื่อป้องกัน error ถ้า $lang ไม่มีค่า
                ?>
            </span>
            <marquee id="newsMarquee" scrollamount="4" behavior="scroll" direction="left" onmouseover="this.stop();" onmouseout="this.start();">
                <div id="newsMarquee-link" style="display: inline;">
                    <?php foreach ($newsList as $news): ?>
                        <span style="padding: 0 50px;">
                            <a href="news.php?id=<?= $news['news_id'] ?>&lang=<?= $lang ?>" style="text-decoration: none; color: inherit;">
                                <?= htmlspecialchars($news[$subjectColumn]) ?>
                            </a>
                        </span>
                    <?php endforeach; ?>
                </div>
            </marquee>
        </div>
    </div>
</div>
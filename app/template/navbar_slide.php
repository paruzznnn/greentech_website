<?php
// ดึงข่าว 3 รายการล่าสุดจาก dn_news
// require_once('../lib/connect.php');
// ตรวจสอบว่า $conn ถูกกำหนดแล้วหรือไม่ก่อนเรียกใช้
if (!isset($conn)) {
    // หากไม่มีการเชื่อมต่อ ให้สร้างขึ้นมาใหม่หรือแจ้งเตือน
    // require_once('../lib/connect.php');
    // หรือใส่โค้ดสำหรับสร้างการเชื่อมต่อฐานข้อมูลตรงนี้
}

$newsList = [];
$sql = "SELECT news_id, subject_news FROM dn_news ORDER BY date_create DESC LIMIT 3";
// ตรวจสอบว่า $conn ถูกกำหนดและมีการเชื่อมต่อก่อนเรียกใช้
if (isset($conn)) {
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $newsList[] = $row;
        }
    }
}
?>

<?php
$isProtocol = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
$isFile = ($isProtocol === 'http') ? '.php' : '';

$navbarItems = [
    ['icon' => '', 'text' => 'หน้าแรก', 'translate' => 'Home', 'link' => 'index' . $isFile],
    ['icon' => '', 'text' => 'เกี่ยวกับเรา', 'translate' => 'About_us', 'link' => 'about' . $isFile],
    ['icon' => '', 'text' => 'บริการ', 'translate' => 'Service_t', 'link' => 'service' . $isFile],
    ['icon' => '', 'text' => 'สินค้า', 'translate' => 'product', 'link' => 'shop' . $isFile],
    ['icon' => '', 'text' => 'insul', 'translate' => 'insul', 'link' => '#', 'isDropdown' => true, 'id' => 'dropdown3'],
    ['icon' => '', 'text' => 'ผลงาน', 'translate' => 'project', 'link' => 'project' . $isFile],
    ['icon' => '', 'text' => 'บทความ', 'translate' => 'blog', 'link' => '#', 'isDropdown' => true, 'id' => 'dropdown4'],
    ['icon' => '', 'text' => 'ข่าว', 'translate' => 'News', 'link' => 'news' . $isFile],
    ['icon' => '', 'text' => 'ติดต่อเรา', 'translate' => 'Contact_us', 'link' => 'contact' . $isFile],
];

$dropdownItems = [
    'dropdown3' => [
        ['icon' => '', 'text' => 'INSUL Software', 'translate' => 'INSUL Software', 'link' => 'INSULSoftware' . $isFile],
        ['icon' => '', 'text' => 'Download', 'translate' => 'Download', 'link' => 'Download' . $isFile],
        ['icon' => '', 'text' => 'Instructions', 'translate' => 'Instructions', 'link' => 'Instructions' . $isFile],
    ],
    'dropdown4' => [
        ['icon' => '', 'text' => 'บทความ', 'translate' => 'blog', 'link' => 'Blog' . $isFile],
        ['icon' => '', 'text' => 'ความรู้ด้านเสียง', 'translate' => 'Acoustic knowledge', 'link' => 'idia' . $isFile],
        ['icon' => '', 'text' => 'วีดีโอ', 'translate' => 'video', 'link' => 'Video' . $isFile],
    ],
];
?>

<style>
/* ------------------------------------------------------------- */
/* CSS สำหรับ Desktop Menu (ขนาดจอ > 1024px) */
/* ------------------------------------------------------------- */
.navbar-menu-desktop {
    background-color: #ff9900;
    position: relative;
    z-index: 999;
    padding: 6px 0;
    display: block; /* แสดงผลบน Desktop */
}

.over-menu-desktop {
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
    white-space: nowrap; /* สำคัญ: ป้องกันการขึ้นบรรทัดใหม่ */
    gap: 15px; /* เว้นระยะห่างระหว่างเมนู */
}

.over-menu-desktop a,
.over-menu-desktop .dropdown-desktop {
    color: #ffffff;
    text-decoration: none;
    padding: 10px 15px;
    font-size: 16px;
    transition: background-color 0.3s;
}

.over-menu-desktop a:hover,
.over-menu-desktop .dropdown-desktop:hover {
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: 4px;
}

.dropdown-desktop {
    position: relative;
    display: inline-block;
}

.dropdown-desktop-content {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background-color: #fff;
    box-shadow: 0px 8px 16px rgba(0,0,0,0.1);
    z-index: 10000;
    min-width: 180px;
    border-radius: 4px;
}

.dropdown-desktop:hover .dropdown-desktop-content {
    display: block;
}

.dropdown-desktop-content a {
    color: #565656;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
    text-align: left;
    white-space: normal; /* อนุญาตให้ข้อความใน dropdown ขึ้นบรรทัดใหม่ */
}

.dropdown-desktop-content a:hover {
    background-color: #f1f1f1;
}

/* ------------------------------------------------------------- */
/* CSS สำหรับ Mobile Menu (ขนาดจอ <= 1024px) */
/* ------------------------------------------------------------- */
.navbar-menu-mobile {
    display: none; /* ซ่อนไว้บน Desktop */
    background-color: #ff9900;
}

.mobile-header {
    display: flex;
    justify-content: flex-end;
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
    right: 0; /* เปลี่ยนจาก left เป็น right */
    width: 250px; /* กำหนดความกว้างของเมนู */
    height: 100%;
    overflow-y: auto;
    z-index: 9999;
    transform: translateX(100%); /* ซ่อนเมนูไปทางขวา */
    transition: transform 0.3s ease-in-out;
    padding-top: 60px; /* เว้นพื้นที่ด้านบนสำหรับปุ่มปิด */
}

.mobile-slide-out-menu.open {
    transform: translateX(0); /* เลื่อนเมนูเข้ามา */
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

.close-btn {
    position: absolute;
    top: 15px;
    right: 25px;
    font-size: 36px;
    color: #fff;
    cursor: pointer;
}

/* ------------------------------------------------------------- */
/* Media Query เพื่อสลับเมนู */
/* ------------------------------------------------------------- */
@media (max-width: 1024px) {
    .navbar-menu-desktop {
        display: none;
    }
    .navbar-menu-mobile {
        display: block;
    }
}

/* ------------------------------------------------------------- */
/* สไตล์อื่นๆ ตามโค้ดเดิมของคุณ */
/* ------------------------------------------------------------- */
.text-ticker {
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    background-color: #ff9900;
    color: #ffffff;
    padding: 15px 40px 20px 10px;
    font-weight: bold;
    z-index: 1;
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
</style>

<div class="navbar-menu-desktop">
    <div class="container">
        <div class="over-menu-desktop">
            <?php foreach ($navbarItems as $item): ?>
                <?php if (isset($item['isDropdown']) && $item['isDropdown']): ?>
                    <div class="dropdown-desktop">
                        <a href="<?php echo $item['link']; ?>" class="dropbtn">
                            <span data-translate="<?php echo $item['translate']; ?>" lang="th">
                                <?php echo $item['text']; ?>
                            </span>
                            <span class="dropdown-icon">
                                <i class="fas fa-caret-down"></i>
                            </span>
                        </a>
                        <div class="dropdown-desktop-content">
                            <?php foreach ($dropdownItems[$item['id']] as $dropdownItem): ?>
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
                    <a href="<?php echo $item['link']; ?>">
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

<div class="navbar-menu-mobile">
    <div class="mobile-header">
        <div class="hamburger" onclick="openMobileNav()">&#9776;</div>
    </div>

    <div class="mobile-slide-out-menu" id="mobileMenu">
        <a href="javascript:void(0)" class="close-btn" onclick="closeMobileNav()">&times;</a>
        <?php foreach ($navbarItems as $item): ?>
            <?php if (isset($item['isDropdown']) && $item['isDropdown']): ?>
                <div class="dropdown-mobile">
                    <a href="javascript:void(0)" class="dropdown-toggle" onclick="toggleMobileDropdown('<?php echo $item['id']; ?>')">
                        <span data-translate="<?php echo $item['translate']; ?>" lang="th">
                            <?php echo $item['text']; ?>
                        </span>
                        <span class="dropdown-icon" style="float:right;">&#9660;</span>
                    </a>
                    <div class="dropdown-content" id="<?php echo $item['id']; ?>">
                        <?php foreach ($dropdownItems[$item['id']] as $dropdownItem): ?>
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
                <a href="<?php echo $item['link']; ?>">
                    <i class="<?php echo $item['icon']; ?>"></i>
                    <span data-translate="<?php echo $item['translate']; ?>" lang="th">
                        <?php echo $item['text']; ?>
                    </span>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>


<script>
// JavaScript สำหรับ Desktop Dropdown (ยังคงใช้ได้อยู่)
let currentDropdown = null;

function toggleDropdown(id) {
    if (currentDropdown && currentDropdown !== id) {
        const prev = document.getElementById(currentDropdown);
        if (prev) prev.style.display = "none";
    }
    const dropdown = document.getElementById(id);
    if (dropdown) {
        dropdown.style.display = "block";
        currentDropdown = id;
    }
}

function closeAllDropdowns() {
    if (currentDropdown) {
        const dropdown = document.getElementById(currentDropdown);
        if (dropdown) dropdown.style.display = "none";
        currentDropdown = null;
    }
}

// JavaScript สำหรับ Mobile Slide-out Menu
const mobileMenu = document.getElementById("mobileMenu");
const hamburger = document.querySelector(".hamburger");

function openMobileNav() {
    mobileMenu.classList.add("open");
}

function closeMobileNav() {
    mobileMenu.classList.remove("open");
}

function toggleMobileDropdown(id) {
    const dropdown = document.getElementById(id);
    if (dropdown) {
        dropdown.classList.toggle("show");
    }
}

// ฟังก์ชันสำหรับปิดเมนูเมื่อคลิกนอกพื้นที่
document.addEventListener('click', function(event) {
    // ตรวจสอบว่าการคลิกไม่ได้เกิดขึ้นภายใน mobileMenu หรือบน hamburger button
    const isClickInsideMenu = mobileMenu.contains(event.target);
    const isClickOnHamburger = hamburger.contains(event.target);

    // ถ้าเมนูกำลังเปิดอยู่ และการคลิกเกิดขึ้นนอกเมนูและนอกปุ่ม hamburger
    if (mobileMenu.classList.contains("open") && !isClickInsideMenu && !isClickOnHamburger) {
        closeMobileNav();
    }
});
</script>

<div id="navbar-news">
    <div class="container">
        <div class="news-ticker">
            <span class="text-ticker">
                <span class="blinking-icon"></span>
                Daily News
            </span>
            <marquee id="newsMarquee" scrollamount="4" behavior="scroll" direction="left" onmouseover="this.stop();" onmouseout="this.start();">
                <div id="newsMarquee-link" style="display: inline;">
                    <?php foreach ($newsList as $news): ?>
                        <span style="padding: 0 50px;">
                            <a href="news.php?id=<?= $news['news_id'] ?>" style="text-decoration: none; color: inherit;">
                                <?= htmlspecialchars($news['subject_news']) ?>
                            </a>
                        </span>
                    <?php endforeach; ?>
                </div>
            </marquee>
        </div>
    </div>
</div>
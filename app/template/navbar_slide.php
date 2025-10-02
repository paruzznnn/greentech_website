<?php
// ต้องแน่ใจว่าได้เปิดใช้งาน Session ก่อนการแสดงผลใดๆ
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// =================================================================
// 1. ส่วนการจัดการภาษา (เหมือนเดิม)
// =================================================================
if (isset($_GET['lang'])) {
    $supportedLangs = ['en', 'cn', 'jp', 'kr'];
    $newLang = $_GET['lang'];
    if (in_array($newLang, $supportedLangs)) {
        $_SESSION['lang'] = $newLang;
    } else {
        unset($_SESSION['lang']);
    }
}
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'th';

$subjectColumn = 'subject_news';
if ($lang === 'en') { $subjectColumn = 'subject_news_en'; }
elseif ($lang === 'cn') { $subjectColumn = 'subject_news_cn'; }
elseif ($lang === 'jp') { $subjectColumn = 'subject_news_jp'; }
elseif ($lang === 'kr') { $subjectColumn = 'subject_news_kr'; }

$newsList = [];
// สมมติว่าไฟล์นี้ถูกเรียกใช้ และมีการเชื่อมต่อฐานข้อมูลในตัวแปร $conn
// require_once('../lib/connect.php'); 
$sql = "SELECT news_id, {$subjectColumn} FROM dn_news WHERE del = 0 ORDER BY date_create DESC LIMIT 3";

if (isset($conn)) {
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $newsList[] = $row;
        }
    }
}

$isProtocol = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
$isFile = ($isProtocol === 'http') ? '.php' : '';


// =================================================================
// 2. ส่วนดึงค่าการตั้งค่า (จำลองฟังก์ชันดึงจาก DB)
// =================================================================
/**
 * ฟังก์ชันจำลองการดึงค่าการตั้งค่าจากตาราง dn_settings
 * ในโค้ดจริง คุณควรเขียนโค้ดเพื่อดึงค่าจากฐานข้อมูลจริง
 *
 * @return array ค่าการตั้งค่า
 */
function getSettings($conn) {
    // 1. กำหนดค่าเริ่มต้น/ค่าสำรอง (Fallback)
    $settings = [
        'navbar_bg_color'       => '#ff9900', 
        'navbar_text_color'     => '#ffffff', 
        'news_ticker_display'   => '1',
        'news_ticker_bg_color'  => '#ffffffff',
        'news_ticker_text_color'=> '#555',
        'news_ticker_title_color'=> '#ff9900',
    ];

    // 2. ดึงค่าจริงจาก DB เพื่อทับค่าเริ่มต้น
    if ($conn) {
        // ใช้ prepared statement เพื่อเพิ่มความปลอดภัย
        $sql = "SELECT setting_key, setting_value FROM dn_settings 
                WHERE setting_key IN (?, ?, ?, ?, ?, ?)";
        
        $required_keys = array_keys($settings);
        
        // เช็คให้แน่ใจว่า Prepared Statement ทำงาน
        if ($stmt = $conn->prepare($sql)) {
             // Bind parameters: หกตัวแปรสตริง (s)
            $stmt->bind_param("ssssss", ...$required_keys);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // ทับค่าเริ่มต้นด้วยค่าที่ดึงมาจากฐานข้อมูล
                    $settings[$row['setting_key']] = $row['setting_value'];
                }
            }
            $stmt->close();
        }
    }
    
    return $settings;
}
// (ส่วนที่เรียกใช้ฟังก์ชันไม่ต้องเปลี่ยน)
// ดึงค่าการตั้งค่าทั้งหมดมาใช้
// ถ้าไม่มี $conn ให้ส่ง null เพื่อใช้ค่าเริ่มต้น
$settings = getSettings(isset($conn) ? $conn : null);

// ดึงค่าการตั้งค่าทั้งหมดมาใช้
// ถ้าไม่มี $conn ให้ส่ง null เพื่อใช้ค่าเริ่มต้น
$settings = getSettings(isset($conn) ? $conn : null);


// =================================================================
// 3. ปรับปรุงโครงสร้างเมนูเพื่อรองรับการเปิด/ปิด/จัดเรียง (ในอนาคต)
//    *** สำหรับการสลับตำแหน่งหรือปิดเมนู ให้จัดการ Array นี้ตามต้องการ ***
// =================================================================
$navbarItems = [
    'th' => [
        // เพิ่มคีย์ 'is_active' และ 'display_order' เพื่อให้รองรับการจัดการในอนาคต
        // 'display_order' จะใช้จัดเรียงเมนู
        ['icon' => '', 'text' => 'หน้าแรก', 'translate' => 'Home', 'link' => 'index' . $isFile, 'is_active' => true, 'order' => 1],
        ['icon' => '', 'text' => 'เกี่ยวกับเรา', 'translate' => 'About_us', 'link' => 'about' . $isFile, 'is_active' => true, 'order' => 2],
        ['icon' => '', 'text' => 'บริการ', 'translate' => 'Service_t', 'link' => 'service' . $isFile, 'is_active' => true, 'order' => 3],
        ['icon' => '', 'text' => 'สินค้า', 'translate' => 'product', 'link' => 'shop' . $isFile, 'is_active' => true, 'order' => 4],
        // ตัวอย่างการปิดเมนู: หากต้องการปิดเมนู 'insul' ที่เคยมีในโค้ดเดิม ให้ตั้ง 'is_active' เป็น false หรือลบคอมเมนต์ออก
        // ['icon' => '', 'text' => 'insul', 'translate' => 'insul', 'link' => '#', 'isDropdown' => true, 'id' => 'dropdown3', 'is_active' => false, 'order' => 5],
        ['icon' => '', 'text' => 'ผลงาน', 'translate' => 'project', 'link' => 'project' . $isFile, 'is_active' => true, 'order' => 6],
        ['icon' => '', 'text' => 'บทความ', 'translate' => 'blog', 'link' => '#', 'isDropdown' => true, 'id' => 'dropdown4', 'is_active' => true, 'order' => 7],
        ['icon' => '', 'text' => 'ข่าว', 'translate' => 'News', 'link' => 'news' . $isFile, 'is_active' => true, 'order' => 8],
        ['icon' => '', 'text' => 'ติดต่อเรา', 'translate' => 'Contact_us', 'link' => 'contact' . $isFile, 'is_active' => true, 'order' => 9],
    ],
    // ... (ส่วนของ 'en', 'cn', 'jp', 'kr' ให้ปรับโครงสร้าง Array เหมือน 'th')
    'en' => [
        ['icon' => '', 'text' => 'Home', 'translate' => 'Home', 'link' => 'index' . $isFile, 'is_active' => true, 'order' => 1],
        ['icon' => '', 'text' => 'About us', 'translate' => 'About_us', 'link' => 'about' . $isFile, 'is_active' => true, 'order' => 2],
        ['icon' => '', 'text' => 'Service', 'translate' => 'Service_t', 'link' => 'service' . $isFile, 'is_active' => true, 'order' => 3],
        ['icon' => '', 'text' => 'Product', 'translate' => 'product', 'link' => 'shop' . $isFile, 'is_active' => true, 'order' => 4],
        ['icon' => '', 'text' => 'Projects', 'translate' => 'project', 'link' => 'project' . $isFile, 'is_active' => true, 'order' => 6],
        ['icon' => '', 'text' => 'Articles', 'translate' => 'blog', 'link' => '#', 'isDropdown' => true, 'id' => 'dropdown4', 'is_active' => true, 'order' => 7],
        ['icon' => '', 'text' => 'News', 'translate' => 'News', 'link' => 'news' . $isFile, 'is_active' => true, 'order' => 8],
        ['icon' => '', 'text' => 'Contact us', 'translate' => 'Contact_us', 'link' => 'contact' . $isFile, 'is_active' => true, 'order' => 9],
    ],
    'cn' => [
        ['icon' => '', 'text' => '主页', 'translate' => 'Home', 'link' => 'index' . $isFile, 'is_active' => true, 'order' => 1],
        ['icon' => '', 'text' => '关于我们', 'translate' => 'About_us', 'link' => 'about' . $isFile, 'is_active' => true, 'order' => 2],
        ['icon' => '', 'text' => '服务', 'translate' => 'Service_t', 'link' => 'service' . $isFile, 'is_active' => true, 'order' => 3],
        ['icon' => '', 'text' => '产品', 'translate' => 'product', 'link' => 'shop' . $isFile, 'is_active' => true, 'order' => 4],
        ['icon' => '', 'text' => '项目', 'translate' => 'project', 'link' => 'project' . $isFile, 'is_active' => true, 'order' => 6],
        ['icon' => '', 'text' => '文章', 'translate' => 'blog', 'link' => '#', 'isDropdown' => true, 'id' => 'dropdown4', 'is_active' => true, 'order' => 7],
        ['icon' => '', 'text' => '新闻', 'translate' => 'News', 'link' => 'news' . $isFile, 'is_active' => true, 'order' => 8],
        ['icon' => '', 'text' => '联系我们', 'translate' => 'Contact_us', 'link' => 'contact' . $isFile, 'is_active' => true, 'order' => 9],
    ],
    'jp' => [
        ['icon' => '', 'text' => 'ホームページ', 'translate' => 'Home', 'link' => 'index' . $isFile, 'is_active' => true, 'order' => 1],
        ['icon' => '', 'text' => '私たちについて', 'translate' => 'About_us', 'link' => 'about' . $isFile, 'is_active' => true, 'order' => 2],
        ['icon' => '', 'text' => 'サービス', 'translate' => 'Service_t', 'link' => 'service' . $isFile, 'is_active' => true, 'order' => 3],
        ['icon' => '', 'text' => '製品', 'translate' => 'product', 'link' => 'shop' . $isFile, 'is_active' => true, 'order' => 4],
        ['icon' => '', 'text' => 'プロジェクト', 'translate' => 'project', 'link' => 'project' . $isFile, 'is_active' => true, 'order' => 6],
        ['icon' => '', 'text' => '記事', 'translate' => 'blog', 'link' => '#', 'isDropdown' => true, 'id' => 'dropdown4', 'is_active' => true, 'order' => 7],
        ['icon' => '', 'text' => 'ニュース', 'translate' => 'News', 'link' => 'news' . $isFile, 'is_active' => true, 'order' => 8],
        ['icon' => '', 'text' => 'お問い合わせ', 'translate' => 'Contact_us', 'link' => 'contact' . $isFile, 'is_active' => true, 'order' => 9],
    ],
    'kr' => [
        ['icon' => '', 'text' => '홈', 'translate' => 'Home', 'link' => 'index' . $isFile, 'is_active' => true, 'order' => 1],
        ['icon' => '', 'text' => '회사 소개', 'translate' => 'About_us', 'link' => 'about' . $isFile, 'is_active' => true, 'order' => 2],
        ['icon' => '', 'text' => '서비스', 'translate' => 'Service_t', 'link' => 'service' . $isFile, 'is_active' => true, 'order' => 3],
        ['icon' => '', 'text' => '제품', 'translate' => 'product', 'link' => 'shop' . $isFile, 'is_active' => true, 'order' => 4],
        ['icon' => '', 'text' => '프로젝트', 'translate' => 'project', 'link' => 'project' . $isFile, 'is_active' => true, 'order' => 6],
        ['icon' => '', 'text' => '기사', 'translate' => 'blog', 'link' => '#', 'isDropdown' => true, 'id' => 'dropdown4', 'is_active' => true, 'order' => 7],
        ['icon' => '', 'text' => '뉴스', 'translate' => 'News', 'link' => 'news' . $isFile, 'is_active' => true, 'order' => 8],
        ['icon' => '', 'text' => '문의', 'translate' => 'Contact_us', 'link' => 'contact' . $isFile, 'is_active' => true, 'order' => 9],
    ],
];
// (dropdownItems ใช้โครงสร้างเดิมได้)
$dropdownItems = [
    'dropdown3' => [
        'th' => [['icon' => '', 'text' => 'INSUL Software', 'translate' => 'INSUL Software', 'link' => 'INSULSoftware' . $isFile], ['icon' => '', 'text' => 'Download', 'translate' => 'Download', 'link' => 'Download' . $isFile], ['icon' => '', 'text' => 'Instructions', 'translate' => 'Instructions', 'link' => 'Instructions' . $isFile],],
        'en' => [['icon' => '', 'text' => 'INSUL Software', 'translate' => 'INSUL Software', 'link' => 'INSULSoftware' . $isFile], ['icon' => '', 'text' => 'Download', 'translate' => 'Download', 'link' => 'Download' . $isFile], ['icon' => '', 'text' => 'Instructions', 'translate' => 'Instructions', 'link' => 'Instructions' . $isFile],],
        'cn' => [['icon' => '', 'text' => 'INSUL 软件', 'translate' => 'INSUL Software', 'link' => 'INSULSoftware' . $isFile], ['icon' => '', 'text' => '下载', 'translate' => 'Download', 'link' => 'Download' . $isFile], ['icon' => '', 'text' => '使用说明', 'translate' => 'Instructions', 'link' => 'Instructions' . $isFile],],
        'jp' => [['icon' => '', 'text' => 'INSUL ソフトウェア', 'translate' => 'INSUL Software', 'link' => 'INSULSoftware' . $isFile], ['icon' => '', 'text' => 'ダウンロード', 'translate' => 'Download', 'link' => 'Download' . $isFile], ['icon' => '', 'text' => '説明書', 'translate' => 'Instructions', 'link' => 'Instructions' . $isFile],],
        'kr' => [['icon' => '', 'text' => 'INSUL 소프트웨어', 'translate' => 'INSUL Software', 'link' => 'INSULSoftware' . $isFile], ['icon' => '', 'text' => '다운로드', 'translate' => 'Download', 'link' => 'Download' . $isFile], ['icon' => '', 'text' => '지침', 'translate' => 'Instructions', 'link' => 'Instructions' . $isFile],],
    ],
    'dropdown4' => [
        'th' => [['icon' => '', 'text' => 'บทความ', 'translate' => 'blog', 'link' => 'Blog' . $isFile], ['icon' => '', 'text' => 'ความรู้ด้านเสียง', 'translate' => 'Design&Idia', 'link' => 'idia' . $isFile], ['icon' => '', 'text' => 'วีดีโอ', 'translate' => 'video', 'link' => 'Video' . $isFile],],
        'en' => [['icon' => '', 'text' => 'Articles', 'translate' => 'blog', 'link' => 'Blog' . $isFile], ['icon' => '', 'text' => 'Acoustics Knowledge', 'translate' => 'Design&Idia', 'link' => 'idia' . $isFile], ['icon' => '', 'text' => 'Video', 'translate' => 'video', 'link' => 'Video' . $isFile],],
        'cn' => [['icon' => '', 'text' => '文章', 'translate' => 'blog', 'link' => 'Blog' . $isFile], ['icon' => '', 'text' => '声学知识', 'translate' => 'Design&Idia', 'link' => 'idia' . $isFile], ['icon' => '', 'text' => '视频', 'translate' => 'video', 'link' => 'Video' . $isFile],],
        'jp' => [['icon' => '', 'text' => '記事', 'translate' => 'blog', 'link' => 'Blog' . $isFile], ['icon' => '', 'text' => '音響知識', 'translate' => 'Design&Idia', 'link' => 'idia' . $isFile], ['icon' => '', 'text' => 'ビデオ', 'translate' => 'video', 'link' => 'Video' . $isFile],],
        'kr' => [['icon' => '', 'text' => '기사', 'translate' => 'blog', 'link' => 'Blog' . $isFile], ['icon' => '', 'text' => '음향 지식', 'translate' => 'Design&Idia', 'link' => 'idia' . $isFile], ['icon' => '', 'text' => '비디오', 'translate' => 'video', 'link' => 'Video' . $isFile],],
    ],
];

// จัดเรียงเมนูตาม 'order' และกรองเมนูที่ไม่ active (เพื่อรองรับการสลับตำแหน่ง/เปิด-ปิด)
$activeNavbarItems = array_filter($navbarItems[$lang], function($item) {
    return ($item['is_active'] ?? true); // ถ้าไม่มีคีย์ is_active ถือว่า active
});
usort($activeNavbarItems, function($a, $b) {
    return ($a['order'] ?? 99) <=> ($b['order'] ?? 99);
});

// กำหนด CSS Variables เพื่อนำไปใช้ใน <style>
$navbarBgColor = $settings['navbar_bg_color'];
$navbarTextColor = $settings['navbar_text_color'];
$newsTickerBgColor = $settings['news_ticker_bg_color'];
$newsTickerTextColor = $settings['news_ticker_text_color'];
$newsTickerTitleBgColor = $settings['news_ticker_title_color'];
$newsTickerDisplay = $settings['news_ticker_display'] == '1' ? 'block' : 'none';

?>

<style>
/* ------------------------------------------------------------- */
/* CSS Variables (กำหนดสีจาก PHP) */
/* ------------------------------------------------------------- */
:root {
    --navbar-bg-color: <?= $navbarBgColor ?>;
    --navbar-text-color: <?= $navbarTextColor ?>;
    --news-ticker-bg-color: <?= $newsTickerBgColor ?>;
    --news-ticker-text-color: <?= $newsTickerTextColor ?>;
    --news-ticker-title-bg-color: <?= $newsTickerTitleBgColor ?>;
}

/* ------------------------------------------------------------- */
/* CSS สำหรับ Desktop Menu โดยเฉพาะ (ปรับใช้ Variable) */
/* ------------------------------------------------------------- */
.navbar-desktop {
    background-color: var(--navbar-bg-color); /* ใช้ Variable */
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
    color: var(--navbar-text-color); /* ใช้ Variable */
    text-decoration: none;
    padding: 10px 15px;
    font-size: 20px;
    transition: background-color 0.3s;
}

.desktop-menu-item:hover {
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: 4px;
}

/* ... (CSS ส่วนอื่นๆ ของ Dropdown และ Mobile ใช้โครงสร้างเดิมได้) ... */

/* ------------------------------------------------------------- */
/* CSS สำหรับ Mobile Menu โดยเฉพาะ (ปรับใช้ Variable) */
/* ------------------------------------------------------------- */
.navbar-mobile-container {
    display: none;
    position: relative;
    background-color: var(--navbar-bg-color); /* ใช้ Variable */
}

.mobile-slide-out-menu {
    background-color: var(--navbar-bg-color); /* ใช้ Variable */
    /* ... */
}

.mobile-slide-out-menu a {
    color: var(--navbar-text-color); /* ใช้ Variable */
    /* ... */
}
/* ... (CSS ส่วนอื่นๆ ของ Mobile) ... */

/* ------------------------------------------------------------- */
/* CSS ของ News Ticker (ปรับใช้ Variable และสีหัวข่าว) */
/* ------------------------------------------------------------- */
#navbar-news {
    position: relative;
    z-index: 998;
    display: <?= $newsTickerDisplay ?>; /* ปิด/เปิดแถบข่าวจาก DB */
}
.news-ticker {
    position: relative;
    background-color: var(--news-ticker-bg-color); /* ใช้ Variable */
    color: var(--news-ticker-text-color); /* ใช้ Variable */
    font-weight: bold;
    z-index: 998;
    white-space: nowrap;
    font-size: 24px;
    border-radius: 0px 70px 10px 0px;
    /* ปรับให้รองรับ Flexbox เพื่อจัดการหัวข้อและ Marquee */
    display: flex; 
    align-items: center;
}

.text-ticker {
    /* ส่วนหัวข้อ "ข่าวประจำวัน" */
    background-color: var(--news-ticker-title-bg-color); /* ใช้ Variable */
    color: var(--navbar-text-color); /* สีตัวอักษรหัวข้อ (ใช้สีเดียวกับ Navbar) */
    padding: 10px 20px;
    border-radius: 0px 70px 10px 0px;
    line-height: 1; /* ช่วยให้ข้อความไม่สูงเกินไป */
}

#newsMarquee {
    flex-grow: 1; /* ให้ Marquee ขยายเต็มพื้นที่ที่เหลือ */
    color: var(--news-ticker-text-color); /* สีข้อความข่าว */
    padding: 0 10px;
}
a {
    color: var(--navbar-text-color); /* สำหรับลิงก์ใน Navbar */
}
a.desktop-menu-item {
    color: var(--navbar-text-color);
}
.desktop-dropdown-content a {
    color: #565656; /* ให้ dropdown content ใช้สีเดิม */
}
</style>

<div class="navbar-desktop">
    <div class="container">
        <div class="desktop-menu-container">
            <?php foreach ($activeNavbarItems as $item): // ใช้ $activeNavbarItems ที่ถูกกรองและจัดเรียงแล้ว ?>
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
                            <?php if (isset($dropdownItems[$item['id']][$lang])): ?>
                                <?php foreach ($dropdownItems[$item['id']][$lang] as $dropdownItem): ?>
                                    <a href="<?php echo $dropdownItem['link']; ?>">
                                        <i class="<?php echo $dropdownItem['icon']; ?>"></i>
                                        <span data-translate="<?php echo $dropdownItem['translate']; ?>" lang="th">
                                            <?php echo $dropdownItem['text']; ?>
                                        </span>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
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
// JavaScript สำหรับ Mobile Menu (เหมือนเดิม)
const mobileMenu = document.getElementById("mobileMenu");
const hamburger = document.querySelector(".hamburger");

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
    <div style="margin-left:5%;">
        <div class="news-ticker">
            <span class="text-ticker">
                <span class="blinking-icon"></span>
                <?php
                // แสดงข้อความ "ข่าวประจำวัน" ตามภาษาที่เลือก
                $newsText = [
                    'th' => 'ข่าวประจำวัน',
                    'en' => 'Daily News',
                    'cn' => '每日新闻',
                    'jp' => 'デイリーニュース',
                    'kr' => '일일 뉴스'
                ];
                echo $newsText[$lang] ?? 'ข่าวประจำวัน';
                ?>
            </span>
            <marquee id="newsMarquee" scrollamount="4" behavior="scroll" direction="left" onmouseover="this.stop();" onmouseout="this.start();">
                <div id="newsMarquee-link" style="display: inline;">
                    <?php foreach ($newsList as $news): ?>
                        <span style="padding: 0 50px;">
                            <a href="news.php?id=<?= $news['news_id'] ?>&lang=<?= $lang ?>" style="text-decoration: none; color: var(--news-ticker-text-color);">
                                <?= htmlspecialchars($news[$subjectColumn]) ?>
                            </a>
                        </span>
                    <?php endforeach; ?>
                </div>
            </marquee>
        </div>
    </div>
</div>
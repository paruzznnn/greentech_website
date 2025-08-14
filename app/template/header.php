<?php
//header-top-right
$isProtocol = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
$isFile = ($isProtocol === 'http') ? '.php' : '';

// แก้ไข URL ให้รวมพารามิเตอร์ lang
function generateLink($link, $params = []) {
    global $isFile;
    $url = $link . $isFile;
    
    // ดึงพารามิเตอร์ที่มีอยู่แล้วใน URL
    $existingParams = $_GET;
    
    // รวมพารามิเตอร์ใหม่เข้ากับพารามิเตอร์เดิม
    $newParams = array_merge($existingParams, $params);
    
    // ลบพารามิเตอร์ที่ไม่มีค่าออก
    $newParams = array_filter($newParams);
    
    // สร้าง query string ใหม่
    $queryString = http_build_query($newParams);
    
    if (!empty($queryString)) {
        $url .= '?' . $queryString;
    }
    
    return $url;
}

$menuItems = [
    //
    [
        'id' => 0,
        'icon' => 'fas fa-user-plus',
        'text' => 'Sign up',
        'translate' => 'Sign_up',
        'link' => 'register' . $isFile,
        'modal_id' => ''
    ],
    [
        'id' => 1,
        'icon' => 'fas fa-sign-in-alt',
        'text' => 'Sign in',
        'translate' => 'Sign_in',
        'link' => '#',
        'modal_id' => 'myBtn-sign-in'
    ],
];
?>
<?php
// ดึงข่าว 3 รายการล่าสุดจาก dn_news
// require_once('../lib/connect.php');
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

<?php
require_once('../lib/connect.php');
global $conn;

$currentPage = basename($_SERVER['PHP_SELF']);
$meta = [];

$stmt = $conn->prepare("SELECT * FROM metatags WHERE page_name = ?");
$stmt->bind_param("s", $currentPage);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $meta = $result->fetch_assoc();
}

$logo_path = '../public/img/LOGOTRAND.png';
$logo_id_for_display = 1;

$stmt_logo = $conn->prepare("SELECT image_path FROM logo_settings WHERE id = ?");
$stmt_logo->bind_param("i", $logo_id_for_display);
$stmt_logo->execute();
$result_logo = $stmt_logo->get_result();

if ($logo_data = $result_logo->fetch_assoc()) {
    $logo_path = htmlspecialchars($logo_data['image_path']);
}
$stmt_logo->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM mb_user WHERE email = ? OR phone_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            header("Location: dashboard.php");
            exit;
        } else {
            $login_error = "รหัสผ่านไม่ถูกต้อง";
        }
    } else {
        $login_error = "ไม่พบบัญชีผู้ใช้นี้";
    }
}
?>

<title><?= $meta['meta_title'] ?? 'Trandar' ?></title>
<meta name="description" content="<?= $meta['meta_description'] ?? 'Trandar ราคาถูก มีบริการหลังการขาย' ?>">
<meta name="keywords" content="<?= $meta['meta_keywords'] ?? 'Trandar, แผ่นฝ้า, ฝ้าดูดซับเสียง' ?>">
<meta name="author" content="trandar.com">

<meta property="og:site_name" content="trandar.com">
<meta property="og:title" content="<?= $meta['og_title'] ?? $meta['meta_title'] ?? 'Trandar' ?>">
<meta property="og:description"
    content="<?= $meta['og_description'] ?? $meta['meta_description'] ?? 'Trandar ราคาถูก มีบริการหลังการขาย' ?>">
<meta property="og:type" content="website">
<meta property="og:image" content="<?= $meta['og_image'] ?? '../../public/img/LOGO TRANDAR.png' ?>">

<script>(function (w, d, s, l, i) {
        w[l] = w[l] || []; w[l].push({
            'gtm.start':
                new Date().getTime(), event='gtm.js'
        }); var f = d.getElementsByTagName(s)[0],
            j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : ''; j.async = true; j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl; f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', 'GTM-N57LMZ6H');</script>
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-N57LMZ6H" height="0" width="0"
        style="display:none;visibility:hidden"></iframe></noscript>
<div id="loading-overlay" class="hidden">
    <div class="spinner"></div>
</div>


<div id="background-blur"></div>

<style>
/* ------------------------------------------------------------- */
/* CSS สำหรับ Desktop Header (แถบบนสุด) */
/* ------------------------------------------------------------- */
.header-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    background-color: #f1f1f1;
    border-bottom: 1px solid #ddd;
    position: relative;
    z-index: 1000;
}
.header-top-left {
    display: flex;
    align-items: center;
}
.header-top .logo {
    height: 55px;
    max-height: 55px;
    width: auto;
}
.header-top-right {
    display: flex;
    align-items: center;
    gap: 15px;
}
.header-top-buttons {
    display: flex;
    align-items: center;
    gap: 5px;
}
.header-top-buttons a,
.header-top-buttons button {
    padding: 8px 15px;
    border-radius: 4px;
    text-decoration: none;
    color: white;
    font-size: 14px;
    white-space: nowrap;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    transition: background-color 0.3s;
}
.header-top-buttons .store-btn {
    background: #ff9900;
}
.header-top-buttons .store-btn:hover {
    background: #ff9900;
}
#logout-btn {
    background: #ff3333;
}
#logout-btn:hover {
    background: #cc0000;
}
.language-select-container {
    position: relative;
    display: inline-block;
    z-index: 1001; /* เพิ่ม z-index ให้สูงขึ้น */
}
.flag-icon {
    width: 24px;
    height: auto;
    cursor: pointer;
    border: 1px solid #ddd;
    border-radius: 2px;
}
.flag-dropdown {
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    background-color: #f9f9f9;
    min-width: 120px;
    box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
    z-index: 1003;
    border-radius: 4px;
    padding: 5px 0;
}
.flag-dropdown a {
    color: black;
    padding: 8px 16px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
    white-space: nowrap;
}
.flag-dropdown a:hover {
    background-color: #f1f1f1;
}
.header-social-links {
    display: flex;
    gap: 5px;
}
.header-social-links a {
    background: #ff9900;
    color: #fff;
    padding: 8px 12px;
    border-radius: 4px;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.3s;
}
.header-social-links a:hover {
    background-color: #ff9900;
}
/* ------------------------------------------------------------- */
/* CSS สำหรับ Mobile Header (แถบบนสุด) */
/* ------------------------------------------------------------- */
.header-mobile {
    display: none;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    background-color: transparent;
    z-index: 1000;
    position: relative;
}
.header-mobile .logo {
    height: 100px;
    width: auto;
    display: block;
}
.mobile-dropdown-tab {
    position: static;
    left: auto;
}
.mobile-dropdown-button {
    background-color: transparent;
    color: #333;
    padding: 10px;
    border: none;
}
.hamburger {
    color: #333;
    font-size: 28px;
    cursor: pointer;
}
.mobile-dropdown-content {
    display: none;
    position: absolute;
    right: 0;
    background-color: #f9f9f9;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
    z-index: 1002;
    border-radius: 4px;
    padding: 10px 0;
}
.mobile-dropdown-content a {
    padding: 8px 12px;
}
.mobile-dropdown-content a,
.mobile-dropdown-content button {
    color: #555;
    text-decoration: none;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    /* background-color: #ff9900; */
    border: none;
    cursor: pointer;
}
.mobile-dropdown-content a[href*="store"],
.mobile-dropdown-content button[id*="logout"] {
    background-color: #ffa719;
    color: white;
}
.mobile-dropdown-content a[href*="store"]:hover,
.mobile-dropdown-content button[id*="logout"]:hover {
    background-color: #ff9900;
}
.mobile-dropdown-content a:hover,
.mobile-dropdown-content button:hover {
    background-color: #f1f1f1;
}
/* ------------------------------------------------------------- */
/* CSS สำหรับ Navbar (เมนูหลัก) */
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
/* CSS สำหรับ Mobile Slide Out Menu */
/* ------------------------------------------------------------- */
.navbar-mobile-container {
    display: none;
    position: relative;
    background-color: #ff9900;
}
.hamburger {
    font-size: 28px;
    cursor: pointer;
    color: #555;
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
    .header-top {
        display: none;
    }
    .header-mobile {
        display: flex;
    }
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
    color: #ffffff;;
}
</style>

<div class="header-top">
    <div class="header-top-left">
        <a href="https://www.trandar.com">
            <img class="logo" src="<?= $logo_path ?>" alt="Website Logo">
        </a>
        <div id="current-date" style="margin-left: 20px; color:#555; font-size: 16px; font-weight: 500;"></div>
    </div>
    <div class="header-top-right">
        <div class="header-top-buttons">
            <div id="auth-buttons">
                <?php foreach ($menuItems as $item): ?>
                    <a type="button" href="<?php echo $item['link']; ?>" id="<?php echo $item['modal_id'] ?>" class="auth-btn">
                        <i class="<?php echo $item['icon']; ?>"></i>
                        <span data-translate="<?php echo $item['translate']; ?>" lang="th">
                            <?php echo $item['text']; ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
            <a href="#" id="logout-btn" style="display:none;">
                <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
            </a>
            <a href="https://www.trandar.com/store/" target="_blank" class="store-btn">
                Trandar Store <i class="fas fa-shopping-cart" style="margin-left: 8px;"></i>
            </a>
        </div>

         <div>
        <select id="language-select" class="language-select"></select>
    </div>
        <!-- <div class="language-select-container">
            <img id="current-flag-desktop" src="https://flagcdn.com/th.svg" alt="Thai Flag" class="flag-icon"
                onclick="toggleFlagDropdown('desktop')">
            <div id="flag-dropdown-desktop" class="flag-dropdown">
                <a href="?<?php if(isset($_GET['id'])) echo 'id=' . urlencode($_GET['id']) . '&'; ?>lang=th">
                    <img src="https://flagcdn.com/th.svg" alt="Thai Flag" width="24"> ไทย
                </a>
                <a href="?<?php if(isset($_GET['id'])) echo 'id=' . urlencode($_GET['id']) . '&'; ?>lang=en">
                    <img src="https://flagcdn.com/us.svg" alt="US Flag" width="24"> English
                </a>
            </div>
        </div> -->
        <div class="header-social-links">
            <a href="https://www.facebook.com/trandaracoustic/" target="_blank">
                <i class="fab fa-facebook-square"></i>
            </a>
            <a href="https://www.youtube.com/channel/UCewsEEtw8DOwSWoQ6ae_Uwg/" target="_blank">
                <i class="fab fa-youtube"></i>
            </a>
            <a href="https://www.instagram.com/trandaracoustics/" target="_blank">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="https://lin.ee/yoSCNwF" target="_blank">
                <i class="fab fa-line"></i>
            </a>
            <a href="https://www.tiktok.com/@trandaracoustics" target="_blank">
                <i class="fab fa-tiktok"></i>
            </a>
        </div>
    </div>
</div>

<div class="header-mobile">
    <div class="mobile-header">
        <div class="hamburger" style="color: #555;" onclick="toggleMobileNav()">☰</div>
    </div>
    <div class="mobile-logo-container">
        <a href="https://www.trandar.com">
            <img class="logo" src="<?= $logo_path ?>" alt="Website Logo">
        </a>
    </div>
    <div class="header-mobile-right">
        <div class="mobile-dropdown-tab">
            <button class="mobile-dropdown-button">
                <i class="fas fa-user-shield"></i>
            </button>
            <div class="mobile-dropdown-content">
                <div id="auth-buttons-mobile">
                    <?php foreach ($menuItems as $item): ?>
                        <a type="button" href="<?php echo $item['link']; ?>" id="<?php echo $item['modal_id'] ?>">
                            <i class="<?php echo $item['icon']; ?>"></i>
                            <span data-translate="<?php echo $item['translate']; ?>" lang="th">
                                <?php echo $item['text']; ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
                <a href="#" id="logout-btn-mobile" style="display:none;">
                    <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
                </a>
                <hr style="margin: 10px 0; border-color: #ddd;">
                <a href="https://www.trandar.com/store/" target="_blank"><i class="fas fa-shopping-cart"></i>Trandar
                    Store</a>
                <div class="language-select-container" style="padding: 12px 16px;">
                    <img id="current-flag-mobile" src="https://flagcdn.com/th.svg" alt="Thai Flag" class="flag-icon"
                        onclick="toggleFlagDropdown('mobile')">
                    <div id="flag-dropdown-mobile" class="flag-dropdown">
                        <a href="<?php echo generateLink(basename($_SERVER['PHP_SELF'], '.php'), ['lang' => 'th']); ?>">
                            <img src="https://flagcdn.com/th.svg" alt="Thai Flag" width="24"> ไทย
                        </a>
                        <a href="<?php echo generateLink(basename($_SERVER['PHP_SELF'], '.php'), ['lang' => 'en']); ?>">
                            <img src="https://flagcdn.com/us.svg" alt="US Flag" width="24"> English
                        </a>
                    </div>
                </div>
                <div class="header-social-links" style="padding: 12px 16px;">
                    <a href="https://www.facebook.com/trandaracoustic/" target="_blank">
                        <i class="fab fa-facebook-square"></i>
                    </a>
                    <a href="https://www.youtube.com/channel/UCewsEEtw8DOwSWoQ6ae_Uwg/" target="_blank">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="https://www.instagram.com/trandaracoustics/" target="_blank">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://lin.ee/yoSCNwF" target="_blank">
                        <i class="fab fa-line"></i>
                    </a>
                    <a href="https://www.tiktok.com/@trandaracoustics" target="_blank">
                        <i class="fab fa-tiktok"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mobile-slide-out-menu" id="mobileMenu">
    <a href="javascript:void(0)" class="close-btn" onclick="toggleMobileNav()">×</a>
    <?php foreach ($navbarItems as $item): ?>
        <?php if (isset($item['isDropdown']) && $item['isDropdown']): ?>
            <div class="dropdown-mobile">
                <a href="javascript:void(0)" class="dropdown-toggle" onclick="toggleMobileDropdown('<?php echo $item['id']; ?>')">
                    <span data-translate="<?php echo $item['translate']; ?>" lang="th">
                        <?php echo $item['text']; ?>
                    </span>
                </a>
                <div class="dropdown-content" id="<?php echo $item['id']; ?>_mobile">
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

<div id="myModal-sign-in" class="modal">
    <div class="modal-content" style="width: 350px !important;">
        <div class="modal-header">
            <span class="modal-close-sign-in">×</span>
        </div>
        <div class="modal-body" style="background-color: #9e9e9e1f;">
            <div class="box-sign-in-container">
                <div class="card">
                    <section class="card-body">
                        <div style="text-align: center;">
                            <img class="" style="width: 70%;" src="../public/img/trandar.jpg" alt="">
                        </div>
                        <h6 style="text-align: center; color: #555;" class="mt-2">
                            <span><i class="fas fa-unlock"></i></span>
                            <span data-key-lang="Pleaselogin" lang="US">Please log in</span>
                        </h6>
                        <hr>
                        <form id="loginModal" action="" method="post">
                            <div class="form-group mt-4">
                                <input id="username" type="text" class="emet-login input"
                                    placeholder="Please enter your email.">
                            </div>
                            <div class="form-group mt-2" style="position: relative;">
                                <input id="password" type="password" class="emet-login inpu" data-type="password">
                                <span class="" style="position: absolute; top: 10px; right: 20px; color: #555555;"
                                    id="togglePasswordSignin">
                                    <i class="fas fa-eye-slash"></i>
                                </span>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-12 text-end" style="
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                                ">
                                    <a href="<?php echo 'register' . $isFile ?>">
                                        <span style="font-size: 13px !important;">
                                            สมัครสมาชิก
                                        </span>
                                    </a>
                                    <a type="button" href="#" id="myBtn-forgot-password">
                                        <span style="font-size: 13px !important;">
                                            ลืมรหัสผ่าน
                                        </span>
                                    </a>
                                </div>
                                <div class="col-md-12">
                                    <div class="d-inline-flex">
                                        <button type="submit" class="" style="
                                            width: 260px;
                                            border: none;
                                            border-radius: 4px;
                                            padding: 10px;
                                            background: #ff8200;
                                            color: white;
                                            "> Login </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="myModal-forgot-password" class="modal">
    <div class="modal-content" style="width: 350px !important;">
        <div class="modal-header">
            <span class="modal-close-forgot-password">×</span>
        </div>
        <div class="modal-body" style="background-color: #9e9e9e1f;">
            <div class="box-forgot-password-container">
                <div class="card">
                    <section class="card-body">
                        <div style="text-align: center;">
                            <img class="" style="width: 70%;" src="../public/img/trandar.jpg" alt="">
                        </div>
                        <h6 style="text-align: center; color: #555;" class="mt-2">
                            <span>
                                <i class="fas fa-key"></i>
                            </span>
                            <span data-key-lang="" lang="US">Forgot your password?</span>
                        </h6>
                        <hr>
                        <form id="forgotModal" action="" method="post">
                            <div class="form-group mt-4">
                                <input id="forgot_email" name="forgot_email" type="text"
                                    class="form-control emet-login input" placeholder="Please enter your email.">
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="d-inline-flex">
                                        <button type="button" id="submitForgot" class="" style="
                                        width: 260px;
                                        border: none;
                                        border-radius: 4px;
                                        padding: 10px;
                                        background: #ff8200;
                                        color: white;
                                        "> send email </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
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

    document.addEventListener('click', function (event) {
        const isClickInsideMenu = mobileMenu.contains(event.target);
        const isClickOnHamburger = hamburger.contains(event.target);
        const closeBtn = document.querySelector(".close-btn");

        if (mobileMenu.classList.contains("open") && !isClickInsideMenu && !isClickOnHamburger && closeBtn && !closeBtn.contains(event.target)) {
            toggleMobileNav();
        }
    });

    // โค้ด JavaScript ที่แก้ไขเพื่อจัดการ dropdown ของธง
    document.addEventListener('DOMContentLoaded', function() {
        const currentFlagDesktop = document.getElementById('current-flag-desktop');
        const flagDropdownDesktop = document.getElementById('flag-dropdown-desktop');
        const currentFlagMobile = document.getElementById('current-flag-mobile');
        const flagDropdownMobile = document.getElementById('flag-dropdown-mobile');

        // ฟังก์ชันสำหรับอัปเดตธงและ URL เมื่อมีการเลือกภาษา
        function updateFlagAndLanguage() {
            const urlParams = new URLSearchParams(window.location.search);
            const lang = urlParams.get('lang') || 'th';
            const flagUrl = (lang === 'en') ? 'https://flagcdn.com/us.svg' : 'https://flagcdn.com/th.svg';
            if (currentFlagDesktop) {
                currentFlagDesktop.src = flagUrl;
            }
            if (currentFlagMobile) {
                currentFlagMobile.src = flagUrl;
            }
        }
        updateFlagAndLanguage();

        // ฟังก์ชันสำหรับสลับการแสดงผล dropdown
        window.toggleFlagDropdown = function(type) {
            if (type === 'desktop') {
                const isDropdownOpen = flagDropdownDesktop.style.display === 'block';
                flagDropdownDesktop.style.display = isDropdownOpen ? 'none' : 'block';
                // ปิด mobile dropdown ถ้าเปิดอยู่
                if (flagDropdownMobile) {
                    flagDropdownMobile.style.display = 'none';
                }
            } else if (type === 'mobile') {
                const isDropdownOpen = flagDropdownMobile.style.display === 'block';
                flagDropdownMobile.style.display = isDropdownOpen ? 'none' : 'block';
                // ปิด desktop dropdown ถ้าเปิดอยู่
                if (flagDropdownDesktop) {
                    flagDropdownDesktop.style.display = 'none';
                }
            }
        };

        // ปิด dropdown ทั้งคู่เมื่อคลิกที่อื่น
        document.addEventListener('click', function(e) {
            if (flagDropdownDesktop && !flagDropdownDesktop.contains(e.target) && e.target !== currentFlagDesktop) {
                flagDropdownDesktop.style.display = 'none';
            }
            if (flagDropdownMobile && !flagDropdownMobile.contains(e.target) && e.target !== currentFlagMobile) {
                flagDropdownMobile.style.display = 'none';
            }
        });
    });


    // สคริปต์สำหรับ Modal และ JWT
    document.addEventListener("DOMContentLoaded", function () {
        const jwt = sessionStorage.getItem("jwt");
        const authButtonsDesktop = document.getElementById("auth-buttons");
        const logoutBtnDesktop = document.getElementById("logout-btn");
        const signinModalBtn = document.getElementById("myBtn-sign-in");

        const authButtonsMobile = document.getElementById("auth-buttons-mobile");
        const logoutBtnMobile = document.getElementById("logout-btn-mobile");

        if (jwt) {
            fetch('actions/protected.php', {
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + jwt
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === "success" && parseInt(data.data.role_id) === 3) {
                        if (authButtonsDesktop) authButtonsDesktop.style.display = "none";
                        if (logoutBtnDesktop) logoutBtnDesktop.style.display = "block";
                        if (authButtonsMobile) authButtonsMobile.style.display = "none";
                        if (logoutBtnMobile) logoutBtnMobile.style.display = "block";
                    }
                })
                .catch(error => console.error("Token verification failed:", error));
        } else {
            if (authButtonsDesktop) authButtonsDesktop.style.display = "block";
            if (logoutBtnDesktop) logoutBtnDesktop.style.display = "none";
            if (authButtonsMobile) authButtonsMobile.style.display = "block";
            if (logoutBtnMobile) logoutBtnMobile.style.display = "none";
        }

        const modalSignin = document.getElementById('myModal-sign-in');
        const modalForgot = document.getElementById('myModal-forgot-password');
        const signinModalCloseBtn = document.querySelector('.modal-close-sign-in');
        const forgotModalBtn = document.getElementById('myBtn-forgot-password');
        const forgotModalCloseBtn = document.querySelector('.modal-close-forgot-password');

        if (signinModalBtn) {
            signinModalBtn.onclick = function (e) {
                e.preventDefault();
                modalSignin.style.display = 'block';
            }
        }

        if (signinModalCloseBtn) {
            signinModalCloseBtn.onclick = function () {
                modalSignin.style.display = 'none';
            }
        }

        if (forgotModalBtn) {
            forgotModalBtn.onclick = function (e) {
                e.preventDefault();
                modalSignin.style.display = 'none';
                modalForgot.style.display = 'block';
            }
        }

        if (forgotModalCloseBtn) {
            forgotModalCloseBtn.onclick = function () {
                modalForgot.style.display = 'none';
            }
        }

        if (logoutBtnDesktop) {
            logoutBtnDesktop.addEventListener("click", function (e) {
                e.preventDefault();
                sessionStorage.removeItem("jwt");
                location.reload();
            });
        }
        if (logoutBtnMobile) {
            logoutBtnMobile.addEventListener("click", function (e) {
                e.preventDefault();
                sessionStorage.removeItem("jwt");
                location.reload();
            });
        }

        const mobileDropdownTab = document.querySelector('.mobile-dropdown-tab');
        const mobileDropdownButton = document.querySelector('.mobile-dropdown-button');
        const mobileDropdownContent = document.querySelector('.mobile-dropdown-content');

        mobileDropdownButton.addEventListener('click', function (event) {
            mobileDropdownContent.style.display = mobileDropdownContent.style.display === 'block' ? 'none' : 'block';
            event.stopPropagation();
        });

        document.addEventListener('click', function (event) {
            if (!mobileDropdownTab.contains(event.target)) {
                mobileDropdownContent.style.display = 'none';
            }
        });

        // window.toggleFlagDropdown = function (mode = 'desktop') {
        //     const dropdownId = mode === 'mobile' ? 'flag-dropdown-mobile' : 'flag-dropdown-desktop';
        //     const dropdown = document.getElementById(dropdownId);
        //     if (dropdown) {
        //         dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        //     }
        // };

    //     const flagLinks = document.querySelectorAll('.flag-dropdown a');
    //     flagLinks.forEach(link => {
    //         link.addEventListener('click', function (event) {
    //             event.preventDefault();
    //             const lang = this.dataset.lang;
    //             const currentFlagDesktop = document.getElementById('current-flag');
    //             const currentFlagMobile = document.getElementById('current-flag-mobile');
    //             const newFlagSrc = this.querySelector('img').src;
    //             const newFlagAlt = this.querySelector('img').alt;
    //             if (currentFlagDesktop) {
    //                 currentFlagDesktop.src = newFlagSrc;
    //                 currentFlagDesktop.alt = newFlagAlt;
    //             }
    //             if (currentFlagMobile) {
    //                 currentFlagMobile.src = newFlagSrc;
    //                 currentFlagMobile.alt = newFlagAlt;
    //             }
    //             const dropdownDesktop = document.getElementById('flag-dropdown-desktop');
    //             const dropdownMobile = document.getElementById('flag-dropdown-mobile');
    //             if (dropdownDesktop) dropdownDesktop.style.display = 'none';
    //             if (dropdownMobile) dropdownMobile.style.display = 'none';
    //         });
    //     });

    //     document.addEventListener('click', function (event) {
    //         const isClickInsideFlagDropdown = event.target.closest('.language-select-container');
    //         const flagDropdownDesktop = document.getElementById('flag-dropdown-desktop');
    //         const flagDropdownMobile = document.getElementById('flag-dropdown-mobile');
    //         if (!isClickInsideFlagDropdown) {
    //             if (flagDropdownDesktop) flagDropdownDesktop.style.display = 'none';
    //             if (flagDropdownMobile) flagDropdownMobile.style.display = 'none';
    //         }
    //     });
    });
</script>
<script>
    const langLinks = {
        th: "<?php echo generateLink(basename($_SERVER['PHP_SELF'], '.php'), ['lang' => 'th']); ?>",
        en: "<?php echo generateLink(basename($_SERVER['PHP_SELF'], '.php'), ['lang' => 'en']); ?>"
    };
</script>

<div id="myModal-sign-in" class="modal">
    <div class="modal-content" style="width: 350px !important;">
        <div class="modal-header">
            <span class="modal-close-sign-in">×</span>
        </div>
        <div class="modal-body" style="background-color: #9e9e9e1f;">
            <div class="box-sign-in-container">
                <div class="card">
                    <section class="card-body">
                        <div style="text-align: center;">
                            <img class="" style="width: 70%;" src="../public/img/trandar.jpg" alt="">
                        </div>
                        <h6 style="text-align: center; color: #555;" class="mt-2">
                            <span><i class="fas fa-unlock"></i></span>
                            <span data-key-lang="Pleaselogin" lang="US">Please log in</span>
                        </h6>
                        <hr>
                        <form id="loginModal" action="" method="post">
                            <div class="form-group mt-4">
                                <input id="username" type="text" class="emet-login input"
                                    placeholder="Please enter your email.">
                            </div>
                            <div class="form-group mt-2" style="position: relative;">
                                <input id="password" type="password" class="emet-login inpu" data-type="password">
                                <span class="" style="position: absolute; top: 10px; right: 20px; color: #555555;"
                                    id="togglePasswordSignin">
                                    <i class="fas fa-eye-slash"></i>
                                </span>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-12 text-end" style="
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                                ">
                                    <a href="<?php echo 'register' . $isFile ?>">
                                        <span style="font-size: 13px !important;">
                                            สมัครสมาชิก
                                        </span>
                                    </a>
                                    <a type="button" href="#" id="myBtn-forgot-password">
                                        <span style="font-size: 13px !important;">
                                            ลืมรหัสผ่าน
                                        </span>
                                    </a>
                                </div>
                                <div class="col-md-12">
                                    <div class="d-inline-flex">
                                        <button type="submit" class="" style="
                                            width: 260px;
                                            border: none;
                                            border-radius: 4px;
                                            padding: 10px;
                                            background: #ff8200;
                                            color: white;
                                            "> Login </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </section>
                </div>

            </div>
        </div>
    </div>
</div>

<div id="myModal-forgot-password" class="modal">
    <div class="modal-content" style="width: 350px !important;">
        <div class="modal-header">
            <span class="modal-close-forgot-password">×</span>
        </div>
        <div class="modal-body" style="background-color: #9e9e9e1f;">
            <div class="box-forgot-password-container">
                <div class="card">
                    <section class="card-body">
                        <div style="text-align: center;">
                            <img class="" style="width: 70%;" src="../public/img/trandar.jpg" alt="">
                        </div>
                        <h6 style="text-align: center; color: #555;" class="mt-2">
                            <span>
                                <i class="fas fa-key"></i>
                            </span>
                            <span data-key-lang="" lang="US">Forgot your password?</span>
                        </h6>
                        <hr>
                        <form id="forgotModal" action="" method="post">
                            <div class="form-group mt-4">
                                <input id="forgot_email" name="forgot_email" type="text"
                                    class="form-control emet-login input" placeholder="Please enter your email.">
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="d-inline-flex">
                                        <button type="button" id="submitForgot" class="" style="
                                        width: 260px;
                                        border: none;
                                        border-radius: 4px;
                                        padding: 10px;
                                        background: #ff8200;
                                        color: white;
                                        "> send email </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>























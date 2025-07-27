<?php
// ดึงข่าว 3 รายการล่าสุดจาก dn_news
// require_once('../lib/connect.php');
$newsList = [];
$sql = "SELECT news_id, subject_news FROM dn_news ORDER BY date_create DESC LIMIT 3";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $newsList[] = $row;
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
#navbar-menu {
    background-color: white;
    position: relative;
    z-index: 999;
    /* border-bottom: 1px solid #ddd; */
    overflow: visible;
    background-color: #ff9900;
}
.container {
    position: relative;
    overflow: visible;
}


.over-menu {
    display: flex;
    flex-wrap: wrap; /* ✅ บังคับขึ้นบรรทัดใหม่เมื่อเต็ม */
    justify-content: center;
    align-items: center;
    padding: 6px 0;
    overflow: visible;
    gap: 5px; /* เว้นระยะระหว่างเมนู */
}

.over-menu a,
.over-menu .dropdown {
    flex: 1 1 auto; /* ✅ ให้แต่ละอันยืดหดได้ */
    text-align: center;
    padding: 0px 0px;
    min-width: 100px; /* ✅ ป้องกันเมนูแคบเกิน */
    white-space: nowrap;
    /* เพิ่ม transition เพื่อความ smooth ในการปรับขนาด */
    transition: all 0.3s ease; 
}

@media (max-width: 768px) {
    .over-menu a,
    .over-menu .dropdown {
        /* ปรับ flex-basis ให้ยืดหยุ่นมากขึ้น และ min-width ลดลง */
        flex: 1 1 30%; /* อาจจะให้แสดง 3 คอลัมน์ ถ้าข้อความสั้นพอ หรือ 2 คอลัมน์ที่ยืดหยุ่นกว่า */
        min-width: 80px; /* ลด min-width ลงเพื่อให้หดได้มากขึ้น */
        font-size: 0.8em; /* ลดขนาดตัวอักษรลง */
        padding: 5px 2px; /* ลด padding */
    }
}

@media (max-width: 480px) { /* สำหรับมือถือที่เล็กกว่า */
    .over-menu a,
    .over-menu .dropdown {
        flex: 1 1 45%; /* อาจจะกลับไป 2 คอลัมน์ หรือ 100% ถ้าข้อความยาวมาก */
        min-width: 120px; /* อาจจะขยายกลับถ้าอยากให้เมนูใหญ่ขึ้นเมื่อแสดง 2 คอลัมน์ */
        font-size: 0.9em; /* ปรับขนาดตัวอักษรให้เหมาะสม */
        padding: 5px 0px;
    }
}


.dropdown {
    position: relative; /* ✅ Anchor ของ absolute dropdown */
}

.dropdown-content {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background-color: #fff;
    box-shadow: 0px 8px 16px rgba(0,0,0,0.1);
    z-index: 10000;
    min-width: 180px;
    max-width: 220px;
    border-radius: 4px;
}

.dropdown-show {
    display: flex;
    flex-direction: column;
}

.dropdown-item {
    padding: 10px 15px;
    /* color: #565656ff; */
    text-decoration: none;
    background-color: #bebcbcff;
}

.dropdown-item:hover {
    /* background-color: #302f2fff; */
}

.dropbtn {
    cursor: pointer;
}
.text-ticker {
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    background-color: #ff9900;
    color: #ffffff;
    /* color: white; */
    padding: 15px 40px 20px 10px;
    font-weight: bold;
    z-index: 1;
    white-space: nowrap;
    font-size: 24px;
    border-radius: 0px 70px 10px 0px;
}

/* ปรับขนาด font-size ของ text-ticker สำหรับหน้าจอขนาดเล็ก */
@media (max-width: 768px) {
    .text-ticker {
        font-size: 18px; /* ลดขนาด font-size */
        padding: 10px 20px 15px 5px; /* ลด padding */
    }
}

@media (max-width: 480px) {
    .text-ticker {
        font-size: 14px; /* ลดขนาด font-size เพิ่มเติม */
        padding: 8px 15px 10px 5px; /* ลด padding เพิ่มเติม */
    }
}
</style>

<div id="navbar-menu">
    <div class="container">
        <div class="over-menu">
            <?php foreach ($navbarItems as $item): ?>
                <?php if (isset($item['isDropdown']) && $item['isDropdown']): ?>
                    <div class="dropdown" onmouseleave="closeAllDropdowns()">
                        <a href="<?php echo $item['link']; ?>"
                           class="dropbtn"
                           onmouseenter="toggleDropdown('<?php echo $item['id']; ?>')">
                            <span data-translate="<?php echo $item['translate']; ?>" lang="th">
                                <?php echo $item['text']; ?>
                            </span>
                            <span class="dropdown-icon">
                                <i class="fas fa-caret-down"></i>
                            </span>
                        </a>

                        <div class="dropdown-content" id="<?php echo $item['id']; ?>">
                            <div class="dropdown-show">
                                <?php foreach ($dropdownItems[$item['id']] as $dropdownItem): ?>
                                    <a class="dropdown-item" href="<?php echo $dropdownItem['link']; ?>">
                                        <i class="<?php echo $dropdownItem['icon']; ?>"></i>
                                        <span data-translate="<?php echo $dropdownItem['translate']; ?>" lang="th">
                                            <?php echo $dropdownItem['text']; ?>
                                        </span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
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

<script>
let currentDropdown = null;

function toggleDropdown(id) {
    // ปิดอันเก่า
    if (currentDropdown && currentDropdown !== id) {
        const prev = document.getElementById(currentDropdown);
        if (prev) prev.style.display = "none";
    }

    // เปิดอันใหม่
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
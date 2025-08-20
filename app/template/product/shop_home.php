<?php
// เริ่มการใช้งาน Session ต้องอยู่บรรทัดแรกสุดของไฟล์เสมอ
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/../../../lib/connect.php');
global $conn;

// 1. ตรวจสอบพารามิเตอร์ lang ใน URL และบันทึกใน Session
$supportedLangs = ['en', 'th', 'cn', 'jp', 'kr'];
if (isset($_GET['lang']) && in_array($_GET['lang'], $supportedLangs)) {
    $_SESSION['lang'] = $_GET['lang'];
}

// 2. กำหนดค่า lang จาก Session หรือค่าเริ่มต้น 'th'
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'th';


// ดึงข้อมูลทั้งหมดในทุกภาษาเพื่อความยืดหยุ่นในการแสดงผล
$sql = "SELECT
            dn.shop_id,
            dn.subject_shop,
            dn.subject_shop_en,
            dn.subject_shop_cn,
            dn.subject_shop_jp,
            dn.subject_shop_kr,
            dn.description_shop,
            dn.description_shop_en,
            dn.description_shop_cn,
            dn.description_shop_jp,
            dn.description_shop_kr,
            dn.content_shop,
            dn.content_shop_en,
            dn.content_shop_cn,
            dn.content_shop_jp,
            dn.content_shop_kr,
            dn.date_create,
            GROUP_CONCAT(dnc.file_name) AS file_name,
            GROUP_CONCAT(dnc.api_path) AS pic_path
        FROM
            dn_shop dn
        LEFT JOIN
            dn_shop_doc dnc ON dn.shop_id = dnc.shop_id
        WHERE
            dn.del = '0' AND
            dnc.del = '0' AND
            dnc.status = '1'
        GROUP BY dn.shop_id
        ORDER BY dn.date_create DESC";

$result = $conn->query($sql);
$boxesshop = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // ใช้ตัวแปรภาษาเพื่อเลือกคอลัมน์ที่ถูกต้อง
        $subject = $row['subject_shop' . ($lang !== 'th' ? '_' . $lang : '')];
        $description = $row['description_shop' . ($lang !== 'th' ? '_' . $lang : '')];
        $content = $row['content_shop' . ($lang !== 'th' ? '_' . $lang : '')];

        // ในกรณีที่ข้อมูลในภาษาที่เลือกเป็นค่าว่าง ให้ใช้ข้อมูลภาษาไทยเป็นค่าสำรอง
        $subject = !empty($subject) ? $subject : $row['subject_shop'];
        $description = !empty($description) ? $description : $row['description_shop'];
        $content = !empty($content) ? $content : $row['content_shop'];
        
        $iframeSrc = null;
        if (preg_match('/<iframe.*?src=["\'](.*?)["\'].*?>/i', $content, $matches)) {
            $iframeSrc = isset($matches[1]) ? $matches[1] : null;
        }

        $paths = explode(',', $row['pic_path']);
        $iframe = isset($iframeSrc) ? $iframeSrc : null;

        $boxesshop[] = [
            'id' => $row['shop_id'],
            'image' => $paths[0],
            'title' => $subject,
            'description' => $description,
            'iframe' => $iframe
        ];
    }
}
?>

<style>
    /* --- สไตล์สำหรับส่วนสินค้าที่ปรับปรุงใหม่ --- */
    .shop-wrapper-container {
        position: relative;
        max-width: 100%;
        margin: auto;
        /* เพิ่ม padding-left และ padding-right เพื่อให้เงาด้านข้างไม่ถูกตัด */
        /* padding-left: 50px;
        padding-right: 50px; */
        /* ลบ overflow: hidden ออกจากที่นี่ */
        /* overflow: hidden; */
    }

    .shop-scroll {
        display: flex;
        gap: 2rem;
        scroll-behavior: smooth;
        overflow-x: auto;
        padding-bottom: 1rem;
        scrollbar-width: none;
        -ms-overflow-style: none;
        /* เพิ่ม padding-top เพื่อป้องกันกล่องด้านบนที่โดนตัดเมื่อ hover */
        padding-top: 10px;
    }

    .shop-scroll::-webkit-scrollbar {
        display: none;
    }

    .shop-card {
        flex: 0 0 calc((100% - 6rem) / 4);
        height: auto;
    }

    .card {
        display: flex;
        flex-direction: column;
        height: 100%;
        border: none;
        border-radius: 6px;
        /* ย้าย overflow: hidden มาไว้ที่ .card แทน */
        overflow: hidden;
        background-color: #fff;
        transition: transform 0.4s ease-in-out, box-shadow 0.4s ease-in-out;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15), 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .card:hover {
        transform: translateY(-8px);
        box-shadow: 0 18px 50px rgba(0, 0, 0, 0.25), 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .card-image-wrapper {
        padding-top: 100%;
        position: relative;
        /* ลบ overflow: hidden ออกจากที่นี่ */
        border-radius: 6px;
    }

    .card-img-top {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .card-img-top:hover {
        /* ไม่ต้องมีสไตล์นี้ก็ได้ เพราะใช้ transform ที่ .card:hover แล้ว */
    }

    .card-body {
        padding: 15px;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        flex-grow: 1;
        min-height: 100px;
    }

    .card-title {
        font-weight: 600;
        margin-bottom: 5px;
        color: #555;
        font-size: 1.1rem;
        line-height: 1.3em;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .card-text {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #777;
        font-size: 0.9rem;
        margin-top: 0px;
        margin-bottom: 0px;
    }

    /* สไตล์ปุ่มเลื่อน */
    .scroll-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background-color: #77777738;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        font-size: 1.5rem;
        text-align: center;
        line-height: 40px;
        cursor: pointer;
        z-index: 5;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    .scroll-btn:hover {
        background-color: #77777738;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    }

    /* ปรับตำแหน่งปุ่มเลื่อนเพื่อให้ไม่ชนขอบและไม่บังเงา */
    .scroll-btn.left {
        left: 0;
    }

    .scroll-btn.right {
        right: 0;
    }

    /* ปรับจำนวนคอลัมน์ตามขนาดหน้าจอ (ไม่เปลี่ยนแปลง) */
    @media (max-width: 1200px) {
        .shop-card {
            flex: 0 0 calc((100% - 6rem) / 4);
        }
    }
    @media (max-width: 992px) {
        .shop-card {
            flex: 0 0 calc((100% - 4rem) / 3);
        }
    }
    @media (max-width: 768px) {
        .shop-card {
            flex: 0 0 calc((100% - 2rem) / 2);
        }
    }
    @media (max-width: 576px) {
        .shop-card {
            flex: 0 0 90%;
        }
    }
    .content-sticky {
        padding-bottom: 0px;
        background-color: #ffffff;

        /* เพิ่มโค้ด 2 บรรทัดนี้เพื่อจัดให้อยู่กึ่งกลาง */
        display: flex;
        justify-content: center;
    }

</style>

<script>
function scrollshop(direction) {
    const box = document.getElementById('shop-scroll-box');

    // Find the width of the first card-wrapper (one product box)
    const cardWidth = document.querySelector('.shop-card').offsetWidth + 32; // 32px is the 2rem gap between boxes

    if (direction === 'left') {
        box.scrollLeft -= cardWidth * 4;
    } else {
        box.scrollLeft += cardWidth * 4;
    }
}
</script>

<div class="shop-wrapper-container">
    <div class="scroll-btn left" onclick="scrollshop('left')">&#10094;</div>
    <div class="scroll-btn right" onclick="scrollshop('right')">&#10095;</div>

    <div style="overflow: hidden;">
        <div class="shop-scroll" id="shop-scroll-box">
            <?php foreach ($boxesshop as $box): ?>
                <div class="shop-card">
                    <a href="shop_detail.php?id=<?= urlencode(base64_encode($box['id'])) ?>&lang=<?= htmlspecialchars($lang) ?>" class="text-decoration-none text-dark">
                        <div class="card">
                            <?php if(empty($box['image'])): ?>
                                <iframe frameborder="0" src="<?= htmlspecialchars($box['iframe']) ?>" width="100%" height="200px" class="note-video-clip" ></iframe>
                            <?php else: ?>
                                <div class="card-image-wrapper">
                                    <img src="<?= htmlspecialchars($box['image']) ?>" class="card-img-top" alt="สินค้า <?= htmlspecialchars($box['title']) ?>">
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h6 class="card-title"><?= htmlspecialchars($box['title']) ?></h6>
                                <p class="card-text"><?= htmlspecialchars($box['description']) ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php
require_once(__DIR__ . '/../../../lib/connect.php');
global $conn;

$sql = "SELECT 
            dn.shop_id, 
            dn.subject_shop, 
            dn.description_shop,
            dn.content_shop, 
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
        ORDER BY dn.date_create DESC
        LIMIT 5";

$result = $conn->query($sql);
$boxesshop = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {

        $content = $row['content_shop'];
        $iframeSrc = null;
        if (preg_match('/<iframe.*?src=["\'](.*?)["\'].*?>/i', $content, $matches)) {
            $iframeSrc = isset($matches[1]) ? explode(',', $matches[1]) : null;
        }

        $paths = explode(',', $row['pic_path']);
        $files = explode(',', $row['file_name']);
        $iframe = isset($iframeSrc[0]) ? $iframeSrc[0] : null;

        $boxesshop[] = [
            'id' => $row['shop_id'],
            'image' =>  $paths[0],
            'title' => $row['subject_shop'],
            'description' => $row['description_shop'],
            'iframe' => $iframe
        ];
    }
}
?>

<style>
.shop-wrapper-container {
    position: relative;
    max-width: 1280px; /* เดิม 960 → เพิ่มเป็น 1280px พอดี 4 กล่อง */
    margin: auto;
    overflow: hidden;
    padding: 0 40px;
}

.shop-scroll {
    display: flex;
    gap: 1rem;
    scroll-behavior: smooth;
    overflow-x: auto;
    padding-bottom: 1rem;
    scrollbar-width: none;
}
.shop-scroll::-webkit-scrollbar {
    display: none;
}

.shop-card {
    flex: 0 0 300px;
    max-width: 300px;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.card {
    display: flex;
    flex-direction: column;
    height: 100%;
    border: 1px solid #ddd;
    border-radius: 6px;
    overflow: hidden;
    background-color: #fff;
}

.card-image-wrapper {
    height: 200px;
    overflow: hidden;
}

.card-img-top {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.card-body {
    padding: 10px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.card-title {
    font-weight: bold;
    margin-bottom: 8px;
    color: #333;
}

/* ✅ จำกัดข้อความแค่ 4 บรรทัด */
.card-text {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    color: #555;
    font-size: 0.9rem;
}

/* ปุ่มเลื่อนซ้ายขวา */
.scroll-btn {
    position: absolute;
    top: 40%;
    background-color: white;
    border: 1px solid #ccc;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    text-align: center;
    line-height: 30px;
    cursor: pointer;
    z-index: 5;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
}

.scroll-btn.left {
    left: 5px;
}

.scroll-btn.right {
    right: 5px;
}

</style>



<script>
function scrollshop(direction) {
    const box = document.getElementById('shop-scroll-box');
    const scrollAmount = 320; // 300px ความกว้าง + 20px ช่องว่าง
    if (direction === 'left') {
        box.scrollLeft -= scrollAmount;
    } else {
        box.scrollLeft += scrollAmount;
    }
}
function scrollshop(direction) {
    const box = document.getElementById('shop-scroll-box');
    const scrollAmount = 320; // 300px + 20px gap
    if (direction === 'left') {
        box.scrollLeft -= scrollAmount;
    } else {
        box.scrollLeft += scrollAmount;
    }
}

// ✅ ฟังก์ชัน auto scroll แบบวนลูป
function startAutoScroll() {
    const box = document.getElementById('shop-scroll-box');
    const scrollAmount = 400;
    const scrollMax = box.scrollWidth - box.clientWidth;

    setInterval(() => {
        // ถ้าเลื่อนไปจนสุด → กลับไปเริ่ม
        if (box.scrollLeft >= scrollMax) {
            box.scrollLeft = 0;
        } else {
            box.scrollLeft += scrollAmount;
        }
    }, 3000); // ทุก 3 วินาที
}

// ✅ เริ่ม auto scroll ทันทีเมื่อโหลดหน้า
window.addEventListener('DOMContentLoaded', startAutoScroll);
</script>

<div class="shop-wrapper-container">
    <div class="scroll-btn left" onclick="scrollshop('left')">&#10094;</div>
    <div class="scroll-btn right" onclick="scrollshop('right')">&#10095;</div>

    <div class="shop-scroll" id="shop-scroll-box">
        <?php foreach ($boxesshop as $box): ?>
            <div class="shop-card">
                <div class="card">
                    <a href="shop_detail.php?id=<?= urlencode(base64_encode($box['id'])) ?>" class="text-decoration-none text-dark">
                        <?php if(empty($box['image'])): ?>
                            <iframe frameborder="0" src="<?= $box['iframe'] ?>" width="100%" height="200px" class="note-video-clip"></iframe>
                        <?php else: ?>
                            <div class="card-image-wrapper">
                                <img src="<?= $box['image'] ?>" class="card-img-top" alt="ข่าว <?= htmlspecialchars($box['title']) ?>">
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h6 class="card-title"><?= htmlspecialchars($box['title']) ?></h6>
                            <p class="card-text"><?= htmlspecialchars($box['description']) ?></p>
                        </div>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>







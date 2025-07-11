<?php
require_once(__DIR__ . '/../../../lib/connect.php');
global $conn;

$sql = "SELECT 
            dn.Blog_id, 
            dn.subject_Blog, 
            dn.description_Blog,
            dn.content_Blog, 
            dn.date_create, 
            GROUP_CONCAT(dnc.file_name) AS file_name,
            GROUP_CONCAT(dnc.api_path) AS pic_path
        FROM 
            dn_blog dn
        LEFT JOIN 
            dn_blog_doc dnc ON dn.Blog_id = dnc.Blog_id
        WHERE 
            dn.del = '0' AND
            dnc.del = '0' AND
            dnc.status = '1'
        GROUP BY dn.Blog_id 
        ORDER BY dn.date_create DESC
        LIMIT 5";

$result = $conn->query($sql);
$boxesBlog = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {

        $content = $row['content_Blog'];
        $iframeSrc = null;
        if (preg_match('/<iframe.*?src=["\'](.*?)["\'].*?>/i', $content, $matches)) {
            $iframeSrc = isset($matches[1]) ? explode(',', $matches[1]) : null;
        }

        $paths = explode(',', $row['pic_path']);
        $files = explode(',', $row['file_name']);
        $iframe = isset($iframeSrc[0]) ? $iframeSrc[0] : null;

        $boxesBlog[] = [
            'id' => $row['Blog_id'],
            'image' =>  $paths[0],
            'title' => $row['subject_Blog'],
            'description' => $row['description_Blog'],
            'iframe' => $iframe
        ];
    }
}
?>

<style>
.Blog-wrapper-container {
    position: relative;
    max-width: 960px;
    margin: auto;
    overflow: hidden;
    padding: 0 40px;
}

.Blog-scroll {
    display: flex;
    gap: 1rem;
    scroll-behavior: smooth;
    overflow-x: auto;
    padding-bottom: 1rem;
    scrollbar-width: none;
}
.Blog-scroll::-webkit-scrollbar {
    display: none;
}

.Blog-card {
    flex: 0 0 300px;
    max-width: 300px;
}

/* ✅ แก้ card ไม่ fix ความสูง */
.card {
    display: flex;
    flex-direction: column;
    height: auto;
    border: 1px solid #ddd;
    border-radius: 6px;
    overflow: hidden;
}

/* ✅ wrapper สำหรับรูปภาพ */
.card-image-wrapper {
    height: 200px;
    overflow: hidden;
}
.card-img-top {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* ✅ ปล่อย card-body ยืดหยุ่น */
.card-body {
    padding: 10px;
}

/* ✅ ปุ่มเลื่อน */
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
    box-shadow: 0 0 5px rgba(0,0,0,0.2);
}
.scroll-btn.left {
    left: 5px;
}
.scroll-btn.right {
    right: 5px;
}
</style>



<script>
function scrollBlog(direction) {
    const box = document.getElementById('Blog-scroll-box');
    const scrollAmount = 320; // 300px ความกว้าง + 20px ช่องว่าง
    if (direction === 'left') {
        box.scrollLeft -= scrollAmount;
    } else {
        box.scrollLeft += scrollAmount;
    }
}
function scrollBlog(direction) {
    const box = document.getElementById('Blog-scroll-box');
    const scrollAmount = 320; // 300px + 20px gap
    if (direction === 'left') {
        box.scrollLeft -= scrollAmount;
    } else {
        box.scrollLeft += scrollAmount;
    }
}

// ✅ ฟังก์ชัน auto scroll แบบวนลูป
function startAutoScroll() {
    const box = document.getElementById('Blog-scroll-box');
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
<div class="container py-4">
    <div class="row">
        <?php foreach (array_slice($boxesBlog, 0, 4) as $box): ?>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100">
                    <a href="Blog_detail.php?id=<?= urlencode(base64_encode($box['id'])) ?>" class="text-decoration-none text-dark">
                        <?php if (empty($box['image'])): ?>
                            <iframe frameborder="0" src="<?= $box['iframe'] ?>" width="100%" height="180px" class="note-video-clip"></iframe>
                        <?php else: ?>
                            <img src="<?= $box['image'] ?>" class="card-img-top" alt="ข่าว <?= htmlspecialchars($box['title']) ?>" style="height: 180px; object-fit: cover;">
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


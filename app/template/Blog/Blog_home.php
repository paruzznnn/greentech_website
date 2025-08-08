<?php
require_once(__DIR__ . '/../../../lib/connect.php');
global $conn;

// จำกัดจำนวนบทความที่ดึงมาแสดงเป็น 6 รายการ
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
        LIMIT 6";

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
/* --- สไตล์สำหรับส่วนบทความที่ปรับปรุงใหม่ให้แสดงผลแบบ Scroll --- */
.blog-wrapper-container {
    position: relative;
    max-width: 1500px;
    margin: auto;
    /* เพิ่ม padding เพื่อให้เงาแสดงผลครบ */
    padding: 10px 50px;
    /* ลบ overflow: hidden; ออกจากที่นี่ */
}

.blog-scroll {
    display: flex;
    gap: 1rem; /* ระยะห่างระหว่างการ์ด */
    scroll-behavior: smooth;
    overflow-x: auto;
    padding-bottom: 1rem;
    scrollbar-width: none;
    -ms-overflow-style: none;
    /* เพิ่ม padding-top เพื่อป้องกันส่วนบนของกล่องถูกตัดเมื่อ hover */
    padding-top: 10px;
}

.blog-scroll::-webkit-scrollbar {
    display: none;
}

/* ขนาดการ์ดใหม่ให้แสดง 3 ใบต่อหน้าจอขนาดใหญ่ */
.blog-card {
    flex: 0 0 calc(33.333% - 0.666rem); /* 100%/3 - (gap * 2/3) */
    max-width: calc(33.333% - 0.666rem);
    display: flex;
    flex-direction: column;
    height: auto;
}

@media (max-width: 992px) {
    .blog-card {
        flex: 0 0 calc(50% - 0.5rem);
        max-width: calc(50% - 0.5rem);
    }
}

@media (max-width: 768px) {
    .blog-card {
        flex: 0 0 100%;
        max-width: 100%;
    }
}

.card-premium-blog {
    border: none;
    border-radius: 6px;
    /* ย้าย overflow: hidden มาไว้ที่นี่แทน */
    overflow: hidden;
    background-color: #ffffff;
    color: #333;
    transition: transform 0.4s ease-in-out, box-shadow 0.4s ease-in-out;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1), 0 5px 15px rgba(0, 0, 0, 0.05);
    height: 100%;
    display: flex;
    flex-direction: column;
}

.card-premium-blog:hover {
    transform: translateY(-10px);
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.2), 0 10px 30px rgba(0, 0, 0, 0.1);
}

.blog-card-image-wrapper {
    height: 250px;
    /* ลบ overflow: hidden ออกจากที่นี่ */
    border-radius: 6px 6px 0 0;
}

.blog-card-img-top {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

/* ลบสไตล์การ hover นี้ออก เพราะจะทำให้ภาพถูกตัด */
/* .blog-card-img-top:hover {
    transform: scale(1.05);
} */

.blog-card-body {
    padding: 25px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.blog-card-title {
    font-weight: 700;
    margin-bottom: 8px;
    color: #555;
    font-size: 1.3rem;
    line-height: 1.4em;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

.blog-card-text {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    color: #555;
    font-size: 1rem;
    margin-top: 0px;
    margin-bottom: 0px;
}

.scroll-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background-color: white;
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
    background-color: #f0f0f0;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

.scroll-btn.left {
    left: 10px;
}

.scroll-btn.right {
    right: 10px;
}
</style>

<script>
function scrollBlog(direction) {
    const box = document.getElementById('blog-scroll-box');
    const scrollAmount = box.clientWidth / 3; // เลื่อนทีละ 1 การ์ด (33.333%)
    if (direction === 'left') {
        box.scrollLeft -= scrollAmount;
    } else {
        box.scrollLeft += scrollAmount;
    }
}
</script>

<div class="blog-wrapper-container">
    <div class="scroll-btn left" onclick="scrollBlog('left')">&#10094;</div>
    <div class="scroll-btn right" onclick="scrollBlog('right')">&#10095;</div>
    <div class="blog-scroll" id="blog-scroll-box">
        <?php foreach ($boxesBlog as $box): ?>
            <div class="blog-card">
                <a href="Blog_detail.php?id=<?= urlencode(base64_encode($box['id'])) ?>" class="text-decoration-none text-dark">
                    <div class="card-premium-blog h-100">
                        <?php if(empty($box['image'])): ?>
                            <iframe frameborder="0" src="<?= $box['iframe'] ?>" width="100%" height="250px" class="note-video-clip" style="border-radius: 20px 20px 0 0;"></iframe>
                        <?php else: ?>
                            <div class="blog-card-image-wrapper">
                                <img src="<?= $box['image'] ?>" class="blog-card-img-top" alt="บทความ <?= htmlspecialchars($box['title']) ?>">
                            </div>
                        <?php endif; ?>
                        <div class="blog-card-body">
                            <h6 class="blog-card-title"><?= htmlspecialchars($box['title']) ?></h6>
                            <p class="blog-card-text"><?= htmlspecialchars($box['description']) ?></p>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
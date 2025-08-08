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
    /* --- สไตล์สำหรับส่วนบทความที่ปรับปรุงใหม่ --- */
    .blog-wrapper-container {
        position: relative;
        max-width: 90%;
        margin: auto;
        /* เพิ่ม padding-left และ padding-right เพื่อให้เงาด้านข้างไม่ถูกตัด */
        padding-left: 50px;
        padding-right: 50px;
    }

    .blog-scroll {
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

    .blog-scroll::-webkit-scrollbar {
        display: none;
    }

    .blog-card {
        flex: 0 0 calc((100% - 4rem) / 3);
        height: auto;
    }

    .card {
        display: flex;
        flex-direction: column;
        height: 100%;
        border: none;
        border-radius: 6px;
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

    /* ปรับจำนวนคอลัมน์ตามขนาดหน้าจอ */
    @media (max-width: 1200px) {
        .blog-card {
            flex: 0 0 calc((100% - 4rem) / 3);
        }
    }
    @media (max-width: 992px) {
        .blog-card {
            flex: 0 0 calc((100% - 2rem) / 2);
        }
    }
    @media (max-width: 768px) {
        .blog-card {
            flex: 0 0 calc((100% - 2rem) / 2);
        }
    }
    @media (max-width: 576px) {
        .blog-card {
            flex: 0 0 90%;
        }
    }
</style>

<script>
function scrollBlog(direction) {
    const box = document.getElementById('blog-scroll-box');
    
    // หาความกว้างของ card-wrapper ตัวแรก (กล่องสินค้า 1 กล่อง)
    const cardWidth = document.querySelector('.blog-card').offsetWidth + 32; // 32px คือ 2rem ที่เป็น gap ระหว่างกล่อง
    
    if (direction === 'left') {
        box.scrollLeft -= cardWidth * 3;
    } else {
        box.scrollLeft += cardWidth * 3;
    }
}
</script>

<div class="blog-wrapper-container">
    <div class="scroll-btn left" onclick="scrollBlog('left')">&#10094;</div>
    <div class="scroll-btn right" onclick="scrollBlog('right')">&#10095;</div>

    <div style="overflow: hidden;">
        <div class="blog-scroll" id="blog-scroll-box">
            <?php foreach ($boxesBlog as $box): ?>
                <div class="blog-card">
                    <a href="Blog_detail.php?id=<?= urlencode(base64_encode($box['id'])) ?>" class="text-decoration-none text-dark">
                        <div class="card">
                            <?php if(empty($box['image'])): ?>
                                <iframe frameborder="0" src="<?= $box['iframe'] ?>" width="100%" height="200px" class="note-video-clip" ></iframe>
                            <?php else: ?>
                                <div class="card-image-wrapper">
                                    <img src="<?= $box['image'] ?>" class="card-img-top" alt="บทความ <?= htmlspecialchars($box['title']) ?>">
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
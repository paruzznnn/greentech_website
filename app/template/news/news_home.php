<?php
// เริ่มการใช้งาน Session ต้องอยู่บรรทัดแรกสุดของไฟล์
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/../../../lib/connect.php');
global $conn;

// --- ส่วนที่แก้ไข: จัดการภาษาด้วย Session ---
// 1. ตรวจสอบพารามิเตอร์ lang ใน URL และบันทึกใน Session
$supportedLangs = ['en', 'cn', 'jp', 'kr'];
if (isset($_GET['lang']) && in_array($_GET['lang'], $supportedLangs)) {
    $_SESSION['lang'] = $_GET['lang'];
}

// 2. กำหนดค่า lang จาก Session หรือค่าเริ่มต้น 'th'
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'th';
// --- สิ้นสุดส่วนที่แก้ไข ---

// --- กำหนดชื่อคอลัมน์ตามภาษาที่เลือก ---
$subjectCol = 'subject_news' . ($lang !== 'th' ? '_' . $lang : '');
$descriptionCol = 'description_news' . ($lang !== 'th' ? '_' . $lang : '');
$contentCol = 'content_news' . ($lang !== 'th' ? '_' . $lang : '');

// --- MODIFIED: Select all four language columns for news content and ADD 'kr' columns ---
$sql = "SELECT 
            dn.news_id, 
            dn.subject_news, 
            dn.subject_news_en, 
            dn.subject_news_cn,
            dn.subject_news_jp,
            dn.subject_news_kr,
            dn.description_news,
            dn.description_news_en,
            dn.description_news_cn,
            dn.description_news_jp,
            dn.description_news_kr,
            dn.content_news,
            dn.content_news_en,
            dn.content_news_cn,
            dn.content_news_jp,
            dn.content_news_kr,
            dn.date_create, 
            GROUP_CONCAT(dnc.file_name) AS file_name,
            GROUP_CONCAT(dnc.api_path) AS pic_path
        FROM 
            dn_news dn
        LEFT JOIN 
            dn_news_doc dnc ON dn.news_id = dnc.news_id
        WHERE 
            dn.del = '0' AND
            dnc.del = '0' AND
            dnc.status = '1'
        GROUP BY dn.news_id 
        ORDER BY dn.date_create DESC
        LIMIT 5";

$result = $conn->query($sql);
$boxesNews = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $content = $row[$contentCol] ?: $row['content_news'];
        $title = $row[$subjectCol] ?: $row['subject_news'];
        $description = $row[$descriptionCol] ?: $row['description_news'];

        $iframeSrc = null;
        if (preg_match('/<iframe.*?src=["\'](.*?)["\'].*?>/i', $content, $matches)) {
            $iframeSrc = isset($matches[1]) ? explode(',', $matches[1]) : null;
        }

        $paths = !empty($row['pic_path']) ? explode(',', $row['pic_path']) : [];
        $iframe = isset($iframeSrc[0]) ? $iframeSrc[0] : null;

        $boxesNews[] = [
            'id' => $row['news_id'],
            'image' => !empty($paths) ? $paths[0] : null,
            'title' => $title,
            'description' => $description,
            'iframe' => $iframe
        ];
    }
}
?>

<style>
    /* ... (CSS styles remain the same) ... */
    .card-premium {
        border: none;
        border-radius: 6px;
        overflow: hidden;
        background-color: #ffffff;
        color: #333;
        transition: transform 0.4s ease-in-out, box-shadow 0.4s ease-in-out;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15), 0 5px 15px rgba(0, 0, 0, 0.05);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .card-premium:hover {
        transform: translateY(-8px);
        box-shadow: 0 18px 50px rgba(0, 0, 0, 0.25), 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .sub-news-image-wrapper {
        position: relative;
        overflow: hidden;
        border-radius: 6px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.4s ease-in-out;
    }
    .news-box-title {
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #555;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .sub-news-image-wrapper:hover {
        transform: scale(1.05);
    }

    .sub-news-img {
        width: 100%;
        height: 170px;
        object-fit: cover;
        display: block;
        border-radius: 6px;
        transition: transform 0.4s ease-in-out;
    }

    .card-img-top {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 6px 6px 0 0;
    }

    .card-body.sub-news {
        padding: 10px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .main-news-description {
        font-size: 1rem;
        color: #666;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ปรับปรุงสไตล์นี้เพื่อจำกัดข้อความให้แสดงแค่ 3 บรรทัด */
    .sub-news-title {
        display: -webkit-box;
        -webkit-line-clamp: 3; /* เปลี่ยนจาก 2 เป็น 3 */
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 4px;
        line-height: 1.4em;
        color: #222;
    }

    .news-small-text {
        font-size: 0.85rem;
        color: #888;
    }

    #mainNewsCarousel .carousel-item img {
        height: 450px;
        max-height: 450px;
        width: 100%;
        object-fit: cover;
        border-radius: 6px 6px 0 0;
    }

    .p-3.bg-light {
        background-color: #f5f6f8 !important;
        border-radius: 0 0 6px 6px;
        padding: 20px !important;
    }

    /* === สไตล์ที่ปรับปรุงใหม่สำหรับปุ่มเลื่อน carousel === */
    .carousel-control-prev,
    .carousel-control-next {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background-color: #2525256d; /* เปลี่ยนเป็นสีขาว */
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
        opacity: 1; /* กำหนดให้ปุ่มมองเห็นได้ตลอด */
    }

    .carousel-control-prev:hover,
    .carousel-control-next:hover {
        background-color: #3b3b3b4a;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        opacity: 1;
    }
    
    .carousel-control-prev {
        left: -20px; /* ปรับตำแหน่งจากซ้าย */
    }

    .carousel-control-next {
        right: -20px; /* ปรับตำแหน่งจากขวา */
    }

    /* สไตล์ไอคอนลูกศร */
    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        background-size: 100%, 100%;
        width: 1.5rem; /* เพิ่มขนาดไอคอน */
        height: 1.5rem;
    }

    .carousel-control-prev-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z'/%3e%3c/svg%3e");
    }

    .carousel-control-next-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
    }
</style>

<?php if (!empty($boxesNews)): ?>
    <div class="row g-4 d-flex align-items-stretch">
        <div class="col-md-8 d-flex flex-column">
            <div id="mainNewsCarousel" class="carousel slide h-100 d-flex flex-column" data-bs-ride="carousel">
                <div class="card-premium">
                    <div class="carousel-inner flex-grow-1">
                        <?php foreach (array_slice($boxesNews, 0, 4) as $i => $box): ?>
                            <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                                <a href="news_detail.php?id=<?= urlencode(base64_encode($box['id'])) ?>&lang=<?= htmlspecialchars($lang) ?>" class="text-decoration-none text-dark">
                                    <img src="<?= htmlspecialchars($box['image']) ?>" class="d-block w-100" style="border-radius: 6px 6px 0 0; height: 450px; object-fit: cover;">
                                </a>
                                <div class="p-3 bg-light">
                                    <h4 class="news-box-title"><?= htmlspecialchars($box['title']) ?></h4>
                                    <p class="mb-0 text-muted main-news-description"><?= htmlspecialchars($box['description']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#mainNewsCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden"><?= match ($lang) { 'cn' => '上一页', 'jp' => '前へ', 'en' => 'Previous', 'kr' => '이전', default => 'ก่อนหน้า', }; ?></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#mainNewsCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden"><?= match ($lang) { 'cn' => '下一页', 'jp' => '次へ', 'en' => 'Next', 'kr' => '다음', default => 'ถัดไป', }; ?></span>
                </button>
            </div>
        </div>

        <div class="col-md-4 d-flex flex-column">
            <div class="row row-cols-1 row-cols-md-2 g-4 h-100">
                <?php foreach (array_slice($boxesNews, offset: 1) as $box): ?>
                    <div class="col d-flex">
                        <a href="news_detail.php?id=<?= urlencode(base64_encode($box['id'])) ?>&lang=<?= htmlspecialchars($lang) ?>" class="text-decoration-none text-dark w-100">
                            <div class="card-premium p-0 d-flex flex-column">
                                <div class="sub-news-image-wrapper flex-shrink-0">
                                    <img src="<?= htmlspecialchars($box['image']) ?>" class="sub-news-img">
                                </div>
                                <div class="card-body sub-news d-flex flex-column">
                                    <h6 class="sub-news-title flex-grow-1"><?= htmlspecialchars($box['title']) ?></h6>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
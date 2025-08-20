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

$perPage = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// 3. กำหนดชื่อคอลัมน์จากค่า lang ที่บันทึกไว้ใน Session
$subject_col = 'subject_project' . ($lang !== 'th' ? '_' . $lang : '');
$description_col = 'description_project' . ($lang !== 'th' ? '_' . $lang : '');

// ใช้ตัวแปรภาษาในการค้นหาและแสดงผล
$totalQuery = "SELECT COUNT(DISTINCT dn.project_id) as total
               FROM dn_project dn
               LEFT JOIN dn_project_doc dnc ON dn.project_id = dnc.project_id
               WHERE dn.del = '0'";
if ($searchQuery) {
    $totalQuery .= " AND dn.{$subject_col} LIKE '%" . $conn->real_escape_string($searchQuery) . "%'";
}
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $perPage);

$sql = "SELECT
            dn.project_id,
            dn.subject_project,
            dn.subject_project_en,
            dn.subject_project_cn,
            dn.subject_project_jp,
            dn.subject_project_kr,
            dn.description_project,
            dn.description_project_en,
            dn.description_project_cn,
            dn.description_project_jp,
            dn.description_project_kr,
            dn.content_project,
            dn.date_create,
            GROUP_CONCAT(DISTINCT dnc.file_name) AS file_name,
            GROUP_CONCAT(DISTINCT dnc.api_path) AS pic_path
        FROM
            dn_project dn
        LEFT JOIN
            dn_project_doc dnc ON dn.project_id = dnc.project_id
                                 AND dnc.del = '0'
                                 AND dnc.status = '1'
        WHERE
            dn.del = '0'";

if ($searchQuery) {
    $sql .= " AND dn.{$subject_col} LIKE '%" . $conn->real_escape_string($searchQuery) . "%'";
}

$sql .= "
GROUP BY dn.project_id
ORDER BY dn.date_create DESC
LIMIT $perPage OFFSET $offset";

$result = $conn->query($sql);

$boxesNews = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // ดึงข้อมูลตามภาษาที่เลือก และใช้ภาษาไทยเป็นค่าสำรองหากข้อมูลภาษานั้นว่าง
        $title = $row['subject_project' . ($lang !== 'th' ? '_' . $lang : '')];
        $description = $row['description_project' . ($lang !== 'th' ? '_' . $lang : '')];

        $title = !empty($title) ? $title : $row['subject_project'];
        $description = !empty($description) ? $description : $row['description_project'];
        
        $content = $row['content_project'];
        
        $iframeSrc = null;
        if (preg_match('/<iframe.*?src=["\'](.*?)["\'].*?>/i', $content, $matches)) {
            $iframeSrc = isset($matches[1]) ? explode(',', $matches[1]) : null;
        }

        $paths = !empty($row['pic_path']) ? explode(',', $row['pic_path']) : [];
        $files = !empty($row['file_name']) ? explode(',', $row['file_name']) : [];

        $iframe = isset($iframeSrc[0]) ? $iframeSrc[0] : null;

        $boxesNews[] = [
            'id' => $row['project_id'],
            'image' => !empty($paths) ? $paths[0] : null,
            'date_time' => $row['date_create'],
            'title' => $title,
            'description' => $description,
            'iframe' => $iframe
        ];
    }
} else {
    // แสดงข้อความตามภาษาที่เลือก
    $noResultsText = [
        'en' => 'No project found.',
        'cn' => '未找到项目。',
        'jp' => 'プロジェクトが見つかりません。',
        'kr' => '프로젝트를 찾을 수 없습니다.',
        'th' => 'ไม่พบโปรเจกต์'
    ];
    echo $noResultsText[$lang];
}
?>
<div style="display: flex; justify-content: space-between;">
    <div>
    </div>
    <div>
        <form method="GET" action="">
            <input type="hidden" name="lang" value="<?php echo htmlspecialchars($lang); ?>">
            <div class="input-group">
                <?php
                $placeholderText = [
                    'en' => 'Search project...',
                    'cn' => '搜索项目...',
                    'jp' => 'プロジェクトを検索...',
                    'kr' => '프로젝트 검색...',
                    'th' => 'ค้นหาโปรเจกต์...'
                ];
                ?>
                <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($searchQuery); ?>" placeholder="<?php echo $placeholderText[$lang]; ?>">
                <button class="btn-search" type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>
    </div>
</div>
<div class="content-news">
    <?php foreach ($boxesNews as $index => $box): ?>
        <div class="box-news">
            <div class="box-image">
                <?php
                    $encodedId = urlencode(base64_encode($box['id']));
                    // ส่งค่า lang ไปยังหน้า detail ด้วย
                    $detailUrl = "project_detail.php?id=" . $encodedId . "&lang=" . htmlspecialchars($lang);
                ?>
                <a href="<?php echo $detailUrl; ?>" class="text-news">
                    <?php
                    if(!empty($box['iframe'])){
                        echo '<iframe frameborder="0" src="' . $box['iframe'] . '" width="100%" height="100%" class="note-video-clip"></iframe>';
                    } else if (!empty($box['image'])){
                        echo '<img src="' . $box['image'] . '" alt="Image for ' . htmlspecialchars($box['title']) . '">';
                    } else {
                        // แสดงภาพ placeholder หากไม่มีภาพ
                        echo '<img src="path/to/default/project_placeholder.jpg" alt="No image available">';
                    }
                    ?>
                </a>
            </div>
            <div class="box-content">
                <a href="<?php echo $detailUrl; ?>" class="text-news">
                    <h5 class="line-clamp"><?php echo htmlspecialchars($box['title']); ?></h5>
                    <p class="line-clamp"><?php echo htmlspecialchars($box['description']); ?></p>
                </a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($searchQuery); ?>&lang=<?php echo htmlspecialchars($lang); ?>">
            <?php
            $prevText = [
                'en' => 'Previous',
                'cn' => '上一页',
                'jp' => '前へ',
                'kr' => '이전',
                'th' => 'ก่อนหน้า'
            ];
            echo $prevText[$lang];
            ?>
        </a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($searchQuery); ?>&lang=<?php echo htmlspecialchars($lang); ?>" <?php echo $i == $page ? 'class="active"' : ''; ?>>
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($searchQuery); ?>&lang=<?php echo htmlspecialchars($lang); ?>">
            <?php
            $nextText = [
                'en' => 'Next',
                'cn' => '下一页',
                'jp' => '次へ',
                'kr' => '다음',
                'th' => 'ถัดไป'
            ];
            echo $nextText[$lang];
            ?>
        </a>
    <?php endif; ?>
</div>
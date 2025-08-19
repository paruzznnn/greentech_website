<?php
$perPage = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// --- MODIFIED: Allow 'cn', 'jp', and 'kr' as a valid language option.
$lang = isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'cn', 'jp', 'kr']) ? $_GET['lang'] : 'th';

// สร้างชื่อคอลัมน์ตามภาษาที่เลือก
$subject_col = 'subject_project';
$description_col = 'description_project';

if ($lang === 'en') {
    $subject_col = 'subject_project_en';
    $description_col = 'description_project_en';
} elseif ($lang === 'cn') {
    $subject_col = 'subject_project_cn';
    $description_col = 'description_project_cn';
} elseif ($lang === 'jp') {
    $subject_col = 'subject_project_jp';
    $description_col = 'description_project_jp';
} elseif ($lang === 'kr') {
    $subject_col = 'subject_project_kr';
    $description_col = 'description_project_kr';
}

// --- MODIFIED: Ensure totalQuery also respects 'del' status and valid documents ---
$totalQuery = "SELECT COUNT(DISTINCT dn.project_id) as total
                FROM dn_project dn
                LEFT JOIN dn_project_doc dnc ON dn.project_id = dnc.project_id
                                                 AND dnc.del = '0'
                                                 AND dnc.status = '1'
                WHERE dn.del = '0'"; // Filter projects that are not deleted
if ($searchQuery) {
    // ใช้คอลัมน์ที่ถูกต้องสำหรับการค้นหาตามภาษา
    $totalQuery .= " AND dn.{$subject_col} LIKE '%" . $conn->real_escape_string($searchQuery) . "%'";
}

$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $perPage);

// --- MODIFIED: Main SQL query to correctly handle filtering and aggregation. Now includes 'cn', 'jp', and 'kr' columns. ---
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
            dn.del = '0'"; // Only select projects where del is 0

if ($searchQuery) {
    // ใช้คอลัมน์ที่ถูกต้องสำหรับการค้นหาตามภาษา
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

        // Use correct language columns for display
        $title = $row['subject_project'];
        $description = $row['description_project'];
        if ($lang === 'en') {
            $title = $row['subject_project_en'];
            $description = $row['description_project_en'];
        } elseif ($lang === 'cn') {
            $title = $row['subject_project_cn'];
            $description = $row['description_project_cn'];
        } elseif ($lang === 'jp') {
            $title = $row['subject_project_jp'];
            $description = $row['description_project_jp'];
        } elseif ($lang === 'kr') {
            $title = $row['subject_project_kr'];
            $description = $row['description_project_kr'];
        }

        $content = $row['content_project'];

        $iframeSrc = null;
        if (preg_match('/<iframe.*?src=["\'](.*?)["\'].*?>/i', $content, $matches)) {
            $iframeSrc = isset($matches[1]) ? explode(',', $matches[1]) : null;
        }

        // Handle cases where pic_path or file_name might be NULL if no valid documents
        $paths = !empty($row['pic_path']) ? explode(',', $row['pic_path']) : [];
        $files = !empty($row['file_name']) ? explode(',', $row['file_name']) : [];

        $iframe = isset($iframeSrc[0]) ? $iframeSrc[0] : null;

        $boxesNews[] = [
            'id' => $row['project_id'],
            'image' => !empty($paths) ? $paths[0] : null, // Set to null if no valid image path
            'date_time' => $row['date_create'],
            'title' => $title,
            'description' => $description,
            'iframe' => $iframe
        ];
    }
} else {
    // --- MODIFIED: Display message based on selected language.
    if ($lang === 'en') {
        echo "No project found.";
    } elseif ($lang === 'cn') {
        echo "未找到项目。";
    } elseif ($lang === 'jp') {
        echo "プロジェクトが見つかりません。";
    } elseif ($lang === 'kr') {
        echo "프로젝트를 찾을 수 없습니다."; // เพิ่ม kr
    } else {
        echo "ไม่พบโปรเจกต์";
    }
}
?>
<div style="display: flex; justify-content: space-between;">

    <div>
        </div>

    <div>
        <form method="GET" action="">
            <input type="hidden" name="lang" value="<?php echo htmlspecialchars($lang); ?>">
            <div class="input-group">
                <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($searchQuery); ?>" placeholder="<?php echo $lang === 'cn' ? '搜索项目...' : ($lang === 'jp' ? 'プロジェクトを検索...' : ($lang === 'kr' ? '프로젝트 검색...' : ($lang === 'en' ? 'Search project...' : 'ค้นหาโปรเจกต์...'))); ?>">
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
                        // Optionally, display a default placeholder image or leave empty
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
            <?php echo $lang === 'cn' ? '上一页' : ($lang === 'jp' ? '前へ' : ($lang === 'kr' ? '이전' : ($lang === 'en' ? 'Previous' : 'ก่อนหน้า'))); ?>
        </a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($searchQuery); ?>&lang=<?php echo htmlspecialchars($lang); ?>" <?php echo $i == $page ? 'class="active"' : ''; ?>>
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($searchQuery); ?>&lang=<?php echo htmlspecialchars($lang); ?>">
            <?php echo $lang === 'cn' ? '下一页' : ($lang === 'jp' ? '次へ' : ($lang === 'kr' ? '다음' : ($lang === 'en' ? 'Next' : 'ถัดไป'))); ?>
        </a>
    <?php endif; ?>
</div>
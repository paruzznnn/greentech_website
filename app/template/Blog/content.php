<?php
$perPage = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// Check for language preference, including 'kr'. Default to Thai.
$lang = 'th'; // Set a default value first
if (isset($_GET['lang'])) {
    if ($_GET['lang'] === 'en') {
        $lang = 'en';
    } elseif ($_GET['lang'] === 'cn') {
        $lang = 'cn';
    } elseif ($_GET['lang'] === 'jp') {
        $lang = 'jp';
    } elseif ($_GET['lang'] === 'kr') { // Added Korean language check
        $lang = 'kr';
    }
}

$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// --- MODIFIED: Adjust column names based on the selected language for display, including 'kr' ---
$subjectCol = 'subject_blog';
$descriptionCol = 'description_blog';
$contentCol = 'content_blog';
if ($lang === 'en') {
    $subjectCol = 'subject_blog_en';
    $descriptionCol = 'description_blog_en';
    $contentCol = 'content_blog_en';
} elseif ($lang === 'cn') {
    $subjectCol = 'subject_blog_cn';
    $descriptionCol = 'description_blog_cn';
    $contentCol = 'content_blog_cn';
} elseif ($lang === 'jp') {
    $subjectCol = 'subject_blog_jp';
    $descriptionCol = 'description_blog_jp';
    $contentCol = 'content_blog_jp';
} elseif ($lang === 'kr') {
    $subjectCol = 'subject_blog_kr';
    $descriptionCol = 'description_blog_kr';
    $contentCol = 'content_blog_kr';
}

// --- MODIFIED: Ensure totalQuery also respects 'del' status and searches across all language columns including 'kr' ---
$totalQuery = "SELECT COUNT(DISTINCT dn.blog_id) as total
                FROM dn_blog dn
                WHERE dn.del = '0'"; // Filter blogs that are not deleted
if ($searchQuery) {
    // Search across Thai, English, Chinese, Japanese, and Korean subject columns
    $totalQuery .= " AND (dn.subject_blog LIKE '%" . $conn->real_escape_string($searchQuery) . "%'
                    OR dn.subject_blog_en LIKE '%" . $conn->real_escape_string($searchQuery) . "%'
                    OR dn.subject_blog_cn LIKE '%" . $conn->real_escape_string($searchQuery) . "%'
                    OR dn.subject_blog_jp LIKE '%" . $conn->real_escape_string($searchQuery) . "%'
                    OR dn.subject_blog_kr LIKE '%" . $conn->real_escape_string($searchQuery) . "%')";
}

$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $perPage);

// --- MODIFIED: Main SQL query to select all language columns for dynamic display, including 'kr' ---
$sql = "SELECT
            dn.blog_id,
            dn.subject_blog,
            dn.subject_blog_en,
            dn.subject_blog_cn,
            dn.subject_blog_jp,
            dn.subject_blog_kr,
            dn.description_blog,
            dn.description_blog_en,
            dn.description_blog_cn,
            dn.description_blog_jp,
            dn.description_blog_kr,
            dn.content_blog,
            dn.content_blog_en,
            dn.content_blog_cn,
            dn.content_blog_jp,
            dn.content_blog_kr,
            dn.date_create,
            GROUP_CONCAT(DISTINCT dnc.file_name) AS file_name,
            GROUP_CONCAT(DISTINCT dnc.api_path) AS pic_path
        FROM
            dn_blog dn
        LEFT JOIN
            dn_blog_doc dnc ON dn.blog_id = dnc.blog_id
                               AND dnc.del = '0'
                               AND dnc.status = '1'
        WHERE
            dn.del = '0'"; // Only select blogs where del is 0

if ($searchQuery) {
    // Search across all language subject columns, including 'kr'
    $sql .= "
    AND (dn.subject_blog LIKE '%" . $conn->real_escape_string($searchQuery) . "%' 
    OR dn.subject_blog_en LIKE '%" . $conn->real_escape_string($searchQuery) . "%'
    OR dn.subject_blog_cn LIKE '%" . $conn->real_escape_string($searchQuery) . "%'
    OR dn.subject_blog_jp LIKE '%" . $conn->real_escape_string($searchQuery) . "%'
    OR dn.subject_blog_kr LIKE '%" . $conn->real_escape_string($searchQuery) . "%')
    ";
}

$sql .= "
GROUP BY dn.blog_id
ORDER BY dn.date_create DESC
LIMIT $perPage OFFSET $offset";

$result = $conn->query($sql);

$boxesNews = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Use content in the selected language if available, otherwise fallback to Thai
        $displaySubject = $row[$subjectCol] ?: $row['subject_blog'];
        $displayDescription = $row[$descriptionCol] ?: $row['description_blog'];
        $displayContent = $row[$contentCol] ?: $row['content_blog'];

        $iframeSrc = null;
        if (preg_match('/<iframe.*?src=["\'](.*?)["\'].*?>/i', $displayContent, $matches)) {
            $iframeSrc = isset($matches[1]) ? explode(',', $matches[1]) : null;
        }

        // Handle cases where pic_path or file_name might be NULL if no valid documents
        $paths = !empty($row['pic_path']) ? explode(',', $row['pic_path']) : [];
        $files = !empty($row['file_name']) ? explode(',', $row['file_name']) : [];

        $iframe = isset($iframeSrc[0]) ? $iframeSrc[0] : null;

        $boxesNews[] = [
            'id' => $row['blog_id'],
            'image' => !empty($paths) ? $paths[0] : null,
            'date_time' => $row['date_create'],
            'title' => $displaySubject,
            'description' => $displayDescription,
            'iframe' => $iframe
        ];
    }
} else {
    echo ($lang === 'en' ? 'No blog found.' : ($lang === 'cn' ? '无博客内容。' : ($lang === 'jp' ? 'ブログが見つかりません。' : ($lang === 'kr' ? '블로그를 찾을 수 없습니다.' : 'ไม่พบบทความ'))));
}
?>

<div style="display: flex; justify-content: space-between;">
    <div></div>
    <div>
        <form method="GET" action="">
            <input type="hidden" name="lang" value="<?php echo htmlspecialchars($lang); ?>">
            <div class="input-group">
                <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($searchQuery); ?>" placeholder="<?= $lang === 'en' ? 'Search blog...' : ($lang === 'cn' ? '搜索文章...' : ($lang === 'jp' ? 'ブログを検索...' : ($lang === 'kr' ? '블로그 검색...' : 'ค้นหาบทความ...'))); ?>">
                <button class="btn-search" type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="content-news">
    <?php foreach ($boxesNews as $index => $box): ?>
        <div class="box-news">
            <div class="box-image">
                <?php $encodedId = urlencode(base64_encode($box['id'])); ?>
                <a href="Blog_detail.php?id=<?php echo $encodedId; ?>&lang=<?php echo htmlspecialchars($lang); ?>" class="text-news">
                    <?php if(!empty($box['iframe'])): ?>
                        <iframe frameborder="0" src="<?= htmlspecialchars($box['iframe']); ?>" width="100%" height="100%" class="note-video-clip"></iframe>
                    <?php elseif (!empty($box['image'])): ?>
                        <img src="<?= htmlspecialchars($box['image']); ?>" alt="Image for <?= htmlspecialchars($box['title']); ?>">
                    <?php else: ?>
                        <div style="width: 100%; height: 100%; background-color: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #ccc;">No Image</div>
                    <?php endif; ?>
                </a>
            </div>
            <div class="box-content">
                <a href="Blog_detail.php?id=<?php echo $encodedId; ?>&lang=<?php echo htmlspecialchars($lang); ?>" class="text-news">
                    <h5 class="line-clamp"><?= htmlspecialchars($box['title']); ?></h5>
                    <p class="line-clamp"><?= htmlspecialchars($box['description']); ?></p>
                </a>
            </div>
        </div>
    <?php endforeach; ?>
</div>


<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($searchQuery); ?>&lang=<?php echo htmlspecialchars($lang); ?>">
            <?= $lang === 'en' ? 'Previous' : ($lang === 'cn' ? '上一页' : ($lang === 'jp' ? '前へ' : ($lang === 'kr' ? '이전' : 'ก่อนหน้า'))); ?>
        </a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($searchQuery); ?>&lang=<?php echo htmlspecialchars($lang); ?>" <?php echo $i == $page ? 'class="active"' : ''; ?>>
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($searchQuery); ?>&lang=<?php echo htmlspecialchars($lang); ?>">
            <?= $lang === 'en' ? 'Next' : ($lang === 'cn' ? '下一页' : ($lang === 'jp' ? '次へ' : ($lang === 'kr' ? '다음' : 'ถัดไป'))); ?>
        </a>
    <?php endif; ?>
</div>
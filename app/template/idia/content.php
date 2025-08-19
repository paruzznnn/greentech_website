<?php
$perPage = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// --- ADDED: Check for language preference, default to Thai ---
$lang = isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'cn', 'jp']) ? $_GET['lang'] : 'th';
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// --- MODIFIED: Ensure totalQuery also respects 'del' status and searches English, Chinese, and Japanese columns too ---
$totalQuery = "SELECT COUNT(DISTINCT dn.idia_id) as total
                FROM dn_idia dn
                LEFT JOIN dn_idia_doc dnc ON dn.idia_id = dnc.idia_id
                                             AND dnc.del = '0'
                                             AND dnc.status = '1'
                WHERE dn.del = '0'"; // Filter idia entries that are not deleted
if ($searchQuery) {
    $totalQuery .= " AND (dn.subject_idia LIKE '%" . $conn->real_escape_string($searchQuery) . "%' OR dn.subject_idia_en LIKE '%" . $conn->real_escape_string($searchQuery) . "%' OR dn.subject_idia_cn LIKE '%" . $conn->real_escape_string($searchQuery) . "%' OR dn.subject_idia_jp LIKE '%" . $conn->real_escape_string($searchQuery) . "%')";
}

$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $perPage);

// --- MODIFIED: Main SQL query to correctly handle filtering and aggregation, including English, Chinese, and Japanese columns ---
$sql = "SELECT
            dn.idia_id,
            dn.subject_idia,
            dn.subject_idia_en,
            dn.subject_idia_cn,
            dn.subject_idia_jp,
            dn.description_idia,
            dn.description_idia_en,
            dn.description_idia_cn,
            dn.description_idia_jp,
            dn.content_idia,
            dn.content_idia_en,
            dn.content_idia_cn,
            dn.content_idia_jp,
            dn.date_create,
            GROUP_CONCAT(DISTINCT dnc.file_name) AS file_name,
            GROUP_CONCAT(DISTINCT dnc.api_path) AS pic_path
        FROM
            dn_idia dn
        LEFT JOIN
            dn_idia_doc dnc ON dn.idia_id = dnc.idia_id
                               AND dnc.del = '0'
                               AND dnc.status = '1'
        WHERE
            dn.del = '0'"; // Only select idia entries where del is 0

if ($searchQuery) {
    $sql .= "
    AND (dn.subject_idia LIKE '%" . $conn->real_escape_string($searchQuery) . "%' OR dn.subject_idia_en LIKE '%" . $conn->real_escape_string($searchQuery) . "%' OR dn.subject_idia_cn LIKE '%" . $conn->real_escape_string($searchQuery) . "%' OR dn.subject_idia_jp LIKE '%" . $conn->real_escape_string($searchQuery) . "%')
    ";
}

$sql .= "
GROUP BY dn.idia_id
ORDER BY dn.date_create DESC
LIMIT $perPage OFFSET $offset";

$result = $conn->query($sql);

$boxesidia = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {

        // --- MODIFIED: Select the correct language content ---
        $content = $row['content_idia'];
        if ($lang === 'en' && !empty($row['content_idia_en'])) {
            $content = $row['content_idia_en'];
        } elseif ($lang === 'cn' && !empty($row['content_idia_cn'])) {
            $content = $row['content_idia_cn'];
        } elseif ($lang === 'jp' && !empty($row['content_idia_jp'])) {
            $content = $row['content_idia_jp'];
        }

        $iframeSrc = null;
        if (preg_match('/<iframe.*?src=["\'](.*?)["\'].*?>/i', $content, $matches)) {
            $iframeSrc = isset($matches[1]) ? explode(',', $matches[1]) : null;
        }

        // Handle cases where pic_path or file_name might be NULL if no valid documents
        $paths = !empty($row['pic_path']) ? explode(',', $row['pic_path']) : [];
        $files = !empty($row['file_name']) ? explode(',', $row['file_name']) : [];

        $iframe = isset($iframeSrc[0]) ? $iframeSrc[0] : null;

        // --- MODIFIED: Select the correct language for title and description ---
        $title = $row['subject_idia'];
        $description = $row['description_idia'];
        if ($lang === 'en' && !empty($row['subject_idia_en'])) {
            $title = $row['subject_idia_en'];
        } elseif ($lang === 'cn' && !empty($row['subject_idia_cn'])) {
            $title = $row['subject_idia_cn'];
        } elseif ($lang === 'jp' && !empty($row['subject_idia_jp'])) {
            $title = $row['subject_idia_jp'];
        }

        if ($lang === 'en' && !empty($row['description_idia_en'])) {
            $description = $row['description_idia_en'];
        } elseif ($lang === 'cn' && !empty($row['description_idia_cn'])) {
            $description = $row['description_idia_cn'];
        } elseif ($lang === 'jp' && !empty($row['description_idia_jp'])) {
            $description = $row['description_idia_jp'];
        }

        $boxesidia[] = [
            'id' => $row['idia_id'],
            'image' => !empty($paths) ? $paths[0] : null, // Set to null if no valid image path
            'date_time' => $row['date_create'],
            'title' => $title,
            'description' => $description,
            'iframe' => $iframe
        ];
    }
} else {
    echo ($lang === 'cn' ? '无新闻内容。' : ($lang === 'jp' ? 'ニュースが見つかりません。' : ($lang === 'en' ? 'No news found.' : 'ไม่พบข่าว')));
}
?>
<div style="display: flex; justify-content: space-between;">

    <div>
        </div>

    <div>
        <form method="GET" action="">
            <div class="input-group">
                <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($searchQuery); ?>" placeholder="<?php echo $lang === 'en' ? 'Search idia...' : ($lang === 'cn' ? '搜索新闻...' : ($lang === 'jp' ? 'ニュースを検索...' : 'ค้นหาข่าว...')); ?>">
                <button class="btn-search" type="submit"><i class="fas fa-search"></i></button>
            </div>
            <input type="hidden" name="lang" value="<?php echo htmlspecialchars($lang); ?>">
        </form>
    </div>

</div>
<div class="content-news">
    <?php foreach ($boxesidia as $index => $box): ?>

        <div class="box-news">
            <div class="box-image">
                <?php
                    $encodedId = urlencode(base64_encode($box['id']));
                ?>
                <a href="idia_detail.php?id=<?php echo $encodedId; ?>&lang=<?php echo $lang; ?>" class="text-news">

                    <?php
                    // Display iframe if available, otherwise image if available, otherwise a placeholder/nothing
                    if(!empty($box['iframe'])){
                        echo '<iframe frameborder="0" src="' . $box['iframe'] . '" width="100%" height="100%" class="note-video-clip"></iframe>';
                    } else if (!empty($box['image'])){
                        echo '<img src="' . $box['image'] . '" alt="Image for ' . htmlspecialchars($box['title']) . '">';
                    } else {
                        // Optionally, display a default placeholder image or leave empty
                        echo '<img src="path/to/default/idia_placeholder.jpg" alt="No image available">';
                    }
                    ?>

                </a>
            </div>
            <div class="box-content">
                <a href="idia_detail.php?id=<?php echo $encodedId; ?>&lang=<?php echo $lang; ?>" class="text-news">
                    <h5 class="line-clamp"><?php echo htmlspecialchars($box['title']); ?></h5>
                    <p class="line-clamp"><?php echo htmlspecialchars($box['description']); ?></p>
                </a>
            </div>
        </div>
    <?php endforeach; ?>
</div>


<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($searchQuery); ?>&lang=<?php echo $lang; ?>">
          <?php echo $lang === 'en' ? 'Previous' : ($lang === 'cn' ? '上一页' : ($lang === 'jp' ? '前へ' : 'ก่อนหน้า')); ?>
        </a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($searchQuery); ?>&lang=<?php echo $lang; ?>" <?php echo $i == $page ? 'class="active"' : ''; ?>>
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($searchQuery); ?>&lang=<?php echo $lang; ?>">
          <?php echo $lang === 'en' ? 'Next' : ($lang === 'cn' ? '下一页' : ($lang === 'jp' ? '次へ' : 'ถัดไป')); ?>
        </a>
    <?php endif; ?>
</div>
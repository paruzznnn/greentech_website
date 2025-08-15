<?php
$perPage = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// Check for language preference, default to Thai
$lang = isset($_GET['lang']) && $_GET['lang'] === 'en' ? 'en' : 'th';

$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// --- MODIFIED: Ensure totalQuery also respects 'del' status and searches English columns too ---
$totalQuery = "SELECT COUNT(DISTINCT dn.Blog_id) as total
                FROM dn_blog dn
                LEFT JOIN dn_blog_doc dnc ON dn.Blog_id = dnc.Blog_id
                                            AND dnc.del = '0'
                                            AND dnc.status = '1'
                WHERE dn.del = '0'"; // Filter blogs that are not deleted
if ($searchQuery) {
    $totalQuery .= " AND (dn.subject_Blog LIKE '%" . $conn->real_escape_string($searchQuery) . "%' OR dn.subject_Blog_en LIKE '%" . $conn->real_escape_string($searchQuery) . "%')";
}

$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $perPage);

// --- MODIFIED: Main SQL query to correctly handle filtering and aggregation, including English columns ---
$sql = "SELECT
            dn.Blog_id,
            dn.subject_Blog,
            dn.subject_Blog_en,
            dn.description_Blog,
            dn.description_Blog_en,
            dn.content_Blog,
            dn.content_Blog_en,
            dn.date_create,
            GROUP_CONCAT(DISTINCT dnc.file_name) AS file_name,
            GROUP_CONCAT(DISTINCT dnc.api_path) AS pic_path
        FROM
            dn_blog dn
        LEFT JOIN
            dn_blog_doc dnc ON dn.Blog_id = dnc.Blog_id
                               AND dnc.del = '0'
                               AND dnc.status = '1'
        WHERE
            dn.del = '0'"; // Only select blogs where del is 0

if ($searchQuery) {
    $sql .= "
    AND (dn.subject_Blog LIKE '%" . $conn->real_escape_string($searchQuery) . "%' OR dn.subject_Blog_en LIKE '%" . $conn->real_escape_string($searchQuery) . "%')
    ";
}

$sql .= "
GROUP BY dn.Blog_id
ORDER BY dn.date_create DESC
LIMIT $perPage OFFSET $offset";

$result = $conn->query($sql);


$boxesNews = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {

        // Use English content if lang is 'en' and content is not empty, otherwise use Thai
        $content = ($lang === 'en' && !empty($row['content_Blog_en'])) ? $row['content_Blog_en'] : $row['content_Blog'];

        $iframeSrc = null;
        if (preg_match('/<iframe.*?src=["\'](.*?)["\'].*?>/i', $content, $matches)) {
            $iframeSrc = isset($matches[1]) ? explode(',', $matches[1]) : null;
        }

        // Handle cases where pic_path or file_name might be NULL if no valid documents
        $paths = !empty($row['pic_path']) ? explode(',', $row['pic_path']) : [];
        $files = !empty($row['file_name']) ? explode(',', $row['file_name']) : [];

        $iframe = isset($iframeSrc[0]) ? $iframeSrc[0] : null;

        $boxesNews[] = [
            'id' => $row['Blog_id'],
            'image' => !empty($paths) ? $paths[0] : null, // Set to null if no valid image path
            'date_time' => $row['date_create'],
            // Use English title and description if lang is 'en' and they are not empty, otherwise use Thai
            'title' => ($lang === 'en' && !empty($row['subject_Blog_en'])) ? $row['subject_Blog_en'] : $row['subject_Blog'],
            'description' => ($lang === 'en' && !empty($row['description_Blog_en'])) ? $row['description_Blog_en'] : $row['description_Blog'],
            'iframe' => $iframe
        ];
    }
} else {
    echo "No Blog found.";
}
?>
<div style="display: flex; justify-content: space-between;">

    <div>
        </div>

    <div>
        <form method="GET" action="">
            <div class="input-group">
                <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($searchQuery); ?>" placeholder="Search Blog...">
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
                ?>
                <a href="Blog_detail.php?id=<?php echo $encodedId; ?>" class="text-news">

                    <?php
                    // Display iframe if available, otherwise image if available, otherwise a placeholder/nothing
                    if(!empty($box['iframe'])){
                        echo '<iframe frameborder="0" src="' . $box['iframe'] . '" width="100%" height="100%" class="note-video-clip"></iframe>';
                    } else if (!empty($box['image'])){
                        echo '<img src="' . $box['image'] . '" alt="Image for ' . htmlspecialchars($box['title']) . '">';
                    } else {
                        // Optionally, display a default placeholder image or leave empty
                        echo '<img src="path/to/default/blog_placeholder.jpg" alt="No image available">';
                    }
                    ?>

                </a>
            </div>
            <div class="box-content">
                <a href="Blog_detail.php?id=<?php echo $encodedId; ?>" class="text-news">
                    <h5 class="line-clamp"><?php echo htmlspecialchars($box['title']); ?></h5>
                    <p class="line-clamp"><?php echo htmlspecialchars($box['description']); ?></p>
                </a>
            </div>
        </div>
    <?php endforeach; ?>
</div>


<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($searchQuery); ?>">Previous</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($searchQuery); ?>" <?php echo $i == $page ? 'class="active"' : ''; ?>>
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($searchQuery); ?>">Next</a>
    <?php endif; ?>
</div>
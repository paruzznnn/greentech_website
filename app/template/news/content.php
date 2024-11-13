<?php
// Pagination parameters
$perPage = 4;  // Number of items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;  // Get current page from query string
$offset = ($page - 1) * $perPage;  // Calculate the offset for SQL query

// Get search query if exists
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Prepare SQL query for counting total items based on search
$totalQuery = "SELECT COUNT(*) as total FROM dn_news dn";
if ($searchQuery) {
    $totalQuery .= " WHERE dn.subject_news LIKE '%" . $conn->real_escape_string($searchQuery) . "%'";
}

$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $perPage);

// Prepare SQL query for fetching news items for the current page
$sql = "SELECT 
            dn.news_id, 
            dn.subject_news, 
            dn.content_news, 
            dn.date_create, 
            dn.status, 
            dn.del,
            GROUP_CONCAT(dnc.file_name) AS file_name,
            GROUP_CONCAT(dnc.api_path) AS pic_path
        FROM 
            dn_news dn
        LEFT JOIN 
            dn_news_doc dnc ON dn.news_id = dnc.news_id";
        
if ($searchQuery) {
    $sql .= " WHERE dn.subject_news LIKE '%" . $conn->real_escape_string($searchQuery) . "%'";
}

$sql .= " GROUP BY dn.news_id LIMIT $perPage OFFSET $offset";

$result = $conn->query($sql);

$boxesNews = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $content = $row['content_news'];
        $paths = explode(',', $row['pic_path']);
        $files = explode(',', $row['file_name']);

        $boxesNews[] = [
            'id' => $row['news_id'],
            'image' =>  $paths[0],
            'date_time' => $row['date_create'],
            'title' => $row['subject_news'],
            'description' => ''
        ];
    }
} else {
    echo "No news found.";
}
?>
<div style="display: flex; justify-content: space-between;">

    <div>
        <p>Showing <?php echo $page; ?> to <?php echo $totalPages; ?> of <?php echo $totalItems; ?> entry</p>
    </div>

    <div>
        <form method="GET" action="">
            <div class="input-group">
                <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($searchQuery); ?>" placeholder="Search news...">
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
                <a href="news_detail.php?id=<?php echo $encodedId; ?>" class="text-news">
                    <img src="<?php echo $box['image']; ?>" alt="Image for <?php echo $box['title']; ?>">
                </a>
            </div>
            <div class="box-content">
                <a href="news_detail.php?id=<?php echo $encodedId; ?>" class="text-news">
                    <p class="line-clamp"><?php echo htmlspecialchars($box['title']); ?></p>
                </a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Pagination Links -->
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

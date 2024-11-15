<?php
$perPage = 4;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

$totalQuery = "SELECT COUNT(*) as total FROM dn_news dn";
if ($searchQuery) {
    $totalQuery .= " WHERE dn.subject_news LIKE '%" . $conn->real_escape_string($searchQuery) . "%'";
}

$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $perPage);

$sql = "SELECT 
            dn.news_id, 
            dn.subject_news, 
            dn.description_news,
            dn.content_news, 
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
            dnc.status = '1'"; // Ensure there's a space before "WHERE"

if ($searchQuery) {
    $sql .= "
    AND dn.subject_news LIKE '%" . $conn->real_escape_string($searchQuery) . "%'
    ";
}

$sql .= " 
GROUP BY dn.news_id 
ORDER BY dn.date_create DESC
LIMIT $perPage OFFSET $offset";

$result = $conn->query($sql);


$boxesNews = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {

        $content = $row['content_news'];

        $iframeSrc = null;
        if (preg_match('/<iframe.*?src=["\'](.*?)["\'].*?>/i', $content, $matches)) {
            // Ensure matches is not empty before accessing the value
            $iframeSrc = isset($matches[1]) ? explode(',', $matches[1]) : null;
        }

        $paths = explode(',', $row['pic_path']);
        $files = explode(',', $row['file_name']);

        // Check if $iframeSrc is set and not null before accessing it
        $iframe = isset($iframeSrc[0]) ? $iframeSrc[0] : null;

        $boxesNews[] = [
            'id' => $row['news_id'],
            'image' =>  $paths[0],
            'date_time' => $row['date_create'],
            'title' => $row['subject_news'],
            'description' => $row['description_news'],
            'iframe' => $iframe
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
                    
                    <?php
                    if(empty($box['image'])){
                        echo '<iframe frameborder="0" src="' . $box['iframe'] . '" width="100%" height="100%" class="note-video-clip"></iframe>';
                    }else{
                        echo '<img src="' . $box['image'] . '" alt="Image for ' . htmlspecialchars($box['title']) . '">';
                    }
                    ?>
                    
                </a>
            </div>
            <div class="box-content">
                <a href="news_detail.php?id=<?php echo $encodedId; ?>" class="text-news">
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

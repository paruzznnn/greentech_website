<?php
$perPage = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// --- MODIFIED: Ensure totalQuery also respects 'del' status and valid documents ---
$totalQuery = "SELECT COUNT(DISTINCT dn.shop_id) as total
               FROM dn_shop dn
               LEFT JOIN dn_shop_doc dnc ON dn.shop_id = dnc.shop_id
                                           AND dnc.del = '0'
                                           AND dnc.status = '1'
               WHERE dn.del = '0'"; // Filter shops that are not deleted
if ($searchQuery) {
    $totalQuery .= " AND dn.subject_shop LIKE '%" . $conn->real_escape_string($searchQuery) . "%'";
}

$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $perPage);

// --- MODIFIED: Main SQL query to correctly handle filtering and aggregation ---
$sql = "SELECT
            dn.shop_id,
            dn.subject_shop,
            dn.description_shop,
            dn.content_shop,
            dn.date_create,
            GROUP_CONCAT(DISTINCT dnc.file_name) AS file_name,
            GROUP_CONCAT(DISTINCT dnc.api_path) AS pic_path
        FROM
            dn_shop dn
        LEFT JOIN
            dn_shop_doc dnc ON dn.shop_id = dnc.shop_id
                                AND dnc.del = '0'
                                AND dnc.status = '1'
        WHERE
            dn.del = '0'"; // Only select shops where del is 0

if ($searchQuery) {
    $sql .= "
    AND dn.subject_shop LIKE '%" . $conn->real_escape_string($searchQuery) . "%'
    ";
}

$sql .= "
GROUP BY dn.shop_id
ORDER BY dn.date_create DESC
LIMIT $perPage OFFSET $offset";

$result = $conn->query($sql);


$boxesNews = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {

        $content = $row['content_shop'];

        $iframeSrc = null;
        if (preg_match('/<iframe.*?src=["\'](.*?)["\'].*?>/i', $content, $matches)) {
            $iframeSrc = isset($matches[1]) ? explode(',', $matches[1]) : null;
        }

        // Handle cases where pic_path or file_name might be NULL if no valid documents
        $paths = !empty($row['pic_path']) ? explode(',', $row['pic_path']) : [];
        $files = !empty($row['file_name']) ? explode(',', $row['file_name']) : [];

        $iframe = isset($iframeSrc[0]) ? $iframeSrc[0] : null;

        $boxesNews[] = [
            'id' => $row['shop_id'],
            'image' => !empty($paths) ? $paths[0] : null, // Set to null if no valid image path
            'date_time' => $row['date_create'],
            'title' => $row['subject_shop'],
            'description' => $row['description_shop'],
            'iframe' => $iframe
        ];
    }
} else {
    echo "No shop found.";
}
?>
<div style="display: flex; justify-content: space-between;">

    <div>
        </div>

    <div>
        <form method="GET" action="">
            <div class="input-group">
                <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($searchQuery); ?>" placeholder="Search shop...">
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
                <a href="shop_detail.php?id=<?php echo $encodedId; ?>" class="text-news">

                    <?php
                    // Display iframe if available, otherwise image if available, otherwise a placeholder/nothing
                    if(!empty($box['iframe'])){
                        echo '<iframe frameborder="0" src="' . $box['iframe'] . '" width="100%" height="100%" class="note-video-clip"></iframe>';
                    } else if (!empty($box['image'])){
                        echo '<img src="' . $box['image'] . '" alt="Image for ' . htmlspecialchars($box['title']) . '">';
                    } else {
                        // Optionally, display a default placeholder image or leave empty
                        echo '<img src="path/to/default/shop_placeholder.jpg" alt="No image available">';
                    }
                    ?>

                </a>
            </div>
            <div class="box-content">
                <a href="shop_detail.php?id=<?php echo $encodedId; ?>" class="text-news">
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

<!-- แสดงฟอร์มด้านล่างนี้ -->
<!-- <h3>ใส่ความคิดเห็น</h3>
<p>อีเมลของคุณจะไม่แสดงให้คนอื่นเห็น ช่องข้อมูลจำเป็นถูกทำเครื่องหมาย *</p>
<form id="commentForm" style="max-width: 600px;">
    <textarea id="commentText" name="comment" rows="5" required placeholder="ความคิดเห็น *"
        style="width: 100%; padding: 12px; margin-bottom: 3px; border: 1px solid #ccc; border-radius: 6px;"></textarea><br>
    <button type="submit"
        style="background-color: red; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer;">
        แสดงความคิดเห็น
    </button>
</form>

<script>
document.getElementById("commentForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const jwt = sessionStorage.getItem("jwt");
    const comment = document.getElementById("commentText").value;
    const pageUrl = window.location.pathname;

    if (!jwt) {
        // alert("กรุณาเข้าสู่ระบบก่อนแสดงความคิดเห็น");
        document.getElementById("myBtn-sign-in").click(); // เปิด modal login
        return;
    }

    fetch('actions/protected.php', {
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + jwt
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success" && parseInt(data.data.role_id) === 3) {
            // ส่งคอมเม้นไปเก็บใน database
            fetch('actions/save_comment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + jwt
                },
                body: JSON.stringify({
                    comment: comment,
                    page_url: pageUrl
                })
            })
            .then(res => res.json())
            .then(result => {
                if (result.status === 'success') {
                    alert("บันทึกความคิดเห็นเรียบร้อยแล้ว");
                    document.getElementById("commentText").value = '';
                } else {
                    alert("เกิดข้อผิดพลาด: " + result.message);
                }
            });
        } else {
            alert("ต้องเข้าสู่ระบบในฐานะ viewer เท่านั้น");
        }
    })
    .catch(err => {
        console.error("Error verifying user:", err);
        alert("เกิดข้อผิดพลาดในการยืนยันตัวตน");
    });
});
</script>
 -->

<?php

$boxesNews = [];

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
dn_news_doc dnc ON dn.news_id = dnc.news_id
GROUP BY 
dn.news_id";
$result = $conn->query($sql);

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
echo "";
}


?>
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

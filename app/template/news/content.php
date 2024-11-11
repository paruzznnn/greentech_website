<?php

$boxesNews = [];

$sql = "SELECT 
pn.news_id, 
pn.subject_news, 
pn.content_news, 
pn.date_create, 
pn.status, 
pn.del,
GROUP_CONCAT(pnd.file_name) AS file_name,
GROUP_CONCAT(pnd.api_path) AS pic_path
FROM 
public_news pn
LEFT JOIN 
public_news_doc pnd ON pn.news_id = pnd.news_id
GROUP BY 
pn.news_id";
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

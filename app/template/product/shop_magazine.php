<?php
$magazineArray = [];

$sql = "SELECT 
dn.shop_id, 
dn.subject_shop, 
dn.description_shop,
dn.content_shop, 
dn.date_create,
GROUP_CONCAT(dnc.file_name) AS file_name,
GROUP_CONCAT(dnc.api_path) AS pic_path
FROM 
dn_shop dn
LEFT JOIN 
dn_shop_doc dnc ON dn.shop_id = dnc.shop_id
WHERE 
dnc.status = '1'
AND dn.del = '0'
AND dnc.del = '0'
GROUP BY 
dn.shop_id
ORDER BY dn.date_create DESC
LIMIT 5";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $content = $row['content_shop'];

        $iframeSrc = null;
        if (preg_match('/<iframe.*?src=["\'](.*?)["\'].*?>/i', $content, $matches)) {
            $iframeSrc = isset($matches[1]) ? explode(',', $matches[1]) : null;
        }

        $paths = explode(',', $row['pic_path']);
        $files = explode(',', $row['file_name']);

        $iframe = isset($iframeSrc[0]) ? $iframeSrc[0] : null;

        $paths = explode(',', $row['pic_path']);
        $files = explode(',', $row['file_name']);

        $magazineArray[] = [
            'id' => $row['shop_id'],
            'image' =>  $paths[0],
            'category' => '',
            'date_time' => $row['date_create'],
            'title' => $row['subject_shop'],
            'description' => $row['description_shop'],
            'iframe' => $iframe,
            'url' => 'shop.php'
        ];
    }
}
?>

<div class="row">
    <?php if (count($magazineArray) > 0): ?>
        <div class="col-12 col-md-6">

            <div class="overflow">
                <div class="image-wrapper zoom">
                    <div class="caption-top">
                        <a href="<?php echo $magazineArray[0]['url'] ?>" class="line-clamp" style="font-size: 18px;">
                            <?php echo $magazineArray[0]['title'] ?>
                        </a>
                    </div>

                    <?php
                    if(empty($magazineArray[0]['image'])){
                        echo '<iframe frameborder="0" src="' . $magazineArray[0]['iframe'] . '" width="100%" height="100%" class="note-video-clip"></iframe>';
                    }else{
                        echo '<img class="img-fluid" src="' . htmlspecialchars($magazineArray[0]['image']) . '" alt="Image">';
                    }
                    ?>

                </div>
            </div>
            
        </div>

        <?php if (count($magazineArray) > 1): ?>
            <div class="col-12 col-md-6">
                <div class="row">
                    <?php foreach (array_slice($magazineArray, 1) as $mgzArr): ?>
                        <div class="col-6 pb-1 pt-0 pr-1 mt-1">

                            <div class="overflow">
                                <div class="image-wrapper zoom">
                                    <div class="caption-top">
                                        <a href="<?php echo $mgzArr['url'] ?>" class="line-clamp">
                                            <?php echo $mgzArr['title'] ?>
                                        </a>

                                    </div>

                                    <?php
                                    if(empty($mgzArr['image'])){
                                        echo '<iframe frameborder="0" src="' . $mgzArr['iframe'] . '" width="100%" height="100%" class="note-video-clip"></iframe>';
                                    }else{
                                        echo '<img class="img-fluid" src="' . htmlspecialchars($mgzArr['image']) . '" alt="Image">';
                                    }
                                    ?>

                                </div>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="col-12">
            <p>No shop available.</p>
        </div>
    <?php endif; ?>
</div>
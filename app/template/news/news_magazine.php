<?php
$magazineArray = [];

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
dn.news_id
ORDER BY dn.date_create DESC
LIMIT 5";
$result = $conn->query($sql);

// $sql = "SELECT 
//     dn.news_id, 
//     dn.subject_news,
//     dn.date_create, 
//     dn.status, 
//     dn.del
// FROM 
//     dn_news dn
// GROUP BY 
//     dn.news_id
// ORDER BY dn.date_create DESC
// LIMIT 1";
// $result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        $paths = explode(',', $row['pic_path']);
        $files = explode(',', $row['file_name']);
    
        $magazineArray[] = [
            'id' => $row['news_id'],
            'img' =>  $paths[0],
            'category' => '',
            'date_time' => $row['date_create'],
            'title' => $row['subject_news'],
            'description' => '',
            'url' => '#'
        ];

        // $news_id = $row['news_id'];

        // $sql_pic = "SELECT 
        //     GROUP_CONCAT(dnc.file_name) AS file_name,
        //     GROUP_CONCAT(dnc.api_path) AS pic_path
        // FROM 
        //     dn_news_doc dnc
        // WHERE news_id = ?";
        
        // $stmt = $conn->prepare($sql_pic);
        // $stmt->bind_param("i", $news_id);
        // $stmt->execute();
        // $result_pic = $stmt->get_result();

        // if ($result_pic->num_rows > 0) {
        //     while ($row_pic = $result_pic->fetch_assoc()) {
        //         $paths = explode(',', $row_pic['pic_path']);
        //         $files = explode(',', $row_pic['file_name']);

        //         $limitedPaths = array_slice($paths, 0, 5);
        //         foreach ($limitedPaths as $path) {
        //             $magazineArray[] = [
        //                 'id' => $row['news_id'],
        //                 'img' => $path,
        //                 'category' => '',
        //                 'date_time' => $row['date_create'],
        //                 'title' => $row['subject_news'],
        //                 'url' => '#'
        //             ];
        //         }
        //     }
        // }
        // $stmt->close();
    }
}
?>

<div class="row">
    <div class="col-12 col-md-6">
        <div class="overflow">
            <div class="image-wrapper zoom">
                <div class="caption-top">
                    <a href="" class="line-clamp" style="font-size: 18px;"><?php echo $magazineArray[0]['title']?></a>
                </div>
                <img class="img-fluid" src="<?php echo htmlspecialchars($magazineArray[0]['img']); ?>" alt="News Image">
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
                                    <a href="" class="line-clamp"><?php echo $mgzArr['title']?></a>
                                </div>
                                <img class="img-fluid" src="<?php echo htmlspecialchars($mgzArr['img']); ?>" alt="News Image">
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>



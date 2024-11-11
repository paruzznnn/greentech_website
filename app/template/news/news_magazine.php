<?php

$articles = [];

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
pn.news_id
ORDER BY pn.date_create DESC
LIMIT 4";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
while($row = $result->fetch_assoc()) {

    $content = $row['content_news'];
    $paths = explode(',', $row['pic_path']);
    $files = explode(',', $row['file_name']);

    $articles[] = [
        'id' => $row['news_id'],
        'img' =>  $paths[0],
        'category' => '',
        'date_time' => $row['date_create'],
        'title' => $row['subject_news'],
        'description' => '',
        'url' => '#'
    ];

    // $limitedPaths = array_slice($paths, 0, 5);

    // foreach ($limitedPaths as $path) {

    //     $articles[] = [
    //         'id' => $row['news_id'],
    //         'img' =>  $path,
    //         'category' => '',
    //         'date_time' => $row['date_create'],
    //         'title' => $row['subject_news'],
    //         'description' => '',
    //         'url' => '#'
    //     ];
    
    // }


}
} else {
echo "";
}

?>

<div class="row">
    <div class="col-12 <?php echo count($articles) == 1 ? 'col-md-6' : 'col-md-6'; ?>">
        <div class="card overflow zoom">
            <div class="position-relative">
                <div class="ratio_right-cover-2 image-wrapper">
                    <a href="">
                        <img class="img-fluid" src="<?php echo $articles[0]['img']; ?>" alt="">
                    </a>
                </div>
                <div class="position-absolute p-2 p-lg-3 b-0 w-100 bg-shadow">
                    <a class="h5 p-1 badge badge-primary rounded-0" href="#"><?php echo $articles[0]['category']; ?></a>
                    <a href="" style="color: rgb(244 110 32);">
                        <p class="my-1"><?php echo $articles[0]['title']; ?></p>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php if (count($articles) > 1): ?>
        <div class="col-12 col-md-6">
            <div class="row">
                <?php foreach (array_slice($articles, 1) as $article): ?>
                    <div class="col-6 pb-1 pt-0 pr-1 mt-1">
                        <div class="card overflow zoom">
                            <div class="position-relative">
                                <div class="ratio_right-cover-2 image-wrapper">
                                    <a href="">
                                        <img class="img-fluid" src="<?php echo $article['img']; ?>" alt="">
                                    </a>
                                </div>
                                <div class="position-absolute p-2 p-lg-3 b-0 w-100 bg-shadow">
                                    <a class="h5 p-1 badge badge-primary rounded-0" href="#"><?php echo $article['category']; ?></a>
                                    <a href="" style="color: rgb(244 110 32);">
                                        <p class="my-1"><?php echo $article['title']; ?></p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>


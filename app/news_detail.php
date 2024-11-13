<?php
require_once('../lib/connect.php');
global $conn;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>

    <?php include 'inc_head.php'?>
    <link href="css/index_.css?v=<?php echo time();?>" rel="stylesheet">
    <link href="css/news_.css?v=<?php echo time();?>" rel="stylesheet">


</head>
<body>

    <?php include 'template/header.php'?>
    <?php include 'template/navbar_slide.php'?>

    <div class="content-sticky" id="">
        <div class="container">
            <div class="box-content">

                <div class="row">

                    <div class="col-md-9">
                        <?php

                            if (isset($_GET['id'])) {
                                $decodedId = base64_decode(urldecode($_GET['id']));
                                
                                if ($decodedId !== false) {
                                    $stmt = $conn->prepare("SELECT 
                                        dn.news_id, 
                                        dn.subject_news, 
                                        dn.content_news, 
                                        dn.date_create, 
                                        dn.status, 
                                        dn.del,
                                        GROUP_CONCAT(dnc.file_name) AS file_name,
                                        GROUP_CONCAT(dnc.api_path) AS pic_path
                                        FROM dn_news dn
                                        LEFT JOIN dn_news_doc dnc ON dn.news_id = dnc.news_id
                                        WHERE dn.news_id = ?
                                        GROUP BY dn.news_id");

                                    $stmt->bind_param('i', $decodedId); 
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $content = $row['content_news'];
                                            $paths = explode(',', $row['pic_path']);
                                            $files = explode(',', $row['file_name']);
                                            $found = false;

                                            foreach ($files as $index => $file) {
                                                $pattern = '/<img[^>]+data-filename="' . preg_quote($file, '/') . '"[^>]*>/i';

                                                if (preg_match($pattern, $content, $matches)) {
                                                    $new_src = $paths[$index];
                                                    $new_img_tag = preg_replace('/(<img[^>]+)(src="[^"]*")/i', '$1 src="' . $new_src . '"', $matches[0]);

                                                    $content = str_replace($matches[0], $new_img_tag, $content);
                                                    
                                                    $found = true;
                                                }
                                            }

                                            if (!$found) {
                                                echo "ไม่พบ <img> ที่มี data-filename ตรงกับค่าใน paths<br>";
                                            }

                                            echo '<div style="">';
                                            echo $content = mb_convert_encoding($content, 'UTF-8', 'auto');
                                            echo '</div>';
                                        }
                                    } else {
                                        echo "ไม่มีข้อมูล";
                                    }

                                    $stmt->close(); 
                                } else {
                                    echo "Invalid ID.";
                                }
                            }

                        ?>
                    </div>

                    <div class="col-md-3">
                        <div class="page-plugin">
                        <iframe src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2Fallablethailand%2F&tabs=timeline&width=340&height=500&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true&appId" width="340" height="500" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <?php include 'template/footer.php'?>
    

    <script src="js/index_.js?v=<?php echo time();?>"></script>
    <script src="js/news/news_.js?v=<?php echo time();?>"></script>

</body>
</html>
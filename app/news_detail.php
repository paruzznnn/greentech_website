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
                                    // ควรใช้ prepared statement แทนการแทรกข้อมูลโดยตรงเพื่อความปลอดภัย
                                    $stmt = $conn->prepare("SELECT 
                                        pn.news_id, 
                                        pn.subject_news, 
                                        pn.content_news, 
                                        pn.date_create, 
                                        pn.status, 
                                        pn.del,
                                        GROUP_CONCAT(pnd.file_name) AS file_name,
                                        GROUP_CONCAT(pnd.api_path) AS pic_path
                                        FROM public_news pn
                                        LEFT JOIN public_news_doc pnd ON pn.news_id = pnd.news_id
                                        WHERE pn.news_id = ?
                                        GROUP BY pn.news_id");

                                    // ผูกค่ากับตัวแปร
                                    $stmt->bind_param('i', $decodedId); // ใช้ 'i' สำหรับ integer
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $content = $row['content_news'];
                                            $paths = explode(',', $row['pic_path']);
                                            $files = explode(',', $row['file_name']);
                                            $found = false;

                                            // ตรวจสอบไฟล์ใน $files
                                            foreach ($files as $index => $file) {
                                                $pattern = '/<img[^>]+data-filename="' . preg_quote($file, '/') . '"[^>]*>/i';

                                                if (preg_match($pattern, $content, $matches)) {
                                                    // เพิ่ม src ใน <img>
                                                    $new_src = $paths[$index]; // ใช้ค่าจาก $paths ที่ตรงกับไฟล์
                                                    $new_img_tag = preg_replace('/(<img[^>]+)(src="[^"]*")/i', '$1 src="' . $new_src . '"', $matches[0]);
                                                    
                                                    // แสดง <img> ที่มีการอัพเดท src
                                                    $content = str_replace($matches[0], $new_img_tag, $content);
                                                    
                                                    $found = true;
                                                }
                                            }

                                            if (!$found) {
                                                echo "ไม่พบ <img> ที่มี data-filename ตรงกับค่าใน paths<br>";
                                            }

                                            echo '<div style="width: 720px; margin-left: 20px;">';
                                            echo $content;
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
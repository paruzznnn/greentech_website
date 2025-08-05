<?php
require_once('../lib/connect.php');
global $conn;

$subjectTitle = "สินค้า"; // fallback title
$pageUrl = "";
$encodedId = "";

if (isset($_GET['id'])) {
    $encodedId = $_GET['id'];
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $pageUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $decodedId = base64_decode(urldecode($encodedId));

    if ($decodedId !== false) {
        $stmt = $conn->prepare("SELECT subject_shop FROM dn_shop WHERE del = 0 AND shop_id = ?");
        $stmt->bind_param('i', $decodedId);
        $stmt->execute();
        $resultTitle = $stmt->get_result();
        if ($resultTitle->num_rows > 0) {
            $row = $resultTitle->fetch_assoc();
            $subjectTitle = $row['subject_shop'];
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($subjectTitle); ?></title>

    <?php include 'inc_head.php'?>
    <link href="css/index_.css?v=<?php echo time();?>" rel="stylesheet">
    <link href="css/news_.css?v=<?php echo time();?>" rel="stylesheet">

    <style>

        img{
            max-width: 600px;
        }
        .shop-content-display {
            font-family: sans-serif, "Roboto" !important;
        }

        /* CSS สำหรับกล่องเลื่อนแนวนอน (นำมาจาก project_detail.php) */
        .shop-wrapper-container {
            position: relative;
            max-width: 1280px;
            margin: 0;
            overflow: hidden;
            padding: 0 40px;
        }

        .shop-scroll {
            display: flex;
            gap: 10px;
            scroll-behavior: smooth;
            overflow-x: auto;
            padding-bottom: 1rem;
            scrollbar-width: none;
        }
        .shop-scroll::-webkit-scrollbar {
            display: none;
        }

        .shop-card {
            flex: 0 0 300px;
            max-width: 300px;
            height: auto;
        }
        
        .related-shop-box {
            display: flex;
            flex-direction: column;
            height: 100%;
            border: 1px solid #ddd;
            border-radius: 6px;
            overflow: hidden;
            background-color: #fff;
            text-decoration: none;
            color: #333;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .related-shop-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }

        .card-image-wrapper {
            height: 220px;
            overflow: hidden;
        }

        .card-img-top {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-body {
            padding: 10px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            flex-grow: 1;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 5px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .card-text {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        
        .scroll-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(255, 255, 255, 0.8);
            border: 1px solid #ccc;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            cursor: pointer;
            z-index: 5;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
            font-size: 1.5rem;
            font-weight: bold;
        }

        .scroll-btn.left {
            left: 5px;
        }

        .scroll-btn.right {
            right: 5px;
        }
        
        aa {
            color: #3e5beaff;;
            text-decoration: underline;
        }

        .social-share {
            margin-top: 20px;
            display: flex;
            align-items: center;
            flex-wrap: wrap; /* Added to handle wrapping on smaller screens */
        }
        .social-share p {
            margin-right: 15px;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .social-share a {
            margin-right: 10px;
            text-decoration: none;
        }
        .social-share img {
            width: 40px;
            height: 40px;
            transition: transform 0.2s ease;
        }
        .social-share a:hover img {
            transform: scale(1.1);
        }
        .copy-link-btn {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 8px 12px;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .copy-link-btn:hover {
            background-color: #5a6268;
        }
    </style>

</head>
<body>

    <?php include 'template/header.php'?>
    <?php include 'template/navbar_slide.php'?>

    <div class="content-sticky" id="">
        <div class="container">
            <div class="box-content">

                <div class="row">
                    <div class="">
                        <?php
                        if (isset($_GET['id'])) {
                            $decodedId = base64_decode(urldecode($_GET['id']));

                            if ($decodedId !== false) {
                                $stmt = $conn->prepare("SELECT
                                    dn.shop_id,
                                    dn.subject_shop,
                                    dn.content_shop,
                                    dn.date_create,
                                    GROUP_CONCAT(dnc.file_name) AS file_name,
                                    GROUP_CONCAT(dnc.api_path) AS pic_path
                                    FROM dn_shop dn
                                    LEFT JOIN dn_shop_doc dnc ON dn.shop_id = dnc.shop_id
                                    WHERE dn.shop_id = ?
                                    GROUP BY dn.shop_id");

                                $stmt->bind_param('i', $decodedId);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $content = $row['content_shop'];
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
                                            echo "";
                                        }

                                        echo '<div class="shop-content-display">';
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
                </div>
            
                <div style="padding-left:50px;">
                    <hr style="border-top: dashed 1px; margin: 40px 0;">
                    <p>สอบถาม/สั่งซื้อผลิตภัณฑ์ Trandar Acoustics ได้ที่</p>
                    <p>🛒 Website : <aa href="https://www.trandar.com/store/app/index.php" target="_blank">www.trandar.com/store/</aa></p>
                    <p>📱 Line OA : @Trandaraocoustic 
                        <aa href="https://lin.ee/yoSCNwF" target="_blank">https://lin.ee/yoSCNwF</aa>
                    </p>
                    <p>📱 Line OA : @Trandarstore 
                        <aa href="https://lin.ee/xJr661u" target="_blank">https://lin.ee/xJr661u</aa>
                    </p>
                    <p>☎️ Tel : 02-722-7007</p>           
                </div> 

                <?php
                if (isset($_GET['id'])) {
                    $decodedId = base64_decode(urldecode($_GET['id']));
                    if ($decodedId !== false) {
                        $stmt_project = $conn->prepare("
                            SELECT 
                                dp.project_id, 
                                dp.subject_project, 
                                dp.description_project,
                                dp.content_project,
                                GROUP_CONCAT(dnd.api_path) AS pic_path
                            FROM dn_project dp
                            JOIN dn_project_shop dps ON dp.project_id = dps.project_id
                            LEFT JOIN dn_project_doc dnd ON dp.project_id = dnd.project_id AND dnd.del = '0' AND dnd.status = '1'
                            WHERE dps.shop_id = ?
                            GROUP BY dp.project_id
                        ");
                        $stmt_project->bind_param('i', $decodedId);
                        $stmt_project->execute();
                        $result_project = $stmt_project->get_result();

                        if ($result_project->num_rows > 0) {
                            echo '<h3 style="padding-top: 40px;">โปรเจกต์ที่เกี่ยวข้องกับสินค้านี้</h3>';
                            echo '<div class="shop-wrapper-container">';
                            echo '<div class="scroll-btn left" onclick="scrollProject(\'left\')">&#10094;</div>';
                            echo '<div class="scroll-btn right" onclick="scrollProject(\'right\')">&#10095;</div>';
                            echo '<div class="shop-scroll" id="project-scroll-box">';
                            
                            while ($row_project = $result_project->fetch_assoc()) {
                                $projectIdEncoded = urlencode(base64_encode($row_project['project_id']));
                                $project_link = "project_detail.php?id=" . $projectIdEncoded;
                                
                                $content = $row_project['content_project'];
                                $iframeSrc = null;
                                if (preg_match('/<iframe.*?src=["\'](.*?)["\'].*?>/i', $content, $matches)) {
                                    $iframeSrc = isset($matches[1]) ? explode(',', $matches[1]) : null;
                                }
                                $iframe = isset($iframeSrc[0]) ? $iframeSrc[0] : null;

                                $paths = !empty($row_project['pic_path']) ? explode(',', $row_project['pic_path']) : [];
                                $image_path = !empty($paths) ? $paths[0] : null;
                                
                                $placeholder_image = 'https://via.placeholder.com/300x220.png?text=Project+Image';

                                echo '<div class="shop-card">';
                                echo '<a href="' . htmlspecialchars($project_link) . '" class="related-shop-box">';
                                
                                if (!empty($iframe)) {
                                    echo '<iframe frameborder="0" src="' . htmlspecialchars($iframe) . '" width="100%" height="220px" class="note-video-clip"></iframe>';
                                } else if (!empty($image_path)) {
                                    echo '<div class="card-image-wrapper">';
                                    echo '<img src="' . htmlspecialchars($image_path) . '" class="card-img-top" alt="' . htmlspecialchars($row_project['subject_project']) . '">';
                                    echo '</div>';
                                } else {
                                    echo '<div class="card-image-wrapper">';
                                    echo '<img src="' . htmlspecialchars($placeholder_image) . '" class="card-img-top" alt="ไม่มีรูปภาพ">';
                                    echo '</div>';
                                }

                                echo '<div class="card-body">';
                                echo '<h5 class="card-title">' . htmlspecialchars($row_project['subject_project']) . '</h5>';
                                echo '<p class="card-text">' . htmlspecialchars($row_project['description_project']) . '</p>';
                                echo '</div>';
                                echo '</a>';
                                echo '</div>';
                            }
                            echo '</div>';
                            echo '</div>';
                        }
                        $stmt_project->close();
                    }
                }
                ?>
                <h3 style ="padding-top: 40px;">ความคิดเห็น</h3>
                <p>อีเมลของคุณจะไม่แสดงให้คนอื่นเห็น ช่องข้อมูลจำเป็นถูกทำเครื่องหมาย *</p>
                <form id="commentForm" style="max-width: 600px;">
                    <textarea id="commentText" name="comment" rows="5" required placeholder="ความคิดเห็น *"
                        style="width: 100%; padding: 12px; margin-bottom: 3px; border: 1px solid #ccc; border-radius: 6px;"></textarea><br>
                    <button type="submit"
                        style="background-color: red; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer;">
                        แสดงความคิดเห็น
                    </button>
                </form>

                <div class="social-share">
                    <p>แชร์หน้านี้:</p>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($pageUrl) ?>" target="_blank">
                        <img src="https://img.icons8.com/color/48/000000/facebook-new.png" alt="Share on Facebook">
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?= urlencode($pageUrl) ?>&text=<?= urlencode($subjectTitle) ?>" target="_blank">
                        <img src="https://img.icons8.com/color/48/000000/twitter--v1.png" alt="Share on Twitter">
                    </a>
                    <a href="https://social-plugins.line.me/lineit/share?url=<?= urlencode($pageUrl) ?>" target="_blank">
                        <img src="https://img.icons8.com/color/48/000000/line-me.png" alt="Share on Line">
                    </a>
                    <a href="https://pinterest.com/pin/create/button/?url=<?= urlencode($pageUrl) ?>&description=<?= urlencode($subjectTitle) ?>" target="_blank">
                        <img src="https://img.icons8.com/color/48/000000/pinterest--v1.png" alt="Share on Pinterest">
                    </a>
                    <a href="https://www.instagram.com/" target="_blank">
                        <img src="https://img.icons8.com/fluency/48/instagram-new.png" alt="Share on Instagram">
                    </a>
                    <a href="https://www.tiktok.com/" target="_blank">
                        <img src="https://img.icons8.com/fluency/48/tiktok.png" alt="Share on TikTok">
                    </a>
                    <button class="copy-link-btn" onclick="copyLink()">คัดลอกลิงก์</button>
                </div>

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

                // JavaScript สำหรับการเลื่อนกล่องแนวนอน
                function scrollProject(direction) {
                    const box = document.getElementById('project-scroll-box');
                    const scrollAmount = 300 + 10;
                    if (direction === 'left') {
                        box.scrollLeft -= scrollAmount;
                    } else {
                        box.scrollLeft += scrollAmount;
                    }
                }
                
                // JavaScript สำหรับการคัดลอกลิงก์
                function copyLink() {
                    const pageUrl = "<?= $pageUrl ?>";
                    navigator.clipboard.writeText(pageUrl).then(function() {
                        alert("คัดลอกลิงก์เรียบร้อยแล้ว");
                    }, function() {
                        alert("ไม่สามารถคัดลอกลิงก์ได้ กรุณาคัดลอกด้วยตนเอง");
                    });
                }
                </script>

                </div>
            
        </div>
        
    </div>
                        
    <?php include 'template/footer.php'?>
    

    <script src="js/index_.js?v=<?php echo time();?>"></script>
    <script src="js/shop/shop_.js?v=<?php echo time();?>"></script>

</body>
</html>
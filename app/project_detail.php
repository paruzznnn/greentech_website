<?php
require_once('../lib/connect.php');
global $conn;


$subjectTitle = "โปรเจกต์"; // fallback title

if (isset($_GET['id'])) {
    $decodedId = base64_decode(urldecode($_GET['id']));

    if ($decodedId !== false) {
        $stmt = $conn->prepare("SELECT subject_project FROM dn_project WHERE del = 0 AND project_id = ?");
        $stmt->bind_param('i', $decodedId);
        $stmt->execute();
        $resultTitle = $stmt->get_result();
        if ($resultTitle->num_rows > 0) {
            $row = $resultTitle->fetch_assoc();
            $subjectTitle = $row['subject_project'];
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

        /* ปรับปรุง CSS ใหม่สำหรับกล่องเลื่อนแนวนอน */
        .shop-wrapper-container {
            position: relative;
            max-width: 1280px;
            /* ปรับ: ให้ชิดซ้าย */
            margin: 0;
            overflow: hidden;
            padding: 0 40px;
        }

        .shop-scroll {
            display: flex;
            gap: 10px; /* ระยะห่างระหว่าง card */
            scroll-behavior: smooth;
            overflow-x: auto;
            padding-bottom: 1rem;
            scrollbar-width: none; /* สำหรับ Firefox */
        }
        .shop-scroll::-webkit-scrollbar {
            display: none; /* สำหรับ Chrome, Safari, Opera */
        }

        .shop-card {
            flex: 0 0 300px; /* กำหนดให้แต่ละ card มีขนาดคงที่ 300px */
            max-width: 300px;
            height: auto;
        }
        
        .related-shop-box {
            display: flex;
            flex-direction: column;
            height: 100%; /* ให้กล่องเต็มความสูงของ parent */
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
            height: 220px; /* ปรับ: เพิ่มความสูงของรูปภาพ */
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
            flex-grow: 1; /* ปรับ: ให้ card-body ขยายเพื่อเติมช่องว่าง */
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
            -webkit-line-clamp: 2; /* แสดงข้อความไม่เกิน 2 บรรทัด */
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
                                        dn.project_id, 
                                        dn.subject_project, 
                                        dn.content_project, 
                                        dn.date_create, 
                                        GROUP_CONCAT(dnc.file_name) AS file_name,
                                        GROUP_CONCAT(dnc.api_path) AS pic_path
                                        FROM dn_project dn
                                        LEFT JOIN dn_project_doc dnc ON dn.project_id = dnc.project_id
                                        WHERE dn.project_id = ?
                                        GROUP BY dn.project_id");

                                    $stmt->bind_param('i', $decodedId); 
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $content = $row['content_project'];
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
                <p>🛒 Website : <a href="https://www.trandar.com" target="_blank">www.trandar.com/store/</a></p>
                <p>📱 Line OA : @Trandaraocoustic 
                    <a href="https://lin.ee/yoSCNwF" target="_blank">https://lin.ee/yoSCNwF</a>
                </p>
                <p>📱 Line OA : @Trandarstore 
                    <a href="https://lin.ee/xJr661u" target="_blank">https://lin.ee/xJr661u</a>
                </p>
                <p>☎️ Tel : 02-722-7007</p>           
            </div> 

            <?php
            if (isset($_GET['id'])) {
                $decodedId = base64_decode(urldecode($_GET['id']));
                if ($decodedId !== false) {
                    $stmt_shop = $conn->prepare("
                        SELECT 
                            ds.shop_id, 
                            ds.subject_shop, 
                            ds.description_shop,
                            ds.content_shop,
                            GROUP_CONCAT(dnd.api_path) AS pic_path
                        FROM dn_shop ds
                        JOIN dn_project_shop dps ON ds.shop_id = dps.shop_id
                        LEFT JOIN dn_shop_doc dnd ON ds.shop_id = dnd.shop_id AND dnd.del = '0' AND dnd.status = '1'
                        WHERE dps.project_id = ?
                        GROUP BY ds.shop_id
                    ");
                    $stmt_shop->bind_param('i', $decodedId);
                    $stmt_shop->execute();
                    $result_shop = $stmt_shop->get_result();

                    if ($result_shop->num_rows > 0) {
                        echo '<h3 style="padding-top: 40px;">สินค้าที่เกี่ยวข้องกับโปรเจกต์นี้</h3>';
                        echo '<div class="shop-wrapper-container">';
                        echo '<div class="scroll-btn left" onclick="scrollshop(\'left\')">&#10094;</div>';
                        echo '<div class="scroll-btn right" onclick="scrollshop(\'right\')">&#10095;</div>';
                        echo '<div class="shop-scroll" id="shop-scroll-box">';
                        
                        while ($row_shop = $result_shop->fetch_assoc()) {
                            $shopIdEncoded = urlencode(base64_encode($row_shop['shop_id']));
                            $shop_link = "shop_detail.php?id=" . $shopIdEncoded;
                            
                            $content = $row_shop['content_shop'];
                            $iframeSrc = null;
                            if (preg_match('/<iframe.*?src=["\'](.*?)["\'].*?>/i', $content, $matches)) {
                                $iframeSrc = isset($matches[1]) ? explode(',', $matches[1]) : null;
                            }
                            $iframe = isset($iframeSrc[0]) ? $iframeSrc[0] : null;

                            $paths = !empty($row_shop['pic_path']) ? explode(',', $row_shop['pic_path']) : [];
                            $image_path = !empty($paths) ? $paths[0] : null;
                            
                            $placeholder_image = 'https://via.placeholder.com/300x220.png?text=Shop+Image';

                            echo '<div class="shop-card">';
                            echo '<a href="' . htmlspecialchars($shop_link) . '" class="related-shop-box">';
                            
                            if (!empty($iframe)) {
                                echo '<iframe frameborder="0" src="' . htmlspecialchars($iframe) . '" width="100%" height="220px" class="note-video-clip"></iframe>';
                            } else if (!empty($image_path)) {
                                echo '<div class="card-image-wrapper">';
                                echo '<img src="' . htmlspecialchars($image_path) . '" class="card-img-top" alt="' . htmlspecialchars($row_shop['subject_shop']) . '">';
                                echo '</div>';
                            } else {
                                echo '<div class="card-image-wrapper">';
                                echo '<img src="' . htmlspecialchars($placeholder_image) . '" class="card-img-top" alt="ไม่มีรูปภาพ">';
                                echo '</div>';
                            }

                            echo '<div class="card-body">';
                            echo '<h5 class="card-title">' . htmlspecialchars($row_shop['subject_shop']) . '</h5>';
                            echo '<p class="card-text">' . htmlspecialchars($row_shop['description_shop']) . '</p>';
                            echo '</div>';
                            echo '</a>';
                            echo '</div>';
                        }
                        echo '</div>';
                        echo '</div>';
                    }
                    $stmt_shop->close();
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

                <script>
                document.getElementById("commentForm").addEventListener("submit", function(e) {
                    e.preventDefault();

                    const jwt = sessionStorage.getItem("jwt");
                    const comment = document.getElementById("commentText").value;
                    const pageUrl = window.location.pathname;

                    if (!jwt) {
                        document.getElementById("myBtn-sign-in").click();
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
                
                function scrollshop(direction) {
                    const box = document.getElementById('shop-scroll-box');
                    const scrollAmount = 300 + 10;
                    if (direction === 'left') {
                        box.scrollLeft -= scrollAmount;
                    } else {
                        box.scrollLeft += scrollAmount;
                    }
                }
                </script>
                
            </div>
            
        </div>
        
    </div>
                        
    <?php include 'template/footer.php'?>
    

    <script src="js/index_.js?v=<?php echo time();?>"></script>
    <script src="js/project/project_.js?v=<?php echo time();?>"></script>

</body>
</html>
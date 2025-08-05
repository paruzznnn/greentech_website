<?php
require_once('../lib/connect.php');
global $conn;


$subjectTitle = "สินค้า"; // fallback title
$pageUrl = ""; // Add this variable

if (isset($_GET['id'])) {
    $encodedId = $_GET['id'];
    // Generate dynamic URL
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $pageUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    
    $decodedId = base64_decode(urldecode($_GET['id']));

    if ($decodedId !== false) {
        $stmt = $conn->prepare("SELECT subject_idia FROM dn_idia WHERE del = 0 AND idia_id = ?");
        $stmt->bind_param('i', $decodedId);
        $stmt->execute();
        $resultTitle = $stmt->get_result();
        if ($resultTitle->num_rows > 0) {
            $row = $resultTitle->fetch_assoc();
            $subjectTitle = $row['subject_idia'];
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

        /* New CSS for social sharing */
        .social-share {
            margin-top: 20px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
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
                                        dn.idia_id, 
                                        dn.subject_idia, 
                                        dn.content_idia, 
                                        dn.date_create, 
                                        GROUP_CONCAT(dnc.file_name) AS file_name,
                                        GROUP_CONCAT(dnc.api_path) AS pic_path
                                        FROM dn_idia dn
                                        LEFT JOIN dn_idia_doc dnc ON dn.idia_id = dnc.idia_id
                                        WHERE dn.idia_id = ?
                                        GROUP BY dn.idia_id");

                                    $stmt->bind_param('i', $decodedId); 
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $content = $row['content_idia'];
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
                
                // JavaScript for Copy Link functionality
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
    <script src="js/idia/idia_.js?v=<?php echo time();?>"></script>

</body>
</html>
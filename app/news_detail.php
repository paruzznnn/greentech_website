<?php
require_once('../lib/connect.php');
global $conn;

// --- MODIFIED: Check for language preference from the URL, now including 'cn' and 'jp'. Default to Thai. ---
$lang = isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'cn', 'jp']) ? $_GET['lang'] : 'th';

$subjectTitle = ($lang === 'cn' ? '产品' : ($lang === 'en' ? 'Product' : ($lang === 'jp' ? '製品' : 'สินค้า'))); // fallback title based on language
$pageUrl = ""; // Add this variable

if (isset($_GET['id'])) {
    $encodedId = $_GET['id'];
    // Generate dynamic URL
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    
    // --- MODIFIED: Append lang parameter to the URL ---
    $pageUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    
    $decodedId = base64_decode(urldecode($_GET['id']));

    if ($decodedId !== false) {
        // --- MODIFIED: Select English, Chinese AND Japanese titles if available ---
        $stmt = $conn->prepare("SELECT subject_news, subject_news_en, subject_news_cn, subject_news_jp FROM dn_news WHERE del = 0 AND news_id = ?");
        $stmt->bind_param('i', $decodedId);
        $stmt->execute();
        $resultTitle = $stmt->get_result();
        if ($resultTitle->num_rows > 0) {
            $row = $resultTitle->fetch_assoc();
            // --- MODIFIED: Use correct language title based on lang parameter ---
            $subjectTitle = $row['subject_news'];
            if ($lang === 'en' && !empty($row['subject_news_en'])) {
                $subjectTitle = $row['subject_news_en'];
            } elseif ($lang === 'cn' && !empty($row['subject_news_cn'])) {
                $subjectTitle = $row['subject_news_cn'];
            } elseif ($lang === 'jp' && !empty($row['subject_news_jp'])) {
                $subjectTitle = $row['subject_news_jp'];
            }
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
            /* max-width: 600px; */
        }
        .shop-content-display {
            font-family: sans-serif, "Roboto" !important;
        }
        aa {
            color: #3e5beaff;;
            text-decoration: underline;
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
        <div class="container" style="max-width: 90%;">
            <div class="box-content">
                <div class="social-share">
                <p><?php echo ($lang === 'cn' ? '分享此页面：' : ($lang === 'en' ? 'Share this page:' : ($lang === 'jp' ? 'このページをシェア：' : 'แชร์หน้านี้:'))); ?></p>
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
                <button class="copy-link-btn" onclick="copyLink()"><?php echo ($lang === 'cn' ? '复制链接' : ($lang === 'en' ? 'Copy Link' : ($lang === 'jp' ? 'リンクをコピー' : 'คัดลอกลิงก์'))); ?></button>
                </div>

                <div class="row">

                    <div class="">
                        <?php

                            if (isset($_GET['id'])) {
                                $decodedId = base64_decode(urldecode($_GET['id']));
                                
                                if ($decodedId !== false) {
                                    // --- MODIFIED: Select English, Chinese AND Japanese content if lang is available ---
                                    $stmt = $conn->prepare("SELECT 
                                        dn.news_id, 
                                        dn.subject_news,
                                        dn.subject_news_en,
                                        dn.subject_news_cn,
                                        dn.subject_news_jp,
                                        dn.content_news,
                                        dn.content_news_en,
                                        dn.content_news_cn,
                                        dn.content_news_jp,
                                        dn.date_create, 
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
                                            // --- MODIFIED: Use the correct language content based on preference ---
                                            $content = $row['content_news'];
                                            if ($lang === 'en' && !empty($row['content_news_en'])) {
                                                $content = $row['content_news_en'];
                                            } elseif ($lang === 'cn' && !empty($row['content_news_cn'])) {
                                                $content = $row['content_news_cn'];
                                            } elseif ($lang === 'jp' && !empty($row['content_news_jp'])) {
                                                $content = $row['content_news_jp'];
                                            }

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
                                        // --- MODIFIED: Change text based on language ---
                                        echo ($lang === 'cn' ? '无可用信息。' : ($lang === 'en' ? 'No information available.' : ($lang === 'jp' ? '情報がありません。' : 'ไม่มีข้อมูล')));
                                    }

                                    $stmt->close(); 
                                } else {
                                    // --- MODIFIED: Change text based on language ---
                                    echo ($lang === 'cn' ? '无效 ID。' : ($lang === 'en' ? 'Invalid ID.' : ($lang === 'jp' ? '無効なIDです。' : 'ID ไม่ถูกต้อง')));
                                }
                            }

                        ?>
                    </div>

                </div>
                                    <hr style="border-top: dashed 1px; margin: 20px 0;">
                <div class="social-share">
                    <p><?php echo ($lang === 'cn' ? '分享此页面：' : ($lang === 'en' ? 'Share this page:' : ($lang === 'jp' ? 'このページをシェア：' : 'แชร์หน้านี้:'))); ?></p>
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
                    <button class="copy-link-btn" onclick="copyLink()"><?php echo ($lang === 'cn' ? '复制链接' : ($lang === 'en' ? 'Copy Link' : ($lang === 'jp' ? 'リンクをコピー' : 'คัดลอกลิงก์'))); ?></button>
                </div>
                <div style="padding-left:50px;">
                    <hr style="border-top: dashed 1px; margin: 20px 0;">
                    
                    <p><?php echo ($lang === 'cn' ? '咨询/订购Trandar Acoustics产品，请联系' : ($lang === 'en' ? 'Inquire/Order Trandar Acoustics products at' : ($lang === 'jp' ? 'Trandar Acoustics製品のお問い合わせ・ご注文は下記まで' : 'สอบถาม/สั่งซื้อผลิตภัณฑ์ Trandar Acoustics ได้ที่'))); ?></p>
                    <p>🛒 Website : <aa href="https://www.trandar.com/store/app/index.php" target="_blank">www.trandar.com/store/</aa></p>
                    <p>📱 Line OA : @Trandaraocoustic 
                        <aa href="https://lin.ee/yoSCNwF" target="_blank">https://lin.ee/yoSCNwF</aa>
                    </p>
                    <p>📱 Line OA : @Trandarstore 
                        <aa href="https://lin.ee/xJr661u" target="_blank">https://lin.ee/xJr661u</aa>
                    </p>
                    <p>☎️ Tel : 02-722-7007</p>           
                </div> 

            
            
                <h3 style ="padding-top: 40px;"><?php echo ($lang === 'cn' ? '评论' : ($lang === 'en' ? 'Comments' : ($lang === 'jp' ? 'コメント' : 'ความคิดเห็น'))); ?></h3>
                <p><?php echo ($lang === 'cn' ? '您的电子邮件不会显示给他人。必填字段标有 *' : ($lang === 'en' ? 'Your email will not be displayed to others. Required fields are marked *' : ($lang === 'jp' ? 'メールアドレスが公開されることはありません。必須フィールドには * が付いています。' : 'อีเมลของคุณจะไม่แสดงให้คนอื่นเห็น ช่องข้อมูลจำเป็นถูกทำเครื่องหมาย *'))); ?></p>
                <form id="commentForm" style="max-width: 600px;">
                    <textarea id="commentText" name="comment" rows="5" required placeholder="<?php echo ($lang === 'cn' ? '评论 *' : ($lang === 'en' ? 'Comment *' : ($lang === 'jp' ? 'コメント *' : 'ความคิดเห็น *'))); ?>"
                        style="width: 100%; padding: 12px; margin-bottom: 3px; border: 1px solid #ccc; border-radius: 6px;"></textarea><br>
                    <button type="submit"
                        style="background-color: red; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer;">
                        <?php echo ($lang === 'cn' ? '发表评论' : ($lang === 'en' ? 'Post Comment' : ($lang === 'jp' ? 'コメントを投稿' : 'แสดงความคิดเห็น'))); ?>
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
                    
                    const lang = "<?= $lang ?>";
                    const loginAlertMsg = lang === 'cn' ? "请登录后发表评论。" : (lang === 'en' ? "Please log in to post a comment." : (lang === 'jp' ? "コメントを投稿するにはログインしてください。" : "กรุณาเข้าสู่ระบบก่อนแสดงความคิดเห็น"));
                    const roleAlertMsg = lang === 'cn' ? "必须以查看者身份登录才能发表评论。" : (lang === 'en' ? "You must be logged in as a viewer to post a comment." : (lang === 'jp' ? "コメントを投稿するには、ビューアーとしてログインする必要があります。" : "ต้องเข้าสู่ระบบในฐานะ viewer เท่านั้น"));
                    const errorAlertMsg = lang === 'cn' ? "身份验证期间发生错误。" : (lang === 'en' ? "An error occurred during authentication." : (lang === 'jp' ? "認証中にエラーが発生しました。" : "เกิดข้อผิดพลาดในการยืนยันตัวตน"));
                    const successAlertMsg = lang === 'cn' ? "评论已成功保存。" : (lang === 'en' ? "Comment saved successfully." : (lang === 'jp' ? "コメントが正常に保存されました。" : "บันทึกความคิดเห็นเรียบร้อยแล้ว"));
                    const failAlertMsg = lang === 'cn' ? "发生错误：" : (lang === 'en' ? "An error occurred: " : (lang === 'jp' ? "エラーが発生しました：" : "เกิดข้อผิดพลาด: "));

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
                                    alert(successAlertMsg);
                                    document.getElementById("commentText").value = '';
                                } else {
                                    alert(failAlertMsg + result.message);
                                }
                            });
                        } else {
                            alert(roleAlertMsg);
                        }
                    })
                    .catch(err => {
                        console.error("Error verifying user:", err);
                        alert(errorAlertMsg);
                    });
                });

                // JavaScript for Copy Link functionality
                function copyLink() {
                    const pageUrl = "<?= $pageUrl ?>";
                    const lang = "<?= $lang ?>";
                    const successAlertMsg = lang === 'cn' ? "链接复制成功！" : (lang === 'en' ? "Link copied successfully!" : (lang === 'jp' ? "リンクが正常にコピーされました！" : "คัดลอกลิงก์เรียบร้อยแล้ว"));
                    const errorAlertMsg = lang === 'cn' ? "复制链接失败。请手动复制。" : (lang === 'en' ? "Failed to copy link. Please copy it manually." : (lang === 'jp' ? "リンクのコピーに失敗しました。手動でコピーしてください。" : "ไม่สามารถคัดลอกลิงก์ได้ กรุณาคัดลอกด้วยตนเอง"));
                    
                    navigator.clipboard.writeText(pageUrl).then(function() {
                        alert(successAlertMsg);
                    }, function() {
                        alert(errorAlertMsg);
                    });
                }
                </script>
            </div>
            </div>
    </div>

    <?php include 'template/footer.php'?>
    

    <script src="js/index_.js?v=<?php echo time();?>"></script>
    <script src="js/news/news_.js?v=<?php echo time();?>"></script>

</body>
</html>
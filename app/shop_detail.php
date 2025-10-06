<?php
require_once('../lib/connect.php');
global $conn;

// 1. กำหนดตัวแปรภาษา
$supported_langs = ['en', 'th', 'cn', 'jp', 'kr'];
$lang = isset($_GET['lang']) && in_array($_GET['lang'], $supported_langs) ? $_GET['lang'] : 'th';
$lang_suffix = '';
if ($lang !== 'th') {
    $lang_suffix = '_' . $lang;
}

// 2. กำหนดตัวแปรสำหรับชื่อคอลัมน์ตามภาษา
$subject_col = "subject_shop" . $lang_suffix;
$content_col = "content_shop" . $lang_suffix;
$project_subject_col = "subject_project" . $lang_suffix;
$project_description_col = "description_project" . $lang_suffix;

// กำหนดข้อความสำหรับภาษาต่างๆ
$translations = [
    'th' => [
        'product' => 'สินค้า',
        'share_page' => 'แชร์หน้านี้:',
        'copy_link' => 'คัดลอกลิงก์',
        'no_data' => 'ไม่มีข้อมูล',
        'invalid_id' => 'รหัสไม่ถูกต้อง',
        'related_projects' => 'โปรเจกต์ที่เกี่ยวข้องกับสินค้านี้',
        'project_image_alt' => 'รูปภาพโครงการ',
        'no_image_available' => 'ไม่มีรูปภาพ',
        'comments' => 'ความคิดเห็น',
        'email_notice' => 'อีเมลของคุณจะไม่แสดงให้คนอื่นเห็น ช่องข้อมูลจำเป็นถูกทำเครื่องหมาย *',
        'comment_placeholder' => 'ความคิดเห็น *',
        'post_comment' => 'แสดงความคิดเห็น',
        'login_to_comment' => 'กรุณาเข้าสู่ระบบก่อนแสดงความคิดเห็น',
        'viewer_only' => 'ต้องเข้าสู่ระบบในฐานะ viewer เท่านั้น',
        'error_verify' => 'เกิดข้อผิดพลาดในการยืนยันตัวตน',
        'copy_success' => 'คัดลอกลิงก์เรียบร้อยแล้ว',
        'copy_fail' => 'ไม่สามารถคัดลอกลิงก์ได้ กรุณาคัดลอกด้วยตนเอง',
        'contact_us' => 'สอบถาม/สั่งซื้อผลิตภัณฑ์ Trandar Acoustics ได้ที่'
    ],
    'en' => [
        'product' => 'Product',
        'share_page' => 'Share this page:',
        'copy_link' => 'Copy Link',
        'no_data' => 'No data found.',
        'invalid_id' => 'Invalid ID.',
        'related_projects' => 'Related projects for this product',
        'project_image_alt' => 'Project Image',
        'no_image_available' => 'No image available',
        'comments' => 'Comments',
        'email_notice' => 'Your email address will not be published. Required fields are marked *',
        'comment_placeholder' => 'Comment *',
        'post_comment' => 'Post Comment',
        'login_to_comment' => 'Please log in to comment',
        'viewer_only' => 'You must be logged in as a viewer to comment.',
        'error_verify' => 'An error occurred while verifying identity.',
        'copy_success' => 'Link copied successfully',
        'copy_fail' => 'Unable to copy link. Please copy it manually.',
        'contact_us' => 'Contact and order Trandar Acoustics products at'
    ],
    'cn' => [
        'product' => '产品',
        'share_page' => '分享此页面:',
        'copy_link' => '复制链接',
        'no_data' => '未找到数据。',
        'invalid_id' => '无效的ID。',
        'related_projects' => '与此产品相关的项目',
        'project_image_alt' => '项目图片',
        'no_image_available' => '没有可用的图片',
        'comments' => '评论',
        'email_notice' => '您的电子邮件地址不会被公开。必填字段已标记 *',
        'comment_placeholder' => '评论 *',
        'post_comment' => '发表评论',
        'login_to_comment' => '请登录后发表评论',
        'viewer_only' => '您必须以查看者身份登录才能发表评论。',
        'error_verify' => '验证身份时出错。',
        'copy_success' => '链接复制成功',
        'copy_fail' => '无法复制链接，请手动复制。',
        'contact_us' => '联系和订购Trandar Acoustics产品'
    ],
    'jp' => [
        'product' => '製品',
        'share_page' => 'このページを共有:',
        'copy_link' => 'リンクをコピー',
        'no_data' => 'データが見つかりませんでした。',
        'invalid_id' => '無効なIDです。',
        'related_projects' => 'この製品に関連するプロジェクト',
        'project_image_alt' => 'プロジェクト画像',
        'no_image_available' => '利用可能な画像はありません',
        'comments' => 'コメント',
        'email_notice' => 'メールアドレスが公開されることはありません。必須項目は * でマークされています',
        'comment_placeholder' => 'コメント *',
        'post_comment' => 'コメントを投稿',
        'login_to_comment' => 'コメントするにはログインしてください',
        'viewer_only' => 'コメントするにはビューアとしてログインする必要があります。',
        'error_verify' => '身元確認中にエラーが発生しました。',
        'copy_success' => 'リンクが正常にコピーされました',
        'copy_fail' => 'リンクをコピーできませんでした。手動でコピーしてください。',
        'contact_us' => 'Trandar Acoustics製品のお問い合わせ・ご注文はこちら'
    ],
    'kr' => [
        'product' => '제품',
        'share_page' => '이 페이지 공유:',
        'copy_link' => '링크 복사',
        'no_data' => '데이터를 찾을 수 없습니다.',
        'invalid_id' => '잘못된 ID입니다.',
        'related_projects' => '이 제품과 관련된 프로젝트',
        'project_image_alt' => '프로젝트 이미지',
        'no_image_available' => '사용 가능한 이미지가 없습니다',
        'comments' => '댓글',
        'email_notice' => '귀하의 이메일 주소는 공개되지 않습니다. 필수 입력란은 *로 표시되어 있습니다',
        'comment_placeholder' => '댓글 *',
        'post_comment' => '댓글 게시',
        'login_to_comment' => '댓글을 작성하려면 로그인하세요',
        'viewer_only' => '댓글을 작성하려면 뷰어로 로그인해야 합니다.',
        'error_verify' => '신원 확인 중 오류가 발생했습니다.',
        'copy_success' => '링크가 성공적으로 복사되었습니다',
        'copy_fail' => '링크를 복사할 수 없습니다. 수동으로 복사해 주세요.',
        'contact_us' => 'Trandar Acoustics 제품 문의 및 주문'
    ]
];
$t = $translations[$lang];

$subjectTitle = $t['product'];
$pageUrl = "";
$encodedId = "";
$decodedId = false;

if (isset($_GET['id'])) {
    $encodedId = $_GET['id'];
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $pageUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $decodedId = base64_decode(urldecode($encodedId));

    if ($decodedId !== false) {
        $stmt = $conn->prepare("SELECT `$subject_col` FROM dn_shop WHERE del = 0 AND shop_id = ?");
        $stmt->bind_param('i', $decodedId);
        $stmt->execute();
        $resultTitle = $stmt->get_result();
        if ($resultTitle->num_rows > 0) {
            $row = $resultTitle->fetch_assoc();
            $subjectTitle = $row[$subject_col];
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($subjectTitle); ?></title>

    <?php include 'inc_head.php'?>
    <link href="css/index_.css?v=<?php echo time();?>" rel="stylesheet">
    <link href="css/news_.css?v=<?php echo time();?>" rel="stylesheet">

    <style>
        img {
            max-width: 100%;
            height: auto;
        }
        .shop-content-display {
            font-family: sans-serif, "Roboto" !important;
            line-height: 1.6;
        }
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
            /* width: 40px; */
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
 <ul id="flag-dropdown-list" class="flag-dropdown" style="left: 74%;">
        </ul>
    <?php include 'template/header.php'?>
    <?php include 'template/navbar_slide.php'?>

    <div class="content-sticky" id="">
        <div class="container" style="max-width: 90%;">
            <div class="box-content">
                <div class="social-share" style="display: flex; flex-direction: column; align-items: flex-end; gap: 10px;">
                    <p data-translate="share" lang="th" style="margin: 0; font-size:18px; font-family: sans-serif;">แชร์หน้านี้:</p>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($pageUrl) ?>" target="_blank">
                            <img src="https://img.icons8.com/color/48/000000/facebook-new.png" alt="Share on Facebook">
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?= urlencode($pageUrl) ?>&text=<?= urlencode($subjectTitle) ?>" target="_blank">
                             <img style="height: 33px;  border-radius: 6px;"src="https://cdn.prod.website-files.com/5d66bdc65e51a0d114d15891/64cebdd90aef8ef8c749e848_X-EverythingApp-Logo-Twitter.jpg" alt="Share on Twitter">
                        </a>
                        <a href="https://social-plugins.line.me/lineit/share?url=<?= urlencode($pageUrl) ?>" target="_blank">
                            <img src="https://img.icons8.com/color/48/000000/line-me.png" alt="Share on Line">
                        </a>
                        <a href="https://pinterest.com/pin/create/button/?url=<?= urlencode($pageUrl) ?>" target="_blank">
                            <img src="https://img.icons8.com/color/48/000000/pinterest--v1.png" alt="Share on Pinterest">
                        </a>
                        <a href="https://www.instagram.com/" target="_blank">
                            <img src="https://img.icons8.com/fluency/48/instagram-new.png" alt="Share on Instagram">
                        </a>
                        <a href="https://www.tiktok.com/" target="_blank">
                            <img src="https://img.icons8.com/fluency/48/tiktok.png" alt="Share on TikTok">
                        </a>
                        <button class="copy-link-btn" onclick="copyLink()">
                            <i class="fas fa-link"></i> 
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="">
                        <?php
                        if ($decodedId !== false) {
                            $stmt = $conn->prepare("SELECT
                                dn.shop_id,
                                dn.`$subject_col`,
                                dn.`$content_col`,
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
                                    $content = $row[$content_col];
                                    $paths = explode(',', $row['pic_path']);
                                    $files = explode(',', $row['file_name']);

                                    // Replace img src with correct path
                                    foreach ($files as $index => $file) {
                                        $pattern = '/<img[^>]+data-filename="' . preg_quote($file, '/') . '"[^>]*>/i';
                                        if (preg_match($pattern, $content, $matches) && isset($paths[$index])) {
                                            $new_src = $paths[$index];
                                            $new_img_tag = preg_replace('/(<img[^>]+)(src="[^"]*")/i', '$1 src="' . htmlspecialchars($new_src) . '"', $matches[0]);
                                            $content = str_replace($matches[0], $new_img_tag, $content);
                                        }
                                    }

                                    echo '<div class="shop-content-display">';
                                    echo mb_convert_encoding($content, 'UTF-8', 'auto');
                                    echo '</div>';
                                }
                            } else {
                                echo htmlspecialchars($t['no_data']);
                            }

                            $stmt->close();
                        } else {
                            echo htmlspecialchars($t['invalid_id']);
                        }
                        ?>
                    </div>
                </div>

                <hr style="border-top: dashed 1px; margin: 20px 0;">
                 <div class="col-md-12 text-start" style="padding-bottom:3em;">
                <p data-translate="share" lang="th" style="margin: 0; padding-bottom: 10px; font-size:18px; font-family: sans-serif;">แชร์หน้านี้:</p>
                <div class="social-share" style="display: flex; align-items: center; gap: 10px;">
                    <button class="copy-link-btn" onclick="copyLink()">
                        <i class="fas fa-link"></i>
                    </button>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($pageUrl) ?>" target="_blank">
                        <img src="https://img.icons8.com/color/48/000000/facebook-new.png" alt="Share on Facebook">
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?= urlencode($pageUrl) ?>&text=<?= urlencode($subjectTitle) ?>" target="_blank">
                        <img style="height: 33px; border-radius: 6px;"src="https://cdn.prod.website-files.com/5d66bdc65e51a0d114d15891/64cebdd90aef8ef8c749e848_X-EverythingApp-Logo-Twitter.jpg" alt="Share on Twitter">
                    </a>
                    <a href="https://social-plugins.line.me/lineit/share?url=<?= urlencode($pageUrl) ?>" target="_blank">
                        <img src="https://img.icons8.com/color/48/000000/line-me.png" alt="Share on Line">
                    </a>
                    <a href="https://pinterest.com/pin/create/button/?url=<?= urlencode($pageUrl) ?>" target="_blank">
                        <img src="https://img.icons8.com/color/48/000000/pinterest--v1.png" alt="Share on Pinterest">
                    </a>
                    <a href="https://www.instagram.com/" target="_blank">
                        <img src="https://img.icons8.com/fluency/48/instagram-new.png" alt="Share on Instagram">
                    </a>
                    <a href="https://www.tiktok.com/" target="_blank">
                        <img src="https://img.icons8.com/fluency/48/tiktok.png" alt="Share on TikTok">
                    </a>
                    
                </div>
            </div>
                <div style="padding-left:50px;">
                    <hr style="border-top: dashed 1px; margin: 20px 0;">
                    <p><?= htmlspecialchars($t['contact_us']); ?></p>
                    <p>🛒 Website : <a href="https://www.trandar.com/store/app/index.php" target="_blank">www.trandar.com/store/</a></p>
                    <p>📱 Line OA : @Trandaraocoustic
                        <a href="https://lin.ee/yoSCNwF" target="_blank">https://lin.ee/yoSCNwF</a>
                    </p>
                    <p>📱 Line OA : @Trandarstore
                        <a href="https://lin.ee/xJr661u" target="_blank">https://lin.ee/xJr661u</a>
                    </p>
                    <p>☎️ Tel : 02-722-7007</p>
                </div>

                <?php
                if ($decodedId !== false) {
                    $stmt_project = $conn->prepare("
                        SELECT
                            dp.project_id,
                            dp.`$project_subject_col` AS subject,
                            dp.`$project_description_col` AS description,
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
                        echo '<h3 style="padding-top: 40px;">' . htmlspecialchars($t['related_projects']) . '</h3>';
                        echo '<div class="shop-wrapper-container">';
                        echo '<div class="scroll-btn left" onclick="scrollProject(\'left\')">&#10094;</div>';
                        echo '<div class="scroll-btn right" onclick="scrollProject(\'right\')">&#10095;</div>';
                        echo '<div class="shop-scroll" id="project-scroll-box">';

                        while ($row_project = $result_project->fetch_assoc()) {
                            $projectIdEncoded = urlencode(base64_encode($row_project['project_id']));
                            $project_link = "project_detail.php?id=" . $projectIdEncoded . "&lang=" . htmlspecialchars($lang);

                            $content = $row_project['content_project'];
                            $iframeSrc = null;
                            if (preg_match('/<iframe.*?src=["\'](.*?)["\'].*?>/i', $content, $matches)) {
                                $iframeSrc = isset($matches[1]) ? explode(',', $matches[1]) : null;
                            }
                            $iframe = isset($iframeSrc[0]) ? $iframeSrc[0] : null;

                            $paths = !empty($row_project['pic_path']) ? explode(',', $row_project['pic_path']) : [];
                            $image_path = !empty($paths) ? $paths[0] : null;
                            $placeholder_image = 'https://via.placeholder.com/300x220.png?text=' . urlencode($t['project_image_alt']);

                            echo '<div class="shop-card">';
                            echo '<a href="' . htmlspecialchars($project_link) . '" class="related-shop-box">';

                            if (!empty($iframe)) {
                                echo '<iframe frameborder="0" src="' . htmlspecialchars($iframe) . '" width="100%" height="220px" class="note-video-clip"></iframe>';
                            } else if (!empty($image_path)) {
                                echo '<div class="card-image-wrapper">';
                                echo '<img src="' . htmlspecialchars($image_path) . '" class="card-img-top" alt="' . htmlspecialchars($row_project['subject']) . '">';
                                echo '</div>';
                            } else {
                                echo '<div class="card-image-wrapper">';
                                echo '<img src="' . htmlspecialchars($placeholder_image) . '" class="card-img-top" alt="' . htmlspecialchars($t['no_image_available']) . '">';
                                echo '</div>';
                            }

                            echo '<div class="card-body">';
                            echo '<h5 class="card-title">' . htmlspecialchars($row_project['subject']) . '</h5>';
                            echo '<p class="card-text">' . htmlspecialchars($row_project['description']) . '</p>';
                            echo '</div>';
                            echo '</a>';
                            echo '</div>';
                        }
                        echo '</div>';
                        echo '</div>';
                    }
                    $stmt_project->close();
                }
                ?>

                <h3 style ="padding-top: 40px;"><?= htmlspecialchars($t['comments']); ?></h3>
                <p><?= htmlspecialchars($t['email_notice']); ?></p>
                <form id="commentForm" style="max-width: 600px;">
                    <textarea id="commentText" name="comment" rows="5" required placeholder="<?= htmlspecialchars($t['comment_placeholder']); ?>"
                        style="width: 100%; padding: 12px; margin-bottom: 3px; border: 1px solid #ccc; border-radius: 6px;"></textarea><br>
                    <button type="submit"
                        style="background-color: red; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer;">
                        <?= htmlspecialchars($t['post_comment']); ?>
                    </button>
                </form>

                <script>
                document.getElementById("commentForm").addEventListener("submit", function(e) {
                    e.preventDefault();

                    const jwt = sessionStorage.getItem("jwt");
                    const comment = document.getElementById("commentText").value;
                    const pageUrl = window.location.pathname;

                    if (!jwt) {
                        alert("<?= htmlspecialchars($t['login_to_comment']); ?>");
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
                                    alert("<?= htmlspecialchars($t['copy_success']); ?>");
                                    document.getElementById("commentText").value = '';
                                } else {
                                    alert("<?= htmlspecialchars($t['error_verify']); ?>: " + result.message);
                                }
                            });
                        } else {
                            alert("<?= htmlspecialchars($t['viewer_only']); ?>");
                        }
                    })
                    .catch(err => {
                        console.error("Error verifying user:", err);
                        alert("<?= htmlspecialchars($t['error_verify']); ?>");
                    });
                });

                function scrollProject(direction) {
                    const box = document.getElementById('project-scroll-box');
                    const scrollAmount = 300 + 10;
                    if (direction === 'left') {
                        box.scrollLeft -= scrollAmount;
                    } else {
                        box.scrollLeft += scrollAmount;
                    }
                }

                function copyLink() {
                    const pageUrl = "<?= htmlspecialchars($pageUrl) ?>";
                    navigator.clipboard.writeText(pageUrl).then(function() {
                        alert("<?= htmlspecialchars($t['copy_success']); ?>");
                    }, function() {
                        alert("<?= htmlspecialchars($t['copy_fail']); ?>");
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
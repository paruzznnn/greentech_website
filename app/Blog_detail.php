<?php
require_once('../lib/connect.php');
global $conn;

// ----------------------------------------------------
// ส่วนที่ 1: กำหนดภาษาและค่าเริ่มต้น
// ----------------------------------------------------
// รองรับภาษาไทย (th), อังกฤษ (en), และจีน (cn)
$lang = 'th'; // Set a default value first
if (isset($_GET['lang'])) {
    if ($_GET['lang'] === 'en') {
        $lang = 'en';
    } elseif ($_GET['lang'] === 'cn') { // Added Chinese language check
        $lang = 'cn';
    }
}

$subjectTitle = ($lang === 'en') ? "Blog" : (($lang === 'cn') ? "博客" : "บล็อก");
$pageUrl = "";

// ----------------------------------------------------
// ส่วนที่ 2: ดึงข้อมูลบล็อกหลัก
// ----------------------------------------------------
if (isset($_GET['id'])) {
    $encodedId = $_GET['id'];
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $pageUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    
    $decodedId = base64_decode(urldecode($_GET['id']));

    if ($decodedId !== false) {
        $subjectColumn = ($lang === 'en') ? 'subject_blog_en' : (($lang === 'cn') ? 'subject_blog_cn' : 'subject_blog');
        $stmt = $conn->prepare("SELECT {$subjectColumn} FROM dn_blog WHERE del = 0 AND blog_id = ?");
        $stmt->bind_param('i', $decodedId);
        $stmt->execute();
        $resultTitle = $stmt->get_result();
        if ($resultTitle->num_rows > 0) {
            $row = $resultTitle->fetch_assoc();
            $subjectTitle = $row[$subjectColumn];
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

        /* --- New CSS for Project & Shop Sliders --- */
        .project-wrapper-container {
            position: relative;
            max-width: 1280px;
            margin: 0;
            overflow: hidden;
            padding: 0 20px;
            margin-bottom: 20px;
            box-sizing: border-box;
        }

        .project-scroll {
            display: flex;
            gap: 40px;
            scroll-behavior: smooth;
            overflow-x: auto;
            padding-bottom: 1rem;
            scrollbar-width: none;
        }
        .project-scroll::-webkit-scrollbar {
            display: none;
        }

        .project-card {
            flex: 0 0 350px;
            max-width: 350px;
            display: flex;
            flex-direction: column;
            border: 1px solid #ddd;
            border-radius: 6px;
            overflow: hidden;
            background-color: #f9f9f9;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 10px;
            padding-bottom: 10px;
        }
        
        .card-image-wrapper {
            height: 220px;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .card-img-top {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-body {
            padding: 0 10px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            flex-grow: 1;
            margin-bottom: 40px;
        }
        .card-body-shop {
            padding: 0 10px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            flex-grow: 1;
            margin-bottom: 10px;
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
        
        h6.shop-title {
            margin: 20px 10px 10px 10px;
            padding-top: 10px;
        }
        
        .shop-wrapper-container {
            position: relative;
            padding: 0 10px;
            margin-top: 10px; 
            margin-bottom: 10px;
            overflow: visible; 
        }

        .shop-scroll {
            display: flex;
            gap: 10px;
            scroll-behavior: smooth;
            overflow-x: auto;
            padding: 10px 0;
            scrollbar-width: none;
        }
        .shop-scroll::-webkit-scrollbar {
            display: none;
        }
        
        .shop-card {
            flex: 0 0 180px;
            max-width: 180px;
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
        
        .shop-card .card-image-wrapper {
            height: 120px;
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
            color: #555;
            display: none;
        }
        .scroll-btn.show {
            display: block;
        }

        .scroll-btn.left {
            left: 5px;
        }

        .scroll-btn.right {
            right: 5px;
        }

        .shop-wrapper-container .scroll-btn {
            background: rgba(204, 204, 204, 0.5);
            border: 1px solid rgba(170, 170, 170, 0.3);
            box-shadow: none;
            font-size: 1rem;
            color: #555;
            width: 30px;
            height: 30px;
            line-height: 30px;
            opacity: 0.8;
            transition: all 0.2s ease;
        }
        
        .shop-wrapper-container .scroll-btn.left {
            left: 0px;
        }
        
        .shop-wrapper-container .scroll-btn.right {
            right: 0px;
        }

        .shop-wrapper-container .scroll-btn:hover {
            opacity: 1;
            background: rgba(204, 204, 204, 0.8);
            color: #333;
        }

        /* --- Social Share CSS (unchanged) --- */
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
                <p><?php echo ($lang === 'en') ? 'Share this page:' : (($lang === 'cn') ? '分享此页面：' : 'แชร์หน้านี้:'); ?></p>
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
                <button class="copy-link-btn" onclick="copyLink()"><?php echo ($lang === 'en') ? 'Copy Link' : (($lang === 'cn') ? '复制链接' : 'คัดลอกลิงก์'); ?></button>
                </div>

                <div class="row">
                    <div class="">
                        <?php
                            if (isset($_GET['id'])) {
                                $decodedId = base64_decode(urldecode($_GET['id']));
                                
                                if ($decodedId !== false) {
                                    $contentColumn = ($lang === 'en') ? 'content_blog_en' : (($lang === 'cn') ? 'content_blog_cn' : 'content_blog');
                                    $stmt = $conn->prepare("SELECT 
                                        dn.blog_id, 
                                        dn.subject_blog, 
                                        dn.subject_blog_en,
                                        dn.subject_blog_cn,
                                        dn.{$contentColumn} AS content_blog, 
                                        dn.date_create, 
                                        GROUP_CONCAT(dnc.file_name) AS file_name,
                                        GROUP_CONCAT(dnc.api_path) AS pic_path
                                        FROM dn_blog dn
                                        LEFT JOIN dn_blog_doc dnc ON dn.blog_id = dnc.blog_id
                                        WHERE dn.blog_id = ?
                                        GROUP BY dn.blog_id");

                                    $stmt->bind_param('i', $decodedId); 
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $content = $row['content_blog'];
                                            $paths = !empty($row['pic_path']) ? explode(',', $row['pic_path']) : [];
                                            $files = !empty($row['file_name']) ? explode(',', $row['file_name']) : [];
                                            $found = false;

                                            foreach ($files as $index => $file) {
                                                $pattern = '/<img[^>]+data-filename="' . preg_quote($file, '/') . '"[^>]*>/i';

                                                if (preg_match($pattern, $content, $matches)) {
                                                    $new_src = $paths[$index] ?? ''; // Use null coalescing to avoid errors if index is missing
                                                    $new_img_tag = preg_replace('/(<img[^>]+)(src="[^"]*")/i', '$1 src="' . $new_src . '"', $matches[0]);
                                                    $content = str_replace($matches[0], $new_img_tag, $content);
                                                    $found = true;
                                                }
                                            }

                                            echo '<div class="shop-content-display">';
                                            echo mb_convert_encoding($content, 'UTF-8', 'auto');
                                            echo '</div>';
                                        }
                                    } else {
                                        echo ($lang === 'en') ? "No data found." : (($lang === 'cn') ? "未找到数据" : "ไม่มีข้อมูล");
                                    }

                                    $stmt->close(); 
                                } else {
                                    echo ($lang === 'en') ? "Invalid ID." : (($lang === 'cn') ? "无效ID" : "Invalid ID.");
                                }
                            }
                        ?>
                    </div>
                </div>
                
                <hr style="border-top: dashed 1px; margin: 20px 0;">
                <div class="social-share">
                    <p><?php echo ($lang === 'en') ? 'Share this page:' : (($lang === 'cn') ? '分享此页面：' : 'แชร์หน้านี้:'); ?></p>
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
                    <button class="copy-link-btn" onclick="copyLink()"><?php echo ($lang === 'en') ? 'Copy Link' : (($lang === 'cn') ? '复制链接' : 'คัดลอกลิงก์'); ?></button>
                </div>
                <div style="padding-left:50px;">
                    <hr style="border-top: dashed 1px; margin: 20px 0;">
                    
                    <p><?= ($lang === 'en') ? "Inquire/Order Trandar Acoustics products at" : (($lang === 'cn') ? "咨询/订购 Trandar Acoustics 产品：" : "สอบถาม/สั่งซื้อผลิตภัณฑ์ Trandar Acoustics ได้ที่") ?></p>
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
                        $projectSubjectColumn = ($lang === 'en') ? 'dp.subject_project_en' : (($lang === 'cn') ? 'dp.subject_project_cn' : 'dp.subject_project');
                        $projectDescColumn = ($lang === 'en') ? 'dp.description_project_en' : (($lang === 'cn') ? 'dp.description_project_cn' : 'dp.description_project');
                        
                        $stmt_project = $conn->prepare("
                            SELECT 
                                dp.project_id, 
                                {$projectSubjectColumn} AS subject_project, 
                                {$projectDescColumn} AS description_project,
                                GROUP_CONCAT(dnc.api_path) AS pic_path
                            FROM dn_project dp
                            JOIN dn_blog_project dbp ON dp.project_id = dbp.project_id
                            LEFT JOIN dn_project_doc dnc ON dp.project_id = dnc.project_id AND dnc.del = '0' AND dnc.status = '1'
                            WHERE dbp.blog_id = ?
                            GROUP BY dp.project_id
                        ");
                        $stmt_project->bind_param('i', $decodedId);
                        $stmt_project->execute();
                        $result_project = $stmt_project->get_result();
                        $project_cards_data = $result_project->fetch_all(MYSQLI_ASSOC);

                        if ($result_project->num_rows > 0) {
                            echo '<h3 style="padding-top: 40px;">' . ($lang === 'en' ? "Related Projects" : (($lang === 'cn') ? "相关项目" : "โปรเจกต์ที่เกี่ยวข้องกับบทความนี้")) . '</h3>';
                            echo '<div class="project-wrapper-container">';
                            echo '<div class="scroll-btn left" id="project-scroll-left" onclick="scrollProject(\'left\')">&#10094;</div>';
                            echo '<div class="scroll-btn right" id="project-scroll-right" onclick="scrollProject(\'right\')">&#10095;</div>';
                            echo '<div class="project-scroll" id="project-scroll-box">';
                            
                            foreach ($project_cards_data as $row_project) {
                                $projectIdEncoded = urlencode(base64_encode($row_project['project_id']));
                                $project_link = "project_detail.php?id=" . $projectIdEncoded . "&lang=" . $lang;
                                $paths_project = !empty($row_project['pic_path']) ? explode(',', $row_project['pic_path']) : [];
                                $image_path_project = !empty($paths_project) ? $paths_project[0] : null;
                                $placeholder_image = 'https://via.placeholder.com/350x220.png?text=Project+Image';
                                
                                echo '<div class="project-card">';
                                echo '<a href="' . htmlspecialchars($project_link) . '" class="related-project-box" style="text-decoration: none; color: inherit;">';
                                echo '<div class="card-image-wrapper">';
                                echo '<img src="' . htmlspecialchars($image_path_project ?: $placeholder_image) . '" class="card-img-top" alt="' . htmlspecialchars($row_project['subject_project']) . '">';
                                echo '</div>';
                                echo '<div class="card-body" style="padding-left:0; padding-right:0;">';
                                echo '<h5 class="card-title">' . htmlspecialchars($row_project['subject_project']) . '</h5>';
                                echo '<p class="card-text">' . htmlspecialchars($row_project['description_project']) . '</p>';
                                echo '</div>';
                                echo '</a>';
                                
                                // Start of related shops for this project
                                $shopSubjectColumn = ($lang === 'en') ? 'ds.subject_shop_en' : (($lang === 'cn') ? 'ds.subject_shop_cn' : 'ds.subject_shop');
                                $shopDescColumn = ($lang === 'en') ? 'ds.description_shop_en' : (($lang === 'cn') ? 'ds.description_shop_cn' : 'ds.description_shop');
                                $stmt_shop = $conn->prepare("
                                    SELECT 
                                        ds.shop_id, 
                                        {$shopSubjectColumn} AS subject_shop, 
                                        {$shopDescColumn} AS description_shop,
                                        ds.content_shop,
                                        GROUP_CONCAT(dnd.api_path) AS pic_path
                                    FROM dn_shop ds
                                    JOIN dn_project_shop dps ON ds.shop_id = dps.shop_id
                                    LEFT JOIN dn_shop_doc dnd ON ds.shop_id = dnd.shop_id AND dnd.del = '0' AND dnd.status = '1'
                                    WHERE dps.project_id = ?
                                    GROUP BY ds.shop_id
                                ");
                                $stmt_shop->bind_param('i', $row_project['project_id']);
                                $stmt_shop->execute();
                                $result_shop = $stmt_shop->get_result();
                                $shop_count = $result_shop->num_rows;

                                if ($shop_count > 0) {
                                    echo '<h6 class="shop-title">' . ($lang === 'en' ? "Products used in this project" : (($lang === 'cn') ? "本项目中使用的产品" : "สินค้าที่ใช้ในโปรเจกต์นี้")) . '</h6>';
                                    echo '<div class="shop-wrapper-container">';
                                    echo '<div class="scroll-btn left" id="shop-scroll-left-' . $row_project['project_id'] . '" onclick="scrollShop(\'shop-scroll-' . $row_project['project_id'] . '\', \'left\')">&#10094;</div>';
                                    echo '<div class="scroll-btn right" id="shop-scroll-right-' . $row_project['project_id'] . '" onclick="scrollShop(\'shop-scroll-' . $row_project['project_id'] . '\', \'right\')">&#10095;</div>';
                                    echo '<div class="shop-scroll" id="shop-scroll-' . $row_project['project_id'] . '">';
                                    
                                    while ($row_shop = $result_shop->fetch_assoc()) {
                                        $shopIdEncoded = urlencode(base64_encode($row_shop['shop_id']));
                                        $shop_link = "shop_detail.php?id=" . $shopIdEncoded . "&lang=" . $lang;
                                        
                                        $paths_shop = !empty($row_shop['pic_path']) ? explode(',', $row_shop['pic_path']) : [];
                                        $image_path_shop = !empty($paths_shop) ? $paths_shop[0] : null;
                                        $placeholder_image_shop = 'https://via.placeholder.com/180x120.png?text=Shop+Image';
                                        
                                        echo '<div class="shop-card">';
                                        echo '<a href="' . htmlspecialchars($shop_link) . '" class="related-shop-box">';
                                        
                                        echo '<div class="card-image-wrapper">';
                                        echo '<img src="' . htmlspecialchars($image_path_shop ?: $placeholder_image_shop) . '" class="card-img-top" alt="' . htmlspecialchars($row_shop['subject_shop']) . '">';
                                        echo '</div>';

                                        echo '<div class="card-body-shop" style="padding: 8px;">';
                                        echo '<h6 class="card-title" style="font-size: 0.9rem;">' . htmlspecialchars($row_shop['subject_shop']) . '</h6>';
                                        echo '<p class="card-text" style="font-size: 0.8rem;">' . htmlspecialchars($row_shop['description_shop']) . '</p>';
                                        echo '</div>';
                                        echo '</a>';
                                        echo '</div>';
                                    }
                                    echo '</div>';
                                    echo '</div>';
                                }
                                $stmt_shop->close();
                                echo '</div>'; // close project-card
                            }
                            echo '</div>'; // close project-scroll
                            echo '</div>'; // close project-wrapper-container
                        }
                        $stmt_project->close();
                    }
                }
                ?>
                
                <h3 style ="padding-top: 40px;"><?= ($lang === 'en') ? "Comments" : (($lang === 'cn') ? "评论" : "ความคิดเห็น") ?></h3>
                <p><?= ($lang === 'en') ? "Your email will not be displayed. Required fields are marked with *" : (($lang === 'cn') ? "您的电子邮件将不会被公开。必填字段标有 *" : "อีเมลของคุณจะไม่แสดงให้คนอื่นเห็น ช่องข้อมูลจำเป็นถูกทำเครื่องหมาย *") ?></p>
                <form id="commentForm" style="max-width: 600px;">
                    <textarea id="commentText" name="comment" rows="5" required placeholder="<?= ($lang === 'en') ? "Comment *" : (($lang === 'cn') ? "评论 *" : "ความคิดเห็น *") ?>"
                        style="width: 100%; padding: 12px; margin-bottom: 3px; border: 1px solid #ccc; border-radius: 6px;"></textarea><br>
                    <button type="submit"
                        style="background-color: red; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer;">
                        <?= ($lang === 'en') ? "Post Comment" : (($lang === 'cn') ? "发表评论" : "แสดงความคิดเห็น") ?>
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
                                        alert("<?= ($lang === 'en') ? "Comment saved successfully." : (($lang === 'cn') ? "评论保存成功。" : "บันทึกความคิดเห็นเรียบร้อยแล้ว") ?>");
                                        document.getElementById("commentText").value = '';
                                    } else {
                                        alert("<?= ($lang === 'en') ? "An error occurred: " : (($lang === 'cn') ? "发生错误：" : "เกิดข้อผิดพลาด: ") ?>" + result.message);
                                    }
                                });
                            } else {
                                alert("<?= ($lang === 'en') ? "You must be logged in as a viewer to comment." : (($lang === 'cn') ? "您必须以 viewer 身份登录才能发表评论。" : "ต้องเข้าสู่ระบบในฐานะ viewer เท่านั้น") ?>");
                            }
                        })
                        .catch(err => {
                            console.error("Error verifying user:", err);
                            alert("<?= ($lang === 'en') ? "Error verifying identity." : (($lang === 'cn') ? "身份验证出错。" : "เกิดข้อผิดพลาดในการยืนยันตัวตน") ?>");
                        });
                    });

                    function scrollProject(direction) {
                        const box = document.getElementById('project-scroll-box');
                        const scrollAmount = 350 + 40; // card width + gap
                        if (direction === 'left') {
                            box.scrollLeft -= scrollAmount;
                        } else {
                            box.scrollLeft += scrollAmount;
                        }
                    }
                    
                    function scrollShop(containerId, direction) {
                        const container = document.getElementById(containerId);
                        const scrollAmount = 180 + 10; // card width + gap
                        if (direction === 'left') {
                            container.scrollLeft -= scrollAmount;
                        } else {
                            container.scrollLeft += scrollAmount;
                        }
                    }
                    
                    function copyLink() {
                        const pageUrl = "<?= $pageUrl ?>";
                        navigator.clipboard.writeText(pageUrl).then(function() {
                            alert("<?= ($lang === 'en') ? "Link copied successfully." : (($lang === 'cn') ? "链接复制成功。" : "คัดลอกลิงก์เรียบร้อยแล้ว") ?>");
                        }, function() {
                            alert("<?= ($lang === 'en') ? "Could not copy link. Please copy manually." : (($lang === 'cn') ? "无法复制链接。请手动复制。" : "ไม่สามารถคัดลอกลิงก์ได้ กรุณาคัดลอกด้วยตนเอง") ?>");
                        });
                    }

                    function toggleScrollButtons() {
                        const projectScrollBox = document.getElementById('project-scroll-box');
                        if(projectScrollBox) {
                            const projectCards = projectScrollBox.querySelectorAll('.project-card');
                            const projectLeftBtn = document.getElementById('project-scroll-left');
                            const projectRightBtn = document.getElementById('project-scroll-right');

                            if (projectCards.length > 3) {
                                projectLeftBtn.classList.add('show');
                                projectRightBtn.classList.add('show');
                            } else {
                                projectLeftBtn.classList.remove('show');
                                projectRightBtn.classList.remove('show');
                            }
                        }
                        
                        document.querySelectorAll('.shop-scroll').forEach(shopContainer => {
                            const shopCards = shopContainer.querySelectorAll('.shop-card');
                            const containerId = shopContainer.id;
                            const shopLeftBtn = document.getElementById('shop-scroll-left-' + containerId.replace('shop-scroll-', ''));
                            const shopRightBtn = document.getElementById('shop-scroll-right-' + containerId.replace('shop-scroll-', ''));
                            
                            if (shopCards.length > 4) { // Check if there are more than 4 cards to enable scrolling on a desktop view
                                if(shopLeftBtn) shopLeftBtn.classList.add('show');
                                if(shopRightBtn) shopRightBtn.classList.add('show');
                            } else {
                                if(shopLeftBtn) shopLeftBtn.classList.remove('show');
                                if(shopRightBtn) shopRightBtn.classList.remove('show');
                            }
                        });
                    }
                    
                    window.addEventListener('resize', toggleScrollButtons);
                    window.addEventListener('DOMContentLoaded', toggleScrollButtons);
                </script>
            </div>
        </div>
    </div>

    
    <?php include 'template/footer.php'?>
    <script src="js/index_.js?v=<?php echo time();?>"></script>
    <script src="js/Blog/Blog_.js?v=<?php echo time();?>"></script>
</body>
</html>
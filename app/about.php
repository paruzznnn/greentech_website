<?php
// เริ่มการใช้งาน Session ต้องอยู่บรรทัดแรกสุดของไฟล์
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once('../lib/connect.php');
global $conn;

// --- ส่วนที่แก้ไข: จัดการภาษาด้วย Session ---
// 1. ตรวจสอบพารามิเตอร์ lang ใน URL
$supportedLangs = ['en', 'cn', 'jp', 'kr'];
if (isset($_GET['lang'])) {
    if (in_array($_GET['lang'], $supportedLangs)) {
        // บันทึกค่า lang ที่ถูกต้องใน Session
        $_SESSION['lang'] = $_GET['lang'];
    } else {
        // ถ้า lang ไม่ถูกต้อง ให้ตั้งค่าเป็น 'th'
        $_SESSION['lang'] = 'th';
    }
}

// 2. กำหนดค่า lang จาก Session หรือค่าเริ่มต้น 'th'
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'th';
// --- สิ้นสุดส่วนที่แก้ไข ---

$contentColumn = "content";
if ($lang !== 'th') {
    $contentColumn .= '_' . $lang;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>เกี่ยวกับเรา | Trandar</title>
    <?php include 'inc_head.php'; ?>
    <link href="css/index_.css?v=<?= time(); ?>" rel="stylesheet">
</head>
<body>
     <ul id="flag-dropdown-list" class="flag-dropdown" style="left: 74%;">
        </ul>
<?php include 'template/header.php'; ?>
<?php include 'template/navbar_slide.php'; ?>

<div class="content-sticky" id="page_about">
    <div class="container" style="max-width: 90%;">
        <div class="box-content" style="padding-top: 2em;">
            <?php
            $query = "SELECT `type`, `image_url`, `author`, `position`, `{$contentColumn}` AS content FROM about_content ORDER BY id ASC";
            $result = $conn->query($query);
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $content = $row['content'];
                    $paths = [];
                    if ($row['type'] === 'image' && !empty($row['image_url'])) {
                        $paths = explode(',', $row['image_url']);
                        
                        // Note: The commented-out code is for a specific scenario where image URLs are embedded in the content.
                        // For a clean separation, it's better to use the image_url column directly.
                    }

                    echo '<div class="row">';
                    if ($row['type'] === 'text') {
                        echo '<div class="col-12">' . $content . '</div>';
                    } elseif ($row['type'] === 'image') {
                        echo '<div class="col-md-6"><img style="width:100%;" src="' . $row['image_url'] . '"></div>';
                        echo '<div class="col-md-6">' . $content . '</div>';
                    } elseif ($row['type'] === 'quote') {
                        echo '
                            <div style="text-align: center; padding: 40px 20px; font-style: italic; font-size: 25px; position: relative; width: 100%;">
                                <div style="font-size: 40px; color: #ccc; position: absolute; left: 10px; top: 0;">“</div>
                                <p style="margin: 0 40px;">' . $content . '</p>
                                <div style="margin-top: 20px; font-style: normal;">
                                    <strong>' . $row['author'] . '</strong><br>' . $row['position'] . '
                                </div>
                            </div>';
                    }
                    echo '</div><hr>';
                }
            } else {
                echo "No content found.";
            }
            ?>
        </div>
    </div>
</div>

<?php include 'template/footer.php'; ?>
<script src="js/index_.js?v=<?= time(); ?>"></script>
</body>
</html>
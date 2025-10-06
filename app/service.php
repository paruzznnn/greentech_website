<?php
// เริ่มการใช้งาน Session ต้องอยู่บรรทัดแรกสุดของไฟล์
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once('../lib/connect.php');
global $conn;

// 1. ตรวจสอบพารามิเตอร์ lang ใน URL และบันทึกใน Session
$supportedLangs = ['en', 'cn', 'jp', 'kr'];
if (isset($_GET['lang'])) {
    if (in_array($_GET['lang'], $supportedLangs)) {
        $_SESSION['lang'] = $_GET['lang'];
    } else {
        $_SESSION['lang'] = 'th'; // ค่าเริ่มต้นเป็นภาษาไทย
    }
}

// 2. กำหนดค่า lang จาก Session หรือค่าเริ่มต้น 'th'
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'th';

// 3. กำหนดชื่อคอลัมน์เนื้อหาตามภาษาที่เลือก
$contentColumn = "content";
if ($lang !== 'th') {
    $contentColumn .= '_' . $lang;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>บริการของเรา | Trandar</title>
    <?php include 'inc_head.php'; ?>
    <link href="css/index_.css?v=<?= time(); ?>" rel="stylesheet">
</head>
<body>
 <ul id="flag-dropdown-list" class="flag-dropdown" style="left: 74%;">
        </ul>
<?php include 'template/header.php'; ?>
<?php include 'template/navbar_slide.php'; ?>

<div class="content-sticky" id="page_service">
    <div class="container" style="max-width: 90%;">
        <div class="box-content" style="padding-top: 2em;">

            <?php
            // ดึงข้อมูลจากฐานข้อมูล
            $query = "SELECT `id`, `type`, `image_url`, `author`, `position`, `{$contentColumn}` AS content FROM service_content ORDER BY id ASC";
            $result = $conn->query($query);
            
            if ($result->num_rows > 0) {
                // เริ่มวนแสดงผลแต่ละบล็อก
                while ($row = $result->fetch_assoc()) {
                    $type = $row['type'];
                    $text = $row['content'];
                    $image = $row['image_url'];
                    $author = $row['author'];
                    $position = $row['position'];

                    echo '<div class="row">';

                    // ถ้าเป็น quote
                    if ($type === 'quote') {
                        echo '
                            <div style="text-align: center; padding: 40px 20px; font-style: italic; font-size: 25px; position: relative; width: 100%;">
                                <div style="font-size: 40px; color: #ccc; position: absolute; left: 10px; top: 0;">“</div>
                                <p style="margin: 0 40px;">' . $text . '</p>
                                <div style="margin-top: 20px; font-style: normal;">
                                    <strong>' . htmlspecialchars($author) . '</strong><br>' . htmlspecialchars($position) . '
                                </div>
                            </div>';
                    }

                    // ถ้าเป็น image + text
                    elseif ($type === 'image') {
                        echo '<div class="col-md-6">';
                        echo '<img style="width: 100%;" src="' . htmlspecialchars($image) . '" alt="">';
                        echo '</div>';
                        echo '<div class="col-md-6">';
                        echo '<p>' . $text . '</p>';
                        echo '</div>';
                    }

                    // ถ้าเป็น text ธรรมดา
                    else {
                        echo '<div class="col-12">';
                        echo '<p>' . $text . '</p>';
                        echo '</div>';
                    }

                    echo '</div><hr>'; // close row and add hr
                }
            } else {
                echo '<div class="col-12"><p>No content found.</p></div>';
            }
            ?>
        </div>
    </div>
</div>

<?php include 'template/footer.php'; ?>
<script src="js/index_.js?v=<?= time(); ?>"></script>

</body>
</html>
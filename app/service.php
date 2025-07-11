<!DOCTYPE html>
<html>
<head>
    <?php include 'inc_head.php'; ?>
    <link href="css/index_.css?v=<?= time(); ?>" rel="stylesheet">
</head>
<body>

<?php include 'template/header.php'; ?>
<?php include 'template/navbar_slide.php'; ?>

<?php
require_once('../lib/connect.php');

$content = '<div class="content-sticky" id="page_about">';
$content .= '<div class="container">';
$content .= '<div class="box-content">';

// ดึงข้อมูลจากฐานข้อมูล
$sql = "SELECT * FROM service_content ORDER BY id ASC";
$result = $conn->query($sql);

// เริ่มวนแสดงผลแต่ละบล็อก
while ($row = $result->fetch_assoc()) {
    $type = $row['type'];
    $text = $row['content'];
    $image = $row['image_url'];
    $author = $row['author'];
    $position = $row['position'];

    $content .= '<div class="row">';

    // ถ้าเป็น quote
    if ($type === 'quote') {
        $content .= '
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
        $content .= '<div class="col-md-6">';
        $content .= '<img style="width: 100%;" src="' . htmlspecialchars($image) . '" alt="">';
        $content .= '</div>';
        $content .= '<div class="col-md-6">';
        $content .= '<p>' . $text . '</p>';
        $content .= '</div>';
        $content .= '<div class="col-12"><hr></div>';
    }

    // ถ้าเป็น text ธรรมดา
    else {
        $content .= '<div class="col-12">';
        $content .= '<p>' . $text . '</p>';
        $content .= '</div>';
        $content .= '<div class="col-12"><hr></div>';
    }

    $content .= '</div>'; // close row
}

$content .= '</div></div></div>';

echo $content;
?>

<?php include 'template/footer.php'; ?>
<script src="js/index_.js?v=<?= time(); ?>"></script>

</body>
</html>

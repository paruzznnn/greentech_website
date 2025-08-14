<?php
require_once('../lib/connect.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>เกี่ยวกับเรา | Trandar</title>
    <?php include 'inc_head.php'; ?>
    <link href="css/index_.css?v=<?= time(); ?>" rel="stylesheet">
</head>
<body>
<?php include 'template/header.php'; ?>
<?php include 'template/navbar_slide.php'; ?>

<div class="content-sticky" id="page_about">
    <div class="container" style="max-width: 90%;">
        <div class="box-content">
            <?php
            $result = $conn->query("SELECT * FROM about_content ORDER BY id ASC");
            while ($row = $result->fetch_assoc()) {
                echo '<div class="row">';
                if ($row['type'] === 'text') {
                    echo '<div class="col-12">' . $row['content'] . '</div>';
                } elseif ($row['type'] === 'image') {
                    echo '<div class="col-md-6"><img style="width:100%;" src="' . $row['image_url'] . '"></div>';
                    echo '<div class="col-md-6">' . $row['content'] . '</div>';
                } elseif ($row['type'] === 'quote') {
                    echo '
                        <div style="text-align: center; padding: 40px 20px; font-style: italic; font-size: 25px; position: relative; width: 100%;">
                            <div style="font-size: 40px; color: #ccc; position: absolute; left: 10px; top: 0;">“</div>
                            <p style="margin: 0 40px;">' . $row['content'] . '</p>
                            <div style="margin-top: 20px; font-style: normal;">
                                <strong>' . $row['author'] . '</strong><br>' . $row['position'] . '
                            </div>
                            
                        </div>';
                }
                echo '</div><hr>';
            }
            ?>
        </div>
    </div>
</div>

<?php include 'template/footer.php'; ?>
<script src="js/index_.js?v=<?= time(); ?>"></script>
</body>
</html>

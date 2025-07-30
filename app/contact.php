<?php
// ** ส่วนเพิ่มใหม่: ดึงข้อมูล Contact จาก database **
// Assuming contact.php is in 'app/' and 'lib/' is in the root of 'trandar/'
// From 'app/contact.php' to 'lib/connect.php' you need to go up one directory (..)
require_once(__DIR__ . '/../lib/connect.php'); //
require_once(__DIR__ . '/../lib/base_directory.php'); //

global $conn;
global $base_path;

$contact_settings = [];
$contact_id_for_display = 1;

$stmt_contact = $conn->prepare("SELECT * FROM contact_settings WHERE id = ?"); // Line 12
$stmt_contact->bind_param("i", $contact_id_for_display);
$stmt_contact->execute();
$result_contact = $stmt_contact->get_result();

if ($data = $result_contact->fetch_assoc()) {
    $contact_settings = $data;
} else {
    // กำหนดค่า default หากไม่พบข้อมูลใน database (ควรมีการ insert ข้อมูลเริ่มต้นไว้แล้ว)
    $contact_settings = [
        'company_name' => 'TRANDAR INTERNATIONAL CO., LTD.',
        'address' => '102 Phatthanakan 40, Suan Luang, Bangkok 10250',
        'phone' => '(+66)2 722 7007',
        'email' => 'info@trandar.com',
        'hours_weekday' => 'Monday – Friday 08:30 AM – 05:00 PM',
        'hours_saturday' => 'Saturday 08:30 AM – 12:00 PM',
        'link_image_path' => '../public/img/photo_2025-07-01_10-43-53.jpg', // Default image path
        'link_image_url' => 'https://www.origami.life/login.php#/register?comp=2&link=company&registype=P', // Default URL
        'map_iframe_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3875.7396441274373!2d100.62457107508995!3d13.734206386655812!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x311d61b2c53ba90d%3A0x4442a96acffee7e9!2z4LmB4LiX4Lij4LiZ4LiU4Liy4Lij4LmMIOC4reC4tOC4meC5gOC4leC4reC4o-C5jOC5gOC4meC4iuC4seC5iOC4meC5geC4meC4pSAoVHJhbmRhciBJbnRlcm5hdGlvbmFsIGNvLiBsdGQp!5e0!3m2!1sen!2sth!4v1730371693406!5m2!1sen!2sth', // Default map URL
    ];
}
$stmt_contact->close();
// ** สิ้นสุดส่วนเพิ่มใหม่ **
?>
<!DOCTYPE html>
<html>
<head>
    <?php include 'inc_head.php'?>
    <link href="css/index_.css?v=<?php echo time();?>" rel="stylesheet">
</head>
<body>

<?php include 'template/header.php'?>
<?php include 'template/navbar_slide.php'?>

<div class="content-sticky" id="page_contact">
    <div class="container">
        <div class="box-content">
            <div class="row">
                <div class="col-md-6">
                    <h1 style="font-size: 32px;">
                        <?= htmlspecialchars($contact_settings['company_name']) ?>
                    </h1>
                    <ul>
                        <li><?= htmlspecialchars($contact_settings['address']) ?></li>
                        <li><?= htmlspecialchars($contact_settings['phone']) ?></li>
                        <li><?= htmlspecialchars($contact_settings['email']) ?></li>
                        <li><?= htmlspecialchars($contact_settings['hours_weekday']) ?></li>
                        <li><?= htmlspecialchars($contact_settings['hours_saturday']) ?></li>
                    </ul>
                    <?php if ($contact_settings['link_image_path'] && $contact_settings['link_image_url']): ?>
                        <a href="<?= htmlspecialchars($contact_settings['link_image_url']) ?>" target="_blank">
                            <img src="<?= htmlspecialchars($contact_settings['link_image_path']) ?>" alt="Trandar Link" style="max-width: 70%; padding-top:18px">
                        </a>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <div id="map-plugin">
                        <?php if ($contact_settings['map_iframe_url']): ?>
                            <iframe src="<?= htmlspecialchars($contact_settings['map_iframe_url']) ?>" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        <?php else: ?>
                            <p class="text-muted">ยังไม่มีแผนที่แสดง</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'template/footer.php'?>
    
<script src="js/index_.js?v=<?php echo time();?>"></script>

</body>
</html>
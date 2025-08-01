<?php
// ไฟล์ contact.php
require_once('../lib/connect.php');
global $conn;

// ... ส่วนอื่นๆ ของหน้าเว็บ

?>
<!DOCTYPE html>
<html>
<head>
    <?php include 'inc_head.php' ?>
    <link href="css/index_.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/news_.css?v=<?php echo time(); ?>" rel="stylesheet">
</head>
<body>
    <?php
    global $conn;
    global $base_path;

    $contact_settings = [];
    $contact_id_for_display = 1;

    // แก้ไขส่วนนี้: ระบุคอลัมน์ที่ต้องการดึงมาอย่างชัดเจน
    $stmt_contact = $conn->prepare("SELECT `company_name`, `address`, `phone`, `email`, `hours_weekday`, `hours_saturday`, `link_image_path`, `link_image_url`, `map_iframe_url` FROM contact_settings WHERE id = ?"); 

    // ตรวจสอบว่า prepare statement สำเร็จหรือไม่ (เพื่อ debug)
    if ($stmt_contact === false) {
        die("Error preparing statement: " . $conn->error);
    }
    
    $stmt_contact->bind_param("i", $contact_id_for_display);
    $stmt_contact->execute();
    $result_contact = $stmt_contact->get_result();

    if ($data = $result_contact->fetch_assoc()) {
        // โค้ดส่วนนี้จะทำงานเมื่อดึงข้อมูลจาก DB ได้
        // เราจะนำค่าจาก DB มาใส่ใน $contact_settings
        // ใช้ ?? (Null Coalescing Operator) เพื่อกำหนดค่า default หากข้อมูลเป็น NULL
        $contact_settings = [
            'company_name' => $data['company_name'] ?? 'TRANDAR INTERNATIONAL CO., LTD.',
            'address' => $data['address'] ?? '102 Phatthanakan 40, Suan Luang, Bangkok 10250',
            'phone' => $data['phone'] ?? '(+66)2 722 7007',
            'email' => $data['email'] ?? 'info@trandar.com',
            'hours_weekday' => $data['hours_weekday'] ?? 'Monday – Friday 08:30 AM – 05:00 PM',
            'hours_saturday' => $data['hours_saturday'] ?? 'Saturday 08:30 AM – 12:00 PM',
            'link_image_path' => $data['link_image_path'] ?? '../public/img/photo_2025-07-01_10-43-53.jpg',
            'link_image_url' => $data['link_image_url'] ?? 'https://www.origami.life/login.php#/register?comp=2&link=company&registype=P',
            'map_iframe_url' => $data['map_iframe_url'] ?? 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3876.326848243615!2d100.62121757510793!3d13.700147986684803!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x311d6199a5c88b0f%3A0x4461158525b64c01!2sTRANDAR%20INTERNATIONAL%20CO.%2C%20LTD.!5e0!3m2!1sen!2sth!4v1722421371520!5m2!1sen!2sth" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade'
        ];
    } else {
        // กำหนดค่า default หากไม่พบข้อมูลใน database เลย (เช่น แถว id=1 ถูกลบ)
        $contact_settings = [
            'company_name' => 'TRANDAR INTERNATIONAL CO., LTD.',
            'address' => '102 Phatthanakan 40, Suan Luang, Bangkok 10250',
            'phone' => '(+66)2 722 7007',
            'email' => 'info@trandar.com',
            'hours_weekday' => 'Monday – Friday 08:30 AM – 05:00 PM',
            'hours_saturday' => 'Saturday 08:30 AM – 12:00 PM',
            'link_image_path' => '../public/img/photo_2025-07-01_10-43-53.jpg', // Default image path
            'link_image_url' => 'https://www.origami.life/login.php#/register?comp=2&link=company&registype=P', // Default URL
            'map_iframe_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3876.326848243615!2d100.62121757510793!3d13.700147986684803!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x311d6199a5c88b0f%3A0x4461158525b64c01!2sTRANDAR%20INTERNATIONAL%20CO.%2C%20LTD.!5e0!3m2!1sen!2sth!4v1722421371520!5m2!1sen!2sth" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade'
        ];
    }
    $stmt_contact->close();
    ?>

    <?php include 'template/header.php' ?>
    <?php include 'template/navbar_slide.php' ?>

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
                        <?php if (isset($contact_settings['link_image_path']) && isset($contact_settings['link_image_url'])): ?>
                            <a href="<?= htmlspecialchars($contact_settings['link_image_url']) ?>" target="_blank">
                                <img src="<?= htmlspecialchars($contact_settings['link_image_path']) ?>" alt="Trandar Link" style="max-width: 70%; padding-top:18px">
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <div id="map-plugin">
                            <?php if (isset($contact_settings['map_iframe_url']) && !empty($contact_settings['map_iframe_url'])): ?>
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

    <?php include 'template/footer.php' ?>
        
    <script src="js/index_.js?v=<?php echo time(); ?>"></script>

</body>
</html>
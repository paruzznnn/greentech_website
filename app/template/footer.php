<?php
// header-top-right (โค้ดส่วนนี้ไม่ได้แก้ไข)
$isProtocol = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
$isFile = ($isProtocol === 'http') ? '.php' : '';

$menuItems = [
    [
        'id' => 0,
        'icon' => 'fas fa-user-plus',
        'text' => '',
        'translate' => 'Sign_up',
        'link' => 'register' . $isFile,
        'modal_id' => ''
    ],
];

// ** ส่วนเพิ่มใหม่: ดึงข้อมูล Footer จาก database **
// require_once(__DIR__ . '/lib/connect.php'); // ปรับ Path ตามจริง
require_once(__DIR__ . '/lib/base_directory.php'); // ปรับ Path ตามจริง (สำหรับ $base_path)

global $conn;
global $base_path;

$footer_settings = [];
$footer_id_for_display = 1;

$stmt_footer = $conn->prepare("SELECT * FROM footer_settings WHERE id = ?");
$stmt_footer->bind_param("i", $footer_id_for_display);
$stmt_footer->execute();
$result_footer = $stmt_footer->get_result();

if ($data = $result_footer->fetch_assoc()) {
    $footer_settings = $data;
    // Decode JSON สำหรับ Social Links
    $footer_settings['social_links'] = json_decode($footer_settings['social_links_json'], true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $footer_settings['social_links'] = []; // ถ้า decode ไม่ได้ ให้เป็น array ว่าง
        error_log("Error decoding social_links_json in footer.php: " . json_last_error_msg());
    }
} else {
    // กำหนดค่า default หากไม่พบข้อมูลใน database (ควรมีการ insert ข้อมูลเริ่มต้นไว้แล้ว)
    $footer_settings = [
        'bg_color' => '#393939',
        'footer_top_title' => 'ลงทะเบียน',
        'footer_top_subtitle' => 'สมัครรับจดหมายข่าวของเราสำหรับข่าวสารล่าสุด และข้อเสนอสุดพิเศษ',
        'about_heading' => 'เกี่ยวกับเรา',
        'about_text' => 'บริษัท แทรนดาร์ อินเตอร์เนชั่นแนล จำกัดได้ก่อตั้งขึ้นเมื่อวันที่ 1 มีนาคม 2531 เราเป็นผู้เชี่ยวชาญด้านระบบฝ้าดูดซับเสียง ผนังกั้นเสียงและฝ้าอะคูสติกทุกชนิด เรามีทีมงานและผู้เชี่ยวชาญที่พร้อมให้คำปรึกษาในการออกแบบและติดตั้ง พร้อมทั้งผลิตและจำหน่ายแผ่นอะคูสติก ผนังดูดซับเสียง ซาวน์บอร์ด ผนังกั้นเสียง แผ่นฝ้า ที่ได้มาตรฐานจากทั้งในและต่างประเทศ รวมถึงการให้บริการที่มีประสิทธิภาพจากแทรนดาร์ อะคูสติก',
        'contact_heading' => 'ติดต่อเรา',
        'contact_address' => '102 Phatthanakan 40, Suan Luang, Bangkok 10250',
        'contact_phone' => '(+66)2 722 7007',
        'contact_email' => 'info@trandar.com',
        'contact_hours_wk' => 'Monday – Friday 08:30 AM – 05:00 PM',
        'contact_hours_sat' => 'Saturday 08:30 AM – 12:00 PM',
        'social_heading' => 'Follow Us',
        'social_links' => [
            ["icon" => "fab fa-facebook-f", "url" => "https://www.facebook.com/trandaracoustic/", "color" => "#3b5998"],
            ["icon" => "fab fa-instagram", "url" => "https://www.instagram.com/trandaracoustics/", "color" => "#e1306c"],
            ["icon" => "fab fa-youtube", "url" => "https://www.youtube.com/channel/UCewsEEtw8DOwSWoQ6ae_Uwg/", "color" => "#ff0000"],
            ["icon" => "fab fa-line", "url" => "https://lin.ee/yoSCNwF", "color" => "#00c300"],
            ["icon" => "fab fa-tiktok", "url" => "https://www.tiktok.com/@trandaracoustics", "color" => "#000000"]
        ],
        'copyright_text' => '© 2025 TRANDAR INTERNATIONAL CO., LTD. ALL RIGHTS RESERVED',
    ];
}
$stmt_footer->close();
// ** สิ้นสุดส่วนเพิ่มใหม่ **
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<!-- กำหนดสีพื้นหลังแบบ Dynamic -->
<footer class="main-footer" style="background-color: <?= htmlspecialchars($footer_settings['bg_color']) ?>;">
    <div class="container text-center footer-top-section">
        <h2 class="footer-title"><?= htmlspecialchars($footer_settings['footer_top_title']) ?></h2>
        <p class="footer-subtitle"><?= htmlspecialchars($footer_settings['footer_top_subtitle']) ?></p>
        <div class="mt-4">
            <div id="auth-buttons">
                <?php foreach ($menuItems as $item): // โค้ดส่วนนี้คงเดิม ?>
                    <a type="button" href="<?php echo $item['link']; ?>" id="<?php echo $item['modal_id'] ?>">
                        <i class="<?php echo $item['icon']; ?>"></i>
                        <span data-translate="<?php echo $item['translate']; ?>" lang="th">
                            <?php echo $item['text']; ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="container main-footer-content">
        <div class="row">
            <div class="col-md-4 col-sm-12 mb-4 pr-md-5">
                <p class="footer-heading"><?= htmlspecialchars($footer_settings['about_heading']) ?></p>
                <p><?= nl2br(htmlspecialchars($footer_settings['about_text'])) ?></p>
            </div>

            <div class="col-md-4 col-sm-12 mb-4 px-md-3">
                <p class="footer-heading"><?= htmlspecialchars($footer_settings['contact_heading']) ?></p>
                <p><?= htmlspecialchars($footer_settings['contact_address']) ?></p>
                <p><?= htmlspecialchars($footer_settings['contact_phone']) ?></p>
                <p><?= htmlspecialchars($footer_settings['contact_email']) ?></p>
                <p><?= htmlspecialchars($footer_settings['contact_hours_wk']) ?></p>
                <p><?= htmlspecialchars($footer_settings['contact_hours_sat']) ?></p>
            </div>

            <div class="col-md-4 col-sm-12 mb-4 pl-md-5">
                <p class="footer-heading"><?= htmlspecialchars($footer_settings['social_heading']) ?></p>
                <div class="social-icons-group">
                    <?php if (!empty($footer_settings['social_links'])): ?>
                        <?php foreach ($footer_settings['social_links'] as $social_link): ?>
                            <a href="<?= htmlspecialchars($social_link['url']) ?>" class="social-icon" style="background-color: <?= htmlspecialchars($social_link['color']) ?>;" target="_blank">
                                <i class="<?= htmlspecialchars($social_link['icon']) ?>"></i>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">ยังไม่มี Social Link กำหนดค่า.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom-bar">
        <?= htmlspecialchars($footer_settings['copyright_text']) ?>
    </div>

</footer>

<style>
    /* Main Footer Styles */
    /* background-color ย้ายไปใน inline style ของ footer tag เพื่อให้ Dynamic */
    .main-footer {
        color: #ccc;
        padding: 40px 0;
    }

    /* Top section (Register) */
    .footer-top-section {
        margin-bottom: 40px;
    }

    .footer-title {
        color: #fff;
        font-weight: bold;
    }

    .footer-subtitle {
        color: #aaa;
    }

    /* Main Content Sections */
    .main-footer-content {
        /* This container already manages horizontal spacing */
    }

    .footer-heading {
        font-size: 25px;
        font-weight: bold; /* Added for prominence */
        margin-bottom: 15px; /* Spacing below heading */
        color: #fff; /* Make headings stand out */
    }

    .main-footer-content p {
        font-size: 14px; /* Adjust text size for readability */
        line-height: 1.6; /* Improve line spacing */
    }

    /* Social Icons */
    .social-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        font-size: 22px;
        color: white;
        margin: 0 8px; /* Adjusted margin for better spacing */
        text-decoration: none;
        transition: transform 0.2s ease-in-out; /* Add smooth hover effect */
    }

    .social-icon:hover {
        transform: translateY(-3px); /* Slightly lift on hover */
    }

    /* Social Icon Background Colors - REMOVED, now set dynamically in HTML/PHP for each icon */
    /* Keep these if you want default colors for specific icons, or remove if only dynamic */
    /* .social-icon.facebook { background-color: #3b5998; } */
    /* .social-icon.instagram { background-color: #e1306c; } */
    /* .social-icon.youtube { background-color: #ff0000; } */
    /* .social-icon.line { background-color: #00c300; } */
    /* .social-icon.tiktok { background-color: #000000; } */


    /* Footer Bottom Bar */
    .footer-bottom-bar {
        text-align: center;
        color: #888;
        font-size: 13px;
        margin-top: 40px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.1); /* Lighter border for subtlety */
    }

    /* Responsive Adjustments */
    @media (max-width: 767.98px) { /* For small devices (tablets and below) */
        .pr-md-5, .px-md-3, .pl-md-5 {
            padding-right: 15px !important;
            padding-left: 15px !important;
            margin-bottom: 20px; /* Reduce margin on mobile */
        }
        .footer-heading {
            text-align: center;
        }
        .social-icons-group {
            text-align: center;
        }
        .main-footer-content p {
            text-align: center; /* Center align text on mobile */
        }
    }
</style>
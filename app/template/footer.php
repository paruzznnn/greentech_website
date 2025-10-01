<?php
// ต้องแน่ใจว่าได้เปิดใช้งาน Session ก่อนการแสดงผลใดๆ
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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
// require_once(__DIR__ . '/lib/base_directory.php'); // ปรับ Path ตามจริง (สำหรับ $base_path)

global $conn;
global $base_path;

// --- MODIFIED: Check for language preference from the URL, or Session, default to Thai if not specified. ---
$lang = 'th'; // กำหนดค่าเริ่มต้นเป็นภาษาไทย
if (isset($_GET['lang'])) {
    $supportedLangs = ['en', 'cn', 'jp', 'kr'];
    if (in_array($_GET['lang'], $supportedLangs)) {
        $_SESSION['lang'] = $_GET['lang'];
        $lang = $_GET['lang'];
    } else {
        unset($_SESSION['lang']); // ล้างค่าถ้าไม่ถูกต้อง
    }
} elseif (isset($_SESSION['lang'])) {
    $lang = $_SESSION['lang'];
}

$footer_settings = [];
$footer_id_for_display = 1;

// --- MODIFIED: Select English, Chinese, Japanese, and Korean columns from the database ---
$stmt_footer = $conn->prepare("SELECT 
    bg_color, footer_top_title, footer_top_title_en, footer_top_title_cn, footer_top_title_jp, footer_top_title_kr, footer_top_subtitle, footer_top_subtitle_en, footer_top_subtitle_cn, footer_top_subtitle_jp, footer_top_subtitle_kr,
    about_heading, about_heading_en, about_heading_cn, about_heading_jp, about_heading_kr, about_text, about_text_en, about_text_cn, about_text_jp, about_text_kr,
    contact_heading, contact_heading_en, contact_heading_cn, contact_heading_jp, contact_heading_kr, contact_address, contact_address_en, contact_address_cn, contact_address_jp, contact_address_kr,
    contact_phone, contact_email,
    contact_hours_wk, contact_hours_wk_en, contact_hours_wk_cn, contact_hours_wk_jp, contact_hours_wk_kr, contact_hours_sat, contact_hours_sat_en, contact_hours_sat_cn, contact_hours_sat_jp, contact_hours_sat_kr,
    social_heading, social_heading_en, social_heading_cn, social_heading_jp, social_heading_kr, social_links_json,
    copyright_text
    FROM footer_settings WHERE id = ?");
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
    // --- MODIFIED: Added English, Chinese, Japanese, and Korean default values ---
    $footer_settings = [
        'bg_color' => '#393939',
        'footer_top_title' => 'ลงทะเบียน',
        'footer_top_title_en' => 'Register',
        'footer_top_title_cn' => '注册',
        'footer_top_title_jp' => '登録',
        'footer_top_title_kr' => '가입하다',
        'footer_top_subtitle' => 'สมัครรับจดหมายข่าวของเราสำหรับข่าวสารล่าสุด และข้อเสนอสุดพิเศษ',
        'footer_top_subtitle_en' => 'Subscribe to our newsletter for the latest news and special offers.',
        'footer_top_subtitle_cn' => '订阅我们的时事通讯，获取最新消息和特别优惠。',
        'footer_top_subtitle_jp' => '最新ニュースや特別オファーのニュースレターを購読してください。',
        'footer_top_subtitle_kr' => '최신 뉴스 및 특별 행사를 위한 뉴스레터를 구독하세요.',
        'about_heading' => 'เกี่ยวกับเรา',
        'about_heading_en' => 'About Us',
        'about_heading_cn' => '关于我们',
        'about_heading_jp' => '会社概要',
        'about_heading_kr' => '회사 소개',
        'about_text' => '',
        'about_text_en' => '',
        'about_text_cn' => '',
        'about_text_jp' => '',
        'about_text_kr' => '',
        'contact_heading' => 'ติดต่อเรา',
        'contact_heading_en' => 'Contact Us',
        'contact_heading_cn' => '联系我们',
        'contact_heading_jp' => 'お問い合わせ',
        'contact_heading_kr' => '문의하기',
        'contact_address' => '',
        'contact_address_en' => '',
        'contact_address_cn' => '',
        'contact_address_jp' => '',
        'contact_address_kr' => '',
        'contact_phone' => '',
        'contact_email' => '',
        'contact_hours_wk' => '',
        'contact_hours_wk_en' => '',
        'contact_hours_wk_cn' => '',
        'contact_hours_wk_jp' => '',
        'contact_hours_wk_kr' => '',
        'contact_hours_sat' => '',
        'contact_hours_sat_en' => '',
        'contact_hours_sat_cn' => '',
        'contact_hours_sat_jp' => '',
        'contact_hours_sat_kr' => '',
        'social_heading' => 'Follow Us',
        'social_heading_en' => 'Follow Us',
        'social_heading_cn' => '关注我们',
        'social_heading_jp' => 'フォロー',
        'social_heading_kr' => '우리를 따르십시오',
        'social_links' => [
           
        ],
        'copyright_text' => '© 2025 GREEN TECH AI',
    ];
}
$stmt_footer->close();
// ** สิ้นสุดส่วนเพิ่มใหม่ **

// --- ADDED: A helper function to get text based on language ---
function get_text($settings, $field_name, $lang) {
    $field_en = $field_name . '_en';
    $field_cn = $field_name . '_cn';
    $field_jp = $field_name . '_jp';
    $field_kr = $field_name . '_kr';
    if ($lang === 'en' && !empty($settings[$field_en])) {
        return $settings[$field_en];
    }
    if ($lang === 'cn' && !empty($settings[$field_cn])) {
        return $settings[$field_cn];
    }
    if ($lang === 'jp' && !empty($settings[$field_jp])) {
        return $settings[$field_jp];
    }
    if ($lang === 'kr' && !empty($settings[$field_kr])) {
        return $settings[$field_kr];
    }
    return $settings[$field_name];
}

?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<footer class="main-footer" style="background-color: <?= htmlspecialchars($footer_settings['bg_color']) ?>;">
    <div class="container text-center footer-top-section">
        <h2 class="footer-title"><?= htmlspecialchars(get_text($footer_settings, 'footer_top_title', $lang)) ?></h2>
        <p class="footer-subtitle"><?= htmlspecialchars(get_text($footer_settings, 'footer_top_subtitle', $lang)) ?></p>
        <div class="mt-4">
            <div id="auth-buttons">
                <?php foreach ($menuItems as $item): // โค้ดส่วนนี้คงเดิม ?>
                    <aa type="button" href="<?php echo $item['link']; ?>" id="<?php echo $item['modal_id'] ?>">
                        <i class="<?php echo $item['icon']; ?>"></i>
                        <span data-translate="<?php echo $item['translate']; ?>" lang="th">
                            <?php echo $item['text']; ?>
                        </span>
                    </aa>
                <?php endforeach; ?>    
            </div>
        </div>
    </div>

    <div class="container main-footer-content">
        <div class="row">
            <div class="col-md-4 col-sm-12 mb-4 pr-md-5">
                <p class="footer-heading"><?= htmlspecialchars(get_text($footer_settings, 'about_heading', $lang)) ?></p>
                <p><?= nl2br(htmlspecialchars(get_text($footer_settings, 'about_text', $lang))) ?></p>
            </div>

            <div class="col-md-4 col-sm-12 mb-4 px-md-3">
                <p class="footer-heading"><?= htmlspecialchars(get_text($footer_settings, 'contact_heading', $lang)) ?></p>
                <p><?= htmlspecialchars(get_text($footer_settings, 'contact_address', $lang)) ?></p>
                <p><?= htmlspecialchars($footer_settings['contact_phone']) ?></p>
                <p><?= htmlspecialchars($footer_settings['contact_email']) ?></p>
                <p><?= htmlspecialchars(get_text($footer_settings, 'contact_hours_wk', $lang)) ?></p>
                <p><?= htmlspecialchars(get_text($footer_settings, 'contact_hours_sat', $lang)) ?></p>
            </div>

            <div class="col-md-4 col-sm-12 mb-4 pl-md-5">
                <p class="footer-heading"><?= htmlspecialchars(get_text($footer_settings, 'social_heading', $lang)) ?></p>
                <div class="social-icons-group">
                    <?php if (!empty($footer_settings['social_links'])): ?>
                        <?php foreach ($footer_settings['social_links'] as $social_link): ?>
                            <a href="<?= htmlspecialchars($social_link['url']) ?>" class="social-icon" style="background-color: <?= htmlspecialchars($social_link['color']) ?>;" target="_blank">
                                <i class="<?= htmlspecialchars($social_link['icon']) ?>"></i>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php
                        $noSocialText = [
                            'th' => 'ยังไม่มี Social Link กำหนดค่า.',
                            'en' => 'No Social Links configured.',
                            'cn' => '未配置社交链接。',
                            'jp' => 'ソーシャルリンクが設定されていません。',
                            'kr' => '구성된 소셜 링크가 없습니다.'
                        ];
                        ?>
                        <p class="text-muted"><?php echo $noSocialText[$lang]; ?></p>
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
    aa {
    color: #4350e4ff;;
    text-decoration: underline;
}
</style>
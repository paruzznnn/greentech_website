<?php
//header-top-right
$isProtocol = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
$isFile = ($isProtocol === 'http') ? '.php' : '';

$menuItems = [
    //
    [
        'id' => 0,
        'icon' => 'fas fa-user-plus',
        'text' => '',
        'translate' => 'Sign_up',
        'link' => 'register' . $isFile,
        'modal_id' => ''
    ],
    // [
    // 'id' => 1,
    // 'icon' => 'fas fa-sign-in-alt',
    // 'text' => '',
    // 'translate' => 'Sign_in',
    // 'link' => '#',
    // 'modal_id' => 'myBtn-sign-in'
    // ],
];
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<footer class="main-footer">
    <div class="container text-center footer-top-section">
        <h2 class="footer-title">ลงทะเบียน</h2>
        <p class="footer-subtitle">สมัครรับจดหมายข่าวของเราสำหรับข่าวสารล่าสุด และข้อเสนอสุดพิเศษ</p>
        <div class="mt-4">
            <div id="auth-buttons">
                <?php foreach ($menuItems as $item): ?>
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
                <p class="footer-heading">เกี่ยวกับเรา</p>
                <p>บริษัท แทรนดาร์ อินเตอร์เนชั่นแนล จำกัดได้ก่อตั้งขึ้นเมื่อวันที่ 1 มีนาคม 2531 เราเป็นผู้เชี่ยวชาญด้านระบบฝ้าดูดซับเสียง ผนังกั้นเสียงและฝ้าอะคูสติกทุกชนิด เรามีทีมงานและผู้เชี่ยวชาญที่พร้อมให้คำปรึกษาในการออกแบบและติดตั้ง พร้อมทั้งผลิตและจำหน่ายแผ่นอะคูสติก ผนังดูดซับเสียง ซาวน์บอร์ด ผนังกั้นเสียง แผ่นฝ้า ที่ได้มาตรฐานจากทั้งในและต่างประเทศ รวมถึงการให้บริการที่มีประสิทธิภาพจากแทรนดาร์ อะคูสติก</p>
                </div>

            <div class="col-md-4 col-sm-12 mb-4 px-md-3">
                <p class="footer-heading">ติดต่อเรา</p>
                <p>102 Phatthanakan 40, Suan Luang, Bangkok 10250</p>
                <p>(+66)2 722 7007</p>
                <p>info@trandar.com</p>
                <p>Monday – Friday 08:30 AM – 05:00 PM</p>
                <p>Saturday 08:30 AM – 12:00 PM</p>
            </div>

            <div class="col-md-4 col-sm-12 mb-4 pl-md-5">
                <p class="footer-heading">Follow Us</p>
                <div class="social-icons-group">
                    <a href="https://www.facebook.com/trandaracoustic/" class="social-icon facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/trandaracoustics/" class="social-icon instagram"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.youtube.com/channel/UCewsEEtw8DOwSWoQ6ae_Uwg/" class="social-icon youtube"><i class="fab fa-youtube"></i></a>
                    <a href="https://lin.ee/yoSCNwF" class="social-icon line"><i class="fab fa-line"></i></a>
                    <a href="https://www.tiktok.com/@trandaracoustics" class="social-icon tiktok"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom-bar">
        © 2025 TRANDAR INTERNATIONAL CO., LTD. ALL RIGHTS RESERVED
    </div>

</footer>

<style>
    /* Main Footer Styles */
    .main-footer {
        background-color: #393939;
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

    /* Social Icon Background Colors */
    .social-icon.facebook {
        background-color: #3b5998;
    }

    .social-icon.instagram {
        background-color: #e1306c;
    }

    .social-icon.youtube {
        background-color: #ff0000;
    }

    .social-icon.line {
        background-color: #00c300;
    }

    .social-icon.tiktok {
        background-color: #000000;
    }

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
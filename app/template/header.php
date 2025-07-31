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
        'link' => 'register'. $isFile,
        'modal_id' => ''
    ],
    [
        'id' => 1,
        'icon' => 'fas fa-sign-in-alt',
        'text' => '',
        'translate' => 'Sign_in',
        'link' => '#',
        'modal_id' => 'myBtn-sign-in'
    ],
];
?>
<?php
require_once('../lib/connect.php');
// ตรวจสอบให้แน่ใจว่า base_directory.php ถูก include ด้วย ถ้า logo_settings เก็บเป็น relative path และคุณต้องการแปลงเป็น full URL
// หาก logo_settings เก็บเป็น full URL อยู่แล้ว อาจไม่จำเป็นต้องใช้ base_path ที่นี่ก็ได้
// แต่ถ้ายังมีการใช้ base_path ในส่วนอื่น หรือเพื่อความสอดคล้อง แนะนำให้ include ไว้
// require_once('../lib/base_directory.php');

global $conn;
// global $base_path; // หากต้องการใช้ $base_path เพื่อต่อกับ relative path

// ✅ โหลดข้อมูล Meta Tags ตามชื่อหน้าอัตโนมัติ เช่น "about.php"
$currentPage = basename($_SERVER['PHP_SELF']);
$meta = [];

$stmt = $conn->prepare("SELECT * FROM metatags WHERE page_name = ?");
$stmt->bind_param("s", $currentPage);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $meta = $result->fetch_assoc();
}

// ** ส่วนแก้ไข: ดึงข้อมูลโลโก้และรูปภาพ Modal จาก database **
$logo_path = '../public/img/LOGOTRAND.png'; // ตั้งค่า default path เผื่อกรณีดึงจาก DB ไม่ได้
$logo_id_for_display = 1; // ID ของโลโก้ที่เราใช้ (ซึ่งคือ 1 เสมอ)
$image_modal_path = '../public/img/trandar.jpg'; // ตั้งค่า default path สำหรับรูปภาพใน Modal

$stmt_logo = $conn->prepare("SELECT image_path, image_modal_path FROM logo_settings WHERE id = ?");
$stmt_logo->bind_param("i", $logo_id_for_display);
$stmt_logo->execute();
$result_logo = $stmt_logo->get_result();

if ($logo_data = $result_logo->fetch_assoc()) {
    $logo_path = htmlspecialchars($logo_data['image_path']);
    $image_modal_path = htmlspecialchars($logo_data['image_modal_path']);
}
$stmt_logo->close();
// ** สิ้นสุดส่วนแก้ไขโลโก้ **

// ** ส่วนเพิ่มใหม่: ดึงข้อมูล Trandar Store และ Social Media Links จาก database **
$contact_settings = [
    'trandar_store_link' => 'https://www.trandar.com/store/',
    'trandar_store_text' => 'Trandar Store',
    'facebook_link' => 'https://www.facebook.com/trandaracoustic/',
    'youtube_link' => 'https://www.youtube.com/@trandaracoustic', // แก้ไขให้ถูกต้องตามที่ต้องการ
    'instagram_link' => 'https://www.instagram.com/trandaracoustics/',
    'line_link' => 'https://lin.ee/yoSCNwF',
    'tiktok_link' => 'https://www.tiktok.com/@trandaracoustics'
]; // Default values

$stmt_contact = $conn->prepare("SELECT trandar_store_link, trandar_store_text, facebook_link, youtube_link, instagram_link, line_link, tiktok_link FROM contact_settings WHERE id = 1"); // สมมติว่ามี ID 1 สำหรับการตั้งค่าหลัก
$stmt_contact->execute();
$result_contact = $stmt_contact->get_result();

if ($contact_data = $result_contact->fetch_assoc()) {
    foreach ($contact_data as $key => $value) {
        $contact_settings[$key] = htmlspecialchars($value);
    }
}
$stmt_contact->close();
// ** สิ้นสุดส่วนเพิ่มใหม่ **


// ✅ ตรวจสอบการเข้าสู่ระบบ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM mb_user WHERE email = ? OR phone_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            header("Location: dashboard.php");
            exit;
        } else {
            $login_error = "รหัสผ่านไม่ถูกต้อง";
        }
    } else {
        $login_error = "ไม่พบบัญชีผู้ใช้นี้";
    }
}
?>

<title><?= $meta['meta_title'] ?? 'Trandar' ?></title>
<meta name="description" content="<?= $meta['meta_description'] ?? 'Trandar ราคาถูก มีบริการหลังการขาย' ?>">
<meta name="keywords" content="<?= $meta['meta_keywords'] ?? 'Trandar, แผ่นฝ้า, ฝ้าดูดซับเสียง' ?>">
<meta name="author" content="trandar.com">

<meta property="og:site_name" content="trandar.com">
<meta property="og:title" content="<?= $meta['og_title'] ?? $meta['meta_title'] ?? 'Trandar' ?>">
<meta property="og:description" content="<?= $meta['og_description'] ?? $meta['meta_description'] ?? 'Trandar ราคาถูก มีบริการหลังการขาย' ?>">
<meta property="og:type" content="website">
<meta property="og:image" content="<?= $meta['og_image'] ?? '../../public/img/LOGO TRANDAR.png' ?>">

<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-N57LMZ6H');</script>
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-N57LMZ6H"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<div id="loading-overlay" class="hidden">
    <div class="spinner"></div>
</div>


<div id="background-blur"></div>

<div class="header-top">
    <div class="header-top-left" style="display: flex; align-items: center;">
        <a href="https://www.trandar.com">
            <img class="logo" src="<?= $logo_path ?>" alt="Website Logo">
        </a>
        <div id="current-date" style="margin-left: 20px; color:rgb(58, 54, 54); font-size: 16px; font-weight: 500;"></div>
    </div>

    <script>
        // สคริปต์แสดงวันที่
        const dateEl = document.getElementById("current-date");
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const today = new Date().toLocaleDateString('en-US', options);
        dateEl.textContent = today;
    </script>


    <div class="header-top-right">
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

        <a href="#" id="logout-btn" style="display:none; background: #ff3333; color: white; padding: 8px 15px; border-radius: 4px; margin-left: 10px;">
            <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
        </a>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const jwt = sessionStorage.getItem("jwt");

                if (jwt) {
                    fetch('actions/protected.php', {
                        method: 'GET',
                        headers: {
                            'Authorization': 'Bearer ' + jwt
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === "success" && parseInt(data.data.role_id) === 3) {
                            // viewer login แล้ว
                            document.getElementById("auth-buttons").style.display = "none";
                            document.getElementById("logout-btn").style.display = "inline-block";
                        }
                    })
                    .catch(error => console.error("Token verification failed:", error));
                }

                document.getElementById("logout-btn").addEventListener("click", function () {
                    sessionStorage.removeItem("jwt");
                    location.reload(); // รีเฟรชหน้าเพื่อให้กลับสู่สถานะไม่ได้ล็อกอิน
                });
            });
        </script>


        <div class="header-link">
            <a href="<?= $contact_settings['trandar_store_link'] ?>" target="_blank" style="background: #ffa719; color: black; padding: 10px 15px; text-decoration: none; border-radius: 4px;">
                <?= $contact_settings['trandar_store_text'] ?> <i class="fas fa-shopping-cart" style="margin-left: 8px;"></i>
            </a>
        </div>
        <div>
            <select id="language-select" class="language-select">
            </select>
        </div>
        <div class="header-link">
            <a href="<?= $contact_settings['facebook_link'] ?>" target="_blank" style="background: #ffa719; color: #fafafa;">
                <i class="fab fa-facebook-square"></i>
            </a>
            <a href="<?= $contact_settings['youtube_link'] ?>" target="_blank" style="background: #ffa719; color: #fafafa;">
                <i class="fab fa-youtube"></i>
            </a>
            <a href="<?= $contact_settings['instagram_link'] ?>" target="_blank" style="background: #ffa719; color: #fafafa;">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="<?= $contact_settings['line_link'] ?>" target="_blank" style="background: #ffa719; color: #fafafa;">
                <i class="fab fa-line"></i>
            </a>
            <a href="<?= $contact_settings['tiktok_link'] ?>" target="_blank" style="background: #ffa719; color: #fafafa;">
                <i class="fab fa-tiktok"></i>
            </a>
        </div>
    </div>

</div>

<div id="myModal-sign-in" class="modal">
    <div class="modal-content" style="width: 350px !important;">
        <div class="modal-header">
            <span class="modal-close-sign-in">×</span>
        </div>
        <div class="modal-body" style="background-color: #9e9e9e1f;">

            <div class="box-sign-in-container">

                <div class="card">
                    <section class="card-body">
                        <div style="text-align: center;">
                            <img class="modal-logo" style="width: 70%;" src="<?= $image_modal_path ?>" alt="Trandar Logo">
                        </div>

                        <h6 style="text-align: center; color: #555;" class="mt-2">
                            <span><i class="fas fa-unlock"></i></span>
                            <span data-key-lang="Pleaselogin" lang="US">Please log in</span>
                        </h6>

                        <hr>

                        <form id="loginModal" action="" method="post">

                            <div class="form-group mt-4">
                                <input id="username" type="text" class="emet-login input" placeholder="Please enter your email.">
                            </div>

                            <div class="form-group mt-2" style="position: relative;">
                                <input id="password" type="password" class="emet-login inpu" data-type="password">
                                <span class=""
                                    style="position: absolute; top: 10px; right: 20px; color: #555555;"
                                    id="togglePasswordSignin">
                                    <i class="fas fa-eye-slash"></i>
                                </span>
                            </div>


                            <div class="row mt-4">


                                <div class="col-md-12 text-end"
                                style="
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                                ">
                                    <a href="<?php echo 'register'.$isFile ?>">
                                        <span style="font-size: 13px !important;">
                                            สมัครสมาชิก
                                        </span>
                                    </a>

                                    <a type="button" href="#"  id="myBtn-forgot-password">
                                        <span style="font-size: 13px !important;">
                                            ลืมรหัสผ่าน
                                        </span>
                                    </a>
                                </div>

                                <div class="col-md-12">
                                    <div class="d-inline-flex">
                                        <button type="submit" class=""
                                            style="
                                            width: 260px;
                                            border: none;
                                            border-radius: 4px;
                                            padding: 10px;
                                            background: #ff8200;
                                            color: white;
                                            "> Login </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </section>
                </div>
            </div>

        </div>
    </div>
</div>


<div id="myModal-forgot-password" class="modal">
    <div class="modal-content" style="width: 350px !important;">
        <div class="modal-header">
            <span class="modal-close-forgot-password">×</span>
        </div>
        <div class="modal-body" style="background-color: #9e9e9e1f;">

            <div class="box-forgot-password-container">

                <div class="card">
                    <section class="card-body">
                        <div style="text-align: center;">
                            <img class="modal-logo" style="width: 70%;" src="<?= $image_modal_path ?>" alt="Trandar Logo">
                        </div>

                        <h6 style="text-align: center; color: #555;" class="mt-2">
                            <span>
                                <i class="fas fa-key"></i>
                            </span>
                            <span data-key-lang="" lang="US">Forgot your password?</span>
                        </h6>

                        <hr>

                        <form id="forgotModal" action="" method="post">

                            <div class="form-group mt-4">
                                <input
                                id="forgot_email"
                                name="forgot_email" type="text"
                                class="form-control emet-login input"
                                placeholder="Please enter your email.">
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="d-inline-flex">
                                        <button type="button"
                                        id="submitForgot"
                                        class=""
                                        style="
                                        width: 260px;
                                        border: none;
                                        border-radius: 4px;
                                        padding: 10px;
                                        background: #ff8200;
                                        color: white;
                                        "> send email </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </section>
                </div>
            </div>

        </div>
    </div>
</div>
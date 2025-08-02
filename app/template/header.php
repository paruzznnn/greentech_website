<?php
//header-top-right
$isProtocol = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
$isFile = ($isProtocol === 'http') ? '.php' : '';

$menuItems = [
    //
    [
        'id' => 0,
        'icon' => 'fas fa-user-plus',
        'text' => 'Sign up',
        'translate' => 'Sign_up',
        'link' => 'register' . $isFile,
        'modal_id' => ''
    ],
    [
        'id' => 1,
        'icon' => 'fas fa-sign-in-alt',
        'text' => 'Sign in',
        'translate' => 'Sign_in',
        'link' => '#',
        'modal_id' => 'myBtn-sign-in'
    ],
];
?>
<?php
require_once('../lib/connect.php');
global $conn;

$currentPage = basename($_SERVER['PHP_SELF']);
$meta = [];

$stmt = $conn->prepare("SELECT * FROM metatags WHERE page_name = ?");
$stmt->bind_param("s", $currentPage);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $meta = $result->fetch_assoc();
}

$logo_path = '../public/img/LOGOTRAND.png';
$logo_id_for_display = 1;

$stmt_logo = $conn->prepare("SELECT image_path FROM logo_settings WHERE id = ?");
$stmt_logo->bind_param("i", $logo_id_for_display);
$stmt_logo->execute();
$result_logo = $stmt_logo->get_result();

if ($logo_data = $result_logo->fetch_assoc()) {
    $logo_path = htmlspecialchars($logo_data['image_path']);
}
$stmt_logo->close();

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

<style>
/* CSS สำหรับ Desktop Header */
.header-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    background-color: #f1f1f1;
    border-bottom: 1px solid #ddd;
    position: relative;
    z-index: 1000;
}
.header-top-left {
    display: flex;
    align-items: center;
}
.header-top .logo {
    height: 55px;
    max-height: 55px;
    width: auto;
}
.header-top-right {
    display: flex;
    align-items: center;
    gap: 15px;
}

/* กลุ่มปุ่มเข้าสู่ระบบ, ลงทะเบียน, Store */
.header-top-buttons {
    display: flex;
    align-items: center;
    gap: 5px;
}
.header-top-buttons a,
.header-top-buttons button {
    padding: 8px 15px;
    border-radius: 4px;
    text-decoration: none;
    color: white;
    font-size: 14px;
    white-space: nowrap;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    transition: background-color 0.3s;
}
.header-top-buttons .auth-btn {
    background: #555;
}
.header-top-buttons .auth-btn:hover {
    background: #333;
}
.header-top-buttons .store-btn {
    background: #ffa719;
}
.header-top-buttons .store-btn:hover {
    background: #ff9900;
}
#logout-btn {
    background: #ff3333;
}
#logout-btn:hover {
    background: #cc0000;
}

/* ภาษา */
.language-select-container {
    position: relative;
    display: inline-block;
}
.flag-icon {
    width: 24px;
    height: auto;
    cursor: pointer;
    border: 1px solid #ddd;
    border-radius: 2px;
}
.flag-dropdown {
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    background-color: #f9f9f9;
    min-width: 120px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1002;
    border-radius: 4px;
    padding: 5px 0;
}
.flag-dropdown a {
    color: black;
    padding: 8px 16px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
    white-space: nowrap;
}
.flag-dropdown a:hover {
    background-color: #f1f1f1;
}

/* โซเชียลมีเดีย */
.header-social-links {
    display: flex;
    gap: 5px;
}
.header-social-links a {
    background: #ffa719;
    color: #fff;
    padding: 8px 12px;
    border-radius: 4px;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.3s;
}
.header-social-links a:hover {
    background-color: #ff9900;
}

/* Mobile Header (ซ่อนบน Desktop) */
.header-mobile {
    display: none;
    padding: 10px 20px;
    background-color: #f1f1f1;
    z-index: 1000;
    position: relative;
    text-align: center;
}
.mobile-dropdown-tab {
    position: absolute;
    /* top: 10px; */
    left: 3px;
    z-index: 1001;
}
.mobile-dropdown-button {
    background-color: #ffa719;
    color: #555;
    padding: 10px 15px;
    font-size: 16px;
    border: none;
    cursor: pointer;
    border-radius: 4px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.mobile-dropdown-content {
    display: none;
    position: absolute;
    /* right: 0; */
    background-color: #f9f9f9;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1002;
    border-radius: 4px;
    padding: 10px 0;
}
.mobile-dropdown-content a {
    padding: 8px 12px;
}
/* แก้ไข CSS ในส่วนนี้ */
.mobile-dropdown-content a,
.mobile-dropdown-content button {
    color: #555;
    text-decoration: none;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    background-color: #ff9900;
    border: none;
    cursor: pointer;
}
/* เพิ่มสไตล์สำหรับปุ่มที่เป็นสีส้มโดยเฉพาะ */
.mobile-dropdown-content a[href*="store"],
.mobile-dropdown-content button[id*="logout"] {
    background-color: #ffa719;
    color: white;
}
.mobile-dropdown-content a[href*="store"]:hover,
.mobile-dropdown-content button[id*="logout"]:hover {
    background-color: #ff9900;
}
.mobile-dropdown-content a:hover,
.mobile-dropdown-content button:hover {
    background-color: #f1f1f1;
}
/* สิ้นสุดส่วนที่แก้ไข */

/* Media Query */
@media (max-width: 1024px) {
    .header-top {
        display: none;
    }
    .header-mobile {
        display: block;
    }
}
</style>

<div class="header-top">
    <div class="header-top-left">
        <a href="https://www.trandar.com">
            <img class="logo" src="<?= $logo_path ?>" alt="Website Logo">
        </a>
        <div id="current-date" style="margin-left: 20px; color:#555; font-size: 16px; font-weight: 500;"></div>
    </div>

    <script>
        const dateEl = document.getElementById("current-date");
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const today = new Date().toLocaleDateString('en-US', options);
        dateEl.textContent = today;
    </script>


    <div class="header-top-right">
        <div class="header-top-buttons">
            <div id="auth-buttons">
                <?php foreach ($menuItems as $item): ?>
                    <a type="button" href="<?php echo $item['link']; ?>" id="<?php echo $item['modal_id'] ?>" class="auth-btn">
                        <i class="<?php echo $item['icon']; ?>"></i>
                        <span data-translate="<?php echo $item['translate']; ?>" lang="th">
                            <?php echo $item['text']; ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>

            <a href="#" id="logout-btn" style="display:none;">
                <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
            </a>
            
            <a href="https://www.trandar.com/store/" target="_blank" class="store-btn">
                Trandar Store <i class="fas fa-shopping-cart" style="margin-left: 8px;"></i>
            </a>

            <div class="language-select-container">
                <img id="current-flag" src="https://flagcdn.com/th.svg" alt="Thai Flag" class="flag-icon" onclick="toggleFlagDropdown('desktop')">
                <div id="flag-dropdown-desktop" class="flag-dropdown">
                    <a href="#" data-lang="th">
                        <img src="https://flagcdn.com/th.svg" alt="Thai Flag" width="24"> ไทย
                    </a>
                    <a href="#" data-lang="en">
                        <img src="https://flagcdn.com/us.svg" alt="US Flag" width="24"> English
                    </a>
                </div>
            </div>
        </div>

        <div class="header-social-links">
            <a href="https://www.facebook.com/trandaracoustic/" target="_blank">
                <i class="fab fa-facebook-square"></i>
            </a>
            <a href="https://www.youtube.com/channel/UCewsEEtw8DOwSWoQ6ae_Uwg/" target="_blank">
                <i class="fab fa-youtube"></i>
            </a>
            <a href="https://www.instagram.com/trandaracoustics/" target="_blank">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="https://lin.ee/yoSCNwF" target="_blank">
                <i class="fab fa-line"></i>
            </a>
            <a href="https://www.tiktok.com/@trandaracoustics" target="_blank">
                <i class="fab fa-tiktok"></i>
            </a>
        </div>
    </div>
</div>

<div class="header-mobile">
    <div class="header-top-right">
        <div class="mobile-dropdown-tab">
            <button class="mobile-dropdown-button">
                <i class="fas fa-grip-lines"></i>
            </button>
            <div class="mobile-dropdown-content">
                <div id="auth-buttons-mobile">
                    <?php foreach ($menuItems as $item): ?>
                        <a type="button" href="<?php echo $item['link']; ?>" id="<?php echo $item['modal_id'] ?>">
                            <i class="<?php echo $item['icon']; ?>"></i>
                            <span data-translate="<?php echo $item['translate']; ?>" lang="th">
                                <?php echo $item['text']; ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>

                <a href="#" id="logout-btn-mobile" style="display:none;">
                    <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
                </a>

                <hr style="margin: 10px 0; border-color: #ddd;">

                <a href="https://www.trandar.com/store/" target="_blank">
                    <i class="fas fa-shopping-cart"></i>
                    Trandar Store
                </a>
                
                <div class="language-select-container" style="padding: 12px 16px;">
                    <img id="current-flag-mobile" src="https://flagcdn.com/th.svg" alt="Thai Flag" class="flag-icon" onclick="toggleFlagDropdown('mobile')">
                    <div id="flag-dropdown-mobile" class="flag-dropdown">
                        <a href="#" data-lang="th">
                            <img src="https://flagcdn.com/th.svg" alt="Thai Flag" width="24"> ไทย
                        </a>
                        <a href="#" data-lang="en">
                            <img src="https://flagcdn.com/us.svg" alt="US Flag" width="24"> English
                        </a>
                    </div>
                </div>
                
                <div class="header-social-links" style="padding: 12px 16px;">
                    <a href="https://www.facebook.com/trandaracoustic/" target="_blank">
                        <i class="fab fa-facebook-square"></i>
                    </a>
                    <a href="https://www.youtube.com/channel/UCewsEEtw8DOwSWoQ6ae_Uwg/" target="_blank">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="https://www.instagram.com/trandaracoustics/" target="_blank">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://lin.ee/yoSCNwF" target="_blank">
                        <i class="fab fa-line"></i>
                    </a>
                    <a href="https://www.tiktok.com/@trandaracoustics" target="_blank">
                        <i class="fab fa-tiktok"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
   <div class="mobile-logo-container">
        <a href="https://www.trandar.com">
            <img class="logo" src="<?= $logo_path ?>" alt="Website Logo">
        </a>
    </div>
</div>

<script>
    // สคริปต์สำหรับ Modal และ JWT
    document.addEventListener("DOMContentLoaded", function () {
        const jwt = sessionStorage.getItem("jwt");
        const authButtonsDesktop = document.getElementById("auth-buttons");
        const logoutBtnDesktop = document.getElementById("logout-btn");
        const signinModalBtn = document.getElementById("myBtn-sign-in");
        
        const authButtonsMobile = document.getElementById("auth-buttons-mobile");
        const logoutBtnMobile = document.getElementById("logout-btn-mobile");
        
        // ตรวจสอบ JWT Token
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
                    // Desktop
                    if(authButtonsDesktop) authButtonsDesktop.style.display = "none";
                    if(logoutBtnDesktop) logoutBtnDesktop.style.display = "block";
                    // Mobile
                    if(authButtonsMobile) authButtonsMobile.style.display = "none";
                    if(logoutBtnMobile) logoutBtnMobile.style.display = "block";
                }
            })
            .catch(error => console.error("Token verification failed:", error));
        } else {
             // Desktop
            if(authButtonsDesktop) authButtonsDesktop.style.display = "block";
            if(logoutBtnDesktop) logoutBtnDesktop.style.display = "none";
            // Mobile
            if(authButtonsMobile) authButtonsMobile.style.display = "block";
            if(logoutBtnMobile) logoutBtnMobile.style.display = "none";
        }
    
        // Modal Event Listeners
        const modalSignin = document.getElementById('myModal-sign-in');
        const modalForgot = document.getElementById('myModal-forgot-password');
        const signinModalCloseBtn = document.querySelector('.modal-close-sign-in');
        const forgotModalBtn = document.getElementById('myBtn-forgot-password');
        const forgotModalCloseBtn = document.querySelector('.modal-close-forgot-password');
        
        // Open Sign In Modal
        if (signinModalBtn) {
            signinModalBtn.onclick = function(e) {
                e.preventDefault();
                modalSignin.style.display = 'block';
            }
        }
        
        // Close Sign In Modal
        if (signinModalCloseBtn) {
            signinModalCloseBtn.onclick = function() {
                modalSignin.style.display = 'none';
            }
        }

        // Open Forgot Password Modal
        if (forgotModalBtn) {
            forgotModalBtn.onclick = function(e) {
                e.preventDefault();
                modalSignin.style.display = 'none'; // Close sign in modal
                modalForgot.style.display = 'block';
            }
        }

        // Close Forgot Password Modal
        if (forgotModalCloseBtn) {
            forgotModalCloseBtn.onclick = function() {
                modalForgot.style.display = 'none';
            }
        }
        
        window.onclick = function(event) {
            if (event.target == modalSignin) {
                modalSignin.style.display = 'none';
            }
            if (event.target == modalForgot) {
                modalForgot.style.display = 'none';
            }
        }
    
        // Logout Event Listener (Desktop & Mobile)
        if(logoutBtnDesktop) {
            logoutBtnDesktop.addEventListener("click", function (e) {
                e.preventDefault(); // ป้องกันการเปลี่ยนหน้า
                sessionStorage.removeItem("jwt");
                location.reload(); 
            });
        }
         if(logoutBtnMobile) {
            logoutBtnMobile.addEventListener("click", function (e) {
                e.preventDefault();
                sessionStorage.removeItem("jwt");
                location.reload();
            });
        }
        
        // Mobile dropdown tab toggle
        const mobileDropdownTab = document.querySelector('.mobile-dropdown-tab');
        const mobileDropdownButton = document.querySelector('.mobile-dropdown-button');
        const mobileDropdownContent = document.querySelector('.mobile-dropdown-content');

        mobileDropdownButton.addEventListener('click', function(event) {
            mobileDropdownContent.style.display = mobileDropdownContent.style.display === 'block' ? 'none' : 'block';
            event.stopPropagation();
        });

        document.addEventListener('click', function(event) {
            if (!mobileDropdownTab.contains(event.target)) {
                mobileDropdownContent.style.display = 'none';
            }
        });

        // สคริปต์สำหรับจัดการภาษาและธงชาติ
        window.toggleFlagDropdown = function(mode = 'desktop') {
            const dropdownId = mode === 'mobile' ? 'flag-dropdown-mobile' : 'flag-dropdown-desktop';
            const dropdown = document.getElementById(dropdownId);
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        };

        const flagLinks = document.querySelectorAll('.flag-dropdown a');
        flagLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                const lang = this.dataset.lang;

                // อัปเดตธงที่แสดง
                const currentFlagDesktop = document.getElementById('current-flag');
                const currentFlagMobile = document.getElementById('current-flag-mobile');
                const newFlagSrc = this.querySelector('img').src;
                const newFlagAlt = this.querySelector('img').alt;

                currentFlagDesktop.src = newFlagSrc;
                currentFlagDesktop.alt = newFlagAlt;
                currentFlagMobile.src = newFlagSrc;
                currentFlagMobile.alt = newFlagAlt;

                // ซ่อน dropdown
                document.getElementById('flag-dropdown-desktop').style.display = 'none';
                document.getElementById('flag-dropdown-mobile').style.display = 'none';
            });
        });
        
        document.addEventListener('click', function(event) {
            const isClickInsideFlagDropdown = event.target.closest('.language-select-container');
            const flagDropdownDesktop = document.getElementById('flag-dropdown-desktop');
            const flagDropdownMobile = document.getElementById('flag-dropdown-mobile');

            if (!isClickInsideFlagDropdown) {
                if (flagDropdownDesktop) flagDropdownDesktop.style.display = 'none';
                if (flagDropdownMobile) flagDropdownMobile.style.display = 'none';
            }
        });
    });
</script>


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
                            <img class="" style="width: 70%;" src="../public/img/trandar.jpg" alt="">
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
                            <img class="" style="width: 70%;" src="../public/img/trandar.jpg" alt="">
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
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
global $conn;

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

<!DOCTYPE html>
<html lang="th">
<head>
 <!-- SEO -->
<title><?= $meta['meta_title'] ?? 'Trandar' ?></title>
<meta name="description" content="<?= $meta['meta_description'] ?? 'Trandar ราคาถูก มีบริการหลังการขาย' ?>">
<meta name="keywords" content="<?= $meta['meta_keywords'] ?? 'Trandar, แผ่นฝ้า, ฝ้าดูดซับเสียง' ?>">
<meta name="author" content="trandar.com">

<!-- Open Graph (Facebook, LINE) -->
<meta property="og:site_name" content="trandar.com">
<meta property="og:title" content="<?= $meta['og_title'] ?? $meta['meta_title'] ?? 'Trandar' ?>">
<meta property="og:description" content="<?= $meta['og_description'] ?? $meta['meta_description'] ?? 'Trandar ราคาถูก มีบริการหลังการขาย' ?>">
<meta property="og:type" content="website">
<meta property="og:image" content="<?= $meta['og_image'] ?? '../../public/img/q-removebg-preview1.png' ?>">


</head>


<div id="loading-overlay" class="hidden">
    <div class="spinner"></div>
</div>


<div id="background-blur"></div>

<div class="header-top">
    <div class="container">

        <div class="header-top-left" style="display: flex; align-items: center;">
    <a href="https://www.trandar.com">
        <img class="logo" src="../public/img/trandar_logo_no_bg_100x55.png" alt="">
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
                <a href="https://www.trandar.com/store/" target="_blank" style="background: #ffa719; color: black; padding: 10px 15px; text-decoration: none; border-radius: 4px;">
                Trandar Store <i class="fas fa-shopping-cart" style="margin-left: 8px;"></i>
                </a>    
                </div>
            <div>
                <select id="language-select" class="language-select">
                </select>
            </div>
            <div class="header-link">
                <a href="https://www.facebook.com/trandaracoustic/" target="_blank" style="background: #ffa719; color: #fafafa;">
                    <i class="fab fa-facebook-square"></i>
                </a>
                <a href="https://www.youtube.com/channel/UCewsEEtw8DOwSWoQ6ae_Uwg/" target="_blank" style="background: #ffa719; color: #fafafa;">
                    <i class="fab fa-youtube"></i>
                </a>
                <a href="https://www.instagram.com/trandaracoustics/" target="_blank" style="background: #ffa719; color: #fafafa;">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://lin.ee/yoSCNwF" target="_blank" style="background: #ffa719; color: #fafafa;">
                    <i class="fab fa-line"></i>
                </a>
                <a href="https://www.tiktok.com/@trandaracoustics" target="_blank" style="background: #ffa719; color: #fafafa;">
                    <i class="fab fa-tiktok"></i>
                </a>
            </div>
        </div>

    </div>
</div>

<div id="myModal-sign-in" class="modal">
    <div class="modal-content" style="width: 350px !important;">
        <div class="modal-header">
            <span class="modal-close-sign-in">&times;</span>
        </div>
        <div class="modal-body" style="background-color: #9e9e9e1f;">

            <div class="box-sign-in-container">

                <div class="card">
                    <section class="card-body">
                        <div style="text-align: center;">
                            <!-- <img class="" style="width: 70%;" src="../public/img/logo-ALLABLE-06.png" alt=""> -->
                             <img class="" style="width: 70%;" src="../public/img/trandar.jpg" alt="">
                        </div>

                        <h6 style="text-align: center; color: #555;" class="mt-2">
                            <span><i class="fas fa-unlock"></i></span>
                            <span data-key-lang="Pleaselogin" lang="US">Please log in</span>
                        </h6>

                        <hr>

                        <form id="loginModal" action="" method="post">

                            <div class="form-group mt-4">
                                <input id="username" type="text" class="emet-login input" placeholder="Please enter your user.">
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
            <span class="modal-close-forgot-password">&times;</span>
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






<?php
// header-top-right
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
    [
        'id' => 1,
        'icon' => 'fas fa-sign-in-alt',
        'text' => '',
        'translate' => 'Sign_in',
        'link' => '#',
        'modal_id' => 'myBtn-sign-in'
    ],
];

require_once('../lib/connect.php');
global $conn;

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
<!DOCTYPE html>
<html lang="th">
<head>
  <!-- <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Trandar</title> -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <!-- <style>
    #ai-chatbox {
      position: fixed;
      bottom: 80px;
      right: 20px;
      z-index: 9999;
      display: none;
    }

    #ai-chatbox-resizable {
      width: 320px;
      height: 400px;
      resize: both;
      overflow: hidden;
      display: inline-block;
      border: 1px solid #ccc;
      border-radius: 12px;
      background: white;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
      max-width: 90vw;
      max-height: 90vh;
      position: relative;
    }

    #ai-chatbox-inner {
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    #ai-chatbox-header {
      background: #ffa719;
      color: white;
      padding: 10px;
      text-align: center;
      flex-shrink: 0;
    }

    #ai-chat-messages {
      flex: 1;
      padding: 10px;
      overflow-y: auto;
      font-size: 14px;
      word-break: break-word;
      white-space: pre-wrap;
    }

    #ai-chat-input {
      display: flex;
      border-top: 1px solid #ddd;
      flex-shrink: 0;
    }

    #ai-chat-input input {
      flex: 1;
      border: none;
      padding: 10px;
    }

    #ai-chat-input button {
      background: #ff8200;
      color: white;
      border: none;
      padding: 10px 15px;
    }
  </style> -->
</head>
<body>
<!-- <div id="showOrigamiAiHtml" style="
    position: fixed;
    bottom: 40px;
    right: 40px;
    z-index: 9999;">
</div>

<script src="https://dev.origami.life/helpdesk/api_ai/helpdesk_live_chat.js"></script> -->
<div id="showOrigamiAiHtml"  style="
    position: fixed;
    bottom: 40px;
    right: 40px;
    z-index: 9999;"></div>
    <script src="https://dev.origami.life/plugin_chat_ai/plugin_chat.js"></script>

<!-- ปุ่มแสดงกล่องแชท -->
<!-- <button id="chat-toggle" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; background: #ff8200; color: white; padding: 10px 15px; border-radius: 50%; border: none; font-size: 18px;">
  <i class="fas fa-comments"></i>
</button>

 กล่องแชท AI 
<div id="ai-chatbox">
  <div id="ai-chatbox-resizable">
    <div id="ai-chatbox-inner">
      <div id="ai-chatbox-header">แชทกับ Trandar 💬</div>
      <div id="ai-chat-messages"></div>
      <div id="ai-chat-input">
        <input type="text" id="userMessage" placeholder="พิมพ์ข้อความ...">
        <button onclick="sendToAI()">ส่ง</button>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById('chat-toggle');
    const chatbox = document.getElementById('ai-chatbox');
    const messages = document.getElementById('ai-chat-messages');
    const input = document.getElementById('userMessage');

    input.addEventListener("keydown", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        sendToAI();
      }
    });

    toggleBtn.onclick = () => {
      chatbox.style.display = chatbox.style.display === 'block' ? 'none' : 'block';
    };

    window.sendToAI = async function () {
  const userText = input.value.trim();
  if (!userText) return;
  addMessage(userText, 'user');
  input.value = '';

  try {
    const res = await fetch('http://localhost:5678/webhook/77b269f1-a7d5-41a9-b61f-cb604dde7107', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ message: userText })
    });

    const rawText = await res.text();
    let replyText;

    try {
      const parsed = JSON.parse(rawText);
      replyText = parsed.reply || rawText; // ถ้ามี field reply ให้ใช้มัน
    } catch {
      replyText = rawText; // ถ้าไม่ใช่ JSON ก็ใช้ทั้งดุ้น
    }

    addMessage(replyText, 'bot');
  } catch (err) {
    addMessage('เกิดข้อผิดพลาดในการเชื่อมต่อ AI', 'bot');
    console.error(err);
  }
}

    function addMessage(text, sender = 'user') {
      const msg = document.createElement('div');
      msg.innerHTML = `<strong>${sender === 'user' ? 'คุณ' : 'AI'}:</strong><br>${text.replace(/\n/g, "<br>")}`;
      msg.style.marginBottom = '8px';
      msg.style.whiteSpace = 'pre-wrap';
      messages.appendChild(msg);
      messages.scrollTop = messages.scrollHeight;
    }
  });
</script> -->

</body>
<style>
/* ปรับปุ่ม Sign in / Sign up ให้มีขนาดพอดี */
.header-top-right a#myBtn-sign-in,
.header-top-right a[href*="register"] {
    display: inline-block;
    padding: 6px 10px;
    font-size: 13px;
    background: #ffffffff;
    color: black;
    border-radius: 4px;
    margin-left: 6px;
    white-space: nowrap; /* ป้องกันขึ้นบรรทัดใหม่ */
}

/* ปรับปุ่ม Trandar Store */
.header-top-right .header-link a[href*="shop"] {
    padding: 6px 10px;
    font-size: 13px;
    display: inline-block;
    white-space: nowrap;
}

/* ปรับ container ให้ไม่ล้น */
.header-top-right {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    align-items: center;
    justify-content: flex-end;
}

/* Responsive เฉพาะหน้าจอเล็ก */
@media (max-width: 768px) {
    .header-top-right a#myBtn-sign-in,
    .header-top-right a[href*="register"],
    .header-top-right .header-link a[href*="shop"],
    .header-top-right .header-link a {
        font-size: 12px;
        padding: 4px 8px;
    }

    .header-top-right {
        justify-content: flex-start;
        gap: 4px;
    }

    .language-select {
        font-size: 13px;
        padding: 3px 6px;
        max-width: 100px;
    }
}
</style>

</html>

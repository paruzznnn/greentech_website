<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include $_SERVER['DOCUMENT_ROOT'] . '/trandar/lib/connect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/trandar/lib/base_directory.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT profile_img FROM mb_user WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $img_file = "default.png";
    if ($row = $result->fetch_assoc()) {
        $img_file = $row['profile_img'];
    }
}

if (!isset($base_path_admin)) {
    $base_path_admin = '/app/admin/';
}
?>

<!-- ✅ Header -->
<div class="header-topp">
  <div class="container-fluid">
    <!-- ซ้าย -->
    <div class="header-top-left">
      <span class="toggle-button" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
      </span>
      <a href="#"><img class="logo" src="<?php echo $new_path; ?>public/img/LOGOTRAND.png" alt=""></a>

    </div>

    <!-- ขวา -->
    <div class="header-top-right dropdown-wrapper">
          <!-- <div id="showOrigamiAiHtml"></div>
        <script src="https://dev.origami.life/plugin_chat_ai/plugin_chat.js"></script> -->
          <!-- 🔔 กระดิ่งแจ้งเตือน -->
      <div class="header-item">
        <i class="fas fa-bell" style="font-size: 30px;"></i>
      </div>

      <!-- 📦 Origami Dropdown -->
      <div class="header-item dropdown-parent" onclick="toggleDropdown('origamiDropdownMenu', event)">
        <img src="<?php echo $new_path; ?>public/img/origami_app.png" height="25" style="cursor:pointer;">
        <div id="origamiDropdownMenu" class="dropdown-box hidden">
          <a href="/app/admin/template/switch.php">
            <img src="<?php echo $new_path; ?>public/img/2_20180425103337.ico" height="40"><br>
            <span>Origami</span>
          </a>
          
        </div>
      </div>

      <!-- 🌐 Language Selector -->
       <div>
                <select id="language-select" class="language-select">
                </select>
            </div>
        
      <!-- 🟣 Profile Dropdown -->
      <div class="profile-container dropdown-parent" onclick="toggleDropdown('globalProfileDropdown', event)">
        <img src="<?php echo $new_path; ?>public/img/<?php echo htmlspecialchars($img_file); ?>" alt="Profile Picture" class="profile-pic">
        <div id="globalProfileDropdown" class="dropdown-box hidden">
          <a href="<?php echo $path_admin; ?>profile.php">Profile</a>
          <a href="<?php echo $path_admin; ?>logout.php">Logout</a>
        </div>
      </div>
    </div>
  </div>
</div>

<input type="hidden" name="new_path" id="new_path" value="<?php echo $new_path; ?>">
<input type="hidden" name="path_admin" id="path_admin" value="<?php echo $path_admin; ?>">

<div id="showTabSidebar" class="row row-tab"></div>


<!-- ✅ กล่องแชท AI ที่ยังซ่อน -->
<!-- <div id="ai-chatbox">
  <div id="ai-chatbox-resizable">
    <div id="ai-chatbox-inner">
      <div id="ai-chatbox-header">🤖 แชทกับ AI</div>
      <div id="ai-chat-messages"></div>
      <div id="ai-chat-input">
        <input type="text" id="userMessage" placeholder="พิมพ์ข้อความ...">
        <button onclick="sendToAI()">ส่ง</button>
      </div>
    </div>
  </div>
</div> -->

<!-- ✅ ปุ่ม toggle แสดง/ซ่อนกล่องแชท -->
<!-- <script>
  function toggleAIChat() {
    const box = document.getElementById("ai-chatbox");
    box.style.display = (box.style.display === "block") ? "none" : "block";
  }

  document.addEventListener("DOMContentLoaded", function () {
    const messages = document.getElementById('ai-chat-messages');
    const input = document.getElementById('userMessage');

    input?.addEventListener("keydown", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        sendToAI();
      }
    });

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
          replyText = parsed.reply || rawText;
        } catch {
          replyText = rawText;
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
</script>-->

<!-- ✅ JavaScript -->
<script>
function toggleSidebar() {
  document.body.classList.toggle('sidebar-open');
  // console.log(this)
}

// Toggle dropdown แบบทั่วไป
function toggleDropdown(id, event) {
  event.stopPropagation();

  // ซ่อน dropdown อื่น
  document.querySelectorAll('.dropdown-box').forEach(el => {
    if (el.id !== id) el.classList.add('hidden');
  });

  // toggle ตัวที่คลิก
  const el = document.getElementById(id);
  el.classList.toggle("hidden");
}

// ปิดทุก dropdown เมื่อคลิกนอก
document.addEventListener("click", function () {
  document.querySelectorAll('.dropdown-box').forEach(el => el.classList.add('hidden'));
});
</script>

<script>
function switchLanguage() {
  alert("เปลี่ยนภาษายังไม่กำหนดฟังก์ชัน (ไว้ใส่ redirect หรือ switch locale ได้ภายหลัง)");
  // หรือ redirect: window.location.href = '/?lang=en';
}
</script>



<!-- ✅ CSS -->
<style>

.header-topp {
  overflow: hidden;
    background-color: #fafafafa;
    /* box-shadow: 0px 3px 4px 0px #d9d9d9; */
    padding: 5px 10px;
    /* position: fixed; */
    top: 0px;
    width: 100%;
    transition: top 0.3s;
    /* z-index: 1000; */
    display: flex;
    justify-content: space-between;
    position: sticky;
    border-bottom: 1px solid #ececec;
    z-index: 10;         /* เอาไว้ให้ header อยู่บน */
    overflow: visible;   /* ✅ สำคัญ: dropdown จะไม่ถูกบีบอยู่ใน header */
}

/* .container-fluid {
  display: flex;
  justify-content: space-between;
  align-items: center;
} */

.header-top-left,
.header-top-right {
  display: flex;
  align-items: center;
}

.toggle-button {
  margin-right: 10px;
  cursor: pointer;
  padding-left:20px;
   /* เพิ่มขนาดตัวอักษร */
  font-size: 25px;

  /* ทำให้ตัวหนา */
  font-weight: bold;

  /* ตัวเลือกเพิ่มเติม: เปลี่ยนสีข้อความ */
  color: #333; /* หรือใช้ #000 สำหรับสีดำสนิท */
}

.logo {
  height: 50px;
}

.header-item {
  margin-left: 10px;
  position: relative;
  cursor: pointer;
}

.profile-container {
  margin-left: 0px;
  position: relative;
  cursor: pointer;
}

.profile-pic {
  width: 50px;
  height: 50px;
  border-radius: 50%;
}

/* ✅ Dropdown Container ตำแหน่งสัมพันธ์กับปุ่ม */
.dropdown-wrapper {
  position: relative;
}

.dropdown-box {
  position: absolute;
  top: 100%;
  right: 0;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 0px 10px rgba(0,0,0,0.15);
  z-index: 9999;
  padding: 12px 20px;
  display: flex;
  flex-direction: column;
  min-width: 100px;
}

#origamiDropdownMenu {
  flex-direction: row;
  min-width: 30px;
  justify-content: space-around;
}

.dropdown-box a {
  text-align: center;
  text-decoration: none;
  color: #333;
  font-size: 14px;
  margin: 5px 10px;
}

.dropdown-box img {
  display: block;
  margin: 0 auto 5px;
}

.dropdown-box a:hover {
  background-color: #f9f9f9;
  border-radius: 5px;
}

.hidden {
  display: none;
}
.lang-switcher img {
  border-radius: 4px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.2);
  cursor: pointer;
  transition: transform 0.2s;
}

.lang-switcher img:hover {
  transform: scale(1.05);
}
/* .ai-chat-icon {
  font-size: 20px;
  color: #333;
  cursor: pointer;
  transition: color 0.2s;
}

.ai-chat-icon:hover {
  color: #ff8200;
}
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
} */
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
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 1003;
        /* แก้ไขค่า z-index เพื่อให้แสดงผลทับเมนูหลัก */
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
</style>

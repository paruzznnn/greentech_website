// ===== CSS Styling =====
const chatStyle = document.createElement("style");
chatStyle.textContent = `
  #chat-button {
    position: fixed;
    bottom: 20px;
    left: 20px;
    background-color: #f18b20;
    color: white;
    border: none;
    border-radius: 50px;
    padding: 12px 20px;
    cursor: pointer;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    z-index: 1000;
  }

  #contact-options {
    display: none;
    position: fixed;
    bottom: 80px;
    left: 20px;
    background: white;
    border: 1px solid #ccc;
    border-radius: 10px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.2);
    padding: 10px;
    z-index: 1000;
  }

  .contact-option {
    display: block;
    margin: 8px 0;
    padding: 10px;
    background: #f1f1f1;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    width: 100%;
    text-align: left;
    font-size: 14px;
  }

  #chat-box {
    display: none;
    position: fixed;
    bottom: 160px;
    right: 20px;
    width: 300px;
    background: white;
    border: 1px solid #ccc;
    border-radius: 10px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.2);
    z-index: 1000;
  }

  #chat-header {
    background: #f18b20;
    color: white;
    padding: 10px;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    font-weight: bold;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  #chat-body {
    padding: 10px;
    height: 320px;
    overflow-y: auto;
    font-size: 14px;
  }

  #chat-box input[type="text"] {
    flex: 1;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 6px;
  }

  #chat-box button.send-button {
    padding: 8px 14px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
  }

  #chat-input-wrapper {
    display: flex;
    gap: 6px;
    padding: 10px;
  }

  @media screen and (orientation: landscape) and (max-width: 900px) {
    #chat-box {
      width: 90vw;
      bottom: 80px;
      right: 10px;
      max-height: 60vh;
    }

    #chat-body {
      height: 200px;
      max-height: 30vh;
    }

    #chat-button {
      bottom: 10px;
      left: 10px;
    }

    #contact-options {
      bottom: 60px;
      left: 10px;
      width: 90vw;
    }
  }
`;
document.head.appendChild(chatStyle);

// ===== สร้างปุ่ม "ติดต่อเรา" =====
const chatButton = document.createElement("button");
chatButton.id = "chat-button";
chatButton.innerText = "ติดต่อเรา";
document.body.appendChild(chatButton);

// ===== เมนูตัวเลือกช่องทางการติดต่อ =====
const contactMenu = document.createElement("div");
contactMenu.id = "contact-options";

const showChatBox = () => {
  contactMenu.style.display = "none";
  chatButton.style.display = "none";
  chatBox.style.display = "block";
};

const contactOptions = [
  {
    key: "line",
    label: "ติดต่อผ่าน LINE",
    lang: "",
    // url: "https://line.me/ti/p/"
    url: "#"
  },
  {
    key: "facebook",
    label: "ติดต่อผ่าน Facebook",
    lang: "",
    // url: "https://facebook.com/"
    url: "#"
  },
  {
    key: "chat",
    label: "คุยกับระบบแชท",
    lang: ""
  }
];

// ปุ่มตัวเลือกช่องทาง
contactOptions.forEach(option => {
  const btn = document.createElement("button");
  btn.className = "contact-option";
  btn.innerText = option.label;
  btn.dataset.lang = option.lang;
  btn.dataset.type = option.key;

  if (option.key === "chat") {
    btn.onclick = showChatBox;
  } else if (option.url) {
    btn.onclick = () => window.open(option.url, "_blank");
  }

  contactMenu.appendChild(btn);
});

document.body.appendChild(contactMenu);

// ===== กล่องแชท =====
const chatBox = document.createElement("div");
chatBox.id = "chat-box";

// ===== Header =====
const chatHeader = document.createElement("div");
chatHeader.id = "chat-header";

const headerTitle = document.createElement("span");
headerTitle.innerText = "สนทนา";

const chatCloseBtn = document.createElement("button");
chatCloseBtn.innerText = "X";
chatCloseBtn.style.background = "transparent";
chatCloseBtn.style.border = "none";
chatCloseBtn.style.color = "white";
chatCloseBtn.style.fontSize = "16px";
chatCloseBtn.style.cursor = "pointer";
chatCloseBtn.title = "ปิด";
chatCloseBtn.onclick = () => {
  chatButton.style.display = "block";
  chatBox.style.display = "none";
};

chatHeader.appendChild(headerTitle);
chatHeader.appendChild(chatCloseBtn);

// ===== Body =====
const chatBody = document.createElement("div");
chatBody.id = "chat-body";
chatBody.innerHTML = "<div>สวัสดี! มีอะไรให้ช่วยไหม?</div>";

// ===== Form =====
const chatForm = document.createElement("form");
// chatForm.id = "chat-form";

const chatInput = document.createElement("input");
chatInput.type = "text";
chatInput.id = "chat-input";
chatInput.placeholder = "พิมพ์ข้อความ...";
chatInput.required = true;
chatInput.style.flex = "1";

const sendBtn = document.createElement("button");
sendBtn.className = "send-button";
sendBtn.type = "submit"; // สำคัญ!
sendBtn.innerText = "ส่ง";

chatForm.appendChild(chatInput);
chatForm.appendChild(sendBtn);

// ===== ใส่ลงในกล่องหลัก =====
chatBox.appendChild(chatHeader);
chatBox.appendChild(chatBody);
chatBox.appendChild(chatForm);
document.body.appendChild(chatBox);

// API URLs
const sendURL = "http://localhost:3000/e-store/web_hook/service-chat-send.php";
const replyURL = "http://localhost:3000/e-store/web_hook/service-chat-reply.php";
const authToken = "ApiKey 1234567890";

// ฟังก์ชันส่ง + ดึงคำตอบ
function sendMessage() {
  const message = chatInput.value.trim();
  if (!message) return;

  const userId = localStorage.getItem("chatUserId") || "guest_" + Date.now();
  localStorage.setItem("chatUserId", userId); // เก็บ userId ไว้ถาวรระหว่าง session

  const payload = {
    message: message,
    userId: userId,
    channel: "customChat",
  };

  // แสดงข้อความผู้ใช้ในหน้าจอแชท
  const msgDiv = document.createElement("div");
  msgDiv.textContent = "คุณ: " + message;
  chatBody.appendChild(msgDiv);
  chatBody.scrollTop = chatBody.scrollHeight;

  // แสดงสถานะ "กำลังพิมพ์..."
  const typingDiv = document.createElement("div");
  typingDiv.textContent = "ระบบ: กำลังพิมพ์...";
  chatBody.appendChild(typingDiv);
  chatBody.scrollTop = chatBody.scrollHeight;

  // ส่งข้อความไปยัง API
  fetch(sendURL, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Authorization: authToken,
    },
    body: JSON.stringify(payload),
  })
    .then((res) =>
      res.ok ? res.json() : Promise.reject("ส่งข้อความไม่สำเร็จ")
    )
    .then(() => {
      // ดึงคำตอบหลังดีเลย์
      setTimeout(() => {
        fetch(
          `${replyURL}?userId=${encodeURIComponent(userId)}&channel=customChat`,
          {
            method: "GET",
            headers: {
              Authorization: authToken,
            },
          }
        )
          .then((res) =>
            res.ok ? res.json() : Promise.reject("ดึงคำตอบไม่สำเร็จ")
          )
          .then((data) => {

            chatBody.removeChild(typingDiv);
            if (data && data.reply) {
              const replyDiv = document.createElement("div");
              replyDiv.textContent = "ระบบ: " + data.reply;
              chatBody.appendChild(replyDiv);
              chatBody.scrollTop = chatBody.scrollHeight;
            } else {
              const replyDiv = document.createElement("div");
              replyDiv.textContent = "ระบบ: ไม่พบคำตอบ";
              chatBody.appendChild(replyDiv);
            }

          })
          .catch((err) => {
            chatBody.removeChild(typingDiv);
            console.error("ดึงคำตอบล้มเหลว:", err);
            const errorDiv = document.createElement("div");
            errorDiv.textContent = "ระบบ: ข้อผิดพลาดในการดึงคำตอบ";
            chatBody.appendChild(errorDiv);
          });
      }, 1000); // Delay 1 วินาที
    })
    .catch((err) => {
      chatBody.removeChild(typingDiv);
      console.error("ส่งคำถามล้มเหลว:", err);
      const errorDiv = document.createElement("div");
      errorDiv.textContent = "ระบบ: ข้อผิดพลาดในการส่งข้อความ";
      chatBody.appendChild(errorDiv);
    });

  chatInput.value = "";
}

sendBtn.addEventListener("click", sendMessage);
chatInput.addEventListener("keydown", (e) => {
  if (e.key === "Enter") sendMessage();
});

chatButton.addEventListener("click", () => {
  const isVisible = contactMenu.style.display === "block";
  contactMenu.style.display = isVisible ? "none" : "block";
});

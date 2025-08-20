export function setupAuthModal(loginTab, registerTab, loginContent, registerContent) {
  function setActiveTab(tabName) {
    if (tabName === "login") {
      loginTab.classList.add("active");
      registerTab.classList.remove("active");
      loginContent.classList.add("active");
      registerContent.classList.remove("active");
    } else {
      registerTab.classList.add("active");
      loginTab.classList.remove("active");
      registerContent.classList.add("active");
      loginContent.classList.remove("active");
    }
  }

  function openModalAuth() {
    document.getElementById("auth-modal").style.display = "flex";
    setActiveTab("login");
  }

  function closeModalAuth() {
    document.getElementById("auth-modal").style.display = "none";
  }

  document.getElementById("modal-auth-store1")?.addEventListener("click", openModalAuth);
  document.getElementById("modal-auth-close-store1")?.addEventListener("click", closeModalAuth);
  loginTab?.addEventListener("click", () => setActiveTab("login"));
  registerTab?.addEventListener("click", () => setActiveTab("register"));
}

export function setupPasswordValidation(passwordInput, confirmInput, matchMessage, rules) {
  function updateRules(value) {
    rules.length.className = `rule-${value.length >= 8 ? "pass" : "fail"}`;
    rules.lower.className = `rule-${/[a-z]/.test(value) ? "pass" : "fail"}`;
    rules.upper.className = `rule-${/[A-Z]/.test(value) ? "pass" : "fail"}`;
    rules.digit.className = `rule-${/[0-9]/.test(value) ? "pass" : "fail"}`;
    rules.special.className = `rule-${/[!@#$%^&*(),.?":{}|<>]/.test(value) ? "pass" : "fail"}`;
  }

  function checkPasswordMatch() {
    const pwd = passwordInput.value;
    const confirmPwd = confirmInput.value;

    if (!confirmPwd) {
      matchMessage.textContent = "";
      matchMessage.className = "message";
      return;
    }

    if (pwd === confirmPwd) {
      matchMessage.textContent = "รหัสผ่านตรงกัน";
      matchMessage.className = "message success";
    } else {
      matchMessage.textContent = "รหัสผ่านไม่ตรงกัน";
      matchMessage.className = "message error";
    }
  }

  document.addEventListener("DOMContentLoaded", () => {
    updateRules(passwordInput.value);
    checkPasswordMatch();
  });

  passwordInput?.addEventListener("input", () => {
    updateRules(passwordInput.value);
    checkPasswordMatch();
  });

  confirmInput?.addEventListener("input", checkPasswordMatch);
}

export function exposeTogglePassword(target = window) {
  target.togglePassword = function(id) {
    const input = document.getElementById(id);
    const icon = input?.nextElementSibling?.querySelector("i");
    if (!input || !icon) return;

    if (input.type === "password") {
      input.type = "text";
      icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
      input.type = "password";
      icon.classList.replace("fa-eye-slash", "fa-eye");
    }
  };
}

export function setupCookieModal() {
  function openModalCookie() {
    document.getElementById("cookie-modal").style.display = "flex";
  }

  function closeModalCookie() {
    document.getElementById("cookie-modal").style.display = "none";
  }

  document.getElementById("settingsCookies")?.addEventListener("click", openModalCookie);
  document.getElementById("modal-cookie-close")?.addEventListener("click", closeModalCookie);
}

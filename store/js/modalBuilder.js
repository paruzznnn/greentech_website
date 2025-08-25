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

export function setupPasswordValidation(passwordInput, confirmInput, matchMessage, rules, submitButton) {
  function updateRules(value) {
    const ruleStates = {
      length: value.length >= 8,
      lower: /[a-z]/.test(value),
      upper: /[A-Z]/.test(value),
      digit: /[0-9]/.test(value),
      special: /[!@#$%^&*(),.?":{}|<>]/.test(value),
    };

    rules.length.className = `rule-${ruleStates.length ? "pass" : "fail"}`;
    rules.lower.className = `rule-${ruleStates.lower ? "pass" : "fail"}`;
    rules.upper.className = `rule-${ruleStates.upper ? "pass" : "fail"}`;
    rules.digit.className = `rule-${ruleStates.digit ? "pass" : "fail"}`;
    rules.special.className = `rule-${ruleStates.special ? "pass" : "fail"}`;

    return Object.values(ruleStates).every(Boolean); // true if all rules pass
  }

  function checkPasswordMatch() {
    const pwd = passwordInput.value;
    const confirmPwd = confirmInput.value;

    if (!confirmPwd) {
      matchMessage.textContent = "โปรดยืนยันรหัสผ่าน";
      matchMessage.className = "message";
      return false;
    }

    if (pwd === confirmPwd) {
      matchMessage.textContent = "รหัสผ่านตรงกัน";
      matchMessage.className = "message success";
      return true;
    } else {
      matchMessage.textContent = "รหัสผ่านไม่ตรงกัน";
      matchMessage.className = "message error";
      return false;
    }
  }

  function updateSubmitState() {
    const rulesValid = updateRules(passwordInput.value);
    const passwordsMatch = checkPasswordMatch();
    submitButton.disabled = !(rulesValid && passwordsMatch);
  }

  document.addEventListener("DOMContentLoaded", () => {
    updateSubmitState();
  });

  passwordInput?.addEventListener("input", updateSubmitState);
  confirmInput?.addEventListener("input", updateSubmitState);
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

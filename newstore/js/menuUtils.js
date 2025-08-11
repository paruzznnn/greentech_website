export function adjustPosition(box) {
  const rect = box.getBoundingClientRect();
  const vw = window.innerWidth;
  if (rect.right > vw) box.style.left = `${box.offsetLeft - (rect.right - vw) - 10}px`;
  if (rect.left < 0) box.style.left = "10px";
}

export function resetPosition(box) {
  box.style.left = "";
  box.style.right = "";
}

export function handleToggleHover() {
  document.querySelectorAll(".toggle-btn-store1").forEach(btn => {
    const id = btn.getAttribute("data-id");
    const box = document.getElementById(id);
    let hideTimeout;

    const show = () => {
      clearTimeout(hideTimeout);
      box.classList.add("show");
      adjustPosition(box);
    };

    const hide = () => {
      hideTimeout = setTimeout(() => {
        if (!box.matches(":hover") && !btn.matches(":hover")) {
          box.classList.remove("show");
          resetPosition(box);
        }
      }, 100);
    };

    btn.addEventListener("mouseenter", show);
    btn.addEventListener("mouseleave", hide);
    box.addEventListener("mouseenter", show);
    box.addEventListener("mouseleave", hide);
  });
}

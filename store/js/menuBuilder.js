
export async function fetchHeader(req, call) {
  try {
    const params = new URLSearchParams({ action: req });
    const url = call + params.toString();
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Authorization': 'Bearer my_secure_token_123',
        'Content-Type': 'application/json'
      }
    });
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const res = await response.json();
    return res.data;
  } catch (error) {
    console.error('Fetch error:', error);
    return [];
  }
}

function generateSectionHTML(section) {
  if (!section || !section.type || !section.content) return "";

  switch (section.type) {
    case "image":
      return `
        <div class="section-store1">
          <h4>รูปภาพ</h4>
          <div class="section-gallery-store1">
            ${section.content.map(img => `<img src="${img.src}" alt="${img.alt || ''}">`).join("")}
          </div>
        </div>`;
    case "menu":
      return `
        <div class="section-store1">
          <h4>เมนู</h4>
          <ul class="submenu-store1">
            ${section.content.map(menu => `<li><a href="${menu.href}" class="sub-a-link-store1">${menu.icon || ''} ${menu.label}</a></li>`).join("")}
          </ul>
        </div>`;
    case "text":
      return `<div class="section-store1"><h4>รายละเอียด</h4><p>${section.content}</p></div>`;
    default:
      return "";
  }
}

function generateItemHTML(item, contentArray) {
  if (item.hasToggle && item.id) {
    const contentObj = contentArray.find(c => c.id === item.id);
    if (!contentObj) return "";

    const sectionsHTML = ["section1", "section2", "section3", "section4"]
      .map(key => generateSectionHTML(contentObj[key]))
      .join("");

    return `
      <div class="toggle-wrapper-store1">
        <a href="${item.path}" class="a-link-store1 toggle-btn-store1" data-id="${item.id}">${item.icon} ${item.label}</a>
        <div id="${item.id}" class="toggle-box-store1">
          <div class="gallery-layout-store1">${sectionsHTML}</div>
        </div>
      </div>`;
  } else {
    return `
      <div class="toggle-wrapper-store1">
        <a href="${item.path}" class="a-link-store1 no-toggle-store1">${item.icon} ${item.label}</a>
      </div>`;
  }
}

export function buildLinkmenu(data, contentArray) {
  const container = document.getElementById("linkContainer");
  if (!container) return;

  const firstFive = data.slice(0, 5);
  const remaining = data.slice(5);

  container.innerHTML = firstFive.map(item => generateItemHTML(item, contentArray)).join("");

  if (remaining.length) {
    container.insertAdjacentHTML("beforeend", `
      <div class="toggle-wrapper-store1">
        <a id="openModalBtn" class="a-link-store1 no-toggle-store1">เมนูเพิ่มเติม (${remaining.length})</a>
      </div>
    `);

    document.getElementById("openModalBtn")?.addEventListener("click", () => {
      console.log("เปิดเมนูเพิ่มเติม:", remaining);
    });
  }

  handleToggleHover();
}

export function buildLinkmenuSlide(menuData) {
  const container = document.getElementById("menuListContainer");
  if (!container) return;
  container.innerHTML = "";

  menuData.forEach(group => {
    const section = document.createElement("p");
    section.style.fontSize = "13px";
    section.style.color = "#555";
    section.textContent = group.section;
    container.appendChild(section);

    const ul = document.createElement("ul");
    ul.classList.add("menu-list2");

    group.items.forEach(item => {
      const li = document.createElement("li");
      const a = document.createElement("a");
      a.href = item.href;
      a.innerHTML = `${item.icon || ''} <span>${item.label}</span>`;
      li.appendChild(a);
      ul.appendChild(li);
    });

    container.appendChild(ul);
  });
}

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

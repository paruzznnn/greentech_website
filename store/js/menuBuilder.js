
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


function buildMenuSection(section) {
  if (!section || !section.type || !section.content) return "";

  switch (section.type) {
    case "image":
      return `
        <div class="section-store1">
          <h4>รูปภาพ</h4>
          <div class="gallery-layout-store1 image-mode">
            ${section.content
              .map(
                (img) =>
                  `<div class="section-gallery-store1">
                    <img src="${img.src}" alt="${img.alt || ""}">
                  </div>`
              )
              .join("")}
          </div>
        </div>`;
    case "menu":
      return `
        <div class="section-store1">
          <h4>เมนู</h4>
          <div class="">
            <ul class="submenu-store1">
              ${section.content
                .map(
                  (menu) =>
                    `<li><a href="${menu.href}" class="sub-a-link-store1">
                      ${menu.icon || ""} ${menu.label}
                    </a></li>`
                )
                .join("")}
            </ul>
          </div>
        </div>`;
    case "text":
      return `
        <div class="section-store1">
          <h4>รายละเอียด</h4>
          <div class="">
            <p>${section.content}</p>
          </div>
        </div>`;
    default:
      return "";
  }
}


function buildLinkmenuBox(item, contentArray) {
  const contentObj = item.hasToggle && item.id 
    ? contentArray.find(c => c.id === item.id) 
    : null;

  if (!contentObj) {
    return `
      <div class="toggle-wrapper-store1">
        <a href="${item.path}" class="a-link-store1 no-toggle-store1">
          <i class="${item.icon}"></i> ${item.label}
        </a>
      </div>`;
  } else {
    const sections = ["section0", "section1", "section2", "section3"]
      .map(key => buildMenuSection(contentObj[key]))
      .filter(html => html !== ""); // เอาเฉพาะ section ที่มีค่า

    const sectionsHTML = sections.join("");

    // นับจำนวน section
    const count = sections.length;

    // สร้าง class name ตามจำนวน section
    let layoutClass = "gallery-layout-store1";
    if (count === 1) layoutClass += " layout-1";
    else if (count === 2) layoutClass += " layout-2";
    else if (count === 3) layoutClass += " layout-3";
    else if (count === 4) layoutClass += " layout-4";

    return `
      <div class="toggle-wrapper-store1">
        <a href="${item.path}" class="a-link-store1 toggle-btn-store1" data-id="${item.id}">
          <i class="${item.icon}"></i> ${item.label}
        </a>
        <div id="${item.id}" class="toggle-box-store1">
          <div class="${layoutClass}">${sectionsHTML}</div>
        </div>
      </div>`;
  }
}

export function buildLinkmenu(data, contentArray) {

  const container = document.getElementById("linkContainer");
  if (!container) return;

  const firstFive = data.slice(0, 7);
  const remaining = data.slice(7);

  container.innerHTML = firstFive.map(item => buildLinkmenuBox(item, contentArray)).join("");

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

function adjustPosition(box) {
  const rect = box.getBoundingClientRect();
  const vw = window.innerWidth;
  const margin = 20;

  if (rect.right > vw - margin) {
    const shift = rect.right - (vw - margin);
    box.style.left = `${box.offsetLeft - shift}px`;
  }

  if (rect.left < margin) {
    const shift = margin - rect.left;
    box.style.left = `${box.offsetLeft + shift}px`;
  }
}

function resetPosition(box) {
  box.style.left = "";
  box.style.right = "";
}

function handleToggleHover() {
  document.querySelectorAll(".toggle-btn-store1").forEach(btn => {
    const id = btn.getAttribute("data-id");
    const box = document.getElementById(id);
    let hideTimeout;

    const show = () => {
      clearTimeout(hideTimeout);
      box.classList.add("show");

      // รอให้ DOM render เสร็จก่อนคำนวณตำแหน่ง
      requestAnimationFrame(() => {
        adjustPosition(box);
      });
    };

    const hide = () => {
      hideTimeout = setTimeout(() => {
        if (!box.matches(":hover") && !btn.matches(":hover")) {
          box.classList.remove("show");
          resetPosition(box);
        }
      }, 120); // เผื่อเวลาขยับ mouse ไปที่ box
    };

    // bind events
    btn.addEventListener("mouseenter", show);
    btn.addEventListener("mouseleave", hide);
    box.addEventListener("mouseenter", show);
    box.addEventListener("mouseleave", hide);
  });
}

// เรียกใช้งาน
// document.addEventListener("DOMContentLoaded", handleToggleHover);


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

export function buildLinkmenuSlideAdmin(menuData, config) {
  const containers = [
    document.getElementById("menuListContainerAdmin1"),
    document.getElementById("menuListContainerAdmin2")
  ].filter(Boolean);

  if (containers.length === 0) return;

  // ฟังก์ชันสร้างเมนู
  function createMenu(items, isSub = false) {
    const ul = document.createElement("ul");
    ul.classList.add(isSub ? "submenu-list" : "menu-list");

    items.forEach(item => {
      const li = document.createElement("li");

      const a = document.createElement("a");
      a.href = item.link || "#";
      a.innerHTML = `
        ${item.icon || ''} 
        <span class="menu-title">${item.title}</span>
        ${item.subMenu && item.subMenu.length > 0
          ? '<span class="submenu-toggle"><i class="bi bi-chevron-down"></i></span>'
          : ''}
      `;
      li.appendChild(a);

      if (item.subMenu && item.subMenu.length > 0) {
        li.classList.add("has-submenu");

        const subMenuEl = createMenu(item.subMenu, true);
        subMenuEl.style.display = "none";
        li.appendChild(subMenuEl);

        a.addEventListener("click", (e) => {
          const clickedInsideTitle = e.target.closest(".menu-title");
          if (clickedInsideTitle) return;

          e.preventDefault();
          e.stopPropagation();

          const isOpen = subMenuEl.style.display === "block";
          subMenuEl.style.display = isOpen ? "none" : "block";

          const toggleIcon = a.querySelector(".submenu-toggle i");
          if (toggleIcon) {
            toggleIcon.className = isOpen ? "bi bi-chevron-down" : "bi bi-chevron-up";
          }
        });
      }

      ul.appendChild(li);
    });

    return ul;
  }

  function filterMenu(container, query) {
    const items = container.querySelectorAll("li");
    items.forEach(li => {
      const titleEl = li.querySelector(".menu-title");
      const text = titleEl ? titleEl.textContent.toLowerCase() : "";
      const match = text.includes(query);

      const subMenu = li.querySelector("ul");
      if (subMenu) {
        const subItems = subMenu.querySelectorAll("li");
        const hasMatchInSub = Array.from(subItems).some(subLi => {
          const subTitleEl = subLi.querySelector(".menu-title");
          return subTitleEl && subTitleEl.textContent.toLowerCase().includes(query);
        });

        li.style.display = match || hasMatchInSub ? "block" : "none";
        if (hasMatchInSub) {
          subMenu.style.display = "block";
        }
      } else {
        li.style.display = match ? "block" : "none";
      }
    });
  }

  const defaultMenu = [
    {
      title: "การจัดการเมนู", 
      link: config.BASE_WEB + "admin/hyperlink/", 
      icon: '<i class="bi bi-gear"></i>',
      subMenu: [
        // { title: "รายการเมนู", link: config.BASE_WEB + "admin/control/" },
        // { title: "ติดตั้งเมนู", link: config.BASE_WEB + "admin/control/setup_link/" },
        // { title: "ระบบสำรองข้อมูล", link: config.BASE_WEB + "admin/control_link/backup" }
      ]
    },
    { 
      title: "ออกจากระบบ", 
      link: config.BASE_WEB + "logout.php", 
      icon: '<i class="bi bi-box-arrow-right"></i>' 
    }
  ];

  containers.forEach((container, index) => {
    container.innerHTML = '';

    const searchInput = document.createElement("input");
    searchInput.type = "text";
    searchInput.placeholder = "ค้นหาเมนู...";
    searchInput.classList.add("menu-search");
    searchInput.id = `menuSearchAdmin${index + 1}`;

    container.appendChild(searchInput);

    const menu = createMenu(menuData);
    container.appendChild(menu);

    const fixedMenu = createMenu(defaultMenu);
    fixedMenu.classList.add("default-menu");
    container.appendChild(fixedMenu);

    searchInput.addEventListener("input", () => {
      filterMenu(container, searchInput.value.toLowerCase());
    });
  });
}



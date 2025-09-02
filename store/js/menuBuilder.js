
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

// export function buildLinkmenuSlideAdmin(menuData) {
//   const containers = [
//     document.getElementById("menuListContainerAdmin1"),
//     document.getElementById("menuListContainerAdmin2")
//   ].filter(Boolean);

//   if (containers.length === 0) return;

//   // ฟังก์ชันสร้างเมนู
//   function createMenu(items, isSub = false) {
//     const ul = document.createElement("ul");
//     ul.classList.add(isSub ? "submenu-list" : "menu-list");

//     items.forEach(item => {
//       const li = document.createElement("li");

//       const a = document.createElement("a");
//       a.href = item.link || "#";
//       a.innerHTML = `
//         ${item.icon || ''} 
//         <span class="menu-title">${item.title}</span>
//         ${item.subMenu && item.subMenu.length > 0
//           ? '<span class="submenu-toggle"><i class="bi bi-chevron-down"></i></span>'
//           : ''}
//       `;
//       li.appendChild(a);

//       if (item.subMenu && item.subMenu.length > 0) {
//         li.classList.add("has-submenu");

//         const subMenuEl = createMenu(item.subMenu, true);
//         subMenuEl.style.display = "none";
//         li.appendChild(subMenuEl);

//         a.addEventListener("click", (e) => {
//           const clickedInsideTitle = e.target.closest(".menu-title");

//           // ถ้าคลิกที่ชื่อเมนูให้ไปตามลิงก์
//           if (clickedInsideTitle) return;

//           // ถ้าไม่ใช่ชื่อเมนู ให้ toggle submenu
//           e.preventDefault();
//           e.stopPropagation();

//           const isOpen = subMenuEl.style.display === "block";
//           subMenuEl.style.display = isOpen ? "none" : "block";

//           const toggleIcon = a.querySelector(".submenu-toggle i");
//           if (toggleIcon) {
//             toggleIcon.className = isOpen ? "bi bi-chevron-down" : "bi bi-chevron-up";
//           }
//         });
//       }

//       ul.appendChild(li);
//     });

//     return ul;
//   }

//   // ฟังก์ชัน filter เมนู
//   function filterMenu(container, query) {
//     const items = container.querySelectorAll("li");
//     items.forEach(li => {
//       const titleEl = li.querySelector(".menu-title");
//       const text = titleEl ? titleEl.textContent.toLowerCase() : "";
//       const match = text.includes(query);

//       // ถ้ามี submenu ต้องเช็คด้วย
//       const subMenu = li.querySelector("ul");
//       if (subMenu) {
//         const subItems = subMenu.querySelectorAll("li");
//         const hasMatchInSub = Array.from(subItems).some(subLi => {
//           const subTitleEl = subLi.querySelector(".menu-title");
//           return subTitleEl && subTitleEl.textContent.toLowerCase().includes(query);
//         });

//         li.style.display = match || hasMatchInSub ? "block" : "none";
//         if (hasMatchInSub) {
//           subMenu.style.display = "block"; // เปิด submenu อัตโนมัติถ้ามี match
//         }
//       } else {
//         li.style.display = match ? "block" : "none";
//       }
//     });
//   }

// containers.forEach((container, index) => {
//   container.innerHTML = '';

//   // เพิ่มช่องค้นหา พร้อม id
//   const searchInput = document.createElement("input");
//   searchInput.type = "text";
//   searchInput.placeholder = "ค้นหาเมนู...";
//   searchInput.classList.add("menu-search");
//   searchInput.id = `menuSearchAdmin${index + 1}`; // id จะเป็น menuSearchAdmin1, menuSearchAdmin2

//   container.appendChild(searchInput);

//   const menu = createMenu(menuData);
//   container.appendChild(menu);

//   // Event สำหรับ filter
//   searchInput.addEventListener("input", () => {
//     filterMenu(container, searchInput.value.toLowerCase());
//   });
// });

// }

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
      link: config.BASE_WEB + "admin/control/", 
      icon: '<i class="bi bi-gear"></i>',
      subMenu: [
        // { title: "การตั้งค่าทั่วไป", link: config.BASE_WEB + "admin/control_link/" },
        { title: "ติดตั้งเมนู", link: config.BASE_WEB + "admin/control/setup_link/" },
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

    // เพิ่มช่องค้นหา พร้อม id
    const searchInput = document.createElement("input");
    searchInput.type = "text";
    searchInput.placeholder = "ค้นหาเมนู...";
    searchInput.classList.add("menu-search");
    searchInput.id = `menuSearchAdmin${index + 1}`;

    container.appendChild(searchInput);

    // เมนูจาก menuData
    const menu = createMenu(menuData);
    container.appendChild(menu);

    // เมนูค่าเริ่มต้นอยู่ด้านล่างสุด
    const fixedMenu = createMenu(defaultMenu);
    fixedMenu.classList.add("default-menu");
    container.appendChild(fixedMenu);

    // Event filter
    searchInput.addEventListener("input", () => {
      filterMenu(container, searchInput.value.toLowerCase());
    });
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

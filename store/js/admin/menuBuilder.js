const MENU_DATA = [{
    icon: '<i class="bi bi-speedometer2"></i>',
    title: "แดชบอร์ด",
    link: "#dashboard"
},
{
    icon: '<i class="bi bi-people"></i>',
    title: "ผู้ใช้งาน",
    link: "#users",
    subMenu: [{
        icon: '<i class="bi bi-person-lines-fill"></i>',
        title: "สมาชิกทั้งหมด",
        link: "#users/all"
    },
    {
        icon: '<i class="bi bi-person-plus"></i>',
        title: "เพิ่มสมาชิกใหม่",
        link: "#users/add"
    },
    {
        icon: '<i class="bi bi-people-fill"></i>',
        title: "กลุ่มผู้ใช้",
        link: "#users/groups",
        subMenu: [{
            icon: '<i class="bi bi-shield-check"></i>',
            title: "Admin",
            link: "#users/groups/admin"
        },
        {
            icon: '<i class="bi bi-shield"></i>',
            title: "Moderator",
            link: "#users/groups/moderator"
        },
        {
            icon: '<i class="bi bi-person-circle"></i>',
            title: "Customer",
            link: "#users/groups/customer"
        }
        ]
    }
    ]
},
{
    icon: '<i class="bi bi-box-seam"></i>',
    title: "สินค้า",
    link: "#products",
    subMenu: [{
        icon: '<i class="bi bi-grid"></i>',
        title: "สินค้าทั้งหมด",
        link: "#products/all"
    },
    {
        icon: '<i class="bi bi-plus-circle"></i>',
        title: "เพิ่มสินค้าใหม่",
        link: "#products/add"
    },
    {
        icon: '<i class="bi bi-tags"></i>',
        title: "หมวดหมู่สินค้า",
        link: "#products/categories"
    }
    ]
},
{
    icon: '<i class="bi bi-graph-up"></i>',
    title: "รายงาน",
    link: "#reports"
},
{
    icon: '<i class="bi bi-gear"></i>',
    title: "การตั้งค่า",
    link: "#settings"
},
{
    icon: '<i class="bi bi-clock-history"></i>',
    title: "ประวัติ",
    link: "#history"
}
];

export function renderMenus(sidebarNav, mobileNav) {

    if (sidebarNav) {
        renderMenu(MENU_DATA, sidebarNav, false);
    }

    if (mobileNav) {
        renderMenu(MENU_DATA, mobileNav, true);
    }
}

export function renderMenu(menuItems, parentElement, isMobile = false) {
    const fragment = document.createDocumentFragment();

    menuItems.forEach(item => {
        const menuItem = createMenuItem(item, isMobile);
        fragment.appendChild(menuItem);
    });

    parentElement.appendChild(fragment);
}

export function createMenuItem(item, isMobile) {
    const li = document.createElement('li');
    li.classList.add('nav-item');

    const a = document.createElement('a');
    a.classList.add('sidebar-link');
    a.href = item.link || '#';
    a.setAttribute('data-title', item.title);

    if (item.subMenu) {
        li.classList.add('has-sub-menu');
        a.setAttribute('data-action', 'toggle-sub-menu');

        const iconHTML = item.icon || '';
        const toggleIcon = isMobile ?
            '<i class="bi bi-chevron-right toggle-icon"></i>' :
            '<i class="bi bi-chevron-down toggle-icon"></i>';

        a.innerHTML = `
                <span class="sidebar-text">
                    ${iconHTML}
                    <span class="menu-title">${item.title}</span>
                    ${toggleIcon}
                </span>
            `;

        // สร้าง submenu
        const subUl = document.createElement('ul');
        subUl.classList.add('sidebar-sub-menu');
        renderMenu(item.subMenu, subUl, isMobile);

        li.appendChild(a);
        li.appendChild(subUl);
    } else {
        const iconHTML = item.icon || '';
        a.innerHTML = `
                <span class="sidebar-text">
                    ${iconHTML}
                    <span class="menu-title">${item.title}</span>
                </span>
            `;
        li.appendChild(a);
    }

    return li;
}
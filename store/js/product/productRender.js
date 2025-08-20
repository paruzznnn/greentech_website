import { redirectGet } from '../formHandler.js';

export async function initCardUI({
    containerId,
    apiUrl,
    authToken = '',
    BASE_WEB
}) {
    let allCards = [];
    const container = document.getElementById(containerId);
    function renderCards() {
        container.innerHTML = '';
        if (allCards.length === 0) {
            container.innerHTML = `<p class="col-span-4 text-center text-gray-500">No results found</p>`;
            return;
        }
        allCards.forEach(card => {
            const div = document.createElement('div');
            div.className = 'col-md-6 col-sm-6 pb-3';
            div.innerHTML = `
                <div class="search-card-product" style="cursor: pointer;">
                    <img src="${card.img}" alt="">
                    <div class="overlay-text">${card.title}</div>
                </div>`;
            div.querySelector('.search-card-product').addEventListener('click', () => {
                window.location.href = `${BASE_WEB}product/list/?categoryid=${card.id}`;
            });
            container.appendChild(div);
        });
    }

    async function fetchCards() {
        try {
            container.innerHTML = `<p class="col-span-4 text-center text-gray-500">Loading...</p>`;
            const response = await fetch(apiUrl, {
                headers: {
                    'Authorization': `Bearer ${authToken}`,
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Failed to fetch cards');

            const res = await response.json();
            const data = res.data;

            // console.log('data', data);
            

            allCards = data.map(item => ({
                id: item.category_id || '',
                img: item.image || '',
                title: item.category || `Card ${item.id}`,
                description: item.description || 'No description'
            }));

            renderCards();
        } catch (err) {
            container.innerHTML = `<p class="col-span-4 text-red-500 text-center">Error loading cards: ${err.message}</p>`;
        }
    }

    await fetchCards();
}

export async function fetchCategoryData(req, call) {
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

        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        return { data: [] };
    }
}

export function buildCategoryMenu(parentSelector, menuItems) {

    let html = '';

    for (const item of menuItems) {

        let iconHtml = '';
        if (item.icon && item.icon.trim() !== '') {
            iconHtml = '<span class="icon">' + item.icon + '</span> ';
        }

        let hasChildren = false;
        if (item.children && item.children.length > 0) {
            hasChildren = true;
        }

        let className = 'menu-item';
        if (hasChildren) {
            className += ' has-children';
        }

        html += '<li class="' + className + '">';

        if (item.label && item.label.trim() !== '') {
            html += '<a href="' + (item.href ? item.href : '#') + '" class="menu-link">';
            html += item.label + ' ' + iconHtml;
            html += '</a>';
        } else {
            if (iconHtml !== '') {
                html += '<span class="menu-link">' + iconHtml + '</span>';
            }
        }

        if (hasChildren) {
            html += '<ul class="submenu">';
            for (const child of item.children) {
                let childIconHtml = '';
                if (child.icon && child.icon.trim() !== '') {
                    childIconHtml = '<span class="icon">' + child.icon + '</span> ';
                }
                html += '<li>';
                html += '<a href="' + (child.href ? child.href : '#') + '">' + childIconHtml + child.label + '</a>';
                html += '</li>';
            }
            html += '</ul>';
        }

        html += '</li>';
    }

    document.querySelector(parentSelector).innerHTML = html;

    // เพิ่ม event สำหรับเปิดปิด submenu
    // const menuItemsWithChildren = document.querySelectorAll(parentSelector + ' .has-children > .menu-link');
    // menuItemsWithChildren.forEach(link => {
    //     link.addEventListener('click', function(e) {
    //         e.preventDefault();
    //         const submenu = this.nextElementSibling;
    //         if (submenu) {
    //             if (submenu.classList.contains('open')) {
    //                 submenu.classList.remove('open');
    //             } else {
    //                 submenu.classList.add('open');
    //             }
    //         }
    //     });
    // });
}

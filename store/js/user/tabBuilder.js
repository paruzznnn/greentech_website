export function setupTabs(menuSelector = '#profileMenu li', tabSelector = '.tabContent') {
    const menuItems = document.querySelectorAll(menuSelector);
    const tabContents = document.querySelectorAll(tabSelector);

    menuItems.forEach(item => {
        item.addEventListener('click', () => {
            menuItems.forEach(i => i.classList.remove('active'));
            item.classList.add('active');

            tabContents.forEach(tab => tab.classList.remove('active'));
            const target = item.getAttribute('data-tab');
            const targetElement = document.getElementById(target);
            if (targetElement) {
                targetElement.classList.add('active');
            }
        });
    });
}

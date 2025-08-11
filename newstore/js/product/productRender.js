export async function initCardUI({
    containerId,
    apiUrl,
    authToken = ''
}) 
{
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
                </div>
            `;

            div.querySelector('.search-card-product').addEventListener('click', () => {
                window.location.href = `/e-store/product/list/?id=${card.id}`;
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

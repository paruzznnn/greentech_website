export async function initCardUI({
    containerId,
    // searchInputId,
    pageInfoId,
    prevButtonId,
    nextButtonId,
    minPriceInputId,
    maxPriceInputId,
    minPriceLabelId,
    maxPriceLabelId,
    priceRangeSelectedId,
    // clearFiltersBtnId,
    cardsPerPage = 6,
    maxPrice = 10000,
    apiUrl,
    authToken = '',
    BASE_WEB
}) {
    let currentPage = 1;
    let allCards = [];
    let filteredCards = [];

    const container = document.getElementById(containerId);
    // const searchInput = document.getElementById(searchInputId);
    const pageInfo = document.getElementById(pageInfoId);
    const prevBtn = document.getElementById(prevButtonId);
    const nextBtn = document.getElementById(nextButtonId);

    const minPriceInput = document.getElementById(minPriceInputId);
    const maxPriceInput = document.getElementById(maxPriceInputId);
    const minPriceLabel = document.getElementById(minPriceLabelId);
    const maxPriceLabel = document.getElementById(maxPriceLabelId);
    const priceRangeSelected = document.getElementById(priceRangeSelectedId);

    // const clearFiltersBtn = document.getElementById(clearFiltersBtnId);

    function updatePriceRangeBar() {
        let minVal = Number(minPriceInput.value);
        let maxVal = Number(maxPriceInput.value);
        if (minVal > maxVal) {
            [minVal, maxVal] = [maxVal, minVal];
            minPriceInput.value = minVal;
            maxPriceInput.value = maxVal;
        }
        const minPercent = (minVal / maxPrice) * 100;
        const maxPercent = (maxVal / maxPrice) * 100;

        priceRangeSelected.style.left = minPercent + '%';
        priceRangeSelected.style.width = (maxPercent - minPercent) + '%';

        minPriceLabel.textContent = minVal.toLocaleString();
        maxPriceLabel.textContent = maxVal.toLocaleString();
    }

    function renderPriceProgress(price) {
        const percent = Math.min((price / maxPrice) * 100, 100);
        let barClass = 'bg-success';
        if (percent > 70) barClass = 'bg-danger';
        else if (percent > 40) barClass = 'bg-warning';

        return `
        <div class="mt-2">
            <small>Price: ${price.toLocaleString()} / ${maxPrice.toLocaleString()}</small>
            <div class="progress" style="height: 10px;">
                <div class="progress-bar ${barClass}" role="progressbar" style="width: ${percent}%"></div>
            </div>
        </div>
        `;
    }

    function renderCards() {
        const start = (currentPage - 1) * cardsPerPage;
        const end = start + cardsPerPage;
        const cardsToDisplay = filteredCards.slice(start, end);

        container.innerHTML = '';
        if (cardsToDisplay.length === 0) {
            container.innerHTML = `<p class="text-center text-muted">ไม่พบผลลัพธ์</p>`;
            return;
        }

        cardsToDisplay.forEach(card => {
            const col = document.createElement('div');
            col.className = 'col-md-12 col-sm-6 mb-4';
            col.innerHTML = `
            <div class="search-card-product-list">
            <a href="${BASE_WEB}product/detail/?id=" class="product-image">
                <img src="${card.img}" alt="${card.title}">
            </a>
            <div class="product-info">
                <h6>${card.title}</h6>
                <p>${card.description}</p>
                <p><b>Color:</b> ${card.color}</p>
                <p><b>Size:</b> ${card.size}</p>
                <p><b>Material:</b> ${card.material}</p>
            </div>
            <div class="product-extra">
                ${renderPriceProgress(card.price)}
                
            </div>
            </div>
            `;
            container.appendChild(col);
        });

        pageInfo.textContent = `Page ${currentPage} of ${Math.ceil(filteredCards.length / cardsPerPage)}`;
    }

    function applyFilter() {
        // const query = searchInput.value.toLowerCase();
        let minVal = Number(minPriceInput.value);
        let maxVal = Number(maxPriceInput.value);
        if (minVal > maxVal) {
            [minVal, maxVal] = [maxVal, minVal];
            minPriceInput.value = minVal;
            maxPriceInput.value = maxVal;
            updatePriceRangeBar();
        }

        const selectedColors = Array.from(document.querySelectorAll('.filter-color:checked')).map(cb => cb.value);
        const selectedSizes = Array.from(document.querySelectorAll('.filter-size:checked')).map(cb => cb.value);
        const selectedMaterials = Array.from(document.querySelectorAll('.filter-material:checked')).map(cb => cb.value);

        filteredCards = allCards.filter(card => {
            // const matchesSearch = card.title.toLowerCase().includes(query) || card.description.toLowerCase().includes(query);
            const matchesPrice = card.price >= minVal && card.price <= maxVal;
            const matchesColor = selectedColors.length === 0 || selectedColors.includes(card.color);
            const matchesSize = selectedSizes.length === 0 || selectedSizes.includes(card.size);
            const matchesMaterial = selectedMaterials.length === 0 || selectedMaterials.includes(card.material);
            // return matchesSearch && matchesPrice && matchesColor && matchesSize && matchesMaterial;
            return matchesPrice && matchesColor && matchesSize && matchesMaterial;
        });

        currentPage = 1;
        renderCards();
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

            if (!response.ok) {
                throw new Error('Failed to fetch data from API');
            }

            const res = await response.json();
            const data = res.data || [];

            allCards = data.map(item => ({
                id: item.id || '',
                title: item.title || 'No title',
                description: item.description || 'No description',
                img: item.image || '',
                category: item.category || '',
                price: item.price || 0,
                color: item.color || '',
                size: item.size || '',
                material: item.material || ''
            }));

            filteredCards = [...allCards];
        } catch (err) {
            console.error('Error fetching cards:', err);
            container.innerHTML = `<p class="col-span-4 text-red-500 text-center">Error loading cards: ${err.message}</p>`;
        }
    }

    // function clearFilters() {
    //     searchInput.value = '';
    //     document.querySelectorAll('.filter-color, .filter-size, .filter-material').forEach(cb => cb.checked = false);
    //     minPriceInput.value = minPriceInput.min;
    //     maxPriceInput.value = maxPriceInput.max;
    //     updatePriceRangeBar();
    //     currentPage = 1;
    //     applyFilter();
    // }

    await fetchCards();
    updatePriceRangeBar();
    renderCards();

    minPriceInput.addEventListener("input", () => {
        updatePriceRangeBar();
        applyFilter();
    });
    maxPriceInput.addEventListener("input", () => {
        updatePriceRangeBar();
        applyFilter();
    });

    // searchInput.addEventListener("input", applyFilter);

    document.querySelectorAll(".filter-color, .filter-size, .filter-material").forEach(cb => {
        cb.addEventListener("change", applyFilter);
    });

    // clearFiltersBtn.addEventListener("click", clearFilters);

    prevBtn.addEventListener("click", () => {
        if (currentPage > 1) {
            currentPage--;
            renderCards();
        }
    });

    nextBtn.addEventListener("click", () => {
        if (currentPage < Math.ceil(filteredCards.length / cardsPerPage)) {
            currentPage++;
            renderCards();
        }
    });
}

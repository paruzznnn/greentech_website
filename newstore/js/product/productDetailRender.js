// ---------- API PRODUCT -----------------------------
export async function fetchProductData(req) {
    try {
        const params = new URLSearchParams({ action: req });
        const url = '/newstore/service/product/product-data?' + params.toString();

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

// ---------- PRODUCT DETAIL -----------------------------
export function createProductDetailHTML(selector, data) {

    const container = document.querySelector(selector);

    if(!data || !Array.isArray(data.images)){
        container.innerHTML = "<p>Product information not found</p>";
        return;
    }

    const thumbnails = data.images.map((img, index) => `
    <img src="${img}" class="product-detail-thumbnail-img ${index === 0 ? "active" : ""}" alt="Thumb ${index + 1}"
    onerror="this.onerror=null;this.src='https://placehold.co/80x80/e0e0e0/888888?text=No+Image';">
    `).join("");

    const carouselItems = data.images.map(img => `
    <div class="product-detail-carousel-item">
        <img src="${img}" alt="Product Image"
        onerror="this.onerror=null;this.src='https://placehold.co/600x400/e0e0e0/888888?text=No+Image';">
        </div>
    `).join("");

    const indicators = data.images.map((_, index) => `
    <div class="product-detail-indicator-dot ${index === 0 ? "active" : ""}"></div>
    `).join("");

    const features = data.features.map(feature => `
    <li class="product-detail-features-list-item">
        <span>${feature}</span>
    </li>
    `).join("");

    let div = `
    <div class="product-detail-card">
        <div class="product-detail-image-gallery">
        <div class="product-detail-main-image-carousel">
            <div class="product-detail-carousel-inner">${carouselItems}</div>`;

            if(data.images.length > 1){
                div += `
                <button class="product-detail-carousel-control product-detail-control-left" id="carouselPrevVibrant"><i class="fas fa-angle-left"></i></button>
                <button class="product-detail-carousel-control product-detail-control-right" id="carouselNextVibrant"><i class="fas fa-angle-right"></i></button>
                `;
            }

            div += `<div class="product-detail-carousel-indicators">${indicators}</div>
        </div>
        <div class="product-detail-thumbnail-strip-wrapper">`;


            if(data.images.length > 1){
                div += `<button class="product-detail-thumbnail-scroll-button" id="thumbPrevVibrant"><i class="fas fa-angle-left"></i></button>`;
            }

            div += `<div class="product-detail-thumbnail-strip">
            <div class="product-detail-thumbnail-list">${thumbnails}</div>
            </div>`;

            if(data.images.length > 1){
                div += `<button class="product-detail-thumbnail-scroll-button" id="thumbNextVibrant"><i class="fas fa-angle-right"></i></button>`;
            }

        div += `
        </div>
        </div>

        <div class="product-detail-info-section">
        <div>
            <h2 class="product-detail-title">${data.icon || ""} ${data.title || "ไม่พบชื่อสินค้า"}</h2>
            <p class="product-detail-category">${data.category || ""}</p>
            <div class="product-detail-price-section">
            <div class="product-detail-price-display">
                <span class="product-detail-current-price">฿${data.currentPrice || "-"}</span>
                <span class="product-detail-old-price">฿${data.oldPrice || "-"}</span>
                <span class="product-detail-discount-badge">ลด ${data.discountPercent || 0}%</span>
            </div>
            <button class="product-detail-add-to-cart-button">เพิ่มลงตะกร้า</button>
            </div>
        </div>
        </div>
    </div>

    <div class="product-detail-tabs-section">
        <div class="product-detail-tab-buttons-container">
        <button class="product-detail-tab-button active" data-tab="description">รายละเอียด</button>
        <button class="product-detail-tab-button" data-tab="features">คุณสมบัติ</button>
        <button class="product-detail-tab-button" data-tab="review">รีวิว</button>
        </div>
        <div id="tab-content-description" class="product-detail-tab-content active">
        <p class="product-detail-description-text">${data.description || ""}</p>
        </div>
        <div id="tab-content-features" class="product-detail-tab-content">
        <ul class="product-detail-features-list">${features}</ul>
        </div>
        <div id="tab-content-review" class="product-detail-tab-content">
        555
        </div>
    </div>
    `;

    container.innerHTML = div;
}

// ---------- INIT PRODUCT -----------------------------
export function initProductDetailLogic(selector, images) {

    const container = document.querySelector(selector);

    const carouselInner = container.querySelector(".product-detail-carousel-inner");
    const carouselItems = container.querySelectorAll(".product-detail-carousel-item");
    const thumbnails = container.querySelectorAll(".product-detail-thumbnail-img");
    const indicators = container.querySelectorAll(".product-detail-indicator-dot");
    const prevBtn = container.querySelector("#carouselPrevVibrant");
    const nextBtn = container.querySelector("#carouselNextVibrant");
    const thumbStrip = container.querySelector(".product-detail-thumbnail-strip");
    const thumbPrev = container.querySelector("#thumbPrevVibrant");
    const thumbNext = container.querySelector("#thumbNextVibrant");

    let currentIndex = 0;
    const totalItems = images.length;

    function updateCarousel() {
        const width = carouselItems[0].clientWidth;
        carouselInner.style.transform = `translateX(-${currentIndex * width}px)`;

        thumbnails.forEach((el, i) => el.classList.toggle("active", i === currentIndex));
        indicators.forEach((el, i) => el.classList.toggle("active", i === currentIndex));

        const activeThumb = thumbnails[currentIndex];
        if (thumbStrip && activeThumb) {
            const containerRect = thumbStrip.getBoundingClientRect();
            const thumbRect = activeThumb.getBoundingClientRect();
            if (thumbRect.left < containerRect.left || thumbRect.right > containerRect.right) {
                thumbStrip.scrollTo({
                    left: activeThumb.offsetLeft - containerRect.width / 2 + thumbRect.width / 2,
                    behavior: 'smooth'
                });
            }
        }
    }

    prevBtn?.addEventListener("click", () => {
        currentIndex = (currentIndex - 1 + totalItems) % totalItems;
        updateCarousel();
    });

    nextBtn?.addEventListener("click", () => {
        currentIndex = (currentIndex + 1) % totalItems;
        updateCarousel();
    });

    thumbnails.forEach((el, i) => el.addEventListener("click", () => {
        currentIndex = i;
        updateCarousel();
    }));

    indicators.forEach((el, i) => el.addEventListener("click", () => {
        currentIndex = i;
        updateCarousel();
    }));

    thumbPrev?.addEventListener("click", () => {
        thumbStrip.scrollBy({
            left: -240,
            behavior: "smooth"
        });
    });

    thumbNext?.addEventListener("click", () => {
        thumbStrip.scrollBy({
            left: 240,
            behavior: "smooth"
        });
    });

    // Tabs
    const tabButtons = container.querySelectorAll(".product-detail-tab-button");
    const tabContents = container.querySelectorAll(".product-detail-tab-content");

    tabButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            tabButtons.forEach(b => b.classList.remove("active"));
            tabContents.forEach(c => c.classList.remove("active"));
            btn.classList.add("active");
            const tab = btn.dataset.tab;
            container.querySelector(`#tab-content-${tab}`)?.classList.add("active");
        });
    });

    window.addEventListener("resize", updateCarousel);
    updateCarousel(); // initial
}

// ---------- PRODUCT SIMILAR -----------------------------
export function createProductSimilarHTML(selector, items) {

    const container = document.querySelector(selector);

    if(!items || items.length === 0){
        container.innerHTML = "<p>Product information not found</p>";
        return;
    }

    items.forEach(item => {
        const div = document.createElement("div");
        div.classList.add("item");
        div.innerHTML = `
        <div class="store-card">
            <div class="store-card-row">
            <div class="store-card-header"></div>
            <img src="${item.image}" alt="" class="store-card-image" />
            <div class="store-card-body"></div>
            <ul class="store-list-group">
                <li class="store-list-item">An item</li>
                <li class="store-list-item">A second item</li>
                <li class="store-list-item">A third item</li>
            </ul>
            <div class="store-card-footer"></div>
            </div>
        </div>
        `;
        container.appendChild(div);
    });

    $(selector).owlCarousel({
        loop: true,
        margin: 10,
        nav: true,
        dots: false,
        autoWidth: true,
    });

}
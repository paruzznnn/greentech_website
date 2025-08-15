import { redirectGet, showNotification } from '../formHandler.js';

//============== API PRODUCT ===========================
export async function fetchProductData(req, call, obj) {

    try {
        const params = new URLSearchParams({
            action: req,
            product_id: obj.productId
        });

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

//=============== PRODUCT DETAIL + INIT PRODUCT ==========================
export const ProductDetailModule = {
    container: null,
    cartItems: [],
    data: null,
    BASE_WEB: null,

    //============== INIT + RENDER ========================
    init(selector, data, BASE_WEB) {
        this.data = data;
        this.container = document.querySelector(selector);
        this.BASE_WEB = BASE_WEB;

        if (!data || !Array.isArray(data.images)) {
            this.container.innerHTML = "<p>Product information not found</p>";
            return;
        }

        this.render();
        this.initLogic(selector, data.images);
        this.bindEvents();
        // this.clearCart();
    },

    render() {
        const { images, features, icon, title, category, description, productId } = this.data;
        const thumbnails = images.map((img, index) => `
            <img src="${img}" class="product-detail-thumbnail-img ${index === 0 ? "active" : ""}" alt="Thumb ${index + 1}">
        `).join("");
        const carouselItems = images.map(img => `
            <div class="product-detail-carousel-item">
                <img src="${img}" alt="Product Image">
            </div>
        `).join("");
        const indicators = images.map((_, index) => `
            <div class="product-detail-indicator-dot ${index === 0 ? "active" : ""}"></div>
        `).join("");
        const featuresList = features.map(feature => `
            <li class="product-detail-features-list-item">
                <span>${feature}</span>
            </li>
        `).join("");
        let html = `
        <div class="product-detail-card">
            <div class="product-detail-image-gallery">
                <div class="product-detail-main-image-carousel">
                    <div class="product-detail-carousel-inner">${carouselItems}</div>
                    ${images.length > 1 ? `
                        <button class="product-detail-carousel-control product-detail-control-left" id="carouselPrevVibrant"><i class="fas fa-angle-left"></i></button>
                        <button class="product-detail-carousel-control product-detail-control-right" id="carouselNextVibrant"><i class="fas fa-angle-right"></i></button>
                    ` : ""}
                    <div class="product-detail-carousel-indicators">${indicators}</div>
                </div>
                <div class="product-detail-thumbnail-strip-wrapper">
                    ${images.length > 1 ? `<button class="product-detail-thumbnail-scroll-button" id="thumbPrevVibrant"><i class="fas fa-angle-left"></i></button>` : ""}
                    <div class="product-detail-thumbnail-strip">
                        <div class="product-detail-thumbnail-list">${thumbnails}</div>
                    </div>
                    ${images.length > 1 ? `<button class="product-detail-thumbnail-scroll-button" id="thumbNextVibrant"><i class="fas fa-angle-right"></i></button>` : ""}
                </div>
            </div>
            <div class="product-detail-info-section">
                <h2 class="product-detail-title">${icon || ""} ${title || "ไม่พบชื่อสินค้า"}</h2>
                <p class="product-detail-category">${category || ""}</p>
                <div class="product-detail-price-section">
                    <div class="product-detail-quantity-control">
                        <button type="button" data-id="${productId}" data-action="decrease">-</button>
                        <input type="number" id="quantity-input" value="1" min="1" data-id="${productId}" class="quantity-input">
                        <button type="button" data-id="${productId}" data-action="increase">+</button>
                    </div>
                    <div class="product-detail-button-diisplay">
                        <button class="product-detail-button btn-add-cart" data-id="${productId}" data-action="addCart">
                            <i class="bi bi-cart3"></i>
                            <span>เพิ่มลงตะกร้า</span>
                        </button>
                        <button class="product-detail-button btn-add-order" data-id="${productId}" data-action="addOrder">
                            <span>ซื้อสินค้า</span>
                        </button>
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
                <p class="product-detail-description-text">${description || ""}</p>
            </div>
            <div id="tab-content-features" class="product-detail-tab-content">
                <ul class="product-detail-features-list">${featuresList}</ul>
            </div>
            <div id="tab-content-review" class="product-detail-tab-content"></div>
        </div>
        `;

        this.container.innerHTML = html;
    },

    //============== EVENTS ========================
    bindEvents() {
        this.container.addEventListener('click', (e) => {
            const target = e.target.closest('[data-action]');
            if (!target) return;

            const productId = target.dataset.id;
            const action = target.dataset.action;

            switch (action) {
                case "decrease":
                    this.updateQuantity(productId, -1);
                    break;
                case "increase":
                    this.updateQuantity(productId, 1);
                    break;
                case "addCart":
                    this.addCart(productId);
                    break;
                case "addOrder":
                    this.addOrder(productId);
                    break;
                default:
                    break;
            }
        });

        this.container.addEventListener('change', (e) => {
            const target = e.target;
            if (target.classList.contains('quantity-input')) {
                const productId = target.dataset.id;
                let newQuantity = Number(target.value);

                if (!Number.isInteger(newQuantity) || newQuantity < 1) {
                    target.value = 1;
                    newQuantity = 1;
                }

                // สมมติถ้าต้องการกำหนด max quantity เช่น 99
                const MAX_QUANTITY = 99;
                if (newQuantity > MAX_QUANTITY) {
                    target.value = MAX_QUANTITY;
                    newQuantity = MAX_QUANTITY;
                }

                let item = this.cartItems.find(item => item.id === productId);
                if (item) {
                    item.quantity = newQuantity;
                } else {
                    this.cartItems.push({ id: productId, quantity: newQuantity });
                }

                // this.saveCart();
            }
        });
    },

    addCart(productId) {
        let item = this.cartItems.find(item => item.id === productId);
        if (item) {
            
        } else {
            this.cartItems.push({ id: productId, quantity: 1 });
        }
        // this.saveCart();
        if(this.cartItems[0].quantity > 0){

            const existingCart = JSON.parse(localStorage.getItem("shoppingCart")) || [];
            const productIndex = existingCart.findIndex((item) => item.id === this.cartItems[0].id);

            if (productIndex !== -1) {
                existingCart[productIndex].quantity = this.cartItems[0].quantity;
                showNotification('เพิ่มสินค้าลงตะกร้าแล้ว', 'success');
            } else {
                existingCart.push({
                    id: this.data.productId,
                    name: this.data.title,
                    price: parseFloat(this.data.currentPrice),
                    quantity: parseInt(this.cartItems[0].quantity),
                    imageUrl: this.data.images[0],
                });
                showNotification('เพิ่มสินค้าลงตะกร้าแล้ว', 'success');
            }

            localStorage.setItem("shoppingCart", JSON.stringify(existingCart));
            
            // redirectGet(`${this.BASE_WEB}user/`, { id: productId });
        }
    },

    addOrder(productId) {
        let item = this.cartItems.find(item => item.id === productId);
        if (item) {
            
        } else {
            this.cartItems.push({ id: productId, quantity: 1 });
        }
        // this.saveCart();
        if(this.cartItems[0].quantity > 0){
            // redirectGet(`${this.BASE_WEB}user/`, { id: productId });
        }
    },

    updateQuantity(productId, change) {
        const input = this.container.querySelector(`.quantity-input[data-id="${productId}"]`);
        if (!input) return;
        let quantity = parseInt(input.value) || 1;
        quantity += change;
        if (quantity < 1) quantity = 1;
        input.value = quantity;
        let item = this.cartItems.find(item => item.id === productId);
        if (item) {
            item.quantity = quantity;
        } else {
            this.cartItems.push({ id: productId, quantity });
        }
        // this.saveCart();
    },

    // saveCart() {
        // localStorage.setItem("cart", JSON.stringify(this.cartItems));
    // },

    // clearCart() {
    //     this.cartItems = [];
    //     localStorage.removeItem("cart");
    //     console.log("Cart cleared.");
    // },

    //============== LOGIC (Carousel + Tabs) ========================
    initLogic(selector, images) {
        const self = this;
        const container = document.querySelector(selector);
        const state = {
            currentIndex: 0,
            images,
            get totalItems() {
                return this.images.length;
            }
        };

        const carouselInner = container.querySelector(".product-detail-carousel-inner");
        const carouselItems = container.querySelectorAll(".product-detail-carousel-item");
        const thumbnails = container.querySelectorAll(".product-detail-thumbnail-img");
        const indicators = container.querySelectorAll(".product-detail-indicator-dot");
        const prevBtn = container.querySelector("#carouselPrevVibrant");
        const nextBtn = container.querySelector("#carouselNextVibrant");
        const thumbStrip = container.querySelector(".product-detail-thumbnail-strip");
        const thumbPrev = container.querySelector("#thumbPrevVibrant");
        const thumbNext = container.querySelector("#thumbNextVibrant");
        const tabButtons = container.querySelectorAll(".product-detail-tab-button");
        const tabContents = container.querySelectorAll(".product-detail-tab-content");

        function updateCarousel() {
            const width = carouselItems[0]?.clientWidth || 0;
            carouselInner.style.transform = `translateX(-${state.currentIndex * width}px)`;

            thumbnails.forEach((el, i) => el.classList.toggle("active", i === state.currentIndex));
            indicators.forEach((el, i) => el.classList.toggle("active", i === state.currentIndex));

            const activeThumb = thumbnails[state.currentIndex];
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

        function goTo(index) {
            state.currentIndex = index;
            updateCarousel();
        }

        prevBtn?.addEventListener("click", () => {
            state.currentIndex = (state.currentIndex - 1 + state.totalItems) % state.totalItems;
            updateCarousel();
        });

        nextBtn?.addEventListener("click", () => {
            state.currentIndex = (state.currentIndex + 1) % state.totalItems;
            updateCarousel();
        });

        thumbnails.forEach((el, i) => {
            el.addEventListener("click", () => goTo(i));
        });

        indicators.forEach((el, i) => {
            el.addEventListener("click", () => goTo(i));
        });

        thumbPrev?.addEventListener("click", () => {
            thumbStrip.scrollBy({ left: -240, behavior: "smooth" });
        });

        thumbNext?.addEventListener("click", () => {
            thumbStrip.scrollBy({ left: 240, behavior: "smooth" });
        });

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
        updateCarousel();
    }
};

//============== PRODUCT SIMILAR ============================
export function createProductSimilarHTML(selector, items) {

    const container = document.querySelector(selector);

    if (!items || items.length === 0) {
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
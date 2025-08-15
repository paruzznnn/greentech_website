// Object Literal --------------------------------------
export const LikedProducts = {
    likedProducts: [],
    LIKED_PRODUCTS_STORAGE_KEY: 'likedProducts',

    init() {
        this.loadProducts();
        this.renderProducts();
    },

    loadProducts() {
        const storedProducts = localStorage.getItem(this.LIKED_PRODUCTS_STORAGE_KEY);
        if (storedProducts) {
            this.likedProducts = JSON.parse(storedProducts);
        }
    },

    saveProducts() {
        localStorage.setItem(this.LIKED_PRODUCTS_STORAGE_KEY, JSON.stringify(this.likedProducts));
    },

    createProductCard(product, ShoppingCart) {
        const card = document.createElement('div');
        card.className = 'product-card';
        card.innerHTML = `
            <img src="${product.imageUrl}" alt="${product.name}">
            <h3 class="product-name">${product.name}</h3>
            <p class="product-price">฿${product.price}</p>
            <div class="product-card-actions">
                <button type="button" class="action-button add-to-cart-button" data-id="${product.id}">เพิ่มลงตะกร้า</button>
                <button type="button" class="action-button remove-button" data-id="${product.id}">ลบ</button>
            </div>
        `;

        card.querySelector('.add-to-cart-button').addEventListener('click', () => ShoppingCart.addToCart(product));
        card.querySelector('.remove-button').addEventListener('click', () => this.removeFromLiked(product.id));
        return card;
    },

    renderProducts(ShoppingCart) {
        const container = document.getElementById('likedProductsGrid');
        if (!container) return;

        container.innerHTML = '';
        const oldMessage = container.parentNode.querySelector('.no-liked-products-message');
        if (oldMessage) oldMessage.remove();

        if (this.likedProducts.length === 0) {
            const message = document.createElement('p');
            message.className = 'no-liked-products-message';
            message.textContent = 'คุณยังไม่มีสินค้าที่ถูกใจ';

            container.parentNode.insertBefore(message, container);
        } else {
            this.likedProducts.forEach(product => {
                container.appendChild(this.createProductCard(product, ShoppingCart));
            });
        }
    },

    removeFromLiked(productId) {
        if (confirm('คุณแน่ใจหรือไม่ที่จะลบสินค้านี้ออกจากรายการที่ถูกใจ?')) {
            this.likedProducts = this.likedProducts.filter(product => product.id !== productId);
            this.saveProducts();
            this.renderProducts(window.ShoppingCart); // ต้องเรียก render ใหม่
            alert('ลบสินค้าออกจากรายการที่ถูกใจเรียบร้อยแล้ว!');
        }
    }
};

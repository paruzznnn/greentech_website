// Object Literal --------------------------------------
export const LikedProducts = {
    likedProducts: [],
    LIKED_PRODUCTS_STORAGE_KEY: 'userLikedProducts',

    init() {
        this.loadProducts();
        // this.addDummyProducts();
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
            <img src="${product.imageUrl}" alt="${product.name}" onerror="this.onerror=null;this.src='https://placehold.co/150x150/CCCCCC/333333?text=No+Image';">
            <h3 class="product-name">${product.name}</h3>
            <p class="product-price">฿${product.price.toFixed(2)}</p>
            <div class="product-card-actions">
                <button type="button" class="action-button add-to-cart-button" data-id="${product.id}">เพิ่มลงตะกร้า</button>
                <button type="button" class="action-button remove-button" data-id="${product.id}">ลบ</button>
            </div>
        `;

        card.querySelector('.add-to-cart-button').addEventListener('click', () => ShoppingCart.addToCart(product));
        card.querySelector('.remove-button').addEventListener('click', () => this.removeFromLiked(product.id));
        return card;
    },

    // renderProducts(ShoppingCart) {
    //     const container = document.getElementById('likedProductsGrid');
    //     if (!container) return;

    //     container.innerHTML = '';
    //     if (this.likedProducts.length === 0) {
    //         container.parentNode.insertBefore('<p class="no-liked-products-message">คุณยังไม่มีสินค้าที่ถูกใจ</p>', container);
    //     } else {
    //         this.likedProducts.forEach(product => {
    //             container.appendChild(this.createProductCard(product, ShoppingCart));
    //         });
    //     }
    // },

    renderProducts(ShoppingCart) {
        const container = document.getElementById('likedProductsGrid');
        if (!container) return;

        container.innerHTML = '';

        // ลบข้อความเดิม (ถ้ามี)
        const oldMessage = container.parentNode.querySelector('.no-liked-products-message');
        if (oldMessage) oldMessage.remove();

        if (this.likedProducts.length === 0) {
            // สร้าง DOM node แทนการใช้ string
            const message = document.createElement('p');
            message.className = 'no-liked-products-message';
            message.textContent = 'คุณยังไม่มีสินค้าที่ถูกใจ';

            // แทรกก่อน container
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
    },

    // addDummyProducts() {
    //     if (this.likedProducts.length === 0) {
    //         const dummyProducts = [
    //             {
    //                 id: 'PROD001',
    //                 name: 'Trandar Mineral Fiber AMF',
    //                 price: 350.00,
    //                 imageUrl: 'https://www.trandar.com//public/shop_img/6883336b6606d_______________________AMF-_________.jpg'
    //             },
    //             {
    //                 id: 'PROD002',
    //                 name: 'Trandar AMF Mercure',
    //                 price: 890.00,
    //                 imageUrl: 'https://www.trandar.com//public/shop_img/687a1a94a6f10_Trandar_AMF_Mercure.jpg'
    //             },
    //             {
    //                 id: 'PROD003',
    //                 name: 'Trandar AMF Fine Fresko',
    //                 price: 1290.00,
    //                 imageUrl: 'https://www.trandar.com//public/shop_img/687a1aa984ae2_Trandar_AMF_Fine_Fresko.jpg'
    //             },
    //             {
    //                 id: 'PROD004',
    //                 name: 'Trandar AMF Star',
    //                 price: 1990.00,
    //                 imageUrl: 'https://www.trandar.com//public/shop_img/687a1a756ce6a_Trandar_AMF_Star.jpg'
    //             },
    //             {
    //                 id: 'PROD005',
    //                 name: 'Trandar AMF  Fine Stratos micro perforated',
    //                 price: 750.00,
    //                 imageUrl: 'https://www.trandar.com//public/shop_img/687a1dddbe703_Trandar_AMF_Fine_Stratos_micro_perforated.jpg'
    //             },
    //             {
    //                 id: 'PROD006',
    //                 name: 'Trandar AMF  Thermofon',
    //                 price: 3500.00,
    //                 imageUrl: 'https://www.trandar.com//public/shop_img/687a1c1b485a9_Trandar_AMF_Thermofon.jpg'
    //             }
    //         ];
    //         this.likedProducts.push(...dummyProducts);
    //         this.saveProducts();
    //     }
    // }
};

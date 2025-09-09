<?php include '../../routes.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>E-STORE</title>
    <?php include '../../inc-meta.php'; ?>
    <link href="../../css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../../css/product/template-product-detail.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../../inc-cdn.php'; ?>
</head>

<body>

    <?php include '../../template/head-bar.php'; ?>
    <main>
        <div id="sections_root_product_detail" class="section-space">
            <div class="container">
                <section>
                    <div class="main-container" id="productContainer"></div>
                </section>
                <section>
                    <div id="productTabsContainer" class="mt-3"></div>
                </section>
            </div>
        </div>
    </main>
    <?php include '../../template/footer-bar.php'; ?>

    <script>
    const ProductApp = {
        product: {
            title: "แทรนดาร์ ซิวาน่า 25 mm.",
            category: "วัสดุดูดซับเสียง",
            reviews: 4,
            currentPrice: 21.6,
            oldPrice: 24,
            discount: "10% Off",
            code: "FBB00255",
            availability: "In Stock",
            type: "Fruits",
            shipping: "01 day shipping, [Free pickup today]",
            images: [
                "https://www.trandar.com//public/shop_img/687dc99925dd3_ZIVANA_25_mm._full.jpg",
                "https://www.trandar.com//public/shop_img/687ddbd4662ab_detail-01.png",
                "https://www.trandar.com//public/shop_img/6883502b859bf_ZIVAN_15_mm._full.jpg",
                "https://www.trandar.com/public/uploads/group_images/1752810403_6879c3a30d6f9.jpg"
            ],
            thumbnails: [
                "https://www.trandar.com//public/shop_img/687dc99925dd3_ZIVANA_25_mm._full.jpg",
                "https://www.trandar.com//public/shop_img/687ddbd4662ab_detail-01.png",
                "https://www.trandar.com//public/shop_img/6883502b859bf_ZIVAN_15_mm._full.jpg",
                "https://www.trandar.com/public/uploads/group_images/1752810403_6879c3a30d6f9.jpg"
            ],
            options: ["250g", "500g", "1kg"],
            details: `Crispy, crunchy and full of flavour! Perfect for snack lovers.`,
            information: `
                <ul>
                    <li>Brand: Haldiram’s</li>
                    <li>Weight: 250g / 500g / 1kg</li>
                    <li>Origin: India</li>
                    <li>Category: Snack & Munchies</li>
                </ul>
            `,
            reviewsList: [
                { user: "Somchai", rating: 5, comment: "อร่อยมากครับ กรอบสุดๆ" },
                { user: "Ananya", rating: 4, comment: "เผ็ดกำลังดี กินเพลิน" },
                { user: "Preecha", rating: 3, comment: "เค็มไปนิด แต่รวมๆ โอเค" }
            ]
        },

        render() {
            const p = this.product;
            const container = document.getElementById("productContainer");

            // Render main image and thumbnails
            container.innerHTML = `
            <div class="product-image-section">
                <div class="main-image-container">
                    <a href="${p.images[0]}" data-fancybox="gallery" data-caption="${p.title}">
                        <img src="${p.images[0]}" alt="${p.title}" class="main-image">
                    </a>
                </div>
                <div class="thumbnails-container">
                    ${p.thumbnails.map((thumb, idx) => `
                        <div class="thumbnail ${idx === 0 ? "active" : ""}">
                            <img src="${thumb}" alt="Thumbnail ${idx + 1}" data-index="${idx}">
                        </div>
                    `).join('')}
                </div>
            </div>

            <div class="product-details-section">
                <span class="category-text">${p.category}</span>
                <h1 class="product-title">${p.title}</h1>
                <div class="reviews-container">
                    <span>★★★★★</span>
                    <span class="reviews-text">(${p.reviews} reviews)</span>
                </div>
                <div class="price-container">
                    <span class="current-price">$${p.currentPrice}</span>
                    <span class="old-price">$${p.oldPrice}</span>
                    <span class="discount-text">${p.discount}</span>
                </div>
                <hr>
                <div class="size-options">
                    ${p.options.map(size => `<button class="size-button">${size}</button>`).join('')}
                </div>
                <div class="actions-container">
                    <div class="quantity-input">
                        <button class="quantity-button">-</button>
                        <span class="quantity-display">1</span>
                        <button class="quantity-button">+</button>
                    </div>
                    <button class="add-to-cart-button"><span>Add to cart</span></button>
                    <button class="wishlist-button"><i class="bi bi-heart"></i></button>
                    <button class="share-button"><i class="bi bi-share"></i></button>
                </div>
                <hr>
                <div class="product-info">
                    <div class="info-row"><span class="info-label">Product Code:</span><span>${p.code}</span></div>
                    <div class="info-row"><span class="info-label">Availability:</span><span class="info-value in-stock">${p.availability}</span></div>
                    <div class="info-row"><span class="info-label">Type:</span><span>${p.type}</span></div>
                    <div class="info-row"><span class="info-label">Shipping:</span><span>${p.shipping}</span></div>
                </div>
            </div>
            `;
        },

        renderTabs() {
            const tabContainer = document.getElementById("productTabsContainer");

            tabContainer.innerHTML = `
                <div class="product-tabs">
                    <button class="product-tab-button active" data-tab="details">Product Details</button>
                    <button class="product-tab-button" data-tab="information">Information</button>
                    <button class="product-tab-button" data-tab="reviews">Reviews</button>
                </div>
                <div class="product-tab-content" id="productTabContent">
                    ${this.getTabContent("details")}
                </div>
            `;

            const buttons = tabContainer.querySelectorAll(".product-tab-button");
            buttons.forEach(btn => {
                btn.addEventListener("click", () => {
                    buttons.forEach(b => b.classList.remove("active"));
                    btn.classList.add("active");
                    document.getElementById("productTabContent").innerHTML = this.getTabContent(btn.dataset.tab);
                });
            });
        },

        getTabContent(tab) {
            const p = this.product;
            if(tab === "details") return `<p>${p.details}</p>`;
            if(tab === "information") return `${p.information}`;
            if(tab === "reviews") {
                return `
                    <div class="reviews-list">
                        ${p.reviewsList.map(r => `
                            <div class="review-item">
                                <strong>${r.user}</strong> - ⭐${r.rating}<br>
                                <span>${r.comment}</span>
                            </div>
                        `).join('')}
                    </div>
                `;
            }
            return "";
        },

        bindThumbnailEvents() {
            const mainImage = document.querySelector(".product-image-section .main-image");
            const mainLink = document.querySelector(".product-image-section .main-image-container a");
            const thumbnails = document.querySelectorAll(".product-image-section .thumbnail img");

            thumbnails.forEach((thumb, idx) => {
                // thumb.addEventListener("mouseenter", () => {
                thumb.addEventListener("click", () => {
                    // Update main image
                    mainImage.src = this.product.images[idx];
                    mainLink.href = this.product.images[idx];
                    mainLink.setAttribute("data-caption", this.product.title);

                    thumbnails.forEach(t => t.parentElement.classList.remove("active"));
                    thumb.parentElement.classList.add("active");
                });
            });
        },

        bindFancyboxGallery() {
            const galleryItems = this.product.images.map((img, idx) => {
                return { src: img, type: "image", caption: this.product.title };
            });

            const mainLink = document.querySelector(".product-image-section .main-image-container a");

            mainLink.addEventListener("click", (e) => {
                e.preventDefault();
                Fancybox.show(galleryItems, {
                    infinite: true,
                    dragToClose: false,
                    click: false,
                    keyboard: { Escape: false },
                    closeButton: true
                });
            });
        },

        init() {
            this.render();
            this.bindThumbnailEvents();
            this.renderTabs();
            this.bindFancyboxGallery(); // initialize gallery
        }
    };

    ProductApp.init();
    </script>



</body>

</html>
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
                    "https://www.trandar.com/public/uploads/group_images/1752810403_6879c3a30d6f9.jpg",
                    "https://placehold.co/800x600/F6F5F3/000?text=Extra+1",
                    "https://placehold.co/800x600/F6F5F3/000?text=Extra+2"
                ],
                thumbnails: [
                    "https://www.trandar.com//public/shop_img/687dc99925dd3_ZIVANA_25_mm._full.jpg",
                    "https://www.trandar.com//public/shop_img/687ddbd4662ab_detail-01.png",
                    "https://www.trandar.com//public/shop_img/6883502b859bf_ZIVAN_15_mm._full.jpg",
                    "https://www.trandar.com/public/uploads/group_images/1752810403_6879c3a30d6f9.jpg",
                    "https://placehold.co/800x600/F6F5F3/000?text=Extra+1",
                    "https://placehold.co/800x600/F6F5F3/000?text=Extra+2"
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
                reviewsList: [{
                        user: "Somchai",
                        rating: 5,
                        comment: "อร่อยมากครับ กรอบสุดๆ"
                    },
                    {
                        user: "Ananya",
                        rating: 4,
                        comment: "เผ็ดกำลังดี กินเพลิน"
                    },
                    {
                        user: "Preecha",
                        rating: 3,
                        comment: "เค็มไปนิด แต่รวมๆ โอเค"
                    }
                ]
            },

            init() {
                this.render();
                this.renderTabs();
            },

            render() {
                const p = this.product;
                p.thumbnails = [...p.images]; // ทำให้ thumbnails = images
                const container = document.getElementById("productContainer");

                container.innerHTML = `
        <div class="product-image-section">
            <div class="main-image-container">
                <a href="${p.images[0]}" data-fancybox="gallery" data-caption="${p.title}">
                    <img src="${p.images[0]}" alt="${p.title}" class="main-image" id="main-product-image">
                </a>
            </div>
            <div class="thumbnails-container">
                <button class="thumbnail-nav" id="prev-thumbnail-btn">&#10094;</button>
                <div class="thumbnails-wrapper" id="thumbnails-wrapper">
                    ${p.thumbnails.map((thumb, idx) => `
                        <div class="thumbnail ${idx === 0 ? "active" : ""}" data-src="${thumb}">
                            <img src="${thumb}" alt="Thumbnail ${idx + 1}">
                        </div>
                    `).join('')}
                </div>
                <button class="thumbnail-nav" id="next-thumbnail-btn">&#10095;</button>
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
            <div class="quantity-container">
                <div class="quantity-input">
                    <button class="quantity-button">-</button>
                    <span class="quantity-display">1</span>
                    <button class="quantity-button">+</button>
                </div>
            </div>
            <div class="actions-container">
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

                this.bindThumbnailEvents();
                this.bindThumbnailNavigation();
                this.bindFancyboxGallery();
            },

            bindThumbnailEvents() {
                const mainImage = document.getElementById("main-product-image");
                const thumbnails = document.querySelectorAll(".thumbnail");

                function scrollThumbnailIntoView(thumbnail) {
                    const wrapper = document.getElementById("thumbnails-wrapper");
                    const wrapperRect = wrapper.getBoundingClientRect();
                    const thumbRect = thumbnail.getBoundingClientRect();
                    const offset = thumbRect.left - wrapperRect.left - (wrapperRect.width / 2) + (thumbRect.width / 2);
                    wrapper.scrollBy({
                        left: offset,
                        behavior: 'smooth'
                    });
                }

                thumbnails.forEach(thumbnail => {
                    // thumbnail.addEventListener('click', (event) => {
                    thumbnail.addEventListener('mouseenter', (event) => {
                        const newSrc = thumbnail.dataset.src;
                        mainImage.src = newSrc;

                        thumbnails.forEach(t => t.classList.remove('active'));
                        thumbnail.classList.add('active');

                        scrollThumbnailIntoView(thumbnail);
                    });
                });
            },

            bindThumbnailNavigation() {
                const wrapper = document.getElementById("thumbnails-wrapper");
                const prevBtn = document.getElementById("prev-thumbnail-btn");
                const nextBtn = document.getElementById("next-thumbnail-btn");
                const thumbnails = wrapper.querySelectorAll(".thumbnail");
                const mainImage = document.getElementById("main-product-image");

                const thumbWidth = thumbnails[0].offsetWidth + 10; // รวม gap
                let scrollIndex = 0; // ตำแหน่ง scroll

                function getActiveIndex() {
                    return Array.from(thumbnails).findIndex(t => t.classList.contains('active'));
                }

                function setActiveIndex(idx) {
                    const newIndex = Math.max(0, Math.min(thumbnails.length - 1, idx));
                    thumbnails.forEach(t => t.classList.remove('active'));
                    thumbnails[newIndex].classList.add('active');
                    mainImage.src = thumbnails[newIndex].dataset.src;

                    // scroll wrapper ให้ active thumbnail อยู่ตรงกลาง
                    const wrapperVisibleCount = Math.floor(wrapper.offsetWidth / thumbWidth);
                    if (newIndex < scrollIndex) scrollIndex = newIndex;
                    if (newIndex >= scrollIndex + wrapperVisibleCount) scrollIndex = newIndex - wrapperVisibleCount + 1;
                    wrapper.scrollTo({
                        left: scrollIndex * thumbWidth,
                        behavior: 'smooth'
                    });
                }

                prevBtn.addEventListener('click', () => {
                    setActiveIndex(getActiveIndex() - 1);
                });
                nextBtn.addEventListener('click', () => {
                    setActiveIndex(getActiveIndex() + 1);
                });
            },

            bindFancyboxGallery() {
                const galleryItems = this.product.images.map(img => ({
                    src: img,
                    type: "image",
                    caption: this.product.title
                }));

                const mainLink = document.querySelector(".main-image-container a");

                mainLink.addEventListener("click", (e) => {
                    e.preventDefault();
                    if (typeof Fancybox !== "undefined") {
                        Fancybox.show(galleryItems, {
                            infinite: true,
                            dragToClose: false,
                            click: false,
                            keyboard: {
                                Escape: false
                            },
                            closeButton: true
                        });
                    }
                });
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
                if (tab === "details") return `<p>${p.details}</p>`;
                if (tab === "information") return p.information;
                if (tab === "reviews") {
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
            }

        };

        document.addEventListener("DOMContentLoaded", () => {
            ProductApp.init();
        });
    </script>




</body>

</html>
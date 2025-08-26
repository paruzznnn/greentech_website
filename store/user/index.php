<?php include '../routes.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-STORE</title>
    <?php include '../inc-meta.php'; ?>
    <link href="../css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../css/template-user.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../inc-cdn.php'; ?>
</head>

<body>

    <?php include '../template/head-bar.php'; ?>
    <main>
        <div id="sections_root_profile">
            <section id="sections_cover_photo" class="section-space">
                <div class="container">
                    <div class="cover-photo">
                        <div class="profile-avatar">
                            <img src="https://dev.origami.life/uploads/employee/20140715173028man20key.png" alt="Profile Picture" />
                        </div>
                    </div>
                </div>
            </section>

            <section id="sections_profile_layout" class="section-space-profile">
                <div class="container">
                    <div class="profile-layout">
                        <div class="sidebar">
                            <ul id="profileMenu">
                                <li data-tab="info">
                                    <span><i class="bi bi-person-gear"></i> บัญชีของฉัน</span>
                                </li>
                                <!-- <li data-tab="payment">
                                    <span><i class="bi bi-cash-coin"></i>ชำระเงิน</span>
                                </li> -->
                                <li class="active" data-tab="orders">
                                    <span><i class="bi bi-tag"></i> รายการสั่งซื้อ</span>
                                </li>
                                <li data-tab="addresses">
                                    <span><i class="bi bi-geo-alt"></i> ที่อยู่จัดส่ง</span>
                                </li>
                                <li data-tab="wishlist">
                                    <span><i class="bi bi-heart"></i> สินค้าที่ถูกใจ</span>
                                </li>
                                <li data-tab="cart">
                                    <span><i class="bi bi-cart3"></i> สินค้าในตะกร้า</span>
                                </li>

                                <!-- <li data-tab="coupon">
                                    <span><i class="bi bi-lightning-charge"></i> ส่วนลด</span>
                                </li>
                                <li data-tab="reviews">
                                    <span><i class="bi bi-chat-right-text"></i> รีวิวของฉัน</span>
                                </li> -->
                                
                                <li data-tab="logout">
                                    <span><i class="bi bi-door-open"></i> ออกจากระบบ</span>
                                </li>
                            </ul>
                        </div>
                        <div class="content">
                            <div class="tabContent active" id="orders">
                                <div class="main-container">
                                    <div id="tab-order-list" class="tab-bar"></div>
                                    <div id="orders-list" class="orders-list-container"></div>
                                </div>
                            </div>
                            <!-- <div class="tabContent" id="payment">
                            </div> -->
                            <div class="tabContent" id="addresses">
                                <div class="main-container">
                                    <form id="shippingAddressForm" class="mb-4" data-url="<?php echo $BASE_WEB ?>service/user/user-action.php" data-redir="<?php echo $BASE_WEB ?>user/" data-type="address">
                                        <input type="text" name="action" value="addAddress" hidden>
                                        <div id="addressesContainer"></div>
                                        <button type="button" id="addAddressCardBtn" class="add-address w-100 mt-4 d-flex align-items-center justify-content-center gap-2">
                                            <i class="fas fa-plus"></i> เพิ่มที่อยู่ใหม่
                                        </button>
                                        <button id="confirmAddress" type="submit" class="saved-address w-100 mt-4">บันทึกการตั้งค่า</button>
                                    </form>
                                </div>
                            </div>
                            <div class="tabContent" id="wishlist">
                                <div class="main-container">
                                    <div id="likedProductsGrid" class="products-grid"></div>
                                </div>
                            </div>
                            <div class="tabContent" id="cart">
                                <div class="main-container">
                                    <div id="cartContent" class="cart-content">
                                    </div>
                                </div>
                            </div>

                            <!-- <div class="tabContent" id="coupon">
                                <div class="main-container">
                                    <div class="mb-5">
                                        <h2 class="section-heading">คูปองที่ฉันมี</h2>
                                        <div id="myCouponsContainer" class="coupon-grid"></div>
                                    </div>
                                    <div>
                                        <h2 class="section-heading">คูปองที่สามารถเก็บได้</h2>
                                        <div id="availableCouponsContainer" class="coupon-grid"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="tabContent" id="reviews">
                                <div class="main-container">
                                    <div id="review-cards-container" class="review-cards-container"></div>
                                </div>
                            </div> -->

                            <div class="tabContent" id="info">
                                <div class="profile-card">
                                    <div class="profile-header-section">
                                        <div class="profile-picture-container">
                                            <img id="profileImage" src="" alt="" class="profile-picture">
                                            <input type="file" id="imageUpload" accept="image/*" style="display: none;">
                                            <div class="edit-icon" id="editImageIcon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                                                    <path d="M15 5l4 4" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="user-info-text">
                                            <h2 class="user-name">ชื่อผู้ใช้: </h2>
                                            <p class="user-detail-text">อีเมล: </p>
                                            <p class="user-detail-text">สถานะ: สมาชิก</p>
                                        </div>
                                    </div>

                                    <form class="profile-form">
                                        <div>
                                            <label for="firstName" class="form-label">ชื่อจริง</label>
                                            <input type="text" id="firstName" name="firstName" value="" class="form-input" placeholder="ชื่อจริง">
                                        </div>
                                        <div>
                                            <label for="lastName" class="form-label">นามสกุล</label>
                                            <input type="text" id="lastName" name="lastName" value="" class="form-input" placeholder="นามสกุล">
                                        </div>
                                        <div>
                                            <label for="email" class="form-label">อีเมล</label>
                                            <input type="email" id="email" name="email" value="" class="form-input" placeholder="อีเมล" readonly>
                                        </div>
                                        <div>
                                            <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                                            <input type="tel" id="phone" name="phone" value="" class="form-input" placeholder="เบอร์โทรศัพท์">
                                        </div>
                                        <div class="full-width-field">
                                            <label for="address" class="form-label">ที่อยู่</label>
                                            <textarea id="address" name="address" rows="3" class="form-input" placeholder="ที่อยู่ปัจจุบัน"></textarea>
                                        </div>
                                        <div>
                                            <label for="birthdate" class="form-label">วันเกิด</label>
                                            <input type="date" id="birthdate" name="birthdate" value="" class="form-input">
                                        </div>
                                        <div>
                                            <label for="gender" class="form-label">เพศ</label>
                                            <select id="gender" name="gender" class="form-input">
                                                <option value="male">ชาย</option>
                                                <option value="female">หญิง</option>
                                                <option value="other">อื่นๆ</option>
                                            </select>
                                        </div>
                                        <div class="save-button-container">
                                            <button type="submit" class="save-button">บันทึกการเปลี่ยนแปลง</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="tabContent" id="logout">
                                <h2>Logout</h2>
                                <p>คุณต้องการออกจากระบบใช่หรือไม่?</p>
                                <a class="btn-user-logout" href="../logout.php">
                                    ยืนยันการออกจากระบบ
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </main>
    <?php include '../template/footer-bar.php'; ?>

    <script type="module">
        const timeVersion = "<?= time() ?>";
        const baseWeb = `${pathConfig.BASE_WEB}`;

        Promise.all([
            import(`${baseWeb}js/formHandler.js?v=${timeVersion}`),
            import(`${baseWeb}js/user/userRender.js?v=${timeVersion}`),
            import(`${baseWeb}js/user/addressRender.js?v=${timeVersion}`),
            import(`${baseWeb}js/user/menuBuilder.js?v=${timeVersion}`),
            import(`${baseWeb}js/user/orderListRender.js?v=${timeVersion}`),
            import(`${baseWeb}js/user/wishlistRender.js?v=${timeVersion}`),
            import(`${baseWeb}js/user/cartRender.js?v=${timeVersion}`)
        ])
        .then(async (
            [
                formModule,
                profileModule,
                addressModule,
                menuBuilderModule, 
                orderListModule, 
                wishlistModule, 
                cartModule
            ]
        ) => {
            const { handleFormSubmit } = formModule;
            const { setupProfileImageUpload } = profileModule;
            const { 
                AddressUI, 
                fetchAddressData, 
                fetchProvincesData, 
                fetchDistrictsData, 
                fetchSubdistricts
            } = addressModule;
            const { setupTabs } = menuBuilderModule;
            const { fetchOrders, OrderListUI } = orderListModule;
            const { LikedProducts } = wishlistModule;
            const { ShoppingCart } = cartModule;

            //===== Profile ==================================
            setupProfileImageUpload();

            //===== Address ==================================
            const address = await fetchAddressData("getAddressItems", baseWeb + 'service/user/user-data.php?');
            const provinces = await fetchProvincesData(baseWeb + 'locales/provinces.json');
            const districts = await fetchDistrictsData(baseWeb + 'locales/districts.json');
            const subdistricts = await fetchSubdistricts(baseWeb + 'locales/subdistricts.json');
            AddressUI.init(
                provinces,
                districts,
                subdistricts,
                address
            );

            const formAddress = document.querySelector("#shippingAddressForm");
            formAddress?.addEventListener("submit", handleFormSubmit);
            
            //===== Order List ==================================
            setupTabs();
            const service = baseWeb + 'service/user/user-data.php?';
            const orders = await fetchOrders("getOrdersItems", service);
            OrderListUI.displayTabOrders('tab-order-list');

            const tabButtonsHandler = () => {
                const tabButtons = document.querySelectorAll('.tab-button');
                tabButtons.forEach(button => {
                    button.addEventListener('click', (event) => {
                        tabButtons.forEach(btn => btn.classList.remove('active'));
                        event.currentTarget.classList.add('active');
                        const selectedStatus = event.currentTarget.dataset.status;
                        OrderListUI.displayOrders(selectedStatus, 'orders-list', orders);
                    });
                });
            };
            tabButtonsHandler();
            OrderListUI.displayOrders('All', 'orders-list', orders);

            //===== Cart ==================================
            window.ShoppingCart = ShoppingCart;
            ShoppingCart.init(baseWeb);

            //===== Like ==================================
            LikedProducts.init();
            LikedProducts.renderProducts(ShoppingCart);

        })
        .catch((e) => console.error("Module import failed", e));
    </script>


    <!-- <script>
        const initialMyCouponsData = [{
                id: 'user_cpn_001',
                name: 'ส่วนลด 15%',
                description: 'ลดสูงสุด ฿100 สำหรับทุกหมวดหมู่',
                minSpend: 300,
                expiry: '31 ธ.ค. 2567',
                type: 'green',
                status: 'active',
                icon: 'percent'
            },
            {
                id: 'user_cpn_002',
                name: 'ส่งฟรี',
                description: 'สำหรับคำสั่งซื้อขั้นต่ำ ฿99',
                minSpend: 99,
                expiry: '15 พ.ย. 2567',
                type: 'gray',
                status: 'expired',
                icon: 'truck'
            },
            {
                id: 'user_cpn_003',
                name: 'คูปองของขวัญ ฿50',
                description: 'สำหรับสินค้าทุกชนิด',
                minSpend: 0,
                expiry: '20 ธ.ค. 2567',
                type: 'purple',
                status: 'used',
                icon: 'gift'
            }
        ];

        const initialAvailableCouponsData = [{
                id: 'claim_cpn_001',
                name: 'ส่วนลด ฿200',
                description: 'สำหรับสินค้าหมวดหมู่แฟชั่น',
                minSpend: 1000,
                expiry: '31 ม.ค. 2568',
                type: 'yellow',
                status: 'claimable',
                icon: 'tags'
            },
            {
                id: 'claim_cpn_002',
                name: 'แฟลชเซลล์ 50%',
                description: 'ลดสูงสุด ฿500 สำหรับสินค้าที่ร่วมรายการ',
                minSpend: 200,
                expiry: '25 ธ.ค. 2567',
                type: 'red',
                status: 'claimable',
                icon: 'fire'
            },
            {
                id: 'claim_cpn_003',
                name: 'คูปองพิเศษ ฿100',
                description: 'สำหรับลูกค้าใหม่เท่านั้น',
                minSpend: 500,
                expiry: '31 ม.ค. 2568',
                type: 'blue',
                status: 'claimable',
                icon: 'star'
            }
        ];

        let myCouponsData = [];
        let availableCouponsData = [];

        const myCouponsContainer = document.getElementById('myCouponsContainer');
        const availableCouponsContainer = document.getElementById('availableCouponsContainer');

        function saveCouponsToLocalStorage() {
            localStorage.setItem('myCoupons', JSON.stringify(myCouponsData));
            localStorage.setItem('availableCoupons', JSON.stringify(availableCouponsData));
        }

        function loadCouponsFromLocalStorage() {
            const storedMyCoupons = localStorage.getItem('myCoupons');
            const storedAvailableCoupons = localStorage.getItem('availableCoupons');

            if (storedMyCoupons) {
                myCouponsData = JSON.parse(storedMyCoupons);
            } else {
                myCouponsData = initialMyCouponsData;
            }

            if (storedAvailableCoupons) {
                availableCouponsData = JSON.parse(storedAvailableCoupons);
            } else {
                availableCouponsData = initialAvailableCouponsData;
            }
            saveCouponsToLocalStorage();
        }

        function renderCoupons(container, coupons, isClaimableSection = false) {
            container.innerHTML = '';
            if (coupons.length === 0) {
                container.innerHTML = `<p class="text-center text-gray-500 w-full p-4 text-lg">ไม่มีคูปองในส่วนนี้</p>`;
                return;
            }

            coupons.forEach(coupon => {
                let buttonText = '';
                let buttonClasses = 'btn-custom';
                let cardBgClass = '';
                let expiryText = '';

                if (isClaimableSection) {
                    buttonText = 'เก็บเลย';
                    buttonClasses += ' claim-coupon-btn';
                } else {
                    switch (coupon.status) {
                        case 'active':
                            buttonText = 'ใช้ตอนนี้';
                            expiryText = `หมดอายุ ${coupon.expiry}`;
                            break;
                        case 'used':
                            buttonText = 'ใช้แล้ว';
                            buttonClasses += ' disabled';
                            expiryText = `ใช้ไปแล้วเมื่อ ${coupon.expiry}`;
                            break;
                        case 'expired':
                            buttonText = 'หมดอายุแล้ว';
                            buttonClasses += ' disabled';
                            expiryText = `หมดอายุ ${coupon.expiry}`;
                            break;
                    }
                }

                switch (coupon.type) {
                    case 'green':
                        cardBgClass = 'coupon-green';
                        break;
                    case 'yellow':
                        cardBgClass = 'coupon-yellow';
                        break;
                    case 'red':
                        cardBgClass = 'coupon-red';
                        break;
                    case 'blue':
                        cardBgClass = 'coupon-blue';
                        break;
                    case 'purple':
                        cardBgClass = 'coupon-purple';
                        break;
                    case 'gray':
                        cardBgClass = 'coupon-gray';
                        break;
                    default:
                        cardBgClass = 'bg-gray-200 text-gray-800';
                }

                const couponCardHtml = `
                    <div class="coupon-card-wrapper">
                        <div class="coupon-card ${cardBgClass}" data-coupon-id="${coupon.id}">
                            <div>
                                <div class="icon-and-title">
                                    <i class="fas fa-${coupon.icon}"></i>
                                    <h3 class="title">${coupon.name}</h3>
                                </div>
                                <p class="description">${coupon.description}</p>
                                <p class="details">
                                    ${coupon.minSpend > 0 ? `ขั้นต่ำ ฿${coupon.minSpend.toLocaleString()} | ` : ''}${expiryText}
                                </p>
                            </div>
                            <button class="${buttonClasses}">
                                ${buttonText}
                            </button>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', couponCardHtml);
            });

            if (isClaimableSection) {
                document.querySelectorAll('.claim-coupon-btn').forEach(button => {
                    button.addEventListener('click', (event) => {
                        const cardElement = event.target.closest('.coupon-card');
                        const couponId = cardElement.dataset.couponId;

                        const claimedCouponIndex = availableCouponsData.findIndex(c => c.id === couponId);
                        if (claimedCouponIndex > -1) {
                            const claimedCoupon = availableCouponsData[claimedCouponIndex];

                            claimedCoupon.status = 'active';
                            myCouponsData.push(claimedCoupon);
                            availableCouponsData.splice(claimedCouponIndex, 1);

                            saveCouponsToLocalStorage();

                            renderCoupons(myCouponsContainer, myCouponsData);
                            renderCoupons(availableCouponsContainer, availableCouponsData, true);
                        }
                    });
                });
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadCouponsFromLocalStorage();
            renderCoupons(myCouponsContainer, myCouponsData);
            renderCoupons(availableCouponsContainer, availableCouponsData, true);
        });
    </script>

    <script>
        const reviews = [{
                productName: "Trandar AMF Mercure แทรนดาร์ เอเอ็มเอฟ เมอร์เคียว",
                rating: 5,
                reviewText: "สินค้าดี แข็งแรง พนักงานบริการดี",
                reviewDate: "15 กรกฎาคม 2567",
                imageUrl: "https://www.trandar.com//public/shop_img/687a1a94a6f10_Trandar_AMF_Mercure.jpg"
            },
            {
                productName: "Trandar Focus F แทรนดาร์ โฟกัส เอฟ",
                rating: 4,
                reviewText: "สินค้าดี แข็งแรง พนักงานบริการดี",
                reviewDate: "10 กรกฎาคม 2567",
                imageUrl: "https://www.trandar.com//public/shop_img/687a21f2467de_Ecophon_Focus_F.jpg"
            },
            {
                productName: "Trandar T-Bar Grooveline แทรนดาร์ ที บาร์ กรูฟไลน์ (T24)",
                rating: 3,
                reviewText: "สินค้าดี แข็งแรง พนักงานบริการดี",
                reviewDate: "01 กรกฎาคม 2567",
                imageUrl: "https://www.trandar.com//public/shop_img/687dc5bd01057_Grooveline-main3.png"
            },
            {
                productName: "Trandar Prime Coat แทรนดาร์ ไพรม์โคท",
                rating: 5,
                reviewText: "สีน้ำรองพื้นปูนใหม่คุณภาพ สำหรับทาภายใน Non VOC",
                reviewDate: "28 มิถุนายน 2567",
                imageUrl: "https://www.trandar.com//public/shop_img/687e1c0470c40_trandar_prime_coat_new_label.jpg"
            },
            {
                productName: "Trandar  dBphon S50 ฉนวนแทรนดาร์ ดีบีโฟน S50",
                rating: 4,
                reviewText: "กันไฟ กันเสียง กันความร้อน",
                reviewDate: "20 มิถุนายน 2567",
                imageUrl: "https://www.trandar.com//public/shop_img/687dcff11c5df_dbPhon2.png"
            }
        ];

        function createStarRating(rating) {
            let starsHtml = '';
            for (let i = 0; i < 5; i++) {
                if (i < rating) {
                    starsHtml += '<span class="star">&#9733;</span>';
                } else {
                    starsHtml += '<span class="empty-star">&#9733;</span>';
                }
            }
            return starsHtml;
        }

        function renderReviews() {
            const container = document.getElementById('review-cards-container');
            container.innerHTML = '';

            reviews.forEach(review => {
                const reviewCard = document.createElement('div');
                reviewCard.className = 'review-card';

                reviewCard.innerHTML = `
                    <div class="review-card-image-wrapper">
                        <img src="${review.imageUrl}" alt="${review.productName}">
                    </div>
                    <div class="review-card-content">
                        <div class="review-card-header">
                            <h2>${review.productName}</h2>
                            <div class="star-rating">
                                ${createStarRating(review.rating)}
                            </div>
                        </div>
                        <p class="review-text">
                            "${review.reviewText}"
                        </p>
                        <div class="review-footer">
                            <span>รีวิวเมื่อ: ${review.reviewDate}</span>
                            <div class="review-actions">
                                <button class="edit-button">แก้ไข</button>
                                <button class="delete-button">ลบ</button>
                            </div>
                        </div>
                    </div>
                `;
                container.appendChild(reviewCard);
            });
        }
        window.onload = renderReviews;
    </script> -->

</body>

</html>
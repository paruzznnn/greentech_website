
const couponApp = {
    selectedCode: [],
    currentTab: 'electronics',
    coupon: {
        title: 'คูปองส่วนลดพิเศษ',
        buttonText: 'กดรับทั้งหมด',
        buttonClass: 'btn-get-coupon',
        coupons: [{
            code: "SAVE10",
            type: "percent",
            value: 10,
            label: "ลด 10% เมื่อซื้อครบ 1,000 บาท",
            desc: 'ใช้ได้กับสินค้าทุกชิ้น ยกเว้นสินค้าโปรโมชั่น',
            expiry: 'หมดอายุ 30/09/2025 23:59',
            available: '5,000 สิทธิ์',
            logo: 'http://localhost:3000/trandar_website/store/trandar_logo.png'
        }, {
            code: "FREESHIP",
            type: "shipping",
            value: 0,
            label: "ส่งฟรีทุกออเดอร์",
            desc: 'เมื่อมียอดสั่งซื้อขั้นต่ำ 499 บาท',
            expiry: 'หมดอายุ 15/10/2025 23:59',
            available: '2,500 สิทธิ์',
            logo: 'http://localhost:3000/trandar_website/store/trandar_logo.png'
        }, {
            code: "DIS200",
            type: "cash",
            value: 200,
            label: "ส่วนลด 200 บาท",
            desc: 'เมื่อซื้อครบ 2,000 บาท',
            expiry: 'หมดอายุ 05/10/2025 23:59',
            available: '1,000 สิทธิ์',
            logo: 'http://localhost:3000/trandar_website/store/trandar_logo.png'
        }]
    },

    couponpro: {
        title: 'คูปองส่วนลดออนไลน์',
        buttonText: 'เก็บทั้งหมด',
        buttonClass: 'btn-get-coupon',
        coupons: [{
            code: "ONLINE15",
            type: "percent",
            value: 15,
            label: "ลด 15% สำหรับสั่งออนไลน์",
            desc: 'ใช้ได้กับทุกหมวดสินค้าในเว็บไซต์',
            expiry: 'หมดอายุ 25/09/2025 23:59',
            available: '3,000 สิทธิ์',
            logo: 'http://localhost:3000/trandar_website/store/trandar_logo.png'
        }, {
            code: "NEWUSER50",
            type: "cash",
            value: 50,
            label: "ส่วนลด 50 บาท สำหรับลูกค้าใหม่",
            desc: 'สำหรับการสั่งซื้อครั้งแรกเท่านั้น',
            expiry: 'หมดอายุ 31/12/2025 23:59',
            available: 'ไม่จำกัดสิทธิ์',
            logo: 'http://localhost:3000/trandar_website/store/trandar_logo.png'
        }]
    },

    store: {
        title: 'คูปองส่วนลดร้านค้า',
        buttonClass: 'btn-get-coupon',
        tabs: {
            electronics: {
                text: 'วัสดุดูดซับเสียง',
                coupons: [{
                    code: "ELEC100",
                    type: "cash",
                    value: 100,
                    label: "ลด 100 บาท",
                    desc: 'เมื่อซื้ออุปกรณ์ดูดซับเสียงครบ 1,500 บาท',
                    expiry: 'หมดอายุ 10/10/2025 23:59',
                    available: '500 สิทธิ์',
                    logo: 'http://localhost:3000/trandar_website/store/trandar_logo.png'
                }]
            },
            film: {
                text: 'วัสดุกันเสียง',
                coupons: [{
                    code: "FILM20",
                    type: "percent",
                    value: 20,
                    label: "ลด 20% สำหรับวัสดุกันเสียง",
                    desc: 'เฉพาะสินค้าประเภทแผ่นกันเสียง',
                    expiry: 'หมดอายุ 30/09/2025 23:59',
                    available: '1,200 สิทธิ์',
                    logo: 'http://localhost:3000/trandar_website/store/trandar_logo.png'
                }]
            },
            furniture: {
                text: 'โครงคร่าวฝ้าเพดาน',
                coupons: [{
                    code: "FRAME15",
                    type: "percent",
                    value: 15,
                    label: "ลด 15% โครงคร่าวฝ้าเพดาน",
                    desc: 'ใช้ได้กับทุกรุ่น ทุกขนาด',
                    expiry: 'หมดอายุ 20/10/2025 23:59',
                    available: '800 สิทธิ์',
                    logo: 'http://localhost:3000/trandar_website/store/trandar_logo.png'
                }]
            },
            more: {
                text: 'เพิ่มเติม',
                coupons: [{
                    code: "MORE5",
                    type: "percent",
                    value: 5,
                    label: "ลดเพิ่ม 5% ทุกหมวดสินค้า",
                    desc: 'ใช้ได้กับสินค้าที่ร่วมรายการ',
                    expiry: 'หมดอายุ 31/10/2025 23:59',
                    available: 'ไม่จำกัดสิทธิ์',
                    logo: 'http://localhost:3000/trandar_website/store/trandar_logo.png'
                }]
            }
        }
    },

    renderCoupon() {
        const data = this.coupon;
        const container = document.getElementById('coupon-containe');
        container.classList.add('section-coupon');
        if (!data || !container) return;
        let html = `
                    <div class="section-coupon-header">
                        <h2 class="section-coupon-title">${data.title}</h2>
                        <a href="#" class="history-btn">
                            <i class="bi bi-ticket"></i>
                            <span>คูปองที่ใช้ได้</span>
                        </a>
                    </div>
                    <div class="coupon-grid">
                `;
        if (data.coupons) {
            html += data.coupons.map(coupon => this.createCouponCardHtml(coupon)).join('');
        }
        html += `</div>
                    <button class="${data.buttonClass}" style="margin-top: 24px;">${data.buttonText}</button>
                `;
        container.innerHTML = html;

        const collectAllBtn = container.querySelector('.btn-get-coupon');
        if (collectAllBtn) {
            collectAllBtn.addEventListener('click', () => {
                this.collectAllCoupons(this.coupon.coupons);
            });
        }
        this.addCouponListeners(container);
    },

    renderCouponPro() {
        const data = this.couponpro;
        const container = document.getElementById('couponpro-containe');
        container.classList.add('section-coupon');
        if (!data || !container) return;
        let html = `
                    <div class="section-coupon-header">
                        <h2 class="section-coupon-title">${data.title}</h2>
                        <a href="#" class="history-btn">
                            <i class="bi bi-ticket"></i>
                            <span>คูปองที่ใช้ได้</span>
                        </a>
                    </div>
                    <div class="coupon-grid">
                `;
        if (data.coupons) {
            html += data.coupons.map(coupon => this.createCouponCardHtml(coupon)).join('');
        }
        html += `</div>
                <button class="${data.buttonClass}" style="margin-top: 24px; background-color: #f28b20;">${data.buttonText}</button>
                `;
        container.innerHTML = html;

        const collectAllBtn = container.querySelector('.btn-get-coupon');
        if (collectAllBtn) {
            collectAllBtn.addEventListener('click', () => {
                this.collectAllCoupons(this.couponpro.coupons);
            });
        }
        this.addCouponListeners(container);
    },

    renderStore() {
        const data = this.store;
        const container = document.getElementById('store-containe');
        container.classList.add('section-coupon');
        if (!data || !container) return;

        let html = `
                    <div class="section-coupon-header">
                        <h2 class="section-coupon-title">${data.title}</h2>
                        <a href="#" class="history-btn">
                            <i class="bi bi-ticket"></i>
                            <span>คูปองที่ใช้แล้ว</span>
                        </a>
                    </div>
                    <div class="tab-coupon-menu">
                        ${Object.keys(data.tabs).map((key) => {
            const tab = data.tabs[key];
            const activeClass = (key === this.currentTab) ? 'active' : '';
            return `<button class="tab-coupon-button ${activeClass}" data-category="${key}">${tab.text}</button>`;
        }).join('')}
                    </div>
                    <div class="banner-coupon">
                        <img src="https://www.trandar.com//public/img/688b3f108a8f9.jpg" alt="">
                    </div>
                    <div id="store-coupon-content"></div>
                `;
        container.innerHTML = html;
        this.showCategory(this.currentTab);
        const buttons = container.querySelectorAll('.tab-coupon-button');
        buttons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const category = e.target.getAttribute('data-category');
                this.currentTab = category;
                this.showCategory(category);
                buttons.forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
            });
        });
    },

    createCouponCardHtml(coupon) {
        const isCollected = this.selectedCode.some(item => item.code === coupon.code);
        const buttonClass = isCollected ? 'btn-collected' : 'btn-coupon';
        const buttonText = isCollected ? 'เก็บแล้ว' : 'เก็บ';
        return `
                    <div class="coupon-card">
                        <img src="${coupon.logo}" alt="Logo">
                        <div class="coupon-info">
                            <h3 class="coupon-title">${coupon.label}</h3>
                            <p class="coupon-desc">${coupon.desc}</p>
                            <p class="coupon-expiry">
                                <i class="bi bi-clock-history"></i>
                                ${coupon.expiry}
                            </p>
                            <div class="expires-info">
                                <span>${coupon.available}</span>
                            </div>
                        </div>
                        <div>
                            <button class="${buttonClass}" data-code="${coupon.code}" ${isCollected ? 'disabled' : ''}>${buttonText}</button>
                        </div>
                    </div>
                `;
    },

    showCategory(category) {
        const contentDiv = document.getElementById('store-coupon-content');
        const coupons = this.store.tabs[category].coupons || [];
        let html = '<div class="coupon-grid">';
        coupons.forEach(coupon => {
            html += this.createCouponCardHtml(coupon);
        });
        html += '</div>';
        contentDiv.innerHTML = html;
        this.addCouponListeners(contentDiv);
    },

    addCouponListeners(container) {
        const collectButtons = container.querySelectorAll('button[data-code]');
        collectButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                const couponCode = e.target.getAttribute('data-code');
                const coupon = this.findCouponByCode(couponCode);
                if (coupon) {
                    this.collectCoupon(coupon);
                } else {
                    console.error('Coupon not found for code:', couponCode);
                }
            });
        });
    },

    findCouponByCode(code) {
        let found = this.coupon.coupons.find(c => c.code === code);
        if (found) return found;
        found = this.couponpro.coupons.find(c => c.code === code);
        if (found) return found;
        for (const tab in this.store.tabs) {
            found = this.store.tabs[tab].coupons.find(c => c.code === code);
            if (found) return found;
        }
        return null;
    },

    collectCoupon(couponObject) {
        const isCollected = this.selectedCode.some(item => item.code === couponObject.code);
        if (!isCollected) {
            this.selectedCode.push({
                code: couponObject.code,
                type: couponObject.type,
                value: couponObject.value,
                label: couponObject.label
            });
            this.saveToStorage();
            console.log(`Coupon ${couponObject.code} collected!`);
            this.renderCoupon();
            this.renderCouponPro();
            this.showCategory(this.currentTab);
        }
    },

    collectAllCoupons(couponsArray) {
        couponsArray.forEach(coupon => {
            this.collectCoupon(coupon);
        });
    },

    saveToStorage() {
        localStorage.setItem('couponsAppData', JSON.stringify(this.selectedCode));
    },

    loadFromStorage() {
        const storedCoupons = localStorage.getItem('couponsAppData');
        if (storedCoupons) {
            try {
                const parsedData = JSON.parse(storedCoupons);
                if (Array.isArray(parsedData)) {
                    this.selectedCode = parsedData;
                }
            } catch (e) {
                console.error("Failed to parse stored coupons:", e);
            }
        }
    },

    init() {
        this.loadFromStorage();
        this.renderCoupon();
        this.renderCouponPro();
        this.renderStore();
    }
};

document.addEventListener('DOMContentLoaded', () => {
    couponApp.init();
});
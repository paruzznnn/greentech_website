<div id="cookie-modal" class="store-modal">
    <div class="store-modal-content">

        <span id="modal-cookie-close" class="store-close-modal">&times;</span>
        <!-- <img src="/e-store/trandar_logo.png" alt="" class="store-modal-title" /> -->

        <form id="cookie-preferences" class="cookie-content">

            <h3>การตั้งค่าความเป็นส่วนตัว</h3>
            <!-- คุกกี้ที่มีความจำเป็น -->
            <div class="cookie-section">
                <div class="cookie-row">
                    <h3 class="cookie-title">คุกกี้ที่มีความจำเป็น</h3>
                    <label class="toggle-switch">
                        <input type="checkbox" name="necessary" checked disabled />
                        <span class="slider"></span>
                    </label>
                </div>
                <p class="cookie-desc">
                    คุกกี้ประเภทนี้จำเป็นต่อการทำงานของเว็บไซต์ และไม่สามารถปิดการใช้งานได้ในระบบของเรา โดยจะทำหน้าที่ที่สำคัญ
                    <br><a href="">รายละเอียดเพิ่มเติม >></a>
                </p>
            </div>

            <!-- คุกกี้เพื่อเสริมประสบการณ์การใช้งาน -->
            <div class="cookie-section">
                <div class="cookie-row">
                    <h3 class="cookie-title">คุกกี้เพื่อช่วยเสริมประสบการณ์การใช้งาน</h3>
                    <label class="toggle-switch">
                        <input type="checkbox" name="experience" />
                        <span class="slider"></span>
                    </label>
                </div>
                <p class="cookie-desc">
                    คุกกี้ประเภทนี้ช่วยให้เราสามารถมอบประสบการณ์ที่เป็นส่วนตัวมากยิ่งขึ้นบน <strong>Trandar Store</strong> โดยจดจำพฤติกรรมและการตั้งค่าการใช้งานของคุณเพื่ออำนวยความสะดวกในการใช้งานครั้งถัดไป
                    <br><a href="">รายละเอียดเพิ่มเติม >></a>
                </p>
            </div>

            <!-- คุกกี้สำหรับการเพิ่มประสิทธิภาพ -->
            <div class="cookie-section">
                <div class="cookie-row">
                    <h3 class="cookie-title">คุกกี้สำหรับการเพิ่มประสิทธิภาพ</h3>
                    <label class="toggle-switch">
                        <input type="checkbox" name="performance" />
                        <span class="slider"></span>
                    </label>
                </div>
                <p class="cookie-desc">
                    คุกกี้ประเภทนี้ช่วยให้เรารวบรวมข้อมูลเกี่ยวกับการใช้งานเว็บไซต์ของคุณในเชิงสถิติ เพื่อวิเคราะห์และพัฒนาเว็บไซต์ให้ดียิ่งขึ้น โดยไม่มีการระบุตัวตนของผู้ใช้
                    <br><a href="">รายละเอียดเพิ่มเติม >></a>
                </p>
            </div>

            <!-- ปุ่มบันทึก -->
            <div class="cookie-actions">
                <button type="submit" class="cookie-btn">
                    <i class="fas fa-cookie-bite"></i>
                    บันทึกการตั้งค่า
                </button>
            </div>

        </form>

    </div>
</div>

<div class="policy-cookie" role="dialog" aria-label="การตั้งค่าคุกกี้">
    <div>
        <span style="font-size: 14px;">
            Trandar Store ใช้คุกกี้เพื่อพัฒนาประสบการณ์การใช้งานของคุณ
        </span>
    </div>
    <div>
        <button id="acceptCookies">
            <i class="bi bi-check-circle"></i>
            <span>ยอมรับ</span>
        </button>
        <button id="rejectCookies">
            <i class="bi bi-x-circle"></i>
            <span>ปฏิเสธ</span>
        </button>
        <button id="settingsCookies">
            <i class="bi bi-gear"></i>
            <span>ตั้งค่าคุกกี้</span>
        </button>
    </div>
</div>

<script type="module">
    import(`${window.AppConfig.BASE_WEB}js/modalBuilder.js?v=<?= time() ?>`)
        .then(({
            setupCookieModal
        }) => {

            setupCookieModal();

            function getCookie(name) {
                const cookies = document.cookie.split(';');
                for (let cookie of cookies) {
                    const [key, ...rest] = cookie.trim().split('=');
                    if (key === name) {
                        return decodeURIComponent(rest.join('='));
                    }
                }
                return null;
            }

            const consent = getCookie('cookieConsent');
            const settingsBar = document.querySelector('.policy-cookie');
            const modal = document.getElementById('cookie-modal');

            if (!consent) {
                settingsBar.style.display = 'flex';
            } else {
                settingsBar.style.display = 'none';
            }

            document.getElementById('acceptCookies').addEventListener('click', () => {
                const prefs = {
                    necessary: true,
                    experience: false,
                    performance: false
                };
                document.cookie = "cookieConsent=accepted; path=/; max-age=31536000";
                document.cookie = `cookieSettings=${encodeURIComponent(JSON.stringify(prefs))}; path=/; max-age=31536000`;
                settingsBar.style.display = 'none';
            });

            document.getElementById('rejectCookies').addEventListener('click', () => {
                document.cookie = "cookieConsent=rejected; path=/; max-age=31536000";
                settingsBar.style.display = 'none';
            });

            const saved = getCookie('cookieSettings');
            if (saved) {
                try {
                    const prefs = JSON.parse(saved);
                    if ('experience' in prefs)
                        document.querySelector('input[name="experience"]').checked = prefs.experience;
                    if ('performance' in prefs)
                        document.querySelector('input[name="performance"]').checked = prefs.performance;
                    if ('necessary' in prefs)
                        document.querySelector('input[name="necessary"]').checked = prefs.necessary;
                } catch (e) {
                    console.warn('can`t convert cookieSettings:', e);
                }
            }


            document.getElementById('cookie-preferences').addEventListener('submit', (e) => {
                e.preventDefault();
                const prefs = {
                    necessary: true,
                    experience: document.querySelector('input[name="experience"]').checked,
                    performance: document.querySelector('input[name="performance"]').checked
                };

                document.cookie = `cookieConsent=custom; path=/; max-age=31536000`;
                document.cookie = `cookieSettings=${encodeURIComponent(JSON.stringify(prefs))}; path=/; max-age=31536000`;
                modal.style.display = 'none';
                settingsBar.style.display = 'none';
            });

            // Close modal when clicking outside
            modal.addEventListener('click', (e) => {
                if (e.target.id === "cookie-modal") {
                    modal.style.display = 'none';
                }
            });


        })
        .catch((e) => console.error("Module import failed", e));
</script>

<footer class="site-footer">
    <div class="container">
        <p>&copy; 2025 Trandar Store. สงวนลิขสิทธิ์.</p>
        <nav class="footer-nav">
            <a href="#">นโยบายความเป็นส่วนตัว</a>
            <a href="#">ข้อตกลงการใช้งาน</a>
            <a href="#">ติดต่อเรา</a>
            <!-- <a href="#privacy-policy">นโยบายความเป็นส่วนตัว</a>
            <a href="#terms">ข้อตกลงการใช้งาน</a>
            <a href="#contact">ติดต่อเรา</a> -->
        </nav>
        <div class="footer-social">
            <span>ติดตามเรา: </span>
            <a href="#" target="_blank">Facebook</a> |
            <a href="#" target="_blank">Twitter</a> |
            <a href="#" target="_blank">Instagram</a> |
            <a href="#">Email</a>
        </div>
    </div>
</footer>

<script type="module">
    import(`${window.AppConfig.BASE_WEB}js/language.js?v=<?= time() ?>`)
        .then(({
            loadLanguage,
            applyTranslations
        }) => {

            const langButtons = document.getElementById('langButtons');
            const defaultLang = localStorage.getItem('lang') || 'en';
            let service = window.AppConfig.BASE_WEB + 'locales/';

            function updateActiveButton(activeLang) {
                [...langButtons.children].forEach(btn => {
                    btn.classList.toggle('active', btn.dataset.lang === activeLang);
                });
            }

            async function setLang(lang, service) {
                try {
                    const dict = await loadLanguage(lang, service);
                    applyTranslations(dict);
                    localStorage.setItem('lang', lang);
                    document.documentElement.lang = lang;
                    updateActiveButton(lang);
                } catch (err) {
                    console.error('Language loading error:', err);
                    alert(`Error loading language: ${err.message}`);
                }
            }

            setLang(defaultLang, service);

            langButtons.addEventListener('click', e => {
                if (e.target.tagName === 'BUTTON') {
                    const lang = e.target.dataset.lang;
                    if (lang) {
                        setLang(lang, service);
                    }
                }
            });

        })
        .catch((e) => console.error("Module import failed", e));
</script>

<script>
    // ---------- wait DOM ---------- //
    document.addEventListener("DOMContentLoaded", () => {
        function generateState(length) {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let result = '';
            for (let i = 0; i < length; i++) {
                result += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            return result;
        }

        function randomString(len = 64) {
            const bytes = new Uint8Array(len);
            crypto.getRandomValues(bytes);
            return btoa(String.fromCharCode(...bytes))
                .replace(/[\+\/=]/g, '') // base64url
                .substring(0, len);
        }

        // LINE Login
        document.getElementById('loginLineBtn')?.addEventListener('click', function() {
            const clientId = '2007612199';
            const redirectUri = encodeURIComponent(pathConfig.LINE_REDIRECT);
            const state = generateState(8);
            const scope = 'profile openid';
            const authUrl = `https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=${clientId}&redirect_uri=${redirectUri}&state=${state}&scope=${encodeURIComponent(scope)}`;
            window.location.href = authUrl;
        });

        // Facebook Login (for page permissions)
        document.getElementById('loginFacebookBtn')?.addEventListener('click', function() {
            const clientId = '2097721124030477';
            const redirectUri = encodeURIComponent(pathConfig.FACE_BOOK_REDIRECT);
            const state = generateState(8);
            const scope = 'email,public_profile';
            const facebookAuthUrl = `https://www.facebook.com/v18.0/dialog/oauth?client_id=${clientId}&redirect_uri=${redirectUri}&state=${state}&scope=${scope}`;
            window.location.href = facebookAuthUrl;
        });

        // Facebook Page
        // const clientId = '699793683081119';
        // const redirectUri = encodeURIComponent('http://localhost:3000/demo_web_app/api/facebook_page.php');
        // const state = generateState(8);
        // const scope = 'email,public_profile,pages_show_list,pages_read_engagement,pages_manage_posts,pages_messaging,pages_manage_metadata';
        // const facebookAuthUrl = `https://www.facebook.com/v18.0/dialog/oauth?client_id=${clientId}&redirect_uri=${redirectUri}&state=${state}&scope=${scope}`;
        // window.location.href = facebookAuthUrl;


        // Google Login
        document.getElementById('loginGoogleBtn')?.addEventListener('click', function() {
            const clientId = '262467068023-qhh35u6a7gkiqvdndgl4ldr2l6nqcdgh.apps.googleusercontent.com';
            const redirectUri = encodeURIComponent(pathConfig.GOOGLE_REDIRECT);
            const state = randomString(12);
            // const scope = encodeURIComponent('https://www.googleapis.com/auth/drive');
            const scope = encodeURIComponent('openid profile email');
            const authUrl =
                `https://accounts.google.com/o/oauth2/v2/auth` +
                `?response_type=code` +
                `&client_id=${clientId}` +
                `&redirect_uri=${redirectUri}` +
                `&scope=${scope}` +
                `&state=${state}` +
                `&access_type=offline`;
            window.location.href = authUrl;
        });
    });
</script>


<!-- <script src="/e-store/js/chatService.js?v=<?php echo time(); ?>"></script> -->
<!-- <script src="/e-store/js/mathematics.js?v=<?php echo time(); ?>"></script> -->
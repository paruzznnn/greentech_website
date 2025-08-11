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
    import(`${pathConfig.BASE_WEB}js/modalBuilder.js?v=<?= time() ?>`)
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
            <a href="#privacy-policy">นโยบายความเป็นส่วนตัว</a>
            <a href="#terms">ข้อตกลงการใช้งาน</a>
            <a href="#contact">ติดต่อเรา</a>
        </nav>
        <div class="footer-social">
            <span>ติดตามเรา: </span>
            <a href="https://www.facebook.com/yourpage" target="_blank">Facebook</a> |
            <a href="https://twitter.com/yourprofile" target="_blank">Twitter</a> |
            <a href="https://www.instagram.com/yourprofile" target="_blank">Instagram</a> |
            <a href="mailto:contact@trandarstore.com">Email</a>
        </div>
    </div>
</footer>

<script type="module">
    import(`${pathConfig.BASE_WEB}js/language.js?v=<?= time() ?>`)
        .then(({
            loadLanguage,
            applyTranslations
        }) => {

            const langButtons = document.getElementById('langButtons');
            const defaultLang = localStorage.getItem('lang') || 'en';
            let service = pathConfig.BASE_WEB + 'locales/';

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


<!-- <script src="/e-store/js/chatService.js?v=<?php echo time(); ?>"></script> -->
<!-- <script src="/e-store/js/mathematics.js?v=<?php echo time(); ?>"></script> -->
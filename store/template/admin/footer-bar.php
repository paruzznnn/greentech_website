<!-- <footer class="site-footer">
    <div class="container">
        <p>&copy; 2025 Trandar Store. สงวนลิขสิทธิ์.</p>
        <nav class="footer-nav">
            <a href="#">นโยบายความเป็นส่วนตัว</a>
            <a href="#">ข้อตกลงการใช้งาน</a>
            <a href="#">ติดต่อเรา</a>
        </nav>
        <div class="footer-social">
            <span>ติดตามเรา: </span>
            <a href="#" target="_blank">Facebook</a> |
            <a href="#" target="_blank">Twitter</a> |
            <a href="#" target="_blank">Instagram</a> |
            <a href="#">Email</a>
        </div>
    </div>
</footer> -->

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
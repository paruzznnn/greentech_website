
(async () => {
    const { loadLanguage, applyTranslations } = await import(`/newstore/js/language.js`);
    const langButtons = document.getElementById('langButtons');
    const defaultLang = localStorage.getItem('lang') || 'en';

    function updateActiveButton(activeLang) {
        [...langButtons.children].forEach(btn => {
            btn.classList.toggle('active', btn.dataset.lang === activeLang);
        });
    }

    async function setLang(lang) {
        try {
            const dict = await loadLanguage(lang);
            applyTranslations(dict);
            localStorage.setItem('lang', lang);
            document.documentElement.lang = lang;
            updateActiveButton(lang);
        } catch (e) {
            console.error('Language loading error:', e);
            alert(e.message);
        }
    }

    await setLang(defaultLang);

    langButtons.addEventListener('click', e => {
        if (e.target.tagName === 'BUTTON') {
            setLang(e.target.dataset.lang);
        }
    });
})();

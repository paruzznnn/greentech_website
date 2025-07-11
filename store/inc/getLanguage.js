function toggleLanguage($elements, texts, newLanguage) {
    const translations = texts[newLanguage] || {};
    
    $elements.each(function() {
        const $element = $(this);
        let key = $element.data('key-lang');

        // Only proceed if key exists and is a string
        if (typeof key === 'string') {
            key = key.toLowerCase();
            const translatedText = translations[key];

            if (translatedText) {
                $element.text(translatedText);
                $element.attr('lang', newLanguage);
            }
        }
    });
}

function initializeLanguageSwitcher(languageSelectId, textElementsSelector, languagesUrl) {
    const $textElements = $(textElementsSelector);
    const $languageSelect = $(languageSelectId);

    // Fetch the language data
    $.getJSON(languagesUrl + '?' + new Date().getTime())
        .done(function(texts) {
            // buildKeyLangData($textElements, texts, $languageSelect.val());
            
            // Handle language change
            $languageSelect.on('change', function() {
                const selectedLanguage = $(this).val();
                toggleLanguage($textElements, texts, selectedLanguage);
            });

            // Optionally, set default language on page load
            const defaultLanguage = $languageSelect.val();
            if (defaultLanguage) {
                toggleLanguage($textElements, texts, defaultLanguage);
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            console.error('Failed to fetch language data:', textStatus, errorThrown);
        });
}
var $navbarItemsArray = [];
var $dropdownItemsArray = [];
var $menuItemsArray = [];
var $imagesItemsArray = [];

// let lastScrollTop = 0;
// const headerTop = document.querySelector('.header-top');

// window.addEventListener('scroll', function() {
//     let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

//     if (scrollTop > lastScrollTop) {
//         headerTop.style.top = "-100px"; 
//     } else {
//         headerTop.style.top = "0";
//     }
//     lastScrollTop = scrollTop;
// });

function toggleDropdown(id) {
    closeAllDropdowns(); 
    const dropdown = document.getElementById(id);
    
    if (dropdown) {
        dropdown.style.display = 'block'; 
        if (dropdown.style.display === 'block') {
            $('#background-blur').addClass('tab-open');
        }
    } else {
        // console.error(`Dropdown with id '${id}' does not exist.`);
    }
}

function closeAllDropdowns() {
    let anyOpen = false;
    const dropdowns = document.querySelectorAll('.dropdown-content');
    
    dropdowns.forEach(dropdown => {
        if (dropdown.style.display === 'block') {
            dropdown.style.display = 'none';
            anyOpen = true;
        }
    });

    if (anyOpen) {
        $('#background-blur').removeClass('tab-open');
    }
}


/****nationLanguages**** */

function nationLanguages() {
    $.getJSON("../api/languages/nation.json" + '?' + new Date().getTime(), function(data) {
        let nationalities = data.nationalities;
        let $select = $('#language-select');
        $select.empty();
    
        $.each(nationalities, function(index, entry) {
            let option = $('<option></option>')
                .attr('value', entry.abbreviation)
                .attr('data-flag', entry.flag)
                .text(entry.name);
            
            $select.append(option);
        });

        if (nationalities.length > 0) {
            $select.val(nationalities[0].abbreviation); 
            updateSelectedLanguageFlag(); 
        }
    });
}

function updateSelectedLanguageFlag() {
    let selectedOption = $('#language-select option:selected');
    let flagUrl = selectedOption.data('flag');

    if (flagUrl) {
        $('#language-select').css({
            'background-image': 'url(' + flagUrl + ')',
            'background-repeat': 'no-repeat',
            'background-position': 'left 8px center',
            'background-size': '20px 15px',
            'padding-left': '30px' 
        });
    }
}

function changeLanguage(lang) {
    fetch(`../api/languages/${lang}.json`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            document.querySelectorAll("[data-translate][lang]").forEach(el => {
                const key = el.getAttribute("data-translate");

                el.textContent = data[key] || el.textContent; 
                // Update the lang attribute to the selected language
                el.setAttribute('lang', lang);

            });
        })
        .catch(error => console.error('Error loading language file:', error));
}

/****nationLanguages**** */


$(document).ready(function() {

    nationLanguages();
    const selectedLanguage = localStorage.getItem('language') || 'th';
    changeLanguage(selectedLanguage);

    $('#language-select').on('change', function() {
        const selectedLang = $(this).val();
        changeLanguage(selectedLang);
        updateSelectedLanguageFlag();
    });

});




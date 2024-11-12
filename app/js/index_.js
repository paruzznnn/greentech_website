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
    if (!id) return;

    closeAllDropdowns(); 
    const $dropdown = $('#' + id);
    
    if ($dropdown.length) {
        $dropdown.css('display', 'block');
        
        if ($dropdown.css('display') === 'block') {
            $('#background-blur').addClass('tab-open'); 
        }
    }
}

function closeAllDropdowns() {
    let anyOpen = false;
    const $dropdowns = $('.dropdown-content'); 
    
    $dropdowns.each(function() {
        if ($(this).css('display') === 'block') {
            $(this).css('display', 'none');
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

function setupModal(modalId, btnId, closeClass) {
    var $modal = $('#' + modalId);
    var $btn = $('#' + btnId);
    var $span = $('.' + closeClass).first(); 

    if ($modal.length && $btn.length && $span.length) {
        $btn.on('click', function() {
            $modal.show();
        });

        $span.on('click', function() {
            $modal.hide();
        });

        $(window).on('click', function(event) {
            if ($(event.target).is($modal)) {
                $modal.hide();
            }
        });
    } else {
        
    }
}





$(document).ready(function() {

    nationLanguages();
    const selectedLanguage = localStorage.getItem('language') || 'th';
    changeLanguage(selectedLanguage);

    $('#language-select').on('change', function() {
        const selectedLang = $(this).val().toLowerCase();
        changeLanguage(selectedLang);
        updateSelectedLanguageFlag();
    });

    setupModal("myModal-channel", "myBtn-channel", "modal-close-channel");

    $('#togglePasswordPage').on('click', function() {
        const password = $('#password');
        const type = password.attr('type') === 'password' ? 'text' : 'password';
        password.attr('type', type);
        $(this).find('i').toggleClass('fa-eye fa-eye-slash');
    });

    $('#loginModal').on('submit', function(event) {
        event.preventDefault();
    
        const email = $('#email').val().trim();
        const password = $('#password').val().trim();
    
        if (!email || !password) {
            alert('Please enter both email and password');
            return;
        }
    
        $.ajax({
            url: './admin/actions/check_login.php',
            type: 'POST',
            data: {
                email: email,
                password: password
            },
            dataType: 'json',
            success: function(response) {

                if (response.status === "success") {

                    sessionStorage.setItem('jwt', response.jwt);
                    

                    const token = sessionStorage.getItem('jwt');

                    $.ajax({
                        url: './admin/actions/protected.php', 
                        type: 'GET',
                        headers: {
                            'Authorization': 'Bearer ' + token
                        },
                        success: function(response) {
                            
                            // if (response.status === "success") {
                                
                            //     switch (response.data.role) {
                            //         case 1:
                            //             window.location.href = './admin/index.php';
                            //             break;
                            //         case 2:
                            //             window.location.href = 'index.php';
                            //             break;
                            //         default:
                            //             alert('Unknown role');
                            //             break;
                            //     }

                            // } else {
                            //     alert(response.message);
                            // }
                        },
                        error: function(xhr, status, error) {
                            console.error("Request failed:", status, error);
                            alert("An error occurred while accessing protected resource.");
                        }
                    });
    
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX request failed:", status, error);
                alert("An error occurred. Please try again.");
            }
        });
    });



});




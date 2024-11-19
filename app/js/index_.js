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

    $dropdowns.each(function () {
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
    $.getJSON("../api/languages/nation.json" + '?' + new Date().getTime(), function (data) {
        let nationalities = data.nationalities;
        let $select = $('#language-select');
        $select.empty();

        $.each(nationalities, function (index, entry) {
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
        $btn.on('click', function () {
            // Close any currently open modal before showing the new one
            $('.modal').each(function() {
                if ($(this).is(':visible')) {
                    $(this).hide();
                }
            });
            $modal.show();
        });

        $span.on('click', function () {
            $modal.hide();
        });

        $(window).on('click', function (event) {
            if ($(event.target).is($modal)) {
                $modal.hide();
            }
        });
    } else {
        // Handle cases where modal, button, or close button doesn't exist
    }
}



$(document).ready(function () {

    $('#loading-overlay').fadeIn();
    $('#loading-overlay').fadeOut();

    nationLanguages();
    const selectedLanguage = localStorage.getItem('language') || 'th';
    changeLanguage(selectedLanguage);

    $('#language-select').on('change', function () {
        const selectedLang = $(this).val().toLowerCase();
        changeLanguage(selectedLang);
        updateSelectedLanguageFlag();
    });

    setupModal("myModal-sign-in", "myBtn-sign-in", "modal-close-sign-in");
    setupModal("myModal-forgot-password", "myBtn-forgot-password", "modal-close-forgot-password");

    $('#togglePasswordSignin').on('click', function () {
        const password = $('#password');
        const type = password.attr('type') === 'password' ? 'text' : 'password';
        password.attr('type', type);
        $(this).find('i').toggleClass('fa-eye fa-eye-slash');
    });

    // $('#togglePasswordSignup').on('click', function () {
    //     const password = $('#signUp_password');
    //     const type = password.attr('type') === 'password' ? 'text' : 'password';
    //     password.attr('type', type);
    //     $(this).find('i').toggleClass('fa-eye fa-eye-slash');
    // });

    $('#loginModal').on('submit', function (event) {
        event.preventDefault();

        const username = $('#username').val().trim();
        const password = $('#password').val().trim();

        if (!username || !password) {
            alert('Please enter both email and password');
            return;
        }

        $.ajax({
            url: 'admin/actions/check_login.php',
            type: 'POST',
            data: {
                username: username,
                password: password
            },
            dataType: 'json',
            success: function (response) {

                console.log('response', response);


                if (response.status === "success") {

                    sessionStorage.setItem('jwt', response.jwt);


                    const token = sessionStorage.getItem('jwt');

                    // $.ajax({
                    //     url: './admin/actions/protected.php', 
                    //     type: 'GET',
                    //     headers: {
                    //         'Authorization': 'Bearer ' + token
                    //     },
                    //     success: function(response) {

                    //         if (response.status === "success") {

                    //             switch (response.data.role) {
                    //                 case 1:
                    //                     window.location.href = './admin/index.php';
                    //                     break;
                    //                 case 2:
                    //                     window.location.href = 'index.php';
                    //                     break;
                    //                 default:
                    //                     alert('Unknown role');
                    //                     break;
                    //             }

                    //         } else {
                    //             alert(response.message);
                    //         }
                    //     },
                    //     error: function(xhr, status, error) {
                    //         console.error("Request failed:", status, error);
                    //         alert("An error occurred while accessing protected resource.");
                    //     }
                    // });

                } else {
                    alert(response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX request failed:", status, error);
                alert("An error occurred. Please try again.");
            }
        });
    });

    $('#submitForgot').on('click', function (event) {

        var formNews = $("#forgotModal")[0];
        var formData = new FormData(formNews);

        $(".is-invalid").removeClass("is-invalid");
        for (var tag of formData.entries()) {

            if (tag[0] === 'forgot_email' && tag[1].trim() === '') {
                $("#forgot_email").addClass("is-invalid");
                return;
            }

        }

        formData.append("action", 'forgotPassword');
        // formData.append("sendMail", isSendMailChecked);

        $('#loading-overlay').fadeIn();
        $.ajax({
            url: 'actions/otp_forgot_password.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
            
                if (response.status == 'succeed') {
                    $('#loading-overlay').fadeOut();
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });
                    
                    Toast.fire({
                        icon: "success",
                        title: response.message
                    }).then(() => {
                        window.location.reload(); 
                    });
                }else{
                    $('#loading-overlay').fadeOut();
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });
                    
                    Toast.fire({
                        icon: "error",
                        title: response.message
                    }).then(() => {
                        // window.location.reload(); 
                    });

                }

            },
            error: function (xhr, status, error) {
                // Handle error, e.g., show an error message
                console.error('Form submission failed:', error);
            }
        });
        

    });

    $('#newsMarquee').hover(
        function () {
            this.stop();
        },
        function () {
            this.start();
        }
    );

    var checkRegister = false;
    $('#signUp_password').on('input', function (event) {
        const password = $(this).val();

        const requirements = [
            { id: '#password_length', test: password.length >= 8 },
            { id: '#password_upper', test: /[A-Z]/.test(password) },
            { id: '#password_lower', test: /[a-z]/.test(password) },
            { id: '#password_number', test: /\d/.test(password) },
            { id: '#password_special', test: /[!@#$%^&*(),.?":{}|<>]/.test(password) },
        ];

        // Reset all indicators
        $('.requirements span i').removeClass('fas fa-check valid').addClass('fas fa-times invalid');

        // Check each requirement
        requirements.forEach(req => {
            if (req.test) {
                $(req.id).find('i').removeClass('fas fa-times invalid').addClass('fas fa-check valid');
            }
        });

        // Check if all requirements are met
        const allRequirementsMet = requirements.every(req => req.test);

        // If all requirements are met, show an alert
        if (allRequirementsMet) {
            checkRegister = true;
        }
    });

    $('#submitSignUp').on('click', function (event) {
        // event.preventDefault();

        var formNews = $("#personal_register")[0];
        var formData = new FormData(formNews);


        $(".is-invalid").removeClass("is-invalid");
        for (var tag of formData.entries()) {

            if (tag[0] === 'signUp_name' && tag[1].trim() === '') {
                $("#signUp_name").addClass("is-invalid");
                return;
            }
            if (tag[0] === 'signUp_surname' && tag[1].trim() === '') {
                $("#signUp_surname").addClass("is-invalid");
                return;
            }
            if (tag[0] === 'signUp_password') {
                if (tag[1].trim() === '') {
                    $("#signUp_password").addClass("is-invalid");
                    return;
                }
                if (!checkRegister) {
                    $("#signUp_password").addClass("is-invalid");
                    return;
                }
            }
            if (tag[0] === 'signUp_confirm_password') {
                if (tag[1].trim() === '') {
                    $("#signUp_confirm_password").addClass("is-invalid");
                    return;
                }
                if (!checkRegister) {
                    $("#signUp_confirm_password").addClass("is-invalid");
                    return;
                }
            }
            if (tag[0] === 'signUp_email' && tag[1].trim() === '') {
                $("#signUp_email").addClass("is-invalid");
                return;
            }
            if (tag[0] === 'signUp_phone' && tag[1].trim() === '') {
                $("#signUp_phone").addClass("is-invalid");
                return;
            }
        }

        var isAgreeChecked = $('#signUp_agree').is(':checked');
        var isSendMailChecked = $('#signUp_send_mail').is(':checked');

        let confirm_password = $('#signUp_confirm_password').val();
        let password = $('#signUp_password').val();


        if (password != confirm_password) {

            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "warning",
                title: "Passwords are not the same."
            });
            return;

        }


        if (!isAgreeChecked) {

            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "warning",
                title: "Click to accept the terms and conditions to continue."
            });

            return;
        }

        if (!isSendMailChecked) {

            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "warning",
                title: "Click to accept email verification terms."
            });


            return;
        }

        formData.append("action", 'save_signup');
        // formData.append("sendMail", isSendMailChecked);

        $('#loading-overlay').fadeIn();

        $.ajax({
            url: 'admin/actions/check_register.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {

                if (response.status == 'succeed') {
                    $('#loading-overlay').fadeOut();
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });
                    
                    Toast.fire({
                        icon: "success",
                        title: response.message
                    }).then(() => {
                        window.location.reload(); 
                    });
                }else{
                    $('#loading-overlay').fadeOut();
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });
                    
                    Toast.fire({
                        icon: "error",
                        title: response.message
                    }).then(() => {
                        // window.location.reload(); 
                    });

                }

            },
            error: function (xhr, status, error) {
                // Handle error, e.g., show an error message
                console.error('Form submission failed:', error);
            }
        });

    });

});




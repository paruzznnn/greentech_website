$(document).ready(function() {
    
    $('#togglePassword').on('click', function() {
        const password = $('#password');
        const type = password.attr('type') === 'password' ? 'text' : 'password';
        password.attr('type', type);
        $(this).find('i').toggleClass('fa-eye fa-eye-slash');
    });


    $('#loginForm').on('submit', function(event) {
        event.preventDefault();
    
        const email = $('#email').val().trim();
        const password = $('#password').val().trim();
        const rememberMe = $('#remember').is(':checked');
    
        if (!email || !password) {
            alert('Please enter both email and password');
            return;
        }
    
        $.ajax({
            url: 'actions/check_login.php', // Adjust path as needed
            type: 'POST',
            data: {
                email: email,
                password: password
            },
            dataType: 'json',
            success: function(response) {

                if (response.status === "success") {

                    if (rememberMe) {
                        localStorage.setItem('jwt', response.jwt);
                    } else {
                        sessionStorage.setItem('jwt', response.jwt);
                    }

                    const token = rememberMe ? localStorage.getItem('jwt') : sessionStorage.getItem('jwt');

                    $.ajax({
                        url: 'actions/protected.php', 
                        type: 'GET',
                        headers: {
                            'Authorization': 'Bearer ' + token
                        },
                        success: function(response) {
                            
                            if (response.status === "success") {
                                
                                switch (response.data.role) {
                                    case 1:
                                        window.location.href = 'index.php';
                                        break;
                                    case 2:
                                        window.location.href = '../../index.php';
                                        break;
                                    default:
                                        alert('Unknown role');
                                        break;
                                }

                            } else {
                                alert(response.message);
                            }
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







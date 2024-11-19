<?php
require_once('../lib/connect.php');
global $conn;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>

    <?php include 'inc_head.php' ?>
    <link href="css/index_.css?v=<?php echo time(); ?>" rel="stylesheet">


    <style>
        .height-100 {
            height: 100vh
        }

        .card {
            width: 400px;
            border: none;
            height: 300px;
            box-shadow: 0px 5px 20px 0px #d2dae3;
            z-index: 1;
            display: flex;
            justify-content: center;
            align-items: center
        }

        .card h6 {
            color: #ff9800;
            font-size: 20px
        }

        .inputs input {
            width: 40px;
            height: 40px
        }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            margin: 0
        }

        .card-2 {
            background-color: #fff;
            padding: 10px;
            width: 350px;
            height: 100px;
            bottom: -50px;
            left: 20px;
            position: absolute;
            border-radius: 5px
        }

        .card-2 .content {
            margin-top: 50px
        }

        .form-control:focus {
            box-shadow: none;
            border: 2px solid #FF9800;
        }

        .validate {
            border-radius: 20px;
            height: 40px;
            background-color: #FF9800;
            border: 1px solid #FF9800;
            width: 140px;
            color: #ffffff;
        }

        .validate:hover {
            color: #ffffff;
            box-shadow: 1px 2px 8px #FF9800;
        }
    </style>

</head>

<body>

    <div id="loading-overlay" class="hidden">
        <div class="spinner"></div>
    </div>

    <?php

    if (isset($_GET['register']) || isset($_GET['forgot'])) {

        $user_id = isset($_GET['otpID']) ? $_GET['otpID'] : '';

        $sql = "SELECT mb_user.email 
        FROM mb_user 
        WHERE mb_user.user_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            exit();
        }

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $row = $result->fetch_assoc();

        $email = $row['email'];
        $maskedEmail = substr($email, 0, 9) . str_repeat('*', strpos($email, '@') - 9) . substr($email, strpos($email, '@'));
    }


    ?>

    <?php if (isset($_GET['register'])) { ?>
        <div class="container height-100 d-flex justify-content-center align-items-center">
            <div class="position-relative">
                <div class="card p-2 text-center">
                    <h6>Please enter the one time OTP <br> to verify your account</h6>
                    <div>
                        <span>A code has been sent to</span> <br>
                        <small id="maskedNumber"><?php echo $maskedEmail; ?></small>
                    </div>
                    <input type="hidden" id="user_id" name="user_id" value="<?php echo $user_id; ?>">
                    <div id="otp" class="inputs d-flex flex-row justify-content-center mt-2">
                        <input class="m-2 text-center form-control rounded" type="text" id="first" maxlength="1" />
                        <input class="m-2 text-center form-control rounded" type="text" id="second" maxlength="1" />
                        <input class="m-2 text-center form-control rounded" type="text" id="third" maxlength="1" />
                        <input class="m-2 text-center form-control rounded" type="text" id="fourth" maxlength="1" />
                        <input class="m-2 text-center form-control rounded" type="text" id="fifth" maxlength="1" />
                        <input class="m-2 text-center form-control rounded" type="text" id="sixth" maxlength="1" />
                    </div>
                    <div class="mt-4">
                        <button id="confirm_emailBtn" class="px-4 validate">confirm</button>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>


    <?php if (isset($_GET['forgot'])) { ?>
        <div class="container height-100 d-flex justify-content-center align-items-center">
            <div class="position-relative">
                <div class="card p-2 text-center">
                    <h6>Please enter the one time OTP <br> to verify your account</h6>
                    <div>
                        <span>A code has been sent to</span> <br>
                        <small id="maskedNumber"><?php echo $maskedEmail; ?></small>
                    </div>
                    <input type="hidden" id="user_id" name="user_id" value="<?php echo $user_id; ?>">
                    <div id="otp" class="inputs d-flex flex-row justify-content-center mt-2">
                        <input class="m-2 text-center form-control rounded" type="text" id="first" maxlength="1" />
                        <input class="m-2 text-center form-control rounded" type="text" id="second" maxlength="1" />
                        <input class="m-2 text-center form-control rounded" type="text" id="third" maxlength="1" />
                        <input class="m-2 text-center form-control rounded" type="text" id="fourth" maxlength="1" />
                        <input class="m-2 text-center form-control rounded" type="text" id="fifth" maxlength="1" />
                        <input class="m-2 text-center form-control rounded" type="text" id="sixth" maxlength="1" />
                    </div>
                    <div class="mt-4">
                        <button id="confirm_resetBtn" class="px-4 validate">confirm</button>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>




    <script>
        function OTPInput() {
            const $inputs = $('#otp > input');

            $inputs.each(function(index) {
                $(this).on('input', function() {
                    if (this.value.length > 1) {
                        this.value = this.value[0];
                    }
                    if (this.value !== '' && index < $inputs.length - 1) {
                        $inputs.eq(index + 1).focus();
                    }
                });

                $(this).on('keydown', function(event) {
                    if (event.key === 'Backspace') {
                        this.value = '';
                        if (index > 0) {
                            $inputs.eq(index - 1).focus();
                        }
                    }
                });
            });
        }

        $(document).ready(function() {

            $('#loading-overlay').fadeIn();
            $('#loading-overlay').fadeOut();

            OTPInput();

            $('#confirm_emailBtn').on('click', function() {
                let otp = '';
                $('#otp > input').each(function() {
                    otp += $(this).val();
                });
                let user_id = $('#user_id').val();
                confirmOTP(user_id, otp);
            });


            $('#confirm_resetBtn').on('click', function() {
                let otp = '';
                $('#otp > input').each(function() {
                    otp += $(this).val();
                });

                let user_id = $('#user_id').val();
                confirmReset(user_id, otp);

            });


        });

        function confirmReset(user_id, otp) {

            $('#loading-overlay').fadeIn();

            $.ajax({
                url: 'actions/otp_forgot_password.php',
                type: 'POST',
                data: {
                    action: 'sendReset',
                    userId: user_id,
                    otpCode: otp
                },
                dataType: 'JSON',
                success: function(response) {

                    if (response.status == 'succeed') {

                        $.ajax({
                            url: 'actions/otp_forgot_password.php',
                            type: 'POST',
                            data: {
                                action: 'generatePassword',
                                userId: response.user_id
                            },
                            dataType: 'JSON',
                            success: function(response) {

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
                                        window.location.href = 'index.php';
                                    });

                                } else {
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
                            error: function(error) {
                                console.log('Error:', error);
                            }
                        });

                    } else {
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
                error: function(error) {
                    console.log('Error:', error);
                }
            });

        }


        function confirmOTP(user_id, otp) {

            $('#loading-overlay').fadeIn();

            $.ajax({
                url: 'actions/otp_confirm_email.php',
                type: 'POST',
                data: {
                    action: 'sendOTP',
                    userId: user_id,
                    otpCode: otp
                },
                dataType: 'JSON',
                success: function(response) {

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
                            window.location.href = 'index.php';
                        });

                    } else {
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
                error: function(error) {
                    console.log('Error:', error);
                }
            });

        }
    </script>


</body>

</html>
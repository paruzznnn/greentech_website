<?php
require_once('../lib/connect.php');
global $conn;
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>

    <?php include 'inc_head.php' ?>

    <!-- Owl Carousel CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>


    <link href="css/index_.css?v=<?php echo time(); ?>" rel="stylesheet">

    <style>
        .valid {
            color: #4CAF50;
        }

        .invalid {
            color: #ff3d00;
        }

        .box-consent {
            border: 1px solid #d2d2d2;
            padding: 20px;
            border-radius: 3px;
        }
    </style>

</head>

<body>

    <?php include 'template/header.php' ?>
    <?php include 'template/navbar_slide.php' ?>

    <div class="content-sticky" id="">
        <div class="container">
            <div class="box-content">

                <h6 style="text-align: center; color: #555;" class="mt-2">
                    <span>
                        <i class="fas fa-id-card"></i>
                    </span>
                    <span data-key-lang="" lang="US">Please fill in the information.</span>
                </h6>

                <hr>

                <form id="personal_register" action="" method="post" style="color: #555;">

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group mt-1">
                                <label for="">
                                    <i class="fas fa-user"></i>
                                    <span>First name</span>:
                                </label>
                                <input id="signUp_name" name="signUp_name" type="text" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mt-1">
                                <label for="">
                                    <span>Last name</span>:
                                </label>
                                <input id="signUp_surname" name="signUp_surname" type="" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mt-1">
                                <label for="">
                                    <i class="fas fa-envelope"></i>
                                    <span>Email</span>:
                                </label>
                                <input id="signUp_email" name="signUp_email" type="email" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mt-1">
                                <label for="">
                                    <i class="fas fa-phone-volume"></i>
                                    <span>Phone</span>:
                                </label>
                                <input id="signUp_phone" name="signUp_phone" type="tel" class="form-control">
                            </div>
                        </div>

                        <!-- row -->
                    </div>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group mt-1">
                                <label for="">
                                    <i class="fas fa-lock"></i>
                                    <span>Password</span>:
                                </label>
                                <input id="signUp_password" name="signUp_password" type="password" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mt-1">
                                <label for="">
                                    <span>Confirm password</span>:
                                </label>
                                <input id="signUp_confirm_password" name="signUp_confirm_password" type="password" class="form-control" disabled>
                            </div>
                        </div>

                    </div>


                    <div class="row">
                        <div class="col-md-12">

                            <div class="mt-3">
                                <div class="requirements">
                                    <span id="password_length"><i class="fas fa-times invalid"></i> Minimum length: 8 characters</span><br>
                                    <span id="password_upper"><i class="fas fa-times invalid"></i> At least one uppercase letter (A-Z)</span><br>
                                    <span id="password_lower"><i class="fas fa-times invalid"></i> At least one lowercase letter (a-z)</span><br>
                                    <span id="password_number"><i class="fas fa-times invalid"></i> At least one digit (0-9)</span><br>
                                    <span id="password_special"><i class="fas fa-times invalid"></i> At least one special character (!@#_)</span>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="box-consent mt-4">
                                <h5>
                                    I have read and acknowledged the privacy policy for applying for membership to purchase products.
                                </h5>
                                <p>
                                    I consent to the use or disclosure of my personal information to the Company for the purpose of processing purchases. Shipping or providing related services Including notification of news, promotions or marketing information from the company.
                                </p>
                                <p>
                                    In this regard, I know that I can revoke this consent at any time.
                                </p>
                                <p>
                                    I understand the terms and conditions. Including understanding about the collection, use and disclosure of personal information related to membership applications and product purchases.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">

                        <div class="col-md-8">
                            <input type="checkbox" id="signUp_agree" name="signUp_agree" value="1">
                            <label for=""> agree</label><br>
                            <input type="checkbox" id="signUp_send_mail" name="signUp_send_mail" value="1">
                            <label for=""> send an email to confirm information.</label><br>
                        </div>
                        <div class="col-md-4" style="display: flex; align-items: center; justify-content: flex-end;">
                            <div>
                                <button type="button" id="submitSignUp" class="btn btn-success">confirm</button>
                            </div>
                        </div>

                    </div>

                </form>

            </div>
        </div>
    </div>


    <?php include 'template/footer.php' ?>


    <script src="js/index_.js?v=<?php echo time(); ?>"></script>

</body>

</html>
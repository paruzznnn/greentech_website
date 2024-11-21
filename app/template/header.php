<?php
//header-top-right

$isProtocol = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
$isFile = ($isProtocol === 'http') ? '.php' : '';

$menuItems = [
    [
        'id' => 0,
        'icon' => 'fas fa-user-plus',
        'text' => '',
        'translate' => 'Sign_up',
        'link' => 'register'. $isFile,
        'modal_id' => ''
    ],
    [
        'id' => 1,
        'icon' => 'fas fa-sign-in-alt',
        'text' => '',
        'translate' => 'Sign_in',
        'link' => '#',
        'modal_id' => 'myBtn-sign-in'
    ]
];
?>

<div id="loading-overlay" class="hidden">
    <div class="spinner"></div>
</div>


<div id="background-blur"></div>

<div class="header-top">
    <div class="container">


        <div class="header-top-left">
            <a href="#">
                <img class="logo" src="../public/img/logo-ALLABLE-06.png" alt="">
            </a>
        </div>


        <div class="header-top-right">
            <?php foreach ($menuItems as $item): ?>
                <div>
                    <a type="button" href="<?php echo $item['link']; ?>" id="<?php echo $item['modal_id'] ?>">
                        <i class="<?php echo $item['icon']; ?>"></i>
                        <span data-translate="<?php echo $item['translate']; ?>" lang="th">
                            <?php echo $item['text']; ?>
                        </span>
                    </a>
                </div>
            <?php endforeach; ?>
            <div>
                <select id="language-select" class="language-select">
                </select>
            </div>
            <div class="header-link">
                <a href="https://www.facebook.com/allablethailand/" target="_blank" style="background: #ffa719; color: #fafafa;">
                    <i class="fab fa-facebook-square"></i>
                </a>
                <a href="https://www.youtube.com/@AllableThailand" target="_blank" style="background: #ffa719; color: #fafafa;">
                    <i class="fab fa-youtube"></i>
                </a>
                <a href="#" style="background: #ffa719; color: #fafafa;">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" style="background: #ffa719; color: #fafafa;">
                    <i class="fab fa-line"></i>
                </a>
            </div>
        </div>

    </div>
</div>

<div id="myModal-sign-in" class="modal">
    <div class="modal-content" style="width: 350px !important;">
        <div class="modal-header">
            <span class="modal-close-sign-in">&times;</span>
        </div>
        <div class="modal-body" style="background-color: #9e9e9e1f;">

            <div class="box-sign-in-container">

                <div class="card">
                    <section class="card-body">
                        <div style="text-align: center;">
                            <img class="" style="width: 70%;" src="../public/img/logo-ALLABLE-06.png" alt="">
                        </div>

                        <h6 style="text-align: center; color: #555;" class="mt-2">
                            <span><i class="fas fa-unlock"></i></span>
                            <span data-key-lang="Pleaselogin" lang="US">Please log in</span>
                        </h6>

                        <hr>

                        <form id="loginModal" action="" method="post">

                            <div class="form-group mt-4">
                                <input id="username" type="text" class="emet-login input" placeholder="Please enter your user.">
                            </div>

                            <div class="form-group mt-2" style="position: relative;">
                                <input id="password" type="password" class="emet-login inpu" data-type="password">
                                <span class=""
                                    style="position: absolute; top: 10px; right: 20px; color: #555555;"
                                    id="togglePasswordSignin">
                                    <i class="fas fa-eye-slash"></i>
                                </span>
                            </div>


                            <div class="row mt-4">

                                <div class="col-md-12 text-end" 
                                style="
                                display: flex; 
                                justify-content: space-between;
                                align-items: center;
                                ">
                                    <a href="<?php echo 'register'.$isFile ?>">
                                        <span style="font-size: 13px !important;">
                                            สมัครสมาชิก
                                        </span>
                                    </a>

                                    <a type="button" href="#"  id="myBtn-forgot-password">
                                        <span style="font-size: 13px !important;">
                                            ลืมรหัสผ่าน
                                        </span>
                                    </a>
                                </div>

                                <div class="col-md-12">
                                    <div class="d-inline-flex">
                                        <button type="submit" class=""
                                            style="
                                        width: 260px;
                                        border: none;
                                        border-radius: 4px;
                                        padding: 10px;
                                        background: #ff8200;
                                        color: white;
                                        "> Login </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </section>
                </div>
            </div>

        </div>
    </div>
</div>

<div id="myModal-forgot-password" class="modal">
    <div class="modal-content" style="width: 350px !important;">
        <div class="modal-header">
            <span class="modal-close-forgot-password">&times;</span>
        </div>
        <div class="modal-body" style="background-color: #9e9e9e1f;">

            <div class="box-forgot-password-container">

                <div class="card">
                    <section class="card-body">
                        <div style="text-align: center;">
                            <img class="" style="width: 70%;" src="../public/img/logo-ALLABLE-06.png" alt="">
                        </div>

                        <h6 style="text-align: center; color: #555;" class="mt-2">
                            <span>
                                <i class="fas fa-key"></i>
                            </span>
                            <span data-key-lang="" lang="US">Forgot your password?</span>
                        </h6>

                        <hr>

                        <form id="forgotModal" action="" method="post">

                            <div class="form-group mt-4">
                                <input 
                                id="forgot_email" 
                                name="forgot_email" type="text" 
                                class="form-control emet-login input" 
                                placeholder="Please enter your email.">
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="d-inline-flex">
                                        <button type="button" 
                                        id="submitForgot"
                                        class=""
                                        style="
                                        width: 260px;
                                        border: none;
                                        border-radius: 4px;
                                        padding: 10px;
                                        background: #ff8200;
                                        color: white;
                                        "> send email </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </section>
                </div>
            </div>

        </div>
    </div>
</div>

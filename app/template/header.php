<?php
    //header-top-right
    $menuItems = [
        // [
        //     'icon' => 'fas fa-user-plus',
        //     'text' => '',
        //     'translate' => 'Sign_up',
        //     'link' => '#'
        // ],
        [
            'icon' => 'fas fa-sign-in-alt',
            'text' => '',
            'translate' => 'Sign_in',
            'link' => '#',
            'modal_id' => 'myBtn-channel'
        ]
    ];
?>


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
                    <a type="button" href="<?php echo $item['link']; ?>" id="<?php echo $item['modal_id']?>">
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
        </div>

    </div>
</div>

<div id="myModal-channel" class="modal">
    <!-- Modal content -->
    <div class="modal-content" style="width: 350px !important;">
        <div class="modal-header">
            <span class="modal-close-channel">&times;</span>
        </div>
        <div class="modal-body" style="background-color: #9e9e9e1f;">

            <div class="box-login-container">

                <div class="card">

                    <article class="card-body">

                        <!-- <div class="box-logo-container">

                            <a href="<?php echo $basePath .'login.php'?>">
                                <div class="box-logo">
                                    <div class="box-logo-image">
                                        <img src="../public/img/trandar_logo.png" alt="Trandar Logo">
                                    </div>
                                    <div data-key-lang="Trandar" lang="US" class="box-logo-text">
                                        Trandar
                                    </div>
                                </div>
                            </a>

                            <a href="https://www.origami.life//login.php#/">
                                <div class="box-logo">
                                    <div class="box-logo-image">
                                        <img src="../public/img/ogm_logo.png" alt="Origami Logo">
                                    </div>
                                    <div data-key-lang="Origami" lang="US" class="box-logo-text">
                                        Origami
                                    </div>
                                </div>
                            </a>

                        </div> -->

                        <br>
                        <h6 style="text-align: center; color: #555;" class="mt-2">
                            <span><i class="fas fa-unlock"></i></span>
                            <span data-key-lang="Pleaselogin" lang="US">Please log in</span>
                        </h6>
                        
                        <hr>

                        <form id="loginModal" action="" method="post">
                                
                            <div class="form-group mt-4">
                                <input id="email" type="text" class="emet-login input" placeholder="Email or login">
                            </div>

                            <div class="form-group mt-2" style="position: relative;">
                                <input id="password" type="password" class="emet-login inpu" data-type="password">
                                <span class="" 
                                style="position: absolute; top: 10px; right: 20px; color: #555555;" 
                                id="togglePasswordPage">
                                    <i class="fas fa-eye-slash"></i>
                                </span>
                            </div>    

                            
                            <div class="row mt-4">

                                <!-- <div class="col-md-12 text-end">
                                    <a href="register.php">
                                        <span style="font-size: 13px !important;">
                                            สมัครสมาชิก
                                        </span>
                                    </a>
                                </div> -->
                        
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
                                        "
                                        > Login  </button>
                                    </div>
                                </div>
                            </div>

                        </form>

                    </article>
                </div>
                    

        </div>

        </div>
    </div>
</div>



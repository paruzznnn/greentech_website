<?php
    //header-top-right
    $menuItems = [
        [
            'icon' => 'fas fa-user-plus',
            'text' => '',
            'translate' => 'Sign_up',
            'link' => '#'
        ],
        [
            'icon' => 'fas fa-sign-in-alt',
            'text' => '',
            'translate' => 'Sign_in',
            'link' => '#'
        ],
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
            

        <!-- <div class="header-top-right">
            <?php foreach ($menuItems as $item): ?>
                <div>
                    <a href="<?php echo $item['link']; ?>">
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
        </div> -->

    </div>
</div>





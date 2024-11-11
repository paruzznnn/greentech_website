<?php

    $isProtocol = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
    $isFile = ($isProtocol === 'http') ? '.php' : '';

    $navbarItems = [
        [
            'icon' => '',
            'text' => '', 
            'translate' => 'Home', 
            'link' => 'index'.$isFile
        ],
        [
            'icon' => '',
            'text' => 'Origami Platforms',
            'translate' => '',
            'link' => '#',
            'isDropdown' => true,
            'id' => 'dropdown1', 
        ],
        [
            'icon' => '',
            'text' => 'Origami AI',
            'translate' => '',
            'link' => '#',
            'isDropdown' => true,
            'id' => 'dropdown2',
        ],
        [
            'icon' => '',
            'text' => 'Allable Cloud', 
            'translate' => '',
            'link' => 'allable_cloud'.$isFile
        ],
        [
            'icon' => '',
            'text' => '',
            'translate' => 'News',
            'link' => 'news'.$isFile
        ],
        [
            'icon' => '',
            'text' => '', 
            'translate' => 'About_us',
            'link' => 'about'.$isFile
        ],
        [
            'icon' => '',
            'text' => 'Contact us', 
            'translate' => 'Contact_us',
            'link' => 'contact'.$isFile
        ],
    ];

    // Dropdown items array
    $dropdownItems = [
        [
            'id' => 'dropdown1',
            'items' => [
                [
                    'icon' => '',
                    'text' => 'HRM', 
                    'translate' => '',
                    'link' => '#'
                ],
                [
                    'icon' => '',
                    'text' => 'CRM', 
                    'translate' => '',
                    'link' => '#'
                ],
                [
                    'icon' => '',
                    'text' => 'Project Management', 
                    'translate' => '',
                    'link' => '#'
                ],
                [
                    'icon' => '',
                    'text' => 'Expense Management', 
                    'translate' => '',
                    'link' => '#'
                ],
                [
                    'icon' => '',
                    'text' => 'iDoc', 
                    'translate' => '',
                    'link' => '#'
                ],
                [
                    'icon' => '',
                    'text' => 'Event Management', 
                    'translate' => '',
                    'link' => '#'
                ],
                [
                    'icon' => '',
                    'text' => 'Asset Management',
                    'translate' => '',
                    'link' => '#'
                ],
                [
                    'icon' => '',
                    'text' => 'Helpdesk Service', 
                    'translate' => '',
                    'link' => '#'
                ],
                [
                    'icon' => '',
                    'text' => 'Learning Management System', 
                    'translate' => '',
                    'link' => '#'
                ],
            ]
        ],
        [
            'id' => 'dropdown2',
            'items' => [
                [
                    'icon' => '',
                    'text' => 'DevRev', 
                    'translate' => '',
                    'link' => 'origami_ai'.$isFile
                ],
                [
                    'icon' => '',
                    'text' => 'Pricing', 
                    'translate' => '',
                    'link' => 'pricing'.$isFile
                ],
            ]
        ],
    ];

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


<div id="navbar-menu" onmouseleave="closeAllDropdowns()">
    <div class="container">
        <div class="over-menu">
            <?php foreach ($navbarItems as $item): ?>
                <?php if (isset($item['isDropdown']) && $item['isDropdown']): ?>
                    <div class="dropdown">
                        <a 
                        href="<?php echo $item['link']; ?>"
                        class="dropbtn" 
                        onmouseenter="toggleDropdown('<?php echo $item['id']; ?>')"
                        >
                            <i class="<?php echo $item['icon']; ?>"></i>
                            <span data-translate="<?php echo $item['translate']; ?>" lang="th">
                                <?php echo $item['text']; ?>
                            </span>
                            <span class="dropdown-icon">
                                <i class="fas fa-caret-down"></i>
                            </span>
                        </a>
                    </div>
                <?php else: ?>
                    <a href="<?php echo $item['link']; ?>" 
                    <?php echo isset($item['target']) ? 'target="' . $item['target'] . '"' : ''; ?>
                    onmouseenter="toggleDropdown()"
                    >
                    <i class="<?php echo $item['icon']; ?>"></i>
                    <span data-translate="<?php echo $item['translate']; ?>" lang="th">
                        <?php echo $item['text']; ?>
                    </span>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <?php foreach ($dropdownItems as $dropdown): ?>
        <div class="dropdown-content" id="<?php echo $dropdown['id']; ?>" style="display:none;">
            <div class="dropdown-show">
                <?php foreach ($dropdown['items'] as $dropdownItem): ?>
                    <a class="dropdown-item" href="<?php echo $dropdownItem['link']; ?>">
                        <i class="<?php echo $dropdownItem['icon']; ?>"></i>
                        <span data-translate="<?php echo $dropdownItem['translate']; ?>" lang="th">
                            <?php echo $dropdownItem['text']; ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>




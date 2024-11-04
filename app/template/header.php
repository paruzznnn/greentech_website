<div id="background-blur"></div>

<?php
    $navbarItems = [
        // ['text' => 'Home', 'link' => 'index.php?tab=page_home'],
        ['text' => 'Home', 'link' => 'index.php'],
        [
            'text' => 'Origami Platforms',
            'link' => '#',
            'isDropdown' => true,
            'id' => 'dropdown1', // Dropdown ID for unique reference
        ],
        [
            'text' => 'Another Dropdown',
            'link' => '#',
            'isDropdown' => true,
            'id' => 'dropdown2', // Dropdown ID for second dropdown
        ],
        // ['text' => 'Origami Ai', 'link' => 'https://devrev.ai/pricing/plug', 'target' => '_blank'],
        ['text' => 'Origami AI', 'link' => 'origami_ai.php?tab=page_AI'],
        ['text' => 'Allable Cloud', 'link' => 'allable_cloud.php?tab=page_allable_cloud'],
        ['text' => 'News', 'link' => '#'],
        ['text' => 'About Us', 'link' => 'about.php?tab=page_about'],
        ['text' => 'Contact us', 'link' => 'contact.php?tab=page_contact'],
        ['text' => 'Pricing', 'link' => 'pricing.php?tab=page_pricing'],
    ];

    // Dropdown items array
    $dropdownItems = [
        [
            'id' => 'dropdown1',
            'items' => [
                ['text' => 'HRM', 'link' => '#subsection1'],
                ['text' => 'CRM', 'link' => '#subsection2'],
                ['text' => 'Project Management', 'link' => '#subsection3'],
                ['text' => 'Expense Management', 'link' => '#subsection4'],
                ['text' => 'iDoc', 'link' => '#subsection5'],
                ['text' => 'Event Management', 'link' => '#subsection6'],
                ['text' => 'Asset Management', 'link' => '#subsection7'],
                ['text' => 'Helpdesk Service', 'link' => '#subsection8'],
                ['text' => 'Learning Management System', 'link' => '#subsection8'],
            ]
        ],
        [
            'id' => 'dropdown2',
            'items' => [
                ['text' => 'Expense Management', 'link' => '#subsection4'],
                ['text' => 'iDoc', 'link' => '#subsection5'],
                ['text' => 'Event Management', 'link' => '#subsection6'],
            ]
        ],
    ];
?>

<?php
    // Array of image sources
    $images = [
        '../public/img/1730258479586.jpg',
        '../public/img/1730258513335.jpg',
        '../public/img/1730258519837.jpg',
        '../public/img/1AllableBannerSlideWebsite.png',
    ];

    // Initialize carousel items
    $carouselItems = '';
    foreach ($images as $index => $image) {
        // Check if the item is the first one to set the active class
        $activeClass = ($index === 0) ? 'active' : '';
        $carouselItems .= '
            <div class="carousel-item ' . $activeClass . '">
                <img src="' . $image . '" class="d-block w-100" alt="...">
            </div>';
    }
?>

<div class="header-top">

    <div class="container">
        <a href="#">
            <img class="logo" src="../public/img/logo-ALLABLE-06.png" alt="">
        </a>

        <div class="header-top-right">
            <!-- <a href="#"><i class="fas fa-ellipsis-v"></i></a> -->
        </div>
    </div>

</div>

<div id="navbar-menu" onmouseleave="closeAllDropdowns()">
    <div class="container">
        <div class="over-menu">
            <?php foreach ($navbarItems as $item): ?>
                <?php if (isset($item['isDropdown']) && $item['isDropdown']): ?>
                    <div class="dropdown">
                        <a 
                        class="dropbtn" 
                        onmouseenter="toggleDropdown('<?php echo $item['id']; ?>')"
                        >
                            <?php echo $item['text']; ?>
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
                        <?php echo $item['text']; ?>
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
                        <?php echo $dropdownItem['text']; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>


<div class="header-center">
    <div class="container">
        <div id="header-carousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">

            <div class="carousel-indicators">
                <?php foreach ($images as $index => $image): ?>
                    <button type="button" data-bs-target="#header-carousel" data-bs-slide-to="<?= $index ?>" 
                        class="<?= ($index === 0) ? 'active' : '' ?>" 
                        aria-current="<?= ($index === 0) ? 'true' : 'false' ?>" 
                        aria-label="Slide <?= $index + 1 ?>"></button>
                <?php endforeach; ?>
            </div>

            <div class="carousel-inner">
                <?= $carouselItems ?>
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#header-carousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#header-carousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>

        </div>
    </div>
</div>


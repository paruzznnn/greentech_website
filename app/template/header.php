<div id="background-blur"></div>

<div class="header-top">
    <a href="#">
        <img class="logo" src="../public/img/logo-ALLABLE-06.png" alt="">
    </a>

    <div class="header-top-right">
        <!-- <a href="#"><i class="fas fa-ellipsis-v"></i></a> -->
    </div>

</div>

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



<!-- <div class="header-center">
    <div class="container">
        <div id="header-carousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
            
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#header-carousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#header-carousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#header-carousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                <button type="button" data-bs-target="#header-carousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                <img src="../public/img/1730258479586.jpg" class="d-block w-100" alt="...">
                </div>
                <div class="carousel-item">
                <img src="../public/img/1730258513335.jpg" class="d-block w-100" alt="...">
                </div>
                <div class="carousel-item">
                <img src="../public/img/1730258519837.jpg" class="d-block w-100" alt="...">
                </div>
                <div class="carousel-item">
                    <a href="#" target="">
                        <img src="../public/img/1AllableBannerSlideWebsite.png" class="d-block w-100" alt="...">
                    </a>
                </div>
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
</div> -->

<!-- Navbar Menu -->
<!-- <div id="navbar-menu">
    <div class="container">
        <div class="over-menu">
            <a href="index.php?tab=page_home">Home</a>
            <div class="dropdown">
                <a class="dropbtn" onclick="toggleDropdown()">Origami Platforms 
                    <span class="dropdown-icon">
                        <i class="fas fa-caret-down"></i>
                    </span>
                </a>
            </div>
            <a href="https://devrev.ai/pricing/plug" target="_blank">Origami Ai</a>
            <a href="allable_cloud.php?tab=page_allable_cloud">Allable Cloud</a>
            <a href="#">News</a>
            <a href="about.php?tab=page_about">About Us</a>
            <a href="contact.php?tab=page_contact">Contact us</a>
            <a href="pricing.php?tab=page_pricing">Pricing</a>
        </div>
    </div>
    <div class="dropdown-content" id="dropdownContent">
        <a class="dropdown-item" href="#subsection1">HRM</a>
        <a class="dropdown-item" href="#subsection2">CRM</a>
        <a class="dropdown-item" href="#subsection3">Project Management</a>
        <a class="dropdown-item" href="#subsection4">Expense Management</a>
        <a class="dropdown-item" href="#subsection5">iDoc</a>
        <a class="dropdown-item" href="#subsection6">Event Management</a>
        <a class="dropdown-item" href="#subsection7">Asset Management</a>
        <a class="dropdown-item" href="#subsection8">Helpdesk Service</a>
        <a class="dropdown-item" href="#subsection9">Learning Management System</a>
    </div>
</div> -->




<?php
$navbarItems = [
    ['text' => 'Home', 'link' => 'index.php?tab=page_home'],
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
    ['text' => 'Origami Ai', 'link' => 'https://devrev.ai/pricing/plug', 'target' => '_blank'],
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

<!-- <div id="navbar-menu" onmouseleave="closeAllDropdowns()"> -->
<!-- onmouseenter="toggleDropdown('<?php echo $item['id']; ?>')" -->
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

<?php

$imagesItems = [
    '../public/img/1AllableBannerSlideWebsite.png',
    '../public/img/1730258479586.jpg',
    '../public/img/1730258513335.jpg',
    '../public/img/1730258519837.jpg',
];

$carouselItems = '';
foreach ($imagesItems as $index => $image) {
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
                <?php foreach ($imagesItems as $index => $image): ?>
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
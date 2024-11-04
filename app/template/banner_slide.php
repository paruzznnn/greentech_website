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
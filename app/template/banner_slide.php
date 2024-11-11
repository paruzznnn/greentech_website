<div class="banner-section">
    <div class="banner-container">
        <?php
        $imagesItems = [
            '../public/img/1AllableBannerSlideWebsite.png',
            '../public/img/1730258479586.jpg',
            '../public/img/1730258513335.jpg',
            '../public/img/1730258519837.jpg',
        ];

        foreach ($imagesItems as $index => $image):
        ?>
            <div class="banner-carousel-item <?= ($index === 0) ? 'active' : '' ?>">
                <img src="<?= $image ?>" alt="Banner Slide <?= $index + 1 ?>" class="banner-image" />
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Carousel Controls -->
    <button class="banner-control-prev" onclick="moveSlide(-1)">&#10094;</button>
    <button class="banner-control-next" onclick="moveSlide(1)">&#10095;</button>
    
    <!-- Carousel Indicators -->
    <div class="banner-indicators">
        <?php foreach ($imagesItems as $index => $image): ?>
            <span class="banner-pagination" onclick="goToSlide(<?= $index ?>)"></span>
        <?php endforeach; ?>
    </div>
</div>


<div id="navbar-news">
    <div class="container">
        <div class="news-ticker">
            <span class="text-ticker">
                <span class="blinking-icon"></span>
                Daily News
            </span>
            <marquee>
                    <div style="padding: 8px 0px 0px 0px; font-size: 18px;">
                    Allable จับมือ DevRev เปิดตัว AI และ SaaS 2.0 เสริมแกร่งธุรกิจครั้งแรกในไทย
                    </div>
            </marquee>
        </div>
    </div>
</div>


										
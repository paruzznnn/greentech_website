<?php
require_once('../lib/connect.php'); // หรือ path ที่ใช้เชื่อม DB

$sql = "SELECT * FROM banner ORDER BY id ASC";
$result = $conn->query($sql);

$imagesItems = [];
while ($row = $result->fetch_assoc()) {
    $imagesItems[] = $row['image_path'];
}
?>
<div class="banner-section">
    <div class="banner-container">
        <?php foreach ($imagesItems as $index => $image): ?>
            <div class="banner-carousel-item <?= ($index === 0) ? 'active' : '' ?>">
                <img src="<?= $image ?>" alt="Banner Slide <?= $index + 1 ?>" class="banner-image" />
            </div>
        <?php endforeach; ?>
    </div>

    <button class="banner-control-prev" onclick="moveSlide(-1)">&#10094;</button>
    <button class="banner-control-next" onclick="moveSlide(1)">&#10095;</button>

    <div class="banner-indicators">
        <?php foreach ($imagesItems as $index => $image): ?>
            <span class="banner-pagination" onclick="goToSlide(<?= $index ?>)"></span>
        <?php endforeach; ?>
    </div>
</div>

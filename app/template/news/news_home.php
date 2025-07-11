<?php
require_once(__DIR__ . '/../../../lib/connect.php');
global $conn;

$sql = "SELECT 
            dn.news_id, 
            dn.subject_news, 
            dn.description_news,
            dn.content_news, 
            dn.date_create, 
            GROUP_CONCAT(dnc.file_name) AS file_name,
            GROUP_CONCAT(dnc.api_path) AS pic_path
        FROM 
            dn_news dn
        LEFT JOIN 
            dn_news_doc dnc ON dn.news_id = dnc.news_id
        WHERE 
            dn.del = '0' AND
            dnc.del = '0' AND
            dnc.status = '1'
        GROUP BY dn.news_id 
        ORDER BY dn.date_create DESC
        LIMIT 5";

$result = $conn->query($sql);
$boxesNews = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {

        $content = $row['content_news'];
        $iframeSrc = null;
        if (preg_match('/<iframe.*?src=["\'](.*?)["\'].*?>/i', $content, $matches)) {
            $iframeSrc = isset($matches[1]) ? explode(',', $matches[1]) : null;
        }

        $paths = explode(',', $row['pic_path']);
        $files = explode(',', $row['file_name']);
        $iframe = isset($iframeSrc[0]) ? $iframeSrc[0] : null;

        $boxesNews[] = [
            'id' => $row['news_id'],
            'image' =>  $paths[0],
            'title' => $row['subject_news'],
            'description' => $row['description_news'],
            'iframe' => $iframe
        ];
    }
}
?>

<style>
/* Basic card styles, largely kept from previous iterations */
.card {
    display: flex;
    flex-direction: column;
    height: 100%;
    border: 1px solid #ddd;
    border-radius: 6px;
    overflow: hidden;
    background-color: #fff;
}

.card-img-top {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.card-body {
    padding: 10px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

/* Specific styles for the main news description (left side) */
.main-news-description {
    font-size: 0.9rem;
    color: #555;
    /* You can add line clamping here if needed for the main news as well */
}

/* ✅ Style to limit bold text (title) on the right side to 2 lines */
.sub-news-title {
    display: -webkit-box;
    -webkit-line-clamp: 3; /* Limit to 2 lines */
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    font-weight: bold; /* Ensure it stays bold */
    font-size: 0.85rem; /* Match the current font size */
    margin-bottom: 0; /* Remove bottom margin to save space */
    line-height: 1.2em; /* Keep consistent line height */
    color: #333; /* Darker color as requested */
}

/* Ensure equal height for the main columns */
.row.g-2.d-flex.align-items-stretch {
    height: auto; /* Allow height to adjust */
}

/* Ensure sub-columns in the right section are of equal height */
.col-md-4 > .row.row-cols-1.row-cols-md-2.g-2.h-100 {
    display: flex;
    flex-wrap: wrap;
    align-content: stretch; /* Stretches wrapped items to fill container height */
}

.col.d-flex {
    display: flex; /* Makes each column a flex container */
}

.col.d-flex .card {
    flex-grow: 1; /* Makes the card grow to fill available height */
}

/* Styles for carousel caption if applicable */
.carousel-caption {
    background: rgba(0, 0, 0, 0.5);
    padding: 10px;
    border-radius: 5px;
}

/* Adjust main image height as seen in the new picture */
#mainNewsCarousel .carousel-item img {
    height: 350px; /* Adjust this value as needed to control the main image height */
    max-height: 350px; /* Ensure it doesn't exceed this height */
    width: 100%;
    object-fit: cover;
    border-radius: 6px;
}
</style>

<script>
// No changes needed for the JS functions based on the current request.
// Keeping them commented out/as-is since they are not the focus of this fix.
// function scrollNews(direction) { /* ... */ }
// function startAutoScroll() { /* ... */ }
// window.addEventListener('DOMContentLoaded', startAutoScroll);
</script>

<?php if (!empty($boxesNews)): ?>
    <div class="row g-2 d-flex align-items-stretch">
        <div class="col-md-8 d-flex flex-column">
            <div class="card h-100">
                <div id="mainNewsCarousel" class="carousel slide h-100 d-flex flex-column" data-bs-ride="carousel">
                    <div class="carousel-inner flex-grow-1">
                        <?php foreach (array_slice($boxesNews, 0, 4) as $i => $box): ?>
                            <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                                <a href="news_detail.php?id=<?= urlencode(base64_encode($box['id'])) ?>" class="text-decoration-none text-dark">
                                    <img src="<?= htmlspecialchars($box['image']) ?>" class="d-block w-100" style="border-radius: 6px; height: 350px; object-fit: cover;">
                                </a>
                                <div class="p-3 bg-light">
                                    <h5 class="mb-1"><?= htmlspecialchars($box['title']) ?></h5>
                                    <p class="mb-0 text-muted main-news-description"><?= htmlspecialchars($box['description']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <button class="carousel-control-prev" type="button" data-bs-target="#mainNewsCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#mainNewsCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-4 d-flex flex-column">
            <div class="row row-cols-1 row-cols-md-2 g-2 h-100">
                <?php foreach (array_slice($boxesNews, 1) as $box): ?>
                    <div class="col d-flex">
                        <div class="card h-100 w-100">
                            <a href="news_detail.php?id=<?= urlencode(base64_encode($box['id'])) ?>" class="text-decoration-none text-dark">
                                <img src="<?= htmlspecialchars($box['image']) ?>" class="card-img-top" style="height: 150px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <h6 class="sub-news-title"><?= htmlspecialchars($box['title']) ?></h6>
                                    </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
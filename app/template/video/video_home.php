<?php
require_once('../lib/connect.php');
global $conn;

$sql = "SELECT youtube_id, title, description FROM videos WHERE show_on_homepage = 1 ORDER BY created_at DESC LIMIT 4";
$result = $conn->query($sql);
?>
<script>
function extractYouTubeID(url) {
    const regex = /(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&]+)/;
    const match = url.match(regex);
    return match ? match[1] : '';
}

document.addEventListener('DOMContentLoaded', function () {
    const inputFullLink = document.getElementById('youtube_full_link');
    const inputIdOnly = document.getElementById('youtube_id');

    inputFullLink.addEventListener('input', function () {
        const id = extractYouTubeID(inputFullLink.value);
        inputIdOnly.value = id;
    });
});
</script>
<div class="row">
<?php while ($row = $result->fetch_assoc()): ?>
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="ratio ratio-16x9">
                <iframe 
                    src="https://www.youtube.com/embed/<?= htmlspecialchars($row['youtube_id']) ?>"
                    title="<?= htmlspecialchars($row['title']) ?>"
                    frameborder="0"
                    allowfullscreen></iframe>
            </div>
            <div class="card-body">
                <h6 class="card-title"><?= htmlspecialchars($row['title']) ?></h6>
                <p class="card-text text-truncate"><?= htmlspecialchars($row['description']) ?></p>
            </div>
        </div>
    </div>
<?php endwhile; ?>
</div>

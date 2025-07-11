<?php
require_once(__DIR__ . '/../../../lib/connect.php');
global $conn;

$sql = "SELECT 
            dn.project_id, 
            dn.subject_project, 
            dn.description_project,
            dn.content_project, 
            dn.date_create, 
            GROUP_CONCAT(dnc.file_name) AS file_name,
            GROUP_CONCAT(dnc.api_path) AS pic_path
        FROM 
            dn_project dn
        LEFT JOIN 
            dn_project_doc dnc ON dn.project_id = dnc.project_id
        WHERE 
            dn.del = '0' AND
            dnc.del = '0' AND
            dnc.status = '1'
        GROUP BY dn.project_id 
        ORDER BY dn.date_create DESC
        LIMIT 4";

$result = $conn->query($sql);
$boxesproject = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {

        $content = $row['content_project'];
        $iframeSrc = null;
        if (preg_match('/<iframe.*?src=["\'](.*?)["\'].*?>/i', $content, $matches)) {
            $iframeSrc = isset($matches[1]) ? explode(',', $matches[1]) : null;
        }

        $paths = explode(',', $row['pic_path']);
        $files = explode(',', $row['file_name']);
        $iframe = isset($iframeSrc[0]) ? $iframeSrc[0] : null;

        $boxesproject[] = [
            'id' => $row['project_id'],
            'image' =>  $paths[0],
            'title' => $row['subject_project'],
            'description' => $row['description_project'],
            'iframe' => $iframe
        ];
    }
}
?>

<div class="row">
    <!-- ซ้าย: Project -->
    <div class="col-md-8">
        <div class="row row-cols-1 row-cols-md-2 g-3">
            <?php foreach ($boxesproject as $box): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <a href="project_detail.php?id=<?= urlencode(base64_encode($box['id'])) ?>" class="text-decoration-none text-dark">
                            <?php if (empty($box['image'])): ?>
                                <iframe frameborder="0" src="<?= $box['iframe'] ?>" width="100%" height="200px" class="note-video-clip"></iframe>
                            <?php else: ?>
                                <img src="<?= $box['image'] ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?= htmlspecialchars($box['title']) ?>">
                            <?php endif; ?>
                            <div class="card-body px-2 pt-3 pb-2">
                                <h6 class="card-title fw-semibold mb-1" style="font-size: 0.95rem;"><?= htmlspecialchars($box['title']) ?></h6>
                                <p class="card-text text-muted" style="font-size: 0.85rem;"><?= htmlspecialchars($box['description']) ?></p>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ขวา: Facebook Page -->
    <div class="col-md-4 ps-3">
        <div class="page-plugin mt-3">
            <iframe src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2Ftrandaracoustic%2F&tabs=timeline&width=340&height=500&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true&appId"
                width="100%" height="500" style="border:none;overflow:hidden" scrolling="no" frameborder="0"
                allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
        </div>
    </div>
</div>

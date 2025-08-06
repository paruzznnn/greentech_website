<?php 
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

include '../../../lib/connect.php';
include '../../../lib/base_directory.php';
include '../check_permission.php';

// ตรวจสอบว่าได้รับค่า Blog_id หรือไม่
if (!isset($_POST['Blog_id'])) {
    echo "<div class='alert alert-danger'>ไม่พบข้อมูลข่าวที่ต้องการแก้ไข</div>";
    exit;
}

$decodedId = $_POST['Blog_id'];

// เพิ่มส่วนนี้: ดึงข้อมูลสินค้าทั้งหมดจากตาราง dn_project
$sql_all_projects = "SELECT project_id, subject_project FROM dn_project WHERE del = 0 ORDER BY subject_project ASC";
$result_all_projects = $conn->query($sql_all_projects);
$all_projects = [];
if ($result_all_projects->num_rows > 0) {
    while ($row = $result_all_projects->fetch_assoc()) {
        $all_projects[] = $row;
    }
}

// เพิ่มส่วนนี้: ดึงข้อมูลสินค้าที่เกี่ยวข้องเดิม
$related_project_ids = [];
$stmt_related_projects = $conn->prepare("SELECT project_id FROM dn_blog_project WHERE Blog_id = ?");
if ($stmt_related_projects) {
    $stmt_related_projects->bind_param("i", $decodedId);
    $stmt_related_projects->execute();
    $result_related_projects = $stmt_related_projects->get_result();
    while ($row = $result_related_projects->fetch_assoc()) {
        $related_project_ids[] = $row['project_id'];
    }
    $stmt_related_projects->close();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit blog</title>

    <link rel="icon" type="image/x-icon" href="../../../public/img/q-removebg-preview1.png">
    <link href="../../../inc/jquery/css/jquery-ui.css" rel="stylesheet">
    <script src="../../../inc/jquery/js/jquery-3.6.0.min.js"></script>
    <script src="../../../inc/jquery/js/jquery-ui.min.js"></script>

    <link href="../../../inc/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="../../../inc/bootstrap/js/bootstrap.min.js"></script>
    <script src="../../../inc/bootstrap/js/bootstrap.bundle.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/fontawesome5-fullcss@1.1.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css" crossorigin="anonymous" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">

    <link href="../../../inc/sweetalert2/css/sweetalert2.min.css" rel="stylesheet">
    <script src="../../../inc/sweetalert2/js/sweetalert2.all.min.js"></script>

    <link href="../../../inc/select2/css/select2.min.css" rel="stylesheet">
    <script src="../../../inc/select2/js/select2.min.js"></script>

    <link href="https://cdn.datatables.net/v/dt/dt-2.1.4/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/dt/dt-2.1.4/datatables.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-iconpicker/1.10.0/css/bootstrap-iconpicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-iconpicker/1.10.0/js/bootstrap-iconpicker.bundle.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>

    <link href='../css/index_.css?v=<?php echo time(); ?>' rel='stylesheet'>

    <style>
        .note-editable {
            color: #424242;
            font-size: 16px;
            line-height: 1.5;
        }
        .box-content p {
            color: #424242;
        }
        .responsive-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        .responsive-button-container {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 10px;
        }
        @media (max-width: 768px) {
            .responsive-grid {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 480px) {
            .responsive-button-container div {
                text-align: center;
            }
        }
        .note-toolbar {
            position: sticky !important;
            top: 70px !important;
            z-index: 1 !important;
        }
    </style>
</head>

<?php include '../template/header.php' ?>

<body>
    <div class="content-sticky" id="">
        <div class="container-fluid">
            <div class="box-content">
                <div class="row">
                    <h4 class="line-ref mb-3">
                        <i class="far fa-newspaper"></i> Edit blog
                    </h4>
                    <?php
$stmt = $conn->prepare("
    SELECT 
    dn.Blog_id, 
    dn.subject_blog, 
    dn.description_blog,
    dn.content_blog, 
    dn.date_create, 
    GROUP_CONCAT(dnc.file_name) AS file_name,
    GROUP_CONCAT(dnc.api_path) AS pic_path,
    MAX(dnc.status) AS status
FROM dn_blog dn
LEFT JOIN dn_blog_doc dnc ON dn.Blog_id = dnc.Blog_id
WHERE dn.Blog_id = ?
GROUP BY dn.Blog_id
");

if ($stmt === false) {
    die('❌ SQL Prepare failed: ' . $conn->error);
}

$stmt->bind_param('i', $decodedId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $content = $row['content_blog'];
        $paths = explode(',', $row['pic_path']);
        $files = explode(',', $row['file_name']);

        foreach ($files as $index => $file) {
            $pattern = '/<img[^>]+data-filename="' . preg_quote($file, '/') . '"[^>]*>/i';
            if (preg_match($pattern, $content, $matches)) {
                $new_src = $paths[$index];
                $new_img_tag = preg_replace('/(<img[^>]+)(src="[^"]*")/i', '$1 src="' . $new_src . '"', $matches[0]);
                $content = str_replace($matches[0], $new_img_tag, $content);
            }
        }

        $previewImageSrc = !empty($paths[0]) ? htmlspecialchars($paths[0]) : '';
        $content = mb_convert_encoding($content, 'UTF-8', 'auto');

        echo "
        <form id='formBlog_edit' enctype='multipart/form-data'>
            <input type='hidden' class='form-control' id='Blog_id' name='Blog_id' value='" . htmlspecialchars($row['Blog_id']) . "'>
            <div class='row'>
                <div class='col-md-4'>
                    <div style='margin: 10px;'>
                        <label><span>Cover photo</span>:</label>
                        <div><span>ขนาดรูปภาพที่เหมาะสม width: 350px และ height: 250px</span></div>
                        <div id='previewContainer' class='previewContainer'>
                            <img id='previewImage' src='{$previewImageSrc}' alt='Image Preview' style='max-width: 100%;'>
                        </div>
                    </div>
                    <div style='margin: 10px;'>
                        <input type='file' class='form-control' id='fileInput' name='fileInput[]'>
                    </div>
                    <div style='margin: 10px;'>
                        <label><span>Subject</span>:</label>
                        <input type='text' class='form-control' id='Blog_subject' name='Blog_subject' value='" . htmlspecialchars($row['subject_blog']) . "'>
                    </div>
                    <div style='margin: 10px;'>
                        <label><span>Description</span>:</label>
                        <textarea class='form-control' id='Blog_description' name='Blog_description'>" . htmlspecialchars($row['description_blog']) . "</textarea>
                    </div>

                    <div style='margin: 10px;'>
                        <label>สินค้าที่เกี่ยวข้อง (เลือกได้หลายชิ้น)</label>
                        <select class='form-control select2' multiple='multiple' name='related_projects[]' style='width: 100%;'>
                            ";
                            foreach ($all_projects as $project):
                                $selected = in_array($project['project_id'], $related_project_ids) ? 'selected' : '';
                                echo "<option value='" . htmlspecialchars($project['project_id']) . "' " . $selected . ">" . htmlspecialchars($project['subject_project']) . "</option>";
                            endforeach;
                        echo "
                        </select>
                    </div>
                    <div style='margin: 10px; text-align: end;'>
                        <button type='button' id='submitEditblog' class='btn btn-success'>
                            <i class='fas fa-save'></i> Save blog
                        </button>
                    </div>
                </div>
                <div class='col-md-8'>
                    <div style='margin: 10px; text-align: end;'>
                        <button type='button' id='backToprojectList' class='btn btn-secondary'> 
                            <i class='fas fa-arrow-left'></i> Back 
                        </button>
                    </div>
                    <div style='margin: 10px;'>
                        <label><span>Content</span>:</label>
                        <textarea class='form-control summernote' id='summernote_update' name='Blog_content'>" . htmlspecialchars($content) . "</textarea>
                    </div>
                </div>
            </div>
        </form>
        ";
    }
} else {
    echo "<div class='alert alert-warning'>ไม่มีข้อมูลข่าว</div>";
}
$stmt->close();
?>

<script>
document.getElementById('fileInput').addEventListener('change', function(e) {
    const container = document.getElementById('previewContainer');
    container.innerHTML = ''; // clear preview
    const files = e.target.files;
    if (files.length > 0) {
        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(evt) {
                const img = document.createElement('img');
                img.src = evt.target.result;
                img.style.maxWidth = '100%';
                img.style.marginBottom = '10px';
                container.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    }
});
</script>

                </div>
            </div>
        </div>
    </div>

    <script src='../js/index_.js?v=<?php echo time(); ?>'></script>
    <script src='js/Blog_.js?v=<?php echo time(); ?>'></script>

    <script>
    $(document).ready(function() {
        // Init Select2
        $('.select2').select2({
            placeholder: "เลือกสินค้าที่เกี่ยวข้อง",
            allowClear: true
        });

        // Summernote init
        $('#summernote_update').summernote({
            placeholder: 'กรอกเนื้อหาโปรเจกต์',
            tabsize: 2,
            height: 300,
            callbacks: {
                // ... (your existing callbacks) ...
            }
        });
    });
    </script>
</body>
</html>
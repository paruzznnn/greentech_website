<?php 
ini_set('display_errors', 1); // เปิดใช้งานการแสดงข้อผิดพลาด
ini_set('display_startup_errors', 1); // เปิดใช้งานการแสดงข้อผิดพลาดตอนเริ่มต้น
error_reporting(E_ALL); // รายงานข้อผิดพลาดทั้งหมด

include '../../../lib/connect.php';
include '../../../lib/base_directory.php';
include '../check_permission.php';

// ตรวจสอบว่าได้รับค่า shop_id หรือไม่
if (!isset($_POST['shop_id'])) {
    echo "<div class='alert alert-danger'>ไม่พบข้อมูลข่าวที่ต้องการแก้ไข</div>";
    exit;
}

$decodedId = $_POST['shop_id'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit shop</title>

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
                        <i class="far fa-newspaper"></i> Edit shop
                    </h4>
                    <?php
$stmt = $conn->prepare("
    SELECT 
    dn.shop_id, 
    dn.subject_shop, 
    dn.description_shop,
    dn.content_shop, 
    dn.date_create, 
    dn.group_id, -- เพิ่ม group_id เข้ามา
    GROUP_CONCAT(dnc.file_name) AS file_name,
    GROUP_CONCAT(dnc.api_path) AS pic_path,
    MAX(dnc.status) AS status
FROM dn_shop dn
LEFT JOIN dn_shop_doc dnc ON dn.shop_id = dnc.shop_id
WHERE dn.shop_id = ?
GROUP BY dn.shop_id
");

if ($stmt === false) {
    die('❌ SQL Prepare failed: ' . $conn->error);
}

$stmt->bind_param('i', $decodedId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // เตรียม options สำหรับกลุ่มแม่
    $mainGroupQuery = $conn->query("SELECT group_id, group_name FROM dn_shop_groups WHERE parent_group_id IS NULL ORDER BY group_name ASC");
    $mainGroupOptions = '';
    while ($group = $mainGroupQuery->fetch_assoc()) {
        $mainGroupOptions .= "<option value='{$group['group_id']}'>{$group['group_name']}</option>";
    }

    while ($row = $result->fetch_assoc()) {
        $current_group_id = $row['group_id']; // กลุ่มที่ถูกเลือกในปัจจุบัน

        // ตรวจสอบว่า group_id ปัจจุบันเป็นกลุ่มแม่หรือกลุ่มย่อย
        $groupInfoQuery = $conn->prepare("SELECT group_id, parent_group_id FROM dn_shop_groups WHERE group_id = ?");
        $groupInfoQuery->bind_param("i", $current_group_id);
        $groupInfoQuery->execute();
        $groupResult = $groupInfoQuery->get_result();
        $groupInfo = $groupResult->fetch_assoc();

        $mainGroupSelected = null;
        $subGroupSelected = null;

        if ($groupInfo['parent_group_id'] !== null) {
            // เป็นกลุ่มย่อย
            $mainGroupSelected = $groupInfo['parent_group_id'];
            $subGroupSelected = $groupInfo['group_id'];
        } else {
            // เป็นกลุ่มแม่
            $mainGroupSelected = $groupInfo['group_id'];
        }

        $content = $row['content_shop'];
        $paths = explode(',', $row['pic_path']);
        $files = explode(',', $row['file_name']);

        // แทนที่ src ของรูปภาพใน content ด้วย api_path ที่ถูกต้อง
        foreach ($files as $index => $file) {
            $pattern = '/<img[^>]+data-filename="' . preg_quote($file, '/') . '"[^>]*>/i';
            if (preg_match($pattern, $content, $matches)) {
                $new_src = $paths[$index] ?? ''; // ตรวจสอบว่ามี path อยู่
                if (!empty($new_src)) {
                    $new_img_tag = preg_replace('/(<img[^>]+)(src="[^"]*")/i', '$1 src="' . $new_src . '"', $matches[0]);
                    $content = str_replace($matches[0], $new_img_tag, $content);
                }
            }
        }
        
        $previewImageSrc = !empty($paths[0]) ? htmlspecialchars($paths[0]) : '';
        $content = mb_convert_encoding($content, 'UTF-8', 'auto');

        echo "
        <form id='formshop_edit' enctype='multipart/form-data'>
            <input type='hidden' class='form-control' id='shop_id' name='shop_id' value='" . htmlspecialchars($row['shop_id']) . "'>
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
                        <input type='file' class='form-control' id='fileInput' name='fileInput'> </div>
                    <div style='margin: 10px;'>
                        <label><span>Subject</span>:</label>
                        <input type='text' class='form-control' id='shop_subject' name='shop_subject' value='" . htmlspecialchars($row['subject_shop']) . "'>
                    </div>
                    <div style='margin: 10px;'>
                        <label><span>Description</span>:</label>
                        <textarea class='form-control' id='shop_description' name='shop_description'>" . htmlspecialchars($row['description_shop']) . "</textarea>
                    </div>
                    <div style='margin: 10px;'>
                        <label><span>กลุ่มแม่</span>:</label>
                        <select id='main_group_select' class='form-control'>
                            <option value=''>-- เลือกกลุ่มแม่ --</option>
                            "; // ปิด PHP เพื่อใส่ mainGroupOptions
                                echo $mainGroupOptions;
                            echo "
                        </select>
                    </div>
                    <div style='margin: 10px;'>
                        <label><span>กลุ่มย่อย</span>:</label>
                        <select id='sub_group_select' name='group_id' class='form-control'>
                            <option value=''>-- เลือกกลุ่มย่อย --</option>
                        </select>
                    </div>
                    <div style='margin: 10px; text-align: end;'>
                        <button type='button' id='submitEditshop' class='btn btn-success'>
                            <i class='fas fa-save'></i> Save shop
                        </button>
                    </div>
                </div>
                <div class='col-md-8'>
                    <div style='margin: 10px;'>
                        <label><span>Content</span>:</label>
                        <textarea class='form-control summernote' id='summernote_update' name='shop_content'>" . htmlspecialchars($content) . "</textarea>
                    </div>
                </div>
            </div>
        </form>
        <script>
            // Set selected values for dropdowns after they are rendered
            $(document).ready(function() {
                var mainGroupSelected = " . json_encode($mainGroupSelected) . ";
                var subGroupSelected = " . json_encode($subGroupSelected) . ";

                if (mainGroupSelected) {
                    $('#main_group_select').val(mainGroupSelected).trigger('change');
                    // setTimeout to allow AJAX to complete and populate sub-group options
                    setTimeout(function() {
                        if (subGroupSelected) {
                            $('#sub_group_select').val(subGroupSelected);
                        }
                    }, 500); // อาจจะต้องปรับเวลาให้เหมาะสม
                }
            });
        </script>
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
$('#main_group_select').on('change', function() {
    var mainGroupId = $(this).val();
    if (!mainGroupId) {
        $('#sub_group_select').html('<option value="">-- เลือกกลุ่มย่อย --</option>');
        return;
    }

    $.ajax({
        url: 'actions/get_sub_groups.php',
        type: 'POST',
        data: { main_group_id: mainGroupId },
        success: function(response) {
            $('#sub_group_select').html(response);
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
            $('#sub_group_select').html('<option value="">-- เกิดข้อผิดพลาด --</option>');
        }
    });
});

</script>

                </div>
            </div>
        </div>
    </div>

    <script src='../js/index_.js?v=<?php echo time(); ?>'></script>
    <script src='js/shop_.js?v=<?php echo time(); ?>'></script>
</body>
</html>
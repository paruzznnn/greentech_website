<?php
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

        .nav-link.active {
            font-weight: bold;
            border-bottom: 2px solid #007bff;
        }
        
        .flag-icon {
            width: 4px; /* ปรับขนาดธงให้เล็กลง */
            margin-right: 8px;
        }
              /* วางโค้ด CSS นี้ไว้ในไฟล์ .css ของคุณหรือในแท็ก <style> */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.7); /* พื้นหลังโปร่งแสง */
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3; /* สีเทาอ่อน */
            border-top: 5px solid #3498db; /* สีน้ำเงิน */
            border-radius: 50%;
            animation: spin 1s linear infinite; /* ทำให้หมุนตลอด */
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
// ดึงข้อมูลหลักของ shop และดึงข้อมูลรูปภาพแยกออกมา
$stmt = $conn->prepare("
    SELECT
        dn.shop_id,
        dn.subject_shop,
        dn.description_shop,
        dn.content_shop,
        dn.date_create,
        dn.group_id,
        dn.subject_shop_en,
        dn.description_shop_en,
        dn.content_shop_en
    FROM dn_shop dn
    WHERE dn.shop_id = ?
");

if ($stmt === false) {
    die('❌ SQL Prepare failed: ' . $conn->error);
}

$stmt->bind_param('i', $decodedId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $content_th = $row['content_shop'];
    $content_en = $row['content_shop_en'];
    $current_group_id = $row['group_id'];

    // ดึงข้อมูลรูปภาพทั้งหมดที่เกี่ยวข้องกับ shop_id นี้
    $stmt_pics = $conn->prepare("SELECT file_name, api_path, status FROM dn_shop_doc WHERE shop_id = ? AND del = 0 ORDER BY status DESC, id ASC");
    if ($stmt_pics === false) {
        die('❌ SQL Prepare for images failed: ' . $conn->error);
    }
    $stmt_pics->bind_param('i', $decodedId);
    $stmt_pics->execute();
    $pics_result = $stmt_pics->get_result();

    $pic_data = [];
    $previewImageSrc = '';

    while ($pic_row = $pics_result->fetch_assoc()) {
        if ($pic_row['status'] == 1) { // นี่คือ Cover Photo
            $previewImageSrc = htmlspecialchars($pic_row['api_path']);
        } else { // รูปภาพใน Content
            $pic_data[htmlspecialchars($pic_row['file_name'])] = htmlspecialchars($pic_row['api_path']);
        }
    }
    $stmt_pics->close();

    // แทนที่ src ของรูปภาพใน content ภาษาไทยด้วย api_path ที่ถูกต้องจาก $pic_data
    $dom_th = new DOMDocument();
    libxml_use_internal_errors(true);
    $source_th = !empty($content_th) ? mb_convert_encoding($content_th, 'HTML-ENTITIES', 'UTF-8') : '<div></div>';
    $dom_th->loadHTML($source_th, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    $images_th = $dom_th->getElementsByTagName('img');
    foreach ($images_th as $img) {
        $data_filename = $img->getAttribute('data-filename');
        if (!empty($data_filename) && isset($pic_data[$data_filename])) {
            $img->setAttribute('src', $pic_data[$data_filename]);
        }
    }
    $content_th_with_correct_paths = $dom_th->saveHTML();

    // แทนที่ src ของรูปภาพใน content ภาษาอังกฤษด้วย api_path ที่ถูกต้องจาก $pic_data
    $dom_en = new DOMDocument();
    libxml_use_internal_errors(true);
    $source_en = !empty($content_en) ? mb_convert_encoding($content_en, 'HTML-ENTITIES', 'UTF-8') : '<div></div>';
    $dom_en->loadHTML($source_en, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    $images_en = $dom_en->getElementsByTagName('img');
    foreach ($images_en as $img) {
        $data_filename = $img->getAttribute('data-filename');
        if (!empty($data_filename) && isset($pic_data[$data_filename])) {
            $img->setAttribute('src', $pic_data[$data_filename]);
        }
    }
    $content_en_with_correct_paths = $dom_en->saveHTML();

    // ดึงข้อมูลกลุ่มทั้งหมดเพื่อใช้ในการแสดงผล
    $mainGroupQuery = $conn->query("SELECT group_id, group_name FROM dn_shop_groups WHERE parent_group_id IS NULL ORDER BY group_name ASC");
    $mainGroupOptions = '';
    while ($group = $mainGroupQuery->fetch_assoc()) {
        $mainGroupOptions .= "<option value='{$group['group_id']}'>{$group['group_name']}</option>";
    }

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

    echo "
    <form id='formshop_edit' enctype='multipart/form-data'>
        <input type='hidden' class='form-control' id='shop_id' name='shop_id' value='" . htmlspecialchars($row['shop_id']) . "'>
        <div class='row' style='flex-direction: column;'>
        
            <div class=''>
                <div style='margin: 10px; text-align: end;'>
                    <button type='button' id='backToShopList' class='btn btn-secondary'> 
                        <i class='fas fa-arrow-left'></i> Back 
                    </button>
                </div>
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
                
                
            </div>
            <div class=''>
                

                <div class='card mb-4'>
                    <div class='card-header p-0'>
                        <ul class='nav nav-tabs' id='languageTabs' role='tablist'>
                            <li class='nav-item' role='presentation'>
                                <button class='nav-link active' id='th-tab' data-bs-toggle='tab' data-bs-target='#th' type='button' role='tab' aria-controls='th' aria-selected='true'>
                                    <img src='https://flagcdn.com/w320/th.png' alt='Thai Flag' class='flag-icon' style=' width: 36px; 
            margin-right: 8px;'>Thai
                                </button>
                            </li>
                            <li class='nav-item' role='presentation'>
                                <button class='nav-link' id='en-tab' data-bs-toggle='tab' data-bs-target='#en' type='button' role='tab' aria-controls='en' aria-selected='false'>
                                    <img src='https://flagcdn.com/w320/gb.png' alt='English Flag' class='flag-icon' style=' width: 36px; 
            margin-right: 8px;'>English
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class='card-body'>
                        <div class='tab-content' id='languageTabsContent'>
                            <div class='tab-pane fade show active' id='th' role='tabpanel' aria-labelledby='th-tab'>
                                <div style='margin: 10px;'>
                                    <label><span>Subject (TH)</span>:</label>
                                    <input type='text' class='form-control' id='shop_subject' name='shop_subject' value='" . htmlspecialchars($row['subject_shop']) . "'>
                                </div>
                                <div style='margin: 10px;'>
                                    <label><span>Description (TH)</span>:</label>
                                    <textarea class='form-control' id='shop_description' name='shop_description'>" . htmlspecialchars($row['description_shop']) . "</textarea>
                                </div>
                                <div style='margin: 10px;'>
                                    <label><span>Content (TH)</span>:</label>
                                    <textarea class='form-control summernote' id='summernote_update' name='shop_content'>" . $content_th_with_correct_paths . "</textarea>
                                </div>
                            </div>
                            <div class='tab-pane fade' id='en' role='tabpanel' aria-labelledby='en-tab'>
                                <div style='display: flex; justify-content: flex-end; margin-bottom: 10px;'>
                                     <button type='button' id='copyFromThai' class='btn btn-info btn-sm float-end mb-2'>Origami Ai Translate</button>
                                                        <div id='loadingIndicator' class='loading-overlay' style='display: none;'>
                                                            <div class='loading-spinner'></div>
                                                        </div>
                                </div>
                                <div style='margin: 10px;'>
                                    
                                    <label><span>Subject (EN)</span>:</label>
                                    <input type='text' class='form-control' id='shop_subject_en' name='shop_subject_en' value='" . htmlspecialchars($row['subject_shop_en']) . "'>
                                </div>
                                <div style='margin: 10px;'>
                                    <label><span>Description (EN)</span>:</label>
                                    <textarea class='form-control' id='shop_description_en' name='shop_description_en'>" . htmlspecialchars($row['description_shop_en']) . "</textarea>
                                </div>
                                <div style='margin: 10px;'>
                                    <label><span>Content (EN)</span>:</label>
                                    <textarea class='form-control summernote' id='summernote_update_en' name='shop_content_en'>" . $content_en_with_correct_paths . "</textarea>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
                </div>
                <div style='margin: 10px; text-align: end;'>
                    <button type='button' id='submitEditshop' class='btn btn-success'>
                        <i class='fas fa-save'></i> Save shop
                    </button>
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
                $('#main_group_select').val(mainGroupSelected);
                $('#main_group_select').trigger('change');
            }
        });
    </script>
    ";
} else {
    echo "<div class='alert alert-warning'>ไม่มีข้อมูลข่าว</div>";
}
$stmt->close();
?>

<script>
    $(document).ready(function() {
        // Initial load for TH content
        $('#summernote_update').summernote({
            height: 600,
            callbacks: {
                onImageUpload: function(files) {
                    uploadFile(files[0], $(this));
                },
                onMediaDelete: function(target) {
                    deleteFile(target);
                }
            }
        });

        // Event listener for tab switch
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            var target = $(e.target).attr("data-bs-target"); // activated tab
            if (target === '#en') {
                if ($('#summernote_update_en').data('summernote')) {
                    $('#summernote_update_en').summernote('destroy');
                }
                $('#summernote_update_en').summernote({
                    height: 600,
                    callbacks: {
                        onImageUpload: function(files) {
                            uploadFile(files[0], $(this));
                        },
                        onMediaDelete: function(target) {
                            deleteFile(target);
                        }
                    }
                });
            }
        });

        document.getElementById('fileInput').addEventListener('change', function(e) {
            const container = document.getElementById('previewContainer');
            container.innerHTML = '';
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
        
        var subGroupSelected = <?php echo json_encode($subGroupSelected); ?>;

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
                    // เมื่อโหลดกลุ่มย่อยเสร็จ ให้เลือกค่าที่ถูกต้อง
                    if (subGroupSelected) {
                        $('#sub_group_select').val(subGroupSelected);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                    $('#sub_group_select').html('<option value="">-- เกิดข้อผิดพลาด --</option>');
                }
            });
        });

        // Initial trigger to load sub-groups if main group is already selected
        var mainGroupSelected = <?php echo json_encode($mainGroupSelected); ?>;
        if (mainGroupSelected) {
            $('#main_group_select').val(mainGroupSelected).trigger('change');
        }

        // --- เพิ่มโค้ดสำหรับปุ่ม Copy from Thai ---
        // $('#copyFromThai').on('click', function() {
        //     // ดึงค่าจากฟิลด์ภาษาไทย
        //     var subjectThai = $('#shop_subject').val();
        //     var descriptionThai = $('#shop_description').val();
        //     var contentThai = $('#summernote_update').summernote('code');

        //     // กำหนดค่าให้ฟิลด์ภาษาอังกฤษ
        //     $('#shop_subject_en').val(subjectThai);
        //     $('#shop_description_en').val(descriptionThai);
        //     $('#summernote_update_en').summernote('code', contentThai);
        // });

        $('#copyFromThai').on('click', function () {
                // 1. แสดง Loading Indicator
                $('#loadingIndicator').show(); // ให้โชว์ loading animation

                // ดึงค่าจากฟอร์มภาษาไทย
                var thaiSubject = $('#shop_subject').val();
                var thaiDescription = $('#shop_description').val();
                var thaiContent = $('#summernote_update').summernote('code');

                // สร้าง Object สำหรับข้อมูลที่จะส่งไป
                const dataToSend = {
                    language: "th",
                    translate: "en",
                    company: 2,
                    content: {
                        subject: thaiSubject,
                        description: thaiDescription,
                        content: thaiContent
                    }
                };

                // ส่งข้อมูลแบบ POST ไปยังไฟล์ actions/translate.php
                fetch('actions/translate.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer',
                    },
                    body: JSON.stringify(dataToSend),
                })
                .then(res => res.json())
                .then(response => {
                    console.log(response);

                    if (response.status === 'success') {
                        $('#shop_subject_en').val(response.subject);
                        $('#shop_description_en').val(response.description);
                        $('#summernote_update_en').summernote('code', response.content);
                        alert('การแปลสำเร็จ!');
                    } else {
                        alert('การแปลล้มเหลว: ' + (response.message || response.error));
                    }
                })
                .catch(error => {
                    console.error("error:", error);
                    alert('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error);
                })
                .finally(() => {
                    // 2. ซ่อน Loading Indicator เมื่อเสร็จสิ้นกระบวนการทั้งหมด
                    $('#loadingIndicator').hide();
                });
            });

        
    });
</script>

            </div>
        </div>
    </div>

    <script src='../js/index_.js?v=<?php echo time(); ?>'></script>
    <script src='js/shop_.js?v=<?php echo time(); ?>'></script>
</body>
</html>
<?php
include '../../../lib/connect.php';
include '../../../lib/base_directory.php';
include '../check_permission.php';

if (!isset($_POST['blog_id'])) {
    echo "<div class='alert alert-danger'>ไม่พบข้อมูลโครงการที่ต้องการแก้ไข</div>";
    exit;
}

$decodedId = $_POST['blog_id'];
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
        .nav-link.active {
            font-weight: bold;
            border-bottom: 2px solid #007bff;
        }
        .flag-icon {
            width: 36px;
            margin-right: 8px;
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
                            p.blog_id,
                            p.subject_blog,
                            p.description_blog,
                            p.content_blog,
                            p.subject_blog_en,
                            p.description_blog_en,
                            p.content_blog_en,
                            p.date_create,
                            GROUP_CONCAT(DISTINCT d.file_name, ':::', d.api_path, ':::', d.status ORDER BY d.status DESC SEPARATOR '|||') AS files
                        FROM dn_blog p
                        LEFT JOIN dn_blog_doc d ON p.blog_id = d.blog_id AND d.del = 0
                        WHERE p.blog_id = ?
                        GROUP BY p.blog_id
                    ");

                    if ($stmt === false) {
                        die('❌ SQL Prepare failed: ' . $conn->error);
                    }

                    $stmt->bind_param('i', $decodedId);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $content_th = $row['content_blog'];
                        $content_en = $row['content_blog_en'];
                        
                        $pic_data = [];
                        $previewImageSrc = '';

                        if (!empty($row['files'])) {
                            $files = explode('|||', $row['files']);
                            foreach ($files as $file_str) {
                                list($file_name, $api_path, $status) = explode(':::', $file_str);
                                if ($status == 1) { 
                                    $previewImageSrc = htmlspecialchars($api_path);
                                } else { 
                                    $pic_data[htmlspecialchars($file_name)] = htmlspecialchars($api_path);
                                }
                            }
                        }

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

                        $allprojectsQuery = $conn->query("SELECT project_id, subject_project FROM dn_project WHERE del = 0 ORDER BY subject_project ASC");
                        $allprojectsOptions = '';
                        while ($project = $allprojectsQuery->fetch_assoc()) {
                            $allprojectsOptions .= "<option value='{$project['project_id']}'>{$project['subject_project']}</option>";
                        }
                        
                        // **แก้ไข** ส่วนนี้โดยลบ WHERE del = 0 ออก
                        $relatedprojectsQuery = $conn->prepare("SELECT project_id FROM dn_blog_project WHERE blog_id = ?");
                        if ($relatedprojectsQuery === false) {
                            die('❌ SQL Prepare failed for related projects: ' . htmlspecialchars($conn->error));
                        }
                        $relatedprojectsQuery->bind_param("i", $decodedId);
                        $relatedprojectsQuery->execute();
                        $relatedprojectsResult = $relatedprojectsQuery->get_result();
                        $relatedprojectIds = [];
                        while ($project = $relatedprojectsResult->fetch_assoc()) {
                            $relatedprojectIds[] = $project['project_id'];
                        }
                        $relatedprojectsJSON = json_encode($relatedprojectIds);

                        echo "
                        <form id='formblog_edit' enctype='multipart/form-data'>
                            <input type='hidden' class='form-control' id='blog_id' name='blog_id' value='" . htmlspecialchars($row['blog_id']) . "'>
                            <div class='row >
                            
                                <div>
                                
                                
                                    <div style='margin: 10px;'>
                                       <div style='margin: 10px; text-align: end;'>
                                            <button type='button' id='backToblogList' class='btn btn-secondary'> 
                                                <i class='fas fa-arrow-left'></i> Back 
                                            </button>
                                        </div>
                                            <label><span>Cover photo</span>:</label>
                                            <div><span>ขนาดรูปภาพที่เหมาะสม width: 350px และ height: 250px</span></div>
                                            <div id='previewContainer' class='previewContainer'>
                                                <img id='previewImage' src='{$previewImageSrc}' alt='Image Preview' style='max-width: 100%;'>
                                            </div>
                                        </div>
                                        <div style='margin: 10px;'>
                                            <input type='file' class='form-control' id='fileInput' name='fileInput'>
                                        </div>
                                        <div style='margin: 10px;'>
                                            <label><span>Related projects</span>:</label>
                                            <select class='form-control select2-multiple' id='related_projects_edit' name='related_projects[]' multiple='multiple'>
                                                " . $allprojectsOptions . "
                                            </select>
                                        </div>
                                </div>
                                <div>
                                    

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
                                                        <input type='text' class='form-control' id='blog_subject' name='blog_subject' value='" . htmlspecialchars($row['subject_blog']) . "'>
                                                    </div>
                                                    <div style='margin: 10px;'>
                                                        <label><span>Description (TH)</span>:</label>
                                                        <textarea class='form-control' id='blog_description' name='blog_description'>" . htmlspecialchars($row['description_blog']) . "</textarea>
                                                    </div>
                                                    <div style='margin: 10px;'>
                                                        <label><span>Content (TH)</span>:</label>
                                                        <textarea class='form-control summernote' id='summernote_update' name='blog_content'>" . $content_th_with_correct_paths . "</textarea>
                                                    </div>
                                                </div>
                                                <div class='tab-pane fade' id='en' role='tabpanel' aria-labelledby='en-tab'>
                                                    <div style='margin: 10px;'>
                                                        <button type='button' id='copyFromThai' class='btn btn-info btn-sm float-end mb-2'>Copy from Thai</button>
                                                        <label><span>Subject (EN)</span>:</label>
                                                        <input type='text' class='form-control' id='blog_subject_en' name='blog_subject_en' value='" . htmlspecialchars($row['subject_blog_en']) . "'>
                                                    </div>
                                                    <div style='margin: 10px;'>
                                                        <label><span>Description (EN)</span>:</label>
                                                        <textarea class='form-control' id='blog_description_en' name='blog_description_en'>" . htmlspecialchars($row['description_blog_en']) . "</textarea>
                                                    </div>
                                                    <div style='margin: 10px;'>
                                                        <label><span>Content (EN)</span>:</label>
                                                        <textarea class='form-control summernote' id='summernote_update_en' name='blog_content_en'>" . $content_en_with_correct_paths . "</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div style='margin: 10px; text-align: end;'>
                                        <button type='button' id='submitEditblog' class='btn btn-success'>
                                            <i class='fas fa-save'></i> Save blog
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        ";
                    } else {
                        echo "<div class='alert alert-warning'>ไม่มีข้อมูลโครงการ</div>";
                    }
                    $stmt->close();
                    $conn->close();
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        $(document).ready(function() {
            $('.select2-multiple').select2();
            var relatedprojects = <?php echo $relatedprojectsJSON; ?>;
            $('#related_projects_edit').val(relatedprojects).trigger('change');

            $('#summernote_update').summernote({
                height: 600,
                minHeight: 600,
                maxHeight: 600,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['fontname', 'fontsize', 'forecolor']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'video', 'table']],
                    ['view', ['fullscreen', ['codeview', 'fullscreen']]],
                    ['image', ['resizeFull', 'resizeHalf', 'resizeQuarter']]
                ],
                fontNames: ['Kanit', 'Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Georgia', 'Times New Roman', 'Verdana', 'sans-serif'],
                fontNamesIgnoreCheck: ['Kanit'],
                fontsizeUnits: ['px', 'pt'],
                fontsize: ['8', '10', '12', '14', '16', '18', '24', '36'],
            });

            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                var target = $(e.target).attr("data-bs-target");
                if (target === '#en') {
                    if ($('#summernote_update_en').data('summernote')) {
                        $('#summernote_update_en').summernote('destroy');
                    }
                    $('#summernote_update_en').summernote({
                        height: 600,
                        minHeight: 600,
                        maxHeight: 600,
                        toolbar: [
                            ['style', ['bold', 'italic', 'underline', 'clear']],
                            ['font', ['fontname', 'fontsize', 'forecolor']],
                            ['para', ['ul', 'ol', 'paragraph']],
                            ['insert', ['link', 'picture', 'video', 'table']],
                            ['view', ['fullscreen', ['codeview', 'fullscreen']]],
                            ['image', ['resizeFull', 'resizeHalf', 'resizeQuarter']]
                        ],
                        fontNames: ['Kanit', 'Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Georgia', 'Times New Roman', 'Verdana', 'sans-serif'],
                        fontNamesIgnoreCheck: ['Kanit'],
                        fontsizeUnits: ['px', 'pt'],
                        fontsize: ['8', '10', '12', '14', '16', '18', '24', '36'],
                    });
                }
            });

            // New Copy from Thai button functionality
            $('#copyFromThai').on('click', function() {
                var thaiSubject = $('#blog_subject').val();
                var thaiDescription = $('#blog_description').val();
                var thaiContent = $('#summernote_update').summernote('code');

                $('#blog_subject_en').val(thaiSubject);
                $('#blog_description_en').val(thaiDescription);
                $('#summernote_update_en').summernote('code', thaiContent);
            });
            
            $('#fileInput').on('change', function() {
                var input = this;
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#previewImage').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            });

            $('#backToblogList').on('click', function() {
                window.location.href = "list_blog.php";
            });

            $("#submitEditblog").on("click", function(event) {
                event.preventDefault();
                var formblog = $("#formblog_edit")[0];
                var formData = new FormData(formblog);
                formData.set("action", "editblog");
                formData.set("blog_id", $("#blog_id").val());
                var contentFromEditor_th = $("#summernote_update").summernote('code');
                var contentFromEditor_en = $('#summernote_update_en').summernote('code');
                var checkIsUrl = false;
                
                if (contentFromEditor_th) {
                    var tempDiv = document.createElement("div");
                    tempDiv.innerHTML = contentFromEditor_th;
                    var imgTags = tempDiv.getElementsByTagName("img");
                    for (var i = 0; i < imgTags.length; i++) {
                        var imgSrc = imgTags[i].getAttribute("src");
                        var filename = imgTags[i].getAttribute("data-filename");
                        if (!imgSrc) continue;

                        imgSrc = imgSrc.replace(/ /g, "%20");
                        if (!isValidUrl(imgSrc)) {
                            var file = base64ToFile(imgSrc, filename);
                            if (file) {
                                formData.append("image_files_th[]", file);
                            }
                            if (imgSrc.startsWith("data:image")) {
                                imgTags[i].setAttribute("src", "");
                            }
                        } else {
                            checkIsUrl = true;
                        }
                    }
                    formData.set("blog_content", tempDiv.innerHTML);
                }

                if (contentFromEditor_en) {
                    var tempDiv_en = document.createElement("div");
                    tempDiv_en.innerHTML = contentFromEditor_en;
                    var imgTags_en = tempDiv_en.getElementsByTagName("img");
                    for (var i = 0; i < imgTags_en.length; i++) {
                        var imgSrc_en = imgTags_en[i].getAttribute("src");
                        var filename_en = imgTags_en[i].getAttribute("data-filename");
                        if (!imgSrc_en) continue;

                        imgSrc_en = imgSrc_en.replace(/ /g, "%20");
                        if (!isValidUrl(imgSrc_en)) {
                            var file_en = base64ToFile(imgSrc_en, filename_en);
                            if (file_en) {
                                formData.append("image_files_en[]", file_en);
                            }
                            if (imgSrc_en.startsWith("data:image")) {
                                imgTags_en[i].setAttribute("src", "");
                            }
                        } else {
                            checkIsUrl = true;
                        }
                    }
                    formData.set("blog_content_en", tempDiv_en.innerHTML);
                }

                $(".is-invalid").removeClass("is-invalid");
                if (!$("#blog_subject").val().trim()) {
                    $("#blog_subject").addClass("is-invalid");
                    return;
                }
                if (!$("#blog_description").val().trim()) {
                    $("#blog_description").addClass("is-invalid");
                    return;
                }
                if (!contentFromEditor_th.trim() && !contentFromEditor_en.trim()) {
                    alertError("Please fill in content information for at least one language.");
                    return;
                }

                formData.set("blog_subject_en", $("#blog_subject_en").val());
                formData.set("blog_description_en", $("#blog_description_en").val());

                Swal.fire({
                    title: checkIsUrl ? "Image detection system from other websites?" : "Are you sure?",
                    text: "Do you want to edit blog?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#4CAF50",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Accept"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#loading-overlay').fadeIn();
                        $.ajax({
                            url: "actions/process_blog.php",
                            type: "POST",
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function (response) {
                                try {
                                    var json = (typeof response === "string") ? JSON.parse(response) : response;
                                    if (json.status === 'success') {
                                        location.reload();
                                    } else {
                                        Swal.fire('Error', json.message || 'Unknown error', 'error');
                                    }
                                } catch (e) {
                                    console.error("❌ JSON parse error:", e);
                                    Swal.fire('Error', 'Invalid response from server', 'error');
                                }
                            },
                            error: function (xhr) {
                                console.error("❌ AJAX error:", xhr.responseText);
                                Swal.fire('Error', 'AJAX request failed', 'error');
                                $('#loading-overlay').fadeOut();
                            },
                        });
                    } else {
                        $('#loading-overlay').fadeOut();
                    }
                });
            });
        });

        function base64ToFile(base64, fileName) {
            // ... (Your existing base64ToFile function) ...
        }

        function alertError(textAlert) {
            // ... (Your existing alertError function) ...
        }

        function isValidUrl(str) {
            // ... (Your existing isValidUrl function) ...
        }
    </script>
    <script src='js/blog_.js?v=<?php echo time(); ?>'></script>
</body>
</html>
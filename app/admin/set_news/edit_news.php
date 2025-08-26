<?php
include '../../../lib/connect.php';
include '../../../lib/base_directory.php';
include '../check_permission.php';

if (!isset($_POST['news_id'])) {
    echo "<div class='alert alert-danger'>ไม่พบข้อมูลข่าวที่ต้องการแก้ไข</div>";
    exit;
}

$decodedId = $_POST['news_id'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit News</title>

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
                        <i class="far fa-newspaper"></i> Edit News
                    </h4>

                    <?php
                    $stmt = $conn->prepare("
                        SELECT
                            n.news_id,
                            n.subject_news,
                            n.description_news,
                            n.content_news,
                            n.subject_news_en,
                            n.description_news_en,
                            n.content_news_en,
                            n.subject_news_cn,
                            n.description_news_cn,
                            n.content_news_cn,
                            n.subject_news_jp,
                            n.description_news_jp,
                            n.content_news_jp,
                            n.subject_news_kr,
                            n.description_news_kr,
                            n.content_news_kr,
                            n.date_create,
                            GROUP_CONCAT(DISTINCT d.file_name, ':::', d.api_path, ':::', d.status ORDER BY d.status DESC SEPARATOR '|||') AS files
                        FROM dn_news n
                        LEFT JOIN dn_news_doc d ON n.news_id = d.news_id AND d.del = 0
                        WHERE n.news_id = ?
                        GROUP BY n.news_id
                    ");

                    if ($stmt === false) {
                        die('❌ SQL Prepare failed: ' . $conn->error);
                    }

                    $stmt->bind_param('i', $decodedId);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $content_th = $row['content_news'];
                        $content_en = $row['content_news_en'];
                        $content_cn = $row['content_news_cn'];
                        $content_jp = $row['content_news_jp'];
                        $content_kr = $row['content_news_kr'];
                        
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
                        
                        $dom_cn = new DOMDocument();
                        libxml_use_internal_errors(true);
                        $source_cn = !empty($content_cn) ? mb_convert_encoding($content_cn, 'HTML-ENTITIES', 'UTF-8') : '<div></div>';
                        $dom_cn->loadHTML($source_cn, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                        libxml_clear_errors();
                        $images_cn = $dom_cn->getElementsByTagName('img');
                        foreach ($images_cn as $img) {
                            $data_filename = $img->getAttribute('data-filename');
                            if (!empty($data_filename) && isset($pic_data[$data_filename])) {
                                $img->setAttribute('src', $pic_data[$data_filename]);
                            }
                        }
                        $content_cn_with_correct_paths = $dom_cn->saveHTML();

                        $dom_jp = new DOMDocument();
                        libxml_use_internal_errors(true);
                        $source_jp = !empty($content_jp) ? mb_convert_encoding($content_jp, 'HTML-ENTITIES', 'UTF-8') : '<div></div>';
                        $dom_jp->loadHTML($source_jp, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                        libxml_clear_errors();
                        $images_jp = $dom_jp->getElementsByTagName('img');
                        foreach ($images_jp as $img) {
                            $data_filename = $img->getAttribute('data-filename');
                            if (!empty($data_filename) && isset($pic_data[$data_filename])) {
                                $img->setAttribute('src', $pic_data[$data_filename]);
                            }
                        }
                        $content_jp_with_correct_paths = $dom_jp->saveHTML();

                        $dom_kr = new DOMDocument();
                        libxml_use_internal_errors(true);
                        $source_kr = !empty($content_kr) ? mb_convert_encoding($content_kr, 'HTML-ENTITIES', 'UTF-8') : '<div></div>';
                        $dom_kr->loadHTML($source_kr, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                        libxml_clear_errors();
                        $images_kr = $dom_kr->getElementsByTagName('img');
                        foreach ($images_kr as $img) {
                            $data_filename = $img->getAttribute('data-filename');
                            if (!empty($data_filename) && isset($pic_data[$data_filename])) {
                                $img->setAttribute('src', $pic_data[$data_filename]);
                            }
                        }
                        $content_kr_with_correct_paths = $dom_kr->saveHTML();

                        echo "
                        <form id='formnews_edit' enctype='multipart/form-data'>
                            <input type='hidden' class='form-control' id='news_id' name='news_id' value='" . htmlspecialchars($row['news_id']) . "'>
                            <div class='row >
                            
                            
                                <div>
                                
                                
                                    <div style='margin: 10px;'>
                                        <div style='margin: 10px; text-align: end;'>
                                            <button type='button' id='backToNewsList' class='btn btn-secondary'> 
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
                                                <li class='nav-item' role='presentation'>
                                                    <button class='nav-link' id='cn-tab' data-bs-toggle='tab' data-bs-target='#cn' type='button' role='tab' aria-controls='cn' aria-selected='false'>
                                                        <img src='https://flagcdn.com/w320/cn.png' alt='Chinese Flag' class='flag-icon' style=' width: 36px; 
                                            margin-right: 8px;'>Chinese
                                                        </button>
                                                </li>
                                                <li class='nav-item' role='presentation'>
                                                        <button class='nav-link' id='jp-tab' data-bs-toggle='tab' data-bs-target='#jp' type='button' role='tab' aria-controls='jp' aria-selected='false'>
                                                            <img src='https://flagcdn.com/w320/jp.png' alt='Japanese Flag' class='flag-icon' style=' width: 36px; 
                                                margin-right: 8px;'>Japanese
                                                        </button>
                                                    </li>
                                                <li class='nav-item' role='presentation'>
                                                    <button class='nav-link' id='kr-tab' data-bs-toggle='tab' data-bs-target='#kr' type='button' role='tab' aria-controls='kr' aria-selected='false'>
                                                        <img src='https://flagcdn.com/w320/kr.png' alt='Korean Flag' class='flag-icon' style=' width: 36px; 
                                            margin-right: 8px;'>Korean
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class='card-body'>
                                            <div class='tab-content' id='languageTabsContent'>
                                                <div class='tab-pane fade show active' id='th' role='tabpanel' aria-labelledby='th-tab'>
                                                    <div style='margin: 10px;'>
                                                        
                                                        <label><span>Subject (TH)</span>:</label>
                                                        <input type='text' class='form-control' id='news_subject' name='news_subject' value='" . htmlspecialchars($row['subject_news']) . "'>
                                                    </div>
                                                    <div style='margin: 10px;'>
                                                        <label><span>Description (TH)</span>:</label>
                                                        <textarea class='form-control' id='news_description' name='news_description'>" . htmlspecialchars($row['description_news']) . "</textarea>
                                                    </div>
                                                    <div style='margin: 10px;'>
                                                        <label><span>Content (TH)</span>:</label>
                                                        <textarea class='form-control summernote' id='summernote_update' name='news_content'>" . $content_th_with_correct_paths . "</textarea>
                                                    </div>
                                                </div>
                                                <div class='tab-pane fade' id='en' role='tabpanel' aria-labelledby='en-tab'>
                                                    <button type='button' id='copyFromThai' class='btn btn-info btn-sm float-end mb-2'>Origami Ai Translate</button>
                                                        <div id='loadingIndicator' class='loading-overlay' style='display: none;'>
                                                            <div class='loading-spinner'></div>
                                                        </div>
                                                    <div style='margin: 10px;'>
                                                        
                                                        <label><span>Subject (EN)</span>:</label>
                                                        <input type='text' class='form-control' id='news_subject_en' name='news_subject_en' value='" . htmlspecialchars($row['subject_news_en']) . "'>
                                                    </div>
                                                    <div style='margin: 10px;'>
                                                        <label><span>Description (EN)</span>:</label>
                                                        <textarea class='form-control' id='news_description_en' name='news_description_en'>" . htmlspecialchars($row['description_news_en']) . "</textarea>
                                                    </div>
                                                    <div style='margin: 10px;'>
                                                        <label><span>Content (EN)</span>:</label>
                                                        <textarea class='form-control summernote' id='summernote_update_en' name='news_content_en'>" . $content_en_with_correct_paths . "</textarea>
                                                    </div>
                                                </div>
                                                <div class='tab-pane fade' id='cn' role='tabpanel' aria-labelledby='cn-tab'>
                                                    <button type='button' id='copyFromThaiCN' class='btn btn-info btn-sm float-end mb-2'>Origami Ai Translate</button>
                                                    <div id='loadingIndicatorCN' class='loading-overlay' style='display: none;'>
                                                        <div class='loading-spinner'></div>
                                                    </div>
                                                    <div style='margin: 10px;'>
                                                        
                                                        <label><span>Subject (CN)</span>:</label>
                                                        <input type='text' class='form-control' id='news_subject_cn' name='news_subject_cn' value='" . htmlspecialchars($row['subject_news_cn']) . "'>
                                                    </div>
                                                    <div style='margin: 10px;'>
                                                        <label><span>Description (CN)</span>:</label>
                                                        <textarea class='form-control' id='news_description_cn' name='news_description_cn'>" . htmlspecialchars($row['description_news_cn']) . "</textarea>
                                                    </div>
                                                    <div style='margin: 10px;'>
                                                        <label><span>Content (CN)</span>:</label>
                                                        <textarea class='form-control summernote' id='summernote_update_cn' name='news_content_cn'>" . $content_cn_with_correct_paths . "</textarea>
                                                    </div>
                                                </div>
                                                <div class='tab-pane fade' id='jp' role='tabpanel' aria-labelledby='jp-tab'>
                                                    <button type='button' id='copyFromThaiJP' class='btn btn-info btn-sm float-end mb-2'>Origami Ai Translate</button>
                                                    <div id='loadingIndicatorJP' class='loading-overlay' style='display: none;'>
                                                        <div class='loading-spinner'></div>
                                                    </div>
                                                    <div style='margin: 10px;'>
                                                        
                                                        <label><span>Subject (JP)</span>:</label>
                                                        <input type='text' class='form-control' id='news_subject_jp' name='news_subject_jp' value='" . htmlspecialchars($row['subject_news_jp']) . "'>
                                                    </div>
                                                    <div style='margin: 10px;'>
                                                        <label><span>Description (JP)</span>:</label>
                                                        <textarea class='form-control' id='news_description_jp' name='news_description_jp'>" . htmlspecialchars($row['description_news_jp']) . "</textarea>
                                                    </div>
                                                    <div style='margin: 10px;'>
                                                        <label><span>Content (JP)</span>:</label>
                                                        <textarea class='form-control summernote' id='summernote_update_jp' name='news_content_jp'>" . $content_jp_with_correct_paths . "</textarea>
                                                    </div>
                                                </div>
                                                <div class='tab-pane fade' id='kr' role='tabpanel' aria-labelledby='kr-tab'>
                                                    <button type='button' id='copyFromThaiKR' class='btn btn-info btn-sm float-end mb-2'>Origami Ai Translate</button>
                                                    <div id='loadingIndicatorKR' class='loading-overlay' style='display: none;'>
                                                        <div class='loading-spinner'></div>
                                                    </div>
                                                    <div style='margin: 10px;'>
                                                        
                                                        <label><span>Subject (KR)</span>:</label>
                                                        <input type='text' class='form-control' id='news_subject_kr' name='news_subject_kr' value='" . htmlspecialchars($row['subject_news_kr']) . "'>
                                                    </div>
                                                    <div style='margin: 10px;'>
                                                        <label><span>Description (KR)</span>:</label>
                                                        <textarea class='form-control' id='news_description_kr' name='news_description_kr'>" . htmlspecialchars($row['description_news_kr']) . "</textarea>
                                                    </div>
                                                    <div style='margin: 10px;'>
                                                        <label><span>Content (KR)</span>:</label>
                                                        <textarea class='form-control summernote' id='summernote_update_kr' name='news_content_kr'>" . $content_kr_with_correct_paths . "</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div style='margin: 10px; text-align: end;'>
                                        <button type='button' id='submitEditnews' class='btn btn-success'>
                                            <i class='fas fa-save'></i> Save News
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        ";
                    } else {
                        echo "<div class='alert alert-warning'>ไม่มีข้อมูลข่าว</div>";
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
            // ลบโค้ดส่วนที่เกี่ยวข้องกับ related_shops ออกทั้งหมด

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
                if (target === '#cn') {
                if ($('#summernote_update_cn').data('summernote')) {
                    $('#summernote_update_cn').summernote('destroy');
                }
                $('#summernote_update_cn').summernote({
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
            if (target === '#jp') {
                if ($('#summernote_update_jp').data('summernote')) {
                    $('#summernote_update_jp').summernote('destroy');
                }
                $('#summernote_update_jp').summernote({
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
            // เพิ่มส่วนของ KR
            if (target === '#kr') {
                if ($('#summernote_update_kr').data('summernote')) {
                    $('#summernote_update_kr').summernote('destroy');
                }
                $('#summernote_update_kr').summernote({
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
            // 1. แสดง Loading Indicator
            $('#loadingIndicator').show(); // ให้โชว์ loading animation

            // ดึงค่าจากฟอร์มภาษาไทย
            var thaiSubject = $('#news_subject').val();
            var thaiDescription = $('#news_description').val();
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
                    $('#news_subject_en').val(response.subject);
                    $('#news_description_en').val(response.description);
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

        // New Copy from Thai to Chinese button functionality
        $('#copyFromThaiCN').on('click', function() {
            // 1. แสดง Loading Indicator
            $('#loadingIndicatorCN').show(); // ให้โชว์ loading animation

            // ดึงค่าจากฟอร์มภาษาไทย
            var thaiSubject = $('#news_subject').val();
            var thaiDescription = $('#news_description').val();
            var thaiContent = $('#summernote_update').summernote('code');

            // สร้าง Object สำหรับข้อมูลที่จะส่งไป
            const dataToSend = {
                language: "th",
                translate: "cn",
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
                    $('#news_subject_cn').val(response.subject);
                    $('#news_description_cn').val(response.description);
                    $('#summernote_update_cn').summernote('code', response.content);
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
                $('#loadingIndicatorCN').hide();
            });
        });
        
        // New Copy from Thai to Japanese button functionality
        $('#copyFromThaiJP').on('click', function() {
            // 1. แสดง Loading Indicator
            $('#loadingIndicatorJP').show(); // ให้โชว์ loading animation

            // ดึงค่าจากฟอร์มภาษาไทย
            var thaiSubject = $('#news_subject').val();
            var thaiDescription = $('#news_description').val();
            var thaiContent = $('#summernote_update').summernote('code');

            // สร้าง Object สำหรับข้อมูลที่จะส่งไป
            const dataToSend = {
                language: "th",
                translate: "jp",
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
                    $('#news_subject_jp').val(response.subject);
                    $('#news_description_jp').val(response.description);
                    $('#summernote_update_jp').summernote('code', response.content);
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
                $('#loadingIndicatorJP').hide();
            });
        });

        // เพิ่ม New Copy from Thai to Korean button functionality
        $('#copyFromThaiKR').on('click', function() {
            // 1. แสดง Loading Indicator
            $('#loadingIndicatorKR').show(); // ให้โชว์ loading animation

            // ดึงค่าจากฟอร์มภาษาไทย
            var thaiSubject = $('#news_subject').val();
            var thaiDescription = $('#news_description').val();
            var thaiContent = $('#summernote_update').summernote('code');

            // สร้าง Object สำหรับข้อมูลที่จะส่งไป
            const dataToSend = {
                language: "th",
                translate: "kr",
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
                    $('#news_subject_kr').val(response.subject);
                    $('#news_description_kr').val(response.description);
                    $('#summernote_update_kr').summernote('code', response.content);
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
                $('#loadingIndicatorKR').hide();
            });
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

        $('#backToNewsList').on('click', function() {
            window.location.href = "list_news.php";
        });

        $("#submitEditnews").on("click", function(event) {
            event.preventDefault();
            var formnews = $("#formnews_edit")[0];
            var formData = new FormData(formnews);
            formData.set("action", "editnews");
            formData.set("news_id", $("#news_id").val());
            var contentFromEditor_th = $("#summernote_update").summernote('code');
            var contentFromEditor_en = $('#summernote_update_en').summernote('code');
            var contentFromEditor_cn = $('#summernote_update_cn').summernote('code');
            var contentFromEditor_jp = $('#summernote_update_jp').summernote('code');
            // เพิ่มตัวแปรสำหรับ KR
            var contentFromEditor_kr = $('#summernote_update_kr').summernote('code');
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
                formData.set("news_content", tempDiv.innerHTML);
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
                formData.set("news_content_en", tempDiv_en.innerHTML);
            }

            if (contentFromEditor_cn) {
                var tempDiv_cn = document.createElement("div");
                tempDiv_cn.innerHTML = contentFromEditor_cn;
                var imgTags_cn = tempDiv_cn.getElementsByTagName("img");
                for (var i = 0; i < imgTags_cn.length; i++) {
                    var imgSrc_cn = imgTags_cn[i].getAttribute("src");
                    var filename_cn = imgTags_cn[i].getAttribute("data-filename");
                    if (!imgSrc_cn) continue;

                    imgSrc_cn = imgSrc_cn.replace(/ /g, "%20");
                    if (!isValidUrl(imgSrc_cn)) {
                        var file_cn = base64ToFile(imgSrc_cn, filename_cn);
                        if (file_cn) {
                            formData.append("image_files_cn[]", file_cn);
                        }
                        if (imgSrc_cn.startsWith("data:image")) {
                            imgTags_cn[i].setAttribute("src", "");
                        }
                    } else {
                        checkIsUrl = true;
                    }
                }
                formData.set("news_content_cn", tempDiv_cn.innerHTML);
            }

            if (contentFromEditor_jp) {
                var tempDiv_jp = document.createElement("div");
                tempDiv_jp.innerHTML = contentFromEditor_jp;
                var imgTags_jp = tempDiv_jp.getElementsByTagName("img");
                for (var i = 0; i < imgTags_jp.length; i++) {
                    var imgSrc_jp = imgTags_jp[i].getAttribute("src");
                    var filename_jp = imgTags_jp[i].getAttribute("data-filename");
                    if (!imgSrc_jp) continue;

                    imgSrc_jp = imgSrc_jp.replace(/ /g, "%20");
                    if (!isValidUrl(imgSrc_jp)) {
                        var file_jp = base64ToFile(imgSrc_jp, filename_jp);
                        if (file_jp) {
                            formData.append("image_files_jp[]", file_jp);
                        }
                        if (imgSrc_jp.startsWith("data:image")) {
                            imgTags_jp[i].setAttribute("src", "");
                        }
                    } else {
                        checkIsUrl = true;
                    }
                }
                formData.set("news_content_jp", tempDiv_jp.innerHTML);
            }
            
            // เพิ่มส่วนของ KR
            if (contentFromEditor_kr) {
                var tempDiv_kr = document.createElement("div");
                tempDiv_kr.innerHTML = contentFromEditor_kr;
                var imgTags_kr = tempDiv_kr.getElementsByTagName("img");
                for (var i = 0; i < imgTags_kr.length; i++) {
                    var imgSrc_kr = imgTags_kr[i].getAttribute("src");
                    var filename_kr = imgTags_kr[i].getAttribute("data-filename");
                    if (!imgSrc_kr) continue;

                    imgSrc_kr = imgSrc_kr.replace(/ /g, "%20");
                    if (!isValidUrl(imgSrc_kr)) {
                        var file_kr = base64ToFile(imgSrc_kr, filename_kr);
                        if (file_kr) {
                            formData.append("image_files_kr[]", file_kr);
                        }
                        if (imgSrc_kr.startsWith("data:image")) {
                            imgTags_kr[i].setAttribute("src", "");
                        }
                    } else {
                        checkIsUrl = true;
                    }
                }
                formData.set("news_content_kr", tempDiv_kr.innerHTML);
            }

            $(".is-invalid").removeClass("is-invalid");
            if (!$("#news_subject").val().trim()) {
                $("#news_subject").addClass("is-invalid");
                return;
            }
            if (!$("#news_description").val().trim()) {
                $("#news_description").addClass("is-invalid");
                return;
            }
            if (!contentFromEditor_th.trim() && !contentFromEditor_en.trim() && !contentFromEditor_cn.trim() && !contentFromEditor_jp.trim() && !contentFromEditor_kr.trim()) {
                alertError("Please fill in content information for at least one language.");
                return;
            }

            formData.set("news_subject_en", $("#news_subject_en").val());
            formData.set("news_description_en", $("#news_description_en").val());
            formData.set("news_subject_cn", $("#news_subject_cn").val());
            formData.set("news_description_cn", $("#news_description_cn").val());
            formData.set("news_subject_jp", $("#news_subject_jp").val());
            formData.set("news_description_jp", $("#news_description_jp").val());
            // เพิ่มส่วนของ KR
            formData.set("news_subject_kr", $("#news_subject_kr").val());
            formData.set("news_description_kr", $("#news_description_kr").val());

            Swal.fire({
                title: checkIsUrl ? "Image detection system from other websites?" : "Are you sure?",
                text: "Do you want to edit news?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#4CAF50",
                cancelButtonColor: "#d33",
                confirmButtonText: "Accept"
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#loading-overlay').fadeIn();
                    $.ajax({
                        url: "actions/process_news.php",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
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
                        error: function(xhr) {
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
<script src='js/news_.js?v=<?php echo time(); ?>'></script>
<script src='../js/index_.js?v=<?php echo time(); ?>'></script>
</body>
</html>
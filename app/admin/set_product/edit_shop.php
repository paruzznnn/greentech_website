<?php
// เริ่ม session
session_start();

include '../../../lib/connect.php';
include '../../../lib/base_directory.php';
include '../check_permission.php';

// กำหนดภาษาเริ่มต้นและตรวจสอบค่าใน URL
$lang = 'th';
if (isset($_GET['lang'])) {
    $supportedLangs = ['th', 'en', 'cn', 'jp', 'kr'];
    $newLang = $_GET['lang'];
    if (in_array($newLang, $supportedLangs)) {
        $_SESSION['lang'] = $newLang;
        $lang = $newLang;
    }
} else {
    // ถ้าไม่มี lang ใน URL ให้ใช้ค่าจาก Session หรือค่าเริ่มต้น 'th'
    if (isset($_SESSION['lang'])) {
        $lang = $_SESSION['lang'];
    }
}

// สร้าง array สำหรับเก็บข้อความในแต่ละภาษา
$texts = [
    'th' => [
        'title' => 'แก้ไขร้านค้า',
        'header' => 'แก้ไขร้านค้า',
        'not_found' => 'ไม่พบข้อมูลข่าวที่ต้องการแก้ไข',
        'no_data' => 'ไม่มีข้อมูลข่าว',
        'back_button' => 'ย้อนกลับ',
        'save_button' => 'บันทึกร้านค้า',
        'cover_photo' => 'รูปหน้าปก',
        'photo_size' => 'ขนาดรูปภาพที่เหมาะสม width: 350px และ height: 250px',
        'main_group' => 'กลุ่มแม่',
        'select_main_group' => '-- เลือกกลุ่มแม่ --',
        'sub_group' => 'กลุ่มย่อย',
        'select_sub_group' => '-- เลือกกลุ่มย่อย --',
        'subject' => 'หัวข้อ (TH)',
        'description' => 'คำอธิบาย (TH)',
        'content' => 'เนื้อหา (TH)',
        'language' => [
            'th' => 'ภาษาไทย',
            'en' => 'ภาษาอังกฤษ',
            'cn' => 'ภาษาจีน',
            'jp' => 'ภาษาญี่ปุ่น',
            'kr' => 'ภาษาเกาหลี'
        ],
        'translate_button' => 'Origami Ai Translate',
    ],
    'en' => [
        'title' => 'Edit Shop',
        'header' => 'Edit Shop',
        'not_found' => 'The news data to be edited was not found.',
        'no_data' => 'No news data available.',
        'back_button' => 'Back',
        'save_button' => 'Save shop',
        'cover_photo' => 'Cover photo',
        'photo_size' => 'Recommended image size: width: 350px and height: 250px',
        'main_group' => 'Main Group',
        'select_main_group' => '-- Select Main Group --',
        'sub_group' => 'Sub Group',
        'select_sub_group' => '-- Select Sub Group --',
        'subject' => 'Subject (EN)',
        'description' => 'Description (EN)',
        'content' => 'Content (EN)',
        'language' => [
            'th' => 'Thai',
            'en' => 'English',
            'cn' => 'Chinese',
            'jp' => 'Japanese',
            'kr' => 'Korean'
        ],
        'translate_button' => 'Origami Ai Translate',
    ],
    'cn' => [
        'title' => '编辑商店',
        'header' => '编辑商店',
        'not_found' => '未找到要编辑的新闻数据',
        'no_data' => '没有新闻数据',
        'back_button' => '返回',
        'save_button' => '保存商店',
        'cover_photo' => '封面照片',
        'photo_size' => '推荐图片尺寸：宽度：350px，高度：250px',
        'main_group' => '主组',
        'select_main_group' => '-- 选择主组 --',
        'sub_group' => '子组',
        'select_sub_group' => '-- 选择子组 --',
        'subject' => '标题 (CN)',
        'description' => '描述 (CN)',
        'content' => '内容 (CN)',
        'language' => [
            'th' => '泰语',
            'en' => '英语',
            'cn' => '中文',
            'jp' => '日语',
            'kr' => '韩语'
        ],
        'translate_button' => 'Origami Ai Translate',
    ],
    'jp' => [
        'title' => 'ショップを編集',
        'header' => 'ショップを編集',
        'not_found' => '編集するニュースデータが見つかりませんでした',
        'no_data' => 'ニュースデータがありません',
        'back_button' => '戻る',
        'save_button' => 'ショップを保存',
        'cover_photo' => 'カバー写真',
        'photo_size' => '推奨画像サイズ：幅：350px、高さ：250px',
        'main_group' => 'メイングループ',
        'select_main_group' => '-- メイングループを選択 --',
        'sub_group' => 'サブグループ',
        'select_sub_group' => '-- サブグループを選択 --',
        'subject' => '件名 (JP)',
        'description' => '説明 (JP)',
        'content' => '内容 (JP)',
        'language' => [
            'th' => 'タイ語',
            'en' => '英語',
            'cn' => '中国語',
            'jp' => '日本語',
            'kr' => '韓国語'
        ],
        'translate_button' => 'Origami Ai Translate',
    ],
    'kr' => [
        'title' => '상점 편집',
        'header' => '상점 편집',
        'not_found' => '편집할 뉴스 데이터를 찾을 수 없습니다.',
        'no_data' => '뉴스 데이터가 없습니다.',
        'back_button' => '뒤로',
        'save_button' => '상점 저장',
        'cover_photo' => '표지 사진',
        'photo_size' => '권장 이미지 크기: 너비 350px, 높이 250px',
        'main_group' => '메인 그룹',
        'select_main_group' => '-- 메인 그룹 선택 --',
        'sub_group' => '서브 그룹',
        'select_sub_group' => '-- 서브 그룹 선택 --',
        'subject' => '제목 (KR)',
        'description' => '설명 (KR)',
        'content' => '내용 (KR)',
        'language' => [
            'th' => '태국어',
            'en' => '영어',
            'cn' => '중국어',
            'jp' => '일본어',
            'kr' => '한국어'
        ],
        'translate_button' => 'Origami Ai Translate',
    ],
];

$current_texts = $texts[$lang];

// ตรวจสอบว่าได้รับค่า shop_id หรือไม่
if (!isset($_POST['shop_id'])) {
    echo "<div class='alert alert-danger'>{$current_texts['not_found']}</div>";
    exit;
}

$decodedId = $_POST['shop_id'];
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $current_texts['title']; ?></title>

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
                        <i class="far fa-newspaper"></i> <?php echo $current_texts['header']; ?>
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
        dn.content_shop_en,
        dn.subject_shop_cn,
        dn.description_shop_cn,
        dn.content_shop_cn,
        dn.subject_shop_jp,
        dn.description_shop_jp,
        dn.content_shop_jp,
        dn.subject_shop_kr,
        dn.description_shop_kr,
        dn.content_shop_kr
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
    $content_cn = $row['content_shop_cn'];
    $content_jp = $row['content_shop_jp']; // เพิ่มสำหรับภาษาญี่ปุ่น
    $content_kr = $row['content_shop_kr']; // เพิ่มสำหรับภาษาเกาหลี
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
    
    // แทนที่ src ของรูปภาพใน content ภาษาจีนด้วย api_path ที่ถูกต้องจาก $pic_data
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
    
    // เพิ่มโค้ดสำหรับภาษาญี่ปุ่น
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

    // เพิ่มโค้ดสำหรับภาษาเกาหลี
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
                        <i class='fas fa-arrow-left'></i> {$current_texts['back_button']}
                    </button>
                </div>
                <div style='margin: 10px;'>
                    <label><span>{$current_texts['cover_photo']}</span>:</label>
                    <div><span>{$current_texts['photo_size']}</span></div>
                    <div id='previewContainer' class='previewContainer'>
                        <img id='previewImage' src='{$previewImageSrc}' alt='Image Preview' style='max-width: 100%;'>
                    </div>
                </div>
                <div style='margin: 10px;'>
                    <input type='file' class='form-control' id='fileInput' name='fileInput'> </div>
                <div style='margin: 10px;'>
                    <label><span>{$current_texts['main_group']}</span>:</label>
                    <select id='main_group_select' class='form-control'>
                        <option value=''>{$current_texts['select_main_group']}</option>
                        "; // ปิด PHP เพื่อใส่ mainGroupOptions
                            echo $mainGroupOptions;
                        echo "
                    </select>
                </div>
                <div style='margin: 10px;'>
                    <label><span>{$current_texts['sub_group']}</span>:</label>
                    <select id='sub_group_select' name='group_id' class='form-control'>
                        <option value=''>{$current_texts['select_sub_group']}</option>
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
            margin-right: 8px;'>{$current_texts['language']['th']}
                                </button>
                            </li>
                            <li class='nav-item' role='presentation'>
                                <button class='nav-link' id='en-tab' data-bs-toggle='tab' data-bs-target='#en' type='button' role='tab' aria-controls='en' aria-selected='false'>
                                    <img src='https://flagcdn.com/w320/gb.png' alt='English Flag' class='flag-icon' style=' width: 36px; 
            margin-right: 8px;'>{$current_texts['language']['en']}
                                </button>
                            </li>
                            <li class='nav-item' role='presentation'>
                                <button class='nav-link' id='cn-tab' data-bs-toggle='tab' data-bs-target='#cn' type='button' role='tab' aria-controls='cn' aria-selected='false'>
                                    <img src='https://flagcdn.com/w320/cn.png' alt='Chinese Flag' class='flag-icon' style=' width: 36px; 
            margin-right: 8px;'>{$current_texts['language']['cn']}
                                </button>
                            </li>
                            <li class='nav-item' role='presentation'>
                                <button class='nav-link' id='jp-tab' data-bs-toggle='tab' data-bs-target='#jp' type='button' role='tab' aria-controls='jp' aria-selected='false'>
                                    <img src='https://flagcdn.com/w320/jp.png' alt='Japanese Flag' class='flag-icon' style=' width: 36px; 
            margin-right: 8px;'>{$current_texts['language']['jp']}
                                </button>
                            </li>
                            <li class='nav-item' role='presentation'>
                                <button class='nav-link' id='kr-tab' data-bs-toggle='tab' data-bs-target='#kr' type='button' role='tab' aria-controls='kr' aria-selected='false'>
                                    <img src='https://flagcdn.com/w320/kr.png' alt='Korean Flag' class='flag-icon' style=' width: 36px; 
            margin-right: 8px;'>{$current_texts['language']['kr']}
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class='card-body'>
                        <div class='tab-content' id='languageTabsContent'>
                            <div class='tab-pane fade show active' id='th' role='tabpanel' aria-labelledby='th-tab'>
                                <div style='margin: 10px;'>
                                    <label><span>{$current_texts['subject']}</span>:</label>
                                    <input type='text' class='form-control' id='shop_subject' name='shop_subject' value='" . htmlspecialchars($row['subject_shop']) . "'>
                                </div>
                                <div style='margin: 10px;'>
                                    <label><span>{$current_texts['description']}</span>:</label>
                                    <textarea class='form-control' id='shop_description' name='shop_description'>" . htmlspecialchars($row['description_shop']) . "</textarea>
                                </div>
                                <div style='margin: 10px;'>
                                    <label><span>{$current_texts['content']}</span>:</label>
                                    <textarea class='form-control summernote' id='summernote_update' name='shop_content'>" . $content_th_with_correct_paths . "</textarea>
                                </div>
                            </div>
                            <div class='tab-pane fade' id='en' role='tabpanel' aria-labelledby='en-tab'>
                                <div style='display: flex; justify-content: flex-end; margin-bottom: 10px;'>
                                    <button type='button' id='copyFromThaiEn' class='btn btn-info btn-sm float-end mb-2'>{$current_texts['translate_button']}</button>
                                    <div id='loadingIndicatorEn' class='loading-overlay' style='display: none;'>
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
                            <div class='tab-pane fade' id='cn' role='tabpanel' aria-labelledby='cn-tab'>
                                <div style='display: flex; justify-content: flex-end; margin-bottom: 10px;'>
                                    <button type='button' id='copyFromThaiCn' class='btn btn-info btn-sm float-end mb-2'>{$current_texts['translate_button']}</button>
                                    <div id='loadingIndicatorCn' class='loading-overlay' style='display: none;'>
                                        <div class='loading-spinner'></div>
                                    </div>
                                </div>
                                <div style='margin: 10px;'>
                                    
                                    <label><span>Subject (CN)</span>:</label>
                                    <input type='text' class='form-control' id='shop_subject_cn' name='shop_subject_cn' value='" . htmlspecialchars($row['subject_shop_cn']) . "'>
                                </div>
                                <div style='margin: 10px;'>
                                    <label><span>Description (CN)</span>:</label>
                                    <textarea class='form-control' id='shop_description_cn' name='shop_description_cn'>" . htmlspecialchars($row['description_shop_cn']) . "</textarea>
                                </div>
                                <div style='margin: 10px;'>
                                    <label><span>Content (CN)</span>:</label>
                                    <textarea class='form-control summernote' id='summernote_update_cn' name='shop_content_cn'>" . $content_cn_with_correct_paths . "</textarea>
                                </div>
                            </div>
                            <div class='tab-pane fade' id='jp' role='tabpanel' aria-labelledby='jp-tab'>
                                <div style='display: flex; justify-content: flex-end; margin-bottom: 10px;'>
                                    <button type='button' id='copyFromThaiJp' class='btn btn-info btn-sm float-end mb-2'>{$current_texts['translate_button']}</button>
                                    <div id='loadingIndicatorJp' class='loading-overlay' style='display: none;'>
                                        <div class='loading-spinner'></div>
                                    </div>
                                </div>
                                <div style='margin: 10px;'>
                                    
                                    <label><span>Subject (JP)</span>:</label>
                                    <input type='text' class='form-control' id='shop_subject_jp' name='shop_subject_jp' value='" . htmlspecialchars($row['subject_shop_jp']) . "'>
                                </div>
                                <div style='margin: 10px;'>
                                    <label><span>Description (JP)</span>:</label>
                                    <textarea class='form-control' id='shop_description_jp' name='shop_description_jp'>" . htmlspecialchars($row['description_shop_jp']) . "</textarea>
                                </div>
                                <div style='margin: 10px;'>
                                    <label><span>Content (JP)</span>:</label>
                                    <textarea class='form-control summernote' id='summernote_update_jp' name='shop_content_jp'>" . $content_jp_with_correct_paths . "</textarea>
                                </div>
                            </div>
                            <div class='tab-pane fade' id='kr' role='tabpanel' aria-labelledby='kr-tab'>
                                <div style='display: flex; justify-content: flex-end; margin-bottom: 10px;'>
                                    <button type='button' id='copyFromThaiKr' class='btn btn-info btn-sm float-end mb-2'>{$current_texts['translate_button']}</button>
                                    <div id='loadingIndicatorKr' class='loading-overlay' style='display: none;'>
                                        <div class='loading-spinner'></div>
                                    </div>
                                </div>
                                <div style='margin: 10px;'>
                                    
                                    <label><span>Subject (KR)</span>:</label>
                                    <input type='text' class='form-control' id='shop_subject_kr' name='shop_subject_kr' value='" . htmlspecialchars($row['subject_shop_kr']) . "'>
                                </div>
                                <div style='margin: 10px;'>
                                    <label><span>Description (KR)</span>:</label>
                                    <textarea class='form-control' id='shop_description_kr' name='shop_description_kr'>" . htmlspecialchars($row['description_shop_kr']) . "</textarea>
                                </div>
                                <div style='margin: 10px;'>
                                    <label><span>Content (KR)</span>:</label>
                                    <textarea class='form-control summernote' id='summernote_update_kr' name='shop_content_kr'>" . $content_kr_with_correct_paths . "</textarea>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
                </div>
                <div style='margin: 10px; text-align: end;'>
                    <button type='button' id='submitEditshop' class='btn btn-success'>
                        <i class='fas fa-save'></i> {$current_texts['save_button']}
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
    echo "<div class='alert alert-warning'>{$current_texts['no_data']}</div>";
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
            } else if (target === '#cn') {
                if ($('#summernote_update_cn').data('summernote')) {
                    $('#summernote_update_cn').summernote('destroy');
                }
                $('#summernote_update_cn').summernote({
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
            } else if (target === '#jp') { // เพิ่มสำหรับภาษาญี่ปุ่น
                if ($('#summernote_update_jp').data('summernote')) {
                    $('#summernote_update_jp').summernote('destroy');
                }
                $('#summernote_update_jp').summernote({
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
            } else if (target === '#kr') { // เพิ่มสำหรับภาษาเกาหลี
                if ($('#summernote_update_kr').data('summernote')) {
                    $('#summernote_update_kr').summernote('destroy');
                }
                $('#summernote_update_kr').summernote({
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

        $('#copyFromThaiEn').on('click', function () {
            $('#loadingIndicatorEn').show(); 
            var thaiSubject = $('#shop_subject').val();
            var thaiDescription = $('#shop_description').val();
            var thaiContent = $('#summernote_update').summernote('code');

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
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'การแปลสำเร็จแล้ว!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'การแปลล้มเหลว: ' + (response.message || response.error),
                    });
                }
            })
            .catch(error => {
                console.error("error:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error,
                });
            })
            .finally(() => {
                $('#loadingIndicatorEn').hide();
            });
        });

        $('#copyFromThaiCn').on('click', function () {
            $('#loadingIndicatorCn').show(); 
            var thaiSubject = $('#shop_subject').val();
            var thaiDescription = $('#shop_description').val();
            var thaiContent = $('#summernote_update').summernote('code');

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
                    $('#shop_subject_cn').val(response.subject);
                    $('#shop_description_cn').val(response.description);
                    $('#summernote_update_cn').summernote('code', response.content);
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'การแปลสำเร็จแล้ว!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'การแปลล้มเหลว: ' + (response.message || response.error),
                    });
                }
            })
            .catch(error => {
                console.error("error:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error,
                });
            })
            .finally(() => {
                $('#loadingIndicatorCn').hide();
            });
        });
        
        $('#copyFromThaiJp').on('click', function () {
            $('#loadingIndicatorJp').show(); 
            var thaiSubject = $('#shop_subject').val();
            var thaiDescription = $('#shop_description').val();
            var thaiContent = $('#summernote_update').summernote('code');

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
                    $('#shop_subject_jp').val(response.subject);
                    $('#shop_description_jp').val(response.description);
                    $('#summernote_update_jp').summernote('code', response.content);
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'การแปลสำเร็จแล้ว!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'การแปลล้มเหลว: ' + (response.message || response.error),
                    });
                }
            })
            .catch(error => {
                console.error("error:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error,
                });
            })
            .finally(() => {
                $('#loadingIndicatorJp').hide();
            });
        });

        // เพิ่มส่วนสำหรับภาษาเกาหลี (kr)
        $('#copyFromThaiKr').on('click', function () {
            $('#loadingIndicatorKr').show(); 
            var thaiSubject = $('#shop_subject').val();
            var thaiDescription = $('#shop_description').val();
            var thaiContent = $('#summernote_update').summernote('code');

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
                    $('#shop_subject_kr').val(response.subject);
                    $('#shop_description_kr').val(response.description);
                    $('#summernote_update_kr').summernote('code', response.content);
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'การแปลสำเร็จแล้ว!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'การแปลล้มเหลว: ' + (response.message || response.error),
                    });
                }
            })
            .catch(error => {
                console.error("error:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error,
                });
            })
            .finally(() => {
                $('#loadingIndicatorKr').hide();
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
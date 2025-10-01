<?php
// ini_set('display_errors', 1); // สามารถเปิดใช้งานเพื่อ debug
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

include '../../../lib/connect.php'; // ตรวจสอบว่ารวม connect.php ด้วย
include '../../../lib/base_directory.php'; // ตรวจสอบว่ารวม base_directory.php ด้วย
include '../check_permission.php';

// ส่วนที่เพิ่ม: ตรวจสอบและกำหนดภาษาจาก URL
// session_start();
$lang = 'th'; // กำหนดภาษาเริ่มต้นเป็น 'th'
if (isset($_GET['lang'])) {
    $supportedLangs = ['th', 'en', 'cn', 'jp', 'kr'];
    $newLang = $_GET['lang'];
    if (in_array($newLang, $supportedLangs)) {
        $_SESSION['lang'] = $newLang;
        $lang = $newLang;
    } else {
        unset($_SESSION['lang']);
    }
} else {
    // ถ้าไม่มี lang ใน URL ให้ใช้ค่าจาก Session หรือค่าเริ่มต้น 'th'
    if (isset($_SESSION['lang'])) {
        $lang = $_SESSION['lang'];
    }
}

// ส่วนที่เพิ่ม: กำหนดข้อความตามแต่ละภาษา
$texts = [
    'page_title' => [
        'th' => 'ตั้งค่าร้านค้า',
        'en' => 'Setup Shop',
        'cn' => '店铺设置',
        'jp' => 'ショップ設定',
        'kr' => '상점 설정'
    ],
    'write_shop' => [
        'th' => 'เขียนร้านค้า',
        'en' => 'Write Shop',
        'cn' => '撰写店铺',
        'jp' => 'ショップを書く',
        'kr' => '상점 작성'
    ],
    'cover_photo' => [
        'th' => 'รูปภาพหน้าปก',
        'en' => 'Cover photo',
        'cn' => '封面图片',
        'jp' => '表紙の写真',
        'kr' => '표지 사진'
    ],
    'cover_photo_size' => [
        'th' => 'ขนาดรูปภาพที่เหมาะสม width: 350px และ height: 250px',
        'en' => 'Appropriate image size: width: 350px and height: 250px',
        'cn' => '合适的图片尺寸：宽: 350px 高: 250px',
        'jp' => '適切な画像サイズ：幅: 350px 高さ: 250px',
        'kr' => '적절한 이미지 크기: 너비: 350px 높이: 250px'
    ],
    'subject' => [
        'th' => 'หัวข้อ',
        'en' => 'Subject',
        'cn' => '主题',
        'jp' => '件名',
        'kr' => '제목'
    ],
    'description' => [
        'th' => 'รายละเอียด',
        'en' => 'Description',
        'cn' => '描述',
        'jp' => '説明',
        'kr' => '설명'
    ],
    'parent_group' => [
        'th' => 'กลุ่มแม่',
        'en' => 'Parent Group',
        'cn' => '主分类',
        'jp' => '親グループ',
        'kr' => '상위 그룹'
    ],
    'select_parent_group' => [
        'th' => '-- เลือกกลุ่มแม่ --',
        'en' => '-- Select Parent Group --',
        'cn' => '-- 选择主分类 --',
        'jp' => '-- 親グループを選択 --',
        'kr' => '-- 상위 그룹 선택 --'
    ],
    'sub_group' => [
        'th' => 'กลุ่มย่อย',
        'en' => 'Sub Group',
        'cn' => '子分类',
        'jp' => 'サブグループ',
        'kr' => '하위 그룹'
    ],
    'select_sub_group' => [
        'th' => '-- เลือกกลุ่มย่อย --',
        'en' => '-- Select Sub Group --',
        'cn' => '-- 选择子分类 --',
        'jp' => '-- サブグループを選択 --',
        'kr' => '-- 하위 그룹 선택 --'
    ],
    'error_message' => [
        'th' => '-- เกิดข้อผิดพลาด --',
        'en' => '-- An error occurred --',
        'cn' => '-- 发生错误 --',
        'jp' => '-- エラーが発生しました --',
        'kr' => '-- 오류 발생 --'
    ],
    'add_shop_button' => [
        'th' => 'เพิ่มร้านค้า',
        'en' => 'Add Shop',
        'cn' => '添加店铺',
        'jp' => 'ショップを追加',
        'kr' => '상점 추가'
    ],
    'back_button' => [
        'th' => 'กลับ',
        'en' => 'Back',
        'cn' => '返回',
        'jp' => '戻る',
        'kr' => '뒤로'
    ],
    'content' => [
        'th' => 'เนื้อหา',
        'en' => 'Content',
        'cn' => '内容',
        'jp' => '内容',
        'kr' => '내용'
    ],
];

// ฟังก์ชันสำหรับเรียกใช้ข้อความตามภาษาที่เลือก
function getTextByLang($key) {
    global $texts, $lang;
    return $texts[$key][$lang] ?? $texts[$key]['th'];
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= getTextByLang('page_title') ?></title>

    <link rel="icon" type="image/x-icon" href="https://www.trandar.com//public/news_img/%E0%B8%94%E0%B8%B5%E0%B9%84%E0%B8%8B%E0%B8%99%E0%B9%8C%E0%B8%97%E0%B8%B5%E0%B9%88%E0%B8%A2%E0%B8%B1%E0%B8%87%E0%B9%84%E0%B8%A1%E0%B9%88%E0%B9%84%E0%B8%94%E0%B9%89%E0%B8%95%E0%B8%B1%E0%B9%89%E0%B8%87%E0%B8%8A%E0%B8%B7%E0%B9%88%E0%B8%AD_5.png">
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
    <link href='../css/index_.css?v=<?php echo time();?>' rel='stylesheet'>
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
                        <i class="fa-solid fa-pen-clip"></i>
                        <?= getTextByLang('write_shop') ?>
                    </h4>

                    <form id="formshop" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-4">
                                <div style="margin: 10px;">
                                    <label for="">
                                        <span><?= getTextByLang('cover_photo') ?></span>:
                                        <div><span><?= getTextByLang('cover_photo_size') ?></span></div>
                                    </label>
                                    <div class="previewContainer">
                                        <img id="previewImage" src="" alt="Image Preview" style="max-width: 100%; display: none;">
                                    </div>
                                </div>
                                <div style="margin: 10px;">
                                    <input type="file" class="form-control" id="fileInput" name="fileInput"> </div>
                                <div style="margin: 10px;">
                                    <label for="">
                                        <span><?= getTextByLang('subject') ?></span>:
                                    </label>
                                    <input type="text" class="form-control" id="shop_subject" name="shop_subject">
                                </div>
                                <div style="margin: 10px;">
                                    <label for="">
                                        <span><?= getTextByLang('description') ?></span>:
                                    </label>
                                    <div>
                                        <textarea class="form-control" id="shop_description" name="shop_description"></textarea>
                                    </div>
                                </div>
                                <div style="margin: 10px;">
                                    <label><span><?= getTextByLang('parent_group') ?></span>:</label>
                                    <select id='main_group_select' class='form-control'>
                                        <option value=''><?= getTextByLang('select_parent_group') ?></option>
                                        <?php
                                        // ดึงข้อมูลกลุ่มแม่จากฐานข้อมูล
                                        $mainGroupQuery = $conn->query("SELECT group_id, group_name FROM dn_shop_groups WHERE parent_group_id IS NULL ORDER BY group_name ASC");
                                        while ($group = $mainGroupQuery->fetch_assoc()) {
                                            echo "<option value='{$group['group_id']}'>{$group['group_name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div style="margin: 10px;">
                                    <label><span><?= getTextByLang('sub_group') ?></span>:</label>
                                    <select id='sub_group_select' name='group_id' class='form-control'>
                                        <option value=''><?= getTextByLang('select_sub_group') ?></option>
                                    </select>
                                </div>
                                <div style="margin: 10px; text-align: end;">
                                    <button
                                        type="button"
                                        id="submitAddshop"
                                        class="btn btn-primary">
                                        <i class="fas fa-plus"></i>
                                        <?= getTextByLang('add_shop_button') ?>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div style='margin: 10px; text-align: end;'>
                                    <button type='button' id='backToShopList' class='btn btn-secondary'> 
                                        <i class='fas fa-arrow-left'></i> <?= getTextByLang('back_button') ?>
                                    </button>
                                </div>
                                <div style="margin: 10px;">
                                    <label for="">
                                        <span><?= getTextByLang('content') ?></span>:
                                    </label>
                                    <div>
                                        <textarea class="form-control summernote" id="summernote" name="shop_content"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
    document.getElementById('fileInput').addEventListener('change', function(e) {
        const previewImage = document.getElementById('previewImage');
        if (e.target.files.length > 0) {
            const file = e.target.files[0];
            const reader = new FileReader();
            reader.onload = function(evt) {
                previewImage.src = evt.target.result;
                previewImage.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            previewImage.src = "";
            previewImage.style.display = 'none';
        }
    });

    // Logic สำหรับโหลดกลุ่มย่อยเมื่อเลือกกลุ่มแม่
    $('#main_group_select').on('change', function() {
        var mainGroupId = $(this).val();
        if (!mainGroupId) {
            $('#sub_group_select').html('<option value=""><?= getTextByLang("select_sub_group") ?></option>');
            return;
        }

        $.ajax({
            url: 'actions/get_sub_groups.php', // ตรวจสอบ Path ของไฟล์นี้
            type: 'POST',
            data: { main_group_id: mainGroupId },
            success: function(response) {
                $('#sub_group_select').html(response);
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                $('#sub_group_select').html('<option value=""><?= getTextByLang("error_message") ?></option>');
            }
        });
    });
</script>

<script src='../js/index_.js?v=<?php echo time();?>'></script>
<script src='js/shop_.js?v=<?php echo time();?>'></script>

</body>

</html>
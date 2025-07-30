<?php
// ini_set('display_errors', 1); // สามารถเปิดใช้งานเพื่อ debug
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

include '../../../lib/connect.php'; // ตรวจสอบว่ารวม connect.php ด้วย
include '../../../lib/base_directory.php'; // ตรวจสอบว่ารวม base_directory.php ด้วย
include '../check_permission.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>setup shop</title>

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

    <link href='../css/index_.css?v=<?php echo time();?>' rel='stylesheet'>

    <style>
        

        /* สไตล์เฉพาะสำหรับพื้นที่ Summernote Editor */
        .note-editable {
            /* font-family: sans-serif, "Kanit", "Roboto" !important; ใช้ตามที่คุณต้องการให้ sans-serif เป็นอันดับแรก */
            color: #424242;
            font-size: 16px;
            line-height: 1.5;
            /* กำหนด min-height/max-height ที่นี่ ถ้าต้องการ override ค่าจาก JS */
            /* min-height: 600px; */
            /* max-height: 600px; */
            /* overflow: auto; */ /* เพื่อให้มี scrollbar ถ้าเนื้อหาเกิน */
        }
        .box-content p {
            /* font-family: sans-serif */
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
                        write shop
                    </h4>

                    <form id="formshop" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-4">
                                <div style="margin: 10px;">
                                    <label for="">
                                        <span>Cover photo</span>:
                                        <div><span>ขนาดรูปภาพที่เหมาะสม width: 350px และ height: 250px</span></div>
                                    </label>
                                    <div class="previewContainer">
                                        <img id="previewImage" src="" alt="Image Preview" style="max-width: 100%; display: none;">
                                    </div>
                                </div>

                                <div style="margin: 10px;">
                                    <input type="file" class="form-control" id="fileInput" name="fileInput"> </div>
                                <div style="margin: 10px;">
                                    <label for="">
                                        <span>Subject</span>:
                                    </label>
                                    <input type="text" class="form-control" id="shop_subject" name="shop_subject">
                                </div>
                                <div style="margin: 10px;">
                                    <label for="">
                                        <span>Description</span>:
                                    </label>
                                    <div>
                                        <textarea class="form-control" id="shop_description" name="shop_description"></textarea>
                                    </div>
                                </div>

                                <div style="margin: 10px;">
                                    <label><span>กลุ่มแม่</span>:</label>
                                    <select id='main_group_select' class='form-control'>
                                        <option value=''>-- เลือกกลุ่มแม่ --</option>
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
                                    <label><span>กลุ่มย่อย</span>:</label>
                                    <select id='sub_group_select' name='group_id' class='form-control'>
                                        <option value=''>-- เลือกกลุ่มย่อย --</option>
                                    </select>
                                </div>
                                <div style="margin: 10px; text-align: end;">
                                    <button
                                    type="button"
                                    id="submitAddshop"
                                    class="btn btn-primary">
                                        <i class="fas fa-plus"></i>
                                        shop
                                    </button>
                                </div>
                            </div>
                                            
                            <div class="col-md-8">
                                 <div style='margin: 10px; text-align: end;'>
                                <button type='button' id='backToShopList' class='btn btn-secondary'> 
                                    <i class='fas fa-arrow-left'></i> Back 
                                </button>
                            </div>
                                <div style="margin: 10px;">
                                    <label for="">
                                        <span>Content</span>:
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
        $('#sub_group_select').html('<option value="">-- เลือกกลุ่มย่อย --</option>');
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
            $('#sub_group_select').html('<option value="">-- เกิดข้อผิดพลาด --</option>');
        }
    });
});
</script>

<script src='../js/index_.js?v=<?php echo time();?>'></script>
<script src='js/shop_.js?v=<?php echo time();?>'></script>

</body>

</html>
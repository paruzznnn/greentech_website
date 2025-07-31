<?php
// edit_logo.php
include '../check_permission.php'; // ตรวจสอบสิทธิ์การเข้าถึง

// Include database connection and base_directory.php
// // สมมติว่าไฟล์ connect.php และ base_directory.php อยู่ใน lib/
// require_once(__DIR__ . '/../../../../lib/connect.php');
// require_once(__DIR__ . '/../../../../lib/base_directory.php'); // ถ้ามี $base_path

global $conn;

// ในกรณีของโลโก้ เราจะดึงข้อมูลจาก ID เดียวคือ 1 เสมอ
$logo_id = 1;

$stmt = $conn->prepare("SELECT id, image_path, image_modal_path FROM logo_settings WHERE id = ?");
$stmt->bind_param("i", $logo_id);
$stmt->execute();
$result = $stmt->get_result();
$logo = $result->fetch_assoc();
$stmt->close();

// หากไม่พบข้อมูลโลโก้ (ซึ่งไม่ควรเกิดขึ้นหากมีการแทรกข้อมูลเริ่มต้นแล้ว)
if (!$logo) {
    $logo = [
        'id' => 1,
        'image_path' => '../public/img/LOGOTRAND.png', // Path โลโก้ default หากไม่พบใน DB
        'image_modal_path' => '../public/img/trandar.jpg' // Path รูปภาพ Modal default หากไม่พบใน DB
    ];
    // สามารถเพิ่ม logic เพื่อ insert ค่า default เข้าไปใน DB ได้ที่นี่ หากจำเป็น
}

// ดึงข้อมูล contact settings
$contact_settings_id = 1; // สมมติว่ามี ID 1 สำหรับการตั้งค่าหลัก
$contact_settings = [
    'trandar_store_link' => '',
    'trandar_store_text' => '',
    'facebook_link' => '',
    'youtube_link' => '',
    'instagram_link' => '',
    'line_link' => '',
    'tiktok_link' => ''
];

$stmt_contact = $conn->prepare("SELECT trandar_store_link, trandar_store_text, facebook_link, youtube_link, instagram_link, line_link, tiktok_link FROM contact_settings WHERE id = ?");
$stmt_contact->bind_param("i", $contact_settings_id);
$stmt_contact->execute();
$result_contact = $stmt_contact->get_result();
if ($data_contact = $result_contact->fetch_assoc()) {
    $contact_settings = $data_contact;
}
$stmt_contact->close();

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขโลโก้และข้อมูลติดต่อ</title>

    <link rel="icon" type="image/x-icon" href="../../../public/img/q-removebg-preview1.png">

    <link href="../../../inc/jquery/css/jquery-ui.css" rel="stylesheet">
    <script src="../../../inc/jquery/js/jquery-3.6.0.min.js"></script>
    <script src="../../../inc/jquery/js/jquery-ui.min.js"></script>

    <link href="../../../inc/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="../../../inc/bootstrap/js/bootstrap.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/fontawesome5-fullcss@1.1.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css">

    <link href="../../../inc/sweetalert2/css/sweetalert2.min.css" rel="stylesheet">
    <script src="../../../inc/sweetalert2/js/sweetalert2.all.min.js"></script>

    <link href="../../../inc/select2/css/select2.min.css" rel="stylesheet">
    <script src="../../../inc/select2/js/select2.min.js"></script>

    <link href="https://cdn.datatables.net/v/dt/dt-2.1.4/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/dt/dt-2.1.4/datatables.min.js"></script>

    <link href='../css/index_.css?v=<?php echo time(); ?>' rel='stylesheet'>

    <style>
        /* สไตล์ที่คุณมีอยู่แล้ว สามารถนำมาใช้ได้ */
        .btn-circle {
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-edit {
            background-color: #FFC107;
            color: white;
        }
        .btn-del {
            background-color: #DC3545;
            color: white;
        }
        .banner-img { /* อาจจะเปลี่ยนเป็น .logo-img */
            height: 60px;
            object-fit: contain; /* เปลี่ยนเป็น contain เพื่อรักษาสัดส่วน */
            border: 1px solid #ccc;
        }
        .line-ref {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
            border-left: 5px solid #f57c00;
            padding-left: 10px;
            color: #333;
        }
        .previewContainer img {
            max-width: 100%;
            height: auto;
            display: block;
            border: 1px solid #ccc;
            padding: 5px;
            border-radius: 4px;
        }
        /* Added for loading overlay */
        #loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
</head>
<body>
<?php include '../template/header.php'; ?>

<div id="loading-overlay">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<div class="container mt-4">
    <div style="gap :20px"><h5>
        <div style="padding-bottom :5px">ขนาดรูปภาพที่แนะนำสำหรับโลโก้หลัก: กว้าง 100px; สูง 55px;</div>
        <div style="padding-bottom :5px">ขนาดรูปภาพที่แนะนำสำหรับโลโก้ใน Modal: กว้าง 70% ของ Modal (ประมาณ 245px); สูงอัตโนมัติ;</div>
    </h5></div>
    <div class="box-content p-4 bg-light rounded shadow-sm">
        <h4 class="line-ref">
            <i class="fa-solid fa-image"></i> แก้ไขโลโก้เว็บไซต์
        </h4>

        <form id="editLogoForm" enctype="multipart/form-data">
            <input type="hidden" name="logo_id" value="<?= htmlspecialchars($logo['id']) ?>">
            <input type="hidden" name="old_image_path" value="<?= htmlspecialchars($logo['image_path']) ?>">
            <input type="hidden" name="old_image_modal_path" value="<?= htmlspecialchars($logo['image_modal_path']) ?>">

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-section">
                        <label>ภาพโลโก้หลักปัจจุบัน:</label>
                        <div class="previewContainer mb-2">
                            <img id="currentImage" src="<?= htmlspecialchars($logo['image_path']) ?>" alt="Current Logo" class="img-thumbnail">
                            <img id="previewNewImage" src="#" alt="New Logo Preview" style="display:none; margin-top: 10px;">
                        </div>
                        <label for="image">เลือกรูปภาพโลโก้หลักใหม่:</label>
                        <input type="file" class="form-control" name="image" id="image" onchange="previewFile('image', 'currentImage', 'previewNewImage')">
                        <small class="form-text text-muted">เลือกไฟล์ใหม่เพื่อเปลี่ยนรูปภาพโลโก้หลัก หากไม่เลือก รูปภาพเดิมจะถูกใช้</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-section">
                        <label>ภาพโลโก้ใน Modal ปัจจุบัน:</label>
                        <div class="previewContainer mb-2">
                            <img id="currentImageModal" src="<?= htmlspecialchars($logo['image_modal_path']) ?>" alt="Current Modal Logo" class="img-thumbnail">
                            <img id="previewNewImageModal" src="#" alt="New Modal Logo Preview" style="display:none; margin-top: 10px;">
                        </div>
                        <label for="image_modal">เลือกรูปภาพโลโก้ใน Modal ใหม่:</label>
                        <input type="file" class="form-control" name="image_modal" id="image_modal" onchange="previewFile('image_modal', 'currentImageModal', 'previewNewImageModal')">
                        <small class="form-text text-muted">เลือกไฟล์ใหม่เพื่อเปลี่ยนรูปภาพโลโก้ใน Modal หากไม่เลือก รูปภาพเดิมจะถูกใช้</small>
                    </div>
                </div>
            </div>

            <h4 class="line-ref mt-5">
                <i class="fa-solid fa-share-nodes"></i> แก้ไขข้อมูลติดต่อและ Social Media
            </h4>
            <input type="hidden" name="contact_settings_id" value="<?= htmlspecialchars($contact_settings_id) ?>">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="trandar_store_link" class="form-label">ลิงก์ Trandar Store:</label>
                    <input type="url" class="form-control" id="trandar_store_link" name="trandar_store_link" value="<?= htmlspecialchars($contact_settings['trandar_store_link']) ?>">
                </div>
                <div class="col-md-6">
                    <label for="trandar_store_text" class="form-label">ข้อความ Trandar Store:</label>
                    <input type="text" class="form-control" id="trandar_store_text" name="trandar_store_text" value="<?= htmlspecialchars($contact_settings['trandar_store_text']) ?>">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="facebook_link" class="form-label">ลิงก์ Facebook:</label>
                    <input type="url" class="form-control" id="facebook_link" name="facebook_link" value="<?= htmlspecialchars($contact_settings['facebook_link']) ?>">
                </div>
                <div class="col-md-6">
                    <label for="youtube_link" class="form-label">ลิงก์ YouTube:</label>
                    <input type="url" class="form-control" id="youtube_link" name="youtube_link" value="<?= htmlspecialchars($contact_settings['youtube_link']) ?>">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="instagram_link" class="form-label">ลิงก์ Instagram:</label>
                    <input type="url" class="form-control" id="instagram_link" name="instagram_link" value="<?= htmlspecialchars($contact_settings['instagram_link']) ?>">
                </div>
                <div class="col-md-6">
                    <label for="line_link" class="form-label">ลิงก์ Line:</label>
                    <input type="url" class="form-control" id="line_link" name="line_link" value="<?= htmlspecialchars($contact_settings['line_link']) ?>">
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="tiktok_link" class="form-label">ลิงก์ TikTok:</label>
                    <input type="url" class="form-control" id="tiktok_link" name="tiktok_link" value="<?= htmlspecialchars($contact_settings['tiktok_link']) ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-12 text-end">
                    <button type="submit" id="submitEditLogo" class="btn btn-primary">
                        <i class="fas fa-edit"></i> อัปเดตข้อมูล
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src='../js/index_.js?v=<?php echo time(); ?>'></script>
<script>
    function previewFile(inputId, currentImgId, previewImgId) {
        const previewCurrent = document.getElementById(currentImgId);
        const previewNew = document.getElementById(previewImgId);
        const file = document.getElementById(inputId).files[0];
        const reader = new FileReader();

        reader.onloadend = function () {
            previewNew.src = reader.result;
            previewNew.style.display = 'block'; // แสดงรูปภาพใหม่
            previewCurrent.style.display = 'none'; // ซ่อนรูปภาพปัจจุบัน
        }

        if (file) {
            reader.readAsDataURL(file);
        } else {
            previewNew.src = "";
            previewNew.style.display = 'none';
            previewCurrent.style.display = 'block'; // แสดงรูปภาพปัจจุบันกลับมา
        }
    }

    function alertError(textAlert) {
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
        Toast.fire({
            icon: "error",
            title: textAlert
        });
    }

    $(document).ready(function() {
        $('#submitEditLogo').on('click', function(e) {
            e.preventDefault(); // ป้องกันการ submit form ปกติ

            var formData = new FormData($('#editLogoForm')[0]);
            formData.append('action', 'edit_all_settings'); // ระบุ action สำหรับการแก้ไขทั้งหมด

            Swal.fire({
                title: "ยืนยันการแก้ไข?",
                text: "คุณต้องการอัปเดตข้อมูลทั้งหมดใช่หรือไม่!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#FFC107", // สีเหลืองสำหรับแก้ไข
                cancelButtonColor: "#d33",
                confirmButtonText: "อัปเดต"
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#loading-overlay').fadeIn(); // แสดง loading overlay

                    $.ajax({
                        url: "actions/process_logo.php", // ไฟล์ PHP ที่จะใช้ประมวลผลการอัปเดต
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json', // คาดหวัง JSON response
                        success: function(response) {
                            $('#loading-overlay').fadeOut(); // ซ่อน loading overlay
                            if (response.status === 'success') {
                                Swal.fire(
                                    'สำเร็จ!',
                                    'แก้ไขข้อมูลเรียบร้อยแล้ว.',
                                    'success'
                                ).then(() => {
                                    location.reload(); // รีโหลดหน้าเพื่อแสดงข้อมูลใหม่
                                });
                            } else {
                                Swal.fire(
                                    'เกิดข้อผิดพลาด!',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            $('#loading-overlay').fadeOut(); // ซ่อน loading overlay
                            console.error("AJAX Error:", status, error, xhr.responseText);
                            Swal.fire(
                                'เกิดข้อผิดพลาด!',
                                'ไม่สามารถแก้ไขข้อมูลได้: ' + error,
                                'error'
                            );
                        }
                    });
                }
            });
        });
    });
</script>

</body>
</html>
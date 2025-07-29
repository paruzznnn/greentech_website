<?php
// edit_logo.php
include '../check_permission.php';

// ** ต้องมีสองบรรทัดนี้ เพื่อให้เข้าถึง $conn และ $base_path ได้ **
require_once(__DIR__ . '/../../../lib/connect.php'); // Include database connection
require_once(__DIR__ . '/../../../lib/base_directory.php'); // Include base_directory.php for $base_path

// กำหนดให้ ID เป็น 1 เสมอสำหรับโลโก้หลัก
$logo_id = 1;

// ดึงข้อมูลโลโก้จากฐานข้อมูล
$stmt = $conn->prepare("SELECT id, image_path FROM logo_settings WHERE id = ?");
$stmt->bind_param("i", $logo_id);
$stmt->execute();
$result = $stmt->get_result();
$logo = $result->fetch_assoc();
$stmt->close();

// หากไม่พบโลโก้ ให้ตั้งค่า Path เริ่มต้น (เช่น รูป default) หรือ redirect
if (!$logo) {
    // สามารถตั้งค่า Path รูปภาพ default ได้ที่นี่
    $logo = [
        'id' => 1,
        'image_path' => '/public/img/LOGOTRAND.png' // Path รูปภาพ default
    ];
    // หรือ redirect ไปหน้าแจ้งเตือน
    // echo "<script>alert('Logo settings not found. Please set up default logo first.'); window.location.href='../dashboard.php';</script>";
    // exit;
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Logo</title>

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

        .logo-img { /* เปลี่ยนจาก .banner-img เป็น .logo-img */
            height: 60px; /* อาจจะปรับให้เหมาะสมกับโลโก้ */
            object-fit: contain; /* เปลี่ยนเป็น contain เพื่อให้โลโก้ไม่ถูกตัด */
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
    <div style="gap :20px">
        <h5>
            <div style="padding-bottom :5px">ความสูงรูปภาพ: (ปรับตามขนาดโลโก้)px;</div>
            <div style="padding-bottom :5px">ความกว้างรูปภาพ: (ปรับตามขนาดโลโก้)px;</div>
        </h5>
    </div>
    <div class="box-content p-4 bg-light rounded shadow-sm">
        <h4 class="line-ref">
            <i class="fa-solid fa-image"></i> แก้ไข Logo
        </h4>

        <form id="editLogoForm" enctype="multipart/form-data">
            <input type="hidden" name="logo_id" value="<?= htmlspecialchars($logo['id']) ?>">
            <input type="hidden" name="old_image_path" value="<?= htmlspecialchars($logo['image_path']) ?>">
            <input type="hidden" name="action" value="edit_logo"> <div class="row">
                <div class="col-md-4">
                    <div class="form-section">
                        <label>ภาพปัจจุบัน:</label>
                        <div class="previewContainer mb-2">
                            <img id="currentImage" src="<?= htmlspecialchars($base_path . $logo['image_path']) ?>" alt="Current Logo" class="img-thumbnail">
                            <img id="previewNewImage" src="#" alt="New Logo Preview" style="display:none; margin-top: 10px;">
                        </div>
                        <label for="image">เลือกรูปภาพใหม่:</label>
                        <input type="file" class="form-control" name="image" id="image" onchange="previewFile()">
                        <small class="form-text text-muted">เลือกไฟล์ใหม่เพื่อเปลี่ยนรูปภาพ หากไม่เลือก รูปภาพเดิมจะถูกใช้</small>
                    </div>
                </div>

                <div class="col-md-8 d-flex align-items-end">
                    <div class="form-section w-100 text-end">
                        <button type="submit" id="submitEditLogo" class="btn btn-primary">
                            <i class="fas fa-edit"></i> อัปเดต
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script src='../js/index_.js?v=<?php echo time(); ?>'></script>
<script>
    function previewFile() {
        const previewCurrent = document.getElementById('currentImage');
        const previewNew = document.getElementById('previewNewImage');
        const file = document.getElementById('image').files[0];
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
            // formData.append('action', 'edit_logo'); // ไม่ต้องเพิ่มตรงนี้เพราะมี hidden input แล้ว

            Swal.fire({
                title: "ยืนยันการแก้ไข?",
                text: "คุณต้องการอัปเดตโลโก้นี้ใช่หรือไม่!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#FFC107", // สีเหลืองสำหรับแก้ไข
                cancelButtonColor: "#d33",
                confirmButtonText: "อัปเดต"
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#loading-overlay').fadeIn(); // แสดง loading overlay

                    $.ajax({
                        url: "actions/process_logo.php", // เรียกไปยัง process_logo.php
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
                                    'แก้ไขโลโก้เรียบร้อยแล้ว.',
                                    'success'
                                ).then(() => {
                                    // หากอัปเดตสำเร็จ ให้อัปเดตรูปภาพที่แสดงในหน้าโดยไม่ต้องโหลดหน้าใหม่
                                    // หรือจะโหลดหน้าใหม่ก็ได้ถ้าต้องการความชัวร์
                                    // window.location.reload(); 
                                    // อัปเดตรูปภาพปัจจุบัน
                                    $('#currentImage').attr('src', '<?= htmlspecialchars($base_path) ?>' + response.new_image_path);
                                    $('#currentImage').show(); // แสดงรูปปัจจุบัน
                                    $('#previewNewImage').hide(); // ซ่อนรูปพรีวิว
                                    // อัปเดต old_image_path ใน hidden input
                                    $('input[name="old_image_path"]').val(response.new_image_path);
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
                                'ไม่สามารถแก้ไขโลโก้ได้: ' + error,
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
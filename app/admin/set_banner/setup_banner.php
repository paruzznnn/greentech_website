<?php
include '../check_permission.php';
// ตรวจสอบให้แน่ใจว่าได้ include lib/connect.php และ lib/base_directory.php เพื่อเข้าถึง $conn และ $base_path
// require_once(__DIR__ . '/../../../../lib/connect.php');
// require_once(__DIR__ . '/../../../../lib/base_directory.php'); // ต้องแน่ใจว่าไฟล์นี้มี $base_path
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Banner</title>

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
        .previewContainer img {
            max-width: 100%;
            display: none;
            border: 1px solid #ccc;
            padding: 5px;
            border-radius: 4px;
        }

        .form-section {
            margin: 10px;
        }

        .line-ref {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
            border-left: 5px solid #f57c00;
            padding-left: 10px;
            color: #333;
        }
        .btn-edit {
            background-color: #FFC107;
            color: white;
        }

        .btn-del {
            background-color: #DC3545;
            color: white;
        }

        .banner-img {
            height: 60px;
            object-fit: cover;
            border: 1px solid #ccc;
        }
        /* เพิ่มสไตล์สำหรับ overlay */
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
    <div style="gap: 20px"><h5>
        <div style="padding-bottom: 5px">ความสูงรูปภาพ: 300px;</div>
        <div style="padding-bottom: 5px">ความกว้างรูปภาพ: 1920px;</div>
    </h5></div>
    <div class="box-content p-4 bg-light rounded shadow-sm">
        <h4 class="line-ref">
            <i class="fa-solid fa-image"></i> เพิ่ม Banner
        </h4>

        <form id="bannerForm" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-section">
                        <label for="image">รูปภาพ:</label>
                        <div class="previewContainer">
                            <img id="previewImage" src="#" alt="Preview">
                        </div>
                        <input type="file" class="form-control mt-2" id="image" name="image" required onchange="previewFile()">
                    </div>
                </div>
                <div class="col-md-8 d-flex align-items-end">
                    <div class="form-section w-100 text-end">
                        <button type="submit" id="submitBanner" class="btn btn-primary">
                            <i class="fas fa-upload"></i> บันทึก
                        </button>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>

<script>
    function previewFile() {
        const preview = document.getElementById('previewImage');
        const file = document.getElementById('image').files[0];
        const reader = new FileReader();

        reader.onloadend = function () {
            preview.src = reader.result;
            preview.style.display = 'block';
        }

        if (file) {
            reader.readAsDataURL(file);
        } else {
            preview.src = "";
            preview.style.display = 'none';
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
        $('#submitBanner').on('click', function(e) {
            e.preventDefault(); // ป้องกันการ submit form ปกติ

            var formData = new FormData($('#bannerForm')[0]);
            formData.append('action', 'addbanner_single'); // ระบุ action สำหรับการอัปโหลดรูปภาพแบนเนอร์เดียว

            // ตรวจสอบว่ามีไฟล์รูปภาพถูกเลือกหรือไม่
            if ($('#image').get(0).files.length === 0) {
                alertError("กรุณาเลือกรูปภาพแบนเนอร์");
                return;
            }

            Swal.fire({
                title: "ยืนยันการบันทึก?",
                text: "คุณต้องการเพิ่มแบนเนอร์นี้ใช่หรือไม่!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#4CAF50",
                cancelButtonColor: "#d33",
                confirmButtonText: "บันทึก"
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#loading-overlay').fadeIn(); // แสดง loading overlay

                    $.ajax({
                        url: "actions/process_banner.php",
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
                                    'บันทึกแบนเนอร์เรียบร้อยแล้ว.',
                                    'success'
                                ).then(() => {
                                    window.location.href = 'list_banner.php'; // กลับไปหน้า list
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
                                'ไม่สามารถบันทึกแบนเนอร์ได้: ' + error,
                                'error'
                            );
                        }
                    });
                }
            });
        });
    });
</script>

<script src='../js/index_.js?v=<?php echo time(); ?>'></script>
</body>
</html>
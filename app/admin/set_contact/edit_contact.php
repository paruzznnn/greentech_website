<?php
// edit_contact.php
include '../check_permission.php';

// require_once(__DIR__ . '/../../../lib/connect.php');
// require_once(__DIR__ . '/../../../lib/base_directory.php'); // ตรวจสอบ Path ให้ถูกต้อง

$contact_id = 1; // ID ของ Contact Settings (เราใช้แค่ 1 ชุด)

$stmt = $conn->prepare("SELECT * FROM contact_settings WHERE id = ?");
$stmt->bind_param("i", $contact_id);
$stmt->execute();
$result = $stmt->get_result();
$contact_data = $result->fetch_assoc();
$stmt->close();

if (!$contact_data) {
    echo "<script>alert('Contact settings not found. Please ensure initial data is in the database.'); window.location.href='../dashboard.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลติดต่อ</title>

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
        .line-ref {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
            border-left: 5px solid #f57c00;
            padding-left: 10px;
            color: #333;
        }
        .form-section {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .form-section label {
            font-weight: bold;
            margin-bottom: 8px;
            display: block;
        }
        .image-preview {
            max-width: 150px;
            height: auto;
            border: 1px solid #ddd;
            padding: 5px;
            margin-top: 10px;
            display: block;
        }
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
    <div class="box-content p-4 bg-light rounded shadow-sm">
        <h4 class="line-ref">
            <i class="fa-solid fa-edit"></i> แก้ไขข้อมูลติดต่อ
        </h4>

        <form id="editContactForm" enctype="multipart/form-data">
            <input type="hidden" name="contact_id" value="<?= htmlspecialchars($contact_data['id']) ?>">
            <input type="hidden" name="action" value="edit_contact">
            <input type="hidden" name="current_link_image_path" value="<?= htmlspecialchars($contact_data['link_image_path']) ?>">

            <div class="form-section">
                <label for="company_name">ชื่อบริษัท:</label>
                <input type="text" id="company_name" name="company_name" class="form-control" value="<?= htmlspecialchars($contact_data['company_name']) ?>">
            </div>

            <div class="form-section">
                <label for="address">ที่อยู่:</label>
                <input type="text" id="address" name="address" class="form-control" value="<?= htmlspecialchars($contact_data['address']) ?>">
            </div>

            <div class="form-section">
                <label for="phone">เบอร์โทรศัพท์:</label>
                <input type="text" id="phone" name="phone" class="form-control" value="<?= htmlspecialchars($contact_data['phone']) ?>">
            </div>

            <div class="form-section">
                <label for="email">อีเมล:</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($contact_data['email']) ?>">
            </div>

            <div class="form-section">
                <label for="hours_weekday">เวลาทำการ (จันทร์-ศุกร์):</label>
                <input type="text" id="hours_weekday" name="hours_weekday" class="form-control" value="<?= htmlspecialchars($contact_data['hours_weekday']) ?>">
            </div>

            <div class="form-section">
                <label for="hours_saturday">เวลาทำการ (เสาร์):</label>
                <input type="text" id="hours_saturday" name="hours_saturday" class="form-control" value="<?= htmlspecialchars($contact_data['hours_saturday']) ?>">
            </div>

            <div class="form-section">
                <label for="link_image">รูปภาพลิงก์ (ถ้ามี):</label>
                <?php if ($contact_data['link_image_path']): ?>
                    <img src="<?= htmlspecialchars($contact_data['link_image_path']) ?>" alt="Link Image" class="image-preview" id="linkImagePreview">
                    <p class="text-muted mt-2">รูปภาพปัจจุบัน</p>
                <?php else: ?>
                    <img src="" alt="Link Image" class="image-preview" id="linkImagePreview" style="display:none;">
                <?php endif; ?>
                <input type="file" id="link_image" name="link_image" class="form-control mt-2" accept="image/*">
                <small class="form-text text-muted">อัปโหลดรูปภาพใหม่เพื่อเปลี่ยนรูปภาพปัจจุบัน</small>
            </div>

            <div class="form-section">
                <label for="link_image_url">URL ของรูปภาพลิงก์:</label>
                <input type="url" id="link_image_url" name="link_image_url" class="form-control" value="<?= htmlspecialchars($contact_data['link_image_url']) ?>">
                <small class="form-text text-muted">URL ที่รูปภาพจะลิงก์ไปเมื่อคลิก</small>
            </div>

            <div class="form-section">
                <label for="map_iframe_url">URL สำหรับ Google Map iframe:</label>
                <textarea id="map_iframe_url" name="map_iframe_url" class="form-control" rows="4"><?= htmlspecialchars($contact_data['map_iframe_url']) ?></textarea>
                <small class="form-text text-muted">ใส่ URL ของแผนที่ Google Map ที่ได้จากการ Embed (Embed a map -> copy iframe URL). <br>ตัวอย่าง: `https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3875.7396441274373!2d100.62457107508995!3d13.734206386655812!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x311d61b2c53ba90d%3A0x4442a96acffee7e9!2z4LmB4LiX4Lij4LiZ4LiU4Liy4Lij4LmMIOC4reC4tOC4meC5gOC4leC4reC4o-C5jOC5gOC4meC4iuC4seC5iOC4meC5geC4meC4pSAoVHJhbmRhciBJbnRlcm5hdGlvbmFsIGNvLiBsdGQp!5e0!3m2!1sen!2sth!4v1730371693406!5m2!1sen!2sth`</small>
                <?php if ($contact_data['map_iframe_url']): ?>
                    <div class="mt-3">
                        <p>ตัวอย่างแผนที่ปัจจุบัน:</p>
                        <iframe src="<?= htmlspecialchars($contact_data['map_iframe_url']) ?>" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                <?php endif; ?>
            </div>

            <div class="text-end mt-4">
                <button type="submit" id="submitEditContact" class="btn btn-primary">
                    <i class="fas fa-save"></i> บันทึกการเปลี่ยนแปลง
                </button>
            </div>
        </form>
    </div>
</div>

<script src='../js/index_.js?v=<?php echo time(); ?>'></script>
<script>
    // Preview image before upload
    document.getElementById('link_image').addEventListener('change', function(event) {
        const [file] = event.target.files;
        if (file) {
            const preview = document.getElementById('linkImagePreview');
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        }
    });

    // Form Submission Handler
    $('#submitEditContact').on('click', function(e) {
        e.preventDefault(); // Prevent default form submission

        var formData = new FormData($('#editContactForm')[0]);

        Swal.fire({
            title: "ยืนยันการแก้ไข?",
            text: "คุณต้องการอัปเดตข้อมูลติดต่อนี้ใช่หรือไม่!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#FFC107",
            cancelButtonColor: "#d33",
            confirmButtonText: "อัปเดต"
        }).then((result) => {
            if (result.isConfirmed) {
                $('#loading-overlay').fadeIn(); // Show loading overlay

                $.ajax({
                    url: "actions/process_contact.php", // Target the new process_contact.php
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        $('#loading-overlay').fadeOut(); // Hide loading overlay
                        if (response.status === 'success') {
                            Swal.fire(
                                'สำเร็จ!',
                                'แก้ไขข้อมูลติดต่อเรียบร้อยแล้ว.',
                                'success'
                            ).then(() => {
                                // Reload page to reflect changes
                                location.reload();
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
                        $('#loading-overlay').fadeOut(); // Hide loading overlay
                        console.error("AJAX Error:", status, error, xhr.responseText);
                        Swal.fire(
                            'เกิดข้อผิดพลาด!',
                            'ไม่สามารถแก้ไขข้อมูลติดต่อได้: ' + error,
                            'error'
                        );
                    }
                });
            }
        });
    });
</script>

</body>
</html>
<?php include '../check_permission.php'?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Service Page</title>

    <link rel="icon" type="image/x-icon" href="../../../public/img/q-removebg-preview1.png">

    <link href="../../../inc/jquery/css/jquery-ui.css" rel="stylesheet">
    <script src="../../../inc/jquery/js/jquery-3.6.0.min.js"></script>
    <script src="../../../inc/jquery/js/jquery-ui.min.js"></script>

    <link href="../../../inc/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="../../../inc/bootstrap/js/bootstrap.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/fontawesome5-fullcss@1.1.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css" crossorigin="anonymous" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit&family=Roboto&display=swap" rel="stylesheet">

    <link href="../../../inc/sweetalert2/css/sweetalert2.min.css" rel="stylesheet">
    <script src="../../../inc/sweetalert2/js/sweetalert2.all.min.js"></script>

    <link href="../../../inc/select2/css/select2.min.css" rel="stylesheet">
    <script src="../../../inc/select2/js/select2.min.js"></script>

    <link href="https://cdn.datatables.net/v/dt/dt-2.1.4/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/dt/dt-2.1.4/datatables.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-iconpicker/1.10.0/css/bootstrap-iconpicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-iconpicker/1.10.0/js/bootstrap-iconpicker.bundle.min.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>

    <link href='../css/index_.css?v=<?php echo time(); ?>' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Kanit&family=Sarabun&display=swap" rel="stylesheet">
    <style>
        .button-class {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
        }
        .responsive-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 10px;
        }
        @media (max-width: 768px) {
            .responsive-grid {
                grid-template-columns: 1fr;
            }
        }
        .btn-circle {
            border: none;
            width: 30px;
            height: 28px;
            border-radius: 50%;
            font-size: 14px;
        }
        .btn-edit {
            background-color: #FFC107;
            color: #ffffff;
        }
        .btn-del {
            background-color: #ff4537;
            color: #ffffff;
        }
       .note-editor .note-editable {
        font-family: inherit !important;
    }

    .note-editable span[style*="font-size"] {
        display: inline-block;
    }
    </style>
</head>

<?php include '../template/header.php' ?>

<body>
<div class="container mt-4">
    <h3>แก้ไขเนื้อหา "Service"</h3>
    <form method="post" action="save_service.php" enctype="multipart/form-data">
        <?php
        $result = $conn->query("SELECT * FROM service_content ORDER BY id ASC");
        while ($row = $result->fetch_assoc()):
        ?>
        <div class="card mb-3">
            <div class="card-body">
                <input type="hidden" name="ids[]" value="<?= $row['id'] ?>">

                <label>ประเภท</label>
                <select name="types[]" class="form-control">
                    <option value="text" <?= $row['type'] == 'text' ? 'selected' : '' ?>>Text</option>
                    <option value="image" <?= $row['type'] == 'image' ? 'selected' : '' ?>>Image + Text</option>
                    <option value="quote" <?= $row['type'] == 'quote' ? 'selected' : '' ?>>Quote</option>
                </select>

                <label>เนื้อหา (HTML)</label>
                <textarea name="contents[]" class="form-control summernote"><?= htmlspecialchars($row['content']) ?></textarea>

                <label>รูปภาพ (ถ้ามี)</label>
                <input type="file" name="images_files[]" class="form-control image-input">
                <?php if (!empty($row['image_url'])): ?>
                    <img src="<?= htmlspecialchars($row['image_url']) ?>" class="img-fluid mt-2 existing-image" style="max-height: 200px;">
                    <br>
                    <small class="form-text text-muted">รูปภาพเดิม</small>
                    <input type="hidden" name="existing_images[]" value="<?= htmlspecialchars($row['image_url']) ?>">
                <?php else: ?>
                    <small class="form-text text-muted">ไม่มีรูปภาพเดิม</small>
                    <input type="hidden" name="existing_images[]" value="">
                <?php endif; ?>
                <img src="#" class="img-fluid mt-2 new-image-preview" style="max-height: 200px; display: none;">
                <div style="padding-top 25px;">
                <label>ผู้พูด (สำหรับ quote)</label>
                </div>
                <input type="text" name="authors[]" class="form-control" value="<?= htmlspecialchars($row['author']) ?>">

                <label>ตำแหน่ง</label>
                <input type="text" name="positions[]" class="form-control" value="<?= htmlspecialchars($row['position']) ?>">
                <button type="button" class="btn btn-danger btn-sm mt-2 remove-block" data-id="<?= $row['id'] ?>">ลบบล็อคนี้</button>
            </div>
        </div>
        <?php endwhile; ?>

        <hr>
        <h4>เพิ่มเนื้อหาใหม่</h4>
        <div class="card mb-3">
            <div class="card-body">
                <label>ประเภท</label>
                <select name="new_type" class="form-control">
                    <option value="text">Text</option>
                    <option value="image">Image + Text</option>
                    <option value="quote">Quote</option>
                </select>

                <label>เนื้อหา (HTML)</label>
                <textarea name="new_content" class="form-control summernote"></textarea>

                <label>รูปภาพ (ถ้ามี)</label>
                <input type="file" name="new_image_file" class="form-control image-input">
                <img src="#" class="img-fluid mt-2 new-image-preview" style="max-height: 200px; display: none;">

                <label>ผู้พูด (สำหรับ quote)</label>
                <input type="text" name="new_author" class="form-control">

                <label>ตำแหน่ง</label>
                <input type="text" name="new_position" class="form-control">
            </div>
        </div>

        <button class="btn btn-success mt-3" type="submit">บันทึกทั้งหมด</button>
    </form>
</div>

<script>
    $(document).ready(function () {
        $('.summernote').summernote({
            height: 250,
            toolbar: [
                ['style', ['style']],
                ['font', ['fontname', 'fontsize', 'bold', 'italic', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['codeview']]
            ],
            fontNames: ['Kanit', 'Sarabun', 'Arial', 'Tahoma', 'Courier New', 'Impact', 'Times New Roman'],
            fontNamesIgnoreCheck: ['Kanit', 'Sarabun'],
            fontsize: ['8', '10', '12', '14', '16', '18', '24', '36', '48', '64']
        });
    });

    $(document).on('click', '.remove-block', function () {
        const $button = $(this);
        const blockId = $button.data('id');

        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: 'คุณต้องการลบบล็อคนี้จริงหรือ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('delete_service_block.php', { id: blockId }, function (response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'ลบแล้ว!',
                            icon: 'success',
                            timer: 1000,
                            showConfirmButton: false
                        });
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    } else {
                        Swal.fire('เกิดข้อผิดพลาด', response.message, 'error');
                    }
                }, 'json');
            }
        });
    });

    // เพิ่มโค้ด JavaScript เพื่อแสดงตัวอย่างรูปภาพที่อัปโหลด
    $(document).on('change', '.image-input', function() {
        const fileInput = this;
        const previewImage = $(this).closest('.card-body').find('.new-image-preview');
        const existingImage = $(this).closest('.card-body').find('.existing-image');
        
        if (fileInput.files && fileInput.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.attr('src', e.target.result).show();
                existingImage.hide(); // ซ่อนภาพเดิม
            };
            reader.readAsDataURL(fileInput.files[0]);
        } else {
            previewImage.hide();
            existingImage.show(); // ถ้าไม่มีไฟล์ใหม่ 
        }
    });

</script>

<script src='../js/index_.js?v=<?php echo time(); ?>'></script>
<script src='js/banner_.js?v=<?php echo time(); ?>'></script>
</body>
</html>
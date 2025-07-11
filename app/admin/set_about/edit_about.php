<?php include '../check_permission.php' ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit About Page</title>

    <link rel="icon" type="image/x-icon" href="../../../public/img/q-removebg-preview1.png">
    
    <!-- jQuery + jQuery UI -->
    <link href="../../../inc/jquery/css/jquery-ui.css" rel="stylesheet">
    <script src="../../../inc/jquery/js/jquery-3.6.0.min.js"></script>
    <script src="../../../inc/jquery/js/jquery-ui.min.js"></script>

    <!-- Bootstrap -->
    <link href="../../../inc/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="../../../inc/bootstrap/js/bootstrap.min.js"></script>

    <!-- Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/fontawesome5-fullcss@1.1.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css" crossorigin="anonymous" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit&family=Roboto&display=swap" rel="stylesheet">

    <!-- SweetAlert2 -->
    <link href="../../../inc/sweetalert2/css/sweetalert2.min.css" rel="stylesheet">
    <script src="../../../inc/sweetalert2/js/sweetalert2.all.min.js"></script>

    <!-- Select2 -->
    <link href="../../../inc/select2/css/select2.min.css" rel="stylesheet">
    <script src="../../../inc/select2/js/select2.min.js"></script>

    <!-- DataTables -->
    <link href="https://cdn.datatables.net/v/dt/dt-2.1.4/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/dt/dt-2.1.4/datatables.min.js"></script>

    <!-- Bootstrap Iconpicker -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-iconpicker/1.10.0/css/bootstrap-iconpicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-iconpicker/1.10.0/js/bootstrap-iconpicker.bundle.min.js"></script>

    <!-- Summernote -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>

    <!-- Custom CSS -->
    <link href='../css/index_.css?v=<?php echo time(); ?>' rel='stylesheet'>
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
         body, .note-editable {
        font-family: 'Kanit', 'Roboto', sans-serif;
    }
    .note-editable {
    font-family: 'Kanit', 'Roboto', sans-serif !important;
    font-size: inherit;
}

.note-editable span[style*="font-size"] {
    display: inline !important;
}
    </style>
</head>

<?php include '../template/header.php' ?>

<body>
<div class="container mt-4">
    <h3>เพิ่มเนื้อหาใหม่</h3>
    <form method="post" action="save_about.php">
        <div class="card mb-4 border-success">
            <div class="card-body">
                <input type="hidden" name="add_new" value="1">

                <label>ประเภท</label>
                <select name="type" class="form-control" required>
                    <option value="text">Text</option>
                    <option value="image">Image + Text</option>
                    <option value="quote">Quote</option>
                </select>

                <label>เนื้อหา (HTML)</label>
                <textarea name="content" class="form-control summernote" required></textarea>

                <label>ลิงก์ภาพ (ถ้ามี)</label>
                <input type="text" name="image_url" class="form-control">

                <label>ผู้พูด (สำหรับ quote)</label>
                <input type="text" name="author" class="form-control">

                <label>ตำแหน่ง</label>
                <input type="text" name="position" class="form-control">

                <button class="btn btn-primary mt-3" type="submit">เพิ่มเนื้อหาใหม่</button>
            </div>
        </div>
    </form>

    <hr>

    <h3>แก้ไขเนื้อหา "เกี่ยวกับเรา"</h3>
    <form method="post" action="save_about.php">
        <?php
        $result = $conn->query("SELECT * FROM about_content ORDER BY id ASC");
        while ($row = $result->fetch_assoc()):
        ?>
            <div class="card mb-3">
                <div class="card-body">
                    <input type="hidden" name="ids[]" value="<?= $row['id'] ?>">

                    <label>ประเภท</label>
                    <select name="types[]" class="form-control" required>
                        <option value="text" <?= $row['type'] == 'text' ? 'selected' : '' ?>>Text</option>
                        <option value="image" <?= $row['type'] == 'image' ? 'selected' : '' ?>>Image + Text</option>
                        <option value="quote" <?= $row['type'] == 'quote' ? 'selected' : '' ?>>Quote</option>
                    </select>

                    <label>เนื้อหา (HTML)</label>
                    <textarea name="contents[]" class="form-control summernote"><?= $row['content'] ?></textarea>

                    <label>ลิงก์ภาพ (ถ้ามี)</label>
                    <input type="text" name="images[]" class="form-control" value="<?= htmlspecialchars($row['image_url']) ?>">

                    <label>ผู้พูด (สำหรับ quote)</label>
                    <input type="text" name="authors[]" class="form-control" value="<?= htmlspecialchars($row['author']) ?>">

                    <label>ตำแหน่ง</label>
                    <input type="text" name="positions[]" class="form-control" value="<?= htmlspecialchars($row['position']) ?>">
                    <!-- เพิ่ม data-id เพื่อส่ง ID ไปยัง AJAX -->
<button type="button" class="btn btn-danger btn-sm mt-2 remove-block" data-id="<?= $row['id'] ?>">ลบบล็อคนี้</button>

                </div>
            </div>

        <?php endwhile; ?>
        <button class="btn btn-success mt-3" type="submit">บันทึกทั้งหมด</button>
    </form>
</div>

<!-- Summernote Config -->
<script>
    $('.summernote').summernote({
        height: 250,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['codeview']]
        ],
        fontNames: ['Arial', 'Kanit', 'Roboto', 'Tahoma', 'Impact', 'Courier New'],
        fontSizes: ['8', '10', '12', '14', '16', '18', '24', '36', '48', '64'],
        fontNamesIgnoreCheck: ['Kanit', 'Roboto']
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
            $.post('delete_about_block.php', { id: blockId }, function (response) {
    console.log(response);
    Swal.fire({
        title: response.success ? 'ลบแล้ว!' : 'ผิดพลาด',
        icon: response.success ? 'success' : 'error',
        text: response.message,
        timer: 1000,
        showConfirmButton: false
    });

    setTimeout(function () {
        location.reload(); // reload ไม่ว่าจะลบได้หรือไม่
    }, 1000);
}, 'json');

        }
    });
});

</script>


<script src='../js/index_.js?v=<?php echo time(); ?>'></script>
<script src='js/banner_.js?v=<?php echo time(); ?>'></script>

</body>
</html>

<?php
include '../check_permission.php'; 
// require_once(__DIR__ . '/../../../../lib/connect.php');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit About Page</title>

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
    <style>
        .button-class { background-color: #4CAF50; color: white; border: none; padding: 10px 15px; cursor: pointer; }
        .responsive-grid { display: grid; grid-template-columns: repeat(1, 1fr); gap: 10px; }
        @media (max-width: 768px) { .responsive-grid { grid-template-columns: 1fr; } }
        .btn-circle { border: none; width: 30px; height: 28px; border-radius: 50%; font-size: 14px; }
        .btn-edit { background-color: #FFC107; color: #ffffff; }
        .btn-del { background-color: #ff4537; color: #ffffff; }
        body, .note-editable { font-family: 'Kanit', 'Roboto', sans-serif; }
        .note-editable { font-family: 'Kanit', 'Roboto', sans-serif !important; font-size: inherit; }
        .note-editable span[style*="font-size"] { display: inline !important; }
        #loading-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); display: flex; justify-content: center; align-items: center; z-index: 9999; }
        .spinner-border { width: 3rem; height: 3rem; }
    </style>
</head>

<?php include '../template/header.php' ?>

<body>
<div id="loading-overlay">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>
<div class="container mt-4">
    <h3>เพิ่มเนื้อหาใหม่</h3>
    <form id="addAboutForm" method="post" enctype="multipart/form-data">
        <div class="card mb-4 border-success">
            <div class="card-body">
                <label>ประเภท</label>
                <select name="type" class="form-control" required>
                    <option value="text">Text</option>
                    <option value="image">Image + Text</option>
                    <option value="quote">Quote</option>
                </select>
                <label>เนื้อหา (HTML)</label>
                <textarea name="content" class="form-control summernote" required></textarea>
                <label>อัปโหลดรูปภาพ (ถ้ามี)</label>
                <input type="file" name="image_file" class="form-control">
                <label>ผู้พูด (สำหรับ quote)</label>
                <input type="text" name="author" class="form-control">
                <label>ตำแหน่ง</label>
                <input type="text" name="position" class="form-control">
                <button class="btn btn-primary mt-3" type="submit" id="submitAdd">เพิ่มเนื้อหาใหม่</button>
            </div>
        </div>
    </form>

    <hr>

    <h3>แก้ไขเนื้อหา "เกี่ยวกับเรา"</h3>
    <form id="editAboutForm" method="post" enctype="multipart/form-data">
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
                    <label>อัปโหลดรูปภาพใหม่ (ถ้ามี)</label>
                    <?php if (!empty($row['image_url'])): ?>
                        <div>
                            <img src="<?= htmlspecialchars($row['image_url']) ?>" style="max-width: 200px; max-height: 200px; object-fit: cover;">
                            <br><small>รูปภาพปัจจุบัน</small>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image_files[]" class="form-control mt-2">
                    <input type="hidden" name="images_old[]" value="<?= htmlspecialchars($row['image_url']) ?>">
                    <label>ผู้พูด (สำหรับ quote)</label>
                    <input type="text" name="authors[]" class="form-control" value="<?= htmlspecialchars($row['author']) ?>">
                    <label>ตำแหน่ง</label>
                    <input type="text" name="positions[]" class="form-control" value="<?= htmlspecialchars($row['position']) ?>">
                    <button type="button" class="btn btn-danger btn-sm mt-2 remove-block" data-id="<?= $row['id'] ?>">ลบบล็อคนี้</button>
                </div>
            </div>
        <?php endwhile; ?>
        <button class="btn btn-success mt-3" type="submit" id="submitEdit">บันทึกทั้งหมด</button>
    </form>
</div>

<script src='../js/index_.js?v=<?php echo time(); ?>'></script>
<script src='js/about_.js?v=<?php echo time(); ?>'></script>
</body>
</html>
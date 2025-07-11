<?php include '../check_permission.php'; ?>

<?php
require_once('../../../lib/connect.php');



$meta = [];

if (isset($_GET['id'])) {
    $stmt = $conn->prepare("SELECT * FROM metatags WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $meta = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Meta tags</title>

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
</head>
 <?php include '../template/header.php'; ?>
<body>
<div class="content-sticky">
    <div class="container-fluid">
        <div class="box-content">
            <div style="margin: 10px;">
                <h4 class="line-ref mb-3">
                    <i class="fa-solid fa-code"></i>
                    จัดการ Meta Tags
                </h4>

                <form method="post" action="setup_metatags.php" enctype="multipart/form-data">
                    <?php if (!empty($meta['id'])): ?>
                        <input type="hidden" name="id" value="<?= $meta['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label>Page Name</label>
                        <input type="text" name="page_name" class="form-control" value="<?= htmlspecialchars($meta['page_name'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label>Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" value="<?= htmlspecialchars($meta['meta_title'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label>Meta Description</label>
                        <textarea name="meta_description" class="form-control"><?= htmlspecialchars($meta['meta_description'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label>Meta Keywords</label>
                        <input type="text" name="meta_keywords" class="form-control" value="<?= htmlspecialchars($meta['meta_keywords'] ?? '') ?>">
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label>OG Title</label>
                        <input type="text" name="og_title" class="form-control" value="<?= htmlspecialchars($meta['og_title'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label>OG Description</label>
                        <textarea name="og_description" class="form-control"><?= htmlspecialchars($meta['og_description'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label>OG Image</label>
                        <input type="file" name="og_image" class="form-control">
                        <?php
                        $defaultImage = '../../public/img/q-removebg-preview1.png';
                        $ogImagePath = !empty($meta['og_image']) ? htmlspecialchars($meta['og_image']) : $defaultImage;
                        ?>
                        <img src="<?= $ogImagePath ?>" style="max-height: 100px; max-width: 150px; object-fit: contain; border:1px solid #ccc; padding: 4px;" class="mt-2">

                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> บันทึก
                    </button>
                </form>

                <hr>
                <h5 class="mt-4"><i class="fa fa-list"></i> รายการ Meta Tags</h5>
                <table class="table table-bordered mt-2">
                    <thead>
                        <tr>
                            <th>Page</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("SELECT * FROM metatags");
                        while ($row = $result->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($row['page_name']) ?></td>
                                <td><?= htmlspecialchars($row['meta_title']) ?></td>
                                <td><?= htmlspecialchars($row['meta_description']) ?></td>
                                <td>
                                    <a href="?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="fa fa-edit"></i> แก้ไข
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<script>
document.querySelector('form').addEventListener('submit', function(e) {
    if (document.querySelector('[name=meta_title]').value.trim() === '') {
        alert('กรุณากรอก Meta Title');
        e.preventDefault();
    }
});
</script>
<script src='../js/index_.js?v=<?php echo time(); ?>'></script>
    <script src='js/banner_.js?v=<?php echo time(); ?>'></script>
</body>
</html>

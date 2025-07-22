<?php include '../check_permission.php'; ?>


<!DOCTYPE html>
<html lang="th">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Banner</title>

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

        .banner-img {
        height: 60px; /* กำหนดความสูงที่ต้องการ */
        width: auto; /* ให้ความกว้างปรับตามสัดส่วนของภาพ */
        max-width: 150px; /* <<<--- เพิ่มอันนี้ เพื่อจำกัดความกว้างสูงสุด */
        object-fit: cover;
        border: 1px solid #ccc;
    }
    </style>
</head>
 <?php include '../template/header.php'; ?>
<?php
$result = $conn->query("SELECT * FROM banner ORDER BY id ASC");
?>
<body>

<div class="content-sticky">
    <div class="container-fluid">
        <div class="box-content">
            <div class="responsive-grid">
                <div style="margin: 10px;">
                    <div style="display: flex; justify-content: space-between;">
                        <h4 class="line-ref mb-3">
                            <i class="fa-solid fa-image"></i>
                            Banner List
                        </h4>
                        

                        <a class="btn btn-primary" href="setup_banner.php">
                            <i class="fa-solid fa-plus"></i> เพิ่ม Banner
                        </a>
                    </div>
                    <div style="gab :20px"><h5>
                            <div style="padding-bottom :5px">ความสูงรูปภาพ: 360px;</div>
                            <div style="padding-bottom :5px">ความกว้างรูปภาพ: 1920px;</div>
                            <!-- <div style="padding-bottom :30px">*หมายเหตุ ถ้าขนาดพอดีจะสวยงามที่สุดถ้ามากว่าหรือน้อยกว่าอาจจะไม่สวยเหมือนที่ดีไซน์</div> -->
                        </h5></div>
                    <table id="td_list_Banner" class="table table-hover" style="width:100%;">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>รูปภาพ</th>
                                <th>วันที่เพิ่ม</th>
                                <th>การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><img src="<?= $row['image_path'] ?>" class="banner-img" /></td>
                                    <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                                    <td>
                                        <a href="edit_banner.php?id=<?= $row['id'] ?>" class="btn btn-circle btn-edit" title="แก้ไข">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <a href="delete_banner.php?id=<?= $row['id'] ?>" onclick="return confirm('ยืนยันการลบ?')" class="btn btn-circle btn-del" title="ลบ">
                                            <i class="fas fa-trash"></i>
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
</div>

<script src='../js/index_.js?v=<?php echo time(); ?>'></script>
    <script src='js/banner_.js?v=<?php echo time(); ?>'></script>

</body>
</html>




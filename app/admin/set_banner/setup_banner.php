
<?php include '../check_permission.php'; ?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadDir = '../../public/img/';
    $fileName = basename($_FILES['image']['name']);
    $targetFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        $dbPath = '/trandar/public/img/' . $fileName;

        $stmt = $conn->prepare("INSERT INTO banner (image_path) VALUES (?)");
        $stmt->bind_param("s", $dbPath);
        $stmt->execute();
        header("Location: list_banner.php");
        exit;
    } else {
        echo "<script>alert('อัปโหลดล้มเหลว');</script>";
    }
}
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
    </style>
</head>
 
<body>
<?php include '../template/header.php'; ?>


<div class="container mt-4">
                        <div style="gab :20px"><h5>
                            <div style="padding-bottom :5px">ความสูงรูปภาพ: 300px;</div>
                            <div style="padding-bottom :5px">ความกว้างรูปภาพ: 1920px;</div>
                            <!-- <div style="padding-bottom :30px">*หมายเหตุ ถ้าขนาดพอดีจะสวยงามที่สุดถ้ามากว่าหรือน้อยกว่าอาจจะไม่สวยเหมือนที่ดีไซน์</div> -->
                        </h5></div>
    <div class="box-content p-4 bg-light rounded shadow-sm">
        <h4 class="line-ref">
            <i class="fa-solid fa-image"></i> เพิ่ม Banner
        </h4>

        <form method="post" enctype="multipart/form-data">
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
                        <button type="submit" class="btn btn-primary">
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
</script>

<!-- FontAwesome -->
<script src='../js/index_.js?v=<?php echo time(); ?>'></script>
    <script src='js/banner_.js?v=<?php echo time(); ?>'></script>
</body>
</html>

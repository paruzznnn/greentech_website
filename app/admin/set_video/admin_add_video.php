
<?php include '../check_permission.php'?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Video</title>

    <link rel="icon" type="image/x-icon" href="../../../public/img/q-removebg-preview1.png">

    <link href="../../../inc/jquery/css/jquery-ui.css" rel="stylesheet">

    <script src="../../../inc/jquery/js/jquery-3.6.0.min.js"></script>
    <script src="../../../inc/jquery/js/jquery-ui.min.js"></script>

    <link href="../../../inc/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="../../../inc/bootstrap/js/bootstrap.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/fontawesome5-fullcss@1.1.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css" integrity="sha512-9xKTRVabjVeZmc+GUW8GgSmcREDunMM+Dt/GrzchfN8tkwHizc5RP4Ok/MXFFy5rIjJjzhndFScTceq5e6GvVQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

    <link href="../../../inc/sweetalert2/css/sweetalert2.min.css" rel="stylesheet">
    <script src="../../../inc/sweetalert2/js/sweetalert2.all.min.js"></script>

    <link href="../../../inc/select2/css/select2.min.css" rel="stylesheet">
    <script src="../../../inc/select2/js/select2.min.js"></script>

    <link href="https://cdn.datatables.net/v/dt/dt-2.1.4/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/dt/dt-2.1.4/datatables.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-iconpicker/1.10.0/css/bootstrap-iconpicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-iconpicker/1.10.0/js/bootstrap-iconpicker.bundle.min.js">
        
    </script>

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

        /* Media query for smaller screens */
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
    </style>
</head>



<?php
require_once '../check_permission.php';
// require_once '../lib/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $youtube_id = $_POST['youtube_id'];
    $show = isset($_POST['show_on_homepage']) ? 1 : 0;

    if ($show) {
        $check = $conn->query("SELECT COUNT(*) AS total FROM videos WHERE show_on_homepage = 1");
        $row = $check->fetch_assoc();
        if ($row['total'] >= 4) {
            echo "<script>alert('แสดงได้สูงสุด 4 วิดีโอบนหน้าหลักเท่านั้น'); history.back();</script>";
            exit;
        }
    }

    $stmt = $conn->prepare("INSERT INTO videos (title, description, youtube_id, show_on_homepage) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $title, $desc, $youtube_id, $show);
    $stmt->execute();

    header("Location: admin_video_list.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการวิดีโอ</title>

    <link rel="icon" type="image/x-icon" href="../../../public/img/q-removebg-preview1.png">

    <link href="../../../inc/jquery/css/jquery-ui.css" rel="stylesheet">
    <script src="../../../inc/jquery/js/jquery-3.6.0.min.js"></script>
    <script src="../../../inc/jquery/js/jquery-ui.min.js"></script>

    <link href="../../../inc/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="../../../inc/bootstrap/js/bootstrap.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/fontawesome5-fullcss@1.1.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="../../../inc/sweetalert2/css/sweetalert2.min.css" rel="stylesheet">
    <script src="../../../inc/sweetalert2/js/sweetalert2.all.min.js"></script>

    <link href="../../../inc/select2/css/select2.min.css" rel="stylesheet">
    <script src="../../../inc/select2/js/select2.min.js"></script>

    <link href="https://cdn.datatables.net/v/dt/dt-2.1.4/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/dt/dt-2.1.4/datatables.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-iconpicker/1.10.0/css/bootstrap-iconpicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-iconpicker/1.10.0/js/bootstrap-iconpicker.bundle.min.js"></script>

    <link href='../css/index_.css?v=<?php echo time(); ?>' rel='stylesheet'>
</head>
<body>
    <script>
function extractYouTubeID(url) {
    const regex = /(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&]+)/;
    const match = url.match(regex);
    return match ? match[1] : '';
}

document.addEventListener("DOMContentLoaded", function () {
    const fullLink = document.getElementById("youtube_full_link");
    const youtubeIdInput = document.getElementById("youtube_id");

    fullLink.addEventListener("input", function () {
        const id = extractYouTubeID(fullLink.value);
        youtubeIdInput.value = id;
    });

    // กรณีเป็นหน้าแก้ไข และมีค่าจาก database
    const currentId = "<?= $video['youtube_id'] ?? '' ?>";
    if (currentId && !youtubeIdInput.value) {
        youtubeIdInput.value = currentId;
        fullLink.value = "https://www.youtube.com/watch?v=" + currentId;
    }
});
</script>
<div class="container mt-5">
    <h3><i class="fas fa-plus-circle"></i> เพิ่มวิดีโอ</h3>
    <form method="post" class="mt-4">
        <div class="mb-3">
            <label class="form-label">ชื่อวิดีโอ</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">คำอธิบาย</label>
            <textarea name="description" class="form-control" rows="4"></textarea>
        </div>
        <div class="mb-3">
    <label class="form-label">ลิงก์ YouTube แบบเต็ม</label>
    <input type="text" id="youtube_full_link" class="form-control" placeholder="เช่น https://www.youtube.com/watch?v=mOznkEkxdNU">

    <!-- ช่องนี้จะ auto fill ด้วยรหัสที่แยกจากลิงก์ -->
    <input type="hidden" id="youtube_id" name="youtube_id">
</div>

        
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="show_on_homepage">
            <label class="form-check-label">แสดงบนหน้าแรก</label>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> เพิ่มวิดีโอ</button>
        <a href="admin_video_list.php" class="btn btn-secondary">ย้อนกลับ</a>
    </form>
</div>

<script src='../js/index_.js?v=<?php echo time(); ?>'></script>
<script src='js/video_.js?v=<?php echo time(); ?>'></script>
</body>
</html>

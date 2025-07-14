

<?php include 'check_permission.php'; ?>
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>

    <?php include 'inc_head.php'; ?>
    <link href="css/index_.css?v=<?= time(); ?>" rel="stylesheet">
    <style>
        .dashboard-wrapper {
            padding: 30px;
        }
        .dashboard-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background-color: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            transition: 0.3s;
        }
        .dashboard-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .dashboard-card h3 {
            margin-bottom: 10px;
            font-size: 1.5rem;
        }
        .dashboard-card p {
            color: #666;
        }
        input[type="text"], input[type="email"], input[type="password"], input[type="file"] {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        }
        button {
        padding: 10px 20px;
        background-color: #007BFF;
        color: white;
        border: none;
        border-radius: 4px;
        }
    </style>
</head>
<body>

    <?php include 'template/header.php'; ?>
        <?php
    if (session_status() === PHP_SESSION_NONE) session_start();
    include $_SERVER['DOCUMENT_ROOT'] . '/trandar/lib/connect.php';

    $user_id = $_SESSION['user_id']; // ต้องแน่ใจว่า login แล้ว

    $sql = "SELECT * FROM mb_user WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    ?>
    <?php if (isset($_GET['updated'])): ?>
    <div style="text-align: center; color: green; font-weight: bold; margin-bottom: 20px;">
        ✅ บันทึกข้อมูลเรียบร้อยแล้ว
    </div>
    <?php endif; ?>
    <div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h3 class="mb-4">ข้อมูลส่วนตัว</h3>
                    <form action="update_profile.php" method="POST" enctype="multipart/form-data">
                        <img src="/public/img/<?php echo htmlspecialchars($user['profile_img']); ?>" alt="รูปโปรไฟล์" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                        <!-- <img src="img/<?php echo htmlspecialchars($user['profile_img']); ?>" alt="รูปโปรไฟล์" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;"> -->
                        <div class="form-group text-start">
                            <label for="profile_img">เปลี่ยนรูปภาพ:</label>
                            <input type="file" name="profile_img" class="form-control">
                        </div>

                        <div class="form-group text-start mt-3">
                            <label>ชื่อ:</label>
                            <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($user['first_name']); ?>">
                        </div>

                        <div class="form-group text-start mt-3">
                            <label>นามสกุล:</label>
                            <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($user['last_name']); ?>">
                        </div>

                        <div class="form-group text-start mt-3">
                            <label>อีเมล:</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>">
                        </div>

                        <div class="form-group text-start mt-3">
                            <label>รหัสผ่านใหม่:</label>
                            <input type="password" name="password" class="form-control" placeholder="********">
                        </div>

                        <div class="form-group text-start mt-3">
                            <label>ยืนยันรหัสผ่านใหม่:</label>
                            <input type="password" name="password_confirm" class="form-control" placeholder="********">
                        </div>

                        <button type="submit" class="btn btn-primary mt-4 w-100">บันทึกการเปลี่ยนแปลง</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



    <script src="js/index_.js?v=<?= time(); ?>"></script>
</body>
</html>

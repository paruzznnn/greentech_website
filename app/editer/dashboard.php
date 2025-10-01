

<?php include 'check_permission.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>editer Dashboard</title>
    <link rel="icon" type="image/x-icon" href="../public/img/greentechlogo.png">
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
    </style>
</head>

<?php

$latestBlogId = 0;

$stmt = $conn->prepare("SELECT MAX(Blog_id) AS max_id FROM dn_blog WHERE del = 0");
if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $latestBlogId = $row['max_id'];
    }
}
$stmt->close();
?>

<?php

$latestUserId = 0;

$stmt = $conn->prepare("SELECT MAX(user_id) AS max_id FROM mb_user WHERE del = 0");
if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $latestUserId = $row['max_id'];
    }
}
$stmt->close();
?>

<?php

// ดึงข่าวล่าสุด
$latestNewsId = 0;
$stmt = $conn->prepare("SELECT MAX(news_id) AS max_id FROM dn_news WHERE del = 0");
if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $latestNewsId = $row['max_id'];
    }
}
$stmt->close();
?>

<?php

// ดึงไอเดียล่าสุด
$latestIdiaId = 0;
$stmt = $conn->prepare("SELECT MAX(idia_id) AS max_id FROM dn_idia WHERE del = 0");
if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $latestIdiaId = $row['max_id'];
    }
}
$stmt->close();
?>

<?php
$latestProjectId = 0;

$stmt = $conn->prepare("SELECT MAX(project_id) AS max_id FROM dn_project WHERE del = 0");
if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $latestProjectId = $row['max_id'];
    }
}
$stmt->close();
?>

<body>

    <?php include 'template/header.php'; ?>

    <div class="dashboard-wrapper">
    <h2 class="mb-4">👋 สวัสดีคุณ Editer</h2>
    <div class="row">
        <!-- บล็อค 1 -->
        <div class="col-md-4 mb-4">
            <div class="dashboard-card">
            <h3>👤 ผู้ใช้งาน</h3>
            <p>รหัสผู้ใช้งานทั้งหมด: <strong><?= $latestUserId ?></strong></p>
            </div>
        </div>

        <!-- บล็อค 2 -->
        <div class="col-md-4 mb-4">
            <a href="set_news/list_news.php" style="text-decoration: none; color: inherit;">
    <div class="dashboard-card">
        <h3>📰 ข่าวสาร</h3>
        <p>ข่าวล่าสุด ID: <strong><?= $latestNewsId ?></strong></p>
    </div>
</a>
        </div>

        <!-- บล็อค 3 -->
        <div class="col-md-4 mb-4">
              <a href="set_project/list_project.php" style="text-decoration: none; color: inherit;">
             <div class="dashboard-card">
            <h3>📁 โปรเจกต์</h3>
            <p>โปรเจกต์ล่าสุด ID: <strong><?= $latestProjectId ?></strong></p>
        </div>
</a>
        </div>


                <!-- บล็อค 4 -->
        <div class="col-md-4 mb-4">
              <a href="set_idia/list_idia.php" style="text-decoration: none; color: inherit;">
             <div class="dashboard-card">
            <h3>💡 Design&Idia</h3>
            <p>Design ทั้งหมด: <strong><?= $latestIdiaId ?></strong></p>
        </div>
</a>
        </div>

                        <!-- บล็อค 5 -->
        <div class="col-md-4 mb-4">
              <a href="set_blog/list_blog.php" style="text-decoration: none; color: inherit;">
             <div class="dashboard-card">
            <h3>📝 Blog</h3>
            <p>Blog ทั้งหมด: <strong><?= $latestBlogId ?></strong></p>
        </div>
</a>
        </div>
            </div>
        </div>
    </div>

    <script src="js/index_.js?v=<?= time(); ?>"></script>
</body>
</html>

<?php include 'check_permission.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="icon" type="image/x-icon" href="../public/img/q-removebg-preview1.png">
    <?php include 'inc_head.php'; ?>
    <link href="css/index_.css?v=<?= time(); ?>" rel="stylesheet">
    
<style>
    /* Existing dashboard-card styles */
    .dashboard-card {
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 12px 14px; /* ลด padding */
    background-color: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    transition: 0.3s;
    min-height: 120px; /* ลดความสูง */
    display: flex;
    align-items: center;
}
    .dashboard-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .dashboard-card .card-inner {
        display: flex;
        align-items: center;
        width: 100%;
    }

    .dashboard-card .emoji {
        font-size: 2.5rem;
        margin-right: 15px;
        flex-shrink: 0;
    }

    .dashboard-card .text-area h3 {
        margin: 0 0 6px 0;
        font-size: 1.1rem;
        font-weight: bold;
        color: #fff;
    }

    .dashboard-card .count {
    font-size: 1.25rem;
}

    .dashboard-card .label {
    font-size: 0.75rem;
}
    .dashboard-card .card-inner {
    display: flex;
    align-items: center;            /* จัดกลางแนวตั้ง */
    justify-content: center;        /* จัดกลางแนวนอน */
    width: 100%;
    text-align: left;               /* ให้ข้อความอยู่ชิดซ้ายของกลุ่ม */
    gap: 10px;                      /* เพิ่มช่องว่างระหว่าง emoji กับข้อความ */
}

    .dashboard-card .emoji {
    font-size: 3rem; /* ลดขนาดอีโมจิ */
    margin-right: 12px;
}

    .dashboard-card .text-area h3 {
    font-size: 1rem;
}
    .dashboard-card .text-area .count,
    .dashboard-card .text-area .label {
        color: #fff;
    }

    /* New styles for the top section */
    .announce-card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        padding: 20px;
        min-height: 300px; /* Adjust as needed */
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        overflow: hidden; /* For image */
    }
    .announce-card h2 {
        color: #555;
        margin-bottom: 15px;
        font-size: 1.5rem;
    }
    .announce-card img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    .announce-card .employee-info h3 {
        margin: 0;
        color: #333;
        font-size: 1.2rem;
    }
    .announce-card .employee-info p {
        color: #777;
        font-size: 0.9rem;
    }

    .attendance-card, .birthday-card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        padding: 20px;
        min-height: 150px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
    .attendance-card h4, .birthday-card h4 {
        color: #555;
        margin-bottom: 15px;
        font-size: 1.1rem;
    }
    .attendance-grid {
        display: flex;
        justify-content: space-around;
        width: 100%;
        margin-top: 15px;
    }
    .attendance-item {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .attendance-item .time {
        font-size: 1.8rem;
        font-weight: bold;
        color: #4CAF50; /* Green for IN */
        margin-bottom: 5px;
    }
    .attendance-item.out .time {
        color: #FF5722; /* Orange for OUT */
    }
    .attendance-item .label {
        font-size: 0.8rem;
        color: #777;
    }
    .attendance-options {
        margin-top: 15px;
    }
    .attendance-options button {
        background-color: #eee;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        margin: 0 5px;
        font-size: 0.9rem;
        color: #555;
    }
    .attendance-options button.active {
        background-color: #007bff;
        color: #fff;
    }

    .birthday-card .emoji {
        font-size: 3rem;
        margin-bottom: 10px;
    }
    .birthday-card .text {
        font-size: 1rem;
        color: #555;
    }

    .calendar-card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        padding: 20px;
        min-height: 300px; /* Adjust as needed */
    }
    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        color: #555;
        font-weight: bold;
    }
    .calendar-header .month-year {
        font-size: 1.2rem;
    }
    .calendar-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        text-align: center;
        font-weight: bold;
        color: #888;
        margin-bottom: 10px;
    }
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 5px;
        text-align: center;
    }
    .calendar-grid .day-number {
        padding: 8px 5px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 0.9rem;
        color: #333;
        position: relative;
    }
    .calendar-grid .day-number:hover {
        background-color: #f0f0f0;
    }
    .calendar-grid .day-number.inactive {
        color: #ccc;
    }
    .calendar-grid .day-number.current-day {
        background-color: #007bff;
        color: #fff;
        font-weight: bold;
    }
    .calendar-grid .day-number.has-event {
        background-color: #FFEBEE; /* Light red for events */
        color: #D32F2F;
        font-weight: bold;
    }
    .calendar-grid .day-number.has-multiple-events {
        background-color: #E3F2FD; /* Light blue for multiple events */
        color: #1976D2;
        font-weight: bold;
    }
    .event-indicator {
        position: absolute;
        bottom: 2px;
        left: 50%;
        transform: translateX(-50%);
        width: 5px;
        height: 5px;
        background-color: #D32F2F;
        border-radius: 50%;
    }
    .multiple-event-indicator {
        background-color: #1976D2;
    }
    .calendar-legend {
        display: flex;
        justify-content: flex-end;
        margin-top: 15px;
        font-size: 0.8rem;
    }
    .calendar-legend-item {
        display: flex;
        align-items: center;
        margin-left: 15px;
    }
    .calendar-legend-item .color-box {
        width: 12px;
        height: 12px;
        border-radius: 3px;
        margin-right: 5px;
    }
    .color-box.activity { background-color: #FFEBEE; }
    .color-box.work { background-color: #E3F2FD; }
    .color-box.helpdesk { background-color: #E8F5E9; } /* Example color */

    /* Specific event colors for calendar */
    .day-number.activity-event { background-color: #FFE0B2; color: #E65100; } /* Orange */
    .day-number.work-event { background-color: #BBDEFB; color: #1565C0; } /* Blue */
    .day-number.helpdesk-event { background-color: #C8E6C9; color: #2E7D32; } /* Green */
    .day-number.support-helpdesk-event { background-color: #F8BBD0; color: #AD1457; } /* Pink */

</style>
</head>
<?php
// นับจำนวนแถวทั้งหมดในตาราง mb_comments
$latestCommentId = 0; // เปลี่ยนชื่อตัวแปรนี้ให้สื่อความหมายมากขึ้น เช่น $totalCommentsCount
$stmt = $conn->prepare("SELECT COUNT(*) AS total_rows FROM mb_comments");
if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $latestCommentId = $row['total_rows']; // เก็บจำนวนแถวทั้งหมด
    }
}
$stmt->close();
?>

<?php
// นับจำนวนแถวทั้งหมดในตาราง dn_blog ที่ del = 0
$latestBlogId = 0; // เปลี่ยนชื่อตัวแปรนี้ให้สื่อความหมายมากขึ้น เช่น $totalBlogsCount
$stmt = $conn->prepare("SELECT COUNT(*) AS total_rows FROM dn_blog WHERE del = 0");
if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $latestBlogId = $row['total_rows']; // เก็บจำนวนแถวทั้งหมด
    }
}
$stmt->close();
?>

<?php
// นับจำนวนแถวทั้งหมดในตาราง mb_user ที่ del = 0
$latestUserId = 0; // เปลี่ยนชื่อตัวแปรนี้ให้สื่อความหมายมากขึ้น เช่น $totalUsersCount
$stmt = $conn->prepare("SELECT COUNT(*) AS total_rows FROM mb_user WHERE del = 0");
if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $latestUserId = $row['total_rows']; // เก็บจำนวนแถวทั้งหมด
    }
}
$stmt->close();
?>

<?php
// นับจำนวนแถวทั้งหมดในตาราง dn_news ที่ del = 0
$latestNewsId = 0; // เปลี่ยนชื่อตัวแปรนี้ให้สื่อความหมายมากขึ้น เช่น $totalNewsCount
$stmt = $conn->prepare("SELECT COUNT(*) AS total_rows FROM dn_news WHERE del = 0");
if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $latestNewsId = $row['total_rows']; // เก็บจำนวนแถวทั้งหมด
    }
}
$stmt->close();
?>

<?php
// นับจำนวนแถวทั้งหมดในตาราง dn_idia ที่ del = 0
$latestIdiaId = 0; // เปลี่ยนชื่อตัวแปรนี้ให้สื่อความหมายมากขึ้น เช่น $totalIdiasCount
$stmt = $conn->prepare("SELECT COUNT(*) AS total_rows FROM dn_idia WHERE del = 0");
if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $latestIdiaId = $row['total_rows']; // เก็บจำนวนแถวทั้งหมด
    }
}
$stmt->close();
?>

<?php
// นับจำนวนแถวทั้งหมดในตาราง dn_shop ที่ del = 0
$latestShopId = 0; // เปลี่ยนชื่อตัวแปรนี้ให้สื่อความหมายมากขึ้น เช่น $totalIdiasCount
$stmt = $conn->prepare("SELECT COUNT(*) AS total_rows FROM dn_shop WHERE del = 0");
if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $latestShopId = $row['total_rows']; // เก็บจำนวนแถวทั้งหมด
    }
}
$stmt->close();
?>

<?php
// นับจำนวนแถวทั้งหมดในตาราง dn_project ที่ del = 0
$latestProjectId = 0; // เปลี่ยนชื่อตัวแปรนี้ให้สื่อความหมายมากขึ้น เช่น $totalProjectsCount
$stmt = $conn->prepare("SELECT COUNT(*) AS total_rows FROM dn_project WHERE del = 0");
if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $latestProjectId = $row['total_rows']; // เก็บจำนวนแถวทั้งหมด
    }
}
$stmt->close();
?>

<?php

// ดึงbannerล่าสุด
$latestBannersId = 0;
$stmt = $conn->prepare("SELECT MAX(id) AS max_id FROM banner");
if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $latestBannersId = $row['max_id'];
    }
}
$stmt->close();
?>

<body>

    <?php include 'template/header.php'; ?>

    <div class="dashboard-wrapper container">
    <?php
date_default_timezone_set('Asia/Bangkok'); // ตั้งเขตเวลาไทย
$hour = date('H');
$greeting = "Hello";

if ($hour >= 5 && $hour < 12) {
    $greeting = "Good Morning";
} elseif ($hour >= 12 && $hour < 18) {
    $greeting = "Good Afternoon";
} else {
    $greeting = "Good Evening";
}

$username = $_SESSION['fullname'] ?? 'Admin'; // หรือใส่ชื่อแบบ static เช่น 'Aphisit'

?>
<h2 class="mb-1"><?= $greeting ?> <?= htmlspecialchars($username) ?>!</h2>
<h3 class="mb-5" style="font-size: 1.2rem; ">ผู้ใช้งาน</h3>
    <div class="row">
    <!-- USER -->
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
    <div class="dashboard-card crm" style="background-color:#ffa726;">
        <div class="card-inner">
            <div class="emoji">👤</div>
            <div class="text-area">
                <h3>ผู้ใช้งาน</h3>
                <div class="count"><?= $latestUserId ?></div>
                <div class="label">ทั้งหมดในระบบ</div>
            </div>
        </div>
    </div>
    </div>


    <!-- NEWS -->
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
    <div class="dashboard-card crm" style="background-color:#ec407a;">
        <a href="set_news/list_news.php" style="text-decoration: none; color: inherit; display: contents;">
            <div class="card-inner">
                <div class="emoji">📰</div>
                <div class="text-area">
                    <h3>ข่าวสาร</h3>
                    <div class="count"><?= $latestNewsId ?></div>
                    <div class="label">ในระบบทั้งหมด</div>
                </div>
            </div>
        </a>
    </div>
</div>


    <!-- PROJECT -->
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
    <div class="dashboard-card work" style="background-color:#26c6da;">
        <a href="set_project/list_project.php" style="text-decoration: none; color: inherit; display: contents;">
            <div class="card-inner">
                <div class="emoji">📁</div>
                <div class="text-area">
                    <h3>โปรเจกต์</h3>
                    <div class="count"><?= $latestProjectId ?></div>
                    <div class="label">โปรเจกต์ในระบบทั้งหมด</div>
                </div>
            </div>
        </a>
    </div>
</div>

    <!-- IDEA -->
    <!-- <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
    <div class="dashboard-card work" style="background-color:#42a5f5;">
        <a href="set_idia/list_idia.php" style="text-decoration: none; color: inherit; display: contents;">
            <div class="card-inner">
                <div class="emoji">💡</div>
                <div class="text-area">
                    <h3>Design & Idea</h3>
                    <div class="count"><?= $latestIdiaId ?></div>
                    <div class="label">ไอเดียทั้งหมด</div>
                </div>
            </div>
        </a>
    </div>
</div> -->
      <!-- product -->
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
    <div class="dashboard-card work" style="background-color:#42a5f5;">
        <a href="set_product/list_shop.php" style="text-decoration: none; color: inherit; display: contents;">
            <div class="card-inner">
                <div class="emoji">📦</div>
                <div class="text-area">
                    <h3>Product</h3>
                    <div class="count"><?= $latestShopId ?></div>
                    <div class="label">Product ทั้งหมด</div>
                </div>
            </div>
        </a>
    </div>
</div>

    <!-- BLOG -->
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
    <div class="dashboard-card work" style="background-color:#ab47bc;">
        <a href="set_Blog/list_Blog.php" style="text-decoration: none; color: inherit; display: contents;">
            <div class="card-inner">
                <div class="emoji">📝</div>
                <div class="text-area">
                    <h3>Blog</h3>
                    <div class="count"><?= $latestBlogId ?></div>
                    <div class="label">บทความทั้งหมด</div>
                </div>
            </div>
        </a>
    </div>
</div>

    <!-- COMMENT -->
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
    <div class="dashboard-card work" style="background-color:#26a69a;">
        <a href="set_comment/comment_service.php" style="text-decoration: none; color: inherit; display: contents;">
            <div class="card-inner">
                <div class="emoji">💬</div>
                <div class="text-area">
                    <h3>ความคิดเห็น</h3>
                    <div class="count"><?= $latestCommentId ?></div>
                    <div class="label">ทั้งหมดในระบบ</div>
                </div>
            </div>
        </a>
    </div>
</div>

    <!-- banner -->
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
    <div class="dashboard-card work" style="background-color:#ec879a;">
        <a href="set_banner/list_banner.php" style="text-decoration: none; color: inherit; display: contents;">
            <div class="card-inner">
                <div class="emoji">🪧</div>
                <div class="text-area">
                    <h3>Banner หน้าหลัก</h3>
                    <!-- <div class="count">?= $latestBannersId ?</div>
                    <div class="label">banner ทั้งหมด</div> -->
                </div>
            </div>
        </a>
    </div>
    </div>

    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
    <div class="dashboard-card work" style="background-color:#730ac9;">
        <a href="set_metatags/list_metatags.php" style="text-decoration: none; color: inherit; display: contents;">
            <div class="card-inner">
                <div class="emoji">🏷️</div>
                <div class="text-area">
                    <h3>Edit Meta tags</h3>
                    <!-- <div class="count">?= $latestBannersId ?</div>
                    <div class="label">banner ทั้งหมด</div> -->
                </div>
            </div>
        </a>
    </div>
    </div>
    

  
</div>

    <script src="js/index_.js?v=<?= time(); ?>"></script>
</body>
</html>

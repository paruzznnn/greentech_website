<?php include 'check_permission.php'; 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="icon" type="image/x-icon" href="../public/img/q-removebg-preview1.png">
    <?php include 'inc_head.php'; ?>
    <link href="css/index_.css?v=<?= time(); ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
    .container, .container-lg, .container-md, .container-sm, .container-xl, .container-xxl {
        max-width: 1450px;
        --bs-gutter-x: 0rem;
    }
    /* Global styles for the dashboard wrapper */
    .dashboard-wrapper {
        /* padding-top: 20px; */
        padding-bottom: 20px;
    }

    /* New: Container for the dashboard cards to give it a background and rounded corners */
    .dashboard-layout {
        background-color: #f5f5f5; /* เปลี่ยนสีพื้นหลังเป็นสีเทาตามภาพ */
        border-radius: 12px; /* ขอบโค้งตามรูปตัวอย่างแรก */
        box-shadow: 0 2px 6px rgba(0,0,0,0.05); /* เพิ่มเงาเล็กน้อย */
        padding: 20px; /* เพิ่ม padding ภายในคอนเทนเนอร์เพื่อให้มีระยะห่างจากขอบ */
        margin-bottom: 20px; /* ระยะห่างด้านล่าง */
    }

    /* Dashboard card container styles */
    .dashboard-card {
        /* เปลี่ยน border จาก #ddd เป็น transparent เพื่อให้เห็น box-shadow สีขาวแทน */
        border: 1px solid transparent; 
        border-radius: 4px;
        padding: 12px 18px; /* ปรับ padding ให้แนวตั้งน้อยลง แนวนอนมากขึ้น เพื่อให้เป็นผืนผ้าแนวนอน */
        /* เพิ่ม box-shadow สำหรับขอบสีขาวและเงาปกติ */
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.8), /* ขอบสีขาว */
                    0 2px 6px rgba(0,0,0,0.05); /* เงาปกติ */
        transition: 0.3s;
        min-height: 120px; /* กำหนดความสูงขั้นต่ำเพื่อให้กล่องไม่เล็กเกินไป */
        display: flex;
        align-items: center; /* จัดกลางเนื้อหาในกล่องแนวตั้ง */
        justify-content: center; /* จัดกลางเนื้อหาในกล่องแนวนอน */
        height: 100%; /* Ensure all cards in a row have the same height */
        position: relative; /* For the top-right icon */
        flex-direction: row; /* เปลี่ยนเป็น row เพื่อให้อีโมจิอยู่ด้านซ้าย และข้อความอยู่ด้านขวา */
    }

    .dashboard-card:hover {
        transform: translateY(-3px);
        /* ปรับ box-shadow เมื่อ hover ให้ขอบสีขาวชัดเจนขึ้นและเงาเข้มขึ้น */
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 1), /* ขอบสีขาวที่ชัดขึ้น */
                    0 4px 12px rgba(0,0,0,0.1); /* เงาเข้มขึ้น */
    }

    /* Inner content of the card */
    .dashboard-card .card-inner {
        display: flex;
        flex-direction: row; /* จัดเรียงอีโมจิและข้อความในแนวนอน */
        align-items: center; /* จัดกลางแนวตั้ง */
        justify-content: flex-start; /* จัดชิดซ้าย */
        width: 100%;
        height: 100%; /* Ensure inner content fills the card */
        text-align: left; /* จัดข้อความชิดซ้าย */
        gap: 12px; /* Space between emoji and text */
    }

    /* Emoji styles */
    .dashboard-card .emoji {
        font-size: 2.8rem; /* ทำให้ emoji เล็กลงเล็กน้อยเพื่อให้เข้ากับสัดส่วนผืนผ้า */
        margin-right: 0; /* No margin needed here as gap handles spacing */
        flex-shrink: 0; /* Prevent emoji from shrinking */
        line-height: 1;
    }

    /* Text area styles */
    .dashboard-card .text-area {
        display: flex;
        flex-direction: column; /* จัดเรียง h3, count, label ในแนวตั้ง */
        align-items: flex-start; /* จัดข้อความใน text-area ชิดซ้าย */
        justify-content: center;
        flex-grow: 1; /* Allow text area to take available space */
    }

    /* Heading styles */
    .dashboard-card .text-area h3 {
        margin: 0; /* Remove top/bottom margin */
        font-size: 1.0rem; /* ขนาด heading */
        font-weight: bold;
        color: #fff;
        line-height: 1.2;
        white-space: nowrap; /* Prevent text wrapping */
    }

    /* Count styles */
    .dashboard-card .count {
        font-size: 1.6rem; /* ปรับขนาดตัวเลข */
        font-weight: bold;
        color: #fff;
        line-height: 1.2;
    }
    .mb-5 {
    margin-bottom: 2rem !important;
    }   
    /* Label styles */
    .dashboard-card .label {
        font-size: 0.85rem; /* ปรับขนาด label */
        color: #fff;
        line-height: 1.2;
        white-space: nowrap; /* Prevent text wrapping */
    }

    /* Top-right info icon */
    .dashboard-card .info-icon {
        position: absolute;
        top: 10px;
        right: 10px;
        color: rgba(255, 255, 255, 0.7); /* Light white color */
        font-size: 1.0rem; /* ขนาดไอคอน */
    }

    /* Override a tag style for full box clickability */
    .dashboard-card a {
        display: flex; /* Changed to flex to align inner content */
        width: 100%;
        height: 100%;
        text-decoration: none;
        color: inherit;
        align-items: center; /* Vertically center content of the link */
        justify-content: center; /* Horizontally center content of the link */
    }

    /* Responsive grid for 5 columns */
    @media (min-width: 1200px) { /* For large devices (lg) and up */
        .col-lg-2-4 { /* Custom class for 5 columns in a 12-column grid (12/5 = 2.4) */
            flex: 0 0 20%;
            max-width: 20%;
        }
    }
    .row>* {
    flex-shrink: 0;
    /* width: 100%; */
    max-width: 100%;
    padding-right: calc(var(--bs-gutter-x) * .2);
    padding-left: calc(var(--bs-gutter-x) * .2);
    margin-top: var(--bs-gutter-y);
}
    /* Keep other section styles (announce, attendance, calendar) as is if they are not dashboard cards */
    .announce-card, .attendance-card, .birthday-card, .calendar-card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        overflow: hidden;
    }
    .announce-card { min-height: 300px; }
    .attendance-card, .birthday-card { min-height: 150px; }
    .calendar-card { min-height: 300px; }

    /* The rest of the calendar/attendance/announce styles (unchanged) */
    .announce-card h2 { color: #555; margin-bottom: 15px; font-size: 1.5rem; }
    .announce-card img { max-width: 100%; height: auto; border-radius: 8px; margin-bottom: 15px; }
    .announce-card .employee-info h3 { margin: 0; color: #333; font-size: 1.2rem; }
    .announce-card .employee-info p { color: #777; font-size: 0.9rem; }

    .attendance-card h4, .birthday-card h4 { color: #555; margin-bottom: 15px; font-size: 1.1rem; }
    .attendance-grid { display: flex; justify-content: space-around; width: 100%; margin-top: 15px; }
    .attendance-item { display: flex; flex-direction: column; align-items: center; }
    .attendance-item .time { font-size: 1.8rem; font-weight: bold; color: #4CAF50; margin-bottom: 5px; }
    .attendance-item.out .time { color: #FF5722; }
    .attendance-item .label { font-size: 0.8rem; color: #777; }
    .attendance-options { margin-top: 15px; }
    .attendance-options button { background-color: #eee; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; margin: 0 5px; font-size: 0.9rem; color: #555; }
    .attendance-options button.active { background-color: #007bff; color: #fff; }

    .birthday-card .emoji { font-size: 3rem; margin-bottom: 10px; }
    .birthday-card .text { font-size: 1rem; color: #555; }

    .calendar-card { background-color: #fff; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); padding: 20px; min-height: 300px; }
    .calendar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; color: #555; font-weight: bold; }
    .calendar-header .month-year { font-size: 1.2rem; }
    .calendar-days { display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; font-weight: bold; color: #888; margin-bottom: 10px; }
    .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 5px; text-align: center; }
    .calendar-grid .day-number { padding: 8px 5px; border-radius: 5px; cursor: pointer; font-size: 0.9rem; color: #333; position: relative; }
    .calendar-grid .day-number:hover { background-color: #f0f0f0; }
    .calendar-grid .day-number.inactive { color: #ccc; }
    .calendar-grid .day-number.current-day { background-color: #007bff; color: #fff; font-weight: bold; }
    .calendar-grid .day-number.has-event { background-color: #FFEBEE; color: #D32F2F; font-weight: bold; }
    .calendar-grid .day-number.has-multiple-events { background-color: #E3F2FD; color: #1976D2; font-weight: bold; }
    .event-indicator { position: absolute; bottom: 2px; left: 50%; transform: translateX(-50%); width: 5px; height: 5px; background-color: #D32F2F; border-radius: 50%; }
    .multiple-event-indicator { background-color: #1976D2; }
    .calendar-legend { display: flex; justify-content: flex-end; margin-top: 15px; font-size: 0.8rem; }
    .calendar-legend-item { display: flex; align-items: center; margin-left: 15px; }
    .calendar-legend-item .color-box { width: 12px; height: 12px; border-radius: 3px; margin-right: 5px; }
    .color-box.activity { background-color: #FFEBEE; }
    .color-box.work { background-color: #E3F2FD; }
    .color-box.helpdesk { background-color: #E8F5E9; }

    .day-number.activity-event { background-color: #FFE0B2; color: #E65100; }
    .day-number.work-event { background-color: #BBDEFB; color: #1565C0; }
    .day-number.helpdesk-event { background-color: #C8E6C9; color: #2E7D32; }
    .day-number.support-helpdesk-event { background-color: #F8BBD0; color: #AD1457; }

    /* Custom styles for thinner text (from the image) */
    .dashboard-wrapper h2 { /* For "Good Morning Aphisit!" */
        font-size: 1.3rem; /* ลดขนาดฟอนต์เล็กน้อย */
        font-weight: 380; /* ทำให้ฟอนต์บางลง */
        color: #333; /* ปรับสีให้เข้มขึ้นเล็กน้อยเพื่อให้อ่านง่าย */
        margin-bottom: 5px; /* ลดระยะห่างด้านล่าง */
    }

    .dashboard-wrapper h3 { /* For "ผู้ใช้งาน" */
        font-size: 0.6rem; /* ลดขนาดฟอนต์เล็กน้อย */
        font-weight: 300; /* ทำให้ฟอนต์บางลง */
        color: #777; /* ปรับสีให้จางลงเล็กน้อย */
        margin-top: 0; /* ตรวจสอบให้แน่ใจว่าไม่มี margin ด้านบน */
        margin-bottom: 20px; /* เพิ่มระยะห่างด้านล่างเพื่อให้ห่างจาก cards */
    }


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
// นับจำนวนแถวทั้งหมดในตาราง dn_idia ที่ del = 0
$latestlogoId = 0; // เปลี่ยนชื่อตัวแปรนี้ให้สื่อความหมายมากขึ้น เช่น $totalIdiasCount
$stmt = $conn->prepare("SELECT COUNT(*) AS total_rows FROM logo_settings");
if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $latestlogoId = $row['total_rows']; // เก็บจำนวนแถวทั้งหมด
    }
}
$stmt->close();
?>

<?php
// นับจำนวนแถวทั้งหมดในตาราง dn_idia ที่ del = 0
$latestvideosId = 0; // เปลี่ยนชื่อตัวแปรนี้ให้สื่อความหมายมากขึ้น เช่น $totalIdiasCount
$stmt = $conn->prepare("SELECT COUNT(*) AS total_rows FROM videos ");
if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $latestvideosId = $row['total_rows']; // เก็บจำนวนแถวทั้งหมด
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
// นับจำนวนแถวทั้งหมดในตาราง dn_idia ที่ del = 0
$latestfooterId = 0; // เปลี่ยนชื่อตัวแปรนี้ให้สื่อความหมายมากขึ้น เช่น $totalIdiasCount
$stmt = $conn->prepare("SELECT COUNT(*) AS total_rows FROM footer_settings");
if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $latestfooterId = $row['total_rows']; // เก็บจำนวนแถวทั้งหมด
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
<h3 class="mb-5">ผู้ใช้งาน</h3>

    <div class="dashboard-layout">
        <div class="row justify-content-center">
    <div class="col-12 col-sm-6 col-md-4 col-lg-2-4 mb-4">
        <div class="dashboard-card" style="background-color:#ffa726;">
            <a href="set_users/edit_users.php">
                <div class="card-inner">
                    <div class="emoji">👤</div>
                    <div class="text-area">
                        <h3>ผู้ใช้งาน</h3>
                        <div class="count"><?= $latestUserId ?></div>
                        <div class="label">ทั้งหมดในระบบ</div>
                    </div>
                </div>
                <div class="info-icon"><i class="fas fa-info-circle"></i></div>
            </a>
        </div>
    </div>



    <div class="col-12 col-sm-6 col-md-4 col-lg-2-4 mb-4">
        <div class="dashboard-card" style="background-color:#42a5f5;">
            <a href="set_product/list_shop.php">
                <div class="card-inner">
                    <div class="emoji">📦</div>
                    <div class="text-area">
                        <h3>Product</h3>
                        <div class="count"><?= $latestShopId ?></div>
                        <div class="label">Product ทั้งหมด</div>
                    </div>
                </div>
                <div class="info-icon"><i class="fas fa-info-circle"></i></div>
            </a>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-4 col-lg-2-4 mb-4">
        <div class="dashboard-card" style="background-color:#26c6da;">
            <a href="set_project/list_project.php">
                <div class="card-inner">
                    <div class="emoji">📁</div>
                    <div class="text-area">
                        <h3>โปรเจกต์</h3>
                        <div class="count"><?= $latestProjectId ?></div>
                        <div class="label">โปรเจกต์ในระบบทั้งหมด</div>
                    </div>
                </div>
                <div class="info-icon"><i class="fas fa-info-circle"></i></div>
            </a>
        </div>
    </div>



    <div class="col-12 col-sm-6 col-md-4 col-lg-2-4 mb-4">
        <div class="dashboard-card" style="background-color:#ab47bc;">
            <a href="set_Blog/list_Blog.php">
                <div class="card-inner">
                    <div class="emoji">✍️</div>
                    <div class="text-area">
                        <h3>Blog</h3>
                        <div class="count"><?= $latestBlogId ?></div>
                        <div class="label">บทความทั้งหมด</div>
                    </div>
                </div>
                <div class="info-icon"><i class="fas fa-info-circle"></i></div>
            </a>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-4 col-lg-2-4 mb-4">
        <div class="dashboard-card" style="background-color:#78909c;">
            <a href="set_idia/list_idia.php">
                <div class="card-inner">
                    <div class="emoji">💡</div>
                    <div class="text-area">
                        <h3>Acoustic knowledge</h3>
                        <div class="count"><?= $latestIdiaId ?></div>
                        <div class="label">Acoustic knowledge ทั้งหมด</div>
                    </div>
                </div>
                <div class="info-icon"><i class="fas fa-info-circle"></i></div>
            </a>
        </div>
    </div>



    <div class="col-12 col-sm-6 col-md-4 col-lg-2-4 mb-4">
        <div class="dashboard-card" style="background-color:#8bc34a;">
            <a href="set_video/admin_video_list.php">
                <div class="card-inner">
                    <div class="emoji">🎥</div>
                    <div class="text-area">
                        <h3>Video</h3>
                        <div class="count"><?= $latestvideosId ?></div>
                        <div class="label">video ทั้งหมด</div>
                    </div>
                </div>
                <div class="info-icon"><i class="fas fa-info-circle"></i></div>
            </a>
        </div>
    </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-2-4 mb-4">
        <div class="dashboard-card" style="background-color:#ec407a;">
            <a href="set_news/list_news.php">
                <div class="card-inner">
                    <div class="emoji">📰</div>
                    <div class="text-area">
                        <h3>ข่าวสาร</h3>
                        <div class="count"><?= $latestNewsId ?></div>
                        <div class="label">ในระบบทั้งหมด</div>
                    </div>
                </div>
                <div class="info-icon"><i class="fas fa-info-circle"></i></div>
            </a>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-4 col-lg-2-4 mb-4">
        <div class="dashboard-card" style="background-color:#730ac9;">
            <a href="set_metatags/list_metatags.php">
                <div class="card-inner">
                    <div class="emoji">🏷️</div>
                    <div class="text-area">
                        <h3>Edit Meta tags</h3>
                    </div>
                </div>
                <div class="info-icon"><i class="fas fa-info-circle"></i></div>
            </a>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-4 col-lg-2-4 mb-4">
        <div class="dashboard-card" style="background-color:#ff7043;">
            <a href="set_logo/edit_logo.php">
                <div class="card-inner">
                    <div class="emoji">⚙️</div>
                    <div class="text-area">
                        <h3>edit header</h3>
                    </div>
                </div>
                <div class="info-icon"><i class="fas fa-info-circle"></i></div>
            </a>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-4 col-lg-2-4 mb-4">
        <div class="dashboard-card" style="background-color:#ffca28;">
            <a href="set_banner/list_banner.php">
                <div class="card-inner">
                    <div class="emoji">🖼️</div>
                    <div class="text-area">
                        <h3>Banner หน้าหลัก</h3>
                    </div>
                </div>
                <div class="info-icon"><i class="fas fa-info-circle"></i></div>
            </a>
        </div>
    </div>



    <div class="col-12 col-sm-6 col-md-4 col-lg-2-4 mb-4">
        <div class="dashboard-card" style="background-color:#d4e157;">
            <a href="set_footer/edit_footer.php">
                <div class="card-inner">
                    <div class="emoji">⬇️</div>
                    <div class="text-area">
                        <h3>Edit footer</h3>
                    </div>
                </div>
                <div class="info-icon"><i class="fas fa-info-circle"></i></div>
            </a>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-4 col-lg-2-4 mb-4">
        <div class="dashboard-card" style="background-color:#ef5350;">
            <a href="set_about/edit_about.php">
                <div class="card-inner">
                    <div class="emoji">ℹ️</div>
                    <div class="text-area">
                        <h3>Edit หน้า about</h3>
                    </div>
                </div>
                <div class="info-icon"><i class="fas fa-info-circle"></i></div>
            </a>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-4 col-lg-2-4 mb-4">
        <div class="dashboard-card" style="background-color:#66bb6a;">
            <a href="set_service/edit_service.php">
                <div class="card-inner">
                    <div class="emoji">🛠️</div>
                    <div class="text-area">
                        <h3>Edit หน้า service</h3>
                    </div>
                </div>
                <div class="info-icon"><i class="fas fa-info-circle"></i></div>
            </a>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-4 col-lg-2-4 mb-4">
        <div class="dashboard-card" style="background-color:#29b6f6;">
            <a href="set_contact/edit_contact.php">
                <div class="card-inner">
                    <div class="emoji">📞</div>
                    <div class="text-area">
                        <h3>Edit หน้า contact</h3>
                    </div>
                </div>
                <div class="info-icon"><i class="fas fa-info-circle"></i></div>
            </a>
        </div>
    </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-2-4 mb-4">
        <div class="dashboard-card" style="background-color:#26a69a;">
            <a href="set_comment/comment_service.php">
                <div class="card-inner">
                    <div class="emoji">💬</div>
                    <div class="text-area">
                        <h3>ความคิดเห็น</h3>
                        <div class="count"><?= $latestCommentId ?></div>
                        <div class="label">ทั้งหมดในระบบ</div>
                    </div>
                </div>
                <div class="info-icon"><i class="fas fa-info-circle"></i></div>
            </a>
        </div>
    </div>

</div>
    </div> <script src="js/index_.js?v=<?= time(); ?>"></script>
</body>
</html>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// เชื่อมต่อฐานข้อมูล
// include '../../lib/connect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/trandar/lib/connect.php';

// ตรวจสอบว่า session มี user_id หรือไม่
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // ดึงข้อมูลผู้ใช้จากฐานข้อมูล
    $sql = "SELECT profile_img FROM mb_user WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $img_file = "default.png"; // กรณีไม่พบข้อมูล
    
    if ($row = $result->fetch_assoc()) {
        $img_file = $row['profile_img'];
    }
}
?>

<div id="loading-overlay" class="hidden">
    <div class="spinner"></div>
</div>

<div class="header-top">
    <div class="container-fluid">

        <div class="header-top-left">
            <div>
                <span class="toggle-button">
                    <i id="toggleIcon" class="fas fa-bars"></i>
                </span>
            </div>
            <div>
                <a href="#">
                    <!-- <img src="#" alt="" class="logo"> -->
                </a>
            </div>
        </div>
            
        <div class="header-top-right">

            <div class="header-item">
                <i class="fas fa-bell"></i>
            </div>

            <!-- <div>
                <select id="language-select" class="language-select">
                </select>
            </div>     -->

            <div class="profile-container"> 
                <img src="../../public/img/<?php echo htmlspecialchars($img_file); ?>" alt="Profile Picture" class="profile-pic">
            </div>

            <div class="dropdown">
                <button class="dropdown-btn">
                    <i class="fas fa-caret-up"></i>
                </button>
            </div>

        </div>

    </div>
</div>
<div class="dropdown-content">

    <a href="profile.php">Profile</a>
    <a href="<?php echo $base_path_admin.'logout.php' ?>">Log out</a>

</div>

<div id="showTabSidebar" class="row row-tab"></div>

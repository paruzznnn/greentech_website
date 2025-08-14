<?php
require_once('../lib/connect.php');
global $conn;
?>
<!DOCTYPE html>
<html>

<head>
   
 

    <?php include 'inc_head.php' ?>
    <link href="css/index_.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/news_.css?v=<?php echo time(); ?>" rel="stylesheet">

    <style>
        /* Basic styles for pagination container */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            /* font-family: Arial, sans-serif; */
        }

        /* Styles for each pagination link */
        .pagination a {
            display: inline-block;
            margin: 0 5px;
            padding: 0px 10px;
            text-decoration: none;
            color: #555;
            /* border: 1px solid #ddd; */
            border-radius: 4px;
            transition: background-color 0.3s, color 0.3s;
        }

        /* Hover effect for pagination links */
        .pagination a:hover {
            background-color: #f1f1f1;
            color: #ffa719;
        }

        /* Active page styling */
        .pagination a.active {
            background-color: #ffa719;
            color: white;
            border: 1px solid #ffa719;
        }

        /* Styles for disabled links (e.g., first or last page) */
        .pagination a[disabled] {
            color: #ccc;
            pointer-events: none;
            border-color: #ccc;
        }

        .btn-search{
            border: none;
            background-color: #ffa719;
            color: #ffffff;
            border-radius: 0px 10px 10px 0px;
        }

    </style>

</head>
<?php
require_once('../lib/connect.php'); // เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล
global $conn; // เข้าถึงตัวแปรเชื่อมต่อฐานข้อมูล
include 'template/header.php';
include 'template/navbar_slide.php';
// ตรวจสอบว่าเชื่อมต่อฐานข้อมูลสำเร็จหรือไม่ (สำหรับ Debugging)
if (!$conn) {
    die("ERROR: ไม่สามารถเชื่อมต่อฐานข้อมูลได้: " . mysqli_connect_error());
} else {
    // echo "DEBUG: เชื่อมต่อฐานข้อมูลสำเร็จแล้ว<br>"; // สามารถเปิดใช้เพื่อ Debug ได้
}

// ----------------------------------------------------------------------------------
// ส่วนการดึงข้อมูลวิดีโอจากฐานข้อมูล (แสดงทุกอัน เรียงตามเวลาล่าสุด)
// ----------------------------------------------------------------------------------
$sql = "SELECT youtube_id, title, description FROM videos ORDER BY created_at DESC";
// หมายเหตุ:
// - "youtube_id", "title", "description" ต้องตรงกับชื่อคอลัมน์ในตาราง 'videos' ของคุณ
// - "ORDER BY created_at DESC" (เรียงลำดับจากวิดีโอใหม่สุดไปเก่าสุด)

$result = $conn->query($sql);

// ตรวจสอบว่า Query SQL ทำงานสำเร็จหรือไม่ (สำหรับ Debugging)
if (!$result) {
    die("ERROR: ข้อผิดพลาดในการเรียกข้อมูลจากฐานข้อมูล: " . $conn->error);
} else {
    // echo "DEBUG: Query ดึงข้อมูลสำเร็จแล้ว<br>"; // สามารถเปิดใช้เพื่อ Debug ได้
    // echo "DEBUG: จำนวนวิดีโอที่พบ: " . $result->num_rows . " รายการ<br>"; // สามารถเปิดใช้เพื่อ Debug ได้
}

?>


<!DOCTYPE html>
<html lang="th">
<head>
    <link rel="icon" type="image/x-icon" href="../../../public/img/q-removebg-preview1.png">
    <link rel="icon" href="../../../public/img/q-removebg-preview1.png" type="image/png">
     <!-- SEO Tags -->
   

   
    <script>
    function filterVideos() {
        const input = document.getElementById('videoSearchInput');
        const filter = input.value.toLowerCase();
        const cards = document.querySelectorAll('.video-card'); // เลือกทุกการ์ดวิดีโอ

        cards.forEach(card => {
            // ดึงข้อความจากการ์ด (ชื่อเรื่อง + คำอธิบาย)
            const title = card.querySelector('.card-title').innerText.toLowerCase();
            const description = card.querySelector('.card-text').innerText.toLowerCase();
            
            // ตรวจสอบว่ามีคำที่ค้นหาอยู่ในชื่อเรื่องหรือคำอธิบายหรือไม่
            if (title.includes(filter) || description.includes(filter)) {
                card.style.display = 'block'; // แสดงการ์ด
            } else {
                card.style.display = 'none';  // ซ่อนการ์ด
            }
        });
    }
    </script>
    
    <?php include 'inc_head.php'?> <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body>
   
     
     <div class="container" style="max-width: 90%;">
        <div class="row align-items-center" style="padding: 40px 0;">
            <div class="col-md-6">
                <h2 style="font-size: 28px; font-weight: bold; margin: 0;">คลังวิดีโอทั้งหมด</h2>
            </div>

            <div class="col-md-6 text-end">
                <div style="display: inline-flex; align-items: center; border: 1px solid #ccc; border-radius: 4px; overflow: hidden;">
                    <input type="text" id="videoSearchInput" placeholder="ค้นหาวิดีโอ..."
                        onkeyup="filterVideos()"
                        style="padding: 8px 12px; font-size: 16px; border: none; outline: none; width: 250px;">
                    <button type="button" onclick="filterVideos()"
                        style="background-color: #f37021; border: none; padding: 8px 16px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-search" style="color: white; font-size: 18px;"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <div class="row">
            <?php 
            // ตรวจสอบว่ามีข้อมูลวิดีโอหรือไม่ ก่อนที่จะวนลูป
            if ($result->num_rows > 0) : 
                // วนลูปเพื่อแสดงผลวิดีโอแต่ละรายการ
                while ($v = $result->fetch_assoc()) :
                    // สำหรับ Debugging: แสดงข้อมูลของแต่ละวิดีโอที่ดึงมาได้
                    // echo "<pre>";
                    // print_r($v);
                    // echo "</pre>";
                ?>
                <div class="col-md-3 mb-4 video-card">
                    <div class="card h-100">
                        <div class="ratio ratio-16x9">
                            <iframe
                                src="https://www.youtube.com/embed/<?= htmlspecialchars($v['youtube_id']) ?>"
                                title="<?= htmlspecialchars($v['title']) ?>"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                referrerpolicy="strict-origin-when-cross-origin"
                                allowfullscreen>
                            </iframe>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title"><?= htmlspecialchars($v['title']) ?></h6>
                            <p class="card-text"><?= htmlspecialchars($v['description']) ?></p>
                        </div>
                    </div>
                </div>
                <?php endwhile;
            else : // ถ้าไม่มีข้อมูลวิดีโอในฐานข้อมูล
            ?>
                <div class="col-12 text-center">
                    <p class="alert alert-info">ไม่พบวิดีโอในขณะนี้</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'template/footer.php'?> 
    <script src="js/index_.js?v=<?php echo time();?>"></script>
    </body>
</html>

<style>
#navbar-menu {
    background-color: white;
    position: relative;
    z-index: 999;
    border-bottom: 1px solid #ddd;
    overflow: visible;
    background-color: #ff9900;
}
.container {
    position: relative;
    overflow: visible;
}


.over-menu {
    display: flex;
    justify-content: space-around;
    align-items: center;
    padding: 6px 0;
    overflow: visible;
}

.over-menu a {
    text-decoration: none;
    padding: 10px 15px;
    color: #333;
    font-weight: 500;
    position: relative;
}

.dropdown {
    position: relative; /* ✅ Anchor ของ absolute dropdown */
}

.dropdown-content {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background-color: #fff;
    box-shadow: 0px 8px 16px rgba(0,0,0,0.1);
    z-index: 10000;
    min-width: 180px;
    max-width: 220px;
    border-radius: 4px;
}

.dropdown-show {
    display: flex;
    flex-direction: column;
}

.dropdown-item {
    padding: 10px 15px;
    color: #333;
    text-decoration: none;
}

.dropdown-item:hover {
    background-color: #f0f0f0;
}

.dropbtn {
    cursor: pointer;
}
.card-text {
    display: -webkit-box;
    -webkit-line-clamp: 2;       /* ✅ จำกัด 3 บรรทัด */
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;     /* ✅ เพิ่ม ... ต่อท้ายถ้าเกิน */
    line-height: 1.4em;
    max-height: calc(1.4em * 2); /* ป้องกันบางเบราว์เซอร์ที่ไม่รองรับ line-clamp */
}
</style>
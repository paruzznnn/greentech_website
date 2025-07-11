<?php

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>

    <?php include 'inc_head.php'?>
    
    
</head>
<body>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <?php include 'template/header.php'?>
    <?php include 'template/navbar_slide.php'?>

    <div class="container">
    <div class="row align-items-center" style="padding: 40px 0;">
        
        <!-- ฝั่งซ้าย: Blog Header -->
        <div class="col-md-6">
            <h2 style="font-size: 28px; font-weight: bold; margin: 0;">Design&Idea</h2>
        </div>

        <!-- ฝั่งขวา: Search box -->
        <div class="col-md-6 text-end">
            <form action="project-search.php" method="get" 
                  style="display: inline-flex; align-items: center; border: 1px solid #ccc; border-radius: 4px; overflow: hidden;">
                <input type="text" name="q" placeholder="Project Search..." 
                       style="padding: 8px 12px; font-size: 16px; border: none; outline: none; width: 250px;">
                <button type="submit" 
                        style="background-color: #f37021; border: none; padding: 8px 16px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-search" style="color: white; font-size: 18px;"></i>
                </button>
            </form>
        </div>

    </div>
</div>


    <div class="container pb-5">
    <div class="row">
        <!-- Blog Card 1 -->
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Blog Image 1">
                <div class="card-body">
                    <h6 class="card-title">5 เคล็ดลับติดตั้งแผ่นฝ้าอะคูสติก</h6>
                    <p class="card-text"><strong>Trandar Solo</strong> ให้สวยด้วยตัวเองแบบง่ายๆ</p>
                </div>
            </div>
        </div>

        <!-- Blog Card 2 -->
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Blog Image 2">
                <div class="card-body">
                    <h6 class="card-title">ตามส่อง 5 อาคารดังในกรุงเทพ</h6>
                    <p class="card-text"><strong>Mineral Fiber</strong> ที่เลือกใช้ฝ้า Trandar</p>
                </div>
            </div>
        </div>

        <!-- Blog Card 3 -->
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Blog Image 3">
                <div class="card-body">
                    <h6 class="card-title">โปรแกรมจำลองค่าการกันเสียง</h6>
                    <p class="card-text"><strong>INSUL</strong> Acoustics Software ที่สุดของมาตรฐาน</p>
                </div>
            </div>
        </div>

        <!-- Blog Card 4 -->
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Blog Image 4">
                <div class="card-body">
                    <h6 class="card-title">5 สิ่งที่ควรรู้ก่อนเลือกฝ้าอะคูสติก</h6>
                    <p class="card-text">เพื่อคุณภาพเสียงและความสวยงามที่ลงตัว</p>
                </div>
            </div>
        </div>
    </div>
</div>



    <?php include 'template/footer.php'?>
    


    <script src="js/index_.js?v=<?php echo time();?>"></script>

</body>
</html>
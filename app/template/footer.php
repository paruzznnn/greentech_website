<?php
//header-top-right
$isProtocol = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
$isFile = ($isProtocol === 'http') ? '.php' : '';

$menuItems = [
    // 
    [
        'id' => 0,
        'icon' => 'fas fa-user-plus',
        'text' => '',
        'translate' => 'Sign_up',
        'link' => 'register'. $isFile,
        'modal_id' => ''
    ],
    // [
    //     'id' => 1,
    //     'icon' => 'fas fa-sign-in-alt',
    //     'text' => '',
    //     'translate' => 'Sign_in',
    //     'link' => '#',
    //     'modal_id' => 'myBtn-sign-in'
    // ],
];
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<footer style="background-color: #393939; color: #ccc; padding: 40px 0; ">
    <div style="text-align: center; margin-bottom: 40px;">
       <!-- <img src="../public/img/q-removebg-preview.png" alt="Your Logo"
     style="height: 50px; margin-bottom: 20px; 
            background-color: #f0f0f0; /* สีเทาอ่อน */
            border-radius: 50%;         /* ทำให้เป็นวงกลม */
            padding: 10px;              /* ระยะห่างรอบรูป */
            display: inline-block;"> -->

        <!-- <img class="footer-logo" src="../public/img/q-removebg-preview.png" alt="">
 <img class="footer-logo" src="../public/img/2logo-png.png" alt=""> -->
        <h2 style="color: #fff; font-weight: bold;">ลงทะเบียน</h2>
        <p style="color: #aaa;">สมัครรับจดหมายข่าวของเราสำหรับข่าวสารล่าสุด และข้อเสนอสุดพิเศษ</p>
        <div style="margin-top: 20px;">
            <div id="auth-buttons">
    <?php foreach ($menuItems as $item): ?>
        <a type="button" href="<?php echo $item['link']; ?>" id="<?php echo $item['modal_id'] ?>">
            <i class="<?php echo $item['icon']; ?>"></i>
            <span data-translate="<?php echo $item['translate']; ?>" lang="th">
                <?php echo $item['text']; ?>
            </span>
        </a>
    <?php endforeach; ?>
</div>
        </div>
        
    </div>

    <div style="display: flex; justify-content: center; flex-wrap: wrap; max-width: 1200px; margin: 0 auto;">
        <div style="flex: 1; width: 300px; margin-bottom: 30px; padding-right:60px;">
            <p style="font-size :25px;">เกี่ยวกับเรา</p>
            <p>บริษัท แทรนดาร์ อินเตอร์เนชั่นแนล จำกัดได้ก่อตั้งขึ้นเมื่อวันที่ 1 มีนาคม 2531 เราเป็นผู้เชี่ยวชาญด้านระบบฝ้าดูดซับเสียง ผนังกั้นเสียงและฝ้าอะคูสติกทุกชนิด เรามีทีมงานและผู้เชี่ยวชาญที่พร้อมให้คำปรึกษาในการออกแบบและติดตั้ง พร้อมทั้งผลิตและจำหน่ายแผ่นอะคูสติก ผนังดูดซับเสียง ซาวน์บอร์ด ผนังกั้นเสียง ฝ้ากันเสียง แผ่นฝ้า ที่ได้มาตรฐานจากทั้งในและต่างประเทศ รวมถึงการให้บริการที่มีประสิทธิภาพจากแทรนดาร์ อะคูสติก</p>
            <!-- <p>การจัดส่งสินค้า</p> -->
            
        </div>
        <!-- <div style="flex: 1; min-width: 200px; margin-bottom: 30px;">
            <p>นโยบายการเปลี่ยน/คืน</p>
            <p>รถเข็นสินค้า</p>
            <p>แคตตาล็อกและโบรชัวร์</p>
        </div> -->
        <div style="flex: 1; width: 300px; margin-bottom: 30px;  padding-left:30px;  padding-right:30px;">
            <p style="font-size :25px;">ติดต่อเรา</p>
            <p>102 Phatthanakan 40, Suan Luang, Bangkok 10250</p>
            <p>(+66)2 722 7007</p>
            <p>info@trandar.com</p>
            <p>Monday – Friday 08:30 AM – 05:00 PM</p>
            <p>Saturday 08:30 AM – 12:00 PM</p>
            
            <div style="margin-top: 0px;">
    <!-- <a href="https://www.facebook.com/trandaracoustic/" class="social-icon facebook"><i class="fab fa-facebook-f"></i></a>
    <a href="https://www.instagram.com/trandaracoustics/" class="social-icon instagram"><i class="fab fa-instagram"></i></a>
    <a href="https://www.youtube.com/channel/UCewsEEtw8DOwSWoQ6ae_Uwg/" class="social-icon youtube"><i class="fab fa-youtube"></i></a>
    <a href="https://lin.ee/yoSCNwF" class="social-icon line"><i class="fab fa-line"></i></a>
    <a href="https://www.tiktok.com/@trandaracoustics" class="social-icon tiktok"><i class="fab fa-tiktok"></i></a> -->
</div>
        </div>
         <div style="flex: 1; width: 300px; margin-bottom: 30px;  padding-left:60px; ">
            <p style="font-size :25px;">Follow Us</p>
            <div style="margin-top: 0px;">
    <a href="https://www.facebook.com/trandaracoustic/" class="social-icon facebook"><i class="fab fa-facebook-f"></i></a>
    <a href="https://www.instagram.com/trandaracoustics/" class="social-icon instagram"><i class="fab fa-instagram"></i></a>
    <a href="https://www.youtube.com/channel/UCewsEEtw8DOwSWoQ6ae_Uwg/" class="social-icon youtube"><i class="fab fa-youtube"></i></a>
    <a href="https://lin.ee/yoSCNwF" class="social-icon line"><i class="fab fa-line"></i></a>
    <a href="https://www.tiktok.com/@trandaracoustics" class="social-icon tiktok"><i class="fab fa-tiktok"></i></a>
        
    </div>
        </div>
        </div>
    <div style="text-align: center; color: #888; font-size: 13px; margin-top: 40px;">
        © 2025 TRANDAR INTERNATIONAL CO., LTD. ALL RIGHTS RESERVED
    </div>
    
</footer>

<style>
   .social-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  font-size: 22px;
  color: white;
  margin: 0 8px;
  text-decoration: none;
}

/* สีพื้นหลังของแต่ละไอคอน */
.social-icon.facebook {
  background-color: #3b5998;
}
.social-icon.instagram {
  background-color: #e1306c;
}
.social-icon.youtube {
  background-color: #ff0000;
}
.social-icon.line {
  background-color: #00c300;
}
.social-icon.tiktok {
  background-color: #000000;
}

    /* --- Footer Styles --- */

/* --- Footer Styles --- */
.footer {
    /* background-color: #353535; สีพื้นหลังเทาเข้มจากภาพ moodandtone */
    color: #cccccc;           /* สีตัวอักษรตั้งต้น */
    padding: 60px 0 20px 0;    /* การเว้นระยะขอบ */
    
}

/* --- Footer Links List --- */
.footer-links {
    list-style: none; /* เอา bullet point หน้า list ออก */
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 12px; /* ระยะห่างระหว่างลิ้งค์ */
}

.footer-links a {
    color: #cccccc;
    text-decoration: none;
    font-size: 14px;
}

.footer-links a:hover {
    color: #ffffff; /* ทำให้ลิ้งค์สว่างขึ้นเมื่อเมาส์ชี้ */
}


/* --- Footer Bottom Bar (สไตล์จากภาพ trandar.com) --- */
.footer-bottom {
    border-top: 1px solid #555555; /* เส้นคั่นด้านบน */
    padding-top: 20px;
    margin-top: 40px;
    display: flex;
    justify-content: space-between; /* จัดให้ข้อความและไอคอนอยู่คนละฝั่ง */
    align-items: center;
}

.copyright-text p {
    margin: 0;
    color: #aaaaaa;
    font-size: 12px;
}

.footer-social-icons a {
    color: #aaaaaa;
    margin-left: 15px; /* ระยะห่างระหว่างไอคอน */
    text-decoration: none;
    font-size: 14px;
    /* คุณสามารถใส่สไตล์เพิ่มเติมให้ไอคอนเป็นกล่องๆได้ถ้าต้องการ */
}

.footer-social-icons a:hover {
    color: #ffffff;
}

/* --- Responsive for mobile --- */
@media (max-width: 767px) {
    .footer-links {
        margin-bottom: 30px; /* เพิ่มระยะห่างระหว่างคอลัมน์บนมือถือ */
    }
    .footer-bottom {
        flex-direction: column; /* บนมือถือให้เรียงบน-ล่าง */
        text-align: center;
    }
    .copyright-text {
        margin-bottom: 15px;
    }
    .footer-social-icons a {
        margin: 0 8px;
    }
}
</style>
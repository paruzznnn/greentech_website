<!-- <?php
// session_start();

// session_unset();
// session_destroy();

// header("Location: ../index.php");
// exit();
?> -->

<?php
session_start();

session_unset();
session_destroy();

// เปลี่ยน redirect ไปหน้าที่จะล้าง sessionStorage ด้วย JavaScript
header("Location: logged_out.html");
exit();
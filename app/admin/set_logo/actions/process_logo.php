<?php
// actions/process_logo.php

// สำหรับ Debugging เท่านั้น: แสดง Error บนหน้าจอ
// ควรปิดเมื่อนำไปใช้จริงบน Production Server เพื่อความปลอดภัย
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json'); // กำหนดให้ response เป็น JSON
date_default_timezone_set('Asia/Bangkok'); // ตั้งค่าโซนเวลา

// ** สำคัญ: ต้องมีสองบรรทัดนี้ ไม่งั้น $conn และ $base_path จะไม่ถูกกำหนด **
// Path ที่ถูกต้องขึ้นอยู่กับโครงสร้างไฟล์ของคุณ
// ตัวอย่าง: ถ้า process_logo.php อยู่ที่ app/admin/set_logo/actions/
// และ lib/ อยู่ที่ root ของโปรเจกต์
// Path จะเป็น ../../../lib/
require_once(__DIR__ . '/../../../lib/connect.php'); // Include database connection
require_once(__DIR__ . '/../../../lib/base_directory.php'); // Include base_directory.php for $base_path

// ประกาศ global variable เพื่อให้เข้าถึงได้ (ถ้า connect.php และ base_directory.php กำหนดตัวแปรใน global scope)
global $conn;
global $base_path; // $base_path ควรเป็น URL root เช่น https://www.trandar.com

$response = ['status' => 'error', 'message' => 'Invalid request.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_logo') {
    $logo_id = $_POST['logo_id'] ?? 0;
    $old_image_path_db = $_POST['old_image_path'] ?? ''; // Path เก่าที่ได้จากฐานข้อมูล (อาจเป็น Full URL หรือ Relative Path)

    // ตรวจสอบว่า ID เป็น 1 เสมอสำหรับโลโก้
    if ($logo_id != 1) {
        $response['message'] = 'Invalid Logo ID. Logo must be ID 1.';
        echo json_encode($response);
        exit;
    }

    // กำหนด Path สำหรับเก็บรูปภาพโลโก้บนเซิร์ฟเวอร์ (Physical Path)
    // ควรใช้ $_SERVER['DOCUMENT_ROOT'] เพื่อให้เป็น Absolute Path ที่ถูกต้อง
    $upload_dir_physical = $_SERVER['DOCUMENT_ROOT'] . '/public/img/';
    
    // ตรวจสอบและสร้างโฟลเดอร์ถ้ายังไม่มี
    if (!is_dir($upload_dir_physical)) {
        if (!mkdir($upload_dir_physical, 0775, true)) { // ใช้ 0775 หรือตามเหมาะสม
            $response['message'] = 'Failed to create upload directory.';
            echo json_encode($response);
            exit;
        }
    }

    $image_path_for_db = $old_image_path_db; // กำหนดให้ Path เก่าเป็นค่าเริ่มต้นสำหรับเก็บใน DB

    // ตรวจสอบว่ามีการอัปโหลดไฟล์ใหม่หรือไม่
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_name = $_FILES['image']['tmp_name'];
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name_new = uniqid('logo_') . '.' . $file_extension; // สร้างชื่อไฟล์ใหม่ที่ไม่ซ้ำกัน
        $destination_physical = $upload_dir_physical . $file_name_new;

        // ลบรูปภาพเก่าออกก่อน ถ้าไม่ใช่รูป default 'LOGOTRAND.png'
        // $old_image_path_db คือ Path ที่ดึงมาจาก DB ซึ่งอาจเป็น URL Path เช่น https://www.trandar.com/public/img/old.png
        // ต้องแปลงเป็น Physical Path ก่อนลบ
        // ตรวจสอบว่าเป็น URL เต็มหรือไม่
        $old_local_file_path = '';
        if (strpos($old_image_path_db, $base_path) === 0) { // ถ้าเป็น URL เต็ม
            $old_local_file_path = str_replace($base_path, $_SERVER['DOCUMENT_ROOT'], $old_image_path_db);
        } else { // ถ้าเป็น relative path (เช่น /public/img/old.png)
            $old_local_file_path = $_SERVER['DOCUMENT_ROOT'] . $old_image_path_db;
        }
        
        // ตรวจสอบว่าไฟล์เก่าไม่ใช่ LOGOTRAND.png และไฟล์มีอยู่จริงก่อนลบ
        if ($old_image_path_db && strpos($old_image_path_db, 'LOGOTRAND.png') === false) {
            if (file_exists($old_local_file_path) && is_file($old_local_file_path)) {
                if (!unlink($old_local_file_path)) {
                    error_log("Failed to delete old logo file: " . $old_local_file_path);
                    // อาจจะไม่ต้อง throw exception ที่นี่ เพื่อให้การอัปเดตใหม่ยังคงดำเนินไปได้
                }
            } else {
                 error_log("Old logo file not found or is not a file: " . $old_local_file_path);
            }
        }

        if (move_uploaded_file($file_tmp_name, $destination_physical)) {
            // Path ที่จะเก็บในฐานข้อมูล ควรเป็น relative path จาก Document Root
            // เพื่อให้สามารถใช้ $base_path นำหน้าได้ในภายหลัง
            $image_path_for_db = '/public/img/' . $file_name_new;

            // **หากยืนยันว่าต้องการเก็บเป็น Full URL ใน DB จริงๆ**
            // คุณสามารถเปลี่ยนบรรทัดด้านบนเป็น:
            // $image_path_for_db = $base_path . '/public/img/' . $file_name_new; 
            // แต่แนะนำให้เก็บเป็น relative path จะดีกว่า
        } else {
            $response['message'] = 'Failed to upload new image.';
            echo json_encode($response);
            exit;
        }
    }

    try {
        // อัปเดต Path รูปภาพในฐานข้อมูล (UPDATE โดยใช้ ID = 1)
        $stmt = $conn->prepare("UPDATE logo_settings SET image_path = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $image_path_for_db, $logo_id);

        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Logo updated successfully.';
            // ส่ง Path ใหม่ที่บันทึกใน DB กลับไปให้ frontend
            $response['new_image_path'] = $image_path_for_db;
        } else {
            $response['message'] = 'Failed to update logo in database: ' . $conn->error;
        }
        $stmt->close();
    } catch (Exception $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
}

echo json_encode($response);
exit;
?>
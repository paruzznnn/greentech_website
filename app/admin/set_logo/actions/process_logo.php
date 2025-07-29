<?php
// actions/process_logo.php
include '../check_permission.php'; // ตรวจสอบสิทธิ์การเข้าถึง
// require_once(__DIR__ . '/../../../lib/connect.php'); // Include database connection
// require_once(__DIR__ . '/../../../lib/base_directory.php'); // Include base_directory.php for $base_path

header('Content-Type: application/json'); // กำหนดให้ response เป็น JSON

$response = ['status' => 'error', 'message' => 'Invalid request.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_logo') {
    $logo_id = $_POST['logo_id'] ?? 0;
    $old_image_path = $_POST['old_image_path'] ?? '';

    // ตรวจสอบว่า ID เป็น 1 เสมอสำหรับโลโก้
    if ($logo_id != 1) {
        $response['message'] = 'Invalid Logo ID.';
        echo json_encode($response);
        exit;
    }

    $upload_dir = __DIR__ . '/../../public/img/'; // Path สำหรับเก็บรูปภาพโลโก้ (ปรับตามโครงสร้างโปรเจกต์ของคุณ)
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // สร้างโฟลเดอร์ถ้ายังไม่มี
    }

    $image_path = $old_image_path; // กำหนดให้ Path เก่าเป็นค่าเริ่มต้น

    // ตรวจสอบว่ามีการอัปโหลดไฟล์ใหม่หรือไม่
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_name = $_FILES['image']['tmp_name'];
        $file_name = uniqid('logo_') . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $destination = $upload_dir . $file_name;

        // ลบรูปภาพเก่าออกก่อน ถ้าไม่ใช่รูป default
        if ($old_image_path && file_exists($base_path . $old_image_path) && strpos($old_image_path, 'LOGOTRAND.png') === false) {
             unlink($base_path . $old_image_path);
        }

        if (move_uploaded_file($file_tmp_name, $destination)) {
            $image_path = '../public/img/' . $file_name; // Path ที่จะเก็บในฐานข้อมูล (ปรับให้สัมพันธ์กับ root ของเว็บ)
        } else {
            $response['message'] = 'Failed to upload new image.';
            echo json_encode($response);
            exit;
        }
    }

    try {
        // อัปเดต Path รูปภาพในฐานข้อมูล (UPDATE โดยใช้ ID = 1)
        $stmt = $conn->prepare("UPDATE logo_settings SET image_path = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $image_path, $logo_id);

        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Logo updated successfully.';
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
<?php
include '../check_permission.php';
date_default_timezone_set('Asia/Bangkok');

// ตรวจสอบว่ามีไฟล์อัปโหลดมาหรือไม่ และจัดการการอัปโหลด
function handleImageUpload($file_array, $base_path) {
    if (isset($file_array) && $file_array['error'] == 0) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = strtolower(pathinfo($file_array['name'], PATHINFO_EXTENSION));

        if (in_array($fileExtension, $allowedExtensions)) {
            $timestamp = time();
            $randomString = bin2hex(random_bytes(5)); // สร้าง string สุ่ม 10 ตัวอักษร
            $newFileName = $timestamp . '-' . $randomString . '.' . $fileExtension;
            $uploadFileDir = '../../../public/news_img/';
            
            // ตรวจสอบและสร้าง directory ถ้ายังไม่มี
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }

            $destFilePath = $uploadFileDir . $newFileName;
            if (move_uploaded_file($file_array['tmp_name'], $destFilePath)) {
                // สร้าง URL ที่ถูกต้อง
                // ตรวจสอบว่ามาจาก localhost หรือ domain จริง
                $serverName = $_SERVER['SERVER_NAME'];
                if ($serverName == 'localhost' || $serverName == '127.0.0.1') {
                    // สำหรับ Localhost
                    return 'http://' . $serverName . '/trandar/public/news_img/' . $newFileName;
                } else {
                    // สำหรับ Production
                    return 'https://' . $serverName . '/public/news_img/' . $newFileName;
                }
            }
        }
    }
    return null; // ถ้าไม่มีการอัปโหลดหรือเกิดข้อผิดพลาด
}

// 1. อัปเดตข้อมูลที่มีอยู่
if (!empty($_POST['ids'])) {
    foreach ($_POST['ids'] as $index => $id) {
        $type = $_POST['types'][$index];
        $content = $_POST['contents'][$index];
        $author = $_POST['authors'][$index];
        $position = $_POST['positions'][$index];
        $image_url = $_POST['existing_images'][$index]; // ตั้งค่าเริ่มต้นเป็นภาพเดิม

        // ตรวจสอบว่ามีการอัปโหลดภาพใหม่หรือไม่
        if (isset($_FILES['images_files']['name'][$index]) && $_FILES['images_files']['error'][$index] == 0) {
            $image_file = [
                'name' => $_FILES['images_files']['name'][$index],
                'type' => $_FILES['images_files']['type'][$index],
                'tmp_name' => $_FILES['images_files']['tmp_name'][$index],
                'error' => $_FILES['images_files']['error'][$index],
                'size' => $_FILES['images_files']['size'][$index],
            ];
            $new_image_url = handleImageUpload($image_file, ''); // ส่งพารามิเตอร์ที่จำเป็น
            if ($new_image_url) {
                $image_url = $new_image_url;
            }
        }

        $stmt = $conn->prepare("UPDATE service_content SET type=?, content=?, image_url=?, author=?, position=? WHERE id=?");
        $stmt->bind_param("sssssi", $type, $content, $image_url, $author, $position, $id);
        $stmt->execute();
        $stmt->close();
    }
}

// 2. เพิ่มเนื้อหาใหม่ (ถ้ามี)
if (!empty($_POST['new_content'])) {
    $new_type = $_POST['new_type'];
    $new_content = $_POST['new_content'];
    $new_author = $_POST['new_author'];
    $new_position = $_POST['new_position'];
    $new_image = null;

    // ตรวจสอบว่ามีการอัปโหลดภาพใหม่สำหรับบล็อกใหม่หรือไม่
    if (isset($_FILES['new_image_file']) && $_FILES['new_image_file']['error'] == 0) {
        $new_image_url = handleImageUpload($_FILES['new_image_file'], ''); // ส่งพารามิเตอร์ที่จำเป็น
        if ($new_image_url) {
            $new_image = $new_image_url;
        }
    }
    
    $stmt = $conn->prepare("INSERT INTO service_content (type, content, image_url, author, position) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $new_type, $new_content, $new_image, $new_author, $new_position);
    $stmt->execute();
    $stmt->close();
}

echo "<script>alert('บันทึกเรียบร้อย'); window.location.href='edit_service.php';</script>";
?>
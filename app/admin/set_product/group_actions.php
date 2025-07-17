<?php
// *** ตรวจสอบ PATH นี้ให้ถูกต้องที่สุด ***
// สมมติว่า group_actions.php อยู่ที่ /admin/set_product/
// connect_db.php อยู่ที่ /inc/connect_db.php
require_once '../../../inc/connect_db.php';

// ตรวจสอบว่าเชื่อมต่อฐานข้อมูลได้หรือไม่
if (!isset($conn) || !$conn) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit();
}

// กำหนด base URL ของเว็บของคุณ (ต้องตรงกับ group_management.php)
$base_url = 'http://localhost/trandar/';
// กำหนด path สำหรับบันทึกรูปภาพ (ควรเป็น path สัมพัทธ์จาก root ของเว็บ)
$upload_dir_relative = 'public/mews_img/'; 
$upload_dir_absolute = realpath(__DIR__ . '/../../../' . $upload_dir_relative) . '/'; // Path จริงบน Server

// ฟังก์ชันสำหรับส่ง response กลับไป
function sendJsonResponse($status, $message) {
    echo json_encode(['status' => $status, 'message' => $message]);
    exit();
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add_group':
        $group_name = trim($_POST['group_name'] ?? '');
        $parent_group_id = !empty($_POST['parent_group_id']) ? (int)$_POST['parent_group_id'] : NULL;
        $image_path = ''; // Default empty for sub-groups or if no image uploaded

        if (empty($group_name)) {
            sendJsonResponse('error', 'กรุณากรอกชื่อหมวดหมู่');
        }

        // ตรวจสอบการอัปโหลดรูปภาพสำหรับกลุ่มหลัก
        if ($parent_group_id === NULL && isset($_FILES['group_image']) && $_FILES['group_image']['error'] === UPLOAD_ERR_OK) {
            $file_name = uniqid() . '_' . basename($_FILES['group_image']['name']);
            $target_file = $upload_dir_absolute . $file_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            $max_size = 5 * 1024 * 1024; // 5MB

            // ตรวจสอบชนิดไฟล์
            if (!in_array($imageFileType, $allowed_types)) {
                sendJsonResponse('error', 'ไม่อนุญาตให้ใช้ไฟล์ประเภทนี้. อนุญาตเฉพาะ JPG, JPEG, PNG, GIF.');
            }
            // ตรวจสอบขนาดไฟล์
            if ($_FILES['group_image']['size'] > $max_size) {
                sendJsonResponse('error', 'ขนาดไฟล์รูปภาพต้องไม่เกิน 5MB.');
            }
            // ย้ายไฟล์
            if (move_uploaded_file($_FILES['group_image']['tmp_name'], $target_file)) {
                // บันทึกเป็น URL เต็มลงใน DB
                $image_path = $base_url . $upload_dir_relative . $file_name;
            } else {
                sendJsonResponse('error', 'เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ.');
            }
        }

        $stmt = $conn->prepare("INSERT INTO dn_shop_groups (group_name, parent_group_id, image_path) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $group_name, $parent_group_id, $image_path);

        if ($stmt->execute()) {
            sendJsonResponse('success', 'เพิ่มหมวดหมู่สำเร็จ!');
        } else {
            sendJsonResponse('error', 'เพิ่มหมวดหมู่ไม่สำเร็จ: ' . $stmt->error);
        }
        $stmt->close();
        break;

    case 'edit_group':
        $group_id = (int)$_POST['group_id'];
        $group_name = trim($_POST['group_name'] ?? '');
        $group_type = $_POST['group_type'] ?? ''; // 'main' or 'sub'
        $parent_group_id = !empty($_POST['parent_group_id']) ? (int)$_POST['parent_group_id'] : NULL;
        $new_image_uploaded = false;
        $current_image_path_from_db = '';

        if (empty($group_name)) {
            sendJsonResponse('error', 'กรุณากรอกชื่อหมวดหมู่');
        }

        // ดึง image_path เดิมจากฐานข้อมูลก่อน
        $stmt_select_old_image = $conn->prepare("SELECT image_path FROM dn_shop_groups WHERE group_id = ?");
        $stmt_select_old_image->bind_param("i", $group_id);
        $stmt_select_old_image->execute();
        $result_old_image = $stmt_select_old_image->get_result();
        if ($row_old_image = $result_old_image->fetch_assoc()) {
            $current_image_path_from_db = $row_old_image['image_path'];
        }
        $stmt_select_old_image->close();

        $image_to_save = $current_image_path_from_db; // ค่าเริ่มต้นคือใช้รูปภาพเดิม

        // ตรวจสอบการอัปโหลดรูปภาพใหม่สำหรับกลุ่มหลัก
        if ($group_type === 'main' && isset($_FILES['group_image']) && $_FILES['group_image']['error'] === UPLOAD_ERR_OK) {
            $file_name = uniqid() . '_' . basename($_FILES['group_image']['name']);
            $target_file = $upload_dir_absolute . $file_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            $max_size = 5 * 1024 * 1024; // 5MB

            if (!in_array($imageFileType, $allowed_types)) {
                sendJsonResponse('error', 'ไม่อนุญาตให้ใช้ไฟล์ประเภทนี้. อนุญาตเฉพาะ JPG, JPEG, PNG, GIF.');
            }
            if ($_FILES['group_image']['size'] > $max_size) {
                sendJsonResponse('error', 'ขนาดไฟล์รูปภาพต้องไม่เกิน 5MB.');
            }

            if (move_uploaded_file($_FILES['group_image']['tmp_name'], $target_file)) {
                $image_to_save = $base_url . $upload_dir_relative . $file_name; // บันทึกเป็น URL เต็ม
                $new_image_uploaded = true;

                // ลบรูปภาพเก่าถ้าไม่ใช่ placeholder
                if (!empty($current_image_path_from_db) && strpos($current_image_path_from_db, 'group_placeholder.jpg') === false) {
                    // แปลง URL เต็มกลับเป็น path สัมพัทธ์จาก root ของโปรเจกต์
                    $old_image_relative_path = str_replace($base_url, '', $current_image_path_from_db);
                    $old_image_absolute_path = realpath(__DIR__ . '/../../../' . $old_image_relative_path);
                    
                    if (file_exists($old_image_absolute_path)) {
                        unlink($old_image_absolute_path);
                    }
                }
            } else {
                sendJsonResponse('error', 'เกิดข้อผิดพลาดในการอัปโหลดรูปภาพใหม่.');
            }
        } elseif ($group_type === 'sub') {
            // ถ้าเป็นกลุ่มย่อย ให้ล้าง image_path ใน DB
            $image_to_save = NULL; // หรือ '' แล้วแต่โครงสร้าง DB ของคุณ
            // ลบรูปเก่าถ้ากลุ่มนี้เคยเป็นกลุ่มหลักและมีรูป
            if (!empty($current_image_path_from_db) && strpos($current_image_path_from_db, 'group_placeholder.jpg') === false) {
                $old_image_relative_path = str_replace($base_url, '', $current_image_path_from_db);
                $old_image_absolute_path = realpath(__DIR__ . '/../../../' . $old_image_relative_path);
                if (file_exists($old_image_absolute_path)) {
                    unlink($old_image_absolute_path);
                }
            }
        }


        // อัปเดตข้อมูลในฐานข้อมูล
        // กรณีเป็นกลุ่มหลัก Parent ID ต้องเป็น NULL
        if ($group_type === 'main') {
            $stmt = $conn->prepare("UPDATE dn_shop_groups SET group_name = ?, parent_group_id = NULL, image_path = ? WHERE group_id = ?");
            $stmt->bind_param("ssi", $group_name, $image_to_save, $group_id);
        } else { // กรณีเป็นกลุ่มย่อย
            $stmt = $conn->prepare("UPDATE dn_shop_groups SET group_name = ?, parent_group_id = ?, image_path = NULL WHERE group_id = ?");
            $stmt->bind_param("sii", $group_name, $parent_group_id, $group_id);
        }
        
        if ($stmt->execute()) {
            sendJsonResponse('success', 'แก้ไขหมวดหมู่สำเร็จ!');
        } else {
            sendJsonResponse('error', 'แก้ไขหมวดหมู่ไม่สำเร็จ: ' . $stmt->error);
        }
        $stmt->close();
        break;

    case 'delete_group':
        $group_id = (int)$_POST['group_id'];

        // ตรวจสอบว่ามีกลุ่มย่อยอยู่ภายใต้กลุ่มนี้หรือไม่
        $stmt_check_sub = $conn->prepare("SELECT COUNT(*) FROM dn_shop_groups WHERE parent_group_id = ? AND del = '0'");
        $stmt_check_sub->bind_param("i", $group_id);
        $stmt_check_sub->execute();
        $stmt_check_sub->bind_result($sub_group_count);
        $stmt_check_sub->fetch();
        $stmt_check_sub->close();

        if ($sub_group_count > 0) {
            sendJsonResponse('error', 'ไม่สามารถลบหมวดหมู่นี้ได้เนื่องจากมีหมวดหมู่ย่อยอยู่ภายใต้หมวดหมู่นี้. กรุณาลบหมวดหมู่ย่อยก่อน.');
        }

        // ดึง image_path ของกลุ่มที่จะลบ
        $image_to_delete = '';
        $stmt_get_image = $conn->prepare("SELECT image_path FROM dn_shop_groups WHERE group_id = ?");
        $stmt_get_image->bind_param("i", $group_id);
        $stmt_get_image->execute();
        $result_get_image = $stmt_get_image->get_result();
        if ($row_get_image = $result_get_image->fetch_assoc()) {
            $image_to_delete = $row_get_image['image_path'];
        }
        $stmt_get_image->close();

        // อัปเดตสถานะ del เป็น 1 แทนการลบจริง
        $stmt = $conn->prepare("UPDATE dn_shop_groups SET del = '1' WHERE group_id = ?");
        $stmt->bind_param("i", $group_id);

        if ($stmt->execute()) {
            // ถ้ามีรูปภาพและไม่ใช่ placeholder ให้ลบไฟล์จริง
            if (!empty($image_to_delete) && strpos($image_to_delete, 'group_placeholder.jpg') === false) {
                // แปลง URL เต็มกลับเป็น path สัมพัทธ์จาก root ของโปรเจกต์
                $relative_image_to_delete = str_replace($base_url, '', $image_to_delete);
                $absolute_image_path = realpath(__DIR__ . '/../../../' . $relative_image_to_delete);
                
                if (file_exists($absolute_image_path)) {
                    unlink($absolute_image_path);
                }
            }
            sendJsonResponse('success', 'ลบหมวดหมู่สำเร็จ!');
        } else {
            sendJsonResponse('error', 'ลบหมวดหมู่ไม่สำเร็จ: ' . $stmt->error);
        }
        $stmt->close();
        break;

    default:
        sendJsonResponse('error', 'Action ไม่ถูกต้อง');
        break;
}

$conn->close(); // ปิดการเชื่อมต่อฐานข้อมูลเมื่อเสร็จสิ้นการทำงาน
?>
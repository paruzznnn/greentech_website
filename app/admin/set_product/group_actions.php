<?php
// group_actions.php
header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');

// --- เริ่มต้นการกำหนดค่า URL และ Path ภายใน group_actions.php ---

// ตรวจ protocol แบบปลอดภัย
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$port = isset($_SERVER['SERVER_PORT']) && (($scheme === 'http' && $_SERVER['SERVER_PORT'] != 80) || ($scheme === 'https' && $_SERVER['SERVER_PORT'] != 443)) ? ':' . $_SERVER['SERVER_PORT'] : '';

// คำนวณ ROOT_URL (URL หลักของเว็บไซต์)
$script_name = $_SERVER['SCRIPT_NAME'];
$base_uri = str_replace(basename($script_name), '', $script_name);

$root_app_uri = '/'; 
if (strpos($base_uri, '/trandar/') !== false) {
    $root_app_uri = '/trandar/';
}

$root_url = $scheme . '://' . $host . $port . $root_app_uri;

// กำหนด ROOT_DIR (Path ของ root project บน Server)
// ถ้า group_actions.php อยู่ที่ /var/www/html/trandar/admin/set_product/group_actions.php
// เราต้องการ ROOT_DIR เป็น /var/www/html/trandar/
define('ROOT_DIR', __DIR__ . '/../../..'); // Path จริงของ 'trandar' folder บน Server
define('PUBLIC_BASE_URL', $root_url . 'public/'); // ทำให้เป็น Constant เพื่อใช้ในฟังก์ชันด้านล่าง

// --- สิ้นสุดการกำหนดค่า URL และ Path ภายใน group_actions.php ---

require_once(ROOT_DIR . '/lib/connect.php'); // ใช้ ROOT_DIR เพื่อความชัดเจน
require_once(ROOT_DIR . '/inc/getFunctions.php');

$response = ['status' => 'error', 'message' => 'Invalid action.'];

if (isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'add_group':
            $group_name = trim($conn->real_escape_string($_POST['group_name']));
            // รับค่า parent_group_id ให้เป็น int หรือ NULL
            $parent_group_id = !empty($_POST['parent_group_id']) ? (int)$_POST['parent_group_id'] : null;
            $image_path = null;

            if (empty($group_name)) {
                $response = ['status' => 'error', 'message' => 'กรุณากรอกชื่อหมวดหมู่.'];
                echo json_encode($response);
                exit();
            }

            // ตรวจสอบว่าชื่อกลุ่มซ้ำหรือไม่ (สำหรับกลุ่มแม่และกลุ่มย่อยภายใต้กลุ่มแม่เดียวกัน)
            $check_sql = "SELECT COUNT(*) AS count FROM dn_shop_groups WHERE group_name = ? AND del = '0'";
            if ($parent_group_id !== null) {
                $check_sql .= " AND parent_group_id = ?";
            } else {
                $check_sql .= " AND parent_group_id IS NULL";
            }
            $stmt_check = $conn->prepare($check_sql);
            if ($stmt_check) {
                if ($parent_group_id !== null) {
                    $stmt_check->bind_param("si", $group_name, $parent_group_id);
                } else {
                    $stmt_check->bind_param("s", $group_name);
                }
                $stmt_check->execute();
                $check_result = $stmt_check->get_result();
                if ($check_result && $check_result->fetch_assoc()['count'] > 0) {
                    $response = ['status' => 'error', 'message' => 'ชื่อหมวดหมู่นี้มีอยู่แล้วในหมวดหมู่เดียวกัน!'];
                    echo json_encode($response);
                    exit();
                }
                $stmt_check->close();
            } else {
                $response = ['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการตรวจสอบชื่อหมวดหมู่: ' . $conn->error];
                echo json_encode($response);
                exit();
            }

            // จัดการอัปโหลดรูปภาพสำหรับกลุ่มแม่เท่านั้น
            if ($parent_group_id === null) { // ถ้าเป็นกลุ่มแม่
                if (isset($_FILES['group_image']) && $_FILES['group_image']['error'] == 0) {
                    $upload_dir = ROOT_DIR . '/public/uploads/group_images/'; 
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    $file_name = time() . '_' . uniqid() . '.' . strtolower(pathinfo($_FILES['group_image']['name'], PATHINFO_EXTENSION));
                    $target_file = $upload_dir . $file_name;
                    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                    $check = getimagesize($_FILES['group_image']['tmp_name']);
                    if ($check === false) {
                        $response = ['status' => 'error', 'message' => 'ไฟล์ที่อัปโหลดไม่ใช่รูปภาพ.'];
                        echo json_encode($response);
                        exit();
                    }

                    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                    if (!in_array($file_type, $allowed_types)) {
                        $response = ['status' => 'error', 'message' => 'ขออภัย, อนุญาตเฉพาะ JPG, JPEG, PNG & GIF files เท่านั้น.'];
                        echo json_encode($response);
                        exit();
                    }

                    if ($_FILES['group_image']['size'] > 5 * 1024 * 1024) {
                        $response = ['status' => 'error', 'message' => 'ขนาดไฟล์รูปภาพต้องไม่เกิน 5MB.'];
                        echo json_encode($response);
                        exit();
                    }

                    if (move_uploaded_file($_FILES['group_image']['tmp_name'], $target_file)) {
                        $image_path = PUBLIC_BASE_URL . 'uploads/group_images/' . $file_name; 
                    } else {
                        $response = ['status' => 'error', 'message' => 'ไม่สามารถอัปโหลดรูปภาพได้.'];
                        echo json_encode($response);
                        exit();
                    }
                }
            } elseif ($parent_group_id !== null && isset($_FILES['group_image']) && $_FILES['group_image']['error'] == 0) {
                // หากพยายามอัปโหลดรูปภาพสำหรับกลุ่มย่อย ให้เกิดข้อผิดพลาด
                $response = ['status' => 'error', 'message' => 'กลุ่มย่อยไม่สามารถมีรูปภาพได้.'];
                echo json_encode($response);
                exit();
            }

            // ใช้ NULL สำหรับ parent_group_id เมื่อเป็น NULL
            // **แก้ไขตรงนี้**: เพิ่มคอลัมน์ status และค่า '1'
            $sql = "INSERT INTO dn_shop_groups (group_name, parent_group_id, image_path, date_create, del, status) VALUES (?, ?, ?, NOW(), '0', '1')";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                // เปลี่ยนการ bind_param ให้รองรับ NULL ได้อย่างถูกต้องโดยใช้ "s" สำหรับ int และ "s" สำหรับ string ที่เป็น NULL
                // MySQL จะแปลง NULL string เป็น NULL ในคอลัมน์ INT หรือ TEXT โดยอัตโนมัติ
                $stmt->bind_param("sss", $group_name, $parent_group_id, $image_path);

                if ($stmt->execute()) {
                    $response = ['status' => 'success', 'message' => 'เพิ่มหมวดหมู่สำเร็จ!'];
                } else {
                    $response = ['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการเพิ่มหมวดหมู่: ' . $stmt->error];
                }
                $stmt->close();
            } else {
                $response = ['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: ' . $conn->error];
            }
            break;

        case 'edit_group':
            $group_id = (int)$_POST['group_id'];
            $group_name = trim($conn->real_escape_string($_POST['group_name']));
            $group_type = $_POST['group_type']; // 'main' or 'sub'
            $parent_group_id = null; // Default for main groups

            if (empty($group_name)) {
                $response = ['status' => 'error', 'message' => 'กรุณากรอกชื่อหมวดหมู่.'];
                echo json_encode($response);
                exit();
            }

            // ตรวจสอบว่าชื่อกลุ่มซ้ำหรือไม่
            $check_sql = "SELECT COUNT(*) AS count FROM dn_shop_groups WHERE group_name = ? AND group_id != ? AND del = '0'";
            if ($group_type === 'sub') {
                $parent_group_id = !empty($_POST['parent_group_id']) ? (int)$_POST['parent_group_id'] : null;
                if ($parent_group_id !== null) {
                    $check_sql .= " AND parent_group_id = ?";
                } else {
                    $check_sql .= " AND parent_group_id IS NULL"; 
                }
            } else { // main group
                $check_sql .= " AND parent_group_id IS NULL";
            }
            $stmt_check = $conn->prepare($check_sql);
            if ($stmt_check) {
                if ($group_type === 'sub' && $parent_group_id !== null) {
                    $stmt_check->bind_param("sii", $group_name, $group_id, $parent_group_id);
                } elseif ($group_type === 'sub' && $parent_group_id === null) {
                    $stmt_check->bind_param("si", $group_name, $group_id);
                } else { // main group
                    $stmt_check->bind_param("si", $group_name, $group_id);
                }
                $stmt_check->execute();
                $check_result = $stmt_check->get_result();
                if ($check_result && $check_result->fetch_assoc()['count'] > 0) {
                    $response = ['status' => 'error', 'message' => 'ชื่อหมวดหมู่นี้มีอยู่แล้วในหมวดหมู่เดียวกัน!'];
                    echo json_encode($response);
                    exit();
                }
                $stmt_check->close();
            } else {
                $response = ['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการตรวจสอบชื่อหมวดหมู่: ' . $conn->error];
                echo json_encode($response);
                exit();
            }

            // ดึง image_path ปัจจุบัน
            $sql_fetch_current_image = "SELECT image_path FROM dn_shop_groups WHERE group_id = ?";
            $stmt_fetch = $conn->prepare($sql_fetch_current_image);
            $stmt_fetch->bind_param("i", $group_id);
            $stmt_fetch->execute();
            $stmt_fetch->bind_result($current_image_path);
            $stmt_fetch->fetch();
            $stmt_fetch->close();

            $new_image_path = $current_image_path; // เริ่มต้นด้วย path รูปภาพปัจจุบัน

            // จัดการอัปโหลดรูปภาพสำหรับกลุ่มแม่เท่านั้น (ถ้ามีการเลือกไฟล์ใหม่)
            if ($group_type === 'main') {
                if (isset($_FILES['group_image']) && $_FILES['group_image']['error'] == 0) {
                    $upload_dir = ROOT_DIR . '/public/uploads/group_images/'; 
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    $file_name = time() . '_' . uniqid() . '.' . strtolower(pathinfo($_FILES['group_image']['name'], PATHINFO_EXTENSION));
                    $target_file = $upload_dir . $file_name;
                    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                    $check = getimagesize($_FILES['group_image']['tmp_name']);
                    if ($check === false) {
                        $response = ['status' => 'error', 'message' => 'ไฟล์ที่อัปโหลดไม่ใช่รูปภาพ.'];
                        echo json_encode($response);
                        exit();
                    }
                    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                    if (!in_array($file_type, $allowed_types)) {
                        $response = ['status' => 'error', 'message' => 'ขออภัย, อนุญาตเฉพาะ JPG, JPEG, PNG & GIF files เท่านั้น.'];
                        echo json_encode($response);
                        exit();
                    }
                    if ($_FILES['group_image']['size'] > 5 * 1024 * 1024) {
                        $response = ['status' => 'error', 'message' => 'ขนาดไฟล์รูปภาพต้องไม่เกิน 5MB.'];
                        echo json_encode($response);
                        exit();
                    }

                    if (move_uploaded_file($_FILES['group_image']['tmp_name'], $target_file)) {
                        $new_image_path = PUBLIC_BASE_URL . 'uploads/group_images/' . $file_name;
                        deleteOldImage($current_image_path); // เรียกใช้ฟังก์ชันที่กำหนดในไฟล์นี้
                    } else {
                        $response = ['status' => 'error', 'message' => 'ไม่สามารถอัปโหลดรูปภาพได้.'];
                        echo json_encode($response);
                        exit();
                    }
                }
            } else { // if group_type is 'sub'
                $new_image_path = null; // กลุ่มย่อยต้องไม่มีรูปภาพ
                deleteOldImage($current_image_path); // ลบรูปภาพเก่าถ้ากลุ่มถูกเปลี่ยนจาก main เป็น sub
                $parent_group_id = !empty($_POST['parent_group_id']) ? (int)$_POST['parent_group_id'] : null;
            }

            $sql = "UPDATE dn_shop_groups SET group_name = ?, parent_group_id = ?, image_path = ? WHERE group_id = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("sssi", $group_name, $parent_group_id, $new_image_path, $group_id);

                if ($stmt->execute()) {
                    $response = ['status' => 'success', 'message' => 'แก้ไขหมวดหมู่สำเร็จ!'];
                } else {
                    $response = ['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการแก้ไขหมวดหมู่: ' . $stmt->error];
                }
                $stmt->close();
            } else {
                $response = ['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: ' . $conn->error];
            }
            break;

        case 'delete_group':
            $group_id = (int)$_POST['group_id'];

            $conn->begin_transaction();
            try {
                // ดึง image_path ของกลุ่มที่กำลังจะลบ
                $sql_get_image = "SELECT image_path FROM dn_shop_groups WHERE group_id = ?";
                $stmt_get_image = $conn->prepare($sql_get_image);
                $stmt_get_image->bind_param("i", $group_id);
                $stmt_get_image->execute();
                $stmt_get_image->bind_result($image_to_delete);
                $stmt_get_image->fetch();
                $stmt_get_image->close();

                // Set parent_group_id to NULL for sub-groups under this main group
                $sql_update_children = "UPDATE dn_shop_groups SET parent_group_id = NULL WHERE parent_group_id = ?";
                $stmt_update_children = $conn->prepare($sql_update_children);
                if ($stmt_update_children) {
                    $stmt_update_children->bind_param("i", $group_id);
                    $stmt_update_children->execute();
                    $stmt_update_children->close();
                } else {
                    throw new Exception("Error preparing update children statement: " . $conn->error);
                }

                // Set group_id to NULL for products under this group
                $sql_update_products = "UPDATE dn_shop SET group_id = NULL WHERE group_id = ?";
                $stmt_update_products = $conn->prepare($sql_update_products);
                if ($stmt_update_products) {
                    $stmt_update_products->bind_param("i", $group_id);
                    $stmt_update_products->execute();
                    $stmt_update_products->close();
                } else {
                    throw new Exception("Error preparing update products statement: " . $conn->error);
                }

                // Soft delete the group
                $sql = "UPDATE dn_shop_groups SET del = '1' WHERE group_id = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("i", $group_id);
                    if ($stmt->execute()) {
                        $conn->commit();
                        $response = ['status' => 'success', 'message' => 'ลบหมวดหมู่สำเร็จ!'];
                        // ลบไฟล์รูปภาพหลังจากลบใน DB สำเร็จ
                        deleteOldImage($image_to_delete); // เรียกใช้ฟังก์ชันที่กำหนดในไฟล์นี้
                    } else {
                        throw new Exception("Error executing delete statement: " . $stmt->error);
                    }
                    $stmt->close();
                } else {
                    throw new Exception("Error preparing delete statement: " . $conn->error);
                }
            } catch (Exception $e) {
                $conn->rollback();
                $response = ['status' => 'error', 'message' => 'ไม่สามารถลบหมวดหมู่ได้: ' . $e->getMessage()];
            }
            break;
    }
}

/**
 * ฟังก์ชันสำหรับลบไฟล์รูปภาพเก่าจาก Server
 * @param string $image_url Full URL ของรูปภาพที่จะลบ
 */
function deleteOldImage($image_url) {
    // กำหนด placeholder image ที่แน่นอน
    $placeholder_image_name = 'group_placeholder.jpg'; // คุณอาจต้องปรับชื่อนี้
    $default_image_from_db = 'https://www.trandar.com/public/shop_img/6878c8c67917f_photo_2025-07-17_16-55-28.jpg'; // URL รูปภาพ default ที่พบในฐานข้อมูล

    if ($image_url && str_starts_with($image_url, PUBLIC_BASE_URL)) {
        $relative_path = substr($image_url, strlen(PUBLIC_BASE_URL)); 
        $local_file_path = ROOT_DIR . '/public/' . $relative_path;
        
        // ตรวจสอบว่าเป็น placeholder image หรือ URL ที่ไม่ควรลบ
        $is_placeholder_or_default = (strpos($image_url, $placeholder_image_name) !== false || $image_url === $default_image_from_db);

        if (!$is_placeholder_or_default && file_exists($local_file_path)) {
            unlink($local_file_path);
        }
    }
}

$conn->close();
echo json_encode($response);
?>
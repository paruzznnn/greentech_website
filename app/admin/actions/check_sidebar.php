<?php
header('Content-Type: application/json');
require_once(__DIR__ . '/../../../lib/connect.php');
require_once(__DIR__ . '/../../../lib/base_directory.php');
require_once(__DIR__ . '/../../../lib/permissions.php');

// $isProtocol = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
// $isFile = ($isProtocol === 'http') ? '.php' : '';

global $base_path;
global $base_path_admin;

$arrPermiss = checkPermissions($_SESSION);
// เก็บสิทธิที่เกี่ยวข้องในรูปแบบ array
$allowedMenus = (isset($arrPermiss) && is_array($arrPermiss) && isset($arrPermiss['menus_id'])) 
    ? explode(',', $arrPermiss['menus_id']) 
    : [];  // กำหนดให้เป็น array เปล่าแทนการใช้ string

// Query เพื่อดึงข้อมูลเมนูทั้งหมด
$sql = "SELECT ml_menus.* FROM ml_menus WHERE ml_menus.del = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(["status" => "error", "message" => "Database error: Unable to prepare statement"]);
    exit();
}

$del = 0;
$stmt->bind_param("i", $del);
$stmt->execute();
$result = $stmt->get_result();
$arrayMenu = $result->fetch_all(MYSQLI_ASSOC);

// สร้างเมนู sidebar
$sidebarItems = [];
foreach ($arrayMenu as $row) {
    // ตรวจสอบสิทธิ: ถ้าเมนูนี้อยู่ใน $allowedMenus ถึงจะเพิ่ม
    if (in_array($row['menu_id'], $allowedMenus)) {
        if ($row['parent_id'] == 0) { // เมนูหลัก
            $sidebarItems[] = [
                'id' => $row['menu_id'],
                'icon' => $row['menu_icon'],
                'label' => $row['menu_label'],
                'link' => ($row['menu_link']) ? $base_path_admin . $row['menu_link'] : '#',
                'order' => $row['menu_order'],
                'subItems' => [],
            ];
        } else { // เมนูย่อย
            foreach ($sidebarItems as &$parentItem) {
                if ($parentItem['id'] == $row['parent_id']) {
                    $parentItem['subItems'][] = [
                        'id' => $row['menu_id'],
                        'icon' => $row['menu_icon'],
                        'label' => $row['menu_label'],
                        'link' => ($row['menu_link']) ? $base_path_admin . $row['menu_link'] : '#',
                        'order' => $row['menu_order'],
                        'parentId' => $row['parent_id'],
                    ];
                    break;
                }
            }
            unset($parentItem); // Clear reference
        }
    }
    
}

echo json_encode([
    'sidebarItems' => $sidebarItems
]);

// $permissions = explode(',', $arrPermiss['permissions']);
// $permissions_id = explode(',', $arrPermiss['permiss_id']);
// $permissionsMap = array_combine($permissions, $permissions_id);

// ส่งผลลัพธ์เป็น JSON
// echo json_encode([
//     'sidebarItems' => $sidebarItems,
//     'permissions' => $permissionsMap,
// ]);
?>

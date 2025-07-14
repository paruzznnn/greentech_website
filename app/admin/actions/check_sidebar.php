<?php
// เปิดการแสดง error
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
require_once(__DIR__ . '/../../../lib/connect.php');
require_once(__DIR__ . '/../../../lib/base_directory.php');
require_once(__DIR__ . '/../../../lib/permissions.php');

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') 
    || $_SERVER['SERVER_PORT'] == 443;

$isProtocol = $isHttps ? 'https' : 'http';

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

$sidebarItems = [];
foreach ($arrayMenu as $row) {
    
    if (in_array($row['menu_id'], $allowedMenus)) {
        if ($row['parent_id'] == 0) { //main menu
            $sidebarItems[] = [
                'id' => $row['menu_id'],
                'icon' => $row['menu_icon'],
                'label' => $row['menu_label'],
                'link' => ($row['menu_link']) ? $base_path_admin . str_replace('.php', '', $row['menu_link']) . $isFile : '#',
                'order' => $row['menu_order'],
                'subItems' => [],
            ];
        } else { // sub menu
            foreach ($sidebarItems as &$parentItem) {
                if ($parentItem['id'] == $row['parent_id']) {
                    $parentItem['subItems'][] = [
                        'id' => $row['menu_id'],
                        'icon' => $row['menu_icon'],
                        'label' => $row['menu_label'],
                        'link' => ($row['menu_link']) ? $base_path_admin . str_replace('.php', '', $row['menu_link']) . $isFile : '#',
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

// echo json_encode([
//     'sidebarItems' => $sidebarItems,
//     'permissions' => $permissionsMap,
// ]);
?>

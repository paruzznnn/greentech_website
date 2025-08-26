<?php

header('Content-Type: application/json');

require_once(__DIR__ . '/../../../lib/connect.php');
require_once(__DIR__ . '/../../../lib/base_directory.php');
require_once(__DIR__ . '/../../../lib/permissions.php');

global $base_path;
global $base_path_admin;
global $isFile; // ดึงตัวแปร isFile ที่ประกาศจาก base_directory.php

$arrPermiss = checkPermissions($_SESSION);
$allowedMenus = (isset($arrPermiss) && is_array($arrPermiss) && isset($arrPermiss['menus_id'])) 
    ? explode(',', $arrPermiss['menus_id']) 
    : [];

$sql_parent = "SELECT ml_menus.* FROM ml_menus WHERE ml_menus.del = ? AND parent_id = 0";
$stmt_parent = $conn->prepare($sql_parent);
if ($stmt_parent === false) {
    echo json_encode(["status" => "error", "message" => "Database error: Unable to prepare statement"]);
    exit();
}

$del = 0;
$stmt_parent->bind_param("i", $del);
$stmt_parent->execute();
$result = $stmt_parent->get_result();
$arrayMainMenu = $result->fetch_all(MYSQLI_ASSOC);
foreach ($arrayMainMenu as $row) {
    if (in_array($row['menu_id'], $allowedMenus)) {
        if ($row['parent_id'] == 0) {
            $sidebarItems[] = [
                'id' => $row['menu_id'],
                'icon' => $row['menu_icon'],
                'label' => $row['menu_label'],
                'link' => ($row['menu_link']) ? $base_path_admin . str_replace('.php', '', $row['menu_link']) . $isFile : '#',
                'order' => $row['menu_order'],
                'subItems' => [],
            ];
        }
    }
}

// Childen menus
$sql_childen = "SELECT ml_menus.* FROM ml_menus WHERE ml_menus.del = ? AND parent_id > 0";
$stmt_childen = $conn->prepare($sql_childen);
if ($stmt_childen === false) {
    echo json_encode(["status" => "error", "message" => "Database error: Unable to prepare statement"]);
    exit();
}

$stmt_childen->bind_param("i", $del);
$stmt_childen->execute();
$result = $stmt_childen->get_result();
$arraySubMenu = $result->fetch_all(MYSQLI_ASSOC);
foreach ($arraySubMenu as $childen) {
    foreach ($sidebarItems as &$parentItem) {
        if ($parentItem['id'] == $childen['parent_id']) {
            $parentItem['subItems'][] = [
                'id' => $childen['menu_id'],
                'icon' => $childen['menu_icon'],
                'label' => $childen['menu_label'],
                'link' => ($childen['menu_link']) ? $base_path_admin . str_replace('.php', '', $childen['menu_link']) . $isFile : '#',
                'order' => $childen['menu_order'],
                'parentId' => $childen['parent_id'],
            ];
            break;
        }
    }
    // unset($parentItem);
}

echo json_encode([
    'sidebarItems' => $sidebarItems
]);
?>

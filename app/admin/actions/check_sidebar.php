<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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
        if ($row['parent_id'] == 0) {
            $sidebarItems[] = [
                'id' => $row['menu_id'],
                'icon' => $row['menu_icon'],
                'label' => $row['menu_label'],
                'link' => ($row['menu_link']) ? $base_path_admin . str_replace('.php', '', $row['menu_link']) . $isFile : '#',
                'order' => $row['menu_order'],
                'subItems' => [],
            ];
        } else {
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
            unset($parentItem);
        }
    }
}

echo json_encode([
    'sidebarItems' => $sidebarItems
]);
?>

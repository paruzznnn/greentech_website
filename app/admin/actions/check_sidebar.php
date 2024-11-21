<?php
header('Content-Type: application/json');
require_once(__DIR__ . '/../../../lib/connect.php');
require_once(__DIR__ . '/../../../lib/base_directory.php');

// $isProtocol = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
// $isFile = ($isProtocol === 'http') ? '.php' : '';

global $base_path;
global $base_path_admin;

$sql = "SELECT admt_menus.* 
        FROM admt_menus 
        WHERE admt_menus.del = ?";

// Prepare statement
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode([
        "status" => "error",
        "message" => "Database error: Unable to prepare statement"
    ]);
    exit();
}

$del = 0;

// Bind parameters and execute
$stmt->bind_param("i", $del);
$stmt->execute();
$result = $stmt->get_result();

// Fetch all rows as an associative array
$arrayMenu = $result->fetch_all(MYSQLI_ASSOC);

$sidebarItems = [];

foreach ($arrayMenu as $row) {
    if ($row['parent_id'] == 0) { // Main menu items
        $sidebarItems[] = [
            'id' => $row['menu_id'],
            'icon' => $row['menu_icon'],
            'label' => $row['menu_label'],
            'link' => ($row['menu_link']) ? $base_path_admin . $row['menu_link'] : '#',
            'level' => $row['menu_level'],
            'order' => $row['menu_order'],
            'subItems' => [],
        ];
    } else { // Submenu items
        foreach ($sidebarItems as &$parentItem) {
            if ($parentItem['id'] == $row['parent_id']) {
                $parentItem['subItems'][] = [
                    'id' => $row['menu_id'],
                    'icon' => $row['menu_icon'],
                    'label' => $row['menu_label'],
                    'link' => ($row['menu_link']) ? $base_path_admin . $row['menu_link'] : '#',
                    'level' => $row['menu_level'],
                    'order' => $row['menu_order'],
                    'parentId' => $row['parent_id'],
                ];
                break;
            }
        }
        unset($parentItem); // Break reference
    }
}

echo json_encode($sidebarItems);
?>

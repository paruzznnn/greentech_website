<?php
session_start();
header('Content-Type: application/json');
require_once '../../../lib/connect.php';
require_once '../../../lib/base_directory.php';

global $base_path_admin;
global $base_path;


$sql = "SELECT * FROM ecm_menu";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$sidebarItems = [];

while ($row = $result->fetch_assoc()) {

    if ($row['parent_id'] === 0) {
        $menuItem = [
            'id' => $row['menu_id'],
            'icon' => $row['menu_icon'],
            'label' => $row['menu_label'],
            'link' => ($row['menu_link']) ? $base_path_admin . $row['menu_link'] : '',
            'order' => $row['menu_order'],
            'subItems' => [], 
        ];

        $sidebarItems[] = $menuItem;

    } else {

        foreach ($sidebarItems as &$parentItem) {

            if ($parentItem['id'] == $row['parent_id']) {
                $parentItem['subItems'][] = [
                    'id' => $row['parent_id'].$row['menu_order'],
                    'icon' => $row['menu_icon'],
                    'label' => $row['menu_label'],
                    'link' => $base_path_admin . $row['menu_link'],
                    'order' => $row['menu_order'],
                    'parentId' => $row['parent_id'],
                ];
                break;
            }

        }

    }
}

echo json_encode($sidebarItems);
?>

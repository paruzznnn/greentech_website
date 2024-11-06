<?php
session_start();
header('Content-Type: application/json');
require_once(__DIR__ . '/../../../lib/base_directory.php');
global $base_path_admin;
global $base_path;
global $public_path;


// Demo data as an array instead of SQL query
$result = [
    [
        'menu_id' => 1, 
        'parent_id' => 0, 
        'menu_icon' => '<i class="fas fa-tachometer-alt"></i>', 
        'menu_label' => 'Dashboard', 
        'menu_link' => 'index.php', 
        'menu_order' => 1
    ],
    [
        'menu_id' => 2, 
        'parent_id' => 0, 
        'menu_icon' => '<i class="fas fa-user-cog"></i>', 
        'menu_label' => 'Setting ', 
        'menu_link' => '', 
        'menu_order' => 2
    ],
    [
        'menu_id' => 3, 
        'parent_id' => 2, 
        'menu_icon' => '<i class="fas fa-th-large"></i>', 
        'menu_label' => 'Set HTML', 
        'menu_link' => 'set_template/set_download.php', 
        'menu_order' => 1
    ],
    [
        'menu_id' => 4, 
        'parent_id' => 2, 
        'menu_icon' => '<i class="far fa-circle"></i>', 
        'menu_label' => 'Set Navbar', 
        'menu_link' => 'set_template/set_navbar.php', 
        'menu_order' => 3
    ],
];

$sidebarItems = [];

foreach ($result as $row) {

    if ($row['parent_id'] === 0) {
        $menuItem = [
            'id' => $row['menu_id'],
            'icon' => $row['menu_icon'],
            'label' => $row['menu_label'],
            'link' => ($row['menu_link']) ? $base_path_admin . $row['menu_link'] : '#',
            'order' => $row['menu_order'],
            'subItems' => [], 
        ];

        $sidebarItems[] = $menuItem;

    } else {

        foreach ($sidebarItems as &$parentItem) {

            if ($parentItem['id'] == $row['parent_id']) {
                $parentItem['subItems'][] = [
                    'id' => $row['parent_id'] . $row['menu_order'],
                    'icon' => $row['menu_icon'],
                    'label' => $row['menu_label'],
                    'link' => ($row['menu_link']) ? $base_path_admin . $row['menu_link'] : '#',
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

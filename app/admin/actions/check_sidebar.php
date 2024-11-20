<?php
header('Content-Type: application/json');
require_once(__DIR__ . '/../../../lib/permissions.php');
require_once(__DIR__ . '/../../../lib/base_directory.php');
// checkPermissions();

$isProtocol = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
$isFile = ($isProtocol === 'http') ? '.php' : '';

global $base_path;
global $base_path_admin;


$result = [
    [
        'menu_id' => 1, 
        'parent_id' => 0, 
        'menu_icon' => '<i class="fas fa-tachometer-alt"></i>', 
        'menu_label' => 'Dashboard', 
        'menu_link' => 'dashboard'.$isFile, 
        'menu_level' => '',
        'menu_order' => 1
    ],
    [
        'menu_id' => 4, 
        'parent_id' => 0, 
        'menu_icon' => '<i class="fas fa-newspaper"></i>', 
        'menu_label' => 'News ', 
        'menu_link' => '', 
        'menu_level' => '',
        'menu_order' => 1
    ],
    [
        'menu_id' => 5, 
        'parent_id' => 4, 
        'menu_icon' => '<i class="fas fa-pen-alt"></i>', 
        'menu_label' => 'write news', 
        'menu_link' => 'set_news/setup_news'.$isFile, 
        'menu_level' => '',
        'menu_order' => 2
    ],
    [
        'menu_id' => 6, 
        'parent_id' => 4, 
        'menu_icon' => '<i class="fas fa-table"></i>', 
        'menu_label' => 'list news', 
        'menu_link' => 'set_news/list_news'.$isFile, 
        'menu_level' => '',
        'menu_order' => 2
    ],
    [
        'menu_id' => 7, 
        'parent_id' => 0, 
        'menu_icon' => '<i class="fas fa-user-cog"></i>', 
        'menu_label' => 'Admin Tools', 
        'menu_link' => '', 
        'menu_level' => 1,
        'menu_order' => 1
    ],
    [
        'menu_id' => 9, 
        'parent_id' => 7, 
        'menu_icon' => '<i class="fas fa-circle-notch"></i>', 
        'menu_label' => 'menu', 
        'menu_link' => 'set_menu/setup_menu'.$isFile, 
        'menu_level' => 1,
        'menu_order' => 3
    ],
    [
        'menu_id' => 10, 
        'parent_id' => 7, 
        'menu_icon' => '<i class="fas fa-circle-notch"></i>', 
        'menu_label' => 'role', 
        'menu_link' => '', 
        'menu_level' => 1,
        'menu_order' => 4
    ],
    [
        'menu_id' => 11, 
        'parent_id' => 7, 
        'menu_icon' => '<i class="fas fa-circle-notch"></i>', 
        'menu_label' => 'company', 
        'menu_link' => '', 
        'menu_level' => 1,
        'menu_order' => 5
    ],
    [
        'menu_id' => 12, 
        'parent_id' => 0, 
        'menu_icon' => '<i class="fas fa-cogs"></i>', 
        'menu_label' => 'Template', 
        'menu_link' => '', 
        'menu_level' => 1,
        'menu_order' => 1
    ],
    [
        'menu_id' => 13, 
        'parent_id' => 12, 
        'menu_icon' => '<i class="fas fa-window-restore"></i>', 
        'menu_label' => 'web Allable', 
        'menu_link' => 'set_template/set_allable'.$isFile, 
        'menu_level' => 1,
        'menu_order' => 2
    ],
    [
        'menu_id' => 14, 
        'parent_id' => 12, 
        'menu_icon' => '<i class="fas fa-th-large"></i>', 
        'menu_label' => 'build layout', 
        'menu_link' => 'set_template/set_layout'.$isFile, 
        'menu_level' => 1,
        'menu_order' => 2
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
            'level' => $row['menu_level'],
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
                    'level' => $row['menu_level'],
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

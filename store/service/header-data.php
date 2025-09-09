<?php
require_once '../server/connect_sqli.php';
require_once '../server/select_sqli.php';
require_once '../server/insert_sqli.php';
header('Content-Type: application/json');

/* ---------------POST NEED USE ------------------
$input = file_get_contents("php://input");
$data = json_decode($input, true);
if ($data == null) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid JSON"]);
    exit;
}
-------------------------------------------------*/

/*------- Authorization AND TIME ZONE ---------- */
if (isset($_SESSION['user_timezone'])) {
    date_default_timezone_set($_SESSION['user_timezone']);
} else {
    date_default_timezone_set("UTC");
}
$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}
$authHeader = $headers['Authorization'];
$token = str_replace('Bearer ', '', $authHeader);
$validToken = "my_secure_token_123";
if ($token !== $validToken) {
    http_response_code(403);
    echo json_encode(["error" => "Invalid token"]);
    exit;
}
/*------------------------------------------*/

/*---------ACTION DATA -------------*/

$action = $_GET['action'];
if ($action == 'getMenuHeaderItems') {

    try {
        $conditions = [
            [
                'column'   => 'del',
                'operator' => '=',
                'value'    => 0
            ]
        ];

        $items = selectData(
            $conn_cloudpanel,
            'ecm_link',
            $conditions,
            '*',
            'link_id ASC'
        );

        if ($items === false || $items === null) {
            throw new Exception("ไม่สามารถดึงข้อมูลจาก ecm_link ได้");
        }

        $data = [];
        foreach ($items as $item) {
            $data[] = [
                'id'        => $item['link_id'],
                'icon'      => $item['link_icon'],
                'label'     => $item['link_name'],
                'path'      => $BASE_WEB . $item['link_url'],
                'hasToggle' => true
            ];
        }

        http_response_code(200);
        $response = [
            "data" => $data
        ];
        echo json_encode($response);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            "status" => false,
            "error"  => $e->getMessage()
        ]);
    } finally {
        if (isset($conn_cloudpanel) && $conn_cloudpanel) {
            $conn_cloudpanel->close();
        }
        exit;
    }
} else if ($action == 'getMenuHeaderBox') {

    try {
        $conditions = [
            [
                'column'   => 'del',
                'operator' => '=',
                'value'    => 0
            ]
        ];

        $images = selectData($conn_cloudpanel, 'ecm_link_sub_img', $conditions, '*', 'link_id ASC');
        if ($images === false) {
            throw new Exception("ไม่สามารถดึงข้อมูลจาก ecm_link_sub_img ได้");
        }

        $menus  = selectData($conn_cloudpanel, 'ecm_link_sub_menu', $conditions, '*', 'link_id ASC');
        if ($menus === false) {
            throw new Exception("ไม่สามารถดึงข้อมูลจาก ecm_link_sub_menu ได้");
        }

        $texts  = selectData($conn_cloudpanel, 'ecm_link_sub_text', $conditions, '*', 'link_id ASC');
        if ($texts === false) {
            throw new Exception("ไม่สามารถดึงข้อมูลจาก ecm_link_sub_text ได้");
        }

        // รวมข้อมูลทั้งหมด โดยใช้ link_id เป็น key หลัก
        $data = [];

        // -------------------- IMAGES --------------------
        foreach ($images as $img) {
            $id = $img['link_id'];
            $sectionKey = "section" . $img['link_sub_sort'];

            if (!isset($data[$id])) {
                $data[$id] = ['id' => $id];
            }

            if (!isset($data[$id][$sectionKey])) {
                $data[$id][$sectionKey] = [
                    'type' => 'image',
                    'content' => []
                ];
            }

            $data[$id][$sectionKey]['content'][] = [
                'src' => $img['link_sub_img'],
                'alt' => $img['link_sub_category']
            ];
        }

        // -------------------- MENUS --------------------
        foreach ($menus as $menu) {
            $id = $menu['link_id'];
            $sectionKey = "section" . $menu['link_sub_sort'];

            if (!isset($data[$id])) {
                $data[$id] = ['id' => $id];
            }

            if (!isset($data[$id][$sectionKey])) {
                $data[$id][$sectionKey] = [
                    'type' => 'menu',
                    'content' => []
                ];
            }

            $data[$id][$sectionKey]['content'][] = [
                'label' => $menu['link_sub_name'],
                'href'  => $menu['link_sub_url']
            ];
        }

        // -------------------- TEXT --------------------
        foreach ($texts as $txt) {
            $id = $txt['link_id'];
            $sectionKey = "section" . $txt['link_sub_sort'];

            if (!isset($data[$id])) {
                $data[$id] = ['id' => $id];
            }

            $data[$id][$sectionKey] = [
                'type'    => 'text',
                'content' => $txt['link_sub_text']
            ];
        }

        $data = array_values($data);

        http_response_code(200);
        echo json_encode([
            "status" => true,
            "data"   => $data
        ]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            "status" => false,
            "error"  => $e->getMessage()
        ]);
    } finally {
        if (isset($conn_cloudpanel) && $conn_cloudpanel) {
            $conn_cloudpanel->close();
        }
        exit;
    }
} else if ($action == 'getMenuHeaderSideItems') {

    $data = [
        [
            'section' => 'คอนเทนต์หลัก',
            'items' => [
                [
                    'icon' => '<i class="bi bi-house-door"></i>',
                    'label' => 'หน้าแรก',
                    'href' => $BASE_WEB
                ],
                [
                    'icon' => '<i class="bi bi-tag"></i>',
                    'label' => 'สินค้าอะคูสติก',
                    'href' => $BASE_WEB . 'search/product/'
                ],
                [
                    'icon' => '',
                    'label' => 'สถาปนิก ช่าง ผู้รับเหมา',
                    'href' => $BASE_WEB . 'search/product/'
                ]
            ]
        ],
        [
            'section' => 'ความช่วยเหลือ',
            'items' => [
                [
                    'icon' => '<i class="bi bi-headset"></i>',
                    'label' => 'แจ้งปัญหาการใช้งาน',
                    'href' => '#'
                ]
            ]
        ]
    ];

    http_response_code(200);
    $response = [
        "data" => $data
    ];

    echo json_encode($response);
    exit;
} else {

    http_response_code(400);
    echo json_encode([
        "error" => "Unauthorized"
    ]);
    exit;
}


/*----------------------------------*/

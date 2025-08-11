<?php
require_once '../server/connect_sqli.php';
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
if($action == 'getMenuHeaderItems'){

    $data = [
        [
            'icon' => '',
            'label' => 'สินค้าแทรนดาร์ อะคูสติก',
            'id' => 'box1',
            'path' => '/newstore/product/',
            'hasToggle' => true
        ]
    ];

    http_response_code(200);
    $response = [
        "data" => $data
    ];

    echo json_encode($response);
    exit;

}
else if($action == 'getMenuHeaderBox') {

    $data = [
        [
            'id' => 'box1',
            'section1' => [
                'type' => 'image',
                'content' => [
                    ['src' => 'https://www.trandar.com//public/shop_img/687e056ead593_Afibus-03.png', 'alt' => 'img1'],
                    ['src' => 'https://www.trandar.com//public/shop_img/687a3420a32fa_Zound_Borad_223.png', 'alt' => 'img2'],
                    ['src' => 'https://www.trandar.com//public/shop_img/687e056ead593_Afibus-03.png', 'alt' => 'img3']
                ]
            ],
            'section2' => [
                'type' => 'menu',
                'content' => [
                    ['icon' => '', 'label' => 'เมนูย่อย 1', 'href' => '#'],
                    ['icon' => '', 'label' => 'เมนูย่อย 2', 'href' => '#'],
                    ['icon' => '', 'label' => '', 'href' => '#']
                ]
            ],
            'section3' => [
                'type' => 'text',
                'content' => 'ข้อมูลรายละเอียดเพิ่มเติมของอะคูสติกออนไลน์'
            ],
            'section4' => null
        ]
    ];

    http_response_code(200);
    $response = [
        "data" => $data
    ];

    echo json_encode($response);
    exit;

}
else if($action == 'getMenuHeaderSideItems'){

    $data = [
        [
            'section' => 'คอนเทนต์หลัก',
            'items' => [
                [
                    'icon' => '<i class="bi bi-house-door"></i>',
                    'label' => 'หน้าแรก',
                    'href' => '/newstore/'
                ],
                [
                    'icon' => '<i class="bi bi-tag"></i>',
                    'label' => 'สินค้าอะคูสติก',
                    'href' => '/newstore/search/product/'
                ],
                [
                    'icon' => '',
                    'label' => 'สถาปนิก ช่าง ผู้รับเหมา',
                    'href' => '/newstore/search/product/'
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

}
else{

    http_response_code(400);
    echo json_encode([
        "error" => "Unauthorized"
    ]);
    exit;
}


/*----------------------------------*/



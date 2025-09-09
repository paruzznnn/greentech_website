<?php
require_once '../../server/connect_sqli.php';
header('Content-Type: application/json');


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

if ($action == 'getPartnerTabItems') {

    $data = [
        [
            "id" => "tab1",
            "label" => "หน้าหลัก",
            "content" => '
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div id="tab1-about"></div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div id="tab1-review"></div>
                </div>
            </div>
            <div id="tab1-article">
            </div>
            '
        ],
        [
            "id" => "tab2",
            "label" => "บทความ",
            "content" => '<div id="tab2-article"></div>'
        ]
    ];

    http_response_code(200);
    $response = [
        "data" => $data
    ];

    echo json_encode($response);
    exit;
} else if ($action == 'getPartnerMenuItems') {

    $data = [
        [
            "id" => "tab1",
            "content" => '<div id="tab1-contact"></div>'
        ],
        [
            "id" => "tab2",
            "content" => '<div id="tab2-article-menu">
                <ul id="articleMenu" class="article-box-menu"></ul>
            </div>'
        ]
    ];

    http_response_code(200);
    $response = [
        "data" => $data
    ];

    echo json_encode($response);
    exit;
} else if ($action == 'getPartnerMenuArticleItems') {

    $data = [
        [
            'label' => 'หมวดบทความ',
            'href' => '#',
            'icon' => ''
        ],
        [
            'label' => 'บทความทั้งหมด',
            'href' => '#',
            'icon' => '',
            'children' => [
                [
                    'label' => 'ความรู้ด้านเสียง',
                    'href' => '#',
                    'icon' => ''
                ],
                [
                    'label' => 'วีดีโอ',
                    'href' => '#',
                    'icon' => ''
                ]
            ]
        ],
        // [
        //     'label' => 'วัสดุกันเสียง',
        //     'href' => '#',
        //     'icon' => '',
        //     'children' => []
        // ]
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

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
if ($action == 'getCategoryItems') {

    $data = [
        [
            "label" => "หมวดสินค้า",
            "href" => "#",
            "icon" => ""
        ],
        [
            "label" => "วัสดุดูดซับเสียง",
            "href" => "#",
            "icon" => "",
            "children" => [
                [
                    "label" => "Trandar Acoustics Mineral Fiber",
                    "href" => "#",
                    "icon" => ""
                ],
                [
                    "label" => "Trandar Acoustics Soft Fiber",
                    "href" => "#",
                    "icon" => ""
                ],
                [
                    "label" => "Trandar ZIVANA",
                    "href" => "#",
                    "icon" => ""
                ],
                [
                    "label" => "Trandar Seamless Acoustics",
                    "href" => "#",
                    "icon" => ""
                ],
                [
                    "label" => "Trandar Solo",
                    "href" => "#",
                    "icon" => ""
                ],
                [
                    "label" => "Trandar AFIBUS",
                    "href" => "#",
                    "icon" => ""
                ]
            ]
        ],
        [
            "label" => "วัสดุกันเสียง",
            "href" => "#",
            "icon" => "",
            "children" => [
                [
                    "label" => "ตัวอย่าง A",
                    "href" => "#",
                    "icon" => ""
                ],
                [
                    "label" => "ตัวอย่าง B",
                    "href" => "#",
                    "icon" => ""
                ]
            ]
        ],
        [
            "label" => "โครงคร่าวฝ้า",
            "href" => "#",
            "icon" => "",
            "children" => [
                [
                    "label" => "อื่นๆ 1",
                    "href" => "#",
                    "icon" => ""
                ],
                [
                    "label" => "อื่นๆ 2",
                    "href" => "#",
                    "icon" => ""
                ]
            ]
        ],
        [
            "label" => "Trandar Solution",
            "href" => "#",
            "icon" => "",
            "children" => [
                [
                    "label" => "อื่นๆ 1",
                    "href" => "#",
                    "icon" => ""
                ],
                [
                    "label" => "อื่นๆ 2",
                    "href" => "#",
                    "icon" => ""
                ]
            ]
        ],
        [
            "label" => "อื่นๆ",
            "href" => "#",
            "icon" => "",
            "children" => [
                [
                    "label" => "อื่นๆ 1",
                    "href" => "#",
                    "icon" => ""
                ],
                [
                    "label" => "อื่นๆ 2",
                    "href" => "#",
                    "icon" => ""
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

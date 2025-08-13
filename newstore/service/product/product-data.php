<?php
require_once '../../server/connect_sqli.php';
require_once '../../server/select_sqli.php';
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
if ($action == 'getProductItems') {

    $conditions = [
        [
            'column' => 'comp_id', 
            'operator' => '=', 
            'value' => '2'
        ]
    ];

    $items = selectData(
        $conn_cloudpanel, 
        'ecm_product', 
        $conditions, 
        'id, pic_icon, category_name, category_id', 
        'id DESC'
    );

    $data = [];
    $seen_category_ids = [];
    foreach ($items as $item) {
        if (in_array($item['category_id'], $seen_category_ids)) {
            continue; // check duplicate
        }
        $data[] = [
            'id' => $item['id'],
            'image' => $item['pic_icon'],
            'category' => $item['category_name'],
            'category_id' => $item['category_id']
        ];
        //keep category_id 
        $seen_category_ids[] = $item['category_id'];
    }

    $response = [
        "data" => $data
    ];

    http_response_code(200);
    echo json_encode($response);
    $conn_cloudpanel->close();
    exit;
} else if ($action == 'getProductListItems') {

    $conditions = [
        [
            'column' => 'comp_id', 
            'operator' => '=', 
            'value' => '2'
        ]
    ];

    $items = selectData(
        $conn_cloudpanel, 
        'ecm_product', 
        $conditions, 
        '*', 
        'id DESC'
    );

    $data = [];
    foreach ($items as $item) {
        $cost = explode(",",$item['cost']);
        $data[] = [
            'id' => $item['id'],
            'title' => $item['code'],
            "description" => $item['description'],
            'image' => $item['pic_icon'],
            'category' => $item['category_name'],
            'category_id' => $item['category_id'],
            'price' => $cost[0],
            'color' => "",
            'size' => "",
            'material' => "",
        ];
    }

    $response = [
        "data" => $data
    ];

    http_response_code(200);
    echo json_encode($response);
    $conn_cloudpanel->close();
    exit;
} else if ($action == 'getProductDetailItems') {

    $conditions = [
        [
            'column' => 'comp_id', 
            'operator' => '=', 
            'value' => '2'
        ]
    ];

    $items = selectData(
        $conn_cloudpanel, 
        'ecm_product', 
        $conditions, 
        '*', 
        'id DESC'
    );

    $data = [];
    foreach ($items as $item) {
        $cost = explode(",",$item['cost']);
        $data[] = [
            // 'id' => $item['id'],
            'icon' => "",
            'title' => $item['code'],
            'category' => $item['category_name'],
            'description' => $item['description'],
            'features' => [
                "มีค่าการกันเสียง 33 dB (STC 33) มีความหนาเพียง 10 มม. หากติดตั้งด้วยระบบ",
                "TSIS จะมีค่าการกันเสียงถึง 56 dB",
                "ลดขนาดระบบผนัง ช่วยเพิ่มพื้นที่ห้องให้มากขึ้น",
            ],
            'images' => [
                "https://www.trandar.com//public/shop_img/6878c8c678fea_photo_2025-07-17_16-55-32.jpg",
                "https://www.trandar.com//public/shop_img/687a325d8bbab_Zound_Borad_223.png",
                "https://www.trandar.com//public/shop_img/687dcff11c5df_dbPhon2.png",
            ],
            'currentPrice' => 450,
            'oldPrice' => 599,
            'discountPercent' => 25,
        ];
    }

    $response = [
        "data" => $data
    ];

    http_response_code(200);
    echo json_encode($response);
    $conn_cloudpanel->close();
    exit;

} else if($action == 'getProductSimilarItems') {

    $data = [
        [
            "title" => "แทรนดาร์ A1 Pro",
            "image" => "https://www.trandar.com//public/shop_img/687e056ead593_Afibus-03.png"
        ],
        [
            "title" => "แทรนดาร์ B2 Compact",
            "image" => "https://www.trandar.com//public/shop_img/687f124b62eb7_Ecophon_Akusto____Wall_C.jpg"
        ],
        [
            "title" => "แทรนดาร์ C3 Ultra",
            "image" => "https://www.trandar.com//public/shop_img/687dc43992e8a_DSC04303.png"
        ],
        [
            "title" => "แทรนดาร์ X5 Max",
            "image" => "https://www.trandar.com//public/shop_img/687a3420a32fa_Zound_Borad_223.png"
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

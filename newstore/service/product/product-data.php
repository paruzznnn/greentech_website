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
if ($action == 'getProductItems') {

    $data = [
        [
            "id" => 1,
            "image" => "https://www.trandar.com//public/shop_img/687dde881194f_Sound_Insulation.png",
            "category" => "ฝ้าเพดานอะคูสติก",
            "category_id" => 1
        ],
        [
            "id" => 2,
            "image" => "https://www.trandar.com//public/shop_img/687de7a795a01_hitech_roof_option1_PNG.png",
            "category" => "ฉนวนกันเสียง",
            "category_id" => 3
        ],
        [
            "id" => 3,
            "image" => "https://www.trandar.com//public/shop_img/687b2f5b393b2_497eeb6fc69f5635590f41fc078dff98.jpg",
            "category" => "ฝ้าทีบาร์",
            "category_id" => 3
        ],
        [
            "id" => 4,
            "image" => "https://www.trandar.com//public/shop_img/687a21f2467de_Ecophon_Focus_F.jpg",
            "category" => "ผนังเบา",
            "category_id" => 1
        ],
        [
            "id" => 5,
            "image" => "https://www.trandar.com//public/shop_img/687dde881193e_render_HITECWALL_Png_2366.png",
            "category" => "ฝ้าดรอป",
            "category_id" => 1
        ],
        [
            "id" => 6,
            "image" => "https://www.trandar.com//public/shop_img/687dfa2242238_System_Cline___sound_board__wall_angel.jpg",
            "category" => "โครงคร่าว",
            "category_id" => 2
        ],
        [
            "id" => 7,
            "image" => "https://www.trandar.com//public/shop_img/687e1de85a67c_trandar_Paint_new_label.jpg",
            "category" => "อุปกรณ์ติดตั้ง",
            "category_id" => 2
        ]
    ];

    http_response_code(200);
    $response = [
        "data" => $data
    ];

    echo json_encode($response);
    exit;
} else if ($action == 'getProductListItems') {

    $data = [
        [
            "id" => 1,
            "title" => "Smartphone",
            "description" => "Android Phone",
            "image" => "https://www.trandar.com//public/shop_img/687dde881194f_Sound_Insulation.png",
            "category" => "electronics",
            "category_id" => 1,
            "price" => 4500,
            "color" => "Red",
            "size" => "M",
            "material" => "Plastic"
        ],
        [
            "id" => 2,
            "title" => "Tablet",
            "description" => "10-inch screen",
            "image" => "https://www.trandar.com//public/shop_img/687de7a795a01_hitech_roof_option1_PNG.png",
            "category" => "electronics",
            "category_id" => 1,
            "price" => 7000,
            "color" => "Blue",
            "size" => "L",
            "material" => "Plastic"
        ],
        [
            "id" => 3,
            "title" => "Book",
            "description" => "Tech Book",
            "image" => "https://www.trandar.com//public/shop_img/687b2f5b393b2_497eeb6fc69f5635590f41fc078dff98.jpg",
            "category" => "books",
            "category_id" => 3,
            "price" => 1200,
            "color" => "Green",
            "size" => "S",
            "material" => "Cotton"
        ],
        [
            "id" => 4,
            "title" => "T-shirt",
            "description" => "Cool Fashion",
            "image" => "https://www.trandar.com//public/shop_img/687a21f2467de_Ecophon_Focus_F.jpg",
            "category" => "fashion",
            "category_id" => 3,
            "price" => 800,
            "color" => "Red",
            "size" => "M",
            "material" => "Cotton"
        ],
        [
            "id" => 5,
            "title" => "Laptop",
            "description" => "High Performance",
            "image" => "https://www.trandar.com//public/shop_img/687dde881193e_render_HITECWALL_Png_2366.png",
            "category" => "electronics",
            "category_id" => 2,
            "color" => "Blue",
            "size" => "L",
            "material" => "Plastic"
        ],
        [
            "id" => 6,
            "title" => "Shoes",
            "description" => "Running shoes",
            "image" => "https://www.trandar.com//public/shop_img/687dfa2242238_System_Cline___sound_board__wall_angel.jpg",
            "category" => "fashion",
            "category_id" => 2,
            "price" => 3200,
            "color" => "Green",
            "size" => "S",
            "material" => "Leather"
        ],
        [
            "id" => 7,
            "title" => "TV",
            "description" => "Smart TV 4K",
            "image" => "https://www.trandar.com//public/shop_img/687e1de85a67c_trandar_Paint_new_label.jpg",
            "category" => "electronics",
            "category_id" => 1,
            "price" => 9500,
            "color" => "Red",
            "size" => "M",
            "material" => "Plastic"
        ]
    ];

    http_response_code(200);
    $response = [
        "data" => $data
    ];

    echo json_encode($response);
    exit;
} else if ($action == 'getProductDetailItems') {

    $data = [
        "icon" => "",
        "title" => "Trandar Zoundboard 10 mm.",
        "category" => "หมวดหมู่: ฝ้าเพดานอะคูสติก",
        "description" => "แผ่นกันเสียง Trandar Zoundboard ...",
        "features" => [
            "มีค่าการกันเสียง 33 dB (STC 33) มีความหนาเพียง 10 มม. หากติดตั้งด้วยระบบ",
            "TSIS จะมีค่าการกันเสียงถึง 56 dB ...",
            "ลดขนาดระบบผนัง ช่วยเพิ่มพื้นที่ห้องให้มากขึ้น",
        ],
        "images" => [
            "https://www.trandar.com//public/shop_img/6878c8c678fea_photo_2025-07-17_16-55-32.jpg",
            "https://www.trandar.com//public/shop_img/687a325d8bbab_Zound_Borad_223.png",
            "https://www.trandar.com//public/shop_img/687dcff11c5df_dbPhon2.png"
        ],
        "currentPrice" => 450,
        "oldPrice" => 599,
        "discountPercent" => 25,
    ];

    http_response_code(200);
    $response = [
        "data" => $data
    ];

    echo json_encode($response);
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

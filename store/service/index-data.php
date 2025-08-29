<?php
require_once '../server/connect_sqli.php';
require_once '../server/select_sqli.php';
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
$dateNow = date("Y-m-d");
$action = $_GET['action'];
if ($action == 'getSectionItems') {

    $data = [
        [
            "id" => "section_popular",
            "class" => "section-space",
            "title" => "การค้นหายอดนิยม",
            "carouselId" => "carousel_popular",
            "isSlide" => true,
            "type" => "crssm",
            "sort" => 2,
            "req" => "getPopularItems"
        ],
        [
            "id" => "section_baner",
            "class" => "section-space",
            "title" => "",
            "carouselId" => "banner_store",
            "isSlide" => false,
            "type" => "bbn",
            "sort" => 1,
            "req" => "getBannersItems"
        ],
        [
            "id" => "section_product",
            "class" => "section-space",
            "title" => "แทรนดาร์ อะคูสติก",
            "carouselId" => "carousel_product",
            "isSlide" => true,
            "type" => "crsmd",
            "sort" => 5,
            "req" => "getProductItems"
        ],
        [
            "id" => "section_products",
            "class" => "section-space",
            "title" => "แทรนดาร์ อะคูสติก แนะนำ",
            "carouselId" => "carousel_products",
            "isSlide" => false,
            "type" => "cmd",
            "sort" => 4,
            "req" => "getProductItems"
        ],
        // [
        //     "id" => "section_news",
        //     "class" => "section-space",
        //     "title" => "อัปเดตข่าวสาร",
        //     "carouselId" => "carousel_news",
        //     "isSlide" => true,
        //     "type" => "crslg",
        //     "sort" => 5,
        //     "req" => "getNewsItems"
        // ],
        [
            "id" => "section_introduce",
            "class" => "section-space",
            "title" => "",
            "carouselId" => "introduce_store",
            "isSlide" => false,
            "type" => "intd",
            "sort" => 3,
            "req" => ""
        ]
    ];

    http_response_code(200);
    $response = [
        "data" => $data
    ];

    echo json_encode($response);
    exit;
} else if ($action == 'getPopularItems') {

    $data = [
        [
            "image" => "https://www.trandar.com//public/shop_img/687dde881194f_Sound_Insulation.png",
            "title" => "ฝ้าเพดานอะคูสติก"
        ],
        [
            "image" => "https://www.trandar.com//public/shop_img/687de7a795a01_hitech_roof_option1_PNG.png",
            "title" => "ฉนวนกันเสียง"
        ],
        [
            "image" => "https://www.trandar.com//public/shop_img/687b2f5b393b2_497eeb6fc69f5635590f41fc078dff98.jpg",
            "title" => "ฝ้าทีบาร์"
        ],
        [
            "image" => "https://www.trandar.com//public/shop_img/687a21f2467de_Ecophon_Focus_F.jpg",
            "title" => "ผนังเบา"
        ],
        [
            "image" => "https://www.trandar.com//public/shop_img/687dde881193e_render_HITECWALL_Png_2366.png",
            "title" => "ฝ้าดรอป"
        ],
        [
            "image" => "https://www.trandar.com//public/shop_img/687dfa2242238_System_Cline___sound_board__wall_angel.jpg",
            "title" => "โครงคร่าว"
        ],
        [
            "image" => "https://www.trandar.com//public/shop_img/687e1de85a67c_trandar_Paint_new_label.jpg",
            "title" => "อุปกรณ์ติดตั้ง"
        ]
    ];

    http_response_code(200);
    $response = [
        "data" => $data
    ];

    echo json_encode($response);
    exit;
} else if ($action == 'getBannersItems') {

    $data = [
        [
            "path" => $BASE_WEB . "product",
            "image" => "https://www.trandar.com//public/img/687f610c3a362.jpg",
            "title" => "BEST ACOUSTIC CONSULTANT",
            "description" => "Inspiration ของ แทรนดาร์ อะคูสติก
                        คือเราต้องการที่จะยกระดับมาตรฐานทางด้านการออกแบบระบบอะคูสติกให้เทียบเท่ากับต่างประเทศและระดับโลก
                        <br>Kridsada Satukijchai (CEO)",
            "buttonText" => "ดูสินค้าเพิ่มเติม"
        ]
    ];

    http_response_code(200);
    $response = [
        "data" => $data
    ];

    echo json_encode($response);
    exit;
} else if ($action == 'getProductItems') {

    $conditions = [
        [
            'column' => 'sync_status', 
            'operator' => '=', 
            'value' => '1'
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
        $currency = explode(",",$item['currency']);
        $data[] = [
            'id' => $item['id'],
            'productId' => $item['material_id'],
            'compName' => "Trandar",
            'compLogo' => "https://www.trandar.com//public/img/logo_688c431f30bf3.png",
            'compId' => $item['comp_id'],
            'image' => $item['pic_icon'],
            'categoryId' => $item['category_id'],
            'category' => $item['category_name'],
            'productName' => $item['code'],
            'productDetail' => $item['description'],
            'productPrice' => $cost[0],
            'productCurrency' => $currency[0],
            'productBadges' => [
                "ร้านแนะนำ",
                "ส่งไว",
                "บริการดี"
            ],
            'dateCreated' => formatDateLocalized($dateNow, 'th', true, $_SESSION['user_timezone']),
        ];
    }

    $memberId = 0;
    if(!empty($_SESSION['user'])){
        switch ($_SESSION['user']['role']) {
            case 'user':
                $memberId = $_SESSION['user']['id'];
                break;
            default:
                $memberId = 0;
                break;
        }
    }

    $response = [
        "member" => $memberId,
        "data" => $data
    ];

    http_response_code(200);
    echo json_encode($response);
    $conn_cloudpanel->close();
    exit;
} else if ($action == 'getNewsItems') {

    $data = [
        [
            "title" => "แทรนดาร์ A1 Pro",
            "image" => "https://www.trandar.com/public/news_img/1750150889923.jpg"
        ],
        [
            "title" => "แทรนดาร์ B2 Compact",
            "image" => "https://www.trandar.com/public/news_img/1750063726405.jpg"
        ],
        [
            "title" => "แทรนดาร์ C3 Ultra",
            "image" => "https://www.trandar.com/public/news_img/1750062950278.jpg"
        ],
        [
            "title" => "แทรนดาร์ X5 Max",
            "image" => "https://www.trandar.com/public/news_img/1750062685537.jpg"
        ]
    ];

    http_response_code(200);
    $response = [
        "data" => $data
    ];

    echo json_encode($response);
    exit;
} else if ($action == 'getIntroItems') {

    $data = [
        [
            "carousel" => [
                [
                    "image" => "https://www.trandar.com/public/news_img/S__13017091.jpg",
                    "alt" => "สินค้าแนะนำ 1",
                    "label" => "สินค้าแนะนำ 1"
                ],
                [
                    "image" => "https://www.trandar.com/public/news_img/107513023_1924908267816933_9058140769602070798_o-1-768x576.jpg",
                    "alt" => "สินค้าแนะนำ 2",
                    "label" => "สินค้าแนะนำ 2"
                ],
                [
                    "image" => "https://www.trandar.com/public/news_img/S__42197035.jpg",
                    "alt" => "สินค้าแนะนำ 3",
                    "label" => "สินค้าแนะนำ 3"
                ]
            ],
            "categories" => [
                // [
                //     "icon" => "",
                //     "title" => "ผ่อน 0%",
                //     "link" => "#"
                // ],
                // [
                //     "icon" => "<i class='bi bi-lightning-charge'></i>",
                //     "title" => "โปรโมชัน",
                //     "link" => "#"
                // ],
                // [
                //     "icon" => "",
                //     "title" => "ดีลจากพาร์ตเนอร์",
                //     "link" => "#"
                // ],
                // [
                //     "icon" => "",
                //     "title" => "วิธีแลกพอยต์ส่วนลด",
                //     "link" => "#"
                // ]
            ],
            "products" => [
                [
                    "image" => "https://www.trandar.com/public/img/687f610c3a362.jpg",
                    "title" => "BEST ACOUSTIC CONSULTANT",
                    "subtitle" => "Kridsada Satukijchai (CEO)",
                    "description" => "Inspiration ของ แทรนดาร์ อะคูสติก<br>คือเราต้องการที่จะยกระดับมาตรฐานทางด้านการออกแบบระบบอะคูสติกให้เทียบเท่ากับต่างประเทศและระดับโลก",
                    "price" => 12500,
                    "oldPrice" => 15000,
                    "discount" => "ลด 17%",
                    "buttonText" => "ดูสินค้าเพิ่มเติม"
                ],
                [
                    "image" => "https://www.trandar.com/store/public/img/banner.jpeg",
                    "title" => "Acoustic Wall Panel",
                    "subtitle" => "AcouDesign Ltd.",
                    "description" => "แผ่นอะคูสติกสำหรับผนัง ช่วยลดเสียงสะท้อนและเพิ่มความสวยงาม",
                    "price" => 3900,
                    "oldPrice" => 4900,
                    "discount" => "ลด 20%",
                    "buttonText" => "รายละเอียดสินค้า"
                ],
                [
                    "image" => "https://www.trandar.com//public/img/688b3f108a8f9.jpg",
                    "title" => "Acoustic Ceiling Tile",
                    "subtitle" => "Trandar Innovation",
                    "description" => "ฝ้าเพดานอะคูสติก รุ่นพรีเมียม ติดตั้งง่าย ป้องกันเสียงสะท้อน",
                    "price" => 8700,
                    "oldPrice" => 9900,
                    "discount" => "ลด 12%",
                    "buttonText" => "สั่งซื้อทันที"
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

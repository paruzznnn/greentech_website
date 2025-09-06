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
            "detail" => "",
            "carouselId" => "carousel_popular",
            "isSlide" => true,
            "type" => "crssm",
            "sort" => 2,
            "req" => "getPopularItems"
        ],
        // [
        //     "id" => "section_baner",
        //     "class" => "section-space",
        //     "title" => "",
        //     "detail" => "",
        //     "carouselId" => "banner_store",
        //     "isSlide" => false,
        //     "type" => "bbn",
        //     "sort" => 1,
        //     "req" => "getBannersItems"
        // ],
        [
            "id" => "section_product",
            "class" => "section-space",
            "title" => "แทรนดาร์ อะคูสติก",
            "detail" => "",
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
            "detail" => "",
            "carouselId" => "carousel_products",
            "isSlide" => false,
            "type" => "gcmd",
            "sort" => 3,
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
            "detail" => "",
            "carouselId" => "introduce_store",
            "isSlide" => false,
            "type" => "intd",
            "sort" => 1,
            "req" => "getIntroItems"
        ],
        [
            "id" => "section_brand",
            "class" => "section-space",
            "title" => "แบรนด์ยอดนิยม",
            "detail" => "แบรนด์ชั้นนำที่เข้ามาร่วมกับเราและอีกมากมาย",
            "carouselId" => "brand_store",
            "isSlide" => true,
            "type" => "gcsm",
            "sort" => 4,
            "req" => "getBrandItems"
        ],
        // [
        //     "id" => "section_baner2",
        //     "class" => "section-space",
        //     "title" => "",
        //     "detail" => "",
        //     "carouselId" => "banner_store2",
        //     "isSlide" => false,
        //     "type" => "bbn",
        //     "sort" => 7,
        //     "req" => "getBannersItems2"
        // ],
    ];

    http_response_code(200);
    $response = [
        "data" => $data
    ];

    echo json_encode($response);
    exit;
} 
else if ($action == 'getPopularItems') {

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
} 
else if ($action == 'getBannersItems') {

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
} 
else if ($action == 'getBannersItems2') {

    $data = [
        [
            "path" => $BASE_WEB . "product",
            "image" => "https://www.trandar.com//public/img/688b3f108a8f9.jpg",
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
} 
else if ($action == 'getProductItems') {

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
} 
else if ($action == 'getNewsItems') {

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
} 
else if ($action == 'getIntroItems') {

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
} 
else if ($action == 'getBrandItems') {

    $data = [
        [
            "image" => "https://www.trandar.com//public/img/logo_688c431f30bf3.png",
            "title" => "Trandar",
            "compLink" => "https://www.trandar.com/app/index.php"
        ],
        [
            "image" => "https://www.allable.co.th/public/img/logo-ALLABLE-06.png",
            "title" => "ALLABLE",
            "compLink" => "https://www.allable.co.th/app/index"
        ],
        [
            "image" => "data:image/jpeg;base64,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",
            "title" => "ALLABLE",
            "compLink" => "https://www.allable.co.th/app/index"
        ],
        [
            "image" => "https://mms.businesswire.com/media/20240809282033/en/2211847/5/devrev4905logowikcom.jpg?download=1",
            "title" => "ALLABLE",
            "compLink" => "https://www.allable.co.th/app/index"
        ],
        [
            "image" => "https://www.allable.co.th/public/img/netizen-logo.png",
            "title" => "NETIZEN",
            "compLink" => "https://www.netizen.co.th/"
        ],
        [
            "image" => "https://www.netizen.co.th/wp-content/uploads/2025/06/Huawei-Logo-1536x864.png",
            "title" => "NETIZEN",
            "compLink" => "https://www.netizen.co.th/"
        ],
        [
            "image" => "https://www.netizen.co.th/wp-content/uploads/2025/06/Logo-SAP-Platinum-Partner-United-VARs-02-1-1.png",
            "title" => "NETIZEN",
            "compLink" => "https://www.netizen.co.th/"
        ],
        [
            "image" => "https://www.netizen.co.th/wp-content/uploads/2025/06/United-Vars-logo-2-1536x506.png",
            "title" => "NETIZEN",
            "compLink" => "https://www.netizen.co.th/"
        ],
        [
            "image" => "https://www.netizen.co.th/wp-content/uploads/2025/06/Amazon_Web_Services-Logo.wine_-1536x1024.png",
            "title" => "NETIZEN",
            "compLink" => "https://www.netizen.co.th/"
        ],
        [
            "image" => "https://www.netizen.co.th/wp-content/uploads/2025/06/celigo-logo-2018-large-blue.png",
            "title" => "NETIZEN",
            "compLink" => "https://www.netizen.co.th/"
        ],
        [
            "image" => "https://www.netizen.co.th/wp-content/uploads/2025/06/PTT-Digital_logo_transparent.png",
            "title" => "NETIZEN",
            "compLink" => "https://www.netizen.co.th/"
        ],
        [
            "image" => "https://www.netizen.co.th/wp-content/uploads/2025/06/Mitsubishi_Electric_logo.svg_.png",
            "title" => "NETIZEN",
            "compLink" => "https://www.netizen.co.th/"
        ],
        [
            "image" => "https://www.netizen.co.th/wp-content/uploads/2025/06/IPS_LOGO-01-1536x833.png",
            "title" => "NETIZEN",
            "compLink" => "https://www.netizen.co.th/"
        ]
    ];

    http_response_code(200);
    $response = [
        "data" => $data
    ];

    echo json_encode($response);
    exit;

}
else {

    http_response_code(400);
    echo json_encode([
        "error" => "Unauthorized"
    ]);
    exit;

}


/*----------------------------------*/

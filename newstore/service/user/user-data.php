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
if ($action == 'getOrdersItems') {

    // $data = [
    //     [
    //         'id' => 'ORD001',
    //         'date' => '2023-07-20',
    //         'total' => 1250.00,
    //         'status' => 'Delivered',
    //         'items' => [
    //             [
    //                 'name' => 'Trandar Mineral Fiber AMF',
    //                 'quantity' => 2,
    //                 'price' => 300,
    //                 'imageUrl' => 'https://www.trandar.com//public/shop_img/6883336b6606d_______________________AMF-_________.jpg'
    //             ],
    //             [
    //                 'name' => 'Trandar AMF Mercure',
    //                 'quantity' => 1,
    //                 'price' => 650,
    //                 'imageUrl' => 'https://www.trandar.com//public/shop_img/687a1a94a6f10_Trandar_AMF_Mercure.jpg'
    //             ]
    //         ]
    //     ],
    //     [
    //         'id' => 'ORD002',
    //         'date' => '2023-07-22',
    //         'total' => 899.50,
    //         'status' => 'Shipped',
    //         'items' => [
    //             [
    //                 'name' => 'Trandar AMF Fine Fresko',
    //                 'quantity' => 1,
    //                 'price' => 899.50,
    //                 'imageUrl' => 'https://www.trandar.com//public/shop_img/687a1aa984ae2_Trandar_AMF_Fine_Fresko.jpg'
    //             ]
    //         ]
    //     ],
    //     [
    //         'id' => 'ORD003',
    //         'date' => '2023-07-25',
    //         'total' => 500.00,
    //         'status' => 'Pending',
    //         'items' => [
    //             [
    //                 'name' => 'Trandar AMF Star',
    //                 'quantity' => 2,
    //                 'price' => 250,
    //                 'imageUrl' => 'https://www.trandar.com//public/shop_img/687a1a756ce6a_Trandar_AMF_Star.jpg'
    //             ]
    //         ]
    //     ],
    //     [
    //         'id' => 'ORD004',
    //         'date' => '2023-07-28',
    //         'total' => 350.00,
    //         'status' => 'Cancelled',
    //         'items' => [
    //             [
    //                 'name' => 'Trandar T-Bar T15',
    //                 'quantity' => 5,
    //                 'price' => 70,
    //                 'imageUrl' => 'https://www.trandar.com//public/shop_img/687b2f5b393b2_497eeb6fc69f5635590f41fc078dff98.jpg'
    //             ]
    //         ]
    //     ]
    // ];

    $data = [];

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

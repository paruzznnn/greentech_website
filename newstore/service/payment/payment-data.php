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
$input = file_get_contents("php://input");
$dataJson = json_decode($input, true);
if ($dataJson == null) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid JSON"]);
    exit;
}
$action = $dataJson['action'];

if ($action == 'payOrder') {

    $product_item_json = $dataJson['product_item'];
    $product_items = json_decode($product_item_json, true);

    echo '<pre>';
    print_r($dataJson);
    echo '</pre>';
    exit;


    // $conditions = [
    //     [
    //         'column' => 'member_id', 
    //         'operator' => '=', 
    //         'value' => $userId
    //     ]
    // ];

    // $items = selectData(
    //     $conn_cloudpanel, 
    //     'ecm_address', 
    //     $conditions, 
    //     '*'
    // );

    // $data = [];
    // $seen_category_ids = [];
    // foreach ($items as $item) {
    //     $data[] = [
    //         'id' => $item['address_id'],
    //         'fullname' => $item['firstname'] . ' ' . $item['lastname'],
    //         'phoneNumber' => $item['phone_number'],
    //         'addressDetail' => $item['detail'],
    //         'province_id' => $item['province_id'],
    //         'district_id' => $item['district_id'],
    //         'sub_district_id' => $item['sub_district_id'],
    //         'postcode_id' => $item['postcode_id']
    //     ];
    // }

    // $response = [
    //     "data" => $data
    // ];

    // http_response_code(200);
    // echo json_encode($response);
    // $conn_cloudpanel->close();
    exit;
} else {

    http_response_code(400);
    echo json_encode([
        "error" => "Unauthorized"
    ]);
    exit;
}

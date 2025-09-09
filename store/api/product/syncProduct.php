<?php
require_once '../../server/connect_sqli.php';
require_once '../../server/insert_sqli.php';
require_once '../../server/select_sqli.php';
header('Content-Type: application/json');


/*------- Authorization AND TIME ZONE ---------- */
if (isset($_SESSION['user_timezone'])) {
    date_default_timezone_set($_SESSION['user_timezone']);
} else {
    date_default_timezone_set("UTC");
}
// $headers = getallheaders();
// if (!isset($headers['Authorization'])) {
//     http_response_code(401);
//     echo json_encode(["error" => "Unauthorized"]);
//     exit;
// }
// $authHeader = $headers['Authorization'];
// $token = str_replace('Bearer ', '', $authHeader);
// $validToken = "my_secure_token_123";
// if ($token !== $validToken) {
//     http_response_code(403);
//     echo json_encode(["error" => "Invalid token"]);
//     exit;
// }
/*------------------------------------------*/

/*---------ACTION DATA -------------*/
$action = $_POST['action'];
if ($action == 'syncProduct') {

    // $data = [];

    $insData = [
        'material_id' => $_POST['matId'],
        'code' => $_POST['code'],
        'pic_icon' => $_POST['pic_icon'],
        'category_id'=> $_POST['material_category_id'],
        'category_name'=> $_POST['category_name'],
        'description'=> $_POST['description'],
        'attb_item'=> $_POST['attb_item'],
        'attb_price'=> $_POST['attb_price'],
        'attb_value'=> $_POST['attb_value'],
        'cost'=> $_POST['cost'],
        'currency'=> $_POST['currency'],
        'module'=> $_POST['module'],
        'stock'=> $_POST['stock'],
        'sync_status'=> $_POST['sync'],
        'comp_id'=> $_POST['company_id'],
        'uom'=> $_POST['uom'],
        'weight'=> ''
    ];

    $checkIns = insertData($conn_cloudpanel, 'ecm_product', $insData);

    // $items = selectData($conn_cloudpanel, 'ecm_product');
    // foreach ($items as $item) {
    //     $data[] = $item;
    // }

    $response = [
        "status" => $checkIns,
        "message" => "Request insert."
    ];

    http_response_code(200);
    echo json_encode($response);
    $conn_cloudpanel->close();
    exit;

}  else {

    http_response_code(400);
    echo json_encode([
        "error" => "Unauthorized"
    ]);
    exit;
}
?>
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

// echo '<pre>';
// print_r($_SESSION);
// print_r($_GET);
// echo '</pre>';
// exit;

function convertOrderStatus($status){
    switch ($status) {
        case '0': return 'Pending';
        case '1': return 'Shipped';
        case '2': return 'Delivered';
        case '3': return 'Cancelled';
        case '4': return 'Finished';
        case '5': return 'Return';
        default: return $status;
    }
}

if ($action == 'getOrdersItems') {

    $data = [];
    $conditionsMain = [
        [
            'column' => 'del',
            'operator' => '=',
            'value' => 0
        ]
    ];
    $mainItems = selectData(
        $conn_cloudpanel,
        'ecm_orders',
        $conditionsMain,
        '*'
    );
    foreach ($mainItems as $item) {
        $orderId = $item['order_id'];
        $conditionsSub = [
            [
                'column' => 'order_id',
                'operator' => '=',
                'value' => $orderId
            ]
        ];
        $subItems = selectData(
            $conn_cloudpanel,
            'ecm_orders_detail',
            $conditionsSub,
            '*'
        );
        $products = [];
        foreach ($subItems as $items) {
            $products[] = [
                'name' => $items['product_name'] ?? '',
                'quantity' => $items['quantity'] ?? 0,
                'price' => $items['price'] ?? 0,
                'imageUrl' => $items['product_pic'] ?? ''
            ];
        }

        $total_price = 
        (double)($item['sub_total'] ?? 0) + 
        (double)($item['vat_amount'] ?? 0) + 
        (double)($item['shipping_amount'] ?? 0) - 
        (double)($item['discount_amount'] ?? 0);

        $data[] = [
            'id' => $orderId,
            'date' => $item['created_at'],
            'total' => $total_price,
            'status' => convertOrderStatus($item['status']),
            'items' => $products
        ];
    }

    $response = [
        "data" => $data
    ];

    http_response_code(200);
    echo json_encode($response);
    $conn_cloudpanel->close();
    exit;
} else {

    http_response_code(400);
    echo json_encode([
        "error" => "Unauthorized"
    ]);
    exit;
}


/*----------------------------------*/

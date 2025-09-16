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
$userId = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;

if ($action == 'getOrdersItems') {

    $conditions = [
        ['column' => 'member_id', 'operator' => '=', 'value' => $userId],
        ['column' => 'del', 'operator' => '=', 'value' => 0],
        ['column' => 'status', 'operator' => '=', 'value' => 0]
    ];

    $orders_data = selectData($conn_cloudpanel, 'ecm_orders', $conditions, '*');
    $payment_data = selectData($conn_cloudpanel, 'ecm_orders_payment', $conditions, '*');

    $combinedData = [];
    foreach ($orders_data as $order) {
        $orderId = $order['order_id'];
        $payments = array_filter($payment_data, fn($p) => $p['order_id'] === $orderId);

        $combinedData[$orderId] = [
            'orderId' => $orderId,
            'total' => $order['order_total'],
            'payments' => array_map(fn($p) => [
                'type' => $p['payment_type'],
                'pic' => $p['payment_pic'],
                'date' => $p['created_at']
            ], $payments)
        ];
    }

    $response = [
        "data" => $combinedData
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

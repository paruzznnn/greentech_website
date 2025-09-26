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

switch ($action) {
    case 'getOrdersItems':
        handleGetOrdersItems($conn_cloudpanel, $userId);
        break;
    default:
        http_response_code(400);
        echo json_encode(["error" => "Unauthorized"]);
        exit;
}

function convertOrderStatus($status) {
    switch ($status) {
        case '0':
            return 'Pending';
        case '1':
            return 'Shipped';
        case '2':
            return 'Delivered';
        case '3':
            return 'Cancelled';
        case '4':
            return 'Finished';
        case '5':
            return 'Return';
        default:
            return $status;
    }
}

function handleGetOrdersItems($conn, $userId) {
    try {
        $data = [];
        $conditionsMain = [
            ['column' => 'member_id', 'operator' => '=', 'value' => $userId],
            ['column' => 'del', 'operator' => '=', 'value' => 0]
        ];

        $mainItems = selectData($conn, 'ecm_orders', $conditionsMain, '*');

        if ($mainItems === false) {
            throw new Exception("ไม่สามารถดึงข้อมูลคำสั่งซื้อได้");
        }

        foreach ($mainItems as $item) {
            $orderId = $item['order_id'];

            $conditionsSub = [
                ['column' => 'order_id', 'operator' => '=', 'value' => $orderId]
            ];

            $subItems = selectData($conn, 'ecm_orders_detail', $conditionsSub, '*');

            if ($subItems === false) {
                throw new Exception("ไม่สามารถดึงข้อมูลสินค้าของคำสั่งซื้อ $orderId ได้");
            }

            $products = [];
            foreach ($subItems as $sub) {
                $products[] = [
                    'product_name' => $sub['product_name'] ?? '',
                    'quantity'     => $sub['quantity'] ?? 0,
                    'price'        => $sub['price'] ?? 0,
                    'product_pic'  => $sub['product_pic'] ?? ''
                ];
            }

            $total_price =
                (float)($item['sub_total'] ?? 0) +
                (float)($item['vat_amount'] ?? 0) +
                (float)($item['shipping_amount'] ?? 0) -
                (float)($item['discount_amount'] ?? 0);

            $data[] = [
                'order_id'        => $orderId,
                'order_code'      => $item['order_code'],
                'sub_total'       => $item['sub_total'],
                'vat_amount'      => $item['vat_amount'],
                'shipping_amount' => $item['shipping_amount'],
                'discount_amount' => $item['discount_amount'],
                'total'           => $total_price,
                'created_at'      => $item['created_at'],
                'status'          => convertOrderStatus($item['status']),
                'items'           => $products
            ];
        }

        http_response_code(200);
        echo json_encode(["data" => $data]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(["status" => false, "error" => $e->getMessage()]);
    } finally {
        if ($conn) $conn->close();
        exit;
    }
}



/*----------------------------------*/

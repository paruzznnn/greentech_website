<?php
require_once '../../server/connect_sqli.php';
require_once '../../server/select_sqli.php';
require_once '../../server/insert_sqli.php';
require_once '../../PromptPay/lib/PromptPayQR.php';
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
$userId = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;

if($action == 'getQRPromptPay'){
    $PromptPayQR = new PromptPayQR();
    $PromptPayQR->size = 8;
    $PromptPayQR->id = $dataJson['phone'];
    $PromptPayQR->amount = $dataJson['amount'];
    $QRCode = $PromptPayQR->generate('../../PromptPay/TMP_FILE_QRCODE_PROMPTPAY.png');
    $response = [
        "qrCodeImageBase64" => $QRCode
    ];
    http_response_code(200);
    echo json_encode($response);
    exit;

} else if ($action == 'payOrder') {

    $order_code = isset($dataJson['order_id']) ? (string) $dataJson['order_id'] : '';
    $delivery_option = isset($dataJson['delivery_option']) ? (string) $dataJson['delivery_option'] : '';

    $full_name = isset($dataJson['full_name']) ? (string) $dataJson['full_name'] : '';
    $phone_number = isset($dataJson['phone_number']) ? (string) $dataJson['phone_number'] : '';
    $address_detail = isset($dataJson['address_detail']) ? (string) $dataJson['address_detail'] : '';

    $province = isset($dataJson['province']) ? (int) $dataJson['province'] : '';
    $district = isset($dataJson['district']) ? (int) $dataJson['district'] : '';
    $subdistrict = isset($dataJson['subdistrict']) ? (int) $dataJson['subdistrict'] : '';
    $postalCode = isset($dataJson['postalCode']) ? (string) $dataJson['postalCode'] : '';
    $payment_method = isset($dataJson['payment_method']) ? (string) $dataJson['payment_method'] : '';

    $product_items = isset($dataJson['product_item']) ? json_decode($dataJson['product_item'], true) : null;
    if (!is_array($product_items)) {
        die("Invalid product_item format.");
    }

    $sub_total = isset($dataJson['sub_total']) ? (float) $dataJson['sub_total'] : 0;
    $vat_amount = isset($dataJson['vat_amount']) ? (float) $dataJson['vat_amount'] : 0;
    $shipping_amount = isset($dataJson['shipping_amount']) ? (float) $dataJson['shipping_amount'] : 0;
    $discount_amount = isset($dataJson['discount_amount']) ? (float) $dataJson['discount_amount'] : 0;
    $total_amount = isset($dataJson['total_amount']) ? (float) $dataJson['total_amount'] : 0;

    // switch ($payment_method) {
    //     case 'bank_transfer':
    //         $payMethod = 1;
    //         break;
    //     case 'promptpay':
    //         $payMethod = 2;
    //         break;
    //     default:
    //         $payMethod = 0;
    //         break;
    // }

    // switch ($delivery_option) {
    //     case 'shipping':
    //         $deliveryMethod = 1;
    //         break;
    //     case 'pickup':
    //         $deliveryMethod = 2;
    //         break;
    //     default:
    //         $deliveryMethod = 0;
    //         break;
    // }

    // $orderKey = time();
    // $dateNow = date('Y-m-d H:i:s');
    // $insdata = [
    //     'member_id' => $userId,
    //     'order_id' => $orderKey,
    //     'order_code' => $order_code,
    //     'payment_method' => $payMethod,
    //     'sub_total' => $sub_total,
    //     'vat_amount' => $vat_amount,
    //     'shipping_amount' => $shipping_amount,
    //     'discount_amount' => $discount_amount,
    //     'delivery_method' => $deliveryMethod,
    //     'created_at' => $dateNow
    // ];
    // if (insertData($conn_cloudpanel, 'ecm_orders', $insdata)) {
    //     $checkIns = true;
    //     foreach ($product_items as $item) {
    //         $product_id = (int)$item['id'];
    //         $name = $item['name'];
    //         $price = (float)$item['price'];
    //         $quantity = (int)$item['quantity'];
    //         $image = $item['imageUrl'];
    //         $totalPrice = $price * $quantity;
    //         $ins_data = [
    //             'member_id' => $userId,
    //             'order_id' => $orderKey,
    //             'order_code' => $order_code,
    //             'product_id' => $product_id,
    //             'product_pic' => $image,
    //             'price' => $price,
    //             'quantity' => $quantity,
    //             'total_price' => $totalPrice,
    //             'currency' => "THB"
    //         ];
    //         insertData($conn_cloudpanel, 'ecm_orders_detail', $ins_data);
    //     }
    // } else {
    //     $checkIns = false;
    // }

    $response = [
        // "status" => $checkIns
        "status" => true
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

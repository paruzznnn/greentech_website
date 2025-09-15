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
$timeZone = isset($_SESSION['user_timezone']) ? $_SESSION['user_timezone'] : '';
$dateNow = date('Y-m-d H:i:s');

function generateQRpromptPay ($dataJson) {
    $PromptPayQR = new PromptPayQR();
    $PromptPayQR->size = 8;
    $PromptPayQR->id = $dataJson['phone'];
    $PromptPayQR->amount = $dataJson['amount'];
    $QRCode = $PromptPayQR->generate('../../PromptPay/TMP_FILE_QRCODE_PROMPTPAY.png');
    return $QRCode;
}

if ($action == 'payOrder') {

try {
    // เริ่ม Transaction
    $conn_cloudpanel->begin_transaction();

    // -----------------------
    // STEP 1: Validate Billing
    // -----------------------
    if (empty($dataJson['billing']['first_name'])) {
        throw new Exception("Billing: first_name is required.");
    }
    $first_name = (string)$dataJson['billing']['first_name'];

    if (empty($dataJson['billing']['last_name'])) {
        throw new Exception("Billing: last_name is required.");
    }
    $last_name = (string)$dataJson['billing']['last_name'];

    if (empty($dataJson['billing']['phone_number'])) {
        throw new Exception("Billing: phone_number is required.");
    }
    $phone_number = (string)$dataJson['billing']['phone_number'];

    // -----------------------
    // STEP 2: Validate Address
    // -----------------------
    if (!isset($dataJson['addresses'][0])) {
        throw new Exception("Addresses: missing address.");
    }

    $addresses_detail       = (string)($dataJson['addresses'][0]['detail'] ?? '');
    $addresses_provinces    = (string)($dataJson['addresses'][0]['provinces'] ?? '');
    $addresses_districts    = (string)($dataJson['addresses'][0]['districts'] ?? '');
    $addresses_subdistricts = (string)($dataJson['addresses'][0]['subdistricts'] ?? '');
    $addresses_postalCode   = (string)($dataJson['addresses'][0]['postalCode'] ?? '');

    if ($addresses_detail === '' || $addresses_provinces === '' || $addresses_postalCode === '') {
        throw new Exception("Addresses: required fields are missing.");
    }

    // -----------------------
    // STEP 3: Validate Shipping
    // -----------------------
    if (!isset($dataJson['selectedShippingOptions'])) {
        throw new Exception("Shipping: missing selectedShippingOptions.");
    }
    $shipping_name  = (string)($dataJson['selectedShippingOptions']['name'] ?? '');
    $shipping_type  = (string)($dataJson['selectedShippingOptions']['value'] ?? '');
    $shipping_price = (float)($dataJson['selectedShippingOptions']['price'] ?? 0);

    // -----------------------
    // STEP 4: Validate Cart Items
    // -----------------------
    $order_items = $dataJson['cartItems'] ?? [];
    if (!is_array($order_items) || count($order_items) === 0) {
        throw new Exception("Cart: must have at least one item.");
    }

    // -----------------------
    // STEP 5: Validate Discount
    // -----------------------
    if (!isset($dataJson['appliedCoupon'])) {
        throw new Exception("Discount: missing appliedCoupon.");
    }
    $discount_code  = (string)($dataJson['appliedCoupon']['code'] ?? '');
    $discount_name  = (string)($dataJson['appliedCoupon']['label'] ?? '');
    $discount_type  = (string)($dataJson['appliedCoupon']['type'] ?? '');
    $discount_price = (float)($dataJson['appliedCoupon']['value'] ?? 0);

    // -----------------------
    // STEP 6: Validate Services
    // -----------------------
    $order_service = $dataJson['selectedServices'] ?? [];
    if (!is_array($order_service)) {
        throw new Exception("Services: invalid format.");
    }

    // -----------------------
    // STEP 7: Validate Summary
    // -----------------------
    if (!isset($dataJson['summary'])) {
        throw new Exception("Summary: missing data.");
    }
    $subtotal = (float)($dataJson['summary']['subtotal'] ?? 0);
    $discount = (float)($dataJson['summary']['discount'] ?? 0);
    $shipping = (float)($dataJson['summary']['shipping'] ?? 0);
    $service  = (float)($dataJson['summary']['service'] ?? 0);
    $vat      = (float)($dataJson['summary']['tax'] ?? 0);
    $total    = (float)($dataJson['summary']['total'] ?? 0);

    if ($total <= 0) {
        throw new Exception("Summary: total must be greater than 0.");
    }

    // -----------------------
    // STEP 8: Generate Order Number
    // -----------------------
    $gencode    = "ODR";
    $newOrderNo = generateOrderNumber($conn_cloudpanel, $gencode);

    // -----------------------
    // STEP 9: Insert ecm_orders
    // -----------------------
    $order_data = [
        'member_id'      => $userId,
        'order_id'       => $newOrderNo,
        'order_subtotal' => $subtotal,
        'order_discount' => $discount,
        'order_shipping' => $shipping,
        'order_service'  => $service,
        'order_vat'      => $vat,
        'order_total'    => $total,
        'order_notes'    => $dataJson['orderNotes'] ?? '',
        'timezone'       => $timeZone,
        'created_at'     => $dateNow
    ];
    if (!insertData($conn_cloudpanel, 'ecm_orders', $order_data)) {
        throw new Exception("Insert ecm_orders failed.");
    }

    // -----------------------
    // STEP 10: Insert ecm_orders_item
    // -----------------------
    foreach ($order_items as $item) {
        if (!isset($item['id'], $item['name'], $item['price'], $item['qty'])) {
            throw new Exception("Invalid order item data.");
        }

        $product_id   = (int)$item['id'];
        $product_name = (string)$item['name'];
        $price        = (float)$item['price'];
        $quantity     = (int)$item['qty'];
        $image        = (string)($item['image'] ?? '');
        $totalPrice   = $price * $quantity;

        $product_data = [
            'member_id'    => $userId,
            'order_id'     => $newOrderNo,
            'product_id'   => $product_id,
            'product_name' => $product_name,
            'product_pic'  => $image,
            'price'        => $price,
            'quantity'     => $quantity,
            'total_price'  => $totalPrice,
            'timezone'     => $timeZone,
            'created_at'   => $dateNow
        ];
        if (!insertData($conn_cloudpanel, 'ecm_orders_item', $product_data)) {
            throw new Exception("Insert ecm_orders_item failed.");
        }
    }

    // -----------------------
    // STEP 11: Insert ecm_orders_shipping
    // -----------------------
    $shipping_data = [
        'member_id'       => $userId,
        'order_id'        => $newOrderNo,
        'shipping_name'   => $shipping_name,
        'shipping_price'  => $shipping_price,
        'shipping_type'   => $shipping_type,
        'timezone'        => $timeZone,
        'created_at'      => $dateNow
    ];
    if (!insertData($conn_cloudpanel, 'ecm_orders_shipping', $shipping_data)) {
        throw new Exception("Insert ecm_orders_shipping failed.");
    }

    // -----------------------
    // STEP 12: Insert ecm_orders_service
    // -----------------------
    foreach ($order_service as $item) {
        if (!isset($item['label'], $item['name'], $item['price'])) {
            throw new Exception("Invalid order service data.");
        }

        $service_name  = (string)$item['label'];
        $service_type  = (string)$item['name'];
        $service_price = (float)$item['price'];

        $service_data = [
            'member_id'     => $userId,
            'order_id'      => $newOrderNo,
            'service_name'  => $service_name,
            'service_price' => $service_price,
            'service_type'  => $service_type,
            'timezone'      => $timeZone,
            'created_at'    => $dateNow
        ];
        if (!insertData($conn_cloudpanel, 'ecm_orders_service', $service_data)) {
            throw new Exception("Insert ecm_orders_service failed.");
        }
    }

    // -----------------------
    // STEP 13: Insert ecm_orders_discount
    // -----------------------
    $discount_data = [
        'member_id'       => $userId,
        'order_id'        => $newOrderNo,
        'discount_code'   => $discount_code,
        'discount_name'   => $discount_name,
        'discount_price'  => $discount_price,
        'discount_type'   => $discount_type,
        'timezone'        => $timeZone,
        'created_at'      => $dateNow
    ];
    if (!insertData($conn_cloudpanel, 'ecm_orders_discount', $discount_data)) {
        throw new Exception("Insert ecm_orders_discount failed.");
    }

    // -----------------------
    // STEP 14: Insert ecm_orders_payment
    // -----------------------
    $payment_data = [
        'member_id'       => $userId,
        'order_id'        => $newOrderNo,
        'timezone'        => $timeZone,
        'created_at'      => $dateNow
    ];
    if (!insertData($conn_cloudpanel, 'ecm_orders_payment', $payment_data)) {
        throw new Exception("Insert ecm_orders_discount failed.");
    }

    // Commit เมื่อสำเร็จทุกขั้นตอน
    $conn_cloudpanel->commit();

    $response = ["status" => true, "order_id" => $newOrderNo];
    http_response_code(200);
    echo json_encode($response);

} catch (Exception $e) {
    // Rollback เมื่อมี Error
    if ($conn_cloudpanel->errno) {
        $conn_cloudpanel->rollback();
    }

    http_response_code(500);
    echo json_encode([
        "status" => false,
        "error"  => $e->getMessage()
    ]);

} finally {
    if (isset($conn_cloudpanel) && $conn_cloudpanel instanceof mysqli) {
        $conn_cloudpanel->close();
    }
    exit;
}

} else {

    http_response_code(400);
    echo json_encode([
        "error" => "Unauthorized"
    ]);
    exit;
}

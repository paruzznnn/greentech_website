<?php
session_start();
date_default_timezone_set('Asia/Bangkok');
require_once '../../lib/connect.php';
include_once '../../api/PromptPay/lib/PromptPayQR.php';
require_once '../../lib/base_directory.php';

function getCartContents() {
    // คืนค่าข้อมูลรถเข็นจาก session
    return isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
}

function getCartOptions() {
    // คืนค่าข้อมูลรถเข็นจาก session
    if (isset($_SESSION['cartOption'])) {
        return $_SESSION['cartOption'];
    }
    return array();
}

function getOrderContents() {
    // คืนค่าข้อมูลคำสั่งซื้อจาก session
    return isset($_SESSION['orderArray']) ? $_SESSION['orderArray'] : array();
}

function saveOrderContents($orderArray) {
    // เก็บข้อมูลคำสั่งซื้อใน session
    $_SESSION['orderArray'] = $orderArray;
}

function addItemOrder($merged_data, $type_id, $pay_channel, $conn) {
    global $base_path;
    $basePath = $base_path;

    $cartContents = getCartContents();
    $cartOption = getCartOptions();

    // Step 1: ตรวจสอบว่าตะกร้าสินค้ามีเนื้อหาหรือไม่
    if (empty($cartContents)) {
        echo json_encode(['status' => false, 'step' => 1, 'message' => 'Cart is empty.', 'errors' => ['Cart is empty']]);
        exit;
    }

    $pro_ids = array_column($cartContents, 'pro_id');
    $pro_ids_placeholder = implode(',', array_fill(0, count($pro_ids), '?'));

    // Step 2: ตรวจสอบสินค้าในฐานข้อมูล
    $sql = "SELECT material_id, stock FROM ecm_product WHERE material_id IN ($pro_ids_placeholder)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('s', count($pro_ids)), ...$pro_ids);
    $stmt->execute();
    $result = $stmt->get_result();

    $existing_pro_ids = [];
    while ($row = $result->fetch_assoc()) {
        $existing_pro_ids[] = $row;
    }

    // Step 3: ตรวจสอบสต็อกสินค้า
    $material_ids = array_column($existing_pro_ids, 'material_id');
    $insufficientStock = false;
    $stockErrors = [];

    foreach ($cartContents as $item) {
        if (in_array($item['pro_id'], $material_ids)) {
            $stock_info = current(array_filter($existing_pro_ids, fn($stockItem) => $stockItem['material_id'] === $item['pro_id']));
            $stock = $stock_info['stock'] ?? 0;

            if ($stock < $item['quantity']) {
                $insufficientStock = true;
                $stockErrors[] = "{$item['description']} has insufficient stock.";
            }

        } else {
            $stockErrors[] = "Product ID: {$item['pro_id']} is not found in the database.";
        }
    }

    // Step 4: ส่งข้อมูลผลลัพธ์หากมีข้อผิดพลาด
    if ($insufficientStock) {

        echo json_encode(['status' => false, 'step' => 4, 'message' => 'Insufficient stock', 'errors' => $stockErrors]);
        exit;

    }

    $stmt->close();

    // Step 5: เริ่มสร้างข้อมูลคำสั่งซื้อ
    $valuesOnly = array_values($cartContents);
    $tmsID = $cartOption['tms_id'] ?? 0;
    $tmsPrice = $cartOption['tms_price'] ?? 0;
    $orderCode = 'ORD-' . date('Ymd-His');

    $orderArray = [
        $orderCode => [
            'product_data' => $valuesOnly,
            'customer_data' => $merged_data,
            'type' => $type_id,
            'transport' => [
                'tms_id' => $tmsID,
                'tms_price' => $tmsPrice
            ]
        ]
    ];

    $totalProduct = count($valuesOnly);
    $totalQuantity = array_sum(array_column($valuesOnly, 'quantity'));
    $totalPrice = array_sum(array_column($valuesOnly, 'total_price'));

    // Step 6: คำนวณ VAT และราคาทั้งหมด
    $vat = ($totalPrice + $tmsPrice) * 0.07; 
    $totalPriceWithVat = $totalPrice + $tmsPrice + $vat;
    $formattedAmount = number_format($totalPriceWithVat, 2);
    $amountNumeric = floatval($totalPriceWithVat);

    // Step 7: สร้าง QR code สำหรับการชำระเงิน
    $PromptPayQR = new PromptPayQR();
    $PromptPayQR->size = 8;
    $PromptPayQR->id = '0988971593';
    $PromptPayQR->amount = $amountNumeric;

    $payment = ($pay_channel == 2) 
        ? '<img class="qr-img" src="' . $PromptPayQR->generate('../../api/PromptPay/TMP_FILE_QRCODE_PROMPTPAY.png') . '"/>'
        : '<img class="qr-img" src="' . $basePath . '/tdi_store/public/img/bankPay.png"/>';

    // Step 8: ส่งข้อมูลคำสั่งซื้อ
    echo json_encode([
        'status' => true,
        'step' => 8,
        'pay' => $payment,
        'orderNumber' => $orderCode,
        'totalOrderProduct' => $totalProduct,
        'totalOrderQuantity' => $totalQuantity,
        'totalOrderPrice' => number_format($totalPrice, 2),
        'transportOrder' => $tmsPrice,
        'vatOrder' => number_format($vat, 2),
        'totalOrderPriceWithVat' => number_format($totalPriceWithVat, 2),
        'payChannel' => $pay_channel
    ]);

    // Step 9: บันทึกข้อมูลคำสั่งซื้อ
    saveOrderContents($orderArray);
    $conn->close();
    exit;
}


function removeItemOrder($itemKey) {
    $orderArray = getOrderContents();

    if (isset($orderArray[$itemKey])) {
        unset($orderArray[$itemKey]);
    }

    saveOrderContents($orderArray);
}

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $check_status = false;

    if ($action == 'add_order') {

        if (isset($_POST['shipping'])) {
            $shipping_data = [];
            parse_str($_POST['shipping'], $shipping_data);
        }

        if (isset($_POST['payment'])) {
            $payment_data = [];
            parse_str($_POST['payment'], $payment_data);
        }

        if(isset($_POST['transport'])){
            $transport_data = [];
            parse_str($_POST['transport'], $transport_data);
        }

        unset($shipping_data['prefix'], $shipping_data['province'], $shipping_data['district'], $shipping_data['subdistrict']);
        $combinedShipping = array_merge($shipping_data, $_POST['shippingSub']);

        $merged_data = array_merge($combinedShipping, $payment_data, $transport_data);
        $type_id = isset($_POST['type']) ? $_POST['type'] : '';
        $pay_channel = isset($payment_data['pay_channel']) ? $payment_data['pay_channel'] : '';

        addItemOrder($merged_data, $type_id, $pay_channel, $conn);

        $check_status = true;
    } elseif ($action == 'remove_order') {
        $itemKey = $_POST['item_key'];

        removeItemOrder($itemKey);
        $check_status = true;
    }

    echo json_encode(['status' => $check_status]);
    exit;
}
?>

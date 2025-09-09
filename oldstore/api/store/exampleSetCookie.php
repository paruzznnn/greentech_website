<?php

 // $cartContents = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];
        // $orderContents = isset($_COOKIE['orderArray']) ? json_decode($_COOKIE['orderArray'], true) : [];
        
        // // Initialize order array
        // $orderArray = [];
        // foreach ($orderContents as $orderCode => $orderDetails) {
        //     $orderID = date('YmdHis'); // Unique order ID
        //     $orderArray[] = [
        //         'order_id' => $orderID,
        //         'order_code' => $orderCode,
        //         'product_data' => $orderDetails['product_data'],
        //         'customer_data' => $orderDetails['customer_data'],
        //         'payment_data' => [
        //             'pay_channel' => $orderDetails['customer_data']['pay_channel']
        //         ],
        //         'type' => $orderDetails['type']
        //     ];
        // }

        // cartOption

        // $cartContents = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];



// session_start();
// function getCartContents() {
//     if (isset($_COOKIE['cart'])) {
//         return json_decode($_COOKIE['cart'], true);
//     }
//     return array();
// }

// function saveCartContents($cart) {
//     //setcookie('cart', json_encode($cart), time() + (86400 * 30), "/"); // Cookie expires in 30 days
//     setcookie('cart', json_encode($cart), time() + 3600, "/"); // Cookie expires in 1 hour
// }

// function addItemCart($cartData) {

//     $cart = getCartContents();
//     $item_key = $cartData['pro_id'];

//     $member_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

//     $cartData['key_item'] = $item_key;
//     $cartData['isMember'] = $member_id;

//     if (isset($cart[$item_key])) {
//         $cart[$item_key]['quantity'] += $cartData['quantity'];
//         $cart[$item_key]['total_price'] += $cartData['total_price'];
//     } else {
        
//         $cart[$item_key] = $cartData;
        
//     }

//     saveCartContents($cart);
// }

// function updateItemQuantity($itemKey, $quantity) {
//     $cart = getCartContents();

//     if (isset($cart[$itemKey])) {
//         $cart[$itemKey]['quantity'] = max(0, $quantity);
//         $cart[$itemKey]['total_price'] = $cart[$itemKey]['price'] * $cart[$itemKey]['quantity'];

//         if ($cart[$itemKey]['quantity'] == 0) {
//             unset($cart[$itemKey]);
//         }
//     }

//     saveCartContents($cart);
// }

// function removeItemCart($itemKey) {
//     $cart = getCartContents();

//     if (isset($cart[$itemKey])) {
//         unset($cart[$itemKey]);
//     }

//     saveCartContents($cart);
// }

// function clearCart() {
//     setcookie('cart', '', time() - 3600, "/"); // Expire the cookie
// }

// if (isset($_POST['action'])) {

//     $action = $_POST['action'];
//     $check_status = false;
//     if ($action == 'add_item') {

//         $cartData = $_POST['cartData'];

//         addItemCart($cartData);

//         $check_status = true;
//     } elseif ($action == 'update_quantity') {
//         $itemKey = $_POST['item_key'];
//         $quantity = intval($_POST['quantity']);

//         updateItemQuantity($itemKey, $quantity);
//         $check_status = true;
//     } elseif ($action == 'remove_item') {
//         $itemKey = $_POST['item_key'];

//         removeItemCart($itemKey);
//         $check_status = true;
//     } elseif ($action == 'clear_cart') {
//         clearCart();
//         $check_status = true;
//     }

    
//     echo json_encode([
//         'status' => $check_status,
//         'data' => getCartContents()
//     ]);
//     exit;
// }


// session_start();
// global $compare;
// function getCompareContents() {
//     if (isset($_COOKIE['compare'])) {
//         return json_decode($_COOKIE['compare'], true);
//     }
//     return array();
// }

// function saveCompareContents($compare) {
//     // setcookie('compare', json_encode($compare), time() + (86400 * 30), "/"); 
//     setcookie('compare', json_encode($compare), time() + 3600, "/"); // Cookie expires in 1 hour
// }

// function addItemCompare($data) {

//     $compare = getCompareContents(); 

//     $item_compare_key = $data['pro_id'];
//     if (isset($compare[$item_compare_key])) {
//     } else {
//         $compare[$item_compare_key] = $data;
//     }

//     saveCompareContents($compare);
// }

// function removeItemCompare($itemKey) {
//     $compare = getCompareContents();

//     if (isset($compare[$itemKey])) {
//         unset($compare[$itemKey]);
//     }

//     saveCompareContents($compare);
// }

// function clearCompare() {
//     setcookie('compare', '', time() - 3600, "/"); 
// }

// if (isset($_POST['action'])) {

//     $action = $_POST['action'];
//     $check_status = false;
//     if ($action == 'add_compare') {

//         if(isset($_POST['compareData'])){
//             $compareData = $_POST['compareData'];
//             addItemCompare($compareData);
//         }

//         $check_status = true;
//     } elseif ($action == 'removeCompare_item') {
//         $itemKey = $_POST['item_key'];

//         removeItemCompare($itemKey);
//         $check_status = true;
//     }

    
//     echo json_encode([
//         'status' => $check_status,
//         'data' => getCompareContents()
//     ]);
//     exit;
// }


// session_start();
// date_default_timezone_set('Asia/Bangkok');
// require_once '../../lib/connect.php';
// include_once '../../api/PromptPay/lib/PromptPayQR.php';
// require_once '../../lib/base_directory.php';

// function getCartContents() {
//     return isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : array();
// }

// function getOrderContents() {
//     if (isset($_COOKIE['orderArray'])) {
//         return json_decode($_COOKIE['orderArray'], true);
//     }
//     return array();
// }

// function saveOrderContents($orderArray) {
//     // setcookie('orderArray', json_encode($orderArray), time() + (86400 * 30), "/"); 
//     setcookie('orderArray', json_encode($orderArray), time() + 3600, "/"); // Cookie expires in 1 hour
// }

// function addItemOrder($merged_data, $type_id, $pay_channel) {

//     global $base_path;

//     $basePath = $base_path;

//     $cartContents = getCartContents();

//     if (empty($cartContents)) {
//         echo json_encode([
//             'status' => false
//         ]);
//         exit;
//     }

//     $valuesOnly = array_values($cartContents);
//     $orderArray = getOrderContents();

//     $orderArray = array();

//     $orderID = date('YmdHis');
//     $orderCode = 'ORD-' . date('Ymd-His');

//     $orderArray[$orderCode] = array(
//         'product_data' => $valuesOnly,
//         'customer_data' => $merged_data,
//         'type' => $type_id
//     );

//     $totalProduct = 0;
//     $totalQuantity = 0;
//     $totalPrice = 0;
    
//     foreach ($valuesOnly as $item) {
//         ++$totalProduct;
    
//         $totalQuantity += $item['quantity'];
//         $totalPrice += $item['total_price'];
//     }
    
//     $vatRate = 0.07;
//     $vat = $totalPrice * $vatRate;
//     $totalPriceWithVat = $totalPrice + $vat;
    
//     $formattedAmount = number_format($totalPriceWithVat, 2);
//     error_log("Formatted Amount: " . $formattedAmount);

//     // Convert formatted amount back to a numeric value
//     $amountNumeric = floatval($totalPriceWithVat); 

//     $PromptPayQR = new PromptPayQR(); 
//     $PromptPayQR->size = 8; // Set QR code size to 8
//     $PromptPayQR->id = '0970727598'; // PromptPay ID
//     $PromptPayQR->amount = $amountNumeric; // Use numeric amount

//     $payment = '';
//     if ($pay_channel == 2) {
//         $payment = '<img class="qr-img" src="' . $PromptPayQR->generate('../../api/PromptPay/TMP_FILE_QRCODE_PROMPTPAY.png') . '"/>';
//     } else {
//         $payment = '<img class="qr-img" src="' . $basePath . '/tdi_store/public/img/bankPay.png"/>';
//     }
    
//     echo json_encode([
//         'status' => true,
//         'pay' => $payment,
//         'orderNumber' => $orderCode,
//         'totalOrderProduct' => $totalProduct,
//         'totalOrderQuantity' => $totalQuantity,
//         'totalOrderPrice' => number_format($totalPrice, 2),
//         'vatOrder' => number_format($vat, 2),
//         'totalOrderPriceWithVat' => number_format($totalPriceWithVat, 2),
//         'payChannel' => $pay_channel
//     ]);

//     saveOrderContents($orderArray);
//     exit;
    
// }

// function removeItemOrder($itemKey) {
//     $orderArray = getOrderContents();

//     if (isset($orderArray[$itemKey])) {
//         unset($orderArray[$itemKey]);
//     }

//     saveOrderContents($orderArray);
// }

// if (isset($_POST['action'])) {

//     $action = $_POST['action'];
//     $check_status = false;
//     if ($action == 'add_order') {

//         if (isset($_POST['shipping'])) {
//             $shipping_data = [];
//             parse_str($_POST['shipping'], $shipping_data);
//         }

//         if (isset($_POST['payment'])) {
//             $payment_data = [];
//             parse_str($_POST['payment'], $payment_data);
//         }

//         $prefix = isset($shipping_data['prefix']) ? $shipping_data['prefix'] : '';
//         $first_name = isset($shipping_data['firstname']) ? $shipping_data['firstname'] : '';
//         $last_name = isset($shipping_data['lastname']) ? $shipping_data['lastname'] : '';
//         $country = isset($shipping_data['country']) ? $shipping_data['country'] : '';
//         $province = isset($shipping_data['province']) ? $shipping_data['province'] : '';
//         $district = isset($shipping_data['district']) ? $shipping_data['district'] : '';
//         $subdistrict = isset($shipping_data['subdistrict']) ? $shipping_data['subdistrict'] : '';
//         $post_code = isset($shipping_data['post_code']) ? $shipping_data['post_code'] : '';
//         $phone_number = isset($shipping_data['phone_number']) ? $shipping_data['phone_number'] : '';
//         $address = isset($shipping_data['address']) ? $shipping_data['address'] : '';
//         $comp_name = isset($shipping_data['comp_name']) ? $shipping_data['comp_name'] : '';
//         $tax_number = isset($shipping_data['tax_number']) ? $shipping_data['tax_number'] : '';
//         $inputLatitude = isset($shipping_data['inputLatitude']) ? $shipping_data['inputLatitude'] : '';
//         $inputLongitude = isset($shipping_data['inputLongitude']) ? $shipping_data['inputLongitude'] : '';
        
//         $pay_channel = isset($payment_data['pay_channel']) ? $payment_data['pay_channel'] : '';

//         $type_id = isset($_POST['type']) ? $_POST['type'] : '';

//         $merged_data = array_merge($shipping_data, $payment_data);

        
//         addItemOrder($merged_data, $type_id, $pay_channel);

//         $check_status = true;
//     } elseif ($action == 'remove_order') {
//         $itemKey = $_POST['item_key'];

//         removeItemOrder($itemKey);
//         $check_status = true;
//     }

//     echo json_encode([
//         'status' => $check_status
//     ]);
//     exit;
// }
?>
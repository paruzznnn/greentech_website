<?php
session_start();
header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');
require_once '../../lib/connect.php';

function getCartContents() {
    // คืนค่าข้อมูลรถเข็นจาก session
    if (isset($_SESSION['cart'])) {
        return $_SESSION['cart'];
    }
    return array();
}

function getCartOptions() {
    // คืนค่าข้อมูลรถเข็นจาก session
    if (isset($_SESSION['cartOption'])) {
        return $_SESSION['cartOption'];
    }
    return array();
}

function saveCartContents($cart) {
    // เก็บข้อมูลรถเข็นใน session
    $_SESSION['cart'] = $cart;
}

function saveCartOptions($cartOption) {
    // เก็บข้อมูลรถเข็นใน session
    $_SESSION['cartOption'] = $cartOption;
}

function addItemCart($cartData) {
    $cart = getCartContents();
    $item_key = $cartData['pro_id'];

    $member_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

    $cartData['key_item'] = $item_key;
    $cartData['isMember'] = $member_id;


    if (isset($cart[$item_key])) {
        $cart[$item_key]['quantity'] += $cartData['quantity'];
        $cart[$item_key]['total_price'] += $cartData['total_price'];
    } else {
        $cart[$item_key] = $cartData;
    }

    saveCartContents($cart);
}

function addTmsCart($tmsID, $tmsPrice){

    $cartOption = getCartOptions();

    if (isset($tmsID)) {
        $cartOption['tms_id'] = $tmsID;
        $cartOption['tms_price'] = $tmsPrice;
    }

    saveCartOptions($cartOption);
}

function updateItemQuantity($itemKey, $quantity) {
    $cart = getCartContents();

    if (isset($cart[$itemKey])) {
        $cart[$itemKey]['quantity'] = max(0, $quantity);
        $cart[$itemKey]['total_price'] = $cart[$itemKey]['price'] * $cart[$itemKey]['quantity'];

        if ($cart[$itemKey]['quantity'] == 0) {
            unset($cart[$itemKey]);
        }
    }

    saveCartContents($cart);
}

function removeItemCart($itemKey) {
    $cart = getCartContents();

    if (isset($cart[$itemKey])) {
        unset($cart[$itemKey]);
    }

    saveCartContents($cart);
}

function clearCart() {
    // ลบข้อมูลรถเข็นใน session
    unset($_SESSION['cart']);
}

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $check_status = false;

    if ($action == 'add_item') {
        $cartData = $_POST['cartData'];
        addItemCart($cartData);
        $check_status = true;
    } elseif ($action == 'update_quantity') {
        $itemKey = $_POST['item_key'];
        $quantity = intval($_POST['quantity']);
        updateItemQuantity($itemKey, $quantity);
        $check_status = true;
    } elseif ($action == 'remove_item') {
        $itemKey = $_POST['item_key'];
        removeItemCart($itemKey);
        $check_status = true;
    } elseif ($action == 'clear_cart') {
        clearCart();
        $check_status = true;
    } elseif ($action == 'addTms') {

        $tmsID = $_POST['tmsId'];
        $tmsPrice = $_POST['tmsPrice'];

        addTmsCart($tmsID, $tmsPrice);
        $check_status = true;
    }
    
    

    echo json_encode([
        'status' => $check_status,
        'data' => getCartContents(),
        'data2' => getCartOptions()
    ]);
    exit;
}




?>

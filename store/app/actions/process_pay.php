<?php
session_start();
header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');
require_once '../../lib/connect.php';
require_once '../../lib/base_directory.php';

global $base_path;
$response = array('status' => 'error', 'message' => '');


$member_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$orderContents = isset($_SESSION['orderArray']) ? $_SESSION['orderArray'] : array();
$orderID = date('YmdHis');
$orderArray = array();

if(isset($_POST['action']) && $_POST['action'] == 'save_evidence'){

    foreach ($orderContents as $orderCode => $orderDetails) {
        $orderArray[] = array(
            'order_id' => $orderID,
            'order_code' => $orderCode,
            'product_data' => $orderDetails['product_data'],
            'customer_data' => $orderDetails['customer_data'],
            'payment_data' => array(
                'pay_channel' => isset($orderDetails['customer_data']['pay_channel']) ? $orderDetails['customer_data']['pay_channel'] : null
            ),
            'type' => $orderDetails['type'],
            'transport_data' => $orderDetails['transport']
        );
    }

    foreach ($orderArray as $order) {

        $tms_id    = isset($order['transport_data']['tms_id']) ? $order['transport_data']['tms_id'] : null;
        $tms_price = isset($order['transport_data']['tms_price']) ? $order['transport_data']['tms_price'] : null;

        if (!empty($order['customer_data'])) {
            $c = $order['customer_data'];

            $order_id      = mysqli_real_escape_string($conn, $order['order_id']);
            $prefix_id     = mysqli_real_escape_string($conn, $c['prefix']);
            $first_name    = mysqli_real_escape_string($conn, $c['firstname']);
            $last_name     = mysqli_real_escape_string($conn, $c['lastname']);
            $county        = mysqli_real_escape_string($conn, $c['country']);
            $province      = mysqli_real_escape_string($conn, $c['province']);
            $district      = mysqli_real_escape_string($conn, $c['district']);
            $subdistrict   = mysqli_real_escape_string($conn, $c['subdistrict']);
            $post_code     = mysqli_real_escape_string($conn, $c['post_code']);
            $phone_number  = mysqli_real_escape_string($conn, $c['phone_number']);
            $address       = mysqli_real_escape_string($conn, $c['address']);
            $comp_name     = mysqli_real_escape_string($conn, $c['comp_name']);
            $tax_number    = mysqli_real_escape_string($conn, $c['tax_number']);
            $latitude      = mysqli_real_escape_string($conn, $c['inputLatitude']);
            $longitude     = mysqli_real_escape_string($conn, $c['inputLongitude']);
            $pay_type      = mysqli_real_escape_string($conn, $order['type']);
            $vehicle_id    = mysqli_real_escape_string($conn, $tms_id);
            $vehicle_price = mysqli_real_escape_string($conn, $tms_price);

            $ins_shipp_sql = "
                    INSERT INTO `ord_shipping` (
                        `member_id`, `order_id`, `prefix_id`, `first_name`, `last_name`,
                        `phone_number`, `address`, `county`, `province`, `district`,
                        `subdistrict`, `post_code`, `comp_name`, `tax_number`, `pay_type`,
                        `vehicle_id`, `vehicle_price`, `latitude`, `longitude`
                    )
                    VALUES (
                        '$member_id', '$order_id', '$prefix_id', '$first_name', '$last_name',
                        '$phone_number', '$address', '$county', '$province', '$district',
                        '$subdistrict', '$post_code', '$comp_name', '$tax_number', '$pay_type',
                        '$vehicle_id', '$vehicle_price', '$latitude', '$longitude'
                    )
                ";

            if (!mysqli_query($conn, $ins_shipp_sql)) {
                echo "Error inserting shipping: " . mysqli_error($conn);
            }
        }

        if (!empty($order['payment_data'])) {
            $p = $order['payment_data'];

            $pay_channel = mysqli_real_escape_string($conn, $p['pay_channel']);
            $type        = mysqli_real_escape_string($conn, $order['type']);
            $order_id    = mysqli_real_escape_string($conn, $order['order_id']);

            $ins_pay_sql = "
                    INSERT INTO `ord_payment` (`member_id`, `order_id`, `pay_channel`, `type`)
                    VALUES ('$member_id', '$order_id', '$pay_channel', '$type')
                ";

            if (!mysqli_query($conn, $ins_pay_sql)) {
                echo "Error inserting payment: " . mysqli_error($conn);
            }
        }

        foreach ($order['product_data'] as $product) {

            $order_code     = mysqli_real_escape_string($conn, $order['order_code']);
            $order_key      = mysqli_real_escape_string($conn, $product['key_item']);
            $pro_id         = mysqli_real_escape_string($conn, $product['pro_id']);
            $pic            = mysqli_real_escape_string($conn, $product['pic']);
            $price          = floatval($product['price']);
            $quantity       = intval($product['quantity']);
            $total_price    = floatval($product['total_price']);
            $currency       = mysqli_real_escape_string($conn, $product['currency']);
            $pay_type       = mysqli_real_escape_string($conn, $order['type']);
            $vehicle_id     = mysqli_real_escape_string($conn, $tms_id);
            $is_del         = 0;
            $is_status      = 0;
            $qr_pp          = mysqli_real_escape_string($conn, isset($_POST['qrCodeInput']) ? $_POST['qrCodeInput'] : '');

            $ins_order_sql = "
                    INSERT INTO `ecm_orders` (
                        `member_id`, `order_id`, `order_code`, `order_key`, `pro_id`, `pic`,
                        `price`, `quantity`, `total_price`, `currency`, `pay_type`, `vehicle_id`,
                        `is_del`, `is_status`, `qr_pp`
                    ) VALUES (
                        '$member_id', '$order_id', '$order_code', '$order_key', '$pro_id', '$pic',
                        $price, $quantity, $total_price, '$currency', '$pay_type', '$vehicle_id',
                        $is_del, $is_status, '$qr_pp'
                    )
                ";

            if (!mysqli_query($conn, $ins_order_sql)) {
                echo "Error inserting order: " . mysqli_error($conn);
            }
        }

        foreach ($order['product_data'] as $item) {
            $quantity_item = intval($item['quantity']);
            $pro_id_item   = mysqli_real_escape_string($conn, $item['pro_id']);

            $up_product_sql = "
                    UPDATE `ecm_product`
                    SET `stock` = stock - $quantity_item
                    WHERE `material_id` = '$pro_id_item' AND stock >= $quantity_item
                ";

            if (!mysqli_query($conn, $up_product_sql)) {
                echo "Error updating stock: " . mysqli_error($conn);
            }
        }
    }

    $response['status'] = 'success';

}

if(isset($_POST['att_file']) && $_POST['att_file'] == 'save_attach_file'){

    if(isset($_FILES['input-b6b']) && !empty($_FILES['input-b6b'])){

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $maxFileSize = 100 * 1024 * 1024; // 100MB

        if ($member_id && $orderID) {
            foreach ($_FILES['input-b6b']['name'] as $key => $fileName) {
                if ($_FILES['input-b6b']['error'][$key] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $_FILES['input-b6b']['tmp_name'][$key];
                    $fileSize = $_FILES['input-b6b']['size'][$key];
                    $fileType = $_FILES['input-b6b']['type'][$key];
                    $fileNameCmps = explode(".", $fileName);
                    $fileExtension = strtolower(end($fileNameCmps));

                    if (in_array($fileExtension, $allowedExtensions) && $fileSize <= $maxFileSize) {
                        $uploadFileDir = './uploaded_files/';
                        // if (!is_dir($uploadFileDir)) {
                        //     mkdir($uploadFileDir, 775, true);
                        // }

                        $destFilePath = $uploadFileDir . basename($fileName);
                        $picPath = $_SERVER['DOCUMENT_ROOT'] . '/store/app/actions/uploaded_files/' . basename($fileName);

                        if (move_uploaded_file($fileTmpPath, $destFilePath)) {
                            $fileNameEsc = mysqli_real_escape_string($conn, $fileName);
                            $fileTypeEsc = mysqli_real_escape_string($conn, $fileType);
                            $filePathEsc = mysqli_real_escape_string($conn, $destFilePath);
                            $picPathEsc = mysqli_real_escape_string($conn, $picPath);

                            // Insert evidence
                            $sqlInsert = "INSERT INTO ord_evidence (member_id, order_id, file_name, file_size, file_type, file_path, pic_path) 
                                VALUES ('$member_id', '$orderID', '$fileNameEsc', '$fileSize', '$fileTypeEsc', '$filePathEsc', '$picPathEsc')";
                            if (!mysqli_query($conn, $sqlInsert)) {
                                echo "Error inserting file record: " . mysqli_error($conn);
                            }

                            // Update order status
                            $sqlUpdate = "UPDATE ecm_orders
                                SET is_status = '1'
                                WHERE order_id = '$orderID' AND member_id = '$member_id'
                                ";
                            if (!mysqli_query($conn, $sqlUpdate)) {
                                echo "Error updating order status: " . mysqli_error($conn);
                            }
                        }
                    }
                }
            }
        } else {
            echo "Invalid member or order ID.";
        }

    $response['status'] = 'success';

    }else if(isset($_FILES['input-b']) && !empty($_FILES['input-b'])){

        $orderID = $_POST['numberOrder'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $maxFileSize = 100 * 1024 * 1024; // 5MB

        if ($member_id && $orderID) {
            foreach ($_FILES['input-b']['name'] as $key => $fileName) {
                if ($_FILES['input-b']['error'][$key] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $_FILES['input-b']['tmp_name'][$key];
                    $fileSize = $_FILES['input-b']['size'][$key];
                    $fileType = $_FILES['input-b']['type'][$key];
                    $fileNameCmps = explode(".", $fileName);
                    $fileExtension = strtolower(end($fileNameCmps));

                    if (in_array($fileExtension, $allowedExtensions) && $fileSize <= $maxFileSize) {
                        $uploadFileDir = './uploaded_files/';

                        $destFilePath = $uploadFileDir . basename($fileName);
                        $picPath = $_SERVER['DOCUMENT_ROOT'] . '/store/app/actions/uploaded_files/' . basename($fileName);

                        if (move_uploaded_file($fileTmpPath, $destFilePath)) {
                            $fileNameEsc = mysqli_real_escape_string($conn, $fileName);
                            $fileTypeEsc = mysqli_real_escape_string($conn, $fileType);
                            $filePathEsc = mysqli_real_escape_string($conn, $destFilePath);
                            $picPathEsc = mysqli_real_escape_string($conn, $picPath);

                            $checkSQL = "SELECT id FROM ord_evidence WHERE member_id = '$member_id' AND order_id = '$orderID'";
                            $checkResult = mysqli_query($conn, $checkSQL);

                            if (mysqli_num_rows($checkResult) > 0) {
                                $updateSQL = "UPDATE ord_evidence SET 
                                                        file_name = '$fileNameEsc',
                                                        file_size = $fileSize,
                                                        file_type = '$fileTypeEsc',
                                                        file_path = '$filePathEsc',
                                                        pic_path = '$picPathEsc'
                                                    WHERE member_id = '$member_id' AND order_id = '$orderID'";
                                mysqli_query($conn, $updateSQL);
                            } else {

                                $insertSQL = "INSERT INTO ord_evidence 
                                            (member_id, order_id, file_name, file_size, file_type, file_path, pic_path)
                                            VALUES ('$member_id', '$orderID', '$fileNameEsc', $fileSize, '$fileTypeEsc', '$filePathEsc', '$picPathEsc')";
                                mysqli_query($conn, $insertSQL);
                            }

                            $updateStatusSQL = "UPDATE ecm_orders SET is_status = '1' 
                                                        WHERE order_id = '$orderID' AND member_id = '$member_id'";
                            mysqli_query($conn, $updateStatusSQL);

                            // // Insert evidence
                            // $sqlInsert = "INSERT INTO ord_evidence (member_id, order_id, file_name, file_size, file_type, file_path, pic_path) 
                            // VALUES ('$member_id', '$orderID', '$fileNameEsc', '$fileSize', '$fileTypeEsc', '$filePathEsc', '$picPathEsc')";
                            // if (!mysqli_query($conn, $sqlInsert)) {
                            //     echo "Error inserting file record: " . mysqli_error($conn);
                            // }

                            // // Update order status
                            // $sqlUpdate = "UPDATE ecm_orders
                            // SET is_status = '1'
                            // WHERE order_id = '$orderID' AND member_id = '$member_id'
                            // ";

                            if (!mysqli_query($conn, $sqlUpdate)) {
                                echo "Error updating order status: " . mysqli_error($conn);
                            }

                        }
                    }
                }
            }
        } else {
            echo "Invalid member or order ID.";
        }

        $response['status'] = 'success';
    }

}

unset($_SESSION['cart'], $_SESSION['orderArray'], $_SESSION['cartOption']);
if (empty($orderArray)) {
    $response['status'] = 'error';
} else {
    $response['status'] = 'success';
}
echo json_encode($response);

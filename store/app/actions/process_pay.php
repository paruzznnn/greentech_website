<?php
session_start();
header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');
require_once '../../lib/connect.php';
require_once '../../lib/base_directory.php';

global $base_path;
$response = array('status' => 'error', 'message' => '');

try {

    if(isset($_POST['action']) && $_POST['action'] == 'save_evidence'){

        $orderContents = isset($_SESSION['orderArray']) ? $_SESSION['orderArray'] : array();
        $orderID = date('YmdHis');
        $orderArray = array();

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

        // foreach ($orderArray as $order) {

        //     $tms_id = isset($order['transport_data']['tms_id']) ? $order['transport_data']['tms_id'] : null;
        //     $tms_price = isset($order['transport_data']['tms_price']) ? $order['transport_data']['tms_price'] : null;

        //     if ($order['customer_data']) {
        //         $c = $order['customer_data'];

        //         $member_id     = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        //         $order_id      = mysqli_real_escape_string($conn, isset($order['order_id']) ? $order['order_id'] : null);
        //         $prefix_id     = mysqli_real_escape_string($conn, isset($c['prefix']) ? $c['prefix'] : null);
        //         $first_name    = mysqli_real_escape_string($conn, isset($c['firstname']) ? $c['firstname'] : null);
        //         $last_name     = mysqli_real_escape_string($conn, isset($c['lastname']) ? $c['lastname'] : null);
        //         $county        = mysqli_real_escape_string($conn, isset($c['country']) ? $c['country'] : null);
        //         $province      = mysqli_real_escape_string($conn, isset($c['province']) ? $c['province'] : null);
        //         $district      = mysqli_real_escape_string($conn, isset($c['district']) ? $c['district'] : null);
        //         $subdistrict   = mysqli_real_escape_string($conn, isset($c['subdistrict']) ? $c['subdistrict'] : null);
        //         $post_code     = mysqli_real_escape_string($conn, isset($c['post_code']) ? $c['post_code'] : null);
        //         $phone_number  = mysqli_real_escape_string($conn, isset($c['phone_number']) ? $c['phone_number'] : null);
        //         $address       = mysqli_real_escape_string($conn, isset($c['address']) ? $c['address'] : null);
        //         $comp_name     = mysqli_real_escape_string($conn, isset($c['comp_name']) ? $c['comp_name'] : null);
        //         $tax_number    = mysqli_real_escape_string($conn, isset($c['tax_number']) ? $c['tax_number'] : null);
        //         $latitude      = mysqli_real_escape_string($conn, isset($c['inputLatitude']) ? $c['inputLatitude'] : null);
        //         $longitude     = mysqli_real_escape_string($conn, isset($c['inputLongitude']) ? $c['inputLongitude'] : null);
        //         $pay_type      = mysqli_real_escape_string($conn, isset($order['type']) ? $order['type'] : null);
        //         $vehicle_id    = mysqli_real_escape_string($conn, $tms_id);
        //         $vehicle_price = mysqli_real_escape_string($conn, $tms_price);

        //         $ins_shipp_sql = "INSERT INTO `ord_shipping`(`member_id`, `order_id`, `prefix_id`, `first_name`, `last_name`, `phone_number`, `address`, `county`, `province`, `district`, `subdistrict`, `post_code`, `comp_name`, `tax_number`, `pay_type`, `vehicle_id`, `vehicle_price`, `latitude`, `longitude`)
        //         VALUES ('$member_id', '$order_id', '$prefix_id', '$first_name', '$last_name', '$phone_number', '$address', '$county', '$province', '$district', '$subdistrict', '$post_code', '$comp_name', '$tax_number', '$pay_type', '$vehicle_id', '$vehicle_price', '$latitude', '$longitude')";
        //         mysqli_query($conn, $ins_shipp_sql);
        //     }

        //     if ($order['payment_data']) {
        //         $p = $order['payment_data'];

        //         $member_id   = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        //         $order_id    = mysqli_real_escape_string($conn, isset($order['order_id']) ? $order['order_id'] : null);
        //         $pay_channel = mysqli_real_escape_string($conn, isset($p['pay_channel']) ? $p['pay_channel'] : null);
        //         $type        = mysqli_real_escape_string($conn, isset($order['type']) ? $order['type'] : null);

        //         $ins_pay_sql = "INSERT INTO ord_payment (member_id, order_id, pay_channel, `type`) 
        //         VALUES ('$member_id', '$order_id', '$pay_channel', '$type')";
        //         mysqli_query($conn, $ins_pay_sql);
        //     }

        //     foreach ($order['product_data'] as $product) {

        //         $member_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        //         $order_id       = mysqli_real_escape_string($conn, $order['order_id']);
        //         $order_code     = mysqli_real_escape_string($conn, $order['order_code']);
        //         $order_key      = mysqli_real_escape_string($conn, $product['key_item']);
        //         $pro_id         = mysqli_real_escape_string($conn, $product['pro_id']);
        //         $pic            = mysqli_real_escape_string($conn, $product['pic']);
        //         $price          = floatval($product['price']);
        //         $quantity       = intval($product['quantity']);
        //         $total_price    = floatval($product['total_price']);
        //         $currency       = mysqli_real_escape_string($conn, $product['currency']);
        //         $pay_type       = mysqli_real_escape_string($conn, $order['type']);
        //         $vehicle_id     = mysqli_real_escape_string($conn, isset($tms_id) ? $tms_id : null);
        //         $is_del         = 0;
        //         $is_status      = 0;
        //         // $created_at     = date("Y-m-d H:i:s");
        //         $qr_pp          = mysqli_real_escape_string($conn, isset($_POST['qrCodeInput']) ? $_POST['qrCodeInput'] : '');

        //         $ins_order_sql = "
        //             INSERT INTO `ecm_orders` (
        //                 `member_id`, `order_id`, `order_code`, `order_key`, `pro_id`, `pic`, 
        //                 `price`, `quantity`, `total_price`, `currency`, `pay_type`, `vehicle_id`, 
        //                 `is_del`, `is_status`, `qr_pp`
        //             ) VALUES (
        //                 '$member_id', '$order_id', '$order_code', '$order_key', '$pro_id', '$pic',
        //                 $price, $quantity, $total_price, '$currency', '$pay_type', '$vehicle_id',
        //                 $is_del, $is_status, '$qr_pp'
        //             )";

        //         mysqli_query($conn, $ins_order_sql);
        //     }

        //     foreach ($order['product_data'] as $item) {
        //         $quantity_item = intval($item['quantity']); 
        //         $pro_id_item = mysqli_real_escape_string($conn, $item['pro_id']);
        //         $up_product_sql = "UPDATE ecm_product SET stock = stock - $quantity_item WHERE material_id = '$pro_id_item'";
        //         mysqli_query($conn, $up_product_sql);
        //     }
        // }

        $member_id = $_SESSION['user_id'];

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

                // ป้องกัน stock < 0
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

        if (isset($_POST['att_file']) && $_POST['att_file'] == 'save_attach_file' && isset($_FILES['input-b6b'])) {
            $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif', 'pdf');
            $maxFileSize = 5 * 1024 * 1024;

            foreach ($_FILES['input-b6b']['name'] as $key => $fileName) {
                if ($_FILES['input-b6b']['error'][$key] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $_FILES['input-b6b']['tmp_name'][$key];
                    $fileSize = $_FILES['input-b6b']['size'][$key];
                    $fileType = $_FILES['input-b6b']['type'][$key];
                    $fileNameCmps = explode(".", $fileName);
                    $fileExtension = strtolower(end($fileNameCmps));

                    if (in_array($fileExtension, $allowedExtensions) && $fileSize <= $maxFileSize) {
                        $uploadFileDir = './uploaded_files/';
                        $destFilePath = $uploadFileDir . $fileName;

                        if (!is_dir($uploadFileDir)) {
                            mkdir($uploadFileDir, 0755, true);
                        }

                        if (move_uploaded_file($fileTmpPath, $destFilePath)) {
                            $picPath = 'app/actions/uploaded_files/' . $fileName;

                            // escape 
                            $member_id   = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
                            $fileNameEsc = mysqli_real_escape_string($conn, $fileName);
                            $fileTypeEsc = mysqli_real_escape_string($conn, $fileType);
                            $filePathEsc = mysqli_real_escape_string($conn, $destFilePath);
                            $picPathEsc  = mysqli_real_escape_string($conn, $picPath);

                            // ins ord_evidence
                            $sqlInsert = "INSERT INTO ord_evidence (member_id, order_id, file_name, file_size, file_type, file_path, pic_path)
                                        VALUES ($member_id, $orderID, '$fileNameEsc', $fileSize, '$fileTypeEsc', '$filePathEsc', '$picPathEsc')";
                            mysqli_query($conn, $sqlInsert);

                            // update order
                            $sqlUpdate = "UPDATE ecm_orders SET is_status = '1' WHERE order_id = $orderID AND member_id = $member_id";
                            mysqli_query($conn, $sqlUpdate);
                        } 
                    }
                }
            }
        }

        unset($_SESSION['cart'], $_SESSION['orderArray'], $_SESSION['cartOption']);
        $response = array('status' => 'success');
        throw new Exception("action save evidence.");

    }else if(isset($_POST['att_file']) && $_POST['att_file'] == 'save_attach_file' && isset($_FILES['input-b'])){
    

        $orderID = $_POST['numberOrder'];
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif', 'pdf');
        $maxFileSize = 5 * 1024 * 1024;

        foreach ($_FILES['input-b']['name'] as $key => $fileName) {
            if ($_FILES['input-b']['error'][$key] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['input-b']['tmp_name'][$key];
                $fileSize = $_FILES['input-b']['size'][$key];
                $fileType = $_FILES['input-b']['type'][$key];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                if (in_array($fileExtension, $allowedExtensions) && $fileSize <= $maxFileSize) {
                    $uploadFileDir = './uploaded_files/';
                    $destFilePath = $uploadFileDir . $fileName;

                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }

                    if (move_uploaded_file($fileTmpPath, $destFilePath)) {
                        $picPath = 'app/actions/uploaded_files/' . $fileName;

                        $fileNameEsc = mysqli_real_escape_string($conn, $fileName);
                        $fileTypeEsc = mysqli_real_escape_string($conn, $fileType);
                        $filePathEsc = mysqli_real_escape_string($conn, $destFilePath);
                        $picPathEsc = mysqli_real_escape_string($conn, $picPath);
                        $orderIDEsc = mysqli_real_escape_string($conn, $orderID);
                        $memberIDEsc = mysqli_real_escape_string($conn, $member_id);

                        $checkSQL = "SELECT id FROM ord_evidence WHERE member_id = '$memberIDEsc' AND order_id = '$orderIDEsc'";
                        $checkResult = mysqli_query($conn, $checkSQL);

                        if (mysqli_num_rows($checkResult) > 0) {
                            $updateSQL = "UPDATE ord_evidence SET 
                                            file_name = '$fileNameEsc',
                                            file_size = $fileSize,
                                            file_type = '$fileTypeEsc',
                                            file_path = '$filePathEsc',
                                            pic_path = '$picPathEsc'
                                        WHERE member_id = '$memberIDEsc' AND order_id = '$orderIDEsc'";
                            mysqli_query($conn, $updateSQL);
                        } else {
                        
                            $insertSQL = "INSERT INTO ord_evidence 
                                (member_id, order_id, file_name, file_size, file_type, file_path, pic_path)
                                VALUES ('$memberIDEsc', '$orderIDEsc', '$fileNameEsc', $fileSize, '$fileTypeEsc', '$filePathEsc', '$picPathEsc')";
                            mysqli_query($conn, $insertSQL);
                        }

                        $updateStatusSQL = "UPDATE ecm_orders SET is_status = '1' 
                                            WHERE order_id = '$orderIDEsc' AND member_id = '$memberIDEsc'";
                        mysqli_query($conn, $updateStatusSQL);

                    }
                } 
            } 
        }

        $response = array('status' => 'success');
        throw new Exception("action save attach file.");
    }else{
        $response = array('status' => 'error');
        throw new Exception("Invalid request or missing parameters.");
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    echo json_encode($response);
}


<?php
session_start();
header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');
require_once '../../lib/connect.php';
require_once '../../lib/base_directory.php';

global $base_path;
$response = array('status' => 'success', 'message' => '', 'steps' => array());

function handleFileUpload($files) {
    $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif', 'pdf');
    $maxFileSize = 5 * 1024 * 1024;

    $uploadResults = array();

    foreach ($files['name'] as $key => $fileName) {
        if ($files['error'][$key] === UPLOAD_ERR_OK) {
            $fileTmpPath = $files['tmp_name'][$key];
            $fileSize = $files['size'][$key];
            $fileType = $files['type'][$key];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            
            if (in_array($fileExtension, $allowedExtensions) && $fileSize <= $maxFileSize) {
                $uploadFileDir = './uploaded_files/';
                $destFilePath = $uploadFileDir . $fileName;

                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }

                if (move_uploaded_file($fileTmpPath, $destFilePath)) {
                    $uploadResults[] = array(
                        'success' => true,
                        'fileName' => $fileName,
                        'fileSize' => $fileSize,
                        'fileType' => $fileType,
                        'filePath' => $destFilePath
                    );
                } else {
                    $uploadResults[] = array(
                        'success' => false,
                        'fileName' => $fileName,
                        'error' => 'Error occurred while moving the uploaded file.'
                    );
                }
            } else {
                $uploadResults[] = array(
                    'success' => false,
                    'fileName' => $fileName,
                    'error' => 'Invalid file type or file size exceeds limit.'
                );
            }
        } else {
            $uploadResults[] = array(
                'success' => false,
                'fileName' => $fileName,
                'error' => 'No file uploaded or there was an upload error.'
            );
        }
    }

    return $uploadResults;
}

function insertIntoDatabase($conn, $table, $columns, $values) {
    $placeholders = implode(', ', array_fill(0, count($values), '?'));
    $query = "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES ($placeholders)";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $types = '';
    foreach ($values as $val) {
        $types .= (is_int($val) ? 'i' : (is_float($val) ? 'd' : 's'));
    }

    $bindNames[] = $types;
    for ($i = 0; $i < count($values); $i++) {
        $bindName = 'bind' . $i;
        $$bindName = $values[$i];
        $bindNames[] = &$$bindName;
    }

    call_user_func_array(array($stmt, 'bind_param'), $bindNames);

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $stmt->close();
}

function updateInDatabase($conn, $table, $columns, $values, $whereClause, $whereValues) {
    $setPart = implode(', ', array_map(function($col) {
        return "$col = ?";
    }, $columns));

    $query = "UPDATE $table SET $setPart WHERE $whereClause";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $allValues = array_merge($values, $whereValues);
    $types = str_repeat('s', count($allValues));

    $bindNames[] = $types;
    for ($i = 0; $i < count($allValues); $i++) {
        $bindName = 'bind' . $i;
        $$bindName = $allValues[$i];
        $bindNames[] = &$$bindName;
    }

    call_user_func_array(array($stmt, 'bind_param'), $bindNames);

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $stmt->close();
}

try {
    $member_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    if (!$member_id) throw new Exception("User not authenticated.");

    if (isset($_POST['action']) && $_POST['action'] === 'save_evidence') {
        $response['steps'][] = 'Processing cart and order contents';
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

        $conn->begin_transaction();
        $response['steps'][] = 'Transaction started';

        foreach ($orderArray as $order) {
            $tms_id = isset($order['transport_data']['tms_id']) ? $order['transport_data']['tms_id'] : null;
            $tms_price = isset($order['transport_data']['tms_price']) ? $order['transport_data']['tms_price'] : null;

            foreach ($order['product_data'] as $product) {
                $orderValues = array(
                    $member_id, $order['order_id'], $order['order_code'],
                    $product['pro_id'], $product['pic'], $product['price'],
                    $product['quantity'], $product['total_price'], $product['key_item'],
                    $product['currency'], $order['type'], $tms_id,
                    isset($_POST['qrCodeInput']) ? $_POST['qrCodeInput'] : null
                );

                insertIntoDatabase($conn, 'ecm_orders',
                    array('member_id', 'order_id', 'order_code', 'pro_id', 'pic', 'price', 'quantity', 'total_price', 'order_key', 'currency', 'pay_type', 'vehicle_id', 'qr_pp'),
                    $orderValues
                );
            }

            foreach ($order['product_data'] as $item) {
                $sqlUpdate = "UPDATE ecm_product SET stock = stock - ? WHERE material_id = ?";
                $stmt = $conn->prepare($sqlUpdate);
                $stmt->bind_param("is", $item['quantity'], $item['pro_id']);
                $stmt->execute();
                $stmt->close();
            }

            if (!empty($order['customer_data'])) {
                $c = $order['customer_data'];
                insertIntoDatabase($conn, 'ord_shipping',
                    array('member_id', 'order_id', 'prefix_id', 'first_name', 'last_name', 'county', 'province', 'district', 'subdistrict', 'post_code', 'phone_number', 'address', 'comp_name', 'tax_number', 'latitude', 'longitude', 'pay_type', 'vehicle_id', 'vehicle_price'),
                    array($member_id, $order['order_id'], isset($c['prefix']) ? $c['prefix'] : '', $c['firstname'], $c['lastname'], $c['country'], isset($c['province']) ? $c['province'] : '', isset($c['district']) ? $c['district'] : '', isset($c['subdistrict']) ? $c['subdistrict'] : '', isset($c['post_code']) ? $c['post_code'] : '', $c['phone_number'], $c['address'], $c['comp_name'], $c['tax_number'], $c['inputLatitude'], $c['inputLongitude'], $order['type'], $tms_id, $tms_price)
                );
            }

            if (!empty($order['payment_data'])) {
                $p = $order['payment_data'];
                insertIntoDatabase($conn, 'ord_payment',
                    array('member_id', 'order_id', 'pay_channel', 'type'),
                    array($member_id, $order['order_id'], $p['pay_channel'], $order['type'])
                );
            }
        }

        if (isset($_POST['att_file']) && $_POST['att_file'] === 'save_attach_file' && isset($_FILES['input-b6b'])) {
            $response['steps'][] = 'Handling file upload';
            foreach (handleFileUpload($_FILES['input-b6b']) as $fileInfo) {
                if (!$fileInfo['success']) {
                    throw new Exception('Upload failed: ' . $fileInfo['fileName']);
                }

                $picPath = 'app/actions/uploaded_files/' . $fileInfo['fileName'];
                $fileColumns = array('member_id', 'order_id', 'file_name', 'file_size', 'file_type', 'file_path', 'pic_path');
                $fileValues = array($member_id, $orderID, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath);
                insertIntoDatabase($conn, 'ord_evidence', $fileColumns, $fileValues);

                updateInDatabase($conn, 'ecm_orders', array('is_status'), array('1'), 'order_id = ? AND member_id = ?', array($orderID, $member_id));
            }
        }

        $conn->commit();
        $response['steps'][] = 'Transaction committed';
        unset($_SESSION['cart'], $_SESSION['orderArray'], $_SESSION['cartOption']);
        $response['message'] = 'Order and evidence saved successfully.';

    } elseif (isset($_POST['att_file']) && $_POST['att_file'] === 'save_attach_file' && isset($_FILES['input-b'])) {
        $orderID = $_POST['numberOrder'];
        $response['steps'][] = 'File upload for existing order';

        foreach (handleFileUpload($_FILES['input-b']) as $fileInfo) {
            if (!$fileInfo['success']) {
                throw new Exception('Upload failed: ' . $fileInfo['fileName']);
            }

            $picPath = 'app/actions/uploaded_files/' . $fileInfo['fileName'];
            $stmt = $conn->prepare("SELECT id FROM ord_evidence WHERE member_id = ? AND order_id = ?");
            $stmt->bind_param("is", $member_id, $orderID);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                updateInDatabase($conn, 'ord_evidence',
                    array('file_name', 'file_size', 'file_type', 'file_path', 'pic_path'),
                    array($fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath),
                    'order_id = ? AND member_id = ?', array($orderID, $member_id));
            } else {
                insertIntoDatabase($conn, 'ord_evidence',
                    array('member_id', 'order_id', 'file_name', 'file_size', 'file_type', 'file_path', 'pic_path'),
                    array($member_id, $orderID, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath));
            }

            updateInDatabase($conn, 'ecm_orders', array('is_status'), array('1'), 'order_id = ? AND member_id = ?', array($orderID, $member_id));
        }

        $response['message'] = 'File evidence saved.';
    } else {
        throw new Exception("Invalid request or missing parameters.");
    }

} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
}

echo json_encode($response);

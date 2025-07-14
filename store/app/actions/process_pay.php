<?php
session_start();
header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');
require_once '../../lib/connect.php';
require_once '../../lib/base_directory.php';


global $base_path;
$response = array('status' => 'success', 'message' => '', 'steps' => []);

function handleFileUpload($files) {

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
    $maxFileSize = 5 * 1024 * 1024; 

    $uploadResults = [];

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
                    $uploadResults[] = [
                        'success' => true,
                        'fileName' => $fileName,
                        'fileSize' => $fileSize,
                        'fileType' => $fileType,
                        'filePath' => $destFilePath
                    ];
                } else {
                    $uploadResults[] = [
                        'success' => false,
                        'fileName' => $fileName,
                        'error' => 'Error occurred while moving the uploaded file.'
                    ];
                }
            } else {
                $uploadResults[] = [
                    'success' => false,
                    'fileName' => $fileName,
                    'error' => 'Invalid file type or file size exceeds limit.'
                ];
            }
        } else {
            $uploadResults[] = [
                'success' => false,
                'fileName' => $fileName,
                'error' => 'No file uploaded or there was an upload error.'
            ];
        }
    }

    return $uploadResults;
}

function insertIntoDatabase($conn, $table, $columns, $values) {
    $placeholders = implode(', ', array_fill(0, count($values), '?'));
    $query = "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES ($placeholders)";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }

    $types = '';
    foreach ($values as $val) {
        if (is_int($val)) {
            $types .= 'i';
        } elseif (is_float($val)) {
            $types .= 'd';
        } elseif (is_null($val)) {
            $types .= 's'; 
        } else {
            $types .= 's';
        }
    }

    $stmt->bind_param($types, ...$values);

    if (!$stmt->execute()) {
        throw new Exception("Execute statement failed: " . $stmt->error);
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
        throw new Exception("Prepare statement failed: " . $conn->error);
    }

    $types = str_repeat('s', count($values)) . str_repeat('s', count($whereValues));
    $stmt->bind_param($types, ...array_merge($values, $whereValues));

    if (!$stmt->execute()) {
        throw new Exception("Execute statement failed: " . $stmt->error);
    }

    $stmt->close();
}


try {
    $member_id = $_SESSION['user_id'] ?? null;
    if (!$member_id) throw new Exception("User not authenticated.");

    if (isset($_POST['action']) && $_POST['action'] === 'save_evidence') {

        $response['steps'][] = 'Processing cart and order contents';
        $orderContents = $_SESSION['orderArray'] ?? [];

        $orderID = date('YmdHis'); 
        $orderArray = [];

        foreach ($orderContents as $orderCode => $orderDetails) {
            $orderArray[] = [
                'order_id' => $orderID,
                'order_code' => $orderCode,
                'product_data' => $orderDetails['product_data'],
                'customer_data' => $orderDetails['customer_data'],
                'payment_data' => [
                    'pay_channel' => $orderDetails['customer_data']['pay_channel'] ?? null
                ],
                'type' => $orderDetails['type'],
                'transport_data' => $orderDetails['transport']
            ];
        }

        $conn->begin_transaction();
        $response['steps'][] = 'Transaction started';

        foreach ($orderArray as $order) {
            $tms_id = $order['transport_data']['tms_id'] ?? null;
            $tms_price = $order['transport_data']['tms_price'] ?? null;

            // Save products
            foreach ($order['product_data'] as $product) {
                $orderValues = [
                    $member_id, $order['order_id'], $order['order_code'],
                    $product['pro_id'], $product['pic'], $product['price'],
                    $product['quantity'], $product['total_price'], $product['key_item'],
                    $product['currency'], $order['type'], $tms_id,
                    $_POST['qrCodeInput'] ?? null
                ];

                insertIntoDatabase($conn, 'ecm_orders', 
                    ['member_id', 'order_id', 'order_code', 'pro_id', 'pic', 'price', 'quantity', 'total_price', 'order_key', 'currency', 'pay_type', 'vehicle_id', 'qr_pp'], 
                    $orderValues
                );
            }

            // Update stock
            foreach ($order['product_data'] as $item) {
                $sqlUpdate = "UPDATE ecm_product SET stock = stock - ? WHERE material_id = ?";
                $stmt = $conn->prepare($sqlUpdate);
                $stmt->bind_param("is", $item['quantity'], $item['pro_id']);
                $stmt->execute();
                $stmt->close();
            }

            // Shipping
            if (!empty($order['customer_data'])) {
                $c = $order['customer_data'];
                insertIntoDatabase($conn, 'ord_shipping', 
                    ['member_id', 'order_id', 'prefix_id', 'first_name', 'last_name', 'county', 'province', 'district', 'subdistrict', 'post_code', 'phone_number', 'address', 'comp_name', 'tax_number', 'latitude', 'longitude', 'pay_type', 'vehicle_id', 'vehicle_price'], 
                    [$member_id, $order['order_id'], $c['prefix'] ?? '', $c['firstname'], $c['lastname'], $c['country'], $c['province'] ?? '', $c['district'] ?? '', $c['subdistrict'] ?? '', $c['post_code'] ?? '', $c['phone_number'], $c['address'], $c['comp_name'], $c['tax_number'], $c['inputLatitude'], $c['inputLongitude'], $order['type'], $tms_id, $tms_price]
                );
            }

            // Payment
            if (!empty($order['payment_data'])) {
                $p = $order['payment_data'];
                insertIntoDatabase($conn, 'ord_payment', 
                    ['member_id', 'order_id', 'pay_channel', 'type'], 
                    [$member_id, $order['order_id'], $p['pay_channel'], $order['type']]
                );
            }
        }

        // File upload
        if ($_POST['att_file'] === 'save_attach_file' && isset($_FILES['input-b6b'])) {
            $response['steps'][] = 'Handling file upload';
            foreach (handleFileUpload($_FILES['input-b6b']) as $fileInfo) {
                if (!$fileInfo['success']) {
                    throw new Exception('Upload failed: ' . $fileInfo['fileName']);
                }

                $picPath = 'app/actions/uploaded_files/' . $fileInfo['fileName'];
                $fileColumns = ['member_id', 'order_id', 'file_name', 'file_size', 'file_type', 'file_path', 'pic_path'];
                $fileValues = [$member_id, $orderID, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath];
                insertIntoDatabase($conn, 'ord_evidence', $fileColumns, $fileValues);

                updateInDatabase($conn, 'ecm_orders', ['is_status'], ['1'], 'order_id = ? AND member_id = ?', [$orderID, $member_id]);
            }
        }

        $conn->commit();
        $response['steps'][] = 'Transaction committed';

        unset($_SESSION['cart'], $_SESSION['orderArray'], $_SESSION['cartOption']);
        $response['message'] = 'Order and evidence saved successfully.';

    } elseif ($_POST['att_file'] === 'save_attach_file' && isset($_FILES['input-b'])) {
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
            $result = $stmt->get_result();
            $exists = $result->fetch_assoc();
            $stmt->close();

            if ($exists) {
                // Update existing evidence
                updateInDatabase($conn, 'ord_evidence', ['file_name', 'file_size', 'file_type', 'file_path', 'pic_path'], [$fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath], 'order_id = ? AND member_id = ?', [$orderID, $member_id]);
            } else {
                // Insert new evidence
                insertIntoDatabase($conn, 'ord_evidence', ['member_id', 'order_id', 'file_name', 'file_size', 'file_type', 'file_path', 'pic_path'], [$member_id, $orderID, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath]);
            }

            updateInDatabase($conn, 'ecm_orders', ['is_status'], ['1'], 'order_id = ? AND member_id = ?', [$orderID, $member_id]);
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
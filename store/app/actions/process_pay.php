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

    if (isset($_POST['action']) && $_POST['action'] === 'save_evidence') {

        $member_id = $_SESSION['user_id'];
        $response['steps'][] = 'Processing cart and order contents';
        $orderContents = isset($_SESSION['orderArray']) ? $_SESSION['orderArray'] : [];

        $orderArray = [];
        foreach ($orderContents as $orderCode => $orderDetails) {
            $orderID = date('YmdHis'); 
            $orderArray[] = [
                'order_id' => $orderID,
                'order_code' => $orderCode,
                'product_data' => $orderDetails['product_data'],
                'customer_data' => $orderDetails['customer_data'],
                'payment_data' => [
                    'pay_channel' => $orderDetails['customer_data']['pay_channel']
                ],
                'type' => $orderDetails['type'],
                'transport_data' => $orderDetails['transport']
            ];
        }
        
        $response['steps'][] = 'Beginning transaction';
        $conn->begin_transaction();
        
        try {
            foreach ($orderArray as $order) {

                $tms_id = $order['transport_data']['tms_id'] ?? null;
                $tms_price = $order['transport_data']['tms_price'] ?? null;

                foreach ($order['product_data'] as $product) {
                    $orderValues = [
                        $member_id,
                        $order['order_id'] ?? null,
                        $order['order_code'] ?? null,
                        $product['pro_id'] ?? null,
                        $product['pic'] ?? null,
                        $product['price'] ?? null,
                        $product['quantity'] ?? null,
                        $product['total_price'] ?? null,
                        $product['key_item'] ?? null,
                        $product['currency'] ?? null,
                        $order['type'] ?? null,
                        $tms_id,
                        $_POST['qrCodeInput'] ?? null
                    ];
        
                    insertIntoDatabase($conn, 'ecm_orders', 
                        ['member_id', 'order_id', 'order_code', 'pro_id', 'pic', 'price',
                        'quantity', 'total_price', 'order_key', 'currency', 'pay_type', 'vehicle_id', 'qr_pp'], 
                        $orderValues
                    );
                }

                foreach ($order['product_data'] as $item) {
        
                    $sqlUpdate = "UPDATE ecm_product SET stock = stock - ? WHERE material_id = ?";
                    $updateStmt = $conn->prepare($sqlUpdate);
                    $updateStmt->bind_param("is", $item['quantity'], $item['pro_id']);
                    $updateStmt->execute();
                    $updateStmt->close();
                }
        
                if (isset($order['customer_data'])) {
                    $customer = $order['customer_data'];
                    insertIntoDatabase($conn, 'ord_shipping', 
                        ['member_id', 'order_id', 'prefix_id', 'first_name', 'last_name', 
                        'county', 'province', 'district', 'subdistrict', 'post_code', 'phone_number', 'address', 
                        'comp_name', 'tax_number', 'latitude', 'longitude', 'pay_type', 'vehicle_id', 'vehicle_price'], 
                        [
                            $member_id,
                            $order['order_id'] ?? null,
                            $customer['prefix'] ?? '',
                            $customer['firstname'] ?? null,
                            $customer['lastname'] ?? null,
                            $customer['country'] ?? null,
                            $customer['province'] ?? '',
                            $customer['district'] ?? '',
                            $customer['subdistrict'] ?? '',
                            $customer['post_code'] ?? '',
                            $customer['phone_number'] ?? null,
                            $customer['address'] ?? null,
                            $customer['comp_name'] ?? null,
                            $customer['tax_number'] ?? null,
                            $customer['inputLatitude'] ?? null,
                            $customer['inputLongitude'] ?? null,
                            $order['type'] ?? null,
                            $tms_id,
                            $tms_price
                        ]
                    );
                }
                if (isset($order['payment_data'])) {
                    $payment = $order['payment_data'];
                    insertIntoDatabase($conn, 'ord_payment', 
                        ['member_id', 'order_id', 'pay_channel', 'type'], 
                        [
                            $member_id,
                            $order['order_id'] ?? null,
                            $payment['pay_channel'] ?? null,
                            $order['type'] ?? null
                        ]
                    );
                }
            }

            if (isset($_POST['att_file']) && $_POST['att_file'] === 'save_attach_file') {
                $member_id = $_SESSION['user_id'];
                $response['steps'][] = 'Handling file uploads';
                $fileInfos = handleFileUpload($_FILES['input-b6b']); 
                foreach ($fileInfos as $fileInfo) {
                    if ($fileInfo['success']) {
                        $picPath = $base_path .'tdi_store/app/actions/uploaded_files/'.$fileInfo['fileName'];
                        $orderID = date('YmdHis');
                        $fileColumns = ['member_id', 'order_id', 'file_name', 'file_size', 'file_type', 'file_path', 'pic_path'];
                        $fileValues = [$member_id, $orderID, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath];
                        insertIntoDatabase($conn, 'ord_evidence', $fileColumns, $fileValues);
                        $orderColumns = ['is_status'];
                        $orderValues = ['1'];
                        $orderWhereClause = 'order_id = ? AND member_id = ?';
                        $orderWhereValues = [$orderID, $member_id];
                        updateInDatabase($conn, 'ecm_orders', $orderColumns, $orderValues, $orderWhereClause, $orderWhereValues);
                    } else {
                        throw new Exception('Error uploading file: ' . $fileInfo['fileName'] . ' - ' . $fileInfo['error']);
                    }
                }
            }

            $response['steps'][] = 'Committing transaction';
            $conn->commit();

            unset($_SESSION['cart']);
            unset($_SESSION['orderArray']);
            unset($_SESSION['cartOption']);

            $response['message'] = 'Operation completed successfully';
        
        } catch (Exception $e) {
            $response['steps'][] = 'Rolling back transaction';
            $conn->rollback();
            throw $e;
        }
    }else{

        if (isset($_POST['att_file']) && $_POST['att_file'] === 'save_attach_file') {

            $member_id = $_SESSION['user_id'];
            $orderID = strval($_POST['numberOrder']);

            $response['steps'][] = 'Handling file uploads';
            $fileInfos = handleFileUpload($_FILES['input-b']); 
            foreach ($fileInfos as $fileInfo) {
                if ($fileInfo['success']) {

                    $stmt = $conn->prepare("SELECT id, order_id, member_id FROM ord_evidence WHERE member_id = ? AND order_id = ?");
                    $stmt->bind_param("is", $member_id, $orderID);
                    
                    if (!$stmt->execute()) {
                        throw new Exception("Execute statement failed: " . $stmt->error);
                    }

                    $result = $stmt->get_result();
                    $data = $result->fetch_all(MYSQLI_ASSOC);

                        if(empty($data[0])){

                            $picPath = $base_path .'tdi_store/app/actions/uploaded_files/'.$fileInfo['fileName'];

                            $fileColumns = ['member_id', 'order_id', 'file_name', 'file_size', 'file_type', 'file_path', 'pic_path'];
                            $fileValues = [$member_id, $orderID, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath];
                            insertIntoDatabase($conn, 'ord_evidence', $fileColumns, $fileValues);

                            $orderColumns = ['is_status'];
                            $orderValues = ['1'];

                            $orderWhereClause = 'order_id = ? AND member_id = ?';
                            $orderWhereValues = [$orderID, $member_id];
        
                            updateInDatabase($conn, 'ecm_orders', $orderColumns, $orderValues, $orderWhereClause, $orderWhereValues);
                            
                        }else{

                            $picPath = $base_path .'tdi_store/app/actions/uploaded_files/'.$fileInfo['fileName'];

                            $fileColumns = ['file_name', 'file_size', 'file_type', 'file_path', 'pic_path'];
                            $fileValues = [$fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath];
                            $whereClause = 'order_id = ? AND member_id = ?';
                            $whereValues = [$orderID, $member_id];

                            updateInDatabase($conn, 'ord_evidence', $fileColumns, $fileValues, $whereClause, $whereValues);
                            $orderColumns = ['is_status'];
                            $orderValues = ['1'];
                            $orderWhereClause = 'order_id = ? AND member_id = ?';
                            $orderWhereValues = [$orderID, $member_id];
                            updateInDatabase($conn, 'ecm_orders', $orderColumns, $orderValues, $orderWhereClause, $orderWhereValues);
                            
                        }

                } else {
                    throw new Exception('Error uploading file: ' . $fileInfo['fileName'] . ' - ' . $fileInfo['error']);
                }
            }
        }

    }
    

} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
} finally {
    echo json_encode($response);
}

<?php
session_start();
header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');
require_once(__DIR__ . '/../../../../lib/base_directory.php');
require_once(__DIR__ . '/../../../../lib/connect.php');
require_once(__DIR__ . '/../../../../inc/getFunctions.php');

global $base_path_admin;
global $base_path;
global $public_path;
global $conn;

$response = array('status' => 'error', 'message' => '');

function handleFileUpload($files) {
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
    $maxFileSize = 5 * 1024 * 1024; // 5 MB

    $uploadResults = [];

    // ตรวจสอบว่า $files['name'] เป็น array หรือไม่
    if (isset($files['name']) && is_array($files['name'])) {
        // ถ้า $files['name'] เป็น array, ให้ loop ผ่านแต่ละไฟล์
        foreach ($files['name'] as $key => $fileName) {
            if ($files['error'][$key] === UPLOAD_ERR_OK) {
                $fileTmpPath = $files['tmp_name'][$key];
                $fileSize = $files['size'][$key];
                $fileType = $files['type'][$key];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                if (in_array($fileExtension, $allowedExtensions) && $fileSize <= $maxFileSize) {
                    $uploadFileDir = '../../../../public/news_img/';
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
    } else {
        $uploadResults[] = [
            'success' => false,
            'error' => 'No files were uploaded.'
        ];
    }

    return $uploadResults;
}



try {
    if (isset($_POST['action']) && $_POST['action'] == 'getData_news') {
        $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $searchValue = isset($_POST['search']['value']) ? $conn->real_escape_string($_POST['search']['value']) : '';

        $orderIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
        $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

        $whereClause = "news_id IS NOT NULL";
        if (!empty($searchValue)) {
            // Add search filter if needed
        }
        
        $dataQuery = "SELECT * FROM public_news 
        WHERE $whereClause 
        LIMIT $start, $length";

        $dataResult = $conn->query($dataQuery);
        $data = [];
        while ($row = $dataResult->fetch_assoc()) {
            $data[] = $row;
        }

        $Index = 'news_id';
        $totalRecords = getTotalRecords($conn, 'public_news', $Index);
        $totalFiltered = getFilteredRecordsCount($conn, 'public_news', $whereClause, $Index);

        $response = [
            "draw" => intval($draw),
            "recordsTotal" => intval($totalRecords),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        ];
        
    } 
    else if (isset($_POST['action']) && $_POST['action'] == 'addNews') {

        // print_r($_POST);
        // print_r($_FILES['image_files']);
        // exit;

        $news_array = [
            'news_subject' => $_POST['news_subject'] ?? '',
            'news_content'  => $_POST['news_content'] ?? '',
        ];

        if (isset($news_array)) {
            
            $stmt = $conn->prepare("INSERT INTO public_news 
                (subject_news, content_news, date_create) 
                VALUES (?, ?, ?)");

            $news_subject = $news_array['news_subject'];
            $news_content = $news_array['news_content'];
            $current_date = date('Y-m-d H:i:s');

            $stmt->bind_param(
                "sss", 
                $news_subject, 
                $news_content, 
                $current_date
            );

            if (!$stmt->execute()) {
                throw new Exception("Execute statement failed: " . $stmt->error);
            }

            $last_inserted_id = $conn->insert_id;
            if ($_FILES['image_files']['error'] != 4) {

                $fileInfos = handleFileUpload($_FILES['image_files']); 
                foreach ($fileInfos as $fileInfo) {
                    if ($fileInfo['success']) {
    
                        $picPath = $base_path .'allable/public/news_img/'.$fileInfo['fileName'];
    
                        $fileColumns = ['news_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path'];
                        $fileValues = [$last_inserted_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath];
                        insertIntoDatabase($conn, 'public_news_doc', $fileColumns, $fileValues);
    
                    } else {
                        throw new Exception('Error uploading file: ' . $fileInfo['fileName'] . ' - ' . $fileInfo['error']);
                    }
                }
            }

            $response = array('status' => 'success', 'message' => 'save');
        }
    }
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
}

if (isset($stmt)) {
    $stmt->close();
}
$conn->close();

echo json_encode($response);

// $picPath = $base_path .'tdi_store/app/actions/uploaded_files/'.$fileInfo['fileName'];
    
// $orderID = date('YmdHis'); // Unique order ID
// $fileColumns = ['member_id', 'order_id', 'file_name', 'file_size', 'file_type', 'file_path', 'pic_path'];
// $fileValues = [$member_id, $orderID, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath];
// insertIntoDatabase($conn, 'ord_evidence', $fileColumns, $fileValues);

// $orderColumns = ['is_status'];
// $orderValues = ['1'];

// // กำหนด WHERE clause และค่าที่ใช้ใน WHERE clause
// $orderWhereClause = 'order_id = ? AND member_id = ?';
// $orderWhereValues = [$orderID, $member_id];

// updateInDatabase($conn, 'ecm_orders', $orderColumns, $orderValues, $orderWhereClause, $orderWhereValues);

?>
<?php
header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');
require_once(__DIR__ . '/../../../../lib/base_directory.php');
require_once(__DIR__ . '/../../../../lib/connect.php');
require_once(__DIR__ . '/../../../../inc/getFunctions.php');

global $base_path;
global $base_path_admin;

global $conn;

function insertIntoDatabase($conn, $table, $columns, $values)
{

    $placeholders = implode(', ', array_fill(0, count($values), '?'));

    $query = "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES ($placeholders)";

    $stmt = $conn->prepare($query);

    $types = str_repeat('s', count($values));
    $stmt->bind_param($types, ...$values);

    if ($stmt->execute()) {
        return 1;
    } else {
        return 0;
    }
}

function updateInDatabase($conn, $table, $columns, $values, $whereClause, $whereValues)
{

    $setPart = implode(', ', array_map(function ($col) {
        return "$col = ?";
    }, $columns));

    $query = "UPDATE $table SET $setPart WHERE $whereClause";

    $stmt = $conn->prepare($query);

    // Bind parameters
    $types = str_repeat('s', count($values)) . str_repeat('s', count($whereValues));
    $stmt->bind_param($types, ...array_merge($values, $whereValues));

    if ($stmt->execute()) {
        return 1;
    } else {
        return 0;
    }
}

function handleFileUpload($files)
{
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
    $maxFileSize = 5 * 1024 * 1024; // 5 MB

    $uploadResults = [];

    if (isset($files['name']) && is_array($files['name'])) {
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



$response = array('status' => 'error', 'message' => '');

try {


    if (isset($_POST['action']) && $_POST['action'] == 'addidia') {

        $idia_array = [
            'idia_subject' => $_POST['idia_subject'] ?? '',
            'idia_description' => $_POST['idia_description'] ?? '',
            'idia_content'  => $_POST['idia_content'] ?? '',
        ];

        if (isset($idia_array)) {

            $stmt = $conn->prepare("INSERT INTO dn_idia 
                (subject_idia, description_idia, content_idia, date_create) 
                VALUES (?, ?, ?, ?)");

            $idia_subject = $idia_array['idia_subject'];
            $idia_description = $idia_array['idia_description'];

            $idia_content = mb_convert_encoding($idia_array['idia_content'], 'UTF-8', 'auto');

            $current_date = date('Y-m-d H:i:s');

            $stmt->bind_param(
                "ssss",
                $idia_subject,
                $idia_description,
                $idia_content,
                $current_date
            );

            if (!$stmt->execute()) {
                throw new Exception("Execute statement failed: " . $stmt->error);
            }

            $last_inserted_id = $conn->insert_id;

            if (isset($_FILES['fileInput']) && $_FILES['fileInput']['error'][0] != 4) {

                $fileInfos = handleFileUpload($_FILES['fileInput']);
                foreach ($fileInfos as $fileInfo) {
                    if ($fileInfo['success']) {

                        $picPath = $base_path . '/public/news_img/' . $fileInfo['fileName'];

                        $fileColumns = ['idia_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'status'];
                        $fileValues = [$last_inserted_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath, 1];
                        insertIntoDatabase($conn, 'dn_idia_doc', $fileColumns, $fileValues);
                    } else {
                        throw new Exception('Error uploading file: ' . $fileInfo['fileName'] . ' - ' . $fileInfo['error']);
                    }
                }
            }

            if (isset($_FILES['image_files']) && $_FILES['image_files']['error'] != 4) {

                $fileInfos = handleFileUpload($_FILES['image_files']);
                foreach ($fileInfos as $fileInfo) {
                    if ($fileInfo['success']) {

                        $picPath = $base_path . '/public/news_img/' . $fileInfo['fileName'];

                        $fileColumns = ['idia_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path'];
                        $fileValues = [$last_inserted_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath];
                        insertIntoDatabase($conn, 'dn_idia_doc', $fileColumns, $fileValues);
                    } else {
                        throw new Exception('Error uploading file: ' . $fileInfo['fileName'] . ' - ' . $fileInfo['error']);
                    }
                }
            }

            $response = array('status' => 'success', 'message' => 'save');
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'editidia') {


        $idia_array = [
            'idia_id' => $_POST['idia_id'] ?? '',
            'idia_subject' => $_POST['idia_subject'] ?? '',
            'idia_description' => $_POST['idia_description'] ?? '',
            'idia_content'  => $_POST['idia_content'] ?? '',
        ];

        if (!empty($idia_array['idia_id'])) {

            $stmt = $conn->prepare("UPDATE dn_idia 
            SET subject_idia = ?, 
            description_idia = ?, 
            content_idia = ?, 
            date_create = ? 
            WHERE idia_id = ?");

            $idia_subject = $idia_array['idia_subject'];
            $idia_description = $idia_array['idia_description'];
            $idia_content = mb_convert_encoding($idia_array['idia_content'], 'UTF-8', 'auto');

            $current_date = date('Y-m-d H:i:s');
            $idia_id = $idia_array['idia_id'];

            $stmt->bind_param(
                "ssssi",
                $idia_subject,
                $idia_description,
                $idia_content,
                $current_date,
                $idia_id
            );

            if (!$stmt->execute()) {
                throw new Exception("Execute statement failed: " . $stmt->error);
            }

            $idia_id = $idia_array['idia_id'];
            // if (isset($_FILES['fileInput']) && $_FILES['fileInput']['error'][0] != 4) {

            //     $fileInfos = handleFileUpload($_FILES['fileInput']);
            //     foreach ($fileInfos as $fileInfo) {
            //         if ($fileInfo['success']) {

            //             $picPath = $base_path . '/public/news_img/' . $fileInfo['fileName'];

            //             $fileColumns = ['file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'status'];
            //             $fileValues = [$fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath, 1];

            //             // กำหนด WHERE clause และค่าที่ใช้ใน WHERE clause
            //             $fileWhereClause = 'idia_id = ?';
            //             $fileWhereValues = [$idia_id];

            //             updateInDatabase($conn, 'dn_idia_doc', $fileColumns, $fileValues, $fileWhereClause, $fileWhereValues);
            //         } else {
            //             throw new Exception('Error uploading file: ' . $fileInfo['fileName'] . ' - ' . $fileInfo['error']);
            //         }
            //     }
            // }

            if (isset($_FILES['image_files']) && $_FILES['image_files']['error'] != 4) {

    $fileInfos = handleFileUpload($_FILES['image_files']);
    foreach ($fileInfos as $fileInfo) {
        if ($fileInfo['success']) {

            $picPath = $base_path . '/public/news_img/' . $fileInfo['fileName'];

            $fileColumns = ['idia_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path'];
            $fileValues = [$idia_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath];

            insertIntoDatabase($conn, 'dn_idia_doc', $fileColumns, $fileValues);
        } else {
            throw new Exception('Error uploading file: ' . $fileInfo['fileName'] . ' - ' . $fileInfo['error']);
        }
    }
}
            $response = array('status' => 'success', 'message' => 'edit save');
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'delidia') {

        $idia_id = $_POST['id'] ?? '';
        $del = '1';
        
        // Update the `dn_idia` table
        $stmt = $conn->prepare("UPDATE dn_idia 
            SET del = ? 
            WHERE idia_id = ?"); // Removed the extra comma here
        
        $stmt->bind_param(
            "si",
            $del,
            $idia_id
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }
        
        // Update the `dn_idia_doc` table
        $stmt = $conn->prepare("UPDATE dn_idia_doc 
            SET del = ? 
            WHERE idia_id = ?"); // Removed the extra comma here
        
        $stmt->bind_param(
            "si",
            $del,
            $idia_id
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }
        
        $response = array('status' => 'success', 'message' => 'Delete');
        


    } elseif (isset($_POST['action']) && $_POST['action'] == 'getData_idia') {
        $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $searchValue = isset($_POST['search']['value']) ? $conn->real_escape_string($_POST['search']['value']) : '';

        $orderIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
        $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

        $columns = ['idia_id'];

        $whereClause = "del = 0";

        if (!empty($searchValue)) {
            $whereClause .= " AND (subject_idia LIKE '%$searchValue%')";
        }

        $orderBy = $columns[$orderIndex] . " " . $orderDir;

        $dataQuery = "SELECT idia_id, subject_idia, date_create FROM dn_idia 
                    WHERE $whereClause
                    ORDER BY $orderBy
                    LIMIT $start, $length";

        $dataResult = $conn->query($dataQuery);
        $data = [];
        while ($row = $dataResult->fetch_assoc()) {
            $data[] = $row;
        }

        $Index = 'idia_id';
        $totalRecords = getTotalRecords($conn, 'dn_idia', $Index);
        $totalFiltered = getFilteredRecordsCount($conn, 'dn_idia', $whereClause, $Index);

        $response = [
            "draw" => intval($draw),
            "recordsTotal" => intval($totalRecords),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        ];
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

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


    if (isset($_POST['action']) && $_POST['action'] == 'addbanner') {
// print_r($_POST); exit;
        $news_array = [
            'banner_subject' => $_POST['banner_subject'] ?? '',
            'banner_description' => $_POST['banner_description'] ?? '',
            'banner_content'  => $_POST['banner_content'] ?? '',
        ];

        if (isset($news_array)) {

            $stmt = $conn->prepare("INSERT INTO banner 
                (subject_banner, description_banner, content_banner, date_create) 
                VALUES (?, ?, ?, ?)");

            $news_subject = $news_array['banner_subject'];
            $news_description = $news_array['banner_description'];

            $news_content = mb_convert_encoding($news_array['banner_content'], 'UTF-8', 'auto');

            $current_date = date('Y-m-d H:i:s');

            $stmt->bind_param(
                "ssss",
                $news_subject,
                $news_description,
                $news_content,
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

                        $fileColumns = ['banner_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'status'];
                        $fileValues = [$last_inserted_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath, 1];
                        insertIntoDatabase($conn, 'banner_doc', $fileColumns, $fileValues);
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

                        $fileColumns = ['banner_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path'];
                        $fileValues = [$last_inserted_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath];
                        insertIntoDatabase($conn, 'banner_doc', $fileColumns, $fileValues);
                    } else {
                        throw new Exception('Error uploading file: ' . $fileInfo['fileName'] . ' - ' . $fileInfo['error']);
                    }
                }
            }

            $response = array('status' => 'success', 'message' => 'save');
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'editbanner') {


        $news_array = [
            'banner_id' => $_POST['banner_id'] ?? '',
            'banner_subject' => $_POST['banner_subject'] ?? '',
            'banner_description' => $_POST['banner_description'] ?? '',
            'banner_content'  => $_POST['banner_content'] ?? '',
        ];

        if (!empty($news_array['banner_id'])) {

            $stmt = $conn->prepare("UPDATE banner 
            SET subject_banner = ?, 
            description_banner = ?, 
            content_banner = ?, 
            date_create = ? 
            WHERE banner_id = ?");

            $news_subject = $news_array['banner_subject'];
            $news_description = $news_array['banner_description'];
            $news_content = mb_convert_encoding($news_array['banner_content'], 'UTF-8', 'auto');

            $current_date = date('Y-m-d H:i:s');
            $news_id = $news_array['banner_id'];

            $stmt->bind_param(
                "ssssi",
                $news_subject,
                $news_description,
                $news_content,
                $current_date,
                $news_id
            );

            if (!$stmt->execute()) {
                throw new Exception("Execute statement failed: " . $stmt->error);
            }

            $news_id = $news_array['banner_id'];
            if (isset($_FILES['fileInput']) && $_FILES['fileInput']['error'][0] != 4) {

                $fileInfos = handleFileUpload($_FILES['fileInput']);
                foreach ($fileInfos as $fileInfo) {
                    if ($fileInfo['success']) {

                        $picPath = $base_path . '/public/news_img/' . $fileInfo['fileName'];

                        $fileColumns = ['file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'status'];
                        $fileValues = [$fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath, 1];

                        // กำหนด WHERE clause และค่าที่ใช้ใน WHERE clause
                        $fileWhereClause = 'banner_id = ?';
                        $fileWhereValues = [$news_id];

                        updateInDatabase($conn, 'banner_doc', $fileColumns, $fileValues, $fileWhereClause, $fileWhereValues);
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

                        $fileColumns = ['file_name', 'file_size', 'file_type', 'file_path', 'api_path'];
                        $fileValues = [$fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath];

                        $fileWhereClause = 'banner_id = ?';
                        $fileWhereValues = [$news_id];

                        updateInDatabase($conn, 'banner_doc', $fileColumns, $fileValues, $fileWhereClause, $fileWhereValues);
                    } else {
                        throw new Exception('Error uploading file: ' . $fileInfo['fileName'] . ' - ' . $fileInfo['error']);
                    }
                }
            }

            $response = array('status' => 'success', 'message' => 'edit save');
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'delbanner') {

        $news_id = $_POST['id'] ?? '';
        $del = '1';
        
        // Update the `dn_news` table
        $stmt = $conn->prepare("UPDATE banner 
            SET del = ? 
            WHERE banner_id = ?"); // Removed the extra comma here
        
        $stmt->bind_param(
            "si",
            $del,
            $news_id
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }
        
        // Update the `dn_news_doc` table
        $stmt = $conn->prepare("UPDATE banner_doc 
            SET del = ? 
            WHERE banner_id = ?"); // Removed the extra comma here
        
        $stmt->bind_param(
            "si",
            $del,
            $news_id
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }
        
        $response = array('status' => 'success', 'message' => 'Delete');
        


    } elseif (isset($_POST['action']) && $_POST['action'] == 'getData_banner') {
        $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $searchValue = isset($_POST['search']['value']) ? $conn->real_escape_string($_POST['search']['value']) : '';

        $orderIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
        $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

        $columns = ['banner_id'];

        $whereClause = "del = 0";

        if (!empty($searchValue)) {
            $whereClause .= " AND (subject_banner LIKE '%$searchValue%')";
        }

        $orderBy = $columns[$orderIndex] . " " . $orderDir;

        $dataQuery = "SELECT banner_id, subject_banner, date_create FROM banner 
                    WHERE $whereClause
                    ORDER BY $orderBy
                    LIMIT $start, $length";

        $dataResult = $conn->query($dataQuery);
        $data = [];
        while ($row = $dataResult->fetch_assoc()) {
            $data[] = $row;
        }

        $Index = 'banner_id';
        $totalRecords = getTotalRecords($conn, 'banner', $Index);
        $totalFiltered = getFilteredRecordsCount($conn, 'banner', $whereClause, $Index);

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

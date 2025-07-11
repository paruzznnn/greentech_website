<?php
header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');
require_once(__DIR__ . '/../../../../lib/base_directory.php');
require_once(__DIR__ . '/../../../../lib/connect.php');
require_once(__DIR__ . '/../../../../inc/getFunctions.php');
require_once(__DIR__ . '/../../../../lib/permissions.php');

global $base_path;
global $base_path_admin;
global $conn;

$arrPermiss = checkPermissions($_SESSION);
$allowedPermiss_id = (isset($arrPermiss) && is_array($arrPermiss) && isset($arrPermiss['permiss_id']))
    ? explode(',', $arrPermiss['permiss_id'])
    : [];

$allowedPermiss = (isset($arrPermiss) && is_array($arrPermiss) && isset($arrPermiss['permissions']))
    ? explode(',', $arrPermiss['permissions'])
    : [];


$permissionsMap = array_combine($allowedPermiss, $allowedPermiss_id);

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

    // if (isset($_POST['action']) && $_POST['action'] == 'getSender') {

    //     $stmt = $conn->prepare("SELECT * FROM mb_user WHERE user_id = ? AND del = ?");
    //     if (!$stmt) {
    //         throw new Exception("Prepare statement failed: " . $conn->error);
    //     }

    //     $user_id = $_SESSION['user_id'];
    //     $del = 0;
    //     $stmt->bind_param("ii", $user_id, $del);

    //     if (!$stmt->execute()) {
    //         throw new Exception("Execute statement failed: " . $stmt->error);
    //     }

    //     $result = $stmt->get_result();
    //     $sender = array();

    //     while ($row = $result->fetch_assoc()) {
    //         $sender[] = $row;
    //     }

    //     $response = array(
    //         'status' => 'success',
    //         'message' => '',
    //         'data' => $sender
    //     );

    // }

    // else if (isset($_POST['action']) && $_POST['action'] == 'getReceiver') {

    //     $stmt = $conn->prepare("SELECT * FROM mb_user WHERE del = ?");
    //     if (!$stmt) {
    //         throw new Exception("Prepare statement failed: " . $conn->error);
    //     }

    //     $del = 0;
    //     $stmt->bind_param("i", $del);

    //     if (!$stmt->execute()) {
    //         throw new Exception("Execute statement failed: " . $stmt->error);
    //     }

    //     $result = $stmt->get_result();
    //     $receiver = array();

    //     while ($row = $result->fetch_assoc()) {
    //         $receiver[] = $row;
    //     }

    //     $response = array(
    //         'status' => 'success',
    //         'message' => '',
    //         'data' => $receiver
    //     );

    // }

} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
}

if (isset($stmt)) {
    $stmt->close();
}
$conn->close();

echo json_encode($response);

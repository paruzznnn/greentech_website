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
    if ($stmt === false) {
        error_log("SQL Prepare failed for insert: " . $conn->error);
        return 0;
    }
    $types = '';
    foreach ($values as $value) {
        if (is_int($value)) {
            $types .= 'i';
        } elseif (is_float($value)) {
            $types .= 'd';
        } else {
            $types .= 's';
        }
    }
    $stmt->bind_param($types, ...$values);
    if ($stmt->execute()) {
        return 1;
    } else {
        error_log("Execute statement failed for insert: " . $stmt->error);
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
    if ($stmt === false) {
        error_log("SQL Prepare failed for update: " . $conn->error);
        return 0;
    }

    $allValues = array_merge($values, $whereValues);
    $types = '';
    foreach ($allValues as $value) {
        if (is_int($value)) {
            $types .= 'i';
        } elseif (is_float($value)) {
            $types .= 'd';
        } else {
            $types .= 's';
        }
    }
    $stmt->bind_param($types, ...$allValues);

    if ($stmt->execute()) {
        return 1;
    } else {
        error_log("Execute statement failed for update: " . $stmt->error);
        return 0;
    }
}

function handleFileUpload($files, $is_single_file = false)
{
    global $base_path;

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
    $maxFileSize = 5 * 1024 * 1024; // 5 MB

    $uploadResults = [];

    if ($is_single_file && isset($files['name']) && !is_array($files['name'])) {
        $files_to_process = [
            'name' => [$files['name']],
            'tmp_name' => [$files['tmp_name']],
            'size' => [$files['size']],
            'type' => [$files['type']],
            'error' => [$files['error']]
        ];
    } else if (isset($files['name']) && is_array($files['name'])) {
        $files_to_process = $files;
    } else {
        if (isset($files['error']) && (is_array($files['error']) ? $files['error'][0] === UPLOAD_ERR_NO_FILE : $files['error'] === UPLOAD_ERR_NO_FILE)) {
            return [];
        }
        return [['success' => false, 'error' => 'No files were uploaded or invalid file structure.']];
    }

    foreach ($files_to_process['name'] as $key => $fileName) {
        if ($files_to_process['error'][$key] === UPLOAD_ERR_OK) {
            $fileTmpPath = $files_to_process['tmp_name'][$key];
            $fileSize = $files_to_process['size'][$key];
            $fileType = $files_to_process['type'][$key];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            if (in_array($fileExtension, $allowedExtensions) && $fileSize <= $maxFileSize) {
                $uploadFileDir = '../../../../public/shop_img/';

                $uniquePrefix = uniqid() . '_';
                $serverFileName = $uniquePrefix . preg_replace("/[^a-zA-Z0-9\._-]/", "_", $fileName);
                $destFilePath = $uploadFileDir . $serverFileName;
                $apiPath = $base_path . '/public/shop_img/' . $serverFileName;

                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }

                if (move_uploaded_file($fileTmpPath, $destFilePath)) {
                    $uploadResults[] = [
                        'success' => true,
                        'fileName' => $fileName,
                        'serverFileName' => $serverFileName,
                        'fileSize' => $fileSize,
                        'fileType' => $fileType,
                        'filePath' => $destFilePath,
                        'apiPath' => $apiPath
                    ];
                } else {
                    $uploadResults[] = [
                        'success' => false,
                        'fileName' => $fileName,
                        'error' => 'Error occurred while moving the uploaded file.',
                        'php_error' => $files_to_process['error'][$key]
                    ];
                    error_log("File upload failed: " . $files_to_process['error'][$key] . " for " . $fileName);
                }
            } else {
                $uploadResults[] = [
                    'success' => false,
                    'fileName' => $fileName,
                    'error' => 'Invalid file type or file size exceeds limit.'
                ];
            }
        } else {
            if ($files_to_process['error'][$key] !== UPLOAD_ERR_NO_FILE) {
                $uploadResults[] = [
                    'success' => false,
                    'fileName' => $fileName,
                    'error' => 'Upload error: ' . $files_to_process['error'][$key]
                ];
                error_log("Upload error for file " . $fileName . ": " . $files_to_process['error'][$key]);
            }
        }
    }

    return $uploadResults;
}


// --- ส่วนที่เพิ่มใหม่สำหรับ Summernote Callback ---
if (isset($_POST['action']) && $_POST['action'] == 'upload_image_summernote') {
    $response = ['status' => 'error', 'message' => ''];
    try {
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("File upload error or no file sent.");
        }

        $fileInfos = handleFileUpload($_FILES['file'], true);

        if (!empty($fileInfos) && $fileInfos[0]['success']) {
            $fileInfo = $fileInfos[0];
            $response = [
                'status' => 'success',
                'url' => $fileInfo['apiPath'],
                'message' => 'Image uploaded successfully.'
            ];
        } else {
            throw new Exception("Error processing uploaded file: " . ($fileInfos[0]['error'] ?? 'Unknown error'));
        }
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
        error_log("Summernote upload error: " . $e->getMessage());
    }
    echo json_encode($response);
    exit;
}

$response = array('status' => 'error', 'message' => '');

try {
    if (!isset($_POST['action'])) {
        throw new Exception("No action specified.");
    }

    if ($_POST['action'] == 'addshop') {
        $group_id = $_POST['group_id'] ?? null;
        $shop_subject = $_POST['shop_subject'] ?? '';
        $shop_description = $_POST['shop_description'] ?? '';
        $shop_content = $_POST['shop_content'] ?? '';
        
        $shop_subject_en = $_POST['shop_subject_en'] ?? '';
        $shop_description_en = $_POST['shop_description_en'] ?? '';
        $shop_content_en = $_POST['shop_content_en'] ?? '';

        $current_date = date('Y-m-d H:i:s');

        $stmt = $conn->prepare("INSERT INTO dn_shop (subject_shop, description_shop, content_shop, subject_shop_en, description_shop_en, content_shop_en, date_create, group_id, del) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)");
        if ($stmt === false) {
            throw new Exception("SQL Prepare failed for insert shop: " . $conn->error);
        }

        $stmt->bind_param(
            "sssssssi",
            $shop_subject,
            $shop_description,
            $shop_content,
            $shop_subject_en,
            $shop_description_en,
            $shop_content_en,
            $current_date,
            $group_id
        );

        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed for insert shop: " . $stmt->error);
        }

        $new_shop_id = $conn->insert_id;

        if (isset($_FILES['fileInput']) && $_FILES['fileInput']['error'] === UPLOAD_ERR_OK) {
            $fileInfos = handleFileUpload($_FILES['fileInput'], true);
            if (!empty($fileInfos) && $fileInfos[0]['success']) {
                $fileInfo = $fileInfos[0];
                $fileColumns = ['shop_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'status'];
                $fileValues = [$new_shop_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $fileInfo['apiPath'], 1];
                if (!insertIntoDatabase($conn, 'dn_shop_doc', $fileColumns, $fileValues)) {
                    throw new Exception('Error inserting cover photo for new shop.');
                }
            } else {
                throw new Exception('Error uploading cover photo: ' . ($fileInfos[0]['error'] ?? 'Unknown error'));
            }
        } else {
            if (!isset($_FILES['fileInput']) || $_FILES['fileInput']['error'] !== UPLOAD_ERR_NO_FILE) {
                throw new Exception("Cover photo is required or there was an upload error.");
            }
        }

        if (isset($_FILES['image_files']) && $_FILES['image_files']['error'][0] !== UPLOAD_ERR_NO_FILE) {
            $fileInfos = handleFileUpload($_FILES['image_files']);
            foreach ($fileInfos as $fileInfo) {
                if ($fileInfo['success']) {
                    $fileColumns = ['shop_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'status'];
                    $fileValues = [$new_shop_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $fileInfo['apiPath'], 0];
                    if (!insertIntoDatabase($conn, 'dn_shop_doc', $fileColumns, $fileValues)) {
                        error_log('Error inserting content image: ' . $fileInfo['fileName']);
                    }
                } else {
                    error_log('Error uploading content image: ' . ($fileInfo['fileName'] ?? 'N/A') . ' - ' . $fileInfo['error']);
                }
            }
        }
        $response = array('status' => 'success', 'message' => 'Shop added successfully!', 'shop_id' => $new_shop_id);

    } elseif ($_POST['action'] == 'editshop') {
        $group_id = $_POST['group_id'] ?? null;
        $shop_array = [
            'shop_id' => $_POST['shop_id'] ?? '',
            'shop_subject' => $_POST['shop_subject'] ?? '',
            'shop_description' => $_POST['shop_description'] ?? '',
            'shop_content'  => $_POST['shop_content'] ?? '',
        ];

        $shop_subject_en = $_POST['shop_subject_en'] ?? '';
        $shop_description_en = $_POST['shop_description_en'] ?? '';
        $shop_content_en = $_POST['shop_content_en'] ?? '';

        if (empty($shop_array['shop_id'])) {
            throw new Exception("Shop ID is missing for editing.");
        }

        $shop_id = (int)$shop_array['shop_id'];

        $stmt = $conn->prepare("UPDATE dn_shop
                SET subject_shop = ?,
                description_shop = ?,
                content_shop = ?,
                subject_shop_en = ?,
                description_shop_en = ?,
                content_shop_en = ?,
                date_create = ?,
                group_id = ?
                WHERE shop_id = ?");

        if ($stmt === false) {
            throw new Exception("SQL Prepare failed for update shop: " . $conn->error);
        }

        $shop_subject = $shop_array['shop_subject'];
        $shop_description = $shop_array['shop_description'];
        $shop_content = $shop_array['shop_content'];

        $current_date = date('Y-m-d H:i:s');

        $stmt->bind_param(
            "sssssssis",
            $shop_subject,
            $shop_description,
            $shop_content,
            $shop_subject_en,
            $shop_description_en,
            $shop_content_en,
            $current_date,
            $group_id,
            $shop_id
        );

        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed for update shop: " . $stmt->error);
        }
        $stmt->close();

        // --- Start Handle Cover photo upload (fileInput) ---
        if (isset($_FILES['fileInput']) && $_FILES['fileInput']['error'] === UPLOAD_ERR_OK) {
            $oldCoverPath = null;
            $getOldCoverStmt = $conn->prepare("SELECT file_path FROM dn_shop_doc WHERE shop_id = ? AND status = 1 AND del = 0");
            if ($getOldCoverStmt) {
                $getOldCoverStmt->bind_param("i", $shop_id);
                $getOldCoverStmt->execute();
                $oldCoverResult = $getOldCoverStmt->get_result();
                if ($oldCoverRow = $oldCoverResult->fetch_assoc()) {
                    $oldCoverPath = $oldCoverRow['file_path'];
                }
                $getOldCoverStmt->close();
            } else {
                error_log("SQL Prepare failed for getting old cover path: " . $conn->error);
            }

            $fileInfos = handleFileUpload($_FILES['fileInput'], true);
            if (!empty($fileInfos) && $fileInfos[0]['success']) {
                $fileInfo = $fileInfos[0];
                $checkExistingCoverStmt = $conn->prepare("SELECT COUNT(*) FROM dn_shop_doc WHERE shop_id = ? AND status = 1 AND del = 0");
                if ($checkExistingCoverStmt) {
                    $checkExistingCoverStmt->bind_param("i", $shop_id);
                    $checkExistingCoverStmt->execute();
                    $existingCount = $checkExistingCoverStmt->get_result()->fetch_row()[0];
                    $checkExistingCoverStmt->close();

                    if ($existingCount > 0) {
                        $updateCoverStmt = $conn->prepare("UPDATE dn_shop_doc
                                SET file_name = ?, file_size = ?, file_type = ?, file_path = ?, api_path = ?
                                WHERE shop_id = ? AND status = 1 AND del = 0");
                        if ($updateCoverStmt === false) {
                            throw new Exception("SQL Prepare failed for updating cover photo: " . $conn->error);
                        }
                        $updateCoverStmt->bind_param(
                            "sisssi",
                            $fileInfo['fileName'],
                            $fileInfo['fileSize'],
                            $fileInfo['fileType'],
                            $fileInfo['filePath'],
                            $fileInfo['apiPath'],
                            $shop_id
                        );
                        if (!$updateCoverStmt->execute()) {
                            throw new Exception('Error updating cover photo: ' . $updateCoverStmt->error);
                        }
                        $updateCoverStmt->close();
                    } else {
                        $fileColumns = ['shop_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'status'];
                        $fileValues = [$shop_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $fileInfo['apiPath'], 1];
                        if (!insertIntoDatabase($conn, 'dn_shop_doc', $fileColumns, $fileValues)) {
                            throw new Exception('Error inserting new cover photo.');
                        }
                    }
                    if ($oldCoverPath && file_exists($oldCoverPath)) {
                        unlink($oldCoverPath);
                        error_log("Deleted old cover photo file: " . $oldCoverPath);
                    }
                } else {
                    throw new Exception("SQL Prepare failed for checking existing cover: " . $conn->error);
                }
            } else {
                throw new Exception('Error uploading new cover photo: ' . ($fileInfos[0]['error'] ?? 'Unknown error'));
            }
        }
        // --- End Handle Cover photo upload (fileInput) ---


        // --- Start Handle content images upload (image_files) - from editshop ---
        if (isset($_FILES['image_files']) && $_FILES['image_files']['error'][0] !== UPLOAD_ERR_NO_FILE) {
            $fileInfos = handleFileUpload($_FILES['image_files']);
            foreach ($fileInfos as $fileInfo) {
                if ($fileInfo['success']) {
                    $checkDuplicateStmt = $conn->prepare("SELECT COUNT(*) FROM dn_shop_doc WHERE shop_id = ? AND file_name = ? AND status = 0 AND del = 0");
                    if ($checkDuplicateStmt) {
                        $checkDuplicateStmt->bind_param("is", $shop_id, $fileInfo['fileName']);
                        $checkDuplicateStmt->execute();
                        $isDuplicate = $checkDuplicateStmt->get_result()->fetch_row()[0];
                        $checkDuplicateStmt->close();

                        if ($isDuplicate == 0) {
                            $fileColumns = ['shop_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'status'];
                            $fileValues = [$shop_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $fileInfo['apiPath'], 0];
                            if (!insertIntoDatabase($conn, 'dn_shop_doc', $fileColumns, $fileValues)) {
                                error_log('Error inserting content image: ' . $fileInfo['fileName']);
                            }
                        } else {
                            error_log("Duplicate content image detected and skipped: " . $fileInfo['fileName']);
                        }
                    } else {
                        error_log("SQL Prepare failed for checking duplicate content image: " . $conn->error);
                    }
                } else {
                    error_log('Error uploading content image: ' . ($fileInfo['fileName'] ?? 'N/A') . ' - ' . $fileInfo['error']);
                }
            }
        }
        // --- End Handle content images upload (image_files) ---
        $response = array('status' => 'success', 'message' => 'Shop updated successfully!');

    } elseif ($_POST['action'] == 'delshop') {
        $shop_id = $_POST['id'] ?? '';
        if (empty($shop_id)) {
            throw new Exception("Shop ID is missing for deletion.");
        }
        $del = '1';

        $stmt = $conn->prepare("UPDATE dn_shop
                SET del = ?
                WHERE shop_id = ?");
        if ($stmt === false) {
            throw new Exception("SQL Prepare failed for delete shop: " . $conn->error);
        }
        $stmt->bind_param(
            "si",
            $del,
            $shop_id
        );
        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed for delete shop: " . $stmt->error);
        }

        $stmt = $conn->prepare("UPDATE dn_shop_doc
                SET del = ?
                WHERE shop_id = ?");
        if ($stmt === false) {
            throw new Exception("SQL Prepare failed for delete shop doc: " . $conn->error);
        }
        $stmt->bind_param(
            "si",
            $del,
            $shop_id
        );
        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed for delete shop doc: " . $stmt->error);
        }

        $response = array('status' => 'success', 'message' => 'Shop deleted successfully!');

    } elseif ($_POST['action'] == 'getData_shop') {
        $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $searchValue = isset($_POST['search']['value']) ? $conn->real_escape_string($_POST['search']['value']) : '';
        
        $lang = isset($_POST['lang']) && $_POST['lang'] === 'en' ? '_en' : '';
        
        $orderIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
        $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

        $columns = ["shop_id", "subject_shop{$lang}", "date_create", "main_group_name{$lang}", "sub_group_name{$lang}"];
        if ($orderIndex >= 0 && $orderIndex < count($columns)) {
            $orderByColumn = $columns[$orderIndex];
        } else {
            $orderByColumn = 'shop_id';
        }

        $whereClause = "s.del = 0";

        if (!empty($searchValue)) {
            $whereClause .= " AND (s.subject_shop{$lang} LIKE '%$searchValue%' OR sub.group_name{$lang} LIKE '%$searchValue%' OR parent.group_name{$lang} LIKE '%$searchValue%')";
        }

        $orderBy = $orderByColumn . " " . $orderDir;

        $totalRecordsQuery = "SELECT COUNT(shop_id) FROM dn_shop WHERE del = 0";
        $totalRecordsResult = $conn->query($totalRecordsQuery);
        $totalRecords = $totalRecordsResult->fetch_row()[0];

        $totalFilteredQuery = "SELECT COUNT(s.shop_id)
                                 FROM dn_shop s
                                 LEFT JOIN dn_shop_groups sub ON s.group_id = sub.group_id
                                 LEFT JOIN dn_shop_groups parent ON sub.parent_group_id = parent.group_id
                                 WHERE $whereClause";
        $totalFilteredResult = $conn->query($totalFilteredQuery);
        $totalFiltered = $totalFilteredResult->fetch_row()[0];

        $dataQuery = "SELECT
                            s.shop_id,
                            s.subject_shop{$lang} AS subject_shop,
                            s.date_create,
                            sub.group_name{$lang} AS sub_group_name,
                            parent.group_name{$lang} AS main_group_name
                        FROM dn_shop s
                        LEFT JOIN dn_shop_groups sub ON s.group_id = sub.group_id
                        LEFT JOIN dn_shop_groups parent ON sub.parent_group_id = parent.group_id
                        WHERE $whereClause
                        ORDER BY $orderBy
                        LIMIT $start, $length";

        $dataResult = $conn->query($dataQuery);
        $data = [];
        if ($dataResult) {
            while ($row = $dataResult->fetch_assoc()) {
                $data[] = $row;
            }
        } else {
            error_log("Error fetching data: " . $conn->error);
            throw new Exception("Error fetching shop data.");
        }

        $response = [
            "draw" => intval($draw),
            "recordsTotal" => intval($totalRecords),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        ];
    } else {
        throw new Exception("Invalid action specified: " . ($_POST['action'] ?? 'N/A'));
    }

} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
    error_log("Error in process_shop.php: " . $e->getMessage());
}

$conn->close();

echo json_encode($response);
?>
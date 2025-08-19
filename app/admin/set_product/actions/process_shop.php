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
        return [['success' => false, 'error' => 'No files were uploaded or invalid file structure.', 'error_en' => 'No files were uploaded or invalid file structure.', 'error_cn' => '未上传文件或文件结构无效.', 'error_jp' => 'ファイルがアップロードされていないか、ファイル構造が無効です。', 'error_kr' => '업로드된 파일이 없거나 파일 구조가 유효하지 않습니다.']];
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
                        'error_en' => 'Error occurred while moving the uploaded file.',
                        'error_cn' => '移动上传文件时出错.',
                        'error_jp' => 'アップロードされたファイルの移動中にエラーが発生しました。',
                        'error_kr' => '업로드된 파일을 옮기는 중 오류가 발생했습니다.',
                        'php_error' => $files_to_process['error'][$key]
                    ];
                    error_log("File upload failed: " . $files_to_process['error'][$key] . " for " . $fileName);
                }
            } else {
                $uploadResults[] = [
                    'success' => false,
                    'fileName' => $fileName,
                    'error' => 'Invalid file type or file size exceeds limit.',
                    'error_en' => 'Invalid file type or file size exceeds limit.',
                    'error_cn' => '文件类型无效或文件大小超出限制.',
                    'error_jp' => '無効なファイルタイプまたはファイルサイズが制限を超えています。',
                    'error_kr' => '유효하지 않은 파일 형식 또는 파일 크기가 제한을 초과했습니다.'
                ];
            }
        } else {
            if ($files_to_process['error'][$key] !== UPLOAD_ERR_NO_FILE) {
                $uploadResults[] = [
                    'success' => false,
                    'fileName' => $fileName,
                    'error' => 'Upload error: ' . $files_to_process['error'][$key],
                    'error_en' => 'Upload error: ' . $files_to_process['error'][$key],
                    'error_cn' => '上传错误：' . $files_to_process['error'][$key],
                    'error_jp' => 'アップロードエラー：' . $files_to_process['error'][$key],
                    'error_kr' => '업로드 오류: ' . $files_to_process['error'][$key]
                ];
                error_log("Upload error for file " . $fileName . ": " . $files_to_process['error'][$key]);
            }
        }
    }

    return $uploadResults;
}


// --- ส่วนที่เพิ่มใหม่สำหรับ Summernote Callback ---
if (isset($_POST['action']) && $_POST['action'] == 'upload_image_summernote') {
    $response = ['status' => 'error', 'message' => '', 'message_en' => '', 'message_cn' => '', 'message_jp' => '', 'message_kr' => ''];
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
                'message' => 'Image uploaded successfully.',
                'message_en' => 'Image uploaded successfully.',
                'message_cn' => '图片上传成功.',
                'message_jp' => '画像のアップロードに成功しました。',
                'message_kr' => '이미지 업로드 성공.'
            ];
        } else {
            throw new Exception("Error processing uploaded file: " . ($fileInfos[0]['error'] ?? 'Unknown error'));
        }
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
        $response['message_en'] = $e->getMessage();
        $response['message_cn'] = '文件上传出错：' . $e->getMessage();
        $response['message_jp'] = 'ファイルのアップロードエラー：' . $e->getMessage();
        $response['message_kr'] = '파일 업로드 오류: ' . $e->getMessage();
        error_log("Summernote upload error: " . $e->getMessage());
    }
    echo json_encode($response);
    exit;
}

$response = array('status' => 'error', 'message' => '', 'message_en' => '', 'message_cn' => '', 'message_jp' => '', 'message_kr' => '');

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

        $shop_subject_cn = $_POST['shop_subject_cn'] ?? '';
        $shop_description_cn = $_POST['shop_description_cn'] ?? '';
        $shop_content_cn = $_POST['shop_content_cn'] ?? '';
        
        $shop_subject_jp = $_POST['shop_subject_jp'] ?? '';
        $shop_description_jp = $_POST['shop_description_jp'] ?? '';
        $shop_content_jp = $_POST['shop_content_jp'] ?? '';

        $shop_subject_kr = $_POST['shop_subject_kr'] ?? '';
        $shop_description_kr = $_POST['shop_description_kr'] ?? '';
        $shop_content_kr = $_POST['shop_content_kr'] ?? '';

        $current_date = date('Y-m-d H:i:s');

        $stmt = $conn->prepare("INSERT INTO dn_shop (subject_shop, description_shop, content_shop, subject_shop_en, description_shop_en, content_shop_en, subject_shop_cn, description_shop_cn, content_shop_cn, subject_shop_jp, description_shop_jp, content_shop_jp, subject_shop_kr, description_shop_kr, content_shop_kr, date_create, group_id, del) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
        if ($stmt === false) {
            throw new Exception("SQL Prepare failed for insert shop: " . $conn->error);
        }

        $stmt->bind_param(
            "ssssssssssssssisi",
            $shop_subject,
            $shop_description,
            $shop_content,
            $shop_subject_en,
            $shop_description_en,
            $shop_content_en,
            $shop_subject_cn,
            $shop_description_cn,
            $shop_content_cn,
            $shop_subject_jp,
            $shop_description_jp,
            $shop_content_jp,
            $shop_subject_kr,
            $shop_description_kr,
            $shop_content_kr,
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
        $response = array('status' => 'success', 'message' => 'Shop added successfully!', 'message_en' => 'Shop added successfully!', 'message_cn' => '商品添加成功!', 'message_jp' => '商品の追加に成功しました！', 'message_kr' => '상품이 성공적으로 추가되었습니다!', 'shop_id' => $new_shop_id);

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

        $shop_subject_cn = $_POST['shop_subject_cn'] ?? '';
        $shop_description_cn = $_POST['shop_description_cn'] ?? '';
        $shop_content_cn = $_POST['shop_content_cn'] ?? '';
        
        $shop_subject_jp = $_POST['shop_subject_jp'] ?? '';
        $shop_description_jp = $_POST['shop_description_jp'] ?? '';
        $shop_content_jp = $_POST['shop_content_jp'] ?? '';

        $shop_subject_kr = $_POST['shop_subject_kr'] ?? '';
        $shop_description_kr = $_POST['shop_description_kr'] ?? '';
        $shop_content_kr = $_POST['shop_content_kr'] ?? '';

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
                subject_shop_cn = ?,
                description_shop_cn = ?,
                content_shop_cn = ?,
                subject_shop_jp = ?,
                description_shop_jp = ?,
                content_shop_jp = ?,
                subject_shop_kr = ?,
                description_shop_kr = ?,
                content_shop_kr = ?,
                group_id = ?
                WHERE shop_id = ?");

        if ($stmt === false) {
            throw new Exception("SQL Prepare failed for update shop: " . $conn->error);
        }

        $shop_subject = $shop_array['shop_subject'];
        $shop_description = $shop_array['shop_description'];
        $shop_content = $shop_array['shop_content'];

        $stmt->bind_param(
            "sssssssssssssssii",
            $shop_subject,
            $shop_description,
            $shop_content,
            $shop_subject_en,
            $shop_description_en,
            $shop_content_en,
            $shop_subject_cn,
            $shop_description_cn,
            $shop_content_cn,
            $shop_subject_jp,
            $shop_description_jp,
            $shop_content_jp,
            $shop_subject_kr,
            $shop_description_kr,
            $shop_content_kr,
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
        $response = array('status' => 'success', 'message' => 'Shop updated successfully!', 'message_en' => 'Shop updated successfully!', 'message_cn' => '商品更新成功！', 'message_jp' => '商品の更新に成功しました！', 'message_kr' => '상품이 성공적으로 업데이트되었습니다!');

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

        $response = array('status' => 'success', 'message' => 'Shop deleted successfully!', 'message_en' => 'Shop deleted successfully!', 'message_cn' => '商品删除成功！', 'message_jp' => '商品の削除に成功しました！', 'message_kr' => '상품이 성공적으로 삭제되었습니다!');

    } elseif ($_POST['action'] == 'getData_shop') {
        $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $searchValue = isset($_POST['search']['value']) ? $conn->real_escape_string($_POST['search']['value']) : '';
        
        $lang = isset($_POST['lang']) ? $_POST['lang'] : '';
        $subject_col = "subject_shop";
        $main_group_col = "main_group_name";
        $sub_group_col = "sub_group_name";
        
        if ($lang === 'en') {
            $subject_col .= "_en";
            $main_group_col .= "_en";
            $sub_group_col .= "_en";
        } elseif ($lang === 'cn') {
            $subject_col .= "_cn";
            $main_group_col .= "_cn";
            $sub_group_col .= "_cn";
        } elseif ($lang === 'jp') {
            $subject_col .= "_jp";
            $main_group_col .= "_jp";
            $sub_group_col .= "_jp";
        } elseif ($lang === 'kr') {
            $subject_col .= "_kr";
            $main_group_col .= "_kr";
            $sub_group_col .= "_kr";
        }
        
        $orderIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
        $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

        $columns = ["shop_id", "subject_shop", "date_create", "main_group_name", "sub_group_name"];
        $orderByColumn = $columns[$orderIndex];

        $whereClause = "s.del = 0";

        if (!empty($searchValue)) {
            $whereClause .= " AND (s.subject_shop LIKE '%$searchValue%' OR s.subject_shop_en LIKE '%$searchValue%' OR s.subject_shop_cn LIKE '%$searchValue%' OR s.subject_shop_jp LIKE '%$searchValue%' OR s.subject_shop_kr LIKE '%$searchValue%' OR sub.group_name LIKE '%$searchValue%' OR sub.group_name_en LIKE '%$searchValue%' OR sub.group_name_cn LIKE '%$searchValue%' OR sub.group_name_jp LIKE '%$searchValue%' OR sub.group_name_kr LIKE '%$searchValue%' OR parent.group_name LIKE '%$searchValue%' OR parent.group_name_en LIKE '%$searchValue%' OR parent.group_name_cn LIKE '%$searchValue%' OR parent.group_name_jp LIKE '%$searchValue%' OR parent.group_name_kr LIKE '%$searchValue%')";
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
                               s.subject_shop AS subject_shop,
                               s.subject_shop_en AS subject_shop_en,
                               s.subject_shop_cn AS subject_shop_cn,
                               s.subject_shop_jp AS subject_shop_jp,
                               s.subject_shop_kr AS subject_shop_kr,
                               s.date_create,
                               sub.group_name AS sub_group_name,
                               sub.group_name_en AS sub_group_name_en,
                               sub.group_name_cn AS sub_group_name_cn,
                               sub.group_name_jp AS sub_group_name_jp,
                               sub.group_name_kr AS sub_group_name_kr,
                               parent.group_name AS main_group_name,
                               parent.group_name_en AS main_group_name_en,
                               parent.group_name_cn AS main_group_name_cn,
                               parent.group_name_jp AS main_group_name_jp,
                               parent.group_name_kr AS main_group_name_kr
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
                $row['subject_shop_display'] = $row[$subject_col];
                $row['main_group_name_display'] = $row[$main_group_col];
                $row['sub_group_name_display'] = $row[$sub_group_col];
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
    $response['message_en'] = $e->getMessage();
    $response['message_cn'] = '操作失败：' . $e->getMessage();
    $response['message_jp'] = '操作に失敗しました：' . $e->getMessage();
    $response['message_kr'] = '작업 실패: ' . $e->getMessage();
    error_log("Error in process_shop.php: " . $e->getMessage());
}

$conn->close();

echo json_encode($response);
?>
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

    // ตรวจสอบว่าเป็นไฟล์ array หรือไฟล์เดียว
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
    } else if (isset($files['name']) && $files['error'] === UPLOAD_ERR_OK) { // จัดการกรณีที่เป็นไฟล์เดียว (เช่น Cover Photo)
        $fileTmpPath = $files['tmp_name'];
        $fileName = $files['name'];
        $fileSize = $files['size'];
        $fileType = $files['type'];
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
            'idia_subject_en' => $_POST['idia_subject_en'] ?? '',
            'idia_description_en' => $_POST['idia_description_en'] ?? '',
            'idia_content_en'  => $_POST['idia_content_en'] ?? '',
            'idia_subject_cn' => $_POST['idia_subject_cn'] ?? '',
            'idia_description_cn' => $_POST['idia_description_cn'] ?? '',
            'idia_content_cn'  => $_POST['idia_content_cn'] ?? '',
            'idia_subject_jp' => $_POST['idia_subject_jp'] ?? '',
            'idia_description_jp' => $_POST['idia_description_jp'] ?? '',
            'idia_content_jp'  => $_POST['idia_content_jp'] ?? '',
            'idia_subject_kr' => $_POST['idia_subject_kr'] ?? '',
            'idia_description_kr' => $_POST['idia_description_kr'] ?? '',
            'idia_content_kr'  => $_POST['idia_content_kr'] ?? '',
        ];

        if (isset($idia_array)) {
            $stmt = $conn->prepare("INSERT INTO dn_idia 
                (subject_idia, description_idia, content_idia, subject_idia_en, description_idia_en, content_idia_en, subject_idia_cn, description_idia_cn, content_idia_cn, subject_idia_jp, description_idia_jp, content_idia_jp, subject_idia_kr, description_idia_kr, content_idia_kr, date_create) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $idia_subject = $idia_array['idia_subject'];
            $idia_description = $idia_array['idia_description'];
            $idia_content = mb_convert_encoding($idia_array['idia_content'], 'UTF-8', 'auto');
            $idia_subject_en = $idia_array['idia_subject_en'];
            $idia_description_en = $idia_array['idia_description_en'];
            $idia_content_en = mb_convert_encoding($idia_array['idia_content_en'], 'UTF-8', 'auto');
            $idia_subject_cn = $idia_array['idia_subject_cn'];
            $idia_description_cn = $idia_array['idia_description_cn'];
            $idia_content_cn = mb_convert_encoding($idia_array['idia_content_cn'], 'UTF-8', 'auto');
            $idia_subject_jp = $idia_array['idia_subject_jp'];
            $idia_description_jp = $idia_array['idia_description_jp'];
            $idia_content_jp = mb_convert_encoding($idia_array['idia_content_jp'], 'UTF-8', 'auto');
            $idia_subject_kr = $idia_array['idia_subject_kr'];
            $idia_description_kr = $idia_array['idia_description_kr'];
            $idia_content_kr = mb_convert_encoding($idia_array['idia_content_kr'], 'UTF-8', 'auto');
            $current_date = date('Y-m-d H:i:s');

            $stmt->bind_param(
                "ssssssssssssssss",
                $idia_subject,
                $idia_description,
                $idia_content,
                $idia_subject_en,
                $idia_description_en,
                $idia_content_en,
                $idia_subject_cn,
                $idia_description_cn,
                $idia_content_cn,
                $idia_subject_jp,
                $idia_description_jp,
                $idia_content_jp,
                $idia_subject_kr,
                $idia_description_kr,
                $idia_content_kr,
                $current_date
            );

            if (!$stmt->execute()) {
                throw new Exception("Execute statement failed: " . $stmt->error);
            }
            $last_inserted_id = $conn->insert_id;
            
            // แก้ไขการตรวจสอบไฟล์
            if (isset($_FILES['fileInput']) && $_FILES['fileInput']['error'][0] !== UPLOAD_ERR_NO_FILE) {
                $fileInfos = handleFileUpload($_FILES['fileInput']);
                foreach ($fileInfos as $fileInfo) {
                    if ($fileInfo['success']) {
                        $picPath = $base_path . '/public/news_img/' . $fileInfo['fileName'];
                        $fileColumns = ['idia_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'status'];
                        $fileValues = [$last_inserted_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath, 1];
                        insertIntoDatabase($conn, 'dn_idia_doc', $fileColumns, $fileValues);
                    } else {
                        throw new Exception('Error uploading file: ' . ($fileInfo['fileName'] ?? 'unknown') . ' - ' . $fileInfo['error']);
                    }
                }
            }
            if (isset($_FILES['image_files']) && $_FILES['image_files']['error'][0] !== UPLOAD_ERR_NO_FILE) {
                $fileInfos = handleFileUpload($_FILES['image_files']);
                foreach ($fileInfos as $fileInfo) {
                    if ($fileInfo['success']) {
                        $picPath = $base_path . '/public/news_img/' . $fileInfo['fileName'];
                        $fileColumns = ['idia_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path'];
                        $fileValues = [$last_inserted_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath];
                        insertIntoDatabase($conn, 'dn_idia_doc', $fileColumns, $fileValues);
                    } else {
                        throw new Exception('Error uploading file: ' . ($fileInfo['fileName'] ?? 'unknown') . ' - ' . $fileInfo['error']);
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
            'idia_subject_en' => $_POST['idia_subject_en'] ?? '',
            'idia_description_en' => $_POST['idia_description_en'] ?? '',
            'idia_content_en'  => $_POST['idia_content_en'] ?? '',
            'idia_subject_cn' => $_POST['idia_subject_cn'] ?? '',
            'idia_description_cn' => $_POST['idia_description_cn'] ?? '',
            'idia_content_cn'  => $_POST['idia_content_cn'] ?? '',
            'idia_subject_jp' => $_POST['idia_subject_jp'] ?? '',
            'idia_description_jp' => $_POST['idia_description_jp'] ?? '',
            'idia_content_jp'  => $_POST['idia_content_jp'] ?? '',
            'idia_subject_kr' => $_POST['idia_subject_kr'] ?? '',
            'idia_description_kr' => $_POST['idia_description_kr'] ?? '',
            'idia_content_kr'  => $_POST['idia_content_kr'] ?? '',
        ];

        if (!empty($idia_array['idia_id'])) {
            $stmt = $conn->prepare("UPDATE dn_idia 
            SET subject_idia = ?, 
            description_idia = ?, 
            content_idia = ?,
            subject_idia_en = ?,
            description_idia_en = ?,
            content_idia_en = ?,
            subject_idia_cn = ?,
            description_idia_cn = ?,
            content_idia_cn = ?,
            subject_idia_jp = ?,
            description_idia_jp = ?,
            content_idia_jp = ?,
            subject_idia_kr = ?,
            description_idia_kr = ?,
            content_idia_kr = ?,
            date_create = ? 
            WHERE idia_id = ?");

            $idia_subject = $idia_array['idia_subject'];
            $idia_description = $idia_array['idia_description'];
            $idia_content = mb_convert_encoding($idia_array['idia_content'], 'UTF-8', 'auto');
            $idia_subject_en = $idia_array['idia_subject_en'];
            $idia_description_en = $idia_array['idia_description_en'];
            $idia_content_en = mb_convert_encoding($idia_array['idia_content_en'], 'UTF-8', 'auto');
            $idia_subject_cn = $idia_array['idia_subject_cn'];
            $idia_description_cn = $idia_array['idia_description_cn'];
            $idia_content_cn = mb_convert_encoding($idia_array['idia_content_cn'], 'UTF-8', 'auto');
            $idia_subject_jp = $idia_array['idia_subject_jp'];
            $idia_description_jp = $idia_array['idia_description_jp'];
            $idia_content_jp = mb_convert_encoding($idia_array['idia_content_jp'], 'UTF-8', 'auto');
            $idia_subject_kr = $idia_array['idia_subject_kr'];
            $idia_description_kr = $idia_array['idia_description_kr'];
            $idia_content_kr = mb_convert_encoding($idia_array['idia_content_kr'], 'UTF-8', 'auto');
            $current_date = date('Y-m-d H:i:s');
            $idia_id = $idia_array['idia_id'];

            $stmt->bind_param(
                "ssssssssssssssssi",
                $idia_subject,
                $idia_description,
                $idia_content,
                $idia_subject_en,
                $idia_description_en,
                $idia_content_en,
                $idia_subject_cn,
                $idia_description_cn,
                $idia_content_cn,
                $idia_subject_jp,
                $idia_description_jp,
                $idia_content_jp,
                $idia_subject_kr,
                $idia_description_kr,
                $idia_content_kr,
                $current_date,
                $idia_id
            );

            if (!$stmt->execute()) {
                throw new Exception("Execute statement failed: " . $stmt->error);
            }
            
            // จัดการรูป Cover Photo
            if (isset($_FILES['fileInput']) && $_FILES['fileInput']['error'] == UPLOAD_ERR_OK) {
                // 1. ดึง path ของรูปเก่าเพื่อลบ
                $getOldCoverStmt = $conn->prepare("SELECT file_path FROM dn_idia_doc WHERE idia_id = ? AND status = 1 AND del = 0");
                if ($getOldCoverStmt) {
                    $getOldCoverStmt->bind_param("i", $idia_id);
                    $getOldCoverStmt->execute();
                    $oldCoverResult = $getOldCoverStmt->get_result();
                    if ($oldCoverRow = $oldCoverResult->fetch_assoc()) {
                        $oldCoverPath = $oldCoverRow['file_path'];
                        // 2. ลบไฟล์เก่าถ้ามีอยู่จริง
                        if ($oldCoverPath && file_exists($oldCoverPath)) {
                            unlink($oldCoverPath);
                        }
                    }
                    $getOldCoverStmt->close();
                }

                // 3. อัปโหลดไฟล์รูปภาพใหม่
                $fileInfo = handleFileUpload($_FILES['fileInput'])[0];
                if ($fileInfo['success']) {
                    $picPath = $base_path . '/public/news_img/' . $fileInfo['fileName'];
                    
                    // 4. ตรวจสอบว่ามี Cover Photo ในฐานข้อมูลอยู่แล้วหรือไม่
                    $checkExistingCoverStmt = $conn->prepare("SELECT COUNT(*) FROM dn_idia_doc WHERE idia_id = ? AND status = 1 AND del = 0");
                    $checkExistingCoverStmt->bind_param("i", $idia_id);
                    $checkExistingCoverStmt->execute();
                    $existingCount = $checkExistingCoverStmt->get_result()->fetch_row()[0];
                    $checkExistingCoverStmt->close();

                    if ($existingCount > 0) {
                        // 5. ถ้ามีอยู่แล้ว ให้อัปเดตข้อมูล
                        $updateCoverStmt = $conn->prepare("UPDATE dn_idia_doc
                            SET file_name = ?, file_size = ?, file_type = ?, file_path = ?, api_path = ?
                            WHERE idia_id = ? AND status = 1 AND del = 0");
                        if ($updateCoverStmt) {
                            $updateCoverStmt->bind_param(
                                "sisssi",
                                $fileInfo['fileName'],
                                $fileInfo['fileSize'],
                                $fileInfo['fileType'],
                                $fileInfo['filePath'],
                                $picPath,
                                $idia_id
                            );
                            $updateCoverStmt->execute();
                            $updateCoverStmt->close();
                        }
                    } else {
                        // 6. ถ้าไม่มี ให้แทรกข้อมูลใหม่
                        $fileColumns = ['idia_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'status'];
                        $fileValues = [$idia_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath, 1];
                        insertIntoDatabase($conn, 'dn_idia_doc', $fileColumns, $fileValues);
                    }
                    
                } else {
                    throw new Exception('Error uploading cover file: ' . ($fileInfo['fileName'] ?? 'unknown') . ' - ' . $fileInfo['error']);
                }
            }

            // จัดการรูปภาพใน Content (ภาษาไทย)
            if (isset($_FILES['image_files_th']) && is_array($_FILES['image_files_th']['name']) && $_FILES['image_files_th']['error'][0] !== UPLOAD_ERR_NO_FILE) {
                $fileInfos = handleFileUpload($_FILES['image_files_th']);
                foreach ($fileInfos as $fileInfo) {
                    if ($fileInfo['success']) {
                        $picPath = $base_path . '/public/news_img/' . $fileInfo['fileName'];
                        $fileColumns = ['idia_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path'];
                        $fileValues = [$idia_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath];
                        insertIntoDatabase($conn, 'dn_idia_doc', $fileColumns, $fileValues);
                    } else {
                        throw new Exception('Error uploading content file (TH): ' . ($fileInfo['fileName'] ?? 'unknown') . ' - ' . $fileInfo['error']);
                    }
                }
            }

            // จัดการรูปภาพใน Content (ภาษาอังกฤษ)
            if (isset($_FILES['image_files_en']) && is_array($_FILES['image_files_en']['name']) && $_FILES['image_files_en']['error'][0] !== UPLOAD_ERR_NO_FILE) {
                $fileInfos = handleFileUpload($_FILES['image_files_en']);
                foreach ($fileInfos as $fileInfo) {
                    if ($fileInfo['success']) {
                        $picPath = $base_path . '/public/news_img/' . $fileInfo['fileName'];
                        $fileColumns = ['idia_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'lang'];
                        $fileValues = [$idia_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath, 'en'];
                        insertIntoDatabase($conn, 'dn_idia_doc', $fileColumns, $fileValues);
                    } else {
                        throw new Exception('Error uploading content file (EN): ' . ($fileInfo['fileName'] ?? 'unknown') . ' - ' . $fileInfo['error']);
                    }
                }
            }

            // จัดการรูปภาพใน Content (ภาษาจีน)
            if (isset($_FILES['image_files_cn']) && is_array($_FILES['image_files_cn']['name']) && $_FILES['image_files_cn']['error'][0] !== UPLOAD_ERR_NO_FILE) {
                $fileInfos = handleFileUpload($_FILES['image_files_cn']);
                foreach ($fileInfos as $fileInfo) {
                    if ($fileInfo['success']) {
                        $picPath = $base_path . '/public/news_img/' . $fileInfo['fileName'];
                        $fileColumns = ['idia_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'lang'];
                        $fileValues = [$idia_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath, 'cn'];
                        insertIntoDatabase($conn, 'dn_idia_doc', $fileColumns, $fileValues);
                    } else {
                        throw new Exception('Error uploading content file (CN): ' . ($fileInfo['fileName'] ?? 'unknown') . ' - ' . $fileInfo['error']);
                    }
                }
            }
            
            // จัดการรูปภาพใน Content (ภาษาญี่ปุ่น)
            if (isset($_FILES['image_files_jp']) && is_array($_FILES['image_files_jp']['name']) && $_FILES['image_files_jp']['error'][0] !== UPLOAD_ERR_NO_FILE) {
                $fileInfos = handleFileUpload($_FILES['image_files_jp']);
                foreach ($fileInfos as $fileInfo) {
                    if ($fileInfo['success']) {
                        $picPath = $base_path . '/public/news_img/' . $fileInfo['fileName'];
                        $fileColumns = ['idia_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'lang'];
                        $fileValues = [$idia_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath, 'jp'];
                        insertIntoDatabase($conn, 'dn_idia_doc', $fileColumns, $fileValues);
                    } else {
                        throw new Exception('Error uploading content file (JP): ' . ($fileInfo['fileName'] ?? 'unknown') . ' - ' . $fileInfo['error']);
                    }
                }
            }
            
            // จัดการรูปภาพใน Content (ภาษาเกาหลี)
            if (isset($_FILES['image_files_kr']) && is_array($_FILES['image_files_kr']['name']) && $_FILES['image_files_kr']['error'][0] !== UPLOAD_ERR_NO_FILE) {
                $fileInfos = handleFileUpload($_FILES['image_files_kr']);
                foreach ($fileInfos as $fileInfo) {
                    if ($fileInfo['success']) {
                        $picPath = $base_path . '/public/news_img/' . $fileInfo['fileName'];
                        $fileColumns = ['idia_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'lang'];
                        $fileValues = [$idia_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath, 'kr'];
                        insertIntoDatabase($conn, 'dn_idia_doc', $fileColumns, $fileValues);
                    } else {
                        throw new Exception('Error uploading content file (KR): ' . ($fileInfo['fileName'] ?? 'unknown') . ' - ' . $fileInfo['error']);
                    }
                }
            }

            $response = array('status' => 'success', 'message' => 'edit save');
        }

    } elseif (isset($_POST['action']) && $_POST['action'] == 'delidia') {
        $idia_id = $_POST['id'] ?? '';
        $del = '1';
        
        $stmt = $conn->prepare("UPDATE dn_idia 
            SET del = ? 
            WHERE idia_id = ?");
        $stmt->bind_param("si", $del, $idia_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }
        $stmt->close();
        
        $stmt = $conn->prepare("UPDATE dn_idia_doc 
            SET del = ? 
            WHERE idia_id = ?");
        $stmt->bind_param("si", $del, $idia_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }
        $stmt->close();
        
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
            $whereClause .= " AND (subject_idia LIKE '%$searchValue%' OR subject_idia_en LIKE '%$searchValue%' OR subject_idia_cn LIKE '%$searchValue%' OR subject_idia_jp LIKE '%$searchValue%' OR subject_idia_kr LIKE '%$searchValue%')";
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
?>
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
    if (isset($_POST['action']) && $_POST['action'] == 'addproject') {
        $project_array = [
            'project_subject' => $_POST['project_subject'] ?? '',
            'project_description' => $_POST['project_description'] ?? '',
            'project_content'  => $_POST['project_content'] ?? '',
            'project_subject_en' => $_POST['project_subject_en'] ?? '',
            'project_description_en' => $_POST['project_description_en'] ?? '',
            'project_content_en'  => $_POST['project_content_en'] ?? '',
            'project_subject_cn' => $_POST['project_subject_cn'] ?? '',
            'project_description_cn' => $_POST['project_description_cn'] ?? '',
            'project_content_cn'  => $_POST['project_content_cn'] ?? '',
        ];
        
        $related_shops = $_POST['related_shops'] ?? [];

        if (isset($project_array)) {
            $stmt = $conn->prepare("INSERT INTO dn_project 
                (subject_project, description_project, content_project, subject_project_en, description_project_en, content_project_en, subject_project_cn, description_project_cn, content_project_cn, date_create) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $project_subject = $project_array['project_subject'];
            $project_description = $project_array['project_description'];
            $project_content = mb_convert_encoding($project_array['project_content'], 'UTF-8', 'auto');
            $project_subject_en = $project_array['project_subject_en'];
            $project_description_en = $project_array['project_description_en'];
            $project_content_en = mb_convert_encoding($project_array['project_content_en'], 'UTF-8', 'auto');
            $project_subject_cn = $project_array['project_subject_cn'];
            $project_description_cn = $project_array['project_description_cn'];
            $project_content_cn = mb_convert_encoding($project_array['project_content_cn'], 'UTF-8', 'auto');
            $current_date = date('Y-m-d H:i:s');

            $stmt->bind_param(
                "ssssssssss",
                $project_subject,
                $project_description,
                $project_content,
                $project_subject_en,
                $project_description_en,
                $project_content_en,
                $project_subject_cn,
                $project_description_cn,
                $project_content_cn,
                $current_date
            );

            if (!$stmt->execute()) {
                throw new Exception("Execute statement failed: " . $stmt->error);
            }
            $last_inserted_id = $conn->insert_id;
            
            if (!empty($related_shops)) {
                $stmt_shop_insert = $conn->prepare("INSERT INTO dn_project_shop (project_id, shop_id) VALUES (?, ?)");
                foreach ($related_shops as $shop_id) {
                    $stmt_shop_insert->bind_param("ii", $last_inserted_id, $shop_id);
                    $stmt_shop_insert->execute();
                }
                $stmt_shop_insert->close();
            }

            // แก้ไขการตรวจสอบไฟล์
            if (isset($_FILES['fileInput']) && $_FILES['fileInput']['error'][0] !== UPLOAD_ERR_NO_FILE) {
                $fileInfos = handleFileUpload($_FILES['fileInput']);
                foreach ($fileInfos as $fileInfo) {
                    if ($fileInfo['success']) {
                        $picPath = $base_path . '/public/news_img/' . $fileInfo['fileName'];
                        $fileColumns = ['project_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'status'];
                        $fileValues = [$last_inserted_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath, 1];
                        insertIntoDatabase($conn, 'dn_project_doc', $fileColumns, $fileValues);
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
                        $fileColumns = ['project_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path'];
                        $fileValues = [$last_inserted_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath];
                        insertIntoDatabase($conn, 'dn_project_doc', $fileColumns, $fileValues);
                    } else {
                        throw new Exception('Error uploading file: ' . ($fileInfo['fileName'] ?? 'unknown') . ' - ' . $fileInfo['error']);
                    }
                }
            }
            
            $response = array('status' => 'success', 'message' => 'save');
        }

    } elseif (isset($_POST['action']) && $_POST['action'] == 'editproject') {
        $project_array = [
            'project_id' => $_POST['project_id'] ?? '',
            'project_subject' => $_POST['project_subject'] ?? '',
            'project_description' => $_POST['project_description'] ?? '',
            'project_content'  => $_POST['project_content'] ?? '',
            'project_subject_en' => $_POST['project_subject_en'] ?? '',
            'project_description_en' => $_POST['project_description_en'] ?? '',
            'project_content_en'  => $_POST['project_content_en'] ?? '',
            'project_subject_cn' => $_POST['project_subject_cn'] ?? '',
            'project_description_cn' => $_POST['project_description_cn'] ?? '',
            'project_content_cn'  => $_POST['project_content_cn'] ?? '',
        ];

        $related_shops = $_POST['related_shops'] ?? [];

        if (!empty($project_array['project_id'])) {
            $stmt = $conn->prepare("UPDATE dn_project 
            SET subject_project = ?, 
            description_project = ?, 
            content_project = ?,
            subject_project_en = ?,
            description_project_en = ?,
            content_project_en = ?,
            subject_project_cn = ?,
            description_project_cn = ?,
            content_project_cn = ?,
            date_create = ? 
            WHERE project_id = ?");

            $project_subject = $project_array['project_subject'];
            $project_description = $project_array['project_description'];
            $project_content = mb_convert_encoding($project_array['project_content'], 'UTF-8', 'auto');
            $project_subject_en = $project_array['project_subject_en'] ?? '';
            $project_description_en = $project_array['project_description_en'] ?? '';
            $project_content_en = mb_convert_encoding($project_array['project_content_en'] ?? '', 'UTF-8', 'auto');
            $project_subject_cn = $project_array['project_subject_cn'] ?? '';
            $project_description_cn = $project_array['project_description_cn'] ?? '';
            $project_content_cn = mb_convert_encoding($project_array['project_content_cn'] ?? '', 'UTF-8', 'auto');
            $current_date = date('Y-m-d H:i:s');
            $project_id = $project_array['project_id'];

            $stmt->bind_param(
                "sssssssssi",
                $project_subject,
                $project_description,
                $project_content,
                $project_subject_en,
                $project_description_en,
                $project_content_en,
                $project_subject_cn,
                $project_description_cn,
                $project_content_cn,
                $current_date,
                $project_id
            );

            if (!$stmt->execute()) {
                throw new Exception("Execute statement failed: " . $stmt->error);
            }
            
            $stmt_delete_shops = $conn->prepare("DELETE FROM dn_project_shop WHERE project_id = ?");
            $stmt_delete_shops->bind_param("i", $project_id);
            $stmt_delete_shops->execute();
            $stmt_delete_shops->close();

            if (!empty($related_shops)) {
                $stmt_shop_insert = $conn->prepare("INSERT INTO dn_project_shop (project_id, shop_id) VALUES (?, ?)");
                foreach ($related_shops as $shop_id) {
                    $stmt_shop_insert->bind_param("ii", $project_id, $shop_id);
                    $stmt_shop_insert->execute();
                }
                $stmt_shop_insert->close();
            }
            
            // --- ส่วนที่แก้ไข: จัดการรูป Cover Photo ---
            if (isset($_FILES['fileInput']) && $_FILES['fileInput']['error'] == UPLOAD_ERR_OK) {
                // 1. ดึง path ของรูปเก่าเพื่อลบ
                $getOldCoverStmt = $conn->prepare("SELECT file_path FROM dn_project_doc WHERE project_id = ? AND status = 1 AND del = 0");
                if ($getOldCoverStmt) {
                    $getOldCoverStmt->bind_param("i", $project_id);
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
                    $checkExistingCoverStmt = $conn->prepare("SELECT COUNT(*) FROM dn_project_doc WHERE project_id = ? AND status = 1 AND del = 0");
                    $checkExistingCoverStmt->bind_param("i", $project_id);
                    $checkExistingCoverStmt->execute();
                    $existingCount = $checkExistingCoverStmt->get_result()->fetch_row()[0];
                    $checkExistingCoverStmt->close();

                    if ($existingCount > 0) {
                             // 5. ถ้ามีอยู่แล้ว ให้อัปเดตข้อมูล
                        $updateCoverStmt = $conn->prepare("UPDATE dn_project_doc
                            SET file_name = ?, file_size = ?, file_type = ?, file_path = ?, api_path = ?
                            WHERE project_id = ? AND status = 1 AND del = 0");
                        if ($updateCoverStmt) {
                            $updateCoverStmt->bind_param(
                                "sisssi",
                                $fileInfo['fileName'],
                                $fileInfo['fileSize'],
                                $fileInfo['fileType'],
                                $fileInfo['filePath'],
                                $picPath,
                                $project_id
                            );
                            $updateCoverStmt->execute();
                            $updateCoverStmt->close();
                        }
                    } else {
                        // 6. ถ้าไม่มี ให้แทรกข้อมูลใหม่
                        $fileColumns = ['project_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'status'];
                        $fileValues = [$project_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath, 1];
                        insertIntoDatabase($conn, 'dn_project_doc', $fileColumns, $fileValues);
                    }
                } else {
                    throw new Exception('Error uploading cover file: ' . ($fileInfo['fileName'] ?? 'unknown') . ' - ' . $fileInfo['error']);
                }
            }
            // --- สิ้นสุดส่วนที่แก้ไข ---
            
            // จัดการรูปภาพใน Content (ภาษาไทย)
            if (isset($_FILES['image_files_th']) && is_array($_FILES['image_files_th']['name']) && $_FILES['image_files_th']['error'][0] !== UPLOAD_ERR_NO_FILE) {
                $fileInfos = handleFileUpload($_FILES['image_files_th']);
                foreach ($fileInfos as $fileInfo) {
                    if ($fileInfo['success']) {
                        $picPath = $base_path . '/public/news_img/' . $fileInfo['fileName'];
                        $fileColumns = ['project_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path'];
                        $fileValues = [$project_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath];
                        insertIntoDatabase($conn, 'dn_project_doc', $fileColumns, $fileValues);
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
                        $fileColumns = ['project_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'lang'];
                        $fileValues = [$project_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath, 'en'];
                        insertIntoDatabase($conn, 'dn_project_doc', $fileColumns, $fileValues);
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
                        $fileColumns = ['project_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'lang'];
                        $fileValues = [$project_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath, 'cn'];
                        insertIntoDatabase($conn, 'dn_project_doc', $fileColumns, $fileValues);
                    } else {
                        throw new Exception('Error uploading content file (CN): ' . ($fileInfo['fileName'] ?? 'unknown') . ' - ' . $fileInfo['error']);
                    }
                }
            }
            
            $response = array('status' => 'success', 'message' => 'edit save');
        }

    } elseif (isset($_POST['action']) && $_POST['action'] == 'delproject') {
        $project_id = $_POST['id'] ?? '';
        $del = '1';
        
        $stmt = $conn->prepare("UPDATE dn_project 
            SET del = ? 
            WHERE project_id = ?");
        $stmt->bind_param("si", $del, $project_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }
        $stmt->close();
        
        $stmt = $conn->prepare("UPDATE dn_project_doc 
            SET del = ? 
            WHERE project_id = ?");
        $stmt->bind_param("si", $del, $project_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }
        $stmt->close();

        $stmt = $conn->prepare("UPDATE dn_project_shop 
            SET del = ? 
            WHERE project_id = ?");
        $stmt->bind_param("si", $del, $project_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }
        $stmt->close();
        
        $response = array('status' => 'success', 'message' => 'Delete');
        
    } elseif (isset($_POST['action']) && $_POST['action'] == 'getData_project') {
        $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $searchValue = isset($_POST['search']['value']) ? $conn->real_escape_string($_POST['search']['value']) : '';

        $orderIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
        $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

        $columns = ['project_id'];

        $whereClause = "del = 0";

        if (!empty($searchValue)) {
            $whereClause .= " AND (subject_project LIKE '%$searchValue%' OR subject_project_en LIKE '%$searchValue%' OR subject_project_cn LIKE '%$searchValue%')";
        }

        $orderBy = $columns[$orderIndex] . " " . $orderDir;

        $dataQuery = "SELECT project_id, subject_project, subject_project_en, subject_project_cn, date_create FROM dn_project 
                WHERE $whereClause
                ORDER BY $orderBy
                LIMIT $start, $length";

        $dataResult = $conn->query($dataQuery);
        $data = [];
        while ($row = $dataResult->fetch_assoc()) {
            $data[] = $row;
        }

        $Index = 'project_id';
        $totalRecords = getTotalRecords($conn, 'dn_project', $Index);
        $totalFiltered = getFilteredRecordsCount($conn, 'dn_project', $whereClause, $Index);

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
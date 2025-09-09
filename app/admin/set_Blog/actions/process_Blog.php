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
    if (isset($_POST['action']) && $_POST['action'] == 'addblog') {
        $blog_array = [
            'blog_subject' => $_POST['blog_subject'] ?? '',
            'blog_description' => $_POST['blog_description'] ?? '',
            'blog_content' => $_POST['blog_content'] ?? '',
            'blog_subject_en' => $_POST['blog_subject_en'] ?? '',
            'blog_description_en' => $_POST['blog_description_en'] ?? '',
            'blog_content_en' => $_POST['blog_content_en'] ?? '',
            'blog_subject_cn' => $_POST['blog_subject_cn'] ?? '',
            'blog_description_cn' => $_POST['blog_description_cn'] ?? '',
            'blog_content_cn' => $_POST['blog_content_cn'] ?? '',
            'blog_subject_jp' => $_POST['blog_subject_jp'] ?? '',
            'blog_description_jp' => $_POST['blog_description_jp'] ?? '',
            'blog_content_jp' => $_POST['blog_content_jp'] ?? '',
            'blog_subject_kr' => $_POST['blog_subject_kr'] ?? '',
            'blog_description_kr' => $_POST['blog_description_kr'] ?? '',
            'blog_content_kr' => $_POST['blog_content_kr'] ?? '',
        ];
        
        $related_projects = $_POST['related_projects'] ?? [];

        if (isset($blog_array)) {
            $stmt = $conn->prepare("INSERT INTO dn_blog 
                (subject_blog, description_blog, content_blog, subject_blog_en, description_blog_en, content_blog_en, subject_blog_cn, description_blog_cn, content_blog_cn, subject_blog_jp, description_blog_jp, content_blog_jp, subject_blog_kr, description_blog_kr, content_blog_kr, date_create) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $blog_subject = $blog_array['blog_subject'];
            $blog_description = $blog_array['blog_description'];
            $blog_content = mb_convert_encoding($blog_array['blog_content'], 'UTF-8', 'auto');
            $blog_subject_en = $blog_array['blog_subject_en'];
            $blog_description_en = $blog_array['blog_description_en'];
            $blog_content_en = mb_convert_encoding($blog_array['blog_content_en'], 'UTF-8', 'auto');
            $blog_subject_cn = $blog_array['blog_subject_cn'];
            $blog_description_cn = $blog_array['blog_description_cn'];
            $blog_content_cn = mb_convert_encoding($blog_array['blog_content_cn'], 'UTF-8', 'auto');
            $blog_subject_jp = $blog_array['blog_subject_jp'];
            $blog_description_jp = $blog_array['blog_description_jp'];
            $blog_content_jp = mb_convert_encoding($blog_array['blog_content_jp'], 'UTF-8', 'auto');
            $blog_subject_kr = $blog_array['blog_subject_kr'];
            $blog_description_kr = $blog_array['blog_description_kr'];
            $blog_content_kr = mb_convert_encoding($blog_array['blog_content_kr'], 'UTF-8', 'auto');
            $current_date = date('Y-m-d H:i:s');

            $stmt->bind_param(
                "ssssssssssssssss",
                $blog_subject,
                $blog_description,
                $blog_content,
                $blog_subject_en,
                $blog_description_en,
                $blog_content_en,
                $blog_subject_cn,
                $blog_description_cn,
                $blog_content_cn,
                $blog_subject_jp,
                $blog_description_jp,
                $blog_content_jp,
                $blog_subject_kr,
                $blog_description_kr,
                $blog_content_kr,
                $current_date
            );

            if (!$stmt->execute()) {
                throw new Exception("Execute statement failed: " . $stmt->error);
            }
            $last_inserted_id = $conn->insert_id;
            
            if (!empty($related_projects)) {
                $stmt_project_insert = $conn->prepare("INSERT INTO dn_blog_project (blog_id, project_id) VALUES (?, ?)");
                foreach ($related_projects as $project_id) {
                    $stmt_project_insert->bind_param("ii", $last_inserted_id, $project_id);
                    $stmt_project_insert->execute();
                }
                $stmt_project_insert->close();
            }

            // แก้ไขการตรวจสอบไฟล์
            if (isset($_FILES['fileInput']) && $_FILES['fileInput']['error'][0] !== UPLOAD_ERR_NO_FILE) {
                $fileInfos = handleFileUpload($_FILES['fileInput']);
                foreach ($fileInfos as $fileInfo) {
                    if ($fileInfo['success']) {
                        $picPath = $base_path . '/public/news_img/' . $fileInfo['fileName'];
                        $fileColumns = ['blog_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'status'];
                        $fileValues = [$last_inserted_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath, 1];
                        insertIntoDatabase($conn, 'dn_blog_doc', $fileColumns, $fileValues);
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
                        $fileColumns = ['blog_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path'];
                        $fileValues = [$last_inserted_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath];
                        insertIntoDatabase($conn, 'dn_blog_doc', $fileColumns, $fileValues);
                    } else {
                        throw new Exception('Error uploading file: ' . ($fileInfo['fileName'] ?? 'unknown') . ' - ' . $fileInfo['error']);
                    }
                }
            }
            
            $response = array('status' => 'success', 'message' => 'save');
        }

    } elseif (isset($_POST['action']) && $_POST['action'] == 'editblog') {
        $blog_array = [
            'blog_id' => $_POST['blog_id'] ?? '',
            'blog_subject' => $_POST['blog_subject'] ?? '',
            'blog_description' => $_POST['blog_description'] ?? '',
            'blog_content' => $_POST['blog_content'] ?? '',
            'blog_subject_en' => $_POST['blog_subject_en'] ?? '',
            'blog_description_en' => $_POST['blog_description_en'] ?? '',
            'blog_content_en' => $_POST['blog_content_en'] ?? '',
            'blog_subject_cn' => $_POST['blog_subject_cn'] ?? '',
            'blog_description_cn' => $_POST['blog_description_cn'] ?? '',
            'blog_content_cn' => $_POST['blog_content_cn'] ?? '',
            'blog_subject_jp' => $_POST['blog_subject_jp'] ?? '',
            'blog_description_jp' => $_POST['blog_description_jp'] ?? '',
            'blog_content_jp' => $_POST['blog_content_jp'] ?? '',
            'blog_subject_kr' => $_POST['blog_subject_kr'] ?? '',
            'blog_description_kr' => $_POST['blog_description_kr'] ?? '',
            'blog_content_kr' => $_POST['blog_content_kr'] ?? '',
        ];

        $related_projects = $_POST['related_projects'] ?? [];

        if (!empty($blog_array['blog_id'])) {
            $stmt = $conn->prepare("UPDATE dn_blog 
            SET subject_blog = ?, 
            description_blog = ?, 
            content_blog = ?,
            subject_blog_en = ?,
            description_blog_en = ?,
            content_blog_en = ?,
            subject_blog_cn = ?,
            description_blog_cn = ?,
            content_blog_cn = ?,
            subject_blog_jp = ?,
            description_blog_jp = ?,
            content_blog_jp = ?,
            subject_blog_kr = ?,
            description_blog_kr = ?,
            content_blog_kr = ?,
            date_create = ? 
            WHERE blog_id = ?");

            $blog_subject = $blog_array['blog_subject'];
            $blog_description = $blog_array['blog_description'];
            $blog_content = mb_convert_encoding($blog_array['blog_content'], 'UTF-8', 'auto');
            $blog_subject_en = $blog_array['blog_subject_en'] ?? '';
            $blog_description_en = $blog_array['blog_description_en'] ?? '';
            $blog_content_en = mb_convert_encoding($blog_array['blog_content_en'] ?? '', 'UTF-8', 'auto');
            $blog_subject_cn = $blog_array['blog_subject_cn'] ?? '';
            $blog_description_cn = $blog_array['blog_description_cn'] ?? '';
            $blog_content_cn = mb_convert_encoding($blog_array['blog_content_cn'] ?? '', 'UTF-8', 'auto');
            $blog_subject_jp = $blog_array['blog_subject_jp'] ?? '';
            $blog_description_jp = $blog_array['blog_description_jp'] ?? '';
            $blog_content_jp = mb_convert_encoding($blog_array['blog_content_jp'] ?? '', 'UTF-8', 'auto');
            $blog_subject_kr = $blog_array['blog_subject_kr'] ?? '';
            $blog_description_kr = $blog_array['blog_description_kr'] ?? '';
            $blog_content_kr = mb_convert_encoding($blog_array['blog_content_kr'] ?? '', 'UTF-8', 'auto');
            $current_date = date('Y-m-d H:i:s');
            $blog_id = $blog_array['blog_id'];

            $stmt->bind_param(
                "ssssssssssssssssi",
                $blog_subject,
                $blog_description,
                $blog_content,
                $blog_subject_en,
                $blog_description_en,
                $blog_content_en,
                $blog_subject_cn,
                $blog_description_cn,
                $blog_content_cn,
                $blog_subject_jp,
                $blog_description_jp,
                $blog_content_jp,
                $blog_subject_kr,
                $blog_description_kr,
                $blog_content_kr,
                $current_date,
                $blog_id
            );

            if (!$stmt->execute()) {
                throw new Exception("Execute statement failed: " . $stmt->error);
            }
            
            $stmt_delete_projects = $conn->prepare("DELETE FROM dn_blog_project WHERE blog_id = ?");
            $stmt_delete_projects->bind_param("i", $blog_id);
            $stmt_delete_projects->execute();
            $stmt_delete_projects->close();

            if (!empty($related_projects)) {
                $stmt_project_insert = $conn->prepare("INSERT INTO dn_blog_project (blog_id, project_id) VALUES (?, ?)");
                foreach ($related_projects as $project_id) {
                    $stmt_project_insert->bind_param("ii", $blog_id, $project_id);
                    $stmt_project_insert->execute();
                }
                $stmt_project_insert->close();
            }

            // จัดการรูป Cover Photo
            if (isset($_FILES['fileInput']) && $_FILES['fileInput']['error'] == UPLOAD_ERR_OK) {
                // 1. ดึง path ของรูปเก่าเพื่อลบ
                $getOldCoverStmt = $conn->prepare("SELECT file_path FROM dn_blog_doc WHERE blog_id = ? AND status = 1 AND del = 0");
                if ($getOldCoverStmt) {
                    $getOldCoverStmt->bind_param("i", $blog_id);
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
                    $checkExistingCoverStmt = $conn->prepare("SELECT COUNT(*) FROM dn_blog_doc WHERE blog_id = ? AND status = 1 AND del = 0");
                    $checkExistingCoverStmt->bind_param("i", $blog_id);
                    $checkExistingCoverStmt->execute();
                    $existingCount = $checkExistingCoverStmt->get_result()->fetch_row()[0];
                    $checkExistingCoverStmt->close();

                    if ($existingCount > 0) {
                        // 5. ถ้ามีอยู่แล้ว ให้อัปเดตข้อมูล
                        $updateCoverStmt = $conn->prepare("UPDATE dn_blog_doc
                            SET file_name = ?, file_size = ?, file_type = ?, file_path = ?, api_path = ?
                            WHERE blog_id = ? AND status = 1 AND del = 0");
                        if ($updateCoverStmt) {
                            $updateCoverStmt->bind_param(
                                "sisssi",
                                $fileInfo['fileName'],
                                $fileInfo['fileSize'],
                                $fileInfo['fileType'],
                                $fileInfo['filePath'],
                                $picPath,
                                $blog_id
                            );
                            $updateCoverStmt->execute();
                            $updateCoverStmt->close();
                        }
                    } else {
                        // 6. ถ้าไม่มี ให้แทรกข้อมูลใหม่
                        $fileColumns = ['blog_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'status'];
                        $fileValues = [$blog_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath, 1];
                        insertIntoDatabase($conn, 'dn_blog_doc', $fileColumns, $fileValues);
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
                        $fileColumns = ['blog_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path'];
                        $fileValues = [$blog_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath];
                        insertIntoDatabase($conn, 'dn_blog_doc', $fileColumns, $fileValues);
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
                        $fileColumns = ['blog_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'lang_tag'];
                        $fileValues = [$blog_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath, 'en'];
                        insertIntoDatabase($conn, 'dn_blog_doc', $fileColumns, $fileValues);
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
                        $fileColumns = ['blog_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'lang_tag'];
                        $fileValues = [$blog_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath, 'cn'];
                        insertIntoDatabase($conn, 'dn_blog_doc', $fileColumns, $fileValues);
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
                        $fileColumns = ['blog_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'lang_tag'];
                        $fileValues = [$blog_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath, 'jp'];
                        insertIntoDatabase($conn, 'dn_blog_doc', $fileColumns, $fileValues);
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
                        $fileColumns = ['blog_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'lang_tag'];
                        $fileValues = [$blog_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath, 'kr'];
                        insertIntoDatabase($conn, 'dn_blog_doc', $fileColumns, $fileValues);
                    } else {
                        throw new Exception('Error uploading content file (KR): ' . ($fileInfo['fileName'] ?? 'unknown') . ' - ' . $fileInfo['error']);
                    }
                }
            }

            $response = array('status' => 'success', 'message' => 'edit save');
        }

    } elseif (isset($_POST['action']) && $_POST['action'] == 'delblog') {
        $blog_id = $_POST['id'] ?? '';
        $del = '1';
        
        $stmt = $conn->prepare("UPDATE dn_blog 
            SET del = ? 
            WHERE blog_id = ?");
        $stmt->bind_param("si", $del, $blog_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }
        $stmt->close();
        
        $stmt = $conn->prepare("UPDATE dn_blog_doc 
            SET del = ? 
            WHERE blog_id = ?");
        $stmt->bind_param("si", $del, $blog_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }
        $stmt->close();

        $stmt = $conn->prepare("UPDATE dn_blog_project 
            SET del = ? 
            WHERE blog_id = ?");
        $stmt->bind_param("si", $del, $blog_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }
        $stmt->close();
        
        $response = array('status' => 'success', 'message' => 'Delete');
        
    } elseif (isset($_POST['action']) && $_POST['action'] == 'getData_blog') {
        $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $searchValue = isset($_POST['search']['value']) ? $conn->real_escape_string($_POST['search']['value']) : '';

        $orderIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
        $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

        $columns = ['blog_id'];

        $whereClause = "del = 0";

        if (!empty($searchValue)) {
            $whereClause .= " AND (subject_blog LIKE '%$searchValue%' OR subject_blog_en LIKE '%$searchValue%' OR subject_blog_cn LIKE '%$searchValue%' OR subject_blog_jp LIKE '%$searchValue%' OR subject_blog_kr LIKE '%$searchValue%')";
        }

        $orderBy = $columns[$orderIndex] . " " . $orderDir;

        $dataQuery = "SELECT blog_id, subject_blog, date_create FROM dn_blog 
                    WHERE $whereClause
                    ORDER BY $orderBy
                    LIMIT $start, $length";

        $dataResult = $conn->query($dataQuery);
        $data = [];
        while ($row = $dataResult->fetch_assoc()) {
            $data[] = $row;
        }

        $Index = 'blog_id';
        $totalRecords = getTotalRecords($conn, 'dn_blog', $Index);
        $totalFiltered = getFilteredRecordsCount($conn, 'dn_blog', $whereClause, $Index);

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
<?php
header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');
require_once(__DIR__ . '/../../../../lib/base_directory.php');
require_once(__DIR__ . '/../../../../lib/connect.php');
require_once(__DIR__ . '/../../../../inc/getFunctions.php');

global $base_path; // ทำให้ $base_path ใช้งานได้ทั่วถึง
global $base_path_admin; // ทำให้ $base_path_admin ใช้งานได้ทั่วถึง

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
    // Determine types for bind_param dynamically
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
    global $base_path; // เข้าถึง $base_path

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
    $maxFileSize = 5 * 1024 * 1024; // 5 MB

    $uploadResults = [];

    // ถ้าเป็นไฟล์เดียว (เช่น Cover Photo)
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
                $uploadFileDir = '../../../../public/news_img/'; // ใช้ Path นี้สำหรับจัดเก็บรูปภาพใน server
                
                // สร้างชื่อไฟล์ที่ไม่ซ้ำกัน
                $newFileName = uniqid() . '_' . preg_replace("/[^a-zA-Z0-9\.]/", "_", $fileName);
                $destFilePath = $uploadFileDir . $newFileName;
                $apiPath = $base_path . '/public/news_img/' . $newFileName; // ใช้ $base_path สำหรับ Path ที่จะเก็บใน DB เพื่อเรียกใช้งานบนเว็บ

                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }

                if (move_uploaded_file($fileTmpPath, $destFilePath)) {
                    $uploadResults[] = [
                        'success' => true,
                        'fileName' => $newFileName, // ชื่อไฟล์ใหม่ที่บันทึก
                        'originalFileName' => $fileName, // ชื่อไฟล์เดิม
                        'fileSize' => $fileSize,
                        'fileType' => $fileType,
                        'filePath' => $destFilePath,
                        'apiPath' => $apiPath // Path สำหรับการแสดงผลบนเว็บ
                    ];
                } else {
                    $uploadResults[] = [
                        'success' => false,
                        'fileName' => $fileName,
                        'error' => 'Error occurred while moving the uploaded file.',
                        'php_error' => $files_to_process['error'][$key] // Debugging PHP upload error
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
            // UPLOAD_ERR_NO_FILE (4) หมายถึงไม่มีไฟล์อัปโหลด ไม่ใช่ข้อผิดพลาด
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
        $current_date = date('Y-m-d H:i:s');

        // Insert into dn_shop table
        $stmt = $conn->prepare("INSERT INTO dn_shop (subject_shop, description_shop, content_shop, date_create, group_id, del) VALUES (?, ?, ?, ?, ?, 0)");
        if ($stmt === false) {
            throw new Exception("SQL Prepare failed for insert shop: " . $conn->error);
        }

        $stmt->bind_param(
            "ssssi",
            $shop_subject,
            $shop_description,
            $shop_content,
            $current_date,
            $group_id
        );

        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed for insert shop: " . $stmt->error);
        }

        $new_shop_id = $conn->insert_id; // Get the newly inserted shop_id

        // Handle Cover photo upload (fileInput)
        if (isset($_FILES['fileInput']) && $_FILES['fileInput']['error'] === UPLOAD_ERR_OK) {
            $fileInfos = handleFileUpload($_FILES['fileInput'], true); // true indicates single file
            if (!empty($fileInfos) && $fileInfos[0]['success']) {
                $fileInfo = $fileInfos[0];
                $fileColumns = ['shop_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'status'];
                $fileValues = [$new_shop_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $fileInfo['apiPath'], 1]; // status = 1 สำหรับ Cover photo
                if (!insertIntoDatabase($conn, 'dn_shop_doc', $fileColumns, $fileValues)) {
                    throw new Exception('Error inserting cover photo for new shop.');
                }
            } else {
                throw new Exception('Error uploading cover photo: ' . ($fileInfos[0]['error'] ?? 'Unknown error'));
            }
        } else {
             // ถ้าไม่มีการอัปโหลดไฟล์ ให้ throw error
            throw new Exception("Cover photo is required.");
        }


        // Handle content images upload (image_files)
        if (isset($_FILES['image_files']) && $_FILES['image_files']['error'][0] !== UPLOAD_ERR_NO_FILE) {
            $fileInfos = handleFileUpload($_FILES['image_files']);
            foreach ($fileInfos as $fileInfo) {
                if ($fileInfo['success']) {
                    $fileColumns = ['shop_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'status']; // เพิ่ม status ด้วย
                    $fileValues = [$new_shop_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $fileInfo['apiPath'], 0]; // status = 0 สำหรับ content images
                    if (!insertIntoDatabase($conn, 'dn_shop_doc', $fileColumns, $fileValues)) {
                        error_log('Error inserting content image: ' . $fileInfo['fileName']);
                        // ไม่ต้อง throw exception เพื่อให้ request สำเร็จหากมีแค่บางรูปที่อัปโหลดไม่ได้
                    }
                } else {
                    error_log('Error uploading content image: ' . ($fileInfo['fileName'] ?? 'N/A') . ' - ' . $fileInfo['error']);
                    // ไม่ต้อง throw exception เพื่อให้ request สำเร็จหากมีแค่บางรูปที่อัปโหลดไม่ได้
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

        if (empty($shop_array['shop_id'])) {
            throw new Exception("Shop ID is missing for editing.");
        }

        $shop_id = (int)$shop_array['shop_id']; // Cast to int for safety

        // Update dn_shop table
        $stmt = $conn->prepare("UPDATE dn_shop 
            SET subject_shop = ?, 
            description_shop = ?, 
            content_shop = ?, 
            date_create = ?, 
            group_id = ? 
            WHERE shop_id = ?");

        if ($stmt === false) {
            throw new Exception("SQL Prepare failed for update shop: " . $conn->error);
        }

        $shop_subject = $shop_array['shop_subject'];
        $shop_description = $shop_array['shop_description'];
        $shop_content = $shop_array['shop_content']; // ไม่ต้องแปลง encoding ถ้า frontend ส่งมาเป็น UTF-8 แล้ว

        $current_date = date('Y-m-d H:i:s');
        
        $stmt->bind_param(
            "ssssii",
            $shop_subject,
            $shop_description,
            $shop_content,
            $current_date,
            $group_id,
            $shop_id
        );

        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed for update shop: " . $stmt->error);
        }

        // Handle Cover photo upload (fileInput)
        if (isset($_FILES['fileInput']) && $_FILES['fileInput']['error'] === UPLOAD_ERR_OK) {
            // ลบรูป Cover photo เก่า (status = 1) ก่อน
            $deleteOldCoverStmt = $conn->prepare("UPDATE dn_shop_doc SET status = 0 WHERE shop_id = ? AND status = 1");
            if ($deleteOldCoverStmt === false) {
                throw new Exception("SQL Prepare failed for deleting old cover: " . $conn->error);
            }
            $deleteOldCoverStmt->bind_param("i", $shop_id);
            if (!$deleteOldCoverStmt->execute()) {
                throw new Exception("Execute statement failed for deleting old cover: " . $deleteOldCoverStmt->error);
            }
            $deleteOldCoverStmt->close();

            $fileInfos = handleFileUpload($_FILES['fileInput'], true); // true indicates single file
            if (!empty($fileInfos) && $fileInfos[0]['success']) {
                $fileInfo = $fileInfos[0];
                $fileColumns = ['shop_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'status'];
                $fileValues = [$shop_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $fileInfo['apiPath'], 1]; // status = 1 สำหรับ Cover photo
                if (!insertIntoDatabase($conn, 'dn_shop_doc', $fileColumns, $fileValues)) {
                    throw new Exception('Error inserting new cover photo.');
                }
            } else {
                throw new Exception('Error uploading cover photo: ' . ($fileInfos[0]['error'] ?? 'Unknown error'));
            }
        }

        // Handle content images upload (image_files)
        // ควรลบรูปภาพที่ไม่อยู่ใน content แล้ว หรือ update เฉพาะที่จำเป็น
        // ตัวอย่างนี้เป็นการเพิ่มรูปใหม่เข้าไปเท่านั้น ยังไม่ได้จัดการเรื่องลบรูปเก่าที่ไม่มีอยู่ใน content แล้ว
        if (isset($_FILES['image_files']) && $_FILES['image_files']['error'][0] !== UPLOAD_ERR_NO_FILE) {
            $fileInfos = handleFileUpload($_FILES['image_files']);
            foreach ($fileInfos as $fileInfo) {
                if ($fileInfo['success']) {
                    $fileColumns = ['shop_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'status']; // เพิ่ม status ด้วย
                    $fileValues = [$shop_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $fileInfo['apiPath'], 0]; // status = 0 สำหรับ content images
                    if (!insertIntoDatabase($conn, 'dn_shop_doc', $fileColumns, $fileValues)) {
                        error_log('Error inserting content image: ' . $fileInfo['fileName']);
                        // ไม่ต้อง throw exception เพื่อให้ request สำเร็จหากมีแค่บางรูปที่อัปโหลดไม่ได้
                    }
                } else {
                    error_log('Error uploading content image: ' . ($fileInfo['fileName'] ?? 'N/A') . ' - ' . $fileInfo['error']);
                    // ไม่ต้อง throw exception เพื่อให้ request สำเร็จหากมีแค่บางรูปที่อัปโหลดไม่ได้
                }
            }
        }
        $response = array('status' => 'success', 'message' => 'Shop updated successfully!');

    } elseif ($_POST['action'] == 'delshop') {
        $shop_id = $_POST['id'] ?? '';
        if (empty($shop_id)) {
            throw new Exception("Shop ID is missing for deletion.");
        }
        $del = '1';
        
        // Update the dn_shop table
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
        
        // Update the dn_shop_doc table
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

        $orderIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
        $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

        $columns = ['shop_id', 'subject_shop', 'date_create', 'main_group_name', 'sub_group_name']; // Added column names for ordering

        $whereClause = "s.del = 0"; // ต้องอ้างถึง alias ของตาราง

        if (!empty($searchValue)) {
            $whereClause .= " AND (s.subject_shop LIKE '%$searchValue%' OR sub.group_name LIKE '%$searchValue%' OR parent.group_name LIKE '%$searchValue%')";
        }

        $orderBy = $columns[$orderIndex] . " " . $orderDir;

        // Query สำหรับนับจำนวนทั้งหมด (ก่อน filter)
        $totalRecordsQuery = "SELECT COUNT(shop_id) FROM dn_shop WHERE del = 0";
        $totalRecordsResult = $conn->query($totalRecordsQuery);
        $totalRecords = $totalRecordsResult->fetch_row()[0];

        // Query สำหรับนับจำนวนที่ filter แล้ว
        $totalFilteredQuery = "SELECT COUNT(s.shop_id) 
                                FROM dn_shop s
                                LEFT JOIN dn_shop_groups sub ON s.group_id = sub.group_id
                                LEFT JOIN dn_shop_groups parent ON sub.parent_group_id = parent.group_id
                                WHERE $whereClause";
        $totalFilteredResult = $conn->query($totalFilteredQuery);
        $totalFiltered = $totalFilteredResult->fetch_row()[0];


        $dataQuery = "SELECT 
                            s.shop_id,
                            s.subject_shop,
                            s.date_create,
                            sub.group_name AS sub_group_name,
                            parent.group_name AS main_group_name
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
    error_log("Error in process_shop.php: " . $e->getMessage()); // Log error for debugging
}

if (isset($stmt)) {
    $stmt->close();
}
$conn->close();

echo json_encode($response);
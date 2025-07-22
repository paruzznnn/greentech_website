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
        // UPLOAD_ERR_NO_FILE (4) สำหรับกรณีที่ไม่มีไฟล์ถูกส่งมาเลย หรือโครงสร้างไม่ถูกต้อง
        // เราจะไม่ส่งคืน error หาก error เป็น UPLOAD_ERR_NO_FILE เพื่อให้โค้ดส่วนอื่นจัดการเอง
        if (isset($files['error']) && (is_array($files['error']) ? $files['error'][0] === UPLOAD_ERR_NO_FILE : $files['error'] === UPLOAD_ERR_NO_FILE)) {
             return []; // คืนค่าว่างเปล่าถ้าไม่มีไฟล์ แต่ไม่ใช่ error
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
                $uploadFileDir = '../../../../public/shop_img/'; // เปลี่ยนจาก news_img เป็น shop_img

                // สร้างชื่อไฟล์ที่ไม่ซ้ำกันสำหรับบันทึกลง server
                // โดยยังคงเก็บชื่อไฟล์เดิมไว้ใน $originalFileName เพื่อใช้บันทึกใน DB
                $uniquePrefix = uniqid() . '_'; // เพิ่ม unique prefix
                $serverFileName = $uniquePrefix . preg_replace("/[^a-zA-Z0-9\._-]/", "_", $fileName); // ทำให้ชื่อไฟล์สะอาดขึ้นสำหรับ server
                $destFilePath = $uploadFileDir . $serverFileName;
                $apiPath = $base_path . '/public/shop_img/' . $serverFileName; // Path สำหรับการแสดงผลบนเว็บ

                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }

                if (move_uploaded_file($fileTmpPath, $destFilePath)) {
                    $uploadResults[] = [
                        'success' => true,
                        'fileName' => $fileName, // ใช้ชื่อไฟล์เดิมสำหรับบันทึกใน DB (ซึ่งจะถูกใช้เป็น original_filename ใน DB)
                        'serverFileName' => $serverFileName, // ชื่อไฟล์ที่บันทึกบน server
                        // 'originalFileName' => $fileName, // ไม่ต้องใช้แล้ว เราจะใช้ fileName เก็บเป็น original
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
        // ตรวจสอบว่ามีไฟล์อัปโหลดมาหรือไม่ และไม่มี error
        if (isset($_FILES['fileInput']) && $_FILES['fileInput']['error'] === UPLOAD_ERR_OK) {
            $fileInfos = handleFileUpload($_FILES['fileInput'], true); // true indicates single file
            if (!empty($fileInfos) && $fileInfos[0]['success']) {
                $fileInfo = $fileInfos[0];
                // Use 'file_name' for both file_name and original_filename logic
                $fileColumns = ['shop_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'status'];
                $fileValues = [$new_shop_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $fileInfo['apiPath'], 1]; // status = 1 สำหรับ Cover photo
                if (!insertIntoDatabase($conn, 'dn_shop_doc', $fileColumns, $fileValues)) {
                    throw new Exception('Error inserting cover photo for new shop.');
                }
            } else {
                throw new Exception('Error uploading cover photo: ' . ($fileInfos[0]['error'] ?? 'Unknown error'));
            }
        } else {
            // ถ้าไม่มีการอัปโหลดไฟล์ หรือมีข้อผิดพลาดในการอัปโหลดไฟล์ (ไม่ใช่แค่ UPLOAD_ERR_NO_FILE) ให้ throw error
            if (!isset($_FILES['fileInput']) || $_FILES['fileInput']['error'] !== UPLOAD_ERR_NO_FILE) {
                throw new Exception("Cover photo is required or there was an upload error.");
            }
        }


        // Handle content images upload (image_files)
        if (isset($_FILES['image_files']) && $_FILES['image_files']['error'][0] !== UPLOAD_ERR_NO_FILE) {
            $fileInfos = handleFileUpload($_FILES['image_files']);
            foreach ($fileInfos as $fileInfo) {
                if ($fileInfo['success']) {
                    // Use 'file_name' for both file_name and original_filename logic
                    $fileColumns = ['shop_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'status'];
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
        $shop_content = $shop_array['shop_content'];

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

        // --- Start Handle Cover photo upload (fileInput) ---
        if (isset($_FILES['fileInput']) && $_FILES['fileInput']['error'] === UPLOAD_ERR_OK) {
            // 1. Get the path of the old cover photo from the database
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

            // 2. Upload the new cover photo
            $fileInfos = handleFileUpload($_FILES['fileInput'], true); // true indicates single file
            if (!empty($fileInfos) && $fileInfos[0]['success']) {
                $fileInfo = $fileInfos[0];

                // 3. Update or Insert the new cover photo record
                // Check if there's an existing cover photo record (status = 1)
                $checkExistingCoverStmt = $conn->prepare("SELECT COUNT(*) FROM dn_shop_doc WHERE shop_id = ? AND status = 1 AND del = 0");
                if ($checkExistingCoverStmt) {
                    $checkExistingCoverStmt->bind_param("i", $shop_id);
                    $checkExistingCoverStmt->execute();
                    $existingCount = $checkExistingCoverStmt->get_result()->fetch_row()[0];
                    $checkExistingCoverStmt->close();

                    if ($existingCount > 0) {
                        // Update existing cover photo record
                        $updateCoverStmt = $conn->prepare("UPDATE dn_shop_doc
                                SET file_name = ?, file_size = ?, file_type = ?, file_path = ?, api_path = ?
                                WHERE shop_id = ? AND status = 1 AND del = 0");
                        if ($updateCoverStmt === false) {
                            throw new Exception("SQL Prepare failed for updating cover photo: " . $conn->error);
                        }
                        $updateCoverStmt->bind_param(
                            "sisssi",
                            $fileInfo['fileName'], // Using fileName here
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
                        // No existing cover photo, insert a new one
                        $fileColumns = ['shop_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'status'];
                        $fileValues = [$shop_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $fileInfo['apiPath'], 1]; // status = 1 for Cover photo
                        if (!insertIntoDatabase($conn, 'dn_shop_doc', $fileColumns, $fileValues)) {
                            throw new Exception('Error inserting new cover photo.');
                        }
                    }

                    // 4. Delete the old file from the server if a new one was successfully uploaded and an old one existed
                    if ($oldCoverPath && file_exists($oldCoverPath)) {
                        unlink($oldCoverPath); // Delete the actual file
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


        // --- Start Handle content images from Summernote (new logic) ---
        // 1. Load the HTML content into a DOMDocument
        $dom = new DOMDocument();
        // Suppress warnings about malformed HTML
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">' . $shop_content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        $dom->encoding = 'UTF-8'; // Ensure UTF-8 encoding

        $imgs = $dom->getElementsByTagName('img');

        foreach ($imgs as $img) {
            $src = $img->getAttribute('src');
            $dataFilename = $img->getAttribute('data-filename');

            // If data-filename exists, use it to find the corresponding image in the database using 'file_name'
            if (!empty($dataFilename)) {
                $stmt_check = $conn->prepare("SELECT api_path FROM dn_shop_doc WHERE file_name = ? AND shop_id = ? AND status = 0 AND del = 0");
                if ($stmt_check) {
                    $stmt_check->bind_param("si", $dataFilename, $shop_id);
                    $stmt_check->execute();
                    $row_check = $stmt_check->get_result()->fetch_assoc();
                    $stmt_check->close();

                    if ($row_check && !empty($row_check['api_path'])) {
                        // If found, update the src attribute and remove data-filename
                        $newSrc = $row_check['api_path'];
                        $img->setAttribute('src', $newSrc);
                        $img->removeAttribute('data-filename'); // Remove data-filename as it's processed
                    } else {
                        // Log if image not found in DB for a data-filename image (might be a new image not yet saved to DB, or a broken reference)
                        error_log("Image with data-filename '{$dataFilename}' not found in dn_shop_doc for shop_id {$shop_id}.");
                        // If the src is still base64, Summernote might handle it on the client-side for new pastes.
                        // If it's a broken link, it will remain as such.
                    }
                } else {
                    error_log("SQL Prepare failed for checking dn_shop_doc: " . $conn->error);
                }
            }
            // Additional check for base64 images that might not have data-filename (less common with Summernote's default file upload handler)
            // This part might be redundant if Summernote always provides data-filename for uploaded/pasted images.
            // If Summernote can still produce base64 without data-filename (e.g., direct copy-paste from other sources), you might need to handle this.
            /*
            else if (strpos($src, 'data:') === 0) {
                 // This is a base64 encoded image. You might need to save it to a file
                 // and insert it into dn_shop_doc if it's new.
                 // This typically requires more complex logic to extract the image data,
                 // generate a unique filename, save it, and update the src.
                 error_log("Base64 image found in content without data-filename. Consider if this needs specific handling: " . substr($src, 0, 100) . "...");
            }
            */
        }
        $shop_content = $dom->saveHTML();
        // Remove the added XML declaration if present
        $shop_content = str_replace('<?xml encoding="UTF-8">', '', $shop_content);
        // Remove unwanted html, body, and doctype tags if they were added by loadHTML
        $shop_content = preg_replace('/<!DOCTYPE html[^>]*>/i', '', $shop_content);
        $shop_content = preg_replace('/<html[^>]*>(.*?)<\/html>/is', '$1', $shop_content);
        $shop_content = preg_replace('/<head[^>]*>(.*?)<\/head>/is', '', $shop_content);
        $shop_content = preg_replace('/<body[^>]*>(.*?)<\/body>/is', '$1', $shop_content);


        // Update the content_shop with the modified HTML
        $stmt_update_content = $conn->prepare("UPDATE dn_shop SET content_shop = ? WHERE shop_id = ?");
        if ($stmt_update_content === false) {
            throw new Exception("SQL Prepare failed for updating content_shop: " . $conn->error);
        }
        $stmt_update_content->bind_param("si", $shop_content, $shop_id);
        if (!$stmt_update_content->execute()) {
            throw new Exception("Execute statement failed for updating content_shop: " . $stmt_update_content->error);
        }
        $stmt_update_content->close();
        // --- End Handle content images from Summernote ---


        // Handle content images upload (image_files) - This section processes new file uploads
        // It's for images directly uploaded via a separate file input (if used), not images pasted into Summernote that are already part of the content string.
        if (isset($_FILES['image_files']) && $_FILES['image_files']['error'][0] !== UPLOAD_ERR_NO_FILE) {
            $fileInfos = handleFileUpload($_FILES['image_files']);
            foreach ($fileInfos as $fileInfo) {
                if ($fileInfo['success']) {
                    // Check if this file already exists for this shop_id and status=0 (content image)
                    // This prevents duplicate entries if a user uploads the same file again.
                    $checkDuplicateStmt = $conn->prepare("SELECT COUNT(*) FROM dn_shop_doc WHERE shop_id = ? AND file_name = ? AND status = 0 AND del = 0"); // Check against file_name
                    if ($checkDuplicateStmt) {
                        $checkDuplicateStmt->bind_param("is", $shop_id, $fileInfo['fileName']); // Use fileName here
                        $checkDuplicateStmt->execute();
                        $isDuplicate = $checkDuplicateStmt->get_result()->fetch_row()[0];
                        $checkDuplicateStmt->close();

                        if ($isDuplicate == 0) {
                            $fileColumns = ['shop_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'status'];
                            $fileValues = [$shop_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $fileInfo['apiPath'], 0]; // status = 0 สำหรับ content images
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
?>

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

// ปรับปรุง handleFileUpload ให้รับ path การอัปโหลด
// ปรับปรุง handleFileUpload ให้รับ path การอัปโหลด
function handleFileUpload($files, $uploadSubDir = 'img') // เพิ่ม $uploadSubDir
{
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
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
                    // กำหนด directory สำหรับอัปโหลดให้ถูกต้อง
                    $uploadFileDir = __DIR__ . '/../../../../public/' . $uploadSubDir . '/';
                    // สร้างชื่อไฟล์ใหม่เพื่อป้องกันชื่อซ้ำ
                    $newFileName = uniqid() . '.' . $fileExtension;
                    $destFilePath = $uploadFileDir . $newFileName;

                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }

                    if (move_uploaded_file($fileTmpPath, $destFilePath)) {
                        $uploadResults[] = [
                            'success' => true,
                            'fileName' => $newFileName, // ใช้ชื่อไฟล์ใหม่
                            'fileSize' => $fileSize,
                            'fileType' => $fileType,
                            'filePath' => $destFilePath,
                            // *** แก้ไขตรงนี้: สร้าง publicPath เป็น URL แบบเต็ม ***
                            'publicPath' => 'https://www.trandar.com/public/' . $uploadSubDir . '/' . $newFileName // Path ที่จะใช้ในฐานข้อมูล/หน้าเว็บ
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
                    'error' => 'No file uploaded or there was an upload error: ' . $files['error'][$key]
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
        $banner_subject = $_POST['banner_subject'] ?? '';
        $banner_description = $_POST['banner_description'] ?? '';
        $banner_content = $_POST['banner_content'] ?? '';

        $current_date = date('Y-m-d H:i:s');
        $image_path_for_banner_table = ''; // จะเก็บ path ของรูปภาพหลักสำหรับตาราง banner

        // Handle the main banner image upload (from input type file)
        if (isset($_FILES['fileInput']) && $_FILES['fileInput']['error'][0] === UPLOAD_ERR_OK) {
            $fileInfos = handleFileUpload($_FILES['fileInput'], 'img'); // อัปโหลดไปที่ /public/img/
            if (!empty($fileInfos) && $fileInfos[0]['success']) {
                $image_path_for_banner_table = $fileInfos[0]['publicPath'];
            } else {
                throw new Exception('Error uploading main banner image: ' . ($fileInfos[0]['error'] ?? 'Unknown error'));
            }
        } else {
             // ถ้าไม่มีการอัปโหลดไฟล์หลัก ให้แจ้งเตือน หรือตั้งค่าเป็นค่าว่าง
            // throw new Exception('No main banner image uploaded or an error occurred during upload.');
        }

        $stmt = $conn->prepare("INSERT INTO banner 
            (subject_banner, description_banner, content_banner, image_path, date_create) 
            VALUES (?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "sssss",
            $banner_subject,
            $banner_description,
            $banner_content,
            $image_path_for_banner_table, // บันทึก image_path ที่นี่
            $current_date
        );

        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }

        $last_inserted_id = $conn->insert_id;

        // Handle additional images within banner_content (if any, although this part seems to be intended for news_img)
        // If 'image_files' is for images embedded in content, ensure the path is correct.
        if (isset($_FILES['image_files']) && $_FILES['image_files']['error'][0] === UPLOAD_ERR_OK) {
            $fileInfos = handleFileUpload($_FILES['image_files'], 'img'); // อัปโหลดไปที่ /public/img/
            foreach ($fileInfos as $fileInfo) {
                if ($fileInfo['success']) {
                    $picPath = $fileInfo['publicPath']; // ใช้ publicPath สำหรับ api_path
                    $fileColumns = ['banner_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'status'];
                    $fileValues = [$last_inserted_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath, 1];
                    insertIntoDatabase($conn, 'banner_doc', $fileColumns, $fileValues);
                } else {
                    throw new Exception('Error uploading file from content: ' . $fileInfo['fileName'] . ' - ' . $fileInfo['error']);
                }
            }
        }

        $response = array('status' => 'success', 'message' => 'save');

    } elseif (isset($_POST['action']) && $_POST['action'] == 'editbanner') {
        $banner_id = $_POST['banner_id'] ?? '';
        $banner_subject = $_POST['banner_subject'] ?? '';
        $banner_description = $_POST['banner_description'] ?? '';
        $banner_content = $_POST['banner_content'] ?? '';

        if (!empty($banner_id)) {
            $current_date = date('Y-m-d H:i:s');
            $image_path_for_banner_table = '';

            // Check if a new main banner image is uploaded for update
            if (isset($_FILES['fileInput']) && $_FILES['fileInput']['error'][0] === UPLOAD_ERR_OK) {
                $fileInfos = handleFileUpload($_FILES['fileInput'], 'img'); // อัปโหลดไปที่ /public/img/
                if (!empty($fileInfos) && $fileInfos[0]['success']) {
                    $image_path_for_banner_table = $fileInfos[0]['publicPath'];

                    // Update image_path in banner table
                    $stmt_update_image = $conn->prepare("UPDATE banner SET image_path = ? WHERE banner_id = ?");
                    $stmt_update_image->bind_param("si", $image_path_for_banner_table, $banner_id);
                    if (!$stmt_update_image->execute()) {
                        throw new Exception("Update image_path failed: " . $stmt_update_image->error);
                    }
                    $stmt_update_image->close();

                    // Optional: Update banner_doc if it's meant for the main image
                    // If banner_doc is specifically for additional images in content, you might not need this.
                    // For now, assume it's for additional docs.
                } else {
                    throw new Exception('Error updating main banner image: ' . ($fileInfos[0]['error'] ?? 'Unknown error'));
                }
            }

            $stmt = $conn->prepare("UPDATE banner 
                SET subject_banner = ?, 
                description_banner = ?, 
                content_banner = ?, 
                date_create = ? 
                WHERE banner_id = ?");

            $stmt->bind_param(
                "ssssi",
                $banner_subject,
                $banner_description,
                $banner_content,
                $current_date,
                $banner_id
            );

            if (!$stmt->execute()) {
                throw new Exception("Execute statement failed: " . $stmt->error);
            }
            $stmt->close(); // Close this statement before starting new file uploads for banner_doc

            // Handle additional images within banner_content (if any) for update
            if (isset($_FILES['image_files']) && $_FILES['image_files']['error'][0] === UPLOAD_ERR_OK) {
                $fileInfos = handleFileUpload($_FILES['image_files'], 'img'); // อัปโหลดไปที่ /public/img/
                foreach ($fileInfos as $fileInfo) {
                    if ($fileInfo['success']) {
                        $picPath = $fileInfo['publicPath'];

                        // Instead of update, you might want to insert new entries into banner_doc
                        // or check if an entry exists and update it. For simplicity, if a new image
                        // is uploaded via 'image_files' it might be a new attachment.
                        $fileColumns = ['banner_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path'];
                        $fileValues = [$banner_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath];
                        insertIntoDatabase($conn, 'banner_doc', $fileColumns, $fileValues); // Insert new document
                    } else {
                        throw new Exception('Error uploading file for content update: ' . $fileInfo['fileName'] . ' - ' . $fileInfo['error']);
                    }
                }
            }

            $response = array('status' => 'success', 'message' => 'edit save');
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'delbanner') {
        $news_id = $_POST['id'] ?? '';
        $del = '1';

        // Update the `banner` table
        $stmt = $conn->prepare("UPDATE banner 
            SET del = ? 
            WHERE banner_id = ?"); 

        $stmt->bind_param(
            "si",
            $del,
            $news_id
        );

        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }
        $stmt->close();

        // Update the `banner_doc` table
        $stmt = $conn->prepare("UPDATE banner_doc 
            SET del = ? 
            WHERE banner_id = ?"); 

        $stmt->bind_param(
            "si",
            $del,
            $news_id
        );

        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }
        $stmt->close();

        $response = array('status' => 'success', 'message' => 'Delete');
    } elseif (isset($_POST['action']) && $_POST['action'] == 'getData_banner') {
        $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $searchValue = isset($_POST['search']['value']) ? $conn->real_escape_string($_POST['search']['value']) : '';

        $orderIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
        $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

        $columns = ['banner_id', 'subject_banner', 'date_create']; // เพิ่ม subject_banner สำหรับการค้นหา

        $whereClause = "del = 0";

        if (!empty($searchValue)) {
            $whereClause .= " AND (subject_banner LIKE '%$searchValue%')";
        }

        $orderBy = $columns[$orderIndex] . " " . $orderDir;

        $dataQuery = "SELECT banner_id, subject_banner, date_create, image_path FROM banner 
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
<?php
header('Content-Type: application/json');

include '../../config/connect_db.php';

date_default_timezone_set('Asia/Bangkok');

//================================================================
// ส่วนของการจัดการอัปโหลดไฟล์
//================================================================
function handleFileUpload($file, $base_path_admin = null, $base_path_web = null)
{
    $uploadResults = [];
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'];
    $maxFileSize = 20 * 1024 * 1024; // 20 MB

    if (isset($file['name']) && is_array($file['name'])) {
        foreach ($file['name'] as $key => $fileName) {
            if ($file['error'][$key] === UPLOAD_ERR_OK) {
                $fileTmpPath = $file['tmp_name'][$key];
                $fileSize = $file['size'][$key];
                $fileType = $file['type'][$key];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                if (in_array($fileExtension, $allowedExtensions) && $fileSize <= $maxFileSize) {
                    
                    // ตั้งชื่อไฟล์ใหม่ด้วย timestamp และ random string เพื่อป้องกันชื่อซ้ำ
                    $newFileName = time() . '-' . bin2hex(random_bytes(5)) . '.' . $fileExtension;

                    // กำหนด path สำหรับบันทึกไฟล์
                    $uploadFileDir = '../../../../public/news_img/';
                    $destFilePath = $uploadFileDir . $newFileName;

                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }

                    if (move_uploaded_file($fileTmpPath, $destFilePath)) {
                        // สร้าง URL ที่ถูกต้องสำหรับบันทึกลง database
                        $serverName = $_SERVER['SERVER_NAME'];
                        if ($serverName == 'localhost' || $serverName == '127.0.0.1') {
                            $apiPath = 'http://' . $serverName . '/trandar/public/news_img/' . $newFileName;
                        } else {
                            $apiPath = 'https://' . $serverName . '/public/news_img/' . $newFileName;
                        }

                        $uploadResults[] = [
                            'success' => true,
                            'fileName' => $newFileName,
                            'fileSize' => $fileSize,
                            'fileType' => $fileType,
                            'filePath' => $destFilePath,
                            'apiPath' => $apiPath
                        ];
                    } else {
                        $uploadResults[] = ['success' => false, 'message' => 'Error moving file.'];
                    }
                } else {
                    $uploadResults[] = ['success' => false, 'message' => 'Invalid file type or size.'];
                }
            } else {
                $uploadResults[] = ['success' => false, 'message' => 'File upload error: ' . $file['error'][$key]];
            }
        }
    } else {
        $uploadResults[] = ['success' => false, 'message' => 'No files uploaded or invalid format.'];
    }
    return $uploadResults;
}

//================================================================
// ฟังก์ชันสำหรับ insert ข้อมูล
//================================================================
function insertIntoDatabase($conn, $table, $columns, $values)
{
    $columnString = implode(", ", $columns);
    $placeholders = implode(", ", array_fill(0, count($columns), "?"));
    $sql = "INSERT INTO $table ($columnString) VALUES ($placeholders)";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        return ['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error];
    }

    $types = str_repeat('s', count($values));
    $stmt->bind_param($types, ...$values);

    if ($stmt->execute()) {
        $last_id = $conn->insert_id;
        $stmt->close();
        return ['status' => 'success', 'last_id' => $last_id];
    } else {
        $stmt->close();
        return ['status' => 'error', 'message' => 'Execute failed: ' . $stmt->error];
    }
}

$response = ['status' => 'error', 'message' => 'Invalid action'];

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $response = ['status' => 'error', 'message' => 'An error occurred.'];

    switch ($action) {
        case 'addservice_content':
            $service_subject = $_POST['service_subject'] ?? '';
            $service_description = $_POST['service_description'] ?? '';
            $service_content = $_POST['service_content'] ?? '';
            $date_create = date('Y-m-d H:i:s');

            $columns = ['service_subject', 'service_description', 'service_content', 'date_create', 'status'];
            $values = [$service_subject, $service_description, $service_content, $date_create, 1];
            $insert_result = insertIntoDatabase($conn, 'service_content', $columns, $values);

            if ($insert_result['status'] == 'success') {
                $last_inserted_id = $insert_result['last_id'];

                // Upload cover image
                if (isset($_FILES['fileInput']) && $_FILES['fileInput']['error'][0] != 4) {
                    $fileInfos = handleFileUpload($_FILES['fileInput']);
                    foreach ($fileInfos as $fileInfo) {
                        if ($fileInfo['success']) {
                            $picPath = $fileInfo['apiPath'];
                            
                            $fileColumns = ['service_content_id', 'file_name', 'file_size', 'file_type', 'file_path', 'api_path', 'status'];
                            $fileValues = [$last_inserted_id, $fileInfo['fileName'], $fileInfo['fileSize'], $fileInfo['fileType'], $fileInfo['filePath'], $picPath, 1];
                            insertIntoDatabase($conn, 'service_content_doc', $fileColumns, $fileValues);
                        }
                    }
                }

                $response = ['status' => 'success', 'message' => 'Service content added successfully.', 'id' => $last_inserted_id];
            } else {
                $response = ['status' => 'error', 'message' => 'Failed to add service content: ' . $insert_result['message']];
            }
            break;

        case 'getData_service_content':
            $draw = $_POST['draw'] ?? 1;
            $start = $_POST['start'] ?? 0;
            $length = $_POST['length'] ?? 10;
            $searchValue = $_POST['search']['value'] ?? '';

            // Total records without filtering
            $totalRecordsQuery = "SELECT COUNT(id) AS total FROM service_content";
            $totalResult = $conn->query($totalRecordsQuery);
            $totalRecords = $totalResult->fetch_assoc()['total'];

            // Query with filtering
            $query = "SELECT * FROM service_content WHERE 1=1";
            if (!empty($searchValue)) {
                $query .= " AND (service_subject LIKE '%$searchValue%' OR date_create LIKE '%$searchValue%')";
            }
            $query .= " ORDER BY id DESC LIMIT $start, $length";
            
            $result = $conn->query($query);
            $data = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
            }

            // Total records with filtering (same as total records if no filter)
            $filteredRecords = count($data); 

            $response = [
                "draw" => intval($draw),
                "recordsTotal" => intval($totalRecords),
                "recordsFiltered" => intval($filteredRecords),
                "data" => $data
            ];
            break;

        case 'delservice_content':
            $id = $_POST['id'] ?? 0;
            if ($id > 0) {
                $stmt = $conn->prepare("DELETE FROM service_content WHERE id = ?");
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    $response = ['status' => 'success', 'message' => 'Service content deleted successfully.'];
                } else {
                    $response = ['status' => 'error', 'message' => 'Failed to delete service content.'];
                }
                $stmt->close();
            }
            break;

        case 'editservice_content':
            $id = $_POST['id'] ?? 0;
            $service_subject = $_POST['service_subject'] ?? '';
            $service_description = $_POST['service_description'] ?? '';
            $service_content = $_POST['service_content'] ?? '';
            $date_update = date('Y-m-d H:i:s');

            if ($id > 0) {
                $stmt = $conn->prepare("UPDATE service_content SET service_subject=?, service_description=?, service_content=?, date_update=? WHERE id=?");
                $stmt->bind_param("ssssi", $service_subject, $service_description, $service_content, $date_update, $id);

                if ($stmt->execute()) {
                    $stmt->close();
                    
                    // Handle file uploads for images inside the content
                    $image_files = $_FILES['image_files'] ?? null;
                    if ($image_files && $image_files['error'][0] != 4) {
                        $fileInfos = handleFileUpload($image_files);
                        // No specific database table for content images in the original script,
                        // so we assume they are handled by the Summernote editor itself.
                        // The file upload is done, and the new URLs are in the content.
                    }

                    // Handle cover image
                    $existing_file = $_POST['existing_file'] ?? null; // Get old file path
                    if (isset($_FILES['fileInput']) && $_FILES['fileInput']['error'][0] != 4) {
                        $fileInfos = handleFileUpload($_FILES['fileInput']);
                        if ($fileInfos[0]['success']) {
                            $picPath = $fileInfos[0]['apiPath'];
                            
                            $stmt_update_file = $conn->prepare("UPDATE service_content_doc SET api_path = ? WHERE service_content_id = ?");
                            $stmt_update_file->bind_param("si", $picPath, $id);
                            $stmt_update_file->execute();
                            $stmt_update_file->close();
                        }
                    }

                    $response = ['status' => 'success', 'message' => 'Service content updated successfully.'];
                } else {
                    $response = ['status' => 'error', 'message' => 'Failed to update service content.'];
                }
            } else {
                $response = ['status' => 'error', 'message' => 'Invalid ID.'];
            }
            break;
    }
}

echo json_encode($response);
?>
<?php
header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');
require_once(__DIR__ . '/../../../../lib/base_directory.php');
require_once(__DIR__ . '/../../../../lib/connect.php');
require_once(__DIR__ . '/../../../../inc/getFunctions.php');

global $base_path;
global $base_path_admin;
global $conn;

// Helper function to handle file uploads
function handleSingleFileUpload($file, $base_path)
{
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $maxFileSize = 5 * 1024 * 1024; // 5 MB

    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileNameCmps = explode(".", $file['name']);
        $fileExtension = strtolower(end($fileNameCmps));
        $newFileName = time() . '-' . uniqid() . '.' . $fileExtension;

        if (in_array($fileExtension, $allowedExtensions) && $fileSize <= $maxFileSize) {
            $uploadFileDir = realpath(__DIR__ . '/../../../../public/news_img/') . '/';
            $destFilePath = $uploadFileDir . $newFileName;
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            if (move_uploaded_file($fileTmpPath, $destFilePath)) {
                $image_url = $base_path . '/public/news_img/' . $newFileName;
                return ['success' => true, 'url' => $image_url, 'message' => 'Image uploaded successfully.'];
            } else {
                return ['success' => false, 'error' => 'Error occurred while moving the uploaded file.'];
            }
        } else {
            return ['success' => false, 'error' => 'Invalid file type or file size exceeds limit.'];
        }
    }
    return ['success' => false, 'error' => 'No file uploaded or there was an upload error.'];
}

$response = ['status' => 'error', 'message' => 'Invalid action.'];

try {
    if (!isset($_POST['action'])) {
        throw new Exception("No action specified.");
    }

    $action = $_POST['action'];

    // Action: Upload image from Summernote
    if ($action == 'upload_image') {
        if (isset($_FILES['image_file'])) {
            $uploadResult = handleSingleFileUpload($_FILES['image_file'], $base_path);
            if ($uploadResult['success']) {
                $response = ['status' => 'success', 'url' => $uploadResult['url']];
            } else {
                $response['message'] = $uploadResult['error'];
            }
        } else {
            $response['message'] = 'No file was uploaded.';
        }
    }

    // Action: Add new content block
    elseif ($action == 'add_new_block') {
        $type = $_POST['type'] ?? '';
        $content = $_POST['content'] ?? '';
        $author = $_POST['author'] ?? '';
        $position = $_POST['position'] ?? '';
        $image_url = null;

        if (!empty($content)) {
            // Handle image file if uploaded
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == UPLOAD_ERR_OK) {
                $uploadResult = handleSingleFileUpload($_FILES['image_file'], $base_path);
                if ($uploadResult['success']) {
                    $image_url = $uploadResult['url'];
                } else {
                    $response['message'] = 'Failed to upload image: ' . $uploadResult['error'];
                    echo json_encode($response);
                    exit;
                }
            }
            
            $stmt = $conn->prepare("INSERT INTO about_content (type, content, image_url, author, position) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $type, $content, $image_url, $author, $position);

            if ($stmt->execute()) {
                $response = ['status' => 'success', 'message' => 'เพิ่มเนื้อหาใหม่เรียบร้อย'];
            } else {
                throw new Exception("Failed to insert into database: " . $stmt->error);
            }
            $stmt->close();
        } else {
            $response['message'] = 'กรุณากรอกเนื้อหา';
        }
    }

    // Action: Save all content blocks
    elseif ($action == 'save_all_blocks') {
        $ids = $_POST['ids'] ?? [];
        $types = $_POST['types'] ?? [];
        $contents = $_POST['contents'] ?? [];
        $authors = $_POST['authors'] ?? [];
        $positions = $_POST['positions'] ?? [];
        $images_old = $_POST['images_old'] ?? [];

        $uploaded_files = $_FILES['image_files'] ?? null;
        $image_urls_new = [];
        
        $hasError = false;
        $errorMessage = '';

        for ($i = 0; $i < count($ids); $i++) {
            $current_image_url = $images_old[$i];
            
            if (isset($uploaded_files['name'][$i]) && $uploaded_files['error'][$i] == UPLOAD_ERR_OK) {
                $file = [
                    'name' => $uploaded_files['name'][$i],
                    'type' => $uploaded_files['type'][$i],
                    'tmp_name' => $uploaded_files['tmp_name'][$i],
                    'error' => $uploaded_files['error'][$i],
                    'size' => $uploaded_files['size'][$i]
                ];
                $uploadResult = handleSingleFileUpload($file, $base_path);
                
                if ($uploadResult['success']) {
                    $current_image_url = $uploadResult['url'];
                } else {
                    $hasError = true;
                    $errorMessage = 'Failed to upload new image for block ID ' . $ids[$i] . ': ' . $uploadResult['error'];
                    break;
                }
            }

            $stmt = $conn->prepare("UPDATE about_content SET type=?, content=?, image_url=?, author=?, position=? WHERE id=?");
            $stmt->bind_param("sssssi",
                $types[$i],
                $contents[$i],
                $current_image_url,
                $authors[$i],
                $positions[$i],
                $ids[$i]
            );

            if (!$stmt->execute()) {
                $hasError = true;
                $errorMessage = "Failed to update database for ID " . $ids[$i] . ": " . $stmt->error;
                break;
            }
            $stmt->close();
        }

        if ($hasError) {
            throw new Exception($errorMessage);
        } else {
            $response = ['status' => 'success', 'message' => 'บันทึกการแก้ไขทั้งหมดเรียบร้อย'];
        }
    }

    // Action: Delete block (should be handled by delete_about_block.php, but can be added here)
    // The existing delete_about_block.php seems to be an independent file. Keep it that way for simplicity.
    // If you prefer to handle everything in one file, you would add an 'action' here.

} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
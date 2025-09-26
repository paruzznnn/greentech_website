<?php

// function sendStoreDataAsJson($url, $storeName, $storeOwner, $logoFileArray, $bannerFileArray, $authToken = null) {
//     $logoBase64 = base64_encode(file_get_contents($logoFileArray['tmp_name']));
//     $bannerBase64 = base64_encode(file_get_contents($bannerFileArray['tmp_name']));

//     $data = [
//         'store_name' => $storeName,
//         'store_owner' => $storeOwner,
//         'store_logo' => [
//             'filename' => $logoFileArray['name'],
//             'mime_type' => mime_content_type($logoFileArray['tmp_name']),
//             'base64_data' => $logoBase64,
//         ],
//         'store_banner' => [
//             'filename' => $bannerFileArray['name'],
//             'mime_type' => mime_content_type($bannerFileArray['tmp_name']),
//             'base64_data' => $bannerBase64,
//         ],
//     ];

//     $jsonData = json_encode($data);

//     $headers = [
//         'Content-Type: application/json',
//         'Content-Length: ' . strlen($jsonData),
//     ];

//     if ($authToken !== null) {
//         $headers[] = 'Authorization: Bearer ' . $authToken;
//     }

//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, $url);
//     curl_setopt($ch, CURLOPT_POST, true);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

//     $response = curl_exec($ch);

//     if (curl_errno($ch)) {
//         $error_msg = curl_error($ch);
//         curl_close($ch);
//         throw new Exception("cURL error: $error_msg");
//     }
//     curl_close($ch);

//     return $response;
// }

// try {

//     $apiUrl = 'http://localhost:3000/trandar_website/store/api/store/saveStore.php';
//     $token = 'my_secure_token_123';

//     $response = sendStoreDataAsJson($apiUrl,
//         $_POST['store_name'],
//         $_POST['store_owner'],
//         $_FILES['store_logo'],
//         $_FILES['store_banner'],
//         $token
//     );

//     echo "Response from server:\n";
//     echo $response;

// } catch (Exception $e) {
//     echo "Error: " . $e->getMessage();
// }


// function sendStoreDataMultipart($url, $storeName, $storeOwner, $logoFileArray, $bannerFileArray, $authToken = null) {
    
//     if (
//         $logoFileArray['error'] !== UPLOAD_ERR_OK || 
//         !is_uploaded_file($logoFileArray['tmp_name'])
//     ) {
//         throw new Exception("Logo file upload error.");
//     }

//     if (
//         $bannerFileArray['error'] !== UPLOAD_ERR_OK || 
//         !is_uploaded_file($bannerFileArray['tmp_name'])
//     ) {
//         throw new Exception("Banner file upload error.");
//     }

//     $postData = [
//         'store_name'    => $storeName,
//         'store_owner'   => $storeOwner,
//         'store_logo'    => new CURLFile(
//             $logoFileArray['tmp_name'],
//             mime_content_type($logoFileArray['tmp_name']),
//             $logoFileArray['name']
//         ),
//         'store_banner'  => new CURLFile(
//             $bannerFileArray['tmp_name'],
//             mime_content_type($bannerFileArray['tmp_name']),
//             $bannerFileArray['name']
//         ),
//     ];

//     $headers = [];
//     if ($authToken !== null) {
//         $headers[] = 'Authorization: Bearer ' . $authToken;
//     }

//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, $url);
//     curl_setopt($ch, CURLOPT_POST, true);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//     $response = curl_exec($ch);

//     if (curl_errno($ch)) {
//         $error_msg = curl_error($ch);
//         curl_close($ch);
//         throw new Exception("cURL error: $error_msg");
//     }

//     curl_close($ch);
//     return $response;
// }

// try {

//     $apiUrl = 'http://localhost:3000/trandar_website/store/api/store/saveStore.php';
//     $token = 'my_secure_token_123';
//     $response = sendStoreDataMultipart(
//         $apiUrl,
//         $_POST['store_name'],
//         $_SESSION['comp_id'],
//         $_FILES['store_logo'],
//         $_FILES['store_banner'],
//         $token
//     );

//     echo "Response from server:\n";
//     echo $response;

// } catch (Exception $e) {
//     echo "Error: " . $e->getMessage();
// }



// ============== ตัวอย่างฝั่งรับ API JSON ============================
// try {
//     $rawData = file_get_contents("php://input");
//     $data = json_decode($rawData, true);

//     if (json_last_error() !== JSON_ERROR_NONE) {
//         throw new Exception("Invalid JSON data");
//     }

//     if (
//         empty($data['store_name']) || 
//         empty($data['store_owner']) || 
//         empty($data['store_logo']) || 
//         empty($data['store_banner'])
//     ) {
//         throw new Exception("Missing required fields");
//     }

//     $logoData = $data['store_logo'];
//     $bannerData = $data['store_banner'];

//     foreach (['filename', 'mime_type', 'base64_data'] as $field) {
//         if (empty($logoData[$field]) || empty($bannerData[$field])) {
//             throw new Exception("Missing file fields in logo or banner");
//         }
//     }

//     function saveBase64File($base64Data, $filename, $uploadDir = '../../uploads/store/') {
//         if (!is_dir($uploadDir)) {
//             mkdir($uploadDir, 0755, true);
//         }
//         $filePath = $uploadDir . basename($filename);
//         $fileData = base64_decode($base64Data);

//         if ($fileData === false) {
//             throw new Exception("Base64 decoding failed for file $filename");
//         }

//         if (file_put_contents($filePath, $fileData) === false) {
//             throw new Exception("Failed to save file $filename");
//         }

//         return $filePath;
//     }

//     // บันทึกไฟล์ logo และ banner
//     $savedLogoPath = saveBase64File($logoData['base64_data'], $logoData['filename']);
//     $savedBannerPath = saveBase64File($bannerData['base64_data'], $bannerData['filename']);

//     // ตัวอย่าง: ประมวลผลข้อมูลอื่นๆ เช่น เก็บลงฐานข้อมูล ฯลฯ
//     // ...

//     // ตอบกลับสำเร็จ
//     $response = [
//         'status' => 'success',
//         'message' => 'Store data saved successfully',
//         'logo_path' => $savedLogoPath,
//         'banner_path' => $savedBannerPath,
//     ];

//     header('Content-Type: application/json');
//     echo json_encode($response);

// } catch (Exception $e) {
//     // ตอบกลับ error
//     http_response_code(400);
//     header('Content-Type: application/json');
//     echo json_encode([
//         'status' => 'error',
//         'message' => $e->getMessage()
//     ]);
// }

// ============== ตัวอย่างฝั่งรับ API FROM ============================
// try {
//     if (
//         empty($_POST['store_name']) || 
//         empty($_POST['store_owner']) || 
//         !isset($_FILES['store_logo']) || 
//         !isset($_FILES['store_banner'])
//     ) {
//         throw new Exception("Missing required form data or files.");
//     }

//     $storeName = $_POST['store_name'];
//     $storeOwner = $_POST['store_owner'];

//     $logoFile = $_FILES['store_logo'];
//     $bannerFile = $_FILES['store_banner'];

//     if ($logoFile['error'] !== UPLOAD_ERR_OK || $bannerFile['error'] !== UPLOAD_ERR_OK) {
//         throw new Exception("File upload error.");
//     }

//     $uploadDir = '../../uploads/store/';
//     if (!is_dir($uploadDir)) {
//         mkdir($uploadDir, 0755, true);
//     }

//     $logoPath = $uploadDir . basename($logoFile['name']);
//     $bannerPath = $uploadDir . basename($bannerFile['name']);

//     move_uploaded_file($logoFile['tmp_name'], $logoPath);
//     move_uploaded_file($bannerFile['tmp_name'], $bannerPath);

//     echo json_encode([
//         'status' => 'success',
//         'message' => 'Store saved',
//         'store_name' => $storeName,
//         'store_owner' => $storeOwner,
//         'logo_path' => $logoPath,
//         'banner_path' => $bannerPath,
//     ]);
// } catch (Exception $e) {
//     http_response_code(400);
//     echo json_encode([
//         'status' => 'error',
//         'message' => $e->getMessage()
//     ]);
// }

?>
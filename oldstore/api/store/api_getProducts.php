<?php
// session_start();
// header('Content-Type: application/json');
// require_once '../../lib/connect.php';
// require_once '../../lib/base_directory.php';


// $memberId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
// // ตรวจสอบว่า action ถูกส่งมาใน POST หรือไม่
// if (isset($_POST['action']) && $_POST['action'] === 'getProducts') {

//     $apiUrl = 'http://origami.local/api/store/getProduct.php?action=getProducts&comp_id=2';

//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, $apiUrl);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//     curl_setopt($ch, CURLOPT_TIMEOUT, 30); // เพิ่มการตั้งค่า timeout
//     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification

//     $response = curl_exec($ch);

//     if (curl_errno($ch)) {
//         echo json_encode(array('status' => 'error', 'message' => 'CURL Error: ' . curl_error($ch)));
//         curl_close($ch);
//         exit();
//     }

//     curl_close($ch);

//     $data = json_decode($response, true);

//     if (json_last_error() !== JSON_ERROR_NONE) {
//         echo json_encode(array('status' => 'error', 'message' => 'Invalid JSON data: ' . json_last_error_msg()));
//         exit();
//     }

//     if (isset($data['status']) && $data['status'] === 'success') {

//         $processedData = array();
//         foreach ($data['data'] as $key => $value) {
//             $processedData[$key] = $value;
//         }

//         echo json_encode(
//             array(
//                 'status' => 'success',
//                 'data' => $processedData,
//                 'member_id' => $memberId
//             )
//         );

//     } else {
//         echo json_encode(array('status' => 'error', 'message' => 'Failed to get product data.'));
//     }

// } 
?>

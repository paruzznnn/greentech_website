<?php
// if (isset($_POST['dataArr']['action']) && $_POST['dataArr']['action'] === 'changeSync') {

//     $dataArr = isset($_POST['dataArr']) ? $_POST['dataArr'] : '';
//     // http://origami.local/store-center/st_order_list.php

//     if (empty($dataArr)) {
//         echo json_encode(array('status' => 'error', 'message' => 'Missing matId or type.'));
//         exit();

//     }else{

//         // $apiAction = [
//         //     'action' => 'changeSync',
//         //     'matID' => $matId,
//         //     'type' => $type,
//         //     'comp_id' => '2'
//         // ];

//         $apiAction = $dataArr;
//         $file_path = 'apiGetRequest.php';

//         apiSendRequest($file_path, $apiAction);
//     }

// } 
// else {
//     echo json_encode(array('status' => 'error', 'message' => 'Invalid request or missing action.'));
// }


// function apiSendRequest($file_path, $apiAction){

//     $queryString = http_build_query($apiAction);
//     $apiUrl = 'http://localhost:3000/tdi_store/api/store/' . $file_path . '?' . $queryString;
//     $ch = curl_init();

//     curl_setopt($ch, CURLOPT_URL, $apiUrl);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//     curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Set timeout
//     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//     $response = curl_exec($ch);

//     // Check if any error occurs
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

//         // $processedData = array();
//         // foreach ($data['data'] as $key => $value) {
//         //     $processedData[$key] = $value;
//         // }

//         echo json_encode(
//             array(
//                 'response' => $data
//             )
//         );

//     } else {
//         echo json_encode(array('status' => 'error', 'message' => 'Failed to get data.'));
//     }

// }


// if($_POST['action'] == 'getOrder'){

//     $dataArr = [
//             'action' => 'getOrders',
//             'comp_id' => '2'
//         ];

//     $apiAction = $dataArr;
//     $file_path = 'apiGetRequest.php';
//     getOrderApiRequest($file_path, $apiAction);

// }
// if($_POST['action'] == 'upDatePriceTms'){

//     $priceTms = str_replace(',', '', $_POST['priceTms']);
//     // $_POST['shippingID']
//     // $priceTms
    
//     $dataArr = [
//         'action' => 'upDatePriceTms',
//         'shippingID' => $_POST['shippingID'],
//         'priceTms' => $priceTms
//     ];
//     $apiAction = $dataArr;
//     $file_path = 'apiGetRequest.php';
//     updatePriceTmsApiRequest($file_path, $apiAction);

// }


// function getOrderApiRequest($file_path, $apiAction){

//     $queryString = http_build_query($apiAction);
//     $apiUrl = 'http://localhost:3000/tdi_store/api/store/' . $file_path . '?' . $queryString;
//     $ch = curl_init();

//     curl_setopt($ch, CURLOPT_URL, $apiUrl);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//     curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Set timeout
//     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//     $response = curl_exec($ch);

//     // Check if any error occurs
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

//         // $processedData = array();
//         // foreach ($data['data'] as $key => $value) {
//         //     $processedData[$key] = $value;
//         // }

//         echo json_encode(
//             array(
//                 'response' => $data
//             )
//         );

//     } else {
//         echo json_encode(array('status' => 'error', 'message' => 'Failed to get data.'));
//     }

// }

// function updatePriceTmsApiRequest($file_path, $apiAction){

//     $queryString = http_build_query($apiAction);
//     $apiUrl = 'http://localhost:3000/tdi_store/api/store/' . $file_path . '?' . $queryString;
//     $ch = curl_init();

//     curl_setopt($ch, CURLOPT_URL, $apiUrl);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//     curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Set timeout
//     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//     $response = curl_exec($ch);

//     // Check if any error occurs
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

//         // $processedData = array();
//         // foreach ($data['data'] as $key => $value) {
//         //     $processedData[$key] = $value;
//         // }

//         echo json_encode(
//             array(
//                 'response' => $data
//             )
//         );

//     } else {
//         echo json_encode(array('status' => 'error', 'message' => 'Failed to get data.'));
//     }

// }


?>

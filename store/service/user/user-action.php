<?php
require_once '../../server/connect_sqli.php';
require_once '../../server/select_sqli.php';
require_once '../../server/insert_sqli.php';
require_once '../../server/update_sqli.php';
header('Content-Type: application/json');


/*------- Authorization AND TIME ZONE ---------- */
if (isset($_SESSION['user_timezone'])) {
    date_default_timezone_set($_SESSION['user_timezone']);
} else {
    date_default_timezone_set("UTC");
}
$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}
$authHeader = $headers['Authorization'];
$token = str_replace('Bearer ', '', $authHeader);
$validToken = "my_secure_token_123";
if ($token !== $validToken) {
    http_response_code(403);
    echo json_encode(["error" => "Invalid token"]);
    exit;
}
/*------------------------------------------*/

/*---------ACTION DATA -------------*/
$input = file_get_contents("php://input");
$dataJson = json_decode($input, true);
if ($dataJson == null) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid JSON"]);
    exit;
}
$action = $dataJson['action'];
$userId = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;
$timeZone = isset($_SESSION['user_timezone']) ? $_SESSION['user_timezone'] : '';
$dateNow = date('Y-m-d H:i:s');

// echo '<pre>';
// print_r($_SESSION);
// print_r($dataJson);
// echo '</pre>';
// exit;

if ($action == 'addAddress') {

    unset($dataJson['action']);
    $addresses = [];
    foreach ($dataJson as $key => $value) {
        if (preg_match('/^(.*?)_(\d+)$/', $key, $matches)) {
            $field = $matches[1];
            $index = $matches[2];
            $addresses[$index][$field] = $value;
        }
    }

    $checkIns = false;
    if (is_array($addresses) && count($addresses) > 0) {
        foreach ($addresses as $address) {
            // ข้ามถ้าไม่ใช่ array
            if (!is_array($address)) {
                continue;
            }
            // เตรียมข้อมูลที่ใช้ร่วมกัน
            $address_data = [
                'member_id' => $userId,
                'full_name' => $address['full_name'],
                'phone_number' => $address['phone_number'],
                'address_detail' => $address['address_detail'],
                'province_code' => $address['province'],
                'district_code' => $address['district'],
                'subdistrict_code' => $address['subdistrict'],
                'post_code' => $address['postalCode'],
                'status' => $address['setupShipping'],
                'timezone' => $timeZone
            ];
            // หากเป็นการลบที่อยู่
            if ($address['addressRemove'] != 0) {
                $address_data['del'] = $address['addressRemove'];
                $address_data['update_date'] = $dateNow;
                $conditions = [
                    'member_id'  => $userId,
                    'address_id' => $address['addressID']
                ];
                if (updateData($conn_cloudpanel, 'ecm_address', $address_data, $conditions)) {
                    $checkIns = true;
                }
                continue; // ไป address ถัดไป
            }
            // หากเป็นการเพิ่มที่อยู่ใหม่
            if ($address['addressID'] == 0) {
                $address_data['create_date'] = $dateNow;
                if (insertData($conn_cloudpanel, 'ecm_address', $address_data)) {
                    $checkIns = true;
                }
            } else { // เป็นการอัปเดตที่อยู่
                $address_data['update_date'] = $dateNow;
                $conditions = [
                    'address_id' => $address['addressID']
                ];
                if (updateData($conn_cloudpanel, 'ecm_address', $address_data, $conditions)) {
                    $checkIns = true;
                }
            }
        }
    }

    $response = [
        "status" => $checkIns
    ];
    http_response_code(200);
    echo json_encode($response);
    $conn_cloudpanel->close();
    exit;
} else {

    http_response_code(400);
    echo json_encode([
        "error" => "Unauthorized"
    ]);
    exit;
}

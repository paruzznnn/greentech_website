<?php
require_once '../server/connect_sqli.php';
require_once '../cookie/cookie_utils.php';
require_once '../server/insert_sqli.php';
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
$timeZone = isset($_SESSION['user_timezone']) ? $_SESSION['user_timezone'] : '';
$dateNow = date('Y-m-d H:i:s');

if ($action == "checkRegister") {

    try {
        $register_email = isset($dataJson['register_email']) ? (string) $dataJson['register_email'] : '';
        $register_password = isset($dataJson['register_password']) ? (string) $dataJson['register_password'] : '';
        $accept_policy = isset($dataJson['accept_policy']) && $dataJson['accept_policy'] == "on" ? 1 : 0;
        $hashed_password = password_hash($register_password, PASSWORD_DEFAULT);
        $register_data = [
            'email' => $register_email,
            'password' => $hashed_password,
            'accept_policy' => $accept_policy,
            'timezone' => $timeZone,
            'created_at' => $dateNow
        ];
        $ins_id = insertDataAndGetId($conn_cloudpanel, 'ecm_member', $register_data);
        $checkIns = $ins_id ? true : false;
        $data = [
            "id" => $ins_id, 
            "action" => "checkLogin"
        ];
        http_response_code(200);
        $response = [
            "data" => $data,
            "status" => $checkIns
        ];
        echo json_encode($response);
    } catch (mysqli_sql_exception $e) {
        http_response_code(400);
        $response = [
            "error" => "An error occurred.: " . $e->getMessage()
        ];
        echo json_encode($response);
        exit;
    } finally {
        $conn_cloudpanel->close();
        exit;
    }
    
} else {

    http_response_code(400);
    echo json_encode([
        "error" => "Unauthorized"
    ]);
    exit;
}

?>
<?php
require_once '../server/connect_sqli.php';
require_once '../cookie/cookie_utils.php';
require_once '../server/select_sqli.php';
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

if ($action == "checkLogin") {

    try {
        $userId = isset($dataJson['user_id']) ? (int)$dataJson['user_id'] : 0;

        if ($userId > 0) {
            $jwtData = generateJWT($userId);
            $cookiePrefs = getCookieSettings();
            setAutoCookie($cookiePrefs, $jwtData);
            $conditions = [['column' => 'member_id', 'operator' => '=', 'value' => $userId]];
            $memberItems = selectData($conn_cloudpanel, 'ecm_member', $conditions, '*');
            if (empty($memberItems)) {
                throw new Exception("ไม่พบข้อมูลสมาชิกที่มี ID = $userId");
            }
            $_SESSION['user'] = [
                'id' => $memberItems[0]['member_id'],
                'username' => $memberItems[0]['username'] ?? 'unknown',
                'role' => $memberItems[0]['role'] ?? 'user'
            ];
            http_response_code(200);
            echo json_encode(["status" => true]);
        } else {
            if (empty($dataJson['login_email'])) {
                throw new Exception("ไม่มีข้อมูล login_email");
            }
            $conditions = [['column' => 'email', 'operator' => '=', 'value' => $dataJson['login_email']]];
            $memberItems = selectData($conn_cloudpanel, 'ecm_member', $conditions, '*');
            if (empty($memberItems)) {
                throw new Exception("ไม่พบข้อมูลสมาชิกที่ใช้ email: " . $dataJson['login_email']);
            }
            echo '<pre>';
            print_r($memberItems );
            echo '</pre>';
            exit;

            http_response_code(200);
            echo json_encode([
                "status" => true,
                "data" => $memberItems
            ]);
            
        }
    } catch (Exception $e) {
        
        http_response_code(400);
        echo json_encode([
            "status" => false,
            "error" => $e->getMessage()
        ]);

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

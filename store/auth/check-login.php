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

try {

    if ($action == "checkLogin") {
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
                'username' => 'admin',
                'role' => 'user'
            ];

            http_response_code(200);
            echo json_encode(["status" => true]);
        } else {
            if (empty($dataJson['login_email']) || empty($dataJson['login_password'])) {
                throw new Exception("ต้องระบุ login_email และ login_password");
            }

            $loginEmail = $dataJson['login_email'];
            $inputPassword = $dataJson['login_password'];

            $conditions = [['column' => 'email', 'operator' => '=', 'value' => $loginEmail]];
            $memberItems = selectData($conn_cloudpanel, 'ecm_member', $conditions, '*');

            if (empty($memberItems)) {
                throw new Exception("ไม่พบข้อมูลสมาชิกที่ใช้ email: $loginEmail");
            }

            $member = $memberItems[0];
            $hashedPassword = $member['password'];

            if (!password_verify($inputPassword, $hashedPassword)) {
                throw new Exception("รหัสผ่านไม่ถูกต้อง");
            }

            $jwtData = generateJWT($member['member_id']);
            $cookiePrefs = getCookieSettings();
            setAutoCookie($cookiePrefs, $jwtData);

            $_SESSION['user'] = [
                'id' => $member['member_id'],
                'username' => 'admin',
                'role' => 'user'
            ];

            http_response_code(200);
            echo json_encode(["status" => true]);

            // "data" => [
            //     "member_id" => $member['member_id'],
            //     "email" => $member['email']
            // ]

        }
    } else {
        http_response_code(400);
        echo json_encode([
            "error" => "Unauthorized"
        ]);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        "status" => false,
        "error" => $e->getMessage()
    ]);
} finally {
    if ($conn_cloudpanel instanceof mysqli) {
        $conn_cloudpanel->close();
    }
    exit;
}

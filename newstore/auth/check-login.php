<?php
require_once '../server/connect_sqli.php';
require_once '../cookie/cookie_utils.php';
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


    //SETING COOKIE
    // $userId = 1;
    // $jwtData = generateJWT($userId);
    // $cookiePrefs = getCookieSettings();
    // setAutoCookie($cookiePrefs, $jwtData);

    $_SESSION['user'] = [
        'id' => 1,
        'username' => 'admin',
        'role' => 'user'
    ];


    // $data = [
    //     "value" => true
    // ];

    http_response_code(200);
    $response = [
        // "data" => $data
        "status" => true
    ];

    echo json_encode($response);
    exit;

}
else{

    http_response_code(400);
    echo json_encode([
        "error" => "Unauthorized"
    ]);
    exit;
}

?>
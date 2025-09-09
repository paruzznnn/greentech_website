<?php
require_once '../server/connect_sqli.php';
header('Content-Type: application/json; charset=utf-8');

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
$apiKey = str_replace('ApiKey ', '', $authHeader);
$validKey = "1234567890";
if ($apiKey !== $validKey) {
    http_response_code(403);
    echo json_encode(["error" => "Invalid ApiKey"]);
    exit;
}
/*------------------------------------------*/


$input = file_get_contents("php://input");
$data = json_decode($input, true);

if ($data && isset($data['message'], $data['userId'], $data['channel'])) {
    // sanitize input
    $message = trim($data['message']);
    $message = htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $userId = trim($data['userId']);
    $channel = trim($data['channel']);

    $logEntry = [
        'message' => $message,
        'userId' => $userId,
        'channel' => $channel,
        'timestamp' => date('c')  
    ];

    $logFile = 'chat_log.json';

    if (file_exists($logFile)) {
        $content = file_get_contents($logFile);
        $logs = json_decode($content, true);
        if (!is_array($logs)) {
            $logs = [];
        }
    } else {
        $logs = [];
    }

    $logs[] = $logEntry;
    $writeResult = file_put_contents($logFile, json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    if ($writeResult !== false) {
        echo json_encode(['status' => 'received']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถบันทึก log ได้']);
    }
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ครบ']);
}

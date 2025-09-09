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


$logFile = 'chat_log.json';
$userId = $_GET['userId'] ?? '';

$reply = "ยังไม่มีข้อความตอบกลับ";

if ($userId && file_exists($logFile)) {
    $content = file_get_contents($logFile);
    $logs = json_decode($content, true);

    if (is_array($logs) && count($logs) > 0) {
        // กรอง log ที่ตรงกับ userId ที่ส่งมา
        $userLogs = array_filter($logs, function($entry) use ($userId) {
            return isset($entry['userId']) && $entry['userId'] === $userId;
        });

        if (!empty($userLogs)) {
            // ดึงรายการสุดท้ายของ user นั้น
            $lastEntry = end($userLogs);
            $reply = "ขอบคุณสำหรับคำถาม: " . ($lastEntry['message'] ?? '');
        }
    }
}

echo json_encode(['reply' => $reply]);


<?php
require_once '../../server/connect_sqli.php';
require_once '../../server/select_sqli.php';
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
$action = $_GET['action'] ?? '';
$store_id = $_GET['store_id'] ?? 0;
$start = $_GET['start'] ?? 0;
$end = $_GET['end'] ?? 0;
switch ($action) {
    case 'getPartnerStores':
        handleGetPartnerStores($conn_ecm, $start, $end);
        break;
    case 'getPartnerStore':
        handleGetPartnerStore($conn_ecm, $store_id);
        break;
    default:
        http_response_code(400);
        echo json_encode(["error" => "Unauthorized"]);
        exit;
}

function handleGetPartnerStores($conn, $start, $end){
    try {
        $conditions = [];
        $limit = "$start, $end";
        $data = selectData(
            $conn, 
            'store_stores', 
            $conditions, 
            '*', 
            'store_id DESC', 
            $limit
        );
        http_response_code(200);
        echo json_encode(["data" => $data]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(["status" => false, "error" => $e->getMessage()]);
    } finally {
        if ($conn) $conn->close();
        exit;
    }
}

function handleGetPartnerStore($conn, $store_id){
    try {
        $conditions = [
            ['column' => 'store_id', 'operator' => '=', 'value' => $store_id]
        ];
        $data = selectData(
            $conn, 
            'store_stores', 
            $conditions, 
            '*'
        );
        http_response_code(200);
        echo json_encode(["data" => $data]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(["status" => false, "error" => $e->getMessage()]);
    } finally {
        if ($conn) $conn->close();
        exit;
    }
}
<?php
require_once '../../server/connect_sqli.php';
require_once '../../server/select_sqli.php';
require_once '../../server/insert_sqli.php';
// header('Content-Type: application/json');

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

/*---------ACTION DATA FROM FORM ----------*/
// $action = isset($_POST['action']) ? $_POST['action'] : '';
// $orderId = isset($_POST['orderId']) ? $_POST['orderId'] : 0;
// $userId = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;
$timeZone = isset($_SESSION['user_timezone']) ? $_SESSION['user_timezone'] : '';
$dateNow = date('Y-m-d H:i:s');

try {
    if (
        empty($_POST['store_name']) ||
        empty($_POST['store_owner']) ||
        !isset($_FILES['store_logo']) ||
        !isset($_FILES['store_banner'])
    ) {
        throw new Exception("Missing required form data or files.");
    }

    $storeName = $_POST['store_name'];
    $storeOwner = $_POST['store_owner'];

    $logoFile = $_FILES['store_logo'];
    $bannerFile = $_FILES['store_banner'];

    if ($logoFile['error'] !== UPLOAD_ERR_OK || $bannerFile['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("File upload error.");
    }

    $uploadDir = '../../uploads/store/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $logoPath = $uploadDir . basename($logoFile['name']);
    $bannerPath = $uploadDir . basename($bannerFile['name']);

    $logoUrl = $GLOBALS['BASE_WEB'] . 'uploads/store/' . basename($logoFile['name']);
    $bannerUrl = $GLOBALS['BASE_WEB'] . 'uploads/store/' . basename($bannerFile['name']);

    if (!move_uploaded_file($logoFile['tmp_name'], $logoPath)) {
        throw new Exception("File upload logo error.");
    }
    if (!move_uploaded_file($bannerFile['tmp_name'], $bannerPath)) {
        throw new Exception("File upload banner error.");
    }

    $store_data = [
        'owner_id'   => $storeOwner,
        'name'  => $storeName,
        'logo_url'  => $logoUrl,
        'banner_url'  => $bannerUrl,
        'created_at' => $dateNow
    ];
    $ins_id = insertDataAndGetId($conn_ecm, 'store_stores', $store_data);

    if (!$ins_id) {
        throw new Exception("Database insert failed");
    }

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Store saved',
        'store_name' => $storeName,
        'store_owner' => $storeOwner,
        'logo_path' => $logoPath,
        'banner_path' => $bannerPath,
        'logo_url' => $logoUrl,
        'banner_url' => $bannerUrl
    ]);
} catch (Exception $e) {

    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} finally {
    $conn_ecm->close();
    exit;
}

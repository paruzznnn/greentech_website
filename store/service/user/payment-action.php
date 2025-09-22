<?php
require_once '../../server/connect_sqli.php';
require_once '../../server/select_sqli.php';
require_once '../../server/insert_sqli.php';
require_once '../../PromptPay/lib/PromptPayQR.php';
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

/*---------ACTION DATA FROM FORM ----------*/
$action = isset($_POST['action']) ? $_POST['action'] : '';
$orderId = isset($_POST['orderId']) ? $_POST['orderId'] : 0;
$userId = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;
$timeZone = isset($_SESSION['user_timezone']) ? $_SESSION['user_timezone'] : '';
$dateNow = date('Y-m-d H:i:s');
if ($action == 'uploadSlip') {

    try {

        if (!isset($_FILES['proof'])) {
            throw new Exception("No file uploaded");
        }

        $file = $_FILES['proof'];
        $uploadDir = "../../uploads/proofs/";

        // สร้างโฟลเดอร์ถ้าไม่มี
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                throw new Exception("Failed to create upload directory");
            }
        }

        // ตั้งชื่อไฟล์ใหม่ ป้องกันชื่อซ้ำ
        // $fileName = time() . "_" . basename($file['name']);
        $fileName = time() . "_" . "slip";
        $targetPath = $uploadDir . $fileName;
        $fileUrl = $GLOBALS['BASE_WEB'] ."uploads/proofs/". $fileName;
        
        // อัปโหลดไฟล์
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception("Upload failed");
        }

        // เตรียมข้อมูลสำหรับ insert
        $slip_data = [
            'member_id'  => $userId,
            'order_id'   => $orderId,
            'file_name'  => $fileName,
            'file_path'  => $targetPath,
            'file_size'  => $file['size'],
            'file_type'  => $file['type'],
            'file_url'   => $fileUrl,
            'del'        => 0,
            'status'     => 1,
            'timezone'   => $timeZone,
            'created_at' => $dateNow
        ];

        // บันทึกลงฐานข้อมูล
        $ins_id = insertDataAndGetId($conn_cloudpanel, 'ecm_orders_slip', $slip_data);

        if (!$ins_id) {
            throw new Exception("Database insert failed");
        }

        http_response_code(200);
        echo json_encode([
            "status" => true
        ]);

    } catch (Exception $e) {

        // ถ้าเกิดข้อผิดพลาดระหว่าง upload ไฟล์ → ลบไฟล์ที่อัปโหลดไปแล้ว
        if (isset($targetPath) && file_exists($targetPath)) {
            unlink($targetPath);
        }

        http_response_code(500);
        echo json_encode([
            "status" => false,
            "error" => $e->getMessage()
        ]);
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

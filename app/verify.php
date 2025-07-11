<?php
require_once('../lib/connect.php');
global $conn;
header('Content-Type: application/json; charset=UTF-8');
date_default_timezone_set('Asia/Bangkok');
@session_start();

if (!isset($_GET['token']) || empty($_GET['token'])) {
    http_response_code(400);
    echo json_encode(['status' => false, 'message' => 'Missing token']);
    exit;
}

$token = $_GET['token'];

// --- ดึงข้อมูลจาก token ---
$sql = "SELECT * FROM mb_user WHERE token = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION['user_email'] = $row['email'];
    $_SESSION['user_role'] = 1; // Admin
    $_SESSION['logged_in'] = true;

    // ไปหน้า admin ได้เลย
    header("Location: admin/dashboard.php");
    exit;
}

// if ($result->num_rows === 0) {
//     http_response_code(401);
//     echo json_encode(['status' => false, 'message' => 'Invalid token']);
//     exit;
// }

$row = $result->fetch_assoc();

// --- ตรวจสอบว่าข้อมูลเพียงพอไหม ---
if (empty($row['fullname']) || empty($row['username']) || empty($row['password'])) {
    http_response_code(400);
    echo json_encode(['status' => false, 'message' => 'Incomplete user information']);
    exit;
}

// เตรียมแยกชื่อ-นามสกุลจาก fullname
// $fullname_parts = explode(' ', $row['fullname'], 2);
// $first_name = $fullname_parts[0];
// $last_name = $fullname_parts[1] ?? '';

// ดึงข้อมูลจาก token

$first_name        = $_GET['first_name'];
$last_name        = $_GET['last_name'];
$email        = $_GET['username'];
$password     = $_GET['password'];
$phone_number = $_GET['phone_number'] ?? ''; // กรณีรับเพิ่มจากเว็บนอก

// ตรวจสอบว่ามี email นี้อยู่ใน mb_user แล้วหรือยัง
$check = $conn->prepare("SELECT user_id FROM mb_user WHERE email = ? LIMIT 1");
$check->bind_param("s", $email);
$check->execute();
$check_result = $check->get_result();

if ($check_result->num_rows === 0) {
    // ยังไม่มี → เพิ่มใหม่
    $otp = rand(100000, 999999);
    $role_id = 1;

    $insert = $conn->prepare("
        INSERT INTO mb_user 
            (first_name, last_name, password, email, phone_number, verify, confirm_email, consent, generate_otp, date_create, role_id, token)
        VALUES (?, ?, ?, ?, ?, 1, 1, 1, ?, NOW(), ?, ?)
    ");
    $insert->bind_param("sssssisss", $first_name, $last_name, $password, $email, $phone_number, $otp, $role_id, $token);

    if (!$insert->execute()) {
        http_response_code(500);
        echo json_encode(['status' => false, 'message' => 'Insert failed']);
        exit;
    }
}

// สร้าง session login อัตโนมัติ
$_SESSION['user_email'] = $email;
$_SESSION['user_role'] = 1; // Admin
$_SESSION['logged_in'] = true;

// ไปหน้า admin ได้เลย
header("Location: admin/dashboard.php");
exit;
?>

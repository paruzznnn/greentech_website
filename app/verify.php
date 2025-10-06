<?php
    require_once('../lib/connect.php');
    require_once('../lib/utils.php');
    header('Content-Type: application/json; charset=UTF-8');
    date_default_timezone_set('Asia/Bangkok');
    session_start();
    if (empty($_POST['data'])) {
        header("Location: index.php");
        exit;
    }
    $data = json_decode(base64_decode($_POST['data']), true);
    if (!is_array($data)) {
        header("Location: index.php");
        exit;
    } 
 
    $oid            = trim($data['oid']          ?? '');
    $redirect_url   = trim($data['redirect_url'] ?? '');
    $firstname      = trim($data['firstname']    ?? '');
    $lastname       = trim($data['lastname']     ?? '');
    // *** แก้ไข: อนุญาตให้ Email เป็นค่าว่างได้ แต่ยังคงดึงมาเก็บไว้ในตัวแปร
    $email          = trim($data['email']        ?? ''); 
    $telephone      = trim($data['telephone']    ?? '');
    $avatar      = trim($data['avatar']    ?? '');
    $role_id        = 1;
    
    // ตรวจสอบหลักคือต้องมี OID เท่านั้น
    if (empty($oid)) {
        header("Location: index.php");
        exit;
    }
    
    // 1. ค้นหาผู้ใช้เดิมจาก token (oid)
    $stmt = $conn->prepare("SELECT * FROM mb_user WHERE token = ? LIMIT 1");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['status' => false, 'message' => 'Prepare failed: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("s", $oid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        // 1.1 ถ้าเจอผู้ใช้เดิม: ทำการล็อกอิน
        $row = $result->fetch_assoc();
        session_regenerate_id(true);
        // *** แก้ไข/ปรับปรุง: ใช้ email ที่มีอยู่ในฐานข้อมูลของผู้ใช้นี้ (ถ้ามี) แทน email ที่ส่งมาใหม่
        // เพื่อไม่ให้ email ใน session เปลี่ยนไปเป็นค่าว่างถ้า user เดิมมี email อยู่แล้ว
        $_SESSION['email']       = $row['email'] ?? $email;
        $_SESSION['role_id']     = (int) $role_id;
        $_SESSION['logged_in']   = true;
        
        $_SESSION['oid']         = $oid;
        $_SESSION['redirect_url'] = $redirect_url;
        $_SESSION['avatar'] = $avatar;
        header("Location: admin/dashboard.php");
        exit;
    }
    
    // 2. ถ้าไม่พบผู้ใช้เดิม: สร้างผู้ใช้ใหม่
    $generated_password = generateRandomPassword(); 
    $hashed_password = password_hash($generated_password, PASSWORD_DEFAULT);
    $otp = rand(100000, 999999);
    
    // การสร้างผู้ใช้ใหม่ยังคงต้องใส่ Email (ถึงแม้จะเป็นค่าว่าง) ลงในฐานข้อมูลตามโครงสร้างเดิม
    $insert = $conn->prepare("INSERT INTO mb_user (first_name, last_name, password, email, phone_number,verify, confirm_email, consent,generate_otp, date_create, token, del) VALUES (?, ?, ?, ?, ?, 1, 1, 1, ?, NOW(), ?, 0)");
    if (!$insert) {
        http_response_code(500);
        echo json_encode(['status' => false, 'message' => 'Prepare failed: ' . $conn->error]);
        exit;
    }
    $insert->bind_param(
        "sssssis",
        $firstname,
        $lastname,
        $hashed_password,
        $email, // $email อาจเป็นค่าว่าง แต่ถูกบันทึกตามเดิม
        $telephone,
        $otp,
        $oid
    );
    if (!$insert->execute()) {
        http_response_code(500);
        echo json_encode(['status' => false, 'message' => 'Insert failed: ' . $insert->error]);
        exit;
    }
    // บันทึกข้อมูลในตาราง acc_user_roles
    $new_user_id = $conn->insert_id; 
    $insert_role = $conn->prepare("INSERT INTO acc_user_roles (user_id, role_id) VALUES (?, ?)");
    if (!$insert_role) {
        http_response_code(500);
        echo json_encode(['status' => false, 'message' => 'Prepare failed for acc_user_roles: ' . $conn->error]);
        exit;
    }
    $insert_role->bind_param(
        "ii",
        $new_user_id,
        $role_id
    );
    if (!$insert_role->execute()) {
        http_response_code(500);
        echo json_encode(['status' => false, 'message' => 'Insert failed for acc_user_roles: ' . $insert_role->error]);
        exit;
    }
    
    // ตั้งค่า Session เพื่อล็อกอิน
    session_regenerate_id(true);
    $_SESSION['email']         = $email; // $email อาจเป็นค่าว่าง
    $_SESSION['role_id']       = $role_id;
    $_SESSION['logged_in']     = true;
    
    $_SESSION['oid']           = $oid;
    $_SESSION['redirect_url']  = $redirect_url;
    $_SESSION['avatar']  = $avatar;
    header("Location: admin/dashboard.php");
    exit;
?>
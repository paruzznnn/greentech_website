<?php
    require_once('../lib/connect.php');
    require_once('../lib/utils.php');
    header('Content-Type: application/json; charset=UTF-8');
    date_default_timezone_set('Asia/Bangkok');
    session_start();
    if (empty($_GET['data'])) {
        header("Location: index.php");
        exit;
    }
    $data = json_decode(base64_decode($_GET['data']), true);
    if (!is_array($data)) {
        header("Location: index.php");
        exit;
    } 
    $token      = trim($data['token']       ?? '');
    $code       = trim($data['code']        ?? '');
    $firstname  = trim($data['firstname']   ?? '');
    $lastname   = trim($data['lastname']    ?? '');
    $email      = trim($data['email']       ?? '');
    $telephone  = trim($data['telephone']    ?? '');
    $role_id    = 1;
    if (empty($token)) {
        header("Location: index.php");
        exit;
    }
    $stmt = $conn->prepare("SELECT * FROM mb_user WHERE token = ? LIMIT 1");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['status' => false, 'message' => 'Prepare failed: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        session_regenerate_id(true);
        $_SESSION['email'] = $email;
        $_SESSION['role_id']  = (int) $role_id;
        $_SESSION['logged_in']  = true;
        $_SESSION['token']      = $token;
        $_SESSION['code']       = $code;
        header("Location: admin/dashboard.php");
        exit;
    }
    $generated_password = generateRandomPassword(); 
    $hashed_password = password_hash($generated_password, PASSWORD_DEFAULT);
    $otp = rand(100000, 999999);
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
        $email,
        $telephone,
        $otp,
        $token
    );
    if (!$insert->execute()) {
        http_response_code(500);
        echo json_encode(['status' => false, 'message' => 'Insert failed: ' . $insert->error]);
        exit;
    }
    session_regenerate_id(true);
    $_SESSION['email']      = $email;
    $_SESSION['role_id']    = $role_id;
    $_SESSION['logged_in']  = true;
    $_SESSION['token']      = $token;
    $_SESSION['code']       = $code;
    header("Location: admin/dashboard.php");
    exit;
?>
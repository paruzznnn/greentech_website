<?php
    require_once('../lib/connect.php');
    global $conn;
    header('Content-Type: application/json; charset=UTF-8');
    date_default_timezone_set('Asia/Bangkok');
    @session_start();
    if (empty($_GET['data'])) {
        header("Location: index.php");
        exit;
    }
    $data = json_decode(base64_decode($_GET['data']), true);
    if (!is_array($data)) {
        header("Location: index.php");
        exit;
    }
    $token = trim($data['token'] ?? '');
    $firstname = trim($data['firstname'] ?? '');
    $lastname = trim($data['lastname'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = trim($data['password'] ?? '');
    $telephone = trim($data['telephone'] ?? '');
    if (empty($token) || empty($email) || empty($password)) {
        header("Location: index.php");
        exit;
    }
    $sql  = "SELECT * FROM mb_user WHERE token = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        session_regenerate_id(true);
        $_SESSION['user_email'] = $row['email'];
        $_SESSION['user_role']  = (int) $row['role_id'];
        $_SESSION['logged_in']  = true;
        header("Location: admin/dashboard.php");
        exit;
    }
    $otp = rand(100000, 999999);
    $role_id = 1;
    $insert = $conn->prepare("
        INSERT INTO mb_user (
            first_name, last_name, password, email, phone_number,
            verify, confirm_email, consent,
            generate_otp, date_create, role_id, token
        ) VALUES (?, ?, ?, ?, ?, 1, 1, 1, ?, NOW(), ?, ?)
    ");
    $insert->bind_param(
        "ssssssis",
        $firstname,
        $lastname,
        $password,
        $email,
        $telephone,
        $otp,
        $role_id,
        $token
    );
    if (!$insert->execute()) {
        http_response_code(500);
        echo json_encode([
            'status'  => false,
            'message' => 'Insert failed: ' . $insert->error
        ]);
        exit;
    }
    session_regenerate_id(true);
    $_SESSION['user_email'] = $email;
    $_SESSION['user_role']  = $role_id;
    $_SESSION['logged_in']  = true;
    header("Location: admin/dashboard.php");
    exit;
?>
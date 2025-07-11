<?php
session_start();
header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');
require_once '../../lib/connect.php';

$response = array('status' => '', 'message' => '');

try {
    if ($_POST['action'] == 'save_signup') {
        // Retrieve and parse register_data and consent_data
        $register_data = [];
        $consent_data = [];
        
        parse_str($_POST['register_data'], $register_data);
        parse_str($_POST['consent_data'], $consent_data);

        // Sanitize input for security
        $register_data = array_map('htmlspecialchars', $register_data);
        $consent_data = array_map('htmlspecialchars', $consent_data);

        // Check if username already exists
        $stmt = $conn->prepare("SELECT COUNT(user_id) as total FROM ecm_users WHERE username = ?");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        $stmt->bind_param("s", $register_data['username']);
        
        if (!$stmt->execute()) {
            throw new Exception("Count query failed: " . $stmt->error);
        }

        $row = $stmt->get_result()->fetch_assoc();

        if (intval($row['total']) <= 0) {
            // Insert new user data
            $stmt = $conn->prepare(
                "INSERT INTO ecm_users (email, username, password, phone, role_id, create_date, is_consent, is_verify) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $conn->error);
            }

            $hashed_password = password_hash($register_data['password'], PASSWORD_BCRYPT);
            $current_date = date('Y-m-d H:i:s');

            $stmt->bind_param(
                "ssssssii", 
                $register_data['email'], 
                $register_data['username'], 
                $hashed_password, 
                $register_data['phone'], 
                $_POST['role'],
                $current_date,
                $consent_data['consent'], 
                $consent_data['verify']
            );

            if (!$stmt->execute()) {
                throw new Exception("Execute statement failed: " . $stmt->error);
            }

            $response['status'] = 'succeed';
            $response['message'] = 'Signup completed successfully';

        } else {
            $response['status'] = 'error';
            $response['message'] = 'Username already exists';
        }
    }
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
}

if (isset($stmt)) {
    $stmt->close();
}
$conn->close();

echo json_encode($response);
?>

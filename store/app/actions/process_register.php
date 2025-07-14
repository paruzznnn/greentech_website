<?php
session_start();
header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');
require_once '../../lib/connect.php';

$response = array('status' => '', 'message' => '');

try {
    if ($_POST['action'] == 'save_signup') {
        // Parse input
        $register_data = [];
        $consent_data = [];

        parse_str($_POST['register_data'], $register_data);
        parse_str($_POST['consent_data'], $consent_data);

        // Escape input manually
        foreach ($register_data as $key => $value) {
            $register_data[$key] = mysqli_real_escape_string($conn, trim($value));
        }

        foreach ($consent_data as $key => $value) {
            $consent_data[$key] = mysqli_real_escape_string($conn, trim($value));
        }

        $username = $register_data['username'];

        // Check if username exists
        $sql_check = "SELECT COUNT(user_id) AS total FROM ecm_users WHERE username = '$username'";
        $result = mysqli_query($conn, $sql_check);
        if (!$result) {
            throw new Exception("Query failed: " . mysqli_error($conn));
        }

        $row = mysqli_fetch_assoc($result);

        if (intval($row['total']) <= 0) {
            // Insert new user
            $email = $register_data['email'];
            $username = $register_data['username'];
            $password = password_hash($register_data['password'], PASSWORD_BCRYPT);
            $phone = $register_data['phone'];
            $role = mysqli_real_escape_string($conn, $_POST['role']);
            $create_date = date('Y-m-d H:i:s');
            $is_consent = isset($consent_data['consent']) ? (int)$consent_data['consent'] : 0;
            $is_verify = isset($consent_data['verify']) ? (int)$consent_data['verify'] : 0;

            $sql_insert = "
                INSERT INTO ecm_users (email, username, password, phone, role_id, create_date, is_consent, is_verify)
                VALUES ('$email', '$username', '$password', '$phone', '$role', '$create_date', $is_consent, $is_verify)
            ";

            if (!mysqli_query($conn, $sql_insert)) {
                throw new Exception("Insert failed: " . mysqli_error($conn));
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

// $conn->close();
echo json_encode($response);
?>

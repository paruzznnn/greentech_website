<?php
header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');
require_once(__DIR__ . '/../../lib/connect.php');
require_once(__DIR__ . '/../../lib/send_mail.php');

$response = array('status' => '', 'message' => '');

function generateOTP($length = 6) {
    $digits = '0123456789';
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= $digits[rand(0, strlen($digits) - 1)];
    }
    return $otp;
}


try {
    // Check if the action is 'save_signup'
    if (isset($_POST['action']) && $_POST['action'] == 'save_signup') {

        // Capture the signup data from POST
        $register_data = array(
            'first_name' => $_POST['signUp_name'],
            'last_name' => $_POST['signUp_surname'],
            'email' => $_POST['signUp_email'],
            'phone' => $_POST['signUp_phone'],
            'password' => $_POST['signUp_password'],
            'confirm_password' => $_POST['signUp_confirm_password'],
            'consent' => $_POST['signUp_agree'],
            'verify' => $_POST['signUp_send_mail']
        );

        // Check if the email already exists
        $stmt = $conn->prepare("SELECT COUNT(user_id) as total FROM mb_user WHERE email = ?");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        $stmt->bind_param("s", $register_data['email']);
        
        if (!$stmt->execute()) {
            throw new Exception("Count query failed: " . $stmt->error);
        }

        $row = $stmt->get_result()->fetch_assoc();

        // Check if email already exists
        if (intval($row['total']) <= 0) {

            $otp = generateOTP();

            // Insert new user data
            $stmt = $conn->prepare(
                "INSERT INTO mb_user (first_name, last_name, password, email, phone_number, consent, verify, generate_otp, date_create) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $conn->error);
            }

            // Hash the password for security
            $hashed_password = password_hash($register_data['password'], PASSWORD_BCRYPT);
            $current_date = date('Y-m-d H:i:s');

            // Bind parameters
            $stmt->bind_param(
                "sssssiiss", 
                $register_data['first_name'], 
                $register_data['last_name'], 
                $hashed_password, 
                $register_data['email'], 
                $register_data['phone'], 
                $register_data['consent'], 
                $register_data['verify'], 
                $otp,
                $current_date
            );

            // Execute the statement
            if (!$stmt->execute()) {
                throw new Exception("Execute statement failed: " . $stmt->error);
            }

            // Get the last inserted ID
            $last_insert_id = $conn->insert_id;
            sendEmail($register_data['email'], 'register', $last_insert_id, $otp);

            $response['status'] = 'succeed';
            $response['message'] = 'Signup completed successfully';

        } else {
            $response['status'] = 'error';
            $response['message'] = 'Email already exists';
        }
    }
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
}

// Close the statement and connection
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();

// Return JSON response
echo json_encode($response);
?>

<?php
header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');
require_once('../../lib/connect.php');
global $conn;

function generateOTPnew($length = 6) {
    $digits = '0123456789';
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= $digits[rand(0, strlen($digits) - 1)];
    }
    return $otp;
}


try {

    if (isset($_POST['action']) && $_POST['action'] == 'sendOTP') {

        $otp_data = array(
            'user_id' => $_POST['userId'],
            'otp_code' => $_POST['otpCode']
        );

        $stmt = $conn->prepare("SELECT COUNT(user_id) as total FROM mb_user WHERE user_id = ? AND generate_otp = ?");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        $stmt->bind_param("ii", $otp_data['user_id'], $otp_data['otp_code']);

        if (!$stmt->execute()) {
            throw new Exception("Count query failed: " . $stmt->error);
        }

        $row = $stmt->get_result()->fetch_assoc();

        if (intval($row['total']) > 0) {

            $stmt = $conn->prepare(
                "UPDATE mb_user 
                SET generate_otp = ?, 
                confirm_email = ?
                WHERE user_id = ?"
            );

            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $conn->error);
            }

            $generate_otp = generateOTPnew();
            $confirm_email = 1; // The value for confirm_email

            // Bind parameters
            $stmt->bind_param(
                "iii",
                $generate_otp,
                $confirm_email,
                $otp_data['user_id'],
            );

            // Execute the statement
            if (!$stmt->execute()) {
                throw new Exception("Execute statement failed: " . $stmt->error);
            }

            $response['status'] = 'succeed';
            $response['message'] = 'OTP code verified successfully.';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Please enter OTP correctly.';
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

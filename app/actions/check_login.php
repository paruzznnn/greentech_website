<?php
header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');
require '../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Dotenv\Dotenv;

require_once(__DIR__ . '/../../lib/connect.php');

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate input
    if (empty($username) || empty($password)) {
        echo json_encode([
            "status" => "error",
            "message" => "Email and password are required"
        ]);
        exit();
    }

    // Corrected SQL query (removed the extra comma)
    $sql = "SELECT mb_user.*,
            acc_user_roles.role_id
            FROM mb_user
            LEFT JOIN acc_user_roles ON acc_user_roles.user_id = mb_user.user_id
            WHERE mb_user.email = ? AND confirm_email = ?";

    // Prepare statement
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode([
            "status" => "error",
            "message" => "Database error: Unable to prepare statement"
        ]);
        exit();
    }

    $confirm_email = 1;

    // Bind parameters and execute
    $stmt->bind_param("si", $username, $confirm_email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch user data
    $row = $result->fetch_assoc();

    // Check if user exists and password is correct
    if (isset($row) && is_array($row) && isset($row['password'])) {
        if ($row && password_verify($password, $row['password']) || $password == $row['password']) {
            
            // --- ส่วนที่ปรับปรุงเพื่อบล็อก Role 1 และ 2 ---
            $blocked_roles = [1, 2];
            if (in_array($row['role_id'], $blocked_roles)) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Permission denied for this role."
                ]);
                exit();
            }
            // --- สิ้นสุดส่วนที่ปรับปรุง ---

            $secret_key = $_ENV['JWT_SECRET_KEY'];
            $payload = array(
                "iss" => "", // Set your issuer here
                "iat" => time(),
                "exp" => time() + (60 * 60), // Expires in 1 hour
                "data" => array(
                    "user_id" => $row['user_id'],
                    "role_id" => $row['role_id']
                )
            );

            // Encode the JWT
            $jwt = JWT::encode($payload, $secret_key, 'HS256');

            echo json_encode([
                "status" => "success",
                "jwt" => $jwt
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Incorrect password or invalid user."
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Email has not been verified or information is not available."
        ]);
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
    exit();
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
    exit();
}
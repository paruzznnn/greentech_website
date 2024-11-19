<?php
header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');
require '../../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Dotenv\Dotenv;

require_once '../../../lib/connect.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
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
    $sql = "SELECT mb_user.* 
            FROM mb_user 
            WHERE mb_user.email = ?";

    // Prepare statement
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode([
            "status" => "error",
            "message" => "Database error: Unable to prepare statement"
        ]);
        exit();
    }

    // Bind parameters and execute
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch user data
    $row = $result->fetch_assoc();

    // Check if user exists and password is correct
    if ($row && password_verify($password, $row['password'])) {
        $secret_key = $_ENV['JWT_SECRET_KEY'];
        $payload = array(
            "iss" => "", // Set your issuer here
            "iat" => time(),
            "exp" => time() + (60 * 60), // Expires in 1 hour
            "data" => array(
                "user_id" => $row['user_id'],
                "user_pic" => '',
                "role" => $row['role_id']
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
            "message" => "Invalid password"
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

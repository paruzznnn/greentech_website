<?php
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

    if (empty($username) || empty($password)) {
        echo json_encode([
            "status" => "error",
            "message" => "Email and password are required"
        ]);
        exit();
    }

    print_r($_POST);
    exit;

    $sql = "SELECT ecm_users.*, umb_docs.file_path FROM mb_user 
    LEFT JOIN umb_docs ON ecm_users.user_id = umb_docs.member_id
    WHERE 
    ecm_users.email = ? 
    OR ecm_users.username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        if (password_verify($password, $row['password'])) {
            $secret_key = $_ENV['JWT_SECRET_KEY'];
            $payload = array(
                "iss" => "",
                "iat" => time(),
                "exp" => time() + (60 * 60), // Expires in 1 h
                "data" => array(
                    "user_id" => $row['user_id'],
                    "user_pic" => $row['file_path'],
                    "role" => $row['role_id']
                )
            );

            $jwt = JWT::encode($payload, $secret_key, 'HS256');

            echo json_encode([
                "status" => "success",
                "jwt" => $jwt
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Invalid email or password"
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid email or password"
        ]);
    }
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
?>

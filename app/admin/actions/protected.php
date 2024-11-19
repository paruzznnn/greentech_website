<?php
header('Access-Control-Allow-Origin: *'); // Replace * with your domain in production
header('Access-Control-Allow-Headers: Authorization, Content-Type');
session_start();
require '../../../vendor/autoload.php'; 
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $headers = getallheaders();

    if (isset($headers['Authorization'])) {
        $authHeader = $headers['Authorization'];
        $token = str_replace('Bearer ', '', $authHeader);

        try {

            $secret_key = $_ENV['JWT_SECRET_KEY'];
            $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));

            // print_r($decoded);
            // exit;

            // Check if token is expired
            if (time() > $decoded->exp) {

                $response = [
                    "status" => "error",
                    "message" => "Token has expired"
                ];

            } else {

                $user_id = $decoded->data->user_id;
                $user_pic = $decoded->data->user_pic;
                $role = $decoded->data->role;
                $iat = $decoded->iat;
                $exp = $decoded->exp;

                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['user_pic'] = $user_pic;
                    $_SESSION['role'] = $role;
                    $_SESSION['iat'] = $iat;
                    $_SESSION['exp'] = $exp;
                    
                    $response = [
                        "status" => "success",
                        "message" => "Access granted",
                        "data" => [
                            "role" => $role
                        ]
                    ];

            }
        } catch (Exception $e) {
            $response = [
                "status" => "error",
                "message" => "Invalid token: " . $e->getMessage()
            ];
        }
    } else {
        $response = [
            "status" => "error",
            "message" => "No token provided"
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    $response = [
        "status" => "error",
        "message" => "Invalid request method"
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
}
?>

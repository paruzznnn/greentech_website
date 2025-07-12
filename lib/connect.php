<?php
@session_start();
require __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if ($_SERVER['REQUEST_SCHEME'] == 'http') {
    $host = $_ENV['DB_HOST_HTTP'];
    $username = $_ENV['DB_USER_HTTP'];
    $password = $_ENV['DB_PASSWORD_HTTP'];
    $database = $_ENV['DB_NAME_HTTP'];
} else {
    $host = $_ENV['DB_HOST_HTTPS'];
    $username = $_ENV['DB_USER_HTTPS'];
    $password = $_ENV['DB_PASSWORD_HTTPS'];
    $database = $_ENV['DB_NAME_HTTPS'];
}


$conn = new mysqli($host, $username, $password, $database);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

ob_start(function ($buffer) {
    // ลบ http://localhost/trandar/
    $buffer = str_replace('http://localhost/trandar/', '', $buffer);
    // ลบ /trandar เฉยๆ ด้วย
    // $buffer = str_replace('/trandar', '', $buffer);
    return $buffer;
});


?>
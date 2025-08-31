<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
@session_start();
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function connectDB($host, $user, $pass, $dbname)
{
    $conn = new mysqli($host, $user, $pass, $dbname);
    $conn->set_charset("utf8mb4");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function webBasePath()
{
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

    $scheme = $isHttps ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $path = $isHttps ? '/store/' : '/trandar_website/store/';
    $base_path = $scheme . '://' . $host . $path;
    return $base_path;
}

$server_name = $_SERVER['SERVER_NAME'];
if ($server_name === 'localhost' || $server_name === '127.0.0.1') {
    $GLOBALS['conn_cloudpanel'] = connectDB($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME']);
    $GLOBALS['conn'] = connectDB($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME']);
} else {
    $GLOBALS['conn_cloudpanel'] = connectDB($_ENV['DB_HOST_PD'], $_ENV['DB_USER_PD'], $_ENV['DB_PASS_PD'], $_ENV['DB_NAME_PD']);
    $GLOBALS['conn'] = connectDB($_ENV['DB_HOST_PD'], $_ENV['DB_USER_PD'], $_ENV['DB_PASS_PD'], $_ENV['DB_NAME_PD']);
}
$GLOBALS['BASE_WEB'] = webBasePath();

function formatDateLocalized($dateString, $lang = 'th', $useBuddhistYear = true, $timezone = 'UTC')
{

    date_default_timezone_set($timezone);

    $timestamp = strtotime($dateString);
    if (!$timestamp) return "Invalid date";

    $day   = date("j", $timestamp);
    $month = date("m", $timestamp);
    $year  = date("Y", $timestamp);

    $months_th = [
        "01" => "มกราคม",
        "02" => "กุมภาพันธ์",
        "03" => "มีนาคม",
        "04" => "เมษายน",
        "05" => "พฤษภาคม",
        "06" => "มิถุนายน",
        "07" => "กรกฎาคม",
        "08" => "สิงหาคม",
        "09" => "กันยายน",
        "10" => "ตุลาคม",
        "11" => "พฤศจิกายน",
        "12" => "ธันวาคม"
    ];

    $months_en = [
        "01" => "January",
        "02" => "February",
        "03" => "March",
        "04" => "April",
        "05" => "May",
        "06" => "June",
        "07" => "July",
        "08" => "August",
        "09" => "September",
        "10" => "October",
        "11" => "November",
        "12" => "December"
    ];

    if ($lang === 'th') {
        $monthName = $months_th[$month];
        $year = $useBuddhistYear ? $year + 543 : $year;
    } else {
        $monthName = $months_en[$month];
    }

    return "$day $monthName $year";
}

function generateJWT($userId)
{
    $secretKey = $_ENV['JWT_SECRET'];

    // $expirationTime = $issuedAt + 60; 1 นาที
    // $expirationTime = $issuedAt + 86400; 1 วัน
    // $expirationTime = $issuedAt + (60 * 60 * 24 * 7); 7 วัน
    // $expirationTime = $issuedAt + (60 * 60 * 24 * 30); 30 วัน

    $issuedAt = time();
    $expirationTime = $issuedAt + 3600; //(1 ชั่วโมง)

    $payload = [
        'iat' => $issuedAt,
        'exp' => $expirationTime,
        'uid' => $userId
    ];

    $encode = JWT::encode($payload, $secretKey, 'HS256');
    $decoded = JWT::decode($encode, new Key($secretKey, 'HS256'));

    return [
        'token' => $encode,
        'data' => $decoded
    ];
}

function generateOtp($length = 4)
{
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

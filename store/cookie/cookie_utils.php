<?php
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function getCookieSettings(): array {
    $arrCookie = [];

    if (isset($_COOKIE['cookieSettings'])) {
        $cookieSettings = json_decode($_COOKIE['cookieSettings'], true);
        if (!empty($cookieSettings) && is_array($cookieSettings)) {
            foreach ($cookieSettings as $type => $accepted) {
                $arrCookie[$type] = $accepted;
            }
        }
    }

    return $arrCookie;
}

function setAutoCookie($cookiePrefs, $jwtData) {
    if (!empty($cookiePrefs)) {
        foreach ($cookiePrefs as $key => $value) {
            if ($value == 1) {
                switch ($key) {
                    case "necessary":
                        setAutoLoginCookie($jwtData);
                        break;
                    case "experience":
                        break;
                    case "performance":
                        break;
                }
            }
        }
    }
}


function setAutoLoginCookie($jwtData) {
    if (!isset($jwtData['token']) || !isset($jwtData['data']->exp)) {
        return false;
    }
    $token = $jwtData['token'];
    $expires = $jwtData['data']->exp;
    setcookie('autologin', $token, $expires, '/', '', false, true);
    return true;
}

function checkAutoLoginCookie() {
    if (!isset($_COOKIE['autologin'])) {
        return false;
    }

    $token = $_COOKIE['autologin'];
    $secretKey = $_ENV['JWT_SECRET'];

    try {
        $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
        if (time() < $decoded->exp) {
            return $decoded->uid;  
        } else {
            return false;
        }
    } catch (Exception $e) {
        return false;
    }
}



?>
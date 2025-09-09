<?php
@session_start();
session_destroy();

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

$scheme = $isHttps ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$path = $isHttps ? '/store/' : '/trandar_website/store/';
$base_path = $scheme . '://' . $host . $path;
header('Location: ' . $base_path);
exit;
?>
<?php
$protocol = $_SERVER['REQUEST_SCHEME'];
$host = $_SERVER['HTTP_HOST'];
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$basePath = sprintf('%s://%s%s', $protocol, $host, $scriptDir);
$urlParts = parse_url($basePath);


$path = isset($urlParts['path']) ? $urlParts['path'] : '/';
$port = isset($urlParts['port']) ? ':'.$urlParts['port'] : '';
// $newPath = dirname($path, 1);

// Array
// (
//     [scheme] => http
//     [host] => localhost
//     [port] => 3000
//     [path] => /tdi_store/app
// )

$fixedPath = '/allable/app/admin/';
$newPath = '/';

// สร้าง path สำหรับ public folder
$publicPath = $urlParts['scheme'] . '://' . $urlParts['host'] . $port . '/allable/public';


$base_Path = $urlParts['scheme'] . '://' . $urlParts['host'] . $port . $newPath;
$base_PathAdmin = $urlParts['scheme'] . '://' . $urlParts['host'] . $port . $fixedPath;



$GLOBALS["base_path_admin"] = $base_PathAdmin;
$GLOBALS["base_path"] = $base_Path;
$GLOBALS["public_path"] = $publicPath;

?>


<?php
$protocol = $_SERVER['REQUEST_SCHEME'];
$host = $_SERVER['HTTP_HOST'];
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$basePath = sprintf('%s://%s%s', $protocol, $host, $scriptDir);
$urlParts = parse_url($basePath);


$path = isset($urlParts['path']) ? $urlParts['path'] : '/';
$port = isset($urlParts['port']) ? ':'.$urlParts['port'] : '';
// $newPath = dirname($path, 1);

$newPath = '/';

// Array
// (
//     [scheme] => http
//     [host] => localhost
//     [port] => 3000
//     [path] => /tdi_store/app
// )

$fixedPath = '/allable/app/admin';

$base_Path = $urlParts['scheme'] . '://' . $urlParts['host'] . $port . $newPath;
$base_PathAdmin = $urlParts['scheme'] . '://' . $urlParts['host'] . $port . $fixedPath . $newPath;


$GLOBALS["base_path_admin"] = $base_PathAdmin;
$GLOBALS["base_path"] = $base_Path;

?>


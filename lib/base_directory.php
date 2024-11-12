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

if($urlParts['scheme'] == 'http'){
    $fixedPath = '/allable/app/admin/';
    $newPath = '/allable/';
}else{
    $fixedPath = '/app/admin/';
    $newPath = '/';
}




$base_Path = $urlParts['scheme'] . '://' . $urlParts['host'] . $port . $newPath;
$base_PathAdmin = $urlParts['scheme'] . '://' . $urlParts['host'] . $port . $fixedPath;

$publicPath = $urlParts['scheme'] . '://' . $urlParts['host'] . $port . '/allable/public';



$GLOBALS["base_path"] = $base_Path;
$GLOBALS["base_path_admin"] = $base_PathAdmin;


$GLOBALS["public_path"] = $publicPath;

?>


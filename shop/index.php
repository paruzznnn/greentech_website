<?php
// Determine the request protocol
$isProtocol = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
// Set the file extension based on the protocol
$isFile = ($isProtocol === 'http') ? '.php' : '';

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$parts = explode('/', trim($uri, '/'));
$folder = $parts[0] ?? '';

// Redirect to the specified URL with the correct file extension
header("Location: /$folder/store/index" . $isFile);
exit();
?>
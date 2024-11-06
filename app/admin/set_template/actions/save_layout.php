<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');
require_once(__DIR__ . '/../../../../lib/base_directory.php');

global $base_path_admin;
global $base_path;
global $public_path;

$data = json_decode(file_get_contents('php://input'), true);
$htmlContent = $data['htmlContent'];
$mainFolder = $data['mainFolder'];
$subFolder = $data['subFolder'];
$fileName = $data['fileName'] ? $data['fileName'] : 'layuot';

if ($mainFolder && !$subFolder) {
    $filePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $mainFolder . DIRECTORY_SEPARATOR . $fileName.'.html';
} elseif ($mainFolder && $subFolder) {
    $filePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $mainFolder . DIRECTORY_SEPARATOR . $subFolder . DIRECTORY_SEPARATOR . $fileName.'.html';
} else {
    echo "Invalid folder information.";
    exit;
}

print_r($filePath);
exit;

if (file_put_contents($filePath, $htmlContent) !== false) {
    echo "File saved successfully to: " . $filePath;
} else {
    echo "Failed to save the file.";
}

// Construct the file URL based on the directory structure
// $fileUrl = '/downloads/' . $mainFolder . '/' . $subFolder . '/layout.html';

// // Return the URL of the saved file
// echo json_encode(['fileUrl' => $fileUrl]);

?>

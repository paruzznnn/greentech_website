<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');
require_once(__DIR__ . '/../../../../lib/base_directory.php');

global $base_path_admin;
global $base_path;
global $public_path;

$data = json_decode(file_get_contents('php://input'), true);
$htmlContent = $data['htmlContent'];

$inc_cdn = '';

$inc_cdn .= "
<link rel='icon' type='image/x-icon' href='../public/img/logo-ALLABLE-07.ico'>
<link href='../../../inc/jquery/css/jquery-ui.css' rel='stylesheet'>
<link href='../../../inc/bootstrap/css/bootstrap.min.css' rel='stylesheet'>
<link href='https://cdn.jsdelivr.net/npm/fontawesome5-fullcss@1.1.0/css/all.min.css' rel='stylesheet'>
<link rel='preconnect' href='https://fonts.googleapis.com'>
<link rel='preconnect' href='https://fonts.gstatic.com' crossorigin>
<link href='https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900&display=swap' rel='stylesheet'>
<link href='../../../inc/sweetalert2/css/sweetalert2.min.css' rel='stylesheet'>
<link href='../../../inc/select2/css/select2.min.css' rel='stylesheet'>
<link href='../css/index_.css' rel='stylesheet'>
";

$inc_cdn .= "
<script src='../../../inc/jquery/js/jquery-3.6.0.min.js'></script>
<script src='../../../inc/jquery/js/jquery-ui.min.js'></script>
<script src='../../../inc/bootstrap/js/bootstrap.min.js'></script>
<script src='../../../inc/sweetalert2/js/sweetalert2.all.min.js'></script>
<script src='../../../inc/select2/js/select2.min.js'></script>
";

$inc_cdn .= "
<style>
    .input-class {
        background-color: #f0f8ff;
        border: 1px solid #ccc; 
        padding: 10px; 
        margin: 5px;
    }

    .button-class {
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 10px 15px;
        cursor: pointer;
    }

    .select-class {
        background-color: #ffffff;
        border: 1px solid #ccc;
        padding: 5px;
        margin: 5px;
    }

    .textarea-class {
        background-color: #f9f9f9;
        border: 1px solid #ccc;
        padding: 10px;
    }

    .checkbox-class,
    .radio-class {
        margin-right: 5px;
    }

    .file-class {
        background-color: #ffffff;
        border: 1px solid #ccc;
        padding: 5px;
    }

    .color-class {
        padding: 5px;
        border: 1px solid #ccc;
    }
</style>
";

$inc_script = "
<script src='../js/index_.js'></script>
";

$inc_header = "
<?php include '../template/header.php'?>
";

$inc_php = "
<?php
include('../../../lib/permissions.php');
include('../../../lib/base_directory.php');
// checkPermissions();
?>
";


$position = strpos($htmlContent, '<!DOCTYPE html>');
if ($position !== false) {
    $htmlContent = substr_replace($htmlContent, $inc_php, $position, 0);
}

$htmlContent = str_replace('</head>', $inc_cdn . '</head>', $htmlContent);

$htmlContent = str_replace('<body>', $inc_header . '<body>', $htmlContent);
$htmlContent = str_replace('</body>', $inc_script . '</body>', $htmlContent);


$mainFolder = $data['mainFolder'];
$subFolder = $data['subFolder'];
$fileName = $data['fileName'] ? $data['fileName'] : 'layuot';

if ($mainFolder && !$subFolder) {
    $filePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $mainFolder . DIRECTORY_SEPARATOR . $fileName.'.php';
} elseif ($mainFolder && $subFolder) {
    $filePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $mainFolder . DIRECTORY_SEPARATOR . $subFolder . DIRECTORY_SEPARATOR . $fileName.'.php';
} else {
    echo "Invalid folder information.";
    exit;
}


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

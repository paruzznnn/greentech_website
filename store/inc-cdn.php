<?php
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

$scheme = $isHttps ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$path = $isHttps ? '/store/' : '/trandar_website/store/';

define('BASE_PATH', $scheme . '://' . $host . $path);
?>

<script src="<?php echo BASE_PATH ?>node_modules/jquery/dist/jquery.min.js"></script>

<link rel="stylesheet" href="<?php echo BASE_PATH ?>node_modules/bootstrap-icons/font/bootstrap-icons.min.css" />
<link href="<?php echo BASE_PATH ?>node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="<?php echo BASE_PATH ?>node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<link href="<?php echo BASE_PATH ?>node_modules/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="https://accounts.google.com/gsi/client" async defer></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<link rel="stylesheet" href="<?php echo BASE_PATH ?>node_modules/owlcarousel/dist/assets/owlcarousel.min.css"/>
<script src="<?php echo BASE_PATH ?>node_modules/owlcarousel/dist/owlcarousel.min.js"></script>

<link href="<?php echo BASE_PATH ?>node_modules/datatables/media/css/dataTables.min.css" rel="stylesheet">
<script src="<?php echo BASE_PATH ?>node_modules/datatables/media/js/dataTables.min.js"></script>







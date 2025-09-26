<?php
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

$scheme = $isHttps ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$path = $isHttps ? '/store/' : '/trandar_website/store/';

define('BASE_PATH', $scheme . '://' . $host . $path);
?>

<!-- <script src="<?php echo BASE_PATH ?>node_modules/jquery/dist/jquery.min.js"></script> -->

<link rel="stylesheet" href="<?php echo BASE_PATH ?>node_modules/bootstrap-icons/font/bootstrap-icons.min.css" />
<!-- <link href="<?php echo BASE_PATH ?>node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="<?php echo BASE_PATH ?>node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script> -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script> -->

<!-- Bootstrap 4 + Popper -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

<!-- Bootstrap Iconpicker -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-iconpicker/1.10.0/css/bootstrap-iconpicker.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-iconpicker/1.10.0/js/bootstrap-iconpicker.bundle.min.js"></script>

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

<link rel="stylesheet" href="<?php echo BASE_PATH ?>node_modules/owlcarousel/dist/assets/owlcarousel.min.css" />
<script src="<?php echo BASE_PATH ?>node_modules/owlcarousel/dist/owlcarousel.min.js"></script>

<link href="<?php echo BASE_PATH ?>node_modules/datatables/media/css/dataTables.min.css" rel="stylesheet">
<script src="<?php echo BASE_PATH ?>node_modules/datatables/media/js/dataTables.min.js"></script>

<!-- Fancybox CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />
<!-- Fancybox JS -->
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>


<link href="https://cdn.jsdelivr.net/npm/bootstrap-fileinput/css/fileinput.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-fileinput/js/fileinput.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-fileinput/themes/fas/theme.min.js"></script>
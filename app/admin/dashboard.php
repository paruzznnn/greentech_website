<?php
include('../../lib/permissions.php');
include('../../lib/base_directory.php');
checkPermissions();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <?php include 'inc_head.php'?>
    <link href="css/index_.css?v=<?php echo time();?>" rel="stylesheet">
</head>
<body>
    <?php include 'template/header.php'?>

    <script src="js/index_.js?v=<?php echo time();?>"></script>
</body>
</html>
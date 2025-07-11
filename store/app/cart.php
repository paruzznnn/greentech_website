<?php
session_start();
if(!empty($_SESSION)){
    if($_SESSION['exp'] < time()){
        header("Location: admin/logout.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>

    <link href="../inc/jquery/css/jquery-ui.css" rel="stylesheet">
    <script src="../inc/jquery/js/jquery-3.6.0.min.js"></script>
    <script src="../inc/jquery/js/jquery-ui.min.js"></script>

    <link href="../inc/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="../inc/bootstrap/js/bootstrap.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/fontawesome5-fullcss@1.1.0/css/all.min.css" rel="stylesheet">


    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    <link href="css/index_.css?v=<?php echo time();?>" rel="stylesheet">

    <link href="../inc/sweetalert2/css/sweetalert2.min.css" rel="stylesheet">
    <script src="../inc/sweetalert2/js/sweetalert2.all.min.js"></script>

    <link href="../inc/select2/css/select2.min.css" rel="stylesheet">
    <script src="../inc/select2/js/select2.min.js"></script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA7erTUVqwvYxOTkV6w6GEZbnKEEsFno1E&libraries=places"></script>
    
</head>
<body>

    <div class="container-fluid">
    <?php include 'template/header.php'?>
    </div>

    <div class="container-fluid">
    <?php include 'template/cart_detail.php'; ?>
    </div>

    <div class="container-fluid">
    <?php include 'template/footer.php'?>
    </div>


<script src="js/index_.js?v=<?php echo time();?>"></script>
<script src="js/cart_.js?v=<?php echo time();?>"></script>

<script src="../inc/getMapApi.js"></script>
<script src="../inc/getLanguage.js"></script>

</body>
</html>
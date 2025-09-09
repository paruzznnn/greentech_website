<?php
session_start();
if(empty($_SESSION)){
    header("Location: admin/logout.php");
    exit();
}else if($_SESSION['exp'] < time()){
    header("Location: admin/logout.php");
    exit();
}else{ 
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TRANDAR STORE</title>

    <link rel="icon" type="image/x-icon" href="../favicon.ico">

    <link href="../inc/jquery/css/jquery-ui.css" rel="stylesheet">
    <script src="../inc/jquery/js/jquery-3.6.0.min.js"></script>
    <script src="../inc/jquery/js/jquery-ui.min.js"></script>

    <link href="../inc/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="../inc/bootstrap/js/bootstrap.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/fontawesome5-fullcss@1.1.0/css/all.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

    <link href="css/index_.css?v=<?php echo time();?>" rel="stylesheet">
    <link href="css/member_.css?v=<?php echo time();?>" rel="stylesheet">

    <link href="../inc/sweetalert2/css/sweetalert2.min.css" rel="stylesheet">
    <script src="../inc/sweetalert2/js/sweetalert2.all.min.js"></script>

    <link href="../inc/select2/css/select2.min.css" rel="stylesheet">
    <script src="../inc/select2/js/select2.min.js"></script>

    <!-- <link href="../inc/krajee/css/bootstrap-icons.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.min.css" crossorigin="anonymous">
    <link href="../inc/krajee/css/fileinput.min.css" rel="stylesheet">

    <script src="../inc/krajee/js/buffer.min.js" type="text/javascript"></script>
    <script src="../inc/krajee/js/filetype.min.js" type="text/javascript"></script>
    <script src="../inc/krajee/js/piexif.min.js" type="text/javascript"></script>

    <script src="../inc/krajee/js/sortable.min.js" type="text/javascript"></script>
    <script src="../inc/krajee/js/fileinput.min.js" type="text/javascript"></script>
    <script src="../inc/krajee/js/LANG.min.js" type="text/javascript"></script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA7erTUVqwvYxOTkV6w6GEZbnKEEsFno1E&libraries=places"></script>


    <style>

        .select2-container--default .select2-results__option {
            pointer-events: auto; 
        }

        #googleMap {
        height: 300px;
        width: 100%;
        margin-top: 15px;
        }
    </style>

</head>
<body>

    <div class="container-fluid">
    <?php include 'template/header.php'?>
    </div>

    <div class="container-fluid">
    <?php include 'template/member_detail.php'?>
    </div>

    <div class="container-fluid">
    <?php include 'template/footer.php'?>
    </div>


<script src="js/index_.js?v=<?php echo time();?>"></script>
<script src="js/member_.js?v=<?php echo time();?>"></script>

<script src="../inc/getMapApi.js"></script>
<script src="../inc/getSelect2.js?v=<?php echo time();?>"></script>
<script src="../inc/getLanguage.js"></script>

</body>
</html>
<?php } ?>
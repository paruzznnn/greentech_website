<?php
require_once('../lib/connect.php');
global $conn;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <title>trandar</title> -->

    <?php include 'inc_head.php'?>

    <!-- Owl Carousel CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>


    <link href="css/index_.css?v=<?php echo time();?>" rel="stylesheet">

    <style>
        .valid {
            color: #4CAF50;
        }
        
        .invalid {
            color: #ff3d00;
        }
        .box-consent{
            border: 1px solid #d2d2d2;
            padding: 20px;
            border-radius: 3px;
        }
    </style>
    
</head>
<body>
    
    <?php include 'template/header.php'?>
    <?php include 'template/banner_slide.php'?>
    <?php include 'template/navbar_slide.php'?>

    <?php include 'template/content.php'?>

    <?php include 'template/footer.php'?>
    

    <script src="js/index_.js?v=<?php echo time();?>"></script>
    <script src="js/news/news_.js?v=<?php echo time();?>"></script>

</body>
</html>
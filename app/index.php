<?php
    if (isset($_GET['tab'])) {
        $tab = $_GET['tab'];
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                const section = document.getElementById('$tab');
                if (section) {
                    section.scrollIntoView({ behavior: 'smooth' });
                }
            });
        </script>";
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>

    <?php include 'inc_head.php'?>
    <link href="css/index_.css?v=<?php echo time();?>" rel="stylesheet">

    
</head>
<body>

    <?php include 'template/header.php'?>

    <?php include 'template/content.php'?>

    <?php include 'template/footer.php'?>
    

    <script src="js/index_.js?v=<?php echo time();?>"></script>

</body>
</html>
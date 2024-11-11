<?php
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>

    <?php include 'inc_head.php'?>
    <link href="css/index_.css?v=<?php echo time();?>" rel="stylesheet">
    <link href="css/pricing_.css?v=<?php echo time();?>" rel="stylesheet">

</head>
<body>

    <?php include 'template/header.php'?>

    <div class="content-sticky" id="">
        <div class="container">
            <div class="box-content">
                <div class="row">

                    <div class="col-md-3">
                        <div>
                            <?php include 'template/pricing/sidebar.php'?>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <div class="over-pricing-menu">
                            <?php include 'template/pricing/content.php'?>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <?php include 'template/footer.php'?>
    

    <script src="js/index_.js?v=<?php echo time();?>"></script>
    <script src="js/pricing/pricing_.js?v=<?php echo time();?>"></script>

</body>
</html>
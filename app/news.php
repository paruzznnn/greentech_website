<?php
require_once('../lib/connect.php');
global $conn;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>

    <?php include 'inc_head.php'?>
    <link href="css/index_.css?v=<?php echo time();?>" rel="stylesheet">
    <link href="css/news_.css?v=<?php echo time();?>" rel="stylesheet">

</head>
<body>

    <?php include 'template/header.php'?>
    <?php include 'template/navbar_slide.php'?>

    <div class="content-sticky" id="">
        <div class="container">
            <div class="box-content">
                <div class="row">

                    <div class="col-md-9">
                            <?php include 'template/news/content.php'?>
                    </div>

                    <div class="col-md-3">

                        <div class="page-plugin">
                        <iframe src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2Fallablethailand%2F&tabs=timeline&width=340&height=500&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true&appId" width="340" height="500" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>

    <?php include 'template/footer.php'?>

    <script src="js/index_.js?v=<?php echo time();?>"></script>
    <script src="js/news/news_.js?v=<?php echo time();?>"></script>

</body>
</html>
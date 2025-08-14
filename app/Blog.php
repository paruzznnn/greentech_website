<?php
require_once('../lib/connect.php');
global $conn;
?>
<!DOCTYPE html>
<html>

<head>
 

    <?php include 'inc_head.php' ?>
    <link href="css/index_.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/news_.css?v=<?php echo time(); ?>" rel="stylesheet">

    <style>
        /* Basic styles for pagination container */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            /* font-family: Arial, sans-serif; */
        }

        /* Styles for each pagination link */
        .pagination a {
            display: inline-block;
            margin: 0 5px;
            padding: 0px 10px;
            text-decoration: none;
            color: #555;
            /* border: 1px solid #ddd; */
            border-radius: 4px;
            transition: background-color 0.3s, color 0.3s;
        }

        /* Hover effect for pagination links */
        .pagination a:hover {
            background-color: #f1f1f1;
            color: #ffa719;
        }

        /* Active page styling */
        .pagination a.active {
            background-color: #ffa719;
            color: white;
            border: 1px solid #ffa719;
        }

        /* Styles for disabled links (e.g., first or last page) */
        .pagination a[disabled] {
            color: #ccc;
            pointer-events: none;
            border-color: #ccc;
        }

        .btn-search{
            border: none;
            background-color: #ffa719;
            color: #ffffff;
            border-radius: 0px 10px 10px 0px;
        }

    </style>

</head>

<body>

    <?php include 'template/header.php' ?>
    <?php include 'template/navbar_slide.php' ?>

    <div class="content-sticky" id="">
        <div class="container" style="max-width: 90%;">
            <div class="box-content">
                <div class="row">

                    <div class="">
                        <h2 style="font-size: 28px; font-weight: bold;" data-translate="blog" lang="th">Blog</h2>
                        <?php include 'template/Blog/content.php' ?>
                    </div>

                    <div class="col-md-3">

                        <!-- <div class="page-plugin mt-3">
                            <iframe src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2Ftrandaracoustic%2F&tabs=timeline&width=340&height=500&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true&appId" width="340" height="500" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
                        </div> -->

                    </div>

                </div>

            </div>
        </div>
    </div>

    <?php include 'template/footer.php' ?>

    <script src="js/index_.js?v=<?php echo time(); ?>"></script>
    <script src="js/news/news_.js?v=<?php echo time(); ?>"></script>

</body>

</html>
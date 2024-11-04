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

<div class="content-sticky" id="page_contact">
    <div class="container">
        <div class="box-content">
            <div class="row">

                <div class="col-md-6">
                    <h1>
                    ALLABLE Co.,ltd.
                    </h1>
                    <ul>
                        <li>102 Phatthanakan 40, Suan Luang, Bangkok 10250</li>
                        <li>(+66)2 722 7002</li>
                        <li>info@allable.co.th</li>
                        <li>Monday – Friday : 08:30 AM – 05:00 PM</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <div>
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3875.7396441274373!2d100.62457107508995!3d13.734206386655812!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x311d61b2c53ba90d%3A0x4442a96acffee7e9!2z4LmB4LiX4Lij4LiZ4LiU4Liy4Lij4LmMIOC4reC4tOC4meC5gOC4leC4reC4o-C5jOC5gOC4meC4iuC4seC5iOC4meC5geC4meC4pSAoVHJhbmRhciBJbnRlcm5hdGlvbmFsIGNvLiBsdGQp!5e0!3m2!1sen!2sth!4v1730371693406!5m2!1sen!2sth" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<?php include 'template/footer.php'?>
    
<script src="js/index_.js?v=<?php echo time();?>"></script>

</body>
</html>
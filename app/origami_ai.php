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
    <link href="css/origami_ai_.css?v=<?php echo time();?>" rel="stylesheet">

</head>
<body>

    <?php include 'template/header.php'?>

    <div class="content-sticky" id="page_pricing">
        <div class="container">
            <div class="box-content">
                <div class="row">

                    <div class="col-md-12">
                        <!-- <div>

                        DevRev เชื่อมต่อ Support Team และ Product Team ด้วย intelligent conversations
                        DevRev แทนที่เครื่องมือเก่าๆด้วยแพลตฟอร์ม AI ที่เชื่อมโยงทีมของคุณ ปรับปรุงกระบวนการทำงาน และสร้างความพึงพอใจให้กับลูกค้า
                        ช่วยทำให้เข้าใจ impact จากการทุ่มเทการทำงานของคุณ และทำให้ทุกการขับเคลื่อนมีความหมาย
                        เข้าร่วมกับบริษัทกว่า 1,000 แห่งที่ได้ใช้ประโยชน์จาก AI Experience อย่างเข้มข้นได้แล้วตั้งแต่วันนี้

                        </div> -->
                    </div>

                    <div class="col-md-12">
                        <div class="over-origami-ai-menu">
                            <?php include 'template/origamiAi/content.php'?>
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
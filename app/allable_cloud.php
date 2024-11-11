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
    
</head>
<body>

    <?php include 'template/header.php'?>

    <?php
        // Array for Allable Cloud Service content
        $allableCloudService = [
            'title' => 'Allable Cloud Service',
            'text' => 'ให้ Allable Cloud Service ช่วยปูรากฐาน 
            IT Infrastructure ให้กับองค์กรของคุณตั้งแต่วันนี้ 
            เรามี Cloud Solution ที่มี service type offerings 
            ครบครัน ให้กับท่านลูกค้าจากองค์กรหลากหลายขนาด 
            นานาประเภท ให้ดำเนินธุรกิจ ได้อย่างรวดเร็ว ปลอดภัย และคุ้มค่า 
            เช่น corporate website, e-commerce platform, streaming platform, DR site, hybrid infrastructure เป็นต้น',
            'images' => [
                '../public/img/netizen-logo.png',
                '../public/img/amazon_web_services_logo.png',
                '../public/img/huawei-cloud.png'
            ],
            'faq_title' => 'ทำไมจึงต้องเลือก Allable Cloud Services ?',
            'faq_list' => [
                'สบายใจ สบายกระเป๋า ด้วย special discount พิเศษสุดๆ จาก Cloud vendors ชั้นนำ ทุกเจ้าในไทย ทั้ง AWS, Huawei Cloud, Google Cloud รับรองว่า ประหยัดกว่าท่านลูกค้าไปสมัครเองที่ website ของ vendor แน่นอน',
                'Invoice และ การชำระต่างๆ เป็น เงินไทยบาท ทั้งหมด ท่านไม่ต้องผูกบัตรเครดิตใดๆทั้งสิ้น ความกังวลคือศูนย์ และท่านลูกค้าสามารถทำ หัก ณ ที่จ่าย พร้อม VAT ได้ตามปรกติ ไม่มีขั้นตอนใดๆที่นอกเหนือให้เป็นภาระวุ่นวายกับฝ่ายบัญชี/การเงินของท่าน',
                'มี Tech Support คอยช่วยเหลือ ให้ท่านทำงานได้ลุล่วง ตามเป้า project',
                'มี บริการ Consulting ให้คำปรึกษา การ Design, Implementation & Monitoring พร้อมเดินหน้าไปกับท่าน ด้วยประสบการณ์ทีมงานที่นำระบบงาน enterprise ขนาดใหญ่ขึ้น cloud มาแล้ว'
            ]
        ];

        // Initialize content
        $content = '<div class="content-sticky" id="page_allable_cloud">';
        $content .= '<div class="container">';
        $content .= '<div class="row">';
        $content .= '<div class="col-md-12">';
        $content .= '<div class="box-content">';
        $content .= '<div style="text-align: center;">';

        // Title
        $content .= '<h3>' . $allableCloudService['title'] . '</h3>';

        // Main text
        $content .= '<p style="text-align: left; margin-top: 20px; padding-left: 5px; box-shadow: -5px 0px 0px 0px #f28b1f;">';
        $content .= $allableCloudService['text'];
        $content .= '</p>';
        $content .= '</div>'; // Close text center div

        // Cloud services logos
        $content .= '<div class="cloud-services">';
        foreach ($allableCloudService['images'] as $image) {
            $content .= '<div class="box">';
            $content .= '<img src="' . $image . '" alt="">';
            $content .= '</div>';
        }
        $content .= '</div>'; // Close cloud services div

        // FAQ section
        $content .= '<div>';
        $content .= '<b>' . $allableCloudService['faq_title'] . '</b>';
        $content .= '<ul>';
        foreach ($allableCloudService['faq_list'] as $item) {
            $content .= '<li>' . $item . '</li>';
        }
        $content .= '</ul>';
        $content .= '</div>'; // Close FAQ div

        $content .= '</div>'; // Close box-content
        $content .= '</div>'; // Close column
        $content .= '</div>'; // Close row
        $content .= '</div>'; // Close container
        $content .= '</div>'; // Close content-sticky

        // Output the content
        echo $content;
    ?>


    <?php include 'template/footer.php'?>


    

    <script src="js/index_.js?v=<?php echo time();?>"></script>

</body>
</html>
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
        // Array of content sections
        $contentSections = [
            [
                'image' => '../public/img/a-team-smiling-while-working-5716008-1536x864.jpg',
                'text' => 'บริษัท ออลลาเบิ้ล จำกัด (Allable Co.,Ltd.) ประกอบธุรกิจการพัฒนาซอฟท์แวร์สำหรับกิจการระดับ Enterprise 
                ตั้งแต่ปี 2557 เราคือผู้เชี่ยวชาญในการสร้างสรรค์ Workplace Collaboration Platform 
                และ Automated Business Workflow/Process เป็นอย่างดี ในช่วงปีที่ผ่านมาไม่นานนี้ เราได้ขยายโฟกัสจาก
                Tailored Made Solutions เดิม ที่ขยายความสามารถของ Industrial-strength ERP Systems 
                มาสู่ Universal Collaboration Platform: Origami System 
                ที่ตอบสนองความต้องการอย่างเข้มข้นและหลากหลายของนานาประเภทธุรกิจได้อย่างฉับไว 
                เป็นทัพหลังเสริมแกร่งให้องค์กรใหญ่หรือเล็กได้เป็นอย่างดี'
            ],
            [
                'text' => 'นอกจากนี้ เริ่มตั้งแต่ปี 2567 บริษัท ออลลาเบิ้ล ยังเสริมบริการให้คำปรึกษาด้าน Business Transformation 
                ด้วย Cloud Infrastructure Technology มาพร้อมกันด้วยเลย ด้วยเราชำนาญในการ implement ระบบงานขนาดใหญ่ 
                และ การทำ SaaS (software-as-a-service) deployment ของ solution เราเองเป็นทุนเดิมอยู่แล้ว เรามีความยินดีนำเสนอ
                Cloud Services จาก Cloud Providers ชั้นนำ ให้ลูกค้าเลือกสรรได้ตรงตามความต้องการ 
                ทั้ง Amazon Web Services, Huawei Cloud Services, Google Cloud Platform เป็นต้น'
            ],
            [
                'image' => '../public/img/2149399290-1536x1025.jpg',
                'text' => 'โดย Solutions และ Services ทั้งหมดนี้ ถูกขับเคลื่อนภายใต้วิสัยทัศน์ในการนำเทคโนโลยีที่เหมาะสมมาปรับใช้ให้ตอบโจทย์ทางธุรกิจได้จริง
                เกิดประโยชน์สูงสุดต่อองค์กรในหลากหลายประเภทและขนาด ให้พร้อมสู้ศึกการแข่งขันทางธุรกิจได้อย่างเต็มกำลังสามารถ'
            ],
        ];

        // Initialize content
        $content = '<div class="content-sticky" id="page_about">';
        $content .= '<div class="container">';
        $content .= '<div class="box-content">';

        // Loop through each section
        foreach ($contentSections as $section) {
            $content .= '<div class="row">';
            
            // Check if there is an image to display
            if (isset($section['image'])) {
                $content .= '<div class="col-md-6">';
                $content .= '<img src="' . $section['image'] . '" alt="">';
                $content .= '</div>';
            }
            
            // Text section (always display)
            $content .= '<div class="col-md-' . (isset($section['image']) ? '6' : '12') . '">';
            $content .= '<p>' . $section['text'] . '</p>';
            $content .= '</div>';
            
            $content .= '</div>'; // Close row

            // Add a horizontal rule after the first section only
            if (isset($section['image'])) {
                $content .= '<hr>'; // Separator after the first section
            }
        }

        $content .= '</div>'; // Close box-content
        $content .= '</div>'; // Close container
        $content .= '</div>'; // Close content-sticky

        // Output the content
        echo $content;
    ?>


    <?php include 'template/footer.php'?>
    

    <script src="js/index_.js?v=<?php echo time();?>"></script>

</body>
</html>
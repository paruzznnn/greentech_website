<?php

?>
<!DOCTYPE html>
<html>
<head>


    <?php include 'inc_head.php'?>
    <link href="css/index_.css?v=<?php echo time();?>" rel="stylesheet">
    
</head>
<body>

    <?php include 'template/header.php'?>
    <?php include 'template/navbar_slide.php'?>

    <?php
        // Array of content sections
        $contentSections = [
            [
                'text' => '<h2 style="font-size: 28px; font-weight: bold;">โปรแกรมจำลองค่าการกันเสียง INSUL Acoustics Software ที่สุดของมาตรฐานบริการด้านอะคูสติก</h2>'
           ],
            [
                'text' => 'จากประสบการณ์การที่ทาง Trandar Acoustics ได้ให้บริการทั้ง Research and Development และเป็นที่ปรึกษาผู้เชี่ยวชาญด้านอะคูสติก ก็ทำให้ค้นพบว่าการติดตั้งหรือการก่อสร้างที่มาจากการเลือกใช้วัสดุที่ดีพอแล้ว ก็ยังไม่ตอบโจทย์จริงๆ กับประสิทธิภาพที่ลูกค้าต้องการได้จริงๆ'
           ],
           [
                'text' => 'Trandar Acoustics จึงนำบริการซอฟต์แวร์เข้ามา นั่นคือ โปรแกรม INSUL นั่นเอง เพื่อที่จะให้ลูกค้ารับรู้ถึงค่าเสียงดังเดิมที่เกิดขึ้นจริง และค่าเสียงที่จะสามารถทำให้อะคูสติกได้ ทั้งยังเป็นตัวช่วยป้องกันการเลือกวัสดุไม่ผิดพลาดอีกด้วยค่ะ'
           ],
            [
                'image' => '../public/img/acoustics.jpg'
                // 'text' => '<h2 style="font-size: 28px; font-weight: bold;">จุดมุ่งหมายบริษัท</h2>'
            ],
            [
                'text' => 'โดย INSUL เป็นโปรแกรมการจำลองค่าการกันเสียง (INSUL Acoustics Software) ซึ่งมีความสะดวก รวดเร็ว และแม่นยำในการคำนวณหาค่าต่าง ๆ
                โดยส่วนมากถูกใช้เพื่อคำนายประสิทธิภาพการป้องกันเสียงของระบบผนัง ซึ่งสามารถกำหนดคุณสมบัติของวัสดุต่าง ๆ ได้อย่างมีประสิทธิภาพ
                เป็นโปรแกรมคำนวณหาค่าการกันเสียงของผนัง พื้น ฝ้าเพดาน และหน้าต่าง
                จะแสดงผลค่าการสูญเสียในรูปแบบความถี่ 1/3 OCTAVE BANDS และค่า Weighted Sound Reduction Index (STC or Rw)
                สำหรับคำนวณค่าการกันเสียง สามารถปรับเปลี่ยนวัสดุ หรือการเปลี่ยนแปลงรูปแบบ
                มีความสะดวก รวดเร็ว และแม่นยำในการคำนวณค่าต่าง ๆ'
           ],
              [
                'text' => 'ปัจจุบัน INSUL ถือเป็นซอฟต์แวร์ที่ได้รับความนิยมจากทุกโรงงานที่ผลิตวัสดุทางด้านแผ่นยิปซัม แผ่นฝ้า เพดาน และผนัง ต่างก็เลือกใช้งาน มาเล่ากันว่าโปรแกรม INSUL มีประโยชน์กับคุณอย่างไรบ้าง!!'
            ],
            [
                'text' => '<h2 style="font-size: 28px; font-weight: bold;">5 ประโยชน์ของโปรแกรม INSUL ตอบโจทย์การใช้งานด้านอะคูสติกดีเยี่ยม</h2>'
            ],
            [
                'text' => '1. ทำรายการสูญเสียของการส่งสัญญาณเสียงกระทบและเสียงฝน'
            ],
            [
                'text' => 'INSUL เป็นโปรแกรมสำหรับทำคำนวณงานกันเสียงของผนัง พื้น หลังคาเพดาน หน้าต่าง เสียงกระทบและเสียงฝนของพื้นและหลังคา
                ซึ่งโปรแกรมสามารถทำคำนวณค่าที่เรียกว่า Transmission Loss (TL) หรือ Impact Sound (Ln) ใน 1/3 octave bands และแสดงค่าเปรียบเทียบต่างๆ เช่น STC หรือ Rw หรือ Impact Rating (IIC / LnTw)
                เพื่อใช้วิเคราะห์คุณภาพของระบบกันเสียงในเรื่องของเสียงรบกวนเสียงฝน การอาบแดน หรือข้อกำหนดเกี่ยวกับเสียง'
            ],
            [
                'image' => '../public/img/insul1.jpg'
            ],
            [
                'text' => '<p style="border-top: 2px; padding-top: 12px; margin-top: 24px;">2. ประเมินการเปลี่ยนแปลงวัสดุหรือการออกแบบ</p>'

            ],
            [
                'text' => 'INSUL สามารถใช้เพื่อประเมินวัสดุและระบบใหม่อย่างรวดเร็วหรือตรวจสอบผลกระทบของการเปลี่ยนแปลงในการออกแบบที่มีอยู่เป็นแบบจำลองวัสดุ
                โดยใช้กราฟที่แผ่นยืดหยุ่นที่รู้จักกันดี รวมถึงค่าที่เหมาะสำหรับเอฟเฟกต์แผงหนาตามที่เผยแพร่โดย Ljunggren, Rindell และอื่น ๆ
                พารามิเตอร์ซับซ้อนมากขึ้นถูกจำลองขึ้นโดยใช้งานของ Sharp, Cremer และอื่น ๆ'
            ],
            [
                'text' => '3. การพัฒนาอย่างต่อเนื่อง'
            ],
            [
                'text' => 'INSUL เปิดให้บริการมานานกว่า 15 ปี และได้รับการปรับปรุงอย่างมากในช่วงเวลานี้
                มีการพัฒนาในหลายรุ่น และกลายเป็นเครื่องมือซึ่งใช้งานง่ายและมีประโยชน์รองรับทั้งระบบ Windows และ Mac
                แถมได้รับการปรับปรุงโดยการเปรียบเทียบอย่างต่อเนื่องกับข้อมูลการทดสอบในห้องปฏิบัติการ
                เพื่อให้ได้ความแม่นยำที่ยอมรับได้สำหรับโครงการจริงที่หลากหลาย'
            ],
            [
                'text' => 'สามารถป้อนข้อมูลการทดสอบเพื่อเปรียบเทียบกับการคาดคะเนได้ง่ายและสามารถบันทึกโครงสร้างเพื่อเรียกคืนได้ในภายหลัง'
            ],
            [
                'image' => '../public/img/insul2.jpg'
            ],
            [
                'text' => '<p style="border-top: 2px; padding-top: 12px; margin-top: 24px;">4. ระบุการก่อสร้างด้วยความเร็วและความมั่นใจ</p>'
            ],
            [
                'text' => 'INSUL คำนึงถึงเอฟเฟกต์ ขนาด ความจำกัดพื้นที่ ซึ่งมีความสำคัญมาก เมื่อต้องทำการคาดคะเนพื้นที่ในส่วนเล็กๆ เช่น หน้าต่าง และสำหรับส่วนประกอบปกติทั่วไปที่มีความถี่ต่ำด้วยเช่นกัน'
            ],
            [
                'text' => 'แต่ทั้งนี้ INSUL ไม่สามารถใช้ในการวัดได้ อย่างไรก็ตาม ในเรื่องของการเทียบกับข้อมูลการทดสอบ บ่งชี้ได้ว่า INSUL สามารถทำนายค่า STC ภายใน 3dB ในงานก่อสร้างขนาดใหญ่ได้อย่างน่าเชื่อถือ จะช่วยเพิ่มความสามารถของทีมผู้ออกแบบด้านอะคูสติกและผู้ผลิตผลิตภัณฑ์ในการระบุโครงสร้างได้อย่างรวดเร็วและมั่นใจ เพื่อใช้ในงานบันทึกข้อมูลในเอกสารที่ต้องการ'
            ],
            [
                'text' => '5. ใช้งานได้หลากหลายภาษา'
            ],
            [
                'text' => 'INSUL สามารถเปลี่ยนเพื่อแสดงข้อมูลเป็นภาษาอังกฤษ ฝรั่งเศส เยอรมนี โปแลนด์ สเปน สวีเดน หรืออื่นๆ ได้ การแปลดำเนินการโดยผู้เชี่ยวชาญด้านอะคูสติก เพื่อให้เหมาะสมกับคำศัพท์ทางเทคนิคที่ใช้กันทั่วไปในแต่ละประเทศมากที่สุด'
            ],
            [
                'text' => 'ปัญหาเรื่องเสียงรบกวน นับว่าเป็นปัญหาสำคัญที่มักจะเกิดขึ้นได้โดยทั่วไป ทั้งในออฟฟิศสำนักงาน ที่พักอาศัย หรือแม้กระทั่งในโรงงานอุตสาหกรรมเองก็ตาม ถือว่าเป็นความเสี่ยงที่ส่งผลเสียต่อการทำงานเป็นอย่างมาก
                ยิ่งเฉพาะในโรงงานอุตสาหกรรม เป็นสถานที่ทำงานที่มีเสียงดังมากที่สุด บางการทำงานของเครื่องจักร อาจก่อให้เกิดอันตรายแก่พนักงานหากไม่มีมาตรการกำกับดูแลได้'
            ],
            [
                'text' => 'หากคุณต้องการซอฟต์แวร์ที่เข้าใจง่าย ใช้งานง่ายดี มีประสิทธิภาพ Trandar Acoustics จึงเสริมบริการด้วย INSUL Acoustics Software บริการให้คุณใช้งานได้อุ่นใจ ไร้ความกังวล ไม่ต้องแก้จบ หรือโครงสร้างใหม่ในอนาคต'
            ],
            [
                'text' => '<p style="border-top: 2px; padding-top: 12px; margin-top: 24px;">ขอบคุณภาพจาก : Insul</p>'
            ],
        ];

        // Initialize content
        $content = '<div class="content-sticky" id="page_about">';
        $content .= '<div class="container">';
        $content .= '<div class="box-content">';

        // Loop through each section
        foreach ($contentSections as $section) {
    $content .= '<div class="row">';

    // กรณีมี image
    if (isset($section['image'])) {
        $content .= '<div class="col-md-6">';
        $content .= '<img style="width: 100%;" src="' . $section['image'] . '" alt="">';
        $content .= '</div>';
    }

    // กรณี texts หลายบล็อก (โครงสร้าง/นโยบาย/วิสัยทัศน์)
    if (isset($section['texts']) && is_array($section['texts'])) {
        $content .= '<div class="col-md-' . (isset($section['image']) ? '6' : '12') . '">';
        $content .= '<div class="d-flex justify-content-between">';
        foreach ($section['texts'] as $text) {
            $content .= '<div style="width: 32%; padding: 0 10px;">' . $text . '</div>';
        }
        $content .= '</div></div>';
        $content .= '<div class="col-12"><hr></div>';
    }

    // กรณีมี quote
    if (isset($section['quote'])) {
        $quote = $section['quote'];
        $content .= '
        <div style="text-align: center; padding: 40px 20px; font-style: italic; font-size: 25px; position: relative; width: 100%;">
            <div style="font-size: 40px; color: #ccc; position: absolute; left: 10px; top: 0;">“</div>
            <p style="margin: 0 40px;">' . $quote['text'] . '</p>
            <div style="margin-top: 20px; font-style: normal;">
                <strong>' . $quote['author'] . '</strong><br>' . $quote['position'] . '
            </div>
        </div>';
        // $content .= '<div class="col-12"><hr></div>';
    }

    // กรณีมีข้อความเดี่ยว
    if (isset($section['text']) && !isset($section['texts'])) {
        $content .= '<div class="col-md-' . (isset($section['image']) ? '6' : '12') . '">';
        $content .= '<p>' . $section['text'] . '</p>';
        $content .= '</div>';
    }

    $content .= '</div>'; // close row

    if (isset($section['image'])) {
        // $content .= '<hr>';
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
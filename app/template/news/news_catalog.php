<?php
$games = [
  [
    'title' => 'HR',
    'image' => 'http://localhost:3000/allable/public/news_img/20241105%20ALLABLE%20_%20DevRev-597.jpg',
    'description' => 'Feature ที่ช่วยจัดการด้านทรัพยากรบุคคล ตั้งแต่ใบสมัครงานจนถึงการเข้าทำงานในองค์กร.'
  ],
  [
    'title' => 'CRM',
    'image' => 'http://localhost:3000/allable/public/news_img/20241105%20ALLABLE%20_%20DevRev-736.jpg',
    'description' => 'ส่วนจัดการข้อมูลที่เชื่อมโยงกันในระดับองค์กร เริ่มต้นข้อมูลตั้งแต่เก็บบันทึกข้อมูลบริษัท ข้อมูลผู้ติดต่อข้อมูลโครงการ ข้อมูลกิจกรรมตามโครงการ ข้อมูลสินค้า ใบเสนอราคา การบันทึกเวลาเข้าออกงานเมื่อออกปฏิบัติงานนอกสถานที่ ข้อมูลที่เชื่อมโยงแสดงบนปฏิทินช่วยให้จัดการการทำงานง่าย'
  ],
  [
    'title' => 'Skoop',
    'image' => 'http://localhost:3000/allable/public/news_img/20241105%20ALLABLE%20_%20DevRev-411.jpg',
    'description' => 'บริหารการให้บริการช่วยเหลือในการทำงาน แก้ผู้ใช้ระบบ และผู้ใช้บริการ
      และมีส่วนบริหารจัดการใช้จ่าย ลดเวลาในการจัดการเอกสาร'
  ],
  [
    'title' => 'IDOC',
    'image' => 'http://localhost:3000/allable/public/news_img/20241105%20ALLABLE%20_%20DevRev-725.jpg',
    'description' => 'ส่วนจัดการไฟล์ข้อมูล ทั้งการเก็บไฟล์ข้อมูล การจัดการการเข้าถึงไฟล์ เพื่อง่ายต่อการใช้ภายในองค์กร
    สามารถบันทึกไฟล์เข้าโครงการได้ ระบุหมวดของเอกสารเพื่อให้สะดวก ต้องการค้นหา'
  ],
  [
    'title' => 'SERVICE',
    'image' => 'http://localhost:3000/allable/public/news_img/20241105%20ALLABLE%20_%20DevRev-27.jpg',
    'description' => 'บริหารการให้บริการช่วยเหลือในการทำงาน แก้ผู้ใช้ระบบ และผู้ใช้บริการ และมีส่วนบริหารจัดการใช้จ่าย ลดเวลาในการจัดการเอกสาร'
  ],
  [
    'title' => 'ACADEMY',
    'image' => 'http://localhost:3000/allable/public/news_img/20241105%20ALLABLE%20_%20DevRev-162.jpg',
    'description' => 'เปิด Course training online ภายในองค์กร พร้อมทั้งระบบ Challenge ที่สร้างแบบทดสอบ เพื่อวัดผลหลังการ Training.'
  ]
];
?>

<div class="game-section">
  <div class="owl-carousel custom-carousel owl-theme">
    <?php foreach ($games as $game): ?>
      <div class="item" style="background-image: url(<?php echo $game['image']; ?>);">
        <div class="item-desc">
          <h3><?php echo $game['title']; ?></h3>
          <p><?php echo $game['description']; ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
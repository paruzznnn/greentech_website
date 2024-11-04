<div class="sidebar">
    <div class="custom-sidebar">
        <?php
        // สร้างอาร์เรย์ที่เก็บข้อมูลเมนู
        $menuItems = [
            ['name' => 'Home', 'link' => '#home', 'active' => true],
            ['name' => 'News', 'link' => '#news', 'active' => false],
            ['name' => 'Contact', 'link' => '#contact', 'active' => false],
            ['name' => 'About', 'link' => '#about', 'active' => false]
        ];

        // loop เพื่อสร้างเมนู
        foreach ($menuItems as $item) {
            // ตรวจสอบว่าเมนูเป็น active หรือไม่
            $activeClass = $item['active'] ? 'active' : '';
            echo '<a class="' . $activeClass . '" href="' . $item['link'] . '">' . $item['name'] . '</a>';
        }
        ?>
    </div>
</div>

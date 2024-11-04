<div class="sidebar">
    <div class="custom-sidebar">
        <?php
        // สร้างอาร์เรย์ที่เก็บข้อมูลเมนู
        $menuItems = [
            ['icon' => '<i class="fas fa-cube"></i>', 'name' => 'PLuG App', 'link' => '#', 'active' => true],
            ['icon' => '', 'name' => 'Support', 'link' => '#', 'active' => false],
            ['icon' => '', 'name' => 'Build', 'link' => '#', 'active' => false]
        ];

        // loop เพื่อสร้างเมนู
        foreach ($menuItems as $item) {
            // ตรวจสอบว่าเมนูเป็น active หรือไม่
            $activeClass = $item['active'] ? 'active' : '';
            echo '<a class="' . $activeClass . '" href="' . $item['link'] . '">
                    ' . $item['icon'] . '
                    <span>' . $item['name'] . '</span>
                </a>';
        }
        ?>
    </div>
</div>

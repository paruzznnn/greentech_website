<div class="sidebar">
    <div class="custom-sidebar">
        <?php

        $menuItems = [
            ['icon' => '<i class="fas fa-cube"></i>', 'name' => 'PLuG App', 'link' => 'tab-plug', 'active' => true],
            ['icon' => '', 'name' => 'Support', 'link' => 'tab-support', 'active' => false],
            ['icon' => '', 'name' => 'Build', 'link' => 'tab-build', 'active' => false]
        ];

        foreach ($menuItems as $item) {
            $activeClass = $item['active'] ? 'active' : '';
            echo '<a class="nav-link ' . $activeClass . '" data-target="#' . $item['link'] . '" type="button">
                    ' . $item['icon'] . '
                    <span>' . $item['name'] . '</span>
                </a>';
        }
        ?>
    </div>
</div>


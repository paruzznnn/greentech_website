<?php
// ตรวจสอบว่าได้มีการเชื่อมต่อฐานข้อมูลและตัวแปร $conn พร้อมใช้งานแล้ว
if (!isset($conn)) {
    $conn = new mysqli('localhost', 'user', 'password', 'database');
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }
}

// 1. กำหนดตัวแปรภาษา
$lang = isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'th', 'cn']) ? $_GET['lang'] : 'th';

// 2. สร้างตัวแปรสำหรับชื่อคอลัมน์
$subject_col = 'subject_shop';
$description_col = 'description_shop';
$content_col = 'content_shop';
$group_name_col = 'group_name';

if ($lang === 'en') {
    $subject_col .= '_en';
    $description_col .= '_en';
    $content_col .= '_en';
    $group_name_col .= '_en';
} elseif ($lang === 'cn') {
    $subject_col .= '_cn';
    $description_col .= '_cn';
    $content_col .= '_cn';
    $group_name_col .= '_cn';
}

$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$selectedGroupId = isset($_GET['group_id']) ? (int)$_GET['group_id'] : 0;
$selectedSubGroupId = isset($_GET['sub_group_id']) ? (int)$_GET['sub_group_id'] : 0;

$allProductsData = [];
$sqlAllProducts = "SELECT
    dn.shop_id,
    dn.$subject_col AS subject_shop,
    dn.$description_col AS description_shop,
    dn.$content_col AS content_shop,
    dn.date_create,
    dn.group_id,
    sub_group.group_id AS sub_group_id,
    sub_group.$group_name_col AS sub_group_name,
    sub_group.parent_group_id,
    main_group.group_id AS main_group_id,
    main_group.$group_name_col AS main_group_name,
    main_group.image_path AS main_group_image_path,
    (SELECT dnc.api_path FROM dn_shop_doc dnc WHERE dnc.shop_id = dn.shop_id AND dnc.del = '0' AND dnc.status = '1' ORDER BY dnc.id ASC LIMIT 1) AS first_image_path
FROM dn_shop dn
LEFT JOIN dn_shop_groups sub_group ON dn.group_id = sub_group.group_id
LEFT JOIN dn_shop_groups main_group ON sub_group.parent_group_id = main_group.group_id
WHERE dn.del = '0' AND sub_group.parent_group_id IS NOT NULL";

if ($searchQuery) {
    $safeQuery = $conn->real_escape_string($searchQuery);
    // ปรับปรุงการค้นหาให้ครอบคลุมคอลัมน์ภาษาอังกฤษและจีนด้วย
    $sqlAllProducts .= " AND (dn.$subject_col LIKE '%$safeQuery%' OR dn.$description_col LIKE '%$safeQuery%')";
}

if ($selectedSubGroupId > 0) {
    $sqlAllProducts .= " AND sub_group.group_id = $selectedSubGroupId";
} elseif ($selectedGroupId > 0) {
    $sqlAllProducts .= " AND main_group.group_id = $selectedGroupId";
}

// เรียงลำดับตามชื่อกลุ่มภาษาที่เลือก
$sqlAllProducts .= " ORDER BY main_group.$group_name_col ASC, sub_group.$group_name_col ASC, dn.shop_id ASC";

$resultAllProducts = $conn->query($sqlAllProducts);
$allProductsData = [];
if ($resultAllProducts && $resultAllProducts->num_rows > 0) {
    while ($row = $resultAllProducts->fetch_assoc()) {
        $allProductsData[] = $row;
    }
}

$organizedGroups = [];
foreach ($allProductsData as $product) {
    $mainGroupId = $product['main_group_id'];
    $mainGroupName = $product['main_group_name'];
    $mainGroupImage = $product['main_group_image_path'];
    $subGroupId = $product['sub_group_id'];
    $subGroupName = $product['sub_group_name'];

    if (!$mainGroupId || !$subGroupId) continue;

    if (!isset($organizedGroups[$mainGroupId])) {
        $organizedGroups[$mainGroupId] = [
            'id' => $mainGroupId,
            'name' => $mainGroupName,
            'image' => $mainGroupImage,
            'total_products' => 0,
            'sub_groups' => []
        ];
    }

    if (!isset($organizedGroups[$mainGroupId]['sub_groups'][$subGroupId])) {
        $organizedGroups[$mainGroupId]['sub_groups'][$subGroupId] = [
            'id' => $subGroupId,
            'name' => $subGroupName,
            'products' => []
        ];
    }

    $organizedGroups[$mainGroupId]['total_products']++;
    $organizedGroups[$mainGroupId]['sub_groups'][$subGroupId]['products'][] = [
        'id' => $product['shop_id'],
        'title' => $product['subject_shop'],
        'description' => $product['description_shop'],
        'iframe' => preg_match('/<iframe.*?src=[\"\'](.*?)[\"\'].*?>/i', $product['content_shop'], $matches) ? $matches[1] : null,
        'image' => $product['first_image_path']
    ];
}

ksort($organizedGroups);
foreach ($organizedGroups as &$group) {
    ksort($group['sub_groups']);
    foreach ($group['sub_groups'] as &$subGroup) {
        usort($subGroup['products'], fn($a, $b) => $a['id'] <=> $b['id']);
    }
}

$finalDisplayItems = array_values($organizedGroups);

if ($searchQuery || $selectedGroupId > 0 || $selectedSubGroupId > 0) {
    $tempDisplayItems = [];
    foreach ($allProductsData as $product) {
        // ใช้ชื่อคอลัมน์ที่ถูกเลือกตามภาษาที่เลือก
        $shouldAddProduct = false;
        if ($searchQuery && (stripos($product['subject_shop'], $searchQuery) !== false || stripos($product['description_shop'], $searchQuery) !== false)) {
            $shouldAddProduct = true;
        } elseif ($selectedSubGroupId > 0 && $product['sub_group_id'] == $selectedSubGroupId) {
            $shouldAddProduct = true;
        } elseif ($selectedGroupId > 0) {
            if (($product['main_group_id'] == $selectedGroupId) ||
                ($product['parent_group_id'] === NULL && $product['group_id'] == $selectedGroupId)) {
                $shouldAddProduct = true;
            }
        }

        if ($shouldAddProduct) {
            $tempDisplayItems[] = [
                'id' => $product['shop_id'],
                'title' => $product['subject_shop'],
                'description' => $product['description_shop'],
                'iframe' => preg_match('/<iframe.*?src=["\'](.*?)["\'].*?>/i', $product['content_shop'], $matches) ? $matches[1] : null,
                'image' => $product['first_image_path']
            ];
        }
    }
    $finalDisplayItems = $tempDisplayItems;

    usort($finalDisplayItems, fn($a, $b) => $a['id'] <=> $b['id']);

} else {
    ksort($organizedGroups);
    $finalDisplayItems = array_values($organizedGroups);
}
?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Categories</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* General container for the product grid */
        .product-grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 20px;
            align-items: start;
        }

        /* Styles for Main Category Blocks */
        .main-category-block {
            position: relative;
            border: 1px solid #ddd;
            overflow: visible;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            background-color: #fcfcfc;
            cursor: pointer;
            transition: box-shadow 0.3s ease-in-out, transform 0.3s ease-in-out;
        }

        /* เพิ่ม z-index และ transform เฉพาะเมื่อบล็อกนั้นถูก active */
        .main-category-block.active {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            transform: translateY(-5px);
            z-index: 10; 
        }

        /* ปิด hover effect เมื่อ dropdown เปิดอยู่ */
        .product-grid-container.is-dropdown-open .main-category-block:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(0);
        }

        /* คง hover effect ไว้สำหรับบล็อกที่ไม่มี class active */
        .product-grid-container:not(.is-dropdown-open) .main-category-block:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            transform: translateY(-5px);
        }

        .main-category-block .block-image-top {
            width: 100%;
            height: 200px;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #e6e6e6;
        }

        .main-category-block .block-image-top img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .main-category-block .block-header-bottom {
            display: flex;
            align-items: center;
            padding: 15px;
            background-color: #f0f0f0;
            border-top: 1px solid #e0e0e0;
            position: relative;
        }

        .main-category-block .block-title-info {
            flex-grow: 1;
        }

        .main-category-block .block-title-info h3 {
            font-size: 1.3em;
            font-weight: bold;
            color: #555;
            margin: 0 0 5px 0;
        }

        .main-category-block .product-count {
            font-size: 0.9em;
            color: #777;
            margin: 0;
        }

        .main-category-block .toggle-arrow,
        .sub-category-item .toggle-arrow-sub {
            font-size: 1.2em;
            color: #555;
            margin-left: 10px;
            transition: transform 0.3s ease;
        }

        /* Dropdown Container สำหรับส่วนที่ Dropdown ลงมา */
        .dropdown-container {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            z-index: 20; /* ค่า z-index ที่สูงกว่า .main-category-block.active */
            background-color: #ffffff;
            border: 1px solid #ddd;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            display: none;
            max-height: 400px;
            overflow-y: auto;
        }

        .block-content-accordion {
            padding: 15px;
        }
        
        .sub-category-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sub-category-item {
            margin-bottom: 8px;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 8px;
        }

        .sub-category-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .sub-category-item .sub-category-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            cursor: pointer;
            font-weight: bold;
            color: #444;
            transition: color 0.2s;
        }

        .sub-category-item .sub-category-header:hover {
            color: #ff9900;
        }

        .sub-category-item .sub-category-header h4 {
            font-size: 1em;
            margin: 0;
        }

        /* Accordion Content for Sub Category (Product List) */
        .product-list-accordion {
            list-style: none;
            padding-left: 25px;
            margin: 5px 0 0 0;
            background-color: #fdfdfd;
            border-radius: 4px;
            padding-top: 5px;
            padding-bottom: 5px;
        }

        .product-list-accordion li {
            padding: 6px 0;
            font-size: 0.95em;
            border-bottom: 1px dotted #e0e0e0;
        }

        .product-list-accordion li:last-child {
            border-bottom: none;
        }

        .product-list-accordion li a {
            text-decoration: none;
            color: #555;
            display: block;
        }

        .product-list-accordion li a:hover {
            color: #ff9900;
            text-decoration: underline;
        }

        /* For Search Results (flat product list) */
        .box-news {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease-in-out, transform 0.3s ease-in-out;
            background-color: #fcfcfc;
        }

        .box-news:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            transform: translateY(-5px);
        }

        .box-image {
            width: 100%;
            height: 200px;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #e6e6e6;
        }

        .box-image img, .box-image iframe {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .box-content {
            padding: 15px;
            background-color: #ffffff;
        }

        .box-content h5 {
            font-size: 1.1em;
            font-weight: bold;
            margin-bottom: 5px;
            height: 40px;
            overflow: hidden;
            color: #333;
        }

        .box-content p {
            font-size: 0.9em;
            color: #666;
            height: 20px;
            overflow: hidden;
        }

        .text-news {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .line-clamp {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Remove default list styles */
        ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
    </style>
</head>
<body>

<div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
    <form method="GET" action="">
        <div class="input-group">
            <input type="hidden" name="lang" value="<?php echo htmlspecialchars($lang); ?>">
            <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($searchQuery); ?>" placeholder="<?php echo getPlaceholderText($lang); ?>">
            <button class="btn-search" type="submit"><i class="fas fa-search"></i></button>
        </div>
    </form>
</div>

<h2 style="font-size: 28px; font-weight: bold; margin-top: 20px;">
    <?php
    function getPlaceholderText($lang) {
        switch ($lang) {
            case 'en':
                return 'Search product...';
            case 'cn':
                return '搜索产品...';
            case 'th':
            default:
                return 'ค้นหาสินค้า...';
        }
    }
    
    function getDisplayText($lang, $searchQuery, $selectedSubGroupId, $selectedGroupId, $allProductsData) {
        if ($searchQuery) {
            $text = [
                'th' => 'ผลการค้นหาสำหรับ',
                'en' => 'Search Results for',
                'cn' => '搜索结果'
            ];
            return $text[$lang] . ' "' . htmlspecialchars($searchQuery) . '"';
        } elseif ($selectedSubGroupId > 0) {
            $groupName = '';
            foreach ($allProductsData as $prod) {
                if ($prod['sub_group_id'] == $selectedSubGroupId) {
                    $groupName = $prod['sub_group_name'];
                    break;
                }
            }
            $text = [
                'th' => 'สินค้าในกลุ่ม "' . htmlspecialchars($groupName ?: 'กลุ่มย่อยที่เลือก') . '"',
                'en' => 'Products in "' . htmlspecialchars($groupName ?: 'Selected Sub-Group') . '"',
                'cn' => '产品在 "' . htmlspecialchars($groupName ?: '选定的子组') . '"'
            ];
            return $text[$lang];
        } elseif ($selectedGroupId > 0) {
            $groupName = '';
            foreach ($allProductsData as $prod) {
                if ($prod['main_group_id'] == $selectedGroupId || ($prod['sub_group_id'] == $selectedGroupId && $prod['parent_group_id'] === NULL)) {
                    $groupName = $prod['main_group_name'];
                    break;
                }
            }
            $text = [
                'th' => 'สินค้าในกลุ่ม "' . htmlspecialchars($groupName ?: 'กลุ่มหลักที่เลือก') . '"',
                'en' => 'Products in "' . htmlspecialchars($groupName ?: 'Selected Main Group') . '"',
                'cn' => '产品在 "' . htmlspecialchars($groupName ?: '选定的主组') . '"'
            ];
            return $text[$lang];
        } else {
            $text = [
                'th' => 'หมวดหมู่สินค้า',
                'en' => 'Product Categories',
                'cn' => '产品分类'
            ];
            return $text[$lang];
        }
    }

    echo getDisplayText($lang, $searchQuery, $selectedSubGroupId, $selectedGroupId, $allProductsData);
    ?>
</h2>

<div class="product-grid-container">
    <?php if ($searchQuery || $selectedGroupId > 0 || $selectedSubGroupId > 0): ?>
        <?php if (empty($finalDisplayItems)): ?>
            <?php
            $noProductsText = [
                'th' => 'ไม่พบสินค้าตามเงื่อนไขที่ระบุ',
                'en' => 'No products found for your criteria.',
                'cn' => '未找到符合您条件的产品'
            ];
            ?>
            <p><?php echo $noProductsText[$lang]; ?></p>
        <?php else: ?>
            <?php foreach ($finalDisplayItems as $product): ?>
                <div class="box-news">
                    <div class="box-image">
                        <?php $encodedId = urlencode(base64_encode($product['id'])); ?>
                        <a href="shop_detail.php?id=<?php echo $encodedId; ?>&lang=<?php echo $lang; ?>" class="text-news">
                            <?php if (!empty($product['iframe'])): ?>
                                <iframe frameborder="0" src="<?php echo htmlspecialchars($product['iframe']); ?>" width="100%" height="100%" class="note-video-clip"></iframe>
                            <?php elseif (!empty($product['image'])): ?>
                                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
                            <?php else: ?>
                                <?php
                                $noImageText = [
                                    'th' => 'ไม่มีรูปภาพ',
                                    'en' => 'No image available',
                                    'cn' => '没有图片'
                                ];
                                ?>
                                <img src="path/to/default/shop_placeholder.jpg" alt="<?php echo $noImageText[$lang]; ?>">
                            <?php endif; ?>
                        </a>
                    </div>
                    <div class="box-content">
                        <a href="shop_detail.php?id=<?php echo $encodedId; ?>&lang=<?php echo $lang; ?>" class="text-news">
                            <h5 class="line-clamp"><?php echo htmlspecialchars($product['title']); ?></h5>
                            <p class="line-clamp"><?php echo htmlspecialchars($product['description']); ?></p>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php else: // ไม่ใช่การค้นหาหรือกรองกลุ่ม ให้แสดงบล็อกหมวดหมู่หลัก ?>
        <?php if (empty($finalDisplayItems)): ?>
            <?php
            $noCategoriesText = [
                'th' => 'ไม่พบหมวดหมู่ที่มีสินค้า',
                'en' => 'No categories found with assigned products.',
                'cn' => '未找到包含产品的分类'
            ];
            ?>
            <p><?php echo $noCategoriesText[$lang]; ?></p>
        <?php else: ?>
            <?php foreach ($finalDisplayItems as $mainGroupData): ?>
                <div class="main-category-block" data-main-group-id="<?php echo htmlspecialchars($mainGroupData['id']); ?>">
                    <div class="block-image-top">
                        <img src="<?php echo htmlspecialchars($mainGroupData['image'] ?: 'path/to/default/category_placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($mainGroupData['name']); ?>">
                    </div>
                    <div class="block-header-bottom">
                        <div class="block-title-info">
                            <h3><?php echo htmlspecialchars($mainGroupData['name']); ?></h3>
                            <?php
                            $productsText = [
                                'th' => 'รายการ',
                                'en' => 'products',
                                'cn' => '个产品'
                            ];
                            ?>
                            <p class="product-count"><?php echo $mainGroupData['total_products'] . ' ' . $productsText[$lang]; ?></p>
                        </div>
                        <span class="toggle-arrow"><i class="fas fa-chevron-down"></i></span>
                    </div>
                    <div class="dropdown-container">
                        <div class="block-content-accordion">
                            <ul class="sub-category-list">
                                <?php foreach ($mainGroupData['sub_groups'] as $subGroupId => $subGroupData): ?>
                                    <li class="sub-category-item" data-sub-group-id="<?php echo htmlspecialchars($subGroupData['id']); ?>">
                                        <div class="sub-category-header">
                                            <h4><?php echo htmlspecialchars($subGroupData['name']); ?></h4>
                                            <span class="toggle-arrow-sub"><i class="fas fa-chevron-down"></i></span>
                                        </div>
                                        <ul class="product-list-accordion" style="display: none;">
                                            <?php foreach ($subGroupData['products'] as $product): ?>
                                                <li><a href="shop_detail.php?id=<?php echo urlencode(base64_encode($product['id'])); ?>&lang=<?php echo $lang; ?>"><?php echo htmlspecialchars($product['title']); ?></a></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    // Toggle for Main Category blocks
    $('.main-category-block .block-header-bottom').on('click', function() {
        var $mainCategoryBlock = $(this).closest('.main-category-block');
        var $blockContent = $(this).siblings('.dropdown-container');
        var $toggleArrow = $(this).find('.toggle-arrow i');
        
        var isCurrentlyVisible = $blockContent.is(':visible');

        $('.main-category-block').removeClass('active');
        $('.product-grid-container').removeClass('is-dropdown-open');
        $('.main-category-block .dropdown-container').slideUp(300);
        $('.main-category-block .toggle-arrow i').removeClass('fa-chevron-up').addClass('fa-chevron-down');

        if (!isCurrentlyVisible) {
            $mainCategoryBlock.addClass('active');
            $('.product-grid-container').addClass('is-dropdown-open');
            $blockContent.slideDown(300);
            $toggleArrow.removeClass('fa-chevron-down').addClass('fa-chevron-up');
        }
    });

    // Toggle for Sub Category items
    $('.sub-category-item .sub-category-header').on('click', function(e) {
        e.stopPropagation();
        var $productList = $(this).next('.product-list-accordion');
        var $toggleArrowSub = $(this).find('.toggle-arrow-sub i');
        
        $(this).closest('.sub-category-list').find('.product-list-accordion').not($productList).slideUp(300);
        $(this).closest('.sub-category-list').find('.toggle-arrow-sub i').not($toggleArrowSub).removeClass('fa-chevron-up').addClass('fa-chevron-down');
        
        $productList.slideToggle(300, function() {
            if ($productList.is(':visible')) {
                $toggleArrowSub.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            } else {
                $toggleArrowSub.removeClass('fa-chevron-up').addClass('fa-chevron-down');
            }
        });
    });
});
</script>

</body>
</html>
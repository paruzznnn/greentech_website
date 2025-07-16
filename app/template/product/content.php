<?php
// ตรวจสอบว่าได้มีการเชื่อมต่อฐานข้อมูลและตัวแปร $conn พร้อมใช้งานแล้ว
if (!isset($conn)) {
    die("Database connection is not established. Please ensure \$conn is available.");
}

$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$selectedGroupId = isset($_GET['group_id']) ? (int)$_GET['group_id'] : 0;
$selectedSubGroupId = isset($_GET['sub_group_id']) ? (int)$_GET['sub_group_id'] : 0;

$allProductsData = [];
$sqlAllProducts = "SELECT
    dn.shop_id,
    dn.subject_shop,
    dn.description_shop,
    dn.content_shop,
    dn.date_create,
    dn.group_id,
    sub_group.group_id AS sub_group_id,
    sub_group.group_name AS sub_group_name,
    sub_group.parent_group_id,
    main_group.group_id AS main_group_id,
    main_group.group_name AS main_group_name,
    (SELECT dnc.api_path FROM dn_shop_doc dnc WHERE dnc.shop_id = dn.shop_id AND dnc.del = '0' AND dnc.status = '1' ORDER BY dnc.id ASC LIMIT 1) AS first_image_path
FROM dn_shop dn
LEFT JOIN dn_shop_groups sub_group ON dn.group_id = sub_group.group_id
LEFT JOIN dn_shop_groups main_group ON sub_group.parent_group_id = main_group.group_id
WHERE dn.del = '0' AND sub_group.parent_group_id IS NOT NULL";

if ($searchQuery) {
    $safeQuery = $conn->real_escape_string($searchQuery);
    $sqlAllProducts .= " AND (dn.subject_shop LIKE '%$safeQuery%' OR dn.description_shop LIKE '%$safeQuery%')";
}

if ($selectedSubGroupId > 0) {
    $sqlAllProducts .= " AND sub_group.group_id = $selectedSubGroupId";
} elseif ($selectedGroupId > 0) {
    $sqlAllProducts .= " AND main_group.group_id = $selectedGroupId";
}

$sqlAllProducts .= " ORDER BY main_group.group_name ASC, sub_group.group_name ASC, dn.subject_shop ASC";

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
    $subGroupId = $product['sub_group_id'];
    $subGroupName = $product['sub_group_name'];

    if (!$mainGroupId || !$subGroupId) continue;

    if (!isset($organizedGroups[$mainGroupId])) {
        $organizedGroups[$mainGroupId] = [
            'id' => $mainGroupId,
            'name' => $mainGroupName,
            'image' => $product['first_image_path'],
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
        usort($subGroup['products'], fn($a, $b) => strcmp($a['title'], $b['title']));
    }
}

$finalDisplayItems = array_values($organizedGroups);

if ($searchQuery || $selectedGroupId > 0 || $selectedSubGroupId > 0) {
    // If searching or filtering by group, collect all products that matched the query
    foreach ($allProductsData as $product) {
        // Ensure that the product has a group_id
        // และอยู่ในเงื่อนไขการกรองที่เลือก
        $shouldAddProduct = false;
        if ($searchQuery) {
            $shouldAddProduct = true; // ถ้าค้นหา แสดงทั้งหมดที่ตรง
        } elseif ($selectedSubGroupId > 0 && $product['sub_group_id'] == $selectedSubGroupId) {
            $shouldAddProduct = true;
        } elseif ($selectedGroupId > 0) {
            if ($product['main_group_id'] == $selectedGroupId || ($product['sub_group_id'] == $selectedGroupId && $product['main_group_id'] === NULL)) {
                $shouldAddProduct = true;
            }
        }

        if ($shouldAddProduct && !empty($product['group_id']) && $product['group_id'] > 0) {
           $finalDisplayItems[] = [
                'id' => $product['shop_id'],
                'title' => $product['subject_shop'],
                'description' => $product['description_shop'],
                'iframe' => preg_match('/<iframe.*?src=["\'](.*?)["\'].*?>/i', $product['content_shop'], $matches) ? $matches[1] : null,
                'image' => $product['first_image_path']
            ];
        }
    }
} else {
    // If not searching, display main categories as primary blocks
    // Sort main categories by name before passing to display
    ksort($organizedGroups);
    $finalDisplayItems = array_values($organizedGroups);
}
?>

<div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
    <form method="GET" action="">
        <div class="input-group">
            <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($searchQuery); ?>" placeholder="Search product...">
            <button class="btn-search" type="submit"><i class="fas fa-search"></i></button>
        </div>
    </form>
</div>

<h2 style="font-size: 28px; font-weight: bold; margin-top: 20px;">
    <?php
    if ($searchQuery) {
        echo 'Search Results for "' . htmlspecialchars($searchQuery) . '"';
    } elseif ($selectedSubGroupId > 0) {
        $groupName = '';
        foreach ($allProductsData as $prod) {
            if ($prod['sub_group_id'] == $selectedSubGroupId) {
                $groupName = $prod['sub_group_name'];
                break;
            }
        }
        echo 'Products in "' . htmlspecialchars($groupName ?: 'Selected Sub-Group') . '"';
    } elseif ($selectedGroupId > 0) {
        $groupName = '';
        foreach ($allProductsData as $prod) {
            if ($prod['main_group_id'] == $selectedGroupId || ($prod['sub_group_id'] == $selectedGroupId && $prod['parent_group_id'] === NULL)) {
                $groupName = $prod['main_group_name'];
                break;
            }
        }
        echo 'Products in "' . htmlspecialchars($groupName ?: 'Selected Main Group') . '"';
    } else {
        echo 'Product Categories';
    }
    ?>
</h2>

<div class="product-grid-container">
    <?php if ($searchQuery || $selectedGroupId > 0 || $selectedSubGroupId > 0): ?>
        <?php if (empty($finalDisplayItems)): ?>
            <p>No products found for your criteria.</p>
        <?php else: ?>
            <?php foreach ($finalDisplayItems as $product): ?>
                <div class="box-news">
                    <div class="box-image">
                        <?php $encodedId = urlencode(base64_encode($product['id'])); ?>
                        <a href="shop_detail.php?id=<?php echo $encodedId; ?>" class="text-news">
                            <?php if (!empty($product['iframe'])): ?>
                                <iframe frameborder="0" src="<?php echo htmlspecialchars($product['iframe']); ?>" width="100%" height="100%" class="note-video-clip"></iframe>
                            <?php elseif (!empty($product['image'])): ?>
                                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
                            <?php else: ?>
                                <img src="path/to/default/shop_placeholder.jpg" alt="No image available">
                            <?php endif; ?>
                        </a>
                    </div>
                    <div class="box-content">
                        <a href="shop_detail.php?id=<?php echo $encodedId; ?>" class="text-news">
                            <h5 class="line-clamp"><?php echo htmlspecialchars($product['title']); ?></h5>
                            <p class="line-clamp"><?php echo htmlspecialchars($product['description']); ?></p>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php else: // ไม่ใช่การค้นหาหรือกรองกลุ่ม ให้แสดงบล็อกหมวดหมู่หลัก ?>
        <?php if (empty($finalDisplayItems)): ?>
            <p>No categories found with assigned products.</p>
        <?php else: ?>
            <?php foreach ($finalDisplayItems as $mainGroupData): ?>
                <div class="main-category-block" data-main-group-id="<?php echo htmlspecialchars($mainGroupData['id']); ?>">
                    <div class="block-image-top">
                        <img src="<?php echo htmlspecialchars($mainGroupData['image'] ?: 'path/to/default/category_placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($mainGroupData['name']); ?>">
                    </div>
                    <div class="block-header-bottom">
                        <div class="block-title-info">
                            <h3><?php echo htmlspecialchars($mainGroupData['name']); ?></h3>
                            <p class="product-count"><?php echo $mainGroupData['total_products']; ?> products</p>
                        </div>
                        <span class="toggle-arrow"><i class="fas fa-chevron-down"></i></span>
                    </div>
                    <div class="block-content-accordion" style="display: none;">
                        <ul class="sub-category-list">
                            <?php foreach ($mainGroupData['sub_groups'] as $subGroupId => $subGroupData): ?>
                                <li class="sub-category-item" data-sub-group-id="<?php echo htmlspecialchars($subGroupData['id']); ?>">
                                    <div class="sub-category-header">
                                        <h4><?php echo htmlspecialchars($subGroupData['name']); ?></h4>
                                        <span class="toggle-arrow-sub"><i class="fas fa-chevron-down"></i></span>
                                    </div>
                                    <ul class="product-list-accordion" style="display: none;">
                                        <?php foreach ($subGroupData['products'] as $product): ?>
                                            <li><a href="shop_detail.php?id=<?php echo urlencode(base64_encode($product['id'])); ?>"><?php echo htmlspecialchars($product['title']); ?></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<script>
$(document).ready(function() {
    // Toggle for Main Category blocks
    $('.main-category-block .block-header-bottom').on('click', function() {
        var $blockContent = $(this).next('.block-content-accordion');
        var $toggleArrow = $(this).find('.toggle-arrow i');

        // ปิด accordion อื่นๆ ใน Main Category blocks ก่อนเปิดอันใหม่
        $('.block-content-accordion').not($blockContent).slideUp();
        $('.main-category-block .toggle-arrow i').removeClass('fa-chevron-up').addClass('fa-chevron-down');

        $blockContent.slideToggle(function() {
            if ($blockContent.is(':visible')) {
                $toggleArrow.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            } else {
                $toggleArrow.removeClass('fa-chevron-up').addClass('fa-chevron-down');
            }
        });
    });

    // Toggle for Sub Category items
    $('.sub-category-item .sub-category-header').on('click', function() {
        var $productList = $(this).next('.product-list-accordion');
        var $toggleArrowSub = $(this).find('.toggle-arrow-sub i');

        // ปิด accordion อื่นๆ ใน Sub Category items ก่อนเปิดอันใหม่
        $(this).closest('.sub-category-list').find('.product-list-accordion').not($productList).slideUp();
        $(this).closest('.sub-category-list').find('.toggle-arrow-sub i').removeClass('fa-chevron-up').addClass('fa-chevron-down');


        $productList.slideToggle(function() {
            if ($productList.is(':visible')) {
                $toggleArrowSub.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            } else {
                $toggleArrowSub.removeClass('fa-chevron-up').addClass('fa-chevron-down');
            }
        });
    });
});
</script>

<style>
/* General container for the product grid */
.product-grid-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); /* ปรับขนาดบล็อกให้ใหญ่ขึ้นเล็กน้อย */
    gap: 25px; /* เพิ่มช่องว่างระหว่างบล็อก */
    margin-top: 20px;
}

/* Styles for Main Category Blocks */
.main-category-block {
    border: 1px solid #eee;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    background-color: #fff;
    cursor: pointer; /* แสดงว่าคลิกได้ */
    transition: box-shadow 0.2s, transform 0.2s;
    display: flex; /* ใช้ flexbox สำหรับการจัดวางรูปและข้อความ */
    flex-direction: column; /* จัดเรียงในแนวตั้ง: รูปอยู่บน ข้อความอยู่ล่าง */
}

.main-category-block:hover {
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    transform: translateY(-3px);
}

.main-category-block .block-image-top { /* เปลี่ยนชื่อ class เพื่อให้แยกแยะได้ง่ายขึ้น */
    width: 100%;
    height: 200px; /* กำหนดความสูงของรูปภาพให้ใหญ่ขึ้น */
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #e0e0e0;
}

.main-category-block .block-image-top img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.main-category-block .block-header-bottom { /* เปลี่ยนชื่อ class สำหรับส่วน header ที่อยู่ด้านล่างรูป */
    display: flex;
    align-items: center;
    padding: 15px;
    background-color: #f7f7f7;
    border-top: 1px solid #eee; /* เปลี่ยนจาก border-bottom เป็น border-top */
    position: relative;
    flex-grow: 1; /* ให้ใช้พื้นที่ที่เหลือ */
}

.main-category-block .block-title-info {
    flex-grow: 1; /* ให้ใช้พื้นที่ที่เหลือ */
}

.main-category-block .block-title-info h3 {
    font-size: 1.3em;
    font-weight: bold;
    color: #333;
    margin: 0 0 5px 0;
}

.main-category-block .product-count {
    font-size: 0.9em;
    color: #888;
    margin: 0;
}

.main-category-block .toggle-arrow,
.sub-category-item .toggle-arrow-sub {
    font-size: 1.2em;
    color: #555;
    margin-left: 10px;
    transition: transform 0.3s;
}


/* Accordion Content for Main Category */
.block-content-accordion {
    padding: 15px;
    background-color: #ffffff;
}

.sub-category-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sub-category-item {
    margin-bottom: 5px;
    border-bottom: 1px dashed #eee; /* เส้นแบ่งระหว่าง Sub Category */
    padding-bottom: 5px;
}

.sub-category-item:last-child {
    border-bottom: none; /* ไม่ต้องมีเส้นแบ่งที่รายการสุดท้าย */
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
    color: #555;
    transition: color 0.2s;
}

.sub-category-item .sub-category-header:hover {
    color: #ffa719; /* สีเมื่อ hover */
}

.sub-category-item .sub-category-header h4 {
    font-size: 1em;
    margin: 0;
}

/* Accordion Content for Sub Category (Product List) */
.product-list-accordion {
    list-style: none;
    padding-left: 20px; /* เยื้องเข้ามา */
    margin: 5px 0 0 0;
}

.product-list-accordion li {
    padding: 5px 0;
    font-size: 0.95em;
}

.product-list-accordion li a {
    text-decoration: none;
    color: #666;
    display: block;
}

.product-list-accordion li a:hover {
    color: #ffa719; /* สีเมื่อ hover */
    text-decoration: underline;
}

/* For Search Results (flat product list) */
.box-news {
    border: 1px solid #eee;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    transition: transform 0.2s;
    background-color: #fff;
}

.box-news:hover {
    transform: translateY(-5px);
}

.box-image {
    width: 100%;
    height: 200px; /* ขนาดรูปภาพสำหรับผลการค้นหา */
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #f0f0f0;
}

.box-image img, .box-image iframe {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.box-content {
    padding: 15px;
}

.box-content h5 {
    font-size: 1.1em;
    font-weight: bold;
    margin-bottom: 5px;
    height: 40px; /* กำหนดความสูงเพื่อให้ข้อความไม่เกิน */
    overflow: hidden;
}

.box-content p {
    font-size: 0.9em;
    color: #777;
    height: 60px; /* กำหนดความสูงเพื่อให้ข้อความไม่เกิน */
    overflow: hidden;
}

.text-news {
    text-decoration: none;
    color: inherit;
    display: block;
}

.line-clamp {
    display: -webkit-box;
    -webkit-line-clamp: 2; /* จำกัดให้แสดงแค่ 2 บรรทัด */
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
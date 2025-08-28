<?php
require_once '../../server/connect_sqli.php';
require_once '../../server/select_sqli.php';
header('Content-Type: application/json');

/* ---------------POST NEED USE ------------------
$input = file_get_contents("php://input");
$data = json_decode($input, true);
if ($data == null) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid JSON"]);
    exit;
}
-------------------------------------------------*/

/*------- Authorization AND TIME ZONE ---------- */
if (isset($_SESSION['user_timezone'])) {
    date_default_timezone_set($_SESSION['user_timezone']);
} else {
    date_default_timezone_set("UTC");
}
$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}
$authHeader = $headers['Authorization'];
$token = str_replace('Bearer ', '', $authHeader);
$validToken = "my_secure_token_123";
if ($token !== $validToken) {
    http_response_code(403);
    echo json_encode(["error" => "Invalid token"]);
    exit;
}
/*------------------------------------------*/

/*---------ACTION DATA ---------------------*/

$action = $_GET['action'];
if($action == 'getMenuHeaderSideItems'){

   // Step 1: ดึงข้อมูลจาก ecm_link_rel
    $conditions = [['column' => 'del', 'operator' => '=', 'value' => 0]];
    $link_rels = selectData($conn_cloudpanel, 'ecm_link_rel', $conditions, '*');

    // Step 2: รวม link_id ทั้งหมด
    $link_ids = [];
    foreach ($link_rels as $rel) {
        $link_ids[$rel['parent_link_id']] = true;
        $link_ids[$rel['child_link_id']] = true;
    }
    // array_keys() จะดึง key ทั้งหมดของ array ออกมาเป็น array ใหม่ที่เป็น list ของตัวเลข link_id
    $link_ids = array_keys($link_ids);

    // Step 3: ดึงข้อมูลลิงก์
    $link_conditions = [['column' => 'link_id', 'operator' => 'IN', 'value' => $link_ids]];
    $all_links = selectData($conn_cloudpanel, 'ecm_link', $link_conditions, '*');

    // Step 4: สร้าง map link_id => link data
    $link_map = [];
    foreach ($all_links as $link) {
        $link['children'] = []; // เตรียมไว้
        $link_map[$link['link_id']] = $link;
    }

    // Step 5: สร้าง tree โครงสร้างความสัมพันธ์
    foreach ($link_rels as $rel) {
        $parent_id = $rel['parent_link_id'];
        $child_id = $rel['child_link_id'];
        //ตรวจสอบ $link_map และ เพิ่ม children 
        if (isset($link_map[$parent_id]) && isset($link_map[$child_id])) {
            $link_map[$parent_id]['children'][] = &$link_map[$child_id];
        }
    }

    // Step 6: หา root nodes
    $child_ids = array_column($link_rels, 'child_link_id');
    $root_ids = array_diff(array_keys($link_map), $child_ids);

    // array_column() เป็นฟังก์ชันใน PHP ที่ใช้ ดึงค่าของคอลัมน์ (key) ใด key หนึ่ง จาก array ของ array หรือ array ของ associative array ออกมาเป็น array ใหม่
    // array_diff() เป็นฟังก์ชันใน PHP ที่ใช้ หาค่าที่อยู่ในอาเรย์ตัวแรก แต่ไม่มีในอาเรย์ตัวที่สอง (หรือตัวอื่น ๆ ต่อไป)


    // Step 7: แปลง tree เป็นโครงสร้างเมนูที่ต้องการ
    function buildMenu($nodes) {
        global $BASE_WEB;
        $menu = [];
        foreach ($nodes as $node) {
            $item = [
                'icon' => !empty($node['link_icon']) ? $node['link_icon'] : '',
                'title' => $node['link_name'],
                'link' => $BASE_WEB . $node['link_url']
            ];
            if (!empty($node['children'])) {
                $item['subMenu'] = buildMenu($node['children']);
            }
            $menu[] = $item;
        }
        return $menu;
    }

    // Step 8: สร้าง $data เมนู
    $data = buildMenu(array_intersect_key($link_map, array_flip($root_ids)));


    http_response_code(200);
    $response = [
        "data" => $data
    ];

    echo json_encode($response);
    exit;

}
else{

    http_response_code(400);
    echo json_encode([
        "error" => "Unauthorized"
    ]);
    exit;
}


/*----------------------------------*/



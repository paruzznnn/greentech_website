<?php
require_once '../../../server/connect_sqli.php';
require_once '../../../server/convert_array.php';
require_once '../../../server/select_sqli.php';
require_once '../../../server/insert_sqli.php';
header('Content-Type: application/json');


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

/*---------ACTION DATA -------------*/
$input = file_get_contents("php://input");
$dataJson = json_decode($input, true);
if ($dataJson == null) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid JSON"]);
    exit;
}
$action = $dataJson['action'];
$userId = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;
$timeZone = isset($_SESSION['user_timezone']) ? $_SESSION['user_timezone'] : '';
$dateNow = date('Y-m-d H:i:s');

if ($action == 'setupAddLink') {

    $link_icon = isset($dataJson['link_icon']) ? (string) $dataJson['link_icon'] : '';
    $link_label = isset($dataJson['link_label']) ? (string) $dataJson['link_label'] : '';
    $link_path = isset($dataJson['link_path']) ? (string) $dataJson['link_path'] : '';


    $nested = expandFormArray($dataJson);
    if(!empty($nested['sections'])){
        foreach ($nested['sections'] as $sectionId => $section) {
            if (isset($section['images'])) {
                foreach ($section['images'] as $i => $img) {
                    // echo "Section $sectionId Image $i: $img \n";
                    echo "Section $sectionId Image $i: {$img['category']}\n";
                    echo "Section $sectionId Image $i: ({$img['url']})\n";
                }
            }

            if (isset($section['menus'])) {
                foreach ($section['menus'] as $i => $menu) {
                    echo "Section $sectionId Menu $i: {$menu['label']}\n";
                    echo "Section $sectionId Menu $i: ({$menu['href']})\n";
                }
            }

            if (isset($section['text'])) {
                echo "Section $sectionId Text: {$section['text']}\n";
            }
        }
    }





    // http_response_code(200);
    // echo json_encode($response);
    exit;
} else {

    http_response_code(400);
    echo json_encode([
        "error" => "Unauthorized"
    ]);
    exit;
}

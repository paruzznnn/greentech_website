<?php
require_once '../../../server/connect_sqli.php';
require_once '../../../server/package_convert_array.php';
require_once '../../../server/select_sqli.php';
require_once '../../../server/insert_sqli.php';
require_once '../../../server/update_sqli.php';
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

// echo '<pre>';
// print_r($dataJson);
// echo '</pre>';
// exit;

if ($action == 'setupAddLink') {

    try {
        $link_icon  = isset($dataJson['link_icon']) ? (string) $dataJson['link_icon'] : null;
        $link_label = isset($dataJson['link_label']) ? (string) $dataJson['link_label'] : null;
        $link_path  = isset($dataJson['link_path']) ? (string) $dataJson['link_path'] : null;

        $link_role  = isset($dataJson['link_role']) ? (string) $dataJson['link_role'] : null;
        $link_comp  = isset($dataJson['link_comp']) ? (string) $dataJson['link_comp'] : null;
        $open_type  = isset($dataJson['open_type']) ? (string) 'Y' : 'N';

        $link_data = [
            'link_name' => $link_label,
            'link_url'  => $link_path,
            'link_icon' => $link_icon,
            'link_role' => $link_role,
            'link_comp' => $link_comp,
            'link_sub_active' => $open_type
        ];

        $link_id = insertDataAndGetId($conn_cloudpanel, 'ecm_link', $link_data);
        if (empty($link_id)) {
            throw new Exception("ไม่สามารถบันทึก link หลักได้");
        }

        $nested = expandFormArray($dataJson);
        if (!empty($nested['sections'])) {
            foreach ($nested['sections'] as $sectionId => $section) {

                if (isset($section['images'])) {
                    foreach ($section['images'] as $i => $img) {
                        $sort_number = preg_replace('/\D/', '', $sectionId);
                        $img_data = [
                            'link_id'          => $link_id,
                            'link_sub_type'    => 'image',
                            'link_sub_sort'    => $sort_number,
                            'link_sub_img'     => $img['url'],
                            'link_sub_category' => $img['category'],
                            'timezone'         => $timeZone,
                            'created_at'       => $dateNow
                        ];

                        $status_action = $img['status'];
                        if ($status_action === "new") {
                            if (!insertData($conn_cloudpanel, 'ecm_link_sub_img', $img_data)) {
                                throw new Exception("ไม่สามารถบันทึก image section {$sectionId} ได้");
                            }
                        }
                    }
                }

                if (isset($section['menus'])) {
                    foreach ($section['menus'] as $i => $menu) {
                        $sort_number = preg_replace('/\D/', '', $sectionId);
                        $menu_data = [
                            'link_id'       => $link_id,
                            'link_sub_type' => 'menu',
                            'link_sub_sort' => $sort_number,
                            'link_sub_name' => $menu['label'],
                            'link_sub_url'  => $menu['href'],
                            'timezone'      => $timeZone,
                            'created_at'    => $dateNow
                        ];

                        $status_action = $menu['status'];
                        if ($status_action === "new") {
                            if (!insertData($conn_cloudpanel, 'ecm_link_sub_menu', $menu_data)) {
                                throw new Exception("ไม่สามารถบันทึก menu section {$sectionId} ได้");
                            }
                        }
                    }
                }

                if (isset($section['text'])) {
                    $sort_number = preg_replace('/\D/', '', $sectionId);
                    $text_data = [
                        'link_id'       => $link_id,
                        'link_sub_type' => 'text',
                        'link_sub_sort' => $sort_number,
                        'link_sub_text' => $section['text'],
                        'timezone'      => $timeZone,
                        'created_at'    => $dateNow
                    ];

                    $status_action = $section['status'];
                    if ($status_action === "new") {
                        if (!insertData($conn_cloudpanel, 'ecm_link_sub_text', $text_data)) {
                            throw new Exception("ไม่สามารถบันทึก menu section {$sectionId} ได้");
                        }
                    }
                }
            }
        }

        http_response_code(200);
        echo json_encode(["status" => true]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            "status" => false,
            "error"  => $e->getMessage()
        ]);
    } finally {
        if (isset($conn_cloudpanel) && $conn_cloudpanel) {
            $conn_cloudpanel->close();
        }
        exit;
    }
} else if ($action == 'editSetupLink') {

    try {

        $link_id  = isset($dataJson['link_id']) ? (int) $dataJson['link_id'] : 0;
        $link_icon  = isset($dataJson['link_icon']) ? (string) $dataJson['link_icon'] : null;
        $link_label = isset($dataJson['link_label']) ? (string) $dataJson['link_label'] : null;
        $link_path  = isset($dataJson['link_path']) ? (string) $dataJson['link_path'] : null;

        $link_role  = isset($dataJson['link_role']) ? (string) $dataJson['link_role'] : null;
        $link_comp  = isset($dataJson['link_comp']) ? (string) $dataJson['link_comp'] : null;
        $open_type  = isset($dataJson['open_type']) ? (string) 'Y' : 'N';

        if (empty($link_id)) {
            throw new Exception("ไม่สามารถบันทึก link หลักได้");
        }

        $link_data = [
            'link_name' => $link_label,
            'link_url'  => $link_path,
            'link_icon' => $link_icon,
            'link_role' => $link_role,
            'link_comp' => $link_comp,
            'link_sub_active' => $open_type
        ];

        $conditions = [
            'link_id' => $link_id
        ];

        if (!updateData($conn_cloudpanel, 'ecm_link', $link_data, $conditions)) {
            throw new Exception("ไม่สามารถ อัปเดต {$sectionId} ได้");
        }

        $nested = expandFormArray($dataJson);
        $deleteFlag = ['del' => 1];

        if (!empty($nested['sections'])) {
            foreach ($nested['sections'] as $sectionId => $section) {

                $sort_number = preg_replace('/\D/', '', $sectionId);

                // ================= IMAGE =================
                if (isset($section['images'])) {
                    foreach ($section['images'] as $i => $img) {
                        $img_data = [
                            'link_id'           => $link_id,
                            'link_sub_type'     => 'image',
                            'link_sub_sort'     => $sort_number,
                            'link_sub_img'      => $img['url'],
                            'link_sub_category' => $img['category'],
                            'timezone'          => $timeZone,
                            'created_at'        => $dateNow
                        ];

                        $status_action = $img['status'];
                        $sub_id = $img['id'];

                        if ($status_action === "updated") {
                            $conditions = [
                                'link_id'      => $link_id,
                                'id' => $sub_id
                            ];
                            if (!updateData($conn_cloudpanel, 'ecm_link_sub_img', $img_data, $conditions)) {
                                throw new Exception("ไม่สามารถอัปเดตข้อมูล {$sectionId} ได้");
                            }
                            continue;
                        }

                        if ($status_action === "deleted") {
                            $conditions = [
                                'link_id'      => $link_id,
                                'id' => $sub_id
                            ];
                            if (!updateData($conn_cloudpanel, 'ecm_link_sub_img', $deleteFlag, $conditions)) {
                                throw new Exception("ไม่สามารถลบ image section {$sectionId} ได้");
                            }
                            continue;
                        }

                        if ($status_action === "existing") {
                            continue;
                        }

                        if ($status_action === "new") {
                            if (!insertData($conn_cloudpanel, 'ecm_link_sub_img', $img_data)) {
                                throw new Exception("ไม่สามารถบันทึก image section {$sectionId} ได้");
                            }
                        }
                    }
                }

                // ================= MENU =================
                if (isset($section['menus'])) {
                    foreach ($section['menus'] as $i => $menu) {
                        $menu_data = [
                            'link_id'       => $link_id,
                            'link_sub_type' => 'menu',
                            'link_sub_sort' => $sort_number,
                            'link_sub_name' => $menu['label'],
                            'link_sub_url'  => $menu['href'],
                            'timezone'      => $timeZone,
                            'created_at'    => $dateNow
                        ];

                        $status_action = $menu['status'];
                        $sub_id = $menu['id'];

                        if ($status_action === "updated") {
                            $conditions = [
                                'link_id'      => $link_id,
                                'id' => $sub_id
                            ];
                            if (!updateData($conn_cloudpanel, 'ecm_link_sub_menu', $menu_data, $conditions)) {
                                throw new Exception("ไม่สามารถอัปเดตข้อมูล {$sectionId} ได้");
                            }
                            continue;
                        }

                        if ($status_action === "deleted") {
                            $conditions = [
                                'link_id'      => $link_id,
                                'id' => $sub_id
                            ];
                            if (!updateData($conn_cloudpanel, 'ecm_link_sub_menu', $deleteFlag, $conditions)) {
                                throw new Exception("ไม่สามารถลบ menu section {$sectionId} ได้");
                            }
                            continue;
                        }

                        if ($status_action === "existing") {
                            continue;
                        }

                        if ($status_action === "new") {
                            if (!insertData($conn_cloudpanel, 'ecm_link_sub_menu', $menu_data)) {
                                throw new Exception("ไม่สามารถบันทึก menu section {$sectionId} ได้");
                            }
                        }
                    }
                }

                // ================= TEXT =================
                if (isset($section['text'])) {
                    $text_data = [
                        'link_id'       => $link_id,
                        'link_sub_type' => 'text',
                        'link_sub_sort' => $sort_number,
                        'link_sub_text' => $section['text'],
                        'timezone'      => $timeZone,
                        'created_at'    => $dateNow
                    ];

                    $status_action = $section['status'];
                    $sub_id = $section['id'];

                    if ($status_action === "updated") {
                        $conditions = [
                            'link_id'      => $link_id,
                            'id' => $sub_id
                        ];
                        if (!updateData($conn_cloudpanel, 'ecm_link_sub_text', $text_data, $conditions)) {
                            throw new Exception("ไม่สามารถอัปเดตข้อมูล {$sectionId} ได้");
                        }
                        continue;
                    }

                    if ($status_action === "deleted") {
                        $conditions = [
                            'link_id'       => $link_id,
                            'id' => $sub_id
                        ];
                        if (!updateData($conn_cloudpanel, 'ecm_link_sub_text', $deleteFlag, $conditions)) {
                            throw new Exception("ไม่สามารถลบ text section {$sectionId} ได้");
                        }
                        continue;
                    }

                    if ($status_action === "existing") {
                        continue;
                    }

                    if ($status_action === "new") {
                        if (!insertData($conn_cloudpanel, 'ecm_link_sub_text', $text_data)) {
                            throw new Exception("ไม่สามารถบันทึก text section {$sectionId} ได้");
                        }
                    }
                }
            }
        }

        http_response_code(200);
        echo json_encode(["status" => true]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            "status" => false,
            "error"  => $e->getMessage()
        ]);
    } finally {
        if (isset($conn_cloudpanel) && $conn_cloudpanel) {
            $conn_cloudpanel->close();
        }
        exit;
    }
} else {

    http_response_code(400);
    echo json_encode([
        "error" => "Unauthorized"
    ]);
    exit;
}

<?php
session_start();
header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');
require_once '../../lib/connect.php';

$response = array('status' => 'success', 'message' => '');

try {
    $action = isset($_POST['action']) ? $_POST['action'] : null;

    if ($action === 'getCategory') {

        $stmt = $conn->prepare("
            SELECT
                material_id AS id,
                MAX(pic_icon) AS pic_icon,
                MAX(code) AS code,
                MAX(category_name) AS category_name,
                MAX(description) AS description
            FROM ecm_product
            WHERE sync_status = '1'
            GROUP BY material_id
        ");

        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);

        $response['status'] = $data ? 'success' : 'error';
        $response['data'] = $data ?: [];
        if (!$data) $response['message'] = 'No data found';

    } elseif ($action === 'getProducts') {

        $memberId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        $stmt = $conn->prepare("
            SELECT
                material_id AS id,
                code,
                pic_icon,
                category_id,
                category_name,
                description,
                attb_item,
                attb_price,
                attb_value,
                cost,
                currency,
                module,
                stock,
                '' AS member_id,
                sync_status,
                uom
            FROM ecm_product 
            WHERE sync_status = '1'
            ORDER BY stock ASC
        ");

        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);

        if ($data) {
            foreach ($data as &$product) {
                $product['member_id'] = $memberId;
            }
            $response['status'] = 'success';
            $response['data'] = $data;
        } else {
            $response['status'] = 'error';
            $response['message'] = 'No data found';
        }

    } elseif ($action === 'getProductDetail') {

        $memberId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $pro_id = isset($_POST['pro_id']) ? trim($_POST['pro_id']) : '';

        if (empty($pro_id)) {
            throw new Exception("Missing product ID");
        }

        $sql = "
            SELECT
                material_id AS id,
                code,
                pic_icon,
                category_id,
                category_name,
                description,
                attb_item,
                attb_price,
                attb_value,
                cost,
                currency,
                module,
                stock,
                '' AS member_id,
                sync_status,
                uom
            FROM ecm_product 
            WHERE sync_status = '1' AND material_id = ?
        ";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        $stmt->bind_param("s", $pro_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);

        if ($data) {
            foreach ($data as &$product) {
                $product['member_id'] = $memberId;
            }
            $response['status'] = 'success';
            $response['data'] = $data;
        } else {
            $response['status'] = 'error';
            $response['message'] = 'No data found';
        }

    } elseif ($action === 'getReview') {

        $pageNumber = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;
        $offset = ($pageNumber - 1) * 10;
        $prod_id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if ($prod_id <= 0) {
            throw new Exception("Invalid product ID");
        }

        $stmt = $conn->prepare("
            SELECT ecm_review.*, umb_docs.file_path,
                CONCAT(ecm_users.firstname,' ',ecm_users.lastname) AS fullname
            FROM ecm_review
            LEFT JOIN ecm_users ON ecm_users.user_id = ecm_review.member_id
            LEFT JOIN umb_docs ON umb_docs.member_id = ecm_review.member_id
            WHERE pro_id = ?
            ORDER BY ecm_review.review_id DESC
            LIMIT 10 OFFSET ?
        ");

        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        $stmt->bind_param("ii", $prod_id, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);

        $response['status'] = $data ? 'success' : 'error';
        $response['data'] = $data ?: [];
        if (!$data) $response['message'] = 'No data found';

    } elseif ($action === 'getBillboard') {

        $billboard = [
            [
                'id' => 1,
                'title' => 'Advertisement 1',
                'image' => 'https://www.trandar.com/shop/wp-content/uploads/2021/07/Card-Message-Line-TDI-2020-02-768x499.jpg',
                'description' => 'This is the first advertisement'
            ],
            [
                'id' => 2,
                'title' => 'Advertisement 2',
                'image' => 'https://www.trandar.com/shop/wp-content/uploads/2024/10/zivana-01.jpg',
                'description' => 'This is the second advertisement'
            ],
            [
                'id' => 3,
                'title' => 'Advertisement 3',
                'image' => 'https://www.trandar.com/shop/wp-content/uploads/2021/07/Card-Message-Line-TDI-2020-03-768x499.jpg',
                'description' => 'This is the third advertisement'
            ]
        ];

        $response['status'] = 'success';
        $response['data'] = $billboard;

    } else {
        $response['status'] = 'error';
        $response['message'] = 'Invalid action';
    }

} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
}

// Safely close statement
if (isset($stmt) && is_object($stmt)) {
    $stmt->close();
}

// $conn->close();

echo json_encode($response);

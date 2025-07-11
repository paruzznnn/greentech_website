<?php
session_start();
header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');
require_once '../../lib/connect.php';

$response = array('status' => 'success', 'message' => '');

try {
    if (isset($_POST['action']) && $_POST['action'] == 'getCategory') {

        $stmt = $conn->prepare("SELECT
        material_id as id,
        pic_icon,
        code,
        category_name,
        description
        FROM ecm_product WHERE sync_status = '1'
        GROUP BY material_id
        -- LIMIT 0, 6
        ");
        
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        // Execute the SQL statement
        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }

        // Get the result set
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);

        // Check if data is found
        if ($data) {
            $response['status'] = 'success';
            $response['data'] = $data;
        } else {
            $response['status'] = 'error';
            $response['message'] = 'No data found';
        }
        
    } 
    else if(isset($_POST['action']) && $_POST['action'] == 'getProducts') {

        $memberId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        $stmt = $conn->prepare("SELECT
        material_id as id,
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
        '' as member_id,
        sync_status,
        uom
        FROM ecm_product WHERE sync_status = '1'
        GROUP BY material_id
        ORDER BY stock ASC
        -- LIMIT 0, 6
        ");
    
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
    
        // Execute the SQL statement
        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }
    
        // Get the result set
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
    
        // Check if data is found
        if ($data) {
            // Loop through each product and add member_id to product_json
            foreach ($data as &$product) {
                // $product_json = json_decode($product['product_json'], true);
                // $product_json['member_id'] = $memberId;
                // $product['product_json'] = json_encode($product_json);

                $product['member_id'] = $memberId;
            }
    
            // Prepare response
            $response['status'] = 'success';
            $response['data'] = $data;
        } else {
            $response['status'] = 'error';
            $response['message'] = 'No data found';
        }

    } 
    else if(isset($_POST['action']) && $_POST['action'] == 'getProductDetail'){

        $memberId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $where = '';
        if (isset($_POST['pro_id']) && !empty($_POST['pro_id'])) {
            $pro_id = $_POST['pro_id'];
            $pro_id = htmlspecialchars($pro_id, ENT_QUOTES, 'UTF-8'); 
            $where = " AND material_id = '$pro_id'"; 
        }

        $stmt = $conn->prepare("SELECT
        material_id as id,
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
        '' as member_id,
        sync_status,
        uom
        FROM ecm_product WHERE sync_status = '1'
        $where
        GROUP BY material_id
        -- LIMIT 0, 6
        ");
    
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
    
        // Execute the SQL statement
        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }
    
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
    
        if ($data) {
            foreach ($data as &$product) {
                $product['member_id'] = $memberId;
            }
    
            // Prepare response
            $response['status'] = 'success';
            $response['data'] = $data;
        } else {
            $response['status'] = 'error';
            $response['message'] = 'No data found';
        }


    } 
    else if (isset($_POST['action']) && $_POST['action'] == 'getReview') {

        $pageNumber = isset($_POST['page']) ? intval($_POST['page']) : 1;

        // คำนวณค่า OFFSET
        $offset = ($pageNumber - 1) * 10;

        $prod_id = $_POST['id'];
    
        $stmt = $conn->prepare("
            SELECT ecm_review.*, umb_docs.file_path,
            CONCAT(ecm_users.firstname,' ',ecm_users.lastname) as fullname
            FROM ecm_review
            LEFT JOIN ecm_users
            ON ecm_users.user_id = ecm_review.member_id
            LEFT JOIN umb_docs 
            ON umb_docs.member_id = ecm_review.member_id
            WHERE pro_id = ? ORDER BY ecm_review.review_id DESC
            LIMIT 10 OFFSET ?
        ");
    
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
    
        // Bind the parameter to the prepared statement
        $stmt->bind_param('ii', $prod_id, $offset);

    
        // Execute the SQL statement
        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }
    
        // Get the result set
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
    
        // Check if data is found
        if ($data) {
            $response['status'] = 'success';
            $response['data'] = $data;
        } else {
            $response['status'] = 'error';
            $response['message'] = 'No data found';
        }
    
    } 
    else if (isset($_POST['action']) && $_POST['action'] == 'getBillboard') {

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

            if ($billboard) {
                $response['status'] = 'success';
                $response['data'] = $billboard; 
            } else {
                $response['status'] = 'error';
                $response['message'] = 'No data found';
            }
    }
    else {
        $response['status'] = 'error';
        $response['message'] = 'Invalid action';
    }



} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
}

if (isset($stmt)) {
    $stmt->close();
}
if (isset($totalResult)) {
    $totalResult->close();
}
$conn->close();

echo json_encode($response);
?>

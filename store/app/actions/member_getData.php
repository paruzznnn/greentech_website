<?php
session_start();
header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');
require_once '../../lib/connect.php';

$response = array('status' => 'success', 'message' => '');

try {

    if (isset($_POST['action']) && $_POST['action'] == 'get_shipment') {
        
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
        $offset = ($page - 1) * $limit;

        $page = max(1, $page);
        $limit = max(1, $limit);

        $member_id = $_SESSION['user_id'];

        $is_status = 0;
        $stmt = $conn->prepare("
            SELECT address_id, prefix, firstname, lastname, 
            phone_number, detail, country, province_id, district_id, 
            sub_district_id, postcode_id, comp_name, tax_number, create_date, 
            latitude, longitude, is_default
            FROM ecm_address 
            WHERE member_id = ? AND is_status = ?
            LIMIT ?, ?
        ");
        
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        $stmt->bind_param("iiii", $member_id, $is_status, $offset, $limit);
        
        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        
        // Get the total number of records to calculate total pages
        $totalResult = $conn->prepare("SELECT COUNT(address_id) as total FROM ecm_address WHERE member_id = ? AND is_status = ?");
        $totalResult->bind_param("ii", $member_id, $is_status);
        
        if (!$totalResult->execute()) {
            throw new Exception("Count query failed: " . $totalResult->error);
        }

        $totalRow = $totalResult->get_result()->fetch_assoc();
        $totalRecords = $totalRow['total'];
        $totalPages = ceil($totalRecords / $limit);
        
        if ($data) {
            $response['status'] = 'success';
            $response['data'] = $data;
            $response['totalPages'] = $totalPages; 
            $response['totalRecords'] = $totalRecords;
        } else {
            $response['status'] = 'error';
            $response['message'] = 'No data found';
        }
        
    }

    else if (isset($_POST['action']) && $_POST['action'] == 'get_order') {

        // Retrieve and sanitize page and limit
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
        $offset = ($page - 1) * $limit;
    
        // Ensure positive values
        $page = max(1, $page);
        $limit = max(1, $limit);
    
        // Get member ID from session
        $member_id = $_SESSION['user_id'];
        $is_del = 0;
    
        // Prepare the main query
        $stmt = $conn->prepare("
            SELECT
            od.order_id,
            od.is_status,
            GROUP_CONCAT(DISTINCT od.id) AS ids,
            GROUP_CONCAT(DISTINCT od.created_at) AS date_created,
            GROUP_CONCAT(DISTINCT od.order_key) AS order_keys,
            GROUP_CONCAT(DISTINCT od.order_code) AS order_codes,
            GROUP_CONCAT(od.pro_id) AS product_ids,
            GROUP_CONCAT(od.price) AS prices,
            GROUP_CONCAT(od.quantity) AS quantities,
            GROUP_CONCAT(od.total_price) AS total_prices,
            CONCAT(sp.first_name, ' ', sp.last_name) AS fullname,
            sp.address,
            sp.phone_number as phone,
            CONCAT(sp.address, ' ', sp.province, ' ', sp.district, ' ', sp.subdistrict, ' ', sp.post_code) AS shipping,
            pm.pay_channel,
            od.pay_type,
            od.qr_pp,
            od.vehicle_id,
            sp.vehicle_price
            FROM
                ecm_orders od
            LEFT JOIN ord_payment pm ON od.order_id = pm.order_id
            LEFT JOIN ord_shipping sp ON od.order_id = sp.order_id
            WHERE
                od.member_id = ? 
                AND od.is_del = ?
            GROUP BY
            od.order_id, 
            od.created_at,
            od.order_code, 
            sp.address, 
            pm.pay_channel, 
            od.pay_type, 
            od.qr_pp
            ORDER BY od.id DESC
            LIMIT ?, ?
        ");
    
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
    
        $stmt->bind_param("iiii", $member_id, $is_del, $offset, $limit);
    
        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }
    
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
    
        // Prepare and execute count query to get total records
        $totalResult = $conn->prepare("SELECT COUNT(DISTINCT order_id) as total FROM ecm_orders 
        WHERE member_id = ? AND is_del = ?");
        $totalResult->bind_param("ii", $member_id, $is_del);
    
        if (!$totalResult->execute()) {
            throw new Exception("Count query failed: " . $totalResult->error);
        }
    
        $totalRow = $totalResult->get_result()->fetch_assoc();
        $totalRecords = $totalRow['total'];
        $totalPages = ceil($totalRecords / $limit);
    
        // Format the response
        if ($data) {
            $response = array(
                'status' => 'success',
                'data' => $data,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalRecords' => $totalRecords
            );
        } else {
            $response = array(
                'status' => 'error',
                'message' => 'No data found'
            );
        }
    
    }

    else if(isset($_POST['action']) && $_POST['action'] == 'get_member'){

        $member_id = $_SESSION['user_id'];
        $is_status = 0;

        $stmt = $conn->prepare("
        SELECT u.user_id, u.email, u.firstname, u.lastname, u.phone, u.create_date,
        d.file_path
        FROM ecm_users u
        LEFT JOIN umb_docs d ON u.user_id = d.member_id
        WHERE u.user_id = ? AND u.is_status = ? 
        ");
        
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        $stmt->bind_param("ii", $member_id, $is_status);
        
        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);

        if ($data) {
            $response['status'] = 'success';
            $response['data'] = $data;
        } else {
            $response['status'] = 'error';
            $response['message'] = 'No data found';
        }


    }

    else if(isset($_POST['action']) && $_POST['action'] == 'get_track'){

        // Retrieve and sanitize page and limit
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
        $offset = ($page - 1) * $limit;
    
        // Ensure positive values
        $page = max(1, $page);
        $limit = max(1, $limit);
    
        // Get member ID from session
        $member_id = $_SESSION['user_id'];
    
        // Prepare the main query
        $stmt = $conn->prepare("
            SELECT
                od.order_id,
                od.is_status,
                od.track_id,
                GROUP_CONCAT(DISTINCT od.created_at) AS date_created,
                GROUP_CONCAT(DISTINCT od.order_key) AS order_keys,
                GROUP_CONCAT(DISTINCT od.order_code) AS order_codes,
                GROUP_CONCAT(od.pro_id) AS product_ids,
                GROUP_CONCAT(od.price) AS prices,
                GROUP_CONCAT(od.quantity) AS quantities,
                GROUP_CONCAT(od.total_price) AS total_prices,
                CONCAT(sp.first_name,' ',sp.last_name) as fullname,
                sp.address,
                CONCAT(sp.county,' ',sp.district,' ',sp.district,' ',sp.post_code) as shipping,
                pm.pay_channel,
                od.pay_type,
                od.qr_pp
            FROM
                ecm_orders od
            LEFT JOIN ord_payment pm ON
                od.order_id = pm.order_id
            LEFT JOIN ord_evidence ed ON
                od.order_id = ed.order_id
            LEFT JOIN ord_shipping sp ON
                od.order_id = sp.order_id
            WHERE
                od.member_id = ?
            GROUP BY
                od.order_id,
                od.order_code
            LIMIT ?, ?
        ");
    
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
    
        $stmt->bind_param("iii", $member_id, $offset, $limit);
    
        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }
    
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
    
        // Prepare and execute count query to get total records
        $totalResult = $conn->prepare("SELECT COUNT(DISTINCT order_id) as total FROM ecm_orders 
        WHERE member_id = ?");
        $totalResult->bind_param("i", $member_id);
    
        if (!$totalResult->execute()) {
            throw new Exception("Count query failed: " . $totalResult->error);
        }
    
        $totalRow = $totalResult->get_result()->fetch_assoc();
        $totalRecords = $totalRow['total'];
        $totalPages = ceil($totalRecords / $limit);
    
        // Format the response
        if ($data) {
            $response = array(
                'status' => 'success',
                'data' => $data,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalRecords' => $totalRecords
            );
        } else {
            $response = array(
                'status' => 'error',
                'message' => 'No data found'
            );
        }
    }

    else if(isset($_POST['action']) && $_POST['action'] == 'member_address') {

        $member_id = $_SESSION['user_id'];
        $is_default = 1;
    
        // Query 1: ค้นหาข้อมูลที่อยู่ทั้งหมดของสมาชิก
        $stmt = $conn->prepare("
        SELECT address_id, prefix, firstname, lastname, phone_number, detail, 
        country, province_id, district_id, sub_district_id, postcode_id, comp_name,
        create_date, tax_number, latitude, longitude, is_default
        FROM ecm_address 
        WHERE member_id = ?
        ");
        
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
    
        $stmt->bind_param("i", $member_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }
    
        $result = $stmt->get_result();
        $data_all = $result->fetch_all(MYSQLI_ASSOC);
    
        // Query 2: ค้นหาที่อยู่ default ของสมาชิก
        $stmt = $conn->prepare("
        SELECT address_id, prefix, firstname, lastname, phone_number, detail, 
        country, province_id, district_id, sub_district_id, postcode_id, comp_name,
        create_date, tax_number, latitude, longitude, is_default
        FROM ecm_address 
        WHERE member_id = ? AND is_default = ?
        ");
        
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
    
        $stmt->bind_param("ii", $member_id, $is_default);
        
        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }
    
        $result = $stmt->get_result();
        $data_default = $result->fetch_all(MYSQLI_ASSOC);
    
        if ($data_all || $data_default) {
            $response['status'] = 'success';
            $response['all_data'] = $data_all;
            $response['default_data'] = $data_default;
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

<?php
session_start();
header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');
require_once '../../lib/connect.php';

$response = array('status' => 'error', 'message' => '');

try {

    if(isset($_POST['action']) && $_POST['action'] == 'get_shipment'){

        $member_id = $_SESSION['user_id'];
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
        $offset = ($page - 1) * $limit;
        $page = max(1, $page);
        $limit = max(1, $limit);
        $is_status = 0;

        // Escape integer values
        $member_id = (int)$member_id;
        $is_status = (int)$is_status;
        $offset = (int)$offset;
        $limit = (int)$limit;

        // Query 1: Retrieve information shipment
        $sql = "
            SELECT address_id, prefix, firstname, lastname, 
                phone_number, detail, country, province_id, district_id, 
                sub_district_id, postcode_id, comp_name, tax_number, create_date, 
                latitude, longitude, is_default
            FROM ecm_address 
            WHERE member_id = $member_id AND is_status = $is_status
            LIMIT $offset, $limit
        ";

        $result = mysqli_query($conn, $sql);
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Query 2: Count all items
        $count_sql = "
            SELECT COUNT(address_id) as total 
            FROM ecm_address 
            WHERE member_id = $member_id AND is_status = $is_status
        ";

        $count_result = mysqli_query($conn, $count_sql);
        $totalRow = mysqli_fetch_assoc($count_result);
        $totalRecords = $totalRow['total'];
        $totalPages = ceil($totalRecords / $limit);

        $response['data'] = $data;
        $response['totalPages'] = $totalPages;
        $response['totalRecords'] = $totalRecords;

        if(empty($data)){
            $response['status'] = 'error';
            throw new Exception("error get shipment.");
        }else{
            $response['status'] = 'success';
            throw new Exception("success get shipment.");
        }
        
    
    }else if(isset($_POST['action']) && $_POST['action'] == 'get_order'){

            $member_id = (int)$_SESSION['user_id'];
            $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
            $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
            $offset = ($page - 1) * $limit;

            $page = max(1, $page);
            $limit = max(1, $limit);

            $is_del = 0;

            // Query Retrieve informatio order
            $sql = "
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
                    od.member_id = $member_id 
                    AND od.is_del = $is_del
                GROUP BY
                    od.order_id, 
                    od.created_at,
                    od.order_code, 
                    sp.address, 
                    pm.pay_channel, 
                    od.pay_type, 
                    od.qr_pp
                ORDER BY od.id DESC
                LIMIT $offset, $limit
            ";

            $result = mysqli_query($conn, $sql);
            $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

            // Query Count all the numbers
            $count_sql = "
                SELECT COUNT(DISTINCT order_id) as total 
                FROM ecm_orders 
                WHERE member_id = $member_id AND is_del = $is_del
            ";

            $count_result = mysqli_query($conn, $count_sql);
            $totalRow = mysqli_fetch_assoc($count_result);
            $totalRecords = $totalRow['total'];
            $totalPages = ceil($totalRecords / $limit);

            $response['data'] = $data;
            $response['currentPage'] = $page;
            $response['totalPages'] = $totalPages;
            $response['totalRecords'] = $totalRecords;

            if(empty($data)){
                $response['status'] = 'error';
                throw new Exception("error get order.");
            }else{
                $response['status'] = 'success';
                throw new Exception("success member address.");
            }
            

    }else if(isset($_POST['action']) && $_POST['action'] == 'get_member'){

        $member_id = $_SESSION['user_id'];
        $is_status = 0;

        $member_id = (int)$member_id;
        $is_status = (int)$is_status;

        $sql = "
            SELECT u.user_id, u.email, u.firstname, u.lastname, u.phone, u.create_date,
            d.file_path
            FROM ecm_users u
            LEFT JOIN umb_docs d ON u.user_id = d.member_id
            WHERE u.user_id = $member_id AND u.is_status = $is_status
        ";

        $result = $conn->query($sql);

        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        } 
        
        $response['data'] = $data;
        if(empty($data)){
            $response['status'] = 'error';
            throw new Exception("error get member.");
        }else{
            $response['status'] = 'success';
            throw new Exception("success get member.");
        }
    
    }else if(isset($_POST['action']) && $_POST['action'] == 'get_track'){

    }else if(isset($_POST['action']) && $_POST['action'] == 'member_address'){

        $member_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
        $is_default = 1;

        // Query 1: Find all member address information
        $sql_all = "
            SELECT address_id, prefix, firstname, lastname, phone_number, detail, 
            country, province_id, district_id, sub_district_id, postcode_id, comp_name,
            create_date, tax_number, latitude, longitude, is_default
            FROM ecm_address 
            WHERE member_id = $member_id
        ";

        $result_all = mysqli_query($conn, $sql_all);
        $data_all = mysqli_fetch_all($result_all, MYSQLI_ASSOC);

        // Query 2: Find a member's default address
        $sql_default = "
            SELECT address_id, prefix, firstname, lastname, phone_number, detail, 
            country, province_id, district_id, sub_district_id, postcode_id, comp_name,
            create_date, tax_number, latitude, longitude, is_default
            FROM ecm_address 
            WHERE member_id = $member_id AND is_default = $is_default
        ";

        $result_default = mysqli_query($conn, $sql_default);
        $data_default = mysqli_fetch_all($result_default, MYSQLI_ASSOC);
        
        $response['status'] = 'success';
        $response['all_data'] = $data_all;
        $response['default_data'] = $data_default;
        if(empty($data_all) && empty($data_default)){
            $response['status'] = 'error';
            throw new Exception("error member address.");
        }else{
            $response['status'] = 'success';
            throw new Exception("success member address.");
        }

    }else {
        $response['status'] = 'error';
        throw new Exception("Invalid request or missing parameters.");
    }
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} finally {
    echo json_encode($response);
}

?>

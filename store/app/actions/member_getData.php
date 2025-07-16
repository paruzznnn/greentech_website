<?php
session_start();
header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');
require_once '../../lib/connect.php';

$response = array('status' => 'error', 'message' => '');

$member_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$check_process = false;

if(isset($_POST['action']) && $_POST['action'] == 'get_shipment'){

    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
    $offset = ($page - 1) * $limit;
    $page = max(1, $page);
    $limit = max(1, $limit);
    $is_status = 0;

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

    $response = array(
        'status' => 'success',
        'data' => $data, 
        'totalPages' => $totalPages,
        'totalRecords' => $totalRecords
    );

    echo json_encode($response);


}else if(isset($_POST['action']) && $_POST['action'] == 'get_order'){

    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
    $offset = ($page - 1) * $limit;
    $page = max(1, $page);
    $limit = max(1, $limit);
    $is_del = 0;

    $sql = "SELECT
            od.id,
            od.order_id,
            od.is_status,
            od.created_at,
            od.order_key,
            od.order_code,
            od.pro_id AS product_id,
            od.price,
            od.quantity,
            od.total_price,
            CONCAT(sp.first_name, ' ', sp.last_name) AS fullname,
            sp.address,
            sp.phone_number AS phone,
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
            od.id, od.order_id, od.is_status, od.created_at, od.order_key, od.order_code,
            od.pro_id, od.price, od.quantity, od.total_price,
            sp.first_name, sp.last_name, sp.address, sp.phone_number,
            sp.province, sp.district, sp.subdistrict, sp.post_code,
            pm.pay_channel, od.pay_type, od.qr_pp, od.vehicle_id, sp.vehicle_price
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

    $response = array(
        'status' => 'success',
        'data' => $data, 
        'currentPage' => $page,
        'totalPages' => $totalPages,
        'totalRecords' => $totalRecords
    );

    echo json_encode($response);


}else if(isset($_POST['action']) && $_POST['action'] == 'get_member'){
    $is_status = 0;

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

        $response = array(
            'status' => 'success',
            'data' => $data
        );

        echo json_encode($response);


}else if(isset($_POST['action']) && $_POST['action'] == 'get_track'){

}else if(isset($_POST['action']) && $_POST['action'] == 'member_address'){

    $is_default = 1;
    //Query 1: Find all member address information
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

    $response = array(
            'status' => 'success',
            'all_data' => $data_all,
            'default_data' => $data_default
        );

    echo json_encode($response);

}else{
    echo json_encode($response);
}


?>

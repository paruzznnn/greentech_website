<?php
require_once '../../../server/connect_sqli.php';
require_once '../../../server/package_data_table.php';
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
if ($action == 'get_tb_listLink') {

    $draw = isset($_GET['draw']) ? intval($_GET['draw']) : 1;
    $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
    $length = isset($_GET['length']) ? intval($_GET['length']) : 10;
    $searchValue = isset($_GET['search']['value']) ? $conn_cloudpanel->real_escape_string($_GET['search']['value']) : '';

    $orderIndex = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 0;
    $orderDir = isset($_GET['order'][0]['dir']) ? $_GET['order'][0]['dir'] : 'asc';

    $columns = ['link_id', 'link_url', 'link_icon'];
    $whereClause = "del = 0";
    if (!empty($searchValue)) {
    $whereClause .= " AND (link_name LIKE '%$searchValue%')";
    }
    $orderBy = $columns[$orderIndex] . " " . $orderDir;

    $dataQuery = "SELECT *
    FROM ecm_link
    WHERE $whereClause
    ORDER BY $orderBy
    LIMIT $start, $length";

    $dataResult = $conn_cloudpanel->query($dataQuery);
    $data = [];
    while ($row = $dataResult->fetch_assoc()) {
    $data[] = $row;
    }

    $Index = 'link_id';
    $totalRecords = getTotalRecords($conn_cloudpanel, 'ecm_link', $Index);
    $totalFiltered = getFilteredRecordsCount($conn_cloudpanel, 'ecm_link', $whereClause, $Index);

    $response = [
    "draw" => intval($draw),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data,
    ];

    echo json_encode($response);

} else {

    http_response_code(400);
    echo json_encode([
        "error" => "Unauthorized"
    ]);
    exit;
}


/*----------------------------------*/


?>



<?php
require_once '../../../server/connect_sqli.php';
require_once '../../../server/select_sqli.php';
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
$action = $_GET['action'];
$userId = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;

// echo '<pre>';
// print_r($_GET);
// echo '</pre>';
// exit;

if ($action == 'getDataMenu') {

    // 1. Fetch and sanitize input parameters
    $draw = isset($_GET['draw']) ? (int) $_GET['draw'] : 1;
    $start = isset($_GET['start']) ? (int) $_GET['start'] : 0;
    $length = isset($_GET['length']) ? (int) $_GET['length'] : 10;
    $searchValue = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';

    $orderIndex = isset($_GET['order'][0]['column']) ? (int) $_GET['order'][0]['column'] : 0;
    $orderDir = isset($_GET['order'][0]['dir']) ? strtolower($_GET['order'][0]['dir']) : 'asc';

    // 2. Define allowed columns to order by
    $columns = ['link_id'];

    // 3. Validate order index, fallback if invalid
    if (!isset($columns[$orderIndex])) {
        $orderIndex = 0;
    }

    // 4. Validate order direction
    if (!in_array($orderDir, ['asc', 'desc'])) {
        $orderDir = 'asc';
    }

    // 5. Build where clause with safe escaping
    $whereClause = "del = 0";

    if (!empty($searchValue)) {
        $searchValueEscaped = $conn_cloudpanel->real_escape_string($searchValue);
        $whereClause .= " AND link_name LIKE '%$searchValueEscaped%'";
    }

    // 6. Construct the ORDER BY clause safely
    $orderBy = $columns[$orderIndex] . " " . $orderDir;

    // 7. Prepare and execute main data query with LIMIT
    $dataQuery = "SELECT * FROM ecm_link WHERE $whereClause ORDER BY $orderBy LIMIT $start, $length";
    $dataResult = $conn_cloudpanel->query($dataQuery);

    if (!$dataResult) {
        // Handle query error gracefully
        http_response_code(500);
        echo json_encode(['error' => $conn_cloudpanel->error]);
        exit;
    }

    // 8. Fetch data
    $data = [];
    while ($row = $dataResult->fetch_assoc()) {
        $data[] = $row;
    }

    // 10. Get total counts
    $Index = 'link_id';
    $totalRecords = getTotalRecords($conn_cloudpanel, 'ecm_link', $Index);
    $totalFiltered = getFilteredRecordsCount($conn_cloudpanel, 'ecm_link', $whereClause, $Index);

    // 11. Prepare response
    $response = [
        "draw" => $draw,
        "recordsTotal" => (int) $totalRecords,
        "recordsFiltered" => (int) $totalFiltered,
        "data" => $data,
    ];

    // 12. Output JSON response and close connection
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($response);

    $conn_cloudpanel->close();
    exit;
} else {

    http_response_code(400);
    echo json_encode([
        "error" => "Unauthorized"
    ]);
    exit;
}

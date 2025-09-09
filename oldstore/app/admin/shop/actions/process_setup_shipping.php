<?php
header('Content-Type: application/json');
include '../../../../lib/connect.php';
include '../../../../inc/getRecords.php';


if($_POST['action'] == 'getData_shipping'){

    $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
    $searchValue = isset($_POST['search']['value']) ? $conn->real_escape_string($_POST['search']['value']) : '';

    $orderIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
    $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';


    // $order = '';
    // switch ($orderIndex) {
    //     case 0:
    //         $order = 'arb_id ' . $orderDir;
    //         break;
    //     case 1:
    //         $order = 'draw_date ' . $orderDir;
    //         break;
    //     default:
    //         break;
    // }

    // $customParam1 = $_POST['customParam1'];
    // $customParam2 = $_POST['customParam2'];

    // $dateRange = $_POST['filter_date'];
    // $dateArray = explode(" - ", $dateRange);

    // $fromDate = date("Y-m-d 00:00:00", strtotime(str_replace("/", "-", $dateArray[0])));
    // $toDate = date("Y-m-d 23:59:59", strtotime(str_replace("/", "-", $dateArray[1])));

    // $fromDate = date("Y-m-d", strtotime(str_replace("/", "-", $dateArray[0])));
    // $toDate = date("Y-m-d", strtotime(str_replace("/", "-", $dateArray[1])));

    // $filter_date = ($dateRange)? " AND draw_date BETWEEN '".$fromDate."' AND '".$toDate."'" : '';


    $whereClause = "vehicle_id IS NOT NULL";
    // $whereClause .= $filter_date;

    if (!empty($searchValue)) {
        // $whereClause .= " AND (draw_date LIKE '%$searchValue%' OR main_number LIKE '%$searchValue%' OR last_number LIKE '%$searchValue%')";
    }
    
    // -- ORDER BY $order 

    $dataQuery = "SELECT * FROM tms_vehicles 
    WHERE $whereClause 
    LIMIT $start, $length";

    $dataResult = $conn->query($dataQuery);
    $data = [];
    while ($row = $dataResult->fetch_assoc()) {
        $data[] = $row;
    }

    $Index = 'vehicle_id';
    $totalRecords = getTotalRecords($conn, 'tms_vehicles', $Index);
    $totalFiltered = getFilteredRecordsCount($conn, 'tms_vehicles', $whereClause, $Index);

    $response = [
        "draw" => intval($draw),
        "recordsTotal" => intval($totalRecords),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data
    ];

    echo json_encode($response);

    $conn->close();

}

?>
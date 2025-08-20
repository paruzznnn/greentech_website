<?php
header('Content-Type: application/json');
include '../../../../lib/connect.php';
include '../../../../inc/getRecords.php';



if($_POST['action'] == 'getData_menu'){

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


    $whereClause = "menu_id IS NOT NULL";
    // $whereClause .= $filter_date;

    if (!empty($searchValue)) {
        // $whereClause .= " AND (draw_date LIKE '%$searchValue%' OR main_number LIKE '%$searchValue%' OR last_number LIKE '%$searchValue%')";
    }
        
    
    // -- ORDER BY $order 

    $dataQuery = "SELECT * FROM ecm_menu 
    WHERE $whereClause 
    LIMIT $start, $length";

    $dataResult = $conn->query($dataQuery);
    $data = [];
    while ($row = $dataResult->fetch_assoc()) {
        $data[] = $row;
    }

    $Index = 'menu_id';
    $totalRecords = getTotalRecords($conn, 'ecm_menu', $Index);
    $totalFiltered = getFilteredRecordsCount($conn, 'ecm_menu', $whereClause, $Index);

    $response = [
        "draw" => intval($draw),
        "recordsTotal" => intval($totalRecords),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data
    ];

    echo json_encode($response);

    $conn->close();

}

else if($_POST['action'] == 'save_set_memu'){

    $response = array('status' => 'success', 'message' => '');

    try {
        $menu_data = [];

        parse_str($_POST['menuData'], $menu_data);


        // Sanitize the data
        // $menu_data = array_map('htmlspecialchars', $menu_data);

        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO ecm_menu (parent_id, menu_icon, menu_label, menu_link, create_date) 
                                VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        $current_date = date('Y-m-d H:i:s');  // Current date and time

        $stmt->bind_param(
            "issss", 
            $menu_data['menu_main'], 
            $menu_data['set_icon'], 
            $menu_data['munu_name'], 
            $menu_data['munu_path'], 
            $current_date
        );

        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }

        // Success
        $response['message'] = 'Save completed successfully';
    } catch (Exception $e) {
        $response['status'] = 'error';
        $response['message'] = $e->getMessage();
    }

    $stmt->close();
    $conn->close();

    echo json_encode($response);

}

else if ($_POST['action'] == 'getMainMenu') {
    $searchTerm = isset($_POST['search']) ? $_POST['search'] : '';
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $perPage = 10;

    $getMenuQuery = "SELECT menu_id, menu_label FROM `ecm_menu` WHERE parent_id <= 0";
    $getMenuResult = mysqli_query($conn, $getMenuQuery);

    $allOptions = [];

    while ($row = mysqli_fetch_assoc($getMenuResult)) {
        $allOptions[] = [
            'id' => $row['menu_id'], // ชื่อคีย์ที่ส่งกลับ
            'text' => $row['menu_label']
        ];
    }

    $allOptions[] = [
        'id' => 0, // แก้ไขชื่อคีย์ที่เป็น 'id'
        'text' => 'Is main'
    ];

    $filteredOptions = array_filter($allOptions, function($option) use ($searchTerm) {
        return stripos($option['text'], $searchTerm) !== false;
    });

    $totalCount = count($filteredOptions);
    $offset = ($page - 1) * $perPage;
    $paginatedOptions = array_slice($filteredOptions, $offset, $perPage);

    $response = [
        'items' => array_values($paginatedOptions), // ชื่อคีย์ที่ต้องตรงกัน
        'total_count' => $totalCount
    ];

    echo json_encode($response);
}

?>
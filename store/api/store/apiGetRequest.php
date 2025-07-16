<?php
session_start();
header('Content-Type: application/json');
require_once '../../lib/connect.php';
require_once '../../lib/base_directory.php';

// approve_order
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $apiAction = $_GET['action'] ?? null;

    switch ($apiAction) {
        case 'changeSync':
            handleChangeSync($_GET);
            break;
        case 'getOrders':
            handleGetOrders($_GET);
            break;
        case 'upDatePriceTms':
            handleGetTms($_GET);
            break;
        case 'approveOrder':
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
            break;
        case 'reOrder':
            handleGetReOrder($_GET);
            break;
        case 'delOrder':
            handleGetDelOrder($_GET);
            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid or missing apiAction parameter.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

function handleChangeSync($apiGetData) {
    global $conn;
    $response = [];

    $matID = $apiGetData['matId'] ?? null;
    $type = $apiGetData['type'] ?? null;
    $syncSt = ($type === 'sync') ? 1 : (($type === 'notSync') ? 0 : null);

    if (!$matID || !$type || $syncSt === null) {
        $response = [
            'status' => 'error',
            'message' => 'Invalid matID, type, or sync status.'
        ];
        echo json_encode($response);
        return;
    }

    $sql = "SELECT id FROM `ecm_product` WHERE material_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $matID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        updateProduct($stmt, $apiGetData, $syncSt, $matID);
    } else {
        insertProduct($stmt, $apiGetData, $syncSt, $matID);
    }

    $stmt->close();
}

function updateProduct($stmt, $apiGetData, $syncSt, $matID) {
    global $conn;
    $stmt = $conn->prepare('UPDATE ecm_product SET 
        code = ?, 
        pic_icon = ?,
        category_id = ?,
        category_name = ?,
        description = ?,
        attb_item = ?,
        attb_price = ?,
        attb_value = ?,
        cost = ?, 
        currency = ?, 
        module = ?, 
        stock = ?,
        sync_status = ?,
        uom = ? 
    WHERE material_id = ?');

    $stmt->bind_param('sssssssssssssss',
        $apiGetData['code'],
        $apiGetData['pic_icon'],
        $apiGetData['material_category_id'],
        $apiGetData['category_name'],
        $apiGetData['description'],
        $apiGetData['attb_item'],
        $apiGetData['attb_price'],
        $apiGetData['attb_value'],
        $apiGetData['cost'],
        $apiGetData['currency'],
        $apiGetData['module'],
        $apiGetData['material_stock'],
        $syncSt,
        $apiGetData['uom'],
        $matID
    );

    if ($stmt->execute()) {
        $sql = "SELECT sync_status FROM `ecm_product` WHERE material_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $matID);
        $stmt->execute();
        $result = $stmt->get_result();
        $updatedData = $result->fetch_assoc();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Data updated successfully.',
            'data' => $updatedData
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Data update failed: ' . $stmt->error
        ]);
    }
}

function insertProduct($stmt, $apiGetData, $syncSt, $matID) {
    global $conn;
    $stmt = $conn->prepare('INSERT INTO ecm_product (
        material_id, 
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
        sync_status,
        comp_id,
        uom
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');

    $stmt->bind_param('ssssssssssssssss',
        $matID,
        $apiGetData['code'],
        $apiGetData['pic_icon'],
        $apiGetData['material_category_id'],
        $apiGetData['category_name'],
        $apiGetData['description'],
        $apiGetData['attb_item'],
        $apiGetData['attb_price'],
        $apiGetData['attb_value'],
        $apiGetData['cost'],
        $apiGetData['currency'],
        $apiGetData['module'],
        $apiGetData['material_stock'],
        $syncSt,
        $apiGetData['comp_id'],
        $apiGetData['uom']
    );

    if ($stmt->execute()) {
        $sql = "SELECT sync_status FROM `ecm_product` WHERE material_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $matID);
        $stmt->execute();
        $result = $stmt->get_result();
        $updatedData = $result->fetch_assoc();

        echo json_encode([
            'status' => 'success',
            'message' => 'Data inserted successfully.',
            'data' => $updatedData
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Data insertion failed: ' . $stmt->error
        ]);
    }
}

function handleGetOrders($apiGetData) {
    global $conn;
    $is_del = 0;

    
    $sql = "
        SELECT
			od.order_id,
			od.is_status as order_status,
			GROUP_CONCAT(DISTINCT od.id) AS ids,
			GROUP_CONCAT(DISTINCT od.created_at) AS date_created,
			GROUP_CONCAT(DISTINCT od.order_key) AS order_keys,
			GROUP_CONCAT(DISTINCT od.order_code) AS order_codes,
			GROUP_CONCAT(od.pro_id) AS product_ids,
			GROUP_CONCAT(od.price) AS prices,
			GROUP_CONCAT(od.quantity) AS quantities,
			GROUP_CONCAT(od.total_price) AS total_prices,
			CONCAT(sp.first_name, ' ', sp.last_name) AS fullname,
            GROUP_CONCAT(DISTINCT  od.currency) AS currency,
            sp.id as shipping_id,
            sp.phone_number,
            sp.prefix_id,
			sp.address,
			sp.county,
			sp.province,
			sp.district,
			sp.subdistrict,
			sp.post_code,
            sp.vehicle_id,
            sp.vehicle_price,
			pm.pay_channel,
			od.pay_type,
            evd.pic_path,
            evd.upload_date,
            GROUP_CONCAT(od.pic) as picArr,
            GROUP_CONCAT(DISTINCT pd.code) as codeArr,
            GROUP_CONCAT(DISTINCT pd.description) AS description
		FROM
			ecm_orders od
		LEFT JOIN ord_payment pm ON
			od.order_id = pm.order_id
		LEFT JOIN ord_shipping sp ON
			od.order_id = sp.order_id
        LEFT JOIN ord_evidence evd ON 
            od.order_id = evd.order_id
        LEFT JOIN ecm_product pd ON 
            od.pro_id = pd.material_id
		WHERE
			od.is_del = ?
		GROUP BY
			od.order_id,
			od.created_at,
			od.order_code,
			sp.address,
			pm.pay_channel,
			od.pay_type,
			od.qr_pp
		ORDER BY
			od.id
		DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $is_del);

    // Execute and fetch results
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $data = [];

        // Loop through each row and add it to the data array
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        // Encode and output JSON with the retrieved data
        echo json_encode([
            'status' => 'success',
            'message' => 'Data retrieved successfully.',
            'data' => $data
        ]);
    } else {
        // Handle errors if query fails
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to retrieve data.',
            'data' => []
        ]);
    }

    // $stmt_pic->close();
    $stmt->close();
    
}

function handleGetTms($apiGetData){
    global $conn;

    $price_Tms = $apiGetData['priceTms'] ?? null;
    $shipping_ID = intval($apiGetData['shippingID']) ?? null;

    if ($price_Tms === null || $shipping_ID === null) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Price or Shipping ID cannot be null.'
        ]);
        return; // หรือหยุดการทำงาน
    }


    $stmt = $conn->prepare('UPDATE ord_shipping SET 
        vehicle_price = ?
    WHERE id = ?');

    $stmt->bind_param('di',
        $price_Tms,
        $shipping_ID
    );

    if ($stmt->execute()) {

        // $sql = "SELECT sync_status FROM `ecm_product` WHERE material_id = ?";
        // $stmt = $conn->prepare($sql);
        // $stmt->bind_param('s', $matID);
        // $stmt->execute();
        // $result = $stmt->get_result();
        // $updatedData = $result->fetch_assoc();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Data updated successfully.',
            'data' => $apiGetData
        ]);

    } else {

        echo json_encode([
            'status' => 'error',
            'message' => 'Data update failed: ' . $stmt->error
        ]);
        
    }

    $stmt->close();

}

function handleGetDelOrder($apiGetData){

    global $conn;

    $order_id = $apiGetData['order_id'] ?? null;
    $status_del = 1;

    if ($order_id === null) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ID cannot be null.'
        ]);
        return;
    }

    $stmt = $conn->prepare("UPDATE ecm_orders SET is_del = ? WHERE order_id = ?");
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }

    // Bind parameters and execute the update
    $stmt->bind_param("ii", $status_del, $order_id);
    if (!$stmt->execute()) {
        throw new Exception("Execute statement failed: " . $stmt->error);
    }

    // Prepare statement to select products from the updated order
    $stmt = $conn->prepare("SELECT pro_id, quantity FROM ecm_orders WHERE order_id = ?");
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }

    // Bind the order_id and execute the selection
    $stmt->bind_param("i", $order_id);
    if (!$stmt->execute()) {
        throw new Exception("Execute statement failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);

    // Loop through each item and update the product stock
    foreach ($data as $item) {
        // Prepare statement for stock update
        $sqlUpdate = "UPDATE ecm_product SET stock = stock + ? WHERE material_id = ?";
        $updateStmt = $conn->prepare($sqlUpdate);
        if (!$updateStmt) {
            throw new Exception("Prepare update statement failed: " . $conn->error);
        }

        // Bind parameters for stock update
        $updateStmt->bind_param("is", $item['quantity'], $item['pro_id']);
        if (!$updateStmt->execute()) {
            throw new Exception("Execute update statement failed: " . $updateStmt->error);
        }
        
        // Close the update statement
        $updateStmt->close();
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Data updated successfully.',
        'data' => $apiGetData
    ]);

    $stmt->close();

}

function handleGetReOrder($apiGetData){
    global $conn;

    $order_id = $apiGetData['order_id'] ?? null;
    $status_ord = 3;

    if ($order_id === null) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ID cannot be null.'
        ]);
        return;
    }


    $stmt = $conn->prepare('UPDATE ecm_orders SET 
        is_status = ?
    WHERE order_id = ?');

    $stmt->bind_param('ii',
        $status_ord,
        $order_id
    );

    if ($stmt->execute()) {

        // $sql = "SELECT sync_status FROM `ecm_product` WHERE material_id = ?";
        // $stmt = $conn->prepare($sql);
        // $stmt->bind_param('s', $matID);
        // $stmt->execute();
        // $result = $stmt->get_result();
        // $updatedData = $result->fetch_assoc();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Data updated successfully.',
            'data' => $apiGetData
        ]);

    } else {

        echo json_encode([
            'status' => 'error',
            'message' => 'Data update failed: ' . $stmt->error
        ]);
        
    }

    $stmt->close();

}

?>

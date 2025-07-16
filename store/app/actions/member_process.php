<?php
session_start();
header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');
require_once '../../lib/connect.php';

function handleFileUpload($file, $conn, $member_id, $file_id = null) {
    $uploadDir = 'uploads/';
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxFileSize = 100 * 1024 * 1024; // 100 MB

    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            return ['status' => 'error', 'message' => 'Failed to create upload directory.'];
        }
    }

    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileName = basename($file['name']);
        $fileTmpName = $file['tmp_name'];
        $fileType = $file['type'];
        $fileSize = $file['size'];

        if (!in_array($fileType, $allowedTypes)) {
            return ['status' => 'error', 'message' => 'Unsupported file type. Allowed types are: ' . implode(', ', $allowedTypes)];
        }

        if ($fileSize > $maxFileSize) {
            return ['status' => 'error', 'message' => 'File size exceeds the maximum limit of ' . ($maxFileSize / 1024 / 1024) . ' MB.'];
        }

        $filePath = $uploadDir . time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '', $fileName);

        if (move_uploaded_file($fileTmpName, $filePath)) {
            $fileNameEsc   = $conn->real_escape_string($fileName);
            $filePathEsc   = $conn->real_escape_string($filePath);
            $fileTypeEsc   = $conn->real_escape_string($fileType);
            $fileSize      = (int) $fileSize;
            $upload_date   = date('Y-m-d H:i:s');
            $member_id     = (int) $member_id;

            if ($file_id) {
                $file_id = (int) $file_id;

                $sql = "
                    UPDATE umb_docs SET
                        file_name = '$fileNameEsc',
                        file_path = '$filePathEsc',
                        file_size = $fileSize,
                        file_type = '$fileTypeEsc',
                        upload_date = '$upload_date'
                    WHERE id = $file_id AND member_id = $member_id
                ";
                $result = mysqli_query($conn, $sql);

                if (!$result) {
                    return ['status' => 'error', 'message' => 'Failed to update file metadata.', 'error' => mysqli_error($conn)];
                }
            } else {
                $is_del = 0;
                $is_status = 1;

                $sql = "
                    INSERT INTO umb_docs (
                        member_id, file_name, file_path, file_size, file_type, is_del, is_status
                    ) VALUES (
                        $member_id, '$fileNameEsc', '$filePathEsc', $fileSize, '$fileTypeEsc', $is_del, $is_status
                    )
                ";
                $result = mysqli_query($conn, $sql);

                if (!$result) {
                    return ['status' => 'error', 'message' => 'Failed to insert file metadata.', 'error' => mysqli_error($conn)];
                }
            }

            return ['status' => 'success', 'message' => 'File uploaded successfully.'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to move uploaded file.'];
        }

    } else {
        return ['status' => 'error', 'message' => 'File upload error. Code: ' . $file['error']];
    }
}

$response = array('status' => 'error', 'message' => '');
$member_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if(isset($_POST['action']) && $_POST['action'] == 'add_shipment'){

    if (isset($_POST['shipping'])) {
        $shipping_data = [];
        parse_str($_POST['shipping'], $shipping_data);
    }

    if (!empty($shipping_data)) {

        $shipping = $shipping_data;

        // Escape string data
        $prefix         = $conn->real_escape_string($shipping['prefix'] ?? '');
        $firstname      = $conn->real_escape_string($shipping['first_name'] ?? '');
        $lastname       = $conn->real_escape_string($shipping['last_name'] ?? '');
        $phone_number   = $conn->real_escape_string($shipping['phone_number'] ?? '');
        $address_detail = $conn->real_escape_string($shipping['address'] ?? '');
        $comp_name      = $conn->real_escape_string($shipping['comp_name'] ?? '');
        $tax_number     = $conn->real_escape_string($shipping['tax_number'] ?? '');
        $latitude       = $conn->real_escape_string(strval($shipping['inputLatitude'] ?? ''));
        $longitude      = $conn->real_escape_string(strval($shipping['inputLongitude'] ?? ''));

        // Integer casting for numeric fields
        $country     = (int)($shipping['country'] ?? 0);
        $province    = (int)($shipping['province'] ?? 0);
        $district    = (int)($shipping['district'] ?? 0);
        $subdistrict = (int)($shipping['subdistrict'] ?? 0);
        $postcode    = (int)($shipping['post_code'] ?? 0);

        $current_date = date('Y-m-d H:i:s');

        // SQL insert (not using prepared statement)
        $sql = "
            INSERT INTO ecm_address (
                member_id, prefix, firstname, lastname, phone_number,
                detail, country, province_id, district_id, sub_district_id, 
                postcode_id, comp_name, tax_number, create_date, latitude, longitude
            ) VALUES (
                '$member_id', '$prefix', '$firstname', '$lastname', '$phone_number',
                '$address_detail', $country, $province, $district, $subdistrict,
                $postcode, '$comp_name', '$tax_number', '$current_date', '$latitude', '$longitude'
            )
        ";

        $result = mysqli_query($conn, $sql);

        if ($result) {
            $response = [
                'status' => 'success',
                'message' => 'add_shipment'
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'insert_failed',
                'error' => mysqli_error($conn)
            ];
        }

        echo json_encode($response);
    }

}else if(isset($_POST['action']) && $_POST['action'] == 're_shipment'){

    $address_id = (int) $_POST['dataID'];
    $statusActive = (int) ($_POST['dataValue'] ?? 0);

    $dataType = $_POST['dataType'] ?? '';

    switch ($dataType) {

        case 'active':
            // Reset all is_default to 0
            $sql_up = "UPDATE ecm_address SET is_default = '0' WHERE member_id = '" . $conn->real_escape_string($member_id) . "'";
            mysqli_query($conn, $sql_up);

            // Set selected address as default
            $sql = "
                UPDATE ecm_address 
                SET is_default = '$statusActive'
                WHERE address_id = '$address_id' AND member_id = '" . $conn->real_escape_string($member_id) . "'
            ";
            $result = mysqli_query($conn, $sql);

            $response = $result
                ? ['status' => 'success', 'message' => 'active']
                : ['status' => 'error', 'message' => 'update_failed', 'error' => mysqli_error($conn)];
            break;

        case 'remove':
            $status_del = 1;
            $is_default = 0;
            $sql = "
                UPDATE ecm_address 
                SET is_status = '$status_del', is_default = '$is_default'
                WHERE address_id = '$address_id' AND member_id = '" . $conn->real_escape_string($member_id) . "'
            ";
            $result = mysqli_query($conn, $sql);

            $response = $result
                ? ['status' => 'success', 'message' => 'remove']
                : ['status' => 'error', 'message' => 'remove_failed', 'error' => mysqli_error($conn)];
            break;

        case 'save':
            $data = $_POST['dataValue'];

            // Escape all input values
            $prefix         = $conn->real_escape_string($data[0]['prefix'] ?? '');
            $firstname      = $conn->real_escape_string($data[1]['firstname'] ?? '');
            $lastname       = $conn->real_escape_string($data[2]['lastname'] ?? '');
            $country        = (int)($data[3]['country'] ?? 0);
            $province       = (int)($data[4]['province'] ?? 0);
            $district       = (int)($data[5]['district'] ?? 0);
            $subdistrict    = (int)($data[6]['subdistrict'] ?? 0);
            $post_code      = (int)($data[7]['post_code'] ?? 0);
            $phone_number   = $conn->real_escape_string($data[8]['phone_number'] ?? '');
            $address        = $conn->real_escape_string($data[9]['address'] ?? '');
            $comp_name      = $conn->real_escape_string($data[10]['comp_name'] ?? '');
            $tax_number     = $conn->real_escape_string($data[11]['tax_number'] ?? '');
            $inputLatitude  = $conn->real_escape_string($data[12]['inputLatitude'] ?? '');
            $inputLongitude = $conn->real_escape_string($data[13]['inputLongitude'] ?? '');
            $current_date   = date('Y-m-d H:i:s');

            $sql = "
                UPDATE ecm_address SET 
                    prefix = '$prefix',
                    firstname = '$firstname',
                    lastname = '$lastname',
                    phone_number = '$phone_number',
                    detail = '$address',
                    country = $country,
                    province_id = $province,
                    district_id = $district,
                    sub_district_id = $subdistrict,
                    postcode_id = $post_code,
                    comp_name = '$comp_name',
                    tax_number = '$tax_number',
                    update_date = '$current_date',
                    latitude = '$inputLatitude',
                    longitude = '$inputLongitude'
                WHERE address_id = '$address_id' AND member_id = '" . $conn->real_escape_string($member_id) . "'
            ";

            $result = mysqli_query($conn, $sql);

            $response = $result
                ? ['status' => 'success', 'message' => 'save']
                : ['status' => 'error', 'message' => 'save_failed', 'error' => mysqli_error($conn)];
            break;

        default:
            $response = ['status' => 'error', 'message' => 'invalid_action'];
            break;
    }

    echo json_encode($response);


}else if(isset($_POST['action']) && $_POST['action'] == 'save_member'){

    $member = [
        'first_name' => $_POST['first_name'] ?? '',
        'last_name'  => $_POST['last_name'] ?? '',
        'email'      => $_POST['email'] ?? '',
        'phone'      => $_POST['phone'] ?? '',
    ];

    if ($_FILES['profile_image']['error'] != 4) {
        $sql_check = "SELECT id, member_id FROM umb_docs WHERE member_id = '$member_id'";
        $result = mysqli_query($conn, $sql_check);
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

        $file_id = ($data && isset($data[0]['id'])) ? $data[0]['id'] : '';

        handleFileUpload($_FILES['profile_image'], $conn, $member_id, $file_id);
    }

    if (!empty($member)) {
        // Escape 
        $firstname      = $conn->real_escape_string($member['first_name']);
        $lastname       = $conn->real_escape_string($member['last_name']);
        $member_email   = $conn->real_escape_string($member['email']);
        $member_phone   = $conn->real_escape_string($member['phone']);
        $current_date   = date('Y-m-d H:i:s');

        //update member
        $sql_update = "
            UPDATE ecm_users SET 
                firstname = '$firstname', 
                lastname = '$lastname', 
                email = '$member_email', 
                phone = '$member_phone', 
                update_date = '$current_date'
            WHERE user_id = '$member_id'
        ";

        $result = mysqli_query($conn, $sql_update);

        $response = $result
            ? ['status' => 'success', 'message' => 'save']
            : ['status' => 'error', 'message' => 'update_failed', 'error' => mysqli_error($conn)];
    }

    echo json_encode($response);

}else if(isset($_POST['action']) && $_POST['action'] == 're_orderBuy'){


    $order_id  = (int) ($_POST['dataID'] ?? 0);
    $status_del = 1;

    // 1. อัปเดตสถานะคำสั่งซื้อให้ถูกลบ
    $sqlUpdateOrder = "
        UPDATE ecm_orders 
        SET is_del = $status_del 
        WHERE order_id = $order_id AND member_id = $member_id
    ";
    $resUpdate = mysqli_query($conn, $sqlUpdateOrder);

    if (!$resUpdate) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update order.',
            'error' => mysqli_error($conn)
        ]);
        exit;
    }

    // 2. ดึงข้อมูลสินค้าที่อยู่ในคำสั่งซื้อนี้
    $sqlSelect = "
        SELECT pro_id, quantity 
        FROM ecm_orders 
        WHERE order_id = $order_id
    ";
    $result = mysqli_query($conn, $sqlSelect);

    if (!$result) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to fetch order items.',
            'error' => mysqli_error($conn)
        ]);
        exit;
    }

    $items = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // 3. อัปเดต stock สินค้าแต่ละตัว
    foreach ($items as $item) {
        $pro_id = $conn->real_escape_string($item['pro_id']);
        $quantity = (int) $item['quantity'];

        $sqlUpdateStock = "
            UPDATE ecm_product 
            SET stock = stock + $quantity 
            WHERE material_id = '$pro_id'
        ";
        $resStock = mysqli_query($conn, $sqlUpdateStock);

        if (!$resStock) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to update stock for product: ' . $pro_id,
                'error' => mysqli_error($conn)
            ]);
            exit;
        }
    }

    // 4. สำเร็จ
    echo json_encode([
        'status' => 'success',
        'message' => 'Stock updated successfully.'
    ]);

}else if(isset($_POST['action']) && $_POST['action'] == 'saveReview'){


    // $member_id = $_POST['member'] ?? '';
    // $comment_text = $_POST['comment'] ?? null;
    // $rating_val = $_POST['rating'] ?? '';
    // $pro_id = $_POST['prod_id'] ?? '';

    // $stmt = $conn->prepare("INSERT INTO ecm_review 
    // (
    // pro_id, member_id, description, rating
    // ) 
    // VALUES (?, ?, ?, ?)");

    // if (!$stmt) {
    //     throw new Exception("Prepare statement failed: " . $conn->error);
    // }

    // $stmt->bind_param(
    //     "iisi", 
    //     $pro_id, 
    //     $member_id,
    //     $comment_text,
    //     $rating_val
    // );

    // if (!$stmt->execute()) {
    //     throw new Exception("Execute statement failed: " . $stmt->error);
    // }

    // $response = array('status' => 'success', 'message' => 'save');

}else{
    echo json_encode($response);
}

?>




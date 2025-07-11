<?php
session_start();
header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');
require_once '../../lib/connect.php';

$response = array('status' => 'error', 'message' => '');

function handleFileUpload($file, $dbConnection, $member_id, $file_id = null) {
    $uploadDir = 'uploads/';
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxFileSize = 2 * 1024 * 1024; // 2 MB

    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception("Failed to create upload directory.");
        }
    }

    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileName = basename($file['name']);
        $fileTmpName = $file['tmp_name'];
        $fileType = $file['type'];
        $fileSize = $file['size'];

        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception("Unsupported file type. Allowed types are: " . implode(', ', $allowedTypes));
        }

        if ($fileSize > $maxFileSize) {
            throw new Exception("File size exceeds the maximum limit of " . ($maxFileSize / 1024 / 1024) . " MB.");
        }

        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($fileTmpName, $filePath)) {
            if ($file_id) {
                // Update existing file
                $stmt = $dbConnection->prepare("UPDATE umb_docs 
                    SET file_name = ?, file_path = ?, file_size = ?, file_type = ?, upload_date = ? 
                    WHERE id = ? AND member_id = ?");

                if (!$stmt) {
                    throw new Exception("Prepare statement failed: " . $dbConnection->error);
                }

                $upload_date = date('Y-m-d H:i:s');

                $stmt->bind_param(
                    "ssissii", 
                    $fileName, 
                    $filePath, 
                    $fileSize, 
                    $fileType, 
                    $upload_date, 
                    $file_id, 
                    $member_id
                );

                if (!$stmt->execute()) {
                    throw new Exception("Error updating file metadata: " . $stmt->error);
                }

            } else {
                // Insert new file
                $stmt = $dbConnection->prepare("INSERT INTO umb_docs 
                    (member_id, file_name, file_path, file_size, file_type, is_del, is_status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)");

                if (!$stmt) {
                    throw new Exception("Prepare statement failed: " . $dbConnection->error);
                }

                $is_del = 0; 
                $is_status = 1; 
                // $upload_date = date('Y-m-d H:i:s');

                $stmt->bind_param(
                    "issisii", 
                    $member_id, 
                    $fileName, 
                    $filePath, 
                    $fileSize, 
                    $fileType, 
                    $is_del, 
                    $is_status
                );

                if (!$stmt->execute()) {
                    throw new Exception("Error saving file metadata: " . $stmt->error);
                }

                $stmt->close();
            }

        } else {
            throw new Exception("Failed to move uploaded file.");
        }
    } else {
        throw new Exception("File upload error: " . $file['error']);
    }

}


try {

    if(isset($_POST['action']) && $_POST['action'] == 'add_shipment') {

        if (isset($_POST['shipping'])) {
            $shipping_data = [];
            parse_str($_POST['shipping'], $shipping_data);
            // $orderArray['shipping'] = array_map('htmlspecialchars', $shipping_data);
        }

        if (isset($shipping_data)) {
            $shipping = $shipping_data;
            $stmt = $conn->prepare("INSERT INTO ecm_address 
            (
            member_id, prefix, firstname, lastname, phone_number,
            detail, country, province_id, district_id, sub_district_id, 
            postcode_id, comp_name, tax_number, create_date, latitude, longitude
            ) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $conn->error);
            }

            $member_id = $_SESSION['user_id'] ?? '';
            $prefix = $shipping['prefix'] ?? '';
            $firstname = $shipping['first_name'] ?? null;
            $lastname = $shipping['last_name'] ?? null;
            $phone_number = $shipping['phone_number'] ?? null;
            $address_detail = $shipping['address'] ?? null;

            $country = $shipping['country'] ?? null;
            $province = $shipping['province'] ?? '';
            $district = $shipping['district'] ?? '';
            $subdistrict = $shipping['subdistrict'] ?? '';
            $postcode = $shipping['post_code'] ?? '';
            
            $comp_name = $shipping['comp_name'] ?? null;
            $tax_number = $shipping['tax_number'] ?? null;

            $current_date = date('Y-m-d H:i:s');
            
            $latitude = strval($shipping['inputLatitude']) ?? null;
            $longitude = strval($shipping['inputLongitude']) ?? null;
            
            $stmt->bind_param(
                "iisssssiiiisssss", 
                $member_id, 
                $prefix,
                $firstname, 
                $lastname,
                $phone_number,
                $address_detail, 
                $country, 
                $province, 
                $district, 
                $subdistrict,
                $postcode,
                $comp_name,
                $tax_number,
                $current_date,
                $latitude,
                $longitude
            );

            if (!$stmt->execute()) {
                throw new Exception("Execute statement failed: " . $stmt->error);
            }

            $response = array('status' => 'success', 'message' => 'add_shipment');

        }
        
    }

    else if(isset($_POST['action']) && $_POST['action'] == 're_shipment'){

        $member_id = $_SESSION['user_id'];
        $status_del = 1;
        $address_id = $_POST['dataID'];
        $statusActive = $_POST['dataValue'];

        switch ($_POST['dataType']) {

            case 'active':

                    $sql_up = "UPDATE ecm_address SET is_default = '0' WHERE member_id = '".$member_id."'";
                    $rs_up = mysqli_query($conn, $sql_up);

                    $stmt = $conn->prepare("UPDATE ecm_address 
                    SET is_default = ?
                    WHERE address_id = ? AND member_id = ?");
                    if (!$stmt) {
                        throw new Exception("Prepare statement failed: " . $conn->error);
                    }

                    $stmt->bind_param(
                    "iii", 
                    $statusActive, 
                    $address_id,
                    $member_id
                    );

                    if (!$stmt->execute()) {
                        throw new Exception("Execute statement failed: " . $stmt->error);
                    }

                    $response = array('status' => 'success', 'message' => 'active');
                
                break;
            case 'remove':

                    $stmt = $conn->prepare("UPDATE ecm_address 
                    SET is_status = ?
                    WHERE address_id = ? AND member_id = ?");
                    if (!$stmt) {
                        throw new Exception("Prepare statement failed: " . $conn->error);
                    }

                    $stmt->bind_param(
                    "iii", 
                    $status_del, 
                    $address_id,
                    $member_id
                    );

                    if (!$stmt->execute()) {
                        throw new Exception("Execute statement failed: " . $stmt->error);
                    }

                    $response = array('status' => 'success', 'message' => 'remove');

                break;
            case 'save':

                    $stmt = $conn->prepare("UPDATE ecm_address 
                    SET prefix = ?, 
                        firstname = ?, 
                        lastname = ?, 
                        phone_number = ?, 
                        detail = ?, 
                        country = ?, 
                        province_id = ?, 
                        district_id = ?, 
                        sub_district_id = ?, 
                        postcode_id = ?, 
                        comp_name = ?, 
                        tax_number = ?, 
                        update_date = ?,
                        latitude = ?,
                        longitude = ?
                    WHERE address_id = ? AND member_id = ?");

                    if (!$stmt) {
                    throw new Exception("Prepare statement failed: " . $conn->error);
                    }

                    // Get the current date and time
                    $current_date = date('Y-m-d H:i:s');

                    // Extract values from $_POST with default values if not set
                    $prefix = $_POST['dataValue'][0]['prefix'] ?? null;
                    $firstname = $_POST['dataValue'][1]['firstname'] ?? null;
                    $lastname = $_POST['dataValue'][2]['lastname'] ?? null;
                    $country = $_POST['dataValue'][3]['country'] ?? null;
                    $province = $_POST['dataValue'][4]['province'] ?? null;
                    $district = $_POST['dataValue'][5]['district'] ?? null;
                    $subdistrict = $_POST['dataValue'][6]['subdistrict'] ?? null;
                    $post_code = $_POST['dataValue'][7]['post_code'] ?? null;
                    $phone_number = $_POST['dataValue'][8]['phone_number'] ?? null;
                    $address = $_POST['dataValue'][9]['address'] ?? null;
                    $comp_name = $_POST['dataValue'][10]['comp_name'] ?? null;
                    $tax_number = $_POST['dataValue'][11]['tax_number'] ?? null;
                    $inputLatitude = $_POST['dataValue'][12]['inputLatitude'] ?? null;
                    $inputLongitude = $_POST['dataValue'][13]['inputLongitude'] ?? null;

                    // Bind parameters and execute the statement
                    $stmt->bind_param(
                    "isssssiiiisssssii", 
                    $prefix,
                    $firstname,
                    $lastname,
                    $phone_number,
                    $address,
                    $country,
                    $province,
                    $district,
                    $subdistrict,
                    $post_code,
                    $comp_name,
                    $tax_number,
                    $current_date,
                    $inputLatitude,
                    $inputLongitude,
                    $address_id,
                    $member_id
                    );

                    if (!$stmt->execute()) {
                    throw new Exception("Execute statement failed: " . $stmt->error);
                    }

                    $response = array('status' => 'success', 'message' => 'save');
            
                break;
            default:
                break;
        }

    }

    else if(isset($_POST['action']) && $_POST['action'] == 'save_member'){

        $member = [
            'first_name' => $_POST['first_name'] ?? '',
            'last_name'  => $_POST['last_name'] ?? '',
            'email'      => $_POST['email'] ?? '',
            'phone'      => $_POST['phone'] ?? '',
        ];

        if($_FILES['profile_image']['error'] != 4){

            $member_id = $_SESSION['user_id'];

            $stmt = $conn->prepare("SELECT id, member_id FROM umb_docs WHERE member_id = ?");
            $stmt->bind_param("i", $member_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute statement failed: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $data = $result->fetch_all(MYSQLI_ASSOC);

            $file_id = ($data) ? $data[0]['id'] : '';

            handleFileUpload($_FILES['profile_image'], $conn, $_SESSION['user_id'], $file_id);

        }

        if (isset($member)) {
            
            // Prepare the UPDATE statement
            $stmt = $conn->prepare("UPDATE ecm_users SET
                firstname = ?, 
                lastname = ?, 
                email = ?, 
                phone = ?, 
                update_date = ?
                WHERE user_id = ?");
        
            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $conn->error);
            }
        
            $member_id = $_SESSION['user_id'];
            $firstname = $member['first_name'];
            $lastname = $member['last_name'];
            $member_email = $member['email'];
            $member_phone = $member['phone'];
            $current_date = date('Y-m-d H:i:s');
            
            // Bind the parameters
            $stmt->bind_param(
                "sssssi", 
                $firstname, 
                $lastname, 
                $member_email, 
                $member_phone, 
                $current_date, 
                $member_id
            );
        
            // Execute the statement
            if (!$stmt->execute()) {
                throw new Exception("Execute statement failed: " . $stmt->error);
            }

            $response = array('status' => 'success', 'message' => 'save');
        }

    }

    else if (isset($_POST['action']) && $_POST['action'] == 're_orderBuy') {

        $member_id = $_SESSION['user_id'];
        $order_id = $_POST['dataID'];
        $status_del = 1;
    
        // Prepare statement to update order status
        $stmt = $conn->prepare("UPDATE ecm_orders SET is_del = ? WHERE order_id = ? AND member_id = ?");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
    
        // Bind parameters and execute the update
        $stmt->bind_param("iii", $status_del, $order_id, $member_id);
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
    
        // Create a successful response
        $response = array(
            'status' => 'success',
            'message' => 'Stock updated successfully'
        );
    
    }
    

    else if(isset($_POST['action']) && $_POST['action'] == 'saveReview'){

        $member_id = $_POST['member'] ?? '';
        $comment_text = $_POST['comment'] ?? null;
        $rating_val = $_POST['rating'] ?? '';
        $pro_id = $_POST['prod_id'] ?? '';
    
        $stmt = $conn->prepare("INSERT INTO ecm_review 
        (
        pro_id, member_id, description, rating
        ) 
        VALUES (?, ?, ?, ?)");
        
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        $stmt->bind_param(
            "iisi", 
            $pro_id, 
            $member_id,
            $comment_text,
            $rating_val
        );

        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }

        $response = array('status' => 'success', 'message' => 'save');

    }

} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
}

if (isset($stmt)) {
    $stmt->close();
}
$conn->close();

echo json_encode($response);


?>




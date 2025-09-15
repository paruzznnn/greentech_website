<?php

function generateOrderNumber(mysqli $conn, string $gencode): string {
    // SQL query (ใช้ ? placeholder แทนการใส่ค่าตรง ๆ)
    $sql = "
        SELECT COALESCE(
            MAX(CONVERT(SUBSTRING_INDEX(order_id, '-', -1), UNSIGNED INTEGER)), 
            0
        ) + 1 AS maxcode
        FROM ecm_orders
        WHERE order_id LIKE CONCAT(?, '-%')
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // bind_param: "s" = string
    $stmt->bind_param("s", $gencode);

    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    // ดึง maxcode จาก query
    $maxcode = $row['maxcode'] ?? 1;

    // สร้าง order_id ใหม่ เช่น ORD-001, ORD-002
    return $gencode . '-' . str_pad($maxcode, 3, "0", STR_PAD_LEFT);
}

function insertData(
    mysqli $conn,
    string $table_name,
    array $data
): bool {
    if (empty($data)) {
        return false;
    }

    $columns = implode(", ", array_keys($data));
    $placeholders = implode(", ", array_fill(0, count($data), "?"));
    $sql = "INSERT INTO `$table_name` ($columns) VALUES ($placeholders)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        return false;
    }

    $types = '';
    $values = [];
    foreach ($data as $value) {
        if (is_int($value)) {
            $types .= 'i';
        } elseif (is_double($value)) {
            $types .= 'd';
        } else {
            $types .= 's';
        }
        $values[] = $value;
    }

    $stmt->bind_param($types, ...$values);

    $result = $stmt->execute();
    if (!$result) {
        echo "Execute failed: " . $stmt->error;
    }

    $stmt->close();
    return $result;
}

// $user_data = [
//     'name' => 'Jane Doe',
//     'email' => 'jane.doe@example.com',
//     'age' => 28,
//     'created_at' => date('Y-m-d H:i:s')
// ];

// if (insertData($conn, 'users', $user_data)) {
//     echo "Data inserted successfully!";
// } else {
//     echo "Error inserting data.";
// }

function insertDataAndGetId(
    mysqli $conn,
    string $table_name,
    array $data
): int|false {
    if (empty($data)) {
        return false;
    }

    $columns = implode(", ", array_keys($data));
    $placeholders = implode(", ", array_fill(0, count($data), "?"));
    $sql = "INSERT INTO `$table_name` ($columns) VALUES ($placeholders)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        return false;
    }

    $types = '';
    $values = [];
    foreach ($data as $value) {
        if (is_int($value)) {
            $types .= 'i';
        } elseif (is_float($value)) { // ใช้ is_float แทน is_double (is_double มีอยู่ แต่ is_float อ่านง่ายกว่า)
            $types .= 'd';
        } else {
            $types .= 's';
        }
        $values[] = $value;
    }

    $stmt->bind_param($types, ...$values);

    if (!$stmt->execute()) {
        echo "Execute failed: " . $stmt->error;
        $stmt->close();
        return false;
    }

    $inserted_id = $stmt->insert_id;
    $stmt->close();

    return $inserted_id;
}

// $data = [
//     'name' => 'John Doe',
//     'email' => 'john@example.com',
//     'age' => 30
// ];

// $insertedId = insertDataAndGetId($conn, 'users', $data);
// if ($insertedId !== false) {
//     echo "Inserted with ID: " . $insertedId;
// } else {
//     echo "Insert failed";
// }
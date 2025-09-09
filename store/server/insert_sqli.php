<?php
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
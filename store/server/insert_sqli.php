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

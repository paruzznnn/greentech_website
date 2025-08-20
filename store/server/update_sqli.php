<?php

function updateData(mysqli $conn, string $table_name, array $data, array $conditions): bool {
    if (empty($data) || empty($conditions)) {
        return false;
    }

    $set_parts = [];
    foreach ($data as $column => $value) {
        $set_parts[] = "`$column` = ?";
    }
    $set_clause = implode(", ", $set_parts);

    $where_parts = [];
    foreach ($conditions as $column => $value) {
        $where_parts[] = "`$column` = ?";
    }
    $where_clause = implode(" AND ", $where_parts);

    $sql = "UPDATE `$table_name` SET $set_clause WHERE $where_clause";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        return false;
    }

    $all_values = array_merge(array_values($data), array_values($conditions));

    $types = '';
    foreach ($all_values as $value) {
        if (is_int($value)) {
            $types .= 'i';
        } elseif (is_double($value)) {
            $types .= 'd';
        } else {
            $types .= 's';
        }
    }

    $stmt->bind_param($types, ...$all_values);

    $result = $stmt->execute();
    if (!$result) {
        echo "Execute failed: " . $stmt->error;
    }

    $stmt->close();
    return $result;
}

// อัปเดตข้อมูลของผู้ใช้ที่มีอีเมลเป็น jane.doe@example.com
// $data_to_update = [
//     'name' => 'Jane D.',
//     'age' => 29
// ];

// $conditions = [
//     'email' => 'jane.doe@example.com'
// ];

// if (updateData($conn, 'users', $data_to_update, $conditions)) {
//     echo "User updated successfully!";
// } else {
//     echo "Failed to update user.";
// }

?>
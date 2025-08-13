<?php
//=============== selectData ========================
function selectData(mysqli $conn, string $table_name, array $conditions = [], string $columns = '*')
{
    $sql = "SELECT $columns FROM `$table_name`";
    $values = [];

    if (!empty($conditions)) {
        $where_clauses = [];
        foreach ($conditions as $column => $value) {
            $where_clauses[] = "`$column` = ?";
            $values[] = $value;
        }
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
    }

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        return false;
    }

    if (!empty($values)) {
        $types = '';
        foreach ($values as $value) {
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_double($value)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }
        $stmt->bind_param($types, ...$values);
    }

    if (!$stmt->execute()) {
        echo "Execute failed: " . $stmt->error;
        return false;
    }

    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();

    return $data;
}

// เรียกดูข้อมูลทั้งหมดในตาราง users
// $all_users = selectData($conn, 'users');
// echo "<h3>All Users:</h3>";
// foreach ($all_users as $user) {
//     echo $user['name'] . ' - ' . $user['email'] . '<br>';
// }

// เรียกดูข้อมูลเฉพาะคนที่มีอีเมลตรงกับเงื่อนไข
// $filtered_users = selectData($conn, 'users', ['email' => 'jane.doe@example.com']);
// echo "<h3>Filtered User:</h3>";
// foreach ($filtered_users as $user) {
//     echo $user['name'] . ' - ' . $user['email'] . '<br>';
// }

//=============== selectComplexData ========================
function selectComplexData(
    mysqli $conn,
    string $base_table,
    string $joins = '',
    string $columns = '*',
    array $conditions = [],
    string $orderBy = '',
    string $limit = ''
) {
    $sql = "SELECT $columns FROM `$base_table` $joins";

    $values = [];
    if (!empty($conditions)) {
        $where_parts = [];
        foreach ($conditions as $column => $value) {
            $where_parts[] = "$column = ?";
            $values[] = $value;
        }
        $sql .= " WHERE " . implode(" AND ", $where_parts);
    }

    if (!empty($orderBy)) {
        $sql .= " ORDER BY $orderBy";
    }

    if (!empty($limit)) {
        $sql .= " LIMIT $limit";
    }

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        return false;
    }

    if (!empty($values)) {
        $types = '';
        foreach ($values as $value) {
            $types .= is_int($value) ? 'i' : (is_double($value) ? 'd' : 's');
        }
        $stmt->bind_param($types, ...$values);
    }

    if (!$stmt->execute()) {
        echo "Execute failed: " . $stmt->error;
        return false;
    }

    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $data;
}

// $joins = "
//     INNER JOIN orders ON users.id = orders.user_id
//     INNER JOIN order_items ON orders.id = order_items.order_id
// ";

// $columns = "users.name AS user_name, users.email, orders.order_date, order_items.product_name, order_items.quantity";

// // เงื่อนไข: เฉพาะผู้ใช้ที่อีเมล = ...
// $conditions = [
//     'users.email' => 'jane.doe@example.com'
// ];

// $data = selectComplexData(
//     $conn,
//     'users',       // base table
//     $joins,
//     $columns,
//     $conditions,
//     'orders.order_date DESC', // ORDER BY
//     '10'                     // LIMIT
// );

// echo "<h3>Order Summary:</h3>";
// foreach ($data as $row) {
//     echo "{$row['user_name']} ordered {$row['product_name']} ({$row['quantity']}) on {$row['order_date']}<br>";
// }

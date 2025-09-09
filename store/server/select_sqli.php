<?php
//=============== selectData ========================
function selectData(
    mysqli $conn,
    string $table_name,
    array $conditions = [],
    string $columns = '*',
    string $order_by = '',
    string $limit = '',
    string $group_by = ''
) {
    $sql = "SELECT $columns FROM `$table_name`";
    $values = [];

    if (!empty($conditions)) {
        $where_clauses = [];
        foreach ($conditions as $index => $condition) {
            if (!isset($condition['column'], $condition['operator'], $condition['value'])) {
                continue;
            }

            $logic = ($index > 0 && isset($condition['logic'])) ? strtoupper($condition['logic']) : 'AND';
            $column = $condition['column'];
            $operator = strtoupper($condition['operator']);
            $value = $condition['value'];

            if (!in_array($operator, ['=', '!=', '<', '<=', '>', '>=', 'LIKE', 'IN'])) {
                continue;
            }

            if ($operator === 'IN' && is_array($value)) {
                $placeholders = implode(',', array_fill(0, count($value), '?'));
                $where_clause = "`$column` IN ($placeholders)";
                foreach ($value as $v) {
                    $values[] = $v;
                }
            } else {
                $where_clause = "`$column` $operator ?";
                $values[] = $value;
            }

            if ($index > 0) {
                $where_clauses[] = "$logic $where_clause";
            } else {
                $where_clauses[] = $where_clause;
            }
        }

        if (!empty($where_clauses)) {
            $sql .= " WHERE " . implode(' ', $where_clauses);
        }
    }

    if (!empty($group_by)) {
        $sql .= " GROUP BY $group_by";
    }

    if (!empty($order_by)) {
        $sql .= " ORDER BY $order_by";
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


// $conditions = [
//     ['column' => 'status', 'operator' => '=', 'value' => 'active'],
//     ['column' => 'age', 'operator' => '>=', 'value' => 18, 'logic' => 'AND'],
//     ['column' => 'name', 'operator' => 'LIKE', 'value' => '%john%', 'logic' => 'OR']
// ];

// $data = selectData($conn, 'users', $conditions, '*', 'id DESC', '10');


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

// $conditions = [
//     'users.email' => 'jane.doe@example.com'
// ];

// $data = selectComplexData(
// $conn,
// 'users',       // base table
// $joins,
// $columns,
// $conditions,
// 'orders.order_date DESC', // ORDER BY
// '10'                     // LIMIT
// );

// echo "<h3>Order Summary:</h3>";
// foreach ($data as $row) {
//     echo "{$row['user_name']} ordered {$row['product_name']} ({$row['quantity']}) on {$row['order_date']}<br>";
// }

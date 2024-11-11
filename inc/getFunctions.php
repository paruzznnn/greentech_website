<?php

function getTotalRecords($conn, $tableName, $Index) {
    if (!tableExists($conn, $tableName)) {
        return 0;
    }
    
    $query = "SELECT COUNT($Index) AS count FROM $tableName";
    $result = $conn->query($query);

    if ($result && $row = $result->fetch_assoc()) {
        return intval($row['count']);
    } else {
        return 0;
    }
}

function getFilteredRecordsCount($conn, $tableName, $whereClause, $Index) {
    if (!tableExists($conn, $tableName)) {
        return 0;
    }
    
    $query = "SELECT COUNT($Index) AS count FROM $tableName WHERE $whereClause";
    $result = $conn->query($query);

    if ($result && $row = $result->fetch_assoc()) {
        return intval($row['count']);
    } else {
        return 0;
    }
}

function tableExists($conn, $tableName) {
    $query = "SHOW TABLES LIKE '$tableName'";
    $result = $conn->query($query);

    return $result && $result->num_rows > 0;
}

function insertIntoDatabase($conn, $table, $columns, $values) {

    $placeholders = implode(', ', array_fill(0, count($values), '?'));

    $query = "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES ($placeholders)";

    $stmt = $conn->prepare($query);

    $types = str_repeat('s', count($values));
    $stmt->bind_param($types, ...$values);

    if($stmt->execute()){
        return 1;
    }else{
        return 0;
    }

}

function updateInDatabase($conn, $table, $columns, $values, $whereClause, $whereValues) {

    $setPart = implode(', ', array_map(function($col) {
        return "$col = ?";
    }, $columns));
    
    $query = "UPDATE $table SET $setPart WHERE $whereClause";
    
    $stmt = $conn->prepare($query);

    // Bind parameters
    $types = str_repeat('s', count($values)) . str_repeat('s', count($whereValues));
    $stmt->bind_param($types, ...array_merge($values, $whereValues));

    if($stmt->execute()){
        return 1;
    }else{
        return 0;
    }

}


?>



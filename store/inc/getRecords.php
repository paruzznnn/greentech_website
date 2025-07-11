<?php

function getTotalRecords($conn, $tableName, $Index) {
    if (!tableExists($conn, $tableName)) {
        return 0;
    }
    
    // สร้างคำสั่ง SQL เพื่อคำนวณจำนวนแถวทั้งหมด
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
    
    // สร้างคำสั่ง SQL เพื่อคำนวณจำนวนแถวที่ถูกกรอง
    $query = "SELECT COUNT($Index) AS count FROM $tableName WHERE $whereClause";
    $result = $conn->query($query);

    if ($result && $row = $result->fetch_assoc()) {
        return intval($row['count']);
    } else {
        return 0;
    }
}

// ฟังก์ชันเพื่อตรวจสอบว่าตารางมีอยู่ในฐานข้อมูลหรือไม่
function tableExists($conn, $tableName) {
    $query = "SHOW TABLES LIKE '$tableName'";
    $result = $conn->query($query);

    return $result && $result->num_rows > 0;
}

?>



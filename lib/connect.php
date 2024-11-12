<?php
// if($_SERVER['SERVER_NAME'] == 'dev.allhiapp.com'){

//     $dbHostPD = "localhost:3306";
//     $dbUserPD = "allhi_store";
//     $dbPassPD = "6Of6h#1w1";
//     $dbNamePD = "allhi_store";

//     $host = $dbHostPD; 
//     $username = $dbUserPD; 
//     $password = $dbPassPD; 
//     $database = $dbNamePD;

// }else{

    $host = "202.129.16.77";
    $username = "allable";
    $password = "@LL@ble#2018";
    $database = "allable_db";

// }


$conn = new mysqli($host, $username, $password, $database);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Close the connection
// $conn->close();
?>
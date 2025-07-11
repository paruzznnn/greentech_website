<?php
if($_SERVER['SERVER_NAME'] == ''){

    $dbHostPD = "localhost:3306";
    $dbUserPD = "";
    $dbPassPD = "6Of6h#1w1";
    $dbNamePD = "";

    $host = $dbHostPD; 
    $username = $dbUserPD; 
    $password = $dbPassPD; 
    $database = $dbNamePD;

}else{

    $dbHost = "localhost";
    $dbUser = "root";
    $dbPass = "";
    $dbName = "store_db";

    // Database configuration
    $host = $dbHost; 
    $username = $dbUser; 
    $password = ''; 
    $database = $dbName;

}


$conn = new mysqli($host, $username, $password, $database);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Close the connection
// $conn->close();
?>
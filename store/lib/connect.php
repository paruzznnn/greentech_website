<?php

if($_SERVER['REQUEST_SCHEME'] == 'https'){

    $host = "localhost";
    $username = "storedb";
    $password = "allable1988";
    $database = "store_db";

}else if($_SERVER['REQUEST_SCHEME'] == 'http'){

    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "store_db";

}



$conn = new mysqli($host, $username, $password, $database);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Close the connection
// $conn->close();
?>
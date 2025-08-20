<?php
    $server_name = $_SERVER['SERVER_NAME'];
    $dbHost = "localhost";
    $dbName = "store_db";
    if ($server_name === 'localhost' || $server_name === '127.0.0.1') {
        // Localhost configuration
        $dbUser = "root";
        $dbPass = "";
        $host = $dbHost;
        $username = $dbUser;
        $password = $dbPass;
        $database = $dbName;
    } else {
        // Production configuration
        $dbUser = "tdi2025admin";
        $dbPass = "wD20#20dW";
        $host = $dbHost;
        $username = $dbUser;
        $password = $dbPass;
        $database = $dbName;
    }
    $conn = new mysqli($host, $username, $password, $database);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>

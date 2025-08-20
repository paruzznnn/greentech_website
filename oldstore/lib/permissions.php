<?php
session_start();

function checkPermissions() {
    if(empty($_SESSION)){
        header("Location: login.php");
        exit();
    } else if($_SESSION['exp'] < time()){
        header("Location: logout.php");
        exit();
    } else if($_SESSION['role'] > 1){
        header("Location: login.php");
        exit();
    }
}
?>
<?php
session_start();

function checkPermissions() {

    if (empty($_SESSION)) {
        header("Location: logout.php");
        exit();
    }

    if ($_SESSION['exp'] < time()) {
        header("Location: logout.php");
        exit();
    }

    if ($_SESSION['role_id'] <= 0) {
        header("Location: logout.php");
        exit();
    }



}
?>
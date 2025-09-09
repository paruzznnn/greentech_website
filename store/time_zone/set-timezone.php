<?php
session_start();
if (isset($_POST['tz'])) {
    $_SESSION['user_timezone'] = $_POST['tz'];
}
?>
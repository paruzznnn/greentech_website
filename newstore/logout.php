<?php
@session_start();
session_destroy();

header('Location: /trandar_website/newstore/'); 
exit;
?>
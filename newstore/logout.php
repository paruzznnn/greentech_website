<?php
@session_start();
session_destroy();

header('Location: /e-store/'); 
exit;
?>
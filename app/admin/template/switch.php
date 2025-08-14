<?php
session_start();
$auth_url = 'https://www.origami.life';
$app_id = $_SESSION['app'];
$app_token = $_SESSION['token'];
        if($app_token){
            $url = $auth_url."/api/oauth/v2/switch?token={$app_token}&app={$app_id}";
            header('Location: '.$url);
        }

?>
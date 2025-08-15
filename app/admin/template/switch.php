<?php
    session_start();
    $auth_url   = 'https://www.origami.life/singlesignon/auth/';
    $code       = $_SESSION['code'];
    $token      = $_SESSION['token'];
    if($token){
        $dataArray = array(
            'token' => $token,
            'code' => $code
        );
        $jsonData = json_encode($dataArray);
        $encodedData = urlencode($jsonData);
        $separator = (strpos($auth_url, '?') !== false) ? '&' : '?';
        $finalUrl = $auth_url . $separator . 'data=' . $encodedData;
        header("Location: " . $finalUrl);
        exit();
    }
?>
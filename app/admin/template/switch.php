<?php
    session_start();
    $auth_url = 'https://www.origami.life/singlesignon/auth/';
    $code = $_SESSION['code'];
    $token = $_SESSION['token'];
    if ($token) {
        $dataArray = [
            'token' => $token,
            'code'  => $code
        ];
        $jsonData = json_encode($dataArray);
        echo '<form id="ssoForm" action="'.$auth_url.'" method="post">';
        echo '<input type="hidden" name="data" value=\'' . htmlspecialchars($jsonData, ENT_QUOTES, 'UTF-8') . '\'>';
        echo '</form>';
        echo '<script>document.getElementById("ssoForm").submit();</script>';
        exit();
    }
?>
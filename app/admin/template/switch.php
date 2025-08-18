<?php
    session_start();
    $auth_url = 'https://www.origami.life/singlesignon/auth/';
    $oid = $_SESSION['oid'];
  
    if ($oid) {
        $dataArray = [
            
            'oid'  => $oid
        ];
        $jsonData = json_encode($dataArray);
        echo '<form id="ssoForm" action="'.$auth_url.'" method="post">';
        echo '<input type="hidden" name="data" value=\'' . htmlspecialchars($jsonData, ENT_QUOTES, 'UTF-8') . '\'>';
        echo '</form>';
        echo '<script>document.getElementById("ssoForm").submit();</script>';
        exit();
    }
?>
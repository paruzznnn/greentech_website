<?php
include_once '../lib/connect_sqli.php';
$client_id = $_ENV['LINE_CLIENT_ID'];
$client_secret = $_ENV['LINE_CLIENT_SECRET'];
$redirect_uri = $_ENV['LINE_REDIRECT_URI'];

/* ----------------------------- */
/*           FUNCTIONS           */
/* ----------------------------- */

function getLineToken($code) {
    global $client_id, $client_secret, $redirect_uri;
    $params = http_build_query([
        'grant_type' => 'authorization_code',
        'code' => $code,
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'redirect_uri' => $redirect_uri
    ]);

    $curl = curl_init("https://api.line.me/oauth2/v2.1/token");

    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $params,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/x-www-form-urlencoded"
        ],
        CURLOPT_SSL_VERIFYPEER => false
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        return json_encode(['error' => true, 'message' => $err]);
    }

    return $response;
}

function getLineProfile($access_token) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.line.me/v2/profile", 
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer " . $access_token
        ],
        CURLOPT_SSL_VERIFYPEER => false 
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    return $response;
}

if (!isset($_GET['code'])) {
    echo '<script language="javascript">window.location = "../auth/login";</script>';
    exit;
}

$token_response = json_decode(getLineToken($_GET['code']), true);
$profile_json = getLineProfile($token_response['access_token']);
$profile = json_decode($profile_json, true);

echo '<pre>';
print_r($profile);
echo '</pre>';
exit;


if (isset($token_response['access_token'])) {
    $profile_json = getLineProfile($token_response['access_token']);
    $profile = json_decode($profile_json, true);

    $line_id = $profile['userId'];
    $stmt_select = $conn->prepare("SELECT m_id, line_id, register FROM m_member WHERE line_id = ?");
    $stmt_select->bind_param("s", $line_id);
    $stmt_select->execute();
    $result_select = $stmt_select->get_result();

    if ($result_select->num_rows > 0) {

        $row = $result_select->fetch_assoc();
        if ($line_id == $row['line_id']) {
            $jwt = generateJWT($row['m_id']); 
            if($jwt['token']){
                $iat = $jwt['data']->iat;
                $exp = $jwt['data']->exp;
                $_SESSION['member_id'] = $row['m_id'];
                $_SESSION['register'] = $row['register'];
                $_SESSION['iat'] = $iat;
                $_SESSION['exp'] = $exp;
                echo '<script language="javascript">window.location = "../app/index";</script>';
            }else{
                echo '<script language="javascript">window.location = "../auth/login";</script>';
            }
        }

    } else {
  
        $register = 'N';
        $stmt_insert = $conn->prepare("INSERT INTO m_member (date_signup, line_id, register) VALUES (NOW(), ?, ?)");
        $stmt_insert->bind_param("ss", $line_id, $register);
        $success = $stmt_insert->execute();
        $last_id = $conn->insert_id;

        if(!empty($last_id)){
            $jwt = generateJWT($last_id); 
            if($jwt['token']){

                $iat = $jwt['data']->iat;
                $exp = $jwt['data']->exp;

                $_SESSION['member_id'] = $last_id;
                $_SESSION['register'] = $register;
                $_SESSION['iat'] = $iat;
                $_SESSION['exp'] = $exp;

                echo '<script language="javascript">window.location = "../app/index";</script>';
            }else{
                echo '<script language="javascript">window.location = "../auth/login";</script>';
            }
        }

        $stmt_insert->close();
    }

    $stmt_select->close();
    $conn->close();

} else {
    echo "Failed to get access token";
}

?>
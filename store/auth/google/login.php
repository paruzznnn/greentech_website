<?php
require_once '../../server/connect_sqli.php';
$client_id = $_ENV['GOOGLE_CLIENT_ID'];
$client_secret = $_ENV['GOOGLE_CLIENT_SECRET'];
$redirect_uri = $_ENV['GOOGLE_REDIRECT_URI'];

/* ----------------------------- */
/*           FUNCTIONS           */
/* ----------------------------- */

class GoogleAuth {
    private $client_id;
    private $client_secret;
    private $redirect_uri;

    public function __construct($client_id, $client_secret, $redirect_uri) {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->redirect_uri = $redirect_uri;
    }

    public function getAccessToken($code) {
        $params = http_build_query([
            'code' => $code,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'redirect_uri' => $this->redirect_uri,
            'grant_type' => 'authorization_code',
        ]);

        $curl = curl_init("https://oauth2.googleapis.com/token");

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
            return [
                'error' => true,
                'message' => $err
            ];
        }

        return json_decode($response, true);
    }

    public function getUserProfile($access_token) {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://www.googleapis.com/oauth2/v2/userinfo",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $access_token
            ],
            CURLOPT_SSL_VERIFYPEER => false 
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }
}

$googleAuth = new GoogleAuth($client_id, $client_secret, $redirect_uri);

if (!isset($_GET['code'])) {
    echo '<script>window.location = "../../index.php";</script>';
    exit;
}

$tokenData = $googleAuth->getAccessToken($_GET['code']);

if (isset($tokenData['access_token'])) {

    $profile = $googleAuth->getUserProfile($tokenData['access_token']);

    echo '<pre>';
    print_r($profile);
    echo '</pre>';
} else {
    echo "Failed to get access token.";
    echo '<pre>';
    print_r($tokenData);
    echo '</pre>';
}


// if (isset($token_response['access_token'])) {
//     $profile_json = getGoogleProfile($token_response['access_token']);
//     $profile = json_decode($profile_json, true);

//     $google_id = $profile['id'];
//     $google_email = $profile['email'];
//     $stmt_select = $conn->prepare("SELECT m_id, google_id, email, register FROM m_member WHERE google_id = ? OR email = ?");
//     $stmt_select->bind_param("ss", $google_id, $google_email);
//     $stmt_select->execute();
//     $result_select = $stmt_select->get_result();

//     if ($result_select->num_rows > 0) {

//         $row = $result_select->fetch_assoc();
//         if ($google_id == $row['google_id'] || $google_email == $row['email']) {
//             $jwt = generateJWT($row['m_id']); 
//             if($jwt['token']){
//                 $iat = $jwt['data']->iat;
//                 $exp = $jwt['data']->exp;
//                 $_SESSION['member_id'] = $row['m_id'];
//                 $_SESSION['register'] = $row['register'];
//                 $_SESSION['iat'] = $iat;
//                 $_SESSION['exp'] = $exp;
//                 echo '<script language="javascript">window.location = "../app/index";</script>';
//             }else{
//                 echo '<script language="javascript">window.location = "../auth/login";</script>';
//             }
//         }

//     } else {
//         // $register = 'N';
//         // $stmt_insert = $conn->prepare("INSERT INTO m_member (date_signup, google_id, email, register) VALUES (NOW(), ?, ?, ?)");
//         // $stmt_insert->bind_param("sss", $google_id, $register);
//         // $success = $stmt_insert->execute();
//         // $last_id = $conn->insert_id;

//         // if(!empty($last_id)){
//         //     $jwt = generateJWT($last_id); 
//         //     if($jwt['token']){

//         //         $iat = $jwt['data']->iat;
//         //         $exp = $jwt['data']->exp;

//         //         $_SESSION['member_id'] = $last_id;
//         //         $_SESSION['register'] = $register;
//         //         $_SESSION['iat'] = $iat;
//         //         $_SESSION['exp'] = $exp;

//         //         echo '<script language="javascript">window.location = "../app/index";</script>';
//         //     }else{
//         //         echo '<script language="javascript">window.location = "../auth/login";</script>';
//         //     }
//         // }

//         // $stmt_insert->close();
//     }

//     $stmt_select->close();
//     $conn->close();

// } else {
//     echo "Failed to get access token";
// }


?>
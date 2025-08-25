<?php
require_once '../../server/connect_sqli.php';
$client_id = $_ENV['FACEBOOK_CLIENT_ID'];
$client_secret = $_ENV['FACEBOOK_CLIENT_SECRET'];
$redirect_uri = $_ENV['FACEBOOK_REDIRECT_URI'];

/* ----------------------------- */
/*           FUNCTIONS           */
/* ----------------------------- */

class FacebookAuth {
    private $client_id;
    private $client_secret;
    private $redirect_uri;

    public function __construct($client_id, $client_secret, $redirect_uri) {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->redirect_uri = $redirect_uri;
    }

    public function getAccessToken($code) {
        $token_url = "https://graph.facebook.com/v18.0/oauth/access_token?" . http_build_query([
            'client_id' => $this->client_id,
            'redirect_uri' => $this->redirect_uri,
            'client_secret' => $this->client_secret,
            'code' => $code,
        ]);

        $response = file_get_contents($token_url);
        $data = json_decode($response, true);

        return $data['access_token'] ?? null;
    }

    public function getUserProfile($access_token) {
        $profile_url = "https://graph.facebook.com/me?fields=id,name,email,picture&access_token=" . $access_token;
        $response = file_get_contents($profile_url);
        return json_decode($response, true);
    }
}

// Check if 'code' exists
if (!isset($_GET['code'])) {
    echo '<script>window.location = "../../index.php";</script>';
    exit;
}

$fbAuth = new FacebookAuth($client_id, $client_secret, $redirect_uri);
$access_token = $fbAuth->getAccessToken($_GET['code']);

if ($access_token) {
    $profile = $fbAuth->getUserProfile($access_token);
    echo '<pre>';
    print_r($profile);
    echo '</pre>';
} else {
    echo 'Failed to get access token.';
}


// if (!empty($access_token)) {
//     $profile = getFacebookProfile($access_token);

//     $facebook_id = $profile['id'];
//     $stmt_select = $conn->prepare("SELECT m_id, facebook_id, register FROM m_member WHERE facebook_id = ?");
//     $stmt_select->bind_param("s", $facebook_id);
//     $stmt_select->execute();
//     $result_select = $stmt_select->get_result();

//     if ($result_select->num_rows > 0) {

//         $row = $result_select->fetch_assoc();
//         if ($facebook_id == $row['facebook_id']) {

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
  
//         $register = 'N';
//         $stmt_insert = $conn->prepare("INSERT INTO m_member (date_signup, facebook_id, register) VALUES (NOW(), ?, ?)");
//         $stmt_insert->bind_param("ss", $facebook_id, $register);
//         $success = $stmt_insert->execute();
//         $last_id = $conn->insert_id;

//         if(!empty($last_id)){
//             $jwt = generateJWT($last_id); 
//             if($jwt['token']){
//                 $iat = $jwt['data']->iat;
//                 $exp = $jwt['data']->exp;
//                 $_SESSION['member_id'] = $last_id;
//                 $_SESSION['register'] = $register;
//                 $_SESSION['iat'] = $iat;
//                 $_SESSION['exp'] = $exp;
//                 echo '<script language="javascript">window.location = "../app/index";</script>';
//             }else{
//                 echo '<script language="javascript">window.location = "../auth/login";</script>';
//             }
//         }

//         $stmt_insert->close();  
//     }

//     $stmt_select->close();
//     $conn->close();

// } else {
//     echo "Failed to get access token";
// }






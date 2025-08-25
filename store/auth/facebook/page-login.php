<?php
include_once '../lib/connect_sqli.php';
$client_id = $_ENV['FACEBOOK_BN_CLIENT_ID'];
$client_secret = $_ENV['FACEBOOK_BN_CLIENT_SECRET'];
$redirect_uri = $_ENV['FACEBOOK_BN_REDIRECT_URI'];

/* ----------------------------- */
/*           FUNCTIONS           */
/* ----------------------------- */

function getFacebookAccessToken($client_id, $client_secret, $redirect_uri, $code) {
    $token_url = "https://graph.facebook.com/v18.0/oauth/access_token?" . http_build_query([
        'client_id' => $client_id,
        'redirect_uri' => $redirect_uri,
        'client_secret' => $client_secret,
        'code' => $code,
    ]);

    $response = file_get_contents($token_url);
    $data = json_decode($response, true);

    return $data['access_token'] ?? null;
}

// function getFacebookProfile($access_token) {
//     $profile_url = "https://graph.facebook.com/me?fields=id,name,email,picture&access_token=" . $access_token;
//     $response = file_get_contents($profile_url);
//     return json_decode($response, true);
// }

// function getFacebookPermissions($access_token){
//     $permissions_url = "https://graph.facebook.com/me/permissions?access_token=" . $access_token;
//     $response = file_get_contents($permissions_url);
//     return json_decode($response, true);
// }

function getFacebookPageShowList($access_token){
    $page_lis_url = "https://graph.facebook.com/me/accounts?access_token=" . $access_token;
    $response = file_get_contents($page_lis_url);
    return json_decode($response, true);
}

function getPageMessaging($page_id, $page_token){
    $url = "https://graph.facebook.com/{$page_id}/conversations?fields=id,senders,updated_time&access_token={$page_token}";
    $response = file_get_contents($url);
    $conversations = json_decode($response, true);
    return $conversations;
}

function getMessagesInConversation($conversation_id, $page_token) {
    $url = "https://graph.facebook.com/{$conversation_id}/messages?fields=message,from,created_time&access_token={$page_token}";
    $response = file_get_contents($url);
    return json_decode($response, true);
}

if (!isset($_GET['code'])) {
    echo '<script language="javascript">window.location = "../auth/login";</script>';
    exit;
}

$code = $_GET['code'];
$access_token = getFacebookAccessToken($client_id, $client_secret, $redirect_uri, $code);
// $profile = getFacebookProfile($access_token);
// $permissions = getFacebookPermissions($access_token);
$page_show_list = getFacebookPageShowList($access_token);
$page_id = $page_show_list['data'][0]['id'];
$page_token = $page_show_list['data'][0]['access_token'];

$page_mes = getPageMessaging($page_id, $page_token);
$conversation_id = $page_mes['data'][0]['id'];

$data_mes = getMessagesInConversation($conversation_id, $page_token);

echo '<pre>';
// print_r($_GET);
// print_r($access_token);
// print_r($permissions);
// print_r($page_show_list);
print_r($page_mes);
print_r($data_mes);
echo '</pre>';
exit;






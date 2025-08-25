<?php
include_once '../lib/connect_sqli.php';
$client_id = $_ENV['GOOGLE_CLIENT_ID'];
$client_secret = $_ENV['GOOGLE_CLIENT_SECRET'];
$redirect_uri = $_ENV['GOOGLE_REDIRECT_URI'];

/* ----------------------------- */
/*           FUNCTIONS           */
/* ----------------------------- */

// echo '<pre>';
// print_r($_GET);
// // print_r($_POST);
// echo '</pre>';
// exit;

function getGoogleToken($code) {
    global $client_id, $client_secret, $redirect_uri;
    $params = http_build_query([
        'code' => $code,
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'redirect_uri' => $redirect_uri,
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
        return json_encode(['error' => true, 'message' => $err]);
    }

    return $response;
}

function getGoogleProfile($access_token) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://www.googleapis.com/oauth2/v2/userinfo", 
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

function getAllDriveFoldersAndFiles(string $access_token): array {
    $allItems = [];
    $pageToken = null;

    do {
        $params = [
            'pageSize' => 1000,
            'fields' => 'nextPageToken, files(id,name,mimeType,parents)',
            'q' => "trashed = false",
            'supportsAllDrives' => 'true',
            'includeItemsFromAllDrives' => 'true',
        ];
        if ($pageToken) {
            $params['pageToken'] = $pageToken;
        }

        $url = 'https://www.googleapis.com/drive/v3/files?' . http_build_query($params);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token,
                'Accept: application/json',
            ],
            CURLOPT_SSL_VERIFYPEER => false, 
        ]);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        $pageToken = $data['nextPageToken'] ?? null;
        $allItems = array_merge($allItems, $data['files'] ?? []);
    } while ($pageToken);

    return $allItems;
}

// function getGoogleDrive($access_token) {
//     $curl = curl_init();
//     curl_setopt_array($curl, array(
//         CURLOPT_URL => "https://www.googleapis.com/drive/v3/about", 
//         CURLOPT_RETURNTRANSFER => true,
//         CURLOPT_CUSTOMREQUEST => "GET",
//         CURLOPT_HTTPHEADER => [
//             "Authorization: Bearer " . $access_token
//         ],
//         CURLOPT_SSL_VERIFYPEER => false 
//     ));

//     $response = curl_exec($curl);
//     curl_close($curl);

//     return $response;
// }

if (!isset($_GET['code'])) {
    echo '<script language="javascript">window.location = "../auth/login";</script>';
    exit;
}

$token_response = json_decode(getGoogleToken($_GET['code']), true);
// $file_drive = getGoogleDrive($token_response['access_token']);
$items = getAllDriveFoldersAndFiles($token_response['access_token']);

echo '<pre>';
print_r($items);
// print_r($_POST);
echo '</pre>';
exit;



if (isset($token_response['access_token'])) {
    $profile_json = getGoogleProfile($token_response['access_token']);
    $profile = json_decode($profile_json, true);

    $google_id = $profile['id'];
    $google_email = $profile['email'];
    $stmt_select = $conn->prepare("SELECT m_id, google_id, email, register FROM m_member WHERE google_id = ? OR email = ?");
    $stmt_select->bind_param("ss", $google_id, $google_email);
    $stmt_select->execute();
    $result_select = $stmt_select->get_result();

    if ($result_select->num_rows > 0) {

        $row = $result_select->fetch_assoc();
        if ($google_id == $row['google_id'] || $google_email == $row['email']) {
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
  
        // $register = 'N';
        // $stmt_insert = $conn->prepare("INSERT INTO m_member (date_signup, google_id, email, register) VALUES (NOW(), ?, ?, ?)");
        // $stmt_insert->bind_param("sss", $google_id, $register);
        // $success = $stmt_insert->execute();
        // $last_id = $conn->insert_id;

        // if(!empty($last_id)){
        //     $jwt = generateJWT($last_id); 
        //     if($jwt['token']){

        //         $iat = $jwt['data']->iat;
        //         $exp = $jwt['data']->exp;

        //         $_SESSION['member_id'] = $last_id;
        //         $_SESSION['register'] = $register;
        //         $_SESSION['iat'] = $iat;
        //         $_SESSION['exp'] = $exp;

        //         echo '<script language="javascript">window.location = "../app/index";</script>';
        //     }else{
        //         echo '<script language="javascript">window.location = "../auth/login";</script>';
        //     }
        // }

        // $stmt_insert->close();
    }

    $stmt_select->close();
    $conn->close();

} else {
    echo "Failed to get access token";
}


?>
<?php
require_once '../../server/connect_sqli.php';
require_once '../../cookie/cookie_utils.php';
require_once '../../server/select_sqli.php';
require_once '../../server/insert_sqli.php';
require_once '../../server/update_sqli.php';
$client_id = $_ENV['FACEBOOK_CLIENT_ID'];
$client_secret = $_ENV['FACEBOOK_CLIENT_SECRET'];
$redirect_uri = $_ENV['FACEBOOK_REDIRECT_URI'];

if (isset($_SESSION['user_timezone'])) {
    date_default_timezone_set($_SESSION['user_timezone']);
} else {
    date_default_timezone_set("UTC");
}
$timeZone = isset($_SESSION['user_timezone']) ? $_SESSION['user_timezone'] : '';
$dateNow = date('Y-m-d H:i:s');

/* ----------------------------- */
/*           FUNCTIONS           */
/* ----------------------------- */

class FacebookAuth
{
    private $client_id;
    private $client_secret;
    private $redirect_uri;

    public function __construct($client_id, $client_secret, $redirect_uri)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->redirect_uri = $redirect_uri;
    }

    public function getAccessToken($code)
    {
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

    public function getUserProfile($access_token)
    {
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



try {

    if (!empty($access_token)) {
        $profile = $fbAuth->getUserProfile($access_token);
        if (!$profile) {
            throw new Exception("Failed to retrieve Facebook user profile.");
        }
        $conditions = [
            [
                'column' => 'facebook_id',
                'operator' => '=',
                'value' => $profile['id']
            ]
        ];
        $facebookItems = selectData($conn_cloudpanel, 'ecm_member', $conditions, 'facebook_id');
        if ($facebookItems === false) {
            throw new Exception("Database error: Failed to select data from ecm_member.");
        }
        if (empty($facebookItems)) {
            $ins_facebook = [
                'facebook_id' => $profile['id'],
                'facebook_email' => $profile['email'],
                'facebook_name' => $profile['name'],
                'facebook_pic' => $profile['picture']['data']['url'],
                'timezone' => $timeZone,
                'created_at' => $dateNow
            ];
            $insertFbResult = insertData($conn_cloudpanel, 'ecm_member_facebook', $ins_facebook);
            if ($insertFbResult === false) {
                throw new Exception("Database error: Failed to insert data into ecm_member_facebook.");
            }
            $ins_member = [
                'facebook_id' => $profile['id'],
                'accept_policy' => 1,
                'timezone' => $timeZone,
                'created_at' => $dateNow
            ];
            $ins_id = insertDataAndGetId($conn_cloudpanel, 'ecm_member', $ins_member);
            if (!$ins_id) {
                throw new Exception("Database error: Failed to insert data into ecm_member.");
            }
            $userId = (int) $ins_id;
            $jwtData = generateJWT($userId);
            $cookiePrefs = getCookieSettings();
            setAutoCookie($cookiePrefs, $jwtData);
            $_SESSION['user'] = [
                'id' => $userId,
                'username' => 'admin',
                'role' => 'user'
            ];
            echo '<script language="javascript">window.location = "../../user/";</script>';
        } else {
            $conditions = [
                [
                    'column' => 'facebook_id',
                    'operator' => '=',
                    'value' => $facebookItems[0]['facebook_id']
                ]
            ];
            $facebookItems = selectData($conn_cloudpanel, 'ecm_member', $conditions, '*');
            if ($facebookItems === false || empty($facebookItems)) {
                throw new Exception("Database error: Failed to retrieve member data.");
            }
            $userId = isset($facebookItems[0]['member_id']) ? (int) $facebookItems[0]['member_id'] : 0;
            if ($userId === 0) {
                throw new Exception("Invalid user ID retrieved.");
            }
            $jwtData = generateJWT($userId);
            $cookiePrefs = getCookieSettings();
            setAutoCookie($cookiePrefs, $jwtData);
            $_SESSION['user'] = [
                'id' => $userId,
                'username' => 'admin',
                'role' => 'user'
            ];
            echo '<script language="javascript">window.location = "../../user/";</script>';
        }

    } else {
        echo 'Failed to get access token.';
        echo '<pre>';
        print_r($access_token);
        echo '</pre>';
    }

} catch (Exception $e) {
    error_log("Error during Facebook login: " . $e->getMessage());
    echo '<script>alert("เกิดข้อผิดพลาดในการเข้าสู่ระบบ กรุณาลองใหม่อีกครั้ง."); window.location = "../../login/";</script>';
} finally {
    $conn_cloudpanel->close();
    exit;
}

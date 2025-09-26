<?php
require_once '../../server/connect_sqli.php';
require_once '../../cookie/cookie_utils.php';
require_once '../../server/select_sqli.php';
require_once '../../server/insert_sqli.php';
require_once '../../server/update_sqli.php';
$client_id = $_ENV['GOOGLE_CLIENT_ID'];
$client_secret = $_ENV['GOOGLE_CLIENT_SECRET'];
$redirect_uri = $_ENV['GOOGLE_REDIRECT_URI'];

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

class GoogleAuth
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

    public function getUserProfile($access_token)
    {
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

try {
    
    if (isset($tokenData['access_token'])) {
        $profile = $googleAuth->getUserProfile($tokenData['access_token']);
        if (!$profile) {
            throw new Exception("Failed to retrieve Google user profile.");
        }
        $conditions = [
            [
                'column' => 'google_id',
                'operator' => '=',
                'value' => $profile['id']
            ]
        ];
        $googleItems = selectData($conn_cloudpanel, 'ecm_member', $conditions, 'google_id');
        if ($googleItems === false) {
            throw new Exception("Database error: Failed to select data from ecm_member.");
        }
        if (empty($googleItems)) {
            $ins_google = [
                'google_id' => $profile['id'],
                'google_email' => $profile['email'],
                'google_name' => $profile['name'],
                'google_pic' => $profile['picture'],
                'timezone' => $timeZone,
                'created_at' => $dateNow
            ];
            $insertGoogleResult = insertData($conn_cloudpanel, 'ecm_member_google', $ins_google);
            if ($insertGoogleResult === false) {
                throw new Exception("Database error: Failed to insert data into ecm_member_google.");
            }
            $ins_member = [
                'google_id' => $profile['id'],
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
                    'column' => 'google_id',
                    'operator' => '=',
                    'value' => $googleItems[0]['google_id']
                ]
            ];
            $googleItems = selectData($conn_cloudpanel, 'ecm_member', $conditions, '*');
            if ($googleItems === false || empty($googleItems)) {
                throw new Exception("Database error: Failed to retrieve member data.");
            }
            $userId = isset($googleItems[0]['member_id']) ? (int) $googleItems[0]['member_id'] : 0;
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
        echo "Failed to get access token.";
        echo '<pre>';
        print_r($tokenData);
        echo '</pre>';
    }

} catch (Exception $e) {
    // Log the error message
    error_log("Error during Google login: " . $e->getMessage());
    echo '<script>alert("An error occurred during login. Please try again later."); window.location = "../../login/";</script>';
} finally {
    $conn_cloudpanel->close();
    exit;
}

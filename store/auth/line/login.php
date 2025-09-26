<?php
require_once '../../server/connect_sqli.php';
require_once '../../cookie/cookie_utils.php';
require_once '../../server/select_sqli.php';
require_once '../../server/insert_sqli.php';
require_once '../../server/update_sqli.php';
$client_id = $_ENV['LINE_CLIENT_ID'];
$client_secret = $_ENV['LINE_CLIENT_SECRET'];
$redirect_uri = $_ENV['LINE_REDIRECT_URI'];

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

class LineAuth
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
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'redirect_uri' => $this->redirect_uri
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
            return ['error' => true, 'message' => $err];
        }

        return json_decode($response, true);
    }

    public function getUserProfile($access_token)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.line.me/v2/profile",
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

if (!isset($_GET['code'])) {
    echo '<script language="javascript">window.location = "../../index.php";</script>';
    exit;
}

$line = new LineAuth($client_id, $client_secret, $redirect_uri);
$token_response = $line->getAccessToken($_GET['code']);

try {

    if (isset($token_response['access_token'])) {
        $profile = $line->getUserProfile($token_response['access_token']);
        if (!$profile) {
            throw new Exception("Failed to retrieve LINE user profile.");
        }
        $conditions = [
            [
                'column' => 'line_id',
                'operator' => '=',
                'value' => $profile['userId']
            ]
        ];
        $lineItems = selectData($conn_cloudpanel, 'ecm_member', $conditions, 'line_id');
        if ($lineItems === false) {
            throw new Exception("Database error: Failed to select data from ecm_member.");
        }
        if (empty($lineItems)) {
            $ins_line = [
                'line_id' => $profile['userId'],
                'line_name' => $profile['displayName'],
                'line_pic' => $profile['pictureUrl'],
                'timezone' => $timeZone,
                'created_at' => $dateNow
            ];
            $insertLineResult = insertData($conn_cloudpanel, 'ecm_member_line', $ins_line);
            if ($insertLineResult === false) {
                throw new Exception("Database error: Failed to insert data into ecm_member_line.");
            }
            $ins_member = [
                'line_id' => $profile['userId'],
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
                    'column' => 'line_id',
                    'operator' => '=',
                    'value' => $lineItems[0]['line_id']
                ]
            ];
            $lineItems = selectData($conn_cloudpanel, 'ecm_member', $conditions, '*');
            if ($lineItems === false || empty($lineItems)) {
                throw new Exception("Database error: Failed to retrieve member data.");
            }
            $userId = isset($lineItems[0]['member_id']) ? (int) $lineItems[0]['member_id'] : 0;
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

        echo '<pre>';
        print_r($token_response);
        echo '</pre>';
    }

} catch (Exception $e) {
    // บันทึกข้อผิดพลาดลง log
    error_log("Error during LINE login: " . $e->getMessage());
    echo '<script>alert("เกิดข้อผิดพลาดในการเข้าสู่ระบบ กรุณาลองใหม่อีกครั้ง."); window.location = "../../login/";</script>';
} finally {
    $conn_cloudpanel->close();
    exit;
}

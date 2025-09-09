<?php
@session_start();
require_once __DIR__ . '/cookie/cookie_utils.php';
if (isset($_SESSION['user_timezone'])) {
    date_default_timezone_set($_SESSION['user_timezone']);
} else {
    date_default_timezone_set("UTC");
}
$timeZone = isset($_SESSION['user_timezone']) ? $_SESSION['user_timezone'] : '';
$dateNow = date('Y-m-d H:i:s');

function getBasePath() {

    /*==== FILE SETUP BASE PATH =======
    parseRoute
    routes.php
    inc-cdn.php
    server/connect_sqli.php
    logout.php
    ==================================*/

    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
    $scheme = $isHttps ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $path = $isHttps ? '/store/' : '/trandar_website/store/';
    $base_path = $scheme . '://' . $host . $path;
    return $base_path;
}

function getRelativePath() {
    $base = getBasePath();
    $uri = $_SERVER['REQUEST_URI'];
    if (strpos($uri, $base) === 0) {
        return substr($uri, strlen($base));
    }
    return $uri;
}

function parseRoute($RELATIVEPath) {
    $path = trim($RELATIVEPath, '/');
    $segments = explode('/', $path);
    return [
        'controller' => $segments ?? []
    ];
}

function buildUrl($path = '', $query = []) {
    $base = getBasePath();
    $url = rtrim($base, '/') . '/' . ltrim($path, '/');
    if (!empty($query)) {
        $url .= '?' . http_build_query($query);
    }
    return $url;
}

function requireLogin() {
    if (!isset($_SESSION['user'])) {
        $base = getBasePath();
        header("Location: " . $base);
        exit;
    }
}

function requireRole($roles = []) {
    requireLogin();
    $userRole = $_SESSION['user']['role'] ?? null;
    if (!in_array($userRole, $roles)) {
        show403();
    }
}

function show403() {
    http_response_code(403);
    echo "<h1>403 Forbidden</h1>";
    echo "<p>You do not have permission to access this page.</p>";
    exit;
}

function redirectTo($path) {
    header("Location: " . buildUrl($path));
    exit;
}

// ===== Main Routing ================
$RELATIVE = getRelativePath();
$ROUTE = parseRoute($RELATIVE);
$GLOBALS['BASE_WEB'] = getBasePath();


// ===== Role-based Access =======  //
//  $Key ใน $access เท่ากับ Folder    //
//  และ [user, admin] คือ Array Role //
//================================  //

$accessControl = [
    'admin' => ['user', 'admin'],
    'user' => ['user', 'admin'],
    'app' => ['user', 'admin'],
    'payment' => ['user', 'admin'],
    // 'control_link' => ['user', 'admin'],
];

foreach ($ROUTE['controller'] as $value) {
    if (array_key_exists($value, $accessControl)) {
        $allowedRoles = $accessControl[$value];
        requireRole($allowedRoles);
    }
}

// echo $sessionId = setVisitorSession() ."<br>";
// echo $page = $_SERVER['REQUEST_URI']."<br>";
// echo $ref = $_SERVER['HTTP_REFERER'] ?? null."<br>";
// echo $ua = $_SERVER['HTTP_USER_AGENT']."<br>";
// echo $time = $dateNow."<br>";

echo '
<script>
//=============================================================//
//                    Developed by: THE DEVELOPER              //
//                 Name: Kittinanthanatch Seekaewnamsai        //
//                     Phone: 083-894-5256 (FEI)               //
//=============================================================//
var pathConfig = {
    BASE_WEB: ' . json_encode($GLOBALS['BASE_WEB']) . ',
    LINE_REDIRECT: ' . json_encode("http://localhost:3000/trandar_website/store/auth/line/login.php") . ',
    FACE_BOOK_REDIRECT: ' . json_encode("http://localhost:3000/trandar_website/store/auth/facebook/login.php") . ',
    GOOGLE_REDIRECT: ' . json_encode("http://localhost:3000/trandar_website/store/auth/google/login.php") . '
};
</script>
';



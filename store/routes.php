<?php
@session_start();
require_once __DIR__ . '/cookie/cookie_utils.php';

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

    $last = "";
    foreach ($segments as $segmen) {
        switch ($segmen) {
            case 'user':
                $last = $segmen;
                break;
            case 'payment':
                $last = $segmen;
                break;
            case 'admin':
                $last = $segmen;
                break;
            default:
                $last = "";
                break;
        }
    }
    return [
        'controller' => $last ?? ''
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

// ===== Role-based Access =====
$accessControl = [
    'admin'     => ['user', 'admin'],
    'user'      => ['user', 'admin'],
    'app'       => ['user', 'admin'],
    'payment'   => ['user', 'admin'],
    // 'protect'   => ['user']
];

// ===== Main Routing =====
$RELATIVE = getRelativePath();
$ROUTE = parseRoute($RELATIVE);
$GLOBALS['BASE_WEB'] = getBasePath();

// echo '<pre>';
// print_r($ROUTE);
// echo '</pre>';

// ===== Verify access rights accordingly role =====
if (array_key_exists($ROUTE['controller'], $accessControl)) {
    $allowedRoles = $accessControl[$ROUTE['controller']];
    requireRole($allowedRoles);

switch ($ROUTE['controller']) {
    case 'app':
        break;
    case 'admin':

echo "
<script>
    var pathConfig = {
        BASE_WEB: " . json_encode($BASE_WEB) . "
    };
</script>
";

        break;
    case 'user':
    case 'payment':

echo "
<script>
    var pathConfig = {
        BASE_WEB: " . json_encode($BASE_WEB) . "
    };
</script>
";

        break;
    default:
echo '
<script>alert("เกิดข้อผิดพลาดในการเข้าสู่ระบบ กรุณาลองใหม่อีกครั้ง.");</script>
';
        break;
}

}else{

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

}


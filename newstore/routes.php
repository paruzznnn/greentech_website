<?php
@session_start();
require_once __DIR__.'/cookie/cookie_utils.php';

function getBasePath() {
    return dirname($_SERVER['SCRIPT_NAME']) .'/';
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
        'controller' => $segments[0] ?? '', //folder
        'action'     => $segments[1] ?? 'index', //file
        'params'     => array_slice($segments, 2),
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
        header("Location: " . buildUrl('login'));
        exit;
    }
}

function redirectTo($path) {
    header("Location: " . buildUrl($path));
    exit;
}

$RELATIVE = getRelativePath();
$ROUTE = parseRoute($RELATIVE);

if (in_array($ROUTE['controller'], ['dashboard', 'admin', 'user', 'app'])) {
    requireLogin();
}

switch ($ROUTE['controller']) {
    case 'app':
        break;
    case 'dashboard':
        break;
    case 'admin':
        break;
    case 'user':
        break;
    default:

        if (checkAutoLoginCookie() !== false) {
            // echo "Welcome back, user ID: " . checkAutoLoginCookie();
        } else {
            // session_destroy();
            // echo "Please login.";
        }
        $GLOBALS['BASE_WEB'] = getBasePath(); 

        echo "<script>
            var pathConfig = {
                BASE_WEB: " . json_encode($BASE_WEB) . "
            };
        </script>";

        break;
}

?>

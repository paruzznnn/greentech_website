<?php
session_start();

function deleteCookie($cookieName) {
    setcookie($cookieName, '', time() - 3600, '/');
}

// deleteCookie('cart');
// deleteCookie('orderArray');
// deleteCookie('compare');
deleteCookie('cookie_consent');

session_unset();
session_destroy();

header("Location: ../index.php");
exit();
?>
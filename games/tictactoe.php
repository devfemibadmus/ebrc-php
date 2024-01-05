<?php

if(!isset($_COOKIE['token'])){
    // Generate CSRF token
    $_SESSION['token'] = bin2hex(random_bytes(32));
    setcookie('token', $_SESSION['token'], time() + (86400 * 30), "/");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_COOKIE['token'])) {
        $receivedToken = $_POST['token'];
        if ($receivedToken === $_COOKIE['token']) {
        echo "Form submission is valid";
        // Generate CSRF token
        $_SESSION['token'] = bin2hex(random_bytes(32));
        setcookie('token', $_SESSION['token'], time() + (86400 * 30), "/");
        } else {
        echo "Form submission is invalid";
        }
    }
    exit;
}
// render index.html
include 'website/game/tictactoe.html';
?>
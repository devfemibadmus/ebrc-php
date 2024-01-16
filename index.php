<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");

$path = $_GET['path'] ?? '';
$root = __DIR__ . '/website/';
$file = $root . $path;

if (strpos($path, 'static/') === 0 && file_exists($file)) {
    if (strpos($path, '.css') !== false) {
        header('Content-Type: text/css');
    } elseif (strpos($path, '.js') !== false) {
        header('Content-Type: application/javascript');
    } elseif (strpos($path, '.jpg') !== false) {
        header('Content-Type: image/jpeg');
    } elseif (strpos($path, '.svg') !== false) {
        header('Content-Type: image/svg+xml');
    } elseif (strpos($path, '.ttf') !== false) {
        header('Content-Type: font/ttf');
    } elseif (strpos($path, '.woff') !== false) {
        header('Content-Type: font/woff');
    } elseif (strpos($path, '.woff2') !== false) {
        header('Content-Type: font/woff2');
    } elseif (strpos($path, '.eot') !== false) {
        header('Content-Type: application/vnd.ms-fontobject');
    }

    readfile($file);
    exit;
}
$path = rtrim($path, '/');
$path_components = explode('/', $path);
$last_path_component = end($path_components);

switch ($path) {
    case 'bank':
        include 'account/bank.php';
        break;
    case 'reward':
        include 'account/reward.php';
        break;
    case 'signin':
        include 'account/signin.php';
        break;
    case 'signup':
        include 'account/signup.php';
        break;
    case 'notification':
        include 'account/notification.php';
        break;
    case 'username':
        include 'account/username.php';
        break;

        
    case 'cashout':
        include 'cashout/cashout.php';
        break;

        
    case 'users':
        include 'admin/users.php';
        break;
    case 'devfemibadmus':
        include 'admin/admin.php';
        break;

    default:
        include 'website/z.html';
        break;
}

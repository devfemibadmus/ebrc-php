<?php
require_once 'config/header.php';
include_once 'config/database.php';
include_once 'objects/account.php';
include_once 'objects/notifications.php';

$database = new Database();
$db = $database->getConnection();

$account = new Account($db);
$notifications = new Notifications($db);

$_POST = json_decode(file_get_contents("php://input"), true);

if(empty($_POST['username']) || empty($_POST['password'])){
    $response = array(
        "status" => "error",
        "message" => "username and password are required"
    );
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode($response, JSON_NUMERIC_CHECK);
    exit;
}

$account->username = $_POST['username'];
$account->password = $_POST['password'];

$stmt = $account->signin();

if($stmt != false){
    $list =  $notifications->listNotifications($stmt['id']);
    $response = array(
        "status" => "success",
        "message" => "Sign in successful",
        "account" => $stmt,
        "notifications" => $list
    );
} else {
    $response = array(
        "status" => "error",
        "message" => $stmt
    );
}
header("Content-Type: application/json");
echo json_encode($response, JSON_NUMERIC_CHECK);
?>
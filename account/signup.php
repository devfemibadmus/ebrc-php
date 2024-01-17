<?php
require_once 'config/header.php';
include_once 'config/database.php';
include_once 'objects/account.php';
include_once 'objects/notifications.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
$database = new Database();
$db = $database->getConnection();

$account = new Account($db);
$notifications = new Notifications($db);

$_POST = json_decode(file_get_contents("php://input"), true);

if(empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])){
    $response = array(
        "status" => "error",
        "message" => "username, email and password are required"
    );
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode($response, JSON_NUMERIC_CHECK);
    exit;
}
$username = $_POST['username'];
$password = $_POST['password'];
$email = $_POST['email'];

$account->username = $username;
$account->password = $password;
$account->email = $email;

// check if the username is already taken
$username_exists = $account->getUser();
if($username_exists){
    $response = array(
        "status" => "error",
        "message" => "username already taken"
    );
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode($response, JSON_NUMERIC_CHECK);
    exit;
}
if(isset($_POST['referral'])){
    $account->referral = $_POST['referral'];
} else {
    $account->referral = "account1";
}

if($account->signup()){
    $account = new Account($db);
    $account->username = $username;
    $account->password = $password;
    $stmt = $account->signin();
    if($stmt != false){
        $date = new DateTime();
        $formatted_date = $date->format("Y-m-d H:i:s");
        $notifications->accountId = $stmt['id'];
        $notifications->referral = '';
        $notifications->amount = 0;
        $notifications->date = $formatted_date;
        $notifications->type = 'account';
        $notifications->comment = 'Account created successfully, @'.$stmt['referral'].' referral you.';
        $notifications->createNotifications();

        if($stmt['referral'] != 'nobody'){
            $account->username = $stmt['referral'];
            $referral = $account->getUser();
            $notifications->referral = $username;
            $notifications->accountId = (int)$referral['id'];
            $notifications->comment = '@'.$username.' use you as a referral code';
            $notifications->createNotifications();
        }

        $list =  $notifications->listNotifications((int)$stmt['id']);
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
} else {
    $response = array(
        "status" => "error",
        "message" => "Signup failed"
    );
}
header("Content-Type: application/json");
echo json_encode($response, JSON_NUMERIC_CHECK);
?>

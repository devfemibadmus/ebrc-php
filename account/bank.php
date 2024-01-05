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

if(empty($_POST['username']) || empty($_POST['password']) || empty($_POST['password']) || empty($_POST['password']) || empty($_POST['password'])){
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
if($stmt != false && $stmt['account_editable']){
    // Update account information
    $id = $stmt['id'];
    $account_name = $stmt['account_name'];
    $account_uname = $stmt['account_uname'];
    $account_number = $stmt['account_number'];
    $date = new DateTime();
    $formatted_date = $date->format("Y-m-d H:i:s");

    if($stmt['account_editable'] == true && isset($_POST['account_name']) && isset($_POST['account_uname']) && isset($_POST['account_number'])){
        $account_name = $_POST['account_name'];
        $account_uname =  $_POST['account_uname'];
        $account_number = $_POST['account_number'];
        $account->account_editable = 0;
    }
    
    $account->id = $id;
    $account->account_name = $account_name;
    $account->account_uname = $account_uname;
    $account->account_number = $account_number;

    $notifications->account_id = $id;
    $notifications->amount = 0;
    $notifications->referral = '';
    $notifications->date = $formatted_date;
    $notifications->type = 'Bank Info Update';
    $notifications->comment = 'You update your Bank account details. '.$account_uname.' '.$account_number.' '.$account_name;
    
    if($account->bank() && $notifications->createNotifications()){
        $response = array(
            "status" => 'success',
            "message" => "Account updated successfully",
        );
    } else {
        $response = array(
            "status" => "error",
            "message" => "Error updating account"
        );
    }
} else {
    $response = array(
        "status" => "error",
        "message" => "Incorrect username or password"
    );
}
header("Content-Type: application/json");
echo json_encode($response, JSON_NUMERIC_CHECK);

?>
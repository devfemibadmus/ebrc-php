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
if($stmt != false && $stmt['accountEditable']){
    // Update account information
    $id = $stmt['id'];
    $accountName = $stmt['accountName'];
    $accountUname = $stmt['accountUname'];
    $accountNumber = $stmt['accountNumber'];
    $date = new DateTime();
    $formatted_date = $date->format("Y-m-d H:i:s");

    if($stmt['accountEditable'] == true && isset($_POST['accountName']) && isset($_POST['accountUname']) && isset($_POST['accountNumber'])){
        $accountName = $_POST['accountName'];
        $accountUname =  $_POST['accountUname'];
        $accountNumber = $_POST['accountNumber'];
        $account->accountEditable = 0;
    }
    
    $account->id = $id;
    $account->accountName = $accountName;
    $account->accountUname = $accountUname;
    $account->accountNumber = $accountNumber;

    $notifications->account_id = $id;
    $notifications->amount = 0;
    $notifications->referral = '';
    $notifications->date = $formatted_date;
    $notifications->type = 'Bank Info Update';
    $notifications->comment = 'You update your Bank account details. '.$accountUname.' '.$accountNumber.' '.$accountName;
    
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
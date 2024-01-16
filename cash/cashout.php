<?php
include_once 'config/database.php';
include_once 'objects/account.php';
include_once 'objects/notifications.php';

$database = new Database();
$db = $database->getConnection();

$account = new Account($db);
$notifications = new Notifications($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(empty($_POST['username']) || empty($_POST['password']) || empty($_POST['amount'])){
        $html = file_get_contents('../website/cashout.html');
        echo 'HH';
        exit;
    }
}else{
    include 'website/cashout.html';
    exit;
}

$username = $_POST['username'];
$password = $_POST['password'];
$amount = $_POST['amount'];

$account->username = $username;
$account->password = $password;

$stmt = $account->signin();
$date = new DateTime();
$formatted_date = $date->format("Y-m-d H:i:s");

if($stmt != false){
    if($stmt['account_editable'] == false){
        if($stmt['pending_cashout'] == false){
            if($stmt['account_balance'] >= $amount){
                $account->id = $stmt['id'];
                $account->account_balance = $stmt['account_balance'] - $amount;
                $account->pending_cashout = true;
                $account->cashout_amount = $amount;
                $account->username = $stmt['username'];
                $account->cashout();
                $notifications->account_id = $stmt['id'];
                $notifications->referral = '';
                $notifications->amount = $amount;
                $notifications->date = $formatted_date;
                $notifications->type = 'pending';
                $notifications->comment = 'You have a pending withdraw of '.$amount;
    
                if($notifications->createNotifications()){
                    $response = 'Cashout out successful, your aza will be credited <strong>'.$amount.'</strong> within 24hrs';
                    include 'website/success.html';
                    exit;
                }
                else{
                    $response = 'Cashout error, this is not common contact admin';
                }
            }
            else{
                $response = 'Your balance '.$stmt['account_balance'].' is too low to cashout '.$amount;
            }
        }
        else{
            $response = 'Oops! you have a pending withdraw check your notification or await 24hrs for the payment.';
        }
    }
    else{
        $response = 'Error your bank account is yet to be set';
    }
}
else {
    $response = 'unable to sign you in';
}

include 'website/error.html';
exit;

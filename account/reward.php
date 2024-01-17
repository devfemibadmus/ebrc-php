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

if(empty($_POST['username']) || empty($_POST['password']) || empty($_POST['reward'])){
    $response = array(
        "status" => "error",
        "message" => "username, password and reward are required"
    );
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode($response, JSON_NUMERIC_CHECK);
    exit;
}

$account->username = $_POST['username'];
$account->password = $_POST['password'];
$preward = $_POST['reward'];

$stmt = $account->signin();
if($stmt != false){
    $spam = true;
    
    $list =  $notifications->listNotifications($stmt['id']);

    $mostRecentWithdraw = null;
    foreach ($list as $notification) {
        if ($notification["type"] == "coin") {
            if(is_null($mostRecentWithdraw) || $notification["date"]>$mostRecentWithdraw["date"]){
                $mostRecentWithdraw = $notification;
            }
        }
    }
    
    $date = new DateTime();
    $diff = $date->diff(new DateTime($mostRecentWithdraw["date"]));
    $formatted_date = $date->format("Y-m-d H:i:s");

    
    if($preward >= 300){
        //
        $notifications->amount = 2;
        $account->coinBalance = $stmt['coinBalance'] + 2;
    }
    else{
        //
        $notifications->amount = 1;
        $account->coinBalance = $stmt['coinBalance'] + 1;
    }
    $notifications->comment = 'You watch an Ad and just got a new coin from the ad you watch. time:'.$diff->i;

    $account->id = $stmt['id'];

    $notifications->accountId = $stmt['id'];
    $notifications->referral = '';
    $notifications->date = $formatted_date;
    $notifications->type = 'coin';
    
    if($account->coin() && $notifications->createNotifications()){
        $response = array(
            "status" => 'success',
            "message" =>$notifications->comment,
        );
    } else {
        $response = array(
            "status" => "error",
            "message" => "Error putting in reward"
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
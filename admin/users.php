<?php
require_once 'config/header.php';
include_once 'config/database.php';
include_once 'objects/account.php';
include_once 'objects/notifications.php';

$database = new Database();
$db = $database->getConnection();

$account = new Account($db);

$stmt = $account->print_all_users();
if($stmt != false){
    foreach($stmt as $key => $user) {
        unset($stmt[$key]['password']);
    }
    $response = array(
        "status" => "success",
        "accounts" => $stmt,
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
<?php
require_once 'config/header.php';
include_once 'config/database.php';
include_once 'objects/account.php';

$database = new Database();
$db = $database->getConnection();

$account = new Account($db);

$_POST = json_decode(file_get_contents("php://input"), true);

if(empty($_POST['username'])){
    $response = array(
        "status" => "error",
        "message" => "username is required ",
    );
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode($response);
    exit;
}
// set product property values
$account->username = $_POST['username'];

// check if the username is available
$username_exists = $account->getUser();

if($username_exists){
    $response = array(
        "status" => "success",
        "message" => "username not available",
        "exist" => 'true'
    );
}else{
    $response = array(
        "status" => "error",
        "message" => "username available",
        "exist" => 'false'
    );
}
header("Content-Type: application/json");
echo json_encode($response, JSON_NUMERIC_CHECK);
?>
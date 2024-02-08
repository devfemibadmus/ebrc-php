<?php
include_once 'config/database.php';
include_once 'objects/account.php';

$database = new Database();
$db = $database->getConnection();

$account = new Account($db);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['username']) || empty($_POST['password'])) {
        $response = array(
            "status" => "error",
            "message" => "Username and password are required"
        );
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($response, JSON_NUMERIC_CHECK);
        exit;
    }

    $account->username = $_POST['username'];
    $account->password = $_POST['password'];

    $stmt = $account->signin();

    if ($stmt != false) {
        if ($account->delete()) {
            $response = array(
                "status" => "success",
                "message" => "Account deleted successfully"
            );
        } else {
            $response = array(
                "status" => "error",
                "message" => "Failed to delete account"
            );
        }
    } else {
        // Account not found
        $response = array(
            "status" => "error",
            "message" => "Invalid username or password"
        );
    }
    header("Content-Type: application/json");
    echo json_encode($response, JSON_NUMERIC_CHECK);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account</title>
</head>
<body>
    <h2>Delete Account</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Delete Account</button>
    </form>
</body>
</html>

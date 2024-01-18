
# EBRSNG App - PHP Backend Repository

  
This repository houses the server side for the [ebrsngapi-flutter](https://github.com/devfemibadmus/ebrsngapi-php) (UI/UX). This app is design basically to watch ads and make money.

 
**ADVANTAGES OF WATCHING ADS ON EBRSNG AND MINIMIZING COSTS**

 
1.  **Monetize Your Time:**

- Users can turn their time into valuable rewards by watching ads on [App Name], creating an opportunity to earn money.

2.  **Receive Regular Updates:**

- Stay informed about the latest news, trends, and updates while watching ads, enhancing users' knowledge and awareness.

3.  **Entertainment Rewards:**

- Enjoy entertainment while earning rewards; users can watch ads and receive incentives for their time spent on the app.

4.  **Cost Minimization with Free Data Bonuses:**

- Users can maximize their benefits by utilizing free data bonuses from their Internet Service Provider (ISP) to watch ads, minimizing data costs.

5.  **Optimize Costs with Cheap Night Browsing:**

- Take advantage of cost-effective night browsing options to watch ads during off-peak hours, ensuring users maximize their rewards efficiently.

7.  **Reward Flexibility:**

- The reward system offers flexibility, allowing users to choose from a variety of options such as cash, gift cards, or discounts based on their preferences.

9.  **Seamless Redemption Process:**

- Enjoy a hassle-free redemption process within the app, making it easy for users to claim their rewards and enjoy the fruits of their engagement.

9.  **Diversify Earnings:**

- Users can diversify their earnings by engaging with different types of ads, ensuring a dynamic and rewarding experience.

  

[![Download on Google Play](https://cloud.githubusercontent.com/assets/5692567/10923351/6b688a92-8278-11e5-9973-8ffbf3c5cc52.png)](https://play.google.com/store/apps/details?id=com.blackstackhub.ebrsng&hl=en-US&ah=WNIlRmUKRT1YYCEwY8gCKLCtK-k)

  

## PHP (code review).

  

1.  **Database Configuration**
    - (config/database.php)
```php
<?php

class Database {
    private $host = "localhost"; // Adjust to your db host
    private $db_name = "ebrsng"; // Adjust to your db name
    private $username = "ebrsng"; // Adjust to your db user username
    private $password = "helloworld"; // Adjust to your db user password

    public $conn;

    // get the database connection
    public function getConnection() {
        $this->conn = null;
        try {
            // Connect to your database
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name;
            $this->conn = new PDO($dsn, $this->username, $this->password);

            // Set PDO attributes
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            // Create tables if they do not exist
            $this->createAccountsTable();
            $this->createNotificationsTable();

            return $this->conn;
        } catch (PDOException $exception) {
            $response = array(
                "status" => "error",
                "message" => $exception->getMessage(),
            );
            header("Content-Type: application/json; charset=UTF-8");
            echo json_encode($response);
        }
        return $this->conn;
    }

    // method to create the accounts table if it does not exist
    private function createAccountsTable() {
        $sql = "
            CREATE TABLE IF NOT EXISTS accounts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255),
                referral VARCHAR(255),
                username VARCHAR(255) NOT NULL,
                password VARCHAR(255) NOT NULL,
                paidReferral VARCHAR(255),
                accountName VARCHAR(255),
                accountUname VARCHAR(255),
                accountNumber INT DEFAULT 0,
                accountBalance DECIMAL(10, 2) DEFAULT 0.00,
                earnBalance DECIMAL(10, 2) DEFAULT 0.00,
                accountEditable TINYINT(1) DEFAULT 1,
                coinBalance INT DEFAULT 0,
                pendingCashout TINYINT(1) DEFAULT 0,
                cashoutAmount DECIMAL(10, 2) DEFAULT 0.00
            );
        ";

        $this->conn->exec($sql);
    }

    // method to create the notifications table if it does not exist
    private function createNotificationsTable() {
        $sql = "
            CREATE TABLE IF NOT EXISTS notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                date TIMESTAMP NOT NULL,
                type VARCHAR(255) NOT NULL,
                amount DECIMAL(10, 2) DEFAULT 0.00,
                referral VARCHAR(255),
                comment TEXT,
                accountId INT NOT NULL
            );
        ";

        $this->conn->exec($sql);
    }
}

```

2.  **Path Configuration**
    - (.htaccess)
```php
# Enable symbolic links for the specified directory
Options +FollowSymLinks

# Enable the Apache mod_rewrite engine
RewriteEngine On

# Disable directory listing (indexes)
Options -Indexes

# Custom error document for 403 Forbidden error
ErrorDocument 403 https://github.com/devfemibadmus/ebrsng-php

# Check if the requested URL does not point to an existing file
RewriteCond %{REQUEST_FILENAME} !-f

# Check if the requested URL does not point to an existing directory
RewriteCond %{REQUEST_FILENAME} !-d

# Rewrite the URL to index.php and pass the path as a parameter
RewriteRule ^(.*)$ index.php?path=$1 [L,QSA]

```

3.  **Url routing(including static files)**
    - (index.php)
    
```php
<?php
// Start or resume the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set security headers
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");

// Get the requested path from the query parameters
$path = $_GET['path'] ?? '';

// Define the root directory for the website
$root = __DIR__ . '/website/';
$file = $root . $path;

// Serve static files directly (e.g., CSS, JS, images, fonts)
if (strpos($path, 'static/') === 0 && file_exists($file)) {
    // Set appropriate content type headers based on file extension
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

    // Output the content of the static file and exit
    readfile($file);
    exit;
}

// Process dynamic routes based on the requested path
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
        // Include a default page if the requested path doesn't match any specific case
        include 'website/z.html';
        break;
}
```

3.  **Header (CORS)**
    - (config/header.php)
```php
<?php

// Set HTTP headers for Cross-Origin Resource Sharing (CORS) and response content type

// Allow any origin to access the resource
header("Access-Control-Allow-Origin: *");

// Specify the allowed HTTP methods (POST and GET)
header("Access-Control-Allow-Methods: POST, GET");

// Set the content type of the response as JSON with UTF-8 character encoding
header("Content-Type: application/json; charset=UTF-8");

// Set the maximum age (in seconds) for which the results of a preflight request can be cached
header("Access-Control-Max-Age: 20");

// Specify the allowed HTTP headers for the actual request
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

```

4.  **Form validation**
    - (account/signin.php)
    
```php
<?php
// Include necessary files and classes
require_once 'config/header.php';
include_once 'config/database.php';
include_once 'objects/account.php';
include_once 'objects/notifications.php';

// Create a new Database instance
$database = new Database();
$db = $database->getConnection();

// Create instances of Account and Notifications classes
$account = new Account($db);
$notifications = new Notifications($db);

// Decode the JSON data received from the client-side form
$_POST = json_decode(file_get_contents("php://input"), true);

// Check if username and password are provided in the POST data
if (empty($_POST['username']) || empty($_POST['password'])) {
    // If not provided, return an error response
    $response = array(
        "status" => "error",
        "message" => "Username and password are required"
    );
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode($response, JSON_NUMERIC_CHECK);
    exit;
}

// Set the username and password from the POST data
$account->username = $_POST['username'];
$account->password = $_POST['password'];

// Attempt to sign in by calling the 'signin' method of the Account class
$stmt = $account->signin();

// Check if sign-in was successful
if ($stmt != false) {
    // If successful, retrieve notifications for the signed-in user
    $list = $notifications->listNotifications($stmt['id']);
    // Prepare a success response with account information and notifications
    $response = array(
        "status" => "success",
        "message" => "Sign in successful",
        "account" => $stmt,
        "notifications" => $list
    );
} else {
    // If sign-in was not successful, return an error response
    $response = array(
        "status" => "error",
        "message" => $stmt
    );
}

// Set the response header and echo the JSON-encoded response
header("Content-Type: application/json");
echo json_encode($response, JSON_NUMERIC_CHECK);

```
  

[![Download on Google Play](https://cloud.githubusercontent.com/assets/5692567/10923351/6b688a92-8278-11e5-9973-8ffbf3c5cc52.png)](https://play.google.com/store/apps/details?id=com.blackstackhub.ebrsng&hl=en-US&ah=WNIlRmUKRT1YYCEwY8gCKLCtK-k)

  


![working terminal](readme/Screenshot%20(1099).png?raw=true)

  

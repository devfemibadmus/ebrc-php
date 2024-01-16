<?php
class Database {
    private $host = "localhost";
    private $db_name = "ebrsng";
    private $username = "ebrsng";
    private $password = "helloworld";
    
    public $conn;

    // get the database connection
    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host, $this->username, $this->password);
            $this->conn->exec("set names utf8");

            // Create the database if it does not exist
            $this->conn->exec("CREATE DATABASE IF NOT EXISTS " . $this->db_name);

            // Switch to the specified database
            $this->conn->exec("USE " . $this->db_name);

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
                accountEditable BOOLEAN DEFAULT 1,
                coinBalance INT DEFAULT 0,
                rewardAds INT DEFAULT 0,
                pendingCashout BOOLEAN DEFAULT 0,
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
                date DATETIME NOT NULL,
                type VARCHAR(255) NOT NULL,
                amount DECIMAL(10, 2) DEFAULT 0.00,
                referral VARCHAR(255),
                comment TEXT,
                accountId INT NOT NULL,
                FOREIGN KEY (account_id) REFERENCES accounts(id)
            );
        ";

        $this->conn->exec($sql);
    }
}

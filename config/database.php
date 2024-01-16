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
                paid_referral VARCHAR(255),
                account_name VARCHAR(255),
                account_uname VARCHAR(255),
                account_number VARCHAR(255),
                account_balance DECIMAL(10, 2) DEFAULT 0.00,
                earn_balance DECIMAL(10, 2) DEFAULT 0.00,
                account_editable BOOLEAN DEFAULT 1,
                coin_balance INT DEFAULT 0,
                reward_ads INT DEFAULT 0,
                pending_cashout BOOLEAN DEFAULT 0,
                cashout_amount DECIMAL(10, 2) DEFAULT 0.00
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
                account_id INT NOT NULL,
                FOREIGN KEY (account_id) REFERENCES accounts(id)
            );
        ";

        $this->conn->exec($sql);
    }
}

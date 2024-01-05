<?php
class Database {
    /**/
    private $host = "localhost";
    private $db_name = "ebrsng";
    private $username = "hello";
    private $password = "hello";
    
    public $conn;

    // get the database connection
    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
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
}
?>
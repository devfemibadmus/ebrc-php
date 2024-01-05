<?php
class Notifications {
    // database connection and table name
    private $conn;
    private $table_name = "notifications";

    // object properties
    public $date;
    public $type;
    public $amount;
    public $referral;
    public $comment;
    public $account_id;

    // constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    public function listNotifications($account_id) {
        $query = "SELECT * FROM notifications WHERE account_id = :account_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':account_id', $account_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createNotifications(){
        $query = "INSERT INTO notifications SET date=:date, type=:type, amount=:amount, referral=:referral, comment=:comment, account_id=:account_id";
        $stmt = $this->conn->prepare($query);
        // sanitize
        $this->date=htmlspecialchars(strip_tags($this->date));
        $this->type=htmlspecialchars(strip_tags($this->type));
        $this->amount=htmlspecialchars(strip_tags($this->amount));
        $this->comment=htmlspecialchars(strip_tags($this->comment));
        $this->account_id=htmlspecialchars(strip_tags($this->account_id));
        // bind values
        $stmt->bindParam(":date", $this->date);
        $stmt->bindParam(":type", $this->type);
        $stmt->bindParam(":amount", $this->amount);
        $stmt->bindParam(":referral", $this->referral);
        $stmt->bindParam(":comment", $this->comment);
        $stmt->bindParam(":account_id", $this->account_id);
        // execute query
        if($stmt->execute()){
            return true;
        }
        return false;
    }


}
?>
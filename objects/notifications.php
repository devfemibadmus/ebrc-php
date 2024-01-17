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
    public $accountId;

    // constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    public function listNotifications($accountId) {
        $query = "SELECT * FROM notifications WHERE accountId = :accountId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':accountId', $accountId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createNotifications(){
        $query = "INSERT INTO notifications SET date=:date, type=:type, amount=:amount, referral=:referral, comment=:comment, accountId=:accountId";
        $stmt = $this->conn->prepare($query);
        // sanitize
        $this->date=htmlspecialchars(strip_tags($this->date));
        $this->type=htmlspecialchars(strip_tags($this->type));
        $this->amount=htmlspecialchars(strip_tags($this->amount));
        $this->comment=htmlspecialchars(strip_tags($this->comment));
        $this->accountId=htmlspecialchars(strip_tags($this->accountId));
        // bind values
        $stmt->bindParam(":date", $this->date);
        $stmt->bindParam(":type", $this->type);
        $stmt->bindParam(":amount", $this->amount);
        $stmt->bindParam(":referral", $this->referral);
        $stmt->bindParam(":comment", $this->comment);
        $stmt->bindParam(":accountId", $this->accountId);
        // execute query
        if($stmt->execute()){
            return true;
        }
        return false;
    }


}
?>
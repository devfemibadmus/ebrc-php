<?php
class Account {
    // database connection and table name
    private $conn;
    private $table_name = "accounts";

    // object properties
    public $id;
    public $email;
    public $referral;
    public $username;
    public $password;
    public $paidReferral;
    public $accountName;
    public $accountUname;
    public $accountNumber;
    public $accountBalance;
    public $earnBalance;
    public $accountEditable;
    public $coinBalance;
    public $pendingCashout;
    public $cashoutAmount;

    // constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }
    public function signin(){
        $query = "SELECT * FROM accounts WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $this->username);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row != false){
            $password_hash = $row['password'];
            if(password_verify($this->password, $password_hash)){
                return $row;
            }
        }
        return false;
    }
    public function admin(){
        $query = "SELECT * FROM accounts WHERE username=:username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $this->username);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row != false){
            $password_hash = $row['password'];
            if(password_verify($this->password, $password_hash)){
                return $row;
            }
        }
        return false;
    }
    public function signup(){
        $query = "INSERT INTO accounts SET username=:username, password=:password, email=:email, referral=:referral";
        $stmt = $this->conn->prepare($query);
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':referral', $this->referral);
        if($stmt->execute()){
            return true;
        }
        return false;
    }
    public function getUser(){
        $query = "SELECT * FROM accounts WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $this->username=htmlspecialchars(strip_tags($this->username));
        $stmt->bindParam(':username', $this->username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function print_all_users(){
        $query = "SELECT * FROM accounts";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $results = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $results[] = $row;
        }
        return $results;
    }
    public function bank(){
        $query = "UPDATE accounts SET accountName=:accountName, accountUname=:accountUname, accountNumber=:accountNumber, accountEditable=:accountEditable WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':accountName', $this->accountName);
        $stmt->bindParam(':accountUname', $this->accountUname);
        $stmt->bindParam(':accountNumber', $this->accountNumber);
        $stmt->bindParam(':accountEditable', $this->accountEditable);
        if($stmt->execute()){
            return true;
        }
        return false;
    }
    public function coin(){
        $query = "UPDATE accounts SET coinBalance=:coinBalance WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':coinBalance', $this->coinBalance);
        if($stmt->execute()){
            return true;
        }
        return false;
    }
    public function cashout(){
        $query = "UPDATE accounts SET accountBalance=:accountBalance, pendingCashout=:pendingCashout WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':accountBalance', $this->accountBalance);
        $stmt->bindParam(':pendingCashout', $this->pendingCashout);
        if($stmt->execute()){
            return true;
        }
        return false;
    }
    public function create_cashout(){
        $query = "INSERT INTO cashout SET username=:username, cashoutAmount=cashoutAmount";
        $stmt = $this->conn->prepare($query);
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':cashoutAmount', $this->cashoutAmount);
        if($stmt->execute()){
            return true;
        }
        return false;
    }
    public function paidCashout(){
        $query = "UPDATE cashout SET paid=true WHERE username=:username AND cashoutAmount=:cashoutAmount AND paid=false";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':cashoutAmount', $this->cashoutAmount);
        if($stmt->execute()){
            return true;
        }
        return false;
    }
    
    public function paidCashoutAccount(){
        $query = "UPDATE accounts SET pendingCashout=:pendingCashout WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':pendingCashout', $this->pendingCashout);
        if($stmt->execute()){
            return true;
        }
        return false;
    }
    public function getPendingCashOut(){
        $query = "SELECT * FROM cashout WHERE paid=false";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $results = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $results[] = $row;
        }
        return $results;
    }
    public function getPendingCashOutAccounts(){
        $query = "SELECT * FROM accounts WHERE pendingCashout=true";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $results = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $results[] = $row;
        }
        return $results;
    }
    public function balance(){
        $query = "UPDATE accounts SET accountBalance=:accountBalance, earnBalance=:earnBalance WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':accountBalance', $this->accountBalance);
        $stmt->bindParam(':earnBalance', $this->earnBalance);
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function delete(){
        $query = "SELECT id FROM accounts WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $this->username);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $account_id = $row['id'];
            $query = "DELETE FROM accounts WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $account_id);
            
            if ($stmt->execute()) {
                return true; 
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    

}


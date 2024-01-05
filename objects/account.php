<?php
class Account {
    // database connection and table name
    private $conn;
    private $table_name = "accounts";

    // object properties
    public $id;
    public $email;
    public $referral;
    public $activated;
    public $username;
    public $password;
    public $paid_referral;
    public $account_name;
    public $account_uname;
    public $account_number;
    public $account_balance;
    public $earn_balance;
    public $account_editable;
    public $coin_balance;
    public $reward_ads;
    public $tic_tac_toe_1;
    public $tic_tac_toe_2;
    public $tic_tac_toe_3;
    public $user_referral;
    public $super_referral;
    public $might_referral;
    public $premium_referral;
    public $pending_cashout;
    public $cashout_amount;

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
        $query = "UPDATE accounts SET account_name=:account_name, account_uname=:account_uname, account_number=:account_number, account_editable=:account_editable WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':account_name', $this->account_name);
        $stmt->bindParam(':account_uname', $this->account_uname);
        $stmt->bindParam(':account_number', $this->account_number);
        $stmt->bindParam(':account_editable', $this->account_editable);
        if($stmt->execute()){
            return true;
        }
        return false;
    }
    public function coin(){
        $query = "UPDATE accounts SET coin_balance=:coin_balance WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':coin_balance', $this->coin_balance);
        if($stmt->execute()){
            return true;
        }
        return false;
    }
    public function cashout(){
        $query = "UPDATE accounts SET account_balance=:account_balance, pending_cashout=:pending_cashout WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':account_balance', $this->account_balance);
        $stmt->bindParam(':pending_cashout', $this->pending_cashout);
        if($stmt->execute()){
            return true;
        }
        return false;
    }
    public function create_cashout(){
        $query = "INSERT INTO cashout SET username=:username, cashout_amount=cashout_amount";
        $stmt = $this->conn->prepare($query);
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':cashout_amount', $this->cashout_amount);
        if($stmt->execute()){
            return true;
        }
        return false;
    }
    public function paidCashout(){
        $query = "UPDATE cashout SET paid=true WHERE username=:username AND cashout_amount=:cashout_amount AND paid=false";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':cashout_amount', $this->cashout_amount);
        if($stmt->execute()){
            return true;
        }
        return false;
    }
    
    public function paidCashoutAccount(){
        $query = "UPDATE accounts SET pending_cashout=:pending_cashout WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':pending_cashout', $this->pending_cashout);
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
        $query = "SELECT * FROM accounts WHERE pending_cashout=true";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $results = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $results[] = $row;
        }
        return $results;
    }
    public function activate(){
        $query = "UPDATE accounts SET activated=true WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        if($stmt->execute()){
            return true;
        }
        return false;
    }
    public function balance(){
        $query = "UPDATE accounts SET account_balance=:account_balance, earn_balance=:earn_balance WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':account_balance', $this->account_balance);
        $stmt->bindParam(':earn_balance', $this->earn_balance);
        if($stmt->execute()){
            return true;
        }
        return false;
    }

}

?>
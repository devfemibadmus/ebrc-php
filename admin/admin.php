<?php
include_once 'config/database.php';
include_once 'objects/account.php';
include_once 'objects/notifications.php';

$database = new Database();
$db = $database->getConnection();

$account = new Account($db);
$notifications = new Notifications($db);

$amount = 200;
$post_url ='your pending cashout has been paid to your bank account';
$date = new DateTime();
$formatted_date = $date->format("Y-m-d H:i:s");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['amount']) && isset($_POST['post_url'])) {
        $amount = $_POST['amount'];
        $post_url = $_POST['post_url'];
    }

    if (isset($_POST['cashouts']) && isset($_POST['action'])) {
        foreach ($_POST['cashouts'] as $cashout_username) {
            $account->username = $cashout_username;
            $stmt = $account->getUser();
            if($stmt){
                $account->id = $stmt['id'];
                $account->pendingCashout = 0;
                $account->cashoutAmount = $amount;
                $pay = $account->paidCashoutAccount();
                $update = $account->paidCashout();
                if($pay && $update){
                    $notifications->account_id = $stmt['id'];
                    $notifications->referral = '';
                    $notifications->amount = $amount;
                    $notifications->date = $formatted_date;
                    $notifications->type = 'withdraw';
                    $notifications->comment = $post_url;
                    if($notifications->createNotifications()){
                        echo $stmt['id']." Operation successful ".$amount;
                    }
                    else{
                        echo 'cant create notification '.$stmt['id'];
                    }
                }
                else{
                    echo 'cant update user cashout '.$stmt['id'];
                }
            }
            else{
                echo 'cant find user';
            }
        }
    }
}

// Get all accounts
$all_accounts = $account->getPendingCashOutAccounts();
// Get pending cashouts
$pendingCashouts = $account->getPendingCashOut();

// Create an empty array to store the merged data
$merged_data = array();

// Iterate over all accounts
foreach ($all_accounts as $account_data) {
  // Check if the account has a pending cashout
  if (in_array($account_data['username'], array_column($pendingCashouts, 'username'))) {
    // Merge the two arrays to get the desired result
    $merged_data[] = array_merge($account_data, array_filter($pendingCashouts, function($item) use($account_data) {
      return $item['username'] == $account_data['username'];
    })[0]);
  }
}


?>
<form method="POST">
    <table style="border: 2px solid black;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Username</th>
                <th>Account Name</th>
                <th>Account User Name</th>
                <th>Account Number</th>
                <th>Account Balance</th>
                <th>Earn Balance</th>
                <th>Coin Balance</th>
                <th>Cashout Amount</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($merged_data as $cashout) { ?>
                <tr>
                    <td><?php echo $cashout['id']; ?></td>
                    <td><?php echo $cashout['email']; ?></td>
                    <td><?php echo $cashout['username']; ?></td>
                    <td><?php echo $cashout['accountName']; ?></td>
                    <td><?php echo $cashout['accountUname']; ?></td>
                    <td><?php echo $cashout['accountNumber']; ?></td>
                    <td><?php echo $cashout['accountBalance']; ?></td>
                    <td><?php echo $cashout['earnBalance']; ?></td>
                    <td><?php echo $cashout['coinBalance']; ?></td>
                    <td><?php echo $cashout['cashoutAmount']; ?></td>
                    <td><?php echo $cashout['date']; ?></td>
                    <td><input type="checkbox" name="cashouts[]" value="<?php echo $cashout['username']; ?>"></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <label for="amount">Amount:</label>
    <input type="text" name="amount" value="<?php echo $amount; ?>">
    <br/>
    <label for="amount">Post:</label>
    <input type="text" name="post_url" value="<?php echo $post_url; ?>">
    <input type="hidden" name="action" value="update_status">
    <button type="submit">Update status</button>
</form>

<style>
    table {
  width: 100%;
  border-collapse: collapse;
  border: 1px solid #ccc;
  margin: 20px 0;
  font-size: 14px;
}

th, td {
  padding: 10px;
  text-align: left;
  border-bottom: 1px solid #ccc;
}

th {
  background-color: #f2f2f2;
  font-weight: bold;
}

tr:nth-child(even) {
  background-color: #f2f2f2;
}

input[type="text"] {
  padding: 5px;
  border: 1px solid #ccc;
  border-radius: 3px;
  font-size: 14px;
  margin-bottom: 10px;
}

button[type="submit"] {
  padding: 10px;
  border: none;
  background-color: #4CAF50;
  color: white;
  border-radius: 3px;
  cursor: pointer;
}

button[type="submit"]:hover {
  background-color: #3e8e41;
}

</style>
<?php
require_once 'dbconnect.php';
session_start();
$errors = [];
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    echo '<p align = "center"> Email : ' . $user[':email'] . '</p><br>';
    echo '<p align = "center"> Name : ' . $user[':first_name'] . ' ' . $user[':last_name'] . '</p><br>';
    echo '<p align="center">Current Balance: $' . htmlspecialchars($user[':balance']['balance']) . '</p><br>';
} else { //Session user is not set yet, user isn't logged in
    header('Location: pages/login.php');
}

//If form == add_value
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_value'])) {
    $amount = $_POST['amount'];
    // Check if amount is a numeric value
    if (!is_numeric($amount)) {
        throw new Exception("Invalid amount value.");
    }
    // Convert to a decimal
    $amount = number_format($amount, 2);
    echo $amount;
    $stmt = $pdo->prepare('UPDATE accountbalance 
                                   JOIN users ON accountbalance.user_id = users.id
                                   SET accountbalance.balance = accountbalance.balance + :amount
                                   WHERE accountbalance.user_id = :user_id');
    $stmt->execute([
        ':user_id' => $user[':id'],
        ':amount' => $amount
    ]);

    header('Location: ../pages/home.php');


}

//If form == transfer_out
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['transfer_out'])) {
    $amount = $_POST['amount'];
    $balance = $user[':balance']['balance'];
    // Check if amount is a numeric value
    if (!is_numeric($amount)) {
        throw new Exception("Invalid amount value.");
    }else if($balance<=$amount){
        throw new Exception(message:"Insufficient amount in your bank.");
    }
    // Convert to a decimal
    $amount = number_format($amount, 2);
    print_r($user);
    $stmt = $pdo->prepare('UPDATE accountbalance 
                                   JOIN users ON accountbalance.user_id = users.id
                                   SET accountbalance.balance = accountbalance.balance - :amount
                                   WHERE accountbalance.user_id = :user_id');
    $stmt->execute([
        ':user_id' => $user[':id'],
        ':amount' => $amount
    ]);

    //header('Location: ../pages/home.php');


}

//If form == send_money
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_money'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senderEmail = $user[':email'];
    $amount = $_POST['amount'];
    // Check if amount is a numeric value
    if (!is_numeric($amount)) {
        throw new Exception("Invalid amount value.");
    }
    // Convert to a decimal
    $amount = number_format($amount, 2);
    //Check if recipient email exists
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email =:email');
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) { //If email is already in users
        $errors['user_exist'] = 'Email is already registered. Please try to log in.';
    }else if($balance<=$amount){
        throw new Exception(message:"Insufficient amount in your bank.");
    }
    
    //Send amount over to the recipient account
    $stmt = $pdo->prepare('UPDATE accountbalance 
                                   JOIN users ON accountbalance.user_id = users.id
                                   SET accountbalance.balance = accountbalance.balance + :amount
                                   WHERE users.email = :email');
    $stmt->execute([
        ':email' => $email,
        ':amount' => $amount
    ]);
    //Remove money from the sender's account
    $stmt = $pdo->prepare('UPDATE accountbalance 
                                   JOIN users ON accountbalance.user_id = users.id
                                   SET accountbalance.balance = accountbalance.balance - :amount
                                   WHERE accountbalance.user_id = :user_id');
    $stmt->execute([
        ':user_id' => $user[':id'],
        ':amount' => $amount
    ]);
}

//If form == addCard
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_money'])) {
    
}
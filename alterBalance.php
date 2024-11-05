<?php
session_start();
require_once 'dbconnect.php';

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
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
    $amount = number_format(1, 2);
    echo $amount;
    $stmt = $pdo->prepare('UPDATE accountbalance JOIN users ON accountbalance.user_id = users.id
                                    SET accountbalance.balance = accountbalance.balance + :amount
                                    WHERE accountbalance.user_id = :user_id;');
    $stmt -> execute([
        ':amount' => $amount,
    ]);
  //If form == add_value
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['subtract_value'])) {
    $amount = $_POST['amount'];
    // Check if amount is a numeric value
    if (!is_numeric($amount)) {
        throw new Exception("Invalid amount value.");
    }
    //$currentAmount = 
    //$totalAmount = 
    // Convert to a decimal
    $amount = number_format(1, 2);
    echo $amount;
    $stmt = $pdo->prepare('UPDATE accountbalance JOIN users ON accountbalance.user_id = users.id
                                    SET accountbalance.balance = accountbalance.balance + :amount
                                    WHERE accountbalance.user_id = :user_id;');
    $stmt -> execute([
        ':amount' => $amount,
    ]);  

}
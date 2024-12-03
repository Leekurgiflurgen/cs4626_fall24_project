<?php
require 'dbconnect.php';
session_start();
$errors = []; //Store errors that occur during login or registration
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $stmt = $pdo->prepare('SELECT balance FROM accountbalance WHERE user_id = :id');
    $stmt->execute([':id' => $user[':id']]);
    $balance = $stmt->fetchColumn();

    $stmt = $pdo->prepare('SELECT iv FROM users WHERE id = :id');
    $stmt->execute([':id' => $user[':id']]);
    $iv = $stmt->fetchColumn();
    $ivDecoded = base64_decode($iv);
    $encryption_key = require '../encryptionKey.php';
    $firstName = $user[':first_name'];
    $lastName = $user[':last_name'];
    $decryptedFirstName = openssl_decrypt($firstName, 'aes-256-cbc', $encryption_key, 0, $ivDecoded);
    $decryptedLastName = openssl_decrypt($lastName, 'aes-256-cbc', $encryption_key, 0, $ivDecoded);

} else { //Session user is not set yet, user isn't logged in
    header('Location: login.php');
}
//If form == signup
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL); //Santizes email to ensure valid email format
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $userPIN = $_POST['accountPIN'];
    $pattern = "/^[a-zA-Z-' ]+$/";
    $nameResult = preg_match($pattern, $name);
    if (!is_numeric($userPIN)) {
        $errors['amount_type'] = 'Invalid amount format';
    }
    //If email format filter fails
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }
    if(!preg_match($pattern, $first_name)) {
        $errors['first_name_type'] = "Invalid name format.";
    }
    if(!preg_match($pattern, $last_name)) {
        $errors["last_name_type"] = "Invalid name format.";
    }
    if (empty($first_name) || empty($last_name)) { //If either name fields are left empty
        $errors['names'] = 'Name is required.';
    }
    if (strlen($password) < 8){
        $errors['password'] = 'Password must be at least eight characters long.';
    }
    if ((strlen($userPIN) < 5)) {
        $errors['pin_length'] = "PIN must be at least five characters long.";
    }
    if ($password !== $password_confirm) {
        $errors['password_confirm'] = 'Passwords do not match.';

    }
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email =:email');
    $stmt->execute(['email' => $email]);

    if ($stmt->fetch()) { //If email is already in users
        $errors['user_exist'] = 'Email is already registered. Please try to log in.';
    }
    if (!empty($errors)) { //If there are any errors, stop script and reload page
        $_SESSION['errors'] = $errors;
        header('Location: ../pages/register.php');
        exit();
    }
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $hashedPIN = password_hash($userPIN, PASSWORD_BCRYPT);

    $encryption_key = require '../encryptionKey.php';
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $ivBase64 = base64_encode($iv);
    $encrypted_first_name = openssl_encrypt($first_name, 'aes-256-cbc', $encryption_key, 0, $iv);
    $encrypted_last_name = openssl_encrypt($last_name, 'aes-256-cbc', $encryption_key, 0, $iv);

    $stmt = $pdo->prepare('INSERT INTO users (email, password, firstName, lastName, accountPIN, iv) 
                            VALUES(:email,:password,:first_name,:last_name, :accountPIN, :iv)');

    //Execute actual statement
    $stmt->execute([
        ':email' => $email,
        ':password' => $hashedPassword,
        ':first_name' => $encrypted_first_name,
        ':last_name' => $encrypted_last_name,
        ':accountPIN' => $hashedPIN,
        ':iv'=> $ivBase64

    ]);

    $accountID = $pdo->lastInsertId(); //Get inserted account id
    $stmtBalance = $pdo->prepare('INSERT INTO accountBalance (user_id, balance) VALUES (:accountID, 0)');
    $stmtBalance->execute(params: [':accountID' => $accountID]);


    header('Location: ../pages/login.php'); //Send to login.php
    exit();
}

//If form == login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }
    if (empty($password)) {
        $errors['password'] = 'Password cannot be empty';
    }
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: login.php');
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=:email");
    $stmt->execute(params: ['email' => $email]);
    $user = $stmt->fetch();

    $id = $user['id'];

    //Get current balance of the user whose email you are querying
    $stmtBalance = $pdo->prepare("SELECT balance FROM accountbalance WHERE user_id = :user_id");
    $stmtBalance->execute([':user_id' => $id]);
    $balanceRow = $stmtBalance->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            ':id' => $user['id'],
            ':email' => $user['email'],
            ':first_name' => $user['firstName'],
            ':last_name' => $user['lastName'],
            ':balance' => $balanceRow,
            ':accountPIN' => $user['accountPIN']
        ];
        header('Location: /project/pages/home.php');
        exit();
    } else {
        $errors['login'] = 'Invalid email or password';
        $_SESSION['errors'] = $errors;
        header('Location: /project/pages/login.php');
        exit();
    }
}

//Update user information such as first/last name, email
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_account'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $pattern = "/^[a-zA-Z-' ]+$/";
    if(!preg_match($pattern, $first_name)) {
        $errors['first_name_type'] = "Invalid name format.";
    }
    if(!preg_match($pattern, $last_name)){
        $errors["last_name_type"] = "Invalid name format.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }
    if (empty($first_name) || empty($last_name)) { //If either name fields are left empty
        $errors['names'] = 'Name is required.';
    }
    if (!empty($errors)) { //If there are any errors, stop script and reload page
        $_SESSION['errors'] = $errors;
        header('Location: ../pages/updateAccount.php');
        exit();
    }
    //get iv from user to prepare for encryption of user input
    $stmt = $pdo->prepare('SELECT iv FROM users WHERE id = :id');
    $stmt->execute([':id' => $user[':id']]);
    $iv = $stmt->fetchColumn();
    $ivDecoded = base64_decode($iv);
    $encryption_key = require '../encryptionKey.php';
    $encrypted_first_name = openssl_encrypt($first_name, 'aes-256-cbc', $encryption_key, 0, $ivDecoded);
    $encrypted_last_name = openssl_encrypt($last_name, 'aes-256-cbc', $encryption_key, 0, $ivDecoded);

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('UPDATE users SET firstName = :encryptedFirst, lastName = :encryptedLast, email = :inputEmail WHERE email=:email');
        //Execute actual statement
         $stmt->execute([
            ':inputEmail' => $email,
            ':email'=> $user[':email'],
            ':encryptedFirst' => $encrypted_first_name,
            ':encryptedLast' => $encrypted_last_name
        ]);
        $pdo->commit();
        $_SESSION['message'] = "Operation was successful! You have changed your account information!";
        $_SESSION['message_type'] = "success";
    } catch (Exception $e) {
        // Rollback the transaction if there was an error
        $pdo->rollBack();
        $errors['transaction'] = 'Transaction has failed, something went wrong?';
        header('Location: /project/pages/updateAccount.php');
        exit();
    }
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=:email");
    $stmt->execute(params: ['email' => $email]);
    $user = $stmt->fetch();
    $id = $user['id'];
    $_SESSION['user'] = [
        ':id' => $user['id'],
        ':email' => $email,
        ':first_name' => $user['firstName'],
        ':last_name' => $user['lastName'],
        ':balance' => $balanceRow,
        ':accountPIN' => $user['accountPIN']
    ];
    
    header('Location: /project/pages/home.php');
    exit();


}

//Update user password
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_password'])) {
    $originalPassword = $_POST['password'];
    $newPassword = $_POST['new_password'];
    $passwordConfirm = $_POST['password_confirm'];

    $stmt = $pdo->prepare('SELECT `password` FROM users WHERE id = :id');
    $stmt->execute([':id' => $user[':id']]);
    $actualPassword = $stmt->fetchColumn();

    if (empty($originalPassword) || empty($newPassword) || empty($passwordConfirm)) {
        $errors['password_empty'] = 'Password cannot be empty';
    }
    if (strlen($newPassword) < 8){
        $errors['password_length'] = 'Password must be at least eight characters long.';
    }
    if(!password_verify($originalPassword, $actualPassword)){
        $errors['password'] = 'Inputted password does not match what is on file';
    }
    if ($newPassword !== $passwordConfirm) {
        $errors['password_confirm'] = 'Passwords do not match.';
    }
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: ../pages/updateAccount.php');
        exit();
    }
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('UPDATE users SET `password` = :newPassword WHERE `id`=:id');
        $stmt->execute([
            ':newPassword' => $hashedPassword,
            ':id'=> $user[':id']
        ]);
        $pdo->commit();
        $_SESSION['message'] = "Operation was successful! You have changed your password!";
        $_SESSION['message_type'] = "success";
    } catch (Exception $e) {
        // Rollback the transaction if there was an error
        $pdo->rollBack();
        $errors['transaction'] = 'Transaction has failed, something went wrong?';
        header('Location: /project/pages/updateAccount.php');
        exit();
    }
    header('Location: ../pages/home.php');
    exit();


}

//Update user pin
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_pin'])) {
    $originalPIN = $_POST['current_pin'];
    $newPIN = $_POST['new_pin'];
    $pinConfirm = $_POST['confirm_pin'];
    $stmt = $pdo->prepare('SELECT accountPIN FROM users WHERE id = :id');
    $stmt->execute([':id' => $user[':id']]);
    $actualPIN = $stmt->fetchColumn();

    if (empty($newPIN) || empty($pinConfirm)) {
        $errors['pin_empty'] = 'Password cannot be empty';
    }
    if (!is_numeric($newPIN)) {
        $errors['amount_type'] = 'Invalid amount format';
    }
    if (($newPIN <= 0)) {
        $errors['amount_sign'] = 'PIN must be above 0';
    }
    if(!password_verify($originalPIN, $actualPIN)){
        $errors['accountPIN'] = 'Inputted pin does not match what is on file';
    }
    if (strlen($newPIN) < 5){
        $errors['pin_length'] = 'Password must be at least five characters long.';
    }
    if ($newPIN !== $pinConfirm) {
        $errors['pin_confirm'] = 'PINS do not match.';
    }
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: ../pages/updateAccount.php');
        exit();
    }
    $hashedPIN = password_hash($newPIN, PASSWORD_BCRYPT);
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('UPDATE users SET accountPIN = :newPIN WHERE `id`=:id');
        $stmt->execute([
            ':newPIN' => $hashedPIN,
            ':id'=> $user[':id']
        ]);
        $pdo->commit();
        $_SESSION['message'] = "Operation was successful! You have changed your PIN!";
        $_SESSION['message_type'] = "success";
    } catch (Exception $e) {
        // Rollback the transaction if there was an error
        $pdo->rollBack();
        $errors['transaction'] = 'Transaction has failed, something went wrong?';
        header('Location: /project/pages/updateAccount.php');
        exit();
    }
    header('Location: ../pages/home.php');
    exit();



}
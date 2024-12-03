<?php
require '../scripts/dbconnect.php';
session_start();
if (isset($_SESSION['message'])) {
    echo "<script>alert('" . addslashes($_SESSION['message']) . "');</script>";
    unset($_SESSION['message']); // Clear the message after displaying it
}
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
    $_SESSION['message'] = "Please log in first!";
    $_SESSION['message_type'] = "error";
    header('Location: login.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body>
    <div class="settings-menu">
        <div class="dropdown">
            <button>Menu Option</button>
            <div class="dropdown-content">
                <a href="../index.html">Home</a>
                <a href="about.html">About</a>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
                <a href="addCash.php">Add Cash</a>
                <a href="sendCash.php">Send Cash</a>
                <a href="./transferOut.php">Transfer Out</a>

            </div>
        </div>
    </div>
    </div>
    <div class="container">
        <h1> Currently Logged In User:</h1><br>
        <div class="body">
            <?php
            echo '<p align = "center"> Email : ' . htmlspecialchars($user[':email']) . '</p><br>';
            echo '<p align = "center"> Name : ' . htmlspecialchars($decryptedFirstName) . ' ' . htmlspecialchars($decryptedLastName) . '</p><br>';
            echo '<p align="center">Current Balance: $' . htmlspecialchars($balance) . '</p><br>';
            ?>

            <div style="text-align: center;">
                <input type="submit" class="btn" value="Add Money" name="referToAdd"
                    onclick="location.href='addCash.php'">
                <input type="submit" class="btn" value="Send Money" name="referToSend"
                    onclick="location.href='sendCash.php'">
                <input type="submit" class="btn" value="Transfer Money" name="referToTransfer"
                    onclick="location.href='transferOut.php'">
                <input type="submit" class="btn" value="Add Card" name="referToAddCard"
                    onclick="location.href='addCard.php'">
                <input type="submit" class="btn" value="Update Account" name="referToUpdateAccount"
                    onclick="location.href='updateAccount.php'">
                <input type="submit" class="btn" value="Log Out" name="logout"
                    onclick="location.href='../scripts/logout.php'">

            </div>
        </div>
    </div>
</body>

</html>
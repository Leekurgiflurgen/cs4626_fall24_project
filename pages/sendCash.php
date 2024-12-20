<?php
require '../scripts/dbconnect.php';
session_start();
ini_set('display_errors', value: 0);  // Hide errors from the user


if (isset($_SESSION['errors'])) {
    $errors = $_SESSION['errors'];
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
$stmt = $pdo->prepare('SELECT creditNumber FROM accountbalance WHERE user_id = :id');
$stmt->execute([':id' => $user[':id']]);
$result = $stmt->fetchColumn();

//if credit card hasn't been added, redirect to the addCard page
if (!strlen($result) > 0) {
    header(header: 'Location: addCard.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Cash</title>
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
                <a href="transferOut.php">Transfer Out</a>
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
                <a href="../scripts/logout.php"> Logout</a>
            </div>
        </div>
    </div>
    <div class="container">
        <h1 class="form-title">
            Send Money To Another Account
        </h1>
        <p style="text-indent: 35px"> Who would you like to send the money to?</p>
        <?php
        if (isset($errors['user_dne'])) {
            echo '<div class = "error-main"> 
                    <p> ' . $errors['user_dne'] . '</p>
                  </div>';
        }
        ?>
        <form method="POST" action="../scripts/alterBalance.php"> <!--Sends form to alterBalance to be processed-->
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" id="email" required placeholder="Email">
            </div><br>
            <p>Account PIN for security purposes</p>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="text" name="accountPIN" id="accountPIN"
                    placeholder="Account Pin: " required>
            </div>
            <?php
            if (isset($errors['amount_type'])) {
                echo '<div class = "error-main"> 
                    <p> ' . $errors['amount_type'] . '</p>
                  </div>';
            }
            if(isset($errors['amount_sign'])) {
                echo '<div class = "error-main"> 
                    <p> ' . $errors['amount_sign'] . '</p>
                  </div>';
            }
            if (isset($errors['accountPIN'])) {
                echo '<div class = "error-main"> 
                    <p> ' . $errors['accountPIN'] . '</p>
                  </div>';
            }
            ?>
            <p>How much would you like to send?</p>

            <div class="input-group">
                <i class="fa-solid fa-dollar-sign"></i>
                <input type="amount" name="amount" id="amount" required placeholder="Amount">
            </div>
            <?php
            if (isset($errors['amount_type'])) {
                echo '<div class = "error-main"> 
                    <p> ' . $errors['amount_type'] . '</p>
                  </div>';
            }else if(isset($errors['amount_sign'])) {
                echo '<div class = "error-main"> 
                    <p> ' . $errors['amount_sign'] . '</p>
                  </div>';
            }else if (isset($errors['insufficient_balance'])) {
                echo '<div class = "error-main"> 
                    <p> ' . $errors['insufficient_balance'] . '</p>
                  </div>';
            }
            ?>
            <p>
                <input type="submit" class="btn" value="Send Money" name="send_money">
            </p>
</body>

</html>
<?php
if (isset($_SESSION['errors'])) {
    unset($_SESSION['errors']);
}
?>
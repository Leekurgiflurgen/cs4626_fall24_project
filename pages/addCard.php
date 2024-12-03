<?php
require '../scripts/dbconnect.php';

session_start();

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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Card</title>
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
                <a href="home.php">My Account</a>
                <a href="register.php">Register</a>
                <a href="addCash.php">Add Cash</a>
                <a href="sendCash.php">Send Cash</a>
            </div>
        </div>
    </div>
    <div class="container">
        <h1> Currently Logged In User:</h1><br>
        <div class="body">
            <?php
            echo '<p align = "center"> Email : ' . $user[':email'] . '</p><br>';
            echo '<p align = "center"> Name : ' . $decryptedFirstName . ' ' . $decryptedLastName . '</p><br>';
            echo '<p align="center">Current Balance: $' . htmlspecialchars($balance) . '</p><br>';
            ?>
            <div style="text-align: center;">
                <a href="../scripts/logout.php"> Logout</a>
            </div>
        </div>
    </div>
    <div class="container">
        <h1 class="form-title">
            Add Card To Account Or Replace Current Card
        </h1>
        <p style="text-indent: 40px"> Please enter your credit card number</p>
        <form method="POST" action="../scripts/alterBalance.php">
            <div class="input-group">
                <i class="fa-regular fa-credit-card"></i></i>
                <input type="creditNumber" name="creditNumber" id="creditNumber" required placeholder="Credit Number">
            </div>
            <?php
            if (isset($errors['credit_error'])) {
                echo '<div class = "error-main"> 
                    <p> ' . $errors['credit_error'] . '</p>
                  </div>';
            } else if (isset($errors['credit_length'])) {
                echo '<div class = "error-main"> 
                    <p> ' . $errors['credit_length'] . '</p>
                  </div>';
            }
            ?>
            <div class="input-group">
                <i class="fa-regular fa-credit-card"></i></i>
                <input type="month" name="creditExpiry" id="creditExpiry" required placeholder="Expiration Date">
            </div>
            <div class="input-group">
                <i class="fa-regular fa-credit-card"></i></i>
                <input type="cvv" name="cvv" id="cvv" required placeholder="cvv">
            </div>
            <?php
            if (isset($errors['cvv_error'])) {
                echo '<div class = "error-main"> 
                    <p> ' . $errors['cvv_error'] . '</p>
                  </div>';
            }
            ?>
            <p>
                <input type="submit" class="btn" value="Add Card" name="add_card">
            </p>
        </form>
    </div>
</body>

</html>
<?php
if (isset($_SESSION['errors'])) {
    unset($_SESSION['errors']);
}
?>
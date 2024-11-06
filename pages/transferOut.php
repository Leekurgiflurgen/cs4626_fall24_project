<?php
session_start();
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    echo '<p align = "center"> Email : ' . $user[':email'] . '</p><br>';
    echo '<p align = "center"> Name : ' . $user[':first_name'] . ' ' . $user[':last_name'] . '</p><br>';
    echo '<p align="center">Current Balance: $' . htmlspecialchars($user[':balance']['balance']) . '</p><br>';
} else { //Session user is not set yet, user isn't logged in
    header('Location: login.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Money</title>
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
        <h1 class="form-title">
            Transfer Money To Your Bank
        </h1>
        <p style="text-indent: 40px"> How much would you like to transfer out?</p>
        <form method="POST" action="../scripts/alterBalance.php">
            <div class="input-group">
                <i class="fa-solid fa-dollar-sign"></i>
                <input type="amount" name="amount" id="amount" required placeholder="Amount">
            </div>
            <p>
                <input type="submit" class="btn" value="Transfer Money" name="transfer_out">
            </p>
    </form>
    </div>

</body>

</html>
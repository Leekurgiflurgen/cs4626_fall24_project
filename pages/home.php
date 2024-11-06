<?php
session_start();
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
} else { //Session user is not set yet, user isn't logged in
    header('Location: ../index.html');
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
            echo '<p align = "center"> Email : ' . $user[':email'] . '</p><br>';
            echo '<p align = "center"> Name : ' . $user[':first_name'] . ' ' . $user[':last_name'] . '</p><br>';
            echo '<p align="center">Current Balance: $' . htmlspecialchars($user[':balance']['balance']) . '</p><br>';
            ?>

            <div style="text-align: center;">
                <input type="submit" class="btn" value="Add Money" name="referToAdd"onclick="location.href='addCash.php'" >
                <input type="submit" class="btn" value="Send Money" name="referToSend"onclick="location.href='sendCash.php'" >
                <input type="submit" class="btn" value="Transfer Money" name="referToTransfer"onclick="location.href='transferOut.php'" >
                <input type="submit" class="btn" value="Log Out" name="logout"onclick="location.href='../scripts/logout.php'" >

            </div>
        </div>
    </div>
</body>
</html>
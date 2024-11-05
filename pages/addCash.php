<?php
if(isset($_SESSION['user'])){
    $user = $_SESSION['user'];
}else { //Session user is not set yet, user isn't logged in
    $_SESSION['error_login'] = "You must be logged in to access this page.";
    header('Location: login.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Money</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body>
    <div class="settings-menu">
        <div class="dropdown">
            <button>Menu Option</button>
            <div class="dropdown-content">
                <a href="../index.html">Home</a>
                <a href="./pages/about.html">About</a>
                <a href="./pages/login.php">Login</a>
                <a href="./pages/register.php">Register</a>
                <a href="./pages/addCash.php">Add Cash</a>
                <a href="./pages/sendCash.php">Send Cash</a>
            </div>
        </div>
    </div>
    <div class="container">
        <h1 class="form-title">
            Add Money To Your Account
        </h1>
        <p> How much would you like to add?</p>
        <form method="POST" action="../alterBalance.php">
            <div class="input-group">
                <i class="fa-solid fa-dollar-sign"></i>
                <input type="amount" name="amount" id="amount" required placeholder="Amount">
            </div>
            <p>
                <input type="submit" class="btn" value="Add Value" name="add_value">
            </p>
        </form>
</body>

</html>
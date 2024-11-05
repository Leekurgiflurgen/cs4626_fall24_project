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
            </div>
        </div>
    </div>
    <div class="container">
        <h1 class="form-title">
            Send Money To Another Account
        </h1>
            <p style="text-indent: 35px">   Who would you like to send the money to?</p>
        <form method="POST" action="../scripts/alterBalance.php"> <!--Sends form to alterBalance to be processed-->
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" id="email" required placeholder="Email">
            </div><br>
            <p>How much would you like to send?</p>
            <div class="input-group">
                <i class="fa-solid fa-dollar-sign"></i>
                <input type="amount" name="amount" id="amount" required placeholder="Amount">
            </div>
            <p>
                <input type="submit" class="btn" value="Send Money" name="send_money">
            </p>
</body>

</html>
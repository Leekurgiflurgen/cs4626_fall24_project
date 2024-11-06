<?php
session_start();
if (isset($_SESSION['errors'])) {
    $errors = $_SESSION['errors'];
    echo '<p align = "center"> Email : ' . $user[':email'] . '</p><br>';
    echo '<p align = "center"> Name : ' . $user[':first_name'] . ' ' . $user[':last_name'] . '</p><br>';
    echo '<p align="center">Current Balance: $' . htmlspecialchars($user[':balance']['balance']) . '</p><br>';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
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
                <a href="addCash.php">Add Cash</a>
                <a href="sendCash.php">Send Cash</a>
                <a href="./transferOut.php">Transfer Out</a>

            </div>
        </div>
    </div>
    </div></br>
    <div class="container">
        <h1 class="form-title">Register</h1>
        <?php
        if (isset($errors['user_exist'])) { //If error caught is user exist, echo on screen
            echo '<div class="error-main"> <p>' . $errors['user_exist'] . '</p> </div>';
        }
        ?>

        <form method="POST" action="../scripts/userAccount.php"> <!--Sends form to userAccount to be processed-->
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="first_name" id="first_name" placeholder="First Name" required>
                <?php
                if (isset($errors['name'])) {
                    echo '<div class="error"> <p>' . $errors['name'] . '</p> </div>';
                }
                ?>
            </div>
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="last_name" id="last_name" placeholder="Last Name" required>
                <?php
                if (isset($errors['name'])) {
                    echo '<div class="error"> <p>' . $errors['name'] . '</p> </div>';
                }
                ?>
            </div>
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="text" name="email" id="email" placeholder="Email" required>
                <?php
                if (isset($errors['email'])) {
                    echo '<div class="error"> <p>' . $errors['email'] . '</p> </div>';
                }
                ?>
            </div>
            <div class="input-group password">
                <i class="fas fa-lock"></i>
                <input type="text" name="password" id="password" placeholder="Password" required>
                <i id="eye" class="fa fa-eye"></i>
                <?php
                if (isset($errors['password'])) {
                    echo '<div class="error"> <p>' . $errors['password'] . '</p> </div>';
                }
                ?>
            </div>
            <div class="input-group password">
                <i class="fas fa-lock"></i>
                <input type="text" name="password_confirm" id="password_confirm" placeholder="Confirm Password"
                    required>
                <?php
                if (isset($errors['password_confirm'])) {
                    echo '<div class="error"> <p>' . $errors['password_confirm'] . '</p> </div>';
                }
                ?>
            </div>
            <input type="submit" class="btn" value="Sign Up" name="signup">
        </form>
        <p class="or">
            ------or------
        </p>
        <div class="links">
            <p>
                Already Have An Account?
            </p>
            <a href="login.php"> Sign In </a>
        </div>
    </div>
    <script src="../script.js"></script>
</body>

</html>

<?php  //Script to unload error message after refreshing page
if (isset($_SESSION['errors'])) {
    unset($_SESSION['errors']);
}
?>
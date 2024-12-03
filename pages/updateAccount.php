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
    <title>Update Account</title>
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
            What would you like to Update?
        </h1>
        <p style="text-indent: 40px"> Your Current Information</p>
        <form method="POST" action="../scripts/userAccount.php">
        <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="first_name" id="first_name" placeholder="First Name" pattern="[A-Za-z\s'-]+" title="Only letters, spaces, hyphens, and apostrophes are allowed." 
                value="<?php echo htmlspecialchars($decryptedFirstName, ENT_QUOTES, 'UTF-8'); ?>" required>
                <?php
                if (isset($errors['name'])) {
                    echo '<div class="error"> <p>' . $errors['name'] . '</p> </div>';
                }
                if(isset($errors['first_name_type'])) {
                    echo '<div class="error"> <p>' . $errors['first_name_type'] . '</p> </div>';

                }
                ?>
            </div>
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="last_name" id="last_name" placeholder="Last Name"
                value="<?php echo htmlspecialchars($decryptedLastName, ENT_QUOTES, 'UTF-8'); ?>" required>
                <?php
                if (isset($errors['name'])) {
                    echo '<div class="error"> <p>' . $errors['name'] . '</p> </div>';
                }
                if(isset($errors['last_name_type'])) {
                    echo '<div class="error"> <p>' . $errors['last_name_type'] . '</p> </div>';

                }
                ?>
            </div>
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="text" name="email" id="email" placeholder="Email" 
                value="<?php echo htmlspecialchars($user[':email'], ENT_QUOTES, 'UTF-8'); ?>" required>
                <?php
                if (isset($errors['email'])) {
                    echo '<div class="error"> <p>' . $errors['email'] . '</p> </div>';
                }
                ?>
            </div>
            <p>
                <input type="submit" class="btn" value="Update Personal Information" name="update_account">
            </p>
        </form>
        <form method="POST" action="../scripts/userAccount.php"> 
        <div class="input-group password">
                <i class="fas fa-lock"></i>
                <input type="text" name="password" id="password" placeholder="Current Password" required>
                <i id="eye" class="fa fa-eye"></i>
                <?php
                if(isset($errors['password'])) {
                    echo '<div class="error"> <p>' . $errors['password'] . '</p> </div>';
                } 
                ?>
            </div>
            <div class="input-group password">
                <i class="fas fa-lock"></i>
                <input type="text" name="new_password" id="new_password" placeholder="New Password" required>
                <i id="eye" class="fa fa-eye"></i>
                <?php
                if (isset($errors['password_empty'])) {
                    echo '<div class="error"> <p>' . $errors['password_empty'] . '</p> </div>';
                }
                if (isset($errors['password_length'])) {
                    echo '<div class="error"> <p>' . $errors['password_length'] . '</p> </div>';
                }
                if (isset($errors['password_confirm'])) {
                    echo '<div class="error"> <p>' . $errors['password_confirm'] . '</p> </div>';
                }
                ?>
            </div>
            <div class="input-group password">
                <i class="fas fa-lock"></i>
                <input type="text" name="password_confirm" id="password_confirm" placeholder="Confirm New Password"
                    required>
                <i id="eye" class="fa fa-eye"></i>

                <?php
                if (isset($errors['password_confirm'])) {
                    echo '<div class="error"> <p>' . $errors['password_confirm'] . '</p> </div>';
                }
                if (isset($errors['password_length'])) {
                    echo '<div class="error"> <p>' . $errors['password'] . '</p> </div>';
                }

                ?>
            </div>
            <p>
                <input type="submit" class="btn" value="Update Password" name="update_password">
            </p>
        </form>

        <form method="POST" action="../scripts/userAccount.php">
        <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="text" name="current_pin" id="current_pin" placeholder="Current PIN"
                    required>
                <i id="eye" class="fa fa-eye"></i>
                <?php
                if (isset($errors['accountPIN'])) {
                    echo '<div class = "error-main"> 
                        <p> ' . $errors['accountPIN'] . '</p>
                      </div>';
                } 
                ?>
        </div>
        <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="text" name="new_pin" id="new_pin"
                    placeholder="New Account Pin: 5 numerical digits+ " required>
                <i id="eye" class="fa fa-eye"></i>
                <?php
                if (isset($errors['amount_type'])) {
                    echo '<div class = "error-main"> 
                        <p> ' . $errors['amount_type'] . '</p>
                      </div>';
                }
                if (isset($errors['amount_sign'])) {
                    echo '<div class = "error-main"> 
                        <p> ' . $errors['amount_sign'] . '</p>
                      </div>';
                }
                if (isset($errors['pin_empty'])) {
                    echo '<div class = "error-main"> 
                        <p> ' . $errors['pin_empty'] . '</p>
                      </div>';
                }
                if (isset($errors['pin_length'])) {
                    echo '<div class = "error-main"> 
                        <p> ' . $errors['pin_length'] . '</p>
                      </div>';
                }
                ?>
        </div>
        <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="text" name="confirm_pin" id="confirm_pin"
                    placeholder="Please confirm your PIN" required>
                <i id="eye" class="fa fa-eye"></i>
                <?php
                if (isset($errors['pin_empty'])) {
                    echo '<div class = "error-main"> 
                        <p> ' . $errors['pin_empty'] . '</p>
                      </div>';
                }
                if (isset($errors['pin_length'])) {
                    echo '<div class = "error-main"> 
                        <p> ' . $errors['pin_length'] . '</p>
                      </div>';
                }
                if (isset($errors['pin_confirm'])) {
                    echo '<div class = "error-main"> 
                        <p> ' . $errors['pin_confirm'] . '</p>
                      </div>';
                }

                ?>
        </div>
            <p>
                <input type="submit" class="btn" value="Update PIN" name="update_pin">
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
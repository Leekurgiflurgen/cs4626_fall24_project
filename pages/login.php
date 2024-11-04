<?php
session_start();
if(isset($_SESSION['errors'])){
    $errors=$_SESSION['errors'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href = "../styles.css">
    <link rel="stylesheet" href = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"> 
</head>
<body>
    <div class="container">
        <h1 class="form-title">
            Sign In
        </h1>
        <?php 
        if(isset($errors['login'])){
            echo '<div class = "error-main"> 
                    <p> '.$errors['login'].'</p>
                  </div>';
        }
        ?>
        <form method="POST" action="../userAccount.php"> <!--Sends form to userAccount to be processed-->
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" id="email" required placeholder="Email">
                <?php 
                if(isset($errors['email'])){
                    echo '<div class = "error"> 
                            <p> '.$errors['email'].'</p>
                        </div>';
                    }
                ?>
            </div>
            <div class = "input-group password">
                <i class="fas fa-lock"></i>
                <input type="password"name="password" id="password" required placeholder="Password">
                <i class = "fa fa-eye" id="eye"></i>
                <?php 
                if(isset($errors['password'])){
                    echo '<div class = "error"> 
                            <p> '.$errors['password'].'</p>
                        </div>';
                    }
                ?>
            </div>
            <p class="recover">
                <a href="recover.html"> Recover Password</a>
            </p>
            <input type = "submit" class="btn" value="Log In" name="login">
        <p class="or">
           ----------or----------
        </p>
        <div class="Links">
            <p> Don't have account yet?</p>
            <a href = "register.php"> Sign Up</a>
        </div>
    </div>
    <script src="../script.js"></script>
</body>
</html>

<?php
if(isset($_SESSION['errors'])){
    unset($_SESSION['errors']);
}
?>
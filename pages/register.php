<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
    <link rel="stylesheet" href ="../styles.css">
    <link rel="stylesheet" href = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"> 

</head>
<body>
    <div class = "container">
        <h1 class ="form-title">Register</h1>
        <form method = "POST" action="">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="first_name" id="first_name" placeholder="First Name" required>             
            </div>
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="last_name" id="last_name" placeholder="Last Name" required>             
            </div>
            <div class="input-group">
                <i class = "fas fa-envelope"></i>
                <input type="text" name="email" id="email" placeholder="Email" required>
            </div>
            <div class="input-group password">
                <i class ="fas fa-lock"></i>
                <input type="text" name="password" id="password" placeholder="Password" required>
                <i id="eye" class="fa fa-eye"></i>
            </div>
            <div class="input-group password">
                <i class ="fas fa-lock"></i>
                <input type="text" name="password_confirm" id="password_confirm" placeholder="Confirm Password" required>
            </div>
            <input type = "submit" class="btn" value="Sign Up" name="signup">
        </form>
        <p class="or">
            ------or------
        </p>
        <div class="links">
            <p> 
                Already Have An Account?
            </p>
            <a href = "login.php"> Sign In </a>
        </div>
    </div>
    <script src="../script.js"></script>
    </body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href = "../styles.css">
    <link rel="stylesheet" href = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"> 
</head>
<body>
    <div class="container">
        <h1 class="form-title">
            Forgot Password?
        </h1>
        <form method="POST" action="../sendReset.php"> <!--Sends form to sendReset to be processed-->
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" id="email" required placeholder="Email">
        </div>
        <input type = "submit" class="btn" value="Send Reset" name="resetPassword">
    </div>
</body>
</html>
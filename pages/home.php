<?php
session_start();
if(isset($_SESSION['user'])){
    $user = $_SESSION['user'];
}else { //Session user is not set yet, user isn't logged in
    header('Location: ../index.html');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href = "../styles.css">
    <link rel="stylesheet" href = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
    <div class="user-details">
        <p> Logged In User</p>
        <?php
            echo '<p> Email : '.$user[':email'].'</p><br>';
            echo '<p> Name : ' . $user[':first_name'] . ' ' . $user[':last_name'] . '</p><br>';
        ?>
        <a href = "logout.php"> Logout</a>
    </div>
    
</body>
</html>
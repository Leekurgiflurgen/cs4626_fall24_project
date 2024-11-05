<?php
    require_once 'dbconnect.php';   
    session_start();
    $email = $_POST["email"]; //Get email from input 
    $token = bin2hex(random_bytes(16)); //random token to be hashed
    $token_hash = hash("sha256", $token); //Returns hashed token of length 64
    $expiry = date("Y-m-d H:i:s", time() + 60 * 5);

    $sql = "UPDATE users
        SET resetTokenHash = ?,
            resetTokenExpiresAt = ?
        WHERE email = ?";
    $stmt= $pdo->prepare('UPDATE users
        SET resetTokenHash = ?,
            resetTokenExpiresAt = ?
        WHERE email = ?');
    //$stmt->bind_param("sss", $token_hash, $expiry, $email);
    $stmt->execute([$token_hash, $expiry, $email]); 

    $stmt = $pdo -> prepare("SELECT * FROM users WHERE email=:email");
    $stmt -> execute(params: ['email'=> $email]);
    if ($stmt->rowCount()){
        $mail= require './mailer.php';
        
        $mail->setFrom("noreply@example.com");
        $mail->addAddress($email);
        $mail->Subject = "Password Reset";
        $mail->Body = <<<END

        Click <a href="http://example.com/reset-password.php?token=$token">here</a> 
        to reset your password.

        END;

        try {
            $mail->send();
        }catch(Exception $e){
            echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
        }
    }
    echo "Message sent, please check your inbox.";

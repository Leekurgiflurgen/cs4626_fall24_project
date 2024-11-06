<?php
require_once 'dbconnect.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../vendor/autoload.php';
require 'C:/xampp/htdocs/vendor/phpmailer/phpmailer/src/Exception.php';
require 'C:/xampp/htdocs/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'C:/xampp/htdocs/vendor/phpmailer/phpmailer/src/SMTP.php';
session_start();

$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL); //Santizes email to ensure valid email format    
$token = bin2hex(random_bytes(16)); //random token to be hashed
$token_hash = hash("sha256", $token); //Returns hashed token of length 64
$expiry = date("Y-m-d H:i:s", time() + 60 * 5);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Invalid email format.';
}

$stmt = $pdo->prepare('UPDATE users
        SET resetTokenHash = ?,
            resetTokenExpiresAt = ?
        WHERE email = ?');

$stmt->execute([$token_hash, $expiry, $email]);

$stmt = $pdo->prepare("SELECT * FROM users WHERE email=:email");
$stmt->execute(params: ['email' => $email]);
if ($stmt->rowCount()) { //if email was updated successfully'
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'localhost';  // Your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'nathankarg@gmail.com';    // Your email username
        $mail->Password = 'ebcr ftvf tffg jwvt';      // App password
        $mail->SMTPSecure = 'tls';                    // Encryption (TLS or SSL)
        $mail->Port = 587;                            // SMTP port

        // Recipients
        $mail->setFrom('nathankarg@gmail.com', 'CS4626 Project IT');
        $mail->addAddress($email); // Add a recipient

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Reset Password';
        $mail->Body = 'Click <a href="localhost/project/pages/forgotPassword.php?token=your_token_here">here</a> to reset your password.';
        $mail->AltBody = 'Click the link to reset your password: localhost/project/pages/forgotPassword.php?token=your_token_here';

        $mail->send();
        echo 'Email sent successfully!';
    } catch (Exception $e) {
        echo 'Email failed: ', $mail->ErrorInfo;
    }
}

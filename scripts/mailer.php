<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;    
require '../vendor/autoload.php';
$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->SMTPAuth = true;
$mail->Host = "localhost";
$mail->SMTPSecure = 'tls';  
$mail->Port = 587;
$mail->Username = "your-user@example.com";
$mail->Password = "your-password";

$mail->isHtml(true);

return $mail;    
?>

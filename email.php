<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/SMTP.php';


define('SMTP_EMAIL', 'kavindumadhuranga954@gmail.com'); // my Gmail ID
define('SMTP_PASSWORD', ''); // Gmail App Password

// Function to send email
function sendEmail($email, $body, $subject)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_EMAIL;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom(SMTP_EMAIL, 'Faculty Board');
        $mail->addAddress($email);
        $mail->Subject = trim($subject);
        $mail->isHTML(true);

        $mail->Body = $body;

        $mail->send();
    } catch (Exception $e) {
        error_log("Email sending failed: {$mail->ErrorInfo}");
    }
}

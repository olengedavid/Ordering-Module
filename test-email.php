<?php

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'IPOS'; // Your Gmail address
    $mail->Password = '&9LvEYNQQ';    // Your Gmail App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('pkiharaw@gmail.com', 'IPOS System');
    $mail->addAddress('olengedavid@gmail.com'); // Where to send the test email

    $mail->isHTML(true);
    $mail->Subject = 'Test Email from IPOS';
    $mail->Body    = 'This is a test email from IPOS system. If you receive this, the email configuration is working.';

    $mail->send();
    echo "Test email sent successfully!\n";
} catch (Exception $e) {
    echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}\n";
}
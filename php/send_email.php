<?php
// send_email.php

// Include PHPMailer classes
require_once 'phpmailer/src/PHPMailer.php';
require_once 'phpmailer/src/SMTP.php';
require_once 'phpmailer/src/Exception.php';
require_once 'email_templates.php'; // Include the email templates

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmail($recipientEmail, $recipientName, $emailTemplate) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
        $mail->SMTPAuth = true;
        $mail->Username = 'webprogrammierung27@gmail.com'; // SMTP username
        $mail->Password = 'eiih ilpp viyx xdzu'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Recipients
        $mail->setFrom('webprogrammierung27@gmail.com', 'Ink & Inspiration');
        $mail->addAddress($recipientEmail, $recipientName); // Add a recipient

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = $emailTemplate['subject'];
        $mail->Body    = $emailTemplate['body'];

        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>

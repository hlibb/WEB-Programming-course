<?php
// email_templates.php

function getPaymentConfirmationEmail($recipientName) {
    $subject = 'Payment Confirmation';
    $body = "<p>Dear $recipientName,</p><p>Thank you for your payment. This is a test email.</p>";
    return ['subject' => $subject, 'body' => $body];
}

function getOrderShippedEmail($recipientName) {
    $subject = 'Order Shipped';
    $body = "<p>Dear $recipientName,</p><p>Your order has been shipped. Thank you for shopping with us.</p>";
    return ['subject' => $subject, 'body' => $body];
}

// Add more email templates as needed
function getPasswordResetEmail($recipientName, $resetToken) {
    $subject = 'Password Reset Request';
    $body = "<p>Dear $recipientName,</p>
             <p>We received a request to reset your password. Click the link below to reset your password:</p>
             <p><a href='http://yourdomain.com/reset_password.php?token=$resetToken'>Reset Password</a></p>
             <p>If you did not request a password reset, please ignore this email.</p>";
    return ['subject' => $subject, 'body' => $body];
}
?>

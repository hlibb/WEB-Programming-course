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
?>

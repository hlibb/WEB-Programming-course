<?php
include_once 'include/db_connection.php';
require 'send_email.php'; // Include the send email function

function generateRandomPassword($length = 12) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
    $charactersLength = strlen($characters);
    $randomPassword = '';

    // Ensure the password contains at least one uppercase letter, one lowercase letter, and one digit
    $randomPassword .= $characters[rand(10, 35)]; // Lowercase letter
    $randomPassword .= $characters[rand(36, 61)]; // Uppercase letter
    $randomPassword .= $characters[rand(0, 9)];  // Digit

    for ($i = 3; $i < $length; $i++) {
        $randomPassword .= $characters[rand(0, $charactersLength - 1)];
    }

    return str_shuffle($randomPassword); // Shuffle to ensure randomness
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $stmt = $link->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $randomPassword = generateRandomPassword();
        $hashedPassword = hash('sha512', $randomPassword); // Hashing the password

        $stmt = $link->prepare("UPDATE users SET password = ?, password_status = 'temporary' WHERE email = ?");
        $stmt->bind_param("ss", $hashedPassword, $email);
        $stmt->execute();
        $stmt->close();

        $emailTemplate = getResetPasswordEmail($user['name'], $randomPassword); // Assuming this function generates the email template
        sendEmail($email, $user['name'], $emailTemplate); // Send the email with the new password

        header("Location: login.php?message=Ein neues Passwort wurde an Ihre E-Mail-Adresse gesendet.");
        exit();
    } else {
        header("Location: reset_password.php?error=E-Mail-Adresse nicht gefunden.");
        exit();
    }
}
?>

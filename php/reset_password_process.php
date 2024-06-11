<?php
include_once 'include/db_connection.php';
require 'send_email.php'; // Include the send email function

function generateRandomPassword($length = 12) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
    $charactersLength = strlen($characters);
    $randomPassword = '';
    for ($i = 0; $i < $length; $i++) {
        $randomPassword .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomPassword;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $stmt = $link->prepare("SELECT * FROM kunden WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $randomPassword = generateRandomPassword();
        $hashedPassword = password_hash($randomPassword, PASSWORD_DEFAULT);

        $stmt = $link->prepare("UPDATE kunden SET password = ?, password_status = 'temporary' WHERE email = ?");
        $stmt->bind_param("ss", $hashedPassword, $email);
        $stmt->execute();
        $stmt->close();

        $emailTemplate = getResetPasswordEmail($user['name'], $randomPassword);
        sendEmail($email, $user['name'], $emailTemplate);

        header("Location: login.php?message=Ein neues Passwort wurde an Ihre E-Mail-Adresse gesendet.");
        exit();
    } else {
        header("Location: reset_password.php?error=E-Mail-Adresse nicht gefunden.");
        exit();
    }
}
?>

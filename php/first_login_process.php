<?php
session_start();
include_once 'include/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $oldPassword = $_POST['oldpassword'];
    $hashedNewPassword = $_POST['hashed_newpassword'];

    $stmt = $link->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($oldPassword, $user['password'])) {
        $stmt = $link->prepare("UPDATE users SET password = ?, password_status = 'active' WHERE email = ?");
        $stmt->bind_param("ss", $hashedNewPassword, $email);
        $stmt->execute();
        $stmt->close();

        header("Location: login.php?message=Ihr Passwort wurde erfolgreich geändert. Bitte loggen Sie sich ein.");
        exit();
    } else {
        header("Location: first_login.php?error=Ungültige E-Mail-Adresse oder Passwort.");
        exit();
    }
}
?>

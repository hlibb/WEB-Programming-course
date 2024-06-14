<?php
session_start();
include_once 'include/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $oldPassword = $_POST['oldpassword'];
    $newPassword = $_POST['newpassword'];

    // Passwortanforderungen überprüfen
    $passwordRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{9,}$/';
    if (!preg_match($passwordRegex, $newPassword)) {
        header("Location: first_login.php?error=Kennwort muss mindestens 9 Zeichen lang sein und einen Großbuchstaben, Kleinbuchstaben und eine Zahl enthalten.");
        exit();
    }

    $stmt = $link->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($oldPassword, $user['password'])) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $link->prepare("UPDATE users SET password = ?, password_status = 'active' WHERE email = ?");
        $stmt->bind_param("ss", $hashedPassword, $email);
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

<?php
session_start();
include_once 'include/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emailOrUsername = $_POST['email_or_username'];
    $password = $_POST['password'];

    $stmt = $link->prepare("SELECT * FROM kunden WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $emailOrUsername, $emailOrUsername);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['logged_in'] = true;
        $_SESSION['kunden_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['previous_login'] = $user['login_timestamp'];

        if ($user['password_status'] == 'temporary') {
            $_SESSION['force_password_change'] = true;
            header("Location: change_password.php");
            exit();
        } else {
            $update_sql = "UPDATE kunden SET login_timestamp = NOW() WHERE id = ?";
            if ($update_stmt = $link->prepare($update_sql)) {
                $update_stmt->bind_param("i", $user['id']);
                $update_stmt->execute();
                $update_stmt->close();
            }
            header("Location: home.php");
            exit();
        }
    } else {
        header("Location: login.php?error=Ungültige Email, Benutzername oder Passwort");
        exit();
    }
}
?>

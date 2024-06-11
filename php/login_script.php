<?php
session_start();
include_once 'include/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $link->prepare("SELECT * FROM kunden WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['logged_in'] = true;
        $_SESSION['kunden_id'] = $user['id'];
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
        header("Location: login.php?error=Ungültige Email oder Passwort");
        exit();
    }
}
?>

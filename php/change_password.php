<?php
session_start();
include_once 'include/db_connection.php';

if (!isset($_SESSION['logged_in']) || !isset($_SESSION['force_password_change'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPassword = $_POST['new_password'];
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $stmt = $link->prepare("UPDATE users SET password = ?, password_status = 'permanent' WHERE id = ?");
    $stmt->bind_param("si", $hashedPassword, $_SESSION['users_id']);
    $stmt->execute();
    $stmt->close();

    unset($_SESSION['force_password_change']);

    header("Location: home.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <title>Passwort ändern</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include '../php/include/headimport.php' ?>
</head>
<body>
<div class="container">
    <h1>Passwort ändern</h1>
    <form action="change_password.php" method="post">
        <div class="form-group">
            <label for="new_password">Neues Passwort</label>
            <input type="password" id="new_password" name="new_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Passwort ändern</button>
    </form>
</div>
</body>
</html>

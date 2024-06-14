<?php
session_start();
include_once 'include/db_connection.php';

if (!isset($_SESSION['logged_in']) || !isset($_SESSION['force_password_change'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPassword = $_POST['hashed_newpassword'];
    $stmt = $link->prepare("UPDATE users SET password = ?, password_status = 'permanent' WHERE id = ?");
    $stmt->bind_param("si", $newPassword, $_SESSION['users_id']);
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
    <form action="change_password.php" method="post" onsubmit="return validateForm(event)">
        <div class="form-group">
            <label for="new_password">Neues Passwort</label>
            <input type="password" id="new_password" name="new_password" class="form-control" required>
        </div>
        <input type="hidden" id="hashed_newpassword" name="hashed_newpassword">
        <button type="submit" class="btn btn-primary">Passwort ändern</button>
    </form>
    <?php if (isset($_GET['error'])): ?>
        <p style="color: red;"><?= htmlspecialchars($_GET['error']) ?></p>
    <?php endif; ?>
</div>
<script>
    async function hashPassword(password) {
        const msgUint8 = new TextEncoder().encode(password);
        const hashBuffer = await crypto.subtle.digest('SHA-512', msgUint8);
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
        return hashHex;
    }

    async function validateForm(event) {
        const password = document.getElementById('new_password').value;
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{9,}$/;

        if (!passwordRegex.test(password)) {
            alert('Kennwort muss mindestens 9 Zeichen lang sein und einen Großbuchstaben, Kleinbuchstaben und eine Zahl enthalten.');
            event.preventDefault();
            return false;
        }

        const hashedPassword = await hashPassword(password);
        document.getElementById('hashed_newpassword').value = hashedPassword;
        document.getElementById('new_password').value = ''; // Clear plain text password
        return true;
    }
</script>
</body>
</html>

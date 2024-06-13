<?php
session_start();
include_once 'include/db_connection.php';

if (!isset($_SESSION['users_id']) || !$_SESSION['first_login']) {
    header("Location: login.php");
    exit();
}

// Update the secret as verified
$stmt = $link->prepare("UPDATE users SET secret_verified = 1 WHERE id = ?");
if ($stmt === false) {
    die("Prepare failed: " . $link->error);
}
$stmt->bind_param("i", $_SESSION['users_id']);
if ($stmt->execute() === false) {
    die("Execute failed: " . $stmt->error);
}
$stmt->close();

unset($_SESSION['first_login']);
header("Location: home.php");
exit();
?>


<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Google Authenticator</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .container {
            text-align: center;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        img {
            margin-bottom: 20px;
        }
        p {
            margin: 10px 0;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            color: white;
            background-color: #007BFF;
            text-decoration: none;
            border-radius: 5px;
        }
        a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Setup Google Authenticator</h2>
    <?php if (isset($qrCodeUrl)): ?>
        <img src="<?= $qrCodeUrl ?>" alt="QR Code">
        <p>Scannen Sie den obigen QR-Code mit Ihrer Google Authenticator App.</p>
        <p>Wenn Sie den QR-Code nicht scannen können, geben Sie das folgende Geheimnis manuell ein:</p>
        <p><strong>Secret: <?= $secret ?></strong></p>
        <a href="login.php">Weiter zum Login</a>
    <?php else: ?>
        <form action="first_login.php" method="post">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="oldpassword">Altes Passwort</label>
                <input type="password" id="oldpassword" name="oldpassword" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="newpassword">Neues Passwort</label>
                <input type="password" id="newpassword" name="newpassword" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Speichern</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>

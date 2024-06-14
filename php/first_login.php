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
        <form action="first_login_process.php" method="post" onsubmit="return validateForm(event)">
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
            <input type="hidden" id="hashed_newpassword" name="hashed_newpassword">
            <button type="submit" class="btn btn-primary">Speichern</button>
        </form>
        <?php if (isset($_GET['error'])): ?>
            <p style="color: red;"><?= htmlspecialchars($_GET['error']) ?></p>
        <?php endif; ?>
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
        const password = document.getElementById('newpassword').value;
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{9,}$/;

        if (!passwordRegex.test(password)) {
            alert('Kennwort muss mindestens 9 Zeichen lang sein und einen Großbuchstaben, Kleinbuchstaben und eine Zahl enthalten.');
            event.preventDefault();
            return false;
        }

        const hashedPassword = await hashPassword(password);
        document.getElementById('hashed_newpassword').value = hashedPassword;
        document.getElementById('newpassword').value = ''; // Clear plain text password
        return true;
    }
</script>
</body>
</html>

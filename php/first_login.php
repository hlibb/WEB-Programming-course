<?php
session_start();

// Authenticator
require_once '../extern/google_auth/PHPGangsta/GoogleAuthenticator.php';

$ga = new PHPGangsta_GoogleAuthenticator();

// DB Settings
include 'include/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_SESSION['user_data'])) {
    // Hole die eingegebenen Daten aus dem Formular
    $email = $_POST['email'];
    $oldPassword = $_POST['oldpassword'];
    $newPassword = $_POST['newpassword'];

    // Überprüfe, ob die Email in der Tabelle existiert
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $link->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if ($user) {
        // Überprüfe das alte Passwort
        if (password_verify($oldPassword, $user['password'])) {
            // Generate a new secret key for the user
            $secret = $ga->createSecret();

            // Aktualisiere das Passwort und füge den geheimen Schlüssel hinzu
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT); // Hash das neue Passwort

            $updateQuery = "UPDATE users SET password = ?, secret = ? WHERE email = ?";
            $updateStmt = $link->prepare($updateQuery);
            $updateStmt->bind_param('sss', $hashedPassword, $secret, $email);
            $updateStmt->execute();

            // Generate the QR code URL
            $qrCodeUrl = $ga->getQRCodeGoogleUrl('YourAppName', $secret, 'https://yourdomain.com');

            // Store user data and QR code URL in the session
            $_SESSION['user_data'] = [
                'email' => $email,
                'qrCodeUrl' => $qrCodeUrl,
                'secret' => $secret
            ];

        } else {
            // Fehlermeldung für falsches Passwort
            echo "Falsches Passwort";
        }
    } else {
        // Fehlermeldung für nicht vorhandene Email in der Tabelle
        echo "Email existiert nicht in der Datenbank";
    }
}

if (isset($_SESSION['user_data'])) {
    $qrCodeUrl = $_SESSION['user_data']['qrCodeUrl'];
    $secret = $_SESSION['user_data']['secret'];
}
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

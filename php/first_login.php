<?php
session_start();
include_once 'include/db_connection.php';

require 'vendor/autoload.php';
use PHPGangsta_GoogleAuthenticator;

if (!isset($_SESSION['logged_in']) || !isset($_SESSION['first_login'])) {
    header("Location: login.php");
    exit();
}

$ga = new PHPGangsta_GoogleAuthenticator();
$secret = $ga->createSecret();
$qrCodeUrl = $ga->getQRCodeGoogleUrl('YourAppName', $secret);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $totpCode = $_POST['totp_code'];
    $checkResult = $ga->verifyCode($secret, $totpCode, 2); // 2 = 2*30sec clock tolerance

    if ($checkResult) {
        $stmt = $link->prepare("UPDATE users SET secret = ? WHERE id = ?");
        $stmt->bind_param("si", $secret, $_SESSION['users_id']);
        $stmt->execute();
        $stmt->close();

        unset($_SESSION['first_login']);

        header("Location: home.php");
        exit();
    } else {
        $error = "Invalid TOTP code. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <title>First Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include '../php/include/headimport.php' ?>
</head>
<body>
<div class="container">
    <h1>Erstmaliger Login</h1>
    <p>Scan the QR code with your Google Authenticator app and enter the generated code below:</p>
    <img src="<?php echo $qrCodeUrl; ?>" alt="QR Code">
    <form action="first_login.php" method="post">
        <div class="form-group">
            <label for="totp_code">TOTP Code</label>
            <input type="text" id="totp_code" name="totp_code" class="form-control" required>
        </div>
        <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
        <button type="submit" class="btn btn-primary">Speichern</button>
    </form>
</div>
</body>
</html>

<?php
session_start();

if (!isset($_GET['qr']) || !isset($_SESSION['user_data'])) {
    echo "Debug Information:";
    echo "<pre>";
    print_r($_GET);
    print_r($_SESSION);
    echo "</pre>";
    // Uncomment the next line after debugging
    // header("Location: registration.php");
    exit();
}

$qrCodeUrl = urldecode($_GET['qr']);
$userData = $_SESSION['user_data'];
$secret = isset($userData['secret']) ? $userData['secret'] : 'Secret not available';
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Setup Google Authenticator</title>
    <?php include '../php/include/headimport.php' ?>
    <style>
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }
        img {
            margin-bottom: 20px;
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
    <img src="<?php echo htmlspecialchars($qrCodeUrl); ?>" alt="QR Code"/>
    <p>Scannen Sie den QR-Code mit Ihrer Authenticator-App.</p>
    <p>Wenn Sie den QR-Code nicht scannen können, geben Sie das folgende Geheimnis manuell ein:</p>
    <p><strong>Secret: <?= htmlspecialchars($secret) ?></strong></p>
    <a href="login.php">Weiter zum Login</a>
</div>
</body>
</html>

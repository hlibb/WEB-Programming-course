<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "webshop";

// Verbindung zur Datenbank herstellen
$conn = new mysqli($servername, $username, $password, $dbname);

// Verbindung überprüfen
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Funktion zur Generierung eines zufälligen Passworts
function generateRandomPassword($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomPassword = '';
    for ($i = 0; $i < $length; $i++) {
        $randomPassword .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomPassword;
}

// Funktion zum Versenden der E-Mail
function sendConfirmationEmail($email, $password) {
    $subject = "Registrierungsbestätigung";
    $message = "Vielen Dank für Ihre Registrierung. Ihr Standardpasswort lautet: $password";
    $headers = "From: no-reply@yourdomain.com";

    mail($email, $subject, $message, $headers);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];

    // Generiere ein zufälliges Passwort
    $randomPassword = generateRandomPassword();
    $hashedPassword = password_hash($randomPassword, PASSWORD_BCRYPT); // Passwort verhasht speichern

    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashedPassword')";

    if ($conn->query($sql) === TRUE) {
        // Sende die Bestätigungs-E-Mail
        sendConfirmationEmail($email, $randomPassword);
        echo "User registered successfully. Please check your email for the password.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

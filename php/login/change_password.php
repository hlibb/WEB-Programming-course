<?php
session_start();

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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
    $user_id = $_SESSION['user_id'];

    $sql = "UPDATE users SET password = '$new_password', first_login = FALSE WHERE id = $user_id";

    if ($conn->query($sql) === TRUE) {
        echo "Password changed successfully";
        session_unset();
        session_destroy();
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $conn->close();
} else {
    echo "Unauthorized access";
}
?>

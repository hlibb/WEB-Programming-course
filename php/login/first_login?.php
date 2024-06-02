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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT id, password, first_login FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            if ($row['first_login']) {
                header('Location: change_password.html');
                exit();
            } else {
                echo "Login successful";
            }
        } else {
            echo "Invalid credentials";
        }
    } else {
        echo "No user found with this email";
    }

    $conn->close();
}
?>

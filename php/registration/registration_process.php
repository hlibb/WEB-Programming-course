<?php
global $link;
include_once '../include/db_connection.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["new-password"];
    $screen_resolution = $_POST["screen_resolution"];
    $operating_system = $_POST["operating_system"];

    // Sanitize input data
    $username = htmlspecialchars($username);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    // You can add further sanitization here

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Perform database connection check
    if ($link->connect_error) {
        die("Connection failed: " . $link->connect_error);
    }

    // Check if the user already exists
    $stmt = $link->prepare("SELECT * FROM users WHERE email = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $link->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        die("Email already exists.");
    }

    $dateTime = date("Y-m-d H:i:s");

    $stmt = $link->prepare("INSERT INTO users (username, email, password, screen_resolution, operating_system, login_timestamp) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $link->error);
    }

    // Bind parameters, including the datetime value
    $bind = $stmt->bind_param("ssssss", $username, $email, $hashedPassword, $screen_resolution, $operating_system, $datetime);
    if ($bind === false) {
        die("Bind failed: " . $stmt->error);
    }
    $exec = $stmt->execute();
    if ($exec === false) {
        die("Execute failed: " . $stmt->error);
    } else {
        // Redirect after successful registration
        header("Location: ../../index.html");
        exit();  // Add exit after header redirection
    }

    // Close the statement and the database connection
    $stmt->close();
    $link->close();
}
?>

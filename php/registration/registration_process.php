<?php
include_once '../include/db_connection.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["new-password"];

    // Debugging output for form data
    echo "Username: $username<br>";
    echo "Email: $email<br>";
    echo "Password: $password<br>";

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Debugging output for hashed password
    echo "Hashed Password: $hashedPassword<br>";

    // Perform database connection check
    if ($link->connect_error) {
        die("Connection failed: " . $link->connect_error);
    } else {
        echo "Database connected successfully.<br>";
    }

    // Prepare and execute the SQL query
    $stmt = $link->prepare("INSERT INTO users (username, email, password, screen_resolution, operating_system) VALUES (?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $link->error);
    }

    $screen_resolution = '1920x1080';  // Example value, replace with actual form data if available
    $operating_system = 'Windows';    // Example value, replace with actual form data if available

    $bind = $stmt->bind_param("sssss", $username, $email, $hashedPassword, $screen_resolution, $operating_system);
    if ($bind === false) {
        die("Bind failed: " . $stmt->error);
    }

    $exec = $stmt->execute();
    if ($exec === false) {
        die("Execute failed: " . $stmt->error);
    } else {
        echo "New record created successfully.<br>";
        header("Location: ../../index.html");
        exit();  // Add exit after header redirection
    }

    // Close the statement and the database connection
    $stmt->close();
    $link->close();
}
?>

<?php
global $link;
include_once 'include/db_connection.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["new-password"];
    $screen_resolution = $_POST["screen_resolution"];
    $operating_system = $_POST["operating_system"];

    $name = htmlspecialchars($name);
    $surname = htmlspecialchars($surname);
    $username = htmlspecialchars($username);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    // You can add further sanitization here

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    if ($link->connect_error) {
        die("Connection failed: " . $link->connect_error);
    }

    // Check if the user already exists
    $stmt = $link->prepare("SELECT * FROM kunden WHERE email = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $link->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        die("Email already exists.");

    }


    $stmt = $link->prepare("INSERT INTO kunden (name, surname, username, email, password, screen_resolution, operating_system) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $link->error);
    }

    // Bind parameters, including the datetime value
    $bind = $stmt->bind_param("sssssss", $name, $surname, $username, $email, $hashedPassword, $screen_resolution, $operating_system);
    if ($bind === false) {
        die("Bind failed: " . $stmt->error);
    }
    $exec = $stmt->execute();
    if ($exec === false) {
        die("Execute failed: " . $stmt->error);
    } else {
        // Update login timestamp
        $update_sql = "UPDATE kunden SET login_timestamp = NOW() WHERE id = ?";
        if ($update_stmt = mysqli_prepare($link, $update_sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($update_stmt, "i", $id);
            // Attempt to execute the prepared statement
            if (!mysqli_stmt_execute($update_stmt)) {
                echo "Oops! Something went wrong. Please try again later.";
            }
            // Close statement
            mysqli_stmt_close($update_stmt);
        }
        // Redirect after successful registration
        header("Location: ../index.html");
        // Close the statement
        $stmt->close();
        // Close the database connection
        $link->close();
        exit();  // Add exit after header redirection
    }
}

<?php
include_once '../db_connection.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["new-password"];
    $accountType = $_POST["account-type"];
    $age = $_POST["age"];
    $referrer = $_POST["referrer"];
    $additionalInfo = $_POST["addinfo"];

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Perform database connection check
    if ($link->connect_error) {
        die("Connection failed: " . $link->connect_error);
    }

    // Prepare and execute the SQL query
    $sql = "INSERT INTO users (username, email, password, account_type, age, referrer, additional_info)
            VALUES ('$username', '$email', '$hashedPassword', '$accountType', '$age', '$referrer', '$additionalInfo')";

    if ($link->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $link->error;
    }

    // Close the database connection
    $link->close();
}
?>
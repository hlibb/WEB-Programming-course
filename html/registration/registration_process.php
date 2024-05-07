<?php
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["new-password"];
    $accountType = $_POST["account-type"];
    $age = $_POST["age"];
    $referrer = $_POST["referrer"];
    $additionalInfo = $_POST["addinfo"];
    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Your database connection code goes here
    // Replace "your_host", "your_username", "your_password", and "your_database" with your actual database credentials
    $conn = new mysqli("localhost:3306", "root", "", "web-programming");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement to insert data into the table
    $sql = "INSERT INTO users (username, email, password, account_type, age, referrer, additional_info)
            VALUES ('$username', '$email', '$hashedPassword', '$accountType', '$age', '$referrer', '$additionalInfo')";

    // Execute SQL statement
    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";

    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close database connection
    $conn->close();
}
?>
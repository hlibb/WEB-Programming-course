<?php
session_start();
include_once 'include/db_connection.php';
require_once '../extern/google_auth/PHPGangsta/GoogleAuthenticator.php';
include 'send_email.php'; // Include the send email function

$ga = new PHPGangsta_GoogleAuthenticator();

function generateRandomPassword($length = 12) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
    $charactersLength = strlen($characters);
    $randomPassword = '';
    for ($i = 0; $i < $length; $i++) {
        $randomPassword .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomPassword;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $screen_resolution = $_POST['screen_resolution'];
    $operating_system = $_POST['operating_system'];

    $name = htmlspecialchars($name);
    $surname = htmlspecialchars($surname);
    $username = htmlspecialchars($username);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    $randomPassword = generateRandomPassword();
    $hashedPassword = password_hash($randomPassword, PASSWORD_DEFAULT);

    if ($link->connect_error) {
        die("Connection failed: " . $link->connect_error);
    }

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

    $stmt = $link->prepare("INSERT INTO users (name, surname, username, email, password, password_status, screen_resolution, operating_system) VALUES (?, ?, ?, ?, ?, 'temporary', ?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $link->error);
    }

    $bind = $stmt->bind_param("sssssss", $name, $surname, $username, $email, $hashedPassword, $screen_resolution, $operating_system);
    if ($bind === false) {
        die("Bind failed: " . $stmt->error);
    }
    $exec = $stmt->execute();
    if ($exec === false) {
        die("Execute failed: " . $stmt->error);
    } else {
        $id = $stmt->insert_id;

        // Insert default points for the new user
        $stmt = $link->prepare("INSERT INTO punkte (users_id, points) VALUES (?, 100)");
        if ($stmt === false) {
            die("Prepare failed: " . $link->error);
        }
        $stmt->bind_param("i", $id);
        if ($stmt->execute() === false) {
            die("Execute failed: " . $stmt->error);
        }

        $update_sql = "UPDATE users SET login_timestamp = NOW() WHERE id = ?";
        if ($update_stmt = mysqli_prepare($link, $update_sql)) {
            mysqli_stmt_bind_param($update_stmt, "i", $id);
            if (!mysqli_stmt_execute($update_stmt)) {
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($update_stmt);
        }

        // Generate the QR code and secret
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl('YourAppName', $secret, 'YourAppName');

        // Store the secret in the database
        $update_secret_sql = "UPDATE users SET secret = ? WHERE id = ?";
        if ($update_secret_stmt = mysqli_prepare($link, $update_secret_sql)) {
            mysqli_stmt_bind_param($update_secret_stmt, "si", $secret, $id);
            if (!mysqli_stmt_execute($update_secret_stmt)) {
                echo "Oops! Something went wrong while saving the secret. Please try again later.";
            }
            mysqli_stmt_close($update_secret_stmt);
        }

        // Store user data and QR code URL in the session
        $_SESSION['user_data'] = [
            'email' => $email,
            'qrCodeUrl' => $qrCodeUrl,
            'secret' => $secret
        ];

        // Send registration email
        $emailTemplate = getRegistrationEmail($name, $username, $randomPassword);
        sendEmail($email, $name, $emailTemplate);

        // Redirect to show_qr_code.php with the QR code URL as a parameter
        header("Location: show_qr_code.php?qr=" . urlencode($qrCodeUrl));
        exit();
    }
}
?>
